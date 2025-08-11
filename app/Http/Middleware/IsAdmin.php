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
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. No token provided or token is invalid.',
                ], 401);
            }

            $user = auth()->user();

            $roleName = strtolower($user->role->name ?? '');
            $allowedByName = in_array($roleName, ['admin', 'subadmin'], true);

            $allowedById = in_array((int)$user->role_id, [1, 5], true);

            if (!($allowedByName || $allowedById)) {  
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admins only.',
                ], 403);
            }

            return $next($request);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error in IsAdmin middleware.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
