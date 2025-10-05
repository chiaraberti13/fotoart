<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "software_setup";
		
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
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE	
		include_lang();
		
		# INCLUDE MANAGER ADDONS FILE
		require_once('../assets/includes/addons.php');
	
	echo dirname(curPageURL());
?>