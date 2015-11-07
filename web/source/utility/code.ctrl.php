<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$x = 120;
$y = 28;
$im = @imagecreatetruecolor($x, $y);
if(empty($im)) {
	exit();
}
$trans = imagecolorallocatealpha($im, 255, 255, 255 , 127);
imagecolortransparent($im, $trans);
imagefilledrectangle($im, 0, 0, $x, $y, $trans);
for($i = 0; $i < $x; $i++) {
	$p = 255 - $i;
	$line = imagecolorallocatealpha($im, $p, $p, $p, 0);
	imagefilledrectangle($im, $i, 0, $i + 1, $y, $line);
}
$letters = random(4, true);
$hash = md5($letters . $_W['config']['setting']['authkey']);
isetcookie('__code', $hash);
$fontColor = imagecolorallocatealpha($im, 40, 40, 40, 0);
$len = strlen($letters);
for($i = 0; $i < $len; $i++) {
	imagestring($im, 5, 35 + $i * 15, 3 + rand(0, 10), $letters[$i], $fontColor);
}
header('content-type: image/png');
imagepng($im);
imagedestroy($im);
