<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\WorkSessionController;
use App\Http\Controllers\ReportController;

// Public Attendance Kiosk (Root Route)
Route::get('/', [AttendanceController::class, 'kiosk'])->name('attendance.kiosk');
Route::post('/attendance/public', [AttendanceController::class, 'storePublic'])->name('attendance.storePublic');

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Admin Profile
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

    // Employees
    Route::get('/admin/employees', [AdminController::class, 'employees'])->name('admin.employees');
    Route::post('/admin/employees', [AdminController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::put('/admin/employees/{user}', [AdminController::class, 'updateEmployee'])->name('admin.employees.update');
    Route::delete('/admin/employees/{user}', [AdminController::class, 'destroyEmployee'])->name('admin.employees.destroy');

    // Work Sessions
    Route::get('/admin/sessions', [WorkSessionController::class, 'index'])->name('admin.sessions');
    Route::post('/admin/sessions', [WorkSessionController::class, 'store'])->name('admin.sessions.store');
    Route::patch('/admin/sessions/{workSession}/toggle', [WorkSessionController::class, 'toggleStatus'])->name('admin.sessions.toggle');
    Route::delete('/admin/sessions/{workSession}', [WorkSessionController::class, 'destroy'])->name('admin.sessions.destroy');
    Route::get('/admin/sessions/{workSession}', [WorkSessionController::class, 'show'])->name('admin.sessions.show');

    // Attendances
    Route::get('/admin/attendances', [AdminController::class, 'attendances'])->name('admin.attendances');

    // Payrolls
    Route::get('/admin/payrolls', [PayrollController::class, 'index'])->name('admin.payrolls');
    Route::get('/admin/payrolls/create', [PayrollController::class, 'create'])->name('admin.payrolls.create');
    Route::post('/admin/payrolls', [PayrollController::class, 'store'])->name('admin.payrolls.store');
    Route::get('/admin/payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('admin.payrolls.print');

    // Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/admin/reports/print', [ReportController::class, 'print'])->name('admin.reports.print');
});

// Employee Routes (Optional now, as attendance is public)
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::get('/employee/history', [EmployeeController::class, 'history'])->name('employee.history');
    
    // Attendance Action
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
});
