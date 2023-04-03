<?php
$plain = $_GET['plain'] ?? "0";
if ($plain !== "1") {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <style type="text/css">
            @font-face {
                font-family: "Source Code Pro";
                src: url("https://fonts.gstatic.com/s/sourcecodepro/v15/HI_SiYsKILh3Ug7L8hQfeg.ttf");
            }

            body {
                font-family: "Source Code Pro", monospace;
            }
        </style>
    </head>

    <body>

        <?php
}
$file = fopen("data/tailscale_status.txt", "r") or die("Unable to open file!");
$content = fread($file, filesize("data/tailscale_status.txt"));
fclose($file);

$content = str_replace(' ', '&nbsp;', $content); // 将空格替换为 &nbsp;

echo nl2br($content);
?>
    <?php
    if ($plain !== "1") {
        ?>
    </body>

    </html>
    <?php
    }
