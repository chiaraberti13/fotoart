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
	
		$page = "media_ratings";
		
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
				//sleep(2);
				//$media_ratings_result = mysqli_query($db,"SELECT status FROM {$dbinfo[pre]}media_ratings where mr_id = '$_REQUEST[id]'");
				//$media_ratings = mysqli_fetch_object($media_ratings_result);
				
				# FLIP THE VALUE
				//$new_value = (empty($media_ratings->status) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}media_ratings SET status='$_REQUEST[newstatus]' where mr_id = '$_REQUEST[id]'";
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
				echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('rating_sp_$_REQUEST[id]');write_status('rating','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
				
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_ratings'],1,$save_type . " > <strong>$mgrlang[media_ratings_f_rating] ($_REQUEST[id])</strong>");
				
				# FIND OUT HOW MANY MORE ARE PENDING
				$_SESSION['pending_media_ratings'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mr_id) FROM {$dbinfo[pre]}media_ratings WHERE status = '0'"));
				
				/*
				# UPDATE THE DIV WITH THE NEW STATUS
				if($_REQUEST['mempage']==1)
				{
					if($new_value == 1)
					{
						echo "$mgrlang[gen_b_approved]";
						echo "<script>$('ratingcheck".$_REQUEST[id]."').className='mtag_dblue';</script>";
					}
					else
					{
						echo "$mgrlang[gen_pending]";
						echo "<script>$('ratingcheck".$_REQUEST[id]."').className='mtag_good';</script>";
					}
				}
				else
				{
					if($new_value == 1)
					{
						echo "<div class='mtag_dblue' onclick='switch_status_rating($_REQUEST[id]);'>$mgrlang[gen_b_approved]</div>";
					}
					else
					{
						echo "<div class='mtag_good' onclick='switch_status_rating($_REQUEST[id]);'>$mgrlang[gen_pending]</div>";
					}
				}
				*/
				
				# OUTPUT JS
				echo "<script>";
				if($_SESSION['pending_media_ratings'] > 0)
				{
					if($_REQUEST['mempage']!=1)
					{
						echo "\$('ph_status').show();";
						echo "\$('ph_status').update('$_SESSION[pending_media_ratings]');";	
					}
					echo "\$('hnp_media_ratings').show();";
					echo "\$('hnp_media_ratings').update('$_SESSION[pending_media_ratings]');";
				}
				else
				{
					if($_REQUEST['mempage']!=1)
					{
						echo "\$('ph_status').hide();";
					}
					echo "\$('hnp_media_ratings').hide();";
				}
				echo "</script>";

				//echo "<a href=\"javascript:switch_status_rating('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			# SET ACTIVE STATUS
			default:			
			# SET STARS
			case "stars":
				$new_rating = $_GET['rating'];
				
				//$media_ratings_result = mysqli_query($db,"SELECT rating FROM {$dbinfo[pre]}media_ratings where mr_id = '$_REQUEST[id]'");
				//$media_ratings = mysqli_fetch_object($media_ratings_result);
				
				# CHECK TO MAKE SURE THE TWEAK IS SET RIGHT
				if($config['RatingStars'] != 5 and $config['RatingStars'] != 10)
				{
					$config['RatingStars']  = 5;
				}
				
				$on_stars = $new_rating;
				
				if($config['RatingStars'] == 5)
				{
					$new_rating = $new_rating*2;
				}
				
				$sql = "UPDATE {$dbinfo[pre]}media_ratings SET rating='$new_rating' where mr_id = '$_GET[id]'";
				$result = mysqli_query($db,$sql);

				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_ratings'],1,$mgrlang['gen_b_ed'] . " > <strong>$mgrlang[media_ratings_f_rating] ($_REQUEST[id])</strong>");
				
				//echo $on_stars; exit;
				
				for($x=1;$x<=$config['RatingStars'];$x++)
				{
					//onmouseout='rollout_stars($x,$_REQUEST[id])'
					if($x <= $on_stars){ $star_status = "1"; } else { $star_status = "0"; }
					echo "<img src='images/mgr.icon.star.$star_status.png' class='rating_star' onclick='update_rating_stars($x,$_REQUEST[id])' onmouseover='rollover_stars($x,$_REQUEST[id])' onmouseout='rollout_stars_delay($_REQUEST[id])' initialvalue='$star_status' starnumber='$x' />";	
				}
				
				//echo "<a href=\"javascript:switch_status('ap','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
		}	
?>
