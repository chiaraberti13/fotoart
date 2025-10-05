<?php
	###################################################################
	####	MANAGER UNENCRYPT                                      ####
	####	Copyright © 2003-2009 Ktools.net. All Rights Reserved  ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-7-2007                                      ####
	####	Modified: 9-7-2007                                     #### 
	###################################################################
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "welcome";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE ENCRYPTION FILE
		//require_once('../assets/classes/encryption.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		//$crypt = new encryption_class;
		
		switch($_REQUEST['action']){
			# SET HOMEPAGE STATUS
			case "admin_pass":
				
				// SELECT PASS FROM ID
				
				$admin_result = mysqli_query($db,"SELECT password FROM {$dbinfo[pre]}admins WHERE uadmin_id = '$_GET[id]'");
				$admin = mysqli_fetch_object($admin_result);
				
				//$decrypt_result = $crypt->decrypt($config['settings']['serial_number'],$admin->password);
				//$errors = $crypt->errors;
				//echo $decrypt_result;
				
				$decrypt_result = k_decrypt($admin->password);
				
				echo "<input type='text' name='password' id='password' style='width: 300px;' maxlength='50' class='textbox' value='$decrypt_result' />";
				/*
				$password = "test";
				$pswdlen = 20;
				$encrypt_result = $crypt->encrypt($config['settings']['serial_number'], $password, $pswdlen);
				$errors = $crypt->errors;
				echo $encrypt_result;
				*/

			
			break;
		}	
?>