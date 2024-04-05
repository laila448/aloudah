<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class UserType 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $usertype)
    {
        /*$user = auth()->user();

        if (!$user || !in_array(auth()->guard()->name, $guards)) {
           return response()->json(['msg'=>'Unauthorized'] , 403); 
        }*/
    //return $next($request);
    if(auth()->user()->type == $usertype){
        return $next($request);
    }

    return response()->json(['msg'=>'Unauthorized'] , 403); 
    }
}
