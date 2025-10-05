<?php
	###################################################################
	####	GROUPS ACTIONS		   	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		//$page = "groups";
		
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
		
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			default:
			break;		
			# DELETE GROUP
			case "delete_group":
				# FIND DETAILS ABOUT THE SELECTED GROUP
				$group_result = mysqli_query($db,"SELECT mgrarea,name,gr_id FROM {$dbinfo[pre]}groups WHERE gr_id = '$_REQUEST[id]'");
				$group = mysqli_fetch_object($group_result);
				
				# DELETE ENTRIES FOR ANY GROUPS UNDER MEDIA_*
				switch($group->mgrarea)
				{
					case "prints":
						# DELETE MEDIA GROUP ENTRIES
						@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_prints WHERE printgrp_id = '$_REQUEST[id]'");
					break;
					case "products":
						# DELETE MEDIA GROUP ENTRIES
						@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_products WHERE prodgrp_id = '$_REQUEST[id]'");
					break;
					case "packages":
						# DELETE MEDIA GROUP ENTRIES
						@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_packages WHERE packgrp_id = '$_REQUEST[id]'");
					break;
				}
				
				# DELETE THE GROUP
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groups WHERE gr_id = '$_REQUEST[id]'");				
				
				# DELETE THE GROUP IDS
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE group_id = '$group->gr_id' AND mgrarea = '$group->mgrarea'");
				
				# FIND THE NUMBER OF REMAINING GROUPS
				$new_group_count = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(gr_id) FROM {$dbinfo[pre]}groups WHERE hidden != '1' AND mgrarea = '$group->mgrarea'"));
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_groups'],1,$mgrlang['gen_b_del'] . " > <strong>$group->name ($_REQUEST[id])</strong>");
				
				//group_list_win
				if($new_group_count < 1)
				{
					echo "<script>$('group_list_win').update('<div style=\"padding: 20px;\"><img src=\"images/mgr.notice.icon.png\" align=\"absmiddle\" />$mgrlang[gen_empty_groups]</div>');</script>";
				}
				else
				{
					//
					echo "<script>$('group_row_$_REQUEST[id]').hide();update_grrow();</script>";
				}
			break;
			case "wb_group_list":
				echo "<table width='100%' cellspacing='0'>";
				# SELECT ITEMS
				$group_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}groups WHERE hidden != '1' AND mgrarea = '$_GET[mgrarea]' ORDER BY name");
				$group_rows = mysqli_num_rows($group_result);
				if($group_rows)
				{
					while($group = mysqli_fetch_object($group_result))
					{
						# SET THE ROW COLOR
						@$row_color++;
						if ($row_color%2 == 0) {
							$row_class = "list_row_on";
						} else {
							$row_class = "list_row_off";
						}
						
						echo "<tr id='group_row_$group->gr_id' class='$row_class'>";
							echo "<td width='100%'";
							if($_SESSION['admin_user']['admin_id'] != "DEMO"){ echo "onclick='edit_group($group->gr_id);'"; } 
							echo ">";
								echo "<img src='./images/mini_icons/$group->flagtype' align='absmiddle' /> ";
								echo "$group->name";
							echo "</td>";
							echo "<td align='right' nowrap='nowrap'>";
								echo "<a href='javascript:";
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message2();"; } else { echo "edit_group($group->gr_id);"; } 
								echo "' class='actionlink'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='$mgrlang[gen_edit]' border='0' />$mgrlang[gen_short_edit]</a>&nbsp;";
								echo "<a href='javascript:";
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message2();"; } else { echo "delete_group($group->gr_id);"; } 
								echo "' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a>";
							echo "</td>";
						echo "</tr>";
					}
				}
				else
				{
					echo "<tr><td><div style='padding: 20px;'><img src='images/mgr.notice.icon.png' align='absmiddle' />$mgrlang[gen_empty_groups]</div></td></tr>";	
				}
			echo "</table>";
			break;
			case "edit_group":
				
				if($_GET['edit'] != "new"){
					$group_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}groups WHERE gr_id = '$_GET[edit]'");
					$group_rows = mysqli_num_rows($group_result);
					$group = mysqli_fetch_object($group_result);
				}
				
?>
				
                <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
                <input type="hidden" name="mode" value="save_group" />
                <?php $row_color = 0; ?>
                <div class="<?php fs_row_color(); ?>" id="group_name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['gen_group_f_name']; ?>:<br />
                        <span><?php echo $mgrlang['gen_group_f_name_d']; ?></span>
                    </p>
                    <input type="text" name="group_name" id="group_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($group->name); ?>" />
                </div>
                                    
                <div class="<?php fs_row_color(); ?>" id="name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="flagtype" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['gen_group_f_flag']; ?>:<br />
                        <span><?php echo $mgrlang['gen_group_f_flag_d']; ?></span>
                    </p>                       
                    <div style="float: left;">
                        <input type="radio" value="icon.none.gif" name="flagtype" id="none" <?php if($_GET['edit'] == "new" or $group->flagtype == "icon.none.gif"){ echo "checked"; } ?> /> <?php echo $mgrlang['gen_none']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" value="<?php if($_GET['edit'] == "new"){ echo "icon.003.gif"; } else { echo $group->flagtype; } ?>" name="flagtype" id="icon" <?php if($_GET['edit'] != "new" and $group->flagtype != "icon.none.gif"){ echo "checked"; } ?> /> <img src="images/mini_icons/<?php if($_GET['edit'] == "new" or $group->flagtype == "icon.none.gif"){ echo "icon.003.gif"; } else { echo $group->flagtype; } ?>" align="absmiddle" id="flagswap" />
                        <br /><br />
                        <div id="flagbox"></div>
                    </div>
                </div>
                <script>load_r_box(0,'flagbox','mgr.flagtypes.php');</script>
<?php				
			break;
			case "save_group":
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# SAVE NEW				
				if($saveid == 'new')
				{
					# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
					require_once('../assets/includes/clean.data.php');
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}groups (
							name,flagtype,mgrarea
							) VALUES (
							'$group_name','$flagtype','$mgrarea'
							)";
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_groups'],1,$mgrlang['gen_b_new'] . " > <strong>$name ($saveid)</strong>");
				}
				# SAVE EDIT
				else
				{
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}groups SET 
								name='$group_name',
								flagtype='$flagtype',
								mgrarea='$mgrarea'
								where gr_id  = '$saveid'";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_groups'],1,$mgrlang['gen_b_ed'] . " > <strong>$name ($saveid)</strong>");
				}
				
				echo "<script>load_group_list_win('$mgrarea');</script>";
				
			break;
			case "grouplist":
				//sleep(2);
				$mgrarea = $_GET['mgrarea'];
				$ingroups = $_GET['ingroups'];
				$dtype = $_SESSION[$dtype];
				$exitpage = $_GET['exitpage'];
				$sessname = $_GET['sessname'];
				$sessgroups = $_SESSION[$sessname];
				
				//echo $ingroups;
				
				$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE hidden != '1' AND mgrarea = '$mgrarea' ORDER BY name");
				$group_rows = mysqli_num_rows($group_result);
?>
				<div class="opbox_buttonbar">
					<p><?php echo $mgrlang['gen_group_header']; ?>:</p>
					<?php if($group_rows){ ?><!--<a href="#" onclick="select_all_cb('grouplist');" class='actionlink'><?php echo $mgrlang['gen_b_sa']; ?></a><a href="#" onclick="deselect_all_cb('grouplist');" class='actionlink'><?php echo $mgrlang['gen_b_sn']; ?></a><a href="javascript:submit_groups();" class='actionlink'><?php echo $mgrlang['gen_t_show_sel']; ?></a>--><?php } echo "<a href=\"javascript:workbox2({page: 'mgr.groups.workbox.php',pars: 'box=groupswb&mgrarea=$mgrarea&grouprows=0'});\" class='actionlink'>{$mgrlang[create_group]}</a><a href=\"javascript:workbox2({page: 'mgr.groups.workbox.php',pars: 'box=groupswb&mgrarea=$mgrarea&grouprows=$group_rows'});\" class='actionlink'>$mgrlang[gen_t_ae_grps]</a>"; ?>
                </div>                
                <div class="options_area_box">
					<div style="clear: both;"></div>
					<?php
						if($group_rows)
						{
							while($group = mysqli_fetch_object($group_result))
							{
								echo "<div class='opbox_list'><input type='checkbox' class='checkbox' name='setgroups[]' value='".$group->gr_id."' "; if(in_array($group->gr_id,$sessgroups) or in_array('all',$sessgroups)){ echo "checked "; } echo "/> "; if($group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$group->flagtype' align='absmiddle' /> "; } echo "<a href='".$exitpage."?dtype=groups&setgroups=$group->gr_id'>".substr($group->name,0,30)."</a></div>";
							}
						}
						else
						{
							echo "<img src='images/mgr.notice.icon.png' align='absmiddle' /> $mgrlang[gen_gmes_2]";
						}
					?>
					<?php if($group_rows){ ?><div style="clear: both; float: right; margin-bottom: 3px;"><a href="#" onclick="select_all_cb('grouplist');" class='actionlink'><?php echo $mgrlang['gen_b_sa']; ?></a> <a href="#" onclick="deselect_all_cb('grouplist');" class='actionlink'><?php echo $mgrlang['gen_b_sn']; ?></a> <a href="javascript:submit_groups();" class='actionlink'><?php echo $mgrlang['gen_t_show_sel']; ?></a><?php if($ingroups == 1){ ?> <a href="<?php echo $exitpage; ?>?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_exit_grps']; ?></a><?php } ?></div><?php } ?>
				</div>

<?php
			break;
		}	
?>
