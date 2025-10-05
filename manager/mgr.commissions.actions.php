<?php
	###################################################################
	####	MEDIA RATINGS ACTIONS                        		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "contrsales";
		
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
			case "updateCompayStatus":
				if($_REQUEST['newstatus'] == 1)
					$addSQL = ',pay_date=now()';
				
				$sql = "UPDATE {$dbinfo[pre]}commission SET compay_status='{$_REQUEST[newstatus]}'{$addSQL} WHERE com_id = '{$_REQUEST[id]}'";
				$result = mysqli_query($db,$sql);
				
				switch($_REQUEST['newstatus'])
				{
					default:
					case 0:
						$save_type = $mgrlang['gen_unpaid'];
						$mtag = 'mtag_pending';
					break;
					case 1:
						$save_type = $mgrlang['gen_paid'];
						$mtag = 'mtag_approved';
					break;
				}
				
				if(!$_REQUEST['returnMode'])
					echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('comstatus_sp_{$_REQUEST[id]}');write_status('payment','{$_REQUEST[id]}',{$_REQUEST[newstatus]});\">{$save_type}</div>";
	
				if($_REQUEST['returnMode'] == 'paidDate')
				{
					$paidDate = new kdate;
					$paidDate->distime = 0;
					
					echo $paidDate->showdate();
				}
	
				//save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_tags'],1,$save_type . " > <strong>$media_tags->keywords ($_REQUEST[id])</strong>");
				echo "<script>";
					//echo "alert('ajax');";
					if($_REQUEST['func'])
					{
						echo "{$_REQUEST[func]}();";
					}
				echo "</script>";
			break;
			# SET ACTIVE STATUS
			default:			
		}	
?>