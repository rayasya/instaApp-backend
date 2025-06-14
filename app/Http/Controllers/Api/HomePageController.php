<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    public function posts()
    {
        try {
            // get all public posts
            $posts = Post::where('visibility', 'public')->latest()->with(['user', 'likes', 'comments'])->get();
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
    public function like($postId)
    {
        try {
            // check if post exists
            $post = Post::find($postId);
            if (!$post || $post->visibility !== 'public') {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found'
                ], 404);
            }

            // check if user has already liked the post
            if ($post->isLikedBy(Auth::user()->id)) {
                $post->likes()->where('user_id', Auth::user()->id)->delete();
                $post->decrement('likes_count');
                return response()->json([
                    'status' => true,
                    'message' => 'like removed'
                ], 200);
            }

            // like the post
            $post->likes()->create([
                'user_id' => Auth::user()->id
            ]);
            $post->increment('likes_count');
            return response()->json([
                'status' => true,
                'message' => 'post liked'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getComments($postId)
    {
        try {
            // check if post exists
            $post = Post::find($postId);
            if (!$post || $post->visibility !== 'public') {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found'
                ], 404);
            }

            // get comments
            return response()->json([
                'status' => true,
                'message' => 'comments retrieved successfully',
                'data' => $post->comments
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function comment($postId)
    {
        try {
            // check if post exists
            $post = Post::find($postId);
            if (!$post || $post->visibility !== 'public') {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found'
                ], 404);
            }

            // get comment input
            $comment = request()->input('comment');
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'comment is required'
                ], 422);
            }

            // add comment
            $newComment = $post->comments()->create([
                'user_id' => Auth::user()->id,
                'comment' => $comment,
            ]);

            // increment comments count
            $post->increment('comments_count');

            // return response
            return response()->json([
                'status' => true,
                'message' => 'comment added successfully',
                'data' => $newComment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function save($postId)
    {
        try {
            // check if post exists
            $post = Post::find($postId);
            if (!$post || $post->visibility !== 'public') {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found'
                ], 404);
            }

            // check if already saved post
            if ($post->isSavedBy(Auth::user()->id)) {
                $post->saves()->where('user_id', Auth::user()->id)->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'post unsaved'
                ], 200);
            }

            // save the post
            $post->saves()->create([
                'user_id' => Auth::user()->id
            ]);
            return response()->json([
                'status' => true,
                'message' => 'post saved'
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
