<?php

namespace Database\Factories;

use App\Models\Data;
use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DataFactory extends Factory
{
    protected $model = Data::class;

    public function definition(): array
    {
        return [
            'uploader' => User::pluck('username')->random(),
            'group' => Group::pluck('group_name')->random(),
            'imgURI' => 'images/sample_' . Str::random(8) . '.jpg',
            'spandukCount' => $this->faker->numberBetween(1, 5),
            'lat' => $this->faker->latitude(-8.0, -5.0),
            'long' => $this->faker->longitude(105.0, 115.0),
            'thoroughfare' => $this->faker->streetName(),
            'subLocality' => $this->faker->citySuffix(),
            'locality' => $this->faker->city(),
            'subAdmin' => $this->faker->state(),
            'adminArea' => $this->faker->state(),
            'postalCode' => $this->faker->postcode(),
            'deleted' => false
        ];
    }
}