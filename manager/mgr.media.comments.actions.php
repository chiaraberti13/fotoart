<?php
	###################################################################
	####	MEDIA COMMENTS ACTIONS                        		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "media_comments";
		
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
			case "ap":
				//$media_comments_result = mysqli_query($db,"SELECT status FROM {$dbinfo[pre]}media_comments where mc_id = '$_REQUEST[id]'");
				//$media_comments = mysqli_fetch_object($media_comments_result);
				
				# FLIP THE VALUE
				//$new_value = (empty($media_comments->status) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}media_comments SET status='$_REQUEST[newstatus]' where mc_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				//$save_type = ($new_value==1) ? $mgrlang['gen_approved'] : $mgrlang['gen_pending'];
				
				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:
					case 0:
						$save_type = $mgrlang['gen_pending'];
						$mtag = 'mtag_pending';
					break;
					case 1:
						$save_type = $mgrlang['gen_b_approved'];
						$mtag = 'mtag_approved';
					break;
				}
				echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('comment_sp_$_REQUEST[id]');write_status('comment','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_comments'],1,$save_type . " > <strong>$mgrlang[media_comments_f_com] ($_REQUEST[id])</strong>");
				
				# FIND OUT HOW MANY MORE ARE PENDING
				$_SESSION['pending_media_comments'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments WHERE status = '0'"));
				
				/*
				# UPDATE THE DIV WITH THE NEW STATUS
				if($_REQUEST['mempage']==1)
				{
					if($new_value == 1)
					{
						echo "$mgrlang[gen_b_approved]";
						echo "<script>$('commentcheck".$_REQUEST[id]."').className='mtag_dblue';</script>";
					}
					else
					{
						echo "$mgrlang[gen_pending]";
						echo "<script>$('commentcheck".$_REQUEST[id]."').className='mtag_good';</script>";
					}
				}
				else
				{
					if($new_value == 1)
					{
						echo "<div class='mtag_dblue' onclick='switch_status_comment($_REQUEST[id]);'>$mgrlang[gen_b_approved]</div>";
					}
					else
					{
						echo "<div class='mtag_good' onclick='switch_status_comment($_REQUEST[id]);'>$mgrlang[gen_pending]</div>";
					}
				}
				*/
				
				
				# OUTPUT JS
				echo "<script>";
				if($_SESSION['pending_media_comments'] > 0)
				{
					if($_REQUEST['mempage']!=1)
					{
						echo "\$('ph_status').show();";
						echo "\$('ph_status').update('$_SESSION[pending_media_comments]');";
					}
					echo "\$('hnp_media_comments').show();";
					echo "\$('hnp_media_comments').update('$_SESSION[pending_media_comments]');";
				}
				else
				{
					if($_REQUEST['mempage']!=1)
					{
						echo "\$('ph_status').hide();";
					}
					echo "\$('hnp_media_comments').hide();";
				}
				echo "</script>";
				
				//echo "<a href=\"javascript:switch_status_comment('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
		}	
?>
