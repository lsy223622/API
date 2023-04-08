<?php
header('Content-Type: image/bmp');

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

// 循环遍历数据库中的课程
while ($finish === false) {
    $finish = true;

    // 获取数据库第一行数据保存到 $row 中
    $sql = "SELECT * FROM class_time ORDER BY id ASC LIMIT 1";

    // 保存到 $row 中
    $row = $db->query($sql)->fetch_assoc();

    // 获取课程开始时间
    $start_time = new DateTime($row['start_time']);

    // 获取课程结束时间
    $end_time = new DateTime($row['end_time']);

    // 如果课程已经结束就删除课程
    if ($now > $end_time) {
        $sql = "DELETE FROM class_time WHERE id = " . $row['id'];
        $db->query($sql);
        $finish = false;
        continue;
    }

    // 获取课程名称
    $course = $row['course'];

    // 获取上课地点
    $location = $row['location'];

    // 如果课程还没开始就把开始时间赋为 0
    if ($now < $start_time) {
        // 把开始时间赋给结束时间
        $end_time = $start_time;

        // 把开始时间赋为 0
        $start_time = new DateTime('2023-01-01 00:00:00');
    }
}

// 创建位图并设置背景为白色
$image = imagecreate(250, 122);
$white = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $white);

// 加载TrueType字体
$font16px = 'data/fhpixel16px.ttf';
$font12px = 'data/fhpixel12px.ttf';

// 计算文本在位图中的位置
$font16px_size = 12; // 字体大小
$font12px_size = 9; // 字体大小

// 处理文本
if ($start_time->format('Y-m-d H:i:s') == '2023-01-01 00:00:00') {
    $text1 = "下一节: " . $end_time->format('H:i');
} else {
    $text1 = $start_time->format('H:i') . " - " . $end_time->format('H:i');
}
if (mb_strlen($course, 'utf-8') > 14) {
    $course = mb_substr($course, 0, 14, 'utf-8') . '…';
}

// 写入文本
$black = imagecolorallocate($image, 0, 0, 0);
imagettftext($image, $font12px_size, 0, 3, 100, $black, $font12px, $text1);
imagettftext($image, $font16px_size, 0, 3, 118, $black, $font16px, $course);

// 输出位图
imagebmp($image);
imagedestroy($image);