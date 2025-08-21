<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JwtFromCookie
{
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        try {
            if ($role === 'admin') {
                $cookieName = 'admin_token';
                $guard = 'admin';
            } else {
                $cookieName = 'token';
                $guard = 'api';
            }

            $token = $request->cookie($cookieName);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found'
                ], 401);
            }

            auth()->shouldUse($guard);

            try {
                $user = JWTAuth::setToken($token)->authenticate();
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expired'
                ], 401);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalid'
                ], 401);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token error: ' . $e->getMessage()
                ], 401);
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($role ?? 'user') . ' not found'
                ], 404);
            }

            // Extra role check for admin
            if ($role === 'admin' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: Admins only'
                ], 403);
            }

            $request->merge(['auth_user' => $user]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: ' . $e->getMessage()
            ], 401);
        }
    }
}