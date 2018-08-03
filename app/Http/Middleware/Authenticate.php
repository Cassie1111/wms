<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class Authenticate
{
    // 子账号类型
    const SUB_ACCOUNT_TYPE = 0;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // 如果laravel_session中无登录状态或者redis中没有登录信息数据（已过期）时，登出，进入登录页
        if (! Session::has('user_no')) {
            $error = [
                'code' => 'A401',
                'msg' => 'Unauthorized',
            ];

            // ajax返回401，则为未登录.
            return response($error);
        }

        $account_type = Session::get('account_type');

        if (self::SUB_ACCOUNT_TYPE === $account_type) {
            $sign = $request->input('sign');
            $nounce = $request->input('nounce');

            if (! $this->validateSignature($sign, $nounce)) {
                $error = [
                    'code' => 'A403',
                    'msg' => '无该操作权限',
                ];

                return response($error);
            }
        }

        return $response;
    }

    private function validateSignature($sign, $nounce)
    {
        if (empty($sign) || empty($nounce)) {
            return false;
        }

        $user_no = Session('user_no');
        $token = Session($user_no.'_token');
        $sign_new = md5($token.$user_no.'ofashion_wms'.$nounce);

        if ($sign !== $sign_new) {
            return false;
        }

        return true;
    }
}
