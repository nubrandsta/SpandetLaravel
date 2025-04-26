<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    /**
     * Handle API login request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate request
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Validate user group
                if (!$user->group || !in_array($user->group, ['admin', 'auditors'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not authorized'
                    ], 401);
                }

                // Generate a session token
                $token = Str::random(60);
                
                // Store token in session with user info
                $request->session()->put('api_token', $token);
                $request->session()->put('user_id', $user->id);
                
                return response()->json([
                    'status' => 'success',
                    'username' => $user->username,
                    'session' => $token
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validate token and return user information
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'session' => 'required'
            ]);
            
            $token = $validated['session'];
            $sessionToken = $request->session()->get('api_token');
            
            // Check if token exists and matches
            if ($sessionToken && $token === $sessionToken) {
                $userId = $request->session()->get('user_id');
                $user = Auth::loginUsingId($userId);
                
                if ($user) {
                    return response()->json([
                        'status' => 'success',
                        'username' => $user->username
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Token expired or invalid'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
}