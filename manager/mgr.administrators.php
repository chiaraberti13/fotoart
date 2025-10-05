<?php
	###################################################################
	####	MANAGER ADMINISTRATORS PAGE                            ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 11-28-2006                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "administrators";
		$lnav = "users";
		
		$supportPageID = '342';
	
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
		
		# INCLUDE THE LANGUAGE FILE - MANAGER PART ONLY
		if(file_exists("../assets/languages/lang." . $config['settings']['lang_file'] . ".calendar.php")){ @ include("../assets/languages/lang." . $config['settings']['lang_file'] . ".calendar.php"); } else { @ include("../assets/languages/lang.english.calendar.php"); }
	
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO"){
			$delete_link = "DEMO_";
		} else {
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		if($_GET['mes'] == "new"){
			$vmessage = $mgrlang['admin_mes_01'];
		}
		if($_GET['mes'] == "edit"){
			$vmessage = $mgrlang['admin_mes_02'];
		}
		
		# REDIRECT IF NOT PRO VERSION
		if(!in_array('pro',$installed_addons))
		{
			header("Location: mgr.administrators.edit.php?edit=460B1BDD209A534EF0AF1EVYKHF04EE9");
			exit;
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# DELETE SINGLE
			case "ds":
				# FIND THE ADMIN NAME
				$log_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins WHERE uadmin_id = '$_REQUEST[id]'");
				$log = mysqli_fetch_object($log_result);
				
				$sql="DELETE FROM {$dbinfo[pre]}admins WHERE uadmin_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# DELETE ACTIVITY LOG
				$sql="DELETE FROM {$dbinfo[pre]}activity_log WHERE member_id IN '$log->admin_id'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_administrators'],1,$mgrlang['gen_b_del'] . " > <strong>$log->username</strong>");
				
				# OUTPUT A VERIFICATION MESSAGE
				$vmessage=$mgrlang['admin_mes_03'];	
			break;
			# DELETE MULTIPE
			case "dm":
				if(!empty($_REQUEST['selected_items'])){
					$delete_array = implode(",",$_REQUEST['selected_items']);
					
					# GET TITLES FOR LOG
					$log_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins WHERE admin_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result)){
						$log_titles.= "$log->username, ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", "){
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					$sql="DELETE FROM {$dbinfo[pre]}admins WHERE admin_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# DELETE ACTIVITY LOG
					$sql="DELETE FROM {$dbinfo[pre]}activity_log WHERE member_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_administrators'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
					
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['admin_mes_04'];
				} else {
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['admin_mes_05'];
				}
			break;
		}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_administrators']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
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
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	
    <script language="javascript">
		// ADDED THESE FUNCTIONS TO TRIM DOWN ON HTML OUTPUT CODE AND MAKE THINGS LOOK CLEANER
		function dlform(){
			<?php
				if(!empty($_SESSION['admin_user']['superadmin']) or $_SESSION['admin_user']['admin_id'] == "DEMO"){
			?>
					delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>','form','<?php echo $_SERVER[PHP_SELF] . "?action=dm" ; ?>');
			<?php
				} else {
					echo "simple_message_box('" . $mgrlang['admin_mes_06'] . "');";
				}
			?>
		}		
		function dllink(idnum){
			<?php
				if(!empty($_SESSION['admin_user']['superadmin']) or $_SESSION['admin_user']['admin_id'] == "DEMO"){
			?>
				delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>','link','<?php echo $delete_link; ?>' + idnum);
			<?php
				} else {
					echo "simple_message_box('" . $mgrlang['admin_mes_06'] . "');";
				}
			?>
		}
		
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.administrators.edit.php?edit=new';
					});
				$('abutton_add_new').observe('mouseover', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.png';
					});
				$('abutton_add_new').observe('mouseout', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.off.png';
					});
			}
			
			// SELECT ALL BUTTON
			if($('abutton_select_all')!=null)
			{
				$('abutton_select_all').observe('click', function()
					{
						select_all_cb('datalist');
					});
				$('abutton_select_all').observe('mouseover', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.png';
					});
				$('abutton_select_all').observe('mouseout', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.off.png';
					});
			}
			
			// SELECT NONE BUTTON
			if($('abutton_select_none')!=null)
			{
				$('abutton_select_none').observe('click', function()
					{
						deselect_all_cb('datalist');
					});
				$('abutton_select_none').observe('mouseover', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.png';
					});
				$('abutton_select_none').observe('mouseout', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.off.png';
					});
			}
			
			// DELETE BUTTON
			if($('abutton_delete')!=null)
			{
				$('abutton_delete').observe('click', function()
					{
						dlform();
					});
				$('abutton_delete').observe('mouseover', function()
					{
						$('img_delete').src='./images/mgr.button.delete.png';
					});
				$('abutton_delete').observe('mouseout', function()
					{
						$('img_delete').src='./images/mgr.button.delete.off.png';
					});
			}
			
		});
			
	</script>	
</head>
<body>
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
            <?php
			/*
            <div id="actions_bar">							
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons"><a href="<?php if(!empty($_SESSION['admin_user']['superadmin'])){ echo "mgr.administrators.edit.php?edit=new"; } else { echo "javascript:simple_message_box('" . $mgrlang['admin_mes_07'] . "');"; } ; ?>"><img src="./images/mgr.button.add.news.gif" align="absmiddle" border="0" alt="<?php echo $mgrlang['admin_b_new']; ?>" /></a><br /><?php echo $mgrlang['admin_b_new']; ?></div>
                    <div style="float: left;" class="abuttons"><a href="#"><img src="./images/mgr.button.select.all.gif" align="absmiddle" border="0" alt="<?php echo $mgrlang['admin_b_sa']; ?>" onclick="select_all_cb('datalist');" /></a><br /><?php echo $mgrlang['admin_b_sa']; ?></div>
                    <div style="float: left;" class="abuttons"><a href="#"><img src="./images/mgr.button.select.none.gif" align="absmiddle" border="0" alt="<?php echo $mgrlang['admin_b_sn']; ?>" onclick="deselect_all_cb('datalist');" /></a><br /><?php echo $mgrlang['admin_b_sn']; ?></div>	
                    <div style="float: left;" class="abuttons"><a href="javascript:dlform();"><img src="./images/mgr.button.delete.gif" align="absmiddle" border="0" alt="<?php echo $mgrlang['admin_b_del']; ?>" /></a><br /><?php echo $mgrlang['admin_b_del']; ?></div>
                </div>
            </div>
            */
			?>
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.admin.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_administrators']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.gif" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                    <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
				</div>               
            </div>
            <!-- START CONTENT -->
            <div id="content">							
                <div style="padding: 25px; overflow: auto">
                
                <form name="datalist" id="datalist" method="post">
                    <?php									
                        $adate = new kdate;
                        $adate->distime = 1;
                        $adate->diswords = 1;
                        
                        # SELECT ITEMS
                        $admin_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}admins ORDER BY admin_id");
                        $admin_rows = mysqli_num_rows($admin_result);
						
						//echo $admin_rows; exit;
						
                        while($admin = mysqli_fetch_object($admin_result)){
                        
                            # TRIM EMAIL IF IT IS TOO LONG
                            if(strlen(strip_tags($admin->email)) > 24){
                                $email = substr(strip_tags($admin->email),0,21) . "..."; 
                            } else {
                                $email = strip_tags($admin->email);
                            }	
                        
                    ?>
                        <div class="subnavlist" style="background-color: #FFF; height: 110px; width: 300px; cursor: auto">
                            <div align='right' style="padding: 5px 10px 0px 5px;">
                                <p style="float: left; font-weight: bold; font-size: 14px; color: #5a5a5a; margin: 8px 0px 0px 10px;"><?php echo $admin->username; ?></p>
                                <p style="float: right; margin-top: 5px;">
                                	<a href="<?php if((!empty($_SESSION['admin_user']['superadmin']) or $_SESSION['admin_user']['admin_id'] == "DEMO") or $admin->admin_id == $_SESSION['admin_user']['admin_id']){ echo "mgr.administrators.edit.php?edit=" . $admin->uadmin_id; } else { echo "javascript:simple_message_box('" . $mgrlang['admin_mes_08'] . "');"; } ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="texttop" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                	<?php if(empty($admin->superadmin)){ ?><a href="javascript:dllink('<?php echo $admin->uadmin_id; ?>');" class='actionlink'><img src="images/mgr.icon.delete.png" align="texttop" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a><?php } ?>
                                	<input type="checkbox" name="selected_items[]" value="<?php echo $admin->admin_id; ?>" align='absmiddle' <?php if(!empty($admin->superadmin)){ echo "disabled"; } ?> style="margin: 4px 0 0 10px;" />
                                </p>
                            </div>
                            <!--<div style="clear: both; float: left; margin-top: 15px; width: 62px; height: 55px; cursor: pointer;" onclick="<?php if((!empty($_SESSION['admin_user']['superadmin']) or $_SESSION['admin_user']['admin_id'] == "DEMO") or $admin->admin_id == $_SESSION['admin_user']['admin_id']){ echo "location.href='mgr.administrators.edit.php?edit=" . $admin->uadmin_id . "'"; } else { echo "simple_message_box('" . $mgrlang['admin_mes_08'] . "');"; } ?>"></div>-->
                            <div style="padding: 6px 0px 0px 0px; font-size: 11px; float: left; clear: both;width: 270px;">
                                <img src="images/mgr.admin.icon.png" style="float: left; margin: 0 10px 0 10px" />
                                <span><a href="mailto:<?php echo $admin->email; ?>" class="default"><?php echo $email; ?></a></span><br />
                                <span style="font-weight: bold; color: #b82711;"><?php if(!empty($admin->superadmin)){ echo $mgrlang['admin_superadmin']; } else { echo $mgrlang['admin_admin']; } ?></span><br />
                                <span style="color: #5a5a5a; margin-top: 1px;"><strong><?php echo $mgrlang['admin_last_login']; ?></strong>: <span style="font-size: 10px;"><?php if($admin->last_login == "2006-01-01 11:00:00"){ echo $mgrlang['admin_never']; } else { echo $adate->showdate($admin->last_login); } ?></span>
                            </div>										
                        </div>
                    <?php
                        }
                    ?>
                </form>		
                </div>                	
            </div>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
        </div>
        
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>
