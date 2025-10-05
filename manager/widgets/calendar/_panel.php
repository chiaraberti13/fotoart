<?php
	###################################################################
	####	WP: CALENDAR : VERSION 1.0                             ####
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
	$panel_name = $wplang['calendar_title'];
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
			# SELECT THE SETTINGS DATABASE
			require_once('../../mgr.select.settings.php');
			
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
					name:		'<?php echo $wplang['calendar_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>            
            <div style="margin: 6px;">
                <!--<img src="wpanels/<?php echo $panel_filename; ?>/panel_icon2.png" align="left" class="wpanel_image" />-->
                Calendar<br /><br /><br /><br /><br /><br /><br /><br /><br />
                <!--
                <a href="javascript:panel_reload(<?php echo $panel_id; ?>)">[Reload Panel]</a> - Built-in reload function<br /><br />
                <a href="javascript:panel_loader(<?php echo $panel_id; ?>);">[Panel Loader]</a> - Built-in show loader function<br /><br />                
                <a href="javascript:panel_close(<?php echo $panel_id; ?>);">[Close Panel]</a> - Built-in close panel function<br /><br />
                <a href="javascript:panel_loadpage('','&panel_mode=test&var=1&var2=2',<?php echo $panel_id; ?>);">[Load Page]</a> - Built-in panel load page (page(leave blank to use panel.php),parameters,panel object)<br /><br />
                <a href="javascript:panel_clear(<?php echo $panel_id; ?>);">[Clear Panel]</a><br /><br />
                <a href="javascript:panel_hack(<?php echo $panel_id; ?>);">[Panel Hack Test]</a>
                -->
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