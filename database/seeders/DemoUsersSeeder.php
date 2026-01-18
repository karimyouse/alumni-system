<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['academic_id' => '2141091038'],
            [
                'name' => 'Demo Alumni',
                'email' => 'alumni@ptc.edu',
                'role' => 'alumni',
                'password' => Hash::make('demo123'),
            ]
        );


        User::updateOrCreate(
    ['academic_id' => '2141091051'],
    [
        'name' => 'Ali Yousef',
        'email' => 'ali@gmail.com',
        'role' => 'alumni',
        'password' => Hash::make('ali@123'),
    ]
);


        User::updateOrCreate(
            ['email' => 'college@ptc.edu'],
            [
                'name' => 'PTC College',
                'academic_id' => null,
                'role' => 'college',
                'password' => Hash::make('college123'),
            ]
        );


        User::updateOrCreate(
            ['email' => 'company@techcorp.com'],
            [
                'name' => 'TechCorp Company',
                'academic_id' => null,
                'role' => 'company',
                'password' => Hash::make('company123'),
            ]
        );

        
        User::updateOrCreate(
            ['email' => 'admin@ptc.edu'],
            [
                'name' => 'System Admin',
                'academic_id' => null,
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
