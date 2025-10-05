<?php
	###################################################################
	####	MANAGER REGIONS ACTIONS                                ####
	####	Copyright © 2003-2007 Ktools.net. All Rights Reserved  ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-13-2006                                    ####
	####	Modified: 11-13-2006                                   #### 
	###################################################################
		//sleep(2);
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "regions";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
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
			# SET ACTIVE STATUS
			case "ac":
				$region_result = mysqli_query($db,"SELECT active,nest FROM {$dbinfo[pre]}regions where region_id = '$_REQUEST[id]'");
				$region = mysqli_fetch_object($region_result);
				
				# FLIP THE VALUE
				$new_value = (empty($region->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}regions SET active='$new_value' where region_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				if($new_value == 0 and $region->nest == 0){
					$sql = "UPDATE {$dbinfo[pre]}regions SET active='0' where nest = '$_REQUEST[id]'";
					$result = mysqli_query($db,$sql);
				}
				
				if($new_value == 1 and $region->nest != 0){
					$sql = "UPDATE {$dbinfo[pre]}regions SET active='1' where region_id = '$region->nest'";
					$result = mysqli_query($db,$sql);
				}
				
				if(!empty($region->nest)){
					echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]','$region->nest','$new_value','$_REQUEST[fid]');\"><img src=\"images/mgr.small.check." . $new_value . ".gif\" border=\"0\" /></a>";
				} else {
					echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]','0','$new_value','0');\"><img src=\"images/mgr.small.check." . $new_value . ".gif\" border=\"0\" /></a>";
				}
			break;
		}	
?>
