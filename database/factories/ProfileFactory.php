<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->paragraph(),
            'location' => fake()->city().', '.fake()->country(),
            'website' => fake()->url(),
            'avatar_path' => null,
            'banner_path' => null,
            'birth_date' => fake()->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'is_private' => false,
        ];
    }

    /**
     * Set profile as private.
     */
    public function private(): static
    {
        return $this->state(fn () => [
            'is_private' => true,
        ]);
    }
}
