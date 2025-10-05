<?php
	###################################################################
	####	LOOK & FEEL  ACTIONS                              	   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-23-2011                                     ####
	####	Modified: 2-23-2011                                   #### 
	###################################################################
		
		//sleep(1);
		
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/db.config.php');				# INCLUDE DATABASE CONFIG FILE
		require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE
		require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE	
		
		# INCLUDE A SECURITY CHECK // USED THIS BECAUSE FLASH DOESN'T KEEP THE SESSION SO SESSION BASED SECURITY WILL NOT WORK
		if($_REQUEST['pass'] != md5($config['settings']['serial_number'])){
			exit; 
		}
		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
		//require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE	- included above
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			case "upload_logo":
				//if($_GET[['adminID'] != "DEMO")
				//{
					$temp_filename = strtolower($_FILES['Filedata']['name']);
					$temp_array = explode(".",$temp_filename);
					$logo_extension = $temp_array[count($temp_array)-1];
					$logo_filename = "main.logo." . $logo_extension;
					move_uploaded_file($_FILES['Filedata']['tmp_name'], "../assets/logos/".$logo_filename);
					
					$sql = "UPDATE {$dbinfo[pre]}settings SET mainlogo='$logo_filename' WHERE settings_id  = '1'";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_website_settings'],1,$mgrlang['gen_b_new'] . " > <strong>{$mgrlang[landf_f_logo]}</strong>");
				//}
			break;
			case "delete_logo":
				if(unlink('../assets/logos/'.$config['settings']['mainlogo']))
				{
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_website_settings'],1,$mgrlang['gen_b_del'] . " > <strong>{$mgrlang[landf_f_logo]}</strong>");					
					echo "<script>create_swfobj();$('uploadwrapper').show();</script>";
				}
				else
				{
					echo "<script>alert('error');</script>";	
				}
				
			break;
			case "logo_window":
				if(file_exists('../assets/logos/'.$config['settings']['mainlogo']))
				{
					echo "<div class='ip_div_inner' style='float: left;'><img src='../assets/logos/".$config['settings']['mainlogo']."?nocache=".date("U")."' /></div><br style='clear:both;' /><a href='javascript:delete_logo();' class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' /> $mgrlang[gen_b_del]</a>";
				}
				else
				{
					echo "Error";	
				}
			break;
			case "upload_window":
				echo "upload";
			break;
		}	
?>
