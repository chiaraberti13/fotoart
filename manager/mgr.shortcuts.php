<?php
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	//require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE
	//require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
	
	//sleep(2);
?>
<div class="shortcuts_header" id="galleries">Galleries & Keywords</div>
	<div class="subsubon" onclick="bringtofront('1');" id="tab1" style="border-left: 0px;">Galleries</div>
	<div class="subsuboff" onclick="bringtofront('2');" id="tab2" style="border-right: 1px solid #6b6b6b;">Keywords</div>
    <div id="galleries" style="clear: both; font-size: 11px; padding: 5px 0 5px 0; border-top: 1px solid #ffffff; overflow: auto;">
    	<?php
			// CREATE ARRAY TO WORK WITH							
			$folders = array();
			$folders['name'] = array();
			$folders['folder_id'] = array();
			$folders['parent_id'] = array();
			$folders['folder_rows'] = array();
			$folders['pass_protected'] = array();
			$folder_array_id = 1;
			
			// READ STRUCTURE FUNCTION															
			read_gal_structure();
			
			# CHECK TO SEE IF THERE ARE ANY GALLERIES
			if(empty($galls)){
				echo $mgrlang['galleries_nogal'];
			} else {
				# BUILD THE GALLERIES AREA
				$mygalleries = new build_galleries;
				$mygalleries->alt_colorA = "f5f5f5";
				$mygalleries->alt_colorB = "eeeeee";
				$mygalleries->scroll_offset_id = "galleries";
				$mygalleries->scroll_offset = 1;
				$mygalleries->output_struc_array(0);	
			}		
			
		?>    
    </div>
<div class="shortcuts_header" id="custom_categories">Shortcuts</div>
	<ul>
		<li><strong>Media</strong>: <br /><a href="#" class="shortcutsnav">Last Batch Imported</a> | <a href="#" class="shortcutsnav">Imported Today</a> | <a href="#" class="shortcutsnav">Imported This Week</a> | <a href="#" class="shortcutsnav">Imported This Month</a>  | <a href="#" class="shortcutsnav">In Queue</a> | <a href="#" class="shortcutsnav">Orphaned</a></li>
		<li><strong>Media Rated</strong>: <br /><a href="#" class="shortcutsnav">Lowest</a> | <a href="#" class="shortcutsnav">Highest</a></li>
		<li><strong>Media Viewed</strong>: <br /><a href="#" class="shortcutsnav">Least</a> | <a href="#" class="shortcutsnav">Most</a> | <a href="#" class="shortcutsnav">Today</a> | <a href="#" class="shortcutsnav">This Week</a> | <a href="#" class="shortcutsnav">This Month</a></li>
		<li><strong>Media Purchased</strong>: <br /><a href="#" class="shortcutsnav">Least</a> | <a href="#" class="shortcutsnav">Most</a> | <a href="#" class="shortcutsnav">Today</a> | <a href="#" class="shortcutsnav">This Week</a> | <a href="#" class="shortcutsnav">This Month</a></li>
        <li><strong>Media Added By Customers</strong>: <br /><a href="#" class="shortcutsnav">Today</a> | <a href="#" class="shortcutsnav">This Week</a> | <a href="#" class="shortcutsnav">This Month</a> | <a href="#" class="shortcutsnav">All</a></li>
		<!--
        <li><a href="#" class="shortcutsnav">Queued Photos</a></li>
		<li><a href="#" class="shortcutsnav">Orphaned Photos</a></li>
		<li><a href="#" class="shortcutsnav">Photos With No Thumbnail</a></li>
        <li><a href="#" class="shortcutsnav">Highest Rated</a></li>
		<li><a href="#" class="shortcutsnav">Lowest Rated</a></li>
		<li><a href="#" class="shortcutsnav">Most Viewed</a></li>
		<li><a href="#" class="shortcutsnav">Least Viewed</a></li>
		<li><a href="#" class="shortcutsnav">Viewed Today</a></li>
		<li><a href="#" class="shortcutsnav">Viewed This Week</a></li>
		<li><a href="#" class="shortcutsnav">Viewed This Month</a></li>
		<li><a href="#" class="shortcutsnav">Most Purchased</a></li>
		<li><a href="#" class="shortcutsnav">Least Purchased</a></li>
		<li><a href="#" class="shortcutsnav">Purchased Today</a></li>
		<li><a href="#" class="shortcutsnav">Purchased This Week</a></li>
		<li><a href="#" class="shortcutsnav">Purchased This Month</a></li>
		<li><a href="#" class="shortcutsnav">Added By Photographer</a></li>
		<li><a href="#" class="shortcutsnav">+Last 10 Manager Searches</a></li>
		<li><a href="#" class="shortcutsnav">+Last 20 Public Searches</a></li>
        <li><a href="#" class="shortcutsnav">+Saved Searches</a></li>
        <li><a href="#" class="shortcutsnav">+File Extensions</a></li>
        <li><a href="#" class="shortcutsnav">+File Types (Groups)</a></li>
        -->
	</ul>
<div class="shortcuts_header" id="collections">Collections</div>
	<!--<div align="right"><a href="" class="shortcutsfeature">Create New Collection</a></div>-->
	<ul>
		<li><a href="#" class="shortcutsnav">100 Animal Photos</a>
		<li><a href="#" class="shortcutsnav">Jones Wedding Photos</a>
		<li><a href="#" class="shortcutsnav">Background Photos</a>
		<li><a href="#" class="shortcutsnav">Wallpapers</a>
		<li><a href="#" class="shortcutsnav">Horse Photos</a>
		<li><a href="#" class="shortcutsnav">50 Photos for $10</a>
	</ul>
<!--
<div class="shortcuts_header" id="custom_categories">Saved Searches</div>
	<ul>
		<li><a href="#" class="shortcutsnav">Test</a>
		<li><a href="#" class="shortcutsnav">Test2</a>
	</ul>
-->
