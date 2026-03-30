<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('scans', function (Blueprint $table) {
        $table->id();
        // Khóa ngoại liên kết với bảng patients
        $table->foreignId('patient_id')->constrained()->onDelete('cascade');
        
        $table->string('image_path'); // Đường dẫn ảnh X-quang
        $table->string('prediction'); // Kết quả: Malignant, Benign, Normal...
        $table->float('confidence_score'); // Độ tin cậy (ví dụ 0.95)
        $table->text('doctor_comments')->nullable(); // Ghi chú của bác sĩ
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
