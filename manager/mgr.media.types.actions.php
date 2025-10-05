<?php
	###################################################################
	####	MEDIA TYPES ACTIONS                            		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "media_types";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			# SET ACTIVE STATUS
			default:
			case "ac":
				$mt_result = mysqli_query($db,"SELECT active,name FROM {$dbinfo[pre]}media_types where mt_id = '$_REQUEST[id]'");
				$mt = mysqli_fetch_object($mt_result);
				
				# FLIP THE VALUE
				$new_value = (empty($mt->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}media_types SET active='$new_value' WHERE mt_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				$save_type = ($new_value==1) ? $mgrlang['gen_active'] : $mgrlang['gen_inactive'];
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_types'],1,$save_type . " > <strong>$mt->name ($_REQUEST[id])</strong>");

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
		}	
?>
