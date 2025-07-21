<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\EventDirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventDirectionController extends Controller
{
    // 🟢 GET all (public)
    public function index()
    {
        $direction = EventDirection::first();

        if ($direction) {
            $direction->call_icon_url = $direction->call_icon 
                ? asset('storage/' . $direction->call_icon)
                : null;
        }

        return response()->json([
            'success' => true,
            'data' => $direction,
        ]);
    }

     // 🟢 GET by ID (public)
    public function show($id)
    {
        $direction = EventDirection::find($id);

        if (!$direction) {
            return response()->json([
                'success' => false,
                'message' => 'Event Direction not found.',
            ], 404);
        }

        $direction->call_icon_url = $direction->call_icon
            ? asset('storage/' . $direction->call_icon)
            : null;

        return response()->json([
            'success' => true,
            'data' => $direction,
        ]);
    }
    // 🔒 POST: Create (Admin only)
    public function store(Request $request)
    {
        try {
            if (EventDirection::count() >= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event Direction already exists. Please update instead.',
                ], 400);
            }

            $validated = $request->validate([
                'section_tagline' => 'required|string|max:255',
                'section_title' => 'required|string|max:255',
                'description' => 'required|string',
                'call_text' => 'required|string|max:255',
                'call_number' => 'required|string|max:255',
                'call_icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
                'counters' => 'required|array',
                'counters.*.title' => 'required|string|max:255',
                'counters.*.value' => 'required|integer',
            ]);

            $callIconPath = null;
            if ($request->hasFile('call_icon')) {
                $callIconPath = $request->file('call_icon')->store('event_direction', 'public');
            }

            $direction = EventDirection::create([
                'section_tagline' => $validated['section_tagline'],
                'section_title' => $validated['section_title'],
                'description' => $validated['description'],
                'call_text' => $validated['call_text'],
                'call_number' => $validated['call_number'],
                'call_icon' => $callIconPath,
                'counters' => $validated['counters'],
            ]);

            $direction->call_icon_url = $callIconPath ? asset('storage/' . $callIconPath) : null;

            return response()->json([
                'success' => true,
                'message' => 'Event Direction created successfully.',
                'data' => $direction,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating Event Direction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔒 PUT: Update (Admin only)
    public function update(Request $request, $id)
    {
        try {
            $direction = EventDirection::findOrFail($id);

            $validated = $request->validate([
                'section_tagline' => 'required|string|max:255',
                'section_title' => 'required|string|max:255',
                'description' => 'required|string',
                'call_text' => 'required|string|max:255',
                'call_number' => 'required|string|max:255',
                'call_icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
                'counters' => 'required|array',
                'counters.*.title' => 'required|string|max:255',
                'counters.*.value' => 'required|integer',
            ]);

            if ($request->hasFile('call_icon')) {
                // Delete old icon if exists
                if ($direction->call_icon && Storage::disk('public')->exists($direction->call_icon)) {
                    Storage::disk('public')->delete($direction->call_icon);
                }
                $direction->call_icon = $request->file('call_icon')->store('event_direction', 'public');
            }

            $direction->update([
                'section_tagline' => $validated['section_tagline'],
                'section_title' => $validated['section_title'],
                'description' => $validated['description'],
                'call_text' => $validated['call_text'],
                'call_number' => $validated['call_number'],
                'counters' => $validated['counters'],
            ]);

            $direction->call_icon_url = $direction->call_icon 
                ? asset('storage/' . $direction->call_icon)
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Event Direction updated successfully.',
                'data' => $direction,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating Event Direction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔒 DELETE: Remove (Admin only)
    public function destroy($id)
    {
        try {
            $direction = EventDirection::findOrFail($id);

            if ($direction->call_icon && Storage::disk('public')->exists($direction->call_icon)) {
                Storage::disk('public')->delete($direction->call_icon);
            }

            $direction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event Direction deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting Event Direction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
