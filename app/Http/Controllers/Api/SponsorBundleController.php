<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SponsorBundle;
use Illuminate\Support\Facades\Mail;
use App\Mail\SponsorshipBundleAppliedAdmin;
use App\Mail\SponsorshipBundleAppliedUser;

class SponsorBundleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => SponsorBundle::all()]);
    }

    public function show($id)
    {
        return response()->json(['data' => SponsorBundle::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:Diamond,Gold,Silver',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
        ]);

        $validated['benefits'] = json_encode($validated['benefits'] ?? []);

        $bundle = SponsorBundle::create($validated);

        return response()->json(['data' => $bundle], 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bundle = SponsorBundle::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|string|in:Diamond,Gold,Silver',
            'price' => 'sometimes|numeric',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
        ]);

        if (isset($validated['benefits'])) {
            $validated['benefits'] = json_encode($validated['benefits']);
        }

        $bundle->update($validated);

        return response()->json(['data' => $bundle]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        SponsorBundle::destroy($id);

        return response()->json(['message' => 'Deleted']);
    }

    public function apply(Request $request, $id)
    {
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bundle = SponsorBundle::findOrFail($id);
        $user = $request->user();

        Mail::to('info@lafeleb.com')->send(new SponsorshipBundleAppliedAdmin($user, $bundle));
        Mail::to($user->email)->send(new SponsorshipBundleAppliedUser($user, $bundle));

        return response()->json(['message' => 'Application sent']);
    }

    public function adminIndex(Request $request)
{
    if (!$request->user() || $request->user()->role_id !== 1) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json(['data' => SponsorBundle::all()]);
}

}
