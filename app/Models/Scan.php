<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    // 1. Cho phép lưu các cột này vào Database (Mass Assignment)
    protected $fillable = [
        'patient_id', 
        'image_path', 
        'prediction', 
        'confidence_score', 
        'doctor_comments'
    ];

    // 2. Thiết lập quan hệ: Một bản quét (Scan) sẽ thuộc về một Bệnh nhân (Patient)
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}