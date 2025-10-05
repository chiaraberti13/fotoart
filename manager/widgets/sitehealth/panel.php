<?php
	###################################################################
	####	WP: EXTRAS : VERSION 1.0                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   #####
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
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['sitehealth_title'];
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
				.wp_sitehealth_table{
					font-size: 12px;
				}
				.wp_sitehealth_table tr td{
				}
				.wp_sitehealth_title{
					font-weight: bold;
					width: 70%;
					padding-left: 10px;
					color: #666
				}
				.wp_sitehealth_title img{
					float: left;
					margin-right: 6px;
					border: 0;
					cursor: pointer;
				}
				.wp_sitehealth_rowa{
					background-color: #FFF;	
				}
				.wp_sitehealth_rowb{
					background-color: #EEE;	
				}
				.wp_sitehealth_column2{
					text-align: center;
					font-weight: normal;
					width: 15%;
				}
				.wp_sitehealth_column3{
					text-align: center;
					font-weight: bold;
					width: 15%;
					font-size: 12px;
					color: #FFF;
					border-bottom: 1px solid #EEE;
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
			# INCLUDE VERSION.PHP FILE
			$inc = 1;
			require_once('../../../assets/includes/version.php');
			
			$supportPageID = '394';
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>$wplang[load_failed]</div>"; exit;
			}

			@$reguptime = trim (exec ('uptime'));
			if ($reguptime)
			{
			if (preg_match ('/, *(\\d) (users?), .*: (.*), (.*), (.*)/', $reguptime, $uptime))
			{
			  $users[0] = $uptime[1];
			  $users[1] = $uptime[2];
			  $loadnow = $uptime[3];
			  $load = 1;
			  $load15 = $uptime[4];
			  $load30 = $uptime[5];
			}
			}
			else
			{
				$users[0] = $wplang['sitehealth_unava'];
				$users[1] = '--';
				$loadnow = $wplang['sitehealth_unava'];
				$load = 0;
				$load15 = '--';
				$load30 = '--';
			}
			
			@$uptime = shell_exec ('cut -d. -f1 /proc/uptime');
			$days = floor ($uptime / 60 / 60 / 24);
			$hours = str_pad ($uptime / 60 / 60 % 24, 2, '0', STR_PAD_LEFT);
			$mins = str_pad ($uptime / 60 % 60, 2, '0', STR_PAD_LEFT);
			$secs = str_pad ($uptime % 60, 2, '0', STR_PAD_LEFT);
			//$phpver = phpversion ();
			//$mysqlver = mysql_get_client_info ();
			//$zendver = zend_version ();
			//echo '' . '<load>Server Load: ' . $loadnow . '</load><br>
			//';
			//echo '' . '<uptime>Uptime: ' . $days . ' Days ' . $hours . ':' . $mins . ':' . $secs . '</uptime><br>
			//';
			/*
			function find_SQL_Version() { 
			   //$output = shell_exec('mysql -V'); 
			   $output = mysql_get_client_info();
			   preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
			   return $version[0]; 
			} 
			*/
			
			# HERE YOU CAN ALSO CHECK TO SEE IF THE ADD-ON IS INSTALLED			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $wplang['sitehealth_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>
			<div style="height: 150px; overflow: auto;">
            <table width="100%" cellspacing="0" cellpadding="6" class="wp_sitehealth_table">
            	<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_dbv']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo $config['settings']['db_version']." / ".$config['productVersion']; ?></td>
                    <td class="wp_sitehealth_column3" <?php if($config['settings']['db_version'] == $config['productVersion']){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_php']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo phpversion(); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(phpversion() >= 5.0){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_mysqlv']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo find_SQL_Version(); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(find_SQL_Version() >= 4.0){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_load']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo $loadnow; ?></td>
                    <td class="wp_sitehealth_column3" <?php if($load){ if($loadnow < 1){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_high']; } } else { echo "style='background-color: #75b1db'>?"; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_upti']; ?></td>
                    <td class="wp_sitehealth_column2"><?php if($days){ echo $days . $mgrlang['gen_days']; } else { echo $wplang['sitehealth_unava']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if($days){ if($days < 599){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_high']; } } else { echo "style='background-color: #75b1db'>?"; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_gd']; ?></td>
                    <td class="wp_sitehealth_column2"><?php if(function_exists('imagecreatetruecolor')){ echo $wplang['sitehealth_inst']; } else { echo $wplang['sitehealth_none']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(function_exists('imagecreatetruecolor')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<?php $memoryLimit = str_replace('M','',ini_get("memory_limit")); ?>
                <tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_mem']; ?></td>
                    <td class="wp_sitehealth_column2"><?php if(!ini_get("memory_limit")){ echo $wplang['sitehealth_none']; } else { echo ini_get("memory_limit"); }; ?></td>
                    <td class="wp_sitehealth_column3" <?php if($memoryLimit > "63"){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #eebc51'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_exe']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo ini_get("max_execution_time"); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(ini_get("max_execution_time") > 29){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_time']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo ini_get("max_input_time"); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(ini_get("max_input_time") > 89 or ini_get("max_input_time") == "-1"){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #eebc51'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_file']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo ini_get("upload_max_filesize"); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(ini_get("upload_max_filesize") > 10){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #eebc51'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_post']; ?></td>
                    <td class="wp_sitehealth_column2"><?php echo ini_get("post_max_size"); ?></td>
                    <td class="wp_sitehealth_column3" <?php if(ini_get("post_max_size") > 11){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #eebc51'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_exif']; ?></td>
                    <td class="wp_sitehealth_column2"><?php if(function_exists('exif_read_data')){ echo $wplang['sitehealth_inst']; } else { echo $wplang['sitehealth_unava']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(function_exists('exif_read_data')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo $wplang['sitehealth_safe']; ?></td>
                    <td class="wp_sitehealth_column2"><?php if(!ini_get("safe_mode")){ echo $wplang['sitehealth_off']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(!ini_get("safe_mode")){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #eebc51'>".$wplang['sitehealth_low']; } ?></td>
                </tr>
                <tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/addons/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/addons/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/addons/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/avatars/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/avatars/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/avatars/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/backups/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/backups/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/backups/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/cache/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/cache/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/cache/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/contributors/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/contributors/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/contributors/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/incoming/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/incoming/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/incoming/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/item_photos/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/item_photos/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/item_photos/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/library/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/library/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/library/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowb">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/files/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/files/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/files/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
				<tr class="wp_sitehealth_rowa">
                	<td class="wp_sitehealth_title"><img src="./images/mgr.icon.questionmark.png" onclick="support_popup('<?php echo $supportPageID; ?>');" /><?php echo realpath('../../../assets/tmp/'); ?></td>
                    <td class="wp_sitehealth_column2"><?php if(is_writable('../../../assets/tmp/')){ echo $wplang['sitehealth_write']; } else { echo $wplang['sitehealth_nonwri']; } ?></td>
                    <td class="wp_sitehealth_column3" <?php if(is_writable('../../../assets/tmp/')){ echo "style='background-color: #75b1db'>".$wplang['sitehealth_ok']; } else { echo "style='background-color: #bb5f5f'>".$wplang['sitehealth_failed']; } ?></td>
                </tr>
            </table>
            </div>
            <!--
            	assets/includes/version.php and db version comparison<br />
                database cleanup (4.1)
            -->
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