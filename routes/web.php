<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\CompanyRegisterController;

// Alumni
use App\Http\Controllers\Alumni\DashboardController;
use App\Http\Controllers\Alumni\ProfileController;
use App\Http\Controllers\Alumni\JobsController;
use App\Http\Controllers\Alumni\ApplicationsController;
use App\Http\Controllers\Alumni\WorkshopsController;
use App\Http\Controllers\Alumni\ScholarshipsController;
use App\Http\Controllers\Alumni\RecommendationsController;
use App\Http\Controllers\Alumni\LeaderboardController;

// Company
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Company\JobsController as CompanyJobsController;
use App\Http\Controllers\Company\ApplicationsController as CompanyApplicationsController;
use App\Http\Controllers\Company\AlumniBrowseController;
use App\Http\Controllers\Company\WorkshopsController as CompanyWorkshopsController;

// College

use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\College\AlumniController as CollegeAlumniController;
use App\Http\Controllers\College\WorkshopsController as CollegeWorkshopsController;
use App\Http\Controllers\College\ScholarshipsController as CollegeScholarshipsController;
use App\Http\Controllers\College\AnnouncementsController as CollegeAnnouncementsController;
use App\Http\Controllers\College\SuccessStoriesController as CollegeSuccessStoriesController;
use App\Http\Controllers\College\ReportsController as CollegeReportsController;
use App\Http\Controllers\College\JobsController as CollegeJobsController;


// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CompanyApprovalsController;

/**
 * =========================
 * Public
 * =========================
 */
Route::view('/', 'home')->name('home');
Route::view('/test-theme', 'test-theme');

/**
 * =========================
 * Auth
 * =========================
 */
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

/**
 * Company Registration (creates company user + company_profile status=pending)
 */
Route::get('/register', [CompanyRegisterController::class, 'show'])->name('register');
Route::post('/register', [CompanyRegisterController::class, 'store'])->name('register.store');

/**
 * =========================
 * Alumni (protected)
 * =========================
 */
Route::prefix('alumni')->middleware(['auth', 'role:alumni'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('alumni.dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('alumni.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('alumni.profile.update');

    Route::get('/jobs', [JobsController::class, 'index'])->name('alumni.jobs');
    Route::get('/jobs/{job}', [JobsController::class, 'show'])->name('alumni.jobs.show');
    Route::post('/jobs/{job}/apply', [JobsController::class, 'apply'])->name('alumni.jobs.apply');
    Route::post('/jobs/{job}/save', [JobsController::class, 'toggleSave'])->name('alumni.jobs.save');

    Route::get('/workshops', [WorkshopsController::class, 'index'])->name('alumni.workshops');
    Route::post('/workshops/{workshop}/register', [WorkshopsController::class, 'register'])->name('alumni.workshops.register');
    Route::post('/workshops/{workshop}/cancel', [WorkshopsController::class, 'cancel'])->name('alumni.workshops.cancel');

    Route::get('/scholarships', [ScholarshipsController::class, 'index'])->name('alumni.scholarships');
    Route::get('/scholarships/{scholarship}', [ScholarshipsController::class, 'show'])->name('alumni.scholarships.show');
    Route::post('/scholarships/{scholarship}/apply', [ScholarshipsController::class, 'apply'])->name('alumni.scholarships.apply');

    Route::get('/recommendations', [RecommendationsController::class, 'index'])->name('alumni.recommendations');
    Route::post('/recommendations', [RecommendationsController::class, 'store'])->name('alumni.recommendations.store');
    Route::delete('/recommendations/{recommendation}', [RecommendationsController::class, 'destroy'])->name('alumni.recommendations.destroy');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('alumni.leaderboard');

    Route::get('/applications', [ApplicationsController::class, 'index'])->name('alumni.applications');
    Route::get('/applications/{type}/{id}', [ApplicationsController::class, 'show'])
        ->whereIn('type', ['jobs','scholarships','workshops'])
        ->name('alumni.applications.show');

    Route::post('/applications/{type}/{id}/withdraw', [ApplicationsController::class, 'withdraw'])
        ->whereIn('type', ['jobs','scholarships','workshops'])
        ->name('alumni.applications.withdraw');
});

/**
 * =========================
 * College (protected)
 * =========================
 */
Route::prefix('college')->middleware(['auth', 'role:college'])->group(function () {

    // Dashboard
    Route::get('/', [CollegeDashboardController::class, 'index'])->name('college.dashboard');

    // Alumni management
    Route::get('/alumni', [CollegeAlumniController::class, 'index'])->name('college.alumni');
    Route::get('/alumni/{alumnus}', [CollegeAlumniController::class, 'show'])->name('college.alumni.show');

    // Workshops CRUD
    Route::get('/workshops', [CollegeWorkshopsController::class, 'index'])->name('college.workshops');
    Route::get('/workshops/create', [CollegeWorkshopsController::class, 'create'])->name('college.workshops.create');
    Route::post('/workshops', [CollegeWorkshopsController::class, 'store'])->name('college.workshops.store');
    Route::get('/workshops/{workshop}/manage', [CollegeWorkshopsController::class, 'manage'])->name('college.workshops.manage');
    Route::post('/workshops/{workshop}/delete', [CollegeWorkshopsController::class, 'destroy'])->name('college.workshops.delete');

    // jobs
    Route::get('/jobs', [CollegeJobsController::class, 'index'])->name('college.jobs');
    Route::post('/jobs/{job}/approve', [CollegeJobsController::class, 'approve'])->name('college.jobs.approve');
    Route::post('/jobs/{job}/reject', [CollegeJobsController::class, 'reject'])->name('college.jobs.reject');
    Route::post('/jobs/{job}/feature', [CollegeJobsController::class, 'toggleFeatured'])->name('college.jobs.feature');
    
    // Scholarships CRUD
    Route::get('/scholarships', [CollegeScholarshipsController::class, 'index'])->name('college.scholarships');
    Route::get('/scholarships/create', [CollegeScholarshipsController::class, 'create'])->name('college.scholarships.create');
    Route::post('/scholarships', [CollegeScholarshipsController::class, 'store'])->name('college.scholarships.store');
    Route::get('/scholarships/{scholarship}', [CollegeScholarshipsController::class, 'show'])->name('college.scholarships.show');
    Route::post('/scholarships/{scholarship}/delete', [CollegeScholarshipsController::class, 'destroy'])->name('college.scholarships.delete');

    // Announcements
    Route::get('/announcements', [CollegeAnnouncementsController::class, 'index'])->name('college.announcements');
    Route::get('/announcements/create', [CollegeAnnouncementsController::class, 'create'])->name('college.announcements.create');
    Route::post('/announcements', [CollegeAnnouncementsController::class, 'store'])->name('college.announcements.store');
    Route::post('/announcements/{announcement}/toggle', [CollegeAnnouncementsController::class, 'toggle'])->name('college.announcements.toggle');

    // Success Stories
    Route::get('/success-stories', [CollegeSuccessStoriesController::class, 'index'])->name('college.successStories');
    Route::get('/success-stories/create', [CollegeSuccessStoriesController::class, 'create'])->name('college.successStories.create');
    Route::post('/success-stories', [CollegeSuccessStoriesController::class, 'store'])->name('college.successStories.store');
    Route::post('/success-stories/{story}/toggle', [CollegeSuccessStoriesController::class, 'toggle'])->name('college.successStories.toggle');

    // Reports
    Route::get('/reports', [CollegeReportsController::class, 'index'])->name('college.reports');
});

/**
 * =========================
 * Company (protected + approved)
 * =========================
 * IMPORTANT:
 * alias company.approved لازم يكون موجود في bootstrap/app.php
 * company_profile.status:
 *   - pending => pending page
 *   - rejected => rejected page
 *   - approved => يسمح بالدخول
 */
Route::prefix('company')->middleware(['auth', 'role:company', 'company.approved'])->group(function () {

    // Dashboard
    Route::get('/', [CompanyDashboardController::class, 'index'])->name('company.dashboard');

    // Jobs (real)
    Route::get('/jobs', [CompanyJobsController::class, 'index'])->name('company.jobs');
    Route::get('/jobs/create', [CompanyJobsController::class, 'create'])->name('company.jobs.create');
    Route::post('/jobs', [CompanyJobsController::class, 'store'])->name('company.jobs.store');

    // Applicants list per job (view only)
    Route::get('/jobs/{job}/applicants', [CompanyJobsController::class, 'applicants'])->name('company.jobs.applicants');

    // Applications (tabs + update status) ✅ route واحدة فقط للحالة
    Route::get('/applications', [CompanyApplicationsController::class, 'index'])->name('company.applications');
    Route::post('/applications/{application}/status', [CompanyApplicationsController::class, 'updateStatus'])
        ->name('company.applications.status');

    // Placeholders (later we make them dynamic)
    Route::get('/alumni', [AlumniBrowseController::class, 'index'])->name('company.alumni');
    Route::get('/alumni/{alumnus}', [AlumniBrowseController::class, 'show'])->name('company.alumni.show');
    Route::get('/workshops', [CompanyWorkshopsController::class, 'index'])->name('company.workshops');
    Route::get('/workshops/create', [CompanyWorkshopsController::class, 'create'])->name('company.workshops.create');
    Route::post('/workshops', [CompanyWorkshopsController::class, 'store'])->name('company.workshops.store');
    Route::get('/workshops/{workshop}', [CompanyWorkshopsController::class, 'manage'])->name('company.workshops.manage');
});

/**
 * =========================
 * Admin (protected)
 * =========================
 */
Route::prefix('admin')->middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Static for now (later we make them dynamic)
    Route::view('/users', 'admin.users')->name('admin.users');
    Route::view('/content', 'admin.content')->name('admin.content');
    Route::view('/reports', 'admin.reports')->name('admin.reports');
    Route::view('/settings', 'admin.settings')->name('admin.settings');
    Route::view('/support', 'admin.support')->name('admin.support');

    // Company approvals (pending/approved/rejected WITHOUT delete)
    Route::get('/company-approvals', [CompanyApprovalsController::class, 'index'])->name('admin.companyApprovals');
    Route::post('/company-approvals/{profile}/approve', [CompanyApprovalsController::class, 'approve'])->name('admin.companyApprovals.approve');
    Route::post('/company-approvals/{profile}/reject', [CompanyApprovalsController::class, 'reject'])->name('admin.companyApprovals.reject');
});
