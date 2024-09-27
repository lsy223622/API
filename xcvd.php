<?php
header('Content-Type: application/json');

// 设置数据目录路径
$data_dir = __DIR__ . '/data';
if (!file_exists($data_dir)) {
    mkdir($data_dir, 0755, true);
}

// 读取配置文件
$config_file = $data_dir . '/xcvd_config.ini';
if (!file_exists($config_file)) {
    // 如果配置文件不存在，创建默认配置
    $default_config = "latest_version = 0.0.0\nmessage = 欢迎使用 XDUClassVideoDownloader！如有问题请联系作者。";
    file_put_contents($config_file, $default_config);
}

$config = parse_ini_file($config_file);

// 获取最新版本号和提示信息
$latest_version = $config['latest_version'] ?? '0.0.0';
$message = $config['message'] ?? '欢迎使用 XDUClassVideoDownloader！如有问题请联系作者。';

// 获取客户端版本号
$client_version = $_GET['version'] ?? 'unknown';

// 准备响应数据
$response = [
    'latest_version' => $latest_version,
    'message' => $message
];

// 记录日志
$log_file = $data_dir . '/xcvd.log';
$log_message = date('Y-m-d H:i:s') . " - Client Version: " . $client_version . "\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

// 输出JSON响应
echo json_encode($response);