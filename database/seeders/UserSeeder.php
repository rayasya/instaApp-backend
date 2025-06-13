<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'username' => 'user' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'),
                'full_name' => 'User ' . $i,
                'bio' => 'This is user ' . $i . ' bio.',
                'profile_picture' => 'https://via.placeholder.com/150',
                'posts_count' => 0,
                'followers_count' => 0,
                'following_count' => 0
            ]);
        }
    }
}
