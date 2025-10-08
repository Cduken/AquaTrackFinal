<?php

namespace Database\Factories;

use App\Models\MeterReading;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class MeterReadingFactory extends Factory
{
    protected $model = MeterReading::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $readingDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $previousReading = $this->faker->numberBetween(1000, 5000);
        $currentReading = $previousReading + $this->faker->numberBetween(10, 200);
        $consumption = $currentReading - $previousReading;
        $baseAmount = $consumption * 0.15;

        // Get billing month from reading date (format: Y-m)
        $billingMonth = Carbon::parse($readingDate)->format('Y-m');

        // For overdue records, add surcharge to the amount
        $status = $this->faker->randomElement(['Pending', 'Paid', 'Overdue']);
        $amount = $baseAmount;

        // If status is Overdue, add 10% surcharge to the amount
        if ($status === 'Overdue') {
            $surcharge = round($baseAmount * 0.10, 2);
            $amount = $baseAmount + $surcharge;
        }

        return [
            'user_id' => $user->id,
            'staff_id' => null,
            'billing_month' => $billingMonth,
            'reading_date' => $readingDate,
            'previous_reading' => $previousReading,
            'reading' => $currentReading,
            'consumption' => $consumption,
            'amount' => $amount, // This now includes surcharge for overdue records
            'status' => $status,
            'due_date' => Carbon::parse($readingDate)->addDays(30)->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Paid',
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Overdue',
        ]);
    }
}
