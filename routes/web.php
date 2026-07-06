<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\DocCheckController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EthicalClearanceController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\ReviewerController;
use App\Http\Controllers\Admin\SecretariatController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute Register Tambahan (Dinonaktifkan)
    // Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Route::post('/register', [AuthController::class, 'register']);

    // Rute Password Reset (Placeholder agar tidak error di view Login)
    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Landing Pages
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::view('/tentang', 'about')->name('about');
Route::view('/sop', 'sop')->name('sop');
Route::get('verify/{token}', [VerificationController::class, 'show'])->name('verification.verify')->middleware(['throttle:verification', 'signed']);

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // ═════════════════════════════════════════════════════════════════════════
    // GLOBAL ROUTES (Aksesibel oleh seluruh role yang terautentikasi)
    // ═════════════════════════════════════════════════════════════════════════
    // 1. Rute Unduh File Template Dokumen Persyaratan (RESTful)
    Route::get('templates/{template}/download', [SubmissionController::class, 'downloadTemplate'])->name('submissions.download-template');
    
    // 2. Rute Pratinjau Dokumen PDF secara Inline (Scoped Binding — IDOR Prevention)
    Route::get('submissions/{submission}/documents/{document}/view', [SubmissionController::class, 'viewDocument'])
        ->name('submissions.view-document')
        ->scopeBindings();

    // Rute Fullboard Schedule (Aksesibel oleh Ketua, Reviewer, Sekretariat)
    Route::middleware('role:ketua|reviewer|sekretariat')->group(function () {
        Route::get('fullboard', [\App\Http\Controllers\FullboardMeetingController::class, 'index'])->name('fullboard.index');
    });

    // Student-only submission routes
    Route::middleware('role:student')->group(function () {
        // Rute CRUD Dasar
        Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('submissions', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
        
        // Rute Aksi Dokumen & Submit (dengan Throttle)
        Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit')->middleware('throttle:submit');
        Route::post('submissions/{submission}/upload-document', [SubmissionController::class, 'uploadDocument'])->name('submissions.upload-document')->middleware('throttle:upload-document');
        Route::delete('submissions/{submission}/documents/{document}', [SubmissionController::class, 'deleteDocument'])->name('submissions.delete-document')->scopeBindings();
        
        // Rute Ethical Clearance (EC) Konfirmasi & Download
        Route::post('submissions/{submission}/confirm', [SubmissionController::class, 'confirmEcData'])->name('submissions.confirm');
        Route::post('submissions/{submission}/request-revision', [SubmissionController::class, 'requestEcRevision'])->name('submissions.request-revision');
        Route::get('submissions/{submission}/preview-draft', [SubmissionController::class, 'previewDraftEc'])->name('submissions.preview-draft');
        Route::get('submissions/{submission}/download-ec', [SubmissionController::class, 'downloadEc'])->name('submissions.download-ec');

        // Halaman Utama Menu Kelola Sertifikat Ethical Clearance Sisi Mahasiswa
        Route::get('ethical-clearance', [EthicalClearanceController::class, 'index'])->name('ethical-clearance.index');
    });

    // Submission show — accessible by all authenticated roles (policy-controlled)
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('submissions/{submission}/certificate', [SubmissionController::class, 'downloadCertificate'])->name('submissions.certificate')->middleware('throttle:downloads');

    // Ketua: signing overview, and monitoring modules
    Route::middleware('role:ketua')->group(function () {
        Route::post('submissions/{submission}/sign', [SubmissionController::class, 'sign'])->name('submissions.sign');
        Route::get('submissions/{submission}/preview-final', [SubmissionController::class, 'previewFinalEc'])->name('submissions.preview-final');
        
        // Rute Baru: Halaman Pemantauan Kerja Reviewer & Riwayat Keputusan Ketua KEP
        Route::get('chairman/monitoring', function() {
            return view('dashboard.monitoring'); 
        })->name('chairman.monitoring');
    });

    // Reviewer: reviews (dengan Throttle pada submit review)
    Route::middleware('role:reviewer')->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{submission}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('reviews/{submission}', [ReviewController::class, 'store'])->name('reviews.store')->middleware('throttle:review');
    });

    // Sekretariat: doc check + decisions + assignments
    Route::middleware('role:sekretariat')->group(function () {
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Modul Verifikasi Kelengkapan Berkas Lapangan (dengan Throttle)
        Route::get('doccheck', [DocCheckController::class, 'index'])->name('doccheck.index');
        Route::get('doccheck/{submission}', [DocCheckController::class, 'show'])->name('doccheck.show');
        Route::post('doccheck/{submission}/approve', [DocCheckController::class, 'approve'])->name('doccheck.approve')->middleware('throttle:approve');
        Route::post('doccheck/{submission}/return', [DocCheckController::class, 'returnToDraft'])->name('doccheck.return')->middleware('throttle:return');

        // Modul Penentuan Sidang Keputusan Etik
        Route::get('decisions', [DecisionController::class, 'index'])->name('decisions.index');
        Route::get('decisions/{submission}', [DecisionController::class, 'show'])->name('decisions.show');
        Route::post('decisions/{submission}', [DecisionController::class, 'store'])->name('decisions.store');
        
        // Modul Fullboard Meeting
        Route::get('submissions/{submission}/fullboard/create', [\App\Http\Controllers\FullboardMeetingController::class, 'create'])->name('fullboard.create');
        Route::post('submissions/{submission}/fullboard', [\App\Http\Controllers\FullboardMeetingController::class, 'store'])->name('fullboard.store');
    });

    // Admin Panel Modules
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Admin Dashboard Main Overview
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen User
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');


        Route::post('proposals/{proposal}/draft', [ProposalController::class, 'storeDraft'])->name('proposals.store-draft');
        Route::post('proposals/{proposal}/send-draft', [ProposalController::class, 'sendDraft'])->name('proposals.send-draft');
        Route::post('proposals/{proposal}/assign-secretary', [ProposalController::class, 'assignSecretary'])->name('proposals.assign-secretary');

        // Admin Modules Resource
        Route::resource('proposals', ProposalController::class)->except(['create', 'store']);
        Route::resource('reviewers', ReviewerController::class)->except(['show']);
        Route::resource('secretariat', SecretariatController::class)->except(['show']);
        Route::resource('announcements', AnnouncementController::class)->except(['show']);
        
        // Fitur Admin Template Dokumen
        Route::post('templates/restore-default', [DocumentTemplateController::class, 'restoreDefault'])->name('templates.restore-default');
        Route::resource('templates', DocumentTemplateController::class)->except(['show', 'destroy']);
        Route::post('templates/{template}/toggle-required', [DocumentTemplateController::class, 'toggleRequired'])->name('templates.toggle-required');
        Route::post('templates/{template}/toggle-shown', [DocumentTemplateController::class, 'toggleShown'])->name('templates.toggle-shown');
        Route::post('templates/{template}/archive', [DocumentTemplateController::class, 'archive'])->name('templates.archive');
        Route::post('templates/{template}/restore', [DocumentTemplateController::class, 'restore'])->name('templates.restore');
        
        // Fitur Admin Tambahan
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.store');
    });
});

// ═════════════════════════════════════════════════════════════════════════
// FALLBACK ROUTE — Menangkap seluruh URL yang tidak cocok
// ═════════════════════════════════════════════════════════════════════════
Route::fallback(function () {
    abort(404);
});