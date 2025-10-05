<?php
	###################################################################
	####	ADD MEDIA ACTIONS                              		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(2);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "add_media";
		
		# KEEP THE PAGE FROM CACHING
		if($_REQUEST['mode'] == 'tinyimg')
		{
			/*
			// calc an offset of 10 days
			$offset = 3600 * 24 * 10;
			// calc the string in GMT not localtime and add the offset
			$expire = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
			//output the HTTP header
			header("Cache-Control: max-age={$offset}, must-revalidate"); // HTTP/1.1
			header($expire);
			//$_SESSION['testerses']++;
			*/
		}
		else
		{
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		}
		
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
		
		# INCLUDE MANAGER ADDONS FILE
		require_once('../assets/includes/addons.php');
		
		# INCLUDE IMAGETOOLS CLASS
		require_once('../assets/classes/imagetools.php');
		
		# INCLUDE COLORS CLASS
		require_once('../assets/classes/colors.php');
		
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			# SET ACTIVE STATUS
			default:
			case "import_list":
				$num_files = 0;
				$num_folders = 0;
				$import_files = array();
				$is_folder = array();
				$file_path = array();
				$folder_name = array();
				$folder_level = array();
				
				//include_lang();
				
				function count_incoming_dir($dir,$level=0)
				{
					global $num_files,$num_folders,$file_path,$folder_name,$folder_level,$config;
					$real_dir = realpath($dir);
					//$dir = opendir($real_dir); // Changed in 4.4.3
					# LOOP THROUGH THE WEB DIRECTORY
					//while($file = readdir($dir)){ // Changed in 4.4.3
					$files = scandir($real_dir);
					foreach ($files as $file) {
						if($file != "." and $file != ".."){
							if(strpos($file,"icon_") === false and strpos($file,"thumb_") === false and strpos($file,"sample_") === false){
								if(is_dir($real_dir.DIRECTORY_SEPARATOR.$file))
								{
									$folder_name[] = $file;
									$folder_level[] = $level;
									$num_folders++;
									$file_path[] = $real_dir . DIRECTORY_SEPARATOR . trim($file);
									count_incoming_dir($real_dir.DIRECTORY_SEPARATOR.$file,$level+1);
								}
								else
								{
									# ONLY COUNT FILETYPES THAT ARE SUPPORTED
									$fileparts = explode(".",$file);
									$extension = array_pop($fileparts);
									if(in_array(strtolower($extension),getAlldTypeExtensions()))
									{
										$num_files++;
									}
								}
							}
						}
					}	
				}
				count_incoming_dir($config['settings']['incoming_path']);
				
				echo "<strong>{$num_files}</strong> {$mgrlang[gen_files2]} / <strong>{$num_folders}</strong> {$mgrlang[gen_folders]}<br />{$mgrlang[gen_importmes]}";
				
				if($num_files > 0){ echo "$num_file<div style='margin-top: 10px; padding: 4px 4px 4px 6px; font-size: 11px; background-color: #e1e1e1; overflow: auto'><input type='button' value='{$mgrlang[gen_sel_all]}' id='select_all' style='float: left; margin-right: 2px; height: 20px;' onclick=\"select_all_cb('import_list_form');\" /><input type='button' value='{$mgrlang[gen_b_sn]}' id='select_none' style='float: left; height: 20px;' onclick=\"deselect_all_cb('import_list_form');\" /><img src='images/mgr.button.small.assets.gif' style='float:left; margin: 3px 4px 0 6px;' /> <p style='float: left; padding: 3px 0 0 0; margin: 0; color: #bebdbd'>Preview</p><input type='button' value='{$mgrlang[gen_b_remove_sel]}' id='remove_selected' style='float: right; height: 20px;' onclick='delete_import_files();' /><input type='button' value='{$mgrlang[gen_refresh]}' id='refresh_import_win' style='float: right; height: 20px;' onclick='load_import_win();' /></div>"; }
				
				echo "<div style='border: 1px solid #eee; height: 300px; padding: 5px 10px 0 10px; overflow: auto; clear: both;"; 
				if($num_files == 0){ echo "margin-top: 20px;"; }
				echo "' id='list_window'><form id='import_list_form' name='import_list_form'>";
				
				echo "<div id='iw' class='details_win' style='display: none; position: absolute; margin-left: 185px; margin-top: -42px'><img src='images/mgr.loader2.gif' align='absmiddle' style='margin: 10px;' /></div>";
				
					function read_incoming_files($dir,$level,$folder_id_num)
					{
						global $config;
						$real_dir = realpath($dir);
						//$dir = opendir($real_dir); // changed in 4.4.3
						$asset_id = 1;
						# LOOP THROUGH THE WEB DIRECTORY
						//while($file = readdir($dir)){ // changed in 4.4.3						
						$files = scandir($real_dir);
						foreach ($files as $file){						
							if($file != "." and $file != ".."){
								if(strpos($file,"icon_") === false and strpos($file,"thumb_") === false and strpos($file,"sample_") === false){
									if(!is_dir($real_dir.DIRECTORY_SEPARATOR.$file))
									{
										# ONLY SHOW FILETYPES THAT ARE SUPPORTED
										$fileparts = explode(".",$file);
										$extension = array_pop($fileparts);
										if(in_array(strtolower($extension),getAlldTypeExtensions()))
										{
										
											if($level > -1){ $padding = 28; } else { $padding = 8; }
											echo "<div style='padding-left: ".$padding."px; padding-top: 5px; float: left; clear: both; vertical-align: middle'>";
											$fsize = round(filesize($real_dir.DIRECTORY_SEPARATOR.$file)/1024);
											echo "<input type='checkbox' name='asset[]' id='cb_$asset_id' is_import_file='1' basename='$file' folder_id_num='$folder_id_num' filesize='$fsize' ";
											if($folder_id_num==0)
											{
												echo "checked='checked' ";
											}
											echo "value='".base64_encode($real_dir.DIRECTORY_SEPARATOR.$file)."' style='float: left; margin-top: 3px; vertical-align: middle'/> ";
											
											$basefilename = clean_filename(basefilename($file));
											
											if(file_exists($real_dir.DIRECTORY_SEPARATOR."icon_".$basefilename.".jpg"))
											{
												//$thumb = "../assets/incoming/icon_".$basefilename.".jpg";
												$fullpath = $real_dir.DIRECTORY_SEPARATOR."icon_".$basefilename.".jpg";
												$thumb = "mgr.add.media.actions.php?mode=tinyimg&img=$fullpath";
											}
											else
											{
												$thumb = 'images/mgr.no.thumb.tiny.png';
											}
											
											if($config['ShowImportIcons'])
											{
												echo "<div style='float: left; width: ".($config['ImportPreviewSizeA']+13)."px; text-align: center;'><img src='$thumb' style='border: 2px solid #dedede; cursor: pointer; vertical-align: middle' id='img_".$asset_id."' onmouseover=\"show_details_win('iw','mgr.add.media.actions.php?mode=preview&id=img_$asset_id&file=$file&path=".urlencode($real_dir)."');\" onmouseout=\"hide_details_win('iw');\" /></div>";
											}
											else
											{
												echo "&nbsp;";
											}
											
											
											echo "<label for='cb_$asset_id'>$file</label> <span style='color: #9c9c9c; font-size: 10px'>(".$fsize."kb) <!--$level--></span>";
											
											echo "</div>";
											//echo "<div style='float: left; overflow: visible;'>";
											//echo "<div id='iw_$asset_id' class='details_win' style='display: none; position: absolute; margin-left: 336px; margin-top: -32px'><img src='images/mgr.loader2.gif' align='absmiddle' style='margin: 10px;' /></div>";
											//echo "</div>";
											$asset_id++;
										}
									}
								}
							}
						}	
					}
					
					$folder_id_num = 1;
					foreach($folder_name as $key => $value)
					{
						//if($level > 1){ $padding = ($level*10); } else { $padding = 0; $level = 1; }
						echo "<div style='padding-left: ".($folder_level[$key]*18)."px; padding-top: 6px; clear: both;'>";
						//echo "<li style='padding-left: ".$indent."px;'>";
						echo "<a href=\"javascript:select_folder_contents('$folder_id_num');\" class='bold-plain-link'><img src='images/mgr.folder.icon.small2.gif' align='absmiddle' style='margin-left: 6px; border: 0' />";
						echo "&nbsp; $value</a><br style='clear: both;' />";
						//echo "&nbsp; <strong>$value</strong> &nbsp; <!-- <img src='images/mgr.button.select.all.png' style='width: 15px;' align='absmiddle' /> <img src='images/mgr.button.select.none.png' style='width: 15px;' align='absmiddle' />--> <!--<span style='font-size: 10px;'><a href='#' class='actionlink' style='padding: 2px;'>Select All</a>$folder_level[$key]  $file_path[$key]  <a href='#' class='actionlink' style='padding: 2px;'>&raquo;</a>--></span><br style='clear: both;' />";
						
						read_incoming_files($file_path[$key],$folder_level[$key],$folder_id_num);
						
						echo "</div>";
						$folder_id_num++;
					}
					
					read_incoming_files($config['settings']['incoming_path'],-1,0);
				
			break;
			
			case "delete":
				
				if($_POST['asset'])
				{					
					foreach($_POST['asset'] as $key => $value)
					{
						# DELETE FILE
						$value = base64_decode($value);
						if(file_exists($value))
						{
							unlink($value);
							// RECORD ACTIVITY?
						}
						
						# DELETE ICON FILE IF IT EXISTS
						//unset($file_name);
						//$file_name_exp = explode(DIRECTORY_SEPARATOR,$value);
						//$file_name = $file_name_exp[count($file_name_exp)-1];
						$path_wo_filename = dirname($value);
						$filename_only = basename($value);
						$filename = explode(".",$filename_only);
						$filename_ext = strtolower(array_pop($filename));				
						$filename_glued = implode(".",$filename);
						
						//$test = array_pop($file_name_exp);
						//echo $test; exit;						
						//$path = implode(DIRECTORY_SEPARATOR,$file_name_exp);						
						//$file_name_short = explode(".",$file_name);						
						//array_pop($file_name_short);
						//$file_name_short = implode(".",$file_name_short);
						
						if(file_exists($path_wo_filename . DIRECTORY_SEPARATOR . "icon_" . $filename_glued . ".jpg"))
						{
							unlink($path_wo_filename . DIRECTORY_SEPARATOR . "icon_" . $filename_glued . ".jpg");
						}
						if(file_exists($path_wo_filename . DIRECTORY_SEPARATOR . "thumb_" . $filename_glued . ".jpg"))
						{
							unlink($path_wo_filename . DIRECTORY_SEPARATOR . "thumb_" . $filename_glued . ".jpg");
						}
						if(file_exists($path_wo_filename . DIRECTORY_SEPARATOR . "sample_" . $filename_glued . ".jpg"))
						{
							unlink($path_wo_filename . DIRECTORY_SEPARATOR . "sample_" . $filename_glued . ".jpg");
						}
					}
				}
				header("Location: " . $_SERVER['PHP_SELF']);
				exit;
			break;
			
			case "preview":
				//sleep(1);
				
				# CHECK TO MAKE SURE A PREVIEW CAN BE MADE
				$filename = explode(".",$_GET['file']);
				$filename_ext = array_pop($filename);
				//$preview_formats = array("jpg","JPG","png","PNG","gif","GIF","jpe","JPE","jpeg","JPEG"); 
				
				//$preview_formats = array('image/png','image/jpeg','image/gif','image/bmp'); // ACCEPTED PREVIEW FORMATS,'image/x-ms-bmp'
				
				$preview_formats = getCreatableFormats();
				
				$mimetype = getMimeType($_GET['path'] . DIRECTORY_SEPARATOR . $_GET['file']);
				
				$filename_glued = implode(".",$filename);
				
				$filename_glued = clean_filename($filename_glued);
				
				//$filename_glued = basefilename($_GET['file']);
				
				//echo $filename_glued; exit;
				
				if(in_array(strtolower($filename_ext),$preview_formats) or file_exists($_GET['path'] .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg"))
				{
					# CHECK THE AMOUNT OF MEMORY THAT IS NEEDED TO MAKE A PREVIEW
					$mem_needed = figure_memory_needed($_GET['path'] .DIRECTORY_SEPARATOR. $_GET['file']);
					
					# CHECK THE MEMORY LIMIT THAT IS ALLOWED
					if(ini_get("memory_limit")){
						$memory_limit = str_replace('M','',ini_get("memory_limit"));
					} else {
						$memory_limit = $config['DefaultMemory'];
					}
					# IF IMAGEMAGICK ALLOW TWEAKED MEMORY LIMIT
					if(class_exists('Imagick') and $config['settings']['imageproc'] == 2)
					{
						$memory_limit = $config['DefaultMemory'];
					}
					
					//$size = getimagesize($_GET['path'] .DIRECTORY_SEPARATOR. $_GET['file']);
					//echo $memory_limit . " " . $mem_needed; exit;
					# OUTPUT IMAGE PREVIEW OR ERROR
					if($memory_limit > $mem_needed or file_exists($_GET['path'] .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg")){
						// GO FOR IT
						//echo "yes"; exit;
						echo "<img src='mgr.add.media.actions.php?mode=previewimg&img=$_GET[file]&path=$_GET[path]&size=".$config['IconDefaultSize']."&save=1' align='absmiddle' style='margin: 4px;' />";
						
						if(!file_exists($_GET['path'] .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg"))
						{
							//$fullpath = $config['settings']['incoming_path']."icon_".$basefilename.".jpg";
							$newpath = realpath($_GET['path'].DIRECTORY_SEPARATOR.$_GET['file']);
							$tinythumb = "mgr.add.media.actions.php?mode=tinyimg&img=".urlencode($newpath);
							//echo $newpath;
							echo "<script>function gettinyimg(){ $('".$_GET['id']."').src='$tinythumb'; } $('".$_GET['id']."').src='images/mgr.loader.gif'; setTimeout(gettinyimg,2000);</script>";
						}
					} else {
						include_lang();
						//echo $size['mime']; exit;
						echo "<div style='padding: 10px; width: 150px;'>$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></div>";
					}
				}
				# FILE IS FORMAT THAT A PREVIEW CANNOT BE CREATED FROM
				else
				{
					include_lang();
					echo "<div style='padding: 10px; width: 150px;'>{$mgrlang[no_preview]} <strong>$filename_ext</strong> <em>({$mimetype})</em></div>";
				}
			break;
			
			case "tinyimg":
				
				/*
				* Caching of images
				*/
				$cacheFile = "tinyimg-".md5("mgr-{$_GET[img]}-{$config[ImportPreviewSizeA]}").'.jpg'; // Name of cached file - added mgr to make sure it is always specific to the management area
				$cachePathFile = "../assets/cache/{$cacheFile}";
				if(file_exists($cachePathFile))
				{	
					if($config['cacheImages']) // Check for debug mode
					{
						$cacheTime = gmdate("U")-$config['cacheImagesTime'];
						$fileTime = filemtime($cachePathFile);
						
						if($cacheTime < $fileTime)
						{	
							header("Content-type: image/jpeg");
							//header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($thumbnail)) . ' GMT');
							readfile($cachePathFile);
							exit;
						}
						else // Cleanup old cached file
							@unlink($cachePathFile);
					}
				}
				
				
				$src = urldecode($_GET['img']);
				$image = new imagetools($src);
				$image->size = $config['ImportPreviewSizeA'];
				
				if($config['cacheImages'] == 0)
					$image->createImage(1,''); // Do not cache
				else
					$image->createImage(1,$cachePathFile); // Cache				
							
			break;
			
			case "importedthumb":
				$src = urldecode($_GET['path'] .DIRECTORY_SEPARATOR. $_GET['img']);
				$image = new imagetools($src);
				$image->setSize('50');
				$image->createImage(1,'');			
			break;
			
			case "previewimg":
				$image = new imagetools($_GET['path'] .DIRECTORY_SEPARATOR. $_GET['img']);

				$filename_only = $_GET['img'];
				$clean_filename = clean_filename($filename_only);				
				$filename = explode(".",$clean_filename);
				$filename_ext = strtolower(array_pop($filename));				
				$filename_glued = implode(".",$filename);
				
				if(file_exists($image->path .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg")) # ICON FILE ALREADY EXISTS / NO NEED TO RECREATE IT
				{
					$image->source = $image->path .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg";
					$image->createImage(1,0,'');
					//debugEmail($image->path);
				}
				else # ICON DOES NOT EXIST - CREATE IT
				{
					$image->createImage(1,$image->path .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg");
					//debugEmail($image->path .DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg");
				}
			break;
			case "galleries":
				
				//echo "testing"; exit;
				if($_GET['gal_mem'])
				{
					$gal_mem = $_GET['gal_mem'];
				}
				else
				{
					$gal_mem = 0;	
				}
				//echo $_GET['gal_mem'];
				// CREATE ARRAY TO WORK WITH							
				$folders = array();
				$folders['name'] = array();
				$folders['folder_id'] = array();
				$folders['parent_id'] = array();
				$folders['folder_rows'] = array();
				$folders['pass_protected'] = array();
				$folder_array_id = 1;
				
				// READ STRUCTURE FUNCTION															
				read_gal_structure(0,'name','',$gal_mem);
				//read_gal_structure(0,$listby,$listtype,$_SESSION['galmem']);
				
				/*
				echo "<div style=\"padding: 0 0 0 10px; margin: 0px; background-color: #eee\">";
				echo "<img src=\"images/mgr.folder.icon.small2.gif\" align=\"absmiddle\" /> <input type='radio' name='parent_gal' value='0' class='radio' style='margin-left: -15px;'";
					if($_GET['edit'] == "new" or $gallery->parent_gal == 0){
						echo " checked";
					}
				echo " /> <strong>None</strong></div>";
				*/
			
				//$gallery_parent = $gallery->parent_gal;
				$gallery_current = 0;
				
				# BUILD THE GALLERIES AREA
				$mygalleries = new build_galleries;
				$mygalleries->scroll_offset_id = "gals";
				$mygalleries->scroll_offset = 1;
				$mygalleries->options_name = 'media_galleries[]';
				$mygalleries->options = "checkbox";
				$mygalleries->output_struc_array(0);
			break;
			
			case "create_gallery":
				//sleep(1);
				
				# CREATE A UNIQUE GALLERY ID
				$ugallery_id = create_unique2();
				
				# CREATE THE EDIT DATE
				$edit_date = gmt_date();
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
				require_once('../assets/includes/clean.data.php');
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}galleries (
						name,
						parent_gal,
						ugallery_id,
						created,
						edited,
						active,
						everyone
						) VALUES (
						'$name',
						'0',
						'$ugallery_id',
						'$edit_date',
						'$edit_date',
						'1',
						'1'
						)";
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_add_media'],1,"{$mgrlang[quick_create_gal]} > <strong>$name</strong>");
				
				echo "<script>$('new_gallery_name').setValue(''); load_gals();</script>";
			break;
			
			case "import_file":
				//$tcount = count($_POST);
				//test($tcount);
				
				# HOW LONG TO SLEEP FOR BEFORE GOING ON
				sleep($config['ImportSleep']);
				
				//$_SESSION['testing']['startImport'] = 'working';
				
				# INCLUDE JSON CLASS FILE
				require_once('../assets/classes/json.php');	
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
				require_once('../assets/includes/clean.data.php');
	
				# METADATA CLASS
				require_once('../assets/classes/metadata.php');
				
				$json = new Services_JSON();
				
				//$status = '1';
				//echo base64_decode($_GET['filename']) . "|" . $status;
				
				// USED TO TRICK JSON INTO PULLING THE RECORD IF NOTHING IS RETURNED
				$data['junk']['name'][] = "test";
				
				// Check to make sure there is no max input in place
				/*
				$maxInput = ini_get('max_input_vars');				
				if($maxInput)
				{
					if($maxInput <= count($_POST))
					{
						$status = '0';
						$errormessage[] = 'testing';
						exit;	
					}
				}
				*/
				
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
				
				# IF GD OR IMAGEMAGIK
				$creatable_filetypes = getCreatableFormats();
				
				# GET FOLDER DETAILS
				$folder_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}folders WHERE folder_id = '{$_POST[folder_id]}'");
				$folder_rows = mysqli_num_rows($folder_result);
				$folder = mysqli_fetch_object($folder_result);
				
				//$_SESSION['testing'] = $_POST;
				
				if(!$folder_rows)
				{	
					$data['myfile']['status'][] = '0';
					$data['myfile']['errormessage'][] = $mgrlang['no_folder_import']." FID: {$_POST[folder_id]} ROWS: {$folder_rows}";
					$data['myfile']['errorcode'][] = '999';
					$data['myfile']['thumbstatus'][] = '0';					
					echo $json->encode($data);
					exit;
				}
				
				# CALCULATE THE MEMORY NEEDED ONLY IF IT IS A CREATABLE FORMAT
				if(in_array(strtolower($filename_ext),$creatable_filetypes))
				{
					# FIGURE MEMORY NEEDED
					$mem_needed = figure_memory_needed($decoded_path);
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
					
					$autoCreateAvailable = 1;
				}
				
				$dtype = getdTypeOfExtension($filename_ext); // Get the associated dtype from the array

				$metadata_filetypes = array('jpg','jpe','jpeg');
				
				$icon_image = $path_wo_filename . DIRECTORY_SEPARATOR. "icon_" . $filename_glued . ".jpg";
				$thumb_image = $path_wo_filename . DIRECTORY_SEPARATOR. "thumb_" . $filename_glued . ".jpg";
				$sample_image = $path_wo_filename . DIRECTORY_SEPARATOR. "sample_" . $filename_glued . ".jpg";
				
				# CHECK FOR EXISTING ICON
				if(!file_exists($icon_image))
				{
					# CHECK TO SEE IF ONE CAN BE CREATED
					if(in_array(strtolower($filename_ext),$creatable_filetypes))
					{
						# CHECK THE MEMORY NEEDED TO CREATE IT
						if($memory_limit > $mem_needed){
							// CREATE ICON
							$image = new imagetools($decoded_path);
							$image->setSize($config['IconDefaultSize']);
							$image->setQuality($config['SaveThumbQuality']);
							$image->createImage(0,$icon_image);
						}
						else
						{
							$status = '0';
							$errormessage[] = $mgrlang['not_enough_mem'];
						}
					}
					else
					{
						$status = '0';
						//$errormessage[] = 'An icon image cannot be automatically created from this filetype: ' . $filename_ext;
					}
				}
				
				//$_SESSION['testing']['step3'] = '3';
				
				# CHECK FOR EXISTING THUMBNAIL
				if(!file_exists($thumb_image))
				{
					# CHECK TO SEE IF ONE CAN BE CREATED
					if(in_array(strtolower($filename_ext),$creatable_filetypes))
					{
						# CHECK THE MEMORY NEEDED TO CREATE IT
						if($memory_limit > $mem_needed)
						{
							// CREATE ICON
							$image = new imagetools($decoded_path);							
							$image->setSize($config['ThumbDefaultSize']);
							$image->setQuality($config['SaveThumbQuality']);
							$image->createImage(0,$thumb_image);
						}
						else
						{
							$status = '0';
							$errormessage[] = ''; //'Your server does not allow enough memory to create a thumbnail for this image. You can manually add a thumbnail in Library > Media.';
						}
					}
					else
					{
						$status = '0';
						//$errormessage[] = 'A thumbnail image cannot be automatically created from this filetype: ' . $filename_ext;
					}
				}
				
				//$_SESSION['testing']['step4'] = '4';
				
				# CHECK FOR EXISTING SAMPLE
				if(!file_exists($sample_image))
				{
					
					//$_SESSION['testing']['fileexist'] = 'no';
					
					# CHECK TO SEE IF ONE CAN BE CREATED
					if(in_array(strtolower($filename_ext),$creatable_filetypes))
					{	
						# CHECK THE MEMORY NEEDED TO CREATE IT
						if($memory_limit > $mem_needed){
							
							//$_SESSION['testing']['SampleDefaultSize'] = $config['SampleDefaultSize'];
							//$_SESSION['testing']['SaveSampleQuality'] = $config['SaveSampleQuality'];
							
							//$_SESSION['testing']['getMemUsage'] = memory_get_usage(true);
							//$_SESSION['testing']['getPeakUsage'] = memory_get_peak_usage(true);
							
							//$_SESSION['testing']['decoded_path'] = $decoded_path;
							//$_SESSION['testing']['sample_image'] = $sample_image;
							
							// CREATE ICON
							$image = new imagetools($decoded_path);
							$image->setSize($config['SampleDefaultSize']);
							$image->setQuality($config['SaveSampleQuality']);
							$image->createImage(0,$sample_image);
						}
						else
						{
							$status = '0';
							$errormessage[] = '';//'Your server does not allow enough memory to create a sample for this image. You can manually add a sample in Library > Media.';
						}
					}
					else
					{
						$status = '0';
						$errormessage[] = $mgrlang['no_auto_thumbs'] . ': <strong>' . $filename_ext . "</strong> <em>({$mimetype})</em><br />" . $mgrlang['manual_add_thumb'];
					}
				}
				
				//$_SESSION['testing']['step5'] = '5';
				
				$folder_name = ($folder->encrypted) ? $folder->enc_name: $folder->name;
				
				if(!is_writable($config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR))
				{	
					$data['myfile']['status'][] = '0';
					$data['myfile']['errormessage'][] = $mgrlang['folder_not_writable']." ({$folder_name})";
					$data['myfile']['errorcode'][] = '999';
					$data['myfile']['thumbstatus'][] = '0';					
					echo $json->encode($data);
					exit;
				}
				
				# MOVE ORIGINAL CHECKS
				//$clean_orig_name = $filename_glued . "." . $filename_ext;
				$enc_orig_name = md5($filename_glued);
				$move_orig_name = ($folder->encrypted) ? $enc_orig_name . "." . $filename_ext : $filename_glued . "." . $filename_ext;
				
				/*
				$checkfile = $config['settings']['library_path'] . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $move_orig_name;
				$x = 1;
				while(file_exists($checkfile) == true)
				{
					$new_filename_glued = $filename_glued . $x;
					$move_orig_name = ($folder->encrypted) ? md5($new_filename_glued) . "." . $filename_ext : $new_filename_glued . "." . $filename_ext;
					$checkfile = $config['settings']['library_path'] . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $move_orig_name;
					$x++;
				}
				*/
				
				$folder_id = $folder->folder_id;
				# CHECK IF MEDIA ALREADY EXISTS
				$media_result = mysqli_query($db,"SELECT filename FROM {$dbinfo[pre]}media WHERE folder_id = '$folder_id' AND filename = '$move_orig_name'");
				$checkfile = mysqli_num_rows($media_result);
				$media = mysqli_fetch_object($media_result);
				
				//$_SESSION['testing']['mediaExists'] = $checkfile;
				
				$x = 1;
				while($checkfile > 0)
				{
					$new_filename_glued = $filename_glued . $x;
					$move_orig_name = ($folder->encrypted) ? md5($new_filename_glued) . "." . $filename_ext : $new_filename_glued . "." . $filename_ext;
					//$checkfile = $config['settings']['library_path'] . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $move_orig_name;
					
					$media_result = mysqli_query($db,"SELECT filename FROM {$dbinfo[pre]}media WHERE folder_id = '$folder_id' AND filename = '$move_orig_name'");
					$checkfile = mysqli_num_rows($media_result);
					$media = mysqli_fetch_object($media_result);
					$x++;
				}
				if($new_filename_glued){ $filename_glued = $new_filename_glued; } 
				
				$ofilename = $filename_glued  . "." . $filename_ext;
				
				# GET IPTC INFO
				if(in_array(strtolower($filename_ext),$metadata_filetypes))
				{	
					$imagemetadata = new metadata($decoded_path);
					
					// UTF8 Detection
					if($config['settings']['iptc_utf8']) // utf-8
						$imagemetadata->setCharset('utf-8');
					else
						$imagemetadata->setCharset('off'); // utf8_encode off
					
					if($config['settings']['readiptc'])
					{
						$iptc = $imagemetadata->getIPTC();	
						//test($iptc);
						if($iptc) $iptc = array_map("addSlashesMap",$iptc); // fix ' and " issues
					}
					if(function_exists('exif_read_data') and $config['settings']['readexif'])
					{
						$exif = $imagemetadata->getEXIF();
						
						if($exif) $exif = array_map("addSlashesMap",$exif); // fix ' and " issues
					}
				}
				
				//test($exif,'exif');
				
				//$_SESSION['testing']['iptc'] = $iptc;
				
				# GET ORIGINALS SIZE
				$origsize = getimagesize($decoded_path);
				$filesize = filesize($decoded_path);				
				
				if(in_array(strtolower($filename_ext),$metadata_filetypes))
				{
					if($exif['DateTimeOriginal']) // Find the date the photo was taken
					{
						$dateCreatedParts = explode(' ',$exif['DateTimeOriginal']);					
						$dateCreatedYMD = str_replace(':','-',$dateCreatedParts[0]);
						$dateCreatedString = "{$dateCreatedYMD} {$dateCreatedParts[1]}";
						//$dateCreated = date("Y-m-d H:m:s",filemtime($decoded_path)); // Date file was created
						//$exif['DateTimeOriginal'];
					}
					else
						$dateCreatedString = '0000-00-00 00:00:00'; //date("Y-m-d H:m:s",filemtime($decoded_path))
				}
				else
					$dateCreatedString = '0000-00-00 00:00:00'; //date("Y-m-d H:m:s",filemtime($decoded_path))
					
				if($dateCreatedString != '0000-00-00 00:00:00') // Check if there is a date to work with
				{
					$ndate = new kdate;
					$dateCreated = $ndate->formdate_to_gmt($dateCreatedString);
				}
				else
					$dateCreated = $dateCreatedString;
					
				# SAVE IPTC DATE INFO - OVERRIDE EXIF
				if($iptc['date_created'])
				{
					$date_created_year = substr($iptc['date_created'],0,4);
					$date_created_month = substr($iptc['date_created'],4,2);
					$date_created_day = substr($iptc['date_created'],6,2);
					$date_created = "$date_created_year-$date_created_month-$date_created_day 00:00:00";
					
					$dateCreated = $date_created;
				}
					
				//test($dateCreated);
				
				//$_SESSION['testing']['dateCreated'] = $dateCreated;
				
				# CHECK IF ORIGINALS SHOULD BE KEPT
				if($keep_originals)
				{
					//$_SESSION['testing']['keepOrig'] = 'yes';
					
					# CHOOSE WHERE TO SAVE THE ORIGINAL
					if($folder->storage_id == 0)
					{
						// rename
						// copy
						# MOVE ORIGINAL
						if($config['mediaMoveFunction']($decoded_path,$config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $move_orig_name))
						{
							# DELETE FROM INCOMING DIR
							unlink($decoded_path);
							$original_success = 1;
						}
						else
						{
							# COULD NOT MOVE ORIGINAIL - FAIL
							//$status = '0';
							$data['myfile']['status'][] = '0';
							$data['myfile']['errormessage'][] = $mgrlang['error_moving_file'];
							$data['myfile']['errorcode'][] = '0';
							$data['myfile']['thumbstatus'][] = '0';
							$data['myfile']['filepath'][] = $clean_filename;							
							echo $json->encode($data);
							exit;
						}
					}
					else
					{
						# SELECT STORAGE LOCATION
						$storage_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage WHERE storage_id = '$folder->storage_id'");
						$storage_rows = mysqli_num_rows($storage_result);
						$storage = mysqli_fetch_object($storage_result);
						
						# CHECK TO SEE IF FILE IS TOO LARGE TO MOVE
						if(filesize($decoded_path) > (($config['OffsiteStogageLimit']*1024)*1024) and $storage->storage_type != 'local')
						{
							$data['myfile']['status'][] = '0';
							$data['myfile']['errormessage'][] = $mgrlang['file_too_large'];
							$data['myfile']['errorcode'][] = '0';
							$data['myfile']['thumbstatus'][] = '0';
							$data['myfile']['filepath'][] = $clean_filename;							
							echo $json->encode($data);
							exit;
						}
						else
						{
							switch($storage->storage_type)
							{
								# COPY TO ANOTHER LOCAL DRIVE
								case "local":
									# MOVE ORIGINAL
									if(copy($decoded_path,k_decrypt($storage->path) . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $move_orig_name))
									{
										# DELETE FROM INCOMING DIR
										unlink($decoded_path);
										$original_success = 1;
									}
									else
									{										
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $mgrlang['error_move_ext'] . ' ($storage->name).';
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';
										$data['myfile']['filepath'][] = $clean_filename;							
										echo $json->encode($data);
										exit;
									}
								break;
								# COPY TO AN FTP LOCATION
								case "ftp":
									if(function_exists('set_time_limit')) set_time_limit(0);
									
									# REQUIRE THE FTP CLASS FILE
									require_once($config['base_path'] . '/assets/classes/ftp.php');	
									
									# FTP CONNECTION
									$ftp = new ftp_connection(stripslashes(k_decrypt($storage->host)),stripslashes(k_decrypt($storage->username)),stripslashes(k_decrypt($storage->password)),$storage->port);
									
									$cleanpath = stripslashes(k_decrypt($storage->path)) . '/' . $folder_name . '/';
									
									# CHANGE FTP DIRECTORY
									$ftp->change_dir($cleanpath);
									
									# TRY AND PUT THE FILE ON THE FTP SITE
									$ftp->put_file($decoded_path,$move_orig_name);
									
									# CLOSE FTP CONNECTION
									$ftp->close_conn();
									
									# OUTPUT ANY ERRORS
									if($ftp->ftp_errors())
									{
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $ftp->ftp_errors();
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';
										$data['myfile']['filepath'][] = $clean_filename;							
										echo $json->encode($data);
										exit;
									}
									else
									{
										# DELETE FROM INCOMING DIR
										unlink($decoded_path);
										$original_success = 1;
									}
								break;
								# COPY TO AMAZON S3
								case "amazon_s3":
									if(function_exists('set_time_limit')) set_time_limit(0);
									
									# INCLUDE AMAZON CLASS FILE
									require_once($config['base_path'] . '/assets/classes/amazonS3/as3.php');
									
									# DEFINE THE CONNECTION KEYS
									if(!defined('awsAccessKey')) define('awsAccessKey', stripslashes(k_decrypt($storage->username)));
									if(!defined('awsSecretKey')) define('awsSecretKey', stripslashes(k_decrypt($storage->password)));
									
									// Check for CURL
									if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
									{
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $mgrlang['no_curl'];
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';					
										echo $json->encode($data);
										exit;
									}
									// Instantiate the class
									$s3 = new S3(awsAccessKey, awsSecretKey);
									
									S3::$useSSL = false;
									
									//$s3->putBucket('jons_test_bucket', S3::ACL_PUBLIC_READ);
									
									if($s3->putObject($s3->inputFile($decoded_path), $folder->enc_name, $move_orig_name, $s3->ACL_PUBLIC_READ))
									{
										# DELETE FROM INCOMING DIR
										unlink($decoded_path);
										$original_success = 1;
									}
									else
									{
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $mgrlang['failed_as3_move'];
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';					
										echo $json->encode($data);
										exit;
									}
									
								break;
								# COPY TO RACKSPACE
								case "cloudfiles":

									require_once($config['base_path'] . '/assets/classes/rackspace/cloudfiles.php');
									
									$username = stripslashes(k_decrypt($storage->username));
									$api_key = stripslashes(k_decrypt($storage->password));
									
									try
									{
										$auth = new CF_Authentication($username, $api_key);
										$auth->authenticate();
									}
									catch (Exception $e)
									{
										//echo 'Caught exception: ',  $e->getMessage(), "\n";											
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $e->getMessage();
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';					
										echo $json->encode($data);
										exit;
									}
									
									if($auth->authenticated())
									{
										try
										{
											$conn = new CF_Connection($auth);
											$container = $conn->get_container($folder_name);
											$putfile = $container->create_object($move_orig_name);
											
											# SET MIME TYPE IF NEEDED
											if(!function_exists('finfo_open'))
											{
												$putfile->setContentType(getMimeType($decoded_path));
											}
											
											if($putfile->load_from_filename($decoded_path))
											{
												# DELETE FROM INCOMING DIR
												unlink($decoded_path);
												$original_success = 1;
											}
											$conn->close();
										}
										catch (Exception $e)
										{
											//echo 'Caught exception: ',  $e->getMessage(), "\n";											
											$data['myfile']['status'][] = '0';
											$data['myfile']['errormessage'][] = $e->getMessage();
											$data['myfile']['errorcode'][] = '0';
											$data['myfile']['thumbstatus'][] = '0';					
											echo $json->encode($data);
											exit;
										}
									}
									else
									{
										$data['myfile']['status'][] = '0';
										$data['myfile']['errormessage'][] = $mgrlang['failed_cf_move'];
										$data['myfile']['errorcode'][] = '0';
										$data['myfile']['thumbstatus'][] = '0';					
										echo $json->encode($data);
										exit;	
									}
								break;
							}
						}
					}
				}
				else
				{
					# DELETE FROM INCOMING DIR
					unlink($decoded_path);
					$original_success = 1;
					
					//$_SESSION['testing']['deleteFromIncoming'] = 'yes';
				}
				
				$clean_icon_name = clean_filename("icon_" . $filename_glued . ".jpg");
				$enc_icon_name = md5($filename_glued) . ".jpg";
				$move_icon_name = ($folder->encrypted) ? "icon_" . $enc_icon_name: $clean_icon_name;
				// MOVE ICON, THUMB, SAMPLE
				if(file_exists($icon_image) and $original_success)
				{
					// move
					if(copy($icon_image,$config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . $move_icon_name))
					{
						$icon_filesize = filesize($icon_image);
						$icon_wh = getimagesize($icon_image);
						$icon_width = $icon_wh[0];
						$icon_height = $icon_wh[1];
						unlink($icon_image);
						$icon_success = 1;
					}
					else
					{
						$status = '0';
						$errormessage[] = $mgrlang['failed_icon_copy'];
					}
				}
				if(file_exists($thumb_image) and $original_success)
				{
					// move
					$clean_thumb_name = clean_filename("thumb_" . $filename_glued . ".jpg");
					$enc_thumb_name = md5($filename_glued) . ".jpg";
					$move_thumb_name = ($folder->encrypted) ? "thumb_" . $enc_thumb_name: $clean_thumb_name;
					$thumbFinalSavePath = $config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . $move_thumb_name;
					if(copy($thumb_image,$thumbFinalSavePath))
					{
						$thumb_filesize = filesize($thumb_image);
						$thumb_wh = getimagesize($thumb_image);
						$thumb_width = $thumb_wh[0];
						$thumb_height = $thumb_wh[1];						
						unlink($thumb_image);
						$thumb_success = 1;
					}
					else
					{
						$status = '0';
						$errormessage[] = $mgrlang['failed_thumb_copy'];
					}	
				}
				
				if(file_exists($sample_image) and $original_success)
				{
					// move
					$clean_sample_name = clean_filename("sample_" . $filename_glued . ".jpg");
					$enc_sample_name = md5($filename_glued) . ".jpg";
					$move_sample_name = ($folder->encrypted) ? "sample_" . $enc_sample_name: $clean_sample_name;
					if(copy($sample_image,$config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR . $move_sample_name))
					{
						$sample_filesize = filesize($sample_image);
						$sample_wh = getimagesize($sample_image);
						$sample_width = $sample_wh[0];
						$sample_height = $sample_wh[1];
						unlink($sample_image);
						$sample_success = 1;
					}
					else
					{
						$status = '0';
						$errormessage[] = $mgrlang['failed_sample_copy'];
					}
				}
				
				# SHOW THE THUMBNAIL IF ONE EXISTS
				if(file_exists($config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . $move_icon_name))
				{
					$thumbpath = 'mgr.add.media.actions.php?mode=importedthumb&path=' . $config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "icons" . "&img=" . $move_icon_name;
					$thumbstatus = '1';	
				}
				
				# ENTER INFO INTO DB
				# CREATE MEDIA ID
				$umedia_id = create_unique2();
				
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
				
				$price_clean = $cleanvalues->currency_clean($price);
				
				# IF NO SESSION OR NEW BATCH CREATE ONE
				if(!$_SESSION['sess_batch_id'] or $_SESSION['sess_batch_id'] != $batch_id)
				{
					$_SESSION['sess_batch_id'] = $batch_id;
					$batch_updated = 1;
				}
				
				
				
				//$_SESSION['testing']['batchID'] = $batch_id;
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$title_val = ${"title_" . $value};
					$description_val = ${"description_" . $value};
					$addsqla.= ",title_$value";
					$addsqlb.= ",'$title_val'";
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				if($config['iptcTitleField'] == 'headline') // Use headline for title instead of title field
					$iptc['title'] = $iptc['headline'];					
				
				# OVERRIDE WITH IPTC DATA
				if($iptc['title'])
				{
					if($config['iptcTitleHandler'] == 'R')
					{
						$title = $iptc['title'];
					}
					else
					{
						$title = $title . $config['iptcSepChar'] . $iptc['title'];
					}
				}
				if($iptc['description'])
				{					
					if($config['iptcDescHandler'] == 'R')
					{
						$description = $iptc['description'];
					}
					else
					{
						$description = $description . $config['iptcSepChar'] . $iptc['description'];
					}
				}
				if($iptc['copyright_notice'])
				{					
					if($config['iptcCopyRightHandler'] == 'R')
					{
						$copyright = $iptc['copyright_notice'];
					}
					else
					{
						$copyright = $copyright . $config['iptcSepChar'] . $iptc['copyright_notice'];
					}
				}
				
				# CREATE THE EDIT DATE
				$date_added = gmt_date();
				
				$licParts = explode('-',$original_copy);
				
				if($licParts[1])
					$original_copy = $licParts[1];
				else
					$original_copy = $licParts[0];
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media (
						umedia_id,
						width,
						height,
						date_added,
						date_created,
						filesize,
						filename,
						ofilename,
						title,
						description,
						file_ext,
						folder_id,
						batch_id,
						license,
						rm_license,
						quantity,
						price,
						sortorder,
						credits,
						active,
						dsp_type,
						model_release_status,
						prop_release_status,
						copyright,
						usage_restrictions";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'{$umedia_id}',
						'{$origsize[0]}',
						'{$origsize[1]}',
						'{$date_added}',
						'{$dateCreated}',
						'{$filesize}',
						'{$move_orig_name}',
						'{$ofilename}',
						'{$title}',
						'{$description}',
						'{$filename_ext}',
						'{$folder->folder_id}',
						'{$batch_id}',
						'{$original_copy}',
						'{$rm_license}',
						'{$quantity}',
						'{$price_clean}',
						'{$sortorder}',
						'{$credits}',
						'1',
						'{$dtype}',
						'{$model_release_status}',
						'{$prop_release_status}',
						'{$copyright}',
						'{$usage_restrictions}'";
				$sql.= $addsqlb;
				$sql.= ")";
				if($result = mysqli_query($db,$sql))
					$saveid = mysqli_insert_id($db);
				else
				{
					/*
					$data['myfile']['status'][] = '0';
					$data['myfile']['errormessage'][] = 'Failed to insert record into DB';
					$data['myfile']['errorcode'][] = '0';							
					echo $json->encode($data);
					exit;	
					*/
				}
				
				//$_SESSION['testing']['mediaSQL'] = $sql;
				
				//$_SESSION['testing']['sqlWorks'] = $result;
				
				// Get the color palette using the thumb as the sample
				if($thumb_success and $config['cpResults'] > 0)
				{
					$colorPalette = new GetMostCommonColors();
					$colors = $colorPalette->Get_Color($thumbFinalSavePath, $config['cpResults'], $config['cpReduceBrightness'], $config['cpReduceGradients'], $config['cpDelta']);

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
									'{$saveid}',
									'{$hex}',
									'{$rgb[red]}',
									'{$rgb[green]}',
									'{$rgb[blue]}',
									'{$percentage}')
								");
							}
						}
					}
				}
				
				# SAVE GALLERIES
				if($checkGalLoaded)
				{
					foreach(@$mediaGalleries as $value)
					{
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}media_galleries (
								gmedia_id,
								gallery_id
								) VALUES (
								'$saveid',
								'$value'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# SAVE COLLECTIONS
				foreach(@$collection as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_collections (
							cmedia_id,
							coll_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE MEDIA TYPES
				foreach(@$media_types as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_types_ref (
							media_id,
							mt_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE PACKAGES
				foreach(@$packages as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_packages (
							media_id,
							pack_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE PACKAGE GROUPS
				foreach(@$packgroup as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_packages (
							media_id,
							packgrp_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE DIGITAL PROFILE GROUPS
				foreach(@$digitalgroup as $value)
				{	
					# INSERT INFO INTO THE DATABASE
					@mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_digital_sizes (media_id,dsgrp_id) VALUES ('{$saveid}','{$value}')");
				}
				
				# SAVE PRODUCTS
				foreach(@$proditem as $key => $value)
				{
					$prod_price_clean = $cleanvalues->currency_clean(${'prod_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_products (
							media_id,
							prod_id,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity
							) VALUES (
							'$saveid',
							'$value',
							'$prod_price_clean',
							'".${'prod_price_calc_'.$value}."',
							'".${'prod_credits_'.$value}."',
							'".${'prod_credits_calc_'.$value}."',
							'".${'prod_customized_'.$value}."',
							'".${'prod_quantity_'.$value}."'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE PRODUCT GROUPS
				foreach(@$prodgroup as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_products (
							media_id,
							prodgrp_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE PRINTS
				foreach(@$printitem as $key => $value)
				{	
					$print_price_clean = $cleanvalues->currency_clean(${'print_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_prints (
							media_id,
							print_id,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity
							) VALUES (
							'$saveid',
							'$value',
							'$print_price_clean',
							'".${'print_price_calc_'.$value}."',
							'".${'print_credits_'.$value}."',
							'".${'print_credits_calc_'.$value}."',
							'".${'print_customized_'.$value}."',
							'".${'print_quantity_'.$value}."'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE PRINT GROUPS
				foreach(@$printgroup as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_prints (
							media_id,
							printgrp_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE MEDIA GROUPS
				foreach(@$mediagroups as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}groupids (
							item_id,
							group_id,
							mgrarea
							) VALUES (
							'$saveid',
							'$value',
							'media'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				/*
				# SAVE DIGITAL SIZE GROUPS
				foreach($dspgroup as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_digital_sizes (
							media_id,
							dsgrp_id
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				*/
				
				# SAVE DIGITAL SIZES
				foreach(@$digitalsp as $key => $value)
				{					
					$dsp_price_clean = $cleanvalues->currency_clean(${'dsp_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_digital_sizes (
							media_id,
							ds_id,
							license,
							rm_license,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity,
							auto_create
							) VALUES (
							'$saveid',
							'$value',
							'".${'dsp_license_'.$value}."',
							'".${'dsp_rm_license_'.$value}."',
							'$dsp_price_clean',
							'".${'dsp_price_calc_'.$value}."',
							'".${'dsp_credits_'.$value}."',
							'".${'dsp_credits_calc_'.$value}."',
							'".${'dsp_customized_'.$value}."',
							'".${'dsp_quantity_'.$value}."',
							'{$autoCreateAvailable}'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# SAVE IPTC INFO
				if($iptc)
				{
					$date_created_year = substr($iptc['date_created'],0,4);
					$date_created_month = substr($iptc['date_created'],4,2);
					$date_created_day = substr($iptc['date_created'],6,2);
					$date_created = "$date_created_year-$date_created_month-$date_created_day 00:00:00";
					
					//test($date_created);
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_iptc(
							media_id,
							description,
							title,
							instructions,
							date_created,
							author,
							creator_title,
							city,
							state,
							country,
							job_identifier,
							headline,
							provider,
							source,
							description_writer,
							urgency,
							copyright_notice
							) VALUES (
							'$saveid',
							'".$iptc['description']."',
							'".$iptc['title']."',
							'".$iptc['instructions']."',
							'$date_created',
							'".$iptc['author']."',
							'".$iptc['creator_title']."',
							'".$iptc['city']."',
							'".$iptc['state']."',
							'".$iptc['country']."',
							'".$iptc['job_identifier']."',
							'".$iptc['headline']."',
							'".$iptc['provider']."',
							'".$iptc['source']."',
							'".$iptc['description_writer']."',
							'".$iptc['urgency']."',
							'".$iptc['copyright_notice']."'
							)";
					$result = mysqli_query($db,$sql);	
				}
				
				# SAVE IPTC INFO
				if($exif)
				{	
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_exif(
							media_id,
							FileName,
							FileDateTime,
							FileSize,
							FileType,
							MimeType,
							SectionsFound,
							ImageDescription,
							Make,
							Model,
							Orientation,
							XResolution,
							YResolution,
							ResolutionUnit,
							Software,
							DateTime,
							YCbCrPositioning,
							Exif_IFD_Pointer,
							GPS_IFD_Pointer,
							ExposureTime,
							FNumber,
							ExposureProgram,
							ISOSpeedRatings,
							ExifVersion,
							DateTimeOriginal,
							DateTimeDigitized,
							ComponentsConfiguration,
							ShutterSpeedValue,
							ApertureValue,
							MeteringMode,
							Flash,
							FocalLength,
							FlashPixVersion,
							ColorSpace,
							ExifImageWidth,
							ExifImageLength,
							SensingMethod,
							ExposureMode,
							WhiteBalance,
							SceneCaptureType,
							Sharpness,
							GPSLatitudeRef,
							GPSLatitude_0,
							GPSLatitude_1,
							GPSLatitude_2,
							GPSLongitudeRef,
							GPSLongitude_0,
							GPSLongitude_1,
							GPSLongitude_2,
							GPSTimeStamp_0,
							GPSTimeStamp_1,
							GPSTimeStamp_2,
							GPSImgDirectionRef,
							GPSImgDirection
							) VALUES (
							'$saveid',
							'".$exif['FileName']."',
							'".$exif['FileDateTime']."',
							'".$exif['FileSize']."',
							'".$exif['FileType']."',
							'".$exif['MimeType']."',
							'".$exif['SectionsFound']."',
							'".$exif['ImageDescription']."',
							'".$exif['Make']."',
							'".$exif['Model']."',
							'".$exif['Orientation']."',
							'".$exif['XResolution']."',
							'".$exif['YResolution']."',
							'".$exif['ResolutionUnit']."',
							'".$exif['Software']."',
							'".$exif['DateTime']."',
							'".$exif['YCbCrPositioning']."',
							'".$exif['Exif_IFD_Pointer']."',
							'".$exif['GPS_IFD_Pointer']."',
							'".$exif['ExposureTime']."',
							'".$exif['FNumber']."',
							'".$exif['ExposureProgram']."',
							'".$exif['ISOSpeedRatings']."',
							'".$exif['ExifVersion']."',
							'".$exif['DateTimeOriginal']."',
							'".$exif['DateTimeDigitized']."',
							'".$exif['ComponentsConfiguration']."',
							'".$exif['ShutterSpeedValue']."',
							'".$exif['ApertureValue']."',
							'".$exif['MeteringMode']."',
							'".$exif['Flash']."',
							'".$exif['FocalLength']."',
							'".$exif['FlashPixVersion']."',
							'".$exif['ColorSpace']."',
							'".$exif['ExifImageWidth']."',
							'".$exif['ExifImageLength']."',
							'".$exif['SensingMethod']."',
							'".$exif['ExposureMode']."',
							'".$exif['WhiteBalance']."',
							'".$exif['SceneCaptureType']."',
							'".$exif['Sharpness']."',
							'".$exif['GPSLatitudeRef']."',
							'".$exif['GPSLatitude'][0]."',
							'".$exif['GPSLatitude'][1]."',
							'".$exif['GPSLatitude'][2]."',
							'".$exif['GPSLongitudeRef']."',
							'".$exif['GPSLongitude'][0]."',
							'".$exif['GPSLongitude'][1]."',
							'".$exif['GPSLongitude'][2]."',
							'".$exif['GPSTimeStamp'][0]."',
							'".$exif['GPSTimeStamp'][1]."',
							'".$exif['GPSTimeStamp'][2]."',
							'".$exif['GPSImgDirectionRef']."',
							'".$exif['GPSImgDirection']."'
							)";
					$result = mysqli_query($db,$sql);	
				}
				
				# SAVE KEYWORDS
				foreach(@$keyword_DEFAULT as $key => $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}keywords (
							media_id,
							keyword
							) VALUES (
							'$saveid',
							'$value'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				//$_SESSION['testing']['iptc'] = $iptc;
				
				# SAVE IPTC KEYWORDS
				foreach(@$iptc['keywords'] as $key => $value)
				{
					if(!in_array($key,$keyword_DEFAULT))
					{						
						if($config['keywordsToLower'])
						{
							//$keyDB = strtolower($value);
							
							if($langset['id'] == 'russian')
								$keyDB = mb_convert_case($value, MB_CASE_LOWER, "UTF-8");
							else
								$keyDB = strtolower($value);							
						}
						else
							$keyDB = $value;
						
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}keywords (
								media_id,
								keyword
								) VALUES (
								'$saveid',
								'$keyDB'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# EXPLODE EXIF KEYWORDS
				if($exif['Keywords'])
				{
					$exifKeywords = explode(";",$exif['Keywords']);
				}
				
				foreach(@$exifKeywords as $key => $value)
				{
					if(!in_array($key,$keyword_DEFAULT))
					{						
						if($config['keywordsToLower'])
						{
							//$keyDB = strtolower($value);
							
							if($langset['id'] == 'russian')
								$keyDB = mb_convert_case($value, MB_CASE_LOWER, "UTF-8");
							else
								$keyDB = strtolower($value);							
						}
						else
							$keyDB = $value;
						
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}keywords (
								media_id,
								keyword
								) VALUES (
								'$saveid',
								'$keyDB'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# ADD SUPPORT FOR ADDITIONAL KEYWORD LANGUAGES
				foreach($active_langs as $value)
				{ 
					$value = strtoupper($value);
					foreach(${'keyword_'.$value} as $key2 => $value2)
					{
						# INSERT INFO INTO THE DATABASE
						$sql = "INSERT INTO {$dbinfo[pre]}keywords (
								media_id,
								keyword,
								language
								) VALUES (
								'$saveid',
								'$value2',
								'$value'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# SAVE REFERENCE IF ICON EXISTS
				if($icon_success)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_thumbnails (
							media_id,
							thumbtype,
							thumb_filename,
							thumb_width,
							thumb_height,
							thumb_filesize
							) VALUES (
							'$saveid',
							'icon',
							'$move_icon_name',
							'$icon_width',
							'$icon_height',
							'$icon_filesize'
							)";
					$result = mysqli_query($db,$sql);
				}
				# SAVE REFERENCE IF THUMB EXISTS
				if($thumb_success)
				{	
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_thumbnails (
							media_id,
							thumbtype,
							thumb_filename,
							thumb_width,
							thumb_height,
							thumb_filesize
							) VALUES (
							'$saveid',
							'thumb',
							'$move_thumb_name',
							'$thumb_width',
							'$thumb_height',
							'$thumb_filesize'
							)";
					$result = mysqli_query($db,$sql);
				}
				# SAVE REFERENCE IF SAMPLE EXISTS
				if($sample_success)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_samples (
							media_id,
							sample_filename,
							sample_width,
							sample_height,
							sample_filesize
							) VALUES (
							'$saveid',
							'$move_sample_name',
							'$sample_width',
							'$sample_height',
							'$sample_filesize'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				# RECORD ACTIVITY
				if($batch_updated)
				{
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_add_media'],1,$mgrlang['gen_import'] . " > <strong>$batch_id</strong>");
				}
				
				$data['myfile']['status'][] = $status;
				$data['myfile']['errormessage'][] = implode(",",$errormessage);
				$data['myfile']['errorcode'][] = $errorcode;
				$data['myfile']['thumbstatus'][] = $thumbstatus;
				$data['myfile']['thumbpath'][] = $thumbpath;
				//$data['myfile']['message'][] = 'Testing';
				$data['myfile']['filepath'][] = $clean_filename;
				
				# SET SITE MENU BUILD TO 0
				menuBuild(0);
				
				echo $json->encode($data);
			break;
			
			case "create_directory":
				# REQUIRE FOLDER BUILDER CLASS FILE
				require_once($config['base_path'] . '/assets/classes/foldertools.php');
				
				# ENCRYPT
				$encrypted = $_GET['encrypted'];
				
				/* OUTPUT FOR TESTING
				echo "<script language='javascript' type='text/javascript'>alert('blah $encrypted');</script>";
				exit;
				*/
				
				# STORAGE ID
				$storage_id = $_GET['storage'];
				
				# CLEAN FOLDER NAME
				$cleanname = clean_foldername($_GET['folder']);
				
				# ENCRYPTED NAME
				$enc_name = md5($cleanname . $config['settings']['serial_number']);
				
				# CHECK TO SEE IF THERE IS A FOLDER WITH THIS NAME BUT IN A DIFFERENT STORAGE LOCATION
				$folder_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}folders WHERE name = '$cleanname'");
				$folder_rows = mysqli_num_rows($folder_result);
				$folder = mysqli_fetch_object($folder_result);
				
				if($folder_rows > 0)
				{
					# CHECK FOR WRONG STORAGE LOCATION
					if($folder->storage_id != $storage_id)
					{
						if($folder->storage_id == 0)
						{
							$storage_name = $mgrlang['gen_local'];
						}
						else
						{
							$storage_result = mysqli_query($db,"SELECT storage_id,name FROM {$dbinfo[pre]}storage WHERE storage_id = '$folder->storage_id'");
                       		$storage = mysqli_fetch_object($storage_result);
							$storage_name = $storage->name;
						}
						
						echo "<script language='javascript' type='text/javascript'>simple_message_box('{$mgrlang[folder_exists]} ($storage_name). {$mgrlang[folder_exists2]}','');</script>";
						exit;
					}
					else
					{	
						# NEED TO CHECK AND MAKE SURE FOLDER REALLY DOES EXIST [todo]
						# FOLDER ALREADY EXISTS - NO REASON TO CONTINUE
						echo "<script language='javascript' type='text/javascript'>$('folder_id').setValue('$folder->folder_id'); openworkbox();</script>";
						exit;
					}
				}
				
				# NEW FOLDER BUILDER OBJECT
				$creator = new folder_builder($cleanname,$encrypted,$storage_id);
				
				# MAKE SURE THE FOLDER DOESN'T ALREADY EXIST
				if($creator->check_for_dup())
				{
					echo "<script language='javascript' type='text/javascript'>$('folder_id').setValue('$folder->folder_id'); openworkbox();</script>";
					//alert('folder already exists. no need to continue?');
					exit;
				}
				
				# CREATE STORAGE DIRECTORIES
				if($storage_id and !$creator->check_errors())
				{						
					if(!$creator->storage_directory($storage_id,'create',''))
					{
						echo "<script language='javascript' type='text/javascript'>alert('could not create storage directory');</script>";
						exit;
					}
				}
				
				# CREATE LOCAL DIRECTORIES
				if(!$creator->create_local_directories())
				{
					echo "<script language='javascript' type='text/javascript'>alert('could not local directories');</script>";
					exit;
					//$creator->return_errors();
				}
					
				# ONE LAST ERROR CHECK JUST IN CASE
				if($creator->check_errors())
				{
					echo "<script language='javascript' type='text/javascript'>alert('there was an error. not sure what.');</script>";
					exit;
				}
				
				# CREATE UNIQUE FOLDER ID
				$ufolder_id = create_unique2();
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}folders (
						name,
						ufolder_id,
						folder_notes,
						storage_id,
						enc_name,
						encrypted
						) VALUES (
						'$cleanname',
						'$ufolder_id',
						'$folder_notes',
						'$storage_id',
						'$enc_name',
						'$encrypted'
						)";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);
								
				# EVERYTHING COMPLETED OK - OPEN THE WORKBOX
				echo "<script language='javascript' type='text/javascript'>$('folder_id').setValue('$saveid'); openworkbox();</script>";
				
			break;
			
			case "dps_details":
				//sleep(2);
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				//echo 'price: '.$_GET['price'].'<br>';
				//echo 'credits: '.$_GET['credits'];
				
				if($_GET['customized'])
				{
					$license = $_GET['license'];
					$rm_license = $_GET['rm_license'];
					$price = $_GET['price'];
					$credits = $_GET['credits'];
					$quantity = $_GET['quantity'];
					$price_calc = $_GET['price_calc'];
					$credits_calc = $_GET['credits_calc'];
					
					$width = $_GET['width'];
					$height = $_GET['height'];
					$format = $_GET['format'];
					$hd = $_GET['hd'];
					$fps = $_GET['fps'];
					$running_time = $_GET['running_time'];

					$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '$_GET[dsp_id]'");
					$digital_sp_rows = mysqli_num_rows($digital_sp_result);
					$digital_sp = mysqli_fetch_object($digital_sp_result);					
				}
				else
				{
					$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '$_GET[dsp_id]'");
					$digital_sp_rows = mysqli_num_rows($digital_sp_result);
					$digital_sp = mysqli_fetch_object($digital_sp_result);
					
					$license = $digital_sp->license;
					$rm_license = $digital_sp->rm_license;
					$price = $digital_sp->price;
					$credits = $digital_sp->credits;
					$quantity = "";
					$price_calc = $digital_sp->price_calc;
					$credits_calc = $digital_sp->credits_calc;
				}
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
				//$cleanvalues->decimal_places = 4;
				//$cleanvalues->strip_ezeros = 1;
				
				//echo $license; exit;
			
				echo "<div style='padding: 20px 15px 15px 15px;'>";
					//echo "$license | $_GET[customized]";
					//echo "license, price, credits, quantity<br /><br /><br />";
					echo "<img src='./images/mgr.button.close2.png' style='float: right; border: 0; cursor: pointer; margin: -15px -10px 0 0;' onclick=\"close_dsp_window('".$_GET['dsp_id']."');\" />";
			?>
                    <input type="hidden" name="dsp_custom_type" id="dsp_custom_type" value="<?php echo $digital_sp->dsp_type; ?>"  />
					<div style="padding-bottom: 10px;">
                        <strong><?php echo $mgrlang['dsp_f_license']; ?>:</strong><br />
                        <select name="dsp_license" id="dsp_license" onchange="update_dsp_license();" style="width: 298px;">
                            <?php
								$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses");
								while($licenseDB = mysqli_fetch_assoc($licenseResult))
								{
									echo "<option ' value='{$licenseDB['lic_purchase_type']}-{$licenseDB['license_id']}' ";
									if($license == $licenseDB['license_id']) echo "selected='selected'";
									echo ">{$licenseDB[lic_name]}</option>";
								}
							?>
                        </select>
                    </div>
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                            <div style="padding-bottom: 10px;<?php if($license == 'cu' or $license == 'fr'){ echo "display: none;"; } ?>" id='dsp_price_div'>
                                <strong><?php echo $mgrlang['dsp_f_price']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="dsp_price_calc" name="dsp_price_calc" style="width: 110px;" onchange="price_preview('dsp');">
                                    <option value="norm" <?php if(@$price_calc == 'norm'){ echo "selected"; } ?>><?php echo $config['settings']['cur_denotation']; ?></option>
                                    <option value="add" <?php if(@$price_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$price_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$price_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='dsp_price_calc' id='dsp_price_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="dsp_price" id="dsp_price" onblur="update_input_cur('dsp_price');price_preview('dsp');" style="width: 60px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($price); ?>" onkeyup="price_preview('dsp');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span><br />
                            	<div id="dsp_price_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($price_calc == 'norm' or !$price_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
						if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
						{
					?>
                            <div style="padding-bottom: 10px;<?php if($license == 'cu' or $license == 'fr'){ echo "display: none;"; } ?>" id='dsp_credits_div'>
                                <strong><?php echo $mgrlang['dsp_f_credits']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="dsp_credits_calc" name="dsp_credits_calc" style="width: 110px;" onchange="credits_preview('dsp');">
                                    <option value="norm" <?php if(@$credits_calc == 'norm'){ echo "selected"; } ?>></option>
                                    <option value="add" <?php if(@$credits_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$credits_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$credits_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='dsp_credits_calc' id='dsp_credits_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="dsp_credits" id="dsp_credits" onblur="credits_preview('dsp');" style="width: 60px;" maxlength="50" value="<?php echo round($credits); ?>" onkeyup="credits_preview('dsp');" />
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>:  <strong><?php echo $config['settings']['default_credits']; ?></strong></span><br />
                                <div id="dsp_credits_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($credits_calc == 'norm' or !$credits_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
					?>
                    <div id='dsp_quantity_div' style="padding-bottom: 10px;">
                        <strong><?php echo $mgrlang['dsp_f_quantity']; ?>:</strong><br />
                        <input type="text" name="dsp_quantity" id="dsp_quantity" style="width: 100px;" maxlength="50" value="<?php echo $quantity; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['leave_blank_quan']; ?></span>
                    </div>
					<div id='dsp_width_div' style="padding-bottom: 10px;">
                        <strong><?php echo $mgrlang['dsp_f_width']; ?>:</strong><br />
                        <input type="text" name="dsp_width" id="dsp_width" style="width: 100px;" maxlength="50" value="<?php echo $width; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['original_prof_set']; ?></span>
                    </div>
					<div id='dsp_height_div' style="padding-bottom: 10px;">
                        <strong><?php echo $mgrlang['dsp_f_height']; ?>:</strong><br />
                        <input type="text" name="dsp_height" id="dsp_height" style="width: 100px;" maxlength="50" value="<?php echo $height; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['original_prof_set']; ?></span>
                    </div>
					<div id='dsp_format_div' style="padding-bottom: 10px;">
                        <strong><?php echo $mgrlang['dsp_f_format']; ?>:</strong><br />
                        <input type="text" name="dsp_format" id="dsp_format" style="width: 100px;" maxlength="50" value="<?php echo $format; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['original_prof_set']; ?></span>
                    </div>
					<?php
						if($digital_sp->dsp_type == 'video')
						{
					?>
						<div id='dsp_hd_div' style="padding-bottom: 10px;">
							<strong><?php echo $mgrlang['dsp_f_hd']; ?>:</strong><br />
							<input type="checkbox" name="dsp_hd" id="dsp_hd" value="1" <?php if($hd){ echo "checked='checked'"; }?> />
							<br /><span style='color: #999;'></span>
						</div>
						<div id='dsp_running_time_div' style="padding-bottom: 10px;">
							<strong><?php echo $mgrlang['dsp_f_running_time']; ?>:</strong><br />
							<input type="text" name="dsp_running_time" id="dsp_running_time" style="width: 100px;" maxlength="50" value="<?php echo $running_time; ?>" /> <?php echo $mgrlang['gen_seconds']; ?>
							<br /><span style='color: #999;'><?php echo $mgrlang['original_prof_set']; ?></span>
						</div>
						<div id='dsp_fps_div' style="padding-bottom: 10px;">
							<strong><?php echo $mgrlang['dsp_f_fps']; ?>:</strong><br />
							<input type="text" name="dsp_fps" id="dsp_fps" style="width: 100px;" maxlength="50" value="<?php echo $fps; ?>" />
							<br /><span style='color: #999;'><?php echo $mgrlang['original_prof_set']; ?></span>
						</div>
					<?php
						}
					?>
            <?php
					echo "<script language='javascript'>";
					if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ echo "price_preview('dsp');"; }
					if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ echo "credits_preview('dsp');"; }
                    echo "</script>";
					echo "<div style='text-align: right; padding-top: 15px;'>";
						echo "<input type='button' value='{$mgrlang[remove_customization]}' onclick=\"remove_dsp_customization('".$_GET['dsp_id']."');\" />";
						echo "<input type='button' value='{$mgrlang[gen_b_save]}' onclick=\"save_dsp_customization('".$_GET['dsp_id']."');\" />";
						echo "<input type='button' value='{$mgrlang[gen_b_close]}' onclick=\"close_dsp_window('".$_GET['dsp_id']."');\" />";
					echo "</div>";
				echo "</div>";
			break;
			
			case "prod_details":
				//sleep(2);
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				if($_GET['customized'])
				{
					$price = $_GET['price'];
					$credits = $_GET['credits'];
					$quantity = $_GET['quantity'];
					$price_calc = $_GET['price_calc'];
					$credits_calc = $_GET['credits_calc'];
				}
				else
				{
					$prod_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}products WHERE prod_id = '$_GET[prod_id]'");
					$prod_rows = mysqli_num_rows($prod_result);
					$prod = mysqli_fetch_object($prod_result);
					$price = $prod->price;
					$credits = $prod->credits;
					$quantity = "";
					$price_calc = $prod->price_calc;
					$credits_calc = $prod->credits_calc;
				}
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
				echo "<div style='padding: 20px 15px 15px 15px;'>";
					echo "<img src='./images/mgr.button.close2.png' style='float: right; border: 0; cursor: pointer; margin: -15px -10px 0 0;' onclick=\"close_prod_window('".$_GET['prod_id']."');\" />";
			?>
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                            <div style="padding-bottom: 10px; white-space: nowrap" id='prod_price_div'>
                                <strong><?php echo $mgrlang['products_f_price']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="prod_price_calc" name="prod_price_calc" style="width: 135px;" onchange="price_preview('prod');">
                                    <option value="norm" <?php if(@$price_calc == 'norm'){ echo "selected"; } ?>><?php echo $config['settings']['cur_denotation']; ?></option>
                                    <option value="add" <?php if(@$price_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$price_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$price_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='prod_price_calc' id='prod_price_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="prod_price" id="prod_price" onblur="update_input_cur('prod_price');price_preview('prod');" style="width: 60px;" maxlength="50" value="<?php if($price > 0) {  echo @$cleanvalues->currency_display($price); } ?>" onkeyup="price_preview('prod');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span><br />
                            	<div id="prod_price_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($price_calc == 'norm' or !$price_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
						if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
						{
					?>
                            <div style="padding-bottom: 10px;<?php if($license == 'cu' or $license == 'fr'){ echo "display: none;"; } ?>" id='prod_credits_div'>
                                <strong><?php echo $mgrlang['products_f_credits']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="prod_credits_calc" name="prod_credits_calc" style="width: 135px;" onchange="credits_preview('prod');">
                                    <option value="norm" <?php if(@$credits_calc == 'norm'){ echo "selected"; } ?>></option>
                                    <option value="add" <?php if(@$credits_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$credits_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$credits_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='prod_credits_calc' id='prod_credits_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="prod_credits" id="prod_credits" onblur="credits_preview('prod');" style="width: 60px;" maxlength="50" value="<?php echo $cleanvalues->number_display($credits); ?>" onkeyup="credits_preview('prod');" />
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>:  <strong><?php echo $config['settings']['default_credits']; ?></strong></span><br />
                                <div id="prod_credits_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($credits_calc == 'norm' or !$credits_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
					?>
                    <div id='prod_quantity_div'>
                        <strong><?php echo $mgrlang['quan_available']; ?>:</strong><br />
                        <input type="text" name="prod_quantity" id="prod_quantity" style="width: 112px;" maxlength="50" value="<?php echo $quantity; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['leave_blank_quan']; ?></span>
                    </div>
            <?php
					echo "<script language='javascript'>";
					if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ echo "price_preview('prod');"; }
					if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_prod']){ echo "credits_preview('prod');"; }
                    echo "</script>";
					echo "<div style='text-align: right; padding-top: 15px;'>";
						echo "<input type='button' value='{$mgrlang[remove_customization]}' onclick=\"remove_prod_customization('".$_GET['prod_id']."');\" />";
						echo "<input type='button' value='{$mgrlang[gen_b_save]}' onclick=\"save_prod_customization('".$_GET['prod_id']."');\" />";
						echo "<input type='button' value='{$mgrlang[gen_b_close]}' onclick=\"close_prod_window('".$_GET['prod_id']."');\" />";
					echo "</div>";
				echo "</div>";
			break;
			
			case "print_details":
				//sleep(2);
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				if($_GET['customized'])
				{
					$price = $_GET['price'];
					$credits = $_GET['credits'];
					$quantity = $_GET['quantity'];
					$price_calc = $_GET['price_calc'];
					$credits_calc = $_GET['credits_calc'];
				}
				else
				{
					$print_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}prints WHERE print_id = '$_GET[print_id]'");
					$print_rows = mysqli_num_rows($print_result);
					$print = mysqli_fetch_object($print_result);
					$price = $print->price;
					$credits = $print->credits;
					$quantity = "";
					$price_calc = $print->price_calc;
					$credits_calc = $print->credits_calc;
				}
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
				echo "<div style='padding: 20px 15px 15px 15px;'>";
					echo "<img src='./images/mgr.button.close2.png' style='float: right; border: 0; cursor: pointer; margin: -15px -10px 0 0;' onclick=\"close_print_window('".$_GET['print_id']."');\" />";
			?>
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                            <div style="padding-bottom: 10px; white-space: nowrap" id='print_price_div'>
                                <strong><?php echo $mgrlang['prints_f_price']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="print_price_calc" name="print_price_calc" style="width: 135px;" onchange="price_preview('print');">
                                    <option value="norm" <?php if(@$price_calc == 'norm'){ echo "selected"; } ?>><?php echo $config['settings']['cur_denotation']; ?></option>
                                    <option value="add" <?php if(@$price_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$price_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$price_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='print_price_calc' id='print_price_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="print_price" id="print_price" onblur="update_input_cur('print_price');price_preview('print');" style="width: 60px;" maxlength="50" value="<?php if($price > 0) { echo @$cleanvalues->currency_display($price); } ?>" onkeyup="price_preview('print');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span><br />
                            	<div id="print_price_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($price_calc == 'norm' or !$price_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
						if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
						{
					?>
                            <div style="padding-bottom: 10px;<?php if($license == 'cu' or $license == 'fr'){ echo "display: none;"; } ?>" id='print_credits_div'>
                                <strong><?php echo $mgrlang['prints_f_credits']; ?>:</strong><br />
                                <?php
                                    if($config['settings']['flexpricing'] == 1){
                                ?>
                                <select id="print_credits_calc" name="print_credits_calc" style="width: 135px;" onchange="credits_preview('print');">
                                    <option value="norm" <?php if(@$credits_calc == 'norm'){ echo "selected"; } ?>></option>
                                    <option value="add" <?php if(@$credits_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( + )</option>
                                    <option value="sub" <?php if(@$credits_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( - )</option>
                                    <option value="mult" <?php if(@$credits_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( x )</option>
                                </select>
                                <?php
                                    } else {
                                        echo "<input type='hidden' name='print_credits_calc' id='print_credits_calc' value='norm' />";
                                    }
                                ?>
                                <input type="text" name="print_credits" id="print_credits" onblur="credits_preview('print');" style="width: 60px;" maxlength="50" value="<?php echo $cleanvalues->number_display($credits); ?>" onkeyup="credits_preview('print');" />
                                <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>:  <strong><?php echo $config['settings']['default_credits']; ?></strong></span><br />
                                <div id="print_credits_preview" style="border: 1px solid #d3d3d3; background-color: #eee; padding: 5px; font-size: 11px; color: #666; <?php if($credits_calc == 'norm' or !$credits_calc){ echo "display: none;"; } ?>"></div>
                            </div>
                    <?php
						}
					?>
                    <div id='print_quantity_div'>
                        <strong><?php echo $mgrlang['quan_available']; ?>:</strong><br />
                        <input type="text" name="print_quantity" id="print_quantity" style="width: 112px;" maxlength="50" value="<?php echo $quantity; ?>" />
                        <br /><span style='color: #999;'><?php echo $mgrlang['leave_blank_quan']; ?></span>
                    </div>
            <?php
					echo "<script language='javascript'>";
					if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ echo "price_preview('print');"; }
					if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print']){ echo "credits_preview('print');"; }
                    echo "</script>";
					echo "<div style='text-align: right; padding-top: 15px;'>";
						echo "<input type='button' value='{$mgrlang[remove_customization]}' onclick=\"remove_print_customization('".$_GET['print_id']."');\" />";
						echo "<input type='button' value='{$mgrlang[gen_b_save]}' onclick=\"save_print_customization('".$_GET['print_id']."');\" />";
						echo "<input type='button' value='$mgrlang[gen_b_close]' onclick=\"close_print_window('".$_GET['print_id']."');\" />";
					echo "</div>";
				echo "</div>";
			break;
		}	
?>
