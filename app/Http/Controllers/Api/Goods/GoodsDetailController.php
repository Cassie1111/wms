<?php

namespace App\Http\Controllers\Api\Goods;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class GoodsDetailController extends BaseController
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

    public function index()
    {
        //
    }

    // 获取商品详情
    public function getGoodsDetail(Request $request)
    {
        $goods_no = $request->get('goods_no');
        $status = $request->get('status', false);

        if ('0' === $status) {
            $params = [
                'product_no' => $goods_no,
                'user_no' => self::$user_no,
            ];
        } else {
            $params = [
                'goods_no' => $goods_no,
                'user_no' => self::$user_no,
            ];
        }

        $api = '/goods/getGoodsDetail';

        $data = $this->get($api, $params);

        if (! empty($data['data']['cover_image'])) {
            foreach ($data['data']['cover_image'] as &$cover) {
                $cover['path_full'] = cdn_image($cover['path'], 'M');
            }
        }

        if (! empty($data['data']['detail_image'])) {
            foreach ($data['data']['detail_image'] as &$detail) {
                $detail['path_full'] = cdn_image($detail['path'], 'M');
            }
        }

        if (! empty($data['data']['product_sku'])) {
            foreach ($data['data']['product_sku'] as &$sku) {
                if (!empty($sku['sku_image_list'][0]['path'])) {
                    $sku['sku_image_list'][0]['path_full'] = cdn_image($sku['sku_image_list'][0]['path'], 'M');
                }
            }
        }

        return response()->json($data);
    }

    // 获取报价信息
    public function getQuoteInfoByCate(Request $request)
    {
        $cate_id = $request->get('cate_id');

        $virtual_cate_id = $request->get('virtual_cate_id');

        $want_type = $request->get('want_type', '');

        $sku_property = $request->get('sku_property', 0);

        $code_id = $request->get('code_id', 0);

        $params = [
            'cate_id' => $cate_id,
            'virtual_cate_id' => $virtual_cate_id,
            'want_type' => $want_type,
            'sku_property' => $sku_property,
            'code_id' => $code_id,
            'user_no' => self::$user_no,
        ];

        $api = '/goods/getQuoteInfoByCate';

        $data = $this->get($api, $params);

        // 格式化颜色规格里面花色系图标url
        $color_list = $data['data']['sku_info']['data'][0]['data_list'][2];
        $color_list['color_url'] = cdn_image($color_list['color_url'], 'M');

        if (!empty($color_list['data_list'])) {
            foreach ($color_list['data_list'] as &$color_item) {
                $color_item['color_url'] = cdn_image($color_item['color_url'], 'M');
            }
        }

        $data['data']['sku_info']['data'][0]['data_list'][2] = $color_list;

        return response()->json($data);
    }

    // 获取所有品牌
    public function getBrands()
    {
        $api = '/brand/getBrandList';
        $data = $this->get($api);

        return response()->json($data);
    }

    // 获取商品属性
    public function getGoodsPropertyList(Request $request)
    {
        $cate_id = $request->get('cate_id');
        $api = '/product/getGoodsPropertyList';

        $params = [
            'cate_id' => $cate_id,
        ];

        $data = $this->get($api, $params);

        return response()->json($data);
    }

    // 编辑，新增商品
    public function editProductDetail(Request $request)
    {
        $user_no = self::$user_no;
        $goods_name = $request->post('goods_name');
        $goods_no = $request->post('goods_no', null);
        $status = $request->post('status', null);
        $brand_id = $request->post('brand_id');
        $brand_name_e = $request->post('brand_name_e');
        $brand_name_c = $request->post('brand_name_c');
        $product_property = $request->post('product_property');
        $description = $request->post('description');
        $virtual_cate_id = $request->post('virtual_cate_id');
        $final_level_cate_id = $request->post('final_level_cate_id', '');
        $final_level_cate_name = $request->post('final_level_cate_name', '');
        $first_level_cate_id = $request->post('first_level_cate_id', '');
        $first_level_cate_name = $request->post('first_level_cate_name', '');
        $show_status = $request->post('show_status', 1);
        $channel_source = $request->post('channel_source');
        $product_sku = $request->post('product_sku');
        $cover_image = $request->post('cover_image');
        $detail_image = $request->post('detail_image');
        $ofashion_bar_code = $request->post('ofashion_bar_code', '');
        $currency = $request->post('currency');
        $stock = $request->post('stock');
        $product_price = $request->post('product_price');
        $official_price = $request->post('official_price');
        $goods_spec_code_id = $request->post('goods_spec_code_id');
        $is_pick_up_by_customer = $request->post('is_pick_up_by_customer');
        $is_logistics = $request->post('is_logistics');
        $is_substituting = $request->post('is_substituting');
        $substituting_shipping_rate = $request->post('substituting_shipping_rate');
        $logistics_shipping_rate = $request->post('logistics_shipping_rate');
        foreach ($product_sku as &$sku) {
            $sku['product_sku_price'] = intval($sku['product_sku_price']) * 100;
            $sku['sku_price'] = intval($sku['sku_price']) * 100;

            if (null === $sku['sku_code']) {
                $sku['sku_code'] = '';
            }
            unset($sku['sku_image_list'][0]['path_full']);

            if (0 === $sku['sku_price'] || '' === $sku['sku_price']) {
                unset($sku['sku_price']);
            }
        }

        unset($sku);

        foreach ($product_property as &$prop) {
            if (! empty($prop['tplVal'])) {
                unset($prop['tplVal']);
            }
        }

        if (! empty($cover_image)) {
            foreach ($cover_image as &$image) {
                unset($image['path_full']);
            }
        }

        if (! empty($detail_image)) {
            foreach ($detail_image as &$image) {
                unset($image['path_full']);
            }
        }

        $api = '/goods/editGoodsDetail';

        if ('0' === $status) {
            $params = [
                'user_no' => $user_no,
                'goods_name' => $goods_name,
                'product_no' => $goods_no,
                'brand_id' => $brand_id,
                'brand_name_e' => $brand_name_e,
                'brand_name_c' => $brand_name_c,
                'product_property' => $product_property,
                'description' => strip_tags($description),
                'virtual_cate_id' => $virtual_cate_id,
                'final_level_cate_id' => $final_level_cate_id,
                'final_level_cate_name' => $final_level_cate_name,
                'first_level_cate_id' => $first_level_cate_id,
                'first_level_cate_name' => $first_level_cate_name,
                'show_status' => $show_status,
                'channel_source' => $channel_source,
                'product_sku' => $product_sku,
                'cover_image' => $cover_image,
                'detail_image' => $detail_image,
                'ofashion_bar_code' => $ofashion_bar_code,
                'currency' => $currency,
                'stock' => $stock,
                'product_price' => $product_price * 100,
                'official_price' => $official_price * 100,
                'goods_spec_code_id' => $goods_spec_code_id,
                'is_pick_up_by_customer' => (int) $is_pick_up_by_customer,
                'is_logistics' => (int) $is_logistics,
                'is_substituting' => (int) $is_substituting,
                'substituting_shipping_rate' => $substituting_shipping_rate * 100,
                'logistics_shipping_rate' => $logistics_shipping_rate * 100,
            ];
        } else {
            $params = [
                'user_no' => $user_no,
                'goods_name' => $goods_name,
                'goods_no' => $goods_no,
                'brand_id' => $brand_id,
                'brand_name_e' => $brand_name_e,
                'brand_name_c' => $brand_name_c,
                'product_property' => $product_property,
                'description' => $description,
                'virtual_cate_id' => $virtual_cate_id,
                'final_level_cate_id' => $final_level_cate_id,
                'final_level_cate_name' => $final_level_cate_name,
                'first_level_cate_id' => $first_level_cate_id,
                'first_level_cate_name' => $first_level_cate_name,
                'show_status' => $show_status,
                'channel_source' => $channel_source,
                'product_sku' => $product_sku,
                'cover_image' => $cover_image,
                'detail_image' => $detail_image,
                'ofashion_bar_code' => $ofashion_bar_code,
                'currency' => $currency,
                'stock' => $stock,
                'product_price' => $product_price * 100,
                'official_price' => $official_price * 100,
                'goods_spec_code_id' => $goods_spec_code_id,
                'is_pick_up_by_customer' => (int) $is_pick_up_by_customer,
                'is_logistics' => (int) $is_logistics,
                'is_substituting' => (int) $is_substituting,
                'substituting_shipping_rate' => $substituting_shipping_rate * 100,
                'logistics_shipping_rate' => $logistics_shipping_rate * 100,
            ];
        }
        //echo json_encode($params);exit;

        if ('' === $ofashion_bar_code) {
            unset($params['ofashion_bar_code']);
        }

        if (null === $substituting_shipping_rate) {
            unset($params['substituting_shipping_rate']);
        }

        if (null === $logistics_shipping_rate) {
            unset($params['logistics_shipping_rate']);
        }

        if (null === $goods_no) {
            unset($params['goods_no']);
        }

        foreach ($params as $key => &$item) {
            $item = $this->filterData($item);
        }

        $data = $this->post($api, $params);

        return response()->json($data);
    }

    public function filterChar($str)
    {
        return preg_replace('/[\p{Zp}\p{Zl}]+/u', '', $str);
    }

    public function filterData($data)
    {
        if (! is_array($data)) {
            return $this->filterChar($data);
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->filterData($value);
            } else {
                $data[$key] = $this->filterChar($value);
            }
        }

        return $data;
    }
}
