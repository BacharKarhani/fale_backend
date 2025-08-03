<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. No token provided or token is invalid.'
            ], 401);
        }

        $user = auth()->user();

        // Make sure role relationship is loaded
        $user->loadMissing('role');

        if (!$user->role || strtolower($user->role->name) !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Only users with the role Company can apply for booths.'
            ], 403);
        }

        return $next($request);
    }
}
