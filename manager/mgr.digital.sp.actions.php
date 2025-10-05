<?php
	###################################################################
	####	DIGITAL SIZES ACTIONS                         		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "digital_sp";
		
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
		switch($_REQUEST['pmode'])
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
				
				# PULL DIGITAL SIZES DETAILS FROM DB	
				if($_GET['id'] != "new"){
					$dsp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '$_GET[id]'");
					$dsp_rows = mysqli_num_rows($dsp_result);
					$dsp = mysqli_fetch_object($dsp_result);
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
				if($dsp->all_galleries == 1){ echo "checked"; }
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
?>
