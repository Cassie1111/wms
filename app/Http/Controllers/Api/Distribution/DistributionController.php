<?php

namespace App\Http\Controllers\Api\Distribution;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class DistributionController extends BaseController
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
        $trade_no = $request->input('trade_no', '');
        $param = [
            'trade_no' => $trade_no,
        ];

        $api = '/trade/getRetailTradeOrder';
        $data = $this->get($api, $param);

        $companyApi = '/transport/getTransportCompanyList';
        $companyData = $this->get($companyApi);

        if (! empty($data['data']['retailTradeInfo'])) {
            if (! empty($companyData['data']['transport_company'])) {
                $data['data']['retailTradeInfo']['transport_company'] = $companyData['data']['transport_company'];
            } else {
                $data['data']['retailTradeInfo']['transport_company'] = [];
            }
        }

        if (! empty($data['data']['retailTradeInfo']['goods_list'])) {
            foreach ($data['data']['retailTradeInfo']['goods_list'] as &$item) {
                $item['cover_image']['new_path']
                    = empty($item['cover_image']['path']) ? '' : cdn_image($item['cover_image']['path'], 'XS');
                $item['new_goods_price'] = empty($item['goods_price'])
                    ? 0 : number_format($item['goods_price'] / 100);
                $item['new_sku_price'] = empty($item['sku_price'])
                    ? 0 : number_format($item['sku_price'] / 100);
                $item['new_discount_price'] = empty($item['discount_price'])
                    ? 0 : number_format($item['discount_price'] / 100);
                $item['new_customs_duties'] = empty($item['customs_duties'])
                    ? 0 : number_format($item['customs_duties'] / 100);
                $item['new_shipping_rate'] = empty($item['shipping_rate'])
                    ? 0 : number_format($item['shipping_rate'] / 100);
                $item['new_pay_price'] = empty($item['pay_price'])
                    ? 0 : number_format($item['pay_price'] / 100);
                $item['goods_status'] = $this->statusToCn($data['data']['retailTradeInfo']['trade_status']);
            }
        }

        if (! empty($data['data']['retailTradeInfo']['logistic_recipients_list'])) {
            foreach ($data['data']['retailTradeInfo']['logistic_recipients_list'] as &$logisticItem) {
                if (! empty($logisticItem['goods_list'])) {
                    foreach ($logisticItem['goods_list'] as &$goodsItem) {
                        $goodsItem['cover_image']['new_path']
                            = empty($goodsItem['cover_image']['path']) ? '' : cdn_image($goodsItem['cover_image']['path'], 'XS');
                    }
                }
            }
        }

        $data['data']['retailTradeInfo']['trade_status_cn']
            = $this->statusToCn($data['data']['retailTradeInfo']['trade_status']);

        $data['data']['retailTradeInfo']['pay_channel_cn']
            = ! empty($data['data']['retailTradeInfo']['pay_channel'])
            ? $this->payToCn($data['data']['retailTradeInfo']['pay_channel'])
            : '';

        $data['data']['retailTradeInfo']['new_goods_price_total']
            = empty($data['data']['retailTradeInfo']['goods_price_total'])
            ? 0 : 'HK$ '.number_format($data['data']['retailTradeInfo']['goods_price_total'] / 100, 2);

        $data['data']['retailTradeInfo']['new_customs_duties_total']
            = empty($data['data']['retailTradeInfo']['customs_duties_total'])
            ? 0 : 'HK$ '.number_format($data['data']['retailTradeInfo']['customs_duties_total'] / 100, 2);

        $data['data']['retailTradeInfo']['new_discount_price_total']
            = empty($data['data']['retailTradeInfo']['discount_price_total'])
            ? 0 : '- HK$ '.number_format($data['data']['retailTradeInfo']['discount_price_total'] / 100, 2);

        $data['data']['retailTradeInfo']['new_pay_price']
            = empty($data['data']['retailTradeInfo']['pay_price'])
            ? 0 : 'HK$ '.number_format($data['data']['retailTradeInfo']['pay_price'] / 100, 2);

        $data['data']['retailTradeInfo']['new_shipping_rate_total']
            = empty($data['data']['retailTradeInfo']['shipping_rate_total'])
            ? 0 : 'HK$ '.number_format($data['data']['retailTradeInfo']['shipping_rate_total'] / 100, 2);

        $data['data']['retailTradeInfo']['new_trade_price']
            = empty($data['data']['retailTradeInfo']['trade_price'])
            ? 0 : 'HK$ '.number_format($data['data']['retailTradeInfo']['trade_price'] / 100, 2);

        return response()->json($data);
    }

    public function launch(Request $request)
    {
        $trade_no = $request->input('trade_no', null);
        $delivery_logistic_info = $request->input('delivery_logistic_info', null);
        $param = [
            'trade_no' => $trade_no,
            'operator_id' => self::$user_no,
            'delivery_logistic_info' => $delivery_logistic_info,
        ];

        $api = '/trade/deliveryRetailTradeOrder';
        $data = $this->post($api, $param);

        return response()->json($data);
    }

    public function editComment(Request $request)
    {
        $trade_no = $request->input('trade_no', null);
        $seller_comment = $request->input('seller_comment', null);
        $param = [
            'trade_no' => $trade_no,
            'operator_id' => self::$user_no,
            'seller_comment' => $seller_comment,
        ];

        $api = '/trade/editTradeComment';
        $data = $this->post($api, $param);

        return response()->json($data);
    }

    /**
     * @param $status
     * 2：待付款；3：待发货；4：待签收；5：交易成功；7：商家发货失败；8：退款成功；9：卖家已取消订单；
     * @return string
     */
    private function statusToCn($status)
    {
        switch ($status) {
            case 2:
                return '待付款';
            case 3:
                return '待发货';
            case 4:
                return '待签收';
            case 5:
                return '交易成功';
            case 7:
                return '商家发货失败';
            case 8:
                return '退款成功';
            case 9:
                return '买家已取消订单';
            default:
                break;
        }
    }

    /**
     * @param $payChannel
     * 1：支付宝支付；2：银联支付；5：银联全渠道；6：微信支付；7：微信web支付；8：微信小程序JSAPI支付；
     * 9：新版阿里APP支付；10：微信跨境支付；11：支付宝跨境支付；
     * @return string
     */
    private function payToCn($payChannel)
    {
        switch ($payChannel) {
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
                return '新版阿里APP支付';
                break;
            case 10:
                return '微信跨境支付';
                break;
            case 11:
                return '支付宝跨境支付';
                break;
            default:
                return '';
                break;
        }
    }
}
