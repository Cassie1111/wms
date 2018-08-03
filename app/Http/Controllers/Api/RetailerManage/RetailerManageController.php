<?php

namespace App\Http\Controllers\Api\RetailerManage;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class RetailerManageController extends BaseController
{
    const RETAILER_TYPE = 1;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 获取参数
        $page = $request->input('page', 1);
        $count = 20;
        $start = ($page - 1) * $count;

        $params = [
            'start' => $start,
            'count' => $count,
        ];

        $url = '/retailer/getRetailerList';
        $data = $this->get($url, $params);

        if (! empty($data['data']['retailer_list'])) {
            foreach ($data['data']['retailer_list'] as &$item) {
                $item['retailer_type_cn'] = self::RETAILER_TYPE === $item['retailer_type'] ? '网络分销' : '-';
                $item['status_cn'] = $this->statusCn($item['status']);
            }
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 获取参数
        $retailer_no = $request->input('retailer_no');
        $retailer_name = $request->input('retailer_name');
        $contact_phone_number = $request->input('contact_phone_number');
        $contact_email_address = $request->input('contact_email_address');
        $country_code = $request->input('country_code');
        $mobile_account = $request->input('mobile_account');
        $password = $request->input('password');
        $status = $request->input('status');
        $params = [
            'retailer_name' => $retailer_name,
            'contact_phone_number' => $contact_phone_number,
            'contact_email_address' => $contact_email_address,
            'country_code' => $country_code,
            'mobile_account' => $mobile_account,
        ];

        if (! empty($retailer_no)) {
            $params['retailer_no'] = $retailer_no;
            $params['status'] = $status;
        }

        if (! empty($password)) {
            $params['password'] = $password;
        }

        $url = '/retailer/editRetailerToRepository';
        $data = $this->post($url, $params);

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = [
            'retailer_no' => $id,
        ];

        $url = '/retailer/getRetailerDetail';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    private function statusCn($status)
    {
        switch ($status) {
            case 0:
                return '删除';
            case 1:
                return '使用中';
            default:
                return '-';
        }
    }
}
