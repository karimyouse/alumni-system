<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AppUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['academic_id' => '2141091038'],
            [
                'name' => 'Karim Shafiq Yousef',
                'email' => 'karim15062000@gmail.com',
                'role' => 'alumni',
                'password' => Hash::make('karim@2000'),
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
            ['academic_id' => '2141091010'],
            [
                'name' => 'Ahmed Saleh',
                'email' => 'ahmedsaleh@gmail.com',
                'role' => 'alumni',
                'password' => Hash::make('ahmed@2026'),
            ]
        );

        User::updateOrCreate(
            ['academic_id' => '2141091045'],
            [
                'name' => 'Ahmed Abu Ammra',
                'email' => 'ahmedammra@gmail.com',
                'role' => 'alumni',
                'password' => Hash::make('ahmedamm@2026'),
            ]
        );

        User::updateOrCreate(
            ['academic_id' => '2141091020'],
            [
                'name' => 'Mohammed Abu Hajar',
                'email' => 'mohammed@gmail.com',
                'role' => 'alumni',
                'password' => Hash::make('mohammed@2026'),
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
