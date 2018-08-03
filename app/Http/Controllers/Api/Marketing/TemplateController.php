<?php

namespace App\Http\Controllers\Api\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class TemplateController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 轮播列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $api = '/schedule/getBannerList';
        $data = $this->get($api);

        if ('success' === $data['msg'] && ! empty($data['data']['banner_list'])) {
            foreach ($data['data']['banner_list'] as &$item) {
                $item['cover_image']['path_full'] = isset($item['cover_image']['path']) ?
                    cdn_image($item['cover_image']['path'], 'S') : '';
            }
        }

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
     * 新建轮播.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $schedule_id = $request->input('schedule_id');
        $title = $request->input('title');
        $cover_image = $request->input('cover_image');
        $resource_type = $request->input('resource_type');
        $resource_id = $request->input('resource_id');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');

        $params = [
            'title' => $title,
            'cover_image' => $cover_image,
            'resource_type' => intval($resource_type),
            'resource_id' => intval($resource_id),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'user_no' => self::$user_no,
        ];

        if ($schedule_id) {
            $params['schedule_id'] = intval($schedule_id);
        }

        $api = '/schedule/editBannerDetail';
        $data = $this->post($api, $params);

        return response()->json($data);
    }

    /**
     * 获取轮播详情.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = [
            'schedule_id' => intval($id),
        ];

        $api = '/schedule/getBannerDetail';
        $data = $this->get($api, $params);

        if ('success' === $data['msg']) {
            $data['data']['banner_detail']['cover_image_path'] = isset($data['data']['banner_detail']['cover_image']['path']) ?
               cdn_image($data['data']['banner_detail']['cover_image']['path'], 'M') : '';
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
     * 删除轮播.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $params = [
            'schedule_id' => intval($id),
            'status' => 0,
        ];

        $api = '/schedule/editBannerStatus';
        $data = $this->post($api, $params);

        return response()->json($data);
    }

    // 获取活动配置列表
    public function getActivityConfigList()
    {
        $api = '/schedule/getActivityConfigList';
        $data = $this->get($api);

        if ('success' === $data['msg'] && ! empty($data['data']['activity_list'])) {
            foreach ($data['data']['activity_list'] as &$item) {
                if (! empty($item['goods_list'])) {
                    foreach ($item['goods_list'] as &$goods) {
                        $goods['cover_image'][0]['path_full'] = isset($goods['cover_image'][0]['path']) ?
                            cdn_image($goods['cover_image'][0]['path'], 'S') : '';
                        $goods['official_price'] = ($goods['official_price'] > 0) ? number_format($goods['official_price'] / 100, 2) : '';
                        $goods['seckill_price'] = ($goods['seckill_price'] > 0) ? number_format($goods['seckill_price'] / 100, 2) : '';
                    }
                }
            }
        }

        return response()->json($data);
    }

    public function getActivityConfigDetail(Request $request)
    {
        $config_id = $request->input('config_id');
        $params = [
            'config_id' => $config_id,
        ];

        $api = '/schedule/getActivityConfigDetail';
        $data = $this->get($api, $params);

        return response()->json($data);
    }

    public function editActivityConfigDetail(Request $request)
    {
        $config_id = $request->input('config_id');
        $title = $request->input('title');
        $activity_id = $request->input('activity_id');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $goods_no_list = $request->input('goods_no_list');

        $params = [
            'config_id' => intval($config_id),
            'title' => $title,
            'activity_id' => intval($activity_id),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'goods_no_list' => $goods_no_list,
        ];

        $api = '/schedule/editActivityConfigDetail';
        $data = $this->post($api, $params);

        return response()->json($data);
    }
}
