<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Seeker\DashboardController as SeekerDashboardController;
use App\Http\Controllers\Company\Kraepelin\KraepelinController;
use App\Http\Controllers\ProfileController;

// Auth Routes (Breeze)
require __DIR__ . '/auth.php';

// ------------------------------------------------------------------
// Public Routes
// ------------------------------------------------------------------
Route::get('/', function () {
    \App\Models\Job::closeExpiredJobs();
    $jobs = \App\Models\Job::with('company')->active()->latest()->paginate(12);
    $company = \App\Models\Company::first();
    return view('welcome', compact('jobs', 'company'));
});

Route::controller(\App\Http\Controllers\PublicJobController::class)->group(function () {
    Route::get('/jobs', 'index')->name('public.jobs.index');
    Route::get('/job/{job}', 'show')->name('public.jobs.show');
});

// ------------------------------------------------------------------
// Dashboard Redirect & Shared Auth Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isCompany()) return redirect()->route('company.dashboard');
        if ($user->isSeeker()) return redirect()->route('seeker.dashboard');
        return redirect('/');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ------------------------------------------------------------------
// Admin Routes (Role: Admin)
// ------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [AdminDashboardController::class, 'statistics'])->name('statistics');

    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Company Management
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class);
    Route::post('companies/{company}/toggle-verification', [\App\Http\Controllers\Admin\CompanyController::class, 'toggleVerification'])->name('companies.toggle-verification');

    // Job Management
    Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
    Route::post('jobs/{job}/approve', [\App\Http\Controllers\Admin\JobController::class, 'approve'])->name('jobs.approve');
    Route::post('jobs/{job}/reject', [\App\Http\Controllers\Admin\JobController::class, 'reject'])->name('jobs.reject');

    // Category & Location Management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);

    // Application Management
    Route::resource('applications', \App\Http\Controllers\Admin\ApplicationController::class);
    Route::post('applications/{application}/status', [\App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::get('applications/{application}/download-cv', [\App\Http\Controllers\Admin\ApplicationController::class, 'downloadCv'])->name('applications.download-cv');
    
    // Export PDF Hasil Psikotes untuk Admin (PERBAIKAN BUG 3)
    Route::get('applications/{application}/kraepelin-pdf', [\App\Http\Controllers\Company\Kraepelin\KraepelinController::class, 'exportPdf'])->name('applications.kraepelin-pdf');
    Route::get('applications/{application}/msdt-pdf', [\App\Http\Controllers\Seeker\MsdtController::class, 'exportPdf'])->name('applications.msdt-pdf');
    Route::get('applications/{application}/papi-pdf', [\App\Http\Controllers\Seeker\PapiController::class, 'exportPdf'])->name('applications.papi-pdf');
    Route::get('applications/{application}/disc-pdf', [\App\Http\Controllers\Seeker\DiscController::class, 'exportPdf'])->name('applications.disc-pdf');
    // Route legacy jika admin masih mengakses dari luar tab
    Route::get('kraepelin/{id}', [\App\Http\Controllers\Company\Kraepelin\KraepelinController::class, 'exportPdf'])->name('kraepelin.show');

    // Reports & Settings
    Route::get('reports/jobs', [\App\Http\Controllers\Admin\ReportController::class, 'jobs'])->name('reports.jobs');
    Route::get('reports/applications', [\App\Http\Controllers\Admin\ReportController::class, 'applications'])->name('reports.applications');
    Route::get('reports/users', [\App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

// ------------------------------------------------------------------
// Company Routes (Role: Company)
// ------------------------------------------------------------------
Route::prefix('company')->name('company.')->middleware(['auth', 'verified', 'role:company'])->group(function () {
    Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');

    // Profile & Jobs
    Route::get('/settings', [\App\Http\Controllers\Company\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings', [\App\Http\Controllers\Company\ProfileController::class, 'update'])->name('profile.update');
    Route::resource('jobs', \App\Http\Controllers\Company\JobController::class);
    Route::post('jobs/{job}/publish', [\App\Http\Controllers\Company\JobController::class, 'publish'])->name('jobs.publish');
    Route::post('jobs/{job}/close', [\App\Http\Controllers\Company\JobController::class, 'close'])->name('jobs.close');

    // Applications & Downloads
    Route::get('applications', [\App\Http\Controllers\Company\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [\App\Http\Controllers\Company\ApplicationController::class, 'show'])->name('applications.show');
    Route::put('applications/{application}/status', [\App\Http\Controllers\Company\ApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::get('applications/{application}/download-cv', [\App\Http\Controllers\Company\ApplicationController::class, 'downloadCv'])->name('applications.download-cv');
    Route::get('applications/{application}/download-cover', [\App\Http\Controllers\Company\ApplicationController::class, 'downloadCover'])->name('applications.download-cover');

    // View Psychological Results Data
    Route::get('applications/{application}/psychological-results', [\App\Http\Controllers\Company\ApplicationController::class, 'showPsychologicalResults'])->name('applications.psychological.results');

    // Export PDF Hasil Psikotes untuk Company (PERBAIKAN BUG 1: Menghapus Prefix 'company.' yg ganda & menambah disc)
    Route::get('applications/{application}/kraepelin-pdf', [KraepelinController::class, 'exportPdf'])->name('applications.kraepelin-pdf');
    Route::get('applications/{application}/msdt-pdf', [\App\Http\Controllers\Seeker\MsdtController::class, 'exportPdf'])->name('applications.msdt-pdf');
    Route::get('applications/{application}/papi-pdf', [\App\Http\Controllers\Seeker\PapiController::class, 'exportPdf'])->name('applications.papi-pdf');
    Route::get('applications/{application}/disc-pdf', [\App\Http\Controllers\Seeker\DiscController::class, 'exportPdf'])->name('applications.disc-pdf');
});

// ------------------------------------------------------------------
// Seeker Routes (Role: Seeker)
// ------------------------------------------------------------------
Route::prefix('seeker')->name('seeker.')->middleware(['auth', 'verified', 'role:seeker'])->group(function () {
    Route::get('/dashboard', [SeekerDashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Seeker\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Seeker\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/education', [\App\Http\Controllers\Seeker\ProfileController::class, 'storeEducation'])->name('profile.education.store');
    Route::delete('/profile/education/{education}', [\App\Http\Controllers\Seeker\ProfileController::class, 'destroyEducation'])->name('profile.education.destroy');
    Route::post('/profile/experience', [\App\Http\Controllers\Seeker\ProfileController::class, 'storeExperience'])->name('profile.experience.store');
    Route::delete('/profile/experience/{experience}', [\App\Http\Controllers\Seeker\ProfileController::class, 'destroyExperience'])->name('profile.experience.destroy');
    Route::post('/profile/skill', [\App\Http\Controllers\Seeker\ProfileController::class, 'storeSkill'])->name('profile.skill.store');
    Route::delete('/profile/skill/{skill}', [\App\Http\Controllers\Seeker\ProfileController::class, 'destroySkill'])->name('profile.skill.destroy');
    Route::post('/profile/certificate', [\App\Http\Controllers\Seeker\ProfileController::class, 'storeCertificate'])->name('profile.certificate.store');
    Route::delete('/profile/certificate/{certificate}', [\App\Http\Controllers\Seeker\ProfileController::class, 'destroyCertificate'])->name('profile.certificate.destroy');

    // Jobs & Applications
    Route::get('/jobs', [\App\Http\Controllers\Seeker\JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [\App\Http\Controllers\Seeker\JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/save', [\App\Http\Controllers\Seeker\JobController::class, 'save'])->name('jobs.save');
    Route::delete('/jobs/{job}/unsave', [\App\Http\Controllers\Seeker\JobController::class, 'unsave'])->name('jobs.unsave');
    Route::get('/jobs/{job}/apply', [\App\Http\Controllers\Seeker\JobController::class, 'showApplyForm'])->name('jobs.apply.form');
    Route::post('/jobs/{job}/submit', [\App\Http\Controllers\Seeker\JobController::class, 'submitApplication'])->name('jobs.apply.submit');
    Route::get('/applications', [\App\Http\Controllers\Seeker\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Seeker\ApplicationController::class, 'show'])->name('applications.show');
    Route::delete('/applications/{application}', [\App\Http\Controllers\Seeker\ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::get('/saved-jobs', [\App\Http\Controllers\Seeker\SavedJobController::class, 'index'])->name('saved-jobs.index');

    // ---------------- TES PSIKOLOGI ----------------

    // Kraepelin Test (PERBAIKAN BUG 2: Menghapus route duplikat yang error)
    Route::prefix('kraepelin-test')->name('kraepelin.')->group(function () {
        Route::get('/{application}/instructions', [KraepelinController::class, 'showInstructions'])->name('instructions');
        Route::get('/{application}/start', [KraepelinController::class, 'startTest'])->name('start');
        Route::post('/{testId}/submit', [KraepelinController::class, 'submitTest'])->name('submit');
        Route::get('/{application}/completed', [KraepelinController::class, 'showCompleted'])->name('completed');
    });

    // MSDT Test
    Route::prefix('msdt-test')->name('msdt.')->group(function () {
        Route::get('/{application}/instructions', [\App\Http\Controllers\Seeker\MsdtController::class, 'showInstructions'])->name('instructions');
        Route::get('/{application}/start', [\App\Http\Controllers\Seeker\MsdtController::class, 'startTest'])->name('start');
        Route::post('/{testId}/submit', [\App\Http\Controllers\Seeker\MsdtController::class, 'submitTest'])->name('submit');
        Route::post('/{testId}/autosave', [\App\Http\Controllers\Seeker\MsdtController::class, 'autoSave'])->name('autosave');
        Route::get('/{application}/completed', [\App\Http\Controllers\Seeker\MsdtController::class, 'showCompleted'])->name('completed');
    });

    // Papi Kostick Test
    Route::prefix('papi-test')->name('papi.')->group(function () {
        Route::get('/{application}/instructions', [\App\Http\Controllers\Seeker\PapiController::class, 'showInstructions'])->name('instructions');
        Route::get('/{application}/start', [\App\Http\Controllers\Seeker\PapiController::class, 'startTest'])->name('start');
        Route::post('/{testId}/submit', [\App\Http\Controllers\Seeker\PapiController::class, 'submitTest'])->name('submit');
        Route::post('/{testId}/autosave', [\App\Http\Controllers\Seeker\PapiController::class, 'autoSave'])->name('autosave');
        Route::get('/{application}/completed', [\App\Http\Controllers\Seeker\PapiController::class, 'showCompleted'])->name('completed');
    });

    // DISC Test
    Route::prefix('disc-test')->name('disc.')->group(function () {
        Route::get('/{application}/instructions', [\App\Http\Controllers\Seeker\DiscController::class, 'showInstructions'])->name('instructions');
        Route::get('/{application}/start', [\App\Http\Controllers\Seeker\DiscController::class, 'startTest'])->name('start');
        Route::post('/{testId}/submit', [\App\Http\Controllers\Seeker\DiscController::class, 'submitTest'])->name('submit');
        Route::post('/{testId}/autosave', [\App\Http\Controllers\Seeker\DiscController::class, 'autoSave'])->name('autosave');
        Route::get('/{application}/completed', [\App\Http\Controllers\Seeker\DiscController::class, 'showCompleted'])->name('completed');
    });
});