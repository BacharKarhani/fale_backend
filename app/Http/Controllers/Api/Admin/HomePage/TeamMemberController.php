<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::all();
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

        public function show($id)
    {
        $member = TeamMember::find($id);
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $member]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'hover_text' => 'nullable|string',
            'link' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_shown' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('team_images', 'public');
        }

        $validated['is_shown'] = $request->has('is_shown') ? $request->is_shown : true;

        $member = TeamMember::create($validated);

        return response()->json([
            'success' => true,
            'data' => $member
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $member = TeamMember::find($id);
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|max:255',
            'hover_text' => 'nullable|string',
            'link' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_shown' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($member->image && Storage::disk('public')->exists($member->image)) {
                Storage::disk('public')->delete($member->image);
            }
            $validated['image'] = $request->file('image')->store('team_images', 'public');
        }

        if ($request->has('is_shown')) {
            $validated['is_shown'] = $request->is_shown;
        }

        $member->update($validated);

        return response()->json(['success' => true, 'data' => $member]);
    }

    public function destroy($id)
    {
        $member = TeamMember::find($id);
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member not found'], 404);
        }

        if ($member->image && Storage::disk('public')->exists($member->image)) {
            Storage::disk('public')->delete($member->image);
        }

        $member->delete();

        return response()->json(['success' => true, 'message' => 'Member deleted']);
    }
}
