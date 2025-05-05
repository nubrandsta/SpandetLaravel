<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if (!$user->group || !in_array($user->group, ['admin', 'auditors'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not authorized'
                    ], 401);
                }

                $token = Str::random(60);
                
                // Store in sessions table
                DB::table('sessions')->insert([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'payload' => $token,
                    'last_activity' => now()->getTimestamp()
                ]);

                // Cleanup expired sessions
                $expiration = 7200;
                DB::table('sessions')
                    ->where('last_activity', '<', now()->getTimestamp() - $expiration)
                    ->delete();

                return response()->json([
                    'status' => 'success',
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'group' => $user->group,
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

    public function validateToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'session' => 'required'
            ]);
            
            $token = $validated['session'];
            $session = DB::table('sessions')
                        ->where('payload', $token)
                        ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            $expiration = 7200; // 2 hours
            if ((now()->getTimestamp() - $session->last_activity) > $expiration) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expired'
                ], 401);
            }

            $user = Auth::loginUsingId($session->user_id);
            
            return response()->json([
                'status' => 'success',
                'username' => $user->username,
                'full_name' => $user->full_name,
                'group' => $user->group
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
        }

    public function uploadData(Request $request)
    {
        try {
            // Validate session token
            $validated = $request->validate([
                'session' => 'required'
            ]);
            
            $token = $validated['session'];
            $session = DB::table('sessions')
                        ->where('payload', $token)
                        ->first();

            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            // Check token expiration
            $expiration = 7200;
            if ((now()->getTimestamp() - $session->last_activity) > $expiration) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expired'
                ], 401);
            }

            // Get authenticated user
            $user = Auth::loginUsingId($session->user_id);

            // Validate request data
            $validatedData = $request->validate([
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
                'thoroughfare' => 'required|string',
                'subloc' => 'required|string',
                'locality' => 'required|string',
                'subadmin' => 'required|string',
                'adminArea' => 'required|string',
                'postalcode' => 'required|string',
                'spandukCount' => 'required|integer',
                'image' => 'required|image|max:2048'
            ]);

            // Store image directly in public_html/images directory using env variable
            $image = $request->file('image');
            
            // Generate a random 32 character filename with original extension for security
            $extension = $image->getClientOriginalExtension();
            $randomName = Str::random(32) . '.' . $extension;
            
            // Use environment variable for public_html path
            $publicHtmlPath = env('PUBLIC_HTML_PATH', '/home/spap8534/public_html') . '/images';
            $image->move($publicHtmlPath, $randomName);
            
            // Set the image URL to be directly accessible
            $imageUrl = '/images/' . $randomName;

            // Create data record
            DB::table('data')->insert([
                'id' => Str::uuid(),
                'uploader' => $user->username,
                'group' => $user->group,
                'lat' => $validatedData['lat'],
                'long' => $validatedData['long'],
                'thoroughfare' => $validatedData['thoroughfare'],
                'sublocality' => $validatedData['subloc'],
                'locality' => $validatedData['locality'],
                'subadmin' => $validatedData['subadmin'],
                'adminArea' => $validatedData['adminArea'],
                'postalcode' => $validatedData['postalcode'],
                'spandukCount' => $validatedData['spandukCount'],
                'imgURI' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data successfully uploaded'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}