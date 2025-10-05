<?php
	###################################################################
	####	MEMBERSHIPS EDIT AREA		                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 6-4-2008                                      ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "memberships";
		$lnav = "users";
		
		$supportPageID = '346';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE			
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		require_once('mgr.defaultcur.php');								# INCLUDE DEFAULT CURRENCY SETTINGS	
		
			
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$membership_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}memberships WHERE ms_id = '$_GET[edit]'");
			$membership_rows = mysqli_num_rows($membership_result);
			$membership = mysqli_fetch_object($membership_result);
		}
		
		# CREATE NEW NUMBER FORMATING OBJECT
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults();
		$cleanvalues->set_cur_defaults();
		
		
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" ){
							
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			# SET PERM
			if(empty($perm) or $perm == 'everyone'){
				$everyone = "1";
			} else {
				$everyone = '0';
			}
			
			# CLEAN PRICES
			$price = $cleanvalues->currency_clean($price);
			$setupfee = $cleanvalues->currency_clean($setupfee);
		
			if($_REQUEST['action'] == "save_edit"){							

				if($signin_groups)
					$signin_groups_imploded = implode(",",$signin_groups);
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach(array_unique($active_langs) as $value){ 
					$name_val = ${"name_" . $value};
					$description_val = ${"description_" . $value};
					$addsql.= "name_$value='$name_val',";
					$addsql.= "description_$value='$description_val',";
				}
				
				if(!$allow_uploads) $allow_selling = 0;
				
				if($file_types)
					$file_types = implode(",",$file_types);
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}memberships SET
							name='$name',
							flagtype='$flagtype',";
				$sql.= $addsql;				
				$sql.= "	description='$description',
							notes='$notes',
							taxable='$taxable',
							mstype='$mstype',
							trail_status='$trail_status',
							trial_length_num='$trial_length_num',
							trial_length_period='$trial_length_period',
							setupfee='$setupfee',
							price='$price',
							period='$period',
							active='$active',
							everyone='$everyone',
							gallery_suggestions='$gallery_suggestions',
							media_requests='$media_requests',
							bio='$bio',
							bio_approval='$bio_approval',
							avatar='$avatar',
							avatar_approval='$avatar_approval',
							lightboxes='$lightboxes',
							lightboxes_num='$lightboxes_num',
							comments='$comments',
							comment_approval='$comment_approval',
							rating='$rating',
							rating_approval='$rating_approval',
							tagging='$tagging',
							tagging_approval='$tagging_approval',
							allow_uploads='$allow_uploads',
							msfeatured='$msfeatured',
							disk_space='$disk_space',
							personal_galleries='$personal_galleries',
							admin_galleries='$admin_galleries',
							editing='$editing',
							contr_digital='$contr_digital',
							editing_approval='$editing_approval',
							deleting='$deleting',
							approval='$approval',
							fs_min='$fs_min',
							fs_max='$fs_max',
							res_min='$res_min',
							res_max='$res_max',
							file_types='$file_types',
							portfolio='$portfolio',
							searches='$searches',
							allow_selling='$allow_selling',
							commission='$commission',
							admin_products='$admin_products',
							admin_prints='$admin_prints',
							additional_sizes='$additional_sizes',
							collections='$collections',
							signin_groups='$signin_groups_imploded',
							contr_col='{$contr_col}'
							WHERE ms_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_memberships'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.memberships.php?mes=edit"); exit;
			}
			
			if($_REQUEST['action'] == "save_new"){

				
				# CREATE A UNIQUE MEMBERSHIP ID
				$ums_id = create_unique2();
				
				# CLEAN PRICES
				//$price = $cleanvalues->currency_clean($price);
				//$setupfee = $cleanvalues->currency_clean($setupfee);
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach(array_unique($active_langs) as $value){ 
					$name_val = ${"name_" . $value};
					$description_val = ${"description_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				$file_types = implode(",",$file_types);
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}memberships (
						name,
						ums_id,
						flagtype,
						description,
						notes,
						taxable,
						mstype,
						trail_status,
						trial_length_num,
						trial_length_period,
						setupfee,
						price,
						period,
						active,
						everyone,
						gallery_suggestions,
						media_requests,
						bio,
						bio_approval,
						avatar,
						avatar_approval,
						lightboxes,
						lightboxes_num,
						comments,
						comment_approval,
						rating,
						rating_approval,
						tagging,
						tagging_approval,
						allow_uploads,
						disk_space,
						personal_galleries,
						admin_galleries,
						editing,
						editing_approval,
						deleting,
						approval,
						fs_min,
						fs_max,
						res_min,
						res_max,
						file_types,
						portfolio,
						searches,
						allow_selling,
						commission,
						contr_digital,
						admin_products,
						admin_prints,
						additional_sizes,
						collections,
						contr_col,
						signin_groups";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$ums_id',
						'$flagtype',
						'$description',
						'$notes',
						'$taxable',
						'$mstype',
						'$trail_status',
						'$trial_length_num',
						'$trial_length_period',
						'$setupfee',
						'$price',
						'$period',
						'$active',
						'$everyone',
						'$gallery_suggestions',
						'$media_requests',
						'$bio',
						'$bio_approval',
						'$avatar',
						'$avatar_approval',
						'$lightboxes',
						'$lightboxes_num',
						'$comments',
						'$comment_approval',
						'$rating',
						'$rating_approval',
						'$tagging',
						'$tagging_approval',
						'$allow_uploads',
						'$disk_space',
						'$personal_galleries',
						'$admin_galleries',
						'$editing',
						'$editing_approval',
						'$deleting',
						'$approval',
						'$fs_min',
						'$fs_max',
						'$res_min',
						'$res_max',
						'$file_types',
						'$portfolio',
						'$searches',
						'$allow_selling',
						'$commission',
						'$contr_digital',
						'$admin_products',
						'$admin_prints',
						'$additional_sizes',
						'$collections',
						'$contr_col',
						'$signin_groups_imploded'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_memberships'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.memberships.php?mes=new"); exit;
			}
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_memberships']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
    <!-- LOAD THE SLIDER STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.slider.css" />
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
    <!-- LOAD SLIDER CODE -->
    <script type="text/javascript" src="../assets/javascript/slider.js"></script>	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
	<script language="javascript" type="text/javascript">	
		function form_submitter(){
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.memberships.edit.php?action=save_new" : "mgr.memberships.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","membership_f_name",1);
				}
			?>
			
			//if(!$('active').checked && $('display').checked)
			//{
			//	alert('It is not recommended that you display a plan that is inactive');
			//}
			//display/active
			
		}
		
		// RUN ON PAGE LOAD
		Event.observe(window, 'load', function()
		{
			 load_r_box(0,'flagbox','mgr.flagtypes.php');
			
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
		
		// MEMBERSHIP TYPE SELECT
		function mstypesel(mstypeval)
		{
			switch(mstypeval)
			{
				case "free":
					$('trial_div').hide();
					$('cost_div').hide();
					$('taxable_div').hide();
					//$('cost_div').removeClassName('fs_row_off');
					//$('cost_div').addClassName('fs_row_on');
				break;
				case "onetime":
					$('trial_div').show();
					$('cost_div').show();
					$('taxable_div').show();
					
					$('price_header').hide();
					$('period_header').hide();
					$('price_p').hide();
					$('period_p').hide();
					
					//$('cost_div').addClassName('fs_row_off');
				break;
				case "recurring":
					$('trial_div').show();
					$('cost_div').show();
					$('taxable_div').show();
					
					$('price_header').show();
					$('period_header').show();
					$('price_p').show();
					$('period_p').show();
					//$('cost_div').addClassName('fs_row_off');
				break;
			}
		}
		
		// TRIAL SELECT
		function trialstatsel()
		{
			if($('trail_status').options[$('trail_status').selectedIndex].value == '0')
			{
				$('trial_details').hide();
			}
			else
			{
				$('trial_details').show();
			}
		}
		
		
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
			<?php
				if($_GET['edit'] == 1)
					echo "<input type='hidden' name='mstype' value='free' />";
			?>
				
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.membership.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['membership_new_header'] : $mgrlang['membership_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['membership_new_message'] : $mgrlang['membership_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>        
                <?php
					# PULL GROUPS
					$membership_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$membership_group_rows = mysqli_num_rows($membership_group_result);
				?> 
                <div id="button_bar">                    
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['membership_tab1']; ?></div>
                    <?php if($_GET['edit'] != 1){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['membership_tab2']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['membership_tab5']; ?></div>
                    <?php if(in_array("contr",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('7');" id="tab7"><?php echo $mgrlang['gen_tab_contributors']; ?></div><?php } ?>
					<?php if($membership_group_rows){ ?><div class="subsuboff" onclick="bringtofront('6');" <?php if($_GET['edit'] == 'new'){ echo "style='border-right: 1px solid #d8d7d7;'"; } ?> id="tab6"><?php echo $mgrlang['membership_tab6']; ?></div><?php } ?>
                    <?php if($_GET['edit'] != 'new'){ ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3""><?php echo $mgrlang['membership_tab3']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group">                    
                    
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['membership_f_name']; ?>:<br />
                        	<span><?php echo $mgrlang['membership_f_name_d']; ?></span>
                        </p>
                        
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="50" value="<?php echo @stripslashes($membership->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($membership->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['membership_f_description']; ?>:<br />
                        	<span><?php echo $mgrlang['membership_f_description_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" style="width: 300px; height: 75px; vertical-align: middle"><?php echo @stripslashes($membership->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_short','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_short" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 300px; height: 75px; vertical-align: middle"><?php echo @stripslashes($membership->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    
                    <?php 
						if($_GET['edit'] != "new"){
					?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_directlink']; ?>:<br />
                                <span><?php echo $mgrlang['membership_f_directlink_d']; ?></span>
                            </p>
                            <span class="groupinfo"><em><a href="<?php echo $config['settings']['site_url']; ?>/create.account.php?msID=<?php echo $membership->ums_id; ?>" target="_blank"><?php echo $config['settings']['site_url']; ?>/create.account.php?msID=<?php echo $membership->ums_id; ?></a></em></span>
                        </div>
                    <?php
						}
					?>
                    
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="flagtype" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['membership_f_flag']; ?>:<br />
                        	<span><?php echo $mgrlang['membership_f_flag_d']; ?></span>
                       	</p>                       
                        <div style="float: left;">
                        	<input type="radio" value="icon.none.gif" name="flagtype" id="none" <?php if($_GET['edit'] == "new" or $membership->flagtype == "icon.none.gif"){ echo "checked"; } ?> /> <?php echo $mgrlang['gen_none']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                        	<input type="radio" value="<?php if($_GET['edit'] == "new"){ echo "icon.003.gif"; } else { echo $membership->flagtype; } ?>" name="flagtype" id="icon" <?php if($_GET['edit'] != "new" and $membership->flagtype != "icon.none.gif"){ echo "checked"; } ?> /> <img src="images/mini_icons/<?php if($_GET['edit'] == "new" or $membership->flagtype == "icon.none.gif"){ echo "icon.003.gif"; } else { echo $membership->flagtype; } ?>" align="absmiddle" id="flagswap" />
                        	<br /><br />
                        	<div id="flagbox"></div>
                        </div>
                    </div>
                    
                    <?php
						if($membership->ms_id != '1' or $_GET['edit'] == "new")
						{
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_active']; ?>: <br />
                                <span class="input_label_subtext"><?php echo $mgrlang['membership_f_active_d']; ?></span>
                            </p>
                            <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($membership->active) or $_GET['edit'] == "new"){ echo "checked='checked'"; } ?> />
                        </div>
                    <?php
						}
						else
						{
							echo "<input type='hidden' name='active' id='active' value='1' />";
							echo "<input type='hidden' name='display' id='active' value='1' />";
						}
					?>
            	</div> 
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group">                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_ms_type']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_ms_type_d']; ?></span>
                        </p>
                        <div style="float: left; line-height: 2;">
                        	<input type="radio" name="mstype" value="free" id="free" onclick="mstypesel('free');" <?php if($_GET['edit'] == 'new' or $membership->mstype == 'free'){ echo "checked='checked'"; } ?> /> <label for="free"><?php echo $mgrlang['membership_f_ms_type_op1']; ?></label><br />
                        	<input type="radio" name="mstype" value="onetime" id="onetime" onclick="mstypesel('onetime');" <?php if($membership->mstype == 'onetime'){ echo "checked='checked'"; } ?> /> <label for="onetime"><?php echo $mgrlang['membership_f_ms_type_op2']; ?></label><br />
                        	<input type="radio" name="mstype" value="recurring" id="recurring" onclick="mstypesel('recurring');" <?php if($membership->mstype == 'recurring'){ echo "checked='checked'"; } ?> /> <label for="recurring"><?php echo $mgrlang['membership_f_ms_type_op3']; ?></label>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="trial_div" <?php if($_GET['edit'] == 'new' or $membership->mstype == 'free'){ echo "style='display: none'"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_trial']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_trial_d']; ?></span>
                        </p>
                        <div style="float: left;">
                            <select name="trail_status" id="trail_status" onchange="trialstatsel();">
                                <option value="0" <?php if($membership->trail_status == '0'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op1']; ?></option>
                                <option value="1" <?php if($membership->trail_status == '1'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op2']; ?></option>
                            </select>
                        </div>
                        <div style="float: left; margin-left: 10px; <?php if($membership->trail_status == '0'){ echo "display: none;"; } ?>" id="trial_details">
                        	<input type="text" name="trial_length_num" style="width: 70px;" value="<?php echo $membership->trial_length_num; ?>" />
                            <select name="trial_length_period">
                                <option value="days" <?php if($membership->trial_length_period == 'days'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op3']; ?></option>
                                <option value="weeks" <?php if($membership->trial_length_period == 'weeks'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op4']; ?></option>
                                <option value="months" <?php if($membership->trial_length_period == 'months'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op5']; ?></option>
                                <option value="years" <?php if($membership->trial_length_period == 'years'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_op6']; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="cost_div" <?php if($_GET['edit'] == 'new' or $membership->mstype == 'free'){ echo "style='display: none'"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_cost']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_cost_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<div style="padding-bottom: 4px; overflow: auto;">
                            	<p style="width: 120px; margin: 0 2px 0 0; padding: 0;"><?php echo $mgrlang['membership_f_setupfee']; ?></p>
                                <p style="width: 120px; margin: 0 2px 0 0; padding: 0;<?php if($membership->mstype == 'onetime'){ echo "display: none"; } ?>" id="price_header"><?php echo $mgrlang['membership_f_cost_h1']; ?></p>
                                <p style="width: 120px; margin: 0 2px 0 0; padding: 0;<?php if($membership->mstype == 'onetime'){ echo "display: none"; } ?>" id="period_header"><?php echo $mgrlang['membership_f_cost_h2']; ?></p>
                            </div>
                            <div style="clear: left;">
                                <p style="width: 120px; margin: 0 2px 0 0; padding: 0;"><input type="text" name="setupfee" id="setupfee"  value="<?php echo $cleanvalues->currency_display($membership->setupfee); ?>" style="width: 100px;" onblur="update_input_cur('setupfee');" /></p>
                                <p style="width: 120px; margin: 0 2px 0 0; padding: 0;<?php if($membership->mstype == 'onetime'){ echo "display: none"; } ?>" id="price_p"><input type="text" name="price" id="price" value="<?php echo $cleanvalues->currency_display($membership->price); ?>" style="width: 100px;" onblur="update_input_cur('price');" /></p>
                                <p style="width: 120px; margin: 0 2px 0 0; padding: 0;<?php if($membership->mstype == 'onetime'){ echo "display: none"; } ?>" id="period_p">
                                    <select name="period">
                                        <option value="weekly" <?php if($membership->period == 'weekly'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_cost_op1']; ?></option>
                                        <option value="monthly" <?php if($membership->period == 'monthly'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_cost_op2']; ?></option>
                                        <option value="quarterly" <?php if($membership->period == 'quarterly'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_cost_op3']; ?></option>
                                        <option value="semi-annually" <?php if($membership->period == 'semi-annually'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_cost_op4']; ?></option>
                                        <option value="annually" <?php if($membership->period == 'annually'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['membership_f_cost_op5']; ?></option>
                                    </select>
                                </p>
                            </div>
                        </div>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="taxable_div">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_taxable']; ?>:<br />
							<span><?php echo $mgrlang['gen_taxable_d']; ?></span>
						</p>
						<input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($membership->taxable){ echo "checked"; } ?> />
					</div>                
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_ms_mems']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_ms_mems_d']; ?></span>
                        </p>
                        <?php
							$member_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE membership = '$membership->ms_id'"));
						?>
                        <strong><?php echo $member_rows; ?></strong><?php if($member_rows > 0 and in_array('members',$_SESSION['admin_user']['permissions'])){ echo " <a href='mgr.members.php?dtype=memberships&setms=".$membership->ms_id."'>[".$mgrlang['membership_f_showmem']."]</a>"; } ?>
                    </div>
                    <!--                  
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Revenue: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d-']; ?>Revenue from this membership plan.</span>
                        </p>
                        <strong>0</strong>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Overdue Members: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d-']; ?>Members who's invoice is overdue.</span>
                        </p>
                        <strong>0</strong>
                    </div> 
                    -->                   
            	</div>
               
				<?php $row_color = 0; ?>
                <div id="tab4_group" class="group">                    
                	 <?php
						if($membership->ms_id == '1')
						{
							echo "<input type='hidden' name='perm' value='everyone' />";
						}
						else
						{
					?>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_f_perm']; ?>: <br />
                            <span><?php echo $mgrlang['gen_f_perm_d']; ?></span>
                        </p>
                        <?php
							if($_GET['edit'] != 'new' and $membership->everyone == '0'){
								$perms_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}perms WHERE perm_area = '$page' AND item_id = '$_GET[edit]'");
								$perms_rows = mysqli_num_rows($perms_result);
								if($perms_rows){
									while($perms = mysqli_fetch_object($perms_result)){
										$perm_value.= ','.$perms->perm_value;
									}
								} else {
									$perm_value = 'everyone';
								}
							} else {
								$perm_value = 'everyone';
							}
						?>
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($membership->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($membership->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $membership->ms_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $membership->ms_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" align="middle" /></a></div>
                    </div>
					<?php
						}
					?>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['int_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['int_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" id="notes" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($membership->notes); ?></textarea>
                    </div>                  
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_bio']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_bio_d']; ?></span>
                        </p>
                        <input type="checkbox" name="bio" id="bio" value="1" <?php if(@!empty($membership->bio)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('bio','bio_approval');" />
                        <select style="margin-left: 20px; display: <?php if($membership->bio == 1){ echo "inline"; } else { echo "none"; } ?>" name="bio_approval" id="bio_approval">
                        	<option value="0" <?php if($membership->bio_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                            <option value="1" <?php if($membership->bio_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_avatar']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_avatar_d']; ?></span>
                        </p>
                        <input type="checkbox" name="avatar" id="avatar" value="1" <?php if(@!empty($membership->avatar)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('avatar','avatar_approval');" />
                        <select style="margin-left: 20px; display: <?php if($membership->avatar == 1){ echo "inline"; } else { echo "none"; } ?>" name="avatar_approval" id="avatar_approval">
                        	<option value="0" <?php if($membership->avatar_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                            <option value="1" <?php if($membership->avatar_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                        </select>
                    </div>
                    <?php
						if(in_array("lightbox",$installed_addons) and $config['settings']['lightbox']){
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_lightboxes']; ?>: <br />
                                <span><?php echo $mgrlang['membership_f_lightboxes_d']; ?></span>
                            </p>
                            <input type="checkbox" name="lightboxes" id="lightboxes" value="1" <?php if(@!empty($membership->lightboxes)){ echo "checked='checked'"; } ?> />
                        </div>
                    <?php
						}
						if(in_array("commenting",$installed_addons) and $config['settings']['comment_system']){
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_comments']; ?>: <br />
                                <span><?php echo $mgrlang['membership_f_comments_d']; ?></span>
                            </p>
                            <input type="checkbox" name="comments" id="comments" value="1" <?php if(@!empty($membership->comments)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('comments','comment_approval');" />
                            <select style="margin-left: 20px; display: <?php if($membership->comments == 1){ echo "inline"; } else { echo "none"; } ?>" name="comment_approval" id="comment_approval">
                                <option value="0" <?php if($membership->comment_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                <option value="1" <?php if($membership->comment_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                            </select>
                        </div>
                    <?php
						}
						if(in_array("rating",$installed_addons) and $config['settings']['rating_system']){
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_rating']; ?>: <br />
                                <span><?php echo $mgrlang['membership_f_rating_d']; ?></span>
                            </p>
                            <input type="checkbox" name="rating" id="rating" value="1" <?php if(@!empty($membership->rating)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('rating','rating_approval');" />
                            <select style="margin-left: 20px; display: <?php if($membership->rating == 1){ echo "inline"; } else { echo "none"; } ?>" name="rating_approval" id="rating_approval">
                                <option value="0" <?php if($membership->rating_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                <option value="1" <?php if($membership->rating_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                            </select>
                        </div>
                    <?php
						}
						if(in_array("tagging",$installed_addons) and $config['settings']['tagging_system']){
					?>
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['membership_f_tagging']; ?>: <br />
                                <span><?php echo $mgrlang['membership_f_tagging_d']; ?></span>
                            </p>
                            <input type="checkbox" name="tagging" id="tagging" value="1" <?php if(@!empty($membership->tagging)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('tagging','tagging_approval');" />
                            <select style="margin-left: 20px; display: <?php if($membership->tagging == 1){ echo "inline"; } else { echo "none"; } ?>" name="tagging_approval" id="tagging_approval">
                                <option value="0" <?php if($membership->tagging_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                <option value="1" <?php if($membership->tagging_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                            </select>
                        </div>
                    <?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_memgroups']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_memgroups_d']; ?></span>
                        </p>
                        <?php
							$global_signup_groups = explode(",",$config['settings']['signup_groups']);
							$signin_groups = explode(",",$membership->signin_groups);
							
							$mem_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'members' ORDER BY name");
							$mem_group_rows = mysqli_num_rows($mem_group_result);
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($mem_group = mysqli_fetch_object($mem_group_result)){
								echo "<li"; if(in_array($mem_group->gr_id,$global_signup_groups)){ echo " style='color: #9d9d9d'"; } echo "><input type='checkbox' id='sg_$mem_group->gr_id' class='permcheckbox' name='signin_groups[]' value='$mem_group->gr_id' "; if(in_array($mem_group->gr_id,$global_signup_groups) or in_array($mem_group->gr_id,$signin_groups)){ echo "checked='checked'"; } if(in_array($mem_group->gr_id,$global_signup_groups)){ echo "disabled='disabled'"; } echo "/> "; if($mem_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' /> "; } echo "<label for='sg_$mem_group->gr_id'>" . substr($mem_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
						?>
                    </div>
                    <?php
						/*
						<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_coupons']; ?>: <br />
                            <span><?php echo $mgrlang['galleris_f_coupons_d']; ?></span>
                        </p>
                    </div>
					
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_galsuggest']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_galsuggest_d']; ?></span>
                        </p>
                        <input type="checkbox" name="gallery_suggestions" id="gallery_suggestions" value="1" <?php if(@!empty($membership->gallery_suggestions)){ echo "checked='checked'"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_medrequest']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_medrequest_d']; ?></span>
                        </p>
                        <input type="checkbox" name="media_requests" id="media_requests" value="1" <?php if(@!empty($membership->media_requests)){ echo "checked='checked'"; } ?> />
                    </div> 
						*/
					?>
            	</div>
                
                <?php	
					if($membership_group_rows){
						$row_color = 0;
				?>
                <div id="tab6_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['membership_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['membership_f_groups_d']; ?></span>
                        </p>
                        <?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$membership_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$membership->ms_id' AND item_id != 0");
							while($membership_groupids = mysqli_fetch_object($membership_groupids_result)){
								$plangroups[] = $membership_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($membership_group = mysqli_fetch_object($membership_group_result)){
								echo "<li><input type='checkbox' id='$membership_group->gr_id' class='permcheckbox' name='setgroups[]' value='$membership_group->gr_id' "; if(in_array($membership_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($membership_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$membership_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$membership_group->flagtype' align='absmiddle' /> "; } echo "<label for='$membership_group->gr_id'>".substr($membership_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
                        ?>
                    </div>
            	</div> 
                <?php
					}
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['membership_f_featured']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_featured_d']; ?></span>
                        </p>
                        <input type="checkbox" name="msfeatured" id="msfeatured" value="1" <?php if(@!empty($membership->msfeatured)){ echo "checked='checked'"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['membership_f_auploads']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_auploads_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" onclick="cb_bool('allow_uploads','upload_ro','upload_ops');cb_bool('allow_uploads','selling_div','');" name="allow_uploads" id="allow_uploads" <?php if($membership->allow_uploads){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($membership->allow_uploads){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="upload_ro"><a href="javascript:show_div('upload_ops');hide_div('upload_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                        <div id="upload_ops" class="related_options" style="width: 500px;">
                            <div style="position: absolute; right: 4px;"><a href="javascript:hide_div('upload_ops');show_div('upload_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_diskspace']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_diskspace_d']; ?></span>
                                </p>
                                <input type="text" name="disk_space" style="width: 70px;" value="<?php if($membership->disk_space){ echo $membership->disk_space; } else { echo "9999"; } ?>" />&nbsp;<span style="font-size: 11px; color: #333;"><?php echo $mgrlang['membership_f_mb']; ?></span>
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_album']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_album_d']; ?></span>
                                </p>
                                <input type="checkbox" name="personal_galleries" id="personal_galleries" value="1" <?php if(@!empty($membership->personal_galleries)){ echo "checked='checked'"; } ?> />
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_admingal']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_admingal_d']; ?></span>
                                </p>
                                <input type="checkbox" name="admin_galleries" id="admin_galleries" value="1" <?php if(@!empty($membership->admin_galleries)){ echo "checked='checked'"; } ?> />
                            </div>
                            
                            
                            
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_approval']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_approval_d']; ?></span>
                                </p>                                
                                <select name="approval" id="approval">
                                    <option value="0" <?php if($membership->approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                    <option value="1" <?php if($membership->approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                                </select>
                            </div>
                            
                            
                            
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_allowedit']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_allowedit_d']; ?></span>
                                </p>
                                <input type="checkbox" name="editing" id="editing" value="1" <?php if(@!empty($membership->editing)){ echo "checked='checked'"; } ?> />
                                <?php
									/* onclick="cb_bool_inline('editing','editing_approval');"
                                <select style="margin-left: 20px; display: <?php if($membership->editing == 1){ echo "inline"; } else { echo "none"; } ?>" name="editing_approval" id="editing_approval">
                                    <option value="0" <?php if($membership->editing_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                    <option value="1" <?php if($membership->editing_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                                </select>
									*/
								?>
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_allowdel']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_allowdel_d']; ?></span>
                                </p>
                                <input type="checkbox" name="deleting" id="deleting" value="1" <?php if(@!empty($membership->deleting)){ echo "checked='checked'"; } ?> />
                                <?php
									/* onclick="cb_bool_inline('deleting','deleting_approval');"
                                <select style="margin-left: 20px; display: <?php if($membership->deleting == 1){ echo "inline"; } else { echo "none"; } ?>" name="deleting_approval" id="deleting_approval">
                                    <option value="0" <?php if($membership->deleting_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                    <option value="1" <?php if($membership->deleting_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                                </select>
									*/
								?>
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_acfilesizes']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_acfilesizes_d']; ?></span>
                                </p>
                                <div style="float: left; font-size: 11px;"><strong><?php echo $mgrlang['membership_f_min']; ?></strong><br /><input type="text" name="fs_min" value="<?php if($membership->fs_min){ echo $membership->fs_min; } else { echo "0"; } ?>" style="width: 50px;" /> <?php echo $mgrlang['membership_f_mb']; ?></div><div style="float: left; font-size: 11px; margin-left: 10px;"><strong><?php echo $mgrlang['membership_f_max']; ?></strong><br /><input type="text" name="fs_max" value="<?php if($membership->fs_max){ echo $membership->fs_max; } else { echo "100"; } ?>" style="width: 50px;" /> <?php echo $mgrlang['membership_f_mb']; ?></div>
                            </div>
							<?php /*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_res']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_res_d']; ?></span>
                                </p>
                                <div style="float: left; font-size: 11px;"><strong><?php echo $mgrlang['membership_f_min']; ?></strong><br /><input type="text" name="res_min" value="<?php if($membership->res_min){ echo $membership->res_min; } else { echo "0"; } ?>" style="width: 50px;" /> <?php echo $mgrlang['membership_f_px']; ?></div><div style="float: left; font-size: 11px; margin-left: 10px;"><strong><?php echo $mgrlang['membership_f_max']; ?></strong><br /><input type="text" name="res_max" value="<?php if($membership->res_max){ echo $membership->res_max; } else { echo "10000"; } ?>" style="width: 50px;" /> <?php echo $mgrlang['membership_f_px']; ?></div>
                            </div>
							*/ ?>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_aft']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_aft_d']; ?></span>
                                </p>
                                <div style="float: left; width: 200px;">
                                	<?php
										$file_types = explode(",",$membership->file_types);
									
										foreach(getAlldTypeExtensions() as $value)
										{
											echo "<div style='float: left; width: 60px;'><input type='checkbox' name='file_types[]' value='$value' id='$value'"; if($_GET['edit'] == 'new' or in_array($value,$file_types)){ echo "checked='checked'"; } echo " /> <label for='$value'>$value</label></div>";	
										}
									?>
                                </div>
                            </div>
							<?php
							/*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_portfolio']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_portfolio_d']; ?></span>
                                </p>
                                <input type="checkbox" name="portfolio" id="portfolio" value="1" <?php if(@!empty($membership->portfolio)){ echo "checked='checked'"; } ?> />
                            </div>
							*/
							?>
                            <!--
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_iis']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_iis_d']; ?></span>
                                </p>
                                <input type="checkbox" name="searches" id="searches" value="1" <?php if(@!empty($membership->searches)){ echo "checked='checked'"; } ?> />
                            </div>
                            -->
                        </div>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>" id="selling_div" <?php if(!$membership->allow_uploads){ echo "style='display: none;'"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['membership_f_allowsell']; ?>: <br />
                            <span><?php echo $mgrlang['membership_f_allowsell_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" onclick="cb_bool('allow_selling','selling_ro','selling_ops');" name="allow_selling" id="allow_selling" <?php if($membership->allow_selling){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($membership->allow_selling){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="selling_ro"><a href="javascript:show_div('selling_ops');hide_div('selling_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                        <div id="selling_ops" class="related_options" style="width: 500px;">
                            <div style="position: absolute; right: 4px;"><a href="javascript:hide_div('selling_ops');show_div('selling_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <?php
								/*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_commission']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_commission_d']; ?></span>
                                </p>
                                <select name="commission" id="commission">
                                    <option value="0" <?php if($membership->commission == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_f_commission_op1']; ?></option>
                                    <option value="1" <?php if($membership->commission == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_f_commission_op2']; ?></option>
                                    <option value="2" <?php if($membership->commission == 2){ echo "selected"; } ?>><?php echo $mgrlang['membership_f_commission_op3']; ?></option>
                                </select>
                                <div style="display: inline; margin-left: 5px;"><input type="text" style="width: 50px;" name="com_value" id="com_val" /> (USD)</div>
                            </div>
								*/
							?>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_commission']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_commission_d']; ?></span>
                                </p>
                                <?php
									# SLIDER POSITION
									//$config['settings']['avatar_size']
									$sb_multiplier = (135/100);
									$sb_position = round($membership->commission*$sb_multiplier);
								?>
                                <div style="margin-top: 10px;">
                                    <div class="carpe_horizontal_slider_track" style="width: 145px">
                                        <div class="carpe_slider_slit" style="width: 140px">&nbsp;</div>
                                        <div class="carpe_slider"
                                            id="commission_slider"
                                            orientation="horizontal"
                                            distance="135"
                                            display="disthumbslider"
                                            style="left: <?php echo $sb_position; ?>px;" >&nbsp;</div><!-- HERE IS WHERE YOU CAN DEFINE THE STARTING POINT -->
                                    </div>
                                    <div class="carpe_slider_display_holder" style="display: inline; white-space: nowrap">
                                        <input class="carpe_slider_display"
                                            id="disthumbslider"
                                            name="commission"
                                            type="text" 
                                            from="0" 
                                            to="100" 
                                            valuecount="60"
                                            value="<?php echo $membership->commission; ?>"
                                            name="avatar_size" 
                                            typelock="off"
                                            slide_action="preview"
                                            drop_action="render_preview" />&nbsp;%
                                    </div>
                                </div>
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_pricing']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_pricing_d']; ?></span>
                                </p>                                
                                <select name="contr_col" id="contr_col">
                                    <option value="0" <?php if($membership->contr_col == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_contrap']; ?></option>
                                    <option value="1" <?php if($membership->contr_col == 1){ echo "selected"; } ?>><?php echo $mgrlang['gen_adminap2']; ?></option>
                                </select>
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_sdf']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_sdf_d']; ?></span>
                                </p>                                
                                <input type="checkbox" name="contr_digital" id="contr_digital" value="1" <?php if(@!empty($membership->contr_digital)){ echo "checked='checked'"; } ?> />
                            </div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_asizes']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_asizes_d']; ?></span>
                                </p>
                                <input type="checkbox" name="additional_sizes" id="additional_sizes" value="1" <?php if(@!empty($membership->additional_sizes)){ echo "checked='checked'"; } ?> />
                            </div>
                            <?php
								/*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_cprod']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_cprod_d']; ?></span>
                                </p>                                
                                <input type="checkbox" name="contr_products" id="contr_products" value="1" <?php if(@!empty($membership->contr_products)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('contr_products','contr_products_approval');" />
                                <select style="margin-left: 20px; display: <?php if($membership->contr_products == 1){ echo "inline"; } else { echo "none"; } ?>" name="contr_products_approval" id="contr_products_approval">
                                    <option value="0" <?php if($membership->contr_products_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                    <option value="1" <?php if($membership->contr_products_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                                </select>                                
                            </div>
								*/
							?>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_aprod']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_aprod_d']; ?></span>
                                </p>
                                <input type="checkbox" name="admin_products" id="admin_products" value="1" <?php if(@!empty($membership->admin_products)){ echo "checked='checked'"; } ?> />
                            </div>
                            <?php
								/*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_cprints']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_cprints_d']; ?></span>
                                </p>
                                <input type="checkbox" name="contr_prints" id="contr_prints" value="1" <?php if(@!empty($membership->contr_prints)){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('contr_prints','contr_prints_approval');" />
                                <select style="margin-left: 20px; display: <?php if($membership->contr_prints == 1){ echo "inline"; } else { echo "none"; } ?>" name="contr_prints_approval" id="contr_prints_approval">
                                    <option value="0" <?php if($membership->contr_prints_approval == 0){ echo "selected"; } ?>><?php echo $mgrlang['membership_manual']; ?></option>
                                    <option value="1" <?php if($membership->contr_prints_approval == 1){ echo "selected"; } ?>><?php echo $mgrlang['membership_auto']; ?></option>
                                </select> 
                            </div>
								*/
							?>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_aprints']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_aprints_d']; ?></span>
                                </p>
                               	<input type="checkbox" name="admin_prints" id="admin_prints" value="1" <?php if(@!empty($membership->admin_prints)){ echo "checked='checked'"; } ?> />
                            </div>
							<?php
								/*
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['membership_f_collect']; ?>:<br />
                                    <span><?php echo $mgrlang['membership_f_collect_d']; ?></span>
                                </p>
                                <input type="checkbox" name="collections" id="collections" value="1" <?php if(@!empty($membership->collections)){ echo "checked='checked'"; } ?> />
                            </div>
								*/
							?>
                        </div>
                    </div>
            	</div>          
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.memberships.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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