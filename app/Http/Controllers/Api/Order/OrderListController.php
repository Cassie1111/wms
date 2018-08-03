<?php

namespace App\Http\Controllers\Api\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Excel;

class OrderListController extends BaseController
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
        $supplier_no = self::$user_no;
        $purchase_no = $request->input('purchase_no');
        $product_model = $request->input('product_model');
        $purchase_status = $request->input('purchase_status');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $goods_name = $request->input('goods_name');
        $logistic_method = $request->input('logistic_method');
        $code = $request->input('code');
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        //设置分页参数
        //分页页码
        $count = 10;
        $start = $count * ($page - 1);
        $param = [
            'start' => $start,
            'count' => $count,
            'supplier_no' => $supplier_no,
            'purchase_no' => $purchase_no,
            'purchase_status' => $purchase_status,
            'product_model' => $product_model,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'goods_name' => $goods_name,
            'code' => $code,
            'logistic_method' => $logistic_method,
        ];

        $api = '/trade/getSupplyTradeList';
        $data = $this->get($api, $param);

        if (! empty($data['data']['purchase_trade_list'])) {
            foreach ($data['data']['purchase_trade_list'] as &$item) {
                $item['is_check'] = false;
                $item['new_pay_price'] = empty($item['pay_price']) ? 0 :
                    number_format($item['pay_price'] / 100, 2);
                $item['new_trade_price'] = empty($item['trade_price']) ? 0 :
                    number_format($item['trade_price'] / 100, 2);
                $purchase_status = $item['trade_status'];

                switch ($purchase_status) {
                    case 1:
                        $item['trade_status_text'] = '待确认';
                        break;
                    case 4:
                        $item['trade_status_text'] = '待入库';
                        break;
                    case 5:
                        $item['trade_status_text'] = '待结算';
                        break;
                    case 15:
                        $item['trade_status_text'] = '已完成';
                        break;
                    case 9:
                        $item['trade_status_text'] = '买家已取消订单';
                        break;
                }

                if (! empty($item['goods_info_list'])) {
                    foreach ($item['goods_info_list'] as &$orderItem) {
                        $orderItem['cover_image']['new_path']
                            = empty($orderItem['cover_image']['path']) ? '' : cdn_image($orderItem['cover_image']['path'], 'XS');
                        $orderItem['new_sku_price']
                            = empty($orderItem['sku_price']) ? 0 :
                            number_format($orderItem['sku_price'] / 100, 2);
                    }
                }
            }
        }

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $purchase_no = $request->input('purchase_no', null);
        $purchase_status = $request->input('purchase_status', null);
        $logistic_method = $request->input('logistic_method', null);
        $goods_name = $request->input('goods_name', null);
        $code = $request->input('code', null);
        $product_model = $request->input('product_model', null);
        $start_time = $request->input('start_time', null);
        $end_time = $request->input('end_time', null);
        $check_list = $request->input('check_list', null);
        $count = 1000;
        $param = [
            'purchase_no' => $purchase_no,
            'purchase_status' => $purchase_status,
            'logistic_method' => $logistic_method,
            'goods_name' => $goods_name,
            'code' => $code,
            'product_model' => $product_model,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'start' => 0,
            'count' => $count,
        ];

        $api = '/trade/exportPurchaseOrder';
        $data = $this->get($api, $param);

        if (! empty($data['data']['purchase_list'])) {
            foreach ($data['data']['purchase_list'] as &$item) {
                $item['trade_status'] = $this->tradeStatusToCn($item['trade_status']);
                $item['logistic_method'] = $this->logisticTypeToCn($item['logistic_method']);
                $item['pay_price'] = ! empty($item['pay_price']) ?
                    number_format($item['pay_price'] / 100, 2) : '';
                $item['sku_price'] = ! empty($item['sku_price']) ?
                    number_format($item['sku_price'] / 100, 2) : '';
                $item['trade_price'] = ! empty($item['trade_price']) ?
                    number_format($item['trade_price'] / 100, 2) : '';
            }

            if (! empty($check_list)) {
                foreach ($data['data']['purchase_list'] as $orderKey => $orderItem) {
                    if (! in_array($orderItem['purchase_no'], $check_list, true)) {
                        unset($data['data']['purchase_list'][$orderKey]);
                    }
                }
            }

            $orderList = $data['data']['purchase_list'];
            $orderList = array_values($orderList);
        } else {
            $orderList = [];
        }

        $title = mb_convert_encoding('买手宝商品管理系统【OFASHION】-采购单', 'UTF-8');
        Excel::create($title, function ($excel) use ($orderList) {
            $excel->sheet('买手宝商品管理系统【OFASHION】-采购单', function ($sheet) use ($orderList) {
                $sheet->row(1, [
                    '采购单编号',
                    '订单编号',
                    '订单状态',
                    '供货商编码',
                    '供应商',
                    '品牌',
                    '品牌货号',
                    '货品名称',
                    '颜色',
                    '规格/尺码',
                    '供货价(HK$)',
                    '数量',
                    '货品总价',
                    '下单时间',
                    '结算时间',
                    '确认时间',
                    '入库时间',
                    '结算金额',
                    '结算方式',
                    '商品条形码（S）',
                    '商家编码（S）',
                    'RFID编码',
                ]);

                foreach ($orderList as $key => $goodsItem) {
                    $sheet->row($key + 2, [
                        empty($goodsItem['purchase_no']) ? null : $goodsItem['purchase_no'],
                        empty($goodsItem['retail_trade_no']) ? null : $goodsItem['retail_trade_no'],
                        empty($goodsItem['trade_status']) ? null : $goodsItem['trade_status'],
                        empty($goodsItem['supplier_no']) ? null : $goodsItem['supplier_no'],
                        empty($goodsItem['supplier_name']) ? null : $this->userTextEncode($goodsItem['supplier_name']),
                        empty($goodsItem['brand_name_e']) ? null : $goodsItem['brand_name_e'],
                        empty($goodsItem['product_model']) ? null : $goodsItem['product_model'],
                        empty($goodsItem['goods_name']) ? null : $this->userTextEncode($goodsItem['goods_name']),
                        empty($goodsItem['sku_color']) ? null : $this->userTextEncode($goodsItem['sku_color']),
                        empty($goodsItem['sku_spec']) ? null : $this->userTextEncode($goodsItem['sku_spec']),
                        empty($goodsItem['sku_price']) ? null : $goodsItem['sku_price'],
                        empty($goodsItem['goods_num']) ? null : $goodsItem['goods_num'],
                        empty($goodsItem['trade_price']) ? null : $goodsItem['trade_price'],
                        empty($goodsItem['create_time']) ? null : $goodsItem['create_time'],
                        empty($goodsItem['pay_time']) ? '冻结中' : $goodsItem['pay_time'],
                        empty($goodsItem['accept_time']) ? null : $goodsItem['accept_time'],
                        empty($goodsItem['pay_time']) ? null : $goodsItem['sign_time'],
                        empty($goodsItem['pay_price']) ? null : $goodsItem['pay_price'],
                        empty($goodsItem['pay_mode']) ? null : $goodsItem['pay_mode'],
                        empty($goodsItem['ofashion_bar_code']) ? null : $goodsItem['ofashion_bar_code'],
                        empty($goodsItem['ofashion_sku_code']) ? null : $goodsItem['ofashion_sku_code'],
                        empty($goodsItem['rfid_list']) ? null : implode(',', $goodsItem['rfid_list']),
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
        $purchase_no = $request->input('purchase_no');
        $trade_status = $request->input('trade_status');
        $operator_id = $request->input('operator_id');

        $param = [
            'purchase_no' => $purchase_no,
            'trade_status' => $trade_status,
            'operator_id' => $operator_id,
        ];
        $api = '/trade/cancelPurchaseOrder';

        $data = $this->post($api, $param);

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
        $purchase_list = [
            1 => '待采购单确认',
            4 => '待入库',
            5 => '已入库，待结算',
            15 => '已完成',
            6 => '商家拒绝接单',
            7 => '商家发货失败',
            8 => '退款成功',
            9 => '买家已取消订单',
        ];

        $purchase_no = $id;
        $param = [
            'purchase_no' => $purchase_no,
        ];

        $api = 'trade/getSupplyTradeOrder';
        $data = $this->get($api, $param);

        if (! empty($data['data']['goods_list'])) {
            foreach ($data['data']['goods_list'] as $key => &$item) {
                $item['cover_image']['new_path'] = empty($item['cover_image']['path'])
                    ? ''
                    : cdn_image($item['cover_image']['path'], 'S');
                $item['new_sku_price'] = empty($item['sku_price'])
                    ? 0
                    : number_format($item['sku_price'] / 100, 2);
                $item['stock_status_text'] = $this->stockStatusToCn($item['stock_status']);
            }
        }

        if (! empty($data['data']['logistic_method'])) {
            $data['data']['logistic_method_cn'] = $this->logisticTypeToCn($data['data']['logistic_method']);
        }

        if (! empty($data['data']['subtotal_price'])) {
            $data['data']['new_subtotal_price']
                = number_format($data['data']['subtotal_price'] / 100, 2);
        }

        $data['data']['purchase_status_text'] = $purchase_list[$data['data']['purchase_status']];

        return response()->json($data);
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
                return '上门取货';
                break;
            case 2:
                return '送货入仓';
                break;
            case 3:
                return '物流送货';
                break;
            default:
                break;
        }
    }

    private function tradeStatusToCn($tradeStatus)
    {
        switch ($tradeStatus) {
            case 1:
                return '待确认';
                break;
            case 4:
                return '待入库';
                break;
            case 15:
                return '已完成';
                break;
            case 5:
                return '待结算';
                break;
            case 9:
                return '买家已取消订单';
                break;
            default:
                break;
        }
    }

    private function stockStatusToCn($stockStatus)
    {
//        1、已确认，待收货；2、收货，待质检；3、质检完成，待安装RFID；4、安装完毕，已入库；5、已出库；
        switch ($stockStatus) {
            case 1:
                return '已确认，待收货';
                break;
            case 2:
                return '收货，待质检';
                break;
            case 3:
                return '质检完成，待安装RFID';
                break;
            case 4:
                return '安装完毕，已入库';
                break;
            case 5:
                return '已出库';
                break;
            default:
                return '未确认';
                break;
        }
    }
}
