<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use Illuminate\Http\Request;

class PublicJobController extends Controller
{
    public function index(Request $request)
    {
        Job::closeExpiredJobs();

        $query = Job::with('company')->active();

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%')
                  ->orWhereHas('company', function ($q2) use ($request) {
                      $q2->where('company_name', 'like', '%' . $request->keyword . '%');
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        $jobs = $query->latest()->paginate(12);
        
        $categories = JobCategory::orderBy('name')->get();
        $locations = JobLocation::orderBy('name')->get();

        return view('public.jobs.index', compact('jobs', 'categories', 'locations'));
    }

    public function show(Job $job)
    {
        Job::closeExpiredJobs();

        if (!$job->isActive()) {
            abort(404);
        }

        $job->increment('views');
        
        return view('public.jobs.show', compact('job'));
    }
}
