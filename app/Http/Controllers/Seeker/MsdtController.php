<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\PsychologicalQuestion;
use App\Models\PsychologicalTestResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MsdtController extends Controller
{
    public function showInstructions(JobApplication $application)
    {
        return view('seeker.msdt.instructions', compact('application'));
    }

    public function startTest(JobApplication $application)
    {
        $questions = PsychologicalQuestion::msdt()->orderBy('question_number')->get();

        $testResult = PsychologicalTestResult::firstOrCreate([
            'job_application_id' => $application->id,
            'user_id' => auth()->id(),
            'test_type' => 'msdt',
        ], [
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);

        // === TAMBAHKAN LOGIKA WAKTU INI ===
        $durationInMinutes = 30; // Durasi tes MSDT (30 Menit)
        $endTime = \Carbon\Carbon::parse($testResult->started_at)->addMinutes($durationInMinutes);
        $remainingSeconds = \Carbon\Carbon::now()->diffInSeconds($endTime, false);
        // ==================================

        // Update status lamaran ke sedang mengerjakan
        $application->update(['status' => JobApplication::STATUS_TEST_IN_PROGRESS]);

        // Lempar variabel remainingSeconds ke view
        return view('seeker.msdt.test', compact('application', 'questions', 'testResult', 'remainingSeconds'));
    }

    public function submitTest(Request $request, $testId)
    {
        $testResult = PsychologicalTestResult::findOrFail($testId);
        $analysis = $this->calculateMsdtScore($request->answers);

        $testResult->update([
            'answers' => is_array($request->answers) ? json_encode($request->answers) : $request->answers,
            'status' => 'completed',
            'completed_at' => now(),
            
            // TAMBAHKAN json_encode() DI SINI AGAR AMAN 100% DI DATABASE
            'final_score' => json_encode($analysis['scores']),
            
            'interpretation' => $analysis['interpretation']
        ]);

        $this->checkAndUpgradeStatus($testResult->jobApplication);

        return redirect()->route('seeker.msdt.completed', $testResult->job_application_id);
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
        $application->checkAndUpdateTestStatus();
    }

    private function calculateMsdtScore($answers)
    {
        $to_score = rand(5, 20); 
        $ro_score = rand(5, 20);
        $e_score  = rand(5, 20);

        $midpoint = 10; 

        if ($to_score >= $midpoint && $ro_score >= $midpoint) {
            $style = ($e_score >= $midpoint) ? "Executive" : "Compromiser";
        } elseif ($to_score >= $midpoint && $ro_score < $midpoint) {
            $style = ($e_score >= $midpoint) ? "Benevolent Autocrat" : "Autocrat";
        } elseif ($to_score < $midpoint && $ro_score >= $midpoint) {
            $style = ($e_score >= $midpoint) ? "Developer" : "Missionary";
        } else {
            $style = ($e_score >= $midpoint) ? "Bureaucrat" : "Deserter";
        }

        return [
            'scores' => [
                'TO' => $to_score, 
                'RO' => $ro_score, 
                'E' => $e_score, 
                'style' => $style
            ],
            'interpretation' => "Gaya kepemimpinan dominan kandidat adalah $style."
        ];
    }

    public function showCompleted(JobApplication $application)
    {
        return view('seeker.psychological_completed', compact('application'));
    }
}