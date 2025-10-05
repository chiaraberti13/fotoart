<?php
	###################################################################
	####	STORAGE EDIT AREA                                      ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-4-2010                                      ####
	####	Modified: 2-4-2010                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "storage";
		$lnav = "settings";
		
		$supportPageID = 0;
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php"))
		{			
			require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
		}
		else
		{ 											
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
		
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$storage_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage WHERE storage_id = '$_GET[edit]'");
			$storage_rows = mysqli_num_rows($storage_result);
			$storage = mysqli_fetch_object($storage_result);
			
			//echo "Test:" . stripslashes($storage->username) . " :end"; exit;
		}
	
		if($_REQUEST['action'])
		{
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			//echo $as3_username; exit;
			
			switch($storage_type)
			{
				case "local":
					$local_path = rtrim($local_path,"/");
					$local_path = rtrim($local_path,"\\");
					$path = k_encrypt($local_path);
				break;
				case "ftp":
					$ftp_path = rtrim($ftp_path,"/");
					$ftp_path = rtrim($ftp_path,"\\");					
					$host = k_encrypt($ftp_host);
					$port = $ftp_port;
					$path = k_encrypt($ftp_path);
					$username = k_encrypt($ftp_username);
					$password = k_encrypt($ftp_password);
				break;
				case "amazon_s3":
					$username = k_encrypt($as3_username);
					$password = k_encrypt($as3_password);
				break;
				case "cloudfiles":
					$username = k_encrypt($cloudfiles_username);
					$password = k_encrypt($cloudfiles_password);
				break;
			}
		}
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SAVE EDIT				
			case "save_edit":
			
				//echo "test:" . $storage_type; exit;
			
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}storage SET 
							name='$name',
							storage_type='$storage_type',
							host='$host',
							port='$port',
							username='$username',
							password='$password',
							path='$path',
							active='$active'
							WHERE storage_id  = '$saveid'";
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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_storage'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.storage.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":		
				
				# CREATE storage ID
				$ustorage_id = create_unique2();
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}storage (
						name,
						ustorage_id,
						storage_type,
						host,
						port,
						username,
						password,
						path,
						active
						) VALUES (
						'$name',
						'$ustorage_id',
						'$storage_type',
						'$host',
						'$port',
						'$username',
						'$password',
						'$path',
						'$active'
						)";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);
				//exit;
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_storage'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.storage.php?mes=new"); exit;
			break;		
		}
		
		//$cleanvalues->decimal_places = 0;
		//echo $cleanvalues->number_display('1000'); exit;
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_storage']; ?></title>
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
					$action_link = ($_GET['edit'] == "new") ? "mgr.storage.edit.php?action=save_new" : "mgr.storage.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","storage_f_alias",1);
					js_validate_select("storage_type","storage_f_st",1);
					
					echo "check_data(1);";
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
		
		// OPEN THE STORAGE TYPE SETTINGS
		function choose_storage_type()
		{
			var selecteditem = $('storage_type').options[$('storage_type').selectedIndex].value;
			reset_option_fields();
			// HIDE ALL
			if(selecteditem != '#')
			{
				$$('.more_options').each(function(s){
						s.setStyle({
							display: 'none'
						});
				  });
				// SHOW SELECTED
				show_div(selecteditem);
			}
			else
			{
				$$('.more_options').each(function(s){
						s.setStyle({
							display: 'none'
						});
				  });
			}
		}
		
		function reset_option_fields()
		{
			$('local_path_div').className='fs_row_off';			
			if($('ftp_host_div') != null){ $('ftp_host_div').className='fs_row_off'; };
			if($('ftp_port_div') != null){ $('ftp_port_div').className='fs_row_off'; };
			if($('ftp_username_div') != null){ $('ftp_username_div').className='fs_row_off'; };
			if($('ftp_password_div') != null){ $('ftp_password_div').className='fs_row_off'; };
			if($('ftp_path_div') != null){ $('ftp_path_div').className='fs_row_off'; };
			$('storage_type_div').className='fs_row_on';
			if($('as3_username_div') != null){ $('as3_username_div').className='fs_row_off'; };
			if($('as3_password_div') != null){ $('as3_password_div').className='fs_row_off'; };
			if($('cloudfiles_username_div') != null){ $('cloudfiles_username_div').className='fs_row_off'; };
			if($('cloudfiles_password_div') != null){ $('cloudfiles_password_div').className='fs_row_off'; };
		}
		
		// CHECK DATA
		function check_data(dosubmit)
		{
			var loadpage = "mgr.storage.actions.php";
			var type = $('storage_type').options[$('storage_type').selectedIndex].value;
			reset_option_fields();
			switch(type)
			{
				case "ftp":
					<?php
						if(!function_exists('ftp_connect'))
						{
							echo "simple_message_box('<span style=\'color: #b91111;\'>$mgrlang[storage_mes_ftp6a]</span><br /><span style=\'font-weight: normal;\'>$mgrlang[storage_mes_ftp6b]</span>','');";
						}
						else
						{
							js_validate_field("ftp_host","storage_f_ftphost",1);
							js_validate_field("ftp_port","storage_f_ftpport",1);
							js_validate_field("ftp_username","storage_f_ftpuser",1);
							js_validate_field("ftp_password","storage_f_ftppass",1);
					?>
						$('ftp_test_button').setValue('<?php echo $mgrlang['storage_b_checking']; ?>');
						$('ftp_test_button').disable();
						var myAjax = new Ajax.Updater(
						'hidden_box',
						loadpage,
							{
							  method: 'get',
							  parameters:
								{
									action: 'check_ftp',
									ftp_host: $F('ftp_host'),
									ftp_port: $F('ftp_port'),
									ftp_username: $F('ftp_username'),
									ftp_password: $F('ftp_password'),
									ftp_path: $F('ftp_path'),
									dosubmit: dosubmit
								},
								evalScripts: true
							}
						);
					<?php
						}
					?>
				break;
				case "local":					
					<?php
						js_validate_field("local_path","storage_f_title",1);
					?>
					$('local_test_button').setValue('<?php echo $mgrlang['storage_b_checking']; ?>');
					$('local_test_button').disable();
					var myAjax = new Ajax.Updater(
					'hidden_box',
					loadpage,
						{
						  method: 'get',
						  parameters:
							{
								action: 'check_local',
								local_path: addslashes($F('local_path')),
								dosubmit: dosubmit
							},
							evalScripts: true
						}
					);
				break;
				case "amazon_s3":
					<?php
						if((!extension_loaded('curl') and !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) or (phpversion() < '5'))
						{
							echo "simple_message_box('<span style=\'color: #b91111;\'>$mgrlang[storage_mes_as3_3a]</span><br /><span style=\'font-weight: normal;\'>$mgrlang[storage_mes_as3_3b]</span>','');";
						}
						else
						{
							js_validate_field("as3_username","storage_f_as3key",1);
							js_validate_field("as3_password","storage_f_as3skey",1);
					?>
							$('as3_test_button').setValue('<?php echo $mgrlang['storage_b_checking']; ?>');
							$('as3_test_button').disable();
							var myAjax = new Ajax.Updater(
							'hidden_box',
							loadpage,
								{
								  method: 'get',
								  parameters:
									{
										action: 'check_as3',
										as3_username: $F('as3_username'),
										as3_password: $F('as3_password'),
										dosubmit: dosubmit
									},
									evalScripts: true
								}
							);
					<?php
						}
					?>
				break;
				case "cloudfiles":
					<?php
						if((!extension_loaded('curl') and !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) or (phpversion() < '5'))
						{
							echo "simple_message_box('<span style=\'color: #b91111;\'>$mgrlang[storage_mes_as3_3a]</span><br /><span style=\'font-weight: normal;\'>$mgrlang[storage_mes_as3_3b]</span>','');";
						}
						else
						{
							js_validate_field("cloudfiles_username","storage_f_cf_username",1);
							js_validate_field("cloudfiles_password","storage_f_cf_apikey",1);
					?>
							$('cloudfiles_test_button').setValue('<?php echo $mgrlang['storage_b_checking']; ?>');
							$('cloudfiles_test_button').disable();
							var myAjax = new Ajax.Updater(
							'hidden_box',
							loadpage,
								{
								  method: 'get',
								  parameters:
									{
										action: 'cloudfiles',
										cloudfiles_username: $F('cloudfiles_username'),
										cloudfiles_password: $F('cloudfiles_password'),
										dosubmit: dosubmit
									},
									evalScripts: true
								}
							);
					<?php
						}
					?>
				break;
			}
			return false;
		}
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
            demo_message($_SESSION['admin_user']['admin_id']);
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.storage.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['storage_new_header'] : $mgrlang['storage_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['storage_new_message'] : $mgrlang['storage_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div style="display: none;" id="hidden_box"></div>
            <div id="spacer_bar"></div>    
            <?php
				# PULL GROUPS
				$storage_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$storage_group_rows = mysqli_num_rows($storage_group_result);
			?> 
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['storage_tab1']; ?></div>
                <?php if($storage_group_rowsXXX){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['storage_tab2']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['storage_tab3']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">                
                <div class="<?php fs_row_color(); ?>" id="name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['storage_f_alias']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['storage_f_alias_d']; ?></span>
                    </p>
                    <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($storage->name); ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" id="storage_type_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['storage_f_st']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['storage_f_st_d']; ?></span>
						<br />
						<br />
						<br />
                    </p> 
                    <?php $supportedStorages = externalStorageSupport(); ?>             
                    <select name="storage_type" id="storage_type" style="width: 311px;" onchange="choose_storage_type();" <?php if($_GET['edit'] != 'new'){ echo "disabled='disabled'"; } ?>>
                    	<option value="#"></option>
                        <option value="local" <?php if($storage->storage_type == 'local'){ echo "selected"; } ?>><?php echo $mgrlang['storage_f_stop1']; ?></option>
                        <!--<option value="ftp" <?php if($storage->storage_type == 'ftp'){ echo "selected"; } ?>><?php echo $mgrlang['storage_f_stop2']; ?> <?php if(!in_array('ftp',$supportedStorages)){ echo "(Not supported by this server)"; } ?></option>-->
                        <option value="amazon_s3" <?php if($storage->storage_type == 'amazon_s3'){ echo "selected"; } ?>><?php echo $mgrlang['storage_f_stop3']; ?> <?php if(!in_array('amazon_s3',$supportedStorages)){ echo "(Not supported by this server)"; } ?></option>
                        <!--<option value="cloudfiles" <?php if($storage->storage_type == 'cloudfiles'){ echo "selected"; } ?>><?php echo $mgrlang['storage_f_stop4']; ?> <?php if(!in_array('cloudfiles',$supportedStorages)){ echo "(Not supported by this server)"; } ?></option>-->
                    </select><br />
                    <?php
						if(!in_array('ftp',$supportedStorages))
						{
							echo "<div class='more_options' id='ftp' style='padding: 0; width: 400px; display: none; background-color: #fde1e1; border: 1px solid #eb8383; background-image: none;'><p style='padding: 15px; width: 90%' class='fs_row_error'>$mgrlang[storage_mes_ftp6a]<br /><span>$mgrlang[storage_mes_ftp6b]</span></p></div>";	
						}
						else
						{
					?>
                        <div class="more_options" id="ftp" style="padding: 0; width: 420px; <?php if($storage->storage_type != 'ftp'){ echo "display: none;"; } ?>">
                            <div class="fs_row_off" id="ftp_host_div" style="padding: 20px;">
                                <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                    <?php echo $mgrlang['storage_f_ftphost']; ?>:<br />
                                    <span><?php echo $mgrlang['storage_f_ftphost_d']; ?></span>
                                </p>
                                <input type="text" name="ftp_host" id="ftp_host" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else { echo stripslashes(k_decrypt($storage->host)); } ?>" />
                            </div>
                            <div class="fs_row_off" id="ftp_port_div" style="padding: 20px;">
                                <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                    <?php echo $mgrlang['storage_f_ftpport']; ?>:<br />
                                    <span><?php echo $mgrlang['storage_f_ftpport_d']; ?></span>
                                </p>
                                <input type="text" name="ftp_port" id="ftp_port" style="width: 50px;" maxlength="100" value="<?php if($_GET['edit'] == 'new'){ echo "21"; } else { echo $storage->port; } ?>" />
                            </div>
                            <div class="fs_row_off" id="ftp_username_div" style="padding: 20px;">
                                <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                    <?php echo $mgrlang['storage_f_ftpuser']; ?>:<br />
                                    <span><?php echo $mgrlang['storage_f_ftpuser']; ?></span>
                                </p>
                                <input type="text" name="ftp_username" id="ftp_username" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->username)); } ?>" />
                            </div>
                            <div class="fs_row_off" id="ftp_password_div" style="padding: 20px;">
                                <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                    <?php echo $mgrlang['storage_f_ftppass']; ?>:<br />
                                    <span><?php echo $mgrlang['storage_f_ftppass_d']; ?></span>
                                </p>
                                <input type="text" name="ftp_password" id="ftp_password" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->password)); } ?>" />
                            </div>
                            <div class="fs_row_off" id="ftp_path_div" style="padding: 20px;">
                                <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                    <?php echo $mgrlang['storage_f_ftppath']; ?>:<br />
                                    <span><?php echo $mgrlang['storage_f_ftppath']; ?></span>
                                </p>
                                <input type="text" name="ftp_path" id="ftp_path" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->path)); } ?>" />
                            </div>
                            <div class="fs_row_off" style="text-align: right">
                                <input type="button" id="ftp_test_button" value="<?php echo $mgrlang['storage_b_test']; ?>" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "check_data(0);"; } ?>" />
                            </div>
                        </div>
                    <?php
						}
					?>
                    <div class="more_options" id="local" style="padding: 0; width: 420px; <?php if($storage->storage_type != 'local'){ echo "display: none;"; } ?>">
                    	<div class="fs_row_off" id="local_path_div" style="padding: 20px;">
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                <?php echo $mgrlang['storage_f_localpath']; ?>:<br />
                                <span><?php echo $mgrlang['storage_f_localpath_d']; ?></span>
                            </p>
                            <input type="text" name="local_path" id="local_path" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->path)); } ?>" />
                        </div>                        
                        <div class="fs_row_off" style="text-align: right">
                            <input type="button" id="local_test_button" value="<?php echo $mgrlang['storage_b_test']; ?>" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "check_data(0);"; } ?>" />
                        </div>
                    </div>
                    <?php
						# CHECK FOR AMAZON S3 SUPPORT
						if(!in_array('amazon_s3',$supportedStorages))
						{
							echo "<div class='more_options' id='amazon_s3' style='padding: 0; width: 400px; display: none; background-color: #fde1e1; border: 1px solid #eb8383; background-image: none;'><p style='padding: 15px; width: 90%' class='fs_row_error'>$mgrlang[storage_mes_as3_3a]<br /><span>$mgrlang[storage_mes_as3_3b]</span></p></div>";
						}
						else
						{
					?>
                    <div class="more_options" id="amazon_s3" style="padding: 0; width: 420px; <?php if($storage->storage_type != 'amazon_s3'){ echo "display: none;"; } ?>">
                    	<div class="fs_row_off" id="as3_username_div" style="padding: 20px;">
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                <?php echo $mgrlang['storage_f_as3key']; ?>:<br />
                                <span><?php echo $mgrlang['storage_f_as3key_d']; ?></span>
                            </p>
                            <input type="text" name="as3_username" id="as3_username" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->username)); } ?>" />
                        </div>
                        <div class="fs_row_off" id="as3_password_div" style="padding: 20px;">
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                <?php echo $mgrlang['storage_f_as3skey']; ?>:<br />
                                <span><?php echo $mgrlang['storage_f_as3skey_d']; ?></span>
                            </p>
                            <input type="text" name="as3_password" id="as3_password" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->password)); } ?>" />
                        </div>                       
                        <div class="fs_row_off" style="text-align: right">
                            <input type="button" id="as3_test_button" value="<?php echo $mgrlang['storage_b_test']; ?>" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "check_data(0);"; } ?>" />
                        </div>
                    </div>
                    <?php
						}
					?>
                    <?php
						# CHECK FOR CURL
						if(!in_array('cloudfiles',$supportedStorages))
						{
							echo "<div class='more_options' id='cloudfiles' style='padding: 0; width: 400px; display: none; background-color: #fde1e1; border: 1px solid #eb8383; background-image: none;'><p style='padding: 15px; width: 90%' class='fs_row_error'>$mgrlang[storage_mes_cf_3a]<br /><span>$mgrlang[storage_mes_cf_3b]</span></p></div>";
						}
						else
						{
					?>
                    <div class="more_options" id="cloudfiles" style="padding: 0; width: 420px; <?php if($storage->storage_type != 'cloudfiles'){ echo "display: none;"; } ?>">
                    	<div class="fs_row_off" id="cloudfiles_username_div" style="padding: 20px;">
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                <?php echo $mgrlang['storage_f_cf_username']; ?>:<br />
                                <span><?php echo $mgrlang['storage_f_cf_username_d']; ?></span>
                            </p>
                            <input type="text" name="cloudfiles_username" id="cloudfiles_username" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->username)); } ?>" />
                        </div>
                        <div class="fs_row_off" id="cloudfiles_password_div" style="padding: 20px;">
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="width: 140px;">
                                <?php echo $mgrlang['storage_f_cf_apikey']; ?>:<br />
                                <span><?php echo $mgrlang['storage_f_cf_apikey_d']; ?></span>
                            </p>
                            <input type="text" name="cloudfiles_password" id="cloudfiles_password" style="width: 200px;" maxlength="100" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[gen_hidden]>"; } else {  echo stripslashes(k_decrypt($storage->password)); } ?>" />
                        </div>                       
                        <div class="fs_row_off" style="text-align: right">
                            <input type="button" id="cloudfiles_test_button" value="<?php echo $mgrlang['storage_b_test']; ?>" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "check_data(0);"; } ?>" />
                        </div>
                    </div>
                    <?php
						}
					?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['storage_f_active']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['storage_f_active_d']; ?></span>
                    </p>
                    <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($storage->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
            </div>
 			
            <?php
				$folders_result = mysqli_query($db,"SELECT folder_id FROM {$dbinfo[pre]}folders WHERE storage_id = '$storage->storage_id'");
				while($folders = mysqli_fetch_array($folders_result))
				{
					$folderlist[] = $folders['folder_id'];
				}
				
				$allfolders = implode(',',$folderlist);
				
				$stmedia_result = mysqli_query($db,"SELECT COUNT(media_id) AS mediaCount,SUM(filesize) AS originalFilesize,umedia_id FROM {$dbinfo[pre]}media WHERE folder_id IN ($allfolders)"); // IN ($allfolders)
				$stmedia = mysqli_fetch_object($stmedia_result);
			?>
            
            <?php $row_color = 0; ?>
            <div id="tab3_group" class="group">
            	<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['storage_f_media']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['storage_f_media_d']; ?></span>
                    </p>
                    <div style="padding-top: 10px;"><strong><?php if($stmedia->mediaCount){ echo $stmedia->mediaCount; } else { echo "0"; } ?></strong></div>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['storage_f_space']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['storage_f_space_d']; ?></span>
                    </p>
                    <div style="padding-top: 10px;"><strong><?php echo convertFilesizeToMB($stmedia->originalFilesize); ?></strong><?php echo $mgrlang['gen_mb']; ?></div>
                </div>
            </div>
            
            <?php
            	if($storage_group_rows)
				{
					$row_color = 0;
			?>
                <div id="tab2_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['storage_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['storage_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$storage_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$storage->storage_id' AND item_id != 0");
							while($storage_groupids = mysqli_fetch_object($storage_groupids_result))
							{
								$plangroups[] = $storage_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($storage_group = mysqli_fetch_object($storage_group_result))
							{
								echo "<li><input type='checkbox' id='grp_$storage_group->gr_id' class='permcheckbox' name='setgroups[]' value='$storage_group->gr_id' "; if(in_array($storage_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($storage_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$storage_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$storage_group->gr_id'>" . substr($storage_group->name,0,30)."</label></li>";
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
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.storage.php');" /><input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick='form_submitter();' />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>