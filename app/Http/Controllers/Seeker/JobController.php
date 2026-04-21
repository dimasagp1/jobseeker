<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    /**
     * Menampilkan daftar lowongan kerja yang tersedia (Published).
     */
    public function index(Request $request)
    {
        Job::closeExpiredJobs();

        $query = Job::with('company')->active();

        // Filter berdasarkan keyword (Judul Pekerjaan atau Nama Perusahaan)
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhereHas('company', function ($c) use ($request) {
                        $c->where('company_name', 'like', '%' . $request->keyword . '%');
                    });
            });
        }

        // Filter Berdasarkan Lokasi
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Filter Berdasarkan Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $jobs = $query->latest()->paginate(12);
        $locations = JobLocation::all();
        $categories = JobCategory::all();

        return view('seeker.jobs.index', compact('jobs', 'locations', 'categories'));
    }

    /**
     * Menampilkan detail lowongan kerja tertentu.
     */
    public function show(Job $job)
    {
        Job::closeExpiredJobs();

        if (!$job->isActive()) {
            abort(404);
        }

        $user = auth()->user();
        $profile = $user->seekerProfile;

        // Audit Kelengkapan Profil untuk Tombol Apply di View
        $profileStatus = [
            'foto'      => !empty($user->avatar),
            'deskripsi' => !empty($profile?->summary),
            'resume'    => !empty($profile?->resume_path),
            'riwayat'   => ($profile?->experiences->count() > 0 || $profile?->educations->count() > 0)
        ];

        // Syarat minimal: Foto, Deskripsi, dan Resume
        $isProfileComplete = $profileStatus['foto'] && $profileStatus['deskripsi'] && $profileStatus['resume'];

        // Cek status lamaran user saat ini
        $hasApplied = $user->applications()->where('job_id', $job->id)->exists();
        $isAcceptedSomewhere = $user->applications()->where('status', 'accepted')->exists();
        $hasActiveApplication = $user->applications()->whereNotIn('status', ['rejected', 'accepted'])->exists();
        $isSaved = $user->savedJobs()->where('jobs.id', $job->id)->exists();

        // Increment View Count
        $job->increment('views');

        return view('seeker.jobs.show', compact(
            'job',
            'isProfileComplete',
            'profileStatus',
            'hasApplied',
            'isAcceptedSomewhere',
            'hasActiveApplication',
            'isSaved'
        ));
    }

    /**
     * Menampilkan form pendaftaran lowongan (Multi-step).
     */
    public function showApplyForm(Job $job)
    {
        Job::closeExpiredJobs();

        if (!$job->isActive()) {
            abort(404);
        }

        $user = Auth::user();
        $profile = $user->seekerProfile;

        // Proteksi: Wajib Profil Lengkap
        $isProfileComplete = $profile && $profile->summary && $profile->resume_path && $user->avatar;
        if (!$isProfileComplete) {
            return redirect()->route('seeker.jobs.show', $job)->with('error', 'Lengkapi profil Anda terlebih dahulu.');
        }

        // Proteksi: Tidak boleh melamar jika ada lamaran yang sedang aktif
        $hasActiveApplication = $user->applications()->whereNotIn('status', ['rejected', 'accepted'])->exists();
        if ($hasActiveApplication) {
            return redirect()->route('seeker.jobs.show', $job)->with('error', 'Anda memiliki lamaran yang sedang diproses.');
        }

        // Proteksi: Tidak boleh melamar posisi yang sama dua kali
        if ($user->applications()->where('job_id', $job->id)->exists()) {
            return redirect()->route('seeker.jobs.show', $job)->with('error', 'Anda sudah melamar posisi ini.');
        }

        return view('seeker.jobs.apply', compact('job', 'user', 'profile'));
    }

    /**
     * Memproses data lamaran kerja (Submit Form).
     */
    public function submitApplication(Request $request, Job $job)
    {
        Job::closeExpiredJobs();

        if (!$job->isActive()) {
            return redirect()->route('seeker.jobs.index')->with('error', 'Lowongan ini sudah tidak aktif.');
        }

        $user = Auth::user();
        $profile = $user->seekerProfile;

        // 1. Validasi Keamanan Akhir
        if ($user->applications()->whereNotIn('status', ['rejected', 'accepted'])->exists()) {
            return redirect()->route('seeker.jobs.show', $job)->with('error', 'Gagal: Anda memiliki lamaran aktif.');
        }

        // 2. Validasi Input
        $request->validate([
            'resume'            => 'required_without:use_existing_resume|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'q1' => 'required', 'q2' => 'required', 'q3' => 'required', 'q4' => 'required', 'q5' => 'required|numeric',
            'q6' => 'required', 'q7' => 'required', 'q8' => 'required', 'q9' => 'required', 'q10' => 'required',
            'q11' => 'required', 'q12' => 'required', 'q13' => 'required', 'q14' => 'required', 'q15' => 'required|date',
        ]);

        // 3. Handling Files (Resume & Cover Letter)
        if ($request->has('use_existing_resume') && $profile->resume_path) {
            $cvPath = $profile->resume_path;
        } else {
            $cvPath = $request->file('resume')->store('resumes', 'public');
        }

        $clPath = $request->file('cover_letter_file')->store('cover_letters', 'public');

        // 4. Mapping Jawaban Kuesioner (q1 - q15)
        $answers = $request->only([
            'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'q10', 'q11', 'q12', 'q13', 'q14', 'q15'
        ]);

        // 5. Simpan Data ke Database
        $user->applications()->create([
            'job_id'            => $job->id,
            'cv_path'           => $cvPath,
            'cover_letter_path' => $clPath,
            'answers'           => $answers, 
            'status'            => 'pending'
        ]);

        return redirect()->route('seeker.applications.index')->with('success', 'Lamaran Anda berhasil dikirim ke perusahaan.');
    }

    /**
     * Fitur Simpan Lowongan (Bookmark).
     */
    public function save(Job $job)
    {
        Auth::user()->savedJobs()->syncWithoutDetaching([$job->id]);
        return back()->with('success', 'Lowongan berhasil disimpan.');
    }

    /**
     * Fitur Hapus Simpanan Lowongan.
     */
    public function unsave(Job $job)
    {
        Auth::user()->savedJobs()->detach($job->id);
        return back()->with('success', 'Lowongan dihapus dari daftar simpan.');
    }
}