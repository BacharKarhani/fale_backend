<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BoothApplicationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'area_id' => 'required|exists:booth_areas,id',
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated: User not found'
                ], 401);
            }

            // 🔍 Debug log
            Log::info('AUTH USER:', [
                'id' => $user->id,
                'role' => $user->role
            ]);

            // ✅ Ensure role exists and is 'company'
if (!$user->role || strtolower($user->role->name) !== 'company') {
                return response()->json([
                    'message' => 'Only users with the role Company can apply for booths.'
                ], 403);
            }

            $exists = BoothApplication::where('user_id', $user->id)
                ->where('area_id', $request->area_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'You have already applied for this booth area.',
                ], 400);
            }

            BoothApplication::create([
                'user_id' => $user->id,
                'area_id' => $request->area_id,
                'status' => 'waiting',
            ]);

            return response()->json([
                'message' => 'Application submitted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
