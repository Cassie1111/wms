<?php

namespace App\Http\Controllers\Api\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class HomeController extends BaseController
{
    const SUCCESS_CODE = 0;
    const ORDER_STATUS = [
        '5' => '待结算',
        '15' => '已结算',
    ];

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
        $page = $request->input('page', 1);
        $count = $request->input('count', 10);
        $start = ($page - 1) * $count;

        $url = '/account/getSettlementOrderList';
        $params = [
            'start' => $start,
            'count' => $count,
            'trade_status' => 5,
            'statistics' => 1,
        ];

        $response = $this->get($url, $params);

        if (self::SUCCESS_CODE === $response['code'] && isset($response['data']['order_list'])) {
            foreach ($response['data']['order_list'] as &$item) {
                $item['price'] = 'HK$ '.number_format($item['pay_price'] / 100, 2);
                $item['status'] = self::ORDER_STATUS[$item['trade_status']];
                $item['account_period_time'] = $item['account_period_time'] ?: '冻结中';
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getBgManageCount()
    {
        $url = '/index/getIndexBackgroundManagement';

        $response = $this->get($url);

        return $response;
    }

    public function getMainAccountInfo()
    {
        $params = [
            'user_no' => '88888888',
        ];

        $url = '/user/getUserDetail';
        $response = $this->get($url, $params);

        return $response;
    }
}
