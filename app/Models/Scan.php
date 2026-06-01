<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'patient_id', 
        'image_path', 
        'result', 
        'confidence', // Đảm bảo cột này khớp với DB (là confidence hay confidence_score?)
        'heatmap_path'
    ];

    protected $casts = [
        'confidence' => 'float', // Sửa lại cho khớp với tên trong fillable
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}