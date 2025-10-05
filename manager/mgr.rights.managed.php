<?php
	$page = "licenses";
	$lnav = "library";
	$supportPageID = '';
		
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
	
	$cleanvalues = new number_formatting;
	$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
	$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS	
	
	function rmList($ogID,$groupColor=235)
	{
		global $licID, $dbinfo, $mgrlang, $cleanvalues, $config, $db;
				
		$rmOptionsResult = mysqli_query($db,
		"
			SELECT * FROM {$dbinfo[pre]}rm_options 
			WHERE og_id = '{$ogID}' 
			AND license_id = '{$licID}'
		");
		if($rmOptionRows = mysqli_num_rows($rmOptionsResult))
		{	
			//echo $rmOptionRows;
			
			while($rmOption = mysqli_fetch_assoc($rmOptionsResult))
			{
				if(!$rmOption['price_mod']) $rmOption['price_mod'] = '+';
				
				// Get linkage
				$rmRefResult = mysqli_query($db,
				"
					SELECT * FROM {$dbinfo[pre]}rm_ref 
					JOIN {$dbinfo[pre]}rm_option_grp 
					ON {$dbinfo[pre]}rm_option_grp.og_id = {$dbinfo[pre]}rm_ref.group_id
					WHERE {$dbinfo[pre]}rm_ref.option_id = '{$rmOption[op_id]}' 
				");
				$rmRefRows = mysqli_num_rows($rmRefResult);
				
				echo "<li class='subLevel'><div class='rmOptionTitle'><img src='images/rm.list.arrow.png' /> {$rmOption[op_name]} ";
				//if($rmRefRows) echo "<span onclick=\"sdisplaybool('rmSubScheme-{$rmOption[op_id]}')\">+</span>";
				echo " <p>({$rmOption[price_mod]} ";
				
				if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
					echo $cleanvalues->currency_display($rmOption['price'],1);					
				
				if($config['settings']['cart'] == 3)
					echo " / ";	
				
				if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
					echo $rmOption['credits']." {$mgrlang[gen_credits]}";	
									
				echo ") <a href=\"javascript:deleteRM('op',{$rmOption[op_id]});\" class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' /></a> <a href='javascript:rmEditOption({$rmOption[op_id]})' class='actionlink'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='$mgrlang[gen_edit]' border='0' /></a></div>";
					
					if($rmRefRows)
					{
						//echo "<img src='images/mgr.updown.arrow.png' style='position: absolute; left: 0'>";
						$borderColor = $groupColor + 30;
						echo "<ul style='background-color: rgb({$groupColor},{$groupColor},{$groupColor}); border: 1px solid rgb({$borderColor},{$borderColor},{$borderColor}); display: block;' id='rmSubScheme-{$rmOption[op_id]}'>";
						while($rmRef = mysqli_fetch_assoc($rmRefResult))
						{
							echo "<li class='topLevel'><div class='rmOptionTitle'>{$rmRef[og_name]} <p><a href=\"javascript:deleteRM('og',{$rmRef[og_id]});\" class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' /></a> <a href='javascript:rmEditOptionGroup({$rmRef[og_id]})' class='actionlink'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='$mgrlang[gen_edit]' border='0' /></a></p></div>";	
								rmList($rmRef['og_id'],($groupColor-5));
							echo "</li>";
						}
						echo "</ul>";
					}
				echo "</li>";
			}
		}		
	}
	
	switch($pmode)
	{
		case "rmList":			
			$rmOptionGroupsResult = mysqli_query($db,
			"
				SELECT * FROM {$dbinfo[pre]}rm_option_grp 
				WHERE og_id NOT IN (SELECT DISTINCT(group_id) FROM {$dbinfo[pre]}rm_ref) 				
				AND license_id = '{$licID}' 
			"); // Find the top level groups which are not nested under an option
			$rmOptionGroupsRows = mysqli_num_rows($rmOptionGroupsResult);
			
			if($rmOptionGroupsRows)
			{	
				echo "<ul id='rmSchemeList' style='background-color: rgb(240,240,240);'>";
				while($optionGroup = mysqli_fetch_assoc($rmOptionGroupsResult))
				{
					echo "<li class='topLevel'><div class='rmOptionTitle'>{$optionGroup['og_name']}<!-- - {$optionGroup['og_id']}--> <p><a href=\"javascript:deleteRM('og',{$optionGroup[og_id]});\" class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' /></a> <a href='javascript:rmEditOptionGroup({$optionGroup[og_id]})' class='actionlink'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='$mgrlang[gen_edit]' border='0' /></a></p></div>";
					rmList($optionGroup['og_id']);					
					echo "</li>";					
				}
				echo "</ul>";
			}
			else
				echo "{$mgrlang[rm_message_2]}";
?>
		
<?php
		break;
		case "editOptionGroup":
			if(!$optionGroupID)
				$optionGroupID = 'new';
			else
			{
				$rmOptionGroupResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_option_grp WHERE og_id = '{$optionGroupID}'");
				$rmOptionGroup = mysqli_fetch_array($rmOptionGroupResult);
				
				$ogRefResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_ref WHERE group_id = '{$optionGroupID}'");
				while($ogRef = mysqli_fetch_assoc($ogRefResult))
					$refArray[] = $ogRef['option_id'];
					
				//$refArrayString = implode(',',$refArray);
				//echo $refArrayString;
			}
			
			echo "<input type='hidden' name='optionGroupID' value='{$optionGroupID}' />";
				
			$row_color = 0;
			
			$rmOptionsResult = mysqli_query($db,
			"
				SELECT * FROM {$dbinfo[pre]}rm_options 
				WHERE license_id = '{$licID}'
			");			
			$rmOptionsRows = mysqli_num_rows($rmOptionsResult);
			
			if($rmOptionsRows)
			{
?>
			<div class="<?php fs_row_color(); ?>">
				<img src="images/mgr.ast.off.gif" class="ast" />
				<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
					<?php echo $mgrlang['rm_link_to']; ?>:<br />
					<span><?php echo $mgrlang['rm_link_to_d']; ?></span>
				</p>
				<select style="width: 260px; height: 200px;" multiple="multiple" name="ogLinks[]">
				<?php
					$rmOGResult = mysqli_query($db,
					"
						SELECT * FROM {$dbinfo[pre]}rm_option_grp  
						WHERE license_id = '{$licID}'
					");
					$rmOGRows = mysqli_num_rows($rmOGResult);
					while($rmOG = mysqli_fetch_assoc($rmOGResult))
					{
						if($rmOG['og_id'] != $optionGroupID)
						{
							echo "<option value='0'>{$rmOG[og_name]}</option>";
							
							$rmOptionsResult = mysqli_query($db,
							"
								SELECT * FROM {$dbinfo[pre]}rm_options 
								WHERE license_id = '{$licID}' 
								AND og_id = '{$rmOG[og_id]}'
							");			
							$rmOptionsRows = mysqli_num_rows($rmOptionsResult);
							while($rmOption = mysqli_fetch_assoc($rmOptionsResult))
							{
								echo "<option value='{$rmOption[op_id]}'";
								if(@in_array($rmOption['op_id'],$refArray)) echo "selected='selected'";
								echo ">&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;{$rmOption[op_name]}</option>";
							}
						}
					}
				?>
				</select>
			</div>
<?php
			}
?>
				
		<div class="<?php fs_row_color(); ?>">
			<img src="images/mgr.ast.off.gif" class="ast" />
			<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
				<?php echo $mgrlang['rm_og_name']; ?>:<br />
				<span><?php echo $mgrlang['rm_og_name_d']; ?></span>
			</p>
			
			<div class="additional_langs">
				<input type="text" style="width: 250px;" name="ogName" value="<?php echo $rmOptionGroup['og_name']; ?>" />
				<?php
					if(in_array('multilang',$installed_addons)){
				?>
					&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_ogName','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
					<div id="lang_ogName" style="display: none;">
					<ul>
					<?php
						foreach($active_langs as $value){
					?>
						<li><input type="text" name="ogName_<?php echo $value; ?>" id="ogName_<?php echo $value; ?>" style="width: 250px;" maxlength="100" value="<?php echo @stripslashes($rmOptionGroup["og_name_" . $value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
				<?php
						}
						echo "</ul></div>";
					}
				?>
			</div>
			
		</div>
<?php
		break;
		case "editOption":
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			if(!$optionID)
				$optionID = 'new';
			else
			{
				$rmOptionResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_options WHERE op_id = '{$optionID}'");
				$rmOption = mysqli_fetch_array($rmOptionResult);
			}
			
			$rmOptionGroupsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}rm_option_grp WHERE license_id = '{$licID}'");
			$rmOptionGroupsRows = mysqli_num_rows($rmOptionGroupsResult);
			
			if(!$rmOptionGroupsRows)
			{
				echo "<div style='padding: 10px 15px 15px 15px;'>{$mgrlang[rm_message_1]}</div>";
				exit;	
			}
			
?>
		<input type="hidden" name="optionID" value="<?php echo $optionID; ?>" />
		<div class="fs_row_off">
			<img src="images/mgr.ast.off.gif" class="ast" />
			<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
				<?php echo $mgrlang['rm_option_group']; ?>:<br />
				<span><?php echo $mgrlang['rm_option_group_d']; ?></span>
			</p>
			<select style="width: 260px;" name="optionOGID">
				<?php
					while($optionGroup = mysqli_fetch_assoc($rmOptionGroupsResult))
					{
						echo "<option value='{$optionGroup[og_id]}'";
						if($optionGroup['og_id'] == $rmOption['og_id']) echo " selected='selected'";
						echo ">{$optionGroup[og_name]}</option>";
					}
				?>
			</select>
		</div>		
				
		<div class="fs_row_on">
			<img src="images/mgr.ast.off.gif" class="ast" />
			<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
				<?php echo $mgrlang['rm_option_name']; ?>:<br />
				<span><?php echo $mgrlang['rm_option_name_d']; ?></span>
			</p>
			
			<div class="additional_langs">
				<input type="text" name="optionName" style="width: 250px;" value="<?php echo $rmOption['op_name']; ?>" />
				<?php
					if(in_array('multilang',$installed_addons)){
				?>
					&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_optionName','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
					<div id="lang_optionName" style="display: none;">
					<ul>
					<?php
						foreach($active_langs as $value){
					?>
						<li><input type="text" name="optionName_<?php echo $value; ?>" id="optionName_<?php echo $value; ?>" style="width: 250px;" maxlength="100" value="<?php echo @stripslashes($rmOption["op_name_" . $value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
				<?php
						}
						echo "</ul></div>";
					}
				?>
			</div>
		</div>
		
		<div class="fs_row_off">
			<img src="images/mgr.ast.off.gif" class="ast" />
			<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
				<?php echo $mgrlang['rm_price_mod']; ?>:<br />
				<span><?php echo $mgrlang['rm_price_mod_d']; ?></span>
			</p>
			<div>
				<select name="optionPriceModifier">
					<option <?php if($rmOption['price_mod'] == '+'){ echo "selected='selected'"; } ?>>+</option>
					<option <?php if($rmOption['price_mod'] == '-'){ echo "selected='selected'"; } ?>>-</option>
					<option <?php if($rmOption['price_mod'] == 'x'){ echo "selected='selected'"; } ?>>x</option>
				</select>
			</div>
		</div>
		<?php
			if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
			{
		?>
			<div class="fs_row_on">
				<img src="images/mgr.ast.off.gif" class="ast" />
				<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
					<?php echo $mgrlang['gen_price']; ?>:<br />
					<span><?php echo $mgrlang['rm_price_d']; ?></span>
				</p>
				<div>
					<input type="text" name="optionPrice" id="optionPrice" value="<?php echo @$cleanvalues->currency_display($rmOption['price']); ?>" />
				</div>
			</div>
		<?php
			}
			if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
			{
		?>
			<div class="fs_row_off">
				<img src="images/mgr.ast.off.gif" class="ast" />
				<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
					<?php echo $mgrlang['gen_credits']; ?>:<br />
					<span><?php echo $mgrlang['rm_credits_d']; ?></span>
				</p>
				<div>
					<input type="text" name="optionCredits" id="optionCredits" value="<?php echo $rmOption['credits']; ?>" />
				</div>
			</div>
<?php
			}
		break;		
	}
?>