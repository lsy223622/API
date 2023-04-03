<?php
$file = fopen("data/tailscale_status.txt", "r") or die("Unable to open file!");
$content = fread($file,filesize("data/tailscale_status.txt"));
fclose($file);

echo nl2br($content);
