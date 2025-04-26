<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force Accept header to application/json
        $request->headers->set('Accept', 'application/json');
        
        // Process the request
        $response = $next($request);
        
        // If there's an exception or error, ensure it returns JSON
        if ($response->getStatusCode() >= 400) {
            // Check if response is not already JSON
            if (!$response->headers->contains('Content-Type', 'application/json')) {
                $content = $response->getContent();
                $message = 'Server Error';
                
                // Try to extract error message from response
                if (is_string($content) && !empty($content)) {
                    $message = strip_tags($content);
                }
                
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], $response->getStatusCode());
            }
        }
        
        return $response;
    }
}