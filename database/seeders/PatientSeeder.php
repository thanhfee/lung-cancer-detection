<?php

namespace Database\Seeders; // Kiểm tra dòng này

use Illuminate\Database\Seeder;
use App\Models\Patient; // Đảm bảo đã import Model

class PatientSeeder extends Seeder // Tên class phải khớp với tên file
{
    public function run(): void
    {
        Patient::create([
            'patient_code' => 'BN9167',
            'name' => 'Nguyễn Văn A',
            'age' => 45,
            'gender' => 'Male',
            'phone' => '0912345678'
        ]);
    }
}