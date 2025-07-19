<?php

namespace App\Http\Controllers\Api\Admin\FAQ;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Get all FAQs (for user & admin)
     * GET /api/faqs or /api/admin/faqs
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Faq::all()
        ]);
    }

    /**
     * Create a new FAQ (Admin)
     * POST /api/admin/faqs
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'data' => $faq
        ], 201);
    }

    /**
     * Update an existing FAQ (Admin)
     * PUT /api/admin/faqs/{faq}
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'data' => $faq
        ]);
    }

    /**
     * Delete an FAQ (Admin)
     * DELETE /api/admin/faqs/{faq}
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }
}
