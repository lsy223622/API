<?php
$file = fopen("data/tailscale_status.txt", "r") or die("Unable to open file!");
echo fread($file,filesize("data/tailscale_status.txt"));
fclose($file);
