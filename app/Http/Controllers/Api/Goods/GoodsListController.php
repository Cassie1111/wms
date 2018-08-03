<?php

namespace App\Http\Controllers\Api\Goods;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Maatwebsite\Excel\Facades\Excel;

class GoodsListController extends BaseController
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

    // 获取商品列表
    public function getGoodsList(Request $request)
    {
        $start = $request->input('start', 0);
        $count = $request->input('count', 50);
        $status = $request->input('status', 1);
        $brand_id = $request->input('brand_id', '');
        $cate_id = $request->input('cate_id', '');
        $brand_no = $request->input('brand_no', '');
        $code = $request->input('code', '');
        $product_name = $request->input('goods_name', '');
        $supplier_no = $request->input('supplier_no', '');

        $param = [
            'supplier_no' => $supplier_no,
            'start' => $start,
            'count' => $count,
            'status' => $status,
            'brand_id' => $brand_id,
            'type_id' => $cate_id,
            'product_model' => $brand_no,
            'code' => $code,
            'goods_name' => $product_name,
        ];

        $api = '/goods/getGoodsList';

        $data = $this->get($api, $param);

        if (! empty($data['data']['goods_list'])) {
            foreach ($data['data']['goods_list'] as &$product_item) {
                foreach ($product_item['cover_image'] as &$img_item) {
                    $img_item['path_full'] = cdn_image($img_item['path'], 'XS');
                }
            }
        }

        return response()->json($data);
    }

    public function getSupplierList()
    {
        $api = '/supplier/getSupplierList';

        $params = [
            'start' => 0,
            'count' => 1000,
        ];

        $data = $this->get($api, $params);

        return response()->json($data);
    }

    // 获取所有品牌
    public function getBrands()
    {
        $supplier_no = self::$user_no;
        $param = [
            'supplier_no' => $supplier_no,
        ];

        $api = '/product/getBrands';

        $data = $this->get($api, $param);

        return response()->json($data);
    }

    // 批量调价
    public function updatePrice(Request $request)
    {
        $goods_no_list = $request->input('goods_no_list');
        $type = $request->input('type');
        $operate_mode = $request->input('operate_mode');
        $operate_value = $request->input('operate_value');
        $user_no = self::$user_no;

        $params = [
            'goods_no_list' => $goods_no_list,
            'type' => $type,
            'operate_mode' => $operate_mode,
            'operate_value' => $operate_value,
            'user_no' => $user_no,
        ];

        $api = '/goods/editGoodsPriceBatch';

        $data = $this->post($api, $params);

        return response()->json($data);
    }

    // 批量下架
    public function unshelve(Request $request)
    {
        $goods_no_list = $request->input('goods_no_list');
        $show_status = $request->input('show_status');
        $user_no = self::$user_no;
        $tag = $request->input('tag');

        if ('product_no' === $tag) {
            $params = [
                'product_no_list' => $goods_no_list,
                'show_status' => intval($show_status),
                'user_no' => $user_no,
            ];
        } else {
            $params = [
                'goods_no_list' => $goods_no_list,
                'show_status' => intval($show_status),
                'user_no' => $user_no,
            ];
        }

        // echo json_encode($params);exit;

        $api = '/goods/editGoodsStatusBatch';

        $data = $this->post($api, $params);

        return response()->json($data);
    }

    // 导出
    public function exportProductList(Request $request)
    {
        $status = $request->post('status', 1);
        $goods_no_list = $request->post('product_no_list', null);

        $params = [
            'status' => intval($status),
            'goods_no_list' => explode(',', $goods_no_list),
        ];
        $api = '/goods/exportGoodsList';
        $data = $this->post($api, $params);
        $goods = [];
        // echo json_encode($data);exit;
        if (empty($data['data']['goods_list'])) {
            array_push($goods, ['暂无数据']);
        } else {
            if ('0' === $status) {
                $headerTitle = ['商品ID', '供货商编码', '供货商', '商品条形码（S）', '商家编码（S）', '类目', '品牌',	'品牌货号', '商品名称', '官方货币单位',
                    '官方价格', '颜色', '规格/尺码', '库存', '供货价', ];
            } else {
                $headerTitle = ['商品ID', '供货商编码', '供货商', '商品条形码（S）', '商家编码（S）', '类目', '品牌',	'品牌货号', '商品名称', '官方货币单位',
                    '官方价格', '颜色', '规格/尺码', '库存', '供货价', '销售价', ];
            }

            array_push($goods, $headerTitle);

            if ('0' === $status) {
                foreach ($data['data']['goods_list'] as $key => $item) {
                    array_push($goods, [
                        $item['goods_no'],
                        $item['supplier_no'],
                        $this->userTextEncode($item['supplier_name']),
                        $item['bar_code'],
                        $item['sku_code'],
                        $this->userTextEncode($item['final_level_cate_name']),
                        $item['brand_name_e'],
                        $item['product_model'],
                        $this->userTextEncode($item['goods_name']),
                        $this->userTextEncode($item['currency']),
                        $item['official_price'] / 100,
                        $this->userTextEncode($item['sku_color']),
                        $this->userTextEncode($item['sku_spec']),
                        $item['sku_stock'],
                        $item['sku_price'] / 100,
                    ]);
                }
            } else {
                foreach ($data['data']['goods_list'] as $key => $item) {
                    array_push($goods, [
                        $item['goods_no'],
                        $item['supplier_no'],
                        $this->userTextEncode($item['supplier_name']),
                        $item['bar_code'],
                        $item['sku_code'],
                        $this->userTextEncode($item['final_level_cate_name']),
                        $item['brand_name_e'],
                        $item['product_model'],
                        $this->userTextEncode($item['goods_name']),
                        $this->userTextEncode($item['currency']),
                        $item['official_price'] / 100,
                        $this->userTextEncode($item['sku_color']),
                        $this->userTextEncode($item['sku_spec']),
                        $item['sku_stock'],
                        $item['product_sku_price'] / 100,
                        $item['sku_price'] / 100,
                    ]);
                }
            }
        }
        // 执行导出excel
        $filename = rawurlencode('买手宝商品管理系统【OFASHION】商品导出列表（OF端）');
        // $filename = mb_convert_encoding($filename, 'UTF-8');
        // $filename = str_replace('+', '%20', urlencode($filename));

        Excel::create($filename, function ($excel) use ($goods, $status) {
            $excel->sheet('买手宝商品管理系统【OFASHION】商品导出列表（OF端）', function ($sheet) use ($goods, $status) {
                $sheet->fromArray($goods, null, 'A1', true, false);
                $sheet->setHeight([
                    1 => 25,
                ]);

                if ('0' === $status) {
                    $sheet->setWidth([
                        'A' => 30,
                        'B' => 30,
                        'C' => 30,
                        'D' => 30,
                        'E' => 30,
                        'F' => 20,
                        'H' => 15,
                        'I' => 15,
                        'G' => 30,
                        'K' => 15,
                        'L' => 15,
                        'M' => 15,
                        'N' => 15,
                        'O' => 15,
                    ]);
                    $sheet->cells('A1:O1', function ($cells) {
                        $cells->setBackground('#CCCCCC');
                    });
                } else {
                    $sheet->setWidth([
                        'A' => 30,
                        'B' => 30,
                        'C' => 30,
                        'D' => 30,
                        'E' => 30,
                        'F' => 20,
                        'H' => 15,
                        'I' => 15,
                        'G' => 30,
                        'K' => 15,
                        'L' => 15,
                        'M' => 15,
                        'N' => 15,
                        'O' => 15,
                        'P' => 15,
                    ]);
                    $sheet->cells('A1:P1', function ($cells) {
                        $cells->setBackground('#CCCCCC');
                    });
                }

                //$sheet->setAutoSize(true);
            });
        })->export('xls');

        return response()->json($data);
    }

    // 导出全部
    public function exportAllProductList(Request $request)
    {
        ini_set('memory_limit', '256M');
        $start = $request->input('start', 0);
        $count = $request->input('count', 50);
        $status = $request->input('status', 1);
        $brand_id = $request->input('brand_id', '');
        $cate_id = $request->input('cate_id', '');
        $brand_no = $request->input('brand_no', '');
        $code = $request->input('code', '');
        $product_name = $request->input('goods_name', '');
        $supplier_no = $request->input('supplier_no', '');

        $param = [
            'supplier_no' => $supplier_no,
            'start' => $start,
            'count' => $count,
            'status' => $status,
            'brand_id' => $brand_id,
            'type_id' => $cate_id,
            'product_model' => $brand_no,
            'code' => $code,
            'goods_name' => $product_name,
        ];

        $api = '/goods/exportGoodsList';
        $data = $this->post($api, $param);
        $goods = [];

        if (empty($data['data']['goods_list'])) {
            array_push($goods, ['暂无数据']);
        } else {
            if ('0' === $status) {
                $headerTitle = ['商品ID', '供货商编码', '供货商', '商品条形码（S）', '商家编码（S）', '类目', '品牌',	'品牌货号', '商品名称', '官方货币单位',
                    '官方价格', '颜色', '规格/尺码', '库存', '供货价', ];
            } else {
                $headerTitle = ['商品ID', '供货商编码', '供货商', '商品条形码（S）', '商家编码（S）', '类目', '品牌',	'品牌货号', '商品名称', '官方货币单位',
                    '官方价格', '颜色', '规格/尺码', '库存', '供货价', '销售价', ];
            }
            array_push($goods, $headerTitle);

            if ('0' === $status) {
                foreach ($data['data']['goods_list'] as $key => $item) {
                    array_push($goods, [
                        $item['goods_no'],
                        $item['supplier_no'],
                        $this->userTextEncode($item['supplier_name']),
                        $item['bar_code'],
                        $item['sku_code'],
                        $this->userTextEncode($item['final_level_cate_name']),
                        $item['brand_name_e'],
                        $item['product_model'],
                        $this->userTextEncode($item['goods_name']),
                        $this->userTextEncode($item['currency']),
                        $item['official_price'] / 100,
                        $this->userTextEncode($item['sku_color']),
                        $this->userTextEncode($item['sku_spec']),
                        $item['sku_stock'],
                        $item['sku_price'] / 100,
                    ]);
                }
            } else {
                foreach ($data['data']['goods_list'] as $key => $item) {
                    array_push($goods, [
                        $item['goods_no'],
                        $item['supplier_no'],
                        $this->userTextEncode($item['supplier_name']),
                        $item['bar_code'],
                        $item['sku_code'],
                        $this->userTextEncode($item['final_level_cate_name']),
                        $item['brand_name_e'],
                        $item['product_model'],
                        $this->userTextEncode($item['goods_name']),
                        $this->userTextEncode($item['currency']),
                        $item['official_price'] / 100,
                        $this->userTextEncode($item['sku_color']),
                        $this->userTextEncode($item['sku_spec']),
                        $item['sku_stock'],
                        $item['product_sku_price'] / 100,
                        $item['sku_price'] / 100,
                    ]);
                }
            }
        }

        // 执行导出excel
        $filename = rawurlencode('买手宝商品管理系统【OFASHION】商品导出列表（OF端）');
        // $filename = mb_convert_encoding($filename, 'UTF-8');
        Excel::create($filename, function ($excel) use ($goods, $status) {
            $excel->sheet('买手宝商品管理系统【OFASHION】商品导出列表（OF端）', function ($sheet) use ($goods, $status) {
                $sheet->fromArray($goods, null, 'A1', true, false);
                $sheet->setHeight([
                    1 => 25,
                ]);

                if ('0' === $status) {
                    $sheet->setWidth([
                        'A' => 30,
                        'B' => 30,
                        'C' => 30,
                        'D' => 30,
                        'E' => 30,
                        'F' => 20,
                        'H' => 15,
                        'I' => 15,
                        'G' => 30,
                        'K' => 15,
                        'L' => 15,
                        'M' => 15,
                        'N' => 15,
                        'O' => 15,
                    ]);
                    $sheet->cells('A1:O1', function ($cells) {
                        $cells->setBackground('#CCCCCC');
                    });
                } else {
                    $sheet->setWidth([
                        'A' => 30,
                        'B' => 30,
                        'C' => 30,
                        'D' => 30,
                        'E' => 30,
                        'F' => 20,
                        'H' => 15,
                        'I' => 15,
                        'G' => 30,
                        'K' => 15,
                        'L' => 15,
                        'M' => 15,
                        'N' => 15,
                        'O' => 15,
                        'P' => 15,
                    ]);
                    $sheet->cells('A1:P1', function ($cells) {
                        $cells->setBackground('#CCCCCC');
                    });
                }
                //$sheet->setAutoSize(true);
            });
        })->export('xls');

        return response()->json($data);
    }

    // 同步商品信息
    public function syncOfGoodsInfo(Request $request)
    {
        $goods_no_list = $request->post('goods_no_list');

        $api = '/goods/syncOfGoodsInfo';

        $params = [
            'goods_no_list' => $goods_no_list,
        ];

        $data = $this->post($api, $params);

        return response()->json($data);
    }

    // 导入商品
    public function importGoods(Request $request)
    {
        $file = $request->file('file');
        $extensionArr = ['xls', 'xlsx'];
        $limit_flag = false;
        $limit_count = 1500;

        if (! $file->isValid()) {
            $errorMessage = '上传文件失败!';
            $data = [
                'error' => $errorMessage,
                'code' => 'A500',
                'status' => 'error',
            ];
        } else {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $file->getClientOriginalName();

            if (! in_array($fileExtension, $extensionArr, true)) {
                $errorMessage = '请上传正确的excel格式,如: xls, xlsx';
                $data = [
                    'error' => $errorMessage,
                    'code' => 'A500',
                    'status' => 'error',
                ];
            } else {
                $arr = [];
                Excel::load($filePath, function ($reader) use (&$arr, &$limit_flag, $limit_count) {
                    $result = $reader->get()->toArray();

                    foreach ($result as $item) {
                        $arr[] = [
                            'goods_no' => strval($this->userTextEncode($item[0])),
                            'color' => strval($this->userTextEncode($item[11])),
                            'spec' => strval($this->userTextEncode($item[12])),
                            'stock' => $this->userTextEncode($item[13]),
                            'goods_name' => strval($this->userTextEncode($item[8])),
                            'sku_price' => is_numeric($item[14]) ? $item[14] * 100 : $item[14],
                            'sku_code' => strval($this->userTextEncode($item[4])),
                            'sale_price' => is_numeric($item[15]) ? $item[15] * 100 : $item[15],
                        ];
                    }
//                    if (count($result) > $limit_count) {
//                        $limit_flag = true;
//                        $arr = array_slice($arr, 0 , $limit_count,true);
//                    }
                });

                if (! empty($arr[0])) {
                    foreach ($arr as $item) {
                        if (! is_numeric($item['sku_price']) || ! preg_match('/^[1-9][0-9]*$/', $item['sku_price'] / 100)) {
                            $errorMessage = '供货价应为正整数';
                            $data = [
                                'error' => $errorMessage,
                                'code' => 'A500',
                                'status' => 'error',
                            ];

                            return response()->json($data);
                        }

                        if (! is_numeric($item['sale_price']) || ! preg_match('/^[1-9][0-9]*$/', $item['sale_price'] / 100)) {
                            $errorMessage = '分销价应为正整数';
                            $data = [
                                'error' => $errorMessage,
                                'code' => 'A500',
                                'status' => 'error',
                            ];

                            return response()->json($data);
                        }

                        if (! is_numeric($item['stock']) || ! preg_match('/^[0-9]*$/', $item['stock'])) {
                            $errorMessage = '库存应该为非负整数';
                            $data = [
                                'error' => $errorMessage,
                                'code' => 'A500',
                                'status' => 'error',
                            ];

                            return response()->json($data);
                        }
                    }
                }

//                if ($limit_flag === true) {
//                    $data = [
//                        'error' => '目前最多支持导入更新1500条数据',
//                        'code' => 'A500',
//                        'data' => json_encode($arr),
//                        'status' => 'error',
//                    ];
//                } else {
//                    $data = $this->editGoodsInfoBatch($arr);
//                }
                $data = $this->editGoodsInfoBatch($arr);
            }
        }

        return response()->json($data);
    }

    private function editGoodsInfoBatch($goods)
    {
        $goods_info_list = $goods;

        $params = [
            'user_no' => self::$user_no,
            'goods_info_list' => $goods_info_list,
        ];

        $api = '/goods/editGoodsInfoBatch';

        $data = $this->post($api, $params);

        return $data;
    }
}
