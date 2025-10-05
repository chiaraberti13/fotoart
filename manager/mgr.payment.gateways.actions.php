<?php
	# INCLUDE THE SESSION START FILE
	require_once('../assets/includes/session.php');
	
	//sleep(2);

	$page = "payment_options";
	
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
	
	switch($_REQUEST['mode'])
	{	
		default:
		case "save_form":
			
			$gateway_setting_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}paymentgateways WHERE gateway = '$_POST[gateway]'");
			$gateway_setting_rows = mysqli_num_rows($gateway_setting_result);
			if($gateway_setting_rows)
			{
				while($gateway_setting = mysqli_fetch_array($gateway_setting_result))
				{
					$postValue = $_POST[$gateway_setting['setting']];
					$sql = "UPDATE {$dbinfo[pre]}paymentgateways SET value='{$postValue}' WHERE gateway = '$_POST[gateway]' AND setting = '{$gateway_setting[setting]}'";
					$result = mysqli_query($db,$sql);
				}
			}
			
			/*
			foreach($_POST as $key => $value)
			{
				$gateway_setting_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}paymentgateways WHERE gateway = '$_POST[gateway]' AND setting = '$key'");
				$gateway_setting_rows = mysqli_num_rows($gateway_setting_result);
				$gateway_setting = mysqli_fetch_object($gateway_setting_result);
				if($gateway_setting_rows)
				{					
					$sql = "UPDATE {$dbinfo[pre]}paymentgateways SET value='$value' WHERE gateway = '$_POST[gateway]' AND setting = '$key'";
					$result = mysqli_query($db,$sql);
					
				}
			}
			
			foreach($_POST as $key => $value)
			{
				$test.="{$key}:{$value}|";	
			}
			*/
			echo "<script>$('vmessage_$_POST[gateway]').show(); setTimeout(\"hide_timer('vmessage_$_POST[gateway]')\",'3000');</script>";
			
			$short_gateway = str_replace(".php","",$_POST['gateway']);
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_payment_options'],1,$mgrlang['gen_b_update'] . " > <strong>$short_gateway</strong>");
			
		break;
		case "activate":
			$gatewayMode = 'activate';
			
			$gatewaymodule['id'] = $_GET['gatewayid'];
			
			require_once('../assets/gateways/' . $_GET['gatewayid'] . "/config.php");
			require_once('../assets/gateways/' . $_GET['gatewayid'] . "/functions.php");
			
			# DEFINE AN ACTIVE ROW - MIGHT NOT BE NEEDED?
			definegatewayfield($_GET['gatewayid'], 'active', '1');
			
			$short_gateway = str_replace(".php","",$_GET['gatewayid']);
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_payment_options'],1,$mgrlang['gen_activate'] . " > <strong>$short_gateway </strong>");
		break;
		case "deactivate":
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}paymentgateways WHERE gateway = '$_GET[gatewayid]'");
			
			$short_gateway = str_replace(".php","",$_GET['gatewayid']);
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_payment_options'],1,$mgrlang['gen_deactivate'] . " > <strong>$short_gateway </strong>");
		break;
	}
?>


