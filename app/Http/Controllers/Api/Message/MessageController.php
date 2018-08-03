<?php
/**
 * Created by PhpStorm.
 * User: zhanglichan
 * Date: 2018/5/30
 * Time: 下午4:56.
 */

namespace App\Http\Controllers\Api\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class MessageController extends BaseController
{
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * show messageList.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMessageList(Request $request)
    {
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        //设置分页参数
        //分页页码
        $count = $request->input('count') ? intval($request->input('count')) : 20;
        $start = $count * ($page - 1);
        $param = [
            'start' => $start,
            'count' => $count,
        ];
        $url = '/message/getMessageList';
        $data = $this->get($url, $param);

        return response()->json($data);
    }

    /**
     * show noticeList.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNoticeList(Request $request)
    {
        $page = $request->input('page') ? intval($request->input('page')) : 1;
        //设置分页参数
        //分页页码
        $count = $request->input('count') ? intval($request->input('count')) : 20;
        $start = $count * ($page - 1);
        $param = [
            'start' => $start,
            'count' => $count,
        ];
        $url = '/notice/getNoticeList';
        $data = $this->get($url, $param);

        return response()->json($data);
    }

    /**
     * read message.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateMessage()
    {
        $url = '/message/editMessageStatus';
        $data = $this->get($url);

        return response()->json($data);
    }

    /**
     * getMessageDetail.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNoticeDetail(Request $request)
    {
        $id = $request->input('id');
        $param = [
            'notice_id' => $id,
        ];
        $url = '/notice/getNoticeDetail';
        $data = $this->get($url, $param);

        return response()->json($data);
    }
}
