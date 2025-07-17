<?php

namespace App\Http\Controllers\Api\Admin\HomePage;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\QueryException;

class BannerController extends Controller
{
    public function index()
    {
        try {
            $banners = Banner::all();

            return response()->json([
                'success' => true,
                'message' => 'Banners fetched successfully',
                'data' => $banners
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Banner $banner)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Banner fetched successfully',
                'data' => $banner
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'required|string', // Expecting image path
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'date' => 'nullable|string|max:255',
            ]);

            $banner = Banner::create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'image' => $request->image,
                'location' => $request->location,
                'date' => $request->date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully',
                'data' => $banner
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Banner $banner)
    {
        try {
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'image' => 'sometimes|string',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'date' => 'nullable|string|max:255',
            ]);

            $banner->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully',
                'data' => $banner
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Banner deleted successfully'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error during deletion',
                'error' => $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error during deletion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
