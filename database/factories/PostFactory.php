<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
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
            'content' => fake()->paragraph(3),
            'visibility' => 'public',
            'reply_settings' => 'everyone',
            'likes_count' => 0,
            'comments_count' => 0,
        ];
    }

    /**
     * Set followers-only visibility.
     */
    public function followersOnly(): static
    {
        return $this->state(fn () => [
            'visibility' => 'followers',
        ]);
    }
}
