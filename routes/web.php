<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController; 
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Dashboard chính
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{hash}', [NewsController::class, 'show'])->name('news.show');
    Route::post('/news/save', [NewsController::class, 'save'])->name('news.save');
    Route::delete('/news/saved/{savedNews}', [NewsController::class, 'destroySaved'])->name('news.saved.destroy');

    // 2. Chat AI (Ưu tiên đặt lên trên các route có tham số {id} để tránh xung đột)
    Route::post('/ai/chat', [PatientController::class, 'chatAI'])->name('ai.chat');
    Route::get('/api/chat-history/{patientId}', [PatientController::class, 'getChatHistory']);

    // 3. Quản lý Bệnh nhân (Xem danh sách & Tạo mới)
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    
    // Xem chi tiết hồ sơ bệnh nhân (Luôn để dưới cùng của cụm /patients để tránh nuốt route create)
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show'); 

    // 4. Quản lý kết quả Scans (Xem kết quả, Xuất file & Xóa)
    Route::get('/scans/{id}/analysis', [ScanController::class, 'analysis'])->name('scans.analysis');
    Route::get('/scans/{scan_id}/pdf', [PatientController::class, 'exportPDF'])->name('patients.exportPDF');
    Route::post('/scans/{scan_id}/send-report', [PatientController::class, 'sendReportEmail'])->name('patients.sendReportEmail');
    Route::delete('/scans/{scan}', [ScanController::class, 'destroy'])->name('scans.destroy');

    // 5. Hồ sơ cá nhân (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 6. Danh sách Tài khoản/Bác sĩ
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // =========================================================================
    // PHÂN QUYỀN CHUYÊN SÂU (ADMIN VS USER/DOCTOR)
    // =========================================================================

    // CẤP QUYỀN ADMIN: Chỉ Admin mới có quyền Sửa / Xóa thông tin bệnh nhân
    Route::middleware('admin')->group(function () {
        Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/news/topics', [NewsController::class, 'storeTopic'])->name('news.topics.store');
        Route::put('/news/topics/{topic}', [NewsController::class, 'updateTopic'])->name('news.topics.update');
        Route::delete('/news/topics/{topic}', [NewsController::class, 'destroyTopic'])->name('news.topics.destroy');
    });

    // CẤP QUYỀN USER / BÁC SĨ: Admin KHÔNG được vào thực hiện tính năng quét ảnh AI này
    // Sử dụng middleware 'user' (IsUser.php) mà bạn đã cấu hình chặn Admin ở bước trước
    Route::middleware('user')->group(function () {
        // Giao diện hiển thị Form tải ảnh lên (Đã chuyển giao chính xác cho ScanController)
        Route::get('/patients/{id}/scan', [ScanController::class, 'showScanForm'])->name('patients.scan');
        
        // Xử lý upload ảnh gửi sang Flask API
        Route::post('/scans/store', [ScanController::class, 'store'])->name('scans.store');
    });

});

require __DIR__.'/auth.php';
