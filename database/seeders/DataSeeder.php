<?php

namespace Database\Seeders;

use App\Models\Data;
use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available users and groups
        $users = User::pluck('username')->toArray();
        $groups = Group::pluck('group_name')->toArray();
        
        // Sample locations in Indonesia
        $locations = [
            [
                'lat' => -6.2088, 
                'long' => 106.8456, 
                'thoroughfare' => 'Jalan Sudirman', 
                'subLocality' => 'Setiabudi',
                'locality' => 'Jakarta Selatan', 
                'subAdmin' => 'Jakarta', 
                'adminArea' => 'DKI Jakarta',
                'postalCode' => '12190'
            ],
            [
                'lat' => -6.1753, 
                'long' => 106.8271, 
                'thoroughfare' => 'Jalan Thamrin', 
                'subLocality' => 'Menteng',
                'locality' => 'Jakarta Pusat', 
                'subAdmin' => 'Jakarta', 
                'adminArea' => 'DKI Jakarta',
                'postalCode' => '10350'
            ],
            [
                'lat' => -7.7971, 
                'long' => 110.3688, 
                'thoroughfare' => 'Jalan Malioboro', 
                'subLocality' => 'Gedong Tengen',
                'locality' => 'Yogyakarta', 
                'subAdmin' => 'Yogyakarta', 
                'adminArea' => 'DI Yogyakarta',
                'postalCode' => '55271'
            ],
            [
                'lat' => -6.9147, 
                'long' => 107.6098, 
                'thoroughfare' => 'Jalan Asia Afrika', 
                'subLocality' => 'Braga',
                'locality' => 'Bandung', 
                'subAdmin' => 'Bandung', 
                'adminArea' => 'Jawa Barat',
                'postalCode' => '40111'
            ],
            [
                'lat' => -7.2575, 
                'long' => 112.7521, 
                'thoroughfare' => 'Jalan Pemuda', 
                'subLocality' => 'Genteng',
                'locality' => 'Surabaya', 
                'subAdmin' => 'Surabaya', 
                'adminArea' => 'Jawa Timur',
                'postalCode' => '60271'
            ],
        ];
        
        // Create sample data entries
        foreach ($locations as $location) {
            Data::create([
                'uploader' => $users[array_rand($users)],
                'group' => $groups[array_rand($groups)],
                'imgURI' => 'images/sample_' . Str::random(8) . '.jpg',
                'spandukCount' => rand(1, 5),
                'lat' => $location['lat'],
                'long' => $location['long'],
                'thoroughfare' => $location['thoroughfare'],
                'subLocality' => $location['subLocality'],
                'locality' => $location['locality'],
                'subAdmin' => $location['subAdmin'],
                'adminArea' => $location['adminArea'],
                'postalCode' => $location['postalCode'],
                'deleted' => false
            ]);
        }
    }
}