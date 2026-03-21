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
        $password = 'password';
        
        \App\Models\User::create([
            'name' => 'Admin User',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'plain_password' => $password,
            'role' => 'admin',
            'username' => 'admin',
            'employee_id' => 'EMP001'
        ]);

        \App\Models\User::create([
            'name' => 'Sales User',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'plain_password' => $password,
            'role' => 'sales_team',
            'username' => 'sales',
            'employee_id' => 'EMP002'
        ]);
    }
}
