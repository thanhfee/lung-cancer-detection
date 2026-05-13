<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ScanResult extends Model
{
    use HasFactory;

    protected $table = 'scan_results'; 

    protected $fillable = ['patient_id', 'doctor_id', 'image_path', 'prediction', 'confidence_score', 'doctor_comments'];

  
public function patient()
{
    return $this->belongsTo(Patient::class);
}

public function doctor()
{
    return $this->belongsTo(User::class, 'doctor_id');
}
}
