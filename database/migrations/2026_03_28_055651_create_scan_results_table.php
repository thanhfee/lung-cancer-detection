<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_results', function (Blueprint $table) {
            $table->id();
            
            // Khóa ngoại liên kết với bảng patients (Bắt buộc bảng patients phải có trước)
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade'); 
            
            $table->string('image_path');      // Đường dẫn ảnh phổi upload lên
            $table->float('confidence_score'); // Tỷ lệ tin tưởng của AI (0.0 - 1.0)
            $table->string('prediction');      // Kết quả: "Bình thường" hoặc "Ác tính"
            $table->text('doctor_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_results');
    }
};