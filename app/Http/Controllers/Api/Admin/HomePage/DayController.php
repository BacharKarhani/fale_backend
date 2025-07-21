<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DayController extends Controller
{
    // 🟢 Get All Days (Admin)
    public function index()
    {
        $days = Day::with('events')->get();

        return response()->json([
            'success' => true,
            'data' => $days
        ]);
    }

        // 🟢 Get Single Day by ID
    public function show($id)
    {
        $day = Day::with('events')->find($id);

        if (!$day) {
            return response()->json([
                'success' => false,
                'message' => 'Day not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $day
        ]);
    }
    // 🟢 Create Day
    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_title' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        $day = Day::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Day created successfully',
            'data' => $day
        ]);
    }

    // 🟢 Update Day
    public function update(Request $request, $id)
    {
        Log::info("Update Day request received for ID: $id", [
            'payload' => $request->all()
        ]);

        $validated = $request->validate([
            'day_title' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        Log::info('Validated Data:', $validated);

        $day = Day::find($id);

        if (!$day) {
            Log::warning("Day with ID $id not found.");
            return response()->json([
                'success' => false,
                'message' => 'Day not found'
            ], 404);
        }

        $day->update($validated);

        Log::info('Day updated successfully:', $day->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Day updated successfully',
            'data' => $day
        ]);
    }

    // 🟢 Delete Day
    public function destroy($id)
    {
        $day = Day::find($id);

        if (!$day) {
            return response()->json([
                'success' => false,
                'message' => 'Day not found'
            ], 404);
        }

        $day->delete();

        return response()->json([
            'success' => true,
            'message' => 'Day deleted successfully'
        ]);
    }
}
