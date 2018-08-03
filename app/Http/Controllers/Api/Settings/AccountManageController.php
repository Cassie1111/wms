<?php

namespace App\Http\Controllers\Api\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class AccountManageController extends BaseController
{
    public static $STATUS = [
        1 => '使用中',
        0 => '已注销',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取账号列表.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $url = '/user/getUserList';
        $response = $this->get($url);

        if (0 === $response['code'] && ! empty($response['data']['user_list'])) {
            foreach ($response['data']['user_list'] as &$item) {
                $item['status_cn'] = self::$STATUS[$item['status']];
            }
        }

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 新建账号.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $username = $request->input('username');
        $realname = $request->input('realname');
        $password = $request->input('password');
        $country_code = $request->input('country_code');
        $mobile_phone = $request->input('mobile_phone');
        $email = $request->input('email');
        $identity_no = $request->input('identity_no');
        $permission = $request->input('permission');
        $department = $request->input('department');
        $duty = $request->input('duty');

        $params = [
            'username' => $username,
            'realname' => $realname,
            'password' => $password,
            'country_code' => $country_code,
            'mobile_phone' => $mobile_phone,
            'email' => $email,
            'identity_no' => $identity_no,
            'permission' => $permission,
            'department' => $department,
            'duty' => $duty,
        ];

        $url = '/user/editUserDetail';

        $response = $this->post($url, $params);

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_no)
    {
        $params = [
            'user_no' => $user_no,
        ];
        $url = '/user/getUserDetail';

        $response = $this->get($url, $params);

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 编辑账号.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_no)
    {
        //
        $password = $request->input('password');
        $realname = $request->input('realname');
        $country_code = $request->input('country_code');
        $mobile_phone = $request->input('mobile_phone');
        $email = $request->input('email');
        $identity_no = $request->input('identity_no');
        $permission = $request->input('permission');
        $department = $request->input('department');
        $duty = $request->input('duty');

        $params = [
            'user_no' => $user_no,
            'password' => $password,
            'realname' => $realname,
            'country_code' => $country_code,
            'mobile_phone' => $mobile_phone,
            'email' => $email,
            'identity_no' => $identity_no,
            'permission' => $permission,
            'department' => $department,
            'duty' => $duty,
        ];

        $url = '/user/editUserDetail';

        $response = $this->post($url, $params);

        return response()->json($response);
    }

    /**
     * 删除账号.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_no)
    {
        //
        $params = [
            'user_no' => $user_no,
            'status' => 0,
        ];
        $url = '/user/editUserStatus';

        $response = $this->post($url, $params);

        return response()->json($response);
    }

    /**
     * 获取国家区号.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountryCode()
    {
        $url = '/user/getCountryCode';

        $response = $this->get($url);

        return response()->json($response);
    }

    /**
     * 获取部门列表.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentList()
    {
        $url = '/user/getDepartment';
        $response = $this->get($url);

        return response()->json($response);
    }

    /**
     * 获取职务列表.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDutyList()
    {
        $url = '/user/getDuty';
        $response = $this->get($url);

        return response()->json($response);
    }
}
