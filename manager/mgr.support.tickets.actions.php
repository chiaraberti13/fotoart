<?php
	###################################################################
	####	MEDIA TAGS ACTIONS   	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "support_tickets";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			# SET ACTIVE STATUS
			default:			
			# SET ACTIVE STATUS
			case "deletemes":
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}ticket_messages WHERE message_id = '$_GET[id]'");
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_support_tickets'],1,$mgrlang['tickets_f_messages'] . " > " . $mgrlang['gen_b_del'] . " > <strong>($_GET[id])</strong>");
				echo "<script>hide_div('message_$_GET[id]');</script>";
			break;
			case "deletefile":
				$file_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}ticket_files WHERE file_id = '$_GET[id]'");
				$file = mysqli_fetch_object($file_result);
				# DELETE DB RECORD
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}ticket_files WHERE file_id = '$_GET[id]'");
				# REMOVE THE FILE
				unlink("../assets/files/$file->saved_name");
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_support_tickets'],1,$mgrlang['tickets_f_files'] . " > " . $mgrlang['gen_b_del'] . " > <strong>($_GET[id])</strong>");
				echo "<script>hide_div('file_row_$_GET[id]');</script>";
			break;			
			# SET STATUS
			case "status":
				$ticket_result = mysqli_query($db,"SELECT status,ticket_id FROM {$dbinfo[pre]}tickets where ticket_id = '$_REQUEST[id]'");
				$ticket = mysqli_fetch_object($ticket_result);
				
				/*
				# FLIP THE VALUE
				switch($ticket->status)
				{
					default:
					case 0:
						$new_value = 2;
						echo "<div class='mtag_good' onclick='switch_status($_REQUEST[id]);'>$mgrlang[gen_pending]</div>";
						$save_type = $mgrlang['gen_pending'];
					break;
					case 1:
						$new_value = 0;
						echo "<div class='mtag_bad' onclick='switch_status($_REQUEST[id]);'>$mgrlang[gen_closed]</div>";
						$save_type = $mgrlang['gen_closed'];
					break;
					case 2:
						$new_value = 1;
						echo "<div class='mtag_dblue' onclick='switch_status($_REQUEST[id]);'>$mgrlang[gen_open]</div>";
						$save_type = $mgrlang['gen_open'];
					break;
				}
				*/
				
				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:					
					case 0:
						$save_type = $mgrlang['gen_closed'];
						$mtag = 'mtag_failed';
					break;
					case 1:
						$save_type = $mgrlang['gen_open'];
						$mtag = 'mtag_approved';
					break;
					case 2:
						$save_type = $mgrlang['gen_pending'];
						$mtag = 'mtag_pending';
					break;
				}
				
				echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('ticket_sp_$_REQUEST[id]');write_status('ticket','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
				
							
				$sql = "UPDATE {$dbinfo[pre]}tickets SET status='$_REQUEST[newstatus]' WHERE ticket_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_support_tickets'],1,$save_type . " > <strong>($_REQUEST[id])</strong>");
				
				# NUMBER OF SUPPORT TICKETS PENDING
				$_SESSION['pending_support_tickets'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(ticket_id) FROM {$dbinfo[pre]}tickets WHERE status = '2'"));
				
				# OUTPUT JS
				echo "<script>";
				# UPDATE THE BIO HEADER PENDING COUNT
				if($_SESSION['pending_support_tickets'] > 0)
				{
					echo "\$('ph_status').show();";
					echo "\$('ph_status').update('$_SESSION[pending_support_tickets]');";				
				}
				else
				{
					echo "\$('ph_status').hide();";
				}
				# UPDATE THE NAV ITEM PENDING COUNT
				if($_SESSION['pending_support_tickets'] > 0)
				{
					echo "\$('hnp_support_tickets').show();";
					echo "\$('hnp_support_tickets').update('$_SESSION[pending_support_tickets]');";
				}
				else
				{
					echo "\$('hnp_support_tickets').hide();";
				}
				echo "</script>";
				
				//echo "<a href=\"javascript:switch_active('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			
		}	
?>
