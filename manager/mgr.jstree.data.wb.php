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
	
	$galOwner = ($_GET['galOwner']) ? $_GET['galOwner'] : 0; // Owner
	$mediaID  = ($_GET['mediaID']) ? $_GET['mediaID'] : 0; // Media ID
	
	if($_GET['id'] == '#' or !$_GET['id'])
		$parentGallery = 0;
	else
		$parentGallery = $_GET['id'];
	
	if($mediaID)
	{
		$mediaInGalleries = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id = '{$mediaID}'");
		while($galleries = mysqli_fetch_assoc($mediaInGalleries))
			$inGalleries[] = $galleries['gallery_id'];	
	}
	

	$galResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}galleries WHERE parent_gal = '{$parentGallery}' AND owner='{$galOwner}' ORDER BY name");
	$galRows = mysqli_num_rows($galResult);
	if($galRows)
	{	
		//echo $children."<br>";
		
		while($gallery = mysqli_fetch_assoc($galResult))
		{
			$subGalResult = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}galleries WHERE parent_gal = '{$gallery[gallery_id]}' AND owner='{$galOwner}'");
			$subGalRows = mysqli_num_rows($subGalResult);
			
			$mediaInThisGallery = in_array($gallery['gallery_id'],$inGalleries) ? true : false;
			
			$galChildren = ($subGalRows > 0) ? true : false;
			
			$treeData[] = array('text' => $gallery['name'], 'children' => $galChildren,  'id' => $gallery['gallery_id'], 'icon' => '', 'state' => array('selected' => $mediaInThisGallery));
		}
	}
	else exit;
	//print_k($treeData); exit;
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($treeData);
?>