<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanResult extends Model
{
    use HasFactory;

    // Chỉ để lại những cột thực sự có trong Database của Thành
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'image_path',
        'prediction',       
        'confidence_score',
        'doctor_comments'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
