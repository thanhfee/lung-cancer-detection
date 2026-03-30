<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanResult extends Model
{
    use HasFactory;

    protected $table = 'scan_results'; 

    protected $fillable = ['patient_id', 'image_path', 'prediction', 'confidence_score', 'doctor_comments'];

  
public function patient()
{
    return $this->belongsTo(Patient::class);
}
}