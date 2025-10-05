<?php
	###################################################################
	####	PRINT PRICING SCHEMES ACTIONS                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 4-4-2008                                      ####
	####	Modified: 4-14-2008                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
		$page = "prints";
		$lnav = "library";
		
		$supportPageID = 0;
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
		//error_reporting(0);											# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		//include_lang();												# INCLUDE THE LANGUAGE FILE			
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		
		
		
		# INCLUDE IMAGETOOLS FILE
		require_once('../assets/classes/imagetools.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		switch($_GET['pmode']){
			case "print_row":				
				# PRINTS
				$sql = "UPDATE {$dbinfo[pre]}prints SET active='0' where print_id  = '$_GET[id]'";
				$result = mysqli_query($db,$sql);
				# GET GROUPS
				$grp_result = mysqli_query($db,"SELECT prg_id FROM {$dbinfo[pre]}print_grp WHERE parent_id = '$_GET[id]'");
				$grp_rows = mysqli_num_rows($grp_result);
				while($grp = mysqli_fetch_object($grp_result)){
					# OPTIONS
					$sql = "UPDATE {$dbinfo[pre]}print_option SET active='0' where parent_id  = '$grp->prg_id'";
					$result = mysqli_query($db,$sql);
					# GROUP
					$sql = "UPDATE {$dbinfo[pre]}print_grp SET active='0' where prg_id  = '$grp->prg_id'";
					$result = mysqli_query($db,$sql);
				}
			break;
			case "print_group":
				# GROUPS
				$sql = "UPDATE {$dbinfo[pre]}print_grp SET active='0' where prg_id  = '$_GET[id]'";
				$result = mysqli_query($db,$sql);
				# OPTIONS
				$sql = "UPDATE {$dbinfo[pre]}print_option SET active='0' where parent_id  = '$_GET[id]'";
				$result = mysqli_query($db,$sql);
			break;
			case "print_option":
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}print_option SET active='0' where op_id  = '$_GET[id]'";
				$result = mysqli_query($db,$sql);
			break;
			case "display_ip_list":

				$ip_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$_GET[id]' AND mgrarea = 'print' ORDER BY ip_id DESC");
				$ip_rows = mysqli_num_rows($ip_result);
				while($ip = mysqli_fetch_object($ip_result))
				{
					$print = zerofill($_GET['id'],4);
					$ip_id = zerofill($ip->ip_id,4);				
					$src_img = "print".$print."_ip".$ip_id."_small.jpg";				
					$src = realpath("../assets/item_photos/$src_img");
					# GET WIDTH
					$size = getimagesize($src);	
					
					$newsize = get_scaled_size(150,$src);
					
					echo "<div class='ip_div' id='ip_$ip->ip_id'>";
						//'../assets/item_photos/prod0022_ip0012_med.jpg'
						echo "<div class='ip_div_inner' style='width: ".$newsize[0]."px;'><img src='mgr.prints.actions.php?pmode=display_ip&print=$_GET[id]&ip_id=$ip->ip_id' /></div>";
						echo "<div><a href='javascript:delete_ip($ip->ip_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a></div>";
					echo "</div>";
				}
			break;
			case "display_ip":
				//$src = urldecode($_GET['img']);
				$print = zerofill($_GET['print'],4);
				$ip_id = zerofill($_GET['ip_id'],4);				
				$src_img = "print".$print."_ip".$ip_id."_small.jpg";				
				$src = realpath("../assets/item_photos/$src_img");
				$image = new imagetools($src);
				$image->size = 150;
				$image->createImage(1,'');
			break;
			case "delete_ip":
				delete_item_photo('print',$_GET['print'],$_GET['ip_id']);
				/*echo "<script>load_ip();</script>";*/
			break;
			# SET ACTIVE STATUS
			case "ac":
				$print_result = mysqli_query($db,"SELECT active,item_name FROM {$dbinfo[pre]}prints where print_id = '$_REQUEST[id]'");
				$print = mysqli_fetch_object($print_result);
				
				# FLIP THE VALUE
				$new_value = (empty($print->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}prints SET active='$new_value' where print_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				$save_type = ($new_value==1) ? $mgrlang['gen_active'] : $mgrlang['gen_inactive'];
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_prints'],1,$save_type . " > <strong>$print->item_name ($_REQUEST[id])</strong>");

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			# SHOW DETAILS
			case "details":
				$print_result = mysqli_query($db,"SELECT notes,description FROM {$dbinfo[pre]}prints where print_id = '$_REQUEST[id]'");
				$print = mysqli_fetch_object($print_result);
				
				echo "<div style='padding: 20px;'>";
					if($print->notes){ echo "<strong>{$mgrlang[gen_notes]}:</strong> $print->notes<br /><br />"; }
					if($print->description){ echo "<strong>{$mgrlang[gen_description]}:</strong> $print->description"; }
					if(!$print->notes and !$print->description){ echo $mgrlang['gen_no_notes']; }
				echo "</div>";
			break;
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
					$print_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}prints WHERE print_id = '$_GET[id]'");
					$print_rows = mysqli_num_rows($print_result);
					$print = mysqli_fetch_object($print_result);
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
				if($print->all_galleries == 1){ echo "checked"; }
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
		}
		mysqli_close($db);
?>