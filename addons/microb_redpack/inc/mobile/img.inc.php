<?php
$this_code = strval(rand(100000,999999));
session_start();
$_SESSION['checkIMGCode'] = $this_code;
$im = @imagecreate (100, 30) or die ("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate ($im, 0, 0, 0);
$text_color = imagecolorallocate ($im, 255, 255, 255);


$tmp_y = rand( -3, 10 );
imagestring ($im, 5, 5, 6+$tmp_y, $this_code[0], $text_color);
$tmp_y = rand( -3, 10 );
imagestring ($im, 5, 20, 4+$tmp_y, $this_code[1], $text_color);
$tmp_y = rand( -3, 10 );
imagestring ($im, 7, 40, 8+$tmp_y, $this_code[2], $text_color);
$tmp_y = rand( -3, 10 );
imagestring ($im, 5, 65, 7+$tmp_y, $this_code[3], $text_color);
$tmp_y = rand( -3, 10 );
imagestring ($im, 5, 78, 7+$tmp_y, $this_code[4], $text_color);
$tmp_y = rand( -3, 10 );
imagestring ($im, 5, 90, 7+$tmp_y, $this_code[5], $text_color);
for( $xi=300; $xi>=0; $xi-- )
{
	$rand1=rand(0,255);
	imagesetpixel ( $im, rand(0,100), rand(0,40), $text_color);
}
for( $i=25; $i>=0; $i-- )
{
	$x = rand(0, 100);  
	$y = rand(0, 100);  
	$x1 = rand(0, 100);  
	$y1 = rand(0, 100);  
	imageline ($im, $x, $y, $x1, $y1, imagecolorallocate($im, rand(0,255), rand(0,255), rand(0,255)));
}

imagepng ($im);
imagedestroy ($im);
?>