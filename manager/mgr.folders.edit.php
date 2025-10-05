<?php
	###################################################################
	####	FOLDERS EDIT AREA                                      ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-10-2010                                     ####
	####	Modified: 2-10-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');										# INCLUDE THE SESSION START FILE
	
		$page = "folders";
		$lnav = "library";
		
		$supportPageID = '339';
	
		require_once('mgr.security.php');											# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');												# INCLUDE MANAGER CONFIG FILE
		require_once($config['base_path'] . '/assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		
		if(file_exists($config['base_path'] . '/assets/includes/db.config.php'))
			require_once('../assets/includes/db.config.php');								# INCLUDE DATABASE CONFIG FILE
		else
			@$script_error[] = "The db.config.php file is missing.";				# DATABASE CONFIG FILE MISSING
		
		require_once($config['base_path'] . '/assets/includes/shared.functions.php');			# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');											# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);															# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once($config['base_path'] . '/assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');									# SELECT THE SETTINGS DATABASE
		include_lang();																# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');												# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');										# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);											# TURN ERROR REPORTING BACK ON	
		
		require_once($config['base_path'] . '/assets/classes/foldertools.php');	# REQUIRE FOLDER BUILDER CLASS FILE
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$folders_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}folders WHERE folder_id = '$_GET[edit]'");
			$folders_rows = mysqli_num_rows($folders_result);
			$folders = mysqli_fetch_object($folders_result);
		}

		# CHECK IF AN ACTION SHOULD BE DONE
		if($_REQUEST['action'])
		{
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			# CLEAN FOLDER NAME
			$cleanname = clean_foldername($name);
			
			# ENCRYPTED NAME
			$enc_name = md5($cleanname . $config['settings']['serial_number']);
			
			# NEW FOLDER BUILDER OBJECT
			$creator = new folder_builder($cleanname,$encrypted,$storage_id);

			# ACTIONS
			switch($_REQUEST['action'])
			{
				# SAVE EDIT				
				case "save_edit":					
					# CHECK IF NEW NAME IS DIFFERENT THAN OLD NAME
					if($cleanname != $oldname)
					{
						# MAKE SURE THE FOLDER DOESN'T ALREADY EXIST
						if($creator->check_for_dup())
							$creator->return_errors();
						
						# SEE IF REMOTE STORAGE WAS SELECTED AND NO ERRORS UP UNTIL THIS POINT
						if($storage_id and !$creator->check_errors())
						{
							# RENAME STORAGE DIRECTORY
							if(!$creator->storage_directory($storage_id,'rename',$oldname))
								$creator->return_errors();
							
							# RENAME LOCAL DIRECTORY
							if(!$creator->rename_local($oldname))
								$creator->return_errors();
						}
						
					}
					
					# NO ERRORS SO FAR
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}folders SET 
								name='$cleanname',
								folder_notes='$folder_notes',
								enc_name='$enc_name'
								WHERE folder_id  = '$saveid'";
					$result = mysqli_query($db,$sql);					
					
					# DELETE GROUPS FIRST
					mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
					# ADD GROUPS
					if($setgroups)
					{
						foreach($setgroups as $value)
						{
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
						}
					}
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_folders'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
					
					header("location: mgr.folders.php?mes=edit"); exit;
				break;
				# SAVE NEW ITEM
				case "save_new":
						
					# MAKE SURE THE FOLDER DOESN'T ALREADY EXIST
					if($creator->check_for_dup())
						$creator->return_errors();
					
					# CREATE STORAGE DIRECTORIES
					if($storage_id and !$creator->check_errors())
					{						
						if(!$creator->storage_directory($storage_id,'create',''))
							$creator->return_errors();
					}
					
					# CREATE LOCAL DIRECTORIES
					if(!$creator->create_local_directories())
						$creator->return_errors();
					
					# CREATE folders ID
					$ufolder_id = create_unique2();
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}folders (
							name,
							ufolder_id,
							folder_notes,
							storage_id,
							enc_name,
							encrypted
							) VALUES (
							'$cleanname',
							'$ufolder_id',
							'$folder_notes',
							'$storage_id',
							'$enc_name',
							'$encrypted'
							)";
					$result = mysqli_query($db,$sql);		
					$saveid = mysqli_insert_id($db);

					# ADD GROUPS
					if($setgroups)
					{
						foreach($setgroups as $value)
						{
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
						}
					}
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_folders'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
					
					header("location: mgr.folders.php?mes=new"); exit;

				break;		
			}
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_folders']; ?></title>
	<!-- LOAD THE STYLE SHEETS -->
	<link rel="stylesheet" href="mgr.style.css" />
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
	<!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>    
    <!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
		<script language="javascript">
		function form_submitter(){
			$('name_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.folders.edit.php?action=save_new" : "mgr.folders.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","folders_f_name",1);
				}
			?>
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
		});	
	</script>	
</head>
<body>
	<?php echo $browser; ?>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # CHECK FOR DEMO MODE
            //demo_message($_SESSION['admin_user']['admin_id']);
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.folders.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['folders_new_header'] : $mgrlang['folders_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['folders_new_message'] : $mgrlang['folders_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div style="display: none;" id="hidden_box"></div>
            <div id="spacer_bar"></div>    
            <?php
				# PULL GROUPS
				$folders_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$folders_group_rows = mysqli_num_rows($folders_group_result);
			?> 
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['folders_tab1']; ?></div>
                <?php if($folders_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['folders_tab2']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['folders_tab3']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">                
                <div class="<?php fs_row_color(); ?>" id="name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['folders_f_name']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['folders_f_name_d']; ?></span>
                    </p>
                    <div style="float: left;">
                        <input type="hidden" name="oldname" id="oldname" value="<?php echo @stripslashes($folders->name); ?>" />
                        <?php
							if($_GET['edit'] == 'new')
							{
						?>
                        	<input type="text" name="name" id="name" style="width: 300px;" maxlength="40" value="<?php echo @stripslashes($folders->name); ?>" />
                        <?php
							}
							else
							{
						?>
                        	<input type="text" name="namedis" id="namedis" style="width: 300px;" maxlength="40" value="<?php echo @stripslashes($folders->name); ?>" disabled="disabled" />
                            <input type="hidden" name="name" id="name" style="width: 300px;" maxlength="40" value="<?php echo @stripslashes($folders->name); ?>" />
                        <?php
							}
                            if($_GET['edit'] != 'new' and $folders->encrypted)
                            {
                                echo "<br /><br /><span style='color: #696969'>{$mgrlang[enc_name]}: " . $folders->enc_name . "</span>";
                            }
                        ?>
                    </div>
                </div>
                <?php
					if(in_array("storage",$installed_addons))
					{
				?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['folders_f_storage']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['folders_f_storage_d']; ?></span>
                    </p>
                    <?php
                    	if($_GET['edit'] != 'new')
						{ 
							echo "<input type='hidden' name='storage_id' value='$folders->storage_id' />";
                    		echo "<select name='storage_id_disabled' id='storage_id_disabled' style='width: 310px;' disabled='disabled'>";
						}
						else
						{
							echo "<select name='storage_id' id='storage_id' style='width: 310px;'>";
						}
						?>
                    	<option value="0"><?php echo $mgrlang['local_lib']; ?></option>
                        <?php
							${$folders->storage_id} = "selected='selected'";
							$storage_result = mysqli_query($db,"SELECT storage_id,name FROM {$dbinfo[pre]}storage WHERE active = '1'");
							while($storage = mysqli_fetch_object($storage_result))
							{
								echo "<option value='$storage->storage_id' " . ${$storage->storage_id} . ">$storage->name</option>";
							}
						?>
                    </select>
                </div>
                <?php
					}
					else
					{
						echo "<input type='hidden' name='storage_id' value='0' />";
					}
				?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['folders_f_notes']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['folders_f_notes_d']; ?></span>
                    </p>
                    <textarea name="folder_notes" id="folder_notes" style="width: 300px; height: 50px;"><?php echo @stripslashes($folders->folder_notes); ?></textarea>
                </div>
            </div>
 			
            <?php $row_color = 0; ?>
            <div id="tab3_group" class="group">
            	<?php
					if($_GET['edit'] != 'new')
					{
						$media_result = mysqli_query($db,"SELECT COUNT(media_id) AS mediaCount, SUM(filesize) AS originalFilesize FROM {$dbinfo[pre]}media WHERE folder_id = '$_GET[edit]'");
						$media = mysqli_fetch_object($media_result);
						
						$smedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_samples.sample_filesize) AS sampleFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_samples ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_samples.media_id WHERE {$dbinfo[pre]}media.folder_id = '$_GET[edit]'");
						$smedia = mysqli_fetch_object($smedia_result);
						
						$tmedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_thumbnails.thumb_filesize) AS thumbFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_thumbnails ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_thumbnails.media_id WHERE {$dbinfo[pre]}media.folder_id = '$_GET[edit]'");
						$tmedia = mysqli_fetch_object($tmedia_result);
						
						$total_fs = $media->originalFilesize + $smedia->sampleFilesize + $tmedia->thumbFilesize;
					}
                ?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['folders_f_media']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['folders_f_media_d']; ?></span>
                    </p>
                    <div style="padding-top: 10px;"><strong><?php if($_GET['edit'] != 'new'){ echo $media->mediaCount; } else { echo "0"; } ?></strong></div>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['folders_f_space']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['folders_f_space_d']; ?></span>
                    </p>
                    <div style="padding-top: 10px;"><strong><?php echo convertFilesizeToMB($total_fs); ?></strong><?php echo $mgrlang['gen_mb']; ?> <!--(<?php echo convertFilesizeToMB($media->originalFilesize); ?>MB Remote)--></div>
                </div>
				<?php
					if($_GET['edit'] != 'new')
					{
				?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_lp']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['setup_f_lp_d']; ?></span>
                        </p>
                        <div style="float: left; padding-top: 4px;">
							<?php
                                if($_SESSION['admin_user']['admin_id'] == "DEMO")
								{
									echo "[" . $mgrlang['gen_hidden'] . "]";
								}
								else
								{ 
									if($folders->encrypted)
									{
										$usename = $folders->enc_name;
									}
									else
									{
										$usename = $folders->name;
									}
									echo $config['settings']['library_path'] . DIRECTORY_SEPARATOR . $usename . "<img src='images/mgr.small.check.1.png' style='margin-left: 10px' /><br />";
								}
                            ?>
                        </div>
                    </div>
                    <?php
						if($folders->storage_id and in_array("storage",$installed_addons))
						{
							$storage_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage WHERE storage_id = '$folders->storage_id'");
							$storage = mysqli_fetch_object($storage_result);
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['folders_f_sp']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['folders_f_sp_d']; ?></span>
                        </p>
						<div style="float: left; padding-top: 4px;">
							<?php
                                if($_SESSION['admin_user']['admin_id'] == "DEMO")
								{
									echo "[" . $mgrlang['gen_hidden'] . "]";
								}
								else
								{ 
									switch($storage->storage_type)
									{
										case "ftp":
											echo "<strong>$storage->name</strong> : " . stripslashes(k_decrypt($storage->path)) . '/' . $usename;
											
											# REQUIRE THE FTP CLASS FILE
											require_once($config['base_path'] . '/assets/classes/ftp.php');	
											
											# FTP CONNECTION
											$ftp = new ftp_connection(stripslashes(k_decrypt($storage->host)),stripslashes(k_decrypt($storage->username)),stripslashes(k_decrypt($storage->password)),$storage->port);
											
											$cleanpath = stripslashes(k_decrypt($storage->path)) . '/' . $usename;
											
											# CHANGE FTP DIRECTORY
											$ftp->change_dir($cleanpath);
											
											if($ftp->ftp_errors())
											{
												$checkdir = 0;
												$error = $ftp->ftp_errors();
											}
											else
											{
												$checkdir = 1;
											}
											
											# CLOSE FTP CONNECTION
											$ftp->close_conn();
											
										break;
										case "local":
											echo "<strong>$storage->name</strong> : " . stripslashes(k_decrypt($storage->path)) . DIRECTORY_SEPARATOR . $usename;
											if(file_exists(stripslashes(k_decrypt($storage->path)) . DIRECTORY_SEPARATOR . $usename))
											{
												$checkdir = 1;	
											}
											else
											{
												$checkdir = 0;
											}
										break;
										case "amazon_s3":
											echo "<strong>$storage->name</strong> : " . $folders->enc_name;
											
											# INCLUDE AMAZON CLASS FILE
											require_once($config['base_path'] . '/assets/classes/amazonS3/as3.php');
											
											# DEFINE THE CONNECTION KEYS
											if(!defined('awsAccessKey')) define('awsAccessKey', stripslashes(k_decrypt($storage->username)));
											if(!defined('awsSecretKey')) define('awsSecretKey', stripslashes(k_decrypt($storage->password)));
											
											// Instantiate the class
											$s3 = new S3(awsAccessKey, awsSecretKey);
											
											S3::$useSSL = false;
											
											// Get the contents of our bucket
											$contents = $s3->getBucket($folders->enc_name);
											
											if(is_array($contents))
											{
												$checkdir = 1;	
											}
											else
											{
												$checkdir = 0;
											}
											
										break;
										case "cloudfiles":
											echo "<strong>$storage->name</strong> : " . $folders->enc_name;
										break;
									}
									
									if($checkdir)
									{
										echo "<img src='images/mgr.small.check.1.png' style='margin-left: 10px' />";
									}
									else
									{
										echo "<img src='images/mgr.notice.icon.small2.png' style='margin-left: 10px; vertical-align:middle' />";
									}
								}
                            ?>
                    	</div>
                    </div>
                <?php
						}
					}

                	if($_GET['edit'] == 'new' and in_array("pro",$installed_addons))
               		{            
                ?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['enc_folder']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['enc_folder_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="encrypted" <?php if($config['settings']['enc_folders']){ echo "checked"; } ?> />
                </div>
                <?php
					}
					else
					{
						echo "<input type='hidden' value='$folders->encrypted' name='encrypted' />";
					}
				?>
            </div>
            
            <?php
            	if($folders_group_rows)
				{
					$row_color = 0;
			?>
                <div id="tab2_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['folders_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['folders_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$folders_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$folders->folder_id' AND item_id != 0");
							while($folders_groupids = mysqli_fetch_object($folders_groupids_result))
							{
								$plangroups[] = $folders_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($folders_group = mysqli_fetch_object($folders_group_result))
							{
								echo "<li><input type='checkbox' id='grp_$folders_group->gr_id' class='permcheckbox' name='setgroups[]' value='$folders_group->gr_id' "; if(in_array($folders_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($folders_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$folders_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$folders_group->gr_id'>" . substr($folders_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
                        ?>
                    </div>
            	</div>
			<?php
                }
            ?>
            
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.folders.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick='form_submitter();' />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>