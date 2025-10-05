<?php
	###################################################################
	####	MANAGER CURRENCIES ACTIONS                             ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-4-2010                                      ####
	####	Modified: 1-4-2010                                     #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE

		$page = $_GET['page'];
		
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
		require_once('mgr.select.settings.php');
		
		# INCLUDE MANAGER ADDONS FILE	
		require_once('../assets/includes/addons.php');
		
		$pmode = $_GET['pmode'];
		
		# DETERMINE IF IT IS A NEW ITEM
		if($_GET['item_id'] == "new" or !$_GET['item_id'])
		{
			$item_id = 0;
		}
		else
		{
			$item_id = $_GET['item_id'];
		}
		
		function opbox($og_id,$haf=0)
		{
			global $config, $db, $dbinfo, $active_langs, $mgrlang, $installed_addons;
			$opgroup_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}option_grp WHERE og_id = '$og_id'");
			$opgroup_rows = mysqli_num_rows($opgroup_result);
			$opgroup = mysqli_fetch_object($opgroup_result);				
?>	
		<div class="optionsbox_row" id="optiongroup_<?php echo $opgroup->og_id; ?>" <?php if($haf){ echo "style='display: none;'"; } ?>>
            <input type="hidden" name="optiongroup[]" value="<?php echo $opgroup->og_id; ?>" />
            <p style="float: left; width: 48px; text-align: center; margin-top:6px;"><img src='images/mgr.updown.arrow.png' class='handle' onmousedown="set_moved_div(<?php echo $opgroup->og_id; ?>);" id="optionsbox_handle_<?php echo $opgroup->og_id; ?>" align='absmiddle' /></p>
            <div style="float: left; width: <?php if(in_array('multilang',$installed_addons)){ echo "300"; } else { echo "220"; } ?>px; font-size: 11px; font-weight: normal" class="additional_langs"><!--<?php echo $opgroup->og_id; ?>-->
                <input type="text" style="width: 200px;" name="optiongrpname[]" value="<?php echo $opgroup->name; ?>" />
				<?php
					//echo $installed_addons;
					if(in_array('multilang',$installed_addons))
					{
				?>
					&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_name_<?php echo $opgroup->og_id; ?>','','','','plusminus-01_<?php echo $opgroup->og_id; ?>');"><img src="images/mgr.plusminus.0.png" id="plusminus01_<?php echo $opgroup->og_id; ?>" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
					<!--<br /><a href="javascript:displaybool('lang_name_<?php echo $opgroup->og_id; ?>','','','','plusminus-01');"><img src="images/mgr.plusminus.0.gif" id="plusminus01" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_name_<?php echo $opgroup->og_id; ?>','','','','plusminus-01');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />-->
					<div id="lang_name_<?php echo $opgroup->og_id; ?>" style="display: none;">
					<ul>
					<?php
						foreach($active_langs as $value){
					?>
						<li><input type="text" name="langname_<?php echo $value; ?>[]" style="width: 200px;" maxlength="100" value="<?php echo @stripslashes($opgroup->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
				<?php
						}
						echo "</ul></div>";
					}
				?>
            </div>
            <p style="float: left; margin: 2px 0 0 10px; width: 120px;">
                <select name="optiongrpltype[]" id="optionsGroupTypeDD_<?php echo $opgroup->og_id; ?>" onChange="optionGroupTypeChange('<?php echo $opgroup->og_id; ?>');">
                    <option value="1" <?php if($opgroup->ltype == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_option_type1']; ?></option>
                    <option value="2" <?php if($opgroup->ltype == 2){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_option_type2']; ?></option>
                    <option value="3" <?php if($opgroup->ltype == 3){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_option_type3']; ?></option>
                </select>
            </p>
            <p style="float: left; margin: 10px 0 0 10px; width: 48px; text-align: center;">
                <input type="checkbox" name="optiongrpactive_<?php echo $opgroup->og_id; ?>" id="optiongrpactive_<?php echo $opgroup->og_id; ?>" value="1" <?php if($opgroup->active or $haf == 1){ echo "checked='checked'"; } ?> style="vertical-align: text-top;" />
            </p>
            <p style="float: left; margin: 10px 0 0 10px; width: 48px; text-align: center;">
                <input type="checkbox" name="optiongrprequired_<?php echo $opgroup->og_id; ?>" id="optiongrprequired_<?php echo $opgroup->og_id; ?>" value="1" <?php if($opgroup->required and $opgroup->ltype != 2){ echo "checked='checked'"; } ?> style="vertical-align: text-top;<?php if($opgroup->ltype == 2){ echo "display: none;"; } ?>" />
            </p>
            <p style="float: right; margin-top: 8px; margin-right: 10px;">
                <a href="javascript:workbox2({page: 'mgr.optionsbox.workbox.php',pars: 'box=optionswb&mgrarea=<?php echo $_GET['page']; ?>&og_id=<?php echo $og_id; ?>'});" class="actionlink"><img src='images/mgr.icon.edit.png' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_tab_options']; ?></a>&nbsp;<a href="javascript:delete_optiongrp(<?php echo $opgroup->og_id; ?>)" class="actionlink"><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' id="optionsbox_delete_<?php echo $opgroup->og_id; ?>" width="14" /> <?php echo $mgrlang['gen_short_delete']; ?></a>
            </p>
        </div>
<?php
    	}
		
		switch($pmode)
		{
			case "firstload":
				
				include_lang();
				
				# SELECT THIS ITEMS GROUPS
				$opgroup_result = mysqli_query($db,"SELECT og_id FROM {$dbinfo[pre]}option_grp WHERE parent_type = '$_GET[page]' AND parent_id = '$item_id' AND deleted = '0' ORDER BY sortorder");
				$opgroup_rows = mysqli_num_rows($opgroup_result);
				
				echo "<div style='background-color: #CCC; overflow: auto; padding: 6px;'>
						<p style='float: left; width: 48px; text-align: center; font-weight: bold;'>$mgrlang[gen_option_h_sort]</p>
						<p style='float: left; width: ";
						if(in_array('multilang',$installed_addons)){ echo "300"; } else { echo "220"; }
						echo "px; text-align: left; font-weight: bold;'>$mgrlang[gen_option_h_name]</p>
						<p style='float: left; width: 120px; text-align: left; font-weight: bold; margin-left: 10px;'>$mgrlang[gen_option_h_type]</p>
						<p style='float: left; width: 48px; text-align: center; font-weight: bold; margin-left: 10px;'>$mgrlang[gen_option_h_active]</p>
						<p style='float: left; width: 48px; text-align: center; font-weight: bold; margin-left: 10px;'>$mgrlang[gen_option_h_required]</p>";
				echo "</div>";
				echo "<div class='optionsbox_row' id='optiongroup_0' style='display: none;'></div>";
				
				if($opgroup_rows)
				{
					while($opgroup = mysqli_fetch_object($opgroup_result))
					{
						opbox($opgroup->og_id);
					}
					# MAKE THE LIST SORTABLE
					echo "<script>create_sortlist();\$('optionsbox').show();</script>";
				}
				else
				{
					echo "<script>\$('optionsbox').hide();</script>";
				}
			break;
			case "delete":
				include_lang();
				
				$opgroup_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}option_grp WHERE og_id = '$_GET[delete]'");
				$opgroup = mysqli_fetch_object($opgroup_result);
				
				# SET TO DELETED
				$sql = "UPDATE {$dbinfo[pre]}option_grp SET deleted='1' WHERE og_id  = '$_GET[delete]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_opgroup'],1,$mgrlang['gen_b_del'] . " > <strong>$opgroup->name ($_GET[delete])</strong>");
			break;
			case "addnew":
				include_lang();
				
				$opgroup_result = mysqli_query($db,"SELECT sortorder FROM {$dbinfo[pre]}option_grp WHERE parent_type = '$page' AND parent_id = '$item_id' AND deleted = '0' ORDER BY sortorder DESC");
				$opgroup = mysqli_fetch_object($opgroup_result);	
				
				$sortnum = $opgroup->sortorder+1;
				
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}option_grp (uog_id,parent_id,parent_type,ltype,sortorder) VALUES ('','$item_id','$page','1','$sortnum')");
				$saveid = mysqli_insert_id($db);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_opgroup'],1,$mgrlang['gen_b_new'] . " > <strong>($saveid)</strong>");
				
				opbox($saveid,1);
				# MAKE THE LIST SORTABLE
				echo "<script>create_sortlist();</script>";
			break;		
			case "update":
				$x = 0;
				foreach($_GET['optionsbox'] as $value)
				{
					if($value)
					{
						$sql = "UPDATE {$dbinfo[pre]}option_grp SET sortorder='$x' WHERE og_id  = '$value'";
						$result = mysqli_query($db,$sql);
					}
					$x++;
				}
				/*echo "<script>alert('saved');</script>";*/
			break;
		}
?>