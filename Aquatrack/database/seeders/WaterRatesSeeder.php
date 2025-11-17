<?php
// database/seeders/WaterRatesSeeder.php

namespace Database\Seeders;

use App\Models\WaterRate;
use Illuminate\Database\Seeder;

class WaterRatesSeeder extends Seeder
{
    public function run()
    {
        $rates = [
            [
                'name' => 'Tier 1 - Minimum Charge',
                'min_consumption' => 1,
                'max_consumption' => 10,
                'rate_per_cubic' => 0, // No per cubic charge for minimum
                'fixed_charge' => 132.00, // Fixed minimum charge
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Tier 2 - 11-20 m³',
                'min_consumption' => 11,
                'max_consumption' => 20,
                'rate_per_cubic' => 14.00, // Per cubic meter rate
                'fixed_charge' => 0, // No fixed charge for higher tiers
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tier 3 - 21-30 m³',
                'min_consumption' => 21,
                'max_consumption' => 30,
                'rate_per_cubic' => 14.85,
                'fixed_charge' => 0,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Tier 4 - 31-40 m³',
                'min_consumption' => 31,
                'max_consumption' => 40,
                'rate_per_cubic' => 16.00,
                'fixed_charge' => 0,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Tier 5 - 41+ m³',
                'min_consumption' => 41,
                'max_consumption' => null, // No upper limit
                'rate_per_cubic' => 17.25,
                'fixed_charge' => 0,
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($rates as $rate) {
            WaterRate::create($rate);
        }
    }
}
