<?php
	###################################################################
	####	MANAGER UTILITIES PAGE                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "utilities";
		$lnav = "settings";
		
		$supportPageID = '388';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
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
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON		
		
		// For clearing smarty cache - clear_all_cache()
		
		switch($_REQUEST['pmode']){
			default:
			break;
			case "clearCache":
				/*
				$dh = opendir ('../assets/cache/');
				while (false !== $file = readdir ($dh))
				{
					if(is_file('../assets/cache/' . $file) and ($file != '.' and $file != '..'))
					{
						if($file != 'index.html')						
							@unlink('../assets/cache/' . $file);
					}
				}
				closedir ($dh);
				*/
				clearCache(); // Clear the image cache
				
				clearSmartyCache(); // Clear smarty cache
				
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_f_cache_b']."</strong>");
				$vmessage = $mgrlang['util_mes_06'];
			break;
			case "settings_backup":
				DuplicateSettings($dbinfo[pre].'settings', 'settings_id', '1', $_POST['restore_name']);
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_f_budb_b']."</strong>");
				$vmessage = $mgrlang['util_mes_02'];
			break;
			case "settings_restore":
				RestoreMySQLRecord($dbinfo[pre].'settings', 'settings_id', $_POST['restore_id']);
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_f_restore']."</strong>");
				$vmessage = $mgrlang['util_mes_03'];
			break;
			case "run_cleanup":
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_f_cleanup_b']."</strong>");
				$vmessage = $mgrlang['util_mes_04'];
			break;
			case "run_backup":
				$backup_inc = 1;
				$backupmode = "backup";
				include('mgr.sql.backup.php');		
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}settings SET last_backup='" . gmt_date() . "' where settings_id  = '1' LIMIT 1";
				$result = mysqli_query($db,$sql);				
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['gen_dbbackup2']."</strong>");
				$vmessage = $mgrlang['util_mes_05'];
			break;
			case "purge_logs":
				$purge_date = $_POST['purge_year'] . "-" . $_POST['purge_month'] . "-" . $_POST['purge_day'] . " 01:01:01";
				$adjusted = explode(".",($config['settings']['time_zone']*-1));				
				//$adj_hours = ($this->daylight_savings and gmdate("I")) ? $adjusted[0]+1  : $adjusted[0]; // WITH AUTOMATIC DAYLIGHT SAVINGS DETECTION
				$adj_hours = ($config['settings']['time_zone']) ? $adjusted[0]-1  : $adjusted[0];
				$adj_minutes = ($adjusted[1] == "5") ? "30" : "0";
				$purge_date = date("Y-m-d H:i:s",strtotime("$purge_date +$adj_hours hours $adj_minutes minutes"));
				//echo $purge_date; exit;
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}activity_log WHERE log_time < '$purge_date'");
				
				# UPDATE ACTIVITY LOG
				$acdate = new kdate;
            	$acdate->distime = 0;
				$acdate->adjust_date = 0;
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['admin_tab3'],1,$mgrlang['setup_f_purge_ac'] . " (*) > <strong>$mgrlang[gen_b_purge_al] ".$acdate->showdate($_POST['purge_year'] . "-" . convert_to_2digit($_POST['purge_month']) . "-" . convert_to_2digit($_POST['purge_day']))."</strong>");

				$vmessage = $mgrlang['util_mes_01'];
			break;
			case "sql":
			if($_SESSION['admin_user']['admin_id'] != "DEMO"){
				$sql = stripslashes($_POST['sql']);
				//EXECUTE SQL AND TRY NOT TO OVERLOAD SERVER.
				if(mysqli_query($db,$sql)){
					$process = "executed";
				} else {
					$process = "failed";
				}
				if($process == "executed"){
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_f_sql']." > ".$sql."</strong>");
					$vmessage = $mgrlang['util_mes_07'];
				} else {
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_utilities'],1,"<strong>".$mgrlang['util_mes_08']." > ".$sql."</strong>");
					$vmessage = $mgrlang['util_mes_08'];
				}
			}
			break;
		}
		
		//if($_GET['ep'] = '1'){
		//	$vmessage = "This area is for advanced users. Please be careful when making any changes.";
		//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_utilities']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
    <link rel="stylesheet" media="print" type="text/css" href="mgr.style.print.css">
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
	<!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<script type="text/javascript" language="javascript">
		// CREATE THE PRINT WINDOW AND INVOKE PRINTING
		function prep_printing(){
			var print_details = new Object();
			print_details.updatecontent = 'print_window_inner';
			print_details.loadpath = 'mgr.activity.log.php';
			var manager = $('manager').options[$('manager').selectedIndex].value;
			print_details.pars = 'displaymode=print&print_all=1&manager=' + manager + get_from_date() + get_to_date();
			do_printing(print_details);
		}
		
		// CREATE THE PRINT WINDOW AND INVOKE PRINTING
		function check_al_for_records(){
			$('print_al').disable();
			$('download_al').disable();
			var url = 'mgr.activity.log.php';
			var manager = $('manager').options[$('manager').selectedIndex].value;
			//alert(al_for); return false;
			var pars = 'displaymode=checkrecords&print_all=1&manager=' + manager + get_from_date() + get_to_date();
			var myAjax = new Ajax.Request( 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true,
					onSuccess: function(transport){
									var response = transport.responseText;
									if(response == 'yes'){
										$('print_al').enable();
										$('download_al').enable();
									} else {
										$('print_al').disable();
										$('download_al').disable();
									}
								}
				});		
		}
		
		Event.observe(window, 'load', function()
		{
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
			check_al_for_records();
		});
		
		// DOWNLOAD CSV
		function download_csv(){
			var manager = $('manager').options[$('manager').selectedIndex].value;
			location.href='mgr.activity.log.php'+'?displaymode=download&manager=' + manager + '&print_all=1&start=0' + get_to_date() + get_from_date();
		}
		
		// Test the email settings
		function testEmailWorkbox()
		{
			workbox2({'page':'mgr.workbox.php?box=test_email'});
		}
    </script>
</head>
<body>
	<?php include("mgr.print.window.php"); ?>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        <div id="content_container">
            <?php
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
            ?>
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.utilities.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_utilities']; ?></strong><br /><span><?php echo $mgrlang['subnav_utilities_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">	
                <!--<div id="spacer_bar"></div>-->
				<?php $row_color = 0; ?>
                <div id="tab1_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_ugmc']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_ugmc_d']; ?></span>
                        </p>
                        <?php
							if($_SESSION['admin_user']['admin_id'] == "DEMO"){
								echo "<input type='button' value='" . $mgrlang['gen_b_update'] . "' class='small_button' onclick='demo_message();' style='margin-top: 4px; width: 310px;' />";
							} else {
								echo "<input type='button' value='" . $mgrlang['gen_b_update'] . "' class='small_button' onclick=\"workbox2({page: 'mgr.workbox.php?box=menuBuilder'});\" style='margin-top: 4px; width: 310px;' />";
							}
						?>
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_cache_b']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_cache_d']; ?></span>
                        </p>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=clearCache" method="post">
                        	<?php
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){
									echo "<input type='button' value='" . $mgrlang['util_f_cache_b'] . "' class='small_button' onclick='demo_message();' style='margin-top: 3px; width: 310px;' />";
								} else {
                            		echo "<input type='submit' value='" . $mgrlang['util_f_cache_b'] . "' class='small_button' style='margin-top: 3px; width: 310px;' />";
								}
							?>
                        </form>
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_srp']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_srp_d']; ?></span>
                        </p>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=settings_backup" method="post">
                        	<?php echo $mgrlang['util_f_srp_o']; ?>:<br /><input type="text" name="restore_name" style="margin-top: 3px; width: 298px;" /><br />
                            <?php
                            	if($_SESSION['admin_user']['admin_id'] == "DEMO"){
									echo "<input type='button' value='" . $mgrlang['util_f_srp_b'] . "' class='small_button' onclick='demo_message();' style='margin-top: 4px; width: 310px;' />";
								} else {
									echo "<input type='submit' value='" . $mgrlang['util_f_srp_b'] . "' class='small_button' style='margin-top: 4px; width: 310px;' />";
								}
							?>
						</form>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_restore']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_restore_d']; ?></span>
                        </p>
                        <div style="padding-top: 5px;">
                            <?php
								$ndate = new kdate;
								$ndate->distime = 1;
								@$settingbackups_result = mysqli_query($db,"SELECT rec_backup,rec_name,settings_id FROM {$dbinfo[pre]}settings WHERE settings_id != '1' AND rec_version >= '$config[productVersion]' AND rec_prod='$config[productCode]' ORDER BY rec_backup DESC");
								@$settingbackups_rows = mysqli_num_rows($settingbackups_result);
								
								if($settingbackups_rows){
							?>
                                <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=settings_restore" method="post">
                                    <select style="width: 310px;" name="restore_id">
                                        <?php
                                            while($settingbackups = mysqli_fetch_object($settingbackups_result)){   
                                        ?>
                                        <option value="<?php echo $settingbackups->settings_id; ?>"><?php echo $ndate->showdate($settingbackups->rec_backup); ?>&nbsp;&nbsp;<?php if($settingbackups->rec_name){ echo "($settingbackups->rec_name)"; } ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select><br />
                                    <?php
                                    	if($_SESSION['admin_user']['admin_id'] == "DEMO"){
											echo "<input type='button' value='" . $mgrlang['util_f_restore_b'] . "' class='small_button' onclick='demo_message();' style='margin-top: 4px; width: 310px;' />";
										} else {
		                                    echo "<input type='submit' value='" . $mgrlang['util_f_restore_b'] . "' class='small_button' style='width: 310px; margin-top: 4px;' />";
										}
									?>
                                </form>
                            <?php
								} else {
									echo "<div style='color: #c3082c; font-weight: bold; padding-top: 14px;'>There are no points to restore to!</div>";
								}
							?>
                    	</div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_cleanup']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_cleanup_d']; ?></span>
                        </p>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=run_cleanup" method="post">
                        	<?php
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){
									echo "<input type='button' value='" . $mgrlang['util_f_cleanup_b'] . "' class='small_button' onclick='demo_message();' style='margin-top: 10px; width: 310px;' />";
								} else {
                            		echo "<input type='submit' value='" . $mgrlang['util_f_cleanup_b'] . "' class='small_button' style='margin-top: 10px; width: 310px;' />";
								}
							?>
                        </form>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_pal']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_pal_d']; ?></span>
                        </p>
						<div style="float: left">
                        <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=purge_logs" method="post">
                            <select style="width: 80px;" name="purge_month">
                                <?php
                                    for($i=1; $i<13; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($i == date("m")){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 80px;" name="purge_day">
                                <?php
                                    for($i=1; $i<=31; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($i == date("d")){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 144px;" name="purge_year">
                                <?php
                                    for($i=2005; $i<(date("Y")+6); $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($i == date("Y")){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select><br />
                            <?php
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){
									echo "<input type='button' value='" . $mgrlang['util_f_pal_b'] . "' class='small_button' onclick='demo_message();' style='margin-top: 4px; width: 310px;' />";
								} else {
                            		echo "<input type='submit' value='" . $mgrlang['util_f_pal_b'] . "' class='small_button' style='width: 310px; margin-top: 4px;' />";
								}
							?>
                    	</form>
						</div>
                    </div>
                    
					<div class="<?php fs_row_color(); ?>" id="smtp1"> <?php /* if($config['settings']['mailproc'] == 1){ echo "style='display: none;'"; }*/ ?>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_test_email']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_test_email_d']; ?></span>
                        </p>
						<?php
							if($_SESSION['admin_user']['admin_id'] == "DEMO"){
								echo "<input type='button' value='" . $mgrlang['setup_f_test_email'] . "' class='small_button' onclick='demo_message();' style='width: 310px; margin-top: 12px;' />";
							} else {
								echo "<input type='submit' value='" . $mgrlang['setup_f_test_email'] . "' class='small_button' onclick='testEmailWorkbox();' style='width: 310px; margin-top: 12px;' />";
							}
						?>
                    </div>
					
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_budb']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_budb_d']; ?></span>
                        </p>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>?pmode=run_backup" method="post">
                        	<?php
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){
									echo "<input type='button' value='" . $mgrlang['util_f_budb_b'] . "' class='small_button' onclick='demo_message();' style='width: 310px; margin-top: 12px;' />";
								} else {
                            		echo "<input type='submit' value='" . $mgrlang['util_f_budb_b'] . "' class='small_button' style='width: 310px; margin-top: 12px;' />";
								}
							?>                            
                        </form>
                    </div>
                    
                    <?php
						if(in_array("software_setup",$_SESSION['admin_user']['permissions'])){
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['util_f_sal']; ?>:<br />
                                <span><?php echo $mgrlang['util_f_sal_d']; ?></span>
                            </p>
                            <form action="mgr.software.setup.php?ep=1&al=1" method="post">
                                <?php
									if($_SESSION['admin_user']['admin_id'] == "DEMO"){
										echo "<input type='button' value='" . $mgrlang['util_f_sal_b'] . "' class='small_button' onclick='demo_message();' style='width: 310px; margin-top: 10px;' />";
									} else {
										echo "<input type='submit' value='" . $mgrlang['util_f_sal_b'] . "' class='small_button' style='margin-top: 10px; width: 310px;' />";
									}
								?>
                            </form>
                        </div>
                    <?php
                     	}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_pdac']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_pdac_d']; ?></span>
                        </p>
                        <div style="float: left; font-weight: bold; color: #333333">
                            <div style="clear: both;">
                                <p style="float: left; width: 40px; margin: 0; padding: 6px 0 0 0;"><?php echo $mgrlang['util_f_pdac_for']; ?>:</p>
                                <select style="width: 268px;" name="manager" id="manager" onchange='check_al_for_records();'>
                                    <option value="1"><?php echo $mgrlang['util_f_pdac_admins']; ?></option>
                                    <option value="0"><?php echo $mgrlang['util_f_pdac_mems']; ?></option>
                                </select>
                            </div>
                            <div style="clear: both; margin-top: 8px;">
                                <p style="clear: both; float: left; width: 40px; margin: 0; padding: 6px 0 0 0;"><?php echo $mgrlang['util_f_pdac_from']; ?>:</p>
                                <select style="width: 70px;" name="from_month" id="from_month" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=1; $i<13; $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == 1){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                                <select style="width: 70px;" name="from_day" id="from_day" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=1; $i<=31; $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == 1){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                                <select style="width: 122px;" name="from_year" id="from_year" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=2008; $i<=(date("Y")); $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == 2008){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div style="clear: both; margin-top: 8px;">
                                <p style="clear: both; float: left; width: 40px; margin: 0; padding: 6px 0 0 0;"><?php echo $mgrlang['util_f_pdac_to']; ?>:</p>
                                <select style="width: 70px;" name="to_month" id="to_month" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=1; $i<13; $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == date("m")){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                                <select style="width: 70px;" name="to_day" id="to_day" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=1; $i<=31; $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == date("d")){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                                <select style="width: 122px;" name="to_year" id="to_year" onchange='check_al_for_records();'>
                                    <?php
                                        for($i=2008; $i<=(date("Y")); $i++){
                                            if(strlen($i) < 2){
                                                $dis_i_as = "0$i";
                                            } else {
                                                $dis_i_as = $i;
                                            }
                                            echo "<option value='$i' ";
                                            if($i == date("Y")){
                                                echo "selected";
                                            }
                                            echo ">$dis_i_as</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div style="clear: both; margin-top: 8px;">
                           		<input type='button' value='<?php echo $mgrlang['gen_b_print']; ?>' class='small_button' style="margin-top: 3px; width: 153px;" id='print_al' onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "prep_printing();"; } ?>" />
                            	<input type='button' value='<?php echo $mgrlang['gen_b_download']; ?>' class='small_button' style="margin-top: 3px; width: 153px;" id='download_al' onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "download_csv();"; } ?>" />
                            </div>
                        </div>
					</div>
												<div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['util_f_sql']; ?>:<br />
                                <span><?php echo $mgrlang['util_f_sql_d']; ?><br><br><br></span>
                            </p>
                            <form action="mgr.utilities.php?pmode=sql" method="post">
                            <textarea name="sql" cols="53" rows="4"></textarea><br>
                            <?php
														if($_SESSION['admin_user']['admin_id'] == "DEMO"){
															echo "<input type='button' value='" . $mgrlang['util_f_exec'] . "' class='small_button' onclick='demo_message();' style='width: 310px; margin-top: 10px;' />";
														} else {
															echo "<input type='submit' value='" . $mgrlang['util_f_exec'] . "' class='small_button' style='margin-top: 10px; width: 310px;' />";
														}
														?>
                            </form>
                        </div>
					<?php
						/*
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <label for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['webset_f_news-']; ?>Gallery Delete:<br />
                            <span><?php echo $mgrlang['webset_f_news_d-']; ?>Force a gallery to delete if it remains after deleting it from the galleries area.</span>
                        </label>
                        list<br />
                        <input type='button' value='Delete' class='small_button' style="margin-top: 10px; width: 210px;" />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <label for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['webset_f_news-']; ?>Update Directory Permission:<br />
                            <span><?php echo $mgrlang['webset_f_news_d-']; ?>If you have done a site move or.....</span>
                        </label>
                        <input type='button' value='Update' class='small_button' style="margin-top: 10px; width: 210px;" />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <label for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['webset_f_news-']; ?>Update Storage Path:<br />
                            <span><?php echo $mgrlang['webset_f_news_d-']; ?>If you have done a site move or.....</span>
                        </label>
                        <input type='button' value='Update' class='small_button' style="margin-top: 10px; width: 210px;" />
                    </div>
						*/
					?>
                </div>
            </div>
            <!-- END CONTENT -->
        </div>
        <div class="footer_spacer"></div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>			
	</div>
		
</body>
</html>
<?php mysqli_close($db); ?>
