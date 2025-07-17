<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if user is authenticated (token present & valid)
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. No token provided or token is invalid.'
                ], 401); // 401 Unauthorized
            }

            // Check if user is admin
            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admins only.'
                ], 403); // 403 Forbidden
            }

            // Pass request to next middleware/controller
            return $next($request);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error in IsAdmin middleware.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
