<?php
	###################################################################
	####	COUNTRIES EDIT AREA                                    ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 5-4-2009                                      ####
	####	Modified: 12-22-2009                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "countries";
		$lnav = "settings";
		
		$supportPageID = '371';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
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
			$country_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}countries WHERE country_id = '$_GET[edit]'");
			$country_rows = mysqli_num_rows($country_result);
			$country = mysqli_fetch_object($country_result);
		}
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->decimal_places = 4;
		$cleanvalues->strip_ezeros = 1;
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			
			# SAVE EDIT				
			case "save_edit":				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# FIX NUMBER VALUES
				$longitude = $cleanvalues->number_clean($longitude,'','');
				$latitude = $cleanvalues->number_clean($latitude,'','');
				
				/*
				# DELETE SHIPPING REGIONS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}shipping_regions WHERE region_id = '$saveid' AND region_type = 'country'");
				
				if($allShippingMethods)
					$dbAllShippingMethods = 1;
				else
				{
					$dbAllShippingMethods = 0;
					foreach($shippingMethods as $value)
					{
						# ADD
						$sql = "INSERT INTO {$dbinfo[pre]}shipping_regions (shipping_id,region_id,region_type) VALUES ('{$value}','{$saveid}','country')";
						$result = mysqli_query($db,$sql);	
					}
				}
				*/
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$name_val = ${"name_" . $value};
					$addsql.= "name_$value='$name_val',";
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}countries SET 
							name='$name',
							code2='$code2',
							code3='$code3',";
				$sql.= $addsql;				
				$sql.= "	active='$active',
							numcode='$numcode',
							longitude='$longitude',
							latitude='$latitude',
							region='$region',
							all_ship_methods ='{$dbAllShippingMethods}',
							ship_percentage='{$ship_percentage}'
							WHERE country_id  = '$saveid'";
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
					
					//echo $tax_a; exit;
					
					# CHECK FOR TAX RECORD 1=Countries 2=States 3=Zips
					$tax_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(tax_id) FROM {$dbinfo[pre]}taxes WHERE region_type='1' AND region_id='$saveid'"));
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
							WHERE region_id='$saveid' AND region_type='1'";
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
							tax_inc,
							tax_subs,
							tax_shipping,
							tax_credits,
							tax_ms,
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
							'$tax_inc',
							'$tax_subs',
							'$tax_shipping',
							'$tax_credits',
							'$tax_ms',
							'1',
							'$saveid'
							)";
						$result = mysqli_query($db,$sql);
					}
				}
				
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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_countries'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.countries.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":			
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# FIX NUMBER VALUES
				$longitude = $cleanvalues->number_clean($longitude,'','');
				$latitude = $cleanvalues->number_clean($latitude,'','');
				
				# CREATE COUNTRY ID
				$ucountry_id = create_unique2();
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$name_val = ${"name_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";
				}
				
				if($allShippingMethods)
					$dbAllShippingMethods = 1;
				else
					$dbAllShippingMethods = 0;
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}countries (
						name,
						ucountry_id,
						code2,
						code3,
						active,
						numcode,
						longitude,
						latitude,
						region,
						ship_percentage,
						all_ship_methods";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$ucountry_id',
						'$code2',
						'$code3',
						'$active',
						'$numcode',
						'$longitude',
						'$latitude',
						'$region',
						'{$ship_percentage}',
						'{$dbAllShippingMethods}'
						";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);
				
				/*
				if(!$allShippingMethods)
				{
					$dbAllShippingMethods = 0;
					foreach($shippingMethods as $value)
					{
						# ADD
						$sql = "INSERT INTO {$dbinfo[pre]}shipping_regions (shipping_id,region_id,region_type) VALUES ('{$value}','{$saveid}','country')";
						$result = mysqli_query($db,$sql);	
					}
				}
				*/
				
				# CHECK TO SEE IF WE NEED TO SAVE TAXES
				if($taxes_present)
				{
					# CLEAN NUMBERS
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
						tax_prints,
						tax_digital,
						tax_in_prices,
						tax_subs,
						tax_shipping,
						tax_credits,
						region_type,
						tax_ms,
						region_id
						) VALUES (
						'$tax_a',
						'$tax_b',
						'$tax_c',
						'$tax_prints',
						'$tax_digital',
						'$tax_in_prices',
						'$tax_subs',
						'$tax_shipping',
						'$tax_credits',
						'1',
						'$tax_ms',
						'$saveid'
						)";
					$result = mysqli_query($db,$sql);
				}

				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_countries'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.countries.php?mes=new"); exit;
			break;		
		}
		
		//$cleanvalues->decimal_places = 0;
		//echo $cleanvalues->number_display('1000'); exit;
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_countries']; ?></title>
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
	<!-- COUNTRIES JAVASCRIPT -->
	<script language="javascript">
		function form_submitter(){
			$('name_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.countries.edit.php?action=save_new" : "mgr.countries.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","country_f_title",1);
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
                <img src="./images/mgr.badge.countries.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['country_new_header'] : $mgrlang['country_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['country_new_message'] : $mgrlang['country_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div id="spacer_bar"></div>    
            <?php
				# PULL GROUPS
				$country_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$country_group_rows = mysqli_num_rows($country_group_result);
			?> 
						
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['country_tab1']; ?></div>
                <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['country_tab2']; ?></div>
				<?php if($config['settings']['tax_type'] == 0) { ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['country_tab3']; ?></div><?php } ?>
                <?php if($country_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['country_tab5']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('4');" id="tab4" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['country_tab4']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">                
                <div class="<?php fs_row_color(); ?>" id="name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_title']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_title_d']; ?></span>
                    </p>
                    
                    <div class="additional_langs">
                        <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($country->name); ?>" />
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_title" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($country->{"name" . "_" . $value}); ?>" />&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_code2']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_code2_d']; ?></span>
                    </p>
                    <input type="text" name="code2" id="code2" style="width: 50px;" maxlength="100" value="<?php echo @stripslashes($country->code2); ?>" /> (<a href="http://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">Wikipedia</a>)                   
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_code3']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_code3_d']; ?></span>
                    </p>
                    <input type="text" name="code3" id="code3" style="width: 50px;" maxlength="100" value="<?php echo @stripslashes($country->code3); ?>" /> (<a href="http://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">Wikipedia</a>)                  
                </div>                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_active']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_active_d']; ?></span>
                    </p>
                    <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($country->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab2_group" class="group"> 
                <?php
				/*
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_numcode-']; ?>I Will Ship To This Country: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_numcode_d-']; ?></span>
                    </p>
                    <input type="checkbox" name="willship" id="willship" value="1" <?php if(@!empty($country->willship) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
				*/
				?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_methods']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_methods_d']; ?></span>
                    </p>
                    <ul style="float: left;">
						<!--<li style="border-bottom: 1px dotted #CCC; padding-bottom: 5px;"><input type="checkbox" id="allShippingMethods" name="allShippingMethods" value="1" <?php if($country->all_ship_methods){ echo "checked='checked'"; } ?> /> <label for="allShippingMethods">Allow All</label></li>-->
						<?php
							// Find groups that this country belongs to
							$countryGroupsResult = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$country->country_id' AND item_id != 0");
							while($countryGroup = mysqli_fetch_array($countryGroupsResult))
							{
								$countryGroups[] = $countryGroup['group_id'];
							}
							$countryGroups[] = 0; // Used to fix empty countryGroupsFlat value in query
							$countryGroupsFlat = implode(",",$countryGroups);
							
							
							// Find everywhere regions
							$everywhereShipResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping WHERE region = '1' AND deleted = '0'");
							while($everywhereShip = mysqli_fetch_array($everywhereShipResult))
							{
								$shippingMethods[] = $everywhereShip;
							}
							
							$shippingRegionResult = mysqli_query($db,
							"
								SELECT * FROM {$dbinfo[pre]}regionids 
								LEFT JOIN {$dbinfo[pre]}shipping 
								ON {$dbinfo[pre]}regionids.item_id = {$dbinfo[pre]}shipping.ship_id  
								WHERE {$dbinfo[pre]}regionids.mgrarea='shipping' 
								AND {$dbinfo[pre]}shipping.active = '1' 
								AND {$dbinfo[pre]}shipping.deleted = '0' 
								AND (({$dbinfo[pre]}regionids.reg_type='c' AND {$dbinfo[pre]}regionids.reg_id = '{$country->country_id}') OR ({$dbinfo[pre]}regionids.reg_type='g' AND {$dbinfo[pre]}regionids.reg_id IN ({$countryGroupsFlat})))
							");
							$shippingRegionRows = mysqli_num_rows($shippingRegionResult);
							while($shippingRegion = mysqli_fetch_array($shippingRegionResult))
							{
								//echo $shippingRegion['title']."<br />";								
								$shippingMethods[] = $shippingRegion;
							}

							foreach($shippingMethods as $key => $value)
							{
								//if(!in_array($value['ship_id'],$alreadyShown)){ echo "<li style='padding-bottom: 4px;'>{$value[title]}</li>"; }
								//$alreadyShown[] = $value['ship_id'];
								echo "<li style='padding-bottom: 4px;'>{$value[title]}</li>";
							}
							
							//print_r($countryGroups);
							
							/*
							$shippingRegionResult = mysqli_query($db,
							"
								SELECT * FROM {$dbinfo[pre]}regionids 
								LEFT JOIN {$dbinfo[pre]}shipping 
								ON {$dbinfo[pre]}regionids.item_id = {$dbinfo[pre]}shipping.ship_id  
								WHERE {$dbinfo[pre]}regionids.mgrarea='shipping' 
								AND {$dbinfo[pre]}regionids.reg_type='c' 
								AND {$dbinfo[pre]}regionids.reg_id = '{$country->country_id}'
							");
							$shippingRegionRows = mysqli_num_rows($shippingRegionResult);
							while($shippingRegion = mysqli_fetch_array($shippingRegionResult))
							{
								$shippingRegions[] = $shippingRegion['item_id'];
								
								if($shippingRegion['region'] == 1)
									$shippingRegionsEverywhere[$shippingRegion['item_id']] = 1;
							}
							
							print_r($shippingRegionsEverywhere);
							
							$shippingMethodResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping WHERE deleted = 0");
							$shippingMethodRows = mysqli_num_rows($shippingMethodResult);
							while($shippingMethod = mysqli_fetch_array($shippingMethodResult))
							{
								echo "<li><input type='checkbox' name='shippingMethods[]' id='shippingMethod{$shippingMethod[ship_id]}'";
								if(in_array($shippingMethod['ship_id'],$shippingRegions)){ echo " checked='checked'"; }
								if($shippingRegionsEverywhere[$shippingMethod['ship_id']]){ echo " disabled='disabled'"; }
								echo " value='{$shippingMethod[ship_id]}' /> <label for='shippingMethod{$shippingMethod[ship_id]}'>{$shippingMethod[title]}</label></li>";	
							}
							//echo $shippingRegionRows;
							*/
						?>
					</ul>                
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_adj']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_adj_d']; ?></span>
                    </p>
                    <input type="text" name="ship_percentage" value="<?php if($_GET['edit'] == "new"){ echo "100"; } else { echo $country->ship_percentage; } ?>" style="width: 50px;" /> %            
                </div>
            </div>
            
            <?php
            	if($config['settings']['tax_type'] == 0)
				{ 
					$row_color = 0;
					# READ TAX DB
					if($_GET['edit'] != "new")
					{
						$tax_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}taxes WHERE region_id = '$_GET[edit]' AND region_type = '1'");
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
                        <?php echo $mgrlang['country_f_tax_a']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_a_d']; ?></span>
                    </p>
                    <input type="text" name="tax_a" id="tax_a" style="width: 50px;" onblur="update_input_num('tax_a',3,1);" maxlength="100" value="<?php if($tax->tax_a){ echo @$cleanvalues->number_display($tax->tax_a,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>       
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_tax_b']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_b_d']; ?></span>
                    </p>
                    <input type="text" name="tax_b" id="tax_b" style="width: 50px;" onblur="update_input_num('tax_b',3,1);" maxlength="100" value="<?php if($tax->tax_b){ echo @$cleanvalues->number_display($tax->tax_b,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_tax_c']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_c_d']; ?></span>
                    </p>
                    <input type="text" name="tax_c" id="tax_c" style="width: 50px;" onblur="update_input_num('tax_c',3,1);" maxlength="100" value="<?php if($tax->tax_c){ echo @$cleanvalues->number_display($tax->tax_c,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_tax_prints']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_prints_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_prints" id="tax_prints" value="1" <?php if(@!empty($tax->tax_prints)){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_tax_shipping']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_shipping_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_shipping" id="tax_shipping" value="1" <?php if(@!empty($tax->tax_shipping)){ echo "checked"; } ?> />
                </div>
				<div class="fs_header"><?php echo $mgrlang['tax_dit']; ?> <span style="font-style:italic; color: #999; font-weight: normal"><?php echo $mgrlang['tax_dit_d']; ?></span></div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_tax_a']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_a_d']; ?></span>
                    </p>
                    <input type="text" name="tax_a_digital" id="tax_a_digital" style="width: 50px;" onblur="update_input_num('tax_a_digital',3,1);" maxlength="100" value="<?php if($tax->tax_a){ echo @$cleanvalues->number_display($tax->tax_a_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>       
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_tax_b']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_b_d']; ?></span>
                    </p>
                    <input type="text" name="tax_b_digital" id="tax_b_digital" style="width: 50px;" onblur="update_input_num('tax_b_digital',3,1);" maxlength="100" value="<?php if($tax->tax_b_digital){ echo @$cleanvalues->number_display($tax->tax_b_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_tax_c']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_c_d']; ?></span>
                    </p>
                    <input type="text" name="tax_c_digital" id="tax_c_digital" style="width: 50px;" onblur="update_input_num('tax_c_digital',3,1);" maxlength="100" value="<?php if($tax->tax_c_digital){ echo @$cleanvalues->number_display($tax->tax_c_digital,3,''); } ?>" /> % <?php $cleanvalues->example_number_text(7,7.5); ?>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['tax_f_taxmem']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_ms_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_ms" id="tax_ms" value="1" <?php if(@!empty($tax->tax_ms)){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_tax_digital']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_digital_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_digital" id="tax_digital" value="1" <?php if(@!empty($tax->tax_digital)){ echo "checked"; } ?> />
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_tax_subs']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_subs_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_subs" id="tax_subs" value="1" <?php if(@!empty($tax->tax_subs)){ echo "checked"; } ?> />
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['country_f_tax_credits']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['country_f_tax_credits_d']; ?></span>
                    </p>
                    <input type="checkbox" name="tax_credits" id="tax_credits" value="1" <?php if(@!empty($tax->tax_credits)){ echo "checked"; } ?> />
                </div>
            </div>
            <?php
				}
			?>
            
            <?php $row_color = 0; ?>
            <div id="tab4_group" class="group"> 
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_numcode']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_numcode_d']; ?></span>
                    </p>
                    <input type="text" name="numcode" id="numcode" style="width: 50px;" maxlength="100" value="<?php echo @stripslashes($country->numcode); ?>" /> (<a href="http://en.wikipedia.org/wiki/ISO_3166-1_numeric" target="_blank">Wikipedia</a>)                 
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_longitude']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_longitude_d']; ?></span>
                    </p>
                    <input type="text" name="longitude" id="longitude" style="width: 50px;" maxlength="100" onblur="update_input_num('longitude',2,1);" value="<?php if($country->longitude){ echo @$cleanvalues->number_display($country->longitude,'',''); } ?>" />                   
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_latitude']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_latitude_d']; ?></span>
                    </p>
                    <input type="text" name="latitude" id="latitude" style="width: 50px;" maxlength="100" onblur="update_input_num('latitude',2,1);" value="<?php if($country->latitude){ echo @$cleanvalues->number_display($country->latitude,'',''); } ?>" />                   
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['country_f_region']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['country_f_region_d']; ?></span>
                    </p>
                    <input type="text" name="region" id="region" style="width: 50px;" maxlength="100" value="<?php echo @stripslashes($country->region); ?>" />                   
                </div>
            </div>
            
            <?php
            	if($country_group_rows)
				{
					$row_color = 0;
			?>
                <div id="tab5_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['country_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['country_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$country_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$country->country_id' AND item_id != 0");
							while($country_groupids = mysqli_fetch_object($country_groupids_result))
							{
								$plangroups[] = $country_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($country_group = mysqli_fetch_object($country_group_result))
							{
								echo "<li><input type='checkbox' id='grp_$country_group->gr_id' class='permcheckbox' name='setgroups[]' value='$country_group->gr_id' "; if(in_array($country_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($country_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$country_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$country_group->gr_id'>" . substr($country_group->name,0,30)."</label></li>";
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
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.countries.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>