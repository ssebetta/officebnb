<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Harold Peter',
            'email' => 'haroldpeter6@gmail.com',
            'role' => 'super_admin',
        ]);

        $admin = User::factory()->create([
            'name' => 'OfficeBnB Admin',
            'email' => 'admin@officebnb.test',
            'role' => 'admin',
        ]);

        $owners = User::factory()->count(3)->create([
            'role' => 'owner',
        ]);

        $renters = User::factory()->count(4)->create([
            'role' => 'renter',
        ]);

        $renters->push(User::factory()->create([
            'name' => 'Test Renter',
            'email' => 'test@example.com',
            'role' => 'renter',
        ]));

        $owners->each(function (User $owner) {
            Office::factory()->count(3)->create([
                'owner_id' => $owner->id,
            ]);
        });

        Office::factory()->count(2)->create([
            'owner_id' => $admin->id,
        ]);

        Office::factory()->count(2)->create([
            'owner_id' => $superAdmin->id,
        ]);

        $offices = Office::query()->pluck('id');
        $renters->each(function (User $renter) use ($offices) {
            $officeId = $offices->random();
            $booking = $renter->bookings()->create([
                'office_id' => $officeId,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(10),
                'guests' => fake()->numberBetween(2, 8),
                'total_amount' => fake()->numberBetween(600, 1800) * 100,
                'currency' => 'USD',
                'status' => fake()->randomElement(['pending', 'approved']),
                'notes' => fake()->optional()->sentence(8),
            ]);

            if ($booking->status === 'approved') {
                $booking->payment()->create([
                    'amount' => $booking->total_amount,
                    'currency' => 'USD',
                    'status' => 'paid',
                    'provider' => 'manual',
                    'reference' => fake()->uuid(),
                    'paid_at' => now()->subDays(2),
                ]);
            }
        });
    }
}
