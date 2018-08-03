<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

class ServiceController extends BaseController
{
    // 发送消息类型，1，普通文本；2，图片
    const TYPE_MSG_TXT = 1;
    const TYPE_MSG_IMG = 2;
    const TYPE_UNREAD_MSG = 2;
    const PAGE_COUNT = 30;

    public function _construct()
    {
        parent::_construct();
    }

    // 获取消息数
    public function getUnReadConversationList()
    {
        $params = [
            'uid' => self::$service_id,
        ];
        $url = '/conversation/getUnReadConversationList';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 左侧用户列表--未读unread
    public function getUnreadUserList()
    {
        $params = [
            'uid' => self::$service_id,
        ];

        $url = '/conversation/getUnReadConversationList';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 左侧用户列表--已读read
    public function getReadUserList()
    {
        $params = [
            'uid' => self::$service_id,
        ];

        $url = '/conversation/getReadConversationList';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 拉取消息   TODO 相关uid是硬编码的，需要更改
    public function pull(Request $request)
    {
        $recv_uid = $request->input('recv_uid');
        $contact_id = $request->input('contact_id');
        $id = $request->input('id', null);
        $call_type = $request->input('call_type', 1);
        $count = self::PAGE_COUNT;

        $params = [
            'send_uid' => self::$service_id,
            'recv_uid' => $recv_uid,
            'contact_id' => $contact_id,
            'id' => $id,
            'call_type' => $call_type,
            'count' => $count,
        ];

        $url = '/conversation/getUnreadPrivateMessageList';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 推送消息
    public function push(Request $request)
    {
        $recv_uid = $request->input('recv_uid');
        $contact_id = $request->input('contact_id');
        $type = intval($request->input('type', 0));
        $content = trim($request->input('content', ''));
        $image = $request->input('image');

        // TODO  用validator做类型判断
        if (! in_array($type, [self::TYPE_MSG_TXT, self::TYPE_MSG_IMG], true) ||
            (self::TYPE_MSG_TXT === $type && '' === $content) ||
            (self::TYPE_MSG_IMG === $type &&
                (! is_array($image) || empty($image['url']) || empty($image['width']) || empty($image['height'])))) {
            return abort(500, '消息不能为空');
        }

        if (self::TYPE_MSG_TXT === $type) {
            $image = null;
        } else {
            $content = null;
        }

        $params = [
            'send_uid' => self::$service_id,
            'recv_uid' => $recv_uid,
            'type' => $type,
            'contact_id' => $contact_id,
            'content' => $content,
            'image' => $image,
        ];
        $url = '/conversation/sendPrivateMessage';
        $data = $this->post($url, $params);

        return response()->json($data);
    }

    // 释放会话
    public function release(Request $request)
    {
        $recv_uid = $request->input('recv_uid');
        $contact_id = $request->input('contact_id');

        $params = [
            'send_uid' => self::$service_id,
            'recv_uid' => $recv_uid,
            'contact_id' => $contact_id,
        ];
        $url = '/conversation/releaseContactLock';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 搜索聊天记录
    public function search(Request $request)
    {
        $keywords = trim($request->input('keywords', ''));
        $start = intval($request->input('start', 0));
        $count = self::PAGE_COUNT;

        $params = [
            'uid' => self::$service_id,
            'keywords' => $keywords,
            'start' => $start,
            'count' => $count,
        ];

        $url = '/conversation/searchChat';
        $data = $this->get($url, $params);

        return response()->json($data);
    }

    // 获取官方客服环信等配置信息
    public function getConfig()
    {
        $data = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'customer_service' => [
                    'service_id' => self::$service_id,
                ],
            ],
        ];

        return response()->json($data);
    }
}
