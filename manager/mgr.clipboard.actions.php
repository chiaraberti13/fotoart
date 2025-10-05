<?php
	###################################################################
	####	CLIPBOARD ACTIONS                                      ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 4-17-2007                                     ####
	####	Modified: 4-17-2007                                    #### 
	###################################################################
		//sleep(1);
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "clipboard_actions";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		//require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		//require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		//require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		//require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		//require_once('mgr.select.settings.php');
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SET HOMEPAGE STATUS
			case "addcut":	
				$_SESSION['scut'][] = $_REQUEST['id'];
				/*
				$news_result = mysqli_query($db,"SELECT homepage FROM {$dbinfo[pre]}news where news_id = '$_REQUEST[id]'");
				$news = mysqli_fetch_object($news_result);
				
				# FLIP THE VALUE
				$new_value = (empty($news->homepage) ? 1 : 0);
							
				$sql = "UPDATE {$dbinfo[pre]}news SET homepage='$new_value' where news_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('hp','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".gif\" border=\"0\" /></a>";
				*/
				
				foreach($_SESSION['scut'] as $value){
					echo "$value,";
				}				
			break;
			# SET ACTIVE STATUS
			case "ac":
				$news_result = mysqli_query($db,"SELECT active FROM {$dbinfo[pre]}news where news_id = '$_REQUEST[id]'");
				$news = mysqli_fetch_object($news_result);
				
				# FLIP THE VALUE
				$new_value = (empty($news->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}news SET active='$new_value' where news_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".gif\" border=\"0\" /></a>";
			break;
		}	
?>
