<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Session;
use Cache;

class BaseController extends Controller
{
    // 调用API时标识来源
    const API_SOURCE = 45;
    const SUB_ACCOUNT_TYPE = 0;

    public static $login_token = '';
    public static $user_no = '';
    public static $service_id = '';
    public static $user_info = [];
    public static $left_menu = [];
    public static $client_ip = '';
    public static $permission_tree = [
        0 => [
            'id' => 1,
            'level' => 1,
            'label' => '我的',
            'router_name' => 'home',
        ],
        1 => [
            'id' => 2,
            'level' => 1,
            'label' => '供应商管理',
            'children' => [
                0 => [
                    'id' => 10,
                    'level' => 2,
                    'label' => '我的供应商',
                    'children' => [
                        0 => [
                            'id' => 25,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'supplier-list',
                        ],
                        1 => [
                            'id' => 26,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'supplier-list',
                        ],
                    ],
                ],
            ],
        ],
        2 => [
            'id' => 3,
            'level' => 1,
            'label' => '分销商管理',
            'children' => [
                0 => [
                    'id' => 11,
                    'level' => 2,
                    'label' => '我的分销商',
                    'children' => [
                        0 => [
                            'id' => 27,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'retailer-list',
                        ],
                        1 => [
                            'id' => 28,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'retailer-list',
                        ],
                    ],
                ],
            ],
        ],
        3 => [
            'id' => 4,
            'level' => 1,
            'label' => '货品管理',
            'children' => [
                0 => [
                    'id' => 12,
                    'level' => 2,
                    'label' => '分销中的商品',
                    'children' => [
                        0 => [
                            'id' => 29,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'selling-goods',
                        ],
                        1 => [
                            'id' => 30,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'selling-goods',
                        ],
                        2 => [
                            'id' => 31,
                            'level' => 3,
                            'label' => '导入 / 导出商品',
                            'type' => 2,
                            'router_name' => 'selling-goods',
                        ],
                    ],
                ],
                1 => [
                    'id' => 13,
                    'level' => 2,
                    'label' => '我的仓库',
                    'children' => [
                        0 => [
                            'id' => 32,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'warehouse',
                        ],
                        1 => [
                            'id' => 33,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'warehouse',
                        ],
                        2 => [
                            'id' => 34,
                            'level' => 3,
                            'label' => '导入 / 导出商品',
                            'type' => 2,
                            'router_name' => 'warehouse',
                        ],
                    ],
                ],
                2 => [
                    'id' => 14,
                    'level' => 2,
                    'label' => '创建新商品',
                    'router_name' => 'create-goods',
                ],
            ],
        ],
        4 => [
            'id' => 5,
            'level' => 1,
            'label' => '订单管理',
            'children' => [
                0 => [
                    'id' => 15,
                    'level' => 2,
                    'label' => '我的订单',
                    'children' => [
                        0 => [
                            'id' => 35,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'setting-order-list',
                        ],
                        1 => [
                            'id' => 36,
                            'level' => 3,
                            'label' => '发货管理',
                            'type' => 3,
                            'router_name' => 'setting-order-list',
                        ],
                        2 => [
                            'id' => 37,
                            'level' => 3,
                            'label' => '导入 / 导出',
                            'type' => 2,
                            'router_name' => 'setting-order-list',
                        ],
                    ],
                ],
                1 => [
                    'id' => 16,
                    'level' => 2,
                    'label' => '我的采购单',
                    'children' => [
                        0 => [
                            'id' => 38,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'order-list',
                        ],
                        1 => [
                            'id' => 39,
                            'level' => 3,
                            'label' => '发货管理',
                            'type' => 3,
                            'router_name' => 'order-list',
                        ],
                        2 => [
                            'id' => 40,
                            'level' => 3,
                            'label' => '导入 / 导出',
                            'type' => 2,
                            'router_name' => 'order-list',
                        ],
                    ],
                ],
                2 => [
                    'id' => 17,
                    'level' => 2,
                    'label' => '退款管理',
                    'children' => [
                        0 => [
                            'id' => 41,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'refund',
                        ],
                    ],
                ],
            ],
        ],
        5 => [
            'id' => 6,
            'level' => 1,
            'label' => '营销中心',
            'children' => [
                0 => [
                    'id' => 18,
                    'level' => 2,
                    'label' => '模版管理-轮播',
                    'children' => [
                        0 => [
                            'id' => 42,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'banner-list',
                        ],
                        1 => [
                            'id' => 43,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'banner-list',
                        ],
                    ],
                ],
                1 => [
                    'id' => 19,
                    'level' => 2,
                    'label' => '模版管理-活动',
                    'children' => [
                        0 => [
                            'id' => 44,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'activity-config',
                        ],
                        1 => [
                            'id' => 45,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'activity-config',
                        ],
                    ],
                ],
                2 => [
                    'id' => 20,
                    'level' => 2,
                    'label' => '活动管理',
                    'children' => [
                        0 => [
                            'id' => 46,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'activity-manager',
                        ],
                        1 => [
                            'id' => 47,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'activity-manager',
                        ],
                    ],
                ],
                3 => [
                    'id' => 21,
                    'level' => 2,
                    'label' => '资源管理',
                    'children' => [
                        0 => [
                            'id' => 48,
                            'level' => 3,
                            'label' => '查询',
                            'type' => 0,
                            'router_name' => 'resource-list',
                        ],
                        1 => [
                            'id' => 49,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'resource-list',
                        ],
                    ],
                ],
                4 => [
                    'id' => 22,
                    'level' => 2,
                    'label' => '推送',
                    'children' => [
                        0 => [
                            'id' => 50,
                            'level' => 3,
                            'label' => '推送',
                            'type' => 4,
                            'router_name' => 'pusher',
                        ],
                        1 => [
                            'id' => 51,
                            'level' => 3,
                            'label' => '短信',
                            'type' => 5,
                            'router_name' => 'pusher',
                        ],
                    ],
                ],
            ],
        ],
        6 => [
            'id' => 7,
            'level' => 1,
            'label' => '资金管理',
            'children' => [
                0 => [
                    'id' => 23,
                    'level' => 2,
                    'label' => '结算单管理',
                    'children' => [
                        0 => [
                            'id' => 52,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'funds',
                        ],
                    ],
                ],
            ],
        ],
        7 => [
            'id' => 8,
            'level' => 1,
            'label' => '设置',
            'children' => [
                0 => [
                    'id' => 24,
                    'level' => 2,
                    'label' => '子账号管理',
                    'children' => [
                        0 => [
                            'id' => 53,
                            'level' => 3,
                            'label' => '编辑',
                            'type' => 1,
                            'router_name' => 'account-list',
                        ],
                    ],
                ],
            ],
        ],
        8 => [
            'id' => 9,
            'level' => 1,
            'label' => '客服中心',
            'router_name' => 'service',
        ],
    ];

    public function __construct()
    {
        // 从session中获取登陆信息
        $this->middleware(function ($request, $next) {
            self::$user_no = $request->session()->get('user_no');
            self::$login_token = $request->session()->get('access_token');
            self::$user_info = $this->getUserInfo(self::$user_no) ?: [];
            self::$left_menu = $this->getCachedLeftMenu(self::$user_no) ?: [];

            // 环信id===>开发、线上环境的环信ID不同
            self::$service_id = get_service_id();

            return $next($request);
        });

        self::$client_ip = get_client_ip();
    }

    public function getUserInfo($user_no)
    {
        if (empty($user_no)) {
            return [];
        }

        $userinfo = $this->getCachedUserInfo($user_no);

        if (! empty($userinfo)) {
            return $userinfo;
        }

        // 缓存过期，则从接口获取账号信息
        $response = $this->getUserDetail($user_no);

        if (0 === $response['code']) {
            if (isset($response['data']['user_detail']['status']) && 1 === $response['data']['user_detail']['status']) {
                $this->updateStorage($response['data']['user_detail']);

                $data = $response['data']['user_detail'];
                $data['token'] = Session::get($user_no.'_token');

                return $data;
            }
            $this->clearStorage();
        }

        return [];
    }

    /**
     * 获取缓存数据.
     * @return array|mixed
     */
    public function getCachedUserInfo($user_no)
    {
        return Cache::get($user_no.'_user_info');
    }

    /**
     * 获取缓存左菜单.
     * @return mixed
     */
    public function getCachedLeftMenu($user_no)
    {
        return Cache::get($user_no.'_left_menu');
    }

    public function checkIpFrequencyRequest($max_count = 10)
    {
        $ip = self::$client_ip ?: 'unknown';
        $cache_key = 'wms_request_ip_'.$ip;

        $ip_count = Cache::get($cache_key, 1);

        if ($ip_count > $max_count) {
            return false;
        }

        ++$ip_count;
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $start = time();
        $end = mktime(23, 59, 59, $month, $day, $year);
        $remain = $end - $start;

        if ($remain < 1) {
            $ip_count = 0;
        }

        $minutes = intval($remain / 60); // 当天剩余分钟数
        $minutes = $minutes < 1 ? 1 : $minutes;

        Cache::put($cache_key, $ip_count, $minutes);

        return true;
    }

    protected function getUserDetail($user_no)
    {
        $params = [
            'user_no' => $user_no,
        ];

        $url = '/user/getUserDetail';
        $response = $this->get($url, $params);

        return $response;
    }

    protected function updateStorage($data)
    {
        $user_no = $data['user_no'];
        Cache::put($user_no.'_user_info', $data, 2);

        // 子账号，需要获取权限列表
        if (self::SUB_ACCOUNT_TYPE === $data['account_type']) {
            $permission_list = $data['permission'];
            $router_list = $this->getRouterList($permission_list);
            $left_menu = $this->getLeftMenu($router_list);
            Cache::put($user_no.'_left_menu', $left_menu, 2);
        }
    }

    protected function clearStorage()
    {
        $user_no = Session::get('user_no');

        // Session::flush() 重新生成了session cookie，在没有刷新页面情况下会导致后续的请求csrf验证失败
        // Session::flush();
        Session::forget([
            $user_no.'_token',
            'user_no',
            'access_token',
            'account_type',
        ]);

        Cache::forget($user_no.'_user_info');
        Cache::forget($user_no.'_left_menu');
    }

    /**
     * 统一封装API请求
     */
    protected function get($url, $data = [], $headers = [], $options = [])
    {
        return $this->request('GET', $url, $data, $headers, $options);
    }

    protected function post($url, $data = [], $headers = [], $options = [])
    {
        return $this->request('POST', $url, $data, $headers, $options);
    }

    protected function request($method, $url, $data = [], $headers = [], $options = [])
    {
        $method = strtoupper($method);
        $url = api_url($url);

        // GET参数处理
        $query = parse_url($url, PHP_URL_QUERY) ?: '';
        parse_str($query, $query_arr);
        $query_arr['login_uid'] = self::$user_no;
        $query_arr['source'] = self::API_SOURCE;
        $query_arr['ip'] = self::$client_ip;

        // 键值对, 值为null的参数, 不能参与签名
        foreach ($query_arr as $key => $val) {
            if (! isset($val)) {
                unset($query_arr[$key]);
            }
        }

        foreach ($data as $key => $val) {
            if (! isset($val)) {
                unset($data[$key]);
            }
        }

        $nounce = uniqid();
        $query_arr['nounce'] = $nounce;
        $data['nounce'] = $nounce;

        if ('GET' === $method) {
            $query_arr = array_merge($data, $query_arr);
            $query_arr['sign'] = $this->generateSignature($query_arr);
        } elseif ('POST' === $method) {
            $query_arr['sign'] = $this->generateSignature($query_arr);
            $data['sign'] = $this->generateSignature($data);
        }

        $length = strpos($url, '?') ?: strlen($url);
        $url = substr($url, 0, $length);
        $url = $url.'?'.http_build_query($query_arr);

        if ('POST' === $method) {
            $headers = array_merge([
                'Content-Type' => 'application/json; charset=utf-8',
            ], $headers);
        }

        $options = array_merge([
            'timeout' => 30,
        ], $options);

        // API响应json格式(失败状态)
        $result = [
            'code' => 400,
            'msg' => '',
        ];

        $client = new Client();
        $flag = true;

        // seaslog模板
        $seaslog_message = 'UserID: {userId}; UserName: {userName}; RunTime: {runTime}; Method: {method}; '
            .'API: {api}; Data: {data}; Status: {status}';
        $seaslog_content = [
            '{userId}' => self::$user_info['user_no'] ?? ' ',
            '{userName}' => self::$user_info['realname'] ?? ' ',
            '{runTime}' => '0ms',
            '{method}' => $method,
            '{api}' => $url,
            '{data}' => ' ',
            '{status}' => 'normal',
        ];

        try {
            $start_time = get_millisecond_timestamp();

            if ('GET' === $method) {
                $data = array_merge($data, $query_arr);
                $response = $client->get($url, array_merge($options, [
                    'query' => $data,
                ]));
            } elseif ('POST' === $method) {
                $post_data = json_encode($data);
                $response = $client->post($url, array_merge($options, [
                    'body' => $post_data,
                    'headers' => $headers,
                ]));

                $seaslog_content['{data}'] = $post_data;
            } else {
                die('invalid request method');
            }

            $run_time = get_millisecond_timestamp() - $start_time;
            $seaslog_content['{runTime}'] = $run_time.'ms';
            $status = $response->getStatusCode();

            if ($status !== 200) {
                $flag = false;
                $seaslog_content['{status}'] = 'statusCode: '.$status. ', body: '.
                    substr($response->getBody(), 0, 100);
                $result['msg'] = '数据错误, 请稍后重试';
            } else {
                $body = $response->getBody();
                $result = json_decode($body, true);
            }
        } catch (ClientException $e) {
            $result['code'] = $e->getCode() ?: 500;
            $result['msg'] = $e->getMessage();

            $flag = false;
            $seaslog_content['{status}'] = 'exceptionCode: '.$e->getCode().', exceptionMessage: '.
                substr($e->getMessage(), 0, 100);
        }

        seas_log($flag ? 'info' : 'error', $seaslog_message, $seaslog_content);

        return $result;
    }

    /**
     * 统一封装.
     *
     * 主要针对特殊符号和emoji表情
     * 1. 转义 不保留
     * 2. 转义 保留unicode输出
     */
    protected function userTextEncode($str)
    {
        if (! is_string($str)) {
            return $str;
        }

        if (! $str || 'undefined' === $str) {
            return '';
        }

        //暴露出unicode
        $text = json_encode($str);

        // 不保留
        $text = preg_replace("/(\\\u[ed][0-9a-f]{3})/i", '', $text);

        return json_decode($text);
    }

    private function getPermitRouter($permission_tree, $permission_id)
    {
        $router = [];

        foreach ($permission_tree as $item) {
            if ($permission_id === $item['id'] && isset($item['router_name'])) {
                $router = [
                    'router_name' => $item['router_name'],
                ];

                if (isset($item['type'])) {
                    $router['type'] = $item['type'];
                }

                return $router;
            }

            if (isset($item['children'])) {
                $router = $this->getPermitRouter($item['children'], $permission_id);

                if (! empty($router)) {
                    return $router;
                }
            }
        }

        return $router;
    }

    /**
     * 获取路由列表即对应的操作类型
     * 如：$router_list = [
     *     'selling-goods' => [0, 1, 2],
     *     'funds-list' => [0],
     *    ].
     */
    private function getRouterList($permission_list)
    {
        if (! is_array($permission_list) || empty($permission_list)) {
            return [];
        }

        $router_list = [];

        foreach ($permission_list as $item) {
            $router = $this->getPermitRouter(self::$permission_tree, $item);

            if (! empty($router)) {
                if (! isset($router_list[$router['router_name']])) {
                    $router_list[$router['router_name']] = [];
                }

                $router_list[$router['router_name']][] = isset($router['type']) ? $router['type'] : '';
            }
        }

        return $router_list;
    }

    /**
     * 将路由列表转换为左菜单所需结构，如：
     * $left_menu = [
     *   0 => [
     *     'router_name' => 'selling-goods',
     *     'operator_types' => [0,1,2],
     *   ],
     *   1 => [
     *     'router_name' => 'funds-list',
     *     'operator_types' => [0],
     *   ],
     * ].
     */
    private function getLeftMenu($router_list)
    {
        $left_menu = [];

        foreach ($router_list as $key => $value) {
            $left_menu[] = [
                'router_name' => $key,
                'operator_types' => $value,
            ];
        }

        return $left_menu;
    }

    // 签名
    private function generateSignature($params)
    {
        ksort($params);
        $item = [];

        foreach ($params as $k => $v) {
            if (is_array($v)) {
                $v = stripslashes(json_encode($v, JSON_UNESCAPED_UNICODE));
            }

            if (is_bool($v)) {
                $v = intval($v);
            }

            $item[] = "${k}=${v}";
        }

        $str = implode('&', $item).self::$login_token.'ofashion'.$params['nounce'];

        $sign = md5($str);

        return $sign;
    }
}
