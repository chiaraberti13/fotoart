<?php
	###################################################################
	####	MANAGER STORAGE PAGE                                   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-2-2010                                      ####
	####	Modified: 2-2-2010                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "storage";
		$lnav = "settings";
		
		$supportPageID = 0;
	
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
		require_once('../assets/classes/mediatools.php');				# REQUIRE MEDIA TOOLS CLASS
		
		/*
		try
		{
			$media = new mediaTools(1);
			$media->clearMediaTables();
		}
		catch(Exception $e)
		{
			echo $e->getMessage(); exit;
		}
		*/
		

		//$perpage = $config['settings']['perpage_storage'];

		# IF AN ENTRY PAGE CLEAR CURRENTPAGE SESSION
		if(!empty($_REQUEST['ep']) && isset($_SESSION['currentpage'])){ $_SESSION['currentpage'] = 1; }

		# ACTIONS
		switch($_REQUEST['action'])
		{
			case "save_groups":				
				save_groups($page,'storage','storage_id');				
			break;			
			case "save_status":
				save_status($page,'storage','storage_id');
			break;
			case "del":
				if(!empty($_REQUEST['items']))
				{
					$items = $_REQUEST['items'];
										
					if(!is_array($items))
					{
						$items = explode(",",$items);
					}				
					$delete_array = implode(",",$items);
					
					# GET TITLES FOR LOG
					$log_result = mysqli_query($db,"SELECT title,storage_id FROM {$dbinfo[pre]}storage WHERE storage_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result))
					{
						$log_titles.= "$log->title ($log->storage_id), ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", ")
					{
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# DELETE GROUPS
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id IN ($delete_array)");

					# DELETE LOCAL FOLDERS
					$folders_result = mysqli_query($db,"SELECT folder_id,name FROM {$dbinfo[pre]}folders WHERE storage_id IN ($delete_array)");
					while($folders = mysqli_fetch_array($folders_result))
					{
						$folderlist[] = $folders['folder_id'];
						
						if($folders->name)
						{
							clean_directory($config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folders->name);
							rmdir($config['settings']['library_path'] . DIRECTORY_SEPARATOR . $folders->name);
						}
					}
					
					# DELETE DB FOLDERS
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}folders WHERE storage_id IN ($delete_array)");
					
					$allfolders = implode(',',$folderlist);
					
					# DELETE MEDIA
					$media_result = mysqli_query($db,"SELECT media_id FROM {$dbinfo[pre]}media WHERE folder_id IN ($allfolders)");
					while($media = mysqli_fetch_object($media_result))
					{
						$mediaObj = new mediaTools($media->media_id);
						$mediaObj->deleteMediaFromDB();
					}
					
					# DELETE
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}storage WHERE storage_id IN ($delete_array)");
										
					# SET SITE MENU BUILD TO 0
					menuBuild(0);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_storage'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
				
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_delsuc'];
				}
				else
				{
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
				
			break;
		}
		
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO")
		{
			$delete_link = "DEMO_";
		}
		else
		{
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		if($_GET['mes'] == "new")
		{
			$vmessage = $mgrlang['gen_mes_newsave'];
		}
		if($_GET['mes'] == "edit")
		{
			$vmessage = $mgrlang['gen_mes_changesave'];
		}
		
		# INCLUDE DATASORTS CLASS
		require_once('mgr.class.datasort.php');			
		$sortprefix="storage";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "storage_id";		
		require_once('mgr.datasort.logic.php');	
		
		# IF THIS IS AN ENTRY PAGE OR storagegroups IS BLANK RESET THE storagegroups SESSION	
		if($_GET['ep'] or empty($_SESSION['storagegroups']))
		{
			$_SESSION['storagegroups'] = array('all');
		}			
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_REQUEST['setgroups'])
		{
			if(is_array($_REQUEST['setgroups']))
			{
				$_SESSION['storagegroups'] = $_REQUEST['setgroups'];
			}
			else
			{				
				$_SESSION['storagegroups'] = array($_REQUEST['setgroups']);
			}
		}
		
		# GET THE TOTAL NUMBER OF ROWS
		if(in_array("all",$_SESSION['storagegroups']))
		{
			$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(storage_id) FROM {$dbinfo[pre]}storage"));
		}
		else
		{
			//$storage_result2 = "SELECT COUNT(storage_id) FROM {$dbinfo[pre]}storage WHERE"
			$storage_result2 = "SELECT COUNT(storage_id) FROM {$dbinfo[pre]}storage LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}storage.storage_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['storagegroups']).")";
			$r_rows = mysqli_result_patch(mysqli_query($db,$storage_result2));
		}
		
		
		$pages = ceil($r_rows/$perpage);
	   
		# CHECK TO SEE IF THE CURRENT PAGE IS SET
		if(isset($_SESSION['currentpage']))
		{
			if(!empty($_REQUEST['updatepage'])) $_SESSION['currentpage'] = $_REQUEST['updatepage'];
		}
		else
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# CALCULATE THE STARTING RECORD						
		$startrecord = ($_SESSION['currentpage'] == 1) ? 0 : (($_SESSION['currentpage'] - 1) * $perpage);
		
		# FIX FOR RECORDS GETTING DELETED
		if($startrecord > ($r_rows - 1))
		{
			$startrecord-=$perpage;
		}
		
		# SELECT ITEMS
		if(in_array("all",$_SESSION['storagegroups']))
		{
			$storage_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
		}
		else
		{				
			$storage_result = mysqli_query($db,"SELECT storage_id,name,active FROM {$dbinfo[pre]}storage LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}storage.storage_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['storagegroups']).") GROUP BY {$dbinfo[pre]}storage.storage_id ORDER BY $listby $listtype LIMIT $startrecord,$perpage"); 				
		}	

		# CREATE DELETE LINKS - TO AVOID IF STATEMENTS LATER
		if($_SESSION['admin_user']['admin_id'] == "DEMO")
		{
			$dmode = "demo";
		}
		else
		{
			if($config['settings']['verify_before_delete'])
			{
				$dmode = "verify";
			}
			else
			{
				$dmode = "direct";
			}
		}
				
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_storage']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
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
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<script language="javascript" type="text/javascript">
		
		function delete_link_storage(dmode,verify,dtype,dlink){		
			if(dmode == "DEMO"){
				demo_message();
			} else {
				if(verify == 1){
					overlay_height();
					Effect.Appear('overlay',{ duration: 0.5, from: 0.0, to: 0.7 });
				
					$('messagebox').setStyle({
						display: "block"
					});
					$('innermessage').update("<p align='left' style='padding: 0px; margin: 0px; font-weight: bold;'>Deleting a Storage location will delete any media it contains from your library and local directories. Files on the remote storage will NOT be deleted for security reasons. You can remove them manually.</p><p align='right' style='padding: 10px 0 0 0; margin: 0px;'><input type='button' value='"+gen_b_del+"' id='deletebutton' class='button' onclick='process_delete(\"" + dlink + "\",\"" + dtype + "\");' /><input type='button' value='"+gen_b_cancel2+"' class='button' onclick='close_message();' /></p>");
				} else {
					if(dtype == "form"){
						document.datalist.action=dlink;
						document.datalist.submit();
					} else {
						location.href=dlink;
					}
				}
				// FOCUS ON THE DELETE BUTTON
				$('deletebutton').focus();
			}
			// JUMP TO THE TOP OF THE BROWSER WINDOW
			//scroll(0,0);
			
			false;
		}
		
		// DELETE RECORD FUNCION
		function deleterec(idnum){
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link_storage('<?php echo $_SESSION['admin_user']['admin_id']; ?>','1',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		// SWITCH STATUS ON HOMEPAGE OR ACTIVE
		function switch_status(item_type,item_id){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO"){
				demo_message();
			} else {
				$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = item_type + item_id;
				var loadpage = "mgr.storage.actions.php?action=" + item_type + "&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
		}
		
		// SUBMIT GROUP LIST
		function submit_groups(){
			$('grouplist').submit();
		}
		
		// DO WORKBOX ACTIONS
		function do_actions(){
			var selecteditem = $('actionsdd').options[$('actionsdd').selectedIndex].value;
			// REVERT BACK TO ACTIONS TITLE
			$('actionsdd').options[0].selected = 1;
			
			// CREATE THE WORKBOX OBJECT
			workboxobj = new Object();
			
			switch(selecteditem){
				case "assign_groups":					
					workboxobj.mode = 'assign_groups';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
				case "set_status":					
					workboxobj.mode = 'set_status';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
			}
		}
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.storage.edit.php?edit=new';
					});
				$('abutton_add_new').observe('mouseover', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.png';
					});
				$('abutton_add_new').observe('mouseout', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.off.png';
					});
			}
			
			// SELECT ALL BUTTON
			if($('abutton_select_all')!=null)
			{
				$('abutton_select_all').observe('click', function()
					{
						select_all_cb('datalist');
					});
				$('abutton_select_all').observe('mouseover', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.png';
					});
				$('abutton_select_all').observe('mouseout', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.off.png';
					});
			}
			
			// SELECT NONE BUTTON
			if($('abutton_select_none')!=null)
			{
				$('abutton_select_none').observe('click', function()
					{
						deselect_all_cb('datalist');
					});
				$('abutton_select_none').observe('mouseover', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.png';
					});
				$('abutton_select_none').observe('mouseout', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.off.png';
					});
			}
			
			// DELETE BUTTON
			if($('abutton_delete')!=null)
			{
				$('abutton_delete').observe('click', function()
					{
						deleterec();
					});
				$('abutton_delete').observe('mouseover', function()
					{
						$('img_delete').src='./images/mgr.button.delete.png';
					});
				$('abutton_delete').observe('mouseout', function()
					{
						$('img_delete').src='./images/mgr.button.delete.off.png';
					});
			}
			
			// GROUPS BUTTON
			if($('abutton_group')!=null)
			{
				$('abutton_group').observe('click', function()
					{
						// ONLY LOAD WHEN OPENING
						if($('group_selector').visible() == false)
						{
							load_group_selector();
						}
						$('group_selector').toggle();						
					});
				$('abutton_group').observe('mouseover', function()
					{
						$('img_group').src='./images/mgr.button.group.png';
					});
				$('abutton_group').observe('mouseout', function()
					{
						$('img_group').src='./images/mgr.button.group.off.png';
					});
			}
			
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
			// ONLY LOAD WHEN OPENING
			if($('group_selector').visible() == true)
			{
				load_group_selector();
			}
		});
		// LOAD GROUPS AREA
		function load_group_selector()
		{
			show_loader('group_selector');
			var loadpage = "mgr.groups.actions.php?mode=grouplist&mgrarea=<?php echo $page; ?>&ingroups=<?php if(in_array('all',$_SESSION['storagegroups'])){ echo 0; } else { echo 1; } ?>&exitpage=<?php echo $_SERVER['PHP_SELF']; ?>&sessname=storagegroups";
			var updatecontent = 'group_selector';
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
		}
	</script>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
			<?php
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
            ?>
            <!-- ACTIONS BAR AREA -->
            <?php
				$storage_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$storage_group_rows = mysqli_num_rows($storage_group_result);
			?>
            <div id="actions_bar">							
                <div class="sec_bar">
                    <img src="./images/mgr.badge.storage.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_storage']; ?></span> &nbsp; 
                </div>							
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
                
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                   	<?php if(!empty($r_rows)){ ?>
                    	<div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_del']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>					
					<?php } ?>
                    <?php if(in_array("proXXXX",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_group"><img src="./images/mgr.button.group.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_group" /><br /><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <?php
					if($r_rows)
					{
				?>
                               
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;">
                    
                    <select align="absmiddle" id="actionsdd" onchange="do_actions();">
                       	<option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                        <?php if($storage_group_rows and $r_rows and in_array("proXXXXX",$installed_addons)){ ?><option value="assign_groups">&nbsp; <?php echo $mgrlang['gen_au_itg']; ?></option><?php } ?>
                        <option value="set_status">&nbsp; <?php echo $mgrlang['gen_tostatus']; ?></option>
                    </select>
                </div>	
                </form>                
                
                <?php
					}
				?>
            </div>
            <!-- GROUPS WINDOW -->
			<form name="grouplist" id="grouplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            	<input type="hidden" name="dtype" value="groups" />
				<div style="<?php if(in_array('all',$_SESSION['storagegroups'])){ echo "display: none;"; } else { echo "display: block;"; } ?>" class="options_area" id="group_selector"></div>
			</form>
            <?php
				$media_result = mysqli_query($db,"SELECT COUNT(media_id) AS mediaCount, SUM(filesize) AS originalFilesize FROM {$dbinfo[pre]}media");
				$media = mysqli_fetch_object($media_result);
				
				$smedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_samples.sample_filesize) AS sampleFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_samples ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_samples.media_id");
				$smedia = mysqli_fetch_object($smedia_result);
				
				$tmedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_thumbnails.thumb_filesize) AS thumbFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_thumbnails ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_thumbnails.media_id");
				$tmedia = mysqli_fetch_object($tmedia_result);
				
				$total_fs = $media->originalFilesize + $smedia->sampleFilesize + $tmedia->thumbFilesize;
			?>
            <div class="perpage_bar" style="text-align: center">
                <img src="images/mgr.bars.gif" align="absmiddle" />&nbsp;<span style="font-weight: bold;"><?php echo $mgrlang['gen_total']; ?>: <?php echo $media->mediaCount . " " . $mgrlang['gen_files']; ?> / <?php echo convertFilesizeToMB($total_fs) . $mgrlang['gen_mb']; ?></span> (<?php echo $mgrlang['gen_ilg']; ?>)
            </div>
            <!-- START CONTENT -->
            <?php
                # CHECK TO MAKE SURE THERE ARE RECORDS
                if(!empty($r_rows))
				{
                    $ndate = new kdate;	
					if($r_rows > 10 and $perpage > 10)
					{
						include('mgr.perpage.php');	
					}
            ?>
                <div id="content">						
                    <form name="datalist" id="datalist" action="#" method="post">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- DATA TABLE HEADER -->
                        <tr>
							<?php $header_name = "storage_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                            <?php $header_name = "name";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['storage_t_alias']; ?></a></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center"><div class="content_table_header"><div><?php echo $mgrlang['gen_t_media']; ?></div></div></td>
							<?php $header_name = "active";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_active']; ?></a></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center"><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # SELECT LOOP THRU ITEMS									
                            while($storage = mysqli_fetch_object($storage_result))
							{
                            
                                # SET THE ROW COLOR
                                @$row_color++;
                                if ($row_color%2 == 0) 
								{
                                    $row_class = "list_row_on";
                                    $color_fade = "EEEEEE";
                                }
								else
								{
                                    $row_class = "list_row_off";
                                    $color_fade = "FFFFFF";
                                }	
								
								$folders_result = mysqli_query($db,"SELECT folder_id FROM {$dbinfo[pre]}folders WHERE storage_id = '$storage->storage_id'");
								while($folders = mysqli_fetch_array($folders_result))
								{
									$folderlist[] = $folders['folder_id'];
								}
								
								$allfolders = implode(',',$folderlist);
								
								$stmedia_result = mysqli_query($db,"SELECT COUNT(media_id) AS mediaCount,SUM(filesize) AS originalFilesize,umedia_id FROM {$dbinfo[pre]}media WHERE folder_id IN ($allfolders)"); // IN ($allfolders)
								$stmedia = mysqli_fetch_object($stmedia_result);
                        ?>
                            <tr><td height="1" colspan="8" bgcolor="ffffff" style="background-color: #FFFFFF;"></td></tr>
                            <tr class="<?php echo $row_class; ?>" onmouseover="cellover(this,'#<?php echo $color_fade; ?>',32);" onmouseout="cellout(this,'#<?php echo $color_fade; ?>');">
                                <td align="center"><a name="row_<?php echo $storage->storage_id; ?>"></a><?php echo $storage->storage_id; ?></td>
                                <td onclick="window.location.href='mgr.storage.edit.php?edit=<?php echo $storage->storage_id; ?>'"><a href="mgr.storage.edit.php?edit=<?php echo $storage->storage_id; ?>" class="editlink"><?php echo $storage->name; ?>&nbsp;</a></td>
                                <td align="center" nowrap>
									<strong><?php if($stmedia->mediaCount){ echo $stmedia->mediaCount; } else { echo "0"; } ?></strong> <?php echo $mgrlang['gen_files']; ?> / <strong><?php echo convertFilesizeToMB($stmedia->originalFilesize); ?></strong><?php echo $mgrlang['gen_mb']; ?>
                                </td>
                                <td align="center"><div id="ac<?php echo $storage->storage_id; ?>"><a href="javascript:switch_status('ac','<?php echo $storage->storage_id; ?>');"><img src="images/mgr.small.check.<?php echo $storage->active; ?>.png" border="0" /></a></div></td>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.storage.edit.php?edit=<?php echo $storage->storage_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a> 
                                    <a href="javascript:deleterec(<?php echo $storage->storage_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                                </td>
                                <td align="center" valign="middle" nowrap>                                                
                                    <input type="checkbox" name="items[]" value="<?php echo $storage->storage_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
                                </td>
                            </tr>
                        <?php
								unset($folderlist);
                            }
                        ?>                        
                    </table>
                    </form>					
                </div>
                <?php include('mgr.perpage.php'); ?>                
            <?php
                }
				else
				{
                    notice($mgrlang['gen_empty']);
                }
            ?>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>