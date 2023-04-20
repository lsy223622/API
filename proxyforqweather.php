<?php

$location = $_GET['location'];
$key = $_GET['key'];

// 如果没有有效参数，则返回错误信息
if (empty($location) || empty($key)) {
    echo '{"code":"400","msg":"Bad Request"}';
    exit;
}

$cache_time = 3 * 60; // 缓存有效时间，单位为秒
$cache_file = 'data/cached_qweather.json'; // 本地保存的JSON文件名

// 检查缓存文件是否存在且在有效期内
if (file_exists($cache_file) && time() - filemtime($cache_file) < $cache_time) {
    // 返回本地缓存的JSON文件内容
    $json_data = file_get_contents($cache_file);
} else {
    // 请求远程JSON文件并保存到本地
    $json_data = file_get_contents('compress.zlib://https://devapi.qweather.com/v7/weather/now?location=' . $location . '&key=' . $key . '&lang=zh&unit=m');
    file_put_contents($cache_file, $json_data);
}

// 返回JSON数据并记录访问时间
echo $json_data;