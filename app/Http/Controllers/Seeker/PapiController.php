<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\PsychologicalQuestion;
use App\Models\PsychologicalTestResult;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PapiController extends Controller
{
    public function showInstructions(JobApplication $application)
    {
        return view('seeker.papi.instructions', compact('application'));
    }

    public function startTest(JobApplication $application)
    {
        $questions = PsychologicalQuestion::papi()->orderBy('question_number')->get();

        $testResult = PsychologicalTestResult::firstOrCreate([
            'job_application_id' => $application->id,
            'user_id' => auth()->id(),
            'test_type' => 'papi',
        ], [
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);

        // === TAMBAHKAN LOGIKA WAKTU INI ===
        $durationInMinutes = 30; // Durasi tes PAPI (30 Menit)
        $endTime = \Carbon\Carbon::parse($testResult->started_at)->addMinutes($durationInMinutes);
        $remainingSeconds = \Carbon\Carbon::now()->diffInSeconds($endTime, false);
        // ==================================

        $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);

        // Lempar variabel remainingSeconds ke view
        return view('seeker.papi.test', compact('application', 'questions', 'testResult', 'remainingSeconds'));
    }

    public function submitTest(Request $request, $testId)
    {
        $testResult = PsychologicalTestResult::findOrFail($testId);
        $scores = $this->calculatePapiScore($request->answers);

        $testResult->update([
            // PERBAIKAN: Pastikan array answers di-encode ke JSON
            'answers' => is_array($request->answers) ? json_encode($request->answers) : $request->answers,
            'status' => 'completed',
            'completed_at' => now(),
            
            // PERBAIKAN: Pastikan scores di-encode ke JSON (meskipun model casts array, untuk berjaga-jaga)
            'final_score' => is_array($scores) ? json_encode($scores) : $scores,
            
            'interpretation' => "Profil kepribadian PAPI Kostick telah dianalisis sesuai standar dimensi."
        ]);

        $this->checkAndUpgradeStatus($testResult->jobApplication);

        return redirect()->route('seeker.papi.completed', $testResult->job_application_id);
    }

    public function autoSave(Request $request, $testId)
    {
        $testResult = \App\Models\PsychologicalTestResult::find($testId);

        // Pastikan tes ditemukan dan belum berstatus completed
        if ($testResult && $testResult->status !== 'completed') {
            // Update HANYA kolom answers saja, status tetap in_progress
            $testResult->update([
                'answers' => $request->answers
            ]);

            return response()->json(['status' => 'success', 'message' => 'Draft tersimpan di database']);
        }

        return response()->json(['status' => 'error', 'message' => 'Tes tidak valid atau sudah selesai'], 400);
    }

    private function checkAndUpgradeStatus($application)
    {
        $application->refresh();

        // Cek Kraepelin
        $kraepelinDone = $application->kraepelinTest()->whereNotNull('completed_at')->exists();

        // Cek MSDT, PAPI, DISC
        $results = $application->psychologicalResults()->where('status', 'completed')->pluck('test_type')->toArray();

        $othersDone = in_array('msdt', $results) && in_array('papi', $results) && in_array('disc', $results);

        if ($kraepelinDone && $othersDone) {
            $application->update(['status' => JobApplication::STATUS_TEST_COMPLETED]);
        } else {
            $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);
        }
    }

    private function calculatePapiScore($answers)
    {
        // 20 Dimensi PAPI Kostick (Kosongkan dulu nilainya)
        $finalScores = [
            'G' => 0, 'L' => 0, 'I' => 0, 'T' => 0, 'V' => 0, 
            'S' => 0, 'R' => 0, 'D' => 0, 'C' => 0, 'E' => 0, 
            'N' => 0, 'A' => 0, 'P' => 0, 'X' => 0, 'B' => 0, 
            'O' => 0, 'Z' => 0, 'K' => 0, 'F' => 0, 'W' => 0
        ];

        // Jika tidak ada jawaban (misal user bypass form), kembalikan array skor kosong
        if (!$answers || !is_array($answers)) {
            return $finalScores;
        }

        // Ambil mapping dimensi (a dan b) dari database soal PAPI
        $questions = PsychologicalQuestion::where('test_type', 'papi')->get()->keyBy('question_number');

        foreach ($answers as $num => $choice) {
            if (!isset($questions[$num])) continue;

            // Pastikan pilihan dikonversi ke huruf kecil agar case-insensitive
            $choiceKey = strtolower($choice);

            // Ambil dimensi (huruf A-Z) sesuai dengan pilihan kandidat (a atau b)
            $dimension = null;
            if ($choiceKey === 'a') {
                $dimension = $questions[$num]->dimension_a;
            } elseif ($choiceKey === 'b') {
                $dimension = $questions[$num]->dimension_b;
            }

            // Jika dimensi valid dan ada di array $finalScores, tambahkan skornya (+1)
            if ($dimension && isset($finalScores[$dimension])) {
                $finalScores[$dimension]++;
            }
        }

        return $finalScores;
    }

    public function showCompleted(JobApplication $application)
    {
        return view('seeker.psychological_completed', compact('application'));
    }

    public function exportPdf(JobApplication $application)
    {
        $application->load(['user', 'job.company']);
        $papiResult = $application->psychologicalResults()
            ->where('test_type', 'papi')
            ->where('status', 'completed')
            ->first();

        if (!$papiResult) {
            return back()->with('error', 'Data tes PAPI tidak ditemukan atau belum selesai.');
        }

        $siteSettings = Company::first();
        $logoBase64 = null;
        if ($siteSettings && $siteSettings->company_logo && file_exists(public_path('storage/' . $siteSettings->company_logo))) {
            $path = public_path('storage/' . $siteSettings->company_logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('company.applications.papi_pdf', compact('application', 'papiResult', 'siteSettings', 'logoBase64'))->setPaper('a4', 'portrait');
        $filename = 'Laporan_PAPI_' . $application->user->name . '.pdf';
        if (request()->query('stream') == 1 || request()->query('preview') == 1) {
            return $pdf->stream($filename);
        }
        return $pdf->download($filename);
    }
}