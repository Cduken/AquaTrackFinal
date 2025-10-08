<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MeterReading;
use App\Models\User;
use Carbon\Carbon;

class MeterReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users first if they don't exist
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }

        // Clear existing records to avoid duplicates
        MeterReading::truncate();

        // Create 10 meter reading records with mixed statuses
        MeterReading::factory()->count(4)->pending()->create();
        MeterReading::factory()->count(3)->paid()->create();
        MeterReading::factory()->count(3)->overdue()->create();

        echo "MeterReadingSeeder completed successfully!\n";
        echo "Created:\n";
        echo "- " . MeterReading::where('status', 'Pending')->count() . " Pending records\n";
        echo "- " . MeterReading::where('status', 'Paid')->count() . " Paid records\n";
        echo "- " . MeterReading::where('status', 'Overdue')->count() . " Overdue records\n";
        echo "Total: " . MeterReading::count() . " records\n";
    }
}
