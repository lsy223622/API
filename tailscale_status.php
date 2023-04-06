<?php
$file = fopen("data/tailscale_status.txt", "r") or die("Unable to open file!");
$content = fread($file, filesize("data/tailscale_status.txt"));
fclose($file);

// 拆分每行数据为数组
$lines = explode("\n", $content);

// 遍历数组，将每行数据拆分为 key-value 对
$data = array();
foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line)) {
        $cols = preg_split('/\s+/', $line, 5);
        $status = isset($cols[4]) ? $cols[4] : '';
        if ($status === "-") {
            $status = "online";
        }
        $data[] = array(
            'ip' => $cols[0],
            'name' => $cols[1],
            'os' => $cols[2],
            'status' => $status
        );
    }
}

// 转换为 JSON 格式输出
header('Content-Type: application/json');
echo json_encode($data);