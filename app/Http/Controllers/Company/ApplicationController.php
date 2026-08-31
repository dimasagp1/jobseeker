<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $applications = JobApplication::whereIn('job_id', $company->jobs()->pluck('id'))
            ->with(['job', 'user'])
            ->latest()
            ->paginate(15);

        return view('company.applications.index', compact('applications'));
    }

    public function show(JobApplication $application)
    {
        $this->authorizeAccess($application);
        $application->load([
            'job',
            'kraepelinTest',
            'psychologicalResults',
            'user.seekerProfile.experiences',
            'user.seekerProfile.educations',
            'user.seekerProfile.skills'
        ]);

        return view('company.applications.show', compact('application'));
    }


    public function updateStatus(Request $request, JobApplication $application)
    {
        try {
            $request->validate([
                'status' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            $application->update([
                'status' => $request->status,
                'notes' => $request->notes ?? $application->notes
            ]);

            // Pastikan mengembalikan JSON dan status 200
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui ke ' . $application->status_label,
                'data' => $application
            ], 200);
        } catch (\Exception $e) {
            // Jika ada error, kembalikan status 422 atau 500
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadCv(JobApplication $application)
    {
        $this->authorizeAccess($application);

        if (!$application->cv_path || !Storage::disk('public')->exists($application->cv_path)) {
            return back()->with('error', 'Resume not found.');
        }

        return Storage::disk('public')->download($application->cv_path);
    }

    public function downloadCover(JobApplication $application)
    {
        // 1. Tambahkan proteksi akses agar perusahaan lain tidak bisa asal download
        $this->authorizeAccess($application);

        // 2. Gunakan disk 'public' agar sama dengan saat file disimpan
        $path = $application->cover_letter_path;

        if ($path && Storage::disk('public')->exists($path)) {
            // 3. Berikan nama file yang rapi saat didownload
            $filename = str_replace(' ', '_', $application->user->name) . '_Cover_Letter.' . pathinfo($path, PATHINFO_EXTENSION);

            return Storage::disk('public')->download($path, $filename);
        }

        return back()->with('error', 'File Surat Lamaran tidak ditemukan di server.');
    }

    public function showPsychologicalResults(JobApplication $application)
    {
        $this->authorizeAccess($application);

        // Ambil hasil MSDT dan PAPI terbaru untuk aplikasi ini
        $msdt = $application->psychologicalResults()->where('test_type', 'msdt')->latest()->first();
        $papi = $application->psychologicalResults()->where('test_type', 'papi')->latest()->first();

        return view('company.applications.psychological_results', compact('application', 'msdt', 'papi'));
    }

    public function downloadPsychologicalPdf(JobApplication $application)
    {
        if (Auth::user()->role === 'company') {
            $this->authorizeAccess($application);
        }

        $application->load(['user', 'job.company', 'kraepelinTest']);

        $kraepelinTest = $application->kraepelinTest;
        $discResult = $application->psychologicalResults()->where('test_type', 'disc')->where('status', 'completed')->first();
        $msdtResult = $application->psychologicalResults()->where('test_type', 'msdt')->where('status', 'completed')->first();
        $papiResult = $application->psychologicalResults()->where('test_type', 'papi')->where('status', 'completed')->first();

        if (!$kraepelinTest && !$discResult && !$msdtResult && !$papiResult) {
            return back()->with('error', 'Belum ada data tes psikotes yang diselesaikan kandidat.');
        }

        $siteSettings = Company::first();
        $logoBase64 = null;
        if ($siteSettings && $siteSettings->company_logo && file_exists(public_path('storage/' . $siteSettings->company_logo))) {
            $path = public_path('storage/' . $siteSettings->company_logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('company.applications.all_psychological_pdf', compact(
            'application', 'kraepelinTest', 'discResult', 'msdtResult', 'papiResult', 'siteSettings', 'logoBase64'
        ));

        return $pdf->setPaper('a4', 'portrait')->download('Laporan_Lengkap_Psikotes_' . $application->user->name . '.pdf');
    }

    private function authorizeAccess(JobApplication $application)
    {
        if ($application->job->company_id !== Auth::user()->company->id) {
            abort(403);
        }
    }
}
