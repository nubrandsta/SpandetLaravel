<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            DataSeeder::class
        ]);

        // Test user factory
        User::factory()->create([
            'username' => 'test_user',
            'full_name' => 'Test User',
            'group' => \App\Enums\UserGroup::USER->value
        ]);
    }
}
