<?php

namespace App\Http\Controllers\Api\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class ResourceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取资源列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $count = $request->input('count', 12);
        $start = ($page - 1) * $count;

        $params = [
            'start' => intval($start),
            'count' => intval($count),
        ];

        $api = '/collection/getCollectionList';
        $data = $this->get($api, $params);

        if ('success' === $data['msg'] && ! empty($data['data']['collection_list'])) {
            foreach ($data['data']['collection_list'] as &$item) {
                $item['cover_image']['path_full'] = isset($item['cover_image']['path']) ?
                    cdn_image($item['cover_image']['path'], 'M') : '';
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
     * 新建资源.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $collection_id = $request->input('collection_id');
        $title = $request->input('title');
        $cover_image = $request->input('cover_image');
        $goods_no_list = $request->input('goods_no_list');
        $params = [
            'collection_id' => intval($collection_id),
            'title' => $title,
            'cover_image' => $cover_image,
            'goods_no_list' => $goods_no_list,
        ];

        $api = '/collection/editCollectionDetail';
        $data = $this->post($api, $params);

        return response()->json($data);
    }

    /**
     * 获取资源详情.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $params = [
            'collection_id' => intval($id),
        ];

        $api = '/collection/getCollectionDetail';
        $data = $this->get($api, $params);

        if ('success' === $data['msg']) {
            $data['data']['collection_detail']['cover_image_path'] = isset($data['data']['collection_detail']['cover_image']['path']) ?
                cdn_image($data['data']['collection_detail']['cover_image']['path'], 'M') : '';

            if (! empty($data['data']['collection_detail']['goods_list'])) {
                foreach ($data['data']['collection_detail']['goods_list'] as &$item) {
                    $item['cover_image'][0]['path_full'] = isset($item['cover_image'][0]['path']) ?
                        cdn_image($item['cover_image'][0]['path'], 'S') : '';
                    $item['product_price'] = (isset($item['product_price']) && $item['product_price'] > 0) ? number_format($item['product_price'] / 100, 2) : '';
                    $item['min_price'] = ($item['min_price'] > 0) ? number_format($item['min_price'] / 100, 2) : '';
                }
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
     * 删除资源.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $params = [
            'collection_id' => intval($id),
            'status' => 0,
        ];

        $api = '/collection/editCollectionStatus';
        $data = $this->post($api, $params);

        return response()->json($data);
    }
}
