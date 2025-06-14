<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfilePageController extends Controller
{
    public function profile()
    {
        try {
            $user = User::find(Auth::user()->id)->first();
            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User retrieved successfully',
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editProfile(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $validator = Validator::make($request->all(), [
                    'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                    'name' => 'required|string|max:255',
                    'bio' => 'nullable|string',
                    'profile_picture' => [
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
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $user->update([
                    'username' => $request->input('username'),
                    'name' => $request->input('name'),
                    'bio' => $request->input('bio'),
                    'profile_picture' => $request->input('profile_picture'),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'User updated successfully',
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
