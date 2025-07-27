<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\EventContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventContentController extends Controller
{
    // 🟢 GET: Fetch first EventContent (public)
    public function index()
    {
        $content = EventContent::first();

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    // 🟢 GET: Fetch EventContent by ID (public)
    public function show($id)
    {
        $content = EventContent::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Event content not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    // 🔐 POST: Create EventContent (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'section_tagline' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'icon_1_class' => 'required|string|max:100',
            'icon_1_subtitle' => 'required|string|max:255',
            'icon_1_subtagline' => 'required|string',
            'icon_2_class' => 'required|string|max:100',
            'icon_2_subtitle' => 'required|string|max:255',
            'icon_2_subtagline' => 'required|string',
            'event_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_shown' => 'sometimes|boolean',
        ]);

        if (EventContent::count() >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Event content already exists. Please edit instead.'
            ], 400);
        }

        $data = $request->only([
            'section_tagline', 'section_title',
            'icon_1_class', 'icon_1_subtitle', 'icon_1_subtagline',
            'icon_2_class', 'icon_2_subtitle', 'icon_2_subtagline'
        ]);

        if ($request->hasFile('event_image')) {
            $data['event_image'] = $request->file('event_image')->store('event_content', 'public');
        }

        $data['is_shown'] = $request->has('is_shown') ? $request->is_shown : true;

        $content = EventContent::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Event content created successfully.',
            'data' => $content
        ]);
    }

    // 🔐 PUT: Update EventContent (admin only)
    public function update(Request $request, $id)
    {
        $request->validate([
            'section_tagline' => 'required|string|max:255',
            'section_title' => 'required|string|max:255',
            'icon_1_class' => 'required|string|max:100',
            'icon_1_subtitle' => 'required|string|max:255',
            'icon_1_subtagline' => 'required|string',
            'icon_2_class' => 'required|string|max:100',
            'icon_2_subtitle' => 'required|string|max:255',
            'icon_2_subtagline' => 'required|string',
            'event_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_shown' => 'sometimes|boolean',
        ]);

        $content = EventContent::findOrFail($id);

        if ($request->hasFile('event_image')) {
            if ($content->event_image && Storage::disk('public')->exists($content->event_image)) {
                Storage::disk('public')->delete($content->event_image);
            }
            $content->event_image = $request->file('event_image')->store('event_content', 'public');
        }

        $updateData = $request->only([
            'section_tagline', 'section_title',
            'icon_1_class', 'icon_1_subtitle', 'icon_1_subtagline',
            'icon_2_class', 'icon_2_subtitle', 'icon_2_subtagline'
        ]);
        if ($request->has('is_shown')) {
            $updateData['is_shown'] = $request->is_shown;
        }

        $content->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Event content updated successfully.',
            'data' => $content
        ]);
    }

    // 🔐 DELETE: Delete EventContent (admin only)
    public function destroy($id)
    {
        $content = EventContent::findOrFail($id);

        if ($content->event_image && Storage::disk('public')->exists($content->event_image)) {
            Storage::disk('public')->delete($content->event_image);
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event content deleted successfully.'
        ]);
    }
}
