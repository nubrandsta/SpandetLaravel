<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Group;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'username' => fake()->userName(),
            'full_name' => fake()->name(),
            'group' => Group::pluck('group_name')->random(),
            'password' => Hash::make('password')
        ];
    }
}
