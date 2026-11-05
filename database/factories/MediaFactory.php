<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mediable_type' => Post::class,
            'mediable_id' => Post::factory(),
            'file_path' => 'posts/'.fake()->uuid().'.webp',
            'disk' => 'public',
            'file_name' => fake()->word().'.webp',
            'mime_type' => 'image/webp',
            'file_size' => fake()->numberBetween(100000, 3000000),
            'meta' => [
                'width' => 1200,
                'height' => 800,
                'aspect_ratio' => '1.5',
            ],
        ];
    }
}
