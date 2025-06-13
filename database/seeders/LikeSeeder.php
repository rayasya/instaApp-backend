<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        foreach ($posts as $post) {
            // Gets 0-20 likes per post
            $likeCount = rand(0, 20);
            $likers = $users->where('id', '!=', $post->user_id)->random(min($likeCount, $users->count() - 1));

            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'user_id' => $liker->id,
                    'post_id' => $post->id,
                ]);
            }

            // Update count
            $post->update(['likes_count' => $post->likes()->count()]);
        }
    }
}
