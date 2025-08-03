<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothArea;
use Illuminate\Support\Facades\Auth;

class BoothAreaController extends Controller
{
    // Public: Get all booth areas
    public function index()
    {
        return BoothArea::all();
    }

    // Admin: Create a booth area
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'label' => 'required|string|max:255',
            'dimensions' => 'required|string|max:50',
            'price' => 'required|numeric',
            'benefits' => 'nullable|string',
            'ticket_number' => 'nullable|string|max:255', // New field
        ]);

        $area = BoothArea::create($request->all());

        return response()->json(['message' => 'Booth area created', 'area' => $area], 201);
    }

    // Admin: Update booth area
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $area = BoothArea::findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:255',
            'dimensions' => 'required|string|max:50',
            'price' => 'required|numeric',
            'benefits' => 'nullable|string',
            'ticket_number' => 'nullable|string|max:255', // New field
        ]);

        $area->update($request->all());

        return response()->json(['message' => 'Booth area updated', 'area' => $area]);
    }

    // Admin: Delete booth area
    public function destroy($id)
    {
        $this->authorizeAdmin();

        BoothArea::destroy($id);

        return response()->json(['message' => 'Booth area deleted']);
    }

    private function authorizeAdmin()
    {
        $user = Auth::user()->loadMissing('role');
        if (!$user || strtolower($user->role->name) !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }
}
