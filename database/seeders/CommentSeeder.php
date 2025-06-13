<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        foreach ($posts as $post) {
            // Gets 0-10 comments per post
            $commentCount = rand(0, 10);

            for ($i = 0; $i < $commentCount; $i++) {
                $commenter = $users->random();

                Comment::create([
                    'user_id' => $commenter->id,
                    'post_id' => $post->id,
                    'comment' => $this->getRandomComment(),
                    'created_at' => now()->subDays(rand(0, 15)),
                ]);
            }

            // Update count
            $post->update(['comments_count' => $post->comments()->count()]);
        }
    }

    private function getRandomComment()
    {
        $comments = [
            'Great post! 👍',
            'Love this! ❤️',
            'Amazing! 🔥',
            'Nice one! 👌',
            'Awesome! 🎉',
            'Beautiful! 😍',
            'Perfect! ✨',
            'Incredible! 🤩',
            'So cool! 😎',
            'Fantastic! 🌟',
        ];

        return $comments[rand(0, count($comments) - 1)];
    }
}
