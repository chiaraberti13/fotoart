<?php
	###################################################################
	####	MANAGER ADMINISTRATORS EDIT AREA                       ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-7-2006                                      ####
	####	Modified: 10-15-2007                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "administrators";
		$lnav = "users";
		
		$supportPageID = '343';
	
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
		require_once('../assets/classes/encryption.php');				# INCLUDE ENCRYPTION CLASS
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	

		//$edittype = ($_GET['edit'] == "new" or $_REQUEST['action'] == "save_new") ? "new" : "";

		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			//$editid = (!empty($_GET['edit'])) ? $_GET['edit'] : $_POST['saveid'];

			$admin_result = mysqli_query($db,"SELECT *,
												DATE_FORMAT(last_login, '%m') AS login_month,
												DATE_FORMAT(last_login, '%Y') AS login_year,
												DATE_FORMAT(last_login, '%d') AS login_day FROM {$dbinfo[pre]}admins WHERE uadmin_id = '$_GET[edit]'");
			$admin_rows = mysqli_num_rows($admin_result);
			$admin = mysqli_fetch_object($admin_result);
			
			$username = $admin->username;
			$password = $admin->password;
			$email = $admin->email;
			$active = $admin->active;
		}
		
		# ACTIONS
		if(!empty($_REQUEST['action'])){
			
			$permissions = ($_POST['group']) ? implode(",",$_POST['group']) : "";
			
			/*
			# CONVERT POST VALUES TO LOCAL VALUES AND ADD SLASHES IF NEEDED					
			foreach($_POST as $key => $value){
				if(!get_magic_quotes_gpc()){
					if(is_array($value)){
						foreach($value as $key2 => $value2){
							${$key}[$key2] = addslashes($value2);
						}	
					} else {
						${$key} = addslashes($value);
					}
				} else {
					${$key} = $value;
				}
			}
			*/
			
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			# STRIP HTML AND WHITE SPACE
			$username = trim(strip_tags($username));	
			$password = trim(str_replace(" ","",strip_tags($password)));	
			$email = trim(strip_tags($email));
			
			# STRIP BAD CHARACTERS
			$username = stripbadchar($username);
			
			switch($_REQUEST['action']){
				# SAVE EDIT				
				case "save_edit":
					# MAKE SURE A SUPERADMIN CAN'T GET DISABLED				
					if($superadmin == 1){
						$active = 1;
					}
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}admins SET 
								username='$username',";								
					# MAKE SURE PASSWORD IS PASSED
					if($password){
						# ENCRYPT PASSWORD
						//$crypt = new encryption_class;
						//$encrypt_result = $crypt->encrypt($config['settings']['serial_number'], $password, 20);
						//$errors = $crypt->errors;
						//$password = $encrypt_result;
						
						$password = k_encrypt($password);
						
						$sql.= "password='$password',";
					}
					$sql.=		"email='$email',
								active='$active'";
					# SEE IF THE PROFILE BELONGS TO THE ONE SAVING IT
					if($_SESSION['admin_user']['uadmin_id'] != $saveid){ $sql.= ",permissions='$permissions'"; }
					$sql.=		"where uadmin_id = '$saveid'";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_administrators'],1,$mgrlang['gen_b_ed'] . " > <strong>$username</strong>");
					
					
					
					# REDIRECT IF NOT PRO VERSION
					if(in_array('pro',$installed_addons))
					{
						header("location: mgr.administrators.php?mes=edit"); exit;
					}
					else
					{
						header("Location: mgr.administrators.edit.php?edit=460B1BDD209A534EF0AF1EVYKHF04EE9&mes=edit");
						exit;
					}
					
				break;
				# SAVE NEW ITEM
				case "save_new":		
					//$uadmin_id = create_unique();
					// CHANGED TO GET TRUE UNIQUE
					$uadmin_id = create_unique2();
					
					$fake_date = "2006-01-01 11:00:00";
					
					/*
					# ENCRYPT PASSWORD
					$crypt = new encryption_class;
					$encrypt_result = $crypt->encrypt($config['settings']['serial_number'], $password, 20);
					$errors = $crypt->errors;
					$password = $encrypt_result;
					*/
					
					$password = k_encrypt($password);
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}admins (
								username,
								password,
								email,
								active,
								uadmin_id,
								last_login,
								permissions
							) VALUES (
								'$username',
								'$password',
								'$email',
								'$active',
								'$uadmin_id',
								'$fake_date',
								'$permissions'
							)";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_administrators'],1,$mgrlang['gen_b_new'] . " > <strong>$username</strong>");
					
					header("location: mgr.administrators.php?mes=new"); exit;
				break;		
			}
		}
		
		if($_GET['mes'] == "new"){
			$vmessage = $mgrlang['admin_mes_01'];
		}
		if($_GET['mes'] == "edit"){
			$vmessage = $mgrlang['admin_mes_02'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_administrators']; ?></title>
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
    <script language="javascript" type="text/javascript">
		function form_submitter(){
			// REVERT BACK
			$('username_div').className='fs_row_off';
			$('password_div').className='fs_row_on';
			$('email_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.administrators.edit.php?action=save_new" : "mgr.administrators.edit.php?action=save_edit";
					
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("username","admin_f_username",1);
					js_validate_field("password","admin_f_password",1);
					js_validate_field("email","admin_f_email",1);
			
			?>
				// AJAX CHECK FORM DETAILS
				var url = "mgr.administrators.actions.php";
				var updatebox = "hidden_box";
				
				var pars = $H({username: $F('username'),password: $F('password'),email: $F('email'),edittype: '<?php echo $edittype; ?>',saveid: $F('saveid')}).toQueryString();
				
				//var pars = "username=" + $('username').value + "&password=" + $('password').value + "&email=" + $('email').value + "&edittype=<?php echo $edittype; ?>&saveid=" + $('saveid').value;
				var myAjax = new Ajax.Updater(
					updatebox, 
					url, 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true
					});	
				return false;	
			<?php
				}
			?>
		}
		
		// LOAD ACTIVITY LOG
		function load_al(startat){			
			if($('activity_window') != null){
				show_loader('activity_window');
			} else {
				show_loader('activity_log');
			}
			var pars = 'mid=<?php echo $admin->admin_id; ?>&manager=1&start=' + startat + get_to_date() + get_from_date();
			var myAjax = new Ajax.Updater(
				'activity_log', 
				'mgr.activity.log.php', 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		// DOWNLOAD CSV
		function download_csv(){
			location.href='mgr.activity.log.php'+'?displaymode=download&manager=1&mid=<?php echo $admin->admin_id; ?>' + get_to_date() + get_from_date();
		}
		
		// SUBMIT THE FORM
		function submit_form(){
			$('data_form').action = "<?php echo $action_link; ?>";
			$('data_form').submit();
		}
		
		// CHECK PERMISSIONS GROUP
		function check_group(groupid,subgroupid){
			//if(document.data_form.group[1].checked == true){
			//var myval = document.getElementById('group_' + groupid).value;
			
			for(var x=1;x<30;x++){
				if($('subgroup_' + groupid + '_' + x) != null){
					if($('subgroup_' + groupid + '_' + x).checked){
						$('group_' + groupid).checked = true;
					}
				}			
			}		
		}
		
		// CHECK PERMISSIONS SUBGROUP
		function check_subgroup(groupid){			
			if($('group_' + groupid).checked == false){			
				for(var x=1;x<30;x++){
					if($('subgroup_' + groupid + '_' + x) != null){
						$('subgroup_' + groupid + '_' + x).checked = false;
					}			
				}				
			}				
		}
		
		// SELECT ALL IN THE PERMISSIONS GROUP
		function select_all_group(groupid){
			//if(document.data_form.group[1].checked == true){
			//var myval = document.getElementById('group_' + groupid).value;
			if($('group_' + groupid).disabled == false){
				$('group_' + groupid).checked = true;
			}
			for(var x=1;x<30;x++){
				if($('subgroup_' + groupid + '_' + x) != null && $('subgroup_' + groupid + '_' + x).disabled == false){
					$('subgroup_' + groupid + '_' + x).checked = true;
				}			
			}		
		}
		
		// SELECT NONE FROM THE PERMISSIONS GROUP
		function select_none_group(groupid){
			//if(document.data_form.group[1].checked == true){
			//var myval = document.getElementById('group_' + groupid).value;
			if($('group_' + groupid).disabled == false){
				$('group_' + groupid).checked = false;
			}
			for(var x=1;x<30;x++){
				if($('subgroup_' + groupid + '_' + x) != null && $('subgroup_' + groupid + '_' + x).disabled == false){
					$('subgroup_' + groupid + '_' + x).checked = false;
				}			
			}		
		}
		
		// UNENCRYPT THE USERS PASSWORD
		function unencrypt_pass(){			
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO"){
				demo_message();
			} else {
				//$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				//var pass = $('password').value;
				//alert(pass);
				var updatecontent = "password_div2";
				var loadpage = "mgr.unencrypt.php?action=admin_pass&id=<?php echo $_GET['edit']; ?>";
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
				
				$('password').disabled = false;
				$('password').name = "password";
			}
		}
		
		// PURGE THE ACTIVITY LOG BEFORE A CERTAIN DATE
		function purge_activity_log(){
			var url = "mgr.activity.log.php";
			var updatebox = "activity_log";
			//var pars;
			var pday = $('purge_day').options[$('purge_day').selectedIndex].value;
			var pmonth = $('purge_month').options[$('purge_month').selectedIndex].value;
			var pyear = $('purge_year').options[$('purge_year').selectedIndex].value;
			var pars = "purge=1&manager=1&mid=<?php echo $admin->admin_id; ?>&pday="+pday+"&pmonth="+pmonth+"&pyear="+pyear;			
			pars = pars + get_to_date() + get_from_date();			
			var myAjax = new Ajax.Updater(
				updatebox, 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		// PRINT WORKBOX - ONLY NEEDED IF THERE WAS A SELECTION FOR THIS PAGE OR ALL PAGES (CURRENTLY NOT USED)
		function print_wb(){
			workboxobj = new Object();
			workboxobj.mode = 'print_box';
			workboxobj.page = '<?php echo $page; ?>&tester=1';
			workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
			workboxobj.supportid = '<?php echo $supportPageID; ?>';
			workbox(workboxobj);
		}
		
		// CREATE THE PRINT WINDOW AND INVOKE PRINTING
		function prep_printing(){
			var print_details = new Object();
			print_details.updatecontent = 'print_window_inner';
			print_details.loadpath = 'mgr.activity.log.php';
			print_details.pars = 'displaymode=print&manager=1&mid=<?php echo $admin->admin_id; ?>';
			print_details.pars = print_details.pars + get_to_date() + get_from_date();
			do_printing(print_details);
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
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # OUTPUT MESSAGE IF ONE EXISTS
            verify_message($vmessage);
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" id="saveid" value="<?php echo $admin->uadmin_id; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.admin.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['admin_new_header'] : $mgrlang['admin_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['admin_new_message'] : $mgrlang['admin_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div>    
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['admin_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2" <?php if($_GET['edit'] == "new"){ echo "style='border-right: 1px solid #d8d7d7;'"; } ?>><?php echo $mgrlang['admin_tab2']; ?></div>
                    <?php if($_GET['edit'] != "new"){ ?><div class="subsuboff" onclick="bringtofront('3');load_al(0);" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['admin_tab3']; ?></div><?php } ?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="username_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="username" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['admin_f_username']; ?>: <br />
                            <span><?php echo $mgrlang['admin_f_username_d']; ?></span>
                       	</p>
                        <input type="text" name="username" id="username" style="width: 300px;" maxlength="17" value="<?php echo @stripslashes($username); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="password_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="password2" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['admin_f_password']; ?>: <br />
                            <span><?php echo $mgrlang['admin_f_password']; ?></span>
                        </p>
						<?php
                            if($_GET['edit'] != "new"){ 
                        ?>
                            <div id="password_div2"><input type="text" name="password2" id="password" style="width: 236px;" maxlength="50" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<$mgrlang[admin_hidden]>"; } else { echo "<encrypted>"; } ?>" disabled /> <?php if($_SESSION['admin_user']['admin_id'] != "DEMO"){ echo "<a href='javascript:unencrypt_pass();' class='actionlink' style='position: absolute; margin-top: 5px;'>$mgrlang[admin_unencrypt]</a><!--<input type='button' class='small_button' value='$mgrlang[admin_unencrypt]' onclick='unencrypt_pass();'>-->"; } ?></div>
                        <?php
                            } else {
                        ?>
                            <input type="text" name="password" id="password" style="width: 300px;" maxlength="50" value="" />
                        <?php	
                            }
                        ?>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="email_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['admin_f_email']; ?>: <br />
                            <span><?php echo $mgrlang['admin_f_email_d']; ?></span>
                        </p>
                        <input type="text" name="email" id="email" style="width: 300px;" maxlength="250" value="<?php echo @stripslashes($email); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                    	<img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['admin_f_active']; ?>: <br />
                            <span><?php echo $mgrlang['admin_f_active_d']; ?></span>
                        </p>
                        <input type="hidden" name="superadmin" value="<?php if($admin->superadmin){ echo "1"; } ?>" /><input type="checkbox" name="active" value="1" <?php if(@!empty($active) or $_GET['edit'] == "new"){ echo "checked"; } if($admin->superadmin){ echo " disabled='disabled'"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['admin_f_perm']; ?>: <br />
                            <span><?php echo $mgrlang['admin_f_perm_d']; ?></span>
                        </p>
                        <div id="permtable" style="float: left; white-space: nowrap; margin-right: 30px;">
							<?php
                                # HIDE OPTIONS THAT ARE SUPERADMIN ONLY
                                if($admin->superadmin != 1){
									$hide_nav[] = "software_upgrade";
								}
                                
                                $lperm_array = explode(",",$admin->permissions);
                                # DISABLE THE CHECKBOXES IS THIS IS A SUPERADMIN OR IF IT IS AN ADMIN
                                $disabled = (!empty($admin->superadmin) or $_SESSION['admin_user']['admin_id'] == $admin->admin_id) ? " disabled" : "";
                                # LOOP THRU THE NAV AND SUBNAV ARRAY
                                $x = 0;
                                foreach($comp as $key => $value){
                                    $x++;
                                    $y = 0;
                                    if(!in_array($value['nav_id'], $hide_nav)){
                                        echo "<div style='clear: both; overflow: auto; width: 550px; border: 1px solid #d0d0d0; margin-bottom: 10px; padding-bottom: 15px;'>";
											echo "<div style='background-color: #eee; padding: 4px 0 10px 10px;'><input type=\"checkbox\" class='checkbox' id=\"group_$x\" name=\"group[]\" onclick=\"check_subgroup($x);\" value=\"$value[nav_id]\" ";
                                       		if(in_array($value['nav_id'],$lperm_array) or !empty($admin->superadmin)) echo "checked";
                                        	echo $disabled;
                                        	echo " /><label for=\"group_$x\">&nbsp;&nbsp;<strong>$value[nav_name]</strong></label></div><br style='clear: both;' />";
                                        # MAKE SURE THE SUBNAV IS AN ARRAY FIRST
                                        if(is_array($value['subnav'])){
                                            foreach($value['subnav'] as $key2 => $value2){
                                                $y++;
                                                if(!in_array($value2['nav_id'], $hide_nav)){
                                                    echo "<div style='padding-left: 20px; float: left; width: 130px;'><input type=\"checkbox\" onclick=\"check_group($x,$y);\" id=\"subgroup_" . $x . "_" . $y . "\" name=\"group[]\" value=\"$value2[nav_id]\" ";
                                                    if(in_array($value2['nav_id'],$lperm_array) or !empty($admin->superadmin)) echo "checked";
                                                    echo $disabled;
                                                    echo " />&nbsp;&nbsp;<label for=\"subgroup_" . $x . "_" . $y . "\">$value2[subnav_name]</label></div>";
                                                }
                                            }
										echo "<div align=\"right\" style=\"padding-right: 15px;\"><a href=\"javascript:select_all_group($x);\">$mgrlang[admin_f_all]</a> | <a href=\"javascript:select_none_group($x);\">$mgrlang[admin_f_none]</a></div>";
                                    	}
										echo "</div>";
									}
                                }
                            ?>
                    	</div>
                        <div style="clear: both; height: 4px;">&nbsp;</div>
                    </div>							
                </div>
                
                <div id="tab3_group" class="group" style="display: none;">							
                    <div id="activity_log"></div>
                </div>
                
            </div>
            <div id="save_bar">
                <?php if(in_array('pro',$installed_addons)){ ?><input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.administrators.php');" /><?php } ?><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>