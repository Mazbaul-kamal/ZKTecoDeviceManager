<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZKDashboardController;
use App\Http\Controllers\ZkUserController;
use App\Http\Controllers\ZkAttendanceController;



    

// Public routes - NO AUTH REQUIRED
Route::get('/', function () {
    return view('welcome');
});
Route::post('/device/restart', [ZKDashboardController::class, 'restart']);
Route::post('/device/shutdown', [ZKDashboardController::class, 'shutdown']);
// Route::post('/device/sync-time', [ZKDashboardController::class, 'syncTime'])
//     ->name('device.sync.time');
Route::post('/zk/device/{action}', [ZKDashboardController::class, 'deviceAction']);



// ZK Users Management - NO AUTH
Route::prefix('zk/users')->name('zk.users.')->group(function () {
    Route::get('/', [ZkUserController::class, 'index'])->name('index');
    Route::get('/create', [ZkUserController::class, 'create'])->name('create');
    Route::post('/', [ZkUserController::class, 'store'])->name('store');
    Route::get('/{uid}/edit', [ZkUserController::class, 'edit'])->name('edit');
    Route::put('/{uid}', [ZkUserController::class, 'update'])->name('update');
    Route::delete('/{uid}', [ZkUserController::class, 'destroy'])->name('destroy');
    


    // Fingerprint & Face enrollment
    Route::post('/zk/users/{uid}/enroll-fingerprint', [ZkUserController::class, 'enrollFingerprint'])->name('enroll.fingerprint');
    Route::post('/{uid}/enroll-face', [ZkUserController::class, 'enrollFace'])->name('enroll.face');
    Route::put('/{uid}/role', [ZkUserController::class, 'updateRole'])->name('update.role');
});

// ZK Attendance Management - NO AUTH
Route::prefix('zk/attendance')->name('zk.attendance.')->group(function () {
    Route::get('/', [ZkAttendanceController::class, 'index'])->name('index');
    Route::post('/sync', [ZkAttendanceController::class, 'sync'])->name('sync');
    Route::get('/export', [ZkAttendanceController::class, 'export'])->name('export');
});

Route::get('/dashboard', [ZKDashboardController::class, 'dashboard'])->name('dashboard');

// Live attendance
Route::get('/dashboard/attendance/live', [ZKDashboardController::class, 'liveAttendance'])->name('dashboard.attendance.live');


// Other device routes
Route::get('/fetch-attendance', [ZKDashboardController::class, 'fetch'])->name('fetch.attendance');
Route::get('/fetch-users', [ZKDashboardController::class, 'fetchUsers'])->name('fetch.users');
Route::get('/full-test', [ZKDashboardController::class, 'fullTest'])->name('full.test');
