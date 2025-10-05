<?php
	###################################################################
	####	PRODUCT ACTIONS                              		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "products";
		
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
		switch($_REQUEST['mode'])
		{
			# SET ACTIVE STATUS
			default:
			case "galleries":
				
				if($_GET['gal_mem'])
				{
					$gal_mem = $_GET['gal_mem'];
				}
				else
				{
					$gal_mem = 0;	
				}
				
				$id = $_GET['id'];
				
				# PULL PRODUCT DETAILS FROM DB	
				if($_GET['id'] != "new"){
					$prod_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}products WHERE prod_id = '$_GET[id]'");
					$prod_rows = mysqli_num_rows($prod_result);
					$prod = mysqli_fetch_object($prod_result);
				}
				
				$selected_gals_array = array();
				
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
				
				$ig_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_galleries WHERE item_id = '$_GET[id]' AND mgrarea = '$page'");
				$ig_rows = mysqli_num_rows($ig_result);
				while($ig = mysqli_fetch_object($ig_result))
				{
					$selected_gals_array[] = $ig->gallery_id;
				}
				
				echo "<div style=\"padding: 7px 0 7px 10px; margin: 0px; background-color: #eee\">";
				echo "<input type='checkbox' name='all_galleries' id='all_galleries' value='1' class='radio' style='margin: -4px 0 0 4px; vertical-align: middle' onclick='display_in_all_check();'";
				if($prod->all_galleries == 1){ echo "checked"; }
				echo " /> <label for='all_galleries' class='gallery_label'>{$mgrlang[all_galleries]}</label></div>";
				
			
				//$gallery_parent = $gallery->parent_gal;
				$gallery_current = 0;
				
				# BUILD THE GALLERIES AREA
				$mygalleries = new build_galleries;
				$mygalleries->options_name = 'selected_galleries[]';
				$mygalleries->scroll_offset_id = "parentgal";
				$mygalleries->scroll_offset = 0;
				$mygalleries->options = "checkbox";
				$mygalleries->selected_gals = $selected_gals_array;
				$mygalleries->output_struc_array(0);
			break;			
			case "display_ip_list":
				//sleep(1);
				$ip_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$_GET[id]' AND mgrarea = 'prod' ORDER BY ip_id DESC");
				$ip_rows = mysqli_num_rows($ip_result);
				while($ip = mysqli_fetch_object($ip_result))
				{
					$prod = zerofill($_GET['id'],4);
					$ip_id = zerofill($ip->ip_id,4);				
					$src_img = "prod".$prod."_ip".$ip_id."_small.jpg";				
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
						echo "<div class='ip_div_inner' style='width: ".$newsize[0]."px;'><img src='mgr.products.actions.php?mode=display_ip&prod=$_GET[id]&ip_id=$ip->ip_id' /></div>";
						echo "<div><a href='javascript:delete_ip($ip->ip_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a></div>";
					echo "</div>";
				}
			break;
			case "display_ip":
				//$src = urldecode($_GET['img']);
				$prod = zerofill($_GET['prod'],4);
				$ip_id = zerofill($_GET['ip_id'],4);				
				$src_img = "prod".$prod."_ip".$ip_id."_small.jpg";				
				$src = realpath("../assets/item_photos/$src_img");
				$image = new imagetools($src);
				$image->size = 150;
				$image->createImage(1,'');
			break;
			case "delete_ip":
				delete_item_photo('prod',$_GET['prod'],$_GET['ip_id']);
				echo "<script>load_ip();</script>";
			break;
			# SET ACTIVE STATUS
			case "ac":
				$prod_result = mysqli_query($db,"SELECT active,item_name FROM {$dbinfo[pre]}products where prod_id = '$_REQUEST[id]'");
				$prod = mysqli_fetch_object($prod_result);
				
				# FLIP THE VALUE
				$new_value = (empty($prod->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}products SET active='$new_value' where prod_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				$save_type = ($new_value==1) ? $mgrlang['gen_active'] : $mgrlang['gen_inactive'];
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_products'],1,$save_type . " > <strong>$prod->item_name ($_REQUEST[id])</strong>");

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			# SHOW DETAILS
			case "details":
				$prod_result = mysqli_query($db,"SELECT notes,description FROM {$dbinfo[pre]}products where prod_id = '$_REQUEST[id]'");
				$prod = mysqli_fetch_object($prod_result);
				
				echo "<div style='padding: 20px;'>";
					if($prod->notes){ echo "<strong>{$mgrlang[gen_notes]}:</strong> $prod->notes<br /><br />"; }
					if($prod->description){ echo "<strong>{$mgrlang[gen_description]}:</strong> $prod->description"; }
					if(!$prod->notes and !$prod->description){ echo $mgrlang['gen_no_notes']; }
				echo "</div>";
			break;
		}	
?>
