<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_histories'; // Đảm bảo tên này khớp 100% với tên bảng trong phpMyAdmin

    protected $fillable = [
        'patient_id',
        'user_message',
        'ai_response',
        'model_used'
    ];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}