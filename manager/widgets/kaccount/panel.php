<?php
	###################################################################
	####	WP: KTOOLS ACCOUNT : VERSION 1.0  			           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-21-2008                                     ####
	####	Modified: 5-7-2008                                     #### 
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
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['kaccount_title'];
	$panel_id = basename(dirname(__FILE__)); //"blankwp"; // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
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
				.wp_account_grey{
					color: #666; !important
					font-size: 9px;
					font-weight: bold;
					padding: 2px 4px 2px 4px;
					width: 70px;
					text-align: center;
					background-color: #e5e5e5;
				}
				.wp_account_grey_footer{
					font-weight: normal;
					font-size: 10px;
					padding: 3px;
					background-color: #d1d0d0;
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
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>Loading Failed!</div>"; exit;
			}
			
			# HERE YOU CAN ALSO CHECK TO SEE IF THE ADD-ON IS INSTALLED			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $mgrlang['wp_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}				
			</script>           
            <div style="margin: 1px; line-height: 1.7; overflow: auto;">
                <?php
					if(ini_get("allow_url_fopen")){
						$timeout = 3;
						$old = ini_set('default_socket_timeout', $timeout);
						$dataFile = fopen('http://www.ktools.net/webmgr/push.account.php?product='.$config['productCode'].'&version='.$config['productVersion'].'&builddate='.$config['productBuildDate'].'&serial='.$config['settings']['serial_number'], 'r');
						ini_set('default_socket_timeout', $old);
						stream_set_timeout($dataFile, $timeout);
						stream_set_blocking($dataFile, 0);
						
						if ($dataFile){
							while (!feof($dataFile)){
								$content.= fgets($dataFile, 4096);
								//echo $buffer;
							}							
							preg_match ('/\\<status\\>(.*?)\\<\\/status\\>/', $content, $status);
							preg_match ('/\\<accountname\\>(.*?)\\<\\/accountname\\>/', $content, $accountname);
							preg_match ('/\\<supportremaining\\>(.*?)\\<\\/supportremaining\\>/', $content, $supportremaining);
							preg_match ('/\\<messages\\>(.*?)\\<\\/messages\\>/', $content, $messages);
							preg_match ('/\\<affiliatesales\\>(.*?)\\<\\/affiliatesales\\>/', $content, $affiliatesales);
							
							//print_r($status);
							
							if($status[1] == 1)
							{
								//echo "<p style='float: right;'><a href='http://www.ktools.net/members/' target='_blank' class='actionlink'>Your Account</a></p>";
								echo "<p style='font-size: 12px; color: #666; padding: 6px 6px 6px 10px; background-color: #FFF; margin: 0 1px 0 0; border-bottom: 1px solid #CCC; text-align: right;'>";
								echo "<span style='font-size: 12px;'><strong>Account:</strong> ".$accountname[0]."</span> <a href='http://www.ktools.net/members/' target='_blank' class='actionlink'>Login</a><br />";
								//echo "<div class='mtag_dblue' style='font-size: 18px; float: left;'>".$supportremaining[0]."</div> Days of support & updates remaining <a href='http://www.ktools.net/cart.php?add=62' target='_blank' class='actionlink'>Extend</a><br />";
								//echo "<br style='clear: both;' />";
								//echo "<div class='mtag_dblue' style='font-size: 18px; float: left;'>".$messages[0]."</div> Unread messages <a href='http://www.ktools.net/members/' target='_blank' class='actionlink'>View</a><br />";
								//echo "<div class='mtag_dblue' style='font-size: 18px; float: left;'>".$affiliatesales[0]."</div> New affiliate sales since last account login <a href='http://www.ktools.net/members/' target='_blank' class='actionlink'>View</a><br />";
								//echo "</p>";
								
								//$support = $supportremaining[1];
								//$supportleft = $supportremaining[1]+1;
								echo "<div style=''>";
									if($supportremaining[1] > 30)
									{
										echo "<div class='wp_account_grey' style='float: left; width: 33%; padding: 0px; color: #FFF'><p style='font-size: 24px; margin: 0; padding: 0'>".$supportremaining[0]."</p><p class='wp_account_grey_footer' style='background-color: #d1d0d0;'>$wplang[kaccount_support]</p></div>";
									}
									else
									{
										echo "<div class='wp_account_grey' style='float: left; width: 33%; padding: 0px; background-color: #bb5f5f; color: #FFF'><p style='font-size: 24px; margin: 0; padding: 0'>".$supportremaining[0]."</p><p class='wp_account_grey_footer' style='background-color: #a04949;'>$wplang[kaccount_support]</p></div>";
									}
									
									//print_r($supportremaining);exit;
									//echo  "left: ". gettype($supportleft) . $supportleft;
									
									echo "<div class='wp_account_grey' style='float: left; width: 33%; padding: 0px; margin-left: 0; background-color: #d1d0d0'><p style='font-size: 24px; margin: 0; padding: 0'>".$messages[0]."</p><p class='wp_account_grey_footer' style='background-color: #bdbdbd;'>$wplang[kaccount_messages]</p></div>";
									
									echo "<div class='wp_account_grey' style='float: left; width: 34%; padding: 0px; margin-left: 0;'><p style='font-size: 24px; margin: 0; padding: 0'>".$affiliatesales[0]."</p><p class='wp_account_grey_footer'>$wplang[kaccount_affil]</p></div>";
								echo "</div>";
							}
							else 
							{
								echo "Error: $status[1]";	
							}
							
							
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
	}	
?>