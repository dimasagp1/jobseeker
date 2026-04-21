<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Helper untuk mengambil data perusahaan tunggal (HerbaTech).
     */
    private function getCompany()
    {
        return Company::first() ?? abort(404, 'Data perusahaan HerbaTech belum dikonfigurasi di database.');
    }

    /**
     * Menampilkan daftar semua lowongan pekerjaan.
     */
    public function index()
    {
        Job::closeExpiredJobs();

        // Menggunakan withCount agar data pelamar muncul di list
        $jobs = Job::withCount('applications')->latest()->paginate(10);
        return view('company.jobs.index', compact('jobs'));
    }

    /**
     * Menampilkan form untuk membuat lowongan baru.
     */
    public function create()
    {
        $categories = JobCategory::where('is_active', true)->get();
        $locations = JobLocation::where('is_active', true)->get();

        return view('company.jobs.create', compact('categories', 'locations'));
    }

    /**
     * Menyimpan lowongan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'category_id' => 'required|exists:job_categories,id',
            'location_id' => 'required|exists:job_locations,id',
            'work_setting' => 'required|in:on_site,hybrid,remote',
            'job_type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'responsibilities' => 'nullable|string',
            'salary_currency' => 'required|string|max:3',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_type' => 'required|in:hourly,monthly,yearly,project',
            'experience_level' => 'required|string',
            'education_level' => 'nullable|in:sd,smp,sma,d3,s1,s2,s3',
            'deadline' => 'required|date|after:today',
            'vacancy' => 'required|integer|min:1',
            'is_salary_visible' => 'nullable|boolean',
            'required_tests'   => 'nullable|array',
            'required_tests.*' => 'in:kraepelin,msdt,papi,disc',
        ]);

        $company = $this->getCompany();

        $company->jobs()->create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'department' => $request->department,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'work_setting' => $request->work_setting,
            'job_type' => $request->job_type,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'responsibilities' => $request->responsibilities,
            'salary_currency' => $request->salary_currency,
            'salary_min' => $request->filled('salary_min') ? $request->salary_min : null,
            'salary_max' => $request->filled('salary_max') ? $request->salary_max : null,
            'salary_type' => $request->salary_type,
            'is_salary_visible' => $request->has('is_salary_visible'), // Logika checkbox
            'experience_level' => $request->experience_level,
            'education_level' => $request->education_level,
            'deadline' => $request->deadline,
            'vacancy' => $request->vacancy,
            'status' => 'published',
            'required_tests' => $request->input('required_tests', []),
        ]);

        return redirect()->route('company.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil diterbitkan.');
    }

    /**
     * Menampilkan detail lowongan.
     */
    public function show(Job $job)
    {
        $job->loadCount('applications');
        return view('company.jobs.show', compact('job'));
    }

    /**
     * Menampilkan form edit lowongan.
     */
    public function edit(Job $job)
    {
        $categories = JobCategory::where('is_active', true)->get();
        $locations = JobLocation::where('is_active', true)->get();

        return view('company.jobs.edit', compact('job', 'categories', 'locations'));
    }

    /**
     * Memperbarui data lowongan.
     */
    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'category_id' => 'required|exists:job_categories,id',
            'location_id' => 'required|exists:job_locations,id',
            'work_setting' => 'required|in:on_site,hybrid,remote',
            'job_type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'responsibilities' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_type' => 'required|in:hourly,monthly,yearly,project',
            'experience_level' => 'required|string',
            'education_level' => 'nullable|in:sd,smp,sma,d3,s1,s2,s3',
            'deadline' => 'required|date',
            'vacancy' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,closed,expired',
            'is_salary_visible' => 'nullable|boolean',
            'required_tests'   => 'nullable|array',
            'required_tests.*' => 'in:kraepelin,msdt,papi,disc',
        ], [
            'department.required' => 'Nama Departemen tidak boleh dikosongkan.',
            'vacancy.required' => 'Jumlah lowongan wajib diisi.',
            'vacancy.min' => 'Jumlah lowongan harus minimal 1.',
        ]);

        $job->update([
            'title' => $request->title,
            // Opsional: Update slug jika judul berubah
            'slug' => $job->title !== $request->title ? Str::slug($request->title) . '-' . time() : $job->slug,
            'department' => $request->department,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'work_setting' => $request->work_setting,
            'job_type' => $request->job_type,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'responsibilities' => $request->responsibilities,
            'salary_min' => $request->filled('salary_min') ? $request->salary_min : null,
            'salary_max' => $request->filled('salary_max') ? $request->salary_max : null,
            'salary_type' => $request->salary_type,
            'is_salary_visible' => $request->has('is_salary_visible'),
            'experience_level' => $request->experience_level,
            'education_level' => $request->education_level,
            'vacancy' => $request->vacancy,
            'status' => $request->status,
            'required_tests' => $request->input('required_tests', []),
        ]);

        return redirect()->route('company.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil diperbarui.');
    }

    /**
     * Menghapus lowongan pekerjaan (Soft Delete).
     */
    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('company.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil dihapus.');
    }

    /**
     * Menutup lowongan secara cepat dari index.
     */
    public function close(Job $job)
    {
        $job->update(['status' => 'closed']);
        return back()->with('success', ' Lowongan telah ditutup.');
    }

    /**
     * Menerbitkan lowongan secara cepat dari index.
     */
    public function publish(Job $job)
    {
        if ($job->deadline && $job->deadline->isPast() && !$job->deadline->isToday()) {
            return back()->with('error', 'Lowongan tidak bisa diaktifkan kembali karena deadline sudah lewat. Silakan perbarui tanggal batas lamaran terlebih dahulu.');
        }

        $job->update(['status' => 'published']);
        return back()->with('success', 'Lowongan berhasil diterbitkan.');
    }
}
