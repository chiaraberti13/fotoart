<?php
	###################################################################
	####	MANAGER SHARED ACTIONS                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-19-2009                                     ####
	####	Modified: 1-19-2009                                    #### 
	###################################################################
		
	# INCLUDE THE SESSION START FILE
	require_once('../assets/includes/session.php');
	
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
	
	switch($_GET['pmode']){
		case "block_ip":
			$blockips = explode("\n",$config['settings']['blockips']);
			$ip = trim($_GET['ip']);
			if(!in_array($ip,$blockips)){
				# SAVE TO DB
				$new_blockips = $config['settings']['blockips'] . "\n" . $ip;
				$sql = "UPDATE {$dbinfo[pre]}settings SET blockips='$new_blockips' WHERE settings_id  = '1'";
				$result = mysqli_query($db,$sql);
			}
			
			echo "<div style='padding-top: 10px;'><span style='color: #bb0000;'>$mgrlang[gen_block_ip]: <strong>$ip</strong></span><br />$mgrlang[gen_block_ip2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
		break;
		
		case "block_email":
			$blockemails = explode("\n",$config['settings']['blockemails']);
			$email = trim($_GET['email']);
			if(!in_array($email,$blockemails)){
				# SAVE TO DB
				$new_blockemails = $config['settings']['blockemails'] . "\n" . $email;
				$sql = "UPDATE {$dbinfo[pre]}settings SET blockemails='$new_blockemails' WHERE settings_id  = '1'";
				$result = mysqli_query($db,$sql);
			}
			echo "<div style='margin-left: 252px;'><span style='color: #bb0000;'>$mgrlang[gen_block_email]: <strong>$email</strong></span><br />$mgrlang[gen_block_email2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
		break;
		
		case "block_domain":
			echo "<div style='padding-top: 10px;'><span style='color: #bb0000;'>$mgrlang[gen_block_domain]: <strong>" . $_GET['domain'] . "</strong></span><br />$mgrlang[gen_block_domain2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
		break;
	}
?>