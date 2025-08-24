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

        if (
            !$user->role ||
            !in_array(strtolower($user->role->name), ['company', 'sponsorship'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only users with the role Company or Sponsorship can apply for Areas.'
            ], 403);
        }

        return $next($request);
    }
}
