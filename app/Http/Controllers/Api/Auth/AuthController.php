<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Session;

class AuthController extends BaseController
{
    // 登陆超时错误码
    const LOGIN_TIME_OUT_STATUS = 40001;

    // 注册页签名 key
    const SECRET = 'ofashion_supplier_register';

    // 签名12小时有效
    const VALID_TIME = 43200;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 判断用户是否登陆，若登陆返回账号信息.
     * @return \Illuminate\Contracts\Routing\ResponseFactory|
     * \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkLogin()
    {
        $success = [
            'code' => 0,
            'msg' => '已登陆',
        ];

        $error = [
            'code' => 'A401',
            'msg' => '未登陆',
        ];

        $user_no = Session::get('user_no');

        // ajax返回401，则为未登录.
        if (empty($user_no)) {
            return response()->json($error);
        }

        // 获取缓存数据
        $cached_data = $this->getCachedUserInfo($user_no);

        if (! empty($cached_data)) {
            $success['data'] = $cached_data;
            $success['data']['token'] = Session::get($user_no.'_token');
            $success['left_menu'] = $this->getCachedLeftMenu($user_no) ?: [];

            return response()->json($success);
        }

        // 缓存过期，则从接口获取账号信息
        $response = $this->updateUserInfo($user_no);

        // 登陆过期或验签不通过
        if (self::LOGIN_TIME_OUT_STATUS === $response['code']) {
            $this->clearStorage();

            return response()->json($error);
        }

        // 账号已注销
        if (0 === $response['code'] && isset($response['data']['user_detail']['status'])
            && 0 === $response['data']['user_detail']['status']) {
            $error['code'] = 30002;
            $error['msg'] = '账号已注销';
            $this->clearStorage();

            return response()->json($error);
        }

        return response()->json($response);
    }

    /**
     * 用户登陆.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doLogin(Request $request)
    {
//        if (! $this->checkIpFrequencyRequest()) {
//            return response()->json([
//                'code' => 500,
//                'msg' => '请求次数过多，请联系客服',
//            ]);
//        }

        $username = $request->input('username');
        $password = $request->input('password');

        $params = [
            'username' => $username,
            'password' => $password,
        ];

        $url = '/user/login';

        $response = $this->post($url, $params);

        // 登陆成功
        if (0 === $response['code']) {
            $this->addStorage($response['data']);
            $user_no = $response['data']['user_no'];
            $token_name = $user_no.'_token';

            $token = uniqid();
            Session::put($token_name, $token);
            $response['data']['token'] = $token;
        }

        return response()->json($response);
    }

    /**
     * 退出登陆.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $this->clearStorage();

        $response = [
            'code' => 0,
            'msg' => 'success',
        ];

        return response()->json($response);
    }

    /**
     * 获取国家区号.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountryCode()
    {
        $url = '/user/getCountryCode';
        $data = $this->get($url);

        return response()->json($data);
    }

    /**
     * 获取注册页签名.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegisterSignature()
    {
        $key = uniqid();
        $t = time();
        $expire = $t + self::VALID_TIME;
        $str = 'expire_time='.$expire.'&key='.$key;
        $sign = hash_hmac('md5', $str, self::SECRET);

        $response = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'sign' => $sign,
                't' => $t,
                'key' => $key,
            ],
        ];

        return response()->json($response);
    }

    /**
     * 更新用户信息.
     * @param $user_no
     * @return array|mixed
     */
    private function updateUserInfo($user_no)
    {
        // 缓存过期，则从接口获取账号信息
        $response = $this->getUserDetail($user_no);

        if (0 === $response['code'] && isset($response['data']['user_detail']['status']) &&
            1 === $response['data']['user_detail']['status']) {
            $this->updateStorage($response['data']['user_detail']);

            $response['data']['user_detail']['token'] = Session::get($user_no.'_token');

            $success = [
                'code' => 0,
                'msg' => 'success',
                'data' => $response['data']['user_detail'],
                'left_menu' => $this->getCachedLeftMenu($user_no) ?: [],
            ];

            return $success;
        }

        return $response;
    }

    /**
     * 用户登陆成功后，将数据保存至session中.
     * @param $user_info
     */
    private function addStorage($data)
    {
        Session::put('user_no', $data['user_no']);
        Session::put('access_token', $data['access_token']);
        Session::put('account_type', $data['account_type']);

        $this->updateStorage($data);
    }
}
