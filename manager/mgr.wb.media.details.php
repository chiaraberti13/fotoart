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
		case "assign_details":
			
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('mgr.defaultcur.php');
			
			$supportPageID = '315';
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			$cleanvalues->cur_hide_denotation = 0;
			
			if($_GET['mediaID'])
			{
				
				# MEDIATOOLS CLASS
				require_once('../assets/classes/mediatools.php');
				
				try
				{
					$media = new mediaTools($_GET['mediaID']);
					$mediaInfo = $media->getMediaInfoFromDB();					
					$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
					
					$useFolderName = ($folderInfo['encrypted']) ? $folderInfo['enc_name'] : $folderInfo['name']; // Check if it is encrypted or not

					$filename = explode(".",$mediaInfo['filename']);
					$filename_ext = strtolower(array_pop($filename));				
					$filename_glued = implode(".",$filename);
					$baseFilename = $filename_glued;
					
					$originalVerify = $media->verifyMediaFileExists(); // See if original file exists
					
					if($sampleInfo = $media->getSampleInfoFromDB())
						$verify = $media->verifyMediaSubFileExists('samples');
					else
						$verify['status'] = 0;	
					
					// Check if the thumbnail file exists
					if($thumbInfo = $media->getThumbInfoFromDB())
						$thumbVerify = $media->verifyMediaSubFileExists('thumbs');
					else
						$thumbVerify['status'] = 0;	
					
					// Check if the video sample file exists
					if($vidSampleInfo = $media->getVidSampleInfoFromDB())
						$vidSampleVerify = $media->verifyVidSampleExists();
					else
						$vidSampleVerify['status'] = 0;	
					
					if(in_array($mediaInfo['file_ext'],$createableFiletypes) and $mediaInfo['dsp_type'] == 'photo') // Make sure it is a type that can be created
					{						
						/*
						// Get the current memory limit
						if(ini_get("memory_limit")) 
							$memory_limit = str_replace('M','',ini_get("memory_limit"));
						else
							$memory_limit = $config['DefaultMemory'];
						
						// If ImageMagik exists then tweak and use default memory setting
						if(class_exists('Imagick') and $config['settings']['imageproc'] == 2)
						{
							$memory_limit = $config['DefaultMemory'];
						}
						
						$mem_needed = figure_memory_needed($originalVerify['path'].$originalVerify['filename']); // Find approximate memory needed
						
						$autoCreateError = ($mem_needed > $memory_limit) ? 1 : 0; // Send error if not enough memory
						
						*/
						$autoCreate = ($originalVerify['status'] and !$autoCreateError) ? 1 : 0; // Only allow autoCreate if original exists					
						
					}
					
				}
				catch(Exception $e)
				{
					echo "<span style='color: #EEE'>" . $e->getMessage() . "</span>";	
				}
								
				echo "<div id='wbheader'><p>{$mgrlang[gen_short_edit]}: {$mediaInfo[title]} ($mediaInfo[media_id])</p>";
					echo "<div style='position: absolute; right: 0; top: 0; padding: 10px; color: #FFF;'>";
						# GET TAGS
						if(in_array("tagging",$installed_addons))
						{	
							$tag_result = mysqli_query($db,"SELECT COUNT(mt_id) AS numtags FROM {$dbinfo[pre]}media_tags WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1'");
							$tag = mysqli_fetch_object($tag_result);
							echo "<p style='float: left; margin-right: 10px; color: #CCC; font-size: 10px; margin-top: -1px;'><img src='images/mgr.icon.tags.png' style='vertical-align: middle; margin-top: -4px;' /> $tag->numtags</p>";
						}
						
						# GET COMMENTS
						if(in_array("commenting",$installed_addons))
						{
							$commnet_result = mysqli_query($db,"SELECT COUNT(mc_id) AS numcomments FROM {$dbinfo[pre]}media_comments WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1'");
							$comment = mysqli_fetch_object($commnet_result);
							echo "<p style='float: left; margin-right: 10px; color: #CCC; font-size: 10px; margin-top: -1px;'><img src='images/mgr.icon.comment.png' style='vertical-align: middle; margin-top: -3px;' /> $comment->numcomments</p>";
						}
						
						# GET RATINGS
						if(in_array("rating",$installed_addons))
						{
							$rating_result = mysqli_query($db,"SELECT AVG(rating) AS avgrating FROM {$dbinfo[pre]}media_ratings WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1'");
							$rating = mysqli_fetch_object($rating_result);			
							
							$adjustment = 10/$config['RatingStars'];
							$ratingAverage = $rating->avgrating/$adjustment;
							$ratingForStars = ($config['RatingStarsRoundUp']) ? ceil($ratingAverage) : round($ratingAverage);
							/*
							if($config['RatingStars'] == 5)
							{
								$on_stars = $rating->avgrating/2;
							}
							else
							{
								$on_stars = $rating->avgrating;
							}
							*/
							for($x=1;$x<=$config['RatingStars'];$x++)
							{
								if($x <= $ratingForStars){ $star_status = "1"; } else { $star_status = "0"; }
								echo "<img src='images/mgr.icon.star.$star_status.png' class='rating_star' style='width: 10px;' />";	
							}
						}
						echo "<p style='float: right; margin-left: 10px; color: #CCC; font-size: 10px; margin-top: -2px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p>";
					echo "</div>";
				echo "</div>";
				echo "<div id='wbbody' style='overflow: auto; padding: 8px; margin: 0;'>";
					echo "<form name='mediaDetailsForm' action='mgr.media.actions.php' id='mediaDetailsForm'>";
					echo "
						<div id='wbThumbUploaderBox' class='wbUploaderBox' style='display: none; height: 175px'>
							<div style='margin: 30px;'>";
								
								if($thumbVerify['status'])
								{
									// Here we will somehow have to check for video files - [todo]
									echo "<p style='float: left; margin-right: 15px;'><img src='mgr.media.preview.php?folder_id={$mediaInfo[folder_id]}&media_id={$mediaInfo[media_id]}&src={$thumbInfo[thumb_filename]}&type=thumbs&width=100' style='border: 4px solid #FFF; cursor: pointer' class='mediaFrame' id='thumbPreviewImg2' /></p>";
								}
								else
								{
									echo "<p style='float: left; margin-right: 15px;'><img src='images/mgr.theme.blank.gif' style='width: 100px; border: 4px solid #FFF;' class='mediaFrame' id='thumbPreviewImg2' /></p>";
								}
					echo "
								<input type='button' value='".$mgrlang['gen_b_close']."' style='position: absolute; bottom: 20px; right: 20px;' onclick='wbThumbUploaderClose();' />
								<div style='float: left; overflow: auto; width: 320px;'>{$mgrlang[choose_thumb_mes]}:<br /><span style='font-size: 11px; color: #777; font-style: italic'>({$mgrlang[min_size_mes]})</span></div>";
								
								//if($_SESSION['admin_user']['admin_id'] == 'DEMO')
								
								echo "<div style='float: left; margin-top: -4px; padding-left: 10px; width: 200px'><div id='thumbuploader'>Flash Based Uploader</div></div>";
					
					echo "	</div>
						</div>";
					echo "<div id='wbVidSampleUploaderBox' class='wbUploaderBox' style='display: none; height: 100px'><div style='margin: 30px;'><input type='button' value='".$mgrlang['gen_b_close']."' style='position: absolute; bottom: 20px; right: 20px;' onclick='wbVidSampleUploaderClose();' /><div style='float: left; overflow: auto; width: 340px;'>Click the upload button to choose a file for the sample video:<br /><span style='font-size: 11px; color: #777; font-style: italic'>(Only MP4 or FLV files are accepted)</span></div> <div style='float: left; margin-top: -4px; padding-left: 10px; width: 200px'><div id='vidsampleuploader'>Flash Based Uploader</div></div></div></div>";
					echo "<div id='wbOverlay' style='display: none;'></div>";
					echo "<div style='padding: 10px 10px 10px 20px; overflow: auto;'>";
			
					//$vidSampleVerify['status'] = 1; // For testing
			?>
			
					<input type="hidden" name="vidSampleExists" id="vidSampleExists" value="<?php echo $vidSampleVerify['status']; ?>" />
					<div class='mediaFrame' style="padding: 0; border: 4px solid #FFF; width: 300px; height: 225px; float: left; overflow: auto; position: relative; cursor: pointer; <?php if($mediaInfo['dsp_type'] != 'video' or !$vidSampleVerify['status']){ echo 'display: none;'; } ?>" id="wbVidPreview">
						<video id="wbVidPreviewContainer">
							<?php
								if($vidSampleVerify['status'])
								{
							?>
								<script>
									createVideoPlayer('<?php echo $mediaInfo['media_id']; ?>');
								</script>
							<?php
								}
							?>
						</video>
					</div>
					<div style='float: left; overflow: auto; position: relative; cursor: pointer; <?php if($mediaInfo['dsp_type'] == 'video' and $vidSampleVerify['status']){ echo 'display: none;'; } ?>' id='wbThumbPreview'>
			<?php		
						if($verify['status']) // Pre 4.4 $thumbVerify['status']
						{
							echo "<img src='mgr.media.preview.php?folder_id={$mediaInfo[folder_id]}&media_id={$mediaInfo[media_id]}&src={$sampleInfo[sample_filename]}&type=samples&width=300' style='border: 4px solid #FFF; cursor: pointer' class='mediaFrame' onclick=\"revealThumbUploader('{$mediaInfo[media_id]}')\" id='thumbPreviewImg' />";
						}
						else
						{
							echo "<p style='position: absolute; color: #a7a7a7; bottom: 15px; right: 15px;'>Click to upload thumbnail</p><img src='images/mgr.theme.blank.gif' style='width: 300px; border: 4px solid #FFF;' class='mediaFrame' onclick=\"revealThumbUploader('{$mediaInfo[media_id]}')\" id='thumbPreviewImg' /></p>";
						}
						
						$colorPaletteResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}color_palettes WHERE media_id = '{$mediaInfo[media_id]}' ORDER BY percentage DESC");
						$colorPaletteRows = mysqli_num_rows($colorPaletteResult);
						if($colorPaletteRows)
						{
							echo "<div style='margin-top: 6px;' id='colorPalette'>";
							while($colorPalette = mysqli_fetch_array($colorPaletteResult))
							{
								$colorPercentage = round($colorPalette['percentage']*100);
								if($colorPercentage < 1) $colorPercentage = '< 1';
								echo "<div style='float: left; width: 15px; height: 6px; margin-right: 2px; background-color: #{$colorPalette[hex]};' title='#{$colorPalette[hex]} ({$colorPercentage}%)'></div>";
							}
							echo "</div>";
						}
						
						echo "</div>";
						
						//echo "<div style='float: right; margin-top: 80px;'>Color Palette</div>";
						
						//echo "<input type='button' value='Upload Thumbnail' />";
						echo "<ul style='margin-left: 10px; float: left;' id='mediaDetailsEditWin'>";
							try
							{
								$filecheck = $media->verifyMediaFileExists(); // Returns array [stauts,path,filename]
							}
							catch (Exception $e)
							{
								echo "<li><span style='color: #EEE'>" . $e->getMessage() . "</span></li>";	
							}
							echo "<li class='detailheader'>";
								echo "<div style='position: absolute; right: 0; margin-right: 25px; white-space: nowrap;'>";
									echo "<input type='button' value='{$mgrlang[gen_thumbnail]}' onclick=\"revealThumbUploader('{$mediaInfo[media_id]}')\" />";
									echo "<input type='button' value='{$mgrlang[gen_vidsample]}' style='";
									if($mediaInfo['dsp_type'] == 'video'){ echo ""; } else { echo "display: none;"; } 
									echo "' onclick=\"revealVidSampleUploader('{$mediaInfo[media_id]}')\" id='uploadVideoSampleButtonTop' />";
									//if($filecheck['status']){ echo "<input type='button' value='Download File' onclick=\"location.href='mgr.media.actions.php?mode=download&mediaID={$_GET[mediaID]}'\" />"; }
						?>
									<span style="color: #CCC;">|</span>&nbsp;Type: <select style="width: 100px;vertical-align: middle" name="dsp_type" id="dsp_type" onchange="dspTypeSelect();">
										<option value="photo" <?php if($mediaInfo['dsp_type'] == 'photo'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_photo']; ?></option>
										<?php if(in_array('mediaextender',$installed_addons)) { ?><option value="video" <?php if($mediaInfo['dsp_type'] == 'video'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_video']; ?></option><?php } ?>
										<option value="other" <?php if($mediaInfo['dsp_type'] == 'other'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_other']; ?></option>
									</select>
						<?php
									//echo "<input type='button' value='Cancel' onclick='close_workbox();' />";
								echo "</div>";
							echo "Media ID</li>";
							echo "<li>{$mediaInfo[media_id]}</li>";							
							echo "<li class='detailheader'>$mgrlang[gen_owner]</li>";
							echo "<li>";
							if($mediaInfo['owner'] == 0)
							{
								echo $config['settings']['business_name'];	
							}
							else
							{
								$member_result = mysqli_query($db,"SELECT f_name,l_name,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '{$mediaInfo[owner]}'");
								$member_rows = mysqli_num_rows($member_result);
								$mgrMemberInfo = mysqli_fetch_object($member_result);
								if($member_rows)
								{
									echo "<a href='mgr.members.edit.php?edit={$mgrMemberInfo->mem_id}'>{$mgrMemberInfo->f_name} {$mgrMemberInfo->l_name}</a> | <a href='mgr.media.php?owner={$mgrMemberInfo->mem_id}'>{$mgrlang[all_media]}</a>";
								}
								else
								{
									echo $mgrlang['gen_unknown'];	
								}
							}
							echo "</li>";
							
							echo "<li class='detailheader'>{$mgrlang[mediadet_views]}</li>";
							echo "<li>{$mediaInfo[views]}</li>";	
							/*
							echo "<li class='detailheader'>Batch ID</li>";
							echo "<li>{$mediaInfo[batch_id]}</li>";
							*/
							$dateObj = new kdate;
							$dateAdded = $dateObj->showdate($mediaInfo['date_added']);
							echo "<li class='detailheader' style='float: left;'>{$mgrlang[mediadet_dateadded]}</li>";
							echo "<li class='detailheader' style='float: left;'>{$mgrlang[mediadet_datecreated]}</li>";
							echo "<li style='clear: left; float: left; width: 120px;'>{$dateAdded}</li>";
							echo "<li style='float: left;'>";
							if($mediaInfo['date_created'] != '0000-00-00 00:00:00')
								echo $dateObj->showdate($mediaInfo['date_created']);
							else
								echo $mgrlang['gen_na'];
							echo "</li>";
							echo "<li class='detailheader' style='clear: both;'>{$mgrlang[mediadet_foldfile]}</li>";
														
							try
							{
								$filecheckimg = ($filecheck['status']) ? "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;margin-top: -4px; vertical-align: middle; width: 10px;' />" : "<img src='images/mgr.notice.icon.small2.png' style='margin-right: 4px; vertical-align: middle; width: 15px; margin-top: -2px;' />";
								
								$fullPathFilename = $filecheck['path'].$filecheck['filename'];
								
								//echo strlen($filecheck['path']);
								
								if(strlen($fullPathFilename) > 70)
								{
									$filePathSplit = substr($fullPathFilename,0,70)."<br />";
									$filePathSplit.= substr($fullPathFilename,70,strlen($filecheck['path']));
								}
								else
								{
									$filePathSplit = $fullPathFilename;
								}
								
								echo "<li><a href='";
								
								if($filecheck['status'])
								{
									//echo "&nbsp;<input type='button' value='Download' onclick=\"";
									if($_SESSION['admin_user']['admin_id'] == "DEMO")
										echo "javascript:demo_message2();";
									else
										echo "mgr.media.actions.php?mode=download&mediaID={$_GET[mediaID]}";
									//echo "\" />";									
								}
								
								echo "' style='text-decoration: none;'>{$filecheckimg} {$filePathSplit}</a>";
								
								echo "</li>";
								
								if($mediaInfo['external_link'])
								{
									echo "<li class='detailheader' style='clear: both;'>{$mgrlang[media_f_el]}</li>";
									
									@$externalfilecheckimg = (checkExternalFile($mediaInfo['external_link']) > 400) ? "<img src='images/mgr.notice.icon.small2.png' style='margin-right: 4px; vertical-align: middle; width: 15px; margin-top: -2px;' />" : "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;margin-top: -4px; vertical-align: middle; width: 10px;' />";
									
									echo "<li>{$externalfilecheckimg} <a href='{$mediaInfo['external_link']}'>{$mediaInfo['external_link']}</a></li>";
								}
							}
							catch (Exception $e)
							{
								echo "<li><span style='color: #EEE'>" . $e->getMessage() . "</span></li>";	
							}	
						echo "</ul>";
					echo "</div>";
					echo "<div id='button_bar' style='clear: both;'>";
						echo "<div class='subsubon' onclick=\"bringtofront('1');\" id='tab1'>{$mgrlang[gen_details]}</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('2');\" id='tab2'>{$mgrlang[gen_tab_galleries]}</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('3');\" id='tab3'>{$mgrlang[gen_dig_ver]}</div>";
						if($config['settings']['cart'])
						{
							echo "<div class='subsuboff' onclick=\"bringtofront('4');\" id='tab4'>{$mgrlang[gen_prods]}</div>";
							echo "<div class='subsuboff' onclick=\"bringtofront('5');\" id='tab5'>{$mgrlang[gen_prints]}</div>";
							echo "<div class='subsuboff' onclick=\"bringtofront('9');\" id='tab9'>{$mgrlang[gen_packs]}</div>";
						}
						echo "<div class='subsuboff' onclick=\"bringtofront('6');\" id='tab6'>{$mgrlang[gen_colls]}</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('7');\" id='tab7'>{$mgrlang[gen_media_types]}</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('8');activate_release_uploaders({$mediaInfo[media_id]})\" id='tab8' style='border-right: 1px solid #d8d7d7;'>{$mgrlang[gen_tab_advanced]}</div>";
					echo "</div>";
					echo "<input type='hidden' name='mode' value='updateMediaDetails' />";
					echo "<input type='hidden' name='mediaID' value='{$mediaInfo[media_id]}' />";
					
					//echo "<input type='text' value='mgr.view.video.php?mediaID={$mediaInfo[media_id]}&pass=".md5($config['settings']['serial_number'])."' />";
					
					if($thumbVerify['status'])
					{
						
						echo "<input type='hidden' name='poster' id='poster' value='mgr.media.preview.php?folder_id={$mediaInfo[folder_id]}&media_id={$mediaInfo[media_id]}&src={$thumbInfo[thumb_filename]}&type=thumbs&width=300' />";
					}
					
					echo "<input type='hidden' name='baseFilename' id='baseFilename' value='{$baseFilename}' />";
					echo "<input type='hidden' name='folderID' id='folderID' value='{$mediaInfo[folder_id]}' />";
			?>
					<div id="tab1_group" class="group">						
						<?php if(in_array("contr",$installed_addons) and $mediaInfo['owner'] != 0){ ?>
							<div class="<?php fs_row_color(); ?>" style="float: left; margin-bottom: 20px;">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_astatus']; ?>: <br />
									<span><?php echo $mgrlang['media_f_astatus_d']; ?></span>
								</p>
								<div style="float: left">
									<select name="approvalStatus" id="approvalStatus" onChange="approvalStatusChange()">
										<option value="1" <?php if($mediaInfo['approval_status'] == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['approvalStatus1']; ?></option>
										<option value="0" <?php if($mediaInfo['approval_status'] == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['approvalStatus0']; ?></option>
										<option value="2" <?php if($mediaInfo['approval_status'] == 2){ echo "selected='selected'"; } ?>><?php echo $mgrlang['approvalStatus2']; ?></option>
									</select>
									<div id="approvalMessage" style="<?php if($mediaInfo['approval_status'] == 2){ echo "display: block;"; } else { echo "display: none;";}  ?>padding-top: 10px; font-weight: bold;"><?php echo $mgrlang['media_f_astatus_mes']; ?>:<br /><textarea style="width: 330px; height: 70px;" name="approvalMessage"><?php echo $mediaInfo['approval_message']; ?></textarea></div>
								</div>
							</div>
						<?php
							}
							else{
						?>
							<input name="approvalStatus" type="hidden" value="1">
						<?php
							} 
						?>
						
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_title']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_title_d']; ?></span>
							</p>
							<div class="additional_langs">
								<input type="text" name="title" id="title" style="width: 330px;" maxlength="100" value="<?php echo $mediaInfo['title']; ?>" />
								<?php
									if(in_array('multilang',$installed_addons)){
								?>
									&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
									<div id="lang_title" style="display: none;">
									<ul>
									<?php
										foreach($active_langs as $value){
									?>
										<li><input type="text" name="title_<?php echo $value; ?>" id="title_<?php echo $value; ?>" style="width: 330px;" maxlength="100" value="<?php echo @stripslashes($mediaInfo['title_'.$value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
								<?php
										}
										echo "</ul></div>";
									}
								?>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_description']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_description_d']; ?></span>
							</p>
							<div class="additional_langs">
								<textarea name="description" id="description" style="width: 330px; height: 50px; vertical-align: middle"><?php echo $mediaInfo['description']; ?></textarea>
								<?php
									if(in_array('multilang',$installed_addons)){
								?>
									&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
									<div id="lang_description" style="display: none;">
									<ul>
									<?php
										foreach($active_langs as $value){
									?>
										<li><textarea name="description_<?php echo $value; ?>" style="width: 330px; height: 50px; vertical-align: middle"><?php echo @stripslashes($mediaInfo['description_'.$value]); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
								<?php
										}
										echo "</ul></div>";
									}
								?>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_keywords']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_keywords_d']; ?></span>
							</p>
							<div class="additional_langs">
								<div style="width: 415px;">
									<div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_keywords','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>&nbsp;<input type="text" id="new_keyword_DEFAULT" /> <input type="button" value="Add" onclick="add_keyword('DEFAULT','<?php echo $mediaInfo['media_id']; ?>');" style="margin-top: -4px;" /></div>
									<div class="keyword_list" id="keywords_DEFAULT">
										<div style="display: none;" kwlanguage="DEFAULT" id="placeholder_DEFAULT"></div>
										<?php
											$keywords_result = mysqli_query($db,"SELECT keyword,key_id FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaInfo[media_id]}' AND language = '' AND memtag = 0 ORDER BY keyword");
											while($keywords = mysqli_fetch_object($keywords_result))
											{	
												echo "<input type='button' onclick=\"remove_keyword('{$keywords->key_id}')\" kwlanguage='DEFAULT' id='key_{$keywords->key_id}' value='{$keywords->keyword}' class='greyButton' />";
												//echo "<input type='hidden' name='keyword_DEFAULT[]' id='DEFAULT_key_{$keywords->key_id}_input' value='{$keywords->keyword}' />";
											}	
										?>
									</div>
								</div>
								
								<?php
									if(in_array('multilang',$installed_addons))
									{
								?>
									<div id="lang_keywords" style="display: none;">
									<?php
										foreach($active_langs as $value)
										{
											$value = strtoupper($value);
									?>
										<!--<li><textarea name="keywords_<?php echo $value; ?>" style="width: 200px; height: 50px;"><?php echo @stripslashes($shipping->{"description" . "_" . $value}); ?></textarea> (<?php echo strtoupper($value); ?>)</li>-->
										<div style="width: 415px; margin-top: 5px">
											<div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF;"><?php echo strtoupper($value); ?></span>&nbsp;<input type="text" id="new_keyword_<?php echo $value; ?>" /> <input type="button" value="Add" onclick="add_keyword('<?php echo $value; ?>','<?php echo $mediaInfo['media_id']; ?>');" style="margin-top: -4px;" /></div>
											<div class="keyword_list" id="keywords_<?php echo $value; ?>">
												<div style="display: none;" kwlanguage="<?php echo $value; ?>" id="placeholder_<?php echo $value; ?>"></div>
												<?php
													$keywords_result = mysqli_query($db,"SELECT keyword,key_id FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaInfo[media_id]}' AND language = '{$value}' AND memtag = 0 ORDER BY keyword");
													while($keywords = mysqli_fetch_object($keywords_result))
													{	
														echo "<input type='button' onclick=\"remove_keyword('{$keywords->key_id}')\" kwlanguage='{$value}' id='key_{$keywords->key_id}' value='{$keywords->keyword}' class='greyButton' />";
														//echo "<input type='hidden' name='keyword_{$value}[]' id='{$value}_key_{$keywords->key_id}_input' value='{$keywords->keyword}' />";
													}	
												?>
											</div>
										</div>
								<?php
										}
										echo "</div>";
									}
								?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<script>
						$j(function()
						{
							$j('#galTestButton').on( "click", function(){ 
								//alert($j("input[name='mediaGalleries[]']").val());
								//alert('test');
								
								//alert($j('#gals').jstree('get_selected'));
							});
						});
						</script>
					
					<div id="tab2_group" class="group">
						<div class="<?php fs_row_color(); ?>" style="float: left; margin-bottom: 20px;">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_galls']; ?>: <br />
								<span><?php echo $mgrlang['media_f_galls_d']; ?></span>
							</p>
							<div style="float: left; width: 430px;">
								<!--<div style="background-color: #eee; padding: 5px; border: 1px solid #d9d9d9; font-size: 11px; font-weight: bold; text-align: right"><input type="text" style="height: 14px; width: 150px" id="new_gallery_name" /> <input type="button" value="Create" style="margin-top: -4px;" onclick="create_gallery();" /></div>-->
								<div id="mediaGalleries" style="display: none;">
									<?php
										$galleriesResult = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id = '$_GET[mediaID]'");
										while($galleries = mysqli_fetch_assoc($galleriesResult))
										{
											echo "<input type='text' name='mediaGalleries[]' id='mediaGalleries-{$galleries[gallery_id]}' value='{$galleries[gallery_id]}'><br>";	
										}
									?>
								</div>
								<input type="hidden" name="checkGalLoaded" id="checkGalLoaded" value="0" />
								<div name="gals" id="gals" style="max-height: 400px; overflow: auto; font-size: 11px; padding: 5px; border: 1px solid #EEE"></div>
							</div>
						</div>
						<?php
							if($mediaInfo['owner'])
							{
						?>
						<div class="<?php fs_row_color(); ?>" style="float: left; margin-bottom: 20px;">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_albums']; ?>: <br />
								<span><?php echo $mgrlang['media_f_albums_d']; ?></span>
							</p>
							<div style="float: left; width: 430px;">
								<div name="galsAlbums" id="galsAlbums" style="max-height: 400px; overflow: auto; font-size: 11px; padding: 5px; border: 1px solid #EEE"></div>
							</div>
						</div>
						<?php
							}
						?>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab3_group" class="group">                	
						<div class="fs_header">Original Version</div>
						<div class="<?php fs_row_color(); ?>" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_format']; ?>:<br />
								<span><?php echo $mgrlang['media_f_format_d']; ?></span>
							</p>
							<input type="text" name="format" id="format" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($mediaInfo['format']); ?>" />
						</div> 
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_width']; ?>:<br />
								<span><?php echo $mgrlang['media_f_width_d']; ?></span>
							</p>
							<input type="text" name="width" id="width" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($mediaInfo['width']); ?>" />
						</div> 
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_height']; ?>:<br />
								<span><?php echo $mgrlang['media_f_height_d']; ?></span>
							</p>
							<input type="text" name="height" id="height" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($mediaInfo['height']); ?>" />
						</div> 
						<div id="video_div" style="display: <?php if($mediaInfo['dsp_type'] == 'video'){ echo "block"; } else { echo "none"; } ?>">
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_hd']; ?>:<br />
									<span><?php echo $mgrlang['media_f_hd_d']; ?></span>
								</p>
								<input type="checkbox" name="hd" id="hd" value="1" <?php if($mediaInfo['hd']){ echo "checked='checked'"; } ?> />
							</div>
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_fps']; ?>:<br />
									<span><?php echo $mgrlang['media_f_fps_d']; ?></span>
								</p>
								<input type="text" name="fps" id="fps" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes(trim($mediaInfo['fps'],"0,.")); ?>" />
							</div>             
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_runtime']; ?>:<br />
									<span><?php echo $mgrlang['media_f_runtime_d']; ?></span>
								</p>
								<input type="text" name="running_time" id="running_time" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($mediaInfo['running_time']); ?>" /> <?php echo $mgrlang['gen_seconds']; ?>
							</div>
						</div>
						
						
						<div class="<?php fs_row_color(); ?>" fsrow="1">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_orgcopy']; ?>:<br />
								<span><?php echo $mgrlang['media_f_orgcopy_d']; ?></span>
							</p>
							<select id="original_copy" name="original_copy" onchange="original_dd();" style="width: 298px;">
								<option value="nfs" <?php if($mediaInfo['license'] == 'nfs'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['media_f_hidden']; ?></option>                            
								<?php
									$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses");
									while($license = mysqli_fetch_assoc($licenseResult))
									{
										echo "<option ' value='{$license['lic_purchase_type']}-{$license['license_id']}' ";
										if($mediaInfo['license_id'] == $license['license_id']) echo "selected='selected'";
										echo ">{$license[lic_name]}</option>";
									}
								?>                            
							</select>
						</div>
						<div class="<?php fs_row_color(); ?>" id="quantity_div" fsrow="1" style="<?php if($mediaInfo['license'] != 'nfs'){ echo "display: block;"; } else { echo "display: none;"; } ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_quantity']; ?>: <br />
								<span><?php echo $mgrlang['leave_blank_quan']; ?></span>
							</p>
							<input type="text" name="quantity" id="quantity" style="width: 90px;" maxlength="50" value="<?php echo $mediaInfo['quantity']; ?>" />
						</div>
						<?php
							if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
							{
						?>
							<div class="<?php fs_row_color(); ?>" id="assign_price_div" fsrow="1" style="<?php if($mediaInfo['license'] == 'rf' or $mediaInfo['license'] == 'rm' or $mediaInfo['license'] == 'ex' or $mediaInfo['license'] == 'eu'){ echo "display: block;"; } else { echo "display: none;"; } ?>">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_price']; ?>: <br />
									<span><?php echo $mgrlang['media_f_price_d']; ?></span>
								</p>
								<div style="float: left;">
									<input type="text" name="price" id="price" style="width: 90px;" maxlength="50" onkeyup="price_preview('dsp');" onblur="update_input_cur('price');" value="<?php if($mediaInfo['price'] > 0){ echo $cleanvalues->currency_display($mediaInfo['price'],0); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?><br />
									<span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
								</div>
							</div>
						<?php
							}
							if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
							{
						?>
							<div class="<?php fs_row_color(); ?>" id="assign_credit_div" fsrow="1" style="<?php if($mediaInfo['license'] == 'rf' or $mediaInfo['license'] == 'rm' or $mediaInfo['license'] == 'ex' or $mediaInfo['license'] == 'eu'){ echo "display: block;"; } else { echo "display: none;"; } ?>">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_credits']; ?>: <br />
									<span><?php echo $mgrlang['media_f_credits_d']; ?></span>
								</p>
								<input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" onkeyup="credits_preview('dsp');" value="<?php if($mediaInfo['credits']){ echo $mediaInfo['credits']; } ?>" /><br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
							</div>                    
						<?php
							}
						?>
						
						<?php
							$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE active = '1' AND deleted = '0' ORDER BY sortorder");
							$digital_sp_rows = mysqli_num_rows($digital_sp_result);
							if($digital_sp_rows)
							{
						?>
							<div class="fs_header" style="margin-top: 20px;"><?php echo $mgrlang['other_digital_sizes']; ?></div>
							<div class="<?php fs_row_color(); ?>" fsrow="1" style="padding: 20px 20px 20px 20px;">
								<div style="position:relative; padding: 0; margin: -16px 0 10px 0;">
									<div id="wbDSPUploaderBox" class="wbUploaderBox" style="display: none; height: 160px; width: 100%; margin-top: -32px; padding-top: 0; border-top: 1px solid #EEE;">
										<div style="margin: 30px;">
											<input type="button" value="<?php echo $mgrlang['gen_b_close']; ?>" style="position: absolute; bottom: 20px; right: 20px;" onclick="wbDSPUploaderClose();" />
											<div style="float: left; overflow: auto; width: 320px;">
												<?php echo $mgrlang['media_click_upbut']; ?>:<br /><span style="font-size: 11px; color: #777; font-style: italic"></span>
											</div>
											<div style="float: left; margin-top: -4px; padding-left: 0; width: 200px">
												<div id='dspuploader'>Flash Based Uploader</div>
											</div>
											<br style="clear: both;" /><p style="font-size: 24px; color: #999"><?php echo $mgrlang['gen_or']; ?></p><br style="clear: both;" /><br style="clear: both;" />
											<div style="clear: both;">
												<?php echo $mgrlang['media_f_el']; ?>: 
												<input type="ds_external_file" id="ds_external_file" value="" style="width: 300px; height: 20px;" /> <input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" id="saveDsExternalLink" />
												<br />
											</div>
										</div>
									</div>
								</div>
								<?php if($autoCreateError){ ?><div style="padding: 5px; background-color: #a91513; font-weight: bold; font-size: 11px; color: #FFF; text-align: center"><?php echo $mgrlang['media_no_mem']; ?></div><?php } ?>
								
								<?php
									if(in_array("pro",$installed_addons))
									{
										$digital_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'digital_sp' ORDER BY name");
										$digital_group_rows = mysqli_num_rows($digital_group_result);
										if($digital_group_rows)
										{
								?>
											<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
											<?php
												$digitalg_result = mysqli_query($db,"SELECT dsgrp_id FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id = '{$mediaInfo[media_id]}' AND dsgrp_id != '0'");
												//$mediapg_rows = mysqli_num_rows($mediapg_result);
												while($digitalg = mysqli_fetch_object($digitalg_result))
												{
													$digitalGroups[] = $digitalg->dsgrp_id;
												}
												
												
												while($digital_group = mysqli_fetch_object($digital_group_result))
												{
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none'><input type='checkbox' value='{$digital_group->gr_id}' id='digitalgroupcb_{$digital_group->gr_id}' name='digitalgroup[]' ";
													if(@in_array($digital_group->gr_id,$digitalGroups)){ echo "checked='checked'"; }
													echo "/> &nbsp; "; if($digital_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$digital_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$digital_group->flagtype' align='absmiddle' /> "; } echo "<label for='digitalgroupcb_{$digital_group->gr_id}'><strong>" . $digital_group->name . "</strong></label>";	
													
													$digital_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}digital_sizes.name FROM {$dbinfo[pre]}digital_sizes JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}digital_sizes.ds_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='{$digital_group->gr_id}' AND {$dbinfo[pre]}digital_sizes.deleted='0'");
													$digital_groupids_rows = mysqli_num_rows($digital_groupids_result);
													
													if($digital_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdigitalgp".$digital_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('digitalgroup_{$digital_group->gr_id}','','','','plusminus-digitalgp{$digital_group->gr_id}');\" />";
														echo "<div id=\"digitalgroup_{$digital_group->gr_id}\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($digital_groupids = mysqli_fetch_object($digital_groupids_result)){
															echo $digital_groupids->name . "<br />"; //$digital_groupids_rows
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";
											}
										}
								?>								
								
								<div style="width: 100%; border: 1px solid #EEE" class="divTable">
									<?php
										while($digital_sp = mysqli_fetch_object($digital_sp_result)){
											
											$mediads_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_digital_sizes WHERE ds_id = '{$digital_sp->ds_id}' AND media_id = '{$mediaInfo[media_id]}'");
											$mediads_rows = mysqli_num_rows($mediads_result);
											$mediads = mysqli_fetch_object($mediads_result);
											
											if($mediads_rows){ $originalMediaDS .= $digital_sp->ds_id . ","; }
											
											$backgroundColor = ($backgroundColor == 'FFF') ? 'f7f7f7' : 'FFF';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor};'>";
												
												echo "<div class='divTableCell' style='padding: 8px 8px 8px 15px; vertical-align: middle;'>";
													//echo "<div>";
														echo "<input type='checkbox' value='$digital_sp->ds_id' name='digitalsp[]' id='digitalsp_$digital_sp->ds_id' onclick='displayDSPOptions({$digital_sp->ds_id});' style='padding: 0; margin: 0;' ";
														if($mediads_rows){ echo " checked='checked'"; }
														
														$dspName = (strlen($digital_sp->name) > 20) ? substr($digital_sp->name,0,20)."..." : $digital_sp->name; // Shorten name
														
														echo "/> &nbsp; <label for='digitalsp_$digital_sp->ds_id' title='{$digital_sp->name}'><strong>$dspName</strong> <span style='";
														if($mediads->customized == 1){ echo "display: inline;"; } else { echo "display: none;"; }
														echo "' id='dsp_clabel_$digital_sp->ds_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
														
														//echo "<div style='color: #b73232; font-size: 11px; padding-left: 22px;font-style:italic'>Warning: Profile type is different than this media's type!</div>";
														
													//echo "</div>";
												echo "</div>";
												
												echo "<div class='divTableCell' style='padding: 8px; vertical-align: middle;'>";
													
													echo "<div class='XXdspOptions{$digital_sp->ds_id}' style='display: ";
													//if($mediads_rows){ echo "block"; } else { echo "none"; }
													echo "'>";
														
														echo "<input type='button' value='{$mgrlang[media_customize]}' style='margin-left: 4px;";
															if(!$config['settings']['customizer']){ echo "display: none;"; }
														echo "' onclick='load_dsp_details($digital_sp->ds_id);' id='dsp_customize_button_$digital_sp->ds_id' />";
														//echo "<div style='float: left;'><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->";
															echo "<div id='dsp_popup_$digital_sp->ds_id' style='display: none; margin-top: -20px;' class='details_win'>";
																echo "<div class='details_win_inner'>";
																	echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
																	echo "<div id='dsp_popup_".$digital_sp->ds_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
																echo "</div>";
															echo "</div>";
														//echo "</div>";
														echo "<div id='dsp_customizations_{$digital_sp->ds_id}' style='display: none; clear: both;'>";
															echo "Can Auto Create:<input type='text' name='dsp_creatable_{$digital_sp->ds_id}' id='dsp_creatable_{$digital_sp->ds_id}' value='{$autoCreate}' class='dspCreateable' /><br />";														
															echo "Customized:<input type='text' name='dsp_customized_{$digital_sp->ds_id}' id='dsp_customized_{$digital_sp->ds_id}' value='{$mediads->customized}' /><br />";
															echo "License:<input type='text' name='dsp_license_{$digital_sp->ds_id}' id='dsp_license_{$digital_sp->ds_id}' value='{$mediads->license}' /><br />";
															echo "RM License:<input type='text' name='dsp_rm_license_{$digital_sp->ds_id}' id='dsp_rm_license_{$digital_sp->ds_id}' value='{$mediads->rm_license}' /><br />";
															echo "Price:<input type='text' name='dsp_price_{$digital_sp->ds_id}' id='dsp_price_{$digital_sp->ds_id}' value='".trim($cleanvalues->currency_display($mediads->price,0))."' /><br />";
															echo "Credits:<input type='text' name='dsp_credits_{$digital_sp->ds_id}' id='dsp_credits_{$digital_sp->ds_id}' value='{$mediads->credits}' /><br />";
															echo "Quantity:<input type='text' name='dsp_quantity_{$digital_sp->ds_id}' id='dsp_quantity_{$digital_sp->ds_id}' value='{$mediads->quantity}' /><br />";
															echo "Credits Calc:<input type='text' name='dsp_credits_calc_{$digital_sp->ds_id}' id='dsp_credits_calc_{$digital_sp->ds_id}' value='{$mediads->credits_calc}' /><br />";
															echo "Price Calc:<input type='text' name='dsp_price_calc_{$digital_sp->ds_id}' id='dsp_price_calc_{$digital_sp->ds_id}' value='{$mediads->price_calc}' /><br />";
															
															echo "Width:<input type='text' name='dsp_width_{$digital_sp->ds_id}' id='dsp_width_{$digital_sp->ds_id}' value='{$mediads->width}' /><br />";
															echo "Height:<input type='text' name='dsp_height_{$digital_sp->ds_id}' id='dsp_height_{$digital_sp->ds_id}' value='{$mediads->height}' /><br />";
															echo "Format:<input type='text' name='dsp_format_{$digital_sp->ds_id}' id='dsp_format_{$digital_sp->ds_id}' value='{$mediads->format}' /><br />";
															
															//if($digital_sp->dsp_type == 'video')
															//{
																echo "HD:<input type='text' name='dsp_hd_{$digital_sp->ds_id}' id='dsp_hd_{$digital_sp->ds_id}' value='{$mediads->hd}' /><br />";
																echo "Running Time:<input type='text' name='dsp_running_time_{$digital_sp->ds_id}' id='dsp_running_time_{$digital_sp->ds_id}' value='{$mediads->running_time}' /><br />";
																echo "FPS:<input type='text' name='dsp_fps_{$digital_sp->ds_id}' id='dsp_fps_{$digital_sp->ds_id}' value='{$mediads->fps}' /><br />";
															//}
															
															echo "Filename:<input type='text' name='dsp_filename_{$digital_sp->ds_id}' id='dsp_filename_{$digital_sp->ds_id}' value='{$mediads->filename}' /><br />";
															echo "oFilename:<input type='text' name='dsp_ofilename_{$digital_sp->ds_id}' id='dsp_ofilename_{$digital_sp->ds_id}' value='{$mediads->ofilename}' /><br />";														
														echo "</div>";
													echo "</div>";
												echo "</div>";
												
												/*
												echo "<div class='divTableCell' style='padding: 8px; vertical-align: top;'>";
													echo "<input type='hidden' name='dspUseOriginal{$digital_sp->ds_id}' id='dspUseOriginal{$digital_sp->ds_id}' value='{$digital_sp->use_original}' />";
													
													// and $autoCreate
													
													echo "<div class='dspOptions{$digital_sp->ds_id}' style='display: ";
													if($mediads_rows){ echo "block"; } else { echo "none"; }
													echo "'>";
													
														if($digital_sp->use_original)
														{	
															echo "<input type='checkbox' name='deliverOriginal[]' class='dsp_autocreate_checkbox' disabled='disabled' checked='checked' > {$mgrlang[dsp_f_delorg]}";
														}
														elseif($autoCreate)
														{
															echo "<input type='checkbox' name='dsp_autocreate_{$digital_sp->ds_id}' id='dsp_autocreate_{$digital_sp->ds_id}' value='1' ";
																if($mediads->auto_create and $autoCreate){ echo "checked='checked'"; }
															echo " onclick='checkAutocreate({$digital_sp->ds_id});' class='dsp_autocreate_checkbox' /> <label for='dsp_autocreate_{$digital_sp->ds_id}'>{$mgrlang[create_automat]} </label>";
														}
														
													echo "</div>";		
												echo "</div>";
												*/
												
												
												$variationPath = str_replace('originals','variations',$originalVerify['path']);
												
												if($mediads->filename)
												{
													if(file_exists($variationPath.$mediads->filename))
													{
														$varExists = true;
														$fileAttachedBG = '#e4f5ff';
														$fileAttachedBorder = '#bfdced';
														$fileAttachedLang = $mgrlang['file_linked'];
														$dspRolloverTitle = $variationPath.$mediads->filename;
													}
													else
													{
														$varExists = false;
														$fileAttachedBG = '#f5bebe';
														$fileAttachedBorder = '#b40b0b';
														$fileAttachedLang = $mgrlang['media_filemissing'];
														$dspRolloverTitle = $variationPath.$mediads->filename;
													}
												}
												elseif($mediads->external_link)
												{
													// Removed for speed
													/*if(checkExternalFile($mediads->external_link) > 400)
													{
														$varExists = false;
														$fileAttachedBG = '#f5bebe';
														$fileAttachedBorder = '#b40b0b';
														$fileAttachedLang = $mgrlang['media_filemissing'];
														$dspRolloverTitle = $mediads->external_link;
													}
													else
													{	
													*/
														$varExists = true;
														$fileAttachedBG = '#e4f5ff';
														$fileAttachedBorder = '#bfdced';
														$fileAttachedLang = $mgrlang['file_linked'];
														$dspRolloverTitle = $mediads->external_link;
													//}
												}
												
												echo "<div class='divTableCell' style='padding: 8px; vertical-align: middle;'>";
													
													echo "<div class='dspOptions{$digital_sp->ds_id}' style='display: ";
													if($mediads_rows){ echo "block"; } else { echo "none"; }
													echo "'>";
													
														echo "<div id='dspUploadDiv{$digital_sp->ds_id}' style='";
														//if(($mediads->auto_create and !$autoCreateError) or !$mediads_rows){ echo "display: none"; }
														echo "' class='dspUploadDiv'>";
															echo "<input type='hidden' name='dspExternalLink-{$digital_sp->ds_id}' id='dspExternalLink-{$digital_sp->ds_id}' value='{$mediads->external_link}'>";
															echo "<input type='button' value='{$mgrlang[gen_attach_file]}' onclick='revealDSPUploader({$mediaInfo[media_id]},{$digital_sp->ds_id});' style='float: right;";
																if($mediads->filename or $mediads->external_link){ echo "display: none;"; }
															echo "' id='dspUploadButton{$digital_sp->ds_id}' />";
															echo "<div style='padding: 3px 6px 3px 6px; border: 1px solid {$fileAttachedBorder}; background-color: {$fileAttachedBG}; overflow: auto; float: right;";
																if(!$mediads->filename and !$mediads->external_link){ echo "display: none;"; } 
															echo "' id='dspFileAttached{$digital_sp->ds_id}' title='{$dspRolloverTitle}'>{$fileAttachedLang} <input type='button' value='x' style='height: 18px; font-size: 10px; margin-left: 4px; vertical-align: middle;' title='{$mgrlang[gen_short_delete]}' onclick='deleteFileDSP({$mediaInfo[media_id]},{$digital_sp->ds_id});' /></div>";
														echo "</div>";
													echo "</div>";
												echo "</div>";
											
											echo "</div>";
										}
										echo "<input type='hidden' name='originalMediaDS' value='{$originalMediaDS}' />";
									?>
								</div>     
								<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
							</div>
						<?php
							}
						?>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab4_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_prods']; ?>: <br />
								<span><?php echo $mgrlang['media_f_prod_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
								<?php
									if(in_array("pro",$installed_addons))
									{
										$prod_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'products' ORDER BY name");
										$prod_group_rows = mysqli_num_rows($prod_group_result);
										if($prod_group_rows)
										{
								?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
											<?php
												$mediapg_result = mysqli_query($db,"SELECT prodgrp_id FROM {$dbinfo[pre]}media_products WHERE media_id = '{$mediaInfo[media_id]}' AND prodgrp_id != '0'");
												//$mediapg_rows = mysqli_num_rows($mediapg_result);
												while($mediapg = mysqli_fetch_object($mediapg_result))
												{
													$mediaProdGroups[] = $mediapg->prodgrp_id;
												}
												
												
												while($prod_group = mysqli_fetch_object($prod_group_result))
												{
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none'><input type='checkbox' value='$prod_group->gr_id' id='prodgroupcb_$prod_group->gr_id' name='prodgroup[]' ";
													if(@in_array($prod_group->gr_id,$mediaProdGroups)){ echo "checked='checked'"; }
													echo "/> &nbsp; "; if($prod_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' /> "; } echo "<label for='prodgroupcb_$prod_group->gr_id'><strong>" . $prod_group->name . "</strong></label>";	
													
													$prod_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}products.item_name FROM {$dbinfo[pre]}products JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}products.prod_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$prod_group->gr_id' AND {$dbinfo[pre]}products.deleted='0'");
													$prod_groupids_rows = mysqli_num_rows($prod_groupids_result);
													
													if($prod_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprodgp".$prod_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('prodgroup_$prod_group->gr_id','','','','plusminus-prodgp$prod_group->gr_id');\" />";
														echo "<div id=\"prodgroup_$prod_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($prod_groupids = mysqli_fetch_object($prod_groupids_result)){
															echo $prod_groupids->item_name . "$prod_groupids_rows<br />";
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";
											}
										}
										
										echo "<div style='width: 100%; border: 1px solid #EEE;' class='divTable'>";
										
										$prod_result = mysqli_query($db,"SELECT prod_id,item_name,price,credits,product_type FROM {$dbinfo[pre]}products WHERE deleted='0'");
										$prod_rows = mysqli_num_rows($prod_result);
										while($prod = mysqli_fetch_object($prod_result))
										{
											
											$mediaprod_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_products WHERE prod_id = '{$prod->prod_id}' AND media_id = '{$mediaInfo[media_id]}'");
											$mediaprod_rows = mysqli_num_rows($mediaprod_result);
											$mediaprod = mysqli_fetch_object($mediaprod_result);
											
											if($mediaprod_rows){ $originalMediaProd .= $mediaprod->prod_id . ","; }
											
											$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor}; height: 40px'>";											
												echo "<div class='divTableCell' style='padding: 5px 5px 5px 10px; vertical-align: middle;'>";
													echo "<input type='checkbox' value='$prod->prod_id' id='prod_$prod->prod_id' name='proditem[]' ";
													if($mediaprod_rows){ echo "checked='checked'"; }
													echo " style='padding: 0; margin: 0;' /> <label for='prod_$prod->prod_id'>{$prod->item_name} <span style='";
													if($mediaprod->customized == 1){ echo "display: inline;"; } else { echo "display: none;"; }
													echo "' id='prod_clabel_$prod->prod_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
												echo "</div>";
											
												
												echo "<div class='divTableCell' style='padding: 8px 5px 5px 15px; vertical-align: middle;'>";
													if($prod->product_type == '1')
													{
														echo "<input type='button' value='{$mgrlang[media_customize]}' style='";
															if(!$config['settings']['customizer']){ echo "display: none;"; }
														echo "' onclick='load_prod_details($prod->prod_id);' id='prod_customize_button_$prod->prod_id' />";
														echo "<div id='prod_popup_$prod->prod_id' style='display: none; margin-top: -20px;' class='details_win'>";
															echo "<div class='details_win_inner'>";
																echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
																echo "<div id='prod_popup_".$prod->prod_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
															echo "</div>";
														echo "</div>";
													}
												
													echo "<div id='prod_customizations_$prod->prod_id' style='display: none; clear: both;'>";
														echo "Customized:<input type='text' name='prod_customized_$prod->prod_id' id='prod_customized_$prod->prod_id' value='{$mediaprod->customized}' /><br />";
														echo "Price:<input type='text' name='prod_price_$prod->prod_id' id='prod_price_$prod->prod_id' value='{$mediaprod->price}' /><br />";
														echo "Credits:<input type='text' name='prod_credits_$prod->prod_id' id='prod_credits_$prod->prod_id' value='{$mediaprod->credits}' /><br />";
														echo "Quantity:<input type='text' name='prod_quantity_$prod->prod_id' id='prod_quantity_$prod->prod_id' value='{$mediaprod->quantity}' /><br />";
														echo "Credits Calc:<input type='text' name='prod_credits_calc_$prod->prod_id' id='prod_credits_calc_$prod->prod_id' value='{$mediaprod->credits_calc}' /><br />";
														echo "Price Calc:<input type='text' name='prod_price_calc_$prod->prod_id' id='prod_price_calc_$prod->prod_id' value='{$mediaprod->price_calc}' /><br />";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										}
									?>
								</div>
								<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
								<?php echo "<input type='hidden' name='originalMediaProd' value='{$originalMediaProd}' />"; ?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab5_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_prints']; ?>: <br />
								<span><?php echo $mgrlang['media_f_print_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
								<?php
									if(in_array("pro",$installed_addons))
									{
										
										$print_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'prints' ORDER BY name");
										$print_group_rows = mysqli_num_rows($print_group_result);
										if($print_group_rows)
										{
								?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
											<?php
												$mediaprg_result = mysqli_query($db,"SELECT printgrp_id FROM {$dbinfo[pre]}media_prints WHERE media_id = '{$mediaInfo[media_id]}' AND printgrp_id != '0'");
												//$mediapg_rows = mysqli_num_rows($mediapg_result);
												while($mediaprg = mysqli_fetch_object($mediaprg_result))
												{
													$mediaPrintGroups[] = $mediaprg->printgrp_id;
												}
												
												while($print_group = mysqli_fetch_object($print_group_result))
												{
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none'><input type='checkbox' value='$print_group->gr_id' id='printgroupcb_$print_group->gr_id' name='printgroup[]' ";
													if(@in_array($print_group->gr_id,$mediaPrintGroups)){ echo "checked='checked'"; }
													echo " /> &nbsp; "; if($print_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' /> "; } echo "<label for='printgroupcb_$print_group->gr_id'><strong>" . $print_group->name . "</strong></label>";	
													
													$print_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}prints.item_name FROM {$dbinfo[pre]}prints JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}prints.print_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$print_group->gr_id' AND {$dbinfo[pre]}prints.deleted='0'");
													$print_groupids_rows = mysqli_num_rows($print_groupids_result);
													
													if($print_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprintgp".$print_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('printgroup_$print_group->gr_id','','','','plusminus-printgp$print_group->gr_id');\" />";
														echo "<div id=\"printgroup_$print_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($print_groupids = mysqli_fetch_object($print_groupids_result)){
															echo $print_groupids->item_name . "<br />";
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";												
											}
										}
										
										echo "<div style='width: 100%; border: 1px solid #EEE' class='divTable'>";
										
										$print_result = mysqli_query($db,"SELECT print_id,item_name FROM {$dbinfo[pre]}prints WHERE deleted='0'");
										$print_rows = mysqli_num_rows($print_result);
										while($print = mysqli_fetch_object($print_result))
										{
											$mediaprint_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_prints WHERE print_id = '{$print->print_id}' AND media_id = '{$mediaInfo[media_id]}'");
											$mediaprint_rows = mysqli_num_rows($mediaprint_result);
											$mediaprint = mysqli_fetch_object($mediaprint_result);
											
											if($mediaprint_rows){ $originalMediaPrint .= $mediaprint->print_id . ","; }
											
											$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor}; height: 40px'>";											
												echo "<div class='divTableCell' style='padding: 5px 5px 5px 10px; vertical-align: middle;'>";
											
													echo "<input type='checkbox' value='$print->print_id' id='print_$print->print_id' name='printitem[]' ";
													if($mediaprint_rows){ echo "checked='checked'"; }
													echo " style='padding: 0; margin: 0;' /> <label for='print_$print->print_id'>{$print->item_name} <span style='";
													if($mediaprint->customized == 1){ echo "display: inline;"; } else { echo "display: none;"; }
													echo "' id='print_clabel_$print->print_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
													
												echo "</div>";
	
												echo "<div class='divTableCell' style='padding: 8px 5px 5px 15px; vertical-align: middle;'>";
										
													echo "<input type='button' value='{$mgrlang[media_customize]}' style='";
														if(!$config['settings']['customizer']){ echo "display: none;"; }
													echo "' onclick='load_print_details($print->print_id);' id='print_customize_button_$print->print_id' />";
													echo "<div id='print_popup_$print->print_id' style='display: none; margin-top: -22px;' class='details_win'>";
														echo "<div class='details_win_inner'>";
															echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
															echo "<div id='print_popup_".$print->print_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
														echo "</div>";
													echo "</div>";
									
													echo "<div id='print_customizations_$print->print_id' style='display: none; clear: both;'>";
														echo "Customized:<input type='text' name='print_customized_$print->print_id' id='print_customized_$print->print_id' value='{$mediaprint->customized}' /><br />";
														echo "Price:<input type='text' name='print_price_$print->print_id' id='print_price_$print->print_id' value='{$mediaprint->price}' /><br />";
														echo "Credits:<input type='text' name='print_credits_$print->print_id' id='print_credits_$print->print_id' value='{$mediaprint->credits}' /><br />";
														echo "Quantity:<input type='text' name='print_quantity_$print->print_id' id='print_quantity_$print->print_id' value='{$mediaprint->quantity}' /><br />";
														echo "Credits Calc:<input type='text' name='print_credits_calc_$print->print_id' id='print_credits_calc_$print->print_id' value='{$mediaprint->credits_calc}' /><br />";
														echo "Price Calc:<input type='text' name='print_price_calc_$print->print_id' id='print_price_calc_$print->print_id' value='{$mediaprint->price_calc}' /><br />";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										}
										echo "<input type='hidden' name='originalMediaPrint' value='{$originalMediaPrint}' />";
									?>
								</div>
								<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab6_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_colls']; ?>: <br />
								<span><?php echo $mgrlang['media_f_coll']; ?></span>
							</p>
							<ul style="float: left; padding: 0; margin: 0; border: 1px solid #EEE; width: 430px;">                   
							<?php
								$mcoll_result = mysqli_query($db,"SELECT coll_id FROM {$dbinfo[pre]}media_collections WHERE cmedia_id = '{$mediaInfo[media_id]}'");
								//$mtr_rows = mysqli_num_rows($mtr_result);
								while($mcoll = mysqli_fetch_object($mcoll_result))
								{
									$selectedMediaCollections[] = $mcoll->coll_id;
								}
								
								$coll_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}collections WHERE colltype = '2' AND deleted = 0");
								$coll_rows = mysqli_num_rows($coll_result);
								while($coll = mysqli_fetch_object($coll_result))
								{
									$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
									echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$coll->coll_id' id='coll_$coll->coll_id' name='collection[]'";
									if(@in_array($coll->coll_id,$selectedMediaCollections)){ echo " checked='checked'"; }
									echo "/> <label for='coll_$coll->coll_id'>" . $coll->item_name . "</label></li>";	
								}
							?>
							</ul>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab7_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_media_types']; ?>: <br />
								<span><?php echo $mgrlang['media_f_mt_d']; ?></span>
							</p>
							<ul style="float: left; padding: 0; margin: 0; width: 430px; border: 1px solid #EEE;">                   
							<?php
								$mtr_result = mysqli_query($db,"SELECT mt_id FROM {$dbinfo[pre]}media_types_ref WHERE media_id = '{$mediaInfo[media_id]}'");
								//$mtr_rows = mysqli_num_rows($mtr_result);
								while($mtr = mysqli_fetch_object($mtr_result))
								{
									$selectedMediaTypes[] = $mtr->mt_id;
								}
								
								$mt_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_types");
								$mt_rows = mysqli_num_rows($mt_result);
								while($mt = mysqli_fetch_object($mt_result))
								{
									$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
									
									echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$mt->mt_id' id='mt_$mt->mt_id' name='media_types[]'";
									if(@in_array($mt->mt_id,$selectedMediaTypes)){ echo " checked='checked'"; }
									echo "/> <label for='mt_$mt->mt_id'>" . $mt->name . "</label></li>";	
								}
							?>
							</ul>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab8_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_active']; ?>: <br />
								<span><?php echo $mgrlang['gen_active_d']; ?></span>
							</p>
							<input type="checkbox" name="active" value="1" <?php if($mediaInfo['active']){ echo "checked='checked'"; } ?> />
						</div>
						
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_el']; ?>: <br />
								<span><?php echo $mgrlang['media_f_el_d']; ?></span>
							</p>
							<input type="text" name="external_link" value="<?php echo $mediaInfo['external_link']; ?>" style="width: 300px;" />
						</div>
						
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['mediadet_dateadded']; ?>: <br />
								<span><?php echo $mgrlang['media_f_da_d']; ?></span>
							</p>
							<div style="float: left;">
								<?php 
									if($mediaInfo['date_added'] != '0000-00-00 00:00:00')
										$formDateAdded = $dateObj->date_to_form($mediaInfo['date_added']);
								?>
								<input type="hidden" name="added_hour" value="<?php echo $formDateAdded['hour']; ?>" />
								<input type="hidden" name="added_minute" value="<?php echo $formDateAdded['minute']; ?>" />
								<input type="hidden" name="added_second" value="<?php echo $formDateAdded['second']; ?>" />
								<select style="width: 70px;" name="added_year">
									<option value="0000"></option>
									<?php
										for($i=1900; $i<(date("Y")+6); $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateAdded['year'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
								<select style="width: 55px;" name="added_month">
									<option value="00"></option>
									<?php
										for($i=1; $i<13; $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateAdded['month'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
								<select style="width: 55px;" name="added_day">
									<option value="00"></option>
									<?php
										for($i=1; $i<=31; $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateAdded['day'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['mediadet_datecreated']; ?>: <br />
								<span><?php echo $mgrlang['media_f_dc_d']; ?></span>
							</p>
							<div style="float: left;">
								<?php 
									if($mediaInfo['date_created'] != '0000-00-00 00:00:00')
										$formDateCreated = $dateObj->date_to_form($mediaInfo['date_created']);
								?>
								<input type="hidden" name="created_hour" value="<?php echo $formDateCreated['hour']; ?>" />
								<input type="hidden" name="created_minute" value="<?php echo $formDateCreated['minute']; ?>" />
								<input type="hidden" name="created_second" value="<?php echo $formDateCreated['second']; ?>" />
								<select style="width: 70px;" name="created_year">
									<option value="0000"></option>
									<?php
										for($i=1900; $i<(date("Y")+6); $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateCreated['year'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
								<select style="width: 55px;" name="created_month">
									<option value="00"></option>
									<?php
										for($i=1; $i<13; $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateCreated['month'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
								<select style="width: 55px;" name="created_day">
									<option value="00"></option>
									<?php
										for($i=1; $i<=31; $i++){
											if(strlen($i) < 2){
												$dis_i_as = "0$i";
											} else {
												$dis_i_as = $i;
											}
											echo "<option ";
											if($formDateCreated['day'] == $dis_i_as){
												echo "selected";
											}
											echo ">$dis_i_as</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_sn']; ?>: <br />
								<span><?php echo $mgrlang['media_f_sn_d']; ?></span>
							</p>
							<input type="text" name="sortorder" value="<?php echo $mediaInfo['sortorder']; ?>" style="width: 50px;" />
						</div>
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_mr']; ?>:<br />
								<span><?php echo $mgrlang['media_f_mr_d']; ?></span>
							</p>
							<!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->							
							<div style="float: left"><input type="checkbox" name="model_release_status" id="model_release_status" value="1" <?php if($mediaInfo['model_release_status']){ echo "checked='checked'"; } ?>  /></div>
							<div style="float: left; margin-left: 20px;">
								<div id='mrUploader'>Flash Based Uploader</div>
								<div id="mrFile" style="margin-top: 10px; display: <?php if($mediaInfo['model_release_form']){ echo "block"; } else { echo "none" ; } ?>;"><a href="../assets/files/releases/<?php echo $mediaInfo['model_release_form']; ?>"><?php echo $mediaInfo['model_release_form']; ?></a> <a href="javascript:delete_release('mr',<?php echo $mediaInfo['media_id']; ?>);" class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_b_del']; ?></a></div>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_pr']; ?>:<br />
								<span><?php echo $mgrlang['media_f_pr_d']; ?></span>
							</p>
							<!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
							<div style="float: left"><input type="checkbox" name="prop_release_status" id="prop_release_status" value="1" <?php if($mediaInfo['prop_release_status']){ echo "checked='checked'"; } ?>  /></div>
							<div style="float: left; margin-left: 20px;">
								<div id='prUploader' style="display: <?php if($mediaInfo['prop_release_form']){ echo "none"; } else { echo "block" ; } ?>;">Flash Based Uploader</div>
								<div id="prFile" style="margin-top: 10px; display: <?php if($mediaInfo['prop_release_form']){ echo "block"; } else { echo "none" ; } ?>;"><a href="../assets/files/releases/<?php echo $mediaInfo['prop_release_form']; ?>"><?php echo $mediaInfo['prop_release_form']; ?></a> <a href="javascript:delete_release('pr',<?php echo $mediaInfo['media_id']; ?>);" class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_b_del']; ?></a></div>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_copyright']; ?>: <br />
								<span><?php echo $mgrlang['media_f_copyright_d']; ?></span>
							</p>
							<textarea name="copyright" id="copyright" style="width: 300px; height: 100px;"><?php echo $mediaInfo['copyright']; ?></textarea>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_usageres']; ?>: <br />
								<span><?php echo $mgrlang['media_f_usageres_d']; ?></span>
							</p>
							<textarea name="usage_restrictions" id="usage_restrictions" style="width: 300px; height: 100px;"><?php echo $mediaInfo['usage_restrictions']; ?></textarea>
						</div>
						<?php
							if(in_array("pro",$installed_addons))
							{
								$media_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'media' ORDER BY name");
								$media_group_rows = mysqli_num_rows($media_group_result);
								if($media_group_rows)
								{
						?>
							<div class="<?php fs_row_color(); ?>">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_groups']; ?>: <br />
									<span><?php echo $mgrlang['media_f_groups_d']; ?></span>
								</p>
								<?php
									$plangroups = array();
									# FIND THE GROUPS THAT THIS ITEM IS IN
									$media_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = 'media' AND item_id = '{$mediaInfo[media_id]}' AND item_id != 0");
									while($media_groupids = mysqli_fetch_object($media_groupids_result))
									{
										$plangroups[] = $media_groupids->group_id;
									}
									echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
									while($media_group = mysqli_fetch_object($media_group_result))
									{
										//echo "1";
										echo "<li><input type='checkbox' id='grp_$media_group->gr_id' class='permcheckbox' name='setgroups[]' value='$media_group->gr_id' "; if(in_array($media_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($media_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$media_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$media_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$media_group->gr_id'>" . substr($media_group->name,0,30)."</label></li>";
									}
									echo "</ul>";
								?>
							</div>
						<?php
								}
							}
						?>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab9_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_packs']; ?>: <br />
								<span><?php echo $mgrlang['media_f_pack_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
								<?php
									if(in_array("pro",$installed_addons))
									{
										$pack_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'packages' ORDER BY name");
										$pack_group_rows = mysqli_num_rows($pack_group_result);
										if($pack_group_rows)
										{
								?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>
											<?php
												$mediapackg_result = mysqli_query($db,"SELECT packgrp_id,pack_id FROM {$dbinfo[pre]}media_packages WHERE media_id = '{$mediaInfo[media_id]}'");
												//$mediapg_rows = mysqli_num_rows($mediapg_result);
												while($mediapackg = mysqli_fetch_object($mediapackg_result))
												{
													if($mediapackg->pack_id != '0')
													{
														$mediaPackages[] = $mediapackg->pack_id;
													}
													else
													{
														$mediaPackageGroups[] = $mediapackg->packgrp_id;
													}
												}
												
												while($pack_group = mysqli_fetch_object($pack_group_result))
												{	
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none;'><input type='checkbox' value='$pack_group->gr_id' id='packgroupcb_$pack_group->gr_id' name='packgroup[]' ";
													if(@in_array($pack_group->gr_id,$mediaPackageGroups)){ echo "checked='checked'"; }
													echo " /> &nbsp; "; if($pack_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' /> "; } echo "<label for='packgroupcb_$pack_group->gr_id'><strong>" . $pack_group->name . "</strong></label>";	
													
													$pack_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}packages.item_name FROM {$dbinfo[pre]}packages JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}packages.pack_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$pack_group->gr_id' AND {$dbinfo[pre]}packages.deleted='0'");
													$pack_groupids_rows = mysqli_num_rows($pack_groupids_result);
													
													if($pack_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminuspackgp".$pack_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('packgroup_$pack_group->gr_id','','','','plusminus-packgp$pack_group->gr_id');\" />";
														echo "<div id=\"packgroup_$pack_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($pack_groupids = mysqli_fetch_object($pack_groupids_result)){
															echo $pack_groupids->item_name . "<br />";
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";
											}
										}
									?>
								<ul style="margin: 0; border: 1px solid #EEE;">                   
								<?php
									$pack_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}packages WHERE deleted = 0");
									$pack_rows = mysqli_num_rows($pack_result);
									while($pack = mysqli_fetch_object($pack_result))
									{
										$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
										
										echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$pack->pack_id' id='pack_$pack->pack_id' name='packages[]' ";
										if($mediaPackages) { if(in_array($pack->pack_id,$mediaPackages)){ echo "checked='checked'"; }}
										echo " /> <label for='pack_$pack->pack_id'>" . $pack->item_name . "</label></li>";	
									}
								?>
								</ul>
							</div>
						</div>
					</div>
			
			<?php
					echo "</form>";
					echo "</div>";
					echo "<div id='wbfooter' style='padding-left: 30px;'>";
						/*
						$colorPaletteResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}color_palettes WHERE media_id = '{$mediaInfo[media_id]}' ORDER BY cp_id");
						$colorPaletteRows = mysqli_num_rows($colorPaletteResult);
						if($colorPaletteRows)
						{
							echo "<div style='float: left; margin-top: 6px;'><div style='float: left; font-weight: bold; color: #666; padding-right: 4px'>Color Palette:</div>";
							while($colorPalette = mysqli_fetch_array($colorPaletteResult))
							{	
								echo "<div style='float: left; width: 20px; height: 12px; margin-right: 2px; background-color: #{$colorPalette[hex]};' title='#{$colorPalette[hex]}'></div>";
							}
							echo "</div>";
						}
						*/					
						echo "<p style='float: right; margin: 0; padding: 0;'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /><input type='button' value='{$mgrlang[gen_b_save]}' id='saveMediaDetailsButton' class='small_button' onclick='saveMediaDetails();' /></p>";
					echo "</div>";
					echo "<script>";
						echo "update_fsrow('tab3_group');\n";
						echo "load_albums({$mediaInfo[media_id]},{$mediaInfo[owner]});\n";
						echo "load_gals({$mediaInfo[media_id]},0);\n";
						echo "Event.observe('new_keyword_DEFAULT', 'keypress', function(){ checkkey('DEFAULT','{$mediaInfo[media_id]}'); });\n";					
						
						echo "Event.observe('saveDsExternalLink', 'click', function(){ saveExternalDSLink(); });\n";					

						
						if(in_array('multilang',$installed_addons))
						{
							foreach($active_langs as $value)
							{
								$value = strtoupper($value);
								echo "Event.observe('new_keyword_".$value."', 'keypress', function(){ checkkey('".$value."','{$mediaInfo[media_id]}'); });\n";	
							}
						}
					echo "</script>";
			}
			else //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			{
				echo "<div id='wbheader'><p>{$mgrlang[media_det_for_batch]}: ".date('U')."</p></div>";
				echo "<div id='vmessage2' style='display: none; border-bottom: 1px solid #6a0a09; background-color: #a91513; color: #FFFFFF; padding: 10px; font-weight: bold; background-image: url(images/mgr.warning.bg.gif); background-repeat: repeat-x;'><img src='images/mgr.notice.icon.png' style='float: left; width: 30px;' />{$mgrlang[media_size_limit]}: $config[OffsiteStogageLimit]MB</div>";
				echo "<div id='wbbody' style='overflow: auto; padding: 8px; margin: 0;'>";
					echo "<p style='padding: 10px;'>{$mgrlang[media_preimport]}</p>";
					
					echo "<div id='button_bar'>";
						echo "<div class='subsubon' onclick=\"bringtofront('1');\" id='tab1'>$mgrlang[gen_details]</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('2');\" id='tab2'>$mgrlang[gen_tab_galleries]</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('3');\" id='tab3'>$mgrlang[gen_dig_ver]</div>";
						//if($config['settings']['cart'])
						//{
							echo "<div class='subsuboff' onclick=\"bringtofront('4');\" id='tab4'>$mgrlang[gen_prods]</div>";
							echo "<div class='subsuboff' onclick=\"bringtofront('5');\" id='tab5'>$mgrlang[gen_prints]</div>";
							echo "<div class='subsuboff' onclick=\"bringtofront('9');\" id='tab9'>$mgrlang[gen_packs]</div>";
						//}
						echo "<div class='subsuboff' onclick=\"bringtofront('6');\" id='tab6'>$mgrlang[gen_colls]</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('7');\" id='tab7'>$mgrlang[gen_media_types]</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('8');\" id='tab8' style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_tab_advanced]</div>";
					echo "</div>";
					echo "<form name='batch_details_form' id='batch_details_form'>";
						echo "<input type='hidden' name='batch_id' value='".date('U')."' />";
			?>
					<div id="tab1_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_title']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_titleimp_d']; ?></span>
							</p>
							<div class="additional_langs">
								<input type="text" name="title" id="title" style="width: 330px;" maxlength="100" />
								<?php
									if(in_array('multilang',$installed_addons)){
								?>
									&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
									<div id="lang_title" style="display: none;">
									<ul>
									<?php
										foreach($active_langs as $value){
									?>
										<li><input type="text" name="title_<?php echo $value; ?>" id="title_<?php echo $value; ?>" style="width: 330px;" maxlength="100" value="<?php echo @stripslashes($shipping->{"title" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
								<?php
										}
										echo "</ul></div>";
									}
								?>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_description']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_descriptionimp_d']; ?></span>
							</p>
							<div class="additional_langs">
								<textarea name="description" id="description" style="width: 330px; height: 50px; vertical-align: middle"></textarea>
								<?php
									if(in_array('multilang',$installed_addons)){
								?>
									&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
									<div id="lang_description" style="display: none;">
									<ul>
									<?php
										foreach($active_langs as $value){
									?>
										<li><textarea name="description_<?php echo $value; ?>" style="width: 330px; height: 50px; vertical-align: middle"><?php echo @stripslashes($shipping->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
								<?php
										}
										echo "</ul></div>";
									}
								?>
							</div>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_keywords']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['media_f_keywordsimp_d']; ?></span>
							</p>
							<div class="additional_langs">
								<div style="width: 415px;">
									<div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_keywords','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>&nbsp;<input type="text" id="new_keyword_DEFAULT" /> <input type="button" value="Add" onclick="add_keyword('DEFAULT');" style="margin-top: -4px;" /></div>
									<div class="keyword_list" id="keywords_DEFAULT">
										<div style="display: none;" kwlanguage="DEFAULT" id="placeholder_DEFAULT"></div>
									</div>
								</div>
								
								<?php
									if(in_array('multilang',$installed_addons))
									{
								?>
									<div id="lang_keywords" style="display: none;">
									<?php
										foreach($active_langs as $value)
										{
											$value = strtoupper($value);
									?>
										<div style="width: 415px; margin-top: 5px">
											<div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF;"><?php echo strtoupper($value); ?></span>&nbsp;<input type="text" id="new_keyword_<?php echo $value; ?>" /> <input type="button" value="Add" onclick="add_keyword('<?php echo $value; ?>');" style="margin-top: -4px;" /></div>
											<div class="keyword_list" id="keywords_<?php echo $value; ?>">
												<div style="display: none;" kwlanguage="<?php echo $value; ?>" id="placeholder_<?php echo $value; ?>"></div>
											</div>
										</div>
								<?php
										}
										echo "</div>";
									}
								?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab2_group" class="group">
						<div class="<?php fs_row_color(); ?>" style="float: left; margin-bottom: 20px;">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_galls']; ?>: <br />
								<span><?php echo $mgrlang['media_f_galls_d']; ?></span>
							</p>
							<div id="mediaGalleries" style="display: none;">
								
							</div>
							<input type="hidden" name="checkGalLoaded" id="checkGalLoaded" value="0" />
							<div style="float: left; width: 430px;">
								<div style="background-color: #eee; padding: 5px; border: 1px solid #d9d9d9; font-size: 11px; font-weight: bold; text-align: right"><input type="text" style="height: 14px; width: 150px" id="new_gallery_name" /> <input type="button" value="Create" style="margin-top: -4px;" onclick="create_gallery();" /></div>
								<div name="gals" id="gals" style="max-height: 400px; overflow: auto; font-size: 11px; padding: 5px; border: 1px solid #EEE"></div>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab3_group" class="group">                	
						<div class="<?php fs_row_color(); ?>" fsrow="1">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_orgcopy']; ?>:<br />
								<span><?php echo $mgrlang['media_f_orgcopy_d']; ?></span>
							</p>
							<select id="original_copy" name="original_copy" onchange="original_dd();" style="width: 298px;">
								<option value="nfs" <?php if(!$config['settings']['cart']){ echo "selected"; } ?>><?php echo $mgrlang['media_f_hidden']; ?></option>                            
								<?php
									$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses");
									while($license = mysqli_fetch_assoc($licenseResult))
									{
										echo "<option ' value='{$license['lic_purchase_type']}-{$license['license_id']}' ";
										if($license['license_id'] == 1) echo "selected='selected'";
										echo ">{$license[lic_name]}</option>";
									}
								?>
							</select>
						</div>
						<div class="<?php fs_row_color(); ?>" id="rmspan" fsrow='1' style="display: none;">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_rmps']; ?>:<br />
								<span><?php echo $mgrlang['media_f_rmps_d']; ?></span>
							</p>
							<select name="rm_license" id="rm_license" style="width: 298px;">
								<?php
									$rm_result = mysqli_query($db,"SELECT name,rm_id FROM {$dbinfo[pre]}rm_schemes WHERE active = '1' ORDER BY name");
									$rm_rows = mysqli_num_rows($rm_result);
									if($rm_rows and $config['settings']['enable_cbp']){
										while($rm_scheme = mysqli_fetch_object($rm_result)){
											echo "\n<option value='$rm_scheme->rm_id'>".$rm_scheme->name."</option>";
										}
									}
								?>
							</select>
						</div>
						<div class="<?php fs_row_color(); ?>" id="quantity_div" fsrow="1" style="<?php if(!$config['settings']['cart']){ echo "display: none;"; } ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_quantity']; ?>: <br />
								<span><?php echo $mgrlang['leave_blank_quan']; ?></span>
							</p>
							<input type="text" name="quantity" id="quantity" style="width: 90px;" maxlength="50" value="" />
						</div>
						<?php
							if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
							{
						?>
							<div class="<?php fs_row_color(); ?>" id="assign_price_div" fsrow="1">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_price']; ?>: <br />
									<span><?php echo $mgrlang['media_f_price_d']; ?></span>
								</p>
								<div style="float: left;">
									<input type="text" name="price" id="price" style="width: 90px;" maxlength="50" onkeyup="price_preview('dsp');" onblur="update_input_cur('price');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?><br />
									<span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
								</div>
							</div>
						<?php
							}
							if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
							{
						?>
							<div class="<?php fs_row_color(); ?>" id="assign_credit_div" fsrow="1">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_credits']; ?>: <br />
									<span><?php echo $mgrlang['media_f_credits_d']; ?></span>
								</p>
								<input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" onkeyup="credits_preview('dsp');" /><br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
							</div>                    
						<?php
							}
						?>
						
						<?php
							$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE active = '1' AND deleted = '0' ORDER BY sortorder");
							$digital_sp_rows = mysqli_num_rows($digital_sp_result);
							if($digital_sp_rows)
							{
						?>
							<div class="<?php fs_row_color(); ?>" fsrow="1">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['media_f_digsizes']; ?>: <br />
									<span><?php echo $mgrlang['media_f_digsizes_d']; ?></span>
								</p>
								<div style="width: 430px; float: left;">
									
									<?php
										if(in_array("pro",$installed_addons))
										{
									?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
										<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
										<?php
											$digitalg_result = mysqli_query($db,"SELECT dsgrp_id FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id = '{$mediaInfo[media_id]}' AND dsgrp_id != '0'");
											//$mediapg_rows = mysqli_num_rows($mediapg_result);
											while($digitalg = mysqli_fetch_object($digitalg_result))
											{
												$digitalGroups[] = $digitalg->dsgrp_id;
											}
											
											$digital_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'digital_sp' ORDER BY name");
											$digital_group_rows = mysqli_num_rows($digital_group_result);
											while($digital_group = mysqli_fetch_object($digital_group_result))
											{
												//echo "$prod_group->name<br />";
												echo "<li style='list-style:none'><input type='checkbox' value='{$digital_group->gr_id}' id='digitalgroupcb_{$digital_group->gr_id}' name='digitalgroup[]' ";
												if(@in_array($digital_group->gr_id,$digitalGroups)){ echo "checked='checked'"; }
												echo "/> &nbsp; "; if($digital_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$digital_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$digital_group->flagtype' align='absmiddle' /> "; } echo "<label for='digitalgroupcb_{$digital_group->gr_id}'><strong>" . $digital_group->name . "</strong></label>";	
												
												$digital_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}digital_sizes.name FROM {$dbinfo[pre]}digital_sizes JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}digital_sizes.ds_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='{$digital_group->gr_id}' AND {$dbinfo[pre]}digital_sizes.deleted='0'");
												$digital_groupids_rows = mysqli_num_rows($digital_groupids_result);
												
												if($digital_groupids_rows)
												{
													echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdigitalgp".$digital_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('digitalgroup_{$digital_group->gr_id}','','','','plusminus-digitalgp{$digital_group->gr_id}');\" />";
													echo "<div id=\"digitalgroup_{$digital_group->gr_id}\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
													//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
													//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
												
													while($digital_groupids = mysqli_fetch_object($digital_groupids_result)){
														echo $digital_groupids->name . "<br />"; //$digital_groupids_rows
													}										
													echo "</div>";
												}
												echo "</li>";
											}
											
											echo "</ul>";
										}
									?>
									
									<?php										
										echo "<div style='width: 100%; border: 1px solid #EEE' class='divTable'>";
										
										while($digital_sp = mysqli_fetch_object($digital_sp_result))
										{
											$backgroundColor = ($backgroundColor == 'FFF') ? 'f7f7f7' : 'FFF';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor};'>";
												echo "<div class='divTableCell' style='padding: 5px 5px 5px 15px; vertical-align: middle;'>";
													echo "<input type='checkbox' value='$digital_sp->ds_id' name='digitalsp[]' id='digitalsp_$digital_sp->ds_id' style='padding: 0; margin: 0;' /> &nbsp; <label for='digitalsp_$digital_sp->ds_id'><strong>{$digital_sp->name}</strong> <span style='display: none;' id='dsp_clabel_$digital_sp->ds_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
												echo "</div>";
											
												echo "<div class='divTableCell' style='padding: 5px; vertical-align: middle;'>";
													echo "<input type='button' value='{$mgrlang[media_customize]}' style='";
														if(!$config['settings']['customizer']){ echo "display: none;"; }
													echo "' onclick='load_dsp_details($digital_sp->ds_id);' id='dsp_customize_button_$digital_sp->ds_id' />";
													echo "<div id='dsp_popup_$digital_sp->ds_id' style='display: none; margin-top: -20px;' class='details_win'>";
														echo "<div class='details_win_inner'>";
															echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
															echo "<div id='dsp_popup_".$digital_sp->ds_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
														echo "</div>";
													echo "</div>";
													echo "<div id='dsp_customizations_$digital_sp->ds_id' style='display: none; clear: both;'>";
														echo "Customized:<input type='text' name='dsp_customized_$digital_sp->ds_id' id='dsp_customized_$digital_sp->ds_id' value='0' /><br />";
														echo "License:<input type='text' name='dsp_license_$digital_sp->ds_id' id='dsp_license_$digital_sp->ds_id' value='' /><br />";
														echo "RM License:<input type='text' name='dsp_rm_license_$digital_sp->ds_id' id='dsp_rm_license_$digital_sp->ds_id' value='' /><br />";
														echo "Price:<input type='text' name='dsp_price_$digital_sp->ds_id' id='dsp_price_$digital_sp->ds_id' value='' /><br />";
														echo "Credits:<input type='text' name='dsp_credits_$digital_sp->ds_id' id='dsp_credits_$digital_sp->ds_id' value='' /><br />";
														echo "Quantity:<input type='text' name='dsp_quantity_$digital_sp->ds_id' id='dsp_quantity_$digital_sp->ds_id' value='' /><br />";
														echo "Credits Calc:<input type='text' name='dsp_credits_calc_$digital_sp->ds_id' id='dsp_credits_calc_$digital_sp->ds_id' value='' /><br />";
														echo "Price Calc:<input type='text' name='dsp_price_calc_$digital_sp->ds_id' id='dsp_price_calc_$digital_sp->ds_id' value='' /><br />";
														
														echo "Width:<input type='text' name='dsp_width_{$digital_sp->ds_id}' id='dsp_width_{$digital_sp->ds_id}' value='' /><br />";
														echo "Height:<input type='text' name='dsp_height_{$digital_sp->ds_id}' id='dsp_height_{$digital_sp->ds_id}' value='' /><br />";
														echo "Format:<input type='text' name='dsp_format_{$digital_sp->ds_id}' id='dsp_format_{$digital_sp->ds_id}' value='' /><br />";
														
														//if($digital_sp->dsp_type == 'video')
														//{
															echo "HD:<input type='text' name='dsp_hd_{$digital_sp->ds_id}' id='dsp_hd_{$digital_sp->ds_id}' value='' /><br />";
															echo "Running Time:<input type='text' name='dsp_running_time_{$digital_sp->ds_id}' id='dsp_running_time_{$digital_sp->ds_id}' value='' /><br />";
															echo "FPS:<input type='text' name='dsp_fps_{$digital_sp->ds_id}' id='dsp_fps_{$digital_sp->ds_id}' value='' /><br />";
														//}
														
													echo "</div>";
												echo "</div>";
											echo "</div>";
										}
									?>
									</div>
									<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
								</div>
							</div>
						<?php
							}
						?>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab4_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_prods']; ?>: <br />
								<span><?php echo $mgrlang['media_f_prod_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
								<?php
									if(in_array("pro",$installed_addons))
									{
										$prod_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'products' ORDER BY name");
										$prod_group_rows = mysqli_num_rows($prod_group_result);
										if($prod_group_rows)
										{
								?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
											<?php
												
												while($prod_group = mysqli_fetch_object($prod_group_result))
												{
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none'><input type='checkbox' value='$prod_group->gr_id' id='prodgroupcb_$prod_group->gr_id' name='prodgroup[]' /> &nbsp; "; if($prod_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' /> "; } echo "<label for='prodgroupcb_$prod_group->gr_id'><strong>" . $prod_group->name . "</strong></label>";	
													
													$prod_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}products.item_name FROM {$dbinfo[pre]}products JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}products.prod_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$prod_group->gr_id' AND {$dbinfo[pre]}products.deleted='0'");
													$prod_groupids_rows = mysqli_num_rows($prod_groupids_result);
													
													if($prod_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprodgp".$prod_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('prodgroup_$prod_group->gr_id','','','','plusminus-prodgp$prod_group->gr_id');\" />";
														echo "<div id=\"prodgroup_$prod_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($prod_groupids = mysqli_fetch_object($prod_groupids_result)){
															echo $prod_groupids->item_name . "$prod_groupids_rows<br />";
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";
											}
										}
										
										echo "<div style='width: 100%; border: 1px solid #EEE' class='divTable'>";
										
										$prod_result = mysqli_query($db,"SELECT prod_id,item_name,price,credits,product_type FROM {$dbinfo[pre]}products WHERE deleted='0'");
										$prod_rows = mysqli_num_rows($prod_result);
										while($prod = mysqli_fetch_object($prod_result))
										{
											$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor}; height: 40px'>";											
												echo "<div class='divTableCell' style='padding: 5px 5px 5px 10px; vertical-align: middle;'>";
													echo "<input type='checkbox' value='$prod->prod_id' id='prod_$prod->prod_id' name='proditem[]' style='padding: 0; margin: 0;' /> <label for='prod_$prod->prod_id'>{$prod->item_name} <span style='display: none;' id='prod_clabel_$prod->prod_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
												echo "</div>";
												
												echo "<div class='divTableCell' style='padding: 8px 5px 5px 15px; vertical-align: middle;'>";
													if($prod->product_type == '1')
													{
														echo "<input type='button' value='{$mgrlang[media_customize]}' style='";
															if(!$config['settings']['customizer']){ echo "display: none;"; }
														echo "' onclick='load_prod_details($prod->prod_id);' id='prod_customize_button_$prod->prod_id' />";
														echo "<div id='prod_popup_$prod->prod_id' style='display: none; margin-top: -22px;' class='details_win'>";
															echo "<div class='details_win_inner'>";
																echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
																echo "<div id='prod_popup_".$prod->prod_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
															echo "</div>";
														echo "</div>";
													}
												
													echo "<div id='prod_customizations_$prod->prod_id' style='display: none; clear: both;'>";
														echo "Customized:<input type='text' name='prod_customized_$prod->prod_id' id='prod_customized_$prod->prod_id' value='0' /><br />";
														echo "Price:<input type='text' name='prod_price_$prod->prod_id' id='prod_price_$prod->prod_id' value='' /><br />";
														echo "Credits:<input type='text' name='prod_credits_$prod->prod_id' id='prod_credits_$prod->prod_id' value='' /><br />";
														echo "Quantity:<input type='text' name='prod_quantity_$prod->prod_id' id='prod_quantity_$prod->prod_id' value='' /><br />";
														echo "Credits Calc:<input type='text' name='prod_credits_calc_$prod->prod_id' id='prod_credits_calc_$prod->prod_id' value='' /><br />";
														echo "Price Calc:<input type='text' name='prod_price_calc_$prod->prod_id' id='prod_price_calc_$prod->prod_id' value='' /><br />";
													echo "</div>";
												echo "</div>";											
											echo "</div>";
										}
									?>
								</div>
								<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab5_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_prints']; ?>: <br />
								<span><?php echo $mgrlang['media_f_print_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
								<?php
									if(in_array("pro",$installed_addons))
									{
										$print_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'prints' ORDER BY name");
										$print_group_rows = mysqli_num_rows($print_group_result);
										if($print_group_rows)
										{
								?>
										<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
											<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>                  
											<?php
												
												while($print_group = mysqli_fetch_object($print_group_result))
												{
													//echo "$prod_group->name<br />";
													echo "<li style='list-style:none'><input type='checkbox' value='$print_group->gr_id' id='printgroupcb_$print_group->gr_id' name='printgroup[]' /> &nbsp; "; if($print_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' /> "; } echo "<label for='printgroupcb_$print_group->gr_id'><strong>" . $print_group->name . "</strong></label>";	
													
													$print_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}prints.item_name FROM {$dbinfo[pre]}prints JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}prints.print_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$print_group->gr_id' AND {$dbinfo[pre]}prints.deleted='0'");
													$print_groupids_rows = mysqli_num_rows($print_groupids_result);
													
													if($print_groupids_rows)
													{
														echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprintgp".$print_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('printgroup_$print_group->gr_id','','','','plusminus-printgp$print_group->gr_id');\" />";
														echo "<div id=\"printgroup_$print_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
														//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
														//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
													
														while($print_groupids = mysqli_fetch_object($print_groupids_result)){
															echo $print_groupids->item_name . "<br />";
														}										
														echo "</div>";
													}
													echo "</li>";
												}
												
												echo "</ul>";
											}
										}
										
										echo "<div style='width: 100%; border: 1px solid #EEE' class='divTable'>";
										
										$print_result = mysqli_query($db,"SELECT print_id,item_name FROM {$dbinfo[pre]}prints WHERE deleted='0'");
										$print_rows = mysqli_num_rows($print_result);
										while($print = mysqli_fetch_object($print_result))
										{
											$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
											
											echo "<div class='divTableRow' style='background-color: #{$backgroundColor}; height: 40px'>";											
												echo "<div class='divTableCell' style='padding: 5px 5px 5px 10px; vertical-align: middle;'>";
													echo "<input type='checkbox' value='$print->print_id' id='print_$print->print_id' name='printitem[]' style='padding: 0; margin: 0;' /> <label for='print_$print->print_id'>{$print->item_name} <span style='display: none;' id='print_clabel_$print->print_id' style='color: #949494'><img src='./images/mgr.tiny.star.1.png' title='{$mgrlang[media_customized]}' style='vertical-align: middle; margin-top: -3px;' /></span></label>";
												echo "</div>";
											
												echo "<div class='divTableCell' style='padding: 8px 5px 5px 15px; vertical-align: middle;'>";
													echo "<input type='button' value='{$mgrlang[media_customize]}' style='";
														if(!$config['settings']['customizer']){ echo "display: none;"; }
													echo "' onclick='load_print_details($print->print_id);' id='print_customize_button_$print->print_id' />";
													echo "<div id='print_popup_$print->print_id' style='display: none; margin-top: -22px;' class='details_win'>";
														echo "<div class='details_win_inner'>";
															echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
															echo "<div id='print_popup_".$print->print_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
														echo "</div>";
													echo "</div>";
												echo "</div>";
												
												echo "<div id='print_customizations_$print->print_id' style='display: none; clear: both;'>";
													echo "Customized:<input type='text' name='print_customized_$print->print_id' id='print_customized_$print->print_id' value='0' /><br />";
													echo "Price:<input type='text' name='print_price_$print->print_id' id='print_price_$print->print_id' value='' /><br />";
													echo "Credits:<input type='text' name='print_credits_$print->print_id' id='print_credits_$print->print_id' value='' /><br />";
													echo "Quantity:<input type='text' name='print_quantity_$print->print_id' id='print_quantity_$print->print_id' value='' /><br />";
													echo "Credits Calc:<input type='text' name='print_credits_calc_$print->print_id' id='print_credits_calc_$print->print_id' value='' /><br />";
													echo "Price Calc:<input type='text' name='print_price_calc_$print->print_id' id='print_price_calc_$print->print_id' value='' /><br />";
												echo "</div>";
												
											echo "</div>";
											
										}
									?>
								</div>
								<?php if($config['settings']['customizer']){ ?><p style="white-space: nowrap; margin-top: 10px; font-weight: normal; font-size: 11px;"><img src="./images/mgr.tiny.star.1.png" style="vertical-align: middle" /> = <?php echo $mgrlang['media_pd_customized']; ?></p><?php } ?>
							</div>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab6_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_colls']; ?>: <br />
								<span><?php echo $mgrlang['media_f_coll']; ?></span>
							</p>
							<ul style="float: left; padding: 0; margin: 0; width: 430px; border: 1px solid #EEE">                   
							<?php
								$coll_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}collections WHERE colltype = '2' AND deleted = 0");
								$coll_rows = mysqli_num_rows($coll_result);
								while($coll = mysqli_fetch_object($coll_result))
								{
									$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
									echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$coll->coll_id' id='coll_$coll->coll_id' name='collection[]' /> <label for='coll_$coll->coll_id'>" . $coll->item_name . "</label></li>";	
								}
							?>
							</ul>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab7_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_media_types']; ?>: <br />
								<span><?php echo $mgrlang['media_f_mt_d']; ?></span>
							</p>
							<ul style="float: left; padding: 0; margin: 0; width: 430px; border: 1px solid #EEE">                   
							<?php
								$mt_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_types");
								$mt_rows = mysqli_num_rows($mt_result);
								while($mt = mysqli_fetch_object($mt_result))
								{
									$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
									echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$mt->mt_id' id='mt_$mt->mt_id' name='media_types[]' /> <label for='mt_$mt->mt_id'>" . $mt->name . "</label></li>";	
								}
							?>
							</ul>
						</div>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab8_group" class="group">
						<?php
						/*
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								Read Metadata: <br />
								<span>Read and import the metadata from your photos if it is available.</span>
							</p>
							<input type="checkbox" name="metadata" value="1" checked="checked" />
						</div>
						*/
						?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_keeporg']; ?>: <br />
								<span><?php echo $mgrlang['media_f_keeporg_d']; ?></span>
							</p>
							<input type="checkbox" name="keep_originals" value="1" checked="checked" />
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_sn']; ?>: <br />
								<span><?php echo $mgrlang['media_f_sn_d']; ?></span>
							</p>
							<input type="text" name="sortorder" value="0" />
						</div>
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_mr']; ?>:<br />
								<span><?php echo $mgrlang['media_f_mr_d']; ?></span>
							</p>
							<input type="checkbox" name="model_release_status" id="model_release_status" value="1" />
						</div>
						<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_pr']; ?>:<br />
								<span><?php echo $mgrlang['media_f_pr_d']; ?></span>
							</p>
							<input type="checkbox" name="prop_release_status" id="prop_release_status" value="1" />
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_copyright']; ?>: <br />
								<span><?php echo $mgrlang['media_f_copyright_d']; ?></span>
							</p>
							<textarea name="copyright" id="copyright" style="width: 300px; height: 100px;"></textarea>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['media_f_usageres']; ?>: <br />
								<span><?php echo $mgrlang['media_f_usageres_d']; ?></span>
							</p>
							<textarea name="usage_restrictions" id="usage_restrictions" style="width: 300px; height: 100px;"></textarea>
						</div>
						<?php
							if(in_array("pro",$installed_addons))
							{
								$media_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'media' ORDER BY name");
								$media_group_rows = mysqli_num_rows($media_group_result);
							
								if($media_group_rows)
								{
						?>
							<div class="<?php fs_row_color(); ?>">
								<img src="images/mgr.ast.off.gif" class="ast" /></td>
								<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['gen_b_grps']; ?>: <br />
									<span><?php echo $mgrlang['media_f_grp_d']; ?></span>
								</p>
								<ul style="padding: 10px; margin: 0; float: left;"> 
								<?php
									while($media_group = mysqli_fetch_object($media_group_result))
									{
										echo "<li style='list-style:none'><input type='checkbox' value='{$media_group->gr_id}' id='mediagrp_{$media_group->gr_id}' name='mediagroups[]' /> &nbsp; "; if($media_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/{$media_group->flagtype}' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/{$media_group->flagtype}' align='absmiddle' /> "; } echo "<label for='mediagrp_{$media_group->gr_id}'><strong>" . $media_group->name . "</strong></label>";	
									}
								?>
								</ul>
							</div>
						<?php
								}
							}
						?>
					</div>
					
					<?php $row_color = 0; ?>
					<div id="tab9_group" class="group">
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" /></td>
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_packs']; ?>: <br />
								<span><?php echo $mgrlang['media_f_pack_d']; ?></span>
							</p>
							<div style="width: 430px; float: left;">
									<?php
										if(in_array("pro",$installed_addons))
										{
											$pack_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'packages' ORDER BY name");
											$pack_group_rows = mysqli_num_rows($pack_group_result);
											if($pack_group_rows)
											{
									?>
											<ul style="padding: 10px; margin: 0 0 10px 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
												<li style='list-style:none; font-weight: bold;'><?php echo $mgrlang['gen_b_grps']; ?></li>
												<?php
													
													while($pack_group = mysqli_fetch_object($pack_group_result))
													{
														//echo "$prod_group->name<br />";
														echo "<li style='list-style:none'><input type='checkbox' value='$pack_group->gr_id' id='packgroupcb_$pack_group->gr_id' name='packgroup[]' /> &nbsp; "; if($pack_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' /> "; } echo "<label for='packgroupcb_$pack_group->gr_id'><strong>" . $pack_group->name . "</strong></label>";	
														
														$pack_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}packages.item_name FROM {$dbinfo[pre]}packages JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}packages.pack_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$pack_group->gr_id' AND {$dbinfo[pre]}packages.deleted='0'");
														$pack_groupids_rows = mysqli_num_rows($pack_groupids_result);
														
														if($pack_groupids_rows)
														{
															echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminuspackgp".$pack_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('packgroup_$pack_group->gr_id','','','','plusminus-packgp$pack_group->gr_id');\" />";
															echo "<div id=\"packgroup_$pack_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
															
															//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
															//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
														
															while($pack_groupids = mysqli_fetch_object($pack_groupids_result)){
																echo $pack_groupids->item_name . "<br />";
															}										
															echo "</div>";
														}
														echo "</li>";
													}
													
													echo "</ul>";
												}
											}
									?>
								<ul style="border: 1px solid #EEE">                   
								<?php
									$pack_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}packages WHERE deleted = 0");
									$pack_rows = mysqli_num_rows($pack_result);
									while($pack = mysqli_fetch_object($pack_result))
									{
										$backgroundColor = ($backgroundColor == 'f7f7f7') ? 'FFF' : 'f7f7f7';
										echo "<li style='list-style:none; background-color: #{$backgroundColor}; height: 30px; padding: 5px 5px 5px 10px; vertical-align: middle'><input type='checkbox' value='$pack->pack_id' id='pack_$pack->pack_id' name='packages[]' /> <label for='pack_$pack->pack_id'>" . $pack->item_name . "</label></li>";	
									}
								?>
								</ul>
							</div>
						</div>
					</div>
			<?php		
					//echo "<div class='more_options' style='width: 708px' id='global'><br /><br /><br /><br /><br /><br /><br /><br />";
					//echo "</div>";
				
				echo "</form>";
				echo "</div>";
				echo "<div id='wbfooter' style='padding-left: 30px;'>";
				/*
				echo "
						<p style='float: left; margin: 0; padding: 8px; border: 1px solid #c4c4c4; background-color: #eee'>
							<input type='button' value='Save Current Settings' class='small_button' style='height: 20px; font-size: 10px;' onclick='start_importing();' />
							<select name='' style='height: 20px; font-size: 10px; padding: 1px;'>
								<option>Load Profile...</option>
							</select>
							<!--
							<input type='button' value='Delete' class='small_button' style='height: 20px; font-size: 10px;' />
							<input type='button' value='Set As Default' class='small_button' style='height: 20px; font-size: 10px;' />
							-->
						</p>";
				*/
				echo "<p style='float: right; margin: 0; padding: 0;'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /><input type='button' value='{$mgrlang[start_importing]}' class='small_button' onclick='start_importing();' /></p></div>";
				echo "<script>";
					echo "update_fsrow('tab3_group');\n";
					echo "load_gals();\n";
					echo "checkFileSizeRestrictions();\n";
					echo "Event.observe('new_keyword_DEFAULT', 'keypress', function(){ checkkey('DEFAULT'); });\n";
					echo "Event.observe('new_gallery_name', 'keypress', checkkeygallery);\n";
					
					if(in_array('multilang',$installed_addons))
					{
						foreach($active_langs as $value)
						{
							$value = strtoupper($value);
							echo "Event.observe('new_keyword_".$value."', 'keypress', function(){ checkkey('".$value."'); });\n";	
						}
					}
				echo "</script>";
			}
		break;
	}	
?>
