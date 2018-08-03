<?php

namespace App\Http\Controllers\Api\Funds;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Excel;

class FundListController extends BaseController
{
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
        //
        $supplier_no = self::$user_no;
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');
        $start_time = $request->input('start_time');
        $over_time = $request->input('over_time');
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        //设置分页参数
        //分页页码
        $count = 10;
        $start = $count * ($page - 1);
        $param = [
            'supplier_no' => $supplier_no,
            'start' => $start,
            'count' => $count,
            'trade_no' => $trade_no,
            'trade_status' => $trade_status,
            'start_time' => $start_time,
            'over_time' => $over_time,
        ];

        $api = '/account/getSettlementOrderList';
        $data = $this->get($api, $param);

        if (! empty($data['data']['order_list'])) {
            foreach ($data['data']['order_list'] as &$item) {
                $item['account_period_time'] = $item['account_period_time'] ?: '冻结中';
                $purchase_status = $item['trade_status'];

                switch ($purchase_status) {
                    case '5':
                        $item['trade_status_text'] = '待结算';
                        break;
                    case '15':
                        $item['trade_status_text'] = '已完成';
                        break;
                }

                $item['new_pay_price'] = empty($item['pay_price']) ? 0 :
                    number_format($item['pay_price'] / 100, 2, '.', '');
            }
        }

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');
        $start_time = $request->input('start_time');
        $over_time = $request->input('over_time');
        //设置分页参数
        //分页页码
        $count = 1000;
        $start = $count * ($page - 1);
        $params = [
            'start' => $start,
            'count' => $count,
            'trade_no' => $trade_no,
            'trade_status' => $trade_status,
            'start_time' => $start_time,
            'over_time' => $over_time,
        ];

        $api = '/account/exportSettlementOrder';
        $data = $this->get($api, $params);
        // dd($data);exit;
        if (! empty($data['data']['order_list'])) {
            foreach ($data['data']['order_list'] as &$item) {
                $item['trade_status'] = $this->tradeStatusToCn($item['trade_status']);
                $item['trade_price'] = empty($item['trade_price']) ? 0 :
                    number_format($item['trade_price'] / 100, 2);
                $item['pay_price'] = empty($item['pay_price']) ? 0 :
                    number_format($item['pay_price'] / 100, 2);
                $item['sku_price'] = empty($item['sku_price']) ? 0 :
                    number_format($item['sku_price'] / 100, 2);
            }
        }

        $excelInfo = $data['data']['order_list'];
        $title = mb_convert_encoding('买手宝商品管理系统【OFASHION】-结算单', 'UTF-8');
        Excel::create($title, function ($excel) use ($excelInfo) {
            $excel->sheet('买手宝商品管理系统【OFASHION】-结算单', function ($sheet) use ($excelInfo) {
                $sheet->row(1, [
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
                    '商品条形码（O）',
                    '商家编码（O）',
                    'RFID编码',
                ]);

                foreach ($excelInfo as $key => $goodsItem) {
                    $sheet->row($key + 2, [
                        empty($goodsItem['purchase_no']) ? null : $goodsItem['purchase_no'],
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
                        empty($goodsItem['sign_time']) ? null : $goodsItem['sign_time'],
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
        $param = [
            'trade_no' => $id,
        ];
        $api = '/account/getSettlementOrderDetail';
        $data = $this->get($api, $param);

        if (! empty($data['data'])) {
            $purchase_status = $data['data']['purchase_status'];
            $data['data']['new_subtotal_price'] = empty($data['data']['subtotal_price']) ? 0 :
                number_format($data['data']['subtotal_price'] / 100, 2, '.', '');
            $data['data']['new_total_price'] = empty($data['data']['total_price']) ? 0 :
                number_format($data['data']['total_price'] / 100, 2, '.', '');
            $cPrice = number_format($data['data']['total_price'] / 100, 2, '.', '');
            $data['data']['word_figure'] = $this->Chinese_Money($cPrice);

            switch ($purchase_status) {
                case '1':
                    $data['data']['purchase_status_text'] = '待确认';
                    break;
                case '4':
                    $data['data']['purchase_status_text'] = '待签收';
                    break;
                case '5':
                    $data['data']['purchase_status_text'] = '待结算';
                    break;
                case '15':
                    $data['data']['purchase_status_text'] = '已完成';
                    break;
                case '6':
                    $data['data']['purchase_status_text'] = '商家拒绝接单';
                    break;
                case '7':
                    $data['data']['purchase_status_text'] = '商家发货失败';
                    break;
                case '8':
                    $data['data']['purchase_status_text'] = '退款成功';
                    break;
                case '9':
                    $data['data']['purchase_status_text'] = '买家已取消订单';
                    break;
            }

            if (! empty($data['data']['goods_list'])) {
                foreach ($data['data']['goods_list'] as &$item) {
                    $item['cover_image']['new_path'] = isset($item['cover_image']['path']) ?
                        cdn_image($item['cover_image']['path'], 'XS') : '';
                    $item['new_sku_price'] = empty($item['sku_price']) ? 0 :
                        number_format($item['sku_price'] / 100, 2, '.', '');
                }
            }
        }

        return response()->json($data);
    }

    public function setOrderStatus(Request $request)
    {
        $trade_no = $request->input('trade_no');
        $trade_status = $request->input('trade_status');
        $operator_id = self::$user_no;

        $params = [
            'trade_no' => $trade_no,
            'trade_status' => $trade_status,
            'operator_id' => $operator_id,
        ];

        $api = '/account/editSettlementOrderStatus';
        $data = $this->post($api, $params);
//        dd($params);exit;
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

    private function Chinese_Money_Max($i, $s = 1)
    {
        $c_digIT_min = ['零', '十', '百', '千', '万', '亿', '兆'];
        $c_num_min = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];

        $c_digIT_max = ['零', '拾', '佰', '仟', '万', '亿', '兆'];
        $c_num_max = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '拾'];

        if (1 === $s) {
            $c_digIT = $c_digIT_max;
            $c_num = $c_num_max;
        } else {
            $c_digIT = $c_digIT_min;
            $c_num = $c_num_min;
        }

        if ($i < 0) {
            return '负'.$this->Chinese_Money_Max(-$i);
        }

        if ($i < 11) {
            return $c_num[$i];
        }

        if ($i < 20) {
            return $c_num[1].$c_digIT[1].$c_num[$i - 10];
        }

        if ($i < 100) {
            if ($i % 10) {
                return $c_num[$i / 10].$c_digIT[1].$c_num[$i % 10];
            }

            return $c_num[$i / 10].$c_digIT[1];
        }

        if ($i < 1000) {
            if (0 === $i % 100) {
                return $c_num[$i / 100].$c_digIT[2];
            } elseif ($i % 100 < 10) {
                return $c_num[$i / 100].$c_digIT[2].$c_num[0].$this->Chinese_Money_Max($i % 100);
            } elseif ($i % 100 < 10) {
                return $c_num[$i / 100].$c_digIT[2].$c_num[1].$this->Chinese_Money_Max($i % 100);
            }

            return $c_num[$i / 100].$c_digIT[2].$this->Chinese_Money_Max($i % 100);
        }

        if ($i < 10000) {
            if (0 === $i % 1000) {
                return $c_num[$i / 1000].$c_digIT[3];
            } elseif ($i % 1000 < 100) {
                return $c_num[$i / 1000].$c_digIT[3].$c_num[0].$this->Chinese_Money_Max($i % 1000);
            }

            return $c_num[$i / 1000].$c_digIT[3].$this->Chinese_Money_Max($i % 1000);
        }

        if ($i < 100000000) {
            if (0 === $i % 10000) {
                return $this->Chinese_Money_Max($i / 10000).$c_digIT[4];
            } elseif ($i % 10000 < 1000) {
                return $this->Chinese_Money_Max($i / 10000).$c_digIT[4].$c_num[0].$this->Chinese_Money_Max($i % 10000);
            }

            return $this->Chinese_Money_Max($i / 10000).$c_digIT[4].$this->Chinese_Money_Max($i % 10000);
        }

        if ($i < 1000000000000) {
            if (0 === $i % 100000000) {
                return $this->Chinese_Money_Max($i / 100000000).$c_digIT[5];
            } elseif ($i % 100000000 < 1000000) {
                return $this->Chinese_Money_Max($i / 100000000).$c_digIT[5].$c_num[0].$this->Chinese_Money_Max($i % 100000000);
            }

            return $this->Chinese_Money_Max($i / 100000000).$c_digIT[5].$this->Chinese_Money_Max($i % 100000000);
        }

        if (0 === $i % 1000000000000) {
            return $this->Chinese_Money_Max($i / 1000000000000).$c_digIT[6];
        } elseif ($i % 1000000000000 < 100000000) {
            return $this->Chinese_Money_Max($i / 1000000000000).$c_digIT[6].$c_num[0].$this->Chinese_Money_Max($i % 1000000000000);
        }

        return $this->Chinese_Money_Max($i / 1000000000000).$c_digIT[6].$this->Chinese_Money_Max($i % 1000000000000);
    }

//    private function Chinese_Money_Min($a){
//        $c_num = array("零","一","二","三","四","五","六","七","八","九","十");
//        if($a<10)
//            return $c_num[0] . "角" . $c_num[$a] . "分";
//        else if($a%10 == 0)
//            return $c_num[$a/10] . "角" . $c_num[0] . "分";
//        else
//            return $c_num[floor($a/10)] . "角" . $c_num[$a%10] ."分";
//    }

    /*小数点后两位*/
    private function Chinese_Num_Min($a)
    {
        $c_num = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];

        if ($a < 10) {
            return $c_num[0].$c_num[$a];
        } elseif (0 === $a % 10) {
            return $c_num[$a / 10].$c_num[0];
        }

        return $c_num[floor($a / 10)].$c_num[$a % 10];
    }

    private function Chinese_Money($i)
    {
        $j = floor($i);
        $x = ($i - $j) * 100;

        return $this->Chinese_Money_Max($j, '0').'点'.$this->Chinese_Num_Min($x);
    }

    private function tradeStatusToCn($tradeStatus)
    {
        switch ($tradeStatus) {
            case 15:
                return '已完成';
                break;
            case 5:
                return '待结算';
                break;
        }
    }
}
