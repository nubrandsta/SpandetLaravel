<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Group::updateOrCreate(
            ['group_name' => 'admin'],
            ['group_description' => 'Administrator Group']
        );

        Group::updateOrCreate(
            ['group_name' => 'user'],
            ['group_description' => 'Regular User Group']
        );

        Group::updateOrCreate(
            ['group_name' => 'auditor'],
            ['group_description' => 'Auditor Group']
        );
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'id' => Str::uuid(),
                'full_name' => 'Administrator',
                'group' => 'admin',
                'password' => Hash::make('admin123')
            ]
        );

        User::updateOrCreate(
            ['username' => 'user'],
            [
                'id' => Str::uuid(),
                'full_name' => 'User Test',
                'group' => 'user',
                'password' => Hash::make('pass123')
            ]
        );

        User::updateOrCreate(
            ['username' => 'auditor'],
            [
                'id' => Str::uuid(),
                'full_name' => 'Auditor User',
                'group' => 'auditor',
                'password' => Hash::make('audit123')
            ]
        );
        
    }
}