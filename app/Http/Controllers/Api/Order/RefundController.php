<?php

namespace App\Http\Controllers\Api\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Excel;

class RefundController extends BaseController
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
        $refund_status = $request->input('refund_status', '1,2,3');
        $trade_no = $request->input('trade_no', null);
        $logistic_method = $request->input('logistic_method', null);
        $goods_name = $request->input('goods_name', null);
        $code = $request->input('code', null);
        $product_model = $request->input('product_model', null);
        $start_time = $request->input('start_time', null);
        $end_time = $request->input('end_time', null);
        $page = $request->input('page', 1);
        $count = 20;

        if (! empty($start_time)) {
            $start_time = $start_time.' 00:00:00';
        }

        if (! empty($end_time)) {
            $end_time = $end_time.' 00:00:00';
        }

        $param = [
            'refund_status' => $refund_status,
            'trade_no' => $trade_no,
            'logistic_method' => $logistic_method,
            'goods_name' => $goods_name,
            'code' => $code,
            'product_model' => $product_model,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'start' => ($page - 1) * $count,
            'count' => $count,
        ];

        $api = 'trade/getRetailRefundTradeList';
        $data = $this->get($api, $param);

//        return response()->json($data);
        if (! empty($data['data']['refundList']['trade_list'])) {
            foreach ($data['data']['refundList']['trade_list'] as &$tradeItem) {
                if (! empty($tradeItem['trade_detail'])) {
                    foreach ($tradeItem['trade_detail'] as &$orderItem) {
                        $orderItem['cover_image']['new_path']
                            = empty($orderItem['cover_image']['path']) ? '' : cdn_image($orderItem['cover_image']['path'], 'XS');
                        $orderItem['new_sku_price']
                            = empty($orderItem['sku_price']) ? 0 : number_format($orderItem['sku_price'] / 100);
                    }
                }

                $tradeItem['new_pay_price']
                    = empty($tradeItem['pay_price']) ? 0 : number_format($tradeItem['pay_price'] / 100);
                $tradeItem['refund_status_cn'] = $this->refundStatusToCn($tradeItem['refund_status']);
            }
        }

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $refund_status = $request->input('refund_status', '1,2,3');
        $trade_no = $request->input('trade_no', null);
        $logistic_method = $request->input('logistic_method', null);
        $goods_name = $request->input('goods_name', null);
        $code = $request->input('code', null);
        $product_model = $request->input('product_model', null);
        $start_time = $request->input('start_time', null);
        $end_time = $request->input('end_time', null);
        $check_list = $request->input('check_list', null);
        $count = 1000000;
        $param = [
            'refund_status' => $refund_status,
            'trade_no' => $trade_no,
            'logistic_method' => $logistic_method,
            'goods_name' => $goods_name,
            'code' => $code,
            'product_model' => $product_model,
            'start' => 0,
            'count' => $count,
        ];

        if (! empty($start_time)) {
            $param['start_time'] = $start_time.' 00:00:00';
        }

        if (! empty($end_time)) {
            $param['end_time'] = $end_time.' 00:00:00';
        }

        $api = 'trade/exportRetailRefund';
        $data = $this->get($api, $param);

        if (! empty($data['data']['detailList']['detail_list'])) {
            foreach ($data['data']['detailList']['detail_list'] as &$item) {
                $item['trade_status'] = $this->tradeStatusToCn($item['trade_status']);
                $item['refund_status'] = $this->refundStatusToCn($item['refund_status']);
                $item['logistic_method'] = $this->logisticTypeToCn($item['logistic_method']);
                $item['trade_price'] = ! empty($item['trade_price']) ? number_format($item['trade_price'] / 100) : '';
                $item['pay_price'] = ! empty($item['pay_price']) ? number_format($item['pay_price'] / 100) : '';
                $item['shipping_rate_total'] = ! empty($item['shipping_rate_total']) ? number_format($item['shipping_rate_total'] / 100) : '';
            }

            if (! empty($check_list)) {
                foreach ($data['data']['detailList']['detail_list'] as $orderKey => $orderItem) {
                    if (! in_array($orderItem['trade_no'], $check_list, true)) {
                        unset($data['data']['detailList']['detail_list'][$orderKey]);
                    }
                }
            }

            $orderList = $data['data']['detailList']['detail_list'];
            $orderList = array_values($orderList);
        } else {
            $orderList = [];
        }

        $filename = mb_convert_encoding('退款订单列表', 'UTF-8');
        Excel::create($filename, function ($excel) use ($orderList) {
            $excel->sheet('退款订单列表', function ($sheet) use ($orderList) {
                $sheet->row(1, [
                    '',
                    '订单编号',
                    '订单状态',
                    '买家id',
                    '买家昵称',
                    '品牌',
                    '品牌货号',
                    '商品名称',
                    '颜色',
                    '规格/尺码',
                    '销售价(HKD)',
                    '数量',
                    '货品总价',
                    '运费（HKD）',
                    '下单时间',
                    '支付时间',
                    '发货时间',
                    '签收时间',
                    '支付金额',
                    '支付方式',
                    '商品条形码（O）',
                    '商家编码（O）',
                    'RFID编码',
                    '配送方式',
                    '收货人姓名1',
                    '收货人电话1',
                    '配送地址1',
                    '配送物流1',
                    '配送物流单号1',
                    '收货人姓名2',
                    '收货人电话2',
                    '配送地址2',
                    '配送物流2',
                    '配送物流单号2',
                    '收货人姓名3',
                    '收货人电话3',
                    '配送地址3',
                    '配送物流3',
                    '配送物流单号3',
                    '收货人姓名4',
                    '收货人电话4',
                    '配送地址4',
                    '配送物流4',
                    '配送物流单号4',
                    '收货人姓名5',
                    '收货人电话5',
                    '配送地址5',
                    '配送物流5',
                    '配送物流单号5',
                ]);

                foreach ($orderList as $key => $orderItem) {
                    $sheet->row($key + 2, [
                        '',
                        empty($orderItem['trade_no']) ? null : $orderItem['trade_no'],
                        empty($orderItem['trade_status']) ? null : $orderItem['trade_status'],
                        empty($orderItem['retailer_no']) ? null : $orderItem['retailer_no'],
                        empty($orderItem['retailer_name']) ? null : $orderItem['retailer_name'],
                        empty($orderItem['brand_name_e']) ? null : $orderItem['brand_name_e'],
                        empty($orderItem['product_model']) ? null : $orderItem['product_model'],
                        empty($orderItem['goods_name']) ? null : $orderItem['goods_name'],
                        empty($orderItem['sku_color']) ? null : $orderItem['sku_color'],
                        empty($orderItem['sku_spec']) ? null : $orderItem['sku_spec'],
                        empty($orderItem['sku_price']) ? null : $orderItem['sku_price'],
                        empty($orderItem['goods_sku_num']) ? null : $orderItem['goods_sku_num'],
                        empty($orderItem['trade_price']) ? null : $orderItem['trade_price'],
                        empty($orderItem['shipping_rate_total']) ? null : $orderItem['shipping_rate_total'],
                        empty($orderItem['create_time']) ? null : $orderItem['create_time'],
                        empty($orderItem['pay_time']) ? null : $orderItem['pay_time'],
                        empty($orderItem['delivery_time']) ? null : $orderItem['delivery_time'],
                        empty($orderItem['sign_time']) ? null : $orderItem['sign_time'],
                        empty($orderItem['pay_price']) ? null : $orderItem['pay_price'],
                        empty($orderItem['pay_channel']) ? null : $orderItem['pay_channel'],
                        empty($orderItem['bar_code']) ? null : $orderItem['bar_code'],
                        empty($orderItem['sku_code']) ? null : $orderItem['sku_code'],
                        empty($orderItem['rfid_list'][0]) ? null : $orderItem['rfid_list'][0],
                        empty($orderItem['logistic_method']) ? null : $orderItem['logistic_method'],
                        empty($orderItem['order_delivery_info'][0]['recipient_name']) ? null : $orderItem['order_delivery_info'][0]['recipient_name'],
                        empty($orderItem['order_delivery_info'][0]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][0]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][0]['recipient_address']) ? null : $orderItem['order_delivery_info'][0]['recipient_address'],
                        empty($orderItem['order_delivery_info'][0]['transport_company']) ? null : $orderItem['order_delivery_info'][0]['transport_company'],
                        empty($orderItem['order_delivery_info'][0]['package_no']) ? null : $orderItem['order_delivery_info'][0]['package_no'],
                        empty($orderItem['order_delivery_info'][1]['recipient_name']) ? null : $orderItem['order_delivery_info'][1]['recipient_name'],
                        empty($orderItem['order_delivery_info'][1]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][1]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][1]['recipient_address']) ? null : $orderItem['order_delivery_info'][1]['recipient_address'],
                        empty($orderItem['order_delivery_info'][1]['transport_company']) ? null : $orderItem['order_delivery_info'][1]['transport_company'],
                        empty($orderItem['order_delivery_info'][1]['package_no']) ? null : $orderItem['order_delivery_info'][1]['package_no'],
                        empty($orderItem['order_delivery_info'][2]['recipient_name']) ? null : $orderItem['order_delivery_info'][2]['recipient_name'],
                        empty($orderItem['order_delivery_info'][2]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][2]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][2]['recipient_address']) ? null : $orderItem['order_delivery_info'][2]['recipient_address'],
                        empty($orderItem['order_delivery_info'][2]['transport_company']) ? null : $orderItem['order_delivery_info'][2]['transport_company'],
                        empty($orderItem['order_delivery_info'][2]['package_no']) ? null : $orderItem['order_delivery_info'][2]['package_no'],
                        empty($orderItem['order_delivery_info'][3]['recipient_name']) ? null : $orderItem['order_delivery_info'][3]['recipient_name'],
                        empty($orderItem['order_delivery_info'][3]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][3]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][3]['recipient_address']) ? null : $orderItem['order_delivery_info'][3]['recipient_address'],
                        empty($orderItem['order_delivery_info'][3]['transport_company']) ? null : $orderItem['order_delivery_info'][3]['transport_company'],
                        empty($orderItem['order_delivery_info'][3]['package_no']) ? null : $orderItem['order_delivery_info'][3]['package_no'],
                        empty($orderItem['order_delivery_info'][4]['recipient_name']) ? null : $orderItem['order_delivery_info'][4]['recipient_name'],
                        empty($orderItem['order_delivery_info'][4]['recipient_mobilephone']) ? null : $orderItem['order_delivery_info'][4]['recipient_mobilephone'],
                        empty($orderItem['order_delivery_info'][4]['recipient_address']) ? null : $orderItem['order_delivery_info'][4]['recipient_address'],
                        empty($orderItem['order_delivery_info'][4]['transport_company']) ? null : $orderItem['order_delivery_info'][4]['transport_company'],
                        empty($orderItem['order_delivery_info'][4]['package_no']) ? null : $orderItem['order_delivery_info'][4]['package_no'],
                    ]);
                }
            });
        })->export('xlsx');
    }

    public function refundOrder(Request $request)
    {
        $trade_no = $request->input('trade_no', null);
        $operator_id = $request->input('operator_id', null);
        $refund_fee = $request->input('refund_fee', null);

        $params = [
            'trade_no' => $trade_no,
            'operator_id' => $operator_id,
            'refund_fee' => $refund_fee,
        ];

        $api = '/trade/orderRefund';
        $data = $this->post($api, $params);

        return response()->json($data);
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

    private function refundStatusToCn($refundStatus)
    {
        switch ($refundStatus) {
            case 0:
                return '未退款';
                break;
            case 1:
                return '待处理';
                break;
            case 2:
                return '处理成功';
                break;
            case 3:
                return '处理失败';
                break;
            default:
                break;
        }
    }

    private function tradeStatusToCn($tradeStatus)
    {
        switch ($tradeStatus) {
            case 1:
                return '待接单';
                break;
            case 2:
                return '待付款';
                break;
            case 3:
                return '待发货';
                break;
            case 13:
                return '部分发货';
                break;
            case 4:
                return '待签收';
                break;
            case 15:
                return '部分签收';
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
            case 18:
                return '部分退款';
                break;
            case 8:
                return '退款成功';
                break;
            case 9:
                return '买家已取消订单';
                break;
            default:
                break;
        }
    }
}
