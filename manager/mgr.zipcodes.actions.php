<?php
	###################################################################
	####	MANAGER ZIPCODES ACTIONS                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 12-22-2009                                    ####
	####	Modified: 12-22-2009                                   #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "zipcodes";
		
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
		switch($_REQUEST['action'])
		{
			# SET ACTIVE STATUS
			case "ac":
				$zipcode_result = mysqli_query($db,"SELECT active FROM {$dbinfo[pre]}zipcodes where zipcode_id = '$_REQUEST[id]'");
				$zipcode = mysqli_fetch_object($zipcode_result);
				
				# FLIP THE VALUE
				$new_value = (empty($zipcode->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}zipcodes SET active='$new_value' where zipcode_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
		}	
?>
