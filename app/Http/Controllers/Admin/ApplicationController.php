<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use App\Mail\ApplicationStatusUpdatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Master status yang diizinkan dalam sistem.
     * Termasuk status untuk integrasi Tes Kraepelin.
     */
    protected $allowedStatuses = 'pending,reviewed,shortlisted,test_invited,test_in_progress,test_completed,interview,rejected,accepted';

    public function index(Request $request)
    {
        $jobs = Job::withTrashed()->latest()->get();

        $query = JobApplication::with([
            'job' => function($q) { $q->withTrashed(); },
            'job.company', 
            'user'
        ]);

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('job', function($jobQuery) use ($search) {
                    $jobQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->latest()->paginate(15)->appends($request->query());
            
        return view('admin.applications.index', compact('applications', 'jobs'));
    }

    public function create()
    {
        // Hanya ambil lowongan yang aktif
        $jobs = Job::with('company')->where('status', 'published')->latest()->get();
        // Hanya ambil user dengan role seeker
        $users = User::where('role', 'seeker')->orderBy('name')->get();
        
        return view('admin.applications.create', compact('jobs', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id'       => 'required|exists:jobs,id',
            'user_id'      => 'required|exists:users,id',
            'cv_path'      => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
        ]);

        // Proteksi data ganda
        if (JobApplication::where('job_id', $request->job_id)->where('user_id', $request->user_id)->exists()) {
             return back()->withInput()->with('error', 'Kandidat ini sudah terdaftar melamar pada lowongan tersebut.');
        }

        $path = $request->file('cv_path')->store('resumes', 'public');

        JobApplication::create([
            'job_id'       => $request->job_id,
            'user_id'      => $request->user_id,
            'cv_path'      => $path,
            'cover_letter' => $request->cover_letter,
            'status'       => 'pending',
        ]);

        return redirect()->route('admin.applications.index')->with('success', 'Lamaran berhasil ditambahkan secara manual.');
    }
    
    public function show(JobApplication $application)
    {
        // Memuat seluruh data pendukung untuk review mendalam oleh Admin
        $application->load([
            'job' => function($q) { $q->withTrashed(); },
            'job.company', 
            'user.seekerProfile.experiences', 
            'user.seekerProfile.educations', 
            'user.seekerProfile.skills',
            'kraepelinTest' // Data hasil tes jika ada
        ]);
        
        return view('admin.applications.show', compact('application'));
    }

    public function edit(JobApplication $application)
    {
        $application->load(['user', 'job' => function($q) { $q->withTrashed(); }]);
        return view('admin.applications.edit', compact('application'));
    }

    public function update(Request $request, JobApplication $application)
    {
        $request->validate([
            'status'       => 'required|in:' . $this->allowedStatuses,
            'cover_letter' => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $application->update([
            'status'       => $request->status,
            'cover_letter' => $request->cover_letter,
            'notes'        => $request->notes,
        ]);

        try {
            $application->load(['user', 'job.company']);
            Mail::to($application->user->email)->send(new ApplicationStatusUpdatedMail($application, $request->notes));
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim email status lamaran admin: ' . $e->getMessage());
        }

        return redirect()->route('admin.applications.index')->with('success', 'Informasi lamaran telah diperbarui.');
    }

    public function destroy(JobApplication $application)
    {
        // Hapus file fisik agar tidak memenuhi storage
        if ($application->cv_path && Storage::disk('public')->exists($application->cv_path)) {
            Storage::disk('public')->delete($application->cv_path);
        }
        
        // Hapus data tes Kraepelin terkait jika ada
        if ($application->kraepelinTest) {
            $application->kraepelinTest->delete();
        }
        
        $application->delete();
        return redirect()->route('admin.applications.index')->with('success', 'Data lamaran dan berkas terkait telah dihapus.');
    }

    /**
     * Quick Action: Update status lamaran tanpa masuk ke halaman edit.
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $request->validate([
            'status' => 'required|in:' . $this->allowedStatuses,
        ]);

        $application->update(['status' => $request->status]);

        try {
            $application->load(['user', 'job.company']);
            Mail::to($application->user->email)->send(new ApplicationStatusUpdatedMail($application, $application->notes));
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim email update status admin: ' . $e->getMessage());
        }

        $statusText = match($request->status) {
            'pending'          => 'Menunggu',
            'reviewed'         => 'Ditinjau',
            'shortlisted'      => 'Terpilih',
            'test_invited'     => 'Diundang Tes Kraepelin',
            'test_in_progress' => 'Sedang Mengerjakan Tes',
            'interview'        => 'Wawancara',
            'accepted'         => 'Diterima',
            'rejected'         => 'Ditolak',
            default            => ucfirst($request->status)
        };

        return back()->with('success', 'Status lamaran berhasil diubah menjadi: ' . $statusText);
    }

    /**
     * Mengunduh file CV dengan aman.
     */
    public function downloadCv(JobApplication $application)
    {
        if (!$application->cv_path || !Storage::disk('public')->exists($application->cv_path)) {
            return back()->with('error', 'Maaf, file CV tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($application->cv_path);
    }
}