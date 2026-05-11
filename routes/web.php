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

// Auth Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute Register Tambahan
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Rute Password Reset (Placeholder agar tidak error di view Login)
    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

use App\Http\Controllers\LandingController;

// Landing Pages
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::view('/tentang', 'about')->name('about');
Route::view('/sop', 'sop')->name('sop');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Student-only submission routes
    Route::middleware('role:student')->group(function () {
        // Rute CRUD Dasar
        Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('submissions', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
        
        // Rute Aksi Dokumen & Submit
        Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
        Route::post('submissions/{submission}/upload-document', [SubmissionController::class, 'uploadDocument'])->name('submissions.upload-document');
        Route::delete('submissions/{submission}/documents/{document}', [SubmissionController::class, 'deleteDocument'])->name('submissions.delete-document');
        
        // Rute Download Template
        Route::get('/templates/download', [SubmissionController::class, 'downloadTemplate'])->name('templates.download');

        // Rute Ethical Clearance (EC) Baru
        Route::post('submissions/{submission}/confirm', [SubmissionController::class, 'confirmEcData'])->name('submissions.confirm');
        Route::get('submissions/{submission}/download-ec', [SubmissionController::class, 'downloadEc'])->name('submissions.download-ec');
    });

    // Submission show — accessible by all authenticated roles (auth checked in controller/policy)
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

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