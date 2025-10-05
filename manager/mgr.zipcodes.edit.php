<?php
	###################################################################
	####	ZIPCODES EDIT AREA                                     ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 12-22-2009                                    ####
	####	Modified: 12-22-2009                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "zipcodes";
		$lnav = "settings";
		
		$supportPageID = '375';
	
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
			$zipcode_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}zipcodes WHERE zipcode_id = '$_GET[edit]'");
			$zipcode_rows = mysqli_num_rows($zipcode_result);
			$zipcode = mysqli_fetch_object($zipcode_result);
			
			if($zipcode->country_id)
			{
				$zipcodes_region_type = 'c';
				$zipcodes_region_id = $zipcode->country_id;
			}
			else
			{
				$zipcodes_region_type = 's';
				$zipcodes_region_id = $zipcode->state_id;
			}
		}
		else
		{
			if($_GET['region'])
			{
				$zregion = explode(",",$_GET['region']);
				$zipcodes_region_type = $zregion[0];
				$zipcodes_region_id = $zregion[1];
			}
		}
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SAVE EDIT				
			case "save_edit":				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				$zregion = explode(",",$region);
				if($zregion[0] == 'c')
				{
					$country = $zregion[1];
				}
				else
				{
					$state = $zregion[1];
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}zipcodes SET 
							zipcode='$zipcode',
							active='$active',
							country_id='$country',
							state_id='$state',
							ship_percentage='{$ship_percentage}'
							WHERE zipcode_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# CHECK TO SEE IF WE NEED TO SAVE TAXES
				if($taxes_present)
				{				
					# CLEAN NUMBERS					
					$tax_a = $cleanvalues->number_clean($tax_a);
					$tax_b = $cleanvalues->number_clean($tax_b);
					$tax_c = $cleanvalues->number_clean($tax_c);				
					$tax_a_digital = $cleanvalues->number_clean($tax_a_digital);
					$tax_b_digital = $cleanvalues->number_clean($tax_b_digital);
					$tax_c_digital = $cleanvalues->number_clean($tax_c_digital);
					
					# CHECK FOR TAX RECORD 1=Countries 2=States 3=Zips
					$tax_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(tax_id) FROM {$dbinfo[pre]}taxes WHERE region_type='3' AND region_id='$saveid'"));
					if($tax_rows)
					{
						# UPDATE
						$sql = "UPDATE {$dbinfo[pre]}taxes SET 
							tax_a='$tax_a',
							tax_b='$tax_b',
							tax_c='$tax_c',
							tax_a_digital='$tax_a_digital',
							tax_b_digital='$tax_b_digital',
							tax_c_digital='$tax_c_digital',
							tax_prints='$tax_prints',
							tax_digital='$tax_digital',
							tax_ms='$tax_ms',
							tax_inc='$tax_inc',
							tax_subs='$tax_subs',
							tax_shipping='$tax_shipping',
							tax_credits='$tax_credits'
							WHERE region_id='$saveid' AND region_type='3'";
							$result = mysqli_query($db,$sql);
					}
					else
					{
						# ADD
						$sql = "INSERT INTO {$dbinfo[pre]}taxes (
							tax_a,
							tax_b,
							tax_c,
							tax_a_digital,
							tax_b_digital,
							tax_c_digital,
							tax_prints,
							tax_digital,
							tax_ms,
							tax_inc,
							tax_subs,
							tax_shipping,
							tax_credits,
							region_type,
							region_id
							) VALUES (
							'$tax_a',
							'$tax_b',
							'$tax_c',
							'$tax_a_digital',
							'$tax_b_digital',
							'$tax_c_digital',
							'$tax_prints',
							'$tax_digital',
							'$tax_ms',
							'$tax_inc',
							'$tax_subs',
							'$tax_shipping',
							'$tax_credits',
							'3',
							'$saveid'
							)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_zipcodes'],1,$mgrlang['gen_b_ed'] . " > <strong>$zipcode</strong>");
				
				header("location: mgr.zipcodes.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":			
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');

				# CREATE COUNTRY ID
				$uzipcode_id = create_unique2();
				
				$zregion = explode(",",$region);
				if($zregion[0] == 'c')
				{
					$country = $zregion[1];
				}
				else
				{
					$state = $zregion[1];
				}
				//echo $state; exit;
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}zipcodes (
						zipcode,
						uzipcode_id,
						country_id,
						state_id,
						active,
						ship_percentage
						) VALUES (
						'$zipcode',
						'$uzipcode_id',
						'$country',
						'$state',
						'$active',
						'{$ship_percentage}'
						)";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);
				
				# CHECK TO SEE IF WE NEED TO SAVE TAXES
				if($taxes_present)
				{
					# CLEAN NUMBERS					
					$tax_a = $cleanvalues->number_clean($tax_a);
					$tax_b = $cleanvalues->number_clean($tax_b);
					$tax_c = $cleanvalues->number_clean($tax_c);				
					$tax_a_digital = $cleanvalues->number_clean($tax_a_digital);
					$tax_b_digital = $cleanvalues->number_clean($tax_b_digital);
					$tax_c_digital = $cleanvalues->number_clean($tax_c_digital);
					
					# ADD TAX DETAILS
					$sql = "INSERT INTO {$dbinfo[pre]}taxes (
						tax_a,
						tax_b,
						tax_c,
						tax_a_digital,
						tax_b_digital,
						tax_c_digital,
						tax_prints,
						tax_digital,
						tax_ms,
						tax_inc,
						tax_subs,
						tax_shipping,
						tax_credits,
						region_type,
						region_id
						) VALUES (
						'$tax_a',
						'$tax_b',
						'$tax_c',
						'$tax_a_digital',
						'$tax_b_digital',
						'$tax_c_digital',
						'$tax_prints',
						'$tax_digital',
						'$tax_ms',
						'$tax_inc',
						'$tax_subs',
						'$tax_shipping',
						'$tax_credits',
						'3',
						'$saveid'
						)";
					$result = mysqli_query($db,$sql);
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_zipcodes'],1,$mgrlang['gen_b_new'] . " > <strong>$zipcode</strong>");
				
				header("location: mgr.zipcodes.php?mes=new"); exit;
			break;		
		}
		
		//echo $zipcodes_region_type; exit;
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_zipcodes']; ?></title>
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
		function form_submitter()
		{
			$('zipcode_div').className='fs_row_off';
			$('region_div').className='fs_row_on';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					echo "return false;";
				}
				else
				{
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.zipcodes.edit.php?action=save_new" : "mgr.zipcodes.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("zipcode","zipcodes_f_zipcode",1);					
					js_validate_select("region","zipcodes_f_region",1);
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
            demo_message($_SESSION['admin_user']['admin_id']);
			
			# STRIP ZEROS IN NUMBER VALUES
			$cleanvalues->strip_ezeros = 1;
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.zip.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['zipcodes_new_header'] : $mgrlang['zipcodes_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['zipcodes_new_message'] : $mgrlang['zipcodes_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div id="spacer_bar"></div>    
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['zipcodes_tab1']; ?></div>
                <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['zipcodes_tab2']; ?></div>
                <?php if($config['settings']['tax_type'] == 0) { ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['zipcodes_tab3']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('4');" id="tab4" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['zipcodes_tab4']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">                
                <div class="<?php fs_row_color(); ?>" id="zipcode_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_zipcode']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_zipcode_d']; ?></span>
                    </p>
                    <input type="text" name="zipcode" id="zipcode" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($zipcode->zipcode); ?>" />
                </div>
                
                <div class="<?php fs_row_color(); ?>" id="region_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_region']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_region_d']; ?></span>
                    </p>
                    <select style="width: 311px;" align="absmiddle" name="region" id="region" >
                        <option value="#"></option>
                        <?php
							$country_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}countries WHERE active='1' AND deleted='0' ORDER BY name");
							while($country = mysqli_fetch_object($country_result))
							{
								echo "<option value='c,$country->country_id'";
									if($zipcodes_region_id == $country->country_id and $zipcodes_region_type == 'c'){ echo "selected"; }
								echo ">$country->name</option>";
								
								$state_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}states WHERE country_id='$country->country_id' AND deleted='0' ORDER BY name");
								while($state = mysqli_fetch_object($state_result))
								{
									echo "<option value='s,$state->state_id'";
									if($zipcodes_region_id == $state->state_id and $zipcodes_region_type == 's'){ echo "selected"; }
									echo ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$state->name</option>";	
								}
							}
						?>
                    </select>                  
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_active']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_active_d']; ?></span>
                    </p>
                    <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($zipcode->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <?php $row_color = 0; ?>
            <div id="tab2_group" class="group"> 
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_methods']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_methods_d']; ?></span>
                    </p>
                    <ul style="float: left;">
						<?php
							if($_GET['edit'] != 'new')
							{
								if($zipcode->state_id) // Zip is at state level
								{
									$stateResult = mysqli_query($db,"SELECT country_id FROM {$dbinfo[pre]}states WHERE state_id = '{$zipcode->state_id}'"); // Find the country the state is in
									$state = mysqli_fetch_array($stateResult);
									$countryID = $state['country_id'];
								}
								
								if($zipcode->country_id)
									$countryID = $zipcode->country_id;	
								
								// Find groups that the country of this state belongs to
								$countryGroupsResult = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = 'countries' AND item_id = '{$countryID}' AND item_id != 0");
								while($countryGroup = mysqli_fetch_array($countryGroupsResult))
								{
									$countryGroups[] = $countryGroup['group_id'];
								}
								$countryGroups[] = 0; // Used to fix empty countryGroupsFlat value in query
								$countryGroupsFlat = implode(",",$countryGroups);
							}
							
							if(!$countryGroupsFlat) // Fix glitch if there are no country groups
								$countryGroupsFlat = '0';
							
							//echo $countryID."-".$zipcode->state_id;
							
							// Find everywhere regions
							$everywhereShipResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping WHERE region = '1' AND deleted = '0'");
							while($everywhereShip = mysqli_fetch_array($everywhereShipResult))
							{
								$shippingMethods[] = $everywhereShip;
							}
							
							/*
							SELECT * FROM ps4_regionids 
								LEFT JOIN ps4_shipping 
								ON ps4_regionids.item_id = ps4_shipping.ship_id  
								WHERE ps4_regionids.mgrarea='shipping' 
								AND ps4_shipping.active = '1' 
								AND ps4_shipping.deleted = '0' 
								AND (
									 (ps4_regionids.reg_type='z' AND ps4_regionids.reg_id = '') 
									 OR (ps4_regionids.reg_type='g' AND ps4_regionids.reg_id IN ()) 
									 OR (ps4_regionids.reg_type='c' AND ps4_regionids.reg_id = '') 
									 OR (ps4_regionids.reg_type='s' AND ps4_regionids.reg_id = '')
									 )
							*/
							$shippingRegionResult = mysqli_query($db,
							"
								SELECT * FROM {$dbinfo[pre]}regionids 
								LEFT JOIN {$dbinfo[pre]}shipping 
								ON {$dbinfo[pre]}regionids.item_id = {$dbinfo[pre]}shipping.ship_id  
								WHERE {$dbinfo[pre]}regionids.mgrarea='shipping' 
								AND {$dbinfo[pre]}shipping.active = '1' 
								AND {$dbinfo[pre]}shipping.deleted = '0' 
								AND (
									 ({$dbinfo[pre]}regionids.reg_type='z' AND {$dbinfo[pre]}regionids.reg_id = '{$zipcode->zipcode_id}') 
									 OR ({$dbinfo[pre]}regionids.reg_type='g' AND {$dbinfo[pre]}regionids.reg_id IN ({$countryGroupsFlat})) 
									 OR ({$dbinfo[pre]}regionids.reg_type='c' AND {$dbinfo[pre]}regionids.reg_id = '{$countryID}') 
									 OR ({$dbinfo[pre]}regionids.reg_type='s' AND {$dbinfo[pre]}regionids.reg_id = '{$zipcode->state_id}')
									 )
							");
							$shippingRegionRows = mysqli_num_rows($shippingRegionResult);
							while($shippingRegion = mysqli_fetch_array($shippingRegionResult))
							{
								$shippingMethods[] = $shippingRegion;
							}

							if($shippingMethods)
							{
								foreach($shippingMethods as $key => $value)
								{
									//if(!in_array($value['ship_id'],$alreadyShown)){ echo "<li style='padding-bottom: 4px;'>{$value[title]}</li>"; }
									//$alreadyShown[] = $value['ship_id'];
									echo "<li style='padding-bottom: 4px;'>{$value[title]}</li>";
								}
							}
						?>
					</ul>                
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_adj']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_adj_d']; ?></span>
                    </p>
                    <input type="text" name="ship_percentage" value="<?php if($_GET['edit'] == "new"){ echo "100"; } else { echo $zipcode->ship_percentage; } ?>" style="width: 50px;" /> %            
                </div>
            </div>
            
            <?php
            	if($config['settings']['tax_type'] == 0)
				{ 
					$row_color = 0;
					# READ TAX DB
					if($_GET['edit'] != "new")
					{
						$tax_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}taxes WHERE region_id = '$_GET[edit]' AND region_type = '3'");
						$tax_rows = mysqli_num_rows($tax_result);
						$tax = mysqli_fetch_object($tax_result);
					}
			?>
            
			
			<input type="hidden" name="taxes_present" value="1" />
            <div id="tab3_group" class="group">
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['tax_f_inc']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['tax_f_inc_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_inc" id="tax_inc" value="1" <?php if(@!empty($tax->tax_inc)){ echo "checked"; } ?> />
                </div>
				<div class="fs_header"><?php echo $mgrlang['tax_pit']; ?> <span style="font-style:italic; color: #999; font-weight: normal"><?php echo $mgrlang['tax_pit_d']; ?></span></div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_a']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_a_d']; ?></span>
                    </p>
                    <input type="text" name="tax_a" id="tax_a" style="width: 50px;" onblur="update_input_num('tax_a',3,1);" maxlength="100" value="<?php if($tax->tax_a){ echo @$cleanvalues->number_display($tax->tax_a,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>       
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_b']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_b_d']; ?></span>
                    </p>
                    <input type="text" name="tax_b" id="tax_b" style="width: 50px;" onblur="update_input_num('tax_b',3,1);" maxlength="100" value="<?php if($tax->tax_b){ echo @$cleanvalues->number_display($tax->tax_b,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_c']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_c_d']; ?></span>
                    </p>
                    <input type="text" name="tax_c" id="tax_c" style="width: 50px;" onblur="update_input_num('tax_c',3,1);" maxlength="100" value="<?php if($tax->tax_c){ echo @$cleanvalues->number_display($tax->tax_c,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_tax_prints']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_prints_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_prints" id="tax_prints" value="1" <?php if(@!empty($tax->tax_prints)){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_tax_shipping']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_shipping_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_shipping" id="tax_shipping" value="1" <?php if(@!empty($tax->tax_shipping)){ echo "checked"; } ?> />
                </div>
				<div class="fs_header"><?php echo $mgrlang['tax_dit']; ?> <span style="font-style:italic; color: #999; font-weight: normal"><?php echo $mgrlang['tax_dit_d']; ?></span></div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_a']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_a_d']; ?></span>
                    </p>
                    <input type="text" name="tax_a_digital" id="tax_a_digital" style="width: 50px;" onblur="update_input_num('tax_a_digital',3,1);" maxlength="100" value="<?php if($tax->tax_a){ echo @$cleanvalues->number_display($tax->tax_a_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>       
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_b']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_b_d']; ?></span>
                    </p>
                    <input type="text" name="tax_b_digital" id="tax_b_digital" style="width: 50px;" onblur="update_input_num('tax_b_digital',3,1);" maxlength="100" value="<?php if($tax->tax_b_digital){ echo @$cleanvalues->number_display($tax->tax_b_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['zipcodes_f_tax_c']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_c_d']; ?></span>
                    </p>
                    <input type="text" name="tax_c_digital" id="tax_c_digital" style="width: 50px;" onblur="update_input_num('tax_c_digital',3,1);" maxlength="100" value="<?php if($tax->tax_c_digital){ echo @$cleanvalues->number_display($tax->tax_c_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['tax_f_taxmem']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_ms_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_ms" id="tax_ms" value="1" <?php if(@!empty($tax->tax_ms)){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_tax_digital']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_digital_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_digital" id="tax_digital" value="1" <?php if(@!empty($tax->tax_digital)){ echo "checked"; } ?> />
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_tax_subs']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_subs_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_subs" id="tax_subs" value="1" <?php if(@!empty($tax->tax_subs)){ echo "checked"; } ?> />
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['zipcodes_f_tax_credits']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['zipcodes_f_tax_credits_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_credits" id="tax_credits" value="1" <?php if(@!empty($tax->tax_credits)){ echo "checked"; } ?> />
                </div>
            </div>
			<?php
				}
			?>
            
            <?php $row_color = 0; ?>
            <div id="tab4_group" class="group"> 
                
            </div>
            
            	
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.zipcodes.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>