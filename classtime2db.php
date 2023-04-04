<?php

// 导入 Sabre\VObject 库
require_once 'vendor/autoload.php';

// 导入数据库凭证
require_once 'db_credentials.php';

// 连接数据库
$db = new mysqli('localhost', DB_USER, DB_PASS, DB_NAME);

// 检查连接是否成功
if ($db->connect_error) {
    die("连接失败: " . $db->connect_error);
}

// 读取 iCalendar 文件
$ical_data = file_get_contents('data/current.ics');

// 使用 Sabre\VObject 库解析 iCalendar 文件
$vcalendar = Sabre\VObject\Reader::read($ical_data);

// 创建一个数组来存储事件数据
$event_data = array();

// 循环遍历所有事件
foreach ($vcalendar->VEVENT as $vevent) {
    // 获取事件数据
    $start_time = strtotime($vevent->DTSTART->getValue());
    $end_time = strtotime($vevent->DTEND->getValue());
    $summary = explode(" @ ", $vevent->SUMMARY->getValue())[0];
    $location = $vevent->LOCATION->getValue();

    // 将事件数据存储到数组中
    $event_data[] = array(
        'start_time' => $start_time,
        'end_time' => $end_time,
        'summary' => $summary,
        'location' => $location
    );
}

// 按照开始时间对事件进行排序
usort($event_data, function ($a, $b) {
    return $a['start_time'] - $b['start_time'];
});

// 循环遍历已排序的事件数组，并将每个事件存储到数据库中
foreach ($event_data as $event) {
    $start_time = date('Y-m-d H:i:s', $event['start_time']);
    $end_time = date('Y-m-d H:i:s', $event['end_time']);
    $course = $event['summary'];
    $location = $vevent->LOCATION ? trim($vevent->LOCATION) : 'Unknown';

    $sql = "INSERT INTO events (start_time, end_time, course, location) VALUES ('$start_time', '$end_time', '$course', '$location')";

    // 执行 SQL 语句
    $result = $db->query($sql);

    // 检查是否执行成功
    if (!$result) {
        die("SQL 语句执行失败: " . $db->error);
    }
}

// 关闭数据库连接
$db->close();