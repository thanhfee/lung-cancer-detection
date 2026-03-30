<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
protected $fillable = ['name', 'age', 'gender', 'phone', 'patient_code'];
    public function scans()
    {
return $this->hasMany(ScanResult::class, 'patient_id')->latest();    }
}