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

// 设置 $finish 为 false，用于循环
$finish = false;

// 初始化空数组用于保存数据
$data = array();

// 循环遍历数据库中的课程
while ($finish === false) {
    $finish = true;

    // 获取数据库中所有行数据
    $sql = "SELECT * FROM class_time ORDER BY id ASC";

    // 保存到 $data 数组中
    while ($row = $db->query($sql)->fetch_assoc()) {
        $data[] = $row;
    }

    // 删除已经结束的课程
    foreach ($data as $key => $row) {
        $end_time = new DateTime($row['end_time']);

        if ($now > $end_time) {
            $sql = "DELETE FROM class_time WHERE id = " . $row['id'];
            $db->query($sql);
            unset($data[$key]);
        }
    }

    // 如果还有剩余的课程，继续循环
    if (!empty($data)) {
        $finish = false;
    }
}

// 输出 CSV 格式的课程信息
if (!empty($data)) {
    // 设置响应头，指定以附件形式下载 CSV 文件
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="classtimetable.csv"');

    // 打开输出流
    $output = fopen('php://output', 'w');

    // 输出 CSV 标头
    fputcsv($output, array_keys($data[0]));

    // 输出每一行数据
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // 关闭输出流
    fclose($output);
}

$db->close();