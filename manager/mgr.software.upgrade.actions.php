<?php
	###################################################################
	####	SOFTWARE UPGRADE ACTIONS                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-24-2008                                     ####
	####	Modified: 9-24-2008                                    #### 
	###################################################################
	
	//sleep(3);
	
	# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');
	
		$page = "administrators";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php');
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
		
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE THE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE	
		include_lang();
		
		# CURRENT INSTALLED INFO
		$current_version = $config['productVersion'];
		$current_code = $config['productCode'];
		
		# CHECK TO SEE IF AN UPGRADE IS WAITING						
		if(file_exists("../assets/upgrades/assets/includes/version.php")){
			$inc = 1;
			@include("../assets/upgrades/assets/includes/version.php");
			# UPDATE VERSION AND CODE
			$upgrade_version = $config['productVersion'];
			$upgrade_code = $config['productCode'];
			# REINCLUDE THE ORIGINAL VERSION.PHP
			@include("../assets/includes/version.php");
			# MAKE SURE WHAT IS IN THE FOLDER IS THE CORRECT SOFTWARE
			if($upgrade_code != $current_code){
				$upgrade_version = '0';
				$upgrade_error = '1';
			}
		} else {
			$upgrade_version = '0';
		}
		
		switch($_GET['pmode']){
			default:
				
				# CURRENT VERSION AVAILABLE
				if(ini_get("allow_url_fopen")){
					$newest_version = getRemoteFile('www.ktools.net', 'GET', '/webmgr/assets/includes/version.php', 'product_code='.$config['productCode']);
					if(!$newest_version){
						$newest_version = '0';
					}				
				} else {
					$newest_version = '0';
				}
				
				//$newest_version=0;
				
				echo "Version Currentlly Installed: <strong>$current_version</strong><br /><br />";
				
				if($newest_version == 0){
					//echo "Unable to determine the newest version. Please check the Ktools.net website.<br />";
					echo "Newest Version Available: <strong>Unknown</strong> (Please check the ktools.net website for the newest version)<br /><br />";
				}
				
				if($newest_version > $current_version){
					echo "Newest Version Available: <strong>$newest_version</strong> &nbsp; ";
					if($newest_version > $upgrade_version){
						echo "<input type='button' value='Download Newest Version' class='small_button' onclick=\"";
						if($_SESSION['admin_user']['admin_id'] == "DEMO"){
							echo "demo_message();";
						} else {
							echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=get_newest');";
						}
						echo "\" /> ";	
					}
					echo "<br /><br />";
				}
				
				//if($newest_version != '0' && $newest_version <= $current_version){
				//	echo "You have the most current version.<br /><br />";
				//}
				
				if($upgrade_error){
					echo "<span style='color: #cc0000'>The queued upgrade in /assets/upgrades is not for this software. It is recommended you remove it to prevent harm to your current installation.</span> <input type='button' value='Remove Files' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade');";
					}
					echo "\" /><br />";
				}
				
				if($upgrade_version < $current_version && !$upgrade_error && $upgrade_version != '0'){
					echo "<span style='color: #cc0000'>The queued upgrade in /assets/upgrades is for an earlier version than the one currently installed. It is recommended that you remove it.</span> <input type='button' value='Remove Files' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade');";
					}
					echo "\" /><br />";
				}
				
				if($upgrade_version == $current_version){
					echo "<span style='color: #cc0000'>Version Queued For Installation: <strong>$upgrade_version</strong> <em>(This version is already installed)</em></span> &nbsp; <input type='button' value='Remove Files' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade');";
					}
					echo "\" /> <input type='button' value='Run This Upgrade Anyway' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=start_upgrade');";
					}
					echo "\" /> ";
					//if($newest_version > $upgrade_version){
					//	echo "<input type='button' value='Download Newest Version' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=get_newest');\" /> ";
					//}
					echo "<br />";
				}
				
				if($upgrade_version > $current_version){					
					echo "<span style='color: #cc0000'>Upgrade Queued For Installation: <strong>$upgrade_version</strong></span> &nbsp; ";
					echo "<input type='button' value='Upgrade Now' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=start_upgrade');";
					}
					echo "\" /> ";
					//if($newest_version > $upgrade_version){
					//	echo "<input type='button' value='Download Newest Version' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=get_newest');\" /> ";
					//}
					echo "<input type='button' value='Remove Files' class='small_button' onclick=\"";
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
						echo "load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade');";
					}
					echo "\" /><br />";
				}
				
				/*
					if($current_version < $upgrade_version){
						echo "<img src='images/mgr.notice.icon.white.gif' align='absmiddle' /> You have version <strong>$upgrade_version</strong> in place and ready to be installed. Install this version now or check for a newer version.";
					} else {
						echo "You have an older version in your assets/upgrades folder. Can we delete this?";
					}
				}
				*/
			break;
			case "start_upgrade":
				echo "<strong>Upgrade to version $upgrade_version:</strong><br /><br />";
				echo "Before upgrading you must agree to the following terms...<br /><span style='font-size: 11px; color: #7a7a7a'>This is where the upgrading terms will go once I write them up. This is where the upgrading terms will go once I write them up. This is where the upgrading terms will go once I write them up. This is where the upgrading terms will go once I write them up. This is where the upgrading terms will go once I write them up. This is where the upgrading terms will go once I write them up.</span><br /><br />";
				echo "<input type='checkbox' value='1' id='agree_checkbox' onclick='enable_start();' /> <label for='agree_checkbox'>I agree to the upgrade terms</label><br />"; 
				echo "<p align='right' style='margin: 0; padding: 0;'><input type='button' value='Cancel' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','');\" /> <input type='button' value='Start Upgrade' id='start_button' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=upgrade_step1');\" disabled /></p>";
			break;	
			case "upgrade_step1":
				
				# IF MANUAL
				//echo "The database has been updated. The following files have changed and will need to be replaced. To complete the upgrade replace each of the files in the list below with the new ones from the zip file. When finished click done and you will be taken back to the upgrade page. ";
				
				// PUT ALL OF THE FILES INTO A JAVASCRIPT ARRAY
				
				# MAKE SURE THAT THE UPGRADE.PHP FILE EXISTS
				if(file_exists('../assets/upgrades/assets/includes/upgrade.php')){
					# BACKUP THE DATABASE FIRST					
					if($config['BU_Database']){
						if(is_writable("../assets/backups")){
							$backup_inc = 1;
							$backupmode = "backup";
							include('mgr.sql.backup.php');
							# UPDATE THE DATABASE
							$upsql = "UPDATE {$dbinfo[pre]}settings SET last_backup='" . gmt_date() . "' where settings_id  = '1' LIMIT 1";
							$upresult = mysqli_query($db,$upsql);
							echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Your database has been backed up successfully.</span><br /><br />";
						} else {
							$upgrade_error[] = "Your database was not backed up before the upgrade.";
						}
					}					
					
					# INCLUDE AND EXECUTE THE UPGRADE.PHP FILE
					include('../assets/upgrades/assets/includes/upgrade.php');
					
					# MAKE SURE THERE WERE NO DATABASE ERRORS
					if(!$db_upgrade_error){
						echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Your database has been updated successfully.</span><br /><br />";
					} else {
						$upgrade_error[] = "There was a problem upgrading your database. The upgrade should not continue until this is resolved.";
					}
					
					//copy("../assets/templates/default/config.php","../assets/backups/config.php");
					
					# BACKUP TEMPLATES
					if($config['BU_Templates']){
						if(is_writable("../assets/backups")){
							$backup_dir_name = "templates_bu-" . date("Y_m_d_Hi");
							umask(0);					
							if(!@mkdir("../assets/backups/" . $backup_dir_name,$config['SetFilePermissions'])){
								$upgrade_error[] = "";
							}						
							# BACKUP THE TEMPLATES
							if(copy_all_files('../assets/templates/',"../assets/backups/" . $backup_dir_name)){
								echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Your templates have been backed up to: /assets/backups/$backup_dir_name</span><br /><br />";
							} else {
								$upgrade_error[] = "Your template files could not be backed up. You should back this up manually before continuing.";
							}							
						} else {
							$upgrade_error[] = "The backups directory is not writable. We can't backup your templates automatically. You may want to do this manually before you continue the upgrade.";
						}
					}
					
					if($config['BU_Languages']){
						# BACKUP LANGUAGE FILES
						if(is_writable("../assets/backups")){
							$backup_dir_name = "languages_bu-" . date("Y_m_d_Hi");
							umask(0);					
							if(!@mkdir("../assets/backups/" . $backup_dir_name,$config['SetFilePermissions'])){
								$upgrade_error[] = "";
							}						
							# BACKUP THE TEMPLATES
							if(copy_all_files('../assets/languages/',"../assets/backups/" . $backup_dir_name)){
								echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Your language files have been backed up to: /assets/backups/$backup_dir_name</span><br /><br />";
							} else {
								$upgrade_error[] = "Your language files could not be backed up. You should back these up manually before continuing.";
							}							
						} else {
							$upgrade_error[] = "The backups directory is not writable. We can't backup your language files automatically. You may want to do this manually before you continue the upgrade.";
						}
					}
					
					# OUTPUT ANY ERRORS
					if($upgrade_error){
						echo "<img src='images/mgr.notice.icon.white.small.png' align='absmiddle' style='margin-right: 5px;' /><span style='color: #cc0000; font-weight: bold;'>The following errors occured during the upgrade process and should be addressed before continuing.</span><br />";
						echo "<div style='padding-left: 25px;'>";
						foreach($upgrade_error as $value){
							echo "<span style='color: #cc0000;'>- $value</span><br />";
						}
						echo "</div><br /><br />";
					}
					
					echo "The following files have been changed in the new version and will need to be replaced. You can do this manually or click replace files below to attempt it automatically. Automatic replacement will work on most systems. Use the 'Replaced' checkboxes to keep your spot when manually replacing files.";
				
			?>
            		
                    <script type="text/javascript" language="javascript">
						current_file_number=1;
						replacement_files = Array();
						replacement_files[0] = "";
						<?php
							if($modfiles){
                            	$x=1;
								foreach($modfiles as $value){
									echo "replacement_files[$x] = '$value';";
									$x++;
								}
							}
						?>
					</script>
                    <br /><br />
                    <div style="border: 1px solid #8b8b8b; overflow: auto;">
                        <div id="hidden_window" style="display: block;"></div>
                        <div id="current_file_window" style="border-bottom: 1px solid #CCCCCC; padding: 3px 5px 3px 5px; display: none; background-color: #e2eaf2; font-weight: 11px;">&nbsp;</div>
                        <div style="height: 100px; overflow: auto; padding: 5px; background-color: #ffffff;" id="file_window">
                        <table style='color: #5d5d5d'>
                            <tr>
                                <td nowrap><span style="font-weight: bold; color: #333333">New File (From Zip Package)</span></td>
                                <td width="100" align="center"><span style="font-weight: bold; color: #333333">&raquo;</span></td>
                                <td nowrap><span style="font-weight: bold; color: #333333">Old File (Replace On Your Site)</span></td>
                                <td width="100" align="center"><span style="font-weight: bold; color: #333333">&raquo;</span></td>
                                <td align='center'><span style="font-weight: bold; color: #333333">Replaced</span></td>
                                <?php
                                    if($modfiles){
                                        foreach($modfiles as $value){
                                            //echo "<tr><td colspan='5' height='1' style='background-color: #eeeeee;'></td></tr>";
											echo "<tr><td nowrap>$value</td>";
											echo "<td width='100' align='center'>&raquo;</td>";
                                            echo "<td nowrap>" . str_replace("manager/",$config['manager_dir_name'],$value) . "</td>";
                                            echo "<td width='100' align='center'>&raquo;</td>";
											echo "<td align='center'><input type='checkbox' /></td>";
											echo "</tr>";
                                        }
                                    }
                                ?>
                        </table>
                        </div>
                        <div id="status_bar" style="display: none; padding: 4px 4px 2px 4px; background-color: #868686; border-bottom: 1px solid #666666; border-top: 1px solid #666666; height: 16px; color: #ffffff;">
                            <div id="files_processed" style="float: left;"></div>
                            <div style="float: left; width: 20px;" align="center"><img src="images/mgr.isb.div.gif" /></div>
                            <div id="time_calc" style="float: left;"></div>
                            <div style="float: left; width: 20px;" align="center"><img src="images/mgr.isb.div.gif" /></div>
                            <div style="width: 150px; border: 1px solid #5a5a5a; background-color: #5a5a5a; height: 10px; float: left;"><div id="progress_bar" style="width: 0%; height: 10px; background-image: url(images/mgr.loader3.gif); background-repeat: repeat-x"></div></div>
                            <div id="show_perc" style="float: left; padding: 0px 4px 0px 4px;"></div>
                        </div>
                    </div>                 
            <?php
					echo "<p align='left' style='margin: 10px 0 0 0; padding: 0; float: left;'><input type='button' value='$mgrlang[gen_b_print]' id='print_button' class='small_button' onclick=\"simple_print('file_window');\" /></p><p align='right' style='margin: 10px 0 0 0; padding: 0; float: right;'><input type='button' value='Automatically Replace Files' id='start_button' class='small_button' onclick=\"start_upgrade();upgrade_files();\" /><input type='button' value='Stop' id='stop_button' class='small_button' onclick=\"stop_upgrade();\" disabled /><input type='button' value='Done' id='cont_button' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=upgrade_complete');\" /></p>";
				} else {
					echo "upgrade.php file missing. Upgrade failed and cannot continue."; 
				};
				
			break;
			case "upgrade_complete":
				if($current_version == $upgrade_version){
					echo "The upgrade has been completed. The new installed version is: <strong>$current_version</strong><br /><br /><span style='color: #cc0000'>You can now remove the queued upgrade files from /assets/upgrades.</span> &nbsp; <input type='button' value='Remove Files' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade');\" /> <input type='button' value='Skip' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','');\" /><br /><br />Update db with version info";
				} else {
					echo "something else";
				}
				
			break;
			case "get_newest":
				echo "get newest";
			break;
			case "remove_upgrade":
				echo "Are you sure that you would like to remove files in the /assets/upgrades directory? No harm will be done to your site by removing these files.  <input type='button' value='Yes' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','pmode=remove_upgrade2');\" /> <input type='button' value='No' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','');\" />";
			break;
			case "remove_upgrade2":
				echo "Removal Complete**************** <input type='button' value='Continue' class='small_button' onclick=\"load_upgrade_win('mgr.software.upgrade.actions.php','');\" />";
			break;
			case "upload_installer":
				echo "Please choose the installer file for the add-on using the browse button below and then click install.<br /><br />";
				echo "<form action='mgr.software.upgrade.php' method='post' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='test' value='1' />";
				echo "<input type='file' name='installer_file' class='small_button' />";
				echo "<input type='submit' value='Install' class='small_button' />";
				echo "</form>";
			break;
		}
?>
<?php /*<script language="javascript">submit_form();</script> */?>