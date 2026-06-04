<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

// Nhóm các Route yêu cầu ĐĂNG NHẬP
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard chính
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');

    // 2. Chat AI (Ưu tiên đặt lên trên các route có tham số {id} để tránh xung đột)
    Route::post('/ai/chat', [PatientController::class, 'chatAI'])->name('ai.chat');
    Route::get('/api/chat-history/{patientId}', [PatientController::class, 'getChatHistory']);

    // 3. Quản lý Bệnh nhân
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    
    // Các route có tham số {id} (Luôn để ở cuối nhóm để không "nuốt" mất các route tĩnh)
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show'); 
    Route::get('/patients/{id}/scan', [PatientController::class, 'scan'])->name('patients.scan');

    // 4. Quản lý Scans & Xuất PDF
    Route::post('/scans/store', [ScanController::class, 'store'])->name('scans.store');
    Route::get('/scans/{id}/analysis', [ScanController::class, 'analysis'])->name('scans.analysis');
    Route::get('/scans/{scan_id}/pdf', [PatientController::class, 'exportPDF'])->name('patients.exportPDF');
    Route::delete('/scans/{scan}', [ScanController::class, 'destroy'])->name('scans.destroy');

    // 5. Nhóm route dành cho ADMIN (Xóa và Sửa)
    Route::middleware('admin')->group(function () {
        Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });


    // 6. Profile (Thông tin bác sĩ)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 7. Danh sách doctors
    Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');
});

require __DIR__.'/auth.php';
