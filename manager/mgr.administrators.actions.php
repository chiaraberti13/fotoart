<?php
	###################################################################
	####	MANAGER ADMIN ACTIONS                                  ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 10-24-2006                                    ####
	####	Modified: 1-19-2008                                    #### 
	###################################################################
		
	# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');
	
		$page = "administrators";
		
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
		
		$username = $_GET['username'];
		$password = $_GET['password'];
		
		# MAKE SURE THE USERNAME HASN'T ALREADY BEEN TAKEN
		if($_GET['edittype'] == "new"){
			$admin_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(admin_id) FROM {$dbinfo[pre]}admins WHERE username = '$username'"));
		} else {
			$admin_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(admin_id) FROM {$dbinfo[pre]}admins WHERE username = '$username' AND uadmin_id != '$_GET[saveid]'"));
		}
		
		# OUTPUT ERROR IF USERNAME IS ALREADY TAKEN
		if(!empty($admin_rows)){
			send_error_back("1",$mgrlang['admin_mes_14'],"username_div","username");
			exit;
		}
		
		# CHECK TO MAKE SURE EMAIL IS VALID
		if (!eregi("^([[:alnum:]]|_|\\.|-)+@([[:alnum:]]|\\.|-)+(\\.)([a-z]{2,10})$", $_GET['email'])) {
			send_error_back("1",$mgrlang['admin_mes_11'],"email_div","email");
			exit;
			
		}
		
		# CHECK USERNAME FOR SPACES
		if(preg_match("/\W+/", $username)){
			send_error_back("1",$mgrlang['admin_mes_12'],"username_div","username");
			exit;
		}
		
		# CHECK FOR SPACES IN PASSWORD
		if(strpos($password," ")){
			send_error_back("1",$mgrlang['admin_mes_13'],"password_div","password");
			exit;
		}
		
		# NO ERRORS - SUBMIT FORM
?>
<script language="javascript">submit_form();</script>