<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddPostController extends Controller
{
    public function addPost(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'caption' => 'required|string|max:255',
            'visibility' => 'required|string|in:public,private',
            'image_url' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    $file = $request->file('image_url');

                    // Check if input file
                    if ($file instanceof UploadedFile) {
                        if (!in_array($file->extension(), ['jpeg', 'jpg', 'png', 'gif', 'svg'])) {
                            $fail('The image must be a valid image file.');
                        }
                    }
                    // Check if input string
                    elseif (is_string($value)) {
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('The image must be a valid URL.');
                        }
                    } elseif (!is_null($value)) {
                        $fail('The image must be a valid URL or image file.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check post type
            $postType = 'text';
            if ($request->filled('image_url')) {
                $postType = $request->filled('caption') ? 'image_with_caption' : 'image';
            }

            // Create post
            $post = Post::create([
                'user_id' => Auth::user()->id,
                'caption' => $request->input('caption'),
                'image_url' => $request->input('image_url'),
                'post_type' => $postType,
                'visibility' => $request->input('visibility')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post added successfully',
                'data' => $post
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
