<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthController extends Controller
{
    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->get('code');
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);

            $tokenData = $response->json();

            if (!isset($tokenData['access_token'])) {
                return response()->json(['message' => 'Access token not received', 'error' => $tokenData], 400);
            }

            $googleUser = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['access_token'],
            ])->get('https://www.googleapis.com/oauth2/v2/userinfo')->json();

            $user = User::updateOrCreate(
                ['user_id' => $googleUser['id']],
                [
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'image' => $googleUser['picture'],
                    'email_verified_at' => now(),
                    'status' => true,
                ]
            );

            $jwtToken = JWTAuth::claims([
                'role' => 'api',
                'user_id' => $user->user_id
            ])->fromUser($user);

            $response = new Response([
                'user' => $user,
                'message' => 'Login successful!',
            ]);

            // $cookie = cookie(
            //     'token',        // Cookie name
            //     $jwtToken,      // Cookie value
            //     60 * 24,        // Expiry in minutes (24 hours)
            //     null,           // Path
            //     null,           // Domain (null = current domain)
            //     false,          // Secure (set true if using HTTPS)
            //     true            // HttpOnly (JS cannot access)
            // );

            $cookie = cookie(
                'token',        // Cookie name
                $jwtToken,      // Cookie value
                60 * 24,        // Expiry in minutes
                '/',            // Path
                'cricktrade-laravel-backend.onrender.com', // Domain
                true,           // Secure (Render uses HTTPS)
                true,           // HttpOnly
                false,          // Raw
                'None'          // SameSite (None = allow cross-site)
            );


            return $response->withCookie($cookie);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Google login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getUser(Request $request)
    {
        try {
            $token = $request->cookie('token');

            if (!$token) {
                return response()->json(['message' => 'Token Expired!'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json([
                'user' => $user
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token invalid or expired',
                'error' => $e->getMessage()
            ], 401);
        }
    }
    public function logout(Request $request)
    {
        try {
            $token = $request->cookie('token');

            if ($token) {
                $payload = JWTAuth::setToken($token)->getPayload();
                $userId = $payload->get('user_id');

                $user = User::find($userId);
                if ($user) {
                    $user->status = false;
                    $user->save();
                }
            }
            JWTAuth::setToken($token)->invalidate();
            $cookie = cookie()->forget('token');
            return response()->json(['message' => 'Logged out'])->withCookie($cookie);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to logout, please try again',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
