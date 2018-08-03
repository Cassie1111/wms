<?php

namespace App\Http\Controllers\Api\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class PusherController extends BaseController
{
    const SEND_CUSTOM_MSG_ALL = 1;
    const SEND_CUSTOM_MSG_PART = 2;

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
        // 获取参数
        $send_type = $request->input('type');
        $page = $request->input('page', 1);
        $count = 20;
        $start = ($page - 1) * $count;

        $params = [
            'send_type' => $send_type,
            'start' => $start,
            'count' => $count,
        ];
        $url = '/message/getPushList';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 获取参数
        $send_type = $request->input('send_type');
        $date = $request->input('date');
        $time = $request->input('time');
        $content = $request->input('content');
        $send_uid_list = $request->input('send_uid_list', ''); // 字符串类型
        $send_custom_type = self::SEND_CUSTOM_MSG_ALL;
        $author_id = self::$user_info['user_no'];
        $author_name = self::$user_info['realname'];

        if (! empty($date) && ! empty($time)) {
            $send_time = $date.' '.$time;
        } else {
            $send_time = date('Y-m-d H:i:s', time());
        }

        $params = [
            'send_type' => $send_type,
            'send_time' => $send_time,
            'content' => $content,
            'author_id' => $author_id,
            'author_name' => $author_name,
        ];

        if (! empty($send_uid_list)) {
            $send_custom_type = self::SEND_CUSTOM_MSG_PART;
            $params['send_uid_list'] = explode(',', $send_uid_list);
        }

        $params['send_custom_type'] = $send_custom_type;
        $url = '/message/senderPush';
        $data = $this->post($url, $params);

        return response()->json($data);
    }
}
