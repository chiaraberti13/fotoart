<?php
	###################################################################
	####	MANAGER SHIPPING ACTIONS                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-20-2009                                     ####
	####	Modified: 12-23-2009                                   #### 
	###################################################################
		//sleep(1);
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "shipping";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		//require_once('mgr.select.settings.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		include_lang();
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SET ACTIVE STATUS
			case "ac":
				$ship_result = mysqli_query($db,"SELECT active FROM {$dbinfo[pre]}shipping where ship_id = '$_REQUEST[id]'");
				$ship = mysqli_fetch_object($ship_result);
				
				# FLIP THE VALUE
				$new_value = (empty($ship->active) ? 1 : 0);	
							
				$sql = "UPDATE {$dbinfo[pre]}shipping SET active='$new_value' where ship_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			case "load_shipping_regions":
				# SELECT REGIONS
				if($_GET['id'] != 'new')
				{
					$sr_array = array();
					$regions_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}regionids WHERE mgrarea='$page' AND item_id  = '$_GET[id]'");
					$regions_rows = mysqli_num_rows($regions_result);
					while($regions = mysqli_fetch_object($regions_result))
					{
						$sr_array[$regions->reg_type . "-" . $regions->reg_id] = "checked='checked'";
						//$shipping_regions->reg_type . "-" . $shipping_regions->reg_id
						//echo $shipping_regions->reg_type . "<br />";
					}
				}
				
				# COUNTRY GROUPS
				$country_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'countries' ORDER BY name");
				$country_group_rows = mysqli_num_rows($country_group_result);
				while($country_group = mysqli_fetch_object($country_group_result))
				{
					echo "<p style='cursor: default; white-space: nowrap'><input type='checkbox' name='region_list[]' value='g-$country_group->gr_id' id='g-$country_group->gr_id' " . $sr_array['g-'.$country_group->gr_id] . " /> &nbsp; ";
					if($country_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$country_group->flagtype' align='absmiddle' /> "; }
					echo "<label for='g-$country_group->gr_id'>$country_group->name</label> <span style='font-weight: normal'>({$mgrlang[gen_group]})</span></p>";	
				}
				if($country_group_rows)
				{
					echo "<br clear='both' /><hr style='margin-top: 15px; border: 0;  width: 98%; color: #f00;background-color: #e3e3e3; height: 1px;' />";
				}
				
				# COUNTRIES
				$country_result = mysqli_query($db,"SELECT country_id,name FROM {$dbinfo[pre]}countries WHERE active='1' AND deleted = '0' ORDER BY name");
				while($country = mysqli_fetch_object($country_result))
				{
					$country_checked = "";
					if($sr_array['c-'.$country->country_id])
					{
						$country_checked = "checked='checked' disabled='disabled'";
					}
					echo "<p style='cursor: default; clear: both;'><input type='checkbox' name='region_list[]' value='c-$country->country_id' id='c-$country->country_id' " . $sr_array['c-'.$country->country_id] . " onclick='subselect(\"c\",$country->country_id)' /> &nbsp; <label for='c-$country->country_id'>$country->name</label></p>";	
					# COUNTRY ZIPS
					$zip_result = mysqli_query($db,"SELECT zipcode_id,zipcode FROM {$dbinfo[pre]}zipcodes WHERE active='1' AND country_id='$country->country_id' ORDER BY zipcode");
					while($zip = mysqli_fetch_object($zip_result))
					{
						echo "<p style='cursor: default; clear: both; padding-left: 20px; font-weight: normal'><input type='checkbox' name='region_list[]' value='z-$zip->zipcode_id' id='z-$zip->zipcode_id' country='$country->country_id' " . $sr_array['z-'.$country->country_id] . " $country_checked /> &nbsp; <label for='z-$zip->zipcode_id'>$zip->zipcode</label></p>";	
					}
					
					# STATES
					$state_result = mysqli_query($db,"SELECT state_id,name FROM {$dbinfo[pre]}states WHERE active='1' AND deleted = '0' AND country_id='$country->country_id' ORDER BY name");
					while($state = mysqli_fetch_object($state_result))
					{
						$state_checked = "";
						if($sr_array['s-'.$state->state_id])
						{
							$state_checked = "checked='checked' disabled='disabled'";
						}
						
						echo "<p style='cursor: default; padding-left: 20px; clear: both; font-weight: normal'><input type='checkbox' name='region_list[]' value='s-$state->state_id' id='s-$state->state_id' country='$country->country_id' " . $sr_array['s-'.$state->state_id] . " onclick='subselect(\"s\",$state->state_id)' $country_checked /> &nbsp; <label for='s-$state->state_id'>$state->name</label></p>";	
						
						# STATE ZIPS
						$zip_result = mysqli_query($db,"SELECT zipcode_id,zipcode FROM {$dbinfo[pre]}zipcodes WHERE active='1' AND deleted = '0' AND state_id='$state->state_id' ORDER BY zipcode");
						while($zip = mysqli_fetch_object($zip_result))
						{
							echo "<p style='cursor: default; padding-left: 40px; clear: both; font-weight: normal'><input type='checkbox' name='region_list[]' value='z-$zip->zipcode_id' id='z$zip->zipcode_id' country='$country->country_id' state='$state->state_id' " . $sr_array['z-'.$zip->zipcode_id] . " $country_checked $state_checked /> &nbsp; <label for='z-$zip->zipcode_id'>$zip->zipcode</label></p>";	
						}
					}
				}
			break; 
			case "regwin": // NO LONGER USED
				$shipping_regions_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping_regions WHERE ship_id  = '$_GET[id]'");
				$shipping_regions_rows = mysqli_num_rows($shipping_regions_result);
				while($shipping_regions = mysqli_fetch_object($shipping_regions_result))
				{
					switch($shipping_regions->reg_type)
					{
						case "g":
							
						break;						
						case "c":
							# COUNTRIES
							$country_result = mysqli_query($db,"SELECT country_id,name FROM {$dbinfo[pre]}countries WHERE country_id='$shipping_regions->reg_id' ORDER BY name");
							while($country = mysqli_fetch_object($country_result))
							{
								echo "<strong>$country->name</strong><br />";
								# SELLECT STATES
								# SELECT ZIPS
							}
						break;
						case "s":
							# COUNTRIES
							$state_result = mysqli_query($db,"SELECT state_id,name FROM {$dbinfo[pre]}states WHERE state_id='$shipping_regions->reg_id' ORDER BY name");
							while($state = mysqli_fetch_object($state_result))
							{
								echo $state->name . "<br />";
								# SELECT ZIPS
							}
						break;
						case "z":
							# ZIPCODES
							$zip_result = mysqli_query($db,"SELECT state_id,name FROM {$dbinfo[pre]}states WHERE state_id='$shipping_regions->reg_id' ORDER BY name");
							while($zip = mysqli_fetch_object($state_result))
							{
								echo $zip->zipcode . "<br />";
							}
						break;
					}
				}
			break;
		}	
?>
