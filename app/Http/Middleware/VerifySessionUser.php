<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifySessionUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated but session doesn't have user_id
        if (Auth::check() && !$request->session()->has('user_id')) {
            // Log the issue for debugging
            Log::warning('User authenticated but session missing user_id. Adding it now.', [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId()
            ]);
            
            // Fix the session by adding the user_id
            $request->session()->put('user_id', Auth::id());
        }
        
        return $next($request);
    }
}