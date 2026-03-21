<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $designation = \App\Models\Designation::updateOrCreate(['name' => 'Field Sales Manager']);

        $data = [
            'West' => [
                'Pune' => ['Kolhapur', 'Pune'],
                'Nagpur' => ['Indore', 'Nagpur'],
                'Mumbai' => ['Mumbai', 'Thane'],
                'Vadodara' => ['Vadodara'],
                'Ahmedabad' => ['Ahmedabad'],
            ],
            'East' => [
                'Kolkata' => ['Howrah', 'Kolkata', 'Bardhaman', 'Guwahati'],
                'Patna' => ['Patna', 'Bhubaneshwar'],
            ],
            'North' => [
                'Lucknow' => ['Ghaziabad', 'Lucknow', 'Varanasi', 'Meerut'],
                'Chandigarh' => ['Chandigarh', 'Ludhiana'],
                'Delhi' => ['Delhi'],
                'Jaipur' => ['Jaipur', 'Jodhpur'],
            ],
            'South 1' => [
                'Bengaluru' => ['Bengaluru', 'Hubballi', 'Mangaluru'],
                'Hyderabad' => ['Hyderabad', 'Visakhapatnam', 'Guntur', 'Vijayawada'],
            ],
            'South 2' => [
                'Chennai' => ['Chennai', 'Madurai'],
                'Ernakulam' => ['Kozhikode', 'Thiruvananthapuram', 'Ernakulam'],
                'Coimbatore' => ['Chennai', 'Salem', 'Coimbatore'],
            ]
        ];

        foreach ($data as $zoneName => $regions) {
            $zone = \App\Models\Zone::updateOrCreate(['name' => $zoneName]);
            foreach ($regions as $regionName => $hqs) {
                $region = \App\Models\Region::updateOrCreate(['name' => $regionName, 'zone_id' => $zone->id]);
                foreach ($hqs as $hqName) {
                    \App\Models\Hq::updateOrCreate(['name' => $hqName, 'region_id' => $region->id]);
                }
            }
        }
    }
}
