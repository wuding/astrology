<?php

use Astrology\Controller;

$cache_dir = CACHE_ROOT . '/http/zu.fang.com';
$api_host = 'http://' . $_SERVER['HTTP_HOST'];
$query_str = http_build_query(Controller::varArr(['type', 'debug']));

return [
    'var' => [
        'enable_relay' => true,
        'overwrite' => false,
        'min_size' => 1000,
        'site_id' => 1,
        'city_abbr' => 'mas',
        'api_host' => $api_host,
        'cache_dir' => $cache_dir,
        'paths' => [
            $cache_dir . '/cities.aspx.gz', //城市列表
            $cache_dir . '/%1/house/i3%2.gz', //出租列表pc 1城市 2页码
            $cache_dir . '/m/zf/%1/index.html', //出租首页 1城市
            $cache_dir . '/m/zf/%1/%3/%2.html', //出租详情 1城市 2ID 3类型
            $cache_dir . '/m/zf/%1/%2.html', //出租列表 1城市 2页码
        ],
        'urls' => [
            'http://zu.fang.com/cities.aspx',
            'http://%1.zu.fang.com/house/i3%2/',
            'https://m.fang.com/zf/%1/',
            'https://m.fang.com/zf/%1/%3_%2.html',
            'https://m.fang.com/zf/?renttype=cz&c=zf&a=ajaxGetList&city=%1&page=%2',
        ],
        'relay_urls' => [
            'parse/city' => "$api_host/robot/fang/parse/city?$query_str",
            'download/zf' => "$api_host/robot/fang/download/zf?$query_str",
            'download/list' => "$api_host/robot/fang/download/list?$query_str",
            'download/detail' => "$api_host/robot/fang/download/detail?$query_str",
        ],
    ],
    'append' => [
        '',
    ],
];
