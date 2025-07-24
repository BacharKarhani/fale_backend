<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\HomeVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class HomeVideoController extends Controller
{
    /**
     * 🔓 Public API: Get home video content
     */
    public function index()
    {
        try {
            $homeVideo = HomeVideo::where('is_active', true)->first();

            if ($homeVideo) {
                if ($homeVideo->thumbnail) {
                    $homeVideo->thumbnail_url = asset('storage/' . $homeVideo->thumbnail);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Home video fetched successfully',
                'data' => $homeVideo
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔒 Admin API: Update home video content
     */
    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $homeVideo = HomeVideo::findOrFail($id);

        $data = $request->only(['title', 'description', 'is_active']);

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            \Log::info('Video file detected in request');
            if ($homeVideo->video_file && Storage::disk('public')->exists($homeVideo->video_file)) {
                Storage::disk('public')->delete($homeVideo->video_file);
            }
            $data['video_file'] = $request->file('video_file')->store('home_videos', 'public');
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            \Log::info('Thumbnail detected in request');
            if ($homeVideo->thumbnail && Storage::disk('public')->exists($homeVideo->thumbnail)) {
                Storage::disk('public')->delete($homeVideo->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('home_videos', 'public');
        }

        $homeVideo->update($data);

        // Add URLs to response
        if ($homeVideo->thumbnail) {
            $homeVideo->thumbnail_url = asset('storage/' . $homeVideo->thumbnail);
        }
        $video_url = asset('storage/' . $homeVideo->video_file);

        return response()->json([
            'success' => true,
            'message' => 'Home video updated successfully',
            'data' => $homeVideo
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);

    } catch (QueryException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database error',
            'error' => $e->getMessage()
        ], 500);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * 🔒 Admin API: Get home video by ID (for admin editing)
     */
    public function show($id)
    {
        try {
            $homeVideo = HomeVideo::findOrFail($id);

            if ($homeVideo->thumbnail) {
                $homeVideo->thumbnail_url = asset('storage/' . $homeVideo->thumbnail);
            }

            return response()->json([
                'success' => true,
                'message' => 'Home video fetched successfully',
                'data' => $homeVideo
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Home video not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * 🔒 Admin API: Create home video content
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_file' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:51200', // 50MB max
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'is_active' => 'sometimes|boolean',
            ]);

            // If creating a new active video, deactivate others
            if ($request->get('is_active', true)) {
                HomeVideo::where('is_active', true)->update(['is_active' => false]);
            }

            $data = $request->only(['title', 'description']);
            $data['is_active'] = $request->get('is_active', true);

            // Handle video file upload
            if ($request->hasFile('video_file')) {
                $data['video_file'] = $request->file('video_file')->store('home_videos', 'public');
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('home_videos', 'public');
            }

            $homeVideo = HomeVideo::create($data);

            // Add URLs to response
            if ($homeVideo->thumbnail) {
                $homeVideo->thumbnail_url = asset('storage/' . $homeVideo->thumbnail);
            }

            return response()->json([
                'success' => true,
                'message' => 'Home video created successfully',
                'data' => $homeVideo
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔒 Admin API: Update video status only
     */
    public function updateStatus(Request $request, $id)
    {
        Log::info('updateStatus called', ['id' => $id, 'payload' => $request->all()]);

        try {
            $validated = $request->validate([
                'is_active' => ['required', 'boolean'],
            ]);

            $isActive = $request->input('is_active');

            $homeVideo = HomeVideo::findOrFail($id);

            if ($isActive) {
                HomeVideo::where('is_active', true)->where('id', '!=', $id)->update(['is_active' => false]);
            }

            $homeVideo->update(['is_active' => $isActive]);

            return response()->json([
                'success' => true,
                'message' => 'Video status updated successfully',
                'data' => [
                    'id' => $homeVideo->id,
                    'title' => $homeVideo->title,
                    'is_active' => $homeVideo->is_active
                ]
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Validation error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Server error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔒 Admin API: Delete home video
     */
    public function destroy($id)
    {
        try {
            $homeVideo = HomeVideo::findOrFail($id);

            // Delete video file if exists
            if ($homeVideo->video_file && Storage::disk('public')->exists($homeVideo->video_file)) {
                Storage::disk('public')->delete($homeVideo->video_file);
            }

            // Delete thumbnail if exists
            if ($homeVideo->thumbnail && Storage::disk('public')->exists($homeVideo->thumbnail)) {
                Storage::disk('public')->delete($homeVideo->thumbnail);
            }

            $homeVideo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Home video deleted successfully'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error during deletion',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
