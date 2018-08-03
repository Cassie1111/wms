<?php

namespace App\Http\Controllers\Api\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class CommonController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getGoodsList(Request $request)
    {
        $status = $request->input('status');
        $supplier_no = $request->input('supplier_no');
        $brand_id = $request->input('brand_id');
        $type_id = $request->input('type_id');
        $product_model = $request->input('product_model');
        $code = $request->input('code');
        $goods_name = $request->input('goods_name');
        $page = $request->input('page', 1);
        $count = $request->input('count', 20);
        $start = ($page - 1) * $count;

        $params = [
            'status' => $status,
            'supplier_no' => $supplier_no,
            'brand_id' => $brand_id,
            'type_id' => $type_id,
            'product_model' => $product_model,
            'code' => $code,
            'goods_name' => $goods_name,
            'start' => intval($start),
            'count' => intval($count),
        ];

        $api = '/goods/getGoodsList';
        $data = $this->get($api, $params);

        if ('success' === $data['msg'] && ! empty($data['data']['goods_list'])) {
            foreach ($data['data']['goods_list'] as &$item) {
                $item['cover_image'][0]['path_full'] = isset($item['cover_image'][0]['path']) ?
                    cdn_image($item['cover_image'][0]['path'], 'S') : '';
                $item['product_price'] = ($item['product_price'] > 0) ? number_format($item['product_price'] / 100, 2) : $item['product_price'];
                $item['min_price'] = ($item['min_price'] > 0) ? number_format($item['min_price'] / 100, 2) : $item['min_price'];
            }
        }

        return response()->json($data);
    }

    public function getSupplierList()
    {
        $api = '/supplier/getSupplierList';
        $data = $this->get($api);

        if ('success' === $data['msg'] && ! empty($data['data']['supplier_list'])) {
            foreach ($data['data']['supplier_list'] as &$item) {
                $item['value'] = isset($item['supplier_name']) ? $item['supplier_name'] : '';
            }
        }

        return response()->json($data);
    }

    public function getBrandList()
    {
        $api = '/brand/getBrandList';
        $data = $this->get($api);

        if ('success' === $data['msg'] && ! empty($data['data']['brand_list'])) {
            foreach ($data['data']['brand_list'] as &$item) {
                $item['value'] = isset($item['name_e']) ? $item['name_e'] : '';
            }
        }

        return response()->json($data);
    }
}
