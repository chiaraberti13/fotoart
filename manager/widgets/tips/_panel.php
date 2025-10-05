<?php
	###################################################################
	####	WP: TIPS : VERSION 1.0                                 ####
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
	$panel_name = $wplang['tips_title'];
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
			<!--<script type="text/javascript" src="wpanels/test.js"></script>-->
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
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>$wplang[load_failed]</div>"; exit;
			}
			
			$rand_keys=array_rand($welcometip,2);
			$weltip = $welcometip[$rand_keys[0]];
			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $wplang['tips_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>            
            <div style="margin: 16px;">
                <!--<img src="wpanels/<?php echo $panel_filename; ?>/panel_icon2.png" align="left" class="wpanel_image" />-->
                <?php echo $weltip; ?>
                <p style="clear: both; text-align:right; overflow: auto; padding: 5px; height: 20px;" /><a href="javascript:load_panel('box_tips_content','tips/panel.php','');" class="actionlink"><?php echo $wplang['tips_next']; ?></a></p>
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