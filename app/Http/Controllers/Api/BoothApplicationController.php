<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothApplication;
use Illuminate\Support\Facades\Auth;

class BoothApplicationController extends Controller
{
    // Get all applications with relations
    public function index()
    {
        return BoothApplication::with(['user', 'area', 'slot'])->get();
    }

    // Store new application (only company)
    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:booth_areas,id',
            'slot_id' => 'required|exists:booth_area_slots,id',
        ]);

        $user = Auth::user();

        if (!$user || strtolower($user->role->name) !== 'company') {
            return response()->json(['message' => 'Only users with the role Company can apply for booths.'], 403);
        }

        $alreadyApplied = BoothApplication::where('area_id', $request->area_id)
            ->where('slot_id', $request->slot_id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['message' => 'This slot has already been reserved.'], 400);
        }

        $application = BoothApplication::create([
            'user_id' => $user->id,
            'area_id' => $request->area_id,
            'slot_id' => $request->slot_id,
            'status' => 'waiting',
        ]);

        return response()->json(['message' => 'Application submitted successfully', 'application' => $application], 201);
    }

    public function cancel($id)
{
    $user = Auth::user();
    $application = BoothApplication::findOrFail($id);

    // Only allow to cancel if this user's application and status is NOT approved
    if (
        $application->user_id !== $user->id ||
        $application->status === 'approved'
    ) {
        return response()->json(['message' => 'You cannot cancel this application.'], 403);
    }

    $application->delete();

    return response()->json(['message' => 'Application cancelled successfully.']);
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,declined,pending'
        ]);

        $application = BoothApplication::findOrFail($id);
        $application->status = $request->status;
        $application->save();

        return response()->json(['message' => 'Status updated', 'application' => $application]);
    }

}
