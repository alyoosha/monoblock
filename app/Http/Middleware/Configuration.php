<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class Configuration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userCookie = $request->cookie('pc-constructor');
        $userSession = Session::get('pc-constructor');

        if(!$userSession) {
            $token = md5(rand(10,10));

            $userInfo = json_encode([
                'token_config' => $token,
            ]);

            Session::put('pc-constructor', $token);

            return $next($request)->withCookie(cookie('pc-constructor', $userInfo, env('SESSION_LIFETIME', 120), '/', null, null, false, false, null));
        }
        else {
            if(!$userCookie) {
                $token = Session::get('pc-constructor')[0];

                $userInfo = json_encode([
                    'token_config' => $token,
                ]);

                return $next($request)->withCookie(cookie('pc-constructor', $userInfo, env('SESSION_LIFETIME', 120), '/', null, null, false, false, null));
            }
            return $next($request);
        }
    }
}
