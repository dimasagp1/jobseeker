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

class DiscController extends Controller
{
    public function showInstructions(JobApplication $application)
    {
        return view('seeker.disc.instructions', compact('application'));
    }

    public function startTest(JobApplication $application)
    {
        $questions = PsychologicalQuestion::where('test_type', 'disc')
            ->orderBy('question_number')
            ->get();

        $testResult = PsychologicalTestResult::firstOrCreate([
            'job_application_id' => $application->id,
            'user_id' => auth()->id(),
            'test_type' => 'disc',
        ], [
            'status' => 'in_progress',
            'started_at' => \Carbon\Carbon::now(),
        ]);

        // === TAMBAHKAN LOGIKA WAKTU INI ===
        $durationInMinutes = 15; // Durasi tes D.I.S.C
        $endTime = \Carbon\Carbon::parse($testResult->started_at)->addMinutes($durationInMinutes);
        $remainingSeconds = \Carbon\Carbon::now()->diffInSeconds($endTime, false);
        // ==================================

        $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);

        // === PERHATIKAN BARIS INI: Tambahkan 'remainingSeconds' di dalam compact ===
        return view('seeker.disc.test', compact('application', 'questions', 'testResult', 'remainingSeconds'));
    }

    public function submitTest(Request $request, $testId)
    {
        $testResult = PsychologicalTestResult::findOrFail($testId);

        // Logic hitung skor
        $analysis = $this->calculateDiscScore($request->answers);

        $testResult->update([
            // Tidak perlu json_encode karena di Model sudah di-cast sebagai 'array'
            'answers' => $request->answers, 
            'status' => 'completed',
            'completed_at' => Carbon::now(),
            
            // KEMBALIKAN KE final_score (Sesuai dengan Database)
            'final_score' => $analysis['totals'],
            
            'interpretation' => $analysis['interpretation']
        ]);

        $this->checkAndUpgradeStatus($testResult->jobApplication);

        return redirect()->route('seeker.disc.completed', $testResult->job_application_id);
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

    private function calculateDiscScore($answers)
    {
        // 1. Inisialisasi Skor Mentah (Raw Score)
        $scoreP = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0, '*' => 0]; // * = Invalid/Bintang
        $scoreK = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0, '*' => 0];

        $questions = PsychologicalQuestion::where('test_type', 'disc')->get()->keyBy('question_number');

        if (!$answers) {
            return ['totals' => ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0], 'interpretation' => 'Data jawaban kosong'];
        }

        // 2. Hitung Jawaban (Scoring)
        foreach ($answers as $num => $choice) {
            if (!isset($questions[$num])) continue;

            $mappingP = json_decode($questions[$num]->dimension_p, true);
            $mappingK = json_decode($questions[$num]->dimension_k, true);

            // Hitung Most (Paling Menggambarkan - P)
            if (isset($choice['p'])) {
                $valP = $mappingP[$choice['p']] ?? '*';
                if (isset($scoreP[$valP])) $scoreP[$valP]++;
            }

            // Hitung Least (Paling Tidak Menggambarkan - K)
            if (isset($choice['k'])) {
                $valK = $mappingK[$choice['k']] ?? '*';
                if (isset($scoreK[$valK])) $scoreK[$valK]++;
            }
        }

        // 3. Hitung Selisih (Graph 3 - Composite / Synthesis)
        // Rumus Asli: P - K
        // Hasilnya bisa minus (contoh: D Most = 2, D Least = 10, maka Change = -8)
        $rawChange = [
            'D' => $scoreP['D'] - $scoreK['D'],
            'I' => $scoreP['I'] - $scoreK['I'],
            'S' => $scoreP['S'] - $scoreK['S'],
            'C' => $scoreP['C'] - $scoreK['C'],
        ];

        // 4. Konversi ke Skala DISC (0 - 40) Menggunakan Interpolasi
        // Di tes DISC standar (24 soal), rentang maksimal untuk P-K secara teoritis adalah -24 sampai +24.
        // Namun, dalam kenyataannya, sangat jarang (hampir mustahil) seseorang mendapat skor ekstrim murni +24 atau -24 di satu faktor.
        // Rentang wajar (norma) biasanya berada di kisaran -15 (sangat rendah) hingga +15 (sangat tinggi).
        
        $totals = [];
        foreach ($rawChange as $trait => $score) {
            // Batasi skor ekstrim (clipping) agar tidak merusak perhitungan
            $score = max(-15, min(15, $score));

            // Konversi dari rentang [-15, 15] ke rentang [0, 40]
            // Rumus: ((Score - MinRange) / (MaxRange - MinRange)) * SkalaBaru
            // ((Score - (-15)) / (15 - (-15))) * 40 => ((Score + 15) / 30) * 40
            
            $converted = round((($score + 15) / 30) * 40);
            
            // Pastikan tidak ada yang kurang dari 0 atau lebih dari 40
            $totals[$trait] = (int) max(0, min(40, $converted));
        }

        return [
            'totals' => $totals,
            'interpretation' => "Analisis DISC: D=" . $totals['D'] . ", I=" . $totals['I'] . ", S=" . $totals['S'] . ", C=" . $totals['C']
        ];
    }

    private function checkAndUpgradeStatus($application)
    {
        $application->refresh();

        $kraepelinDone = $application->kraepelinTest()->whereNotNull('completed_at')->exists();

        // Ambil semua hasil tes psikotes
        $results = $application->psychologicalResults()->where('status', 'completed')->pluck('test_type')->toArray();

        // Syarat: Kraepelin + MSDT + PAPI + DISC harus Selesai
        $othersDone = in_array('msdt', $results) && in_array('papi', $results) && in_array('disc', $results);

        if ($kraepelinDone && $othersDone) {
            $application->update(['status' => JobApplication::STATUS_TEST_COMPLETED]);
        } else {
            $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);
        }
    }

    public function showCompleted(JobApplication $application)
    {
        // Menggunakan view yang sama dengan MSDT & PAPI untuk efisiensi
        return view('seeker.psychological_completed', compact('application'));
    }

    public function exportPdf(JobApplication $application)
    {
        $application->load(['user', 'job.company']);
        $discResult = $application->psychologicalResults()
            ->where('test_type', 'disc')
            ->where('status', 'completed')
            ->first();

        if (!$discResult) {
            return back()->with('error', 'Data tes DISC tidak ditemukan atau belum selesai.');
        }

        $siteSettings = Company::first();
        $logoBase64 = null;
        if ($siteSettings && $siteSettings->company_logo && file_exists(public_path('storage/' . $siteSettings->company_logo))) {
            $path = public_path('storage/' . $siteSettings->company_logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('company.applications.disc_pdf', compact('application', 'discResult', 'siteSettings', 'logoBase64'))->setPaper('a4', 'portrait');
        $filename = 'Laporan_DISC_' . $application->user->name . '.pdf';
        if (request()->query('stream') == 1 || request()->query('preview') == 1) {
            return $pdf->stream($filename);
        }
        return $pdf->download($filename);
    }
}