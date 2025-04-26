<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class Userauth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        
        // Custom condition check
        if (auth('api')->check() && auth('api')->user()->status=='1') {
            // If the condition is met, proceed with the request
            return $next($request);
        }

        // If the condition is not met, return an unauthorized response
        return response()->json(['error' => 'Unauthorized'], 401);
		
        
    }
    
}
