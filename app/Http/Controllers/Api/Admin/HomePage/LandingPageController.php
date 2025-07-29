<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    // ✅ PUBLIC GET (for frontend or preview)
    public function index()
    {
        try {
            $content = LandingPage::first();

            return response()->json([
                'success' => true,
                'message' => $content ? 'Landing page content retrieved.' : 'No content found.',
                'data' => $content
            ]);
        } catch (\Throwable $e) {
            Log::error('LandingPage index error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Server error.',
                'data' => null
            ], 500);
        }
    }

    // ✅ ADMIN GET by ID (optional)
    public function show($id)
    {
        try {
            $content = LandingPage::find($id);

            return response()->json([
                'success' => true,
                'message' => $content ? 'Landing page found.' : 'Not found.',
                'data' => $content
            ]);
        } catch (\Throwable $e) {
            Log::error('LandingPage show error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Server error.',
                'data' => null
            ], 500);
        }
    }

    // ✅ CREATE/UPDATE LANDING PAGE (admin only)
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'date_range' => 'nullable|string',
                'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                
                // Statistics section
                'stats_section_title' => 'nullable|string|max:255',
                'exhibitors_count' => 'nullable|integer|min:0',
                'visitors_count' => 'nullable|integer|min:0',
                'panels_count' => 'nullable|integer|min:0',
                'stats_enabled' => 'nullable|boolean',
            ]);

            // ✅ HANDLE IMAGE UPLOAD
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Save to storage/app/public/landing
                $path = $request->file('image')->store('landing', 'public');

                // Save publicly accessible URL
                $validated['image'] = Storage::url($path); // /storage/landing/filename.jpg

                Log::info('Landing page image saved at', ['url' => $validated['image']]);
            } else {
                Log::info('No image uploaded.');
            }

            // ✅ GET EXISTING OR CREATE
            $content = LandingPage::first();

            if (!$content) {
                $content = LandingPage::create($validated);
                $message = 'Landing page created successfully.';
            } else {
                $content->update($validated);
                $message = 'Landing page updated successfully.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $content
            ]);
        } catch (\Throwable $e) {
            Log::error('LandingPage update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // ✅ DELETE
    public function destroy($id)
    {
        try {
            $content = LandingPage::find($id);
            if (!$content) {
                return response()->json(['success' => false, 'message' => 'Not found'], 404);
            }

            $content->delete();

            return response()->json(['success' => true, 'message' => 'Landing page deleted.']);
        } catch (\Throwable $e) {
            Log::error('LandingPage destroy error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }
}
