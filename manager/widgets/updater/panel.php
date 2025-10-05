<?php
	###################################################################
	####	WP: UPDATER : VERSION 1.0                              ####
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
		require_once('../../../assets/includes/tweak.php');	
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
	$panel_name = $wplang['updater_title'];
	$panel_id = basename(dirname(__FILE__)); // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
	$panel_id = preg_replace("[^A-Za-z0-9]", "", $panel_id); // CLEAN THE PANEL ID JUST IN CASE
	$panel_enable = ($config['BrandAdWidgets']) ? 1 : 0; // ENABLE PANEL
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
				.wp_updater_blue{
					color: #FFF; !important
					font-size: 9px;
					font-weight: bold;
					padding: 2px 4px 2px 4px;
					width: 70px;
					text-align: center;
					background-color: #75b1db;
					float: left;
					width: 120px;
					padding: 0px;
				}
				.wp_updater_blue_footer{
					font-weight: normal;
					font-size: 10px;
					padding: 3px;
					background-color: #629dc6;
				}
				.wp_updater_red{
					color: #FFF; !important
					font-size: 9px;
					font-weight: bold;
					padding: 2px 4px 2px 4px;
					width: 70px;
					text-align: center;
					background-color: #bb5f5f;
					float: left;
					width: 120px;
					padding: 0px;
				}
				.wp_updater_red_footer{
					font-weight: normal;
					font-size: 10px;
					padding: 3px;
					background-color: #a54e4e;
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
			# INCLUDE MGR CONFIG FILE
			require_once('../../mgr.config.php');
			# INCLUDE DATABASE CONFIG FILE
			require_once('../../../assets/includes/db.config.php');
			# INCLUDE DATABASE CONNECTION FILE
			require_once('../../../assets/includes/db.conn.php');
			# INCLUDE SHARED FUNCTIONS FILE
			require_once('../../../assets/includes/shared.functions.php');
			# SELECT THE SETTINGS DATABASE
			require_once('../../mgr.select.settings.php');
			
			//echo $config['product_version']; exit;
			
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
					name:		'<?php echo $wplang['updater_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>
            <div style="margin: 1px; overflow: auto;">
                <?php
					if(ini_get("allow_url_fopen")){
						$timeout = 3;
						$old = ini_set('default_socket_timeout', $timeout);
						$dataFile = fopen('http://www.ktools.net/webmgr/push.updatecheck.php?product='.$config['productCode'].'&version='.$config['productVersion'].'&builddate='.$config['productBuildDate'], 'r');
						ini_set('default_socket_timeout', $old);
						stream_set_timeout($dataFile, $timeout);
						stream_set_blocking($dataFile, 0);
						
						if ($dataFile){
							//while (!feof($dataFile)){
								$currentversion = fgets($dataFile, 4096);
								//echo $buffer;
								
								if($config['productVersion'] >= $currentversion)
								{
									echo "<div class='wp_updater_blue'><p style='font-size: 24px; margin: 0; padding: 6px 6px 2px 6px;'>$currentversion</p><p class='wp_updater_blue_footer'>$wplang[updater_newest]</p></div>";
								}
								else
								{
									echo "<div class='wp_updater_red'><p style='font-size: 24px; margin: 0; padding: 14px 6px 2px 6px; height: 38px'>$currentversion</p><p class='wp_updater_red_footer'>$wplang[updater_update]</p></div>";
									//echo "<div class='mtag_bad' style='float: left; width: 80px;'><span style='font-size: 24px;'>$currentversion</span><br /><div style='font-weight: normal; font-size: 10px;'>Update Available</div></div>";
								}
								
								echo "<div style='float: left; padding: 5px 10px 0 10px; line-height: 1.75; font-style: italic; color: #7a7a7a'>$wplang[updater_yourv]<strong> $config[productVersion]</strong><br />$wplang[updater_newestis]<strong> $currentversion</strong><br />$wplang[updater_getnew].</div>";
								
							//}		
							fclose($dataFile);
						} else {
							die( "fopen failed" ) ;
						}
						
					} else {
						echo $mgrlang['gen_error_28'];
					}
				?>
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