<?php
	###################################################################
	####	MANAGER CLOCK PREVIEW                                  ####
	####	Copyright © 2003-2007 Ktools.net. All Rights Reserved  ####
	####	http://www.ktools.net                                  ####
	####	Created: 10-18-2006                                    ####
	####	Modified: 10-18-2006                                   #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		//error_reporting(0);
		
		# INCLUDE MANAGER CONFIG FILE
		//require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		include('mgr.select.settings.php');
		
		// INCLUDE SETTINGS FILE
		if(file_exists("../assets/languages/$config[settings][lang_file_mgr]/lang.settings.php")){
			include("../assets/languages/$config[settings][lang_file_mgr]/lang.settings.php");
		} else {
			include("../assets/languages/english/lang.settings.php");
		}
		// INCLUDE CALENDAR FILE
		if(file_exists("../assets/languages/$config[settings][lang_file_mgr]/lang.calendar.php")){
			include("../assets/languages/$config[settings][lang_file_mgr]/lang.calendar.php");
		} else {
			include("../assets/languages/english/lang.calendar.php");
		}
		
		if($_GET['daylight_savings'] == "true"){
			$daylight_savings = "1";
		} else {
			$daylight_savings = "0";
		}
		
		//echo $daylight_savings; exit;
		
		/*
		//if(!empty($_GET['adjby']) or !empty($_GET['adjby'])){
			# UPDATE THE DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings SET 
						time_zone='$_GET[time_zone]',
						daylight_savings='$daylight_savings',
						date_format='$_GET[date_format]',
						clock_format='$_GET[clock_format]',
						date_sep='$_GET[date_sep]'
						where settings_id  = '1'";
			$result = mysqli_query($db,$sql);
		//}
		*/
				
		# SELECT THE SETTINGS DATABASE
		include('mgr.select.settings.php');
		
		$ndate = new kdate;
		$ndate->time_zone = $_GET['time_zone'];
		$ndate->daylight_savings = $daylight_savings;
		$ndate->date_format = $_GET['date_format'];
		$ndate->date_display = $_GET['date_display'];
		$ndate->clock_format = $_GET['clock_format'];
		$ndate->date_sep = $_GET['date_sep'];
		$ndate->diswords = 0;
		$ndate->distime = 1;
		echo "<strong>" . $ndate->showdate(gmdate("Y-m-d H:i:s")) . "</strong>";
		
?>
