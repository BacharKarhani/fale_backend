<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\BuyTicketContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuyTicketContentController extends Controller
{
    // 🟢 GET: Fetch BuyTicket content (public)
    public function index()
    {
        $content = BuyTicketContent::first();

        return response()->json([
            'success' => true,
            'data' => $content,
        ]);
    }


     public function show($id)
    {
        $content = BuyTicketContent::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'BuyTicket content not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $content,
        ]);
    }
    // 🟢 PUT: Update BuyTicket content (admin only)
    public function update(Request $request, $id)
    {
        $request->validate([
            'address'     => 'required|string|max:255',
            'timing'      => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_shown'    => 'sometimes|boolean',
        ]);

        $content = BuyTicketContent::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($content->image && Storage::disk('public')->exists($content->image)) {
                Storage::disk('public')->delete($content->image);
            }
            $content->image = $request->file('image')->store('buy_ticket', 'public');
        }

        $updateData = $request->only(['address', 'timing', 'title', 'description']);
        if ($request->has('is_shown')) {
            $updateData['is_shown'] = $request->is_shown;
        }

        $content->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'BuyTicket content updated successfully.',
            'data'    => $content,
        ]);
    }

    
}
