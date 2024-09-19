<?php

// 导入数据库凭证
require_once 'db_credentials.php';

// 连接数据库
$db = new mysqli('localhost', DB_USER, DB_PASS, DB_NAME);

// 检查连接是否成功
if ($db->connect_error) {
    die("连接失败: " . $db->connect_error);
}

// 获取当前时间
$now = new DateTime();

// 设置时区为 UTC+8
$now->setTimezone(new DateTimeZone('Asia/Shanghai'));

// 初始化空数组用于保存数据
$data = array();

// 获取数据库中所有行数据
$sql = "SELECT * FROM class_time ORDER BY id ASC";
$result = $db->query($sql);

// 删除已经结束的课程，并保存剩余的课程数据
while ($row = $result->fetch_assoc()) {
    $end_time = new DateTime($row['end_time']);

    if ($now <= $end_time) {
        $data[] = $row;
    } else {
        $sql = "DELETE FROM class_time WHERE id = " . $row['id'];
        $db->query($sql);
    }
}

// 输出 CSV 格式的课程信息

// 设置响应头
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="classtimetable.csv"');

// 打开输出流
$output = fopen('php://output', 'w');

// 输出每一行数据
foreach ($data as $row) {
    fputcsv($output, $row);
}

// 添加额外的行
$extraRow = array(9999, "2023-01-01 00:00:00", "2030-01-01 00:00:00", "没课啦", "NULL");
fputcsv($output, $extraRow);

// 关闭输出流
fclose($output);

$db->close();