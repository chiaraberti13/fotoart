<?php
	###################################################################
	####	MEDIA TYPES EDIT AREA                                  ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "licenses";
		$lnav = "library";
		
		$supportPageID = '364';
	
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
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$licenseresult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses WHERE license_id = '$_GET[edit]'");
			$licenserows = mysqli_num_rows($licenseresult);
			$license = mysqli_fetch_object($licenseresult);
		}
		
		# DELETE ANY OLD RM RECORDS
		if($_GET['edit'] == "new")
		{
			$rmGroupResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_option_grp WHERE license_id = 0");
			while($rmGroup = mysqli_fetch_assoc($rmGroupResult))
			{
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_options WHERE og_id = '{$rmGroup[og_id]}'");
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_ref WHERE group_id = '{$rmGroup[og_id]}'");
			}
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_option_grp WHERE license_id = 0");
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":							
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				if(in_array('multilang',$installed_addons))
				{
					foreach(array_unique($active_langs) as $value)
					{ 
						$name_val = ${"name_" . $value};
						$addsql.= "lic_name_$value='$name_val',";
						
						$description_val = ${"description_" . $value};
						$addsql.= "lic_description_$value='$description_val',";
					}
				}
				
				$rmBasePriceClean = $cleanvalues->currency_clean($rmBasePrice);
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}licenses SET 
							lic_name='$name',";						
				$sql.= $addsql;
				$sql.= "lic_description='$description',lic_purchase_type='$purchase_type',rm_base_price='$rmBasePriceClean',rm_base_credits='$rmBaseCredits',rm_base_type='$rmBaseType',attachlicense='$attachlicense'";						
				$sql.= " where license_id  = '$saveid'";
				
				//echo $sql; exit; // testing
				
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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_types'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.licenses.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				$rmBasePriceClean = $cleanvalues->currency_clean($rmBasePrice);
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				if(in_array('multilang',$installed_addons))
				{
					foreach(array_unique($active_langs) as $value)
					{
						$name_val = ${"name_" . $value};
						$addsqla.= ",lic_name_$value";
						$addsqlb.= ",'$name_val'";						
						$description_val = ${"description_" . $value};
						$addsqla.= ",lic_description_$value";
						$addsqlb.= ",'$description_val'";
					}
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}licenses (
						lic_name,
						lic_description,
						lic_purchase_type,
						rm_base_price,
						rm_base_credits,
						rm_base_type,
						attachlicense";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$description',
						'$purchase_type',
						'$rmBasePriceClean',
						'$rmBaseCredits',
						'$rmBaseType',
						'$attachlicense'";
				$sql.= $addsqlb;
				$sql.= ")";
				//echo $sql; exit;
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
				
				# SAVE ANY RM RECORDS
				mysqli_query($db,"UPDATE {$dbinfo[pre]}rm_option_grp SET license_id ='$saveid' WHERE license_id = 0");		
				
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_types'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.licenses.php?mes=new"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_types']; ?></title>
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
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.licenses.edit.php?action=save_new" : "mgr.licenses.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","licenses_f_name",1);			
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
			
			$('rmConfigButton').observe('click', function()
			{
				workbox2({page: 'mgr.workbox.php',pars: 'box=rmWorkbox&licID=<?php echo $_GET['edit']; ?>'});
			});
			
		});
		
		
		// RM Pricing
		function deleteRM(delType,delID)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
			?>
				demo_message2();
			<?php
				}
				else
				{
			?>			
				var updatecontent = 'rmListContainer';
				var loadpage = "mgr.rights.managed.actions.php";
				var pars = "amode=deleteRM&licID=<?php echo $_GET['edit']; ?>&delType="+delType+"&delID="+delID;
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});			
				showRMCategoryContainer();			
			<?php
				}
			?>
		}
		
		function showRMCategoryContainer(){
			show_div('rmListContainer');
			hide_div('rmOptionGroupContainer');
			hide_div('rmOptionContainer');
			
			show_loader('rmListContainer');
			
			$('rmamode').value = 'saveRMList';
			$('rmOptionGroupButton').update('<?php echo $mgrlang['gen_new_op_grp'] ; ?>');
			$('rmOptionButton').update('<?php echo $mgrlang['gen_new_option'] ; ?>');
					
			$('rmListButton').className = 'subsubon';
			$('rmOptionGroupButton').className = 'subsuboff';
			$('rmOptionButton').className = 'subsuboff';
			
			var updatecontent = 'rmListContainer';
			var loadpage = "mgr.rights.managed.php?pmode=rmList&licID=<?php echo $_GET['edit']; ?>";
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
		}
		
		function rmEditOptionGroup(ogID){
			hide_div('rmListContainer');
			show_div('rmOptionGroupContainer');
			hide_div('rmOptionContainer');
			
			$('rmamode').value = 'saveOptionGroup';
			
			$('rmOptionButton').update('<?php echo $mgrlang['gen_new_option'] ; ?>');
			
			if(ogID == 'new')
				$('rmOptionGroupButton').update('<?php echo $mgrlang['gen_new_op_grp'] ; ?>');
			else
				$('rmOptionGroupButton').update('<?php echo $mgrlang['gen_edit_op_grp'] ; ?>');
			
			show_loader('rmOptionGroupContainer');
					
			$('rmListButton').className = 'subsuboff';
			$('rmOptionGroupButton').className = 'subsubon';
			$('rmOptionButton').className = 'subsuboff';
			
			var updatecontent = 'rmOptionGroupContainer';
			var loadpage = "mgr.rights.managed.php?pmode=editOptionGroup&licID=<?php echo $_GET['edit']; ?>&optionGroupID="+ogID;
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
		}
		
		function rmEditOption(opID){
			hide_div('rmListContainer');
			hide_div('rmOptionGroupContainer');
			show_div('rmOptionContainer');
			
			$('rmamode').value = 'saveOption';
			
			$('rmOptionGroupButton').update('<?php echo $mgrlang['gen_new_op_grp'] ; ?>');
			
			if(opID == 'new')
				$('rmOptionButton').update('<?php echo $mgrlang['gen_new_option'] ; ?>');
			else
				$('rmOptionButton').update('<?php echo $mgrlang['gen_edit_option'] ; ?>');
			
			show_loader('rmOptionContainer');
					
			$('rmListButton').className = 'subsuboff';
			$('rmOptionGroupButton').className = 'subsuboff';
			$('rmOptionButton').className = 'subsubon';
			
			var updatecontent = 'rmOptionContainer';
			var loadpage = "mgr.rights.managed.php?pmode=editOption&licID=<?php echo $_GET['edit']; ?>&optionID="+opID;
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
		}
		
		function updatePurchaseType()
		{
			var selecteditem = $('purchase_type').options[$('purchase_type').selectedIndex].value;
			if(selecteditem == 'rm')
				$('rmConfig').show();
			else
				$('rmConfig').hide();
		}
		
		function saveRMOptions()
		{
			//alert($F('rmamode'));
			
			switch($F('rmamode'))
			{
				case 'saveRMList':
					//$('rmBasePrice').setValue($('rmFauxBasePrice').value);
					close_workbox();
				break;
				case 'saveOptionGroup':
					
					$('rmConfigForm').request({
						onFailure: function() { alert('failed'); }, 
						onSuccess: function(transport) {
							showRMCategoryContainer();
						}
					});
					
				break;
				case 'saveOption':
				
					$('rmConfigForm').request({
						onFailure: function() { alert('failed'); }, 
						onSuccess: function(transport) {
							showRMCategoryContainer();
						}
					});
				
				break;
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
			<input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.licenses.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['licenses_new_header'] : $mgrlang['licenses_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['licenses_new_message'] : $mgrlang['licenses_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
				<?php
					# PULL GROUPS
					$licensegroup_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$licensegroup_rows = mysqli_num_rows($licensegroup_result);
				?>
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['licenses_tab1']; ?></div>
                    <?php if($licensegroup_rows){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['licenses_tab2']; ?></div><?php } ?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['licenses_f_name']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['licenses_f_name_d']; ?></span></p>
                        
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($license->lic_name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_name','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($license->{"lic_name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
                            <?php echo $mgrlang['gen_description']; ?>:<br />
                            <span><?php echo $mgrlang['licenses_f_description_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 300px; height: 50px; vertical-align: middle"><?php echo @stripslashes($license->lic_description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons))
                                {
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <!--<br /><a href="javascript:displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.gif" id="plusminus02" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_description','','','','plusminus-02');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />-->
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value)
                                    {
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($license->{"lic_description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
                            <?php echo $mgrlang['gen_option_h_type']; ?>:<br />
                            <span><?php echo $mgrlang['licenses_f_handle_d']; ?></span>
                        </p>
						<div style="float: left;">
							<select name="purchase_type" id="purchase_type" onChange="updatePurchaseType();">
								<option value="rf" <?php if($license->lic_purchase_type == 'rf'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_reg_license']; ?></option>
								<option value="cu" <?php if($license->lic_purchase_type == 'cu'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_cu']; ?></option>
								<option value="fr" <?php if($license->lic_purchase_type == 'fr'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['mem_download_free']; ?></option>
								<?php if(in_array('pro',$installed_addons)){ ?><option value="rm" <?php if($license->lic_purchase_type == 'rm'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_rm']; ?></option><?php } ?>
							</select>
						</div>
						
						<div style='border: 1px solid #d8d7d7; border-top: 2px solid #d8d7d7; border-bottom: 2px solid #c5c5c5; background-color: #EEE; float: left; padding: 10px 20px 20px 20px; margin: 0; display: <?php if($license->lic_purchase_type == 'rm'){ echo 'block'; } else { echo 'none'; } ?>' id="rmConfig">
							<input type='radio' name='rmBaseType' value="mp" id="rmBaseTypeMP" onclick="$('rmBasePriceContainer').hide();" <?php if($license->rm_base_type == 'mp' or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> /> <label for="rmBaseTypeMP"><?php echo $mgrlang['rm_custom_mp']; ?></label><br />
							<input type='radio' name='rmBaseType' value="cp" id="rmBaseTypeCP" onclick="$('rmBasePriceContainer').show();" <?php if($license->rm_base_type == 'cp'){ echo "checked='checked'"; } ?> /> <label for="rmBaseTypeCP"><?php echo $mgrlang['rm_custom_bc']; ?></label><br />
							<table style="background-color: #EEE; margin-top: 10px; display: <?php if($license->rm_base_type == 'mp' or $_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; }?>" id="rmBasePriceContainer">
								<?php
									if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
									{
								?>
									<tr>
										<td><strong><?php echo $mgrlang['gen_base_price']; ?></strong></td><td><input type="text" name="rmBasePrice" id="rmBasePrice" onblur="update_input_cur('rmBasePrice');" value="<?php echo $cleanvalues->currency_display($license->rm_base_price,0); ?>" style='width: 50px;' /></td>
									</tr>
								<?php
									}
									if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
									{
								?>
									<tr>
										<td><strong><?php echo $mgrlang['gen_base_credits']; ?></strong></td><td><input type="text" name="rmBaseCredits" id="rmBaseCredits" value="<?php echo $license->rm_base_credits; ?>" style='width: 50px;' /></td>
									</tr>
								<?php
									}
								?>
							</table>
							<input type="button" value="<?php echo $mgrlang['rm_cps']; ?>" id="rmConfigButton" style="float: right; margin-top: 20px;" />
						
						</div>
						
						<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['licenses_attach']; ?>:<br />
                            <span><?php echo $mgrlang['licenses_attach_d']; ?></span>
                        </p>
						<div style="float: left;">
							<select name="attachlicense" id="attachlicense">
								<option value="0" <?php if($licenseids->content_id == $license->attachlicense){ echo "selected='selected'"; } ?>><?PHP echo $mgrlang['gen_t_none']; ?></option>
								<?php
								//FIND LIST OF LICENSES
								$license_result = mysqli_query($db,"SELECT name,content_id FROM {$dbinfo[pre]}content WHERE ca_id = '8' AND active = '1'");
								while($licenseids = mysqli_fetch_object($license_result))
								{
									?>
									<option value="<?PHP echo $licenseids->content_id; ?>" <?php if($licenseids->content_id == $license->attachlicense){ echo "selected='selected'"; } ?>><?php echo $licenseids->name; ?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
                </div>
                
                <?php
            	if($licensegroup_rows)
				{
						$row_color = 0;
				?>
					<div id="tab2_group" class="group"> 
						<div class="<?php fs_row_color(); ?>" id="name_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['licenses_f_groups']; ?>:<br />
								<span><?php echo $mgrlang['licenses_f_groups_d']; ?></span>
							</p>
							<?php
								$plangroups = array();
								# FIND THE GROUPS THAT THIS ITEM IS IN
								$licensegroupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$license->license_id' AND item_id != 0");
								while($licensegroupids = mysqli_fetch_object($licensegroupids_result))
								{
									$plangroups[] = $licensegroupids->group_id;
								}
								echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
								while($licensegroup = mysqli_fetch_object($licensegroup_result))
								{
									echo "<li><input type='checkbox' id='grp_$licensegroup->gr_id' class='permcheckbox' name='setgroups[]' value='$licensegroup->gr_id' "; if(in_array($licensegroup->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($licensegroup->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$licensegroup->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$licensegroup->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$licensegroup->gr_id'>" . substr($licensegroup->name,0,30)."</label></li>";
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
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.licenses.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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