<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	
	switch($_GET['box']){
		default:
		case "groupswb":
			//echo "<form>";
			echo "<form id='group_edit_form' name='group_edit_form' action='mgr.groups.actions.php' method='post'>";
			echo "<div id='wbheader'><p>Debug Window:</p></div>";
   				echo "<div id='wbbody'>";
					echo "switch to debugging theme";
			echo "</div>";
			echo "<div id='wbfooter' style='padding: 0 13px 20px 20px; margin: 0;'>";
				echo "<p style='float: right;'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='close_workbox();' /></p>";
				//echo "<p style='float: right; display: none;' id='group_edit_win_buttons'><input type='button' value='$mgrlang[gen_b_cancel]' onclick=\"show_group_list();load_group_list_win('$_GET[mgrarea]');\" /><input type='button' value='$mgrlang[gen_b_save]' onclick=\"submit_group_form('$_GET[mgrarea]');\" /></p>";
			echo "</div>";
			echo "</form>";
		break;		
	}	
?>
