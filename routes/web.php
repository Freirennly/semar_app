<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\DocCheckController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\LandingController;

// Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Student submissions
    Route::resource('submissions', SubmissionController::class)->except(['destroy']);
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
    Route::post('submissions/{submission}/upload-document', [SubmissionController::class, 'uploadDocument'])->name('submissions.upload-document');
    Route::delete('submissions/{submission}/documents/{document}', [SubmissionController::class, 'deleteDocument'])->name('submissions.delete-document');

    // Ketua: assignments
    Route::middleware('role:ketua')->group(function () {
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('assignments/{submission}', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    });

    // Reviewer: reviews
    Route::middleware('role:reviewer')->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{submission}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('reviews/{submission}', [ReviewController::class, 'store'])->name('reviews.store');
    });

    // Sekretariat: doc check + decisions
    Route::middleware('role:sekretariat')->group(function () {
        Route::get('doccheck', [DocCheckController::class, 'index'])->name('doccheck.index');
        Route::get('doccheck/{submission}', [DocCheckController::class, 'show'])->name('doccheck.show');
        Route::post('doccheck/{submission}/approve', [DocCheckController::class, 'approve'])->name('doccheck.approve');
        Route::post('doccheck/{submission}/return', [DocCheckController::class, 'returnToDraft'])->name('doccheck.return');

        Route::get('decisions', [DecisionController::class, 'index'])->name('decisions.index');
        Route::get('decisions/{submission}', [DecisionController::class, 'show'])->name('decisions.show');
        Route::post('decisions/{submission}', [DecisionController::class, 'store'])->name('decisions.store');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
