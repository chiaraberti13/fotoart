<?php
	require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');				# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');					# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
	
	$galOwner = ($_SESSION['mediaOwner']) ? $_SESSION['mediaOwner'] : 0; // Load galleries for a specific members
	
	if($_GET['id'] == '#' or !$_GET['id'])
		$parentGallery = 0;
	else
		$parentGallery = $_GET['id'];


	$galResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}galleries WHERE parent_gal = '{$parentGallery}' AND owner='{$galOwner}' ORDER BY name");
	$galRows = mysqli_num_rows($galResult);
	if($galRows)
	{	
		//echo $children."<br>";
		
		while($gallery = mysqli_fetch_assoc($galResult))
		{
			$subGalResult = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}galleries WHERE parent_gal = '{$gallery[gallery_id]}' AND owner='{$galOwner}'");
			$subGalRows = mysqli_num_rows($subGalResult);
			
			$galChildren = ($subGalRows > 0) ? true : false;
			
			$treeData[] = array('text' => $gallery['name'], 'children' => $galChildren,  'id' => $gallery['gallery_id'], 'icon' => '', 'url' => 'google.com');
		}
	}
	else exit;
	//print_k($treeData); exit;
	
	/*

	//$gallery_parent = $gallery->parent_gal;
	$gallery_current = 0;
	
	# BUILD THE GALLERIES AREA
	$mygalleries = new build_galleries;
	$mygalleries->scroll_offset_id = "gals";
	//$mygalleries->alt_colorA = "efefef";
	$mygalleries->scroll_offset = 1;
	$mygalleries->selected_gals = $inGalleries;
	$mygalleries->options_name = 'media_galleries[]';
	$mygalleries->options = "checkbox";
	$mygalleries->output_struc_array(0);
	
	*/
	
	/*
	$treeData[] = array('text' => 'This is a gallery name', 'children' => true,  'id' => createRandomID(), 'icon' => '');
	$treeData[] = array('text' => 'Gallery b', 'children' => false,  'id' => createRandomID(), 'icon' => '');
	$treeData[] = array('text' => 'A test gallery c', 'children' => true,  'id' => createRandomID(), 'icon' => '');
	$treeData[] = array('text' => 'Another test for gallery', 'children' => true,  'id' => createRandomID(), 'icon' => '');
	*/
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($treeData);
?>