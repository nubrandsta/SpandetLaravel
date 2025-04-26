<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            // Check if user belongs to authorized groups (admin or auditor)
            $user = Auth::user();
            if ($user->group !== 'admin' && $user->group !== 'auditor') {
                // Log the user out if not in authorized groups
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'username' => 'Not authorized',
                ]);
            }
            
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Explicitly store the user ID in the session to ensure proper handling with UUIDs
            $request->session()->put('user_id', $user->id);
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials',
        ]);
    }
    
    public function show()
    {
        return view('auth.login');
    }
    
    public function logout(Request $request)
    {
        // Explicitly remove user_id from session before logout
        $request->session()->forget('user_id');
        
        Auth::logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}