<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class ExplorePageController extends Controller
{
    public function explore()
    {
        try {
            // get all public posts
            $posts = Post::where('visibility', 'public')->get();
            return response()->json([
                'status' => true,
                'message' => 'posts retrieved successfully',
                'data' => $posts
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function postDetail($postId)
    {
        try {
            // get post detail
            $post = Post::find($postId)->with(['user', 'likes', 'comments'])->first();
            if (!$post || $post->visibility !== 'public') {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'post detail retrieved successfully',
                'data' => $post
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
