<?php
	
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	//echo md5($config['settings']['serial_number']); exit;
	
	if($_REQUEST['pass'] != md5($config['settings']['serial_number']))
	{
		echo "You do not have access to view this file.";
		exit;
	}
	
	require_once('../assets/classes/mediatools.php');				# MEDIATOOLS CLASS	
	require_once('../assets/includes/clean.data.php');				# CLEAN DATA
	
	try
	{
		$media = new mediaTools($mediaID);
		$mediaInfo = $media->getMediaInfoFromDB();					
		$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
		
		$useFolderName = ($folderInfo['encrypted']) ? $folderInfo['enc_name'] : $folderInfo['name']; // Check if it is encrypted or not
		
		// Check if the video sample file exists
		if($vidSampleInfo = $media->getVidSampleInfoFromDB())
		{
			$vidSampleVerify = $media->verifyVidSampleExists();
		}
		else
		{
			$vidSampleVerify['status'] = 0;	
		}
	}
	catch(Exception $e)
	{
		echo "<span style='color: #EEE'>" . $e->getMessage() . "</span>";	
	}
	
	$file = "{$config[settings][library_path]}/{$useFolderName}/samples/{$vidSampleInfo[vidsample_filename]}";
	
	/*
	jpg	image/jpeg
	gif	image/gif
	png	image/png
	mid	audio/midi
	amr	audio/amr
	mmf	application/vnd.smaf
	mp3	audio/mpeg
	qcp	audio/vnd.qcelp
	jad	text/vnd.sun.j2me.app-descriptor
	jar	application/java-archive
	3gp	video/3gpp
	3g2	video/3gpp2
	*/
	
	switch(filenameExt($file))
	{
		default: 
		case "mp4":
			header('Content-Type: video/mp4');
		break;
		case "flv":
		case "f4v":
			header('Content-Type: video/x-flv');
		break;
		case "mov":
			header('Content-Type: video/mov');
		break;
	}
	
		
	if(isset($_SERVER['HTTP_RANGE']))
	{ // do it for any device that supports byte-ranges not only iPhone
		rangeDownload($file);
	}
	else
	{
 		header("Content-Length: ".filesize($file));
		readfile($file);
	}
	
	function rangeDownload($file)
	{
		$fp = @fopen($file, 'rb');
		$size   = filesize($file); // File size
		$length = $size;           // Content length
		$start  = 0;               // Start byte
		$end    = $size - 1;       // End byte
	
		header("Accept-Ranges: 0-$length");
		
		if(isset($_SERVER['HTTP_RANGE']))
		{
			$c_start = $start;
			$c_end   = $end;
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			if (strpos($range, ',') !== false)
			{
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}
			if ($range0 == '-')
			{
				$c_start = $size - substr($range, 1);
			}
			else
			{
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
		
			$c_end = ($c_end > $end) ? $end : $c_end;
			if($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
			{
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				exit;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1; // Calculate new content length
			fseek($fp, $start);
			header('HTTP/1.1 206 Partial Content');
		}
		header("Content-Range: bytes $start-$end/$size");
		header("Content-Length: $length");
 
		// Start buffered download
		$buffer = 1024 * 8;
		while(!feof($fp) && ($p = ftell($fp)) <= $end)
		{
 			if($p + $buffer > $end)
			{
				$buffer = $end - $p + 1;
			}
			if(function_exists('set_time_limit')) set_time_limit(0); // Reset time limit for big files
			echo fread($fp, $buffer);
			flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
		}
		fclose($fp);
 	}
	
	/*
	$ctype = "video/mp4";
	header("Content-Type: {$ctype}");
	header("Accept-Ranges:0-{$videoFilesize}");
	header("Connection:Keep-Alive");
	
	
	header("Pragma: no-cache");
	header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");	
	header("Content-Description: File Transfer");
	header("Content-Location:\"{$file}\"");
	header("Content-Disposition: attachment; filename=\"{$vidSampleInfo[vidsample_filename]}\";");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($file));
	*/
	@readfile($file);
?>