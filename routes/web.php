<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root route to login page
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(); // Authentication routes

// Protected routes (requires authentication)
Route::middleware(['auth','twofactor'])->group(function () {

    Route::get('/two-factor', function () {
        return view('auth.two_factor');
    })->name('two.factor.form');
    Route::post('/verify-2fa', [TwoFactorController::class, 'verify'])->name('verify.2fa');

    // Dashboard (Admin full access, limited access for others)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
Route::get('/autocomplete/clients', [AdminController::class, 'autocompleteClients'])->name('autocomplete.clients');
Route::get('/clients/projects', [AdminController::class, 'clientProjects'])->name('clientProjects');
Route::get('/autocomplete/projects', [AdminController::class, 'autocompleteProjects'])->name('autocomplete.projects');

    // Admin-only routes for managing users, roles, and permissions
    Route::middleware(['role:admin'])->group(function () {
        Route::get('users/data', [UserController::class, 'getUsers'])->name('users.data');
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        // Admin-specific project management
        Route::post('/projects/{project}/assign', [ProjectController::class, 'assign'])->name('projects.assign');
        Route::post('/projects/{project}/revoke-access', [ProjectController::class, 'revokeAccess'])->name('projects.revokeAccess');
        Route::get('/projects/{project}/assign-users', [ProjectController::class, 'assignUsers'])->name('projects.assignUsers');
        Route::post('/projects/{project}/upload-statement-csv', [ProjectController::class, 'UploadStatementCsv'])->name('projects.uploadstatementcsv');

        // Admin Audits
        Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    });

    // Project routes
    Route::resource('projects', ProjectController::class)->except(['destroy']);
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy'); // Only admin can delete projects
    });

    // Evidence routes
    Route::middleware(['role:client'])->group(function () {
        Route::post('/projects/{project}/evidences/upload', [EvidenceController::class, 'upload'])->name('evidences.upload');
        Route::post('/statements/{statement}/evidences/upload', [StatementController::class, 'uploadEvidence'])->name('statements.evidences.upload');
    });

    Route::middleware(['role:admin|auditor'])->group(function () {
        Route::get('/evidences/{evidence}/download', [EvidenceController::class, 'download'])->name('evidences.download');
        Route::post('/evidences/{evidence}/approve', [EvidenceController::class, 'approve'])->name('evidences.approve');
        Route::post('/evidences/{evidence}/reject', [EvidenceController::class, 'reject'])->name('evidences.reject');
        Route::post('/evidences/{evidence}/rate', [ProjectController::class, 'rateEvidence'])->name('evidences.rate');
        Route::post('/evidences/{evidence}/compliance', [ProjectController::class, 'complianceEvidence'])->name('evidences.compliance');
    });

    // Statement routes
    Route::middleware(['role:client|auditor'])->group(function () {
        Route::post('/projects/{project}/statements', [StatementController::class, 'store'])->name('statements.store');
    });

    Route::middleware(['role:admin|auditor|client'])->group(function () {
        Route::get('/projects/{project}/statements', [StatementController::class, 'show'])->name('statements.show');
    });

    Route::middleware(['role:admin|auditor'])->group(function () {
        Route::delete('/statements/{statement}', [StatementController::class, 'destroy'])->name('statements.destroy'); // Admin and auditors can delete statements
    });

    // Comment routes
    Route::middleware(['role:client|auditor'])->group(function () {
        Route::post('/statements/{statement}/comments', [CommentController::class, 'store'])->name('comments.store');
    });

    Route::middleware(['role:admin|auditor'])->group(function () {
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy'); // Admin and auditors can delete comments
    });
});