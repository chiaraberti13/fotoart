<?php
	###################################################################
	####	TOOLS/LINKS EDIT AREA                                  ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 4-11-2007                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "toolslinks";
		$lnav = "settings";
		
		$supportPageID = 'index.php?action=artikel&cat=40&id=85&artlang=en';
	
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
		
		# GET THE ACTIVE LANGUAGES
		//$active_langs = explode(",",$config['settings']['lang_file_pub']);
		//$active_langs[] = $config['settings']['lang_file_mgr'];
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$tl_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}toolslinks WHERE tl_id = '$_GET[edit]'");
			$tl_rows = mysqli_num_rows($tl_result);
			$tl = mysqli_fetch_object($tl_result);
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":							
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# DETERMINE OWNER
				if($tl_everyone == '1')
				{
					$tl_owner = '0';
				}
				else
				{
					$tl_owner = $_SESSION['admin_user']['admin_id'];	
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}toolslinks SET 
							tl_name='$name',
							tl_link='$url',
							tl_target='$target',
							tl_owner='$tl_owner'
							where tl_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_toolslinks'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.toolslinks.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# DETERMINE OWNER
				if($tl_everyone == '1')
				{
					$tl_owner = '0';
				}
				else
				{
					$tl_owner = $_SESSION['admin_user']['admin_id'];	
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}toolslinks (
						tl_name,
						tl_link,
						tl_target,
						tl_owner
						) VALUES (
						'$name',
						'$url',
						'$target',
						'$tl_owner'
						)";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_toolslinks'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.toolslinks.php?mes=new"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_toolslinks']; ?></title>
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<script language="javascript">	
		function fixspaces(){
			$('url').value = removeSpaces($('url').value);
		}
		function form_sumbit(){
			// REVERT BACK
			$('name_div').className='fs_row_off';
			$('url_div').className='fs_row_on';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.toolslinks.edit.php?action=save_new" : "mgr.toolslinks.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","tl_f_name",1);
					js_validate_field("url","tl_f_url",1);			
			?>
				// FIX SPACES
				fixspaces();

				//$('data_form').action = "<?php echo $action_link; ?>";
				//$('data_form').submit();
			<?php
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.links.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['tl_new_header'] : $mgrlang['tl_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['tl_new_message'] : $mgrlang['tl_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
        
                <div id="button_bar">
                    <div class="subsubon" id="tab1"><?php echo $mgrlang['tl_tab1']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tl_f_name']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tl_f_name_d']; ?></span></p>
                        <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($tl->tl_name); ?>" />
                    </div>                    
                    <div class="<?php fs_row_color(); ?>" id="url_div">
                        <img src="images/mgr.ast.gif" class="ast" /></td>
						<p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tl_f_url']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tl_f_url_d']; ?></span></p>
                        <input type="text" name="url" id="url" style="width: 300px;" maxlength="300" onblur="fixspaces();" value="<?php echo @stripslashes($tl->tl_link); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tl_f_target']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tl_f_target_d']; ?></span></p>
                        <select name="target" style="width: 308px;" >
                            <option value="_blank" <?php if($tl->tl_target == "_blank"){ echo "selected"; } ?>><?php echo $mgrlang['tl_newwindow']; ?></option>
                            <option value="_self" <?php if($tl->tl_target == "_self"){ echo "selected"; } ?>><?php echo $mgrlang['tl_thiswindow']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="url_div">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
						<p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tl_f_owner']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tl_f_owner_d']; ?></span></p>
                        <input type="checkbox" value="1" name="tl_everyone" <?php if($tl->tl_owner == '0' or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
                </div>
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.toolslinks.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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