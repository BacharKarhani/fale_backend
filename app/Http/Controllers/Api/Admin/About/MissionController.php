<?php

namespace App\Http\Controllers\Api\Admin\About;

use Illuminate\Http\Request;
use App\Models\Mission;
use App\Http\Controllers\Controller;

class MissionController extends Controller
{
    // 🟢 Public: Get the latest mission & vision
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Mission::latest()->first()
        ]);
    }

    // 🟡 Admin: Get all records (if needed for admin table)
    public function getAll()
    {
        return response()->json([
            'success' => true,
            'data' => Mission::latest()->get()
        ]);
    }

    // 🟢 Admin: Create new
    public function store(Request $request)
    {
        $request->validate([
            'vision' => 'required|string',
            'mission' => 'required|string',
        ]);

        $mission = Mission::create($request->only(['vision', 'mission']));

        return response()->json([
            'success' => true,
            'message' => 'Mission created successfully.',
            'data' => $mission
        ]);
    }

    // 🟡 Admin: Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'vision' => 'required|string',
            'mission' => 'required|string',
        ]);

        $mission = Mission::findOrFail($id);
        $mission->update($request->only(['vision', 'mission']));

        return response()->json([
            'success' => true,
            'message' => 'Mission updated successfully.',
            'data' => $mission
        ]);
    }

    // 🔴 Admin: Delete
    public function destroy($id)
    {
        $mission = Mission::findOrFail($id);
        $mission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mission deleted successfully.'
        ]);
    }
}
