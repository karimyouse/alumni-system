<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Alumni\JobsController;
use App\Http\Controllers\Alumni\ApplicationsController;
use App\Http\Controllers\Alumni\WorkshopsController;
use App\Http\Controllers\Alumni\ScholarshipsController;
use App\Http\Controllers\Alumni\RecommendationsController;
use App\Http\Controllers\Alumni\ProfileController;
use App\Http\Controllers\Alumni\DashboardController;

Route::view('/', 'home')->name('home');
Route::view('/test-theme', 'test-theme');


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');


Route::view('/register', 'auth.register-company')->name('register');


Route::prefix('alumni')->middleware(['auth', 'role:alumni'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('alumni.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('alumni.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('alumni.profile.update');


    Route::get('/jobs', [JobsController::class, 'index'])->name('alumni.jobs');
    Route::post('/jobs/{job}/apply', [JobsController::class, 'apply'])->name('alumni.jobs.apply');
    Route::get('/applications', [ApplicationsController::class, 'index'])->name('alumni.applications');
    Route::get('/applications/{type}/{id}', [ApplicationsController::class, 'show'])
    ->whereIn('type', ['jobs','scholarships','workshops'])
    ->name('alumni.applications.show');
    Route::post('/applications/{type}/{id}/withdraw', [ApplicationsController::class, 'withdraw'])
    ->whereIn('type', ['jobs','scholarships','workshops'])
    ->name('alumni.applications.withdraw');
    Route::view('/workshops', 'alumni.workshops')->name('alumni.workshops');
    Route::get('/scholarships', [ScholarshipsController::class, 'index'])->name('alumni.scholarships');
    Route::get('/scholarships/{scholarship}', [ScholarshipsController::class, 'show'])->name('alumni.scholarships.show');
    Route::post('/scholarships/{scholarship}/apply', [ScholarshipsController::class, 'apply'])->name('alumni.scholarships.apply');
    Route::get('/recommendations', [RecommendationsController::class, 'index'])->name('alumni.recommendations');
    Route::post('/recommendations', [RecommendationsController::class, 'store'])->name('alumni.recommendations.store');
    Route::delete('/recommendations/{recommendation}', [RecommendationsController::class, 'destroy'])->name('alumni.recommendations.destroy');
    Route::view('/leaderboard', 'alumni.leaderboard')->name('alumni.leaderboard');
    Route::get('/applications', [ApplicationsController::class, 'index'])->name('alumni.applications');
    Route::post('/jobs/{job}/save', [JobsController::class, 'toggleSave'])->name('alumni.jobs.save');
    Route::get('/workshops', [WorkshopsController::class, 'index'])->name('alumni.workshops');
    Route::post('/workshops/{workshop}/register', [WorkshopsController::class, 'register'])->name('alumni.workshops.register');
    Route::post('/workshops/{workshop}/cancel', [WorkshopsController::class, 'cancel'])->name('alumni.workshops.cancel');
});


Route::prefix('college')->middleware(['auth', 'role:college'])->group(function () {
    Route::view('/', 'college.index')->name('college.dashboard');
    Route::view('/alumni', 'college.alumni-management')->name('college.alumni');
    Route::view('/workshops', 'college.workshops')->name('college.workshops');
    Route::view('/jobs', 'college.jobs')->name('college.jobs');
    Route::view('/announcements', 'college.announcements')->name('college.announcements');
    Route::view('/scholarships', 'college.scholarships')->name('college.scholarships');
    Route::view('/success-stories', 'college.success-stories')->name('college.successStories');
    Route::view('/reports', 'college.reports')->name('college.reports');
});


Route::prefix('company')->middleware(['auth', 'role:company'])->group(function () {
    Route::view('/', 'company.index')->name('company.dashboard');
    Route::view('/jobs', 'company.jobs')->name('company.jobs');
    Route::view('/alumni', 'company.alumni-browse')->name('company.alumni');
    Route::view('/applications', 'company.applications')->name('company.applications');
    Route::view('/workshops', 'company.workshops')->name('company.workshops');
});


Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/', 'admin.index')->name('admin.dashboard');
    Route::view('/users', 'admin.users')->name('admin.users');
    Route::view('/content', 'admin.content')->name('admin.content');
    Route::view('/reports', 'admin.reports')->name('admin.reports');
    Route::view('/settings', 'admin.settings')->name('admin.settings');
    Route::view('/support', 'admin.support')->name('admin.support');
});
