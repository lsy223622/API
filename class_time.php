<?php

// 加载 Sabre\VObject 库
require_once 'vendor/autoload.php';

// 读取 iCalendar 文件
$ical = file_get_contents('data/current.ics');

// 获取当前时间
$now = new DateTime();

// 设置时区为UTC+8
$timezone = new DateTimeZone('Asia/Shanghai');
$now->setTimezone($timezone);

// 使用 Reader 类读取 iCalendar 数据
$reader = new Sabre\VObject\Reader();
$vcalendar = $reader->read($ical);

// 获取所有课程
$vevents = $vcalendar->VEVENT;

// 初始化数据
$data = [];

// 遍历所有课程，检查是否有课程与当前时间匹配
foreach ($vevents as $vevent) {
  $dtstart = new DateTime($vevent->DTSTART);
  $dtend = new DateTime($vevent->DTEND);
  $dtstart->setTimezone($timezone); // 设置时区为UTC+8
  $dtend->setTimezone($timezone); // 设置时区为UTC+8
  if ($now >= $dtstart && $now <= $dtend) {
    // 当前有课程
    $data['summary'] = (string)$vevent->SUMMARY;
    $data['start_time'] = $dtstart->format('Y-m-d H:i:s');
    $data['end_time'] = $dtend->format('Y-m-d H:i:s');
    break;
  }
}

if (!isset($data['summary'])) {
  // 当前没有课程
  $prev_end_time = null;
  $next_start_time = null;

  foreach ($vevents as $vevent) {
    $dtstart = new DateTime($vevent->DTSTART);
    $dtend = new DateTime($vevent->DTEND);
    $dtstart->setTimezone($timezone); // 设置时区为UTC+8
    $dtend->setTimezone($timezone); // 设置时区为UTC+8
    if ($now < $dtstart) {
      $next_start_time = $dtstart->format('Y-m-d H:i:s');
      break;
    }
    $prev_end_time = $dtend->format('Y-m-d H:i:s');
  }

  $data['summary'] = '0';
  $data['start_time'] = $prev_end_time;
  $data['end_time'] = $next_start_time;
}

// 输出JSON
header('Content-Type: application/json');
echo json_encode($data);
