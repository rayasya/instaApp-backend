<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Save;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        foreach ($users as $user) {
            // Svaes 0-10 posts per user
            $saveCount = rand(0, 10);
            $savedPosts = $posts->where('user_id', '!=', $user->id)->random(min($saveCount, $posts->count()));

            foreach ($savedPosts as $savedPost) {
                Save::firstOrCreate([
                    'user_id' => $user->id,
                    'post_id' => $savedPost->id,
                ]);
            }
        }

        // Update count
        foreach ($posts as $post) {
            $post->update(['saves_count' => $post->saves()->count()]);
        }
    }
}
