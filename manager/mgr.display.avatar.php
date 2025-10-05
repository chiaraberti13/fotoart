<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	# KEEP THE PAGE FROM CACHING
	//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	//sleep(1);
	
	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	
	//require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	//require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	//require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	//require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	//require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	//include_lang();												# INCLUDE THE LANGUAGE FILE		
	
	//require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
	require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE	
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON

	header("Content-type: image/png");
	$quality = $config['DisplayAvatarQuality'];

	$mem_id = $_GET['mem_id'];
	if($_GET['size']){
		$icon_width = $_GET['size'];
	} else {
		$icon_width = 70;
	}
								
	$src = $config['base_path']."/assets/avatars/" . $_GET['mem_id'] . "_large.png";
	$size = getimagesize($src);

	//FIND THE SCALE RATIOS		
	if($size[0] >= $size[1]){
		if($size[0] > $icon_width){
			$width = $icon_width;
		} else {
			$width = $size[0];
		}
		$ratio = $width/$size[0];
		$height = $size[1] * $ratio;				
	} else {
		if($size[1] > $icon_width){
			$height = $icon_width;	
		} else {
			$height = $size[1];	
		}
		$ratio = $height/$size[1];
		$width = $size[0] * $ratio;
	}
	
	/*
	switch($ext){
		case "jpeg":
		case "jpg":
			$src_img = imagecreatefromjpeg($src);
		break;
		case "gif":
			$src_img = imagecreatefromgif($src);
		break;
		case "png":
			$src_img = imagecreatefrompng($src);
		break;
	}
	*/
	$src_img = imagecreatefrompng($src);
	$dst_img = imagecreatetruecolor($width, $height);
	
	# KEEP TRANSPARENCY
	imagealphablending($dst_img, false);
	imagesavealpha($dst_img,true);
	$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
	imagefilledrectangle($dst_img, 0, 0, $width, $height, $transparent);
	# END KEEP TRANSPARENCY
	
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, imagesx($src_img), imagesy($src_img));
		
	# OUTPUT AND DESTROY
	//imagejpeg($dst_img,$config['settings']['incoming_path'] . "web/icn_" . $_GET['img'], $quality); // SAVE THIS ONE
	imagepng($dst_img,NULL, $quality); // DISPLAY THIS OUT
	
	imagedestroy($src_img); 
	imagedestroy($dst_img);
		
	/*
		sleep(1);
		$mem_needed = figure_memory_needed($config['settings']['incoming_path'] . "web/" . $_GET['file']);
		if(ini_get("memory_limit")){
			$memory_limit = ini_get("memory_limit");
		} else {
			$memory_limit = $config['DefaultMemory'];
		}
		if($memory_limit > $mem_needed){
			// GO FOR IT
			echo "<img src='mgr.add.files.actions.php?mode=previewimg&img=$_GET[file]' align='absmiddle' style='margin: 4px;' />";
		} else {
			include_lang();
			echo "<div style='padding: 10px; background-color: #ffffff; width: 150px;'>$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></div>";
		}			
	*/
?>