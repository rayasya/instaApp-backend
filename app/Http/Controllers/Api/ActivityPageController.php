<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityPageController extends Controller
{
    public function activity()
    {
        $userId = Auth::id();

        try {
            // Get all like last 7 days
            $likeActivities = Like::where('created_at', '>', now()->subDays(7))
                ->whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with(['user', 'post'])
                ->get()
                ->map(function ($like) {
                    return [
                        'username' => $like->user->username,
                        'action' => 'liked',
                        'post_id' => $like->post_id,
                        'created_at' => $like->created_at,
                    ];
                });

            // Get all comment last 7 days
            $commentActivities = Comment::where('created_at', '>', now()->subDays(7))
                ->whereHas('post', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with(['user', 'post'])
                ->get()
                ->map(function ($comment) {
                    return [
                        'username' => $comment->user->username,
                        'action' => 'commented',
                        'post_id' => $comment->post_id,
                        'created_at' => $comment->created_at,
                    ];
                });

            // Merge likeActivities and commentActivities
            $activities = $likeActivities
                ->merge($commentActivities)
                ->sortByDesc('created_at')
                ->values()
                ->map(function ($activity) {
                    return [
                        'username' => $activity['username'],
                        'action' => $activity['action'],
                        'post_id' => $activity['post_id'],
                        'time_ago' => $activity['created_at']->diffForHumans(),
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'activities retrieved successfully',
                'data' => $activities,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
