<?php

namespace App\Http\Controllers\Api\Admin\Homepage;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // 🔓 Public API: List all blogs
    public function index()
    {
        $blogs = Blog::orderBy('published_at', 'desc')->get();

        // Add full image URL
        $blogs->transform(function ($blog) {
            $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;
            return $blog;
        });

        return response()->json(['success' => true, 'data' => $blogs]);
    }

    // 🔓 Public API: Show a single blog
    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
        }

        $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;

        return response()->json(['success' => true, 'data' => $blog]);
    }

    // 🔒 Admin API: Create a blog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blog_images', 'public');
        }

        $blog = Blog::create($validated);
        $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;

        return response()->json(['success' => true, 'data' => $blog], 201);
    }

    // 🔒 Admin API: Update a blog
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('blog_images', 'public');
        }

        $blog->update($validated);
        $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;

        return response()->json(['success' => true, 'data' => $blog]);
    }

    // 🔒 Admin API: Delete a blog
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
        }

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(['success' => true, 'message' => 'Blog deleted']);
    }
}
