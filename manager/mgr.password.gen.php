<?php
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
	require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE
	$pass = new genrandom;
	$pass->type = "spec";
	if($_GET){		
		$pass->override = 1;
		$pass->chars = $_GET['chars'];
		$pass->seedtype = $_GET['seedtype'];
		$pass->caset = $_GET['caset'];
	}
	echo $pass->generate();
?>

