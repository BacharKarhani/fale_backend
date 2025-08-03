<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothAreaSlot;
use Illuminate\Support\Facades\Auth;

class BoothAreaSlotController extends Controller
{
    // Public: Get all slots with area relation
    public function index()
    {
        return response()->json(['slots' => BoothAreaSlot::with('area')->get()]);
    }

    // Admin: Create slot
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'area_id' => 'required|exists:booth_areas,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $slot = BoothAreaSlot::create($request->all());

        return response()->json(['message' => 'Slot created', 'slot' => $slot], 201);
    }

    // Admin: Update slot
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $slot = BoothAreaSlot::findOrFail($id);

        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $slot->update($request->only(['start_time', 'end_time']));

        return response()->json(['message' => 'Slot updated', 'slot' => $slot]);
    }

    // Admin: Delete slot
    public function destroy($id)
    {
        $this->authorizeAdmin();

        BoothAreaSlot::destroy($id);

        return response()->json(['message' => 'Slot deleted']);
    }

    private function authorizeAdmin()
    {
        $user = Auth::user()->loadMissing('role');
        if (!$user || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }
}
