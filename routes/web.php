<?php
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authentication required routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $userRole = auth()->user()->role->name;
        if ($userRole == 'admin') {
            return app(LeaveRequestController::class)->adminDashboard();
        } else {
            // Call the dashboard method of the EmployeeController for non-admin users
            return app(EmployeeController::class)->dashboard();
        }
    })->name('dashboard');

    // Admin specific routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    });

    // Profile routes accessible to all users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/demandes-de-conge', [LeaveRequestController::class, 'index'])
        ->name('leave-requests.index');  // Vue de l'employé pour voir ses demandes

    Route::get('/demande-de-conge/nouveau', [LeaveRequestController::class, 'create'])
        ->name('leave-requests/create');  // Formulaire de soumission de nouvelle demande
    Route::get('/leave-balance', [LeaveRequestController::class, 'getLeaveBalance']);
    Route::post('/demandes-de-conge', [LeaveRequestController::class, 'store'])
        ->name('leave-requests.store');  // Soumettre une nouvelle demande de congé
    Route::get('/demande-de-conge/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');

    // Routes that need email verification
    Route::middleware('verified')->group(function () {
        // Employee management routes (Assumed to be admin only based on your description)
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/{employee}/show', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

        // User management routes (Admin only)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    // Leave Requests routes (Admin only)
    Route::get('/admin/demandes-de-conge', [LeaveRequestController::class, 'adminIndex'])
        ->name('admin.leave-requests.index');
    Route::patch('/admin/demandes-de-conge/{leaveRequest}', [LeaveRequestController::class, 'update'])
        ->name('admin.leave-requests.update');
    Route::get('/admin/demandes-de-conge/{leaveRequest}', [LeaveRequestController::class, 'showAdmin'])->name('admin.leave-requests.show');
    //calendrier
    Route::get('/leave-requests/calendar', [LeaveRequestController::class, 'calendar'])->name('leave-requests.calendar');
    Route::get('/reports', [ReportController::class, 'generateReport'])->name('reports.generate');
    Route::get('/admin/reports/another', [ReportController::class, 'sendDataToAnotherView']);

   


  

    // Notification routes


Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
});

   
});

require __DIR__.'/auth.php';
