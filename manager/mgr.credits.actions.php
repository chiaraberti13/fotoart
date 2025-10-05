<?php
	###################################################################
	####	CREDIT ACTIONS                                   	   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-31-2011                                     ####
	####	Modified: 3-31-2011                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "credits";
		
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
		
		# INCLUDE IMAGETOOLS FILE
		require_once('../assets/classes/imagetools.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		# ACTIONS
		switch($_REQUEST['pmode'])
		{
			case "display_ip_list":
				//sleep(1);
				$ip_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$_GET[id]' AND mgrarea = 'credit' ORDER BY ip_id DESC");
				$ip_rows = mysqli_num_rows($ip_result);
				while($ip = mysqli_fetch_object($ip_result))
				{
					$credit = zerofill($_GET['id'],4);
					$ip_id = zerofill($ip->ip_id,4);				
					$src_img = "credit".$credit."_ip".$ip_id."_small.jpg";				
					$src = realpath("../assets/item_photos/$src_img");
					# GET WIDTH
					$size = getimagesize($src);	
					
					
					/*
					$icon_width = 150;
					
					//FIND THE SCALE RATIOS		
					if($size[0] >= $size[1]){
						if($size[0] > $icon_width){
							$width = $icon_width;
						} else {
							$width = $size[0];
						}
						$ratio = $width/$size[0];
						$height = $size[1] * $ratio;				
					} else {
						if($size[1] > $icon_width){
							$height = $icon_width;	
						} else {
							$height = $size[1];	
						}
						$ratio = $height/$size[1];
						$width = $size[0] * $ratio;
					}
					*/
					
					$newsize = get_scaled_size(150,$src);
					
					echo "<div class='ip_div' id='ip_$ip->ip_id'>";
						//'../assets/item_photos/prod0022_ip0012_med.jpg'
						echo "<div class='ip_div_inner' style='width: ".$newsize[0]."px;'><img src='mgr.credits.actions.php?pmode=display_ip&credit=$_GET[id]&ip_id=$ip->ip_id' /></div>";
						echo "<div><a href='javascript:delete_ip($ip->ip_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a></div>";
					echo "</div>";
				}
			break;
			case "display_ip":
				//$src = urldecode($_GET['img']);
				$credit = zerofill($_GET['credit'],4);
				$ip_id = zerofill($_GET['ip_id'],4);				
				$src_img = "credit".$credit."_ip".$ip_id."_small.jpg";				
				$src = realpath("../assets/item_photos/$src_img");
				$image = new imagetools($src);
				$image->size = 150;
				$image->createImage(1,'');
			break;
			case "delete_ip":
				delete_item_photo('credit',$_GET['credit'],$_GET['ip_id']);
				echo "<script>load_ip();</script>";
			break;
			# SET ACTIVE STATUS
			case "ac":
				$credit_result = mysqli_query($db,"SELECT active FROM {$dbinfo[pre]}credits where credit_id = '$_REQUEST[id]'");
				$credit = mysqli_fetch_object($credit_result);
				
				# FLIP THE VALUE
				$new_value = (empty($credit->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}credits SET active='$new_value' where credit_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			# SHOW DETAILS
			case "details":
				$credit_result = mysqli_query($db,"SELECT notes,description FROM {$dbinfo[pre]}credits where credit_id = '$_REQUEST[id]'");
				$credit = mysqli_fetch_object($credit_result);
				
				echo "<div style='padding: 20px;'>";
					if($credit->notes){ echo "<strong>{$mgrlang[gen_notes]}:</strong> $credit->notes<br /><br />"; }
					if($credit->description){ echo "<strong>{$mgrlang[gen_description]}:</strong> $credit->description"; }
					if(!$credit->notes and !$credit->description){ echo $mgrlang['gen_no_notes']; }
				echo "</div>";
			break;
		}	
?>
