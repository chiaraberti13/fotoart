<?php
	$current_version = $_GET['productVersion'];
	header("Content-type: image/jpeg");
	$src_img = imagecreatefrompng('images/mgr.version.bg.blue.png');
	$dst_img = imagecreatetruecolor('91','20');
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, '91', '20', '91', '20');
	$textcolor = imagecolorallocate($dst_img, 209, 214, 224);
	//$bgcolor = imagecolorallocate($dst_img, 255, 255, 255);
	//imagefill($dst_img, 0, 0, $bgcolor);
	imagestring($dst_img, 4, 16, 1, $current_version, $textcolor);
	imagejpeg($dst_img,'','95');
	imagedestroy($src_img);
	imagedestroy($dst_img);
?>