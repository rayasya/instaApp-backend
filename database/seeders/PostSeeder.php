<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create 3-8 posts per user
            $postsCount = rand(3, 8);

            for ($i = 0; $i < $postsCount; $i++) {
                $postType = ['text', 'image', 'image_with_caption'][rand(0, 2)];
                $visibility = ['public', 'private'][rand(0, 1)];

                Post::create([
                    'user_id' => $user->id,
                    'caption' => $this->getRandomCaption(),
                    'image_url' => $postType !== 'text' ? 'https://picsum.photos/600/400?random=' . rand(1, 1000) : null,
                    'post_type' => $postType,
                    'visibility' => $visibility,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        // Update counts
        foreach ($users as $user) {
            $user->update(['posts_count' => $user->posts()->count()]);
        }
    }

    private function getRandomCaption()
    {
        $captions = [
            'Beautiful sunset today! 🌅',
            'Great day with friends! 👫',
            'New adventure begins! 🎉',
            'Loving this weather ☀️',
            'Coffee time ☕',
            'Weekend vibes 🎵',
            'Amazing view from here! 🏔️',
            'Delicious food! 🍕',
            'Book recommendation 📚',
            'Workout done! 💪',
        ];

        return $captions[rand(0, count($captions) - 1)];
    }
}
