<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = $this->faker->city();
        $country = $this->faker->country();
        $title = $this->faker->randomElement([
            'Light-filled studio near',
            'Boutique loft by',
            'Calm focus space off',
            'Creative hub next to',
        ]) . ' ' . $this->faker->streetName();
        $amenities = $this->faker->randomElements([
            'High-speed WiFi',
            'Meeting rooms',
            'Video conferencing',
            'Whiteboards',
            '24/7 access',
            'On-site staff',
            'Kitchenette',
            'Phone booths',
        ], $this->faker->numberBetween(3, 6));

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'city' => $city,
            'country' => $country,
            'address' => $this->faker->streetAddress(),
            'description' => $this->faker->sentence(18),
            'amenities' => $amenities,
            'image_urls' => [
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'workspace_type' => $this->faker->randomElement(['Private office', 'Coworking suite', 'Studio floor']),
            'size_sqft' => $this->faker->numberBetween(400, 2500),
            'meeting_rooms' => $this->faker->numberBetween(0, 4),
            'desks' => $this->faker->numberBetween(4, 40),
            'timezone' => $this->faker->timezone(),
            'capacity' => $this->faker->numberBetween(2, 20),
            'daily_rate' => $this->faker->numberBetween(120, 980) * 100,
            'is_active' => true,
        ];
    }
}
