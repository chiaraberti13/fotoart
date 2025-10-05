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
	
	# ADDRESS
	$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '$mgrMemberInfo->mem_id'");
	$address_rows = mysqli_num_rows($address_result);
	$address = mysqli_fetch_object($address_result);
	
	# COUNTRY
	$country_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}countries WHERE country_id = '$address->country'");
	$country_rows = mysqli_num_rows($country_result);
	$country = mysqli_fetch_object($country_result);
	
	# STATE
	$state_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}states WHERE state_id = '$address->state'");
	$state_rows = mysqli_num_rows($state_result);
	$state = mysqli_fetch_object($state_result);
	
	# MEMBERSHIPS
	$ms_result = mysqli_query($db,"SELECT name,flagtype FROM {$dbinfo[pre]}memberships WHERE ms_id = '$mgrMemberInfo->membership'");
	$ms_rows = mysqli_num_rows($ms_result);
	$ms = mysqli_fetch_object($ms_result);
	
	# GROUPS
	$mem_group_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}groupids LEFT JOIN {$dbinfo[pre]}groups ON {$dbinfo[pre]}groupids.group_id = {$dbinfo[pre]}groups.gr_id WHERE {$dbinfo[pre]}groupids.mgrarea = '$page' AND {$dbinfo[pre]}groupids.item_id = '$mem_id'");
	$mem_group_rows = mysqli_num_rows($mem_group_result);
	
	$ndate = new kdate;
	$ndate->distime = 1;
	
	$avatar_width2 = 150;
	echo "<div style='float: left; text-align: right; min-height: 186px;'>";
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
			if($mgrMemberInfo->avatar_status)
			{
				echo "<img src='images/mgr.small.check.1.png' style='margin: -21px 0 0 0; padding-right: 2px;' />";
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
<div style="float: right; margin: 6px 10px 6px 0; background-color: transparent;">
	<table cellspacing="0" border="0" style="background-color: transparent;">
    	<tr>
        	<td colspan="2" style="border: none;">
            	<strong><?php echo $mgrMemberInfo->f_name . " " . $mgrMemberInfo->l_name; ?></strong> (<?php echo $mgrMemberInfo->email; ?>)<br />
                <span style="color: #636262;">
				<?php
					echo $address->address . "<br />";
					if($address->address_2){ echo $address->address_2 . "<br />"; }
					echo $address->city;											
					if($state_rows){ echo ", " . $state->name; }
					echo " " . $address->postal_code . "<br />";
					if($country_rows){ echo $country->name; }
				?>
                </span>
            </td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['mem_member_num']; ?>:</td>
            <td><strong><?php echo $mgrMemberInfo->mem_id; ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['mem_last_login']; ?>:</td>
            <td><strong><?php if($mgrMemberInfo->signup_date == "0000-00-00 00:00:00"){ echo $mgrlang['mem_never']; } else { echo $ndate->showdate($mgrMemberInfo->last_login); } ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['mem_signup_date']; ?>:</td>
            <td><strong><?php echo $ndate->showdate($mgrMemberInfo->signup_date); ?></strong></td>
        </tr>
        <?php
			if($mem_group_rows)
			{
		?>
        <tr>
        	<td valign="top"><?php echo $mgrlang['mem_f_groups']; ?>:</td>
            <td>
            	<?php
					while($mem_group = mysqli_fetch_object($mem_group_result)){
						if($mem_group->flagtype == 'icon.none.gif' or !$mem_group->flagtype)
						{ 
							echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' width='1' height='16' style='margin-left: -3px;' /> "; 
						}
						else
						{
							echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' style='margin-left: -3px;' /> ";
						}
						echo substr($mem_group->name,0,30);
						echo "<br />";
						
						//echo $mem_group->id;  // testing
					}
				?>
            </td>
        </tr>
       	<?php
			}
		?>
        <tr>
        	<td valign="top"><?php echo $mgrlang['mem_f_membership']; ?>:</td>
            <td>
				<?php
                	if($ms->flagtype == 'icon.none.gif')
					{
						echo "<img src='./images/mini_icons/$ms->flagtype' align='absmiddle' width='1' height='16' style='margin-left: -3px;' /> ";
					}
					else
					{
						echo "<img src='./images/mini_icons/$ms->flagtype' align='absmiddle' style='margin-left: -3px;' /> ";
					}
					echo substr($ms->name,0,30);
				?>
            </td>
        </tr>
    </table>
</div>