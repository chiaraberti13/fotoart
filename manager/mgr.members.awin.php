<?php
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	//error_reporting(0);
	
	$page = "members";
	
	require_once('../assets/includes/session.php');			# INCLUDE THE SESSION START FILE
	require_once('mgr.security.php');				# INCLUDE SECURITY CHECK FILE	
	require_once('mgr.config.php');					# INCLUDE MANAGER CONFIG FILE	
	if(file_exists("../assets/includes/db.config.php"))
	{
		require_once('../assets/includes/db.config.php');	# INCLUDE DATABASE CONFIG FILE
	}
	else
	{
		@$script_error[] = "The db.config.php file is missing.";
	}		
	require_once('../assets/includes/shared.functions.php');	# INCLUDE SHARED FUNCTIONS FILE
	require_once('../assets/includes/db.conn.php');			# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.functions.php');				# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.select.settings.php');		# SELECT THE SETTINGS DATABASE
	include_lang();									# INCLUDE THE LANGUAGE FILE
	
	$mem_id = $_GET['id'];
	
	# CHOOSE WHICH FIELDS TO SELECT
	$select_fields = 'mem_id,f_name,l_name,email,comp_name,website,signup_date,last_login,status,membership,credits,notes,avatar,avatar_status';
	$member_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members WHERE mem_id = '$mem_id'");
	$mgrMemberInfo = mysqli_fetch_object($member_result);	
		
	$avatar_width2 = 250;
	
	$newsize = get_scaled_size($avatar_width2,"../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png");
	
	echo "<div style='text-align: right; margin: 8px 10px 0 10px; min-height: ".round($newsize[1]+30)."px'>";
	if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png"))
	{
		//echo "<img src='../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png" . "' style='border: 2px solid #ffffff; margin-right: 1px;' width='$avatar_width2' />";
		$mem_needed = figure_memory_needed("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png");
		if(ini_get("memory_limit"))
		{
			$memory_limit = ini_get("memory_limit");
		}
		else
		{
			$memory_limit = $config['DefaultMemory'];
		}
		if($memory_limit > $mem_needed)
		{
			// GO FOR IT
			echo "<div style='margin: 10px;border: 6px solid #eee;'><img src='mgr.display.avatar.php?mem_id=$mem_id&size=$avatar_width2' style='border: 1px solid #fff;' /></div>";
			if($mgrMemberInfo->avatar_status == 1)
			{
				echo "<img src='images/mgr.small.check.1.png' style='margin: -15px 0 0 0;' />";
			}			
		}
		else
		{
			echo "<p style='white-space: pre-wrap; text-align: left; padding: 10px; background-color: #fae8e8; width: 180px; border: 1px solid #ba0202; margin: 4px;'><img src='images/mgr.icon.mem.summary2.gif' style='border: 2px solid #eeeeee; margin-right: 10px;' width='40' align='left' />$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></p>";
		}
	}
	else
	{ 
		echo "<div style='border: 1px solid #d6e1f2; margin: 10px;'><img src='images/mgr.icon.mem.summary.gif' style='border: 6px solid #eeeeee;' width='$avatar_width2' /></div>";
	}
	echo "</div>";
?>
<div style="margin: 0 10px 10px 10px; line-height: 30px; text-align: center;">
	<a href="javascript:hide_div('avatar_win_<?php echo $mem_id; ?>');" class="actionlink"><img src="images/mgr.button.close2.png" align="absmiddle" border="0" /><?php echo $mgrlang['gen_b_close']; ?></a> <a href="javascript:delete_avatar(<?php echo $mem_id; ?>);" class="actionlink"><img src="images/mgr.icon.delete.png" align="absmiddle" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a> 
    <?php
    	if($mgrMemberInfo->avatar_status == 0 or $mgrMemberInfo->avatar_status == 2){
			echo "<a href='javascript:approve_avatar($mem_id);' class='actionlink'><img src='images/mgr.tiny.check.1.png' align='absmiddle' border='0' />" . $mgrlang['gen_b_approve'] . "</a>";
		}
	?>
</div>