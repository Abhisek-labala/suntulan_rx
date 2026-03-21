<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = \Illuminate\Support\Str::random(10);
        $username = 'Admin' . '-' . \Illuminate\Support\Str::random(5);

        \App\Models\User::create([
            'name' => 'Admin',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'plain_password' => $password,
            'role' => 'admin',
            'username' => $username,
            'employee_id' => 'ADM-001'
        ]);
    }
}
