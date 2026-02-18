<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZKDashboardController;
use App\Http\Controllers\ZkUserController;
use App\Http\Controllers\ZKBioDashboardController;


// ZKBio Time 9.0 Routes
Route::prefix('zkbio')->name('zkbio.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ZKBioDashboardController::class, 'dashboard'])->name('dashboard');
    
    // System
    Route::get('/system-time', [ZKBioDashboardController::class, 'getSystemTime'])->name('system.time');
    Route::post('/sync-time', [ZKBioDashboardController::class, 'syncTime'])->name('sync.time');
    
    // Employees
    Route::get('/employees', [ZKBioDashboardController::class, 'getEmployees'])->name('employees.index');
    Route::get('/employees/{id}', [ZKBioDashboardController::class, 'getEmployee'])->name('employees.show');
    Route::post('/employees', [ZKBioDashboardController::class, 'createEmployee'])->name('employees.store');
    Route::patch('/employees/{id}', [ZKBioDashboardController::class, 'updateEmployee'])->name('employees.update');
    Route::delete('/employees/{id}', [ZKBioDashboardController::class, 'deleteEmployee'])->name('employees.destroy');
    
    // Transactions (Attendance)
    Route::get('/transactions', [ZKBioDashboardController::class, 'getTransactions'])->name('transactions.index');
    
    // Devices
    Route::get('/devices', [ZKBioDashboardController::class, 'getDevices'])->name('devices.index');
    Route::get('/devices/{id}', [ZKBioDashboardController::class, 'getDevice'])->name('devices.show');
    
    // Departments
    Route::get('/departments', [ZKBioDashboardController::class, 'getDepartments'])->name('departments.index');
    
    // Areas
    Route::get('/areas', [ZKBioDashboardController::class, 'getAreas'])->name('areas.index');
    
    // Positions
    Route::get('/positions', [ZKBioDashboardController::class, 'getPositions'])->name('positions.index');
});

// Redirect root to ZKBio dashboard
Route::get('/', function () {
    return redirect()->route('zkbio.dashboard');
});

// Public routes - NO AUTH REQUIRED
Route::get('/', function () {
    return view('welcome');
});

// ZK Users Management - NO AUTH
Route::prefix('zk/users')->name('zk.users.')->group(function () {
    Route::get('/', [ZkUserController::class, 'index'])->name('index');
    Route::get('/create', [ZkUserController::class, 'create'])->name('create');
    Route::post('/', [ZkUserController::class, 'store'])->name('store');
    Route::get('/{uid}/edit', [ZkUserController::class, 'edit'])->name('edit');
    Route::put('/{uid}', [ZkUserController::class, 'update'])->name('update');
    Route::delete('/{uid}', [ZkUserController::class, 'destroy'])->name('destroy');
    
    // Fingerprint & Face enrollment
    Route::post('/{uid}/enroll-fingerprint', [ZkUserController::class, 'enrollFingerprint'])->name('enroll.fingerprint');
    Route::post('/{uid}/enroll-face', [ZkUserController::class, 'enrollFace'])->name('enroll.face');
    Route::put('/{uid}/role', [ZkUserController::class, 'updateRole'])->name('update.role');
});

// Dashboard - NO AUTH
Route::get('/dashboard', [ZKDashboardController::class, 'dashboard'])->name('dashboard');

// Live attendance
Route::get('/dashboard/attendance/live', [ZKDashboardController::class, 'liveAttendance'])->name('dashboard.attendance.live');

// Device sync routes
Route::post('/device/sync-time', [ZKDashboardController::class, 'syncTime'])->name('device.sync.time');
Route::get('/system-time', [ZKDashboardController::class, 'getSystemTime'])->name('system.time');

// Other device routes
Route::get('/fetch-attendance', [ZKDashboardController::class, 'fetch'])->name('fetch.attendance');
Route::get('/fetch-users', [ZKDashboardController::class, 'fetchUsers'])->name('fetch.users');
Route::get('/full-test', [ZKDashboardController::class, 'fullTest'])->name('full.test');
Route::get('/test-json', function() {
    return response()->json([
        'success' => true,
        'message' => 'Test JSON response'
    ]);
});