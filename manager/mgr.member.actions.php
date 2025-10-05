<?php	
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
	//require_once('../assets/includes/tweak.php');					# INCLUDE THE TWEAK FILE	
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON

	require_once('../assets/includes/clean.data.php');

	switch($_REQUEST['pmode']){
		case "loadEmailTemplate":
		
			$contentResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content WHERE content_id = '{$_GET[templateID]}'");
			$contentRows = mysqli_num_rows($contentResult);
			$content = mysqli_fetch_assoc($contentResult);
			
			echo "<p style='float: left;'><strong>$mgrlang[tickets_f_summary]:</strong><br /><input type='text' name='email_summary' style='width: 338px;' value='{$content[name]}'></p>";
			echo "<br style='clear: both;' /><br /><strong>{$mgrlang[gen_message]}:</strong><br /><textarea style='width: 678px; height: 100px' id='email_body' name='email_body'>{$content[content]}</textarea><br />";
			
		break;
		
		
		case "addMemSub":
			if($newMemSub)
			{				
				// Find sub details
				$subResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}subscriptions WHERE sub_id = '{$newMemSub}'");
				$sub = mysqli_fetch_assoc($subResult);
			
				$startDate = gmdate("Y-m-d H:m:s"); // Subscription start date
				$expires = gmdate("Y-m-d H:m:s",strtotime("{$startDate} +{$sub[durvalue]} {$sub[durrange]}s")); // Subscription expiration date 
				
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}memsubs (mem_id,sub_id,status,perday,start_date,expires,total_downloads) VALUES ('{$memID}','{$newMemSub}','1','{$sub[downloads]}','{$startDate}','{$expires}','{$sub[tdownloads]}')");
				$saveid = mysqli_insert_id($db);
			}
			
			//echo "sub {$startDate}"; // Testing
			
		break;
		case "deleteMemSub":
			
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}memsubs WHERE msub_id  = '$_GET[subID]'");
			
		break;
		# UPLOAD THE AVATAR AND MAKE A SMALL ICON FROM IT
		case "upload_avatar":			
			$temp_filename = strtolower($_FILES['Filedata']['name']);
			$temp_array = explode(".",$temp_filename);
			$avatar_extension = $temp_array[count($temp_array)-1];
			$avatar_filename = $_REQUEST['mid'] . "_tmp_avatar." . $avatar_extension;
			move_uploaded_file($_FILES['Filedata']['tmp_name'], "../assets/avatars/".$avatar_filename);
			
			# CREATE SMALL ICON
			if(file_exists("../assets/avatars/" . $avatar_filename)){
				# FIGURE MEMORY NEEDED
				$mem_needed = figure_memory_needed($config['base_path']."/assets/avatars/" . $avatar_filename);
				if(ini_get("memory_limit")){
					$memory_limit = ini_get("memory_limit");
				} else {
					$memory_limit = $config['DefaultMemory'];
				}
				if($memory_limit > $mem_needed){
					$src = $config['base_path']."/assets/avatars/" . $avatar_filename;
					$size = getimagesize($src);			
					switch($avatar_extension){
						case "jpeg":
						case "jpg":
							$src_img = imagecreatefromjpeg($src);
						break;
						case "gif":
							$src_img = imagecreatefromgif($src);
						break;
						case "png":
							$src_img = imagecreatefrompng($src);
						break;
					}
					
					# CREATE THE LARGE AVATAR
					$icon_width = 500;
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
					
					$dst_img = imagecreatetruecolor($width, $height);	
					
					# KEEP TRANSPARENCY
					imagealphablending($dst_img, false);
					imagesavealpha($dst_img,true);
					$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
					imagefilledrectangle($dst_img, 0, 0, $width, $height, $transparent);
					# END KEEP TRANSPARENCY
									
					imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, imagesx($src_img), imagesy($src_img));						
					# SAVE AND DESTROY
					/*
					switch($avatar_extension){
						case "jpeg":
						case "jpg":
							imagejpeg($dst_img,"../assets/avatars/" . $_REQUEST['mid'] . '_large.jpg', 95); // SAVE THIS OUT
						break;
						case "gif":
							imagegif($dst_img,"../assets/avatars/" . $_REQUEST['mid'] . '_large.gif', 95); // SAVE THIS OUT
						break;
						case "png":
							imagepng($dst_img,"../assets/avatars/" . $_REQUEST['mid'] . '_large.png', 95); // SAVE THIS OUT
						break;
					}
					*/
					imagepng($dst_img,$config['base_path']."/assets/avatars/" . $_REQUEST['mid'] . '_large.png', $config['SaveAvatarQuality']); // SAVE THIS OUT
					imagedestroy($dst_img);
					
					# CREATE THE SMALL AVATAR
					$icon_width = 19;
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
					$dst_img = imagecreatetruecolor($width, $height);
					
					# KEEP TRANSPARENCY
					imagealphablending($dst_img, false);
					imagesavealpha($dst_img,true);
					$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
					imagefilledrectangle($dst_img, 0, 0, $width, $height, $transparent);
					# END KEEP TRANSPARENCY
										
					imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, imagesx($src_img), imagesy($src_img));						
					# SAVE AND DESTROY
					imagepng($dst_img,"../assets/avatars/" . $_REQUEST['mid'] . '_small.png', $config['SaveAvatarQuality']); // SAVE THIS OUT
					imagedestroy($src_img); 
					imagedestroy($dst_img);
					
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}members SET avatar='1',avatar_updated='" . gmt_date() . "' WHERE mem_id  = '$_REQUEST[mid]'";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_REQUEST['aid'],$mgrlang['subnav_members'],1,$mgrlang['gen_b_new'] . " " . $mgrlang['mem_f_avatar'] . " > <strong>$mgrlang[mem_member_num]: $_REQUEST[mid]</strong>");
				}
				
				# DELETE THE ORIGINAL
				@unlink("../assets/avatars/" . $avatar_filename);				
				
			}
			
	
		break;
		case "show_avatar_win";
			//sleep(1);
				
			$member_result = mysqli_query($db,"SELECT mem_id,avatar,avatar_status FROM {$dbinfo[pre]}members WHERE mem_id = '$_GET[mid]'");
			$member_rows = mysqli_num_rows($member_result);
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			//include_lang();
			
			if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png")){
				//echo "<img src='../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png" . "' style='border: 2px solid #ffffff; margin-right: 1px;' width='$avatar_width2' />";
				$mem_needed = figure_memory_needed($config['base_path']."/assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png");
				if(ini_get("memory_limit")){
					$memory_limit = ini_get("memory_limit");
				} else {
					$memory_limit = $config['DefaultMemory'];
				}
				
				# FIGURE NEW SIZE NEEDED
				$size = getimagesize("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png");
				//$icon_width = $config['settings']['avatar_size'];
				$icon_width = 150;
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
				
				if($memory_limit > $mem_needed){
					echo "<div style='float: left; padding: 10px; background-color: #FFF; border: 1px solid #c7d3de'><div style='border: 1px solid #d5d5d5; overflow: auto; float: left; margin-right: 7px;' class='dropshadow'><img src='mgr.display.avatar.php?mem_id=$mgrMemberInfo->mem_id&ext=$mgrMemberInfo->avatar&size=$icon_width' style='border: 6px solid #ffffff;' align='left' /></div>";					
					if($_GET['upd']){
						echo "<script>\$('avatar_summary').update(\"<img src='mgr.display.avatar.php?mem_id=" . $mgrMemberInfo->mem_id . "&size=70' style='border: 2px solid #FFF; margin-right: 1px;' class='dropshadow' />\");</script>";
					}
                } else {
					echo "<div style='float: left; padding: 10px; background-color: #fae8e8; border: 1px solid #ba0202; width: 380px'><img src='images/mgr.icon.mem.summary2.gif' style='border: 2px solid #ffffff; margin-right: 7px;' width='40' align='left' />$mgrlang[gen_error_20]: <strong>" . $mem_needed . "mb</strong><br /><br />";
					if($_GET['upd']){
						echo "<script>\$('avatar_summary').update(\"<div style='padding: 10px; background-color: #fae8e8; width: 200px; border: 1px solid #ba0202'><img src='images/mgr.icon.mem.summary2.gif' style='border: 2px solid #ffffff; margin-right: 10px;' width='40' align='left' />$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></div>\");</script>";
                    }
				}
				
				echo "<table>";
				echo "<tr><td>File Name:</td><td><strong>" . $mgrMemberInfo->mem_id . "_large.png" . "</strong></td></tr>";
				echo "<tr><td>Original Size:</td><td><strong>" . $size[0] . "x" . $size[1] . "px</strong></td></tr>";
				//echo "<tr><td>Display Size:</td><td><strong>" . floor($width) . "x" . floor($height) . "px</strong></td></tr>";
				echo "<tr><td>Original File Size:</td><td><strong>" . ceil(filesize("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png")/1024) . "kb</strong></td></tr>";
				echo "</table>";
				//$avatar_status = ($mgrMemberInfo->avatar_status) ? "checked='checked'" : '';
				echo "<p style='cursor: auto; margin-top: 6px;'>";
				echo "<select name='avatar_status'>";
                	echo "<option value='1'";
					if($mgrMemberInfo->avatar_status == 1){ echo "selected"; }
					echo ">$mgrlang[gen_b_approved]</option>";
					echo "<option value='2'";
					if($mgrMemberInfo->avatar_status == 2){ echo "selected"; }
					echo ">$mgrlang[gen_pending]</option>";
                echo "</select>";				
				echo "</p>";
				echo "<div style='padding-bottom: 3px; padding-top: 10px; float: right; clear: both;'><a href='javascript:delete_avatar();' class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' /> $mgrlang[gen_b_del]</a></div>";
				echo "</div>";
					//echo "<div style='padding: 10px; background-color: #fae8e8; width: 300px; border: 1px solid #ba0202'>";
					//echo "<p align='right'><a href='' class='actionlink'>Delete Avatar</a></p>";
					//echo "</div>";
					//}										
				
				//echo "<img src='images/mgr.loader.gif' style='margin-top: 20px;' />";
			} else { 
				echo "<div style='float: left; padding: 10px; background-color: #fae8e8; border: 1px solid #ba0202; width: 300px'><strong>$mgrlang[gen_error_24]</strong><br />$mgrlang[gen_error_23]";
				echo "<p align='right' style='padding-bottom: 3px;'><a href='javascript:try_again();' class='actionlink'>$mgrlang[gen_b_try_again]</a></p>";
				echo "</div>";
				//echo "<img src='images/mgr.icon.mem.summary.gif' style='border: 2px solid #ffffff; margin-right: 1px;' width='$avatar_width2' />";
			}
		break;
		case "delete_avatar":
			//sleep(1);
			
			$member_result = mysqli_query($db,"SELECT mem_id,avatar,f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '$_GET[mid]'");
			$member_rows = mysqli_num_rows($member_result);
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png")){
				if(unlink("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png")){
					if($_GET['lp'] == 1)
					{
						
					}
					else
					{
						echo "<script language='javascript'>flashObj.write(\"avatar_box\");</script>";
					}
					
					# UPDATE THE DB
					$sql = "UPDATE {$dbinfo[pre]}members SET avatar='0',avatar_status='0' WHERE mem_id = '$_GET[mid]'";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_REQUEST['aid'],$mgrlang['subnav_members'],1,$mgrlang['gen_b_del'] . " " . $mgrlang['mem_f_avatar'] . " > <strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name ($_REQUEST[mid])</strong>");
					
				} else {
					echo "cannot delete";
				}
			} else {
				echo "doesn't exist";
			}
			if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_small.png")){
				@unlink("../assets/avatars/" . $mgrMemberInfo->mem_id . "_small.png");
			}
		break;
		case "approve_avatar":			
			$member_result = mysqli_query($db,"SELECT mem_id,avatar,f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '$_GET[mid]'");
			$member_rows = mysqli_num_rows($member_result);
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			# UPDATE THE DB
			$sql = "UPDATE {$dbinfo[pre]}members SET avatar_status='1' WHERE mem_id = '$_GET[mid]'";
			$result = mysqli_query($db,$sql);
						
			# UPDATE ACTIVITY LOG
			save_activity($_REQUEST['aid'],$mgrlang['subnav_members'],1,$mgrlang['gen_b_approve'] . " " . $mgrlang['mem_f_avatar'] . " > <strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name ($_REQUEST[mid])</strong>");
			
			# FIND OUT HOW MANY MORE ARE PENDING
			$_SESSION['pending_member_bios'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE bio_status = '2'"));			
			$_SESSION['pending_member_avatars'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE avatar_status = '2'"));
			$_SESSION['pending_members_inactive'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE status = '2'"));
			$_SESSION['pending_members'] = $_SESSION['pending_member_bios'] + $_SESSION['pending_member_avatars'] + $_SESSION['pending_members_inactive'];
			
			# UPDATE THE DIV WITH THE NEW STATUS
			if($new_value == 1)
			{
				echo "<div class='mtag_approved mtag' onclick='switch_status_bio($_REQUEST[id]);'>$mgrlang[gen_b_approved]</div>";
			}
			else
			{
				echo "<div class='mtag_pending mtag' onclick='switch_status_bio($_REQUEST[id]);'>$mgrlang[gen_pending]</div>";
			}
			
			# OUTPUT JS
			echo "<script>";
			# UPDATE THE BIO HEADER PENDING COUNT
			if($_SESSION['pending_member_avatars'] > 0)
			{
				echo "\$('ph_avatar_status').show();";
				echo "\$('ph_avatar_status').update('$_SESSION[pending_member_avatars]');";
				echo "\$('header_avatar').show();";
				echo "\$('header_avatar').update('$_SESSION[pending_member_avatars]');";				
			}
			else
			{
				echo "\$('ph_avatar_status').hide();";
				echo "\$('header_avatar').hide();";
			}
			# UPDATE THE NAV ITEM PENDING COUNT
			if($_SESSION['pending_members'] > 0)
			{
				echo "\$('hnp_members').show();";
				echo "\$('hnp_members').update('$_SESSION[pending_members]');";
			}
			else
			{
				echo "\$('hnp_members').hide();";
			}
			echo "</script>";
		break;
		case "ratings":
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			$member_id = $_GET['mem_id'];
			
			# CHECK TO MAKE SURE THE TWEAK IS SET RIGHT
			if($config['RatingStars'] != 5 and $config['RatingStars'] != 10)
			{
				$config['RatingStars']  = 5;
			}
			
			# CREATE A DATE OBJECT
			$mrdate = new kdate;
			$zindex = 1000;
			$rating_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_ratings WHERE member_id = '$member_id' ORDER BY status, posted DESC");
			$rating_rows = mysqli_num_rows($rating_result);
			
			if($rating_rows)
			{
			
				while($rating = mysqli_fetch_object($rating_result))
				{
					
					try
					{
						$media = new mediaTools($rating->media_id);
						$mediaInfo = $media->getMediaInfoFromDB();
						$thumbInfo = $media->getIconInfoFromDB();										
						$verify = $media->verifyMediaSubFileExists('icons');										
						$mediaStatus = $verify['status'];
					}
					catch(Exception $e)
					{
						$mediaStatus = 0;
					}
					
					//$src_img = "pack0002_ip0030_small.jpg";				
					//$src = realpath("../assets/item_photos/$src_img");
					# GET WIDTH
					//$size = getimagesize($src);	
					
					if($config['RatingStars'] == 5)
					{
						$on_stars = $rating->rating/2;
					}
					else
					{
						$on_stars = $rating->rating;
					}
					//echo $rating->rating; exit;
					
					//$newsize = get_scaled_size(150,$src);
					
					echo "<div class='ip_div' id='rating_$rating->mr_id' style='height: 175px; width: 180px; padding-top: 20px;'>";
						echo "<div style='height: 120px;'>";
							echo "<div>";
							
								if($mediaStatus == 1)
								{
							?>
								<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=100" class="mediaFrame" />
							<?php
								}
								else
								{
									echo "<img src='images/mgr.theme.blank.gif' style='width: 100px;' class='mediaFrame' />";
								}
							echo "</div>";
						echo "</div>";
						echo "<div id='star_div_".$rating->mr_id."' class='rating_stars_div'>";
						for($x=1;$x<=$config['RatingStars'];$x++)
						{
							if($x <= $on_stars){ $star_status = "1"; } else { $star_status = "0"; }
							echo "<img src='images/mgr.icon.star.$star_status.png' class='rating_star' onclick='update_rating_stars($x,$rating->mr_id)' onmouseover='rollover_stars($x,$rating->mr_id)' onmouseout='rollout_stars_delay($rating->mr_id)' initialvalue='$star_status' starnumber='$x' />";	
						}
						echo "</div>";
						
						?>
						
						<div style="clear: both; overflow: auto; margin-top: 10px; padding: 0 21px 0 21px;">
							<a href='javascript:delete_rating(<?php echo $rating->mr_id; ?>);' class='actionlink' style='white-space: nowrap; float: right; padding: 2px 6px 1px 2px; margin-top: -1px;'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a>
							<div style="float: left; overflow: auto;">
								<div class='status_popup' id='rating_sp_<?php echo $rating->mr_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none; margin-left: -8px; margin-top: -5px; padding-left: 7px; width: 84px;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
								<div id="ratingcheck<?php echo $rating->mr_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-top: 0;">
									<?php
										switch($rating->status)
										{
											case 0: // PENDING
												$tag_label = $mgrlang['gen_pending'];
												$mtag = 'mtag_pending';
											break;
											case 1: // APPROVED
												$tag_label = $mgrlang['gen_b_approved'];
												$mtag = 'mtag_approved';
											break;
										}
									?>
								  <div class='<?php echo $mtag; ?> mtag' style='cursor: pointer' onmouseover="show_sp('rating_sp_<?php echo $rating->mr_id; ?>');write_status('rating','<?php echo $rating->mr_id; ?>',<?php echo $rating->status; ?>)"><?php echo $tag_label; ?></div>
							  </div>
						  </div>
						<?php
						
						/*
						echo "<div style='clear: both;margin-top: 15px;' align='center'>";
							if($rating->status == 1)
							{
								echo "<span class='mtag_dblue' id='ratingcheck".$rating->mr_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_rating($rating->mr_id);'>$mgrlang[gen_b_approved]</span>";
							}
							else
							{
								echo "<span class='mtag_good' id='ratingcheck".$rating->mr_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_rating($rating->mr_id);'>$mgrlang[gen_pending]</span>";
							}
						echo "&nbsp;<a href='javascript:delete_rating($rating->mr_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a>";
						echo "</div>";
						*/
						
					echo "</div></div>";
					$zindex-=2;
				}
			}
			else
			{
				echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_02]}</div></div>";
            }
		break;
		case "delete_rating":
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_ratings WHERE mr_id = '$_GET[mr_id]'");			
			echo "<script>load_ratings();</script>";
		break;
		case "tags":
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			$member_id = $_GET['mem_id'];

			# CREATE A DATE OBJECT
			$mrdate = new kdate;
			$zindex = 1000;
			$tag_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE member_id = '$member_id' AND memtag = 1 ORDER BY status, posted DESC");
			$tag_rows = mysqli_num_rows($tag_result);
			
			if($tag_rows)
			{
				while($tag = mysqli_fetch_object($tag_result))
				{
					try
					{
						$media = new mediaTools($tag->media_id);
						$mediaInfo = $media->getMediaInfoFromDB();
						$thumbInfo = $media->getIconInfoFromDB();										
						$verify = $media->verifyMediaSubFileExists('icons');										
						$mediaStatus = $verify['status'];
					}
					catch(Exception $e)
					{
						$mediaStatus = 0;
					}
					
					//$src_img = "pack0002_ip0030_small.jpg";				
					//$src = realpath("../assets/item_photos/$src_img");
					# GET WIDTH
					//$size = getimagesize($src);	
					
					//$newsize = get_scaled_size(150,$src);
					
					echo "<div class='ip_div' id='rating_$rating->mr_id' style='height: 175px; width: 180px; padding-top: 20px;'>";
						echo "<div style='height: 120px;'>";
							echo "<div>";
							
								if($mediaStatus == 1)
								{
							?>
								<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=100" class="mediaFrame" />
							<?php
								}
								else
								{
									echo "<img src='images/mgr.theme.blank.gif' style='width: 100px;' class='mediaFrame' />";
								}
							echo "</div>";
						echo "</div>";
						echo "<div>";
							echo "<strong>$tag->keyword</strong>";
							//if(in_array('multilang',$installed_addons)){ echo "&nbsp;&nbsp;<span class='mtag_dblue' style='color: #FFF;'>".strtoupper($tag->language)."</span>"; }
						echo "</div>";
						?>
						
						<div style="clear: both; overflow: auto; margin-top: 10px; padding: 0 21px 0 21px;">
							<a href='javascript:delete_tag(<?php echo $tag->key_id; ?>);' class='actionlink' style='white-space: nowrap; float: right; padding: 2px 6px 1px 2px; margin-top: -1px;'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a>
							<div style="float: left; overflow: auto;">
								<div class='status_popup' id='tag_sp_<?php echo $tag->key_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none; margin-left: -8px; margin-top: -5px; padding-left: 7px; width: 84px;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
								<div id="tagcheck<?php echo $tag->key_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-top: 0;">
									<?php
										switch($tag->status)
										{
											case 0: // PENDING
												$tag_label = $mgrlang['gen_pending'];
												$mtag = 'mtag_pending';
											break;
											case 1: // APPROVED
												$tag_label = $mgrlang['gen_b_approved'];
												$mtag = 'mtag_approved';
											break;
										}
									?>
								  <div class='<?php echo $mtag; ?> mtag' style='cursor: pointer' onmouseover="show_sp('tag_sp_<?php echo $tag->key_id; ?>');write_status('tag','<?php echo $tag->key_id; ?>',<?php echo $tag->status; ?>)"><?php echo $tag_label; ?></div>
							  </div>
						  </div>
						<?php
						
						/*
						
						echo "<div style='clear: both;margin-top: 15px;' align='center'>";
							if($tag->status == 1)
							{
								echo "<span class='mtag_dblue' id='tagcheck".$tag->key_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_tag($tag->key_id);'>$mgrlang[gen_b_approved]</span>";
							}
							else
							{
								echo "<span class='mtag_good' id='tagcheck".$tag->key_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_tag($tag->key_id);'>$mgrlang[gen_pending]</span>";
							}
						echo "&nbsp;<a href='javascript:delete_tag($tag->key_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a>";
						*/
					echo "</div></div>";
					$zindex-=2;
				}
			}
			else
			{
				echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_03]}</div></div>";
            }
		break;
		case "delete_tag":
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}keywords WHERE key_id = '$_GET[key_id]'");			
			echo "<script>load_tags();</script>";
		break;
		case "comments":
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			$member_id = $_GET['mem_id'];
			
			# CREATE A DATE OBJECT
			$mrdate = new kdate;
			$zindex = 1000;
			$comments_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_comments WHERE member_id = '$member_id' ORDER BY status, posted DESC");
			$comments_rows = mysqli_num_rows($comments_result);
			
			if($comments_rows)
			{
				while($comments = mysqli_fetch_object($comments_result))
				{
					
					try
					{
						$media = new mediaTools($comments->media_id);
						$mediaInfo = $media->getMediaInfoFromDB();
						$thumbInfo = $media->getIconInfoFromDB();										
						$verify = $media->verifyMediaSubFileExists('icons');										
						$mediaStatus = $verify['status'];
					}
					catch(Exception $e)
					{
						$mediaStatus = 0;
					}
					
					//$src_img = "pack0002_ip0030_small.jpg";				
					//$src = realpath("../assets/item_photos/$src_img");
					# GET WIDTH
					//$size = getimagesize($src);	
					
					//$newsize = get_scaled_size(150,$src);
					
					echo "<div class='ip_div' id='comment_$comments->mc_id' style='width: 100%; height: auto; text-align: left;'>";
						
						echo "<div>";					
							echo "<div class='ip_div_inner' style='float: left; width: ".$newsize[0]."px; margin: 10px 20px 10px 20px'>";
								if($mediaStatus == 1)
								{
							?>
								<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=100" class="mediaFrame" />
							<?php
								}
								else
								{
									echo "<img src='images/mgr.theme.blank.gif' style='width: 100px;' class='mediaFrame' />";
								}
							echo "</div>"
							?>
							<div style="float: left; clear: right; overflow: auto; margin-top: 10px; margin-right: 10px; width: 140px;">
								<a href='javascript:delete_tag(<?php echo $comments->mc_id; ?>);' class='actionlink' style='white-space: nowrap; float: right; padding: 2px 6px 1px 2px; margin-top: -1px;'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a>
								
								<div style="float: left; overflow: auto;">
									<div class='status_popup' id='comment_sp_<?php echo $comments->mc_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none; margin-left: -8px; margin-top: -5px; padding-left: 7px; width: 84px;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
									<div id="commentcheck<?php echo $comments->mc_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-top: 0;">
										<?php
											switch($comments->status)
											{
												case 0: // PENDING
													$tag_label = $mgrlang['gen_pending'];
													$mtag = 'mtag_pending';
												break;
												case 1: // APPROVED
													$tag_label = $mgrlang['gen_b_approved'];
													$mtag = 'mtag_approved';
												break;
											}
										?>
										<div class='<?php echo $mtag; ?> mtag' style='cursor: pointer' onmouseover="show_sp('comment_sp_<?php echo $comments->mc_id; ?>');write_status('comment','<?php echo $comments->mc_id; ?>',<?php echo $comments->status; ?>)"><?php echo $tag_label; ?></div>
									</div>
								</div>
							</div>
							<?php
							echo "<p style='padding: 10px 15px 10px 0;'>$comments->comment</p>";
							/*
							echo "<p style='float: right; padding: 10px 15px 10px 10px;'>";
								if($comments->status == 1)
								{
									echo "<span class='mtag_dblue' id='commentcheck".$comments->mc_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_comment($comments->mc_id);'>$mgrlang[gen_b_approved]</span>";
								}
								else
								{
									echo "<span class='mtag_good' id='commentcheck".$comments->mc_id."' style='padding: 4px; cursor: pointer' onclick='switch_status_comment($comments->mc_id);'>$mgrlang[gen_pending]</span>";
								}
								echo "&nbsp;<a href='javascript:delete_comment($comments->mc_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a>";
							echo "</p>";
							echo "<p style='padding: 10px 15px 10px 0;'>$comments->comment</p>";
						*/	
						echo "</div>";
					echo "</div>";
					$zindex-=2;
				}
			}
			else
			{	
				echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_04]}</div></div>";
            }
		break;
		case "delete_comment":
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_comments WHERE mc_id = '$_GET[mc_id]'");			
			echo "<script>load_comments();</script>";
		break;
		# SET ACTIVE STATUS
		case "ap":
			$member_result = mysqli_query($db,"SELECT status,f_name,l_name FROM {$dbinfo[pre]}members where mem_id = '$_REQUEST[id]'");
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			/*
			# FLIP THE VALUE
			switch($mgrMemberInfo->status)
			{
				default:
				case 0:
					$new_value = 2;
					echo "<div class='mtag_good' onclick='switch_active($_REQUEST[id]);'>$mgrlang[gen_pending]</div>";
					$save_type = $mgrlang['gen_pending'];
				break;
				case 1:
					$new_value = 0;
					echo "<div class='mtag_bad' onclick='switch_active($_REQUEST[id]);'>$mgrlang[gen_closed]</div>";
					$save_type = $mgrlang['gen_closed'];
				break;
				case 2:
					$new_value = 1;
					echo "<div class='mtag_dblue' onclick='switch_active($_REQUEST[id]);'>$mgrlang[gen_active]</div>";
					$save_type = $mgrlang['gen_active'];
				break;
			}
			*/
			
			# FLIP THE VALUE
			switch($_REQUEST['newstatus'])
			{
				default:
				case 0:
					$save_type = $mgrlang['gen_closed'];
					$mtag = 'mtag_closed';
				break;
				case 1:
					$save_type = $mgrlang['gen_active'];
					$mtag = 'mtag_active';
				break;
				case 2:
					$save_type = $mgrlang['gen_pending'];
					$mtag = 'mtag_pending';
				break;
			}
			
			echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('memberstatus_sp_$_REQUEST[id]');write_status('memberstatus','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
						
			$sql = "UPDATE {$dbinfo[pre]}members SET status='$_REQUEST[newstatus]' where mem_id = '$_REQUEST[id]'";
			$result = mysqli_query($db,$sql);
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_members'],1,$save_type . " > <strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name ($_REQUEST[id])</strong>");
			
			# FIND OUT HOW MANY MORE ARE PENDING
			$_SESSION['pending_member_bios'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE bio_status = '2'"));			
			$_SESSION['pending_member_avatars'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE avatar_status = '2'"));
			$_SESSION['pending_members_inactive'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE status = '2'"));
			$_SESSION['pending_members'] = $_SESSION['pending_member_bios'] + $_SESSION['pending_member_avatars'] + $_SESSION['pending_members_inactive'];
			
			# OUTPUT JS
			echo "<script>";
			# UPDATE THE BIO HEADER PENDING COUNT
			if($_SESSION['pending_members_inactive'] > 0)
			{
				echo "\$('ph_status').show();";
				echo "\$('ph_status').update('$_SESSION[pending_members_inactive]');";				
			}
			else
			{
				echo "\$('ph_status').hide();";
			}
			# UPDATE THE NAV ITEM PENDING COUNT
			if($_SESSION['pending_members'] > 0)
			{
				echo "\$('hnp_members').show();";
				echo "\$('hnp_members').update('$_SESSION[pending_members]');";
			}
			else
			{
				echo "\$('hnp_members').hide();";
			}
			echo "</script>";
			
			//echo "<a href=\"javascript:switch_active('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
		break;
		# MEMBER NOTES
		case "notes":
			$member_result = mysqli_query($db,"SELECT notes FROM {$dbinfo[pre]}members where mem_id = '$_REQUEST[id]'");
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			//echo strip_tags($mgrMemberInfo->notes);
			echo "<div style='padding: 10px;'>".strip_tags($mgrMemberInfo->notes)."</div>";
		break;
		# MEMBER TICKETS
		case "tickets":
			# CREATE A DATE OBJECT
			$ticketdate = new kdate;
			$ticketdate->distime = 1;
			
			# TICKETS
			$ticket_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}tickets WHERE member_id = '$_GET[mem_id]' ORDER BY status DESC,lastupdated DESC");
			$ticket_rows = mysqli_num_rows($ticket_result);
			if($ticket_rows)
			{
		?>
			<div class="fs_row_part2" style="width: 100%">
			<table width="100%">
				<tr>
					<th><?php echo $mgrlang['gen_t_id']; ?></th>
					<th align="left"><?php echo $mgrlang['tickets_t_summary']; ?></th>
					<th align="left"><?php echo $mgrlang['gen_lu_caps']; ?></th>
					<th><?php echo $mgrlang['gen_t_status']; ?></th>
					<th></th>
				</tr>
				<?php
					while($ticket = mysqli_fetch_object($ticket_result))
					{                                        
						# SET THE ROW COLOR
						@$row_color++;
						if ($row_color%2 == 0)
						{
							$backcolor = "EEE";
						}
						else
						{
							$backcolor = "FFF";
						}
				?>
                <tr style="background-color: #<?php echo $backcolor; ?>">
                    <td align="center"><a href="mgr.support.tickets.edit.php?edit=<?php echo $ticket->ticket_id; ?>"><?php echo $ticket->ticket_id; ?></a></td>
                    <td align="left"><a href="mgr.support.tickets.edit.php?edit=<?php echo $ticket->ticket_id; ?>"><?php echo $ticket->summary; ?></a></td>
                    <td align="left"><?php echo $ticketdate->showdate($ticket->lastupdated); ?></td>
                    <td align="center">
                    <?php
                        switch($ticket->status)
                        {
                            case 0:
                                echo "<div class='mtag_bad' style='color: #fff; font-weight: bold;'>$mgrlang[gen_closed]</div>";
                            break;
                            case 1:
                                echo "<div class='mtag_dblue' style='color: #fff; font-weight: bold;'>$mgrlang[gen_open]</div>";
                            break;
                            case 2:
                                echo "<div class='mtag_good' style='color: #fff; font-weight: bold;'>$mgrlang[gen_pending]</div>";
                            break;
                        }									
                    ?>
                    </td>                                        
                    <td align="center"><a href="mgr.support.tickets.edit.php?edit=<?php echo $ticket->ticket_id; ?>" class="actionlink">View</a></td>
                </tr>
		<?php
				}
				echo "</table></div>";
            }
            else
            {
				//echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>No support tickets have been opened under this account.</div></div>";
				
				echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_05]}</div></div>";
            }
        break;
		case "submit_message":
			
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			//require_once('../assets/includes/clean.data.php');
			
			switch($message_type)
			{
				case "email":
					
					$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members where mem_id = '$mem_id'");
					$member = mysqli_fetch_assoc($member_result);
					
					$member['unencryptedPassword'] = k_decrypt($member['password']);
					
					$smarty->assign('member',$member);
							
					$email_body = str_replace('\n','<br />',$email_body);	
								
					$email_summary = $smarty->fetch('eval:'.$email_summary);
					$email_body = $smarty->fetch('eval:'.$email_body);
					
					$full_name = "$member[f_name] $member[l_name]";
					
					require_once('../assets/classes/swiftmailer/swift_required.php');
	
					//http://swiftmailer.org/
					
					kmail($member['email'],$member['email'],$config['settings']['support_email'],$config['settings']['business_name'],$email_summary,$email_body,$options); // Send email
					//test('passed-d');
					/*
					if($config['settings']['mailproc'] == 1)
					{
						//Create the Transport - PHP Mailer
						$transport = Swift_MailTransport::newInstance();
					}
					else
					{
						//Create the Transport - SMTP
						$transport = Swift_SmtpTransport::newInstance($config['settings']['smtp_host'], $config['settings']['smtp_port'])
						->setUsername($config['settings']['smtp_port'])
						->setPassword($config['settings']['smtp_port'])
						;
					}
					
					
					//Create the Mailer using your created Transport
					$mailer = Swift_Mailer::newInstance($transport);
					
					//Create the message
					$message = Swift_Message::newInstance()
					
					//Give the message a subject
					->setSubject($email_summary)
					
					
					
					//Set the From address with an associative array
					->setFrom(array($config['settings']['support_email'] => $config['settings']['business_name']))
					
					//Set the To addresses with an associative array
					->setTo(array($member['email'] => $full_name))
					
					//Give it a body
					->setBody($email_body, 'text/html')
					;
					
					//And optionally an alternative body
					//->addPart('<q>Here is the message itself</q>', 'text/html')
				
					//Send the message
					$result = $mailer->send($message);
					*/
					
					
				break;
				case "ticket":
					$status = ($close == 1) ? 0: 1;
					# NEW TICKET
					if($ticket_id == 0)
					{
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}tickets (
								summary,
								viewed,
								member_id,
								opened,
								lastupdated,
								status,
								updatedby
								) VALUES (
								'$summary',
								'0',
								'$mem_id',
								'" . gmt_date() . "',
								'" . gmt_date() . "',
								'$status',
								'".$_SESSION['admin_user']['admin_id']."'
								)";
						$result = mysqli_query($db,$sql);
						$saveid = mysqli_insert_id($db);
					}
					# UPDATE TICKET
					else
					{				
						$saveid = $ticket_id;
						
						# UPDATE THE DATABASE
						$sql = "UPDATE {$dbinfo[pre]}tickets SET 
									status='$status',
									lastupdated='" . gmt_date() . "'
									where ticket_id  = '$saveid'";
						$result = mysqli_query($db,$sql);
					}
					
					if($reply)
					{
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}ticket_messages (
								ticket_id,
								message,
								submit_date,
								admin_response,
								admin_id
								) VALUES (
								'$saveid',
								'$reply',
								'" . gmt_date() . "',
								'1',
								'".$_SESSION['admin_user']['admin_id']."'
								)";
						$result = mysqli_query($db,$sql);
						//$saveid = mysqli_insert_id($db);
					}
				break;
			}
		break;
		case "bio_status":
			$member_result = mysqli_query($db,"SELECT bio_status,f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '$_REQUEST[id]'");
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			# FLIP THE VALUE
			//$new_value = ($mgrMemberInfo->bio_status == 1) ? 2 : 1;	
			
			# FLIP THE VALUE
			switch($_REQUEST['newstatus'])
			{
				default:
				case 1:
					$save_type = $mgrlang['gen_b_approved'];
					$mtag = 'mtag_dblue';
				break;
				case 2:
					$save_type = $mgrlang['gen_pending'];
					$mtag = 'mtag_good';
				break;
			}
			
			echo "<div class='$mtag' onmouseover=\"show_sp('biostatus_sp_$_REQUEST[id]');write_status('biostatus','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
			
			
			$sql = "UPDATE {$dbinfo[pre]}members SET bio_status='$_REQUEST[newstatus]' WHERE mem_id = '$_REQUEST[id]'";
			$result = mysqli_query($db,$sql);
			
			//$save_type = ($new_value==1) ? $mgrlang['gen_approved'] : $mgrlang['gen_unapproved'];
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_member_bios'],1,$save_type . " > <strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name ($_REQUEST[id])</strong>");
			
			# FIND OUT HOW MANY MORE ARE PENDING
			$_SESSION['pending_member_bios'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE bio_status = '2'"));			
			$_SESSION['pending_member_avatars'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE avatar_status = '2'"));
			$_SESSION['pending_members_inactive'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE status = '2'"));
			$_SESSION['pending_members'] = $_SESSION['pending_member_bios'] + $_SESSION['pending_member_avatars'] + $_SESSION['pending_members_inactive'];
			
			# OUTPUT JS
			echo "<script>";
			# UPDATE THE BIO HEADER PENDING COUNT
			if($_SESSION['pending_member_bios'] > 0)
			{
				echo "\$('ph_bio_status').show();";
				echo "\$('ph_bio_status').update('$_SESSION[pending_member_bios]');";
				echo "\$('header_bio').show();";
				echo "\$('header_bio').update('$_SESSION[pending_member_bios]');";				
			}
			else
			{
				echo "\$('ph_bio_status').hide();";
				echo "\$('header_bio').hide();";
			}
			# UPDATE THE NAV ITEM PENDING COUNT
			if($_SESSION['pending_members'] > 0)
			{
				echo "\$('hnp_members').show();";
				echo "\$('hnp_members').update('$_SESSION[pending_members]');";
			}
			else
			{
				echo "\$('hnp_members').hide();";
			}
			echo "</script>";
			
			//echo "<a href=\"javascript:switch_status_bio('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
		break;
		# MEMBER NOTES
		case "bio":
			$member_result = mysqli_query($db,"SELECT bio FROM {$dbinfo[pre]}members where mem_id = '$_REQUEST[id]'");
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			//echo strip_tags($mgrMemberInfo->notes);
			$bio = (strlen(strip_tags($mgrMemberInfo->bio)) > 200) ? substr(strip_tags($mgrMemberInfo->bio),0,200)."..." : strip_tags($mgrMemberInfo->bio);
			echo "<div style='padding: 10px;'>".$bio."</div>";
		break;
		case "avatar_status":
			$member_result = mysqli_query($db,"SELECT avatar_status,f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '$_REQUEST[id]'");
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			# FLIP THE VALUE
			$new_value = (empty($mgrMemberInfo->avatar_status) ? 1 : 0);	
						
			$sql = "UPDATE {$dbinfo[pre]}members SET avatar_status='$new_value' WHERE mem_id = '$_REQUEST[id]'";
			$result = mysqli_query($db,$sql);
			
			$save_type = ($new_value==1) ? $mgrlang['gen_approved'] : $mgrlang['gen_unapproved'];
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_member_avatars'],1,$save_type . " > <strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name ($_REQUEST[id])</strong>");
			
			echo "<a href=\"javascript:switch_status_avatar('$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
		break;
		case "checkEmail":
			$member_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members WHERE email = '$_REQUEST[email]' AND mem_id != '$_REQUEST[edit]'");
			//$mgrMemberInfo = mysqli_fetch_object($member_result);
			$memberRows = mysqli_num_rows($member_result);
			
			//echo '{"errorCode": "'.$memberRows.'"}'; // Email in use
			//exit;
			
			if($memberRows)
			{
				echo '{"errorCode": "emailInUse"}'; // Email in use
			}
			else
			{
				echo '{"errorCode": "good"}'; // Email in use
				//echo '{"errorCode": "good"}'; // No error messages - everything is OK
			}
		break;
		# MEMBER TICKETS
		case "bills":
			# CREATE A DATE OBJECT
			$billDate = new kdate;
			$billDate->distime = 0;
			
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('mgr.defaultcur.php');
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			# BILLS
			$bill_result = mysqli_query($db,
			"
				SELECT * FROM {$dbinfo[pre]}billings 
				JOIN {$dbinfo[pre]}invoices 
				ON {$dbinfo[pre]}invoices.bill_id = {$dbinfo[pre]}billings.bill_id 
				WHERE {$dbinfo[pre]}billings.member_id = '$_GET[mem_id]' AND {$dbinfo[pre]}billings.deleted = 0 ORDER BY {$dbinfo[pre]}invoices.invoice_date DESC");
			$bill_rows = mysqli_num_rows($bill_result);
			
			if($bill_rows)
			{
		?>
			<div class="fs_row_part2" style="width: 100%">
			<table width="100%">
				<tr>
					<th><?php echo $mgrlang['gen_t_id']; ?></th>
					<th align="left"><?php echo $mgrlang['order_t_invoicenum']; ?></th>
					<th align="left"><?php echo $mgrlang['bill_date_caps']; ?></th>
					<th align="left"><?php echo $mgrlang['gen_due_date_caps']; ?></th>
					<th><?php echo $mgrlang['gen_total_caps']; ?></th>
					<th><?php echo $mgrlang['gen_payment_status_caps']; ?></th>
					<th></th>
				</tr>
				<?php
					$zindex = 1000;
					
					while($bill = mysqli_fetch_object($bill_result))
					{                                        
						# SET THE ROW COLOR
						@$row_color++;
						if ($row_color%2 == 0)
						{
							$backcolor = "EEE";
						}
						else
						{
							$backcolor = "FFF";
						}
						
						if(in_array("pro",$installed_addons))
						{
							$invoiceLink = 'mgr.billings.edit.php?edit='.$bill->bill_id;
							$invoiceTarget = '_self';
						}
						else
						{					
							$invoiceLink = '../invoice.php?billID='.$bill->ubill_id; 
							$invoiceTarget = '_blank';
						}
				?>
                <tr style="background-color: #<?php echo $backcolor; ?>">
                    <td align="center"><a href="<?php echo $invoiceLink; ?>" target="<?php echo $invoiceTarget; ?>"><?php echo $bill->bill_id; ?></a></td>
                    <td align="left"><a href="<?php echo $invoiceLink; ?>" target="<?php echo $invoiceTarget; ?>"><?php echo $bill->invoice_number; ?></a></td>
                    <td align="left"><?php echo $billDate->showdate($bill->invoice_date); ?></td>
					<td align="left"><?php echo $billDate->showdate($bill->due_date); ?></td>
                    <td align="center"><?php echo $cleanvalues->currency_display($bill->total,1); ?></td>
					<td align="center">
					<div class='status_popup' id='billstatus_sp_<?php echo $bill->bill_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_bill_sp();" onmouseover="clear_bill_sp_timeout();">xxxxx</div>
					<div id="billpaymentstatuscheck<?php echo $bill->bill_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
						<?php
							switch($bill->payment_status)
							{
								case 0: // PROCESSING
									$tag_label = $mgrlang['gen_processing'];
									$mtag = 'mtag_processing';
								break;
								case 1: // APPROVED
									$tag_label = $mgrlang['gen_paid'];
									$mtag = 'mtag_paid';
								break;
								case 2: // INCOMPLETE
									$tag_label = $mgrlang['gen_unpaid'];
									$mtag = 'mtag_unpaid';
								break;
								case 3: 
									// BILL LATER
								break;
								case 4: // FAILED
									$tag_label = $mgrlang['gen_failed'];
									$mtag = 'mtag_failed';
								break;
								case 5: // REFUNDED
									$tag_label = $mgrlang['gen_refunded'];
									$mtag = 'mtag_refunded';
								break;
								case 6: // FAILED
									$tag_label = $mgrlang['gen_cancelled'];
									$mtag = 'mtag_cancelled';
								break;
							}
						?>
					  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_bill_sp('billstatus_sp_<?php echo $bill->bill_id; ?>');write_bill_status('billstatus','<?php echo $bill->bill_id; ?>',<?php echo $bill->payment_status; ?>)"><?php echo $tag_label; ?></div>
					</div>
					
					
					<?php
						/*
						switch($bill->payment_status)
						{
							case 0: // PENDING                                                
								echo "<div class='mtag_processing mtag'>$mgrlang[gen_processing]</div>";
							break;
							case 1: // APPROVED
								echo "<div class='mtag_paid mtag'>$mgrlang[gen_paid]</div>";
							break;
							case 2: // INCOMPLETE/NONE
								echo "<div class='mtag_unpaid mtag'>$mgrlang[gen_unpaid]</div>";
							break;
							case 4: // FAILED
								echo "<div class='mtag_failed mtag'>$mgrlang[gen_failed]</div>";
							break;
							case 5: // REFUNDED
								echo "<div class='mtag_refunded mtag'>$mgrlang[gen_refunded]</div>";
							break;
							case 6: // CANCELLED
								echo "<div class='mtag_cancelled mtag'>$mgrlang[gen_cancelled]</div>";
							break;
						}
						*/
					?>
                    </td>                                        
                    <td align="center"><a href="<?php echo $invoiceLink; ?>" target="<?php echo $invoiceTarget; ?>" class="actionlink"><?php echo $mgrlang['gen_short_view']; ?></a> <a href="javascript:deleteBill('<?php echo $bill->bill_id; ?>')" class="actionlink"><?php echo $mgrlang['gen_short_delete']; ?></a></td>
                </tr>
		<?php
					$zindex-=2;
				}
				echo "</table></div>";
            }
            else
            {
                echo "<div class='fs_row_part2' style='width: 100%'><div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_10]}</div></div>";
            }
        break;
		case "deleteBill":			
				if(!$billID) exit;
					
				# GET TITLE FOR LOG
				$log_result = mysqli_query($db,"SELECT invoice_number FROM {$dbinfo[pre]}invoices WHERE bill_id = {$billID}");
				$log = mysqli_fetch_object($log_result);
					$log_title= "$log->invoice_number ($log->bill_id)";

					
				# SET BiLLINGS TO DELETED
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}billings SET deleted='1' WHERE bill_id = {$billID}");
					
				# FIND INVOICE ITEMS AND SET THEM TO DELETED
				$invoice_result = mysqli_query($db,"SELECT invoice_id FROM {$dbinfo[pre]}invoices WHERE bill_id = {$billID}");
				while($invoice = mysqli_fetch_object($invoice_result))
				{
					@mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET deleted='1' WHERE invoice_id = '$invoice->invoice_id'");
				}
					
				# SET INVOICES TO DELETED
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}invoices SET deleted='1' WHERE bill_id = {$billID}");
					
				# UPDATE ORDERS IF BILL ME LATER BILL
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}orders SET bill_id='0' WHERE bill_id = {$billID}");
					
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_bill_item'],1,$mgrlang['gen_b_del'] . " > <strong>$log_title</strong>");

		break;
		case "memberDownloads":
			//require_once('../assets/includes/clean.data.php');
			require_once('../assets/classes/mediatools.php');
			
			// Create date object
			$downloadDate = new kdate;
			$downloadDate->distime = 1;
		?>
			<div class="fs_row_part2" style="width: 100%">
				<?php
					// Downloads by this member
					$downloadResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}downloads WHERE mem_id = '{$mem_id}' ORDER BY dl_date DESC");
					$downloadRows = mysqli_num_rows($downloadResult);
					if($downloadRows)
					{
				?>
					<table width="100%">
						<tr>
							<th><?php echo $mgrlang['gen_media_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_dd_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_ds_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_dat_caps']; ?></th>
						</tr>
						<?php
							while($download = mysqli_fetch_array($downloadResult))
							{
								// Row color
								@$rowColor++;
								if($rowColor%2 == 0)
									$bgColor = "EEE";
								else
									$bgColor = "FFF";
									
								if($download['asset_id'])
								{
									try
									{
										$media = new mediaTools($download['asset_id']);
										$mediaInfo = $media->getMediaInfoFromDB();
										$thumbInfo = $media->getIconInfoFromDB();										
										$verify = $media->verifyMediaSubFileExists('icons');										
										$mediaStatus = $verify['status'];
									}
									catch(Exception $e)
									{
										$mediaStatus = 0;
									}
								}
								
								switch($download['dl_type']) // Download type language
								{
									default:
										$downloadTypeLang = $mgrlang['mem_download_unknown'];
									break;
									case "free":
										$downloadTypeLang = $mgrlang['mem_download_free'];
									break;
									case "sub":
										$downloadTypeLang = $mgrlang['mem_download_sub'];
									break;
									case "order":
										$downloadTypeLang = $mgrlang['mem_download_order'];
									break;
									case "credits":
										$downloadTypeLang = $mgrlang['mem_download_credits'];
									break;
									case "prevDown":
										$downloadTypeLang = $mgrlang['mem_download_prev'];
									break;
								}
								
								if($download['dsp_id']) // Download size
								{
									// Find the download size name
									$dspResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '{$download[dsp_id]}'");
									$dspRows = mysqli_num_rows($dspResult);
									if($dspRows)
									{
										$dsp = mysqli_fetch_array($dspResult);
										$sizeName = ($dsp['name_'.$config['settings']['lang_file_mgr']]) ? $dsp['name_'.$config['settings']['lang_file_mgr']] : $dsp['name']; // Find correct lang
									}
									else
										$sizeName = $mgrlang['mem_download_unknown'];
								}
								else
									$sizeName = $mgrlang['mem_orig'];
								
								echo
								"
									<tr style='background-color: #{$bgColor}'>
										<td style='text-align: center;'>
								";
										if($mediaStatus == 1)
											echo "<img src='mgr.media.preview.php?src={$thumbInfo[thumb_filename]}&folder_id={$mediaInfo[folder_id]}&width=60' class='mediaFrame' />";
										else
											echo "<img src='images/mgr.theme.blank.gif' style='width: 60px;' class='mediaFrame' />";
											
										
										echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_id]}: <a href='mgr.media.php?dtype=search&ep=1&search={$download[asset_id]}'>{$download[asset_id]}</a></span>";
										// xxxxxxxxxxxxxxx Should show the actual file based on dp and not the original?
										echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_file]}: <a href='mgr.media.php?dtype=search&ep=1&search={$mediaInfo[filename]}'>{$mediaInfo[filename]}</a></span>";
											
								echo 
								"
										</td>
										<td>".$downloadDate->showdate($download['dl_date'])."</td>
										<td>{$sizeName}</td>
										<td>{$downloadTypeLang}
								";
										if($download['dl_type'] == 'sub'){ echo $download['dl_type_id']; } // Show the subscription number
								echo "
										</td>
									</tr>
								";
							}
						?>
					</table>
				<?php
					}
					else echo "<div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_06]}</div>";
				?>
			</div>		
		<?php
		break;
		case "subscriptions":
			//require_once('../assets/includes/clean.data.php');
			
			// Create date object
			$subDate = new kdate;
			$subDate->distime = 0;
		?>
			<div class="fs_row_part2" style="width: 100%">
				<?php
					// Member subscriptions
					//$subResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}memsubs WHERE mem_id = '{$mem_id}' ORDER BY expires DESC");
					$subResult = mysqli_query($db,
						"
						SELECT *
						FROM {$dbinfo[pre]}memsubs 
						LEFT JOIN {$dbinfo[pre]}subscriptions  
						ON {$dbinfo[pre]}memsubs.sub_id = {$dbinfo[pre]}subscriptions.sub_id 
						WHERE {$dbinfo[pre]}memsubs.mem_id = {$mem_id} 
						ORDER BY {$dbinfo[pre]}memsubs.expires DESC
						"
					);
					
					$subRows = mysqli_num_rows($subResult);
					if($subRows)
					{
				?>
					<table width="100%">
						<tr>
							<th align="center"><?php echo $mgrlang['gen_t_id']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_t_sub_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_t_exp_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_t_dlrem_caps']; ?></th>
							<th align="left"><?php echo $mgrlang['gen_t_dpdr_caps']; ?></th>
							<th align="center"><?php echo $mgrlang['gen_t_status']; ?></th>
							<th align="center"></th>
						</tr>
						<?php
							/* From font end
							while($memsub = mysqli_fetch_array($memsubResult))
							{
									
								
								
								//$today = explode(" ",$nowGMT);
								$dateMinus24Hours = date("Y-m-d H:i:s", strtotime("{$nowGMT} -24 hours"));
				
								$todayDownloads = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(*) FROM {$dbinfo[pre]}downloads WHERE dl_type = 'sub' AND dl_type_id = '{$memsub[msub_id]}' AND mem_id = '{$memberID}' AND dl_date > '{$dateMinus24Hours}'"));
								$totalDownloads = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(*) FROM {$dbinfo[pre]}downloads WHERE dl_type = 'sub' AND dl_type_id = '{$memsub[msub_id]}' AND mem_id = '{$memberID}'"));	
								
								//echo $downloads; exit;
								
								@$totalRemaining = $memsub['total_downloads'] - $totalDownloads;			
								@$todayRemaining = $memsub['perday'] - $todayDownloads;
								
								$memsubArray[$memsub['msub_id']]['totalDownloads'] = $totalDownloads;
								$memsubArray[$memsub['msub_id']]['todayDownloads'] = $todayDownloads;
								$memsubArray[$memsub['msub_id']]['todayRemaining'] = $todayRemaining;
								$memsubArray[$memsub['msub_id']]['totalRemaining'] = $totalRemaining;
							}
							*/							
							
							while($sub = mysqli_fetch_array($subResult))
							{
								// Row color
								@$rowColor++;
								if($rowColor%2 == 0)
									$bgColor = "EEE";
								else
									$bgColor = "FFF";
								
								if($sub['perday'])
									$downloadsPerDay = $sub['perday']; // Downloads per day
								else
									$downloadsPerDay = 0; // Unlimited
								
								$nowGMT = gmt_date();
								$dateMinus24Hours = date("Y-m-d H:i:s", strtotime("{$nowGMT} -24 hours"));
								$todayDownloads = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(*) FROM {$dbinfo[pre]}downloads WHERE dl_type = 'sub' AND dl_type_id = '{$sub[msub_id]}' AND mem_id = '{$mem_id}' AND dl_date > '{$dateMinus24Hours}'"));
								$totalDownloads = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(*) FROM {$dbinfo[pre]}downloads WHERE dl_type = 'sub' AND dl_type_id = '{$sub[msub_id]}' AND mem_id = '{$mem_id}'"));	
								@$totalRemaining = $sub['total_downloads'] - $totalDownloads;			
								@$todayRemaining = $sub['perday'] - $todayDownloads;
								
								$downloadsPerDay = ($sub['perday']) ? $sub['perday'] : 0; // Downloads per day allowed
								
								$status = ($sub['expires'] > gmt_date()) ? 1 : 0; // Expired or not
								
								$today = explode(" ",gmt_date());
								/*
								$downloads = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(dl_id) FROM {$dbinfo[pre]}downloads WHERE dl_type = 'sub' AND dl_type_id = {$sub[msub_id]} AND mem_id = {$mem_id} AND dl_date LIKE '{$today[0]}%'"));
								
								echo "downloads: ".$downloads;
								//$downloads = mysqli_num_rows($downloadResults);								
								//echo $downloads."-";
								
								@$totalDownloadsRemaining = $sub['total_downloads'] - $downloads;
								@$downloadsRemaining = $sub['perday'] - $downloads;
								*/
						?>
							<tr style="background-color: #<?php echo $bgColor; ?>">
								<td align="center"><?php echo $sub['msub_id']; ?></td>
								<td><?php echo $sub['item_name']; ?></td>
								<td><?php echo $subDate->showdate($sub['expires']); ?></td>
								<td><?php if(!$sub['total_downloads']){ echo "{$mgrlang[gen_unlimited]}"; } else { echo "{$sub[total_downloads]} / {$totalRemaining}"; } ?></td>
								<td><?php if($downloadsPerDay == 0){ echo "{$mgrlang[no_limit]}"; } else { echo "{$downloadsPerDay} / {$todayRemaining}"; } ?></td>
								<td align="center"><?php if($status){ echo "<span style='font-weight: bold; color: #41a913;'>{$mgrlang[gen_active]}</span>"; } else { echo "<span style='font-weight: bold; color: #a91513;'>{$mgrlang[gen_expired]}</span>"; } ?></td>
								<td align="center"><a href='javascript:delete_sub(<?php echo $sub['msub_id']; ?>);' class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_b_del']; ?></a></td>
							</tr>
						<?php	
							}
						?>
					</table>
				<?php	
					}
					else echo "<div style='font-weight: bold; padding: 10px;'>{$mgrlang[mem_mes_07]}</div>";
				?>
			</div>
		<?php
		break;
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
					$save_type = $mgrlang['gen_b_paid'];
					$mtag = 'mtag_approved';
				break;
			}
			//echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('compayment_sp_{$_REQUEST[id]}');write_status('payment','{$_REQUEST[id]}',{$_REQUEST[newstatus]});\">{$save_type}xxxxxx</div>";

			//save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_tags'],1,$save_type . " > <strong>$media_tags->keywords ($_REQUEST[id])</strong>");
			
			echo "<script>";
				echo "loadContrSales();";
			echo "</script>";

		break;
		case "deleteCommission":
						
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}commission WHERE com_id = '$_GET[com_id]'");			
			echo "<script>loadContrSales();</script>";

		break;
	}

	//chmod("./files/".$_FILES['Filedata']['name'], 0777); 
?>