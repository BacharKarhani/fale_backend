<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\SlidingText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class SlidingTextController extends Controller
{
    // ✅ Get all sliding texts
    public function index()
    {
        try {
            $texts = SlidingText::all();

            return response()->json([
                'success' => true,
                'data' => $texts
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliding texts.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Store new sliding text
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'hover_text' => 'nullable|string|max:255',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048'
            ]);

            $iconPath = null;

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('sliding_icons', 'public');
            }

            $slidingText = SlidingText::create([
                'title' => $request->title,
                'hover_text' => $request->hover_text,
                'icon' => $iconPath,
            ]);

            return response()->json([
                'success' => true,
                'data' => $slidingText,
                'message' => 'Sliding text created successfully.'
            ], 201);

        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $ve->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sliding text.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Update existing sliding text
    public function update(Request $request, $id)
    {
        try {
            $slidingText = SlidingText::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'hover_text' => 'nullable|string|max:255',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048'
            ]);

            if ($request->hasFile('icon')) {
                // Delete old icon if exists
                if ($slidingText->icon && Storage::disk('public')->exists($slidingText->icon)) {
                    Storage::disk('public')->delete($slidingText->icon);
                }
                $iconPath = $request->file('icon')->store('sliding_icons', 'public');
                $slidingText->icon = $iconPath;
            }

            $slidingText->title = $request->title;
            $slidingText->hover_text = $request->hover_text;
            $slidingText->save();

            return response()->json([
                'success' => true,
                'data' => $slidingText,
                'message' => 'Sliding text updated successfully.'
            ], 200);

        } catch (ModelNotFoundException $mnfe) {
            return response()->json([
                'success' => false,
                'message' => 'Sliding text not found.'
            ], 404);

        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $ve->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sliding text.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Delete sliding text
    public function destroy($id)
    {
        try {
            $slidingText = SlidingText::findOrFail($id);

            // Delete icon if exists
            if ($slidingText->icon && Storage::disk('public')->exists($slidingText->icon)) {
                Storage::disk('public')->delete($slidingText->icon);
            }

            $slidingText->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sliding text deleted successfully.'
            ], 200);

        } catch (ModelNotFoundException $mnfe) {
            return response()->json([
                'success' => false,
                'message' => 'Sliding text not found.'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sliding text.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
