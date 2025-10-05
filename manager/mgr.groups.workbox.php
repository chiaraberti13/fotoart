<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');				# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');					# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	
	switch($_GET['box']){
		default:
		case "groupswb":
			//echo "<form>";
			echo "<form id='group_edit_form' name='group_edit_form' action='mgr.groups.actions.php' method='post'>";
			echo "<input type='hidden' name='mgrarea' value='$_GET[mgrarea]' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_groups]:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   				echo "<div id='wbbody'>";
					echo "<div style='overflow: auto; position: relative'>";
						echo "<div class='subsubon' style='border-left: 1px solid #d8d7d7;' id='group_list' onclick=\"show_group_list();load_group_list_win('$_GET[mgrarea]');\">$mgrlang[gen_group_list]</div>";
						echo "<div class='subsuboff' id='group_edit' onclick=\"edit_group('new');\" style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_new_group]</div>";
					echo "</div>";
					echo "<div class='more_options' style='background-image:none; width: 835px; padding: 0' id='group_list_win'>";
					echo "</div>";
					echo "<div class='more_options' style='background-position:top; width: 835px; padding: 0; display: none;' id='group_edit_win'>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
			echo "<div id='wbfooter' style='padding: 0 13px 20px 20px; margin: 0;'>";
				echo "<p style='float: right;' id='group_list_win_buttons'><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick='close_workbox();load_group_selector();' /></p>";
				echo "<p style='float: right; display: none;' id='group_edit_win_buttons'><input type='button' value='$mgrlang[gen_b_cancel]' onclick=\"show_group_list();load_group_list_win('$_GET[mgrarea]');\" /><input type='button' value='$mgrlang[gen_b_save]' onclick=\"submit_group_form('$_GET[mgrarea]','{$_SESSION[admin_user][admin_id]}');\" /></p>";
			echo "</div>";
			echo "</form>";
			if($_GET['grouprows'] > 0)
			{
				echo "<script>load_group_list_win('".$_GET['mgrarea']."');</script>";
			}
			else
			{
				echo "<script>edit_group('new');</script>";
			}
		break;		
	}	
?>
