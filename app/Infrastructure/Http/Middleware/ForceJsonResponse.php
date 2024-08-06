<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $response = $next($request);
        
        if (!$response instanceof Response) {
            $response = response()->json($response);
        }
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
} 