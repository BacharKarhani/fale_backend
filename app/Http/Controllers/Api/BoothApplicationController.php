<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothApplication;
use Illuminate\Support\Facades\Auth;

class BoothApplicationController extends Controller
{
    /**
     * Get all applications with relations (no slot).
     */
    public function index()
    {
        $applications = BoothApplication::with(['user', 'area'])->get();

        return response()->json([
            'applications' => $applications,
        ]);
    }

    /**
     * Store new application (Company only) WITHOUT slot_id.
     */
    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:booth_areas,id',
        ]);

        $user = Auth::user();

        if (!$user || strtolower($user->role->name) !== 'company') {
            return response()->json([
                'message' => 'Only users with the role Company can apply for booths.'
            ], 403);
        }

        // 1) ممنوع التقديم إذا البوث already approved لأيّ حدا
        $alreadyReserved = BoothApplication::where('area_id', $request->area_id)
            ->where('status', 'approved')
            ->exists();

        if ($alreadyReserved) {
            return response()->json([
                'message' => 'This booth has already been reserved.'
            ], 400);
        }

        // 2) ممنوع المستخدم يقدّم مرّة تانية على نفس البوث (حتى لو انتظار)
        $alreadyAppliedByUser = BoothApplication::where('area_id', $request->area_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyAppliedByUser) {
            return response()->json([
                'message' => 'You have already applied for this booth.'
            ], 400);
        }

        $application = BoothApplication::create([
            'user_id' => $user->id,
            'area_id' => $request->area_id,
            // ما بقا في slot_id
            'status'  => 'waiting',
        ]);

        return response()->json([
            'message'     => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    /**
     * Cancel (delete) an application if it belongs to the user and is NOT approved.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $application = BoothApplication::findOrFail($id);

        // بس صاحب الطلب فيه يلغي وإذا مش approved
        if ($application->user_id !== $user->id || $application->status === 'approved') {
            return response()->json([
                'message' => 'You cannot cancel this application.'
            ], 403);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application cancelled successfully.'
        ]);
    }

    /**
     * Update application status (admin flow typically).
     * Unified statuses to: approved | declined | waiting
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,declined,waiting',
        ]);

        $application = BoothApplication::findOrFail($id);

        // إذا عم نوافق هالطلب، لازم نضمن ما في approved تاني لنفس الـ area
        if ($request->status === 'approved') {
            $alreadyApproved = BoothApplication::where('area_id', $application->area_id)
                ->where('status', 'approved')
                ->where('id', '!=', $application->id)
                ->exists();

            if ($alreadyApproved) {
                return response()->json([
                    'message' => 'Another approved application already exists for this booth.'
                ], 400);
            }
        }

        $application->status = $request->status;
        $application->save();

        return response()->json([
            'message'     => 'Status updated',
            'application' => $application,
        ]);
    }
}
