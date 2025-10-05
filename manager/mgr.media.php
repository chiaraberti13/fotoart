<?php
	###################################################################
	####	MEDIA EDITOR                                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 5-5-2012                                     #### 
	###################################################################
	
		$page = "media";
		$lnav = "library";	
			
		$supportPageID = '316';
		
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php"))
		{			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		}
		else
		{ 											
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
		require_once('../assets/classes/mediatools.php');				# INCLUDE MEDIA TOOLS CLASS
		
		require_once('mgr.defaultcur.php');								# INCLUDE DEFAULT CURRENCY SETTINGS	
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			case "save_groups":
				save_groups($page,'media','media_id');				
			break;
			case "save_status":
				save_status($page,'media','media_id');
			break;
			case "contr_save_status":
				//print_r($_POST);
				//exit;
				
				if($_POST['set_to'] == 'selitems')
				{
					// Selected items
					$mediaIDs = explode(',',substr($_POST['selected_items'],0,-1));
					$mediaIDsFlat = implode(',',$mediaIDs);
					//echo $mediaIDsFlat; exit;
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET approval_status='{$_POST[status]}' WHERE media_id IN ({$mediaIDsFlat}) AND owner != 0");
					
					// Find the remaining number of pending media and update the session
					$_SESSION['pending_media'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner != '0' AND approval_status = '0'"));

				}
				else
				{
					// All items
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET approval_status='{$_POST[status]}' WHERE owner != 0");	
					
					// Find the remaining number of pending media and update the session
					$_SESSION['pending_media'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner != '0' AND approval_status = '0'"));
				}
				
			break;
			# DELETE
			case "del":
				if(!empty($_REQUEST['items']))
				{	
					$items = $_REQUEST['items'];
					if(!is_array($items))
					{
						$items = explode(",",$items);
					}				
					
					foreach($items as $value)
					{
						try
						{
							$media = new mediaTools($value);
							$media->deleteMedia();
						}
						catch(Exception $e)
						{
							echo $e->getMessage();
							exit;
						}
					}
					//$delete_array = implode(",",$items);
					//@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}members WHERE mem_id IN ($delete_array)");
					
					# UPDATE MENU BUILD MESSAGE
					menuBuild(0);
					
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_delsuc'];
					
					if(in_array("contr",$installed_addons)) // Update the pending count
						$_SESSION['pending_media'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner != '0' AND approval_status = '0'"));
					
					echo "</script>";
					
				}
				else
				{
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
		}
		
		/*
		* Set media owner
		*/
		if($_GET['owner'])
		{
			$_SESSION['mediaOwner'] = $_GET['owner'];
			$_SESSION['media_dtype'] = 'default'; // If you are setting owner then go back to default display type
		}
		
		if($_GET['ep'] or !$_SESSION['mediaOwner'])
			$_SESSION['mediaOwner'] = 0;
		
		# INCLUDE DATASORTS CLASS
		require_once("mgr.class.datasort.php");			
		$sortprefix="media";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "media_id";			
		require_once('mgr.datasort.logic.php');	
		
		# IF THIS IS AN ENTRY PAGE OR mediagroups IS BLANK RESET THE mediagroups SESSION	
		if($_GET['ep'] or empty($_SESSION['mediagroups'])){
			$_SESSION['mediagroups'] = array('all');
		}			
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_REQUEST['setgroups']){
			if(is_array($_REQUEST['setgroups'])){
				$_SESSION['mediagroups'] = $_REQUEST['setgroups'];
			} else {				
				$_SESSION['mediagroups'] = array($_REQUEST['setgroups']);
			}
		}
		
		# IF THIS IS AN ENTRY PAGE RESET THE SESSION DISPLAY TYPE
		if($_GET['ep'] == '1')
		{
			$_SESSION['media_dtype'] = 'default';			
			$_SESSION['currentpage'] = 1;
		}
		
		if($_REQUEST['dtype'] or $_GET['ep'])
			unset($_SESSION['collid']); // Clear collid session if dtype changes
		
		# IF THE DISPLAY TYPE IS RESET THROUGH GET UPDATE THE SESSION
		if($_REQUEST['dtype'])
		{
			$_SESSION['media_dtype'] = $_REQUEST['dtype'];
			# RESET THE CURRENT PAGE
			if(isset($_SESSION['currentpage']))
			{
				$_SESSION['currentpage'] = 1;
			}				
		}
		
		# CHECK TO SEE IF THE SEARCH FIELDS ARE GETTING SET
		if($_REQUEST['setsearchfields'])
		{
			$_SESSION['sfo_array'] = $_REQUEST['setsearchfields'];
		}
		else
		{
			// set it to all?
		}
		
		# IF THIS IS AN ENTRY PAGE RESET SESSION	
		if($_GET['ep'] or empty($_SESSION['sfo_array']) or $_REQUEST['dtype'] != 'search')
		{
			$_SESSION['sfo_array'] = array('all');
		}
		
		# SET TO LOCAL VALUE
		$sfo_array = $_SESSION['sfo_array'];
		
		# SET THE DEFAULT SESSION DISPLAY TYPE - MIGHT NOT BE NEEDED
		if(!$_SESSION['media_dtype'])
		{
			$_SESSION['media_dtype'] = 'default';
		}

		# Set gallery
		if($_GET['galid'])
		{
			$_SESSION['galid'] = $_GET['galid'];
		}
		if(!$_SESSION['galid'])
		{
			$_SESSION['galid'] = 0;
		}
		
		# Set lightbox
		if($_GET['lbid'])
		{
			$_SESSION['lbid'] = $_GET['lbid'];
		}
		
		# Set collection
		if($_GET['collid'])
		{
			$_SESSION['collid'] = $_GET['collid'];
		}
		
		# SEE IF ANY SEARCH HAS BEEN PASSED
		if(!empty($_REQUEST['search']))
		{
			$_SESSION['media_search'] = $_REQUEST['search'];
		}
		
		# FOR EASE MAKE THE VARIABLE LOCAL
		$insearch = $_SESSION['media_search'];
		
		# GET THE TOTAL NUMBER OF ROWS
		# DECIDE WHICH TYPE OF RECORDS TO PULL
		switch($_SESSION['media_dtype'])
		{
			default:
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]'"));
			break;
			
			case "featured":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE featured = '1'"));
			break;
			
			case "active":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE active = '1' AND owner = '$_SESSION[mediaOwner]'"));
			break;
			
			case "inactive":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE active = '0' AND owner = '$_SESSION[mediaOwner]'"));
			break;
			
			case "contr":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner != '0'"));
			break;
			
			case "pending":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE approval_status = '0'"));
			break;
			
			case "failed":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE approval_status = '2'"));
			break;
			
			case "collection":
				$collectionResult = mysqli_query($db,"SELECT colltype,coll_id FROM {$dbinfo[pre]}collections WHERE coll_id = {$_SESSION[collid]}");
				$collection = mysqli_fetch_object($collectionResult);
				
				if($collection->colltype == 1)
				{
					$collectionGalleriesResult = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}item_galleries WHERE mgrarea = 'collections' AND item_id = '{$_SESSION[collid]}'"); // get galleries
					while($collectionGalleries = mysqli_fetch_object($collectionGalleriesResult))
					{
						$collGalleries[] = $collectionGalleries->gallery_id;
					}
					
					@$checkCollGalleries = implode(",",$collGalleries);
					
					@$media_result2 = mysqli_query($db,"SELECT DISTINCT {$dbinfo[pre]}media.media_id FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_galleries ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_galleries.gmedia_id WHERE {$dbinfo[pre]}media_galleries.gallery_id IN ({$checkCollGalleries})");
					@$r_rows = mysqli_num_rows($media_result2);
					
				}
				else
					$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT({$dbinfo[pre]}media_collections.mc_id) FROM {$dbinfo[pre]}media_collections LEFT JOIN {$dbinfo[pre]}media ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_collections.cmedia_id  WHERE {$dbinfo[pre]}media_collections.coll_id = '{$_SESSION[collid]}' AND {$dbinfo[pre]}media.umedia_id != ''"));
			break;
			
			case "gallery":				
				$galSQL = 
				"
					SELECT DISTINCT {$dbinfo[pre]}media.media_id 
					FROM {$dbinfo[pre]}media 
					JOIN {$dbinfo[pre]}media_galleries 
					ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_galleries.gmedia_id 
					WHERE {$dbinfo[pre]}media_galleries.gallery_id = '$_SESSION[galid]'
				";
				/*
				$galSQL = 
				"
					SELECT DISTINCT(media_id)
					FROM {$dbinfo[pre]}media 
					WHERE media_id IN (SELECT DISTINCT(gmedia_id) FROM {$dbinfo[pre]}media_galleries WHERE gallery_id = '$_SESSION[galid]')					
				"; // New 4.3.2
				*/
				$media_result2 = mysqli_query($db,$galSQL); // GROUP BY {$dbinfo[pre]}media.media_id
				$r_rows = mysqli_num_rows($media_result2);
			break;
			
			case "orphaned_media":
				$media_result2 = mysqli_query($db,"SELECT DISTINCT {$dbinfo[pre]}media.media_id FROM {$dbinfo[pre]}media WHERE {$dbinfo[pre]}media.owner = '$_SESSION[mediaOwner]' AND {$dbinfo[pre]}media.media_id NOT IN (SELECT {$dbinfo[pre]}media_galleries.gmedia_id FROM {$dbinfo[pre]}media_galleries)"); // GROUP BY {$dbinfo[pre]}media.media_id
				$r_rows = mysqli_num_rows($media_result2);
				//$r_rows = mysqli_result_patch(mysqli_query($db,$media_result2));
			break;
			
			case "last_batch":
				# Find last batch
				$last_batch = mysqli_result_patch(mysqli_query($db,"SELECT batch_id FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' ORDER BY batch_id DESC"));				
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND batch_id = '$last_batch'"));
			break;
			
			case "imported_today":
				$today_date = gmdate("Y-m-d");
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added LIKE '$today_date%'"));
			break;
			
			case "imported_week":
				$week_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s") . " -7 days"));
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added >= '$week_date'"));
			break;
			
			case "imported_month":
				$month_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s") . " -30 days"));
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added >= '$month_date'"));
			break;
			
			case "search":
				# MEDIA SEARCH - media, members, keywords
				$search_words = explode(" ",$insearch);
				$search_word_length = 1;
				$snext = 1;
				foreach($search_words as $value){
					if(strlen($value) >= $search_word_length){
						// ADD OR IF YOU ARE ON THE SECOND TERM ON
						if($snext > 1){ $sql_search.= " or "; }
						$sql_search.= " {$dbinfo[pre]}media.media_id LIKE '%$value%'";
						if(in_array("sfo_firstname",$sfo_array) or in_array('all',$sfo_array)){ 	$sql_search.= " or {$dbinfo[pre]}members.f_name LIKE '%$value%'";	}			
						if(in_array("sfo_lastname",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.l_name LIKE '%$value%'";	}
						if(in_array("sfo_memid",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.mem_id LIKE '%$value%'";	}
						if(in_array("sfo_memid",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.umem_id LIKE '%$value%'";	}
						if(in_array("sfo_email",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.email LIKE '%$value%'";		}
						if(in_array("sfo_filename",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}media.filename LIKE '%$value%'";	}
						if(in_array("sfo_title",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}media.title LIKE '%$value%'";		}
						if(in_array("sfo_desc",$sfo_array) or in_array('all',$sfo_array)){ 			$sql_search.= " or {$dbinfo[pre]}media.description LIKE '%$value%'";	}
						//if(in_array("sfo_lastname",$sfo_array) or in_array('all',$sfo_array)){ 	$sql_search.= " or {$dbinfo[pre]}media.media_id LIKE '%$value%'";	}
						if(in_array("sfo_id",$sfo_array) or in_array('all',$sfo_array)){ 			$sql_search.= " or {$dbinfo[pre]}media.umedia_id LIKE '%$value%'";	}
						if(in_array("sfo_keywords",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}keywords.keyword LIKE '%$value%'";	}
						$snext++;				
					}
				}
				
				//echo $sql_search; exit;
				
				$media_count_result = mysqli_query($db,"SELECT DISTINCT({$dbinfo[pre]}media.media_id) FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}media.owner = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}keywords ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}keywords.media_id WHERE $sql_search");
				$r_rows = mysqli_num_rows($media_count_result);
			break;
			
			case "groups":
				$media_result2 = "SELECT COUNT(media_id) FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['mediagroups']).") AND {$dbinfo[pre]}media.owner = '$_SESSION[mediaOwner]'";
				$r_rows = mysqli_result_patch(mysqli_query($db,$media_result2));
			break;
			
			case "lightbox":
				$lightboxResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}lightboxes WHERE lightbox_id = {$_SESSION[lbid]}");
				$lightbox = mysqli_fetch_object($lightboxResult);
				
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(item_id) FROM {$dbinfo[pre]}lightbox_items WHERE lb_id = '{$_SESSION[lbid]}'"));				
			break;
		}
		
		$pages = ceil($r_rows/$perpage);
	   
		# CHECK TO SEE IF THE CURRENT PAGE IS SET
		if(isset($_SESSION['currentpage']))
		{
			if(!empty($_REQUEST['updatepage'])) $_SESSION['currentpage'] = $_REQUEST['updatepage'];
		}
		else
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# CALCULATE THE STARTING RECORD						
		$startrecord = ($_SESSION['currentpage'] == 1) ? 0 : (($_SESSION['currentpage'] - 1) * $perpage);
		
		# FIX FOR RECORDS GETTING DELETED
		if($startrecord > ($r_rows - 1))
		{
			$startrecord-=$perpage;
		}		
		
		if($startrecord < 0) $startrecord = 0; // Make sure this doesn't become negative
		
		switch($_SESSION['media_dtype'])
		{
			default:
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "featured":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE featured = '1' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "active":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE active = '1' AND owner = '$_SESSION[mediaOwner]' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "inactive":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE active = '0' AND owner = '$_SESSION[mediaOwner]' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "contr":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner != '0' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "pending":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE approval_status = '0' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "failed":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE approval_status = '2' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "collection":
				if($collection->colltype == 1)
				{
					$media_result = mysqli_query($db,"SELECT DISTINCT({$dbinfo[pre]}media.media_id),{$dbinfo[pre]}media.folder_id,{$dbinfo[pre]}media.featured,{$dbinfo[pre]}media.sortorder,{$dbinfo[pre]}media.active FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_galleries ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_galleries.gmedia_id WHERE {$dbinfo[pre]}media.owner = '$_SESSION[mediaOwner]' AND {$dbinfo[pre]}media_galleries.gallery_id IN ({$checkCollGalleries}) ORDER BY {$dbinfo[pre]}media.{$listby} $listtype LIMIT $startrecord,$perpage"); // GROUP BY {$dbinfo[pre]}media.media_id
				}
				else
				{
					$media_result = mysqli_query($db,
					"
						SELECT * FROM {$dbinfo[pre]}media 
						LEFT JOIN {$dbinfo[pre]}media_collections 
						ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_collections.cmedia_id 
						WHERE {$dbinfo[pre]}media_collections.coll_id = {$_SESSION[collid]} 
						ORDER BY {$dbinfo[pre]}media.{$listby} $listtype 
						LIMIT $startrecord,$perpage
					");
				}
				/*
				while($media = mysqli_fetch_object($media_result))
				{
					echo "{$media->media_id} - {$media->mc_id}<br />";
				}
				exit;
				*/
				//checkCollGalleries
			
			break;
			
			case "search":
				//$mem_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id WHERE $sql_search ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
				$media_result = mysqli_query($db,"SELECT DISTINCT({$dbinfo[pre]}media.media_id),{$dbinfo[pre]}media.folder_id,{$dbinfo[pre]}media.featured,{$dbinfo[pre]}media.sortorder,{$dbinfo[pre]}media.active FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}media.owner = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}keywords ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}keywords.media_id WHERE $sql_search ORDER BY {$dbinfo[pre]}media.{$listby} $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "gallery":
				
				$gallerySQL = 
				"
					SELECT DISTINCT({$dbinfo[pre]}media.media_id),{$dbinfo[pre]}media.folder_id,{$dbinfo[pre]}media.featured,{$dbinfo[pre]}media.sortorder,{$dbinfo[pre]}media.active 
					FROM {$dbinfo[pre]}media 
					JOIN {$dbinfo[pre]}media_galleries 
					ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_galleries.gmedia_id 
					WHERE {$dbinfo[pre]}media_galleries.gallery_id = '$_SESSION[galid]' 
					ORDER BY {$dbinfo[pre]}media.{$listby} $listtype 
					LIMIT $startrecord,$perpage
				";
				/*
				$gallerySQL = 
				"
					SELECT DISTINCT({$dbinfo[pre]}media.media_id),{$dbinfo[pre]}media.folder_id,{$dbinfo[pre]}media.featured,{$dbinfo[pre]}media.sortorder,{$dbinfo[pre]}media.active 
					FROM {$dbinfo[pre]}media 
					WHERE {$dbinfo[pre]}media.media_id IN (SELECT DISTINCT(gmedia_id) FROM {$dbinfo[pre]}media_galleries WHERE gallery_id = '$_SESSION[galid]') 
					ORDER BY {$dbinfo[pre]}media.{$listby} $listtype 
					LIMIT $startrecord,$perpage
				"; // New 4.3.2	
				*/
				$media_result = mysqli_query($db,$gallerySQL); // GROUP BY {$dbinfo[pre]}media.media_id
				
				// Get gallery details
				$gallery_result = mysqli_query($db,"SELECT icon FROM {$dbinfo[pre]}galleries WHERE gallery_id = '{$_SESSION[galid]}'");
				$gallery = mysqli_fetch_object($gallery_result);
			break;
			
			case "orphaned_media":
				$media_result = mysqli_query($db,"SELECT DISTINCT {$dbinfo[pre]}media.media_id,{$dbinfo[pre]}media.folder_id,{$dbinfo[pre]}media.featured,{$dbinfo[pre]}media.active FROM {$dbinfo[pre]}media WHERE {$dbinfo[pre]}media.owner = '$_SESSION[mediaOwner]' AND {$dbinfo[pre]}media.media_id NOT IN (SELECT {$dbinfo[pre]}media_galleries.gmedia_id FROM {$dbinfo[pre]}media_galleries) ORDER BY {$dbinfo[pre]}media.{$listby} $listtype LIMIT $startrecord,$perpage"); // GROUP BY {$dbinfo[pre]}media.media_id
			break;
			
			case "imported_today":				
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added LIKE '$today_date%' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "imported_week":				
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added >= '$week_date' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "imported_month":				
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND date_added >= '$month_date' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "groups":
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['mediagroups']).") AND {$dbinfo[pre]}media.owner = '$_SESSION[mediaOwner]' GROUP BY {$dbinfo[pre]}media.media_id ORDER BY {$dbinfo[pre]}media.{$listby} $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "last_batch": // all
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE owner = '$_SESSION[mediaOwner]' AND batch_id = '$last_batch' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "lightbox":				
				$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}lightbox_items JOIN {$dbinfo[pre]}media ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}lightbox_items.media_id WHERE {$dbinfo[pre]}lightbox_items.lb_id = '{$_SESSION[lbid]}' ORDER BY {$dbinfo[pre]}media.{$listby} $listtype LIMIT $startrecord,$perpage"); // GROUP BY {$dbinfo[pre]}media.media_id
			break;
		}			
		@$media_rows = mysqli_num_rows($media_result);
		
		// CALCULATE MAX UPLOAD SIZE FOR LOGO
		if(ini_get("upload_max_filesize"))
		{	
			$upload_limit = ini_get("upload_max_filesize") * 1024;
			$upload_limit-= 50;
		}
		else
		{
			$upload_limit = 90000;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_types']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
	<!--[if lt IE 7.]><script defer type="text/javascript" src="../assets/javascript/pngfix.js"></script><![endif]-->
    <!-- PHP TO JAVASCRIPT VARS -->
    <?php include('mgr.javascript.vars.php'); ?>
	<!-- LOAD PUBLIC AND MANAGER SHARED JAVASCRIPT -->	
	<script type="text/javascript" src="../assets/javascript/shared.min.js"></script>
	<!-- LOAD PROTOTYPE LIBRARY -->	
	<script type="text/javascript" src="../assets/javascript/prototype/prototype.js"></script>
	<!-- LOAD jQUERY -->
	<script type="text/javascript" src="../assets/javascript/jquery/jquery.min.js"></script>
	<script>var $j = jQuery.noConflict();</script>
	<script type="text/javascript" src="../assets/javascript/jstree/v3/jstree.min.js"></script>
	<link rel="stylesheet" href="../assets/javascript/jstree/v3/themes/default/style.min.css" />
	
    <!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- FLASH OBJECT -->
	<script type="text/javascript" src="../assets/javascript/swfobject.js"></script> 
	
	<!-- VIDEO PLAYER -->
	<script type="text/javascript" src="../assets/jwplayer/jwplayer.min.js"></script> 
	
	<script language="javascript">
		// DELETE RECORD FUNCION
		function deleterec(idnum)
		{
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		
		function closeBatchWindow()
		{
			$('leftNavList').show();
			$('batchEditContainer').hide();	
		}
		
		// SELECT ALL CHECKBOXES
		function select_all_cb_media(formname)
		{
			$$('.mediaCheckbox').each(function(elem)
			{
				var parentDivID = elem.up('div').id; // Parent element				
				$(parentDivID).addClassName('mediaContainerSelected');
			//alert(elem);
				if(!elem.getAttribute('disabled'))
				{
					elem.checked = true;
				}
			
			});
			
			if($('batchActive') != null)
			{
				if($F('batchActive') == '1')
				{
					loadBatchKeywords(); // Load keywords if batch editor is open
				}
			}
		}
	
		// DESELECT ALL CHECKBOXES
		function deselect_all_cb_media(formname)
		{
			$$('.mediaCheckbox').each(function(elem)
			{
				var parentDivID = elem.up('div').id; // Parent element				
				$(parentDivID).removeClassName('mediaContainerSelected');
			
				//alert(elem); 
				elem.checked = false;
			});
			
			if($('batchActive') != null)
			{
				if($F('batchActive') == '1')
				{
					loadBatchKeywords(); // Load keywords if batch editor is open
				}
			}
		}
		
		Event.observe(window, 'load', function()
			{			
			// ADD MEDIA BUTTON
			if($('abutton_add_media')!=null)
			{
				$('abutton_add_media').observe('click', function()
					{
						window.location.href='mgr.add.media.php?ep=1';
					});
				$('abutton_add_media').observe('mouseover', function()
					{
						$('img_add_media').src='./images/mgr.button.add.media.png';
					});
				$('abutton_add_media').observe('mouseout', function()
					{
						$('img_add_media').src='./images/mgr.button.add.media.off.png';
					});
			}
			
			// SELECT ALL BUTTON
			if($('abutton_select_all')!=null)
			{
				$('abutton_select_all').observe('click', function()
					{
						select_all_cb_media('datalist');
					});
				$('abutton_select_all').observe('mouseover', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.png';
					});
				$('abutton_select_all').observe('mouseout', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.off.png';
					});
			}
			
			// SELECT NONE BUTTON
			if($('abutton_select_none')!=null)
			{
				$('abutton_select_none').observe('click', function()
					{
						deselect_all_cb_media('datalist');
					});
				$('abutton_select_none').observe('mouseover', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.png';
					});
				$('abutton_select_none').observe('mouseout', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.off.png';
					});
			}
			
			// GROUPS BUTTON
			if($('abutton_group')!=null)
			{
				$('abutton_group').observe('click', function()
					{
						// ONLY LOAD WHEN OPENING
						if($('group_selector').visible() == false)
						{
							load_group_selector();
						}
						$('group_selector').toggle();
						$('members_selector').hide();
						$('media_adv_search').hide();
						//$('orders_adv_search').hide();
						//$('orders_details_sel').hide();
					});
				$('abutton_group').observe('mouseover', function()
					{
						$('img_group').src='./images/mgr.button.group.png';
					});
				$('abutton_group').observe('mouseout', function()
					{
						$('img_group').src='./images/mgr.button.group.off.png';
					});
			}
			
			// DELETE BUTTON
			if($('abutton_delete')!=null)
			{
				$('abutton_delete').observe('click', function()
					{
						deleterec();
					});
				$('abutton_delete').observe('mouseover', function()
					{
						$('img_delete').src='./images/mgr.button.delete.png';
					});
				$('abutton_delete').observe('mouseout', function()
					{
						$('img_delete').src='./images/mgr.button.delete.off.png';
					});
			}
			
			// HELP BUTTON
			if($('abutton_help')!=null)
			{
				$('abutton_help').observe('click', function()
					{
						support_popup('<?php echo $supportPageID; ?>');
					});
				$('abutton_help').observe('mouseover', function()
					{
						$('img_help').src='./images/mgr.button.help.png';
					});
				$('abutton_help').observe('mouseout', function()
					{
						$('img_help').src='./images/mgr.button.help.off.png';
					});
			}
			
			// MEMBERS BUTTON
			if($('abutton_members')!=null)
			{
				$('abutton_members').observe('click', function()
					{
						$('members_selector').toggle();
						$('group_selector').hide();
						$('media_adv_search').hide();
					});
				$('abutton_members').observe('mouseover', function()
					{
						$('img_members').src='./images/mgr.button.members.png';
					});
				$('abutton_members').observe('mouseout', function()
					{
						$('img_members').src='./images/mgr.button.members.off.png';
					});
			}
			
			// SEARCH BUTTON
			if($('abutton_search')!=null)
				{
				$('abutton_search').observe('click', function()
					{
						$('media_adv_search').toggle();
						$('search_field').focus();
						$('group_selector').hide();
						$('members_selector').hide();
					});
				$('abutton_search').observe('mouseover', function()
					{
						$('img_search').src='./images/mgr.button.search.png';
					});
				$('abutton_search').observe('mouseout', function()
					{
						$('img_search').src='./images/mgr.button.search.off.png';
					});
			}
			
			// SEARCH BUTTON
			if($('button_search')!=null)
			{
				$('button_search').observe('click', function()
					{
						$('search_from').submit();
					});
			}
			
			//perpage_bar_
		   
		  	perPageBarWidth();
			//galleriesHeight();
			
			<?php
				// LOAD THE GROUPLIST AREA
				if($_SESSION['media_dtype'] == 'groups' or $_GET['dtype'] == 'groups')
				{
					echo "load_group_selector();";
				}
			?>
			
		});
		
		Event.observe(window, 'resize', function()
		{
			perPageBarWidth();
			//galleriesHeight();
		});
		/*
		//galleriesHeight();
		//mediaWidth();
		*/
		
		// SUBMIT GROUPS LIST
		function submit_groups(){
			$('grouplist').submit();
		}
		
		// Show video fields if needed
		function dspTypeSelect()
		{
			
			var selecteditem = $F('dsp_type');
			
			switch(selecteditem)
			{			
				default:
					$('video_div').hide();
					$('uploadVideoSampleButtonTop').hide();
					$('wbVidPreview').hide();
					$('wbThumbPreview').show();
				break;
				case "video":
					$('video_div').show();
					$('uploadVideoSampleButtonTop').show();
										
					if($F('vidSampleExists') == 1)
					{					
						$('wbVidPreview').show();
						$('wbThumbPreview').hide();
					}
					
					$$('.dspCreateable').each(function(e)
					{
						e.setValue('0');
					});
					
					$$('.dsp_autocreate_checkbox').each(function(e)
					{
						e.checked = false;
					});
					
					$$('.dspOptions').each(function(e)
					{
						e.hide();
					});
					
					$$('.dspUploadDiv').each(function(e)
					{
						e.show();	
					});
					
				break;
			}
		}
		
		// LOAD GROUPS AREA
		function load_group_selector()
		{
			show_loader('group_selector');
			var loadpage = "mgr.groups.actions.php?mode=grouplist&mgrarea=<?php echo $page; ?>&ingroups=<?php if($_SESSION['media_dtype'] == 'groups'){ echo 1; } else { echo 0; } ?>&exitpage=<?php echo $_SERVER['PHP_SELF']; ?>&sessname=mediagroups";
			var updatecontent = 'group_selector';
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
		}
		
		function load_media_owner(selchar)
		{
			show_loader('wbmembers_inner');
			alphabet_clean();
			$('sl_'+selchar).className =  'alphabet_on';
			var pars = 'mode=members&media_owner=<?php echo $_SESSION['mediaOwner']; ?>&selchar='+selchar;
			var myAjax = new Ajax.Updater('wbmembers_inner', 'mgr.media.actions.php', {method: 'get', parameters: pars});
		}
		
		function updateShowMedia()
		{
			var dtype = $F('dtype');			
			if(dtype != '0')
			{
				location.href='mgr.media.php?dtype=' + dtype;
			}
		}
		
		function updateOrderBy()
		{
			var uselistby = $F('listby');
			if(uselistby != '0')
			{
				location.href='mgr.media.php?listby=' + uselistby;
			}
		}
		
		function updateListType(uselisttype)
		{
			location.href='mgr.media.php?listtype=' + uselisttype;
		}
		
		function dark_color()
		{
			 $('content').setStyle({
				 backgroundImage: 'none',
				 backgroundColor: '#333'
			 });
		}
		
		/*
		function galleriesHeight()
		{
			var mediaheight = $('media').getHeight();
			var navheight = $('media_leftnav').getHeight();
			
			if(mediaheight > navheight)
			{
				resizeDivHeight('media_leftnav',mediaheight);
			}
			else
			{
				resizeDivHeight('media',navheight);
			}
		}
		*/
		
		function perPageBarWidth()
		{
			var viewwidth = document.viewport.getWidth() - ($('media_leftnav').getWidth()+45);
			//alert(viewwidth);
			//resizeDivWidth('perpage_bar_2',viewwidth);
		}
		
		
		function setGalleryAvatar(mediaID,galleryID)
		{
			alert(mediaID + ' | ' + galleryID);
		}
		
		var mediaWinTimeout;
		function createDetailsWindow(mediaID)
		{
			clearTimeout(mediaWinTimeout);
			
			var offset = $('media_div_'+mediaID).cumulativeOffset();			
			var mediaDivWidth = $('media_div_'+mediaID).getWidth();
			var arrowoffset = (Math.round(mediaDivWidth/2)-14)+90;
			var leftoffset = 0;
			var arrow = 'right';
			var arrowpos = [400,arrowoffset];
			
			if((offset[0] - 402) < 0)
			{
				leftoffset = offset[0]+402+mediaDivWidth;
				arrow = 'left';
				arrowpos = [-10,arrowoffset];
			}
			else
			{
				leftoffset = offset[0];
				arrow = 'right';
				arrowpos = [400,arrowoffset];
			}
			
			var arrow = "<img src='images/mgr.detailswin.arrow."+arrow+".dark.png' style='position: absolute; margin: "+arrowpos[1]+"px 0 0 "+arrowpos[0]+"px;' />";
			$('mediaDetailsWindow').update(arrow + "<div class='details_win_inner' style='min-height: 320px; padding: 10px 20px 20px 20px; background-color: #333' id='mediaDetailsWindow_inner'><img src='images/mgr.loader.gif' style='margin: 10px;' /></div>");
			
			mediaWinTimeout = setTimeout(function(){ fadeInDetailsWindow(mediaID); },700);

			$('mediaDetailsWindow').setStyle(
			{
				top: offset[1] + "px",
				left: leftoffset + "px",
				marginTop: "-90px",
				marginRight: "0",
				marginBottom: "0",
				marginLeft: "-402px"
			});
		}
		
		function fadeInDetailsWindow(mediaID)
		{
			//$('mediaDetailsWindow').show();
			
			show_div_fade_load('mediaDetailsWindow','mgr.media.actions.php?mode=preview&mediaid='+mediaID,'_inner')
			
		}
		
		function hideDetailsWindow()
		{
			$('mediaDetailsWindow').hide();
			clearTimeout(mediaWinTimeout);
		}
		
		function setActiveStatus(mediaID)
		{	
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
			?>
				demo_message();
			<?php
				}
				else
				{
			?>
				if($('status_'+mediaID).getAttribute('src') == 'images/mgr.icon.active.1.png')
				{
					var newVal = 0;
				}
				else
				{
					var newVal = 1;	
				}
				
				var loadpage = "mgr.media.actions.php?mode=status&id=" + mediaID + "&newval=" + newVal;
				
				var myAjax = new Ajax.Request( 
					loadpage, 
					{
						method: 'get', 
						parameters: '',
						evalScripts: true,
						onSuccess: function(transport){					
							transport.responseText.evalScripts();					
							//alert(transport.responseText);
							//eval(transport.responseText);
						}
					});
			<?php
				}
			?>
		}
		
		function featuredStatus(mediaID)
		{	
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
			?>
				demo_message();
			<?php
				}
				else
				{
			?>
				if($('featured_'+mediaID).getAttribute('src') == 'images/mgr.icon.featured.1.png')
				{
					var newVal = 0;
				}
				else
				{
					var newVal = 1;	
				}
				
				var loadpage = "mgr.media.actions.php?mode=featured&id=" + mediaID + "&newval=" + newVal;
				
				var myAjax = new Ajax.Request( 
					loadpage, 
					{
						method: 'get', 
						parameters: '',
						evalScripts: true,
						onSuccess: function(transport){					
							transport.responseText.evalScripts();					
							//alert(transport.responseText);
							//eval(transport.responseText);
						}
					});
			<?php
				}
			?>
		}
		
		function setGalleryAvatar(mediaID)
		{	
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
			?>
				demo_message();
			<?php
				}
				else
				{
			?>
				if($('avatar_'+mediaID).getAttribute('src') == 'images/mgr.icon.seticon.1.png')
				{
					var newVal = 0;
				}
				else
				{
					var newVal = 1;	
				}
				var loadpage = "mgr.media.actions.php?mode=setGalleryAvatar&id=" + mediaID + "&newval=" + newVal + "&galleryID=<?php echo $_SESSION['galid']; ?>";
				var myAjax = new Ajax.Request( 
					loadpage, 
					{
						method: 'get', 
						parameters: '',
						evalScripts: true,
						onSuccess: function(transport){					
							updateGalleryAvatarIcons();
							transport.responseText.evalScripts();					
							//alert(transport.responseText);
							//eval(transport.responseText);
						}
					});
			<?php
				}
			?>
		}
		
		function updateGalleryAvatarIcons()
		{
			$$('[avatar="1"]').each(function(s)
			{
				s.setAttribute('src','images/mgr.icon.seticon.0.png');
			});
		}
		
		// DO WORKBOX ACTIONS
		function do_actions()
		{
			var selecteditem = $('actionsdd').options[$('actionsdd').selectedIndex].value;
			// REVERT BACK TO ACTIONS TITLE
			$('actionsdd').options[0].selected = 1;
			
			// CREATE THE WORKBOX OBJECT
			workboxobj = new Object();
			
			switch(selecteditem)
			{
				case "assign_groups":					
					workboxobj.mode = 'assign_groups';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
				case "set_status":					
					workboxobj.mode = 'set_status';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
				case "approve_media":					
					workboxobj.mode = 'approve_media';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
			}
		}
		
		// EDIT MEDIA INFO WORKBOX
		function editMediaInfo(mediaID)
		{
			clearTimeout(mediaWinTimeout);
			workbox2({page: 'mgr.wb.media.details.php',pars: 'box=assign_details&mediaID='+mediaID});
			$('mediaDetailsWindow').hide();
		}
		
		
		function removeGalleryFromList(galleryID,type)
		{
			//$j('#'+type+'-'+galleryID).remove();
			
			$j('#'+type).find('#'+type+'-'+galleryID).remove();
		}
		
		function addGalleryToList(galleryID,type)
		{
			//alert('#'+type);
			
			$j('#'+type).append("<input type='text' name='mediaGalleries[]' id='mediaGalleries-"+galleryID+"' value='"+galleryID+"'><br>");
			
		}
		
		function load_albums(mediaID,owner)
		{
			<?php
				if($_SESSION['mediaOwner'] != 0)
				{
			?>
			
			show_loader('galsAlbums');
			
			$j('#galsAlbums').jstree({
				'core' : {
					'data' : {
						'url' : 'mgr.jstree.data.wb.php',
						'data' : function (node) {
							//alert(node.url);
							return { 'id' : node.id, 'galOwner' : owner, 'mediaID' : mediaID };
						}
					},
					'check_callback' : function(o, n, p, i, m) {
						
					},
					'themes' : {
						'responsive' : false,
						'stripes' : false,
						'dots' : false
					}
				},
				'plugins' : ['checkbox'],
				'checkbox' : {
						'visible' : true,
						'three_state' : false,
						'cascade' : ''
					}
			})
			.on('select_node.jstree', function (e, data) {
				//alert(data.node.id);
				// add to gallery array
				addGalleryToList(data.node.id,'mediaGalleries');
				
			})
			.on('deselect_node.jstree', function (e, data) {
				//alert('test');
				// remove from gallery array
				removeGalleryFromList(data.node.id,'mediaGalleries');
			})
			.on('activate_node.jstree', function (e, data) {
				
			})
			.on('changed.jstree', function (e, data) {
				if(data && data.selected && data.selected.length)
				{
					//alert('test');
				}
			})
			.on('ready.jstree', function (e, data) {
				$j('#checkGalLoaded').val('1');
			});
			
			<?php
				}
			?>
		}
		
		// LOAD THE GALLERY LIST
		function load_gals(mediaID,owner)
		{
			show_loader('gals');
			
			$j('#gals').jstree({
				'core' : {
					'data' : {
						'url' : 'mgr.jstree.data.wb.php',
						'data' : function (node) {
							//alert(node.url);
							return { 'id' : node.id, 'galOwner' : owner, 'mediaID' : mediaID };
						}
					},
					'check_callback' : function(o, n, p, i, m) {
						
					},
					'themes' : {
						'responsive' : false,
						'stripes' : false,
						'dots' : false
					}
				},
				'plugins' : ['checkbox'],
				'checkbox' : {
						'visible' : true,
						'three_state' : false,
						'cascade' : ''
					}
			})
			.on('select_node.jstree', function (e, data) {
				//alert(data.node.id);
				// add to gallery array
				addGalleryToList(data.node.id,'mediaGalleries');
				
			})
			.on('deselect_node.jstree', function (e, data) {
				//alert('test');
				// remove from gallery array
				removeGalleryFromList(data.node.id,'mediaGalleries');
			})
			.on('activate_node.jstree', function (e, data) {
				
			})
			.on('changed.jstree', function (e, data) {
				if(data && data.selected && data.selected.length)
				{
					//alert('test');
				}
			})
			.on('ready.jstree', function (e, data) {
				$j('#checkGalLoaded').val('1');
			});
			
			
			
			/*
			var pars = 'mode=galleries&gal_mem=0&mediaID='+mediaID;
			var myAjax = new Ajax.Updater('gals', 'mgr.media.actions.php', {method: 'get', parameters: pars});
			
			if(owner != 0)
			{
				var pars = 'mode=galleries&gal_mem='+owner+'&mediaID='+mediaID;
				var myAjax = new Ajax.Updater('galsAlbums', 'mgr.media.actions.php', {method: 'get', parameters: pars});
			}
			*/
		}
		
		// CHECK FOR ENTER KEY ON KEYWORDS
		function checkkey(language,mediaID)
		{
			if (Event.KEY_RETURN == event.keyCode)
			{
				//alert('tester');
				add_keyword(language,mediaID);
			}
		}
		
		// ADD KEYWORD TO LIST
		function add_keyword(language,mediaID)
		{	
			// GET KEYWORD
			var new_keyword = $F('new_keyword_'+language);
			
			//alert(mediaID);
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message2();";
				}
				else
				{
			?>
				if(new_keyword != '')
				{
					$('new_keyword_'+language).setValue('');
					
					var numofkeywords = $$('[kwlanguage="'+language+'"]').length-1;
					var lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
					//alert(lastkeywordid);
					
					var loadpage = "mgr.media.actions.php";
					var myAjax = new Ajax.Request( 
						loadpage, 
						{
							method: 'post', 
							parameters: "mode=addKeyword&mediaID=" + mediaID + "&language=" + language + "&keyword=" + new_keyword,
							evalScripts: true,
							onSuccess: function(transport){	
								//var saveID = transport.responseText;
								var json = transport.responseText.evalJSON(true);
								var saveID = json.saveID;
								
								//var keywordsLength = json.keywords.saveID.length;
								
								$(json.keywords.saveID).each(function(s,index)
								{
									//alert(index);
									var templatedata = "<input type='button' onclick=\"remove_keyword('"+s+"');\" keyword='' kwlanguage='"+language+"' id='key_"+s+"' value='"+json.keywords.name[index]+"' class='greyButton' />";
									lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
									$(lastkeywordid).insert({'after':templatedata});
								});
								
								//alert(json.keywords.name[1]);
								
								//alert(saveID);
								//var templatedata = "<input type='button' onclick=\"remove_keyword('"+saveID+"');\" keyword='' kwlanguage='"+language+"' id='key_"+saveID+"' value='"+new_keyword+"' class='greyButton' />";
								//templatedata += "<input type=\"hidden\" name=\"keyword_"+language+"[]\" id=\"DEFAULT_key_"+saveID+"_input\" value=\""+new_keyword+"\" />";
								//$(lastkeywordid).insert({'after':templatedata});
							}
						});	
				}
			<?php
				}
			?>
		}
		
		// REMOVE KEYWORD FROM LIST
		function remove_keyword(id)
		{
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message2();";
				}
				else
				{
			?>
			
				var loadpage = "mgr.media.actions.php?mode=removeKeyword&keyID=" + id;
				//alert("-"+id+"-");
				
				var myAjax = new Ajax.Request( 
					loadpage, 
					{
						method: 'get', 
						parameters: '',
						evalScripts: true,
						onSuccess: function(transport){	
							$('key_'+id).remove();
						}
					});
			<?php
				}
			?>
		}
		
		function saveMediaDetails()
		{	
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message2();";
				}
				else
				{
			?>
				$('saveMediaDetailsButton').disable();
				$('mediaDetailsForm').request({
					onFailure: function(){ alert('failed'); }, 
					onSuccess: function(transport){						
						//alert(transport.responseText);						
						transport.responseText.evalScripts();						
						close_workbox();						
					}
				});
			<?php
				}
			?>
		}
		
		var uploaderDetails = {}; // Set the uploaderDetails object
		
		function revealThumbUploader(mediaID)
		{
			$('wbThumbUploaderBox').slideDown({duration:.3,easing: 2});
			//$('wbOverlay').fade({duration:1, from:0, to:.7}).show();			
			uploaderDetails.mediaID = mediaID;
			uploaderDetails.type = 'thumb';			
			create_thumb_uploader(mediaID);
		}
		
		function revealDSPUploader(mediaID,dspID)
		{
			var externalLink =  $F('dspExternalLink-'+dspID);
			
			$('ds_external_file').setValue(externalLink);			
			$('wbDSPUploaderBox').slideDown({duration:.3,easing: 2});		
			uploaderDetails.mediaID = mediaID;
			uploaderDetails.dspID = dspID;
			uploaderDetails.type = 'dsp';			
			create_dsp_uploader(mediaID,dspID);
		}
		
		function saveExternalDSLink()
		{
			//alert($F('ds_external_file'));
			
			var loadpage = "mgr.media.actions.php?mode=saveExternalLinkDSP&mediaID=" + uploaderDetails.mediaID + '&dspID=' + uploaderDetails.dspID + '&externalLink=' + $F('ds_external_file');
			//alert(uploaderDetails.dspID);
			//var dspID = uploaderDetails.dspID;
			
			var myAjax = new Ajax.Request( 
				loadpage, 
				{
					method: 'get', 
					parameters: '',
					evalScripts: true,
					onSuccess: function(transport){	
						
						if($F('ds_external_file'))
						{
							$('dspFileAttached'+uploaderDetails.dspID).show();
							//$('dspUploadDiv'+uploaderDetails.dspID).hide();
						
							$('dspUploadButton'+uploaderDetails.dspID).hide();						
							//$('dsp_filename_'+uploaderDetails.dspID).setValue('');						
						
							$('dspExternalLink-'+uploaderDetails.dspID).setValue($F('ds_external_file'));
						}
						wbDSPUploaderClose();
					}
				});
			
		}
		
		function revealVidSampleUploader(mediaID)
		{
			$('wbVidSampleUploaderBox').slideDown({duration:.3,easing: 2});
			//$('wbOverlay').fade({duration:1, from:0, to:.7}).show();
			uploaderDetails.mediaID = mediaID;
			uploaderDetails.type = 'vidsample';	
			create_vidsample_uploader(mediaID);
		}
		
		function wbThumbUploaderClose()
		{
			$('wbThumbUploaderBox').slideUp({duration:.5,easing: 2});
		}
		
		function wbDSPUploaderClose()
		{
			$('wbDSPUploaderBox').slideUp({duration:.5,easing: 2});
		}
		
		function wbVidSampleUploaderClose()
		{
			$('wbVidSampleUploaderBox').slideUp({duration:.5,easing: 2});
		}
		
		function create_vidsample_uploader(mediaID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('vidsampleuploader').update('<input type=\"button\" value=\"Upload\" onclick=\"demo_message2();\">');";
				}
				else
				{
			?>
				var flashvarsThumb = {
					myextensions: "*.mp4;*.flv;*.f4v;*.webm;*.ogv;*.mov",
					uploadUrl: "mgr.upload.actions.php",
					uploadParms: "?mode=upload_video_sample%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26mediaID="+mediaID,
					maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
					maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
					uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
				};
				var paramsThumb = {
					bgcolor: "#FFFFFF",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributesThumb = {
					id: "vidsampleuploader",
					tester: "1234"
				};
				
				swfobject.embedSWF("mgr.single.uploader.swf", "vidsampleuploader", "300", "40", "6.0.0", "expressInstall.swf", flashvarsThumb, paramsThumb, attributesThumb);
			<?php
				}
			?>
		}
		
		function create_dsp_uploader(mediaID,dspID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('dspuploader').update('<input type=\"button\" value=\"Upload\" onclick=\"demo_message2();\">');";
				}
				else
				{
			?>
				var flashvarsThumb = {
					myextensions: "<?php foreach(getAlldTypeExtensions() as $fileEXT){ echo "*.{$fileEXT};"; } ?>",
					uploadUrl: "mgr.upload.actions.php",
					uploadParms: "?mode=upload_dsp_file%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26mediaID="+mediaID+"%26dspID="+dspID,
					maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
					maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
					uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
				};
				var paramsThumb = {
					bgcolor: "#FFFFFF",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributesThumb = {
					id: "dspuploader",
					tester: "1234"
				};
							
				swfobject.embedSWF("mgr.single.uploader.swf", "dspuploader", "300", "40", "6.0.0", "expressInstall.swf", flashvarsThumb, paramsThumb, attributesThumb);	
			<?php
				}
			?>
		}
		
		function create_mr_uploader(mediaID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('mrUploader').update('<input type=\"button\" value=\"Upload\" onclick=\"demo_message2();\">');";
				}
				else
				{
			?>
				var flashvarsMR = {
					myextensions: "<?php foreach(getAlldTypeExtensions() as $fileEXT){ echo "*.{$fileEXT};"; } ?>",
					uploadUrl: "mgr.upload.actions.php",
					uploadParms: "?mode=uploadMR%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26mediaID="+mediaID,
					maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
					maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
					uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
				};
				var paramsMR = {
					bgcolor: "#FFFFFF",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributesMR = {
					id: "mrUploader",
					tester: "1234"
				};
							
				swfobject.embedSWF("mgr.single.uploader.swf", "mrUploader", "300", "40", "6.0.0", "expressInstall.swf", flashvarsMR, paramsMR, attributesMR);	
			<?php
				}
			?>
		}
		
		function create_pr_uploader(mediaID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('mrUploader').update('<input type=\"button\" value=\"Upload\" onclick=\"demo_message2();\">');";
				}
				else
				{
			?>
				var flashvarsPR = {
					myextensions: "<?php foreach(getAlldTypeExtensions() as $fileEXT){ echo "*.{$fileEXT};"; } ?>",
					uploadUrl: "mgr.upload.actions.php",
					uploadParms: "?mode=uploadPR%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26mediaID="+mediaID,
					maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
					maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
					uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
				};
				var paramsPR = {
					bgcolor: "#FFFFFF",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributesPR = {
					id: "prUploader",
					tester: "1234"
				};
							
				swfobject.embedSWF("mgr.single.uploader.swf", "prUploader", "300", "40", "6.0.0", "expressInstall.swf", flashvarsPR, paramsPR, attributesPR);	
			<?php
				}
			?>
		}
		
		function activate_release_uploaders(mediaID)
		{
			create_mr_uploader(mediaID);
			create_pr_uploader(mediaID);
		}
		
		// DELETE THE RELEASE
		function delete_release(rType,mediaID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					//echo "return false;";
				}
				else
				{
					echo "do_delete_release(rType,mediaID);";
				}
			?>
		}
			
		function do_delete_release(rType,mediaID)
		{
			var myAjax = new Ajax.Updater(
			rType+'File', 
			'mgr.upload.actions.php', 
			{
				method: 'get', 
				parameters: 'mode=delete_release&pass=<?php echo md5($config['settings']['serial_number']); ?>&rType='+rType+'&mediaID='+mediaID,
				evalScripts: true
			});	
		}
		
		function create_thumb_uploader(mediaID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('thumbuploader').update('<input type=\"button\" value=\"Upload\" onclick=\"demo_message2();\">');";
				}
				else
				{
			?>
				var flashvarsThumb = {
					myextensions: "*.jpg;*.gif;*.png;*.jpeg",
					uploadUrl: "mgr.upload.actions.php",
					uploadParms: "?mode=upload_thumb%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26mediaID="+mediaID,
					maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
					maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
					uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
				};
				var paramsThumb = {
					bgcolor: "#FFFFFF",
					allowScriptAccess: "always",
					wmode: "opaque"
				};
				var attributesThumb = {
					id: "thumbuploader",
					tester: "1234"
				};
			
				swfobject.embedSWF("mgr.single.uploader.swf", "thumbuploader", "300", "40", "6.0.0", "expressInstall.swf", flashvarsThumb, paramsThumb, attributesThumb);	
			<?php
				}
			?>
		}
		
		function update_image_win()
		{
			//alert('called');
			
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message2();";
				}
				else
				{
			?>
			
				switch(uploaderDetails.type)
				{
					case 'thumb':
						var folderID = $F('folderID');
						var baseFilename = $F('baseFilename');
						var randomNum = randomNumber();
						//alert(randomNumber())
											
						$('thumbPreviewImg2').setAttribute('src',"mgr.media.preview.php?folder_id="+folderID+"&media_id="+uploaderDetails.mediaID+"&src=thumb_"+baseFilename+".jpg&type=thumbs&width=100&rand="+randomNum);
						$('thumbPreviewImg').setAttribute('src',"mgr.media.preview.php?folder_id="+folderID+"&media_id="+uploaderDetails.mediaID+"&src=thumb_"+baseFilename+".jpg&type=thumbs&width=300&rand="+randomNum);
						$('thumbnailPreviewItem'+uploaderDetails.mediaID).setAttribute('src',"mgr.media.preview.php?folder_id="+folderID+"&media_id="+uploaderDetails.mediaID+"&src=thumb_"+baseFilename+".jpg&type=thumbs&rand="+randomNum);
						$('colorPalette').hide(); // Hide color palette until next load
						
						wbThumbUploaderClose();
					break;
					case 'vidsample':
						wbVidSampleUploaderClose();
						$('wbVidPreview').show();
						$('wbThumbPreview').hide();
						createVideoPlayer(uploaderDetails.mediaID);
					break;
					case 'dsp':
						//alert('done - '+uploaderDetails.mediaID);
						wbDSPUploaderClose();
						$('dspUploadButton'+uploaderDetails.dspID).hide();
						Effect.Appear('dspFileAttached'+uploaderDetails.dspID,{ duration: 0.9, from: 0.0, to: 1.0 });					
						$('dspFileAttached'+uploaderDetails.dspID).setAttribute('title','<?php echo $mgrlang['media_save_first']; ?>');
					break;
				}
			<?php
				}
			?>
		}
		
		//../assets/jwplayer/video.mp4
		
		function createVideoPlayer(mediaID)
		{
			//alert($F('poster'));
			// For start image use image: $F('poster'),
			setTimeout(function()
			{
				jwplayer("wbVidPreviewContainer").setup(
				{
					flashplayer: "../assets/jwplayer/player.swf",
					file: 'mgr.view.video.php?mediaID='+mediaID+'&pass=<?php echo md5($config['settings']['serial_number']); ?>',
					autostart: false,
					type: 'video',
					repeat: 'never',
					'controlbar.position': 'none',
					stretching: 'uniform',
					width: '100%',
					height: '100%',
					volume: 100,
					'modes': [
						{type: 'flash', src: '../assets/jwplayer/player.swf'},
						{type: 'html5'},
						{type: 'download'}
					]
				});
			},100);
		}
		
		function displayDSPOptions(dspID)
		{			
			if($F('digitalsp_'+dspID) == dspID)
			{
				//alert(dspID);
				
				$$('.dspOptions'+dspID).each(function(e)
				{
					e.show();
				});
				
				
				/*
				if($F('dsp_creatable_'+dspID) == 1)
				{
					$('dspOptions'+dspID).show();
				}
				
				if($F('dsp_autocreate_'+dspID) != 1)
				{
					$('dspUploadDiv'+dspID).show();
				}
				*/
			}
			else
			{
				$$('.dspOptions'+dspID).each(function(e)
				{
					e.hide();
				});
				
				//alert('hide');
				//$$('.dspOptions'+dspID).hide();
				//$('dspOptions'+dspID).hide();				
				//$('dspUploadDiv'+dspID).hide();
			}
		}
		
		function checkAutocreate(dspID)
		{
			//alert($F('dsp_autocreate_'+dspID));
			
			if($F('dsp_autocreate_'+dspID) == 1)
			{
				$('dspUploadDiv'+dspID).hide();
				//$('dspFileAttached'+dspID).hide();
			}
			else
			{
				$('dspUploadDiv'+dspID).show();
			}
		}
		
		function deleteFileDSP(mediaID,dspID)
		{
			
			var loadpage = "mgr.media.actions.php?mode=deleteFileDSP&mediaID=" + mediaID + '&dspID=' + dspID;
			//alert("-"+id+"-");
			
			var myAjax = new Ajax.Request( 
				loadpage, 
				{
					method: 'get', 
					parameters: '',
					evalScripts: true,
					onSuccess: function(transport){	
						
						$('dspExternalLink-'+dspID).setValue('');
						
						$('dspFileAttached'+dspID).hide();
						$('dspUploadDiv'+dspID).show();
						$('dspUploadButton'+dspID).show();						
						$('dsp_filename_'+dspID).setValue('');
					}
				});
		}
		
		function approvalStatusChange()
		{
			if($F('approvalStatus') == 2)
				$('approvalMessage').show();
			else
				$('approvalMessage').hide();
		}
		
		/*
		* Batch editing functions
		*/
		
		function loadBatchKeywords()
		{
			//alert('loadkeys');
			
			//var numofkeywords = $$('[kwlanguage="'+language+'"]').length-1;
			//var lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
			
			var formData = $('datalist').serialize();
			var loadpage = "mgr.batch.edit.actions.php";
			var myAjax = new Ajax.Request( 
				loadpage, 
				{
					method: 'post', 
					parameters: 'mode=grabKeywords&'+formData,
					evalScripts: true,
					onSuccess: function(transport){	
						//var saveID = transport.responseText;
						var json = transport.responseText.evalJSON(true);

						<?php
							foreach($active_langs as $value)
								echo "\$('batchKeywordsList_{$value}').update('');\n";
						?>

						// Do the same for each lang************************
						<?php
							$defaultLang = strtoupper($config['settings']['lang_file_mgr']);
						?>						
						$('batchKeywordsList').update('');
						
						//alert(json.keywords.<?php echo $defaultLang; ?>.length + 'xxxx');
						
						if(json.keywords.<?php echo $defaultLang; ?> != null)
						{
							$(json.keywords.<?php echo $defaultLang; ?>).each(function(s,index)
							{
								var batchKeywordsListContent = $('batchKeywordsList').innerHTML;							
								
								//alert(s.name);							
								//$('batchKeywordsList').innerHTML() + 							
								//$('batchKeywordsList').update(s.name);
							
								var defaultTemplate = "<input type='button' value='"+s.name+"' class='greyButton' onclick=\"beRemoveKeyword('"+s.name+"','<?php echo $defaultLang; ?>');\" /> ";
								//$('batchKeywordsList').insert({'bottom':templatedata});
								
								$('batchKeywordsList').update(batchKeywordsListContent + defaultTemplate);							
							});
						}
						
						<?php
							foreach($active_langs as $value)
							{
								$valueUpper = strtoupper($value);
						?>
							if(json.keywords.<?php echo $valueUpper; ?> != null)
							{							
								$(json.keywords.<?php echo $valueUpper; ?>).each(function(s,index)
								{								
									var batchKeywordsListContent = $('batchKeywordsList_<?php echo $value; ?>').innerHTML;							
									var <?php echo $value; ?>Template = "<input type='button' value='"+s.name+"' class='greyButton' onclick=\"beRemoveKeyword('"+s.name+"','<?php echo $valueUpper; ?>');\" /> ";
									$('batchKeywordsList_<?php echo $value; ?>').update(batchKeywordsListContent + <?php echo $value; ?>Template);	
								});	
							}
						<?php
							}
						?>
						//$('batchKeywordsList').update(englishTemplate);
						
						
						/*
						$(json.keywords.saveID).each(function(s,index)
						{
							//alert(index);
							var templatedata = "<input type='button' onclick=\"remove_keyword('"+s+"');\" keyword='' kwlanguage='"+language+"' id='key_"+s+"' value='"+json.keywords.name[index]+"' class='greyButton' />";
							lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
							$(lastkeywordid).insert({'after':templatedata});
						});
						*/
					}
				});
			
			
			
		}
		
		function beRemoveKeyword(keyword,lang)
		{
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
				}
				else
				{
			?>
			
				var formData = $('datalist').serialize();
				var loadpage = "mgr.batch.edit.actions.php";
				var myAjax = new Ajax.Request( 
				loadpage, 
				{
					method: 'post', 
					parameters: 'mode=removeKeyword&keyword='+keyword+'&language='+lang+'&'+formData,
					evalScripts: true,
					onSuccess: function(transport){	
						loadBatchKeywords();
					}
				});
			<?php
				}
			?>
		}
		
		function beAddKeyword(lang)
		{
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
				}
				else
				{
			?>
				var formData = $('datalist').serialize();
				var loadpage = "mgr.batch.edit.actions.php";
				var myAjax = new Ajax.Request( 
				loadpage, 
				{
					method: 'post', 
					parameters: 'mode=addKeyword&'+formData,
					evalScripts: true,
					onSuccess: function(transport){	
						loadBatchKeywords();
						$('beNewKeyword_'+lang).setValue('');
						//alert('done');
					}
				});
			<?php
				}
			?>
		}
		
		Event.observe(window, 'load', function()
		{
			$("mediadivs").on("click", ".mediaCheckbox", function(event,element)
			{
				//alert($F(element));
				var parentDivID = element.up('div').id; // Parent element
				
				if($(element).checked == true)
				{
					//alert(parentDivID);
					$(parentDivID).addClassName('mediaContainerSelected');
				}
				else
				{
					//alert(parentDivID);
					$(parentDivID).removeClassName('mediaContainerSelected');
				}
				
				var checkboxCount = $$('.mediaCheckbox:checked').length; // Number of checkboxes checked
				
				if($('batchActive') != null)
				{
					if($F('batchActive') == '1')
					{
						$('batchItemCount').update('('+checkboxCount+')'); // Update count if batch editor is open
						
						loadBatchKeywords(); // Load keywords if batch editor is open
					}
				}
	
			});
			
			$("mediadivs").on("click", ".fakeDivClicker", function(event,element)
			{
				var parentDivID = element.up('div').id;
				var checkboxID = $(parentDivID).down('.mediaCheckbox').id;
				$(checkboxID).click();	
			});	
			
			// Batch Edit
			if($('abutton_batch_edit')!=null)
			{
				$('abutton_batch_edit').observe('click', function()
					{
						$('leftNavList').hide();
						$('batchEditContainer').show();
						
						// Load AJAX
						loadBatchOptions();
											
					});
				$('abutton_batch_edit').observe('mouseover', function()
					{
						$('img_edit').src='./images/mgr.button.edit.png';
					});
				$('abutton_batch_edit').observe('mouseout', function()
					{
						$('img_edit').src='./images/mgr.button.edit.off.png';
					});
			}		
			
		});
		
		function loadBatchOptions()
		{
			var checkboxCount = $$('.mediaCheckbox:checked').length; // Number of checkboxes checked
			
			show_loader('batchEditContainer');
			
			/*
			var pars = '';		
			var myAjax = new Ajax.Updater(
				'batchEditContainer', 
				'mgr.batch.edit.php', 
				{
					method: 'get', 
					parameters: pars
				});
			*/
			
			$('datalist').request({
				onFailure: function() { alert('failed'); }, 
				onSuccess: function(transport) {
					$('batchEditContainer').update(transport.responseText);
				}
			});
			
			if(checkboxCount == 1)
			{
				// single load
			}
			else
			{
				
			}
		}
		
		function saveBatchOptions(saveType,mode2)
		{
			
			<?php 
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
				}
				else
				{
			?>
			
				//alert(saveType);
				$('sm_'+saveType).hide();
				
				//$('save_'+saveType).disabled = true;
				
				$$('.save_'+saveType).each(function(e)
				{
					e.disabled = true;
				});
				
				
				var formData = $('datalist').serialize();
				//alert(formData);
				var myAjax = new Ajax.Request('mgr.batch.edit.actions.php',
				{
					method: 'get',
					parameters: 'mode='+saveType+'&mode2='+mode2+'&'+formData,
					onFailure: function(){ alert('failed'); }, 
					onSuccess: function(transport)
					{
						$('sm_'+saveType).show();
						//$$('.save_'+saveType).disabled = false;
						
						$$('.save_'+saveType).each(function(e)
						{
							e.disabled = false;
						});
						
					}
				});
				//alert($('datalist').serialize());
			<?php
				}
			?>
		}
		
		function openBatchEditGroup(beGroupID)
		{
			$(beGroupID).toggle();
		}
		
	</script>
	<?php include('mgr.media.details.js.php'); ?>
    <style type="text/css">
		#workbox{
			position: absolute;
		}
		
		.media_div{
			float: left;
			margin: 2px;
			height: 200px;
			width: 190px;
			overflow: auto;
			text-align: center;
			/*border: 1px dotted #CCC;*/
			border: 1px solid #EEE;
			padding-top: 10px;
			padding-bottom: 10px;
			background-color: #f8f8f8;
			overflow: hidden;
			position: relative;
		}
		
		.media_div:hover{
			background-color: #edfeff;
			border: 1px solid #CCC;
			-moz-box-shadow: 0 0 10px #bcbcbc; 
			-webkit-box-shadow: 0 0 10px #bcbcbc;     
			box-shadow: 0 0 10px #bcbcbc;
			z-index: 1000;
		}
		
		.media_div_actions{
			display: none;
			position: absolute;
			bottom: 0;
			background-color: #EEE;
			border-top: 1px solid #FFF;
			background-repeat: repeat-x;
			height: 21px;	
			margin: 0px;
			font-weight: bold;
			/*
			background-image: url(images/mgr.actionlink.bg.gif);
			background-repeat: repeat-x;
			*/
			padding: 3px;
			width: 98%;
			/*
			-moz-box-shadow: -1px -1px 2px #CCC; 
			-webkit-box-shadow: -1px -1px 2px #CCC;     
			box-shadow: -1px -1px 2px #CCC;
			*/
		}
		
		.media_action{
			margin: 1px 0 0 0;
			cursor: pointer;
		}
		.media_action:hover{
			background-color: #CCC;
		}
		
		.media_div:hover .media_div_actions,.media_div:hover .handle{
			display: block;
		}
		
		.handle{
			cursor: move;
			position: absolute;
			top: 0;
			right: 0;
			margin-right: 2px;
			margin-top: 2px;
			display: none;
		}
		
		.media_div_inner{
			position: relative;
			margin-right: auto;
			margin-left: auto;
			margin-top: auto;
			margin-bottom: 15px;
			overflow: visible;
			text-align:center;
			border: 1px solid #CCC;
			padding: 5px;
			background-color: #fff;
			-moz-box-shadow: 1px 1px 4px #d9d9d9; 
			-webkit-box-shadow: 1px 1px 4px #d9d9d9;     
			box-shadow: 1px 1px 4px #d9d9d9;
			/* For IE 8 */ 
			-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#d9d9d9')";   
			/* For IE 5.5 - 7 */     
			filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#d9d9d9');
			cursor: pointer;
			background-image: url(images/mgr.loader.gif);
			background-repeat: no-repeat;
			background-position: center;
		}
		
		.media_div img{
			border: 0;
		}
		
		.media_div input[type="checkbox"]{
			position: absolute;
			margin-left: 4px;
			margin-top: -6px;
			z-index: 999;
			width: 12px;
			top: 10px;
			left: 0;
		}
		
		.catops{
			margin-top: 5px;
			padding: 0;
			list-style: none;
			font-size: 12px;
			font-weight: bold;
			font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		}
		.catops li{
			margin-bottom: 6px;
		}
		
		.catops li a{
			text-decoration: none;
		}
		
		.catops li a:visited, .catops li a:link{
			color: #666
		}
		
		.catops li a:hover{
			color: #3cbae1;
		}
		
		#collectionsList a{
			font-weight: normal;
		}
		
		a.editlink:link, a.editlink:visited{
			color: #666;
			font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
			font-weight: bold;
		}
		
		a.editlink:hover{
			color: #3cbae1;
		}
		
		#outputnotice{
			border-bottom: 1px solid #CCC;	
		}
		
		.keyword_list{
			min-height: 50px;
			overflow: auto;
			padding: 7px;
			border: 1px solid #d9d9d9;
		}
		
		.keyword_list input[type="button"]{
			margin: 2px;
			padding-left: 20px;
			background-image: url(images/mgr.actionlink.bg3.gif);
		}
		
		.keyword_list input[type="button"]:hover{
			padding-left: 20px;
			background-image: url(images/mgr.actionlink.bg4.gif);
		}
		
		.keyword_list_header{
			text-align: right;
			background-color: #eee;
			padding: 5px;
			border: 1px solid #d9d9d9;
			white-space: normal;
		}
		
		.approvalStatus0, .approvalStatus1, .approvalStatus2{
			text-align: center;
			padding: 5px 0 5px 0;
			position: absolute;
			right: 0;
			top: 0;
			z-index: 500;
			width: 100%;
		}
		
		.approvalStatus0{
			position: absolute;
			background-color: #faa419;
			color: #333;
		}
		.approvalStatus1{
			display: none;
			background-color: #0fa692;
		}
		.approvalStatus2{
			position: absolute;
			background-color: #ef3c23;
		}
		
		.mediaContainerSelected{
			background-color: #edfeff;
			border: 1px solid #009aef;
		}
		
		.mediaContainerSelected:hover{
			background-color: #edfeff;
			border: 1px solid #009aef;
		}
		
		.fakeDivClicker{
			position: absolute;
			height: 100%;
			width: 100%;
			top: 0;
			left: 0;
		}
		
		.beKeywordList{
			width: 216px;
			vertical-align:text-top;
			background-color: #EEE;
			border: 1px solid #CCC;
			min-height: 80px; 
			margin-bottom: 5px; 
			padding: 10px; 
			float: left; 
			text-align: left;
		}
		
		.beKeywordList input[type="button"]{
			float: left !important;
			margin: 2px 2px 2px 2px !important;
			padding: 2px 4px 2px 14px;
			height: 20px;
			background-image: url(images/mgr.actionlink.bg3.gif);
			background-position: -5px -3px;
		}
		
		.beKeywordList input[type="button"]:hover{
			background-image: url(images/mgr.actionlink.bg4.gif);
		}
	</style>
</head>
<body>
	<div id="mediaDetailsWindow" style="display: none; width: 400px; margin: 0; border: 1px solid #000" class="details_win"></div>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?></td>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # OUTPUT MESSAGE IF ONE EXISTS
			verify_message($vmessage);
        ?>
            <!-- ACTIONS BAR AREA -->
            <?php
				$media_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$media_group_rows = mysqli_num_rows($media_group_result);
			?>
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.media.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_media']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_media"><img src="./images/mgr.button.add.media.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['add_media']; ?>" id="img_add_media" /><br /><?php echo $mgrlang['add_media']; ?></div>
					<div style="float: left;" class="abuttons" id="abutton_batch_edit"><img src="./images/mgr.button.edit.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['batch_edit']; ?>" id="img_edit" /><br /><?php echo $mgrlang['batch_edit']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                    <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                    <?php if(in_array("contr",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_members"><img src="./images/mgr.button.members.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_wb_members']; ?>" id="img_members" /><br /><?php echo $mgrlang['gen_wb_members']; ?></div><?php } ?>
                    <?php if(in_array("pro",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_group"><img src="./images/mgr.button.group.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_group" /><br /><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>
                    <div style="float: left;" class="abuttons" id="abutton_search"><img src="./images/mgr.button.search.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_search" /><br /><?php echo $mgrlang['gen_search']; ?></div>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;">
                    <?php
						if($r_rows)
						{
					?>
						<select align="absmiddle" id="actionsdd" onchange="do_actions();">
							<option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
							<option value="set_status">&nbsp; <?php echo $mgrlang['gen_tostatus']; ?></option>
                        	<?php if($media_group_rows and in_array("pro",$installed_addons)){ ?><option value="assign_groups">&nbsp; <?php echo $mgrlang['gen_au_itg']; ?></option><?php } ?>
							<?php if(in_array("contr",$installed_addons)){ ?><option value="approve_media">&nbsp; <?php echo $mgrlang['approve_media']; ?></option><?php } ?>
						</select>
                   	<?php
						}
					?>
                </div>	
                </form>
            </div>
            
            <!-- GROUPS WINDOW -->
			<form name="grouplist" id="grouplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            	<input type="hidden" name="dtype" value="groups" />
				<div style="<?php if($_SESSION['media_dtype'] == 'groups'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="group_selector"></div>
			</form>
			
			<!-- ADVANCED SEARCH AREA -->
            <form action="mgr.media.php" method="post" id="search_from">
            <input type="hidden" name="dtype" value="search" /> 
            <div style="<?php if($_SESSION['media_dtype'] == 'search'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="media_adv_search">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['gen_search']; ?>:</p>
                    <a href="#" class='actionlink' id="button_search"><?php echo $mgrlang['gen_search']; ?></a><?php if($_SESSION['media_dtype'] == 'search'){ ?><a href="mgr.media.php?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_exit_search']; ?></a><?php } ?>
                </div>
                <div class="options_area_box">
					<?php
						if($search_words)
						{
							echo "{$mgrlang[gen_search_results]}: ";
							$x = count($search_words);
							$y = 1;
							foreach($search_words as $value)
							{
								echo "<strong>$value</strong>";
								if($y < $x)
								{
									echo ", ";
								}
								$y++;
							}
							echo "<br />";
						}
                    ?>
                    <p style="clear: both; margin: 6px 0 6px 0;"><input type="text" name="search" id='search_field' value="<?php if($_SESSION['media_dtype'] == 'search'){ echo $_SESSION['media_search']; } ?>" style="width: 250px;" /></p>
                    <div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_id" value="sfo_id" <?php if(in_array("sfo_id",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_id"> <?php echo $mgrlang['mediadet_id']; ?></label></strong></div>
					<div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_title" value="sfo_title" <?php if(in_array("sfo_title",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_title"> <?php echo $mgrlang['mediadet_title']; ?></label></strong></div>
					<div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_desc" value="sfo_desc" <?php if(in_array("sfo_desc",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_desc"> <?php echo $mgrlang['mediadet_description']; ?></label></strong></div>
					<div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_keywords" value="sfo_keywords" <?php if(in_array("sfo_keywords",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_keywords"> <?php echo $mgrlang['mediadet_keywords']; ?></label></strong></div>
					<div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_filename" value="sfo_filename" <?php if(in_array("sfo_filename",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_filename"> <?php echo $mgrlang['mediadet_filename']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_firstname" value="sfo_firstname" <?php if(in_array("sfo_firstname",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_firstname"> <?php echo $mgrlang['mediadet_mem_fname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_lastname" value="sfo_lastname" <?php if(in_array("sfo_lastname",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_lastname"> <?php echo $mgrlang['mediadet_mem_lname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_email" value="sfo_email" <?php if(in_array("sfo_email",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_email"> <?php echo $mgrlang['mediadet_mem_email']; ?></label></strong></div>
					<div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_memid" value="sfo_memid" <?php if(in_array("sfo_memid",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_memid"> <?php echo $mgrlang['mediadet_mem_id']; ?></label></strong></div>
                </div>                                
            </div>
            </form>
            
            <?php
				# MAKE SURE THE CONTRIBUTORS ADD-ON IS INSTALLED
				if(in_array("contr",$installed_addons))
				{
					$member_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members"));
					
					$passobj = "{inputbox: 'permowner', multiple: '0'}";
					
					if($_SESSION['mediaOwner'] != 0)
					{
						$member_result = mysqli_query($db,"SELECT mem_id,f_name,l_name,email FROM {$dbinfo[pre]}members WHERE mem_id = '$_SESSION[mediaOwner]'");
						$mgrMemberInfo = mysqli_fetch_object($member_result);
					}
			?>
                <form name="memberlist" id="memberlist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="dtype" value="members" />
                <input type="hidden" name="permowner" id="permowner" value="<?php echo $_SESSION['mediaOwner']; ?>" />
                <div style="<?php if($_SESSION['mediaOwner'] != 0){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="members_selector">
                    <div class="opbox_buttonbar">
                        <p><?php echo $mgrlang['media_mem_media']; ?><span style="color: #900"><?php if($_SESSION['mediaOwner'] != 0){ echo ": $mgrMemberInfo->f_name $mgrMemberInfo->l_name"; } ?></span></p>
                        <?php if($member_rows){ ?><a href="mgr.media.php?ep=1&owner=0" class='actionlink'><?php echo $mgrlang['media_exit_mem']; ?></a><?php } ?>
                    </div>               
                    <div class="options_area_box_b" id="wbmembers_inner">
                        <?php
                            # IF THERE ARE NO MEMBERS SHOW THE NOTICE
                            if(!$member_rows)
                            {
                                echo "<img src='images/mgr.notice.icon.png' align='absmiddle' /> {$mgrlang[galleries_no_mem]}";
                            }
                        ?>
                    </div>
                    <?php
                        # IF MEMBERS EXIST SHOW THE ALPHABET
                        if($member_rows)
                        {
                            echo "<p style='font-size: 11px; text-align: center; font-weight: normal; border-top: 1px solid #ffffff; border-bottom: none; padding-top: 4px;'><strong>{$mgrlang[mem_f_lname]}</strong>: ";
                            $x=0;
                            //$alphabet = explode(",",$mgrlang['alphabet']);
							
							$alphabet = array();
							
							$memlet_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members");
							while($memlet = mysqli_fetch_object($memlet_result)){
								$trimmed_lname = strtoupper(substr($memlet->l_name,0,1));
								if(!in_array($trimmed_lname,$alphabet)) $alphabet[] = $trimmed_lname;
							}
							
							sort($alphabet);
							
                            foreach($alphabet as $value){
                                //if($x==0){
                                //    echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value'  class='alphabet_on'>$value</a>";
                                //} else {
                                    echo "<a href=\"javascript:load_media_owner('$value');\" id='sl_$value' class='alphabet_off'>$value</a>";
                                //}
                                $x++;
                            }
                            echo "</p>";
                        }
                        
                        # FIND THE STARTING LETTER TO LOAD
                        if($_SESSION['mediaOwner'] != 0)
                        {
                            $fchar = strtoupper(substr($mgrMemberInfo->l_name,0,1));
                        }
                        # LOAD THE FIRST LETTER
                        else
                        {
                            $fchar = $alphabet[0];
                        }
                       
					   echo "<script>load_media_owner('$fchar');</script>";
                    ?>
                </div>
                </form>
            <?php
				}
			?>
           <form name="datalist" id="datalist" id="datalist" action="mgr.batch.edit.php" method="post">
           <div id="content" class="divTable" style="width: 100%;">
                <div class="divTableRow">
					<?php
						if($_GET['owner'] and !$r_rows)
						{
							// No media exists so do nothing for the left nav
						}
						else
						{
					?>
					<div id="media_leftnav" class="divTableCell" style="width: 280px; background-color: #FFF;border-right: 1px solid #CCC;"><!--style="float: left; width: 280px; min-height: 436px;   z-index: 1001;"-->
						<div style="display: none;" id="batchEditContainer"></div>
						
						<div style="border-top: 1px solid #FFF; padding: 14px 20px 20px 20px; display: block;" id="leftNavList"> 
							<?php
								if($r_rows)
								{
							?>
							<select name="listby" id="listby" onchange='updateOrderBy();' style="border: 1px solid #EEE; width: 146px; margin-left: -6px;">             
								<option value='0'>Order By</option>
								<option value='0'>-----------------</option>
								<option value='media_id' 	<?php if($listby == 'media_id'){ echo "selected='selected'"; } ?>>Database ID</option>
								<option value='date_added' 	<?php if($listby == 'date_added'){ echo "selected='selected'"; } ?>>Date Added</option>
								<option value='sortorder' 	<?php if($listby == 'sortorder'){ echo "selected='selected'"; } ?>>Sort Number</option>
								<option value='title' 		<?php if($listby == 'title'){ echo "selected='selected'"; } ?>>Title</option>
								<option value='filesize' 	<?php if($listby == 'filesize'){ echo "selected='selected'"; } ?>>Filesize</option>
								<option value='filename' 	<?php if($listby == 'filename'){ echo "selected='selected'"; } ?>>Filename</option>
								<option value='batch_id' 	<?php if($listby == 'batch_id'){ echo "selected='selected'"; } ?>>Batch</option>
								<option value='width' 		<?php if($listby == 'width'){ echo "selected='selected'"; } ?>>Width</option>
								<option value='height' 		<?php if($listby == 'height'){ echo "selected='selected'"; } ?>>Height</option>
								<option value='views' 		<?php if($listby == 'views'){ echo "selected='selected'"; } ?>>Views</option>
								<!--
								<option>------------</option>
								<option>Highest Rated</option>
								<option>Lowest Rated</option>
								<option>Most Viewed</option>
								<option>Least Viewed</option>
								<option>Viewed Today</option>
								<option>Viewed This Week</option>
								<option>Viewed This Month</option>
								<option>Most Purchased</option>
								<option>Least Purchased</option>
								<option>Purchsed Today</option>
								<option>Purchased This Week</option>
								<option>Purchased This Month</option>
								<option>Media Without Title</option>
								<option>Media Without Description</option>
								<option>Media Without Keywords</option>
								<option>Free Media</option>
								-->
							</select>
							<span class="<?php if($listtype == 'desc'){ echo "mtag_msort_on"; } else { echo "mtag_msort_off"; } ?>" onclick="updateListType('desc');">Desc</span>
							<span class="<?php if($listtype == 'asc'){ echo "mtag_msort_on"; } else { echo "mtag_msort_off"; } ?>" onclick="updateListType('asc');">Asce</span><br /><br />
							<?php
								}
							?>
							<!--<input type='button' value="test" onclick="$('catops').toggle();" />-->
							<ul class="catops" style="margin-top: 2px;" id="catops">
								<li><a href="mgr.media.php?dtype=default" <?php if($_SESSION['media_dtype'] == 'default'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['all_media']; ?></a></li>
								<li><a href="mgr.media.php?dtype=orphaned_media" <?php if($_SESSION['media_dtype'] == 'orphaned_media'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['orph_media']; ?></a></li>
								<li><a href="mgr.media.php?dtype=last_batch" <?php if($_SESSION['media_dtype'] == 'last_batch'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['last_batch']; ?></a></li>
								<li><a href="mgr.media.php?dtype=imported_today" <?php if($_SESSION['media_dtype'] == 'imported_today'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['added_today']; ?></a></li>
								<li><a href="mgr.media.php?dtype=imported_week" <?php if($_SESSION['media_dtype'] == 'imported_week'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['added_week']; ?></a></li>
								<li><a href="mgr.media.php?dtype=imported_month" <?php if($_SESSION['media_dtype'] == 'imported_month'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['added_month']; ?></a></li>
								<li><a href="mgr.media.php?dtype=active" <?php if($_SESSION['media_dtype'] == 'active'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['gen_active']; ?></a></li>
								<li><a href="mgr.media.php?dtype=inactive" <?php if($_SESSION['media_dtype'] == 'inactive'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['gen_inactive']; ?></a></li>
								<?php if($_SESSION['mediaOwner'] == 0){ ?>
									<li><a href="mgr.media.php?dtype=featured" <?php if($_SESSION['media_dtype'] == 'featured'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['featured_media']; ?></a></li>
									<?php if(in_array("contr",$installed_addons)){ ?>
										<li><a href="mgr.media.php?dtype=contr" <?php if($_SESSION['media_dtype'] == 'contr'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['contributors_media']; ?></a></li>
									<?php } ?>
								<?php } ?>		
								<?php if(in_array("contr",$installed_addons)){ ?>
									<li><a href="mgr.media.php?dtype=pending" <?php if($_SESSION['media_dtype'] == 'pending'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['approvalStatus0']; ?></a><span class="pending_number" id="contrMediaPendingApproval" <?php if($_SESSION['pending_media'] == 0){ echo " style='display: none;'"; } ?>><?php echo $_SESSION['pending_media']; ?></span></li>
									<li><a href="mgr.media.php?dtype=failed" <?php if($_SESSION['media_dtype'] == 'failed'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['approvalStatus2']; ?></a></li>
								<?php } ?>
								<?php if($_SESSION['mediaOwner'] == 0){ ?>
									<li><a href="#" onclick="displaybool('collectionsList');" <?php if($_SESSION['media_dtype'] == 'collection'){ echo "style='text-decoration: underline;'"; } ?>><?php echo $mgrlang['gen_colls']; ?></a>
										<div id="collectionsList" style="padding: 4px 0 0 10px; display: <?php if($_SESSION['media_dtype'] == 'collection'){ echo "block"; } else { echo "none"; } ?>">
											<?php
												$collectionListResult = mysqli_query($db,"SELECT item_name,coll_id FROM {$dbinfo[pre]}collections WHERE deleted = 0");
												while($collection = mysqli_fetch_object($collectionListResult))
												{
													echo "<a href='mgr.media.php?dtype=collection&collid={$collection->coll_id}'";
													if($_SESSION['collid'] == $collection->coll_id){ echo "style='text-decoration: underline;'"; }
													echo ">{$collection->item_name}</a><br />";	
												}
											?>
										</div>
									</li>
								<?php } ?>
								<?php if($_SESSION['media_dtype'] == 'lightbox'){ ?><li><span style="color: #666; text-decoration: underline">Lightbox: <strong><?php echo $lightbox->name; ?></strong></span></li><?php } ?>
							</ul>
							
							<div style="border-top: 1px dotted #CCC; margin-top: 10px; margin-bottom: 10px; padding-top: 10px;">
								<span style="font-size: 12pt; color: #666; font-weight: bold;font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;"><?php if($_SESSION['mediaOwner'] == 0){ echo $mgrlang['media_f_galls']; } else { echo $mgrlang['media_f_albums']; } ?></span>
							</div>
							
							<script>
								
								$j(function()
								{
									var jsLoadCounter = 0;
									
									$j('#galleryTree').jstree({
										'core' : {
											'data' : {
												'url' : 'mgr.jstree.data.php',
												'data' : function (node) {
													//alert(node.url);
													return { 'id' : node.id };
												}
											},
											'check_callback' : function(o, n, p, i, m) {
												
											},
											'themes' : {
												'responsive' : false,
												'stripes' : false,
												'dots' : false
											}
										}
										,'plugins' : ['state']
									})
									.on('select_node.jstree', function (e, data) {
										//document.location.href = 'mgr.media.php?dtype=gallery&galid='+data.selected;
									})
									.on('activate_node.jstree', function (e, data) {
										
											//alert(data.node.id);
											document.location.href = 'mgr.media.php?dtype=gallery&galid='+data.node.id;
										
									})
									.on('changed.jstree', function (e, data) {
										if(data && data.selected && data.selected.length)
										{
											//alert(data.testing);
											/*									
											if(jsLoadCounter == 0)
												jsLoadCounter = 1;
											else
												document.location.href = 'mgr.media.php?dtype=gallery&galid='+data.selected;
											//document.location.href = 'mgr.media.php?dtype=gallery&galid='+data.selected;
											*/
										}
									})
									.on('ready.jstree', function (e, data) {
										//alert('allloaded');
										//$j.jstree("deselect_all");
										//$j("#galleryTree").jstree("deselect_all");
										
										<?php
											//if($_GET['dtype'] != 'gallery') echo "\$j('#galleryTree').jstree(true).clear_state();"; // Clear the state 
											//if($_GET['dtype'] != 'gallery') echo "setTimeout(clearTreeSelection,5000);";
										?>
										
									});
									
									//http://www.jstree.com/docs/json/
									
									<?php
										if($_GET['dtype'] != 'gallery') echo "\$j('#galleryTree').jstree(true).clear_state();"; // Clear the state 
									?>
									
									function clearTreeSelection()
									{
										$j("#galleryTree").jstree("deselect_all");
									}
																		
									//'state'
									
									$j('#testingjsTreeButton').on( "click", function(){ 
										//$j(galleryTree).hide();
										$j("#galleryTree").jstree("deselect_all");
										//$j('#galleryTree').jstree(true).clear_state();
										
										//alert($j("#galleryTree").jstree('get_selected'));
										//$j('#galleryTree').jstree("init");
									});
									/*
									
									.on('changed.jstree', function (e, data) {
										if(data && data.selected && data.selected.length)
										{
											//alert(data.selected);
											//document.location.href = 'mgr.media.php?dtype=gallery&galid='+data.selected;
										}
									})
									*/
								});
							</script>
							
							<!--<div><input type="button" value="test" id="testingjsTreeButton"></div>-->
							
							<div id="galleryTree">Loading Gallery Tree...</div>
							
							<div style="max-height: 400px; overflow: auto" id="galleryScroller"><!--<input type="button" value="mediaWidth" onclick="mediaWidth();" />-->
							<?php
								//echo "testing"; exit;
								/*
								if($_SESSION['mediaOwner'])
								{
									$gal_mem = $_SESSION['mediaOwner'];
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
								
								//$gallery_parent = $gallery->parent_gal;
								$gallery_current = 113;
								
								# BUILD THE GALLERIES AREA
								$mygalleries = new build_galleries;
								$mygalleries->scroll_offset_id = "galleryScroller";
								$mygalleries->scroll_offset = 1;
								$mygalleries->edit_link = 1;
								$mygalleries->gotolink = 'mgr.media.php?dtype=gallery&galid=';
								$mygalleries->options_name = 'media_galleries[]';
								$mygalleries->options = "";
								$mygalleries->output_struc_array(0);
								*/
							?>
							</div>
						</div>
					</div>
					<?php
						}
					?>
					<div id="media" class="divTableCell" style="vertical-align: top; position: relative;"><!--  style="position: relative; float: left;" -->
						<?php
							# CHECK TO MAKE SURE THERE ARE RECORDS
							if(!empty($r_rows)){
							   include('mgr.perpage.php');	
						?>
						<div id="mediadivs" style="min-height: 700px; padding-bottom: 570px;">
							<?php
								$iconSize = $config['MediaIconPreviewSize'];
								
								$mgrBasePath = dirname(__FILE__); //$_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/";
								$basePath = dirname(dirname(__FILE__)); //$_SERVER['DOCUMENT_ROOT'] . dirname(dirname($_SERVER['PHP_SELF']));
								
								# SELECT LOOP THRU ITEMS									
								while($media = mysqli_fetch_object($media_result))
								{
									# SET THE ROW COLOR
									@$row_color++;
									if ($row_color%2 == 0) {
										$row_class = "list_row_on";
										$color_fade = "EEEEEE";
									} else {
										$row_class = "list_row_off";
										$color_fade = "FFFFFF";
									}
									
									# GET THUMBNAIL INFO
									$thumb_result = mysqli_query($db,"SELECT thumb_filename,thumb_width,thumb_height FROM {$dbinfo[pre]}media_thumbnails WHERE thumbtype = 'icon' AND media_id = '$media->media_id'");
									$thumb_rows = mysqli_num_rows($thumb_result);
									
									if($thumb_rows)
									{
										$thumb = mysqli_fetch_object($thumb_result);
									
										# FIND WIDTH AND HEIGHT
										$newsize = get_scaled_size_nosource($thumb->thumb_width,$thumb->thumb_height,$iconSize,0);
										
										$cacheFile = "id{$media->media_id}-".md5("mgr-{$media->media_id}-{$media->folder_id}-icons-{$thumb->thumb_filename}-{$config[MediaIconPreviewSize]}").'.jpg'; // Name of cached file - added mgr to make sure it is always specific to the management area
										$cachePathFile = $basePath."/assets/cache/{$cacheFile}";
										
										//echo $cachePathFile; exit;
										if(file_exists($cachePathFile))
											$thumbLink = "../assets/cache/{$cacheFile}";
										else
											$thumbLink = "mgr.media.preview.php?folder_id={$media->folder_id}&media_id={$media->media_id}&src={$thumb->thumb_filename}";
									}
									else
									{
										$newsize[0] = $iconSize;
										$newsize[1] = round($iconSize * .75);
										
										$thumbLink = "images/mgr.theme.blank.gif";
									}
									
									$subDivHeight = ($iconSize + 30);
									$mediaMargin = round(($subDivHeight - $newsize[1])/2)-10;

							?>
							<div class="media_div" id="media_div_<?php echo $media->media_id; ?>" style="width: <?php echo ($iconSize + 50); ?>px; height: <?php echo ($iconSize + 25); ?>px;">
								<div class="fakeDivClicker"></div>
								
								<div id="approvalStatus<?php echo $media->media_id; ?>" class="approvalStatus<?php echo $media->approval_status; ?>"><?php echo $mgrlang['approvalStatus'.$media->approval_status]; ?></div>
								<input type="checkbox" class="atitems opac_70 mediaCheckbox" name="items[]" value="<?php echo $media->media_id; ?>" id="mediaCheckbox<?php echo $media->media_id; ?>" />
								<div style="height: <?php echo $subDivHeight; ?>px; overflow: hidden;">
									<div class='media_div_inner' style="margin-top: <?php echo $mediaMargin; ?>px; width: <?php echo $newsize[0]; ?>px; min-height: <?php echo $newsize[1]; ?>px;" onclick="editMediaInfo(<?php echo $media->media_id; ?>);"><img src="<?php echo $thumbLink; ?>" width="<?php echo $newsize[0]; ?>" onmouseover="createDetailsWindow(<?php echo $media->media_id; ?>)" onmouseout="hideDetailsWindow()" id="thumbnailPreviewItem<?php echo $media->media_id; ?>" /></div>
								</div>
								<div class="media_div_actions">
									<img src="images/mgr.icon.active.<?php echo $media->active; ?>.png" class="media_action" id="status_<?php echo $media->media_id; ?>" onclick="setActiveStatus(<?php echo $media->media_id; ?>);" title="<?php echo $mgrlang['active_inactive']; ?>" />
									<?php if($config['settings']['hpfeaturedmedia'] or $config['settings']['featuredpage']){ ?><img src="images/mgr.icon.featured.<?php echo $media->featured; ?>.png" class="media_action" id="featured_<?php echo $media->media_id; ?>" onclick="featuredStatus(<?php echo $media->media_id; ?>);" title="<?php echo $mgrlang['gen_featured']; ?>" /><?php } ?>
									<img src="images/mgr.icon.delete.png" class="media_action" onclick="deleterec(<?php echo $media->media_id; ?>);" title="Delete" />
									<img src="images/mgr.icon.edit.png" class="media_action" title="<?php echo $mgrlang['edit_details']; ?>" onclick="editMediaInfo(<?php echo $media->media_id; ?>);" />
									<?php if($_SESSION['media_dtype'] == 'gallery'){ ?><img src="images/mgr.icon.seticon.<?php if($gallery->icon == $media->media_id){ echo 1; } else { echo 0; } ?>.png" class="media_action" id="avatar_<?php echo $media->media_id; ?>" avatar="1" onclick="setGalleryAvatar(<?php echo $media->media_id; ?>);" title="<?php echo $mgrlang['set_gal_icon']; ?>" /><?php } ?><!--<a href='javascript:delete_ip($ip->ip_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a>-->
								</div>
							</div>
							<?php
								}
							?>
						</div>
						<?php
								$mediaPerPageFooter = true;
								include('mgr.perpage.php');
							}
							else
							{
								notice($mgrlang['gen_empty']);
								//echo "test";
							}
						?>
					</div>
                </div>      
            </div>
            </form>
            <script language="javascript">						
                //mediaWidth();
                //galleriesHeight();
				<?php
					if($_SESSION['media_dtype'] == 'gallery')
					{
				?>
					$('gallink_<?php echo $_SESSION['galid']; ?>').setStyle({
						 textDecoration: 'underline'
					});
				<?php
					}
				?>
            </script>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>
	</div>
		
</body>
</html>
<?php mysqli_close($db); ?>