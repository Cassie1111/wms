<?php

namespace App\Http\Controllers\Api\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class OrderDetailController extends BaseController
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
        $purchase_no = $request->input('purchase_no', '');
        $param = [
            'purchase_no' => $purchase_no,
        ];
        $post_param = [
            'purchase_no' => $purchase_no,
            'operator_id' => '',
            'goods_list' => [],
        ];

        $api = 'trade/getSupplyTradeOrder';
        $data = $this->get($api, $param);
//        return response()->json($data);

        if (! empty($data['data']['goods_list'])) {
            foreach ($data['data']['goods_list'] as $key => &$item) {
                $item['cover_image']['new_path'] = empty($item['cover_image']['path'])
                    ? ''
                    : cdn_image($item['cover_image']['path'], 'S');
                $item['new_sku_price'] = empty($item['sku_price'])
                    ? 0
                    : number_format($item['sku_price'] / 100, 2);

                // 构建修改订单商品数量时传递给接口的参数内容
                $post_param['goods_list'][$key]['id'] = $item['id'];
                $post_param['goods_list'][$key]['goods_num_new'] = $item['goods_num'];
            }
        }

        if (! empty($data['data']['logistic_method'])) {
            $data['data']['logistic_method_cn'] = $this->logisticTypeToCn($data['data']['logistic_method']);
        }

        if (! empty($data['data']['subtotal_price'])) {
            $data['data']['new_subtotal_price']
                = number_format($data['data']['subtotal_price'] / 100, 2);
        }

        if (! empty($data['data']['total_price'])) {
            $data['data']['new_total_price']
                = number_format($data['data']['total_price'] / 100, 2);
            $cPrice = number_format($data['data']['total_price'] / 100, 2, '.', '');
            $data['data']['word_figure'] = $this->Chinese_Money($cPrice);
        }

        $data['data']['post_param'] = $post_param;

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
    public function edit(Request $request)
    {
        $param = $request->input('post_param', '');

        $api = '/trade/editPurchaseOrder';
        $data = $this->post($api, $param);

        return response()->json($data);
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
                return '物流发货';
                break;
            default:
                break;
        }
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

    private function Chinese_Money_Min($a)
    {
        $c_num = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];

        if ($a < 10) {
            return $c_num[0].'角'.$c_num[$a].'分';
        } elseif (0 === $a % 10) {
            return $c_num[$a / 10].'角'.$c_num[0].'分';
        }

        return $c_num[floor($a / 10)].'角'.$c_num[$a % 10].'分';
    }

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
}
