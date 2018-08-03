<?php

namespace App\Http\Controllers\Api\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Excel;

class SettingOrderListController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        //
        $is_search = $request->input('is_search', 0);
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $product_model = $request->input('product_model');
        $goods_name = $request->input('goods_name');
        $code = $request->input('code');
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        $count = 10;
        $start = $count * ($page - 1);
        $logistic_method = $request->input('logistic_method');
        $param = [
            'is_search' => $is_search,
            'trade_no' => $trade_no,
            'start' => $start,
            'count' => $count,
            'trade_status' => $trade_status,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'goods_name' => $goods_name,
            'code' => $code,
            'product_model' => $product_model,
            'logistic_method' => $logistic_method,
        ];

        $api = '/trade/getRetailTradeOrderList';
        $data = $this->get($api, $param);

        if (! empty($data['data']['retail_trade_list'])) {
            foreach ($data['data']['retail_trade_list'] as &$item) {
                $item['new_trade_price'] = empty($item['trade_price']) ? 0 :
                    number_format($item['trade_price'] / 100, 2, '.', '');
                $item['new_pay_price'] = empty($item['pay_price']) ? 0 :
                    number_format($item['pay_price'] / 100, 2, '.', '');
                $item['new_shipping_rate_total'] = empty($item['shipping_rate_total']) ? 0 :
                    number_format($item['shipping_rate_total'] / 100, 2, '.', '');
                $purchase_status = $item['trade_status'];
                $item['changeFlag'] = false;
                $item['is_check'] = false;

                foreach ($item['trade_detail'] as &$goodsItem) {
                    $goodsItem['cover_image']['new_path'] = empty($goodsItem['cover_image']['path']) ? '' :
                        cdn_image($goodsItem['cover_image']['path'], 'XS');
                    $goodsItem['new_sku_price'] = empty($goodsItem['sku_price']) ? 0 :
                        number_format($goodsItem['sku_price'] / 100, 2);
                }

                switch ($purchase_status) {
                    case 2:
                        $item['trade_status_text'] = '待付款';
                        break;
                    case 3:
                        $item['trade_status_text'] = '待发货';
                        break;
                    case 4:
                        $item['trade_status_text'] = '待签收';
                        break;
                    case 5:
                        $item['trade_status_text'] = '交易成功';
                        break;
                    case 9:
                        $item['trade_status_text'] = '订单关闭';
                        break;
                }
            }
        }

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $is_search = $request->input('is_search', 0);
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $product_model = $request->input('product_model');
        $goods_name = $request->input('goods_name');
        $code = $request->input('code');
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        $count = 500;
        $start = $count * ($page - 1);
        $logistic_method = $request->input('logistic_method');
        $check_list = $request->input('check_list', null);

        if (!empty($check_list)) {
            $param = [
                'is_search' => $is_search,
                'trade_no' => implode(',', $check_list),
            ];
        } else {
            $param = [
                'is_search' => $is_search,
                'trade_no' => $trade_no,
                'start' => $start,
                'count' => $count,
                'trade_status' => $trade_status,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'goods_name' => $goods_name,
                'code' => $code,
                'product_model' => $product_model,
                'logistic_method' => $logistic_method,
            ];
        }
        $api = '/trade/exportRetailOrder';
        $data = $this->get($api, $param);

        if (! empty($data['data']['detailList'])) {
            foreach ($data['data']['detailList'] as &$item) {
                $item['trade_status'] = $this->tradeStatusToCn($item['trade_status']);
                $item['logistic_method'] = $this->logisticTypeToCn($item['logistic_method']);
                $item['pay_channel'] = $this->payChannel($item['pay_channel']);
                $item['product_sku_price'] = ! empty($item['product_sku_price']) ?
                    number_format($item['product_sku_price'] / 100, 2) : '';
                $item['pay_price_total'] = ! empty($item['pay_price_total']) ?
                    number_format($item['pay_price_total'] / 100, 2) : '';
                $item['pay_price'] = ! empty($item['pay_price']) ?
                    number_format($item['pay_price'] / 100, 2) : '';
                $item['shipping_rate'] = ! empty($item['shipping_rate']) ?
                    number_format($item['shipping_rate'] / 100, 2) : '';
            }

            $orderList = $data['data']['detailList'];
        } else {
            $orderList = [];
        }

        $title = mb_convert_encoding('买手宝商品管理系统【OFASHION】-订单', 'UTF-8');
        Excel::create($title, function ($excel) use ($orderList) {
            $excel->sheet('买手宝商品管理系统【OFASHION】-订单', function ($sheet) use ($orderList) {
                $sheet->row(1, [
                    '订单编号',
                    '采购单编号',
                    '订单状态',
                    '买家ID',
                    '买家昵称',
                    '品牌',
                    '品牌货号',
                    '货品名称',
                    '颜色',
                    '规格/尺码',
                    '供货价(HK$)',
                    '数量',
                    '货品总价',
                    '运费（HK$）',
                    '下单时间',
                    '支付时间',
                    '发货时间',
                    '签收时间',
                    '结算金额',
                    '结算方式',
                    '商品条形码（O）',
                    '商家编码（O）',
                    'RFID编码',
                    '配送方式',
                    '收货人姓名1',
                    '收货人电话1',
                    '配送地址1',
                    '配送物流1',
                    '配送物流单号1',
                ]);

                foreach ($orderList as $key => $orderItem) {
                    $sheet->row($key + 2, [
                        empty($orderItem['trade_no']) ? null : $orderItem['trade_no'],
                        empty($orderItem['purchase_no']) ? null : $orderItem['purchase_no'],
                        empty($orderItem['trade_status']) ? null : $orderItem['trade_status'],
                        empty($orderItem['retailer_no']) ? null : $orderItem['retailer_no'],
                        empty($orderItem['retailer_name']) ? null : $this->userTextEncode($orderItem['retailer_name']),
                        empty($orderItem['brand_name_e']) ? null : $orderItem['brand_name_e'],
                        empty($orderItem['product_model']) ? null : $orderItem['product_model'],
                        empty($orderItem['goods_name']) ? null : $this->userTextEncode($orderItem['goods_name']),
                        empty($orderItem['sku_color']) ? null : $this->userTextEncode($orderItem['sku_color']),
                        empty($orderItem['sku_spec']) ? null : $this->userTextEncode($orderItem['sku_spec']),
                        empty($orderItem['product_sku_price']) ? null : $orderItem['product_sku_price'],
                        empty($orderItem['goods_sku_num']) ? null : $orderItem['goods_sku_num'],
                        empty($orderItem['pay_price']) ? null : $orderItem['pay_price'],
                        empty($orderItem['shipping_rate']) ? null : $orderItem['shipping_rate'],
                        empty($orderItem['create_time']) ? null : $orderItem['create_time'],
                        empty($orderItem['pay_time']) ? null : $orderItem['pay_time'],
                        empty($orderItem['delivery_time']) ? null : $orderItem['delivery_time'],
                        empty($orderItem['sign_time']) ? null : $orderItem['sign_time'],
                        empty($orderItem['pay_price_total']) ? null : $orderItem['pay_price_total'],
                        empty($orderItem['pay_channel']) ? null : $orderItem['pay_channel'],
                        empty($orderItem['bar_code']) ? null : $orderItem['bar_code'],
                        empty($orderItem['sku_code']) ? null : $orderItem['sku_code'],
                        empty($orderItem['rfid_list']) ? null : $orderItem['rfid_list'],
                        empty($orderItem['logistic_method']) ? null : $orderItem['logistic_method'],
                        empty($orderItem['order_delivery_info'][0]['recipient_name']) ? null : $this->userTextEncode($orderItem['order_delivery_info'][0]['recipient_name']),
                        empty($orderItem['order_delivery_info'][0]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][0]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][0]['recipient_address']) ? null : $this->userTextEncode($orderItem['order_delivery_info'][0]['recipient_address']),
                        empty($orderItem['order_delivery_info'][0]['transport_company']) ? null : $orderItem['order_delivery_info'][0]['transport_company'],
                        empty($orderItem['order_delivery_info'][0]['package_no']) ? null : $orderItem['order_delivery_info'][0]['package_no'],
                    ]);
                }
            });
        })->export('xls');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user_info = self::$user_info;
        $trade_no = $request->input('trade_no');
        $operator_id = $user_info['user_no'];
        $refund_fee = $request->input('refund_fee');
        $operator_name = $user_info['username'];

        $params = [
            'trade_no' => $trade_no,
            'operator_id' => $operator_id,
            'refund_fee' => $refund_fee,
            'operator_name' => $operator_name,
        ];

        $api = '/trade/stockoutRefund';
        $data = $this->post($api, $params);

        return response()->json($data);
    }

    public function changePrice(Request $request)
    {
        $user_info = self::$user_info;
        $trade_no = $request->input('trade_no');
        $pay_price_old = $request->input('pay_price_old');
        $pay_price_new = $request->input('pay_price_new');
        $old_shipping_rate = $request->input('old_shipping_rate');
        $new_shipping_rate = $request->input('new_shipping_rate');
        $operator_uid = $user_info['user_no'];
        $operator_name = $user_info['username'];
        $remark = $request->input('remark', '测试');

//        dd($user_info);exit;
        $params = [
            'trade_no' => $trade_no,
            'pay_price_old' => $pay_price_old,
            'pay_price_new' => $pay_price_new,
            'old_shipping_rate' => $old_shipping_rate,
            'new_shipping_rate' => $new_shipping_rate,
            'operator_uid' => $operator_uid,
            'operator_name' => $operator_name,
            'remark' => $remark,
        ];
        $api = '/trade/editRetailTradeOrderPrice';
        $data = $this->post($api, $params);

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
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function logisticTypeToCn($payType)
    {
        switch ($payType) {
            case 1:
                return '自提';
                break;
            case 2:
                return '单个地址发货';
                break;
            case 3:
                return '多个地址发货';
                break;
            default:
                break;
        }
    }

    private function tradeStatusToCn($tradeStatus)
    {
        switch ($tradeStatus) {
            case 2:
                return '待付款';
                break;
            case 3:
                return '待发货';
                break;
            case 4:
                return '待签收';
                break;
            case 5:
                return '交易成功';
                break;
            case 6:
                return '商家拒绝接单';
                break;
            case 7:
                return '商家发货失败';
                break;
            case 8:
                return '退款成功';
                break;
            case 9:
                return '订单关闭';
                break;
            default:
                break;
        }
    }

    private function payChannel($tradeStatus)
    {
        switch ($tradeStatus) {
            case 1:
                return '支付宝支付';
                break;
            case 2:
                return '银联支付';
                break;
            case 5:
                return '银联全渠道';
                break;
            case 6:
                return '微信支付';
                break;
            case 7:
                return '微信web支付';
                break;
            case 8:
                return '微信小程序JSAPI支付';
                break;
            case 9:
                return '新版阿里app支付';
                break;
            case 10:
                return '微信跨境支付';
                break;
            case 11:
                return '支付宝跨境支付';
                break;
            default:
                break;
        }
    }
}
