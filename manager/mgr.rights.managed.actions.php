<?php
	$page = "licenses";
	$lnav = "library";
		
	require_once '../assets/includes/session.php';
	require_once 'mgr.security.php';	
	require_once 'mgr.config.php';
	require_once '../assets/includes/tweak.php';
	require_once '../assets/includes/db.config.php';
	require_once '../assets/includes/shared.functions.php';
	require_once 'mgr.functions.php';
	error_reporting(0);
	require_once '../assets/includes/db.conn.php';
	require_once 'mgr.select.settings.php';
	include_lang();
	require_once '../assets/includes/addons.php';		
	require_once 'mgr.error.check.php';		
	error_reporting(E_ALL & ~E_NOTICE);			
	require_once '../assets/includes/clean.data.php';
	require_once 'mgr.defaultcur.php';	
	
	if($licID == 'new') $licID = 0; // If the license is new then set the ID to 0
	
	switch($amode)
	{
		case "deleteRM":
			
			if($_SESSION['admin_user']['admin_id'] != "DEMO") // Don't delete in demo mode
			{
				if($delType == 'og') // Option Group
				{
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_option_grp WHERE og_id = {$delID} AND license_id = {$licID}"); // Delete the option group
					
					$optionsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_options WHERE og_id = {$delID} AND license_id = {$licID}");
					while($options = mysqli_fetch_assoc($optionsResult))
					{
						@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_ref WHERE option_id = {$options[op_id]}"); // Delete the references	
					}
					
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_options WHERE og_id = {$delID}"); // Delete the options
					//@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_ref WHERE group_id = {$delID}"); // Delete the references
				}
				elseif($delType == 'op') // Option
				{
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_options WHERE op_id = {$delID} AND license_id = {$licID}"); // Delete the option
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_ref WHERE option_id = {$delID}"); // Delete the references
				}
			}
			
		break;
		
		case "saveOptionGroup":
			
			if($_SESSION['admin_user']['admin_id'] != "DEMO" and $ogName and ($optionGroupID == 'new' or $licID)) // Don't save in demo mode or if fields are empty // 
			{
				if($optionGroupID == 'new')
				{
					// New
					$sql = "INSERT INTO {$dbinfo[pre]}rm_option_grp (
							og_name,
							license_id
							) VALUES (
							'{$ogName}',
							'{$licID}'
							)";
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					
					// Additional Languages
					if(in_array('multilang',$installed_addons))
					{
						foreach(array_unique($active_langs) as $value)
							@mysqli_query($db,"UPDATE {$dbinfo[pre]}rm_option_grp SET og_name_{$value}='".${'ogName_'.$value}."' WHERE og_id = '{$saveid}'");
					}
					
					foreach($ogLinks as $value)
					{
						if($value != 0)
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}rm_ref (group_id,option_id) VALUES ('{$saveid}','{$value}')");
					}
				}
				else
				{
					$sql = "UPDATE {$dbinfo[pre]}rm_option_grp SET 
								og_name='{$ogName}',
								license_id='{$licID}'
								WHERE og_id  = '{$optionGroupID}'";
					$result = mysqli_query($db,$sql);
					
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}rm_ref WHERE group_id = {$optionGroupID}");
					
					// Additional Languages
					if(in_array('multilang',$installed_addons))
					{
						foreach(array_unique($active_langs) as $value)
							@mysqli_query($db,"UPDATE {$dbinfo[pre]}rm_option_grp SET og_name_{$value}='".${'ogName_'.$value}."' WHERE og_id = '{$optionGroupID}'");
					}
					
					foreach($ogLinks as $value)
					{
						if($value != 0)
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}rm_ref (group_id,option_id) VALUES ('{$optionGroupID}','{$value}')");
					}
				}
			}
			
		break;
		
		case "saveOption":
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			$cleanOptionPrice = $cleanvalues->currency_clean($optionPrice);
			
			if($_SESSION['admin_user']['admin_id'] != "DEMO" and $optionName and $licID and $optionOGID) // Don't save in demo mode or if fields are empty
			{
			
				if($optionID == 'new')
				{
					// New
					$sql = "INSERT INTO {$dbinfo[pre]}rm_options (
							op_name,
							license_id,
							og_id,
							price,
							credits,
							price_mod
							) VALUES (
							'{$optionName}',
							'{$licID}',
							'{$optionOGID}',
							'{$cleanOptionPrice}',
							'{$optionCredits}',
							'{$optionPriceModifier}'
							)";
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					
					// Additional Languages
					if(in_array('multilang',$installed_addons))
					{
						foreach(array_unique($active_langs) as $value)
							@mysqli_query($db,"UPDATE {$dbinfo[pre]}rm_options SET op_name_{$value}='".${'optionName_'.$value}."' WHERE op_id = '{$saveid}'");
					}
				}
				else
				{
					// Edit
					$sql = "UPDATE {$dbinfo[pre]}rm_options SET 
								og_id='{$optionOGID}',
								license_id='{$licID}',
								op_name='{$optionName}',
								price='{$cleanOptionPrice}',
								credits='{$optionCredits}',
								price_mod='{$optionPriceModifier}'
								where op_id  = '{$optionID}'";
					$result = mysqli_query($db,$sql);
					
					// Additional Languages
					if(in_array('multilang',$installed_addons))
					{
						foreach(array_unique($active_langs) as $value)
							@mysqli_query($db,"UPDATE {$dbinfo[pre]}rm_options SET op_name_{$value}='".${'optionName_'.$value}."' WHERE op_id = '{$optionID}'");
					}
				}
			}
		break;		
	}
?>