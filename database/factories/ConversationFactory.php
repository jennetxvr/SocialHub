<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => null,
            'type' => 'direct',
            'last_message_at' => now(),
        ];
    }

    /**
     * Set conversation as group chat.
     */
    public function group(): static
    {
        return $this->state(fn () => [
            'title' => fake()->words(3, true),
            'type' => 'group',
        ]);
    }
}
