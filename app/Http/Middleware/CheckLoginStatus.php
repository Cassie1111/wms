<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckLoginStatus
{
    const LOGIN_TIME_OUT_STATUS = 40001;

    /**
     * 接口登陆验签不通过，则清除session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $code = $response->getData()->code;

        if (self::LOGIN_TIME_OUT_STATUS === $code) {
            $user_no = Session::get('user_no');
            Session::forget($user_no.'_token');
            Session::forget('user_no');
            Session::forget('access_token');
            Session::forget('account_type');
        }

        return $response;
    }
}
