<?php
	/*
	$timeout = 3;
	$old = ini_set('default_socket_timeout', $timeout);
	// TURN OFF ERROR REPORTING JUST IN CASE
	error_reporting(0);
	
	// TRY TO LOAD THE FILE
	if(!$dataFile = fopen('http://www.ktools.net/images/ps_index_hero.gif', 'r')){
		echo "failed";
		exit;
	}
	
	// TURN BACK ON ERROR REPORTING
	error_reporting(E_ALL & ~E_NOTICE);
	
	ini_set('default_socket_timeout', $old);
	stream_set_timeout($dataFile, $timeout);
	stream_set_blocking($dataFile, 0);
	
	// READ DATA
	if($dataFile){
		while (!feof($dataFile)){
			$buffer.= fgets($dataFile, 4096);
		}
	}
	
	// OUTPUT CODE
	//echo $buffer;
	
	// CLOSE FILE	
	fclose($dataFile);
	
	// WRITE INFORMATION TO A FILE - $buffer is the info that was loaded
	
	$exportFile = "./testwrite/" . "test.gif";
	$fpOut = fopen( $exportFile, 'w' );
	fputs( $fpOut, $buffer );
	*/
	
	// DOWNLOAD FUNCTION... MAY WORK BETTER
	
	function download ($file_source, $file_target)
	{
	  // Preparations
	  $file_source = str_replace(' ', '%20', html_entity_decode($file_source)); // fix url format
	  if (file_exists($file_target)) { chmod($file_target, 0777); } // add write permission
	
	  // Begin transfer
	  if (($rh = fopen($file_source, 'rb')) === FALSE) { return false; } // fopen() handles
	  if (($wh = fopen($file_target, 'wb')) === FALSE) { return false; } // error messages.
	  while (!feof($rh))
	  {
		// unable to write to file, possibly because the harddrive has filled up
		if (fwrite($wh, fread($rh, 1024)) === FALSE) { fclose($rh); fclose($wh); return false; }
	  }
	
	  // Finished without errors
	  fclose($rh);
	  fclose($wh);
	  return true;
	}
	
	download("http://www.jonkent.com/music/IMG_0183.jpg","./testwrite/" . "test2.jpg");
	
?>