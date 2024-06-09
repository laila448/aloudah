<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class DriverMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function authenticate($request, array $guards)
    {
        try{

            if ($this->auth->guard('driver')->check()) {
                return $this->auth->shouldUse('driver');
            }

           $this->unauthenticated($request, ['driver']);
        }
        catch (TokenExpiredException $e){
            return  response()->json(['msg'=>'Unauthenticated user']);
        }catch (JWTException $e)
        {
            return  response()->json(['msg'=>'token_invaled',$e ->getMessage()]);
        }

    }
}
