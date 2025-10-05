<?php
	###################################################################
	####	WEBSITE SETTINGS ACTIONS                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 10-10-2008                                    ####
	####	Modified: 10-10-2008                                   #### 
	###################################################################
		
	# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');
	
		$page = "website_settings";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php');
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
		
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
		//require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		//require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		$sql="DELETE FROM {$dbinfo[pre]}lab_contacts WHERE lab_id = '$_GET[id]'";
		$result = mysqli_query($db,$sql);
?>