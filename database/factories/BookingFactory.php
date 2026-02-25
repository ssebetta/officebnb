<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = Carbon::now()->addDays($this->faker->numberBetween(3, 30));
        $end = (clone $start)->addDays($this->faker->numberBetween(1, 5));
        $dailyRate = $this->faker->numberBetween(180, 650) * 100;
        $days = $start->diffInDays($end) + 1;

        return [
            'office_id' => Office::factory(),
            'user_id' => User::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'guests' => $this->faker->numberBetween(2, 12),
            'total_amount' => $dailyRate * $days,
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'notes' => $this->faker->optional()->sentence(10),
        ];
    }
}
