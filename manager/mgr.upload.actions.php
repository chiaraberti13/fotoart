<?php
	###################################################################
	####	MANAGER UPLOAD ACTIONS PAGE                            ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 12-9-2011                                     ####
	####	Modified: 12-9-2011                                    #### 
	###################################################################
	
	//sleep(3);
	
	//require_once('mgr.security.php');	The security check file is left out of this file for flash based uploaders
	
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	//echo md5($config['settings']['serial_number']); exit;
	
	if($_REQUEST['pass'] != md5($config['settings']['serial_number']))
	{
		echo "You do not have access to view this file.";
		exit;
	}
	
	require_once('../assets/classes/imagetools.php');				# INCLUDE IMAGETOOLS CLASS
	require_once('../assets/classes/mediatools.php');				# MEDIATOOLS CLASS	
	require_once('../assets/includes/clean.data.php');				# CLEAN DATA
	require_once('../assets/classes/colors.php');				# INCLUDE COLORS CLASS
	
	// Activation code check for security
	switch($_REQUEST['mode'])
	{	
		case "upload_thumb":
			if($_SESSION['admin_user']['admin_id'] != "DEMO")
			{
				
				$media = new mediaTools($mediaID);
				$mediaInfo = $media->getMediaInfoFromDB();			
				$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
				
				if($folderInfo['encrypted'])
				{
					$folderName = $folderInfo['enc_name'];
				}
				else
				{
					$folderName = $folderInfo['name'];
				}
				
				$folderPath = "{$config[settings][library_path]}/{$folderName}/";
				
				
				// Temp filename
				$temp_filename = clean_filename(strtolower($_FILES['Filedata']['name']));
				$temp_filename_parts = explode(".",$temp_filename);
				$temp_filename_ext = strtolower(array_pop($temp_filename_parts));
				
				// Original filename
				$clean_filename = $mediaInfo['filename'];
				$filename = explode(".",$clean_filename);
				$filename_ext = strtolower(array_pop($filename));				
				$filename_glued = implode(".",$filename);
				
				$temp_file_path = realpath("../assets/tmp/")."/".$temp_filename;
				
				// Move the uploaded file so we can work with it
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $temp_file_path);
				
				/*
				$temp_filename = strtolower($_FILES['Filedata']['name']);
				$temp_array = explode(".",$temp_filename);
				$logo_extension = $temp_array[count($temp_array)-1];
				$logo_filename = "main.logo." . $logo_extension;
				move_uploaded_file($_FILES['Filedata']['tmp_name'], "../assets/logos/".$logo_filename);
				
				
				$decoded_path = base64_decode($_POST['filename']);
				$path_wo_filename = dirname($decoded_path);
				$filename_only = basename($decoded_path);
				$clean_filename = clean_filename($filename_only);
				
				$filename = explode(".",$clean_filename);
				$filename_ext = strtolower(array_pop($filename));				
				$filename_glued = implode(".",$filename);
				
				$mimetype = getMimeType($decoded_path);
				
				$status = '1';
				$errormessage = array();
				$errorcode = '0';
				$thumbstatus = '0';
				$thumbpath = '';
				*/
				
				
				# IF GD OR IMAGEMAGIK
				$creatable_filetypes = getCreatableFormats();
					
				# CALCULATE THE MEMORY NEEDED ONLY IF IT IS A CREATABLE FORMAT
				if(in_array(strtolower($temp_filename_ext),$creatable_filetypes))
				{
					# FIGURE MEMORY NEEDED
					$mem_needed = figure_memory_needed($temp_file_path);
					if(ini_get("memory_limit")){
						$memory_limit = ini_get("memory_limit");
					} else {
						$memory_limit = $config['DefaultMemory'];
					}
					# IF IMAGEMAGICK ALLOW TWEAKED MEMORY LIMIT
					if(class_exists('Imagick') and $config['settings']['imageproc'] == 2)
					{
						$memory_limit = $config['DefaultMemory'];
					}
				}
	
				$icon_image = $folderPath . "icons/icon_" . $filename_glued . ".jpg";
				$icon_image_name = "icon_{$filename_glued}.jpg";
				$thumb_image = $folderPath . "thumbs/thumb_" . $filename_glued . ".jpg";
				$thumb_image_name = "thumb_{$filename_glued}.jpg";
				$sample_image = $folderPath . "samples/sample_" . $filename_glued . ".jpg";
				$sample_image_name = "sample_{$filename_glued}.jpg";
					
				# CHECK FOR EXISTING ICON
				if(file_exists($temp_file_path))
				{
					# CHECK TO SEE IF ONE CAN BE CREATED
					if(in_array(strtolower($temp_filename_ext),$creatable_filetypes))
					{
						# CHECK THE MEMORY NEEDED TO CREATE IT
						if($memory_limit > $mem_needed){
							// CREATE ICON
							$image = new imagetools($temp_file_path);
							$image->setSize($config['IconDefaultSize']);
							$image->setQuality($config['SaveThumbQuality']);
							$image->createImage(0,$icon_image);
							// CREATE THUMB
							$image->setSize($config['ThumbDefaultSize']);
							$image->setQuality($config['SaveThumbQuality']);
							$image->createImage(0,$thumb_image);
							
							@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}color_palettes WHERE media_id = '{$mediaID}'"); // Delete old color palette first

							if($config['cpResults'] > 0)
							{
								$colorPalette = new GetMostCommonColors();
								$colors = $colorPalette->Get_Color($thumb_image, $config['cpResults'], $config['cpReduceBrightness'], $config['cpReduceGradients'], $config['cpDelta']);
							}
							
							if(count($colors) > 0)
							{
								// Save color palette
								foreach($colors as $hex => $percentage)
								{
									if($percentage > 0)
									{
										$percentage = round($percentage,6);
										$rgb = html2rgb($hex);
										
										mysqli_query($db,
										"
											INSERT INTO {$dbinfo[pre]}color_palettes (
											media_id,
											hex,
											red,
											green,
											blue,
											percentage
											) VALUES (
											'{$mediaID}',
											'{$hex}',
											'{$rgb[red]}',
											'{$rgb[green]}',
											'{$rgb[blue]}',
											'{$percentage}')
										");
									}
								}
							}
							
							// CREATE SAMPLE
							$image->setSize($config['SampleDefaultSize']);
							$image->setQuality($config['SaveSampleQuality']);
							$image->createImage(0,$sample_image);
						}
						else
						{
							$status = '0';
							$errormessage[] = $mgrlang['gen_not_enough_mem'];
						}
					}
					else
					{
						$status = '0';
						//$errormessage[] = 'An icon image cannot be automatically created from this filetype: ' . $filename_ext;
					}
				}			
				
				$icon_filesize = filesize($icon_image);
				$icon_size = getimagesize($icon_image);
				
				$thumb_filesize = filesize($thumb_image);
				$thumb_size = getimagesize($thumb_image);
				
				$sample_filesize = filesize($sample_image);
				$sample_size = getimagesize($sample_image);
				
				if(file_exists($icon_image))
				{
					if($thumbInfo = $media->getThumbInfoFromDB()) // Thumb exists
					{
						// Update thumb
						mysqli_query($db,
							"
							UPDATE {$dbinfo[pre]}media_thumbnails SET 
							thumb_filename='{$thumb_image_name}',
							thumb_width='{$thumb_size[0]}',
							thumb_height='{$thumb_size[1]}',
							thumb_filesize='{$thumb_filesize}'
							WHERE media_id = '{$mediaID}' 
							AND thumbtype = 'thumb'
							"
						);
						
						// Update icon
						mysqli_query($db,
							"
							UPDATE {$dbinfo[pre]}media_thumbnails SET 
							thumb_filename='{$icon_image_name}',
							thumb_width='{$icon_size[0]}',
							thumb_height='{$icon_size[1]}',
							thumb_filesize='{$icon_filesize}'
							WHERE media_id = '{$mediaID}' 
							AND thumbtype = 'icon'
							"
						);
					}
					else
					{
						// No sample - upload and create
						
						# INSERT THUMB INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}media_thumbnails (
								media_id,
								thumbtype,
								thumb_filename,
								thumb_width,
								thumb_height,
								thumb_filesize
								) VALUES (
								'{$mediaID}',
								'thumb',
								'{$thumb_image_name}',
								'{$thumb_size[0]}',
								'{$thumb_size[1]}',
								'{$thumb_filesize}'
								)";
						$result = mysqli_query($db,$sql);
						$thumbSaveID = mysqli_insert_id($db);
						
						# INSERT ICON INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}media_thumbnails (
								media_id,
								thumbtype,
								thumb_filename,
								thumb_width,
								thumb_height,
								thumb_filesize
								) VALUES (
								'{$mediaID}',
								'icon',
								'{$icon_image_name}',
								'{$icon_size[0]}',
								'{$icon_size[1]}',
								'{$icon_filesize}'
								)";
						$result = mysqli_query($db,$sql);
						$iconSaveID = mysqli_insert_id($db);
					}
				}
				
				if(file_exists($sample_image))
				{
					if($sampleInfo = $media->getSampleInfoFromDB()) // Sample exists
					{
						// Update sample
						mysqli_query($db,
							"
							UPDATE {$dbinfo[pre]}media_samples SET 
							sample_filename='{$sample_image_name}',
							sample_width='{$sample_size[0]}',
							sample_height='{$sample_size[1]}',
							sample_filesize='{$sample_filesize}'
							WHERE media_id = '{$mediaID}'
							"
						);
					}
					else
					{
						// No sample - upload and create
						# INSERT SAMPLE INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}media_samples (
								media_id,
								sample_filename,
								sample_width,
								sample_height,
								sample_filesize
								) VALUES (
								'{$mediaID}',
								'{$sample_image_name}',
								'{$sample_size[0]}',
								'{$sample_size[1]}',
								'{$sample_filesize}'
								)";
						$result = mysqli_query($db,$sql);
						$thumbSaveID = mysqli_insert_id($db);
					}
				}
				
				// Remove mgr caches
				
				if($cacheA = glob("../assets/cache/id{$mediaID}-*"))
				{
					foreach($cacheA as $filename)
						@unlink($filename);
				}
				
				if($cacheB = glob("../assets/cache/id{$encCacheID}-*"))
				{
					// Remove public caches	
					$encCacheID = k_encrypt($mediaID);
					foreach($cacheB as $filename)
						@unlink($filename);
				}	
			}
		break;
		
		case "upload_video_sample":
			if($_SESSION['admin_user']['admin_id'] != "DEMO")
			{
				$media = new mediaTools($mediaID);
				$mediaInfo = $media->getMediaInfoFromDB();
				$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
				
				if($folderInfo['encrypted'])
				{
					$folderName = $folderInfo['enc_name'];
				}
				else
				{
					$folderName = $folderInfo['name'];
				}
				
				//print_r($folderInfo); exit;
				
				$folderPath = "{$config[settings][library_path]}/{$folderName}/samples/";
				
				//echo $folderPath; exit;
				
				$newFilename = basefilename($mediaInfo['filename']);
				
				if($vidSampleInfo = $media->getVidSampleInfoFromDB())
				{
					// It already exists - delete the old one first	
					@unlink($folderPath.$vidSampleInfo['vidsample_filename']);
				}
				
				$temp_filename = strtolower($_FILES['Filedata']['name']);
				$temp_array = explode(".",$temp_filename);
				$video_extension = $temp_array[count($temp_array)-1];
				$video_filename = "video_".$newFilename.".".$video_extension;
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $folderPath.$video_filename);
				
				if($vidSampleInfo) // Update
				{
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}media_vidsamples SET 
								media_id='{$mediaID}',
								vidsampletype='sample',
								vidsample_filename='{$video_filename}',
								vidsample_width='0',
								vidsample_height='0',
								vidsample_filesize='{}',
								vidsample_extension='{$video_extension}'
								WHERE media_id  = '{$mediaID}'
								AND vidsampletype = 'sample'";
					$result = mysqli_query($db,$sql);
				}
				else // Insert
				{
					# INSERT ICON INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_vidsamples (
							media_id,
							vidsampletype,
							vidsample_filename,
							vidsample_width,
							vidsample_height,
							vidsample_filesize,
							vidsample_extension
							) VALUES (
							'{$mediaID}',
							'sample',
							'{$video_filename}',
							'0',
							'0',
							'{}',
							'{$video_extension}'
							)";
					$result = mysqli_query($db,$sql);
					$iconSaveID = mysqli_insert_id($db);
				}
			}
		break;
		case "upload_dsp_file":
			if($_SESSION['admin_user']['admin_id'] != "DEMO")
			{
				$folderPath = "../assets/tmp/";
				
				$temp_filename = strtolower($_FILES['Filedata']['name']);
				$temp_array = explode(".",$temp_filename);
				$file_extension = $temp_array[count($temp_array)-1];
				$new_filename = "{$mediaID}_{$dspID}.{$file_extension}";
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $folderPath.$new_filename);
				
				// Check for and delete any prior records
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_dsp_temp WHERE media_id  = '{$mediaID}' AND dsp_id = '{$dspID}'");
				
				// Insert new record
				$sql = "INSERT INTO {$dbinfo[pre]}media_dsp_temp (
						media_id,
						dsp_id,
						filename,
						ofilename
						) VALUES (
						'{$mediaID}',
						'{$dspID}',
						'{$new_filename}',
						'{$_FILES[Filedata][name]}'
						)";
				$result = mysqli_query($db,$sql);
			}
		break;
		case "delete_dsp_file":
			// Check for and delete tmp file
			// Check for and delete lib file
		break;
		case "uploadMR":
			if($_SESSION['admin_user']['admin_id'] != "DEMO")
			{
				$folderPath = '../assets/files/releases/';
				
				// Check if directory exists
				if(!is_dir($folderPath))
					mkdir($folderPath,0777,true);
					
				$newFilename = strtolower(clean_filename($_FILES['Filedata']['name']));
				$newFilename = "mr_{$mediaID}_{$newFilename}";
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $folderPath.$newFilename);
				
				// Delete old release
				
				// Update database
				mysqli_query($db,
					"
					UPDATE {$dbinfo[pre]}media SET 
					model_release_form='{$newFilename}'
					WHERE media_id = '{$mediaID}'
					"
				);
				
			}
		break;
		case "uploadPR":
			if($_SESSION['admin_user']['admin_id'] != "DEMO")
			{
				$folderPath = '../assets/files/releases/';
				
				// Check if directory exists
				if(!is_dir($folderPath))
					mkdir($folderPath,0777,true);
				
				$newFilename = strtolower(clean_filename($_FILES['Filedata']['name']));
				$newFilename = "pr_{$mediaID}_{$newFilename}";
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $folderPath.$newFilename);
				
				// Delete old release XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
				
				// Update database
				mysqli_query($db,
					"
					UPDATE {$dbinfo[pre]}media SET 
					prop_release_form='{$newFilename}'
					WHERE media_id = '{$mediaID}'
					"
				);
			}
		break;
		case "delete_release":
			$mediaResult = mysqli_query($db,"SELECT model_release_form,prop_release_form FROM {$dbinfo[pre]}media WHERE media_id = '{$mediaID}'");
			$media = mysqli_fetch_assoc($mediaResult);
			
			if($rType == 'mr')
			{
				if($media['model_release_form']) @unlink("../assets/files/releases/{$media[model_release_form]}");
				
				// Update database
				mysqli_query($db,
					"
					UPDATE {$dbinfo[pre]}media SET 
					model_release_form=''
					WHERE media_id = '{$mediaID}'
					"
				);
				
				echo "<script>$('mrFile').hide();</script>";
					
			}
			elseif($rType == 'pr')
			{
				if($media['prop_release_form']) @unlink("../assets/files/releases/{$media[prop_release_form]}");
				
				// Update database
				mysqli_query($db,
					"
					UPDATE {$dbinfo[pre]}media SET 
					prop_release_form=''
					WHERE media_id = '{$mediaID}'
					"
				);
				
				echo "<script>$('prFile').hide();</script>";

			}
			
		break;
	}
?>