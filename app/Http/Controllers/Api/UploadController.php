<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OSS\OssClient;
use OSS\Core\OssException;

class UploadController extends BaseController
{
    //
    private $bucket = 'com-ofashion-supply-chain';
    private $access_key_id = 'LTAI7ktPHKkrWA7x';
    private $access_key_secret = '9llaexRGJu0cJ98qKvEJfxd2AJvz2D';
    private $endpoint = 'https://oss-cn-beijing.aliyuncs.com';

    public function __construct()
    {
        parent::__construct();
    }

    public function getUploadSign(Request $request)
    {
        $path_name = $request->input('path_name', 'register');
        $path_name = preg_replace('/[^a-zA-Z0-9\-\_\/]/', '', $path_name);
        $host = 'https://com-ofashion-supply-chain.oss-cn-beijing.aliyuncs.com';

        // 设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $expire = 30;
        $now = time();
        $end = $now + $expire;

        // 过期时间
        $expiration = date('c', $end);

        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        $expiration = $expiration.'Z';

        // 指定路径
        $date = date('Ymd', time());

        // 根据path_name 区分对应路径
        if ('register' === $path_name) {
            $path_name_cn = 'projects/ofms/'.$path_name;
        } else {
            $path_name_cn = 'public/'.$path_name;
        }

        $path_prefix = '';

        if ('local' === env('APP_ENV') || 'test' === env('APP_ENV')) {
            $path_prefix = 'testing/';
        }

        $dir_tmp = $path_prefix.$path_name_cn.'/'.$date.'/'.uniqid().'/';
        $dir = preg_replace('#\/{2,}#', '/', $dir_tmp);

        // 最大文件大小10M，用户可以自己设置
        $condition = [0 => 'content-length-range', 1 => 0, 2 => 10485760];
        $conditions[] = $condition;
        $start = [0 => 'starts-with', 1 => '$key', 2 => $dir];
        $conditions[] = $start;

        $arr = ['expiration' => $expiration, 'conditions' => $conditions];

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->access_key_secret, true));

        $response = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'accessid' => $this->access_key_id,
                'host' => $host,
                'policy' => $base64_policy,
                'signature' => $signature,
                'expire' => $end,
                'dir' => $dir,
            ],
        ];

        return response()->json($response);
    }

    public function uploadBlob(Request $request)
    {
        // 开放出上传到又拍云的自定义文件夹路径
        $targetDir = $request->input('targetDir');
        $data = $request->input('file');
        $file = $this->base64ToBlob($data);

        // visiting upyun begin-----------------------------------------------------
        $date = date('Ymd', time());
        $time = date('YmdHis', time());
        $timestamp = time();
        $path_name = '/ofms/'.$targetDir.$date.'/'.$timestamp.'_'.uniqid();

        // 文件上传到服务器的服务端路径
        $uri = '/'.$this->bucket.$path_name;

        // 阿里云上传
        $object = str_replace('/'.$this->bucket.'/', '', $uri);

        $data = $this->aliyunOssUpload([
            'object' => $object,
            'file' => $file,
            'realPath' => $file,
        ]);

        return response()->json($data);
    }

    // base64 转 二进制流
    public function base64ToBlob($base64Str)
    {
        $index = strpos($base64Str, 'base64,', 0);

        if ($index) {
            $blobStr = substr($base64Str, $index + 7);
            $typestr = substr($base64Str, 0, $index);
            preg_match('/^data:(.*);$/', $typestr, $arr);

            return ['blob' => base64_decode($blobStr, true), 'type' => $arr[1]];
        }

        return false;
    }

    // 上传阿里云php SDK
    private function aliyunOssUpload($config)
    {
        // 阿里云上传，object字段必须是相对路径
        $config['object'] = preg_replace('#^/#', '', $config['object']);
        $result = null;

        try {
            $ossClient = new OssClient($this->access_key_id, $this->access_key_secret, $this->endpoint);

            $ossClient->setTimeout(60); // seconds
            $ossClient->setConnectTimeout(10); // seconds

            $result = $ossClient->uploadFile($this->bucket, $config['object'], $config['file']);
        } catch (OssException $e) {
            $result = 'Status: '.$e->getHTTPStatus().' Code: '.$e->getErrorCode().' Message: '.$e->getMessage();
        }

        if (is_array($result) && ! empty($result['oss-request-url'])) {
            $rst = [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'path' => '/'.$config['object'],
                    'width' => $config['width'],
                    'height' => $config['height'],
                    'realPath' => $config['realPath'],
                    'path_cn' => cdn_image('/'.$config['object'], ''),
                ],
            ];
        } else {
            $rst = [
                'code' => 500,
                'msg' => 'fail',
            ];
        }

        // 删除本地图片文件
        @unlink($config['file']);

        return $rst;
    }
}
