<?php
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		
	$page = "galleries";
	
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
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE
	
	$gal_id = $_GET['id'];
	
	$gallery_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}galleries WHERE gallery_id = '$gal_id'");
	$gallery_rows = mysqli_num_rows($gallery_result);
	$gallery = mysqli_fetch_object($gallery_result);
		
	$ndate = new kdate;
	$ndate->distime = 0;
	$ndate->adjust_date = 0;
?>
<div style="padding: 10px; background-color: #FFF;">
    <table cellspacing="0" border="0" width="100%">
    	<tr>
        	<td colspan="2" style="border: none; white-space: normal;">
                
				<?php
					echo "<span style='color: #333; font-weight: bold;'>";
					echo $gallery->name;
					echo "</span>";
					
					if($gallery->password){
						echo "<img src='images/mgr.lock.icon.png' style='vertical-align: middle;' /></em>";
					}
					
					if($gallery->description)
					{
						echo "<span style='color: #8f8e8e; font-size: 10px;'>";
						echo "<br />$gallery->description";
						echo "</span>";
					}
				?>
            </td>
        </tr>
		<tr>
        	<td><?php echo $mgrlang['gen_medianame_media']; ?>:</td>
            <td><strong><?php echo $gallery->gallery_count; ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['gen_active_date']; ?>:</td>
            <td><strong><?php if($gallery->active_type == 0){ echo $mgrlang['gen_now']; } else { echo $ndate->showdate($gallery->active_date); } ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['gen_expire_date']; ?>:</td>
            <td><strong><?php if($gallery->expire_type == 0){ echo $mgrlang['gen_never']; } else { echo $ndate->showdate($gallery->expire_date); } ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $mgrlang['gen_owner']; ?>:</td>
            <td>
            	<strong>
				<?php
                    if($gallery->owner == 0)
                    {
                        echo $config['settings']['business_name'];
                    }
                    else
                    {
                        $member_result = mysqli_query($db,"SELECT mem_id,f_name,l_name,email FROM {$dbinfo[pre]}members WHERE mem_id = '$gallery->owner'");
                        $mgrMemberInfo = mysqli_fetch_object($member_result);
                        echo $mgrMemberInfo->l_name . ", " . $mgrMemberInfo->f_name;
                    }
                ?>            
            	</strong>
            </td>
        </tr>
        <?php if(in_array("contr",$installed_addons)){ ?>
        <tr>
        	<td><?php echo $mgrlang['allow_contr']; ?>:</td>
            <td><strong><?php echo "<img src='images/mgr.tiny.check.".$gallery->allow_uploads.".png' />"; ?></strong></td>
        </tr>
        <?php } ?>
        <tr>
        	<td><?php echo $mgrlang['access']; ?>:</td>
            <td><strong><?php if($gallery->everyone == 1){ echo $mgrlang['gen_wb_everyone']; } else { echo $mgrlang['gen_wb_limited']; } ?></strong></td>
        </tr>
    </table>
</div>