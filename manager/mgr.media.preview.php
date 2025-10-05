<?php
	###################################################################
	####	THUMBS			      	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-18-2010                                    ####
	####	Modified: 11-18-2010                                   #### 
	###################################################################
	
	require_once('../assets/includes/session.php');	# INCLUDE THE SESSION START FILE
	include('../assets/includes/tweak.php'); # INCLUDE TWEAK FILE

	# GET FOLDER ID, MEDIA ID, SRC
	$folder_id = $_GET['folder_id'];
	$media_id = $_GET['media_id'];
	$src = $_GET['src'];
	$type = ($_GET['type']) ? $_GET['type'] : 'icons';
	$icon_width = ($_GET['width'])? $_GET['width'] : $config['MediaIconPreviewSize'];
	
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	$mgrBasePath = dirname(__FILE__); //$_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/";
	$basePath = dirname(dirname(__FILE__)); //$_SERVER['DOCUMENT_ROOT'] . dirname(dirname($_SERVER['PHP_SELF']));
	
	/*
	* Caching of images
	*/
	$cacheFile = "id{$media_id}-".md5("mgr-{$media_id}-{$folder_id}-{$type}-{$src}-{$icon_width}").'.jpg'; // Name of cached file - added mgr to make sure it is always specific to the management area
	$cachePathFile = $basePath."/assets/cache/{$cacheFile}";
	if(file_exists($cachePathFile))
	{	
		if($config['cacheImages']) // Check for debug mode
		{
			$cacheTime = gmdate("U")-$config['cacheImagesTime'];
			$fileTime = filemtime($cachePathFile);
			
			if($cacheTime < $fileTime)
			{	
				header("Content-type: image/jpeg");
				//header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($thumbnail)) . ' GMT');
				
				ob_clean();
    			flush();
				
				readfile($cachePathFile);
				exit;
			}
			else // Cleanup old cached file
				@unlink($cachePathFile);
		}
	}
	
	$page = "media";
	
	# KEEP THE PAGE FROM CACHING
	//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	//error_reporting(0);
	
	require_once('mgr.security.php'); # INCLUDE SECURITY CHECK FILE // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
	require_once('mgr.config.php'); # INCLUDE MANAGER CONFIG FILE
	if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; } # INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php'); # INCLUDE SHARED FUNCTIONS FILE
	require_once('../assets/includes/db.conn.php'); # INCLUDE DATABASE CONNECTION FILE
	require_once '../assets/classes/imagetools.php';
	require_once('mgr.functions.php'); # INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.select.settings.php'); # SELECT THE SETTINGS DATABASE
	include('../assets/includes/tweak.php'); # INCLUDE TWEAK FILE - Had to include this because it gets cleared out with the inclusion of the mgr.config file
	
	$quality = $config['settings']['thumb_quality'];
	$sharpen = $config['settings']['thumb_sharpen'];
	//$watermark = $_GET['watermark'];
	
	# GET FOLDER INFO
	$folder_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}folders WHERE folder_id = '$folder_id'");
	$folder_rows = mysqli_num_rows($folder_result);
	$folder = mysqli_fetch_object($folder_result);
	
	if($folder->encrypted)
	{
		$folder_name = $folder->enc_name;
	}
	else
	{
		$folder_name = $folder->name;	
	}
	
	try
	{
		$previewThumb = new imagetools($src = $config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $src);
		$previewThumb->setQuality($quality);
		$previewThumb->setSize($icon_width);
		$previewThumb->setCrop(0);
		$previewThumb->setSharpen($sharpen);
		//$previewThumb->createImage(1,'');
		
		if($config['cacheImages'] == 0)
			$previewThumb->createImage(1,''); // Do not cache
		else
			$previewThumb->createImage(1,$cachePathFile); // Cache
	}
	catch(Exception $e)
	{	
		echo $e->getMessage();			
	}
?>