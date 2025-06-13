<?php

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Follows 5-15 random other users per user
            $followCount = rand(5, 15);
            $otherUsers = $users->where('id', '!=', $user->id)->random($followCount);

            foreach ($otherUsers as $otherUser) {
                Follow::firstOrCreate([
                    'follower_id' => $user->id,
                    'following_id' => $otherUser->id,
                ]);
            }
        }

        // Update counts
        foreach ($users as $user) {
            $user->update([
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
            ]);
        }
    }
}
