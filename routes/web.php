<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login'); // Redirect root route to login page
});

Auth::routes(); // Authentication routes

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard'); // Dashboard route
    Route::get('users/data', [UserController::class, 'getUsers'])->name('users.data');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::post('/projects/{project}/assign', [ProjectController::class, 'assign'])->name('projects.assign');
    Route::post('/evidences/{evidence}/approve', [EvidenceController::class, 'approve'])->name('evidences.approve');
    Route::post('/evidences/{evidence}/reject', [EvidenceController::class, 'reject'])->name('evidences.reject');
    Route::resource('projects', ProjectController::class);
    Route::resource('evidences', EvidenceController::class);
    Route::resource('statements', StatementController::class);

});
