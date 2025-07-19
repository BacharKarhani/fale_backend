<?php

namespace App\Http\Controllers\Api\Admin\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscriptions,email',
        ]);

        Subscription::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully!',
        ], 201);
    }

    public function index()
    {
        $subscriptions = Subscription::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }
}
