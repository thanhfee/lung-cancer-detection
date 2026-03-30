<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 01 tài khoản Admin mẫu
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'), // Mật khẩu mặc định
            'role' => 'admin',
        ]);

        // 2. Tạo 10 tài khoản Bác sĩ mẫu
        $faker = \Faker\Factory::create('vi_VN'); // Dùng ngôn ngữ tiếng Việt

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Bác sĩ ' . $faker->name,
                'email' => 'bacsi' . $i . '@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'doctor',
            ]);
        }
    }
}