<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        Job::closeExpiredJobs();

        $query = Job::with(['company', 'category', 'location'])->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($compQuery) use ($search) {
                        $compQuery->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $jobs = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $companies = Company::all();
        $categories = JobCategory::active()->get();
        $locations = JobLocation::active()->get();

        return view('admin.jobs.create', compact('companies', 'categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id'       => 'required|exists:companies,id',
            'title'            => 'required|string|max:255',
            'department'       => 'nullable|string|max:255',
            'category_id'      => 'required|exists:job_categories,id',
            'location_id'      => 'required|exists:job_locations,id',
            'work_setting'     => 'required|in:on_site,remote,hybrid',
            'description'      => 'required|string',
            'requirements'     => 'nullable|string',
            'salary_min'       => 'nullable|numeric|min:0',
            'salary_max'       => 'nullable|numeric|min:0|gte:salary_min',
            'salary_type'      => 'required|in:monthly,yearly,hourly,project',
            'salary_currency'  => 'required|string|max:3',
            'is_salary_visible' => 'boolean',
            'job_type'         => 'required|in:full_time,part_time,contract,freelance,internship,harian_lepas',
            'experience_level' => 'required|string|max:255',
            'education_level'  => 'nullable|in:sd,smp,sma,d3,s1,s2,s3',
            'deadline'         => 'nullable|date',
            'vacancy'          => 'required|integer|min:1',
            'is_featured'      => 'boolean',
            'status'           => 'required|in:draft,published,closed',
            'required_tests'   => 'nullable|array',
            'required_tests.*' => 'in:kraepelin,msdt,papi,disc',
        ]);

        // Jika HR tidak mencentang apa-apa, jadikan array kosong
        $validatedData['required_tests'] = $request->input('required_tests', []);

        Job::create([
            'company_id'       => $request->company_id,
            'title'            => $request->title,
            'department'       => $request->department,
            'work_setting'     => $request->work_setting,
            'slug'             => Str::slug($request->title) . '-' . time(),
            'category_id'      => $request->category_id,
            'location_id'      => $request->location_id,
            'description'      => $request->description,
            'requirements'     => $request->requirements,
            'responsibilities' => null, // Dikosongkan karena tidak ada di form baru
            'salary_min'       => $request->filled('salary_min') ? $request->salary_min : null,
            'salary_max'       => $request->filled('salary_max') ? $request->salary_max : null,
            'salary_type'      => $request->salary_type,
            'salary_currency'  => $request->salary_currency,
            'is_salary_visible' => $request->has('is_salary_visible'),
            'job_type'         => $request->job_type,
            'experience_level' => $request->experience_level,
            'education_level'  => $request->education_level,
            'deadline'         => $request->deadline,
            'vacancy'          => $request->vacancy,
            'status'           => $request->status,
            'is_featured'      => $request->has('is_featured'),
            // Otomatis set is_remote berdasarkan work_setting
            'is_remote'        => in_array($request->work_setting, ['remote', 'hybrid']),
            'required_tests' => $request->input('required_tests', []),
        ]);

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan pekerjaan berhasil dibuat.');
    }

    public function show(Job $job)
    {
        $job->load(['company', 'category', 'location']);
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $companies = Company::all();
        $categories = JobCategory::active()->get();
        $locations = JobLocation::active()->get();

        return view('admin.jobs.edit', compact('job', 'companies', 'categories', 'locations'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'company_id'       => 'required|exists:companies,id',
            'title'            => 'required|string|max:255',
            'department'       => 'nullable|string|max:255',
            'category_id'      => 'required|exists:job_categories,id',
            'location_id'      => 'required|exists:job_locations,id',
            'work_setting'     => 'required|in:on_site,remote,hybrid',
            'description'      => 'required|string',
            'requirements'     => 'nullable|string',
            'salary_min'       => 'nullable|numeric|min:0',
            'salary_max'       => 'nullable|numeric|min:0|gte:salary_min',
            'salary_type'      => 'required|in:monthly,yearly,hourly,project',
            'salary_currency'  => 'required|string|max:3',
            'is_salary_visible' => 'boolean',
            'job_type'         => 'required|in:full_time,part_time,contract,freelance,internship,harian_lepas',
            'experience_level' => 'required|string|max:255',
            'education_level'  => 'nullable|in:sd,smp,sma,d3,s1,s2,s3',
            'deadline'         => 'nullable|date|after:today',
            'vacancy'          => 'required|integer|min:1',
            'is_featured'      => 'boolean',
            'status'           => 'required|in:draft,published,closed,expired',
            'required_tests'   => 'nullable|array',
            'required_tests.*' => 'in:kraepelin,msdt,papi,disc',
        ]);

        $data = $request->all();

        // Pastikan field gaji kosong tetap tersimpan sebagai NULL, bukan string kosong.
        $data['salary_min'] = $request->filled('salary_min') ? $request->salary_min : null;
        $data['salary_max'] = $request->filled('salary_max') ? $request->salary_max : null;

        if ($job->title !== $request->title) {
            $data['slug'] = Str::slug($request->title) . '-' . time();
        }

        // Penanganan Checkbox & Logika Otomatis
        $data['is_featured']       = $request->has('is_featured');
        $data['is_salary_visible'] = $request->has('is_salary_visible');
        $data['is_remote']         = in_array($request->work_setting, ['remote', 'hybrid']); // Logika otomatis

        $data['required_tests']    = $request->input('required_tests', []);

        $job->update($data);

        return redirect()->route('admin.jobs.index')->with('success', 'Data lowongan berhasil diperbarui.');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan pekerjaan berhasil dihapus.');
    }

    public function approve(Job $job)
    {
        $job->update(['status' => 'published']);
        return back()->with('success', 'Lowongan disetujui dan berhasil ditayangkan.');
    }

    public function reject(Job $job)
    {
        $job->update(['status' => 'closed']);
        return back()->with('success', 'Lowongan ditolak dan ditutup.');
    }
}
