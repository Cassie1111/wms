<?php

namespace App\Http\Controllers\Api\Goods;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Cache;

class CreateGoodsController extends BaseController
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

    // 类目关键字搜索
    public function searchProductQuoteType(Request $request)
    {
        $keywords = $request->get('keywords', '');

        $params = [
            'keywords' => $keywords,
        ];

        $api = '/goods/searchProductQuoteType';

        $data = $this->get($api, $params);

        return response()->json($data);
    }

    // 获取所有类目
    public function getCates()
    {
        $params = [
            'user_no' => self::$user_no,
        ];

        // $cates = Cache::get('get_cates');

        $api = '/goods/getSellerGoodsQuoteTypeTree';

        $data = $this->get($api, $params);

        return response()->json($data);
    }

    // 阅读公告及规则
    public function readRemindInfo(Request $request)
    {
        $announcement_id = $request->input('announcement_id');
        $ip = $_SERVER['SERVER_ADDR'];
        $params = [
            'announcement_id' => $announcement_id,
            'user_no' => self::$user_no,
            'ip' => $ip,
        ];

        $api = '/user/readRemindInfo';

        $data = $this->post($api, $params);

        return response()->json($data);
    }

    // 获取最新公告
    public function getLastestAnnouncement()
    {
        $api = '/announcement/getLastestAnnouncement';

        $data = $this->get($api);

        return response()->json($data);
    }
}
