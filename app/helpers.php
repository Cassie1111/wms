<?php

/**
 * 获取完整的图片路径，aliyun CDN.
 * @param string $img_uri 绝对、相对路径
 * @param string $size [optional] 默认为原图，可选缩率图大小 [XS,S,M,L]
 * @return string 完整的缩率图路径
 */
function cdn_image($img_uri, $size = '')
{
    static $thumbnail_size = [
        'XS' => 240,
        'S' => 360,
        'M' => 640,
        'L' => 750,
    ];

    $path = preg_replace('#^https?:\/\/.*?\/#', '/', $img_uri);
    $domain = env('IMG_URL');

    // 从header里判断浏览器是否支持webp格式
    $support_webp = isset($_SERVER['HTTP_ACCEPT']) && false !== strpos($_SERVER['HTTP_ACCEPT'], 'image/webp');

    // 阿里云--缩率图裁剪前缀
    $crop_prefix = ($size || $support_webp) ? '?x-oss-process=image' : '';

    // 阿里云--webp格式后缀
    $webp_suffix = $support_webp ? '/format,webp' : '';

    // 缩率图大小--按宽来缩放
    $size = $size ? '/resize,w_'.$thumbnail_size[strtoupper($size)] : '';

    return $domain.$path.$crop_prefix.$size.$webp_suffix;
}

// 返回当前站点的url
function site_url($path = '')
{
    $scheme = isset($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'] ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $tail = $path ? '/'.trim($path, '/') : '';

    return $scheme.'://'.$host.$tail;
}

// 返回API url
function api_url($path = '')
{
    // 如果是full url，直接返回
    if ($path && preg_match('#^https?:\/\/#', $path)) {
        return $path;
    }

    $host = env('API_URL');
    $tail = $path ? '/'.ltrim($path, '/') : '';

    return $host.$tail;
}

// 获取客服环信ID
function get_service_id()
{
    return env('SERVICE_ID');
}

// 获取毫秒时间戳
function get_millisecond_timestamp()
{
    return intval(microtime(true) * 1000);
}

// 报错信息通过邮件提醒
function send_email($info, $type = 'PHP')
{
    Mail::send('emails.error', ['info' => $info, 'type' => $type], function ($message) {
        $to = [
            'yazhou.zou@mfashion.com.cn',
            'jinfu.wangliu@mfashion.com.cn',
        ];
        $message->to($to)->subject('移动站报错信息');
    });
}

/**
 * seasLog日志.
 * @param mixed $message
 * @param array $content
 * @return null
 */
function seas_log($level, $message, $content)
{
    if (! config('seaslog.enable') || ! class_exists('SeasLog')) {
        return false;
    }

    SeasLog::setBasePath(config('seaslog.base_path'));
    SeasLog::setLogger(config('seaslog.logger'));
    SeasLog::log($level, $message, $content);
}

// 获取用户IP
function get_client_ip()
{
    if (! empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (false !== strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
            $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            foreach ($ip_list as $ip_item) {
                if (validate_ip($ip_item)) {
                    return $ip_item;
                }
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    if (! empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    } elseif (! empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (! empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }

    return $ip;
}

// 验证IP合法性
function validate_ip($ip)
{
    if ('unknown' === strtolower($ip)) {
        return false;
    }

    $ip = ip2long($ip);

    if (false !== $ip && -1 !== $ip) {
        $ip = sprintf('%u', $ip);

        if ($ip >= 0 && $ip <= 50331647) {
            return false;
        }

        if ($ip >= 167772160 && $ip <= 184549375) {
            return false;
        }

        if ($ip >= 2130706432 && $ip <= 2147483647) {
            return false;
        }

        if ($ip >= 2851995648 && $ip <= 2852061183) {
            return false;
        }

        if ($ip >= 2886729728 && $ip <= 2887778303) {
            return false;
        }

        if ($ip >= 3221225984 && $ip <= 3221226239) {
            return false;
        }

        if ($ip >= 3232235520 && $ip <= 3232301055) {
            return false;
        }

        if ($ip >= 4294967040) {
            return false;
        }
    }

    return true;
}

// 生成uuid
function create_uuid($prefix = '')
{
    $str = md5(uniqid(mt_rand(), true));
    $uuid = substr($str, 0, 8);
    $uuid .= substr($str, 8, 4);
    $uuid .= substr($str, 12, 4);
    $uuid .= substr($str, 16, 4);
    $uuid .= substr($str, 20, 12);

    return $prefix.$uuid;
}
