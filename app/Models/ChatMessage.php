<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    // 1. Khai báo các cột được phép thêm dữ liệu hàng loạt
    protected $fillable = [
        'patient_id', 
        'role', 
        'content'
    ];

    // 2. Thiết lập mối quan hệ: Một tin nhắn thuộc về một Bệnh nhân
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}