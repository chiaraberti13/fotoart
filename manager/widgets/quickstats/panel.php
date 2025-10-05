<?php
	###################################################################
	####	WP: QUCIK STATS : VERSION 1.0                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-21-2008                                     ####
	####	Modified: 1-26-2010                                    #### 
	###################################################################

	error_reporting(E_ALL ^ E_NOTICE); // All but notices

	define('BASE_PATH',dirname(dirname(dirname(dirname(__FILE__))))); // Define the base path
	
	# GRAB THE PANEL MODE
	$panel_mode = ($_GET['panel_mode']) ? $_GET['panel_mode']: $panel_mode;

	# INCLUDE THE SESSION FILE IF THE MODE IS OTHER THAN PRELOAD
	if($panel_mode != "preload"){
		# INCLUDE SESSION FILE
		require_once('../../../assets/includes/session.php');
		$panel_language = ($_SESSION['sess_mgr_lang']) ? $_SESSION['sess_mgr_lang']: 'english';
		# GRAB THE NAME OF THE CURRENT LANGUAGE THAT IS BEING USED
		if(file_exists('../../../assets/languages/'.$panel_language.'/lang.widgets.php'))
		{
			require_once('../../../assets/languages/'.$panel_language.'/lang.widgets.php');
		}
		else
		{
			require_once('../../../assets/languages/english/lang.widgets.php');
		}
	}
	
	# INCLUDE THE LANGUAGE INFO	IN THE PANEL INSTEAD OF READING FROM THE LANGUAGE FILE
	/*
	switch($panel_language){
		default:
		case "english": // ALL YOUR LANGUAGE FOR THIS PANEL SHOULD GO BELOW
			# structure should be $wplang[{panel_id}_{description}] = "YOUR LANGUAGE";
			$wplang['notes_title'] 	= "Notes";
		break;
		case "spanish":
			$wplang['notes_title'] 	= "Panel En blanco";
		break;
		case "german":
			$wplang['notes_title'] 	= "Leerplatte";
		break;
	}
	*/
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['qstats_title'];
	$panel_id = basename(dirname(__FILE__)); // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
	$panel_id = preg_replace("[^A-Za-z0-9]", "", $panel_id); // CLEAN THE PANEL ID JUST IN CASE
	$panel_enable = 1; // ENABLE PANEL
	$panel_version = 1; // THIS SHOULD ALWAYS BE 1 UNLESS OTHERWISE NOTED BY KTOOLS
	$panel_filename = basename(dirname(__FILE__));
	$panel_template = 1; // USE THE PANEL TEMPLATE - CURRENTLY NOT USED BUT MAY BE IN THE FUTURE
	
	switch($panel_mode){
		case "preload";
			# PRELOAD SOME JAVASCRIPT BELOW
			# THIS IS THE OLD WAY OF DOING IT. PREFERABLY THE JAVASCRIPT SHOULD BE IN THE LOAD CASE TO INCREASE INITIAL LOAD TIMES ON THE WELCOME PAGE	
		?>
        	<script language="javascript"></script>
            <style>
				.wp_qstats_rstat{
					float: left;
					width: 20%;
					background-color: #75b1db;
					text-align: center;
					color: #FFF;
					font-weight: bold;
					height: 30px;
					/*-moz-border-radius-bottomright: 8px;
					border-bottom-right-radius: 8px;
					-moz-border-radius-topright: 8px;
					border-top-right-radius: 8px;*/
				}
				.wp_qstats_rstat p{
					padding-top: 5px;
					vertical-align: middle;
					font-size: 16px;
				}
				.wp_qstats_lstat{
					float: left;
					width: 80%;
					background-color: #FFF;
					height: 30px;
					/*border-top: 1px solid #CCC;
					-moz-border-radius-bottomleft: 8px;
					border-bottom-left-radius: 8px;
					-moz-border-radius-topleft: 8px;
					border-top-left-radius: 8px;*/
					
				}
				.wp_qstats_lstat p{
					padding: 8px 0 0 6px;
					vertical-align: middle;
					text-align: left;
				}
				.wp_qstats_lstat p span{
					padding: 10px 6px 6px 6px;
					font-size: 12px;
					color: #666;
				}
			</style>
        <?php			
		break;
		case "install":
			# INSTALL THE ADD-ON IF NEEDED
		break;
		case "load":		
			# KEEPS THE PAGE FROM CACHING
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			# INCLUDE DATABASE CONFIG FILE
			require_once('../../../assets/includes/db.config.php');
			# INCLUDE DATABASE CONNECTION FILE
			require_once('../../../assets/includes/db.conn.php');
			# INCLUDE SHARED FUNCTIONS FILE
			require_once('../../../assets/includes/shared.functions.php');
			# SELECT THE SETTINGS DATABASE
			require_once('../../mgr.select.settings.php');
			# INCLUDE MANAGER ADDONS FILE
			require_once('../../../assets/includes/addons.php');
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>$wplang[load_failed]</div>"; exit;
			}
			
			# HERE YOU CAN ALSO CHECK TO SEE IF THE ADD-ON IS INSTALLED			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $wplang['qstats_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>
            <?php				
				$last_login = $_SESSION['admin_user']['last_login'];
				$new_members = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE signup_date > '{$last_login}'"));

				$new_orders = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(order_id) FROM {$dbinfo[pre]}orders WHERE order_date > '$last_login' AND (order_status = 1 OR order_status = 0) AND deleted = 0")); // [todo] still have to mark only completed orders
				
				if(in_array("commenting",$installed_addons))
				{
					$new_comments = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments WHERE posted > '$last_login'"));
				}
				
				if(in_array("tagging",$installed_addons))
				{
					$new_tags = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mt_id) FROM {$dbinfo[pre]}media_tags WHERE posted > '$last_login'"));
				}
				
				if(in_array("rating",$installed_addons))
				{
					$new_ratings = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mr_id) FROM {$dbinfo[pre]}media_ratings WHERE posted > '$last_login'"));
				}
				
				function alternate_widget_rows($side)
				{
					global $row_color;
					# SET THE ROW COLOR					
					if ($row_color%2 == 0) 
					{
						$color = ($side == 'a') ? "FFF" : "75b1db";
					}
					else
					{	
						$color = ($side == 'a') ? "EEE" : "629dc6";
					}
					if($side != 'a') { @$row_color++; }
					return $color;
				}
			?>           
            <div style="margin: 1px; overflow: auto">
                <div style="width: 50%; float: left;" id="wp_qstats_row1">
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($new_members > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_logmem']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $new_members; ?></p></div>
                    
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($new_orders > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_logorders']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $new_orders; ?></p></div>
                    
                    <?php
						# COMMENTS ADD-ON
						if(in_array("commenting",$installed_addons))
						{
					?>
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($new_comments > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_logcomm']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $new_comments; ?></p></div>
                    <?php
						}
					?>
                    
                    <?php
						# COMMENTS ADD-ON
						if(in_array("tagging",$installed_addons))
						{
					?>
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($new_tags > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_logtags']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $new_tags; ?></p></div>
                    <?php
						}
					?>
                    
                    <?php
						# COMMENTS ADD-ON
						if(in_array("rating",$installed_addons))
						{
					?>
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($new_ratings > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_lograte']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $new_ratings; ?></p></div>
                    <?php
						}
					?>                    
                    
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_member_bios'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_penbios']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_member_bios']; ?></p></div>
                    
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_support_tickets'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_pensupport']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_support_tickets']; ?></p></div>
                </div>
                <?php alternate_widget_rows('b'); ?>
                <div style="width: 50%; float: left;" id="wp_qstats_row2">
                    <div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_members_inactive'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_penmem']; ?></span></p></div>
                    <div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_members_inactive']; ?></p></div><!--  style='background-color: #66a366' -->

                   	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_orders'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_penorders']; ?></span></p></div>
                   	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_orders']; ?></p></div>

                    <?php
						# COMMENTS ADD-ON
						if(in_array("commenting",$installed_addons))
						{
					?>                    
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_media_comments'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_pencomm']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_media_comments']; ?></p></div>
                    <?php
						}
					?>
                    
                    <?php
						# COMMENTS ADD-ON
						if(in_array("tagging",$installed_addons))
						{
					?>
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_media_tags'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_pentags']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_media_tags']; ?></p></div>
                    <?php
						}
					?>                  
                    
                    <?php
						# COMMENTS ADD-ON
						if(in_array("rating",$installed_addons))
						{
					?>
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_media_ratings'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_penrate']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_media_ratings']; ?></p></div>
                    <?php
						}
					?>
					
					<?php
						# COMMENTS ADD-ON
						if(in_array("ticketsystem",$installed_addons))
						{
					?> 
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_member_avatars'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_penavatars']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_member_avatars']; ?></p></div>
                    <?php
						}
					?>
					
					<?php
						# CONTRIBUTORS ADD-ON
						if(in_array("contr",$installed_addons))
						{
					?>
                    	<div class='wp_qstats_lstat' style='background-color: #<?php echo alternate_widget_rows('a'); ?>'><p><span <?php if($_SESSION['pending_media'] > 0){ echo "style='font-weight: bold; color: #333;'"; } ?>><?php echo $wplang['qstats_pending_media']; ?></span></p></div>
                    	<div class='wp_qstats_rstat' style='background-color: #<?php echo alternate_widget_rows('b'); ?>'><p><?php echo $_SESSION['pending_media']; ?></p></div>
                    <?php
						}
					?>
                </div>
            </div>
        <?php
		break;
		default:
			# DO NOTHING - NOT LOADED
		break;
		case "test":
			echo "Works!!";
		break;
	}	
?>