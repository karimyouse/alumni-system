<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\SupportTicketsController;
use App\Http\Controllers\SupportTrackController;
use App\Http\Controllers\HomeController;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\CompanyRegisterController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Auth\PasswordResetController;

// Alumni
use App\Http\Controllers\Alumni\DashboardController as AlumniDashboardController;
use App\Http\Controllers\Alumni\ProfileController;
use App\Http\Controllers\Alumni\JobsController as AlumniJobsController;
use App\Http\Controllers\Alumni\ApplicationsController as AlumniApplicationsController;
use App\Http\Controllers\Alumni\WorkshopsController as AlumniWorkshopsController;
use App\Http\Controllers\Alumni\ScholarshipsController as AlumniScholarshipsController;
use App\Http\Controllers\Alumni\RecommendationsController as AlumniRecommendationsController;
use App\Http\Controllers\Alumni\LeaderboardController as AlumniLeaderboardController;

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
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/test-theme', 'test-theme');


Route::match(['GET', 'POST'], '/lang', function (Request $request) {
    $locale = $request->input('locale')
        ?? $request->query('locale')
        ?? $request->input('lang')
        ?? $request->query('lang');

    if (!$locale) {
        $locale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    }

    if (!in_array($locale, ['en', 'ar'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);
    Cookie::queue(Cookie::forever('locale', $locale));

    $redirectTo = $request->input('redirect_to') ?? $request->query('redirect_to');
    $fallback = '/';

    if (is_string($redirectTo) && trim($redirectTo) !== '') {
        $redirectTo = trim($redirectTo);
        $appUrl = rtrim((string) config('app.url', ''), '/');
        $origin = rtrim($request->getSchemeAndHttpHost(), '/');

        if (str_starts_with($redirectTo, '/')) {
            return redirect($redirectTo);
        }

        if ($appUrl !== '' && str_starts_with($redirectTo, $appUrl)) {
            $path = substr($redirectTo, strlen($appUrl));
            return redirect($path !== '' ? $path : $fallback);
        }

        if ($origin !== '' && str_starts_with($redirectTo, $origin)) {
            $path = substr($redirectTo, strlen($origin));
            return redirect($path !== '' ? $path : $fallback);
        }
    }

    $previous = url()->previous();
    $appUrl = rtrim((string) config('app.url', ''), '/');
    $origin = rtrim($request->getSchemeAndHttpHost(), '/');

    if (is_string($previous) && str_starts_with($previous, '/')) {
        return redirect($previous);
    }

    if ($appUrl !== '' && is_string($previous) && str_starts_with($previous, $appUrl)) {
        $path = substr($previous, strlen($appUrl));
        return redirect($path !== '' ? $path : $fallback);
    }

    if ($origin !== '' && is_string($previous) && str_starts_with($previous, $origin)) {
        $path = substr($previous, strlen($origin));
        return redirect($path !== '' ? $path : $fallback);
    }

    return redirect($fallback);
})->name('lang.switch');



Route::get('/support/track', [SupportTrackController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('support.track.show');

/**
 * Auth
 */
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');


Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])
    ->middleware('throttle:30,1')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:10,1')
    ->name('password.email');


Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('throttle:30,1')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:10,1')
    ->name('password.update');


Route::get('/support/request', [SupportRequestController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('support.request.show');

Route::post('/support/request', [SupportRequestController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('support.request.store');


Route::get('/register', [CompanyRegisterController::class, 'show'])->name('register');
Route::post('/register', [CompanyRegisterController::class, 'store'])->name('register.store');



Route::middleware('auth')->group(function () {
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'read'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationsController::class, 'readAll'])
        ->name('notifications.readAll');
});



Route::middleware('auth')->prefix('support')->group(function () {
    Route::get('/tickets', [SupportTicketsController::class, 'index'])->name('support.tickets');
    Route::get('/tickets/{ticket}', [SupportTicketsController::class, 'show'])->name('support.tickets.show');
});

/**
 * Alumni
 */
Route::prefix('alumni')->middleware(['auth', 'role:alumni'])->group(function () {

    Route::get('/', [AlumniDashboardController::class, 'index'])->name('alumni.dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('alumni.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('alumni.profile.update');

    Route::get('/jobs', [AlumniJobsController::class, 'index'])->name('alumni.jobs');
    Route::get('/jobs/{job}', [AlumniJobsController::class, 'show'])->name('alumni.jobs.show');
    Route::post('/jobs/{job}/apply', [AlumniJobsController::class, 'apply'])->name('alumni.jobs.apply');
    Route::post('/jobs/{job}/save', [AlumniJobsController::class, 'toggleSave'])->name('alumni.jobs.save');

    Route::get('/workshops', [AlumniWorkshopsController::class, 'index'])->name('alumni.workshops');
    Route::post('/workshops/{workshop}/register', [AlumniWorkshopsController::class, 'register'])->name('alumni.workshops.register');
    Route::post('/workshops/{workshop}/cancel', [AlumniWorkshopsController::class, 'cancel'])->name('alumni.workshops.cancel');

    Route::get('/scholarships', [AlumniScholarshipsController::class, 'index'])->name('alumni.scholarships');
    Route::get('/scholarships/{scholarship}', [AlumniScholarshipsController::class, 'show'])->name('alumni.scholarships.show');
    Route::post('/scholarships/{scholarship}/apply', [AlumniScholarshipsController::class, 'apply'])->name('alumni.scholarships.apply');

    Route::get('/recommendations', [AlumniRecommendationsController::class, 'index'])->name('alumni.recommendations');
    Route::post('/recommendations', [AlumniRecommendationsController::class, 'store'])->name('alumni.recommendations.store');
    Route::delete('/recommendations/{recommendation}', [AlumniRecommendationsController::class, 'destroy'])->name('alumni.recommendations.destroy');

    Route::get('/leaderboard', [AlumniLeaderboardController::class, 'index'])->name('alumni.leaderboard');

    Route::get('/applications', [AlumniApplicationsController::class, 'index'])->name('alumni.applications');
    Route::get('/applications/{type}/{id}', [AlumniApplicationsController::class, 'show'])
        ->whereIn('type', ['jobs','scholarships','workshops'])
        ->name('alumni.applications.show');

    Route::post('/applications/{type}/{id}/withdraw', [AlumniApplicationsController::class, 'withdraw'])
        ->whereIn('type', ['jobs','scholarships','workshops'])
        ->name('alumni.applications.withdraw');
});

/**
 * College
 */
Route::prefix('college')->middleware(['auth', 'role:college'])->group(function () {

    Route::get('/', [CollegeDashboardController::class, 'index'])->name('college.dashboard');

    Route::get('/alumni', [CollegeAlumniController::class, 'index'])->name('college.alumni');
    Route::get('/alumni/{alumnus}', [CollegeAlumniController::class, 'show'])->name('college.alumni.show');

    Route::get('/workshops', [CollegeWorkshopsController::class, 'index'])->name('college.workshops');
    Route::get('/workshops/create', [CollegeWorkshopsController::class, 'create'])->name('college.workshops.create');
    Route::post('/workshops', [CollegeWorkshopsController::class, 'store'])->name('college.workshops.store');
    Route::get('/workshops/{workshop}/edit', [CollegeWorkshopsController::class, 'edit'])->name('college.workshops.edit');
    Route::post('/workshops/{workshop}/update', [CollegeWorkshopsController::class, 'update'])->name('college.workshops.update');
    Route::get('/workshops/{workshop}/manage', [CollegeWorkshopsController::class, 'manage'])->name('college.workshops.manage');
    Route::post('/workshops/{workshop}/delete', [CollegeWorkshopsController::class, 'destroy'])->name('college.workshops.delete');
    Route::post('/workshops/{workshop}/approve', [CollegeWorkshopsController::class, 'approve'])->name('college.workshops.approve');
    Route::post('/workshops/{workshop}/reject', [CollegeWorkshopsController::class, 'reject'])->name('college.workshops.reject');

    Route::get('/jobs', [CollegeJobsController::class, 'index'])->name('college.jobs');
    Route::get('/jobs/create', [CollegeJobsController::class, 'create'])->name('college.jobs.create');
    Route::post('/jobs', [CollegeJobsController::class, 'store'])->name('college.jobs.store');
    Route::get('/jobs/{job}/edit', [CollegeJobsController::class, 'edit'])->name('college.jobs.edit');
    Route::post('/jobs/{job}/update', [CollegeJobsController::class, 'update'])->name('college.jobs.update');
    Route::post('/jobs/{job}/delete', [CollegeJobsController::class, 'destroy'])->name('college.jobs.delete');
    Route::get('/jobs/{job}/applicants', [CollegeJobsController::class, 'applicants'])->name('college.jobs.applicants');
    Route::post('/jobs/{job}/approve', [CollegeJobsController::class, 'approve'])->name('college.jobs.approve');
    Route::post('/jobs/{job}/reject', [CollegeJobsController::class, 'reject'])->name('college.jobs.reject');
    Route::post('/jobs/{job}/feature', [CollegeJobsController::class, 'toggleFeatured'])->name('college.jobs.feature');

    Route::get('/scholarships', [CollegeScholarshipsController::class, 'index'])->name('college.scholarships');
    Route::get('/scholarships/create', [CollegeScholarshipsController::class, 'create'])->name('college.scholarships.create');
    Route::post('/scholarships', [CollegeScholarshipsController::class, 'store'])->name('college.scholarships.store');

    Route::get('/scholarships/{scholarship}/edit', [CollegeScholarshipsController::class, 'edit'])->name('college.scholarships.edit');
    Route::post('/scholarships/{scholarship}/update', [CollegeScholarshipsController::class, 'update'])->name('college.scholarships.update');

    Route::get('/scholarships/{scholarship}/applicants', [CollegeScholarshipsController::class, 'applicants'])->name('college.scholarships.applicants');
    Route::post('/scholarships/{scholarship}/delete', [CollegeScholarshipsController::class, 'destroy'])->name('college.scholarships.delete');
    Route::get('/announcements', [CollegeAnnouncementsController::class, 'index'])->name('college.announcements');
    Route::get('/announcements/create', [CollegeAnnouncementsController::class, 'create'])->name('college.announcements.create');
    Route::post('/announcements', [CollegeAnnouncementsController::class, 'store'])->name('college.announcements.store');

    Route::get('/announcements/{announcement}/edit', [CollegeAnnouncementsController::class, 'edit'])->name('college.announcements.edit');
    Route::post('/announcements/{announcement}/update', [CollegeAnnouncementsController::class, 'update'])->name('college.announcements.update');
    Route::post('/announcements/{announcement}/toggle', [CollegeAnnouncementsController::class, 'toggle'])->name('college.announcements.toggle');
    Route::post('/announcements/{announcement}/delete', [CollegeAnnouncementsController::class, 'destroy'])->name('college.announcements.delete');

    Route::get('/success-stories', [CollegeSuccessStoriesController::class, 'index'])->name('college.successStories');
    Route::get('/success-stories/create', [CollegeSuccessStoriesController::class, 'create'])->name('college.successStories.create');
    Route::post('/success-stories', [CollegeSuccessStoriesController::class, 'store'])->name('college.successStories.store');
    Route::get('/success-stories/{story}/edit', [CollegeSuccessStoriesController::class, 'edit'])->name('college.successStories.edit');
    Route::post('/success-stories/{story}/update', [CollegeSuccessStoriesController::class, 'update'])->name('college.successStories.update');
    Route::post('/success-stories/{story}/toggle', [CollegeSuccessStoriesController::class, 'toggle'])->name('college.successStories.toggle');
    Route::post('/success-stories/{story}/delete', [CollegeSuccessStoriesController::class, 'destroy'])->name('college.successStories.delete');

    Route::get('/reports', [CollegeReportsController::class, 'index'])->name('college.reports');
});

/**
 * Company
 */
Route::prefix('company')->middleware(['auth', 'role:company', 'company.approved'])->group(function () {

    Route::get('/', [CompanyDashboardController::class, 'index'])->name('company.dashboard');

    Route::get('/jobs', [CompanyJobsController::class, 'index'])->name('company.jobs');
    Route::get('/jobs/create', [CompanyJobsController::class, 'create'])->name('company.jobs.create');
    Route::post('/jobs', [CompanyJobsController::class, 'store'])->name('company.jobs.store');
    Route::get('/jobs/{job}/edit', [CompanyJobsController::class, 'edit'])->name('company.jobs.edit');
    Route::post('/jobs/{job}/update', [CompanyJobsController::class, 'update'])->name('company.jobs.update');
    Route::post('/jobs/{job}/delete', [CompanyJobsController::class, 'destroy'])->name('company.jobs.delete');
    Route::get('/jobs/{job}/applicants', [CompanyJobsController::class, 'applicants'])->name('company.jobs.applicants');

    Route::get('/applications', [CompanyApplicationsController::class, 'index'])->name('company.applications');
    Route::post('/applications/{application}/status', [CompanyApplicationsController::class, 'updateStatus'])->name('company.applications.status');

    Route::get('/alumni', [AlumniBrowseController::class, 'index'])->name('company.alumni');
    Route::get('/alumni/{alumnus}', [AlumniBrowseController::class, 'show'])->name('company.alumni.show');

    Route::get('/workshops', [CompanyWorkshopsController::class, 'index'])->name('company.workshops');
    Route::get('/workshops/create', [CompanyWorkshopsController::class, 'create'])->name('company.workshops.create');
    Route::post('/workshops', [CompanyWorkshopsController::class, 'store'])->name('company.workshops.store');
    Route::get('/workshops/{workshop}/edit', [CompanyWorkshopsController::class, 'edit'])->name('company.workshops.edit');
    Route::post('/workshops/{workshop}/update', [CompanyWorkshopsController::class, 'update'])->name('company.workshops.update');
    Route::post('/workshops/{workshop}/delete', [CompanyWorkshopsController::class, 'destroy'])->name('company.workshops.delete');
    Route::get('/workshops/{workshop}', [CompanyWorkshopsController::class, 'manage'])->name('company.workshops.manage');
});

/**
 * Admin
 */
Route::prefix('admin')->middleware(['auth', 'role:admin,super_admin'])->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users', [AdminUsersController::class, 'index'])->name('admin.users');
    Route::get('/users/{user}', [AdminUsersController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{user}/role', [AdminUsersController::class, 'updateRole'])->name('admin.users.role');

    Route::post('/users/{user}/toggle-suspend', [AdminUsersController::class, 'toggleSuspend'])->name('admin.users.toggleSuspend');
    Route::post('/users/{user}/suspend', [AdminUsersController::class, 'toggleSuspend'])->name('admin.users.suspend');

    Route::get('/content', [AdminContentController::class, 'index'])->name('admin.content');
    Route::post('/content/{type}/{id}/approve', [AdminContentController::class, 'approve'])->name('admin.content.approve');
    Route::post('/content/{type}/{id}/reject', [AdminContentController::class, 'reject'])->name('admin.content.reject');

    Route::get('/reports', [AdminReportsController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export-excel', [AdminReportsController::class, 'exportExcel'])->name('admin.reports.exportExcel');
    Route::get('/reports/export', [AdminReportsController::class, 'exportExcel'])->name('admin.reports.export');

    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');

    Route::get('/support', [AdminSupportController::class, 'index'])->name('admin.support');
    Route::post('/support/{ticket}/status', [AdminSupportController::class, 'updateStatus'])->name('admin.support.status');
    Route::post('/support/{ticket}/respond', [AdminSupportController::class, 'respond'])->name('admin.support.respond');
    Route::post('/support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('admin.support.reply');

    Route::get('/company-approvals', [CompanyApprovalsController::class, 'index'])->name('admin.companyApprovals');
    Route::post('/company-approvals/{profile}/approve', [CompanyApprovalsController::class, 'approve'])->name('admin.companyApprovals.approve');
    Route::post('/company-approvals/{profile}/reject', [CompanyApprovalsController::class, 'reject'])->name('admin.companyApprovals.reject');
});
