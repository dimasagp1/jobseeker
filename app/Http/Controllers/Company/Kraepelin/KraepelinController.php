<?php

namespace App\Http\Controllers\Company\Kraepelin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\KraepelinTest;
use App\Models\Company;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KraepelinController extends Controller
{
    /**
     * Menampilkan instruksi tes.
     */
    public function showInstructions($applicationId)
    {
        $application = JobApplication::with('job.company')->findOrFail($applicationId);

        // Proteksi Akses
        if (!in_array($application->status, [JobApplication::STATUS_TEST_INVITED, JobApplication::STATUS_TEST_IN_PROGRESS])) {
            return redirect()->route('seeker.dashboard')->with('error', 'Akses tes ditolak atau sudah selesai.');
        }

        return view('seeker.kraepelin.instructions', compact('application'));
    }

    /**
     * Inisialisasi Tes & Generate Soal.
     */
    public function startTest($applicationId)
    {
        $application = JobApplication::where('id', $applicationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Hanya izinkan jika status mengundang tes
        if (!in_array($application->status, [JobApplication::STATUS_TEST_INVITED, JobApplication::STATUS_TEST_IN_PROGRESS])) {
            return redirect()->route('seeker.dashboard')->with('error', 'Tes tidak tersedia.');
        }

        $test = KraepelinTest::where('job_application_id', $application->id)
            ->whereNull('completed_at')
            ->first();

        if (!$test) {
            // Reset tes lama jika ada kebocoran data
            KraepelinTest::where('job_application_id', $application->id)->delete();

            $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);

            // Generate 50 Kolom x 40 Baris (Standar Industri)
            $generatedQuestions = [];
            for ($i = 0; $i < 50; $i++) {
                $column = [];
                for ($j = 0; $j < 40; $j++) {
                    $column[] = rand(0, 9);
                }
                $generatedQuestions[] = $column;
            }

            $test = KraepelinTest::create([
                'job_application_id' => $application->id,
                'questions' => $generatedQuestions,
                'started_at' => now(),
            ]);
        }

        return view('seeker.kraepelin.test', compact('application', 'test'));
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

    /**
     * Submit Jawaban & Analisis Psikometri Otomatis.
     */
    public function submitTest(Request $request, $testId)
    {
        DB::beginTransaction();
        try {
            $test = KraepelinTest::findOrFail($testId);

            // 1. Parsing Input
            $userAnswers = $request->input('answers', []);
            if (is_string($userAnswers)) {
                $userAnswers = json_decode($userAnswers, true);
            }

            // Gunakan data dari model (pastikan casting array di model aktif)
            $questions = $test->questions;

            $resultsPerColumn = [];
            $totalCorrect = 0;
            $totalError = 0;
            $totalSkipped = 0;

            // 2. Analisis per Kolom
            foreach ($questions as $colIndex => $column) {
                $maxRowFilled = -1;

                // Cari baris tertinggi yang diisi (Cek 39 celah)
                for ($r = count($column) - 2; $r >= 0; $r--) {
                    $key = "{$colIndex}-{$r}";
                    if (isset($userAnswers[$key]) && $userAnswers[$key] !== "") {
                        $maxRowFilled = $r;
                        break;
                    }
                }

                if ($maxRowFilled !== -1) {
                    for ($r = 0; $r <= $maxRowFilled; $r++) {
                        $key = "{$colIndex}-{$r}";
                        $correctSum = ($column[$r] + $column[$r + 1]) % 10;

                        if (!isset($userAnswers[$key]) || $userAnswers[$key] === "") {
                            $totalSkipped++; // Hole
                        } else {
                            if ((int)$userAnswers[$key] === $correctSum) {
                                $totalCorrect++;
                            } else {
                                $totalError++;
                            }
                        }
                    }
                    $resultsPerColumn[] = $maxRowFilled + 1;
                } else {
                    $resultsPerColumn[] = 0;
                }
            }

            // 3. Hitung Indikator Utama
            $totalCols = count($resultsPerColumn);

            // PANKER (Kecepatan)
            $panker = array_sum($resultsPerColumn) / $totalCols;

            // TIANKER (Ketelitian)
            $tianker = $totalError + $totalSkipped;

            // JANKER (Stabilitas)
            $janker = max($resultsPerColumn) - min($resultsPerColumn);

            // GANKER (Ketahanan/Slope)
            $half = floor($totalCols / 2);
            if ($half > 0) {
                $avgFirst = array_sum(array_slice($resultsPerColumn, 0, $half)) / $half;
                $avgSecond = array_sum(array_slice($resultsPerColumn, $half)) / ($totalCols - $half);
                $ganker = $avgSecond - $avgFirst;
            } else {
                $ganker = 0;
            }

            // 4. Update Database
            $test->update([
                'answers' => $userAnswers,
                'results_chart' => $resultsPerColumn,
                'panker' => $panker,
                'tianker' => $tianker,
                'janker' => $janker,
                'ganker' => $ganker,
                'total_correct' => $totalCorrect,
                'total_answered' => $totalCorrect + $totalError,
                'completed_at' => now(),
            ]);

            $this->checkAndUpgradeStatus($test->jobApplication);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'redirect' => route('seeker.kraepelin.completed', $test->job_application_id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Kraepelin Submit Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Halaman sukses setelah tes.
     */
    public function showCompleted($applicationId)
    {
        $application = JobApplication::where('id', $applicationId)
            ->where('user_id', Auth::id())
            ->with('kraepelinTest')
            ->firstOrFail();

        $test = $application->kraepelinTest;

        if (!$test || !$test->completed_at) {
            return redirect()->route('seeker.dashboard')->with('error', 'Hasil tes belum tersedia.');
        }

        return view('seeker.kraepelin.completed', compact('application', 'test'));
    }

    /**
     * Ekspor PDF.
     */
    public function exportPdf(JobApplication $application)
    {
        $application->load(['kraepelinTest', 'user', 'job.company']);
        $test = $application->kraepelinTest;

        if (!$test || !$test->completed_at) {
            return back()->with('error', 'Data tes tidak lengkap.');
        }

        $siteSettings = Company::first();
        $logoBase64 = null;
        if ($siteSettings && $siteSettings->company_logo && file_exists(public_path('storage/' . $siteSettings->company_logo))) {
            $path = public_path('storage/' . $siteSettings->company_logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $analysis = [
            'speed' => $this->getLabel($test->panker, 10, 15),
            'accuracy' => $test->tianker < 5 ? 'Sangat Baik' : ($test->tianker < 15 ? 'Baik' : 'Perlu Perhatian'),
            'consistency' => $test->janker < 5 ? 'Sangat Stabil' : ($test->janker < 10 ? 'Stabil' : 'Fluktuatif'),
            'endurance' => $test->ganker >= 0 ? 'Meningkat/Stabil' : 'Menurun (Mudah Lelah)'
        ];

        $pdf = Pdf::loadView('company.applications.kraepelin_pdf', compact('application', 'test', 'analysis', 'siteSettings', 'logoBase64'))->setPaper('a4', 'portrait');
        $filename = 'Laporan_Kraepelin_' . $application->user->name . '.pdf';
        if (request()->query('stream') == 1 || request()->query('preview') == 1) {
            return $pdf->stream($filename);
        }
        return $pdf->download($filename);
    }

    private function getLabel($val, $low, $high)
    {
        if ($val < $low) return 'Rendet (Rendah)';
        if ($val > $high) return 'Cepat (Tinggi)';
        return 'Normal (Sedang)';
    }
}
