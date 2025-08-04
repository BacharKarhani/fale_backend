<?php
header('Content-Type: image/png');
$image = imagecreatetruecolor(100, 100);
$bg = imagecolorallocate($image, 0, 255, 0);
imagefill($image, 0, 0, $bg);
imagepng($image);
imagedestroy($image);
?>
