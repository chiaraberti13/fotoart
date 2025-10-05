<?php
	# INCLUDE THE SESSION START FILE
	require_once('../assets/includes/session.php');
	
	//sleep(2);

	$page = "galleries";
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	error_reporting(0);
	
	# INCLUDE SECURITY CHECK FILE
	require_once('mgr.security.php');
	
	# INCLUDE MANAGER CONFIG FILE
	require_once('mgr.config.php');
	
	# INCLUDE DATABASE CONFIG FILE
	if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
	
	# INCLUDE DATABASE CONNECTION FILE
	require_once('../assets/includes/db.conn.php');
	
	# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
	require_once('../assets/includes/shared.functions.php');
	
	# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.functions.php');
	
	# SELECT THE SETTINGS DATABASE
	require_once('mgr.select.settings.php');
	
	# INCLUDE IMAGETOOLS FILE
	require_once('../assets/classes/imagetools.php');
	
	# INCLUDE THE LANGUAGE FILE	
	include_lang();
	
	switch($_GET['mode'])
	{
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
			//echo $_GET['gal_mem'];
			// CREATE ARRAY TO WORK WITH							
			$folders = array();
			$folders['name'] = array();
			$folders['folder_id'] = array();
			$folders['parent_id'] = array();
			$folders['folder_rows'] = array();
			$folders['password'] = array();
			$folders['active'] = array();
			$folder_array_id = 1;
			
			// READ STRUCTURE FUNCTION															
			read_gal_structure(0,'name','',$gal_mem);
			//read_gal_structure(0,$listby,$listtype,$_SESSION['galmem']);
			
			if($_GET['curgal'] != "new")
			{
				$gallery_result = mysqli_query($db,"SELECT parent_gal FROM {$dbinfo[pre]}galleries WHERE gallery_id = '$_GET[curgal]'");
				$gallery = mysqli_fetch_object($gallery_result);
			}
			
			echo "<div style=\"padding: 0 0 0 10px; margin: 0px; background-color: #eee\">";
			echo "<img src=\"images/mgr.folder.icon.small2.gif\" align=\"absmiddle\" /> <input type='radio' name='parent_gal' id='parent_gal0' onclick=\"$('parentgal_override').setValue('0');\" value='0' class='radio' style='margin-left: -15px;'";
				if($_GET['curgal'] == "new" or $gallery->parent_gal == 0){
					echo " checked";
				}
			echo " /> <label for='parent_gal0' style='cursor: pointer'><strong>{$mgrlang[gen_none]}</strong></label></div>";
			
			if($gal_mem == 0) // Only if the gallery is owned by the admin
			{
				//$gallery_parent = $gallery->parent_gal;
				$gallery_current = $_GET['curgal'];
				
				# BUILD THE GALLERIES AREA
				$mygalleries = new build_galleries;			
				$mygalleries->selected_gals = array($gallery->parent_gal);			
				//$mygalleries->disable_all = 1;
				$mygalleries->scroll_offset_id = "parentgal";
				$mygalleries->scroll_offset = 0;			
				$mygalleries->override_input = 'parentgal_override';
				$mygalleries->options = "radio";
				$mygalleries->output_struc_array(0);
			}
		break;
		case "members":
			echo "<ul>";
			$mem_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE l_name LIKE '$_GET[selchar]%'");
			while($mem = mysqli_fetch_object($mem_result))
			{
				if($_GET['gal_mem'] == $mem->mem_id)
				{
					echo "<li><strong><a href='mgr.galleries.php?setmem=$mem->mem_id'>$mem->l_name, $mem->f_name ($mem->email)</a></strong></li>";
				}
				else
				{
					echo "<li><a href='mgr.galleries.php?setmem=$mem->mem_id'>$mem->l_name, $mem->f_name ($mem->email)</a></li>";
				}
			}
			echo "</ul>";
		break;
		case "display_ip_list":
			$ip_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$_GET[id]' AND mgrarea = 'gallery' ORDER BY ip_id DESC");
			$ip_rows = mysqli_num_rows($ip_result);
			while($ip = mysqli_fetch_object($ip_result))
			{
				$icon_id = $ip->ip_id;
				$gallery = zerofill($_GET['id'],4);
				$ip_id = zerofill($ip->ip_id,4);				
				$src_img = "gallery".$gallery."_ip".$ip_id."_small.jpg";				
				$src = realpath("../assets/item_photos/$src_img");
				# GET WIDTH
				$size = getimagesize($src);	
				
				$newsize = get_scaled_size(150,$src);
				
				echo "<div class='ip_div' id='ip_$ip->ip_id'>";
					//'../assets/item_photos/prod0022_ip0012_med.jpg'
					echo "<div class='ip_div_inner' style='width: ".$newsize[0]."px;'><img src='mgr.galleries.actions.php?mode=display_ip&gallery=$_GET[id]&ip_id=$ip->ip_id' /></div>";
					echo "<div><a href='javascript:delete_ip($ip->ip_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a></div>";
				echo "<input type='hidden' value='{$icon_id}' name='icon_id' />"; // Added so that I don't have to query the DB to grab the icon on the public side
				echo "</div>";
			}
			
		break;
		case "display_ip":
			//$src = urldecode($_GET['img']);
			$gallery = zerofill($_GET['gallery'],4);
			$ip_id = zerofill($_GET['ip_id'],4);				
			$src_img = "gallery".$gallery."_ip".$ip_id."_small.jpg";				
			$src = realpath("../assets/item_photos/$src_img");
			$image = new imagetools($src);
			$image->size = 150;
			$image->createImage(1,'');
		break;
		case "delete_ip":
			delete_item_photo('gallery',$_GET['gallery'],$_GET['ip_id']);
			
			updateGalleryVersion(); // Something has changed - update the gallery version
			
			$sql = "UPDATE {$dbinfo[pre]}galleries SET icon_id=0 where gallery_id = '$_GET[gallery]'";
			$result = mysqli_query($db,$sql);
			
		break;
		# SET ACTIVE STATUS
		case "ac":
			$gallery_result = mysqli_query($db,"SELECT active,name FROM {$dbinfo[pre]}galleries where gallery_id = '$_REQUEST[id]'");
			$gallery = mysqli_fetch_object($gallery_result);
			
			# FLIP THE VALUE
			$new_value = (empty($gallery->active) ? 1 : 0);	
						
			$sql = "UPDATE {$dbinfo[pre]}galleries SET active='$new_value' where gallery_id = '$_REQUEST[id]'";
			$result = mysqli_query($db,$sql);
			
			$save_type = ($new_value==1) ? $mgrlang['gen_active'] : $mgrlang['gen_inactive'];
			
			updateGalleryVersion(); // Something has changed - update the gallery version
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_galleries'],1,$save_type . " > <strong>$gallery->name ($_REQUEST[id])</strong>");

			echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
		break;
	}
?>


