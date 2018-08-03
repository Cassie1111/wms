<?php

namespace App\Http\Controllers\Api\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class ActivityController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取活动列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activity_id = $request->input('activity_id');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $page = $request->input('page', 1);
        $count = $request->input('count', 20);
        $start = ($page - 1) * $count;

        $params = [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'start' => intval($start),
            'count' => intval($count),
        ];

        if ($activity_id) {
            $params['activity_id'] = $activity_id;
        }

        $api = '/activity/getSeckillActivityList';
        $data = $this->get($api, $params);

        if ('success' === $data['msg'] && ! empty($data['data']['activity_list'])) {
            foreach ($data['data']['activity_list'] as &$item) {
                $item['cover_image']['path_full'] = isset($item['cover_image']['path']) ?
                    cdn_image($item['cover_image']['path'], 'M') : '';
                $item['status_cn'] = $this->statusCn($item['status']);
            }
        }

        return response()->json($data);
    }

    /**
     * 获取带有秒杀价格的商品列表
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $goods_no_list = $request->input('goods_no_list');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $params = [
            'goods_no_list' => $goods_no_list,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];

        $api = '/activity/getGoodsSeckillPriceList';
        $data = $this->get($api, $params);

        if ('success' === $data['msg'] && ! empty($data['data']['goods_list'])) {
            foreach ($data['data']['goods_list'] as &$item) {
                $item['cover_image'][0]['path_full'] = isset($item['cover_image'][0]['path']) ?
                    cdn_image($item['cover_image'][0]['path'], 'S') : '';
                $item['product_price'] = ($item['product_price'] > 0) ? number_format($item['product_price'] / 100, 2) : $item['product_price'];
                $item['format_min_price'] = ($item['min_price'] > 0) ? number_format($item['min_price'] / 100, 2) : $item['min_price'];
                $item['min_price'] = ($item['min_price'] > 0) ? ($item['min_price'] / 100) : $item['min_price'];
                $item['seckill_price'] = ($item['seckill_price'] > 0) ? ($item['seckill_price'] / 100) : $item['seckill_price'];
            }
        }

        return response()->json($data);
    }

    /**
     * 新建活动
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seckill_id = $request->input('seckill_id');
        $title = $request->input('title');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $preheating_time = $request->input('preheating_time');
        $goods_list = $request->input('goods_list');

        foreach ($goods_list as &$item) {
            if (isset($item['seckill_price'])) {
                $item['seckill_price'] = $item['seckill_price'] * 100;
            }
        }

        $params = [
            'seckill_id' => $seckill_id,
            'title' => $title,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'preheating_time' => $preheating_time,
            'goods_list' => $goods_list,
        ];

        $api = '/activity/editSeckillActivityDetail';
        $data = $this->post($api, $params);

        return response()->json($data);
    }

    /**
     * 获取活动详情
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $page = $request->input('page');
        $count = $request->input('count');
        $start = isset($page) ? ($page - 1) * $count : null;

        $params = [
            'seckill_id' => $id,
            'start' => $start,
            'count' => $count,
        ];

        $api = '/activity/getSeckillActivityDetail';
        $data = $this->get($api, $params);

        if ('success' === $data['msg'] && ! empty($data['data']['activity_detail']['goods_list'])) {
            foreach ($data['data']['activity_detail']['goods_list'] as &$item) {
                $item['cover_image'][0]['path_full'] = isset($item['cover_image'][0]['path']) ?
                    cdn_image($item['cover_image'][0]['path'], 'S') : '';
                $item['product_price'] = ($item['product_price'] > 0) ? number_format($item['product_price'] / 100, 2) : $item['product_price'];
                $item['min_price'] = ($item['min_price'] > 0) ? number_format($item['min_price'] / 100, 2) : $item['min_price'];
                $item['seckill_price'] = ($item['seckill_price'] > 0) ? number_format($item['seckill_price'] / 100, 2) : $item['seckill_price'];
            }
        }

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

    private function statusCn($status)
    {
        switch ($status) {
            case 1:
                return '未开始';
            case 2:
                return '已开始';
            case 3:
                return '已结束';
            default:
                return '-';
        }
    }
}
