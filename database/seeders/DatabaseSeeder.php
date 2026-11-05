<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Follow;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Primary Demo User
        $demoUser = User::factory()->create([
            'name' => 'Sophia Vance',
            'username' => 'sophiav',
            'email' => 'demo@socialhub.com',
            'password' => Hash::make('password'),
        ]);

        Profile::factory()->create([
            'user_id' => $demoUser->id,
            'bio' => 'Design engineer & digital artisan. Passionate about minimalist UI, typography, and building next-gen web experiences on SocialHub.',
            'location' => 'San Francisco, CA',
            'website' => 'https://sophiavance.design',
            'is_private' => false,
        ]);

        // 2. Create Network of 10 Users with Profiles
        $networkUsers = User::factory(10)->create()->each(function (User $user) {
            Profile::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        $allUsers = collect([$demoUser])->merge($networkUsers);

        // 3. Create Posts
        $posts = collect();

        foreach ($allUsers as $user) {
            $userPosts = Post::factory(rand(2, 4))->create([
                'user_id' => $user->id,
            ]);
            $posts = $posts->merge($userPosts);
        }

        // 4. Create Interactions (Likes, Comments, Nested Replies)
        foreach ($posts as $post) {
            // Likes
            $likers = $allUsers->where('id', '!=', $post->user_id)->random(rand(1, 5));
            foreach ($likers as $liker) {
                PostLike::create([
                    'post_id' => $post->id,
                    'user_id' => $liker->id,
                    'created_at' => now()->subMinutes(rand(5, 500)),
                ]);
            }
            $post->update(['likes_count' => $likers->count()]);

            // Comments
            $commenters = $allUsers->random(rand(1, 3));
            $commentCount = 0;
            foreach ($commenters as $commenter) {
                $comment = Comment::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $commenter->id,
                    'created_at' => now()->subMinutes(rand(10, 200)),
                ]);
                $commentCount++;

                // Optional nested reply
                if (rand(0, 1)) {
                    Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $post->user_id,
                        'parent_id' => $comment->id,
                        'created_at' => now()->subMinutes(rand(1, 9)),
                    ]);
                    $commentCount++;
                }
            }
            $post->update(['comments_count' => $commentCount]);
        }

        // 5. Follow Relationships
        foreach ($networkUsers as $otherUser) {
            // Some follow the demo user
            if (rand(0, 1)) {
                Follow::firstOrCreate([
                    'follower_id' => $otherUser->id,
                    'following_id' => $demoUser->id,
                ], [
                    'status' => 'accepted',
                ]);
            }

            // Demo user follows some
            if (rand(0, 1)) {
                Follow::firstOrCreate([
                    'follower_id' => $demoUser->id,
                    'following_id' => $otherUser->id,
                ], [
                    'status' => 'accepted',
                ]);
            }
        }

        // 6. Direct Message Conversations for Demo User
        $chatPartners = $networkUsers->take(3);
        foreach ($chatPartners as $partner) {
            $conversation = Conversation::create([
                'type' => 'direct',
                'last_message_at' => now(),
            ]);

            $conversation->users()->attach([
                $demoUser->id => ['last_read_at' => now()->subHours(1)],
                $partner->id => ['last_read_at' => now()],
            ]);

            Message::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $partner->id,
                'body' => 'Hey Sophia! Loved your latest post about the design architecture.',
                'created_at' => now()->subMinutes(30),
            ]);

            Message::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $demoUser->id,
                'body' => 'Thanks a lot! Really excited about how SocialHub is coming together.',
                'created_at' => now()->subMinutes(25),
            ]);

            Message::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $partner->id,
                'body' => 'Let me know once you test the real-time messaging features!',
                'created_at' => now()->subMinutes(5),
            ]);
        }
    }
}
