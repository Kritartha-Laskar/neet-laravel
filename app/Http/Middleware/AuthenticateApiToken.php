<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from Authorization header (Bearer <token>)
        $token = $request->bearerToken();

        // If no token in header, check if it's passed as query parameter 'api_token'
        if (!$token) {
            $token = $request->query('api_token');
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Token is missing.'
            ], 401);
        }

        // Find user by this API token
        $user = User::where('api_token', $token)
                    ->where('status', 'active')
                    ->whereNull('deleted_at')
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Invalid or expired token.'
            ], 401);
        }

        if ((int)$user->role === 3) {
            return response()->json([
                'success' => false,
                'message' => 'you are not authorised to acces this application'
            ], 403);
        }

        // Log the user in for the current request context
        Auth::setUser($user);

        return $next($request);
    }
}
