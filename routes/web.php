<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware(['role:Developer,HR,Sales']);
        Route::get('/presence-data', [DashboardController::class, 'presence'])->name('dashboard.presence-data');

        // Resource routes for departments
        Route::resource('departments', DepartmentController::class)->middleware(['role:HR']);

        // Resource routes for roles
        Route::resource('roles', RoleController::class)->middleware(['role:HR']);

        // Resource routes for employees
        Route::resource('employees', EmployeeController::class)->middleware(['role:HR']);

        // Resource routes for tasks
        Route::resource('tasks', TaskController::class)->middleware(['role:Developer,HR']);
        Route::get('tasks/done/{id}', [TaskController::class, 'done'])->name('tasks.done');
        Route::get('tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending');

        // Resource routes for presences (attendance)
        Route::resource('presences', PresenceController::class)->middleware(['role:Developer,HR,Sales']);

        // Resource routes for payroll
        Route::resource('payrolls', PayrollController::class)->middleware(['role:Developer,HR']);

        // Resource routes for leave requests
        Route::resource('leave-requests', LeaveRequestController::class)->middleware(['role:Developer,HR']);
        Route::get('leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm');
        Route::get('leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    });
});







Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
