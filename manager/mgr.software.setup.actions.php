<?php
	###################################################################
	####	SOFTWARE SETUP ACTIONS                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-23-2008                                     ####
	####	Modified: 1-23-2008                                    #### 
	###################################################################
		$checkForValidURL = false;
		
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
		
		switch($_GET['pmode']){
			default:
			$site_url = $_POST['site_url'];
			$incoming_path = $_POST['incoming_path'];
			$library_path = $_POST['library_path'];
			
			# OUTPUT ERROR IF USERNAME IS ALREADY TAKEN
			//if(!empty($admin_rows)){
			//	send_error_back("1",$mgrlang['admin_mes_14'],"username_div");
			//	exit;
			//}
			
			//$site_url = str_replace('www.','',$site_url); // Remove www.
			$checkAgainstURL = dirname(curPageURL());
			//$config['base_url'] = str_replace('www.','',$config['base_url']); // Remove www.	
			
			# CHECK FULL URL
			if($checkForValidURL)
			{
				if($site_url != $checkAgainstURL){
					send_error_back("1",$mgrlang['setup_mes_01'].' - '.$checkAgainstURL,"site_url_div","site_url");
					exit;
				}
			}
			
			# CHECK INCOMING PATH EXISTS
			if(!file_exists($incoming_path)){
				send_error_back("1",$mgrlang['setup_mes_02'],"incoming_path_div","incoming_path");
				exit;
			}
			# CHECK INCOMING PATH WRITABLE
			if(!is_writable($incoming_path)){
				send_error_back("1",$mgrlang['setup_mes_03'],"incoming_path_div","incoming_path");
				exit;
			}
			
			# CHECK GALLERY PATH EXISTS
			if(!file_exists($library_path)){
				send_error_back("1",$mgrlang['setup_mes_05'],"library_path_div","library_path");
				exit;
			}
			# CHECK GALLERY PATH WRITABLE
			if(!is_writable($library_path)){
				send_error_back("1",$mgrlang['setup_mes_06'],"library_path_div","library_path");
				exit;
			}

		break;
		}
?>
<script language="javascript">submit_form();</script>