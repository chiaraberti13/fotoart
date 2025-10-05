<?php
	###################################################################
	####	MANAGER GALLERIES PAGE                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 4-23-2007                                     ####
	####	Modified: 12-10-2009                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE		
			
		$page = "galleries";
		$lnav = "library";
		
		$supportPageID = '314';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE			
		require_once('../assets/includes/addons.php');								# INCLUDE MANAGER ADDONS FILE
		# ADDITIONAL GALLERY AREA ERROR CHECKS
		//require_once('mgr.galleries.ec.php');
		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		# MAKE SURE THIS ADDON IS INSTALLED
		//if(!$config['rss_feeds']){ echo $mgrlang['gen_addonerror']; exit; }
		
		# UPDATE SETTINGS IF DISPLAY CHANGES
		if($_GET['display']){
			$sql = "UPDATE {$dbinfo[pre]}settings SET mgr_gal_display='$_GET[display]' where settings_id  = '1'";
			$result = mysqli_query($db,$sql);
			
			$config['settings']['mgr_gal_display'] = $_GET['display'];
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# DELETE
			case "del":
				if(!empty($_REQUEST['items']))
				{
					updateGalleryVersion(); // Something has changed - update the gallery version
					
					$items = $_REQUEST['items'];
					if(!is_array($items)){
						$items = explode(",",$items);
					}
					$delete_array = implode(",",$items);
					
					# SET PARENTS OF DELETED DIRECTORIES TO 0
					$sql = "UPDATE {$dbinfo[pre]}galleries SET parent_gal='0' where parent_gal IN ($delete_array)";
					$result = mysqli_query($db,$sql);
						
					# REMOVE THE DIRECTORY - FIX SUBS - INCLUDE ERROR REPORTING				
					$gal_result = mysqli_query($db,"SELECT name,gallery_id,folder_name FROM {$dbinfo[pre]}galleries WHERE gallery_id IN ($delete_array)");
					while($gal = mysqli_fetch_object($gal_result)){
						
						# REMOVE ANY PRODUCT SHOTS
						$ip_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$gal->gallery_id' AND mgrarea = 'gallery'");
						$ip_rows = mysqli_num_rows($ip_result);
						while($ip = mysqli_fetch_object($ip_result))
						{
							# DELETE ORPHANED ITEM PHOTOS
							mysqli_query($db,"DELETE FROM {$dbinfo[pre]}item_photos WHERE ip_id = '$ip->ip_id'");
			
							$ipidzf = zerofill($ip->ip_id,4);
							$printzf = zerofill($log->prod_id,4);
							
							# DELETE SMALL, MED, ORG ITEM PHOTOS
							if(file_exists("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_org.jpg"))
							{
								unlink("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_org.jpg");
							}
							if(file_exists("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_med.jpg"))
							{
								unlink("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_med.jpg");
							}
							if(file_exists("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_small.jpg"))
							{
								unlink("../assets/item_photos/gallery".$printzf."_ip".$ipidzf."_small.jpg");
							}
						}
						# ADD TO THE LOG
						$log_titles.= "$gal->name ($gal->gallery_id), ";
					}
					
					# STRIP THE LAST COMMA FROM THE LOG
					if(substr($log_titles,strlen($log_titles)-2,2) == ", "){
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# REMOVE FROM MEDIA GALLERIES DATABASE
					$sql="DELETE FROM {$dbinfo[pre]}media_galleries WHERE gallery_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# REMOVE FROM ITEM GALLERIES DATABASE
					$sql="DELETE FROM {$dbinfo[pre]}item_galleries WHERE gallery_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# REMOVE FROM GALLERIES DATABASE
					$sql="DELETE FROM {$dbinfo[pre]}galleries WHERE gallery_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_galleries'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
					
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_delsuc'];
					
					# ADD ALERT FOR BUILDING THE MENU
					//menuBuild(0); // No longer needed
					
					//exit;
				} else {
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
		}
		
		if($_GET['mes'] == "new"){
			$vmessage = $mgrlang['galleries_mes_01'];
		}
		if($_GET['mes'] == "edit"){
			$vmessage = $mgrlang['galleries_mes_02'];
		}
		
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO"){
			$delete_link = "DEMO_";
		} else {
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		
		# SET THE MEMBER	
		if($_GET['ep'] or empty($_SESSION['galmem']))
		{
			$_SESSION['galmem'] = 0;
		}			
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_GET['setmem'])
		{
			$_SESSION['galmem'] = $_GET['setmem'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_galleries']; ?></title>
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
		<script language="javascript">
		// DELETE RECORD FUNCION
		function deleterec(idnum){
			if(idnum){ var goto = '&items=' + idnum; var dtype = 'link'; } else { var goto = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + goto);
		}
		
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.galleries.edit.php?edit=new&premem=<?php echo $_SESSION['galmem']; ?>';
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
			
			// MEMBERS BUTTON
			if($('abutton_members')!=null)
			{
				$('abutton_members').observe('click', function()
					{
						$('members_selector').toggle();						
					});
				$('abutton_members').observe('mouseover', function()
					{
						$('img_members').src='./images/mgr.button.members.png';
					});
				$('abutton_members').observe('mouseout', function()
					{
						$('img_members').src='./images/mgr.button.members.off.png';
					});
			}
			
		});
		
		// SWITCH STATUS ON ACTIVE
		function switch_status(item_type,item_id){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = item_type + item_id;
				var loadpage = "mgr.galleries.actions.php?mode=" + item_type + "&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
		}
		
		function load_gal_owner(selchar)
		{
			show_loader('wbmembers_inner');
			alphabet_clean();
			$('sl_'+selchar).className =  'alphabet_on';
			var pars = 'mode=members&gal_mem=<?php echo $_SESSION['galmem']; ?>&selchar='+selchar;
			var myAjax = new Ajax.Updater('wbmembers_inner', 'mgr.galleries.actions.php', {method: 'get', parameters: pars});
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
           	# CHECK FOR DEMO MODE
           	//demo_message($_SESSION['admin_user']['admin_id']);
			
			# INCLUDE DATASORTS CLASS
			require_once('mgr.class.datasort.php');			
			$sortprefix="gallery";
			$datasorts = new data_sorting;
			$datasorts->prefix = $sortprefix;
            $datasorts->clear_sorts($_GET['ep']);
			$id_field_name = "gallery_id";		
			require_once('mgr.datasort.logic.php');	
		?>
            <!-- ACTIONS BAR AREA -->
            <?php
				//echo "{$listby} / {$listtype} / {$_SESSION[galmem]}";
			
                // READ STRUCTURE FUNCTION															
                read_gal_structure(0,$listby,$listtype,$_SESSION['galmem']);
				
				//echo print_r($folders['name']); exit;
            ?>
            
            
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">							
                <div class="sec_bar">
                    <img src="./images/mgr.badge.galleries.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_galleries']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
                
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                    <?php if(!empty($galls)){ ?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_del']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>					
                    <?php } ?>
                    <?php if(in_array("contr",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_members"><img src="./images/mgr.button.members.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_wb_members']; ?>" id="img_members" /><br /><?php echo $mgrlang['gen_wb_members']; ?></div><?php } ?>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <!--
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;"> 
                    <select align="absmiddle" id="actions" >
                        <option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                        <option value="">&nbsp; Batch Edit Selected</option>
                        <option value="">&nbsp; Show Combined Stats</option>
                    </select>
                </div>	
                </form>
                -->
                
            </div>
            
            <?php
				# MAKE SURE THE CONTRIBUTORS ADD-ON IS INSTALLED
				if(in_array("contr",$installed_addons))
				{
					$member_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members"));
					
					$passobj = "{inputbox: 'permowner', multiple: '0'}";
					
					if($_SESSION['galmem'] != 0)
					{
						$member_result = mysqli_query($db,"SELECT mem_id,f_name,l_name,email FROM {$dbinfo[pre]}members WHERE mem_id = '$_SESSION[galmem]'");
						$mgrMemberInfo = mysqli_fetch_object($member_result);
					}
			?>
                <form name="memberlist" id="memberlist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="dtype" value="members" />
                <input type="hidden" name="permowner" id="permowner" value="<?php echo $_SESSION['galmem']; ?>" />
                <div style="<?php if($_SESSION['galmem'] != 0){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="members_selector">
                    <div class="opbox_buttonbar">
                        <p><?php echo $mgrlang['gen_mem_gals']; ?><span style="color: #900"><?php if($_SESSION['galmem'] != 0){ echo ": $mgrMemberInfo->f_name $mgrMemberInfo->l_name"; } ?></span></p>
                        <?php if($member_rows){ ?><a href="mgr.galleries.php?ep=1&setmem=0" class='actionlink'><?php echo $mgrlang['exit_members']; ?></a><?php } ?>
                    </div>               
                    <div class="options_area_box_b" id="wbmembers_inner">
                        <?php
                            # IF THERE ARE NO MEMBERS SHOW THE NOTICE
                            if(!$member_rows)
                            {
                                echo "<img src='images/mgr.notice.icon.png' align='absmiddle' /> {$mgrlang[galleries_no_mem]}";
                            }
                        ?>
                    </div>
                    <?php
                        # IF MEMBERS EXIST SHOW THE ALPHABET
                        if($member_rows)
                        {
                            echo "<p style='font-size: 11px; text-align: center; font-weight: normal; border-top: 1px solid #ffffff; border-bottom: none; padding-top: 4px;'><strong>{$mgrlang[mem_f_lname]}</strong>: ";
                            $x=0;
                            //$alphabet = explode(",",$mgrlang['alphabet']);
							
							$alphabet = array();
							
							$memlet_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members");
							while($memlet = mysqli_fetch_object($memlet_result)){
								$trimmed_lname = strtoupper(substr($memlet->l_name,0,1));
								if(!in_array($trimmed_lname,$alphabet)) $alphabet[] = $trimmed_lname;
							}
							
							sort($alphabet);
							
                            foreach($alphabet as $value){
                                //if($x==0){
                                //    echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value'  class='alphabet_on'>$value</a>";
                                //} else {
                                    echo "<a href=\"javascript:load_gal_owner('$value');\" id='sl_$value' class='alphabet_off'>$value</a>";
                                //}
                                $x++;
                            }
                            echo "</p>";
                        }
                        
                        # FIND THE STARTING LETTER TO LOAD
                        if($_SESSION['galmem'] != 0)
                        {
                            $fchar = strtoupper(substr($mgrMemberInfo->l_name,0,1));
                        }
                        # LOAD THE FIRST LETTER
                        else
                        {
                            $fchar = $alphabet[0];
                        }
                       
					   echo "<script>load_gal_owner('$fchar');</script>";
                    ?>
                </div>
                </form>
            <?php
				}
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
                if(!empty($galls)){
            ?>
            <!-- START CONTENT -->
            <div id="content" style="border-bottom: 3px solid #909090;">	
            <form name="datalist" id="datalist" id="datalist" method="post">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <!-- DATA TABLE HEADER -->
                    <tr>
                        <?php $header_name = "gallery_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                        <?php $header_name = "name";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_name']; ?></a></div></div></td>
                        <?php $header_name = "active";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_active']; ?></a></div></div></td>
                        <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                        <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                    </tr>
                    <?php
                        // FIND CURRENT DATE
						$curdate = new kdate;
            			$curdate->distime = 1;
						$curdate->date_format = 'none';
						//$curdate->adjust_date = 0;
						$cur_adjusted_date = $curdate->showdate(gmdate("Y-m-d H:i:s"));						
						//echo "<br />". $cur_adjusted_date;
						
						$gdate = new kdate;
						
						// LOOP THRU A SHOW THE FILES
                        function output_struc_array($id,$level=0)
						{
                            global $folders, $files, $countarray, $supported_extensions,$blue_folders,$levelgraphic, $mgrlang, $cur_adjusted_date, $gdate;
                            foreach($folders['name'] as $x => $value){
                                # SET THE ROW COLOR
                                @$row_color++;
                                if ($row_color%2 == 0) {
                                    $row_class = "list_row_off";
                                    $color_fade = "FFFFFF";
                                } else {
                                    $row_class = "list_row_on";
                                    $color_fade = "EEEEEE";
                                    //$row_class = "list_row_off";
                                    //$color_fade = "FFFFFF";
                                }
                                                                        
                                if($folders['parent_id'][$x] == $id){
                                    $margin=18*$level;
                                    
                                    //if(!$levelgraphic[$level]){ 											
                                        if($folders['folder_rows'][$x] > 1 and in_array($folders['folder_id'][$x],$folders['parent_id'])){
                                            $levelgraphic[$level] = "a";
                                        } else {
                                            $levelgraphic[$level] = "b";
                                        }
                                    //}
                                    
                                    echo "<tr class='$row_class' style='padding: 0px; margin: 0px;' onMouseOver=\"cellover(this,'#$color_fade',32);\" onMouseOut=\"cellout(this,'#$color_fade');\">";
                                    echo "<td align='center'>".$folders['folder_id'][$x]."</td>";
                                    echo "<td onclick=\"window.location.href='mgr.galleries.edit.php?edit=".$folders['folder_id'][$x]."'\" style=\"padding: 0px 0px 0px 0px; margin: 0px;\"><div style='float: left;'>";
                                        //echo "<input type=\"checkbox\" name=\"fbox\" checked /> ";
                                        echo "<img src=\"images/mgr.gallery.dots.blank.gif\" align=\"absmiddle\" />";
                                        
                                        for($z=0;$z<$level;$z++){
                                            if($z == ($level-1)){
                                                $rev_array = array_reverse($folders['parent_id'],true);
                                                $lastshown = array_search($folders['parent_id'][$x],$rev_array);
                                                
                                                if($x == $lastshown){
                                                    echo "<img src=\"images/mgr.gallery.dots.end.gif\" align=\"absmiddle\" />";
                                                    $levelgraphic[$level] = "b";
                                                } else {
                                                    echo "<img src=\"images/mgr.gallery.dots.con.gif\" align=\"absmiddle\" />";
                                                }
                                            } else {
                                                //if($z == 0){
                                                    //echo "<img src=\"images/mgr.gallery.dots.blank.gif\" align=\"absmiddle\" alt='$z' />";
                                                //} else {
                                                    if($levelgraphic[$z+1] == "a"){																	
                                                        echo "<img src=\"images/mgr.gallery.dots.gif\" align=\"absmiddle\" alt='$z' />";
                                                    } else {
                                                        echo "<img src=\"images/mgr.gallery.dots.blank.gif\" align=\"absmiddle\" alt='$z' />";
                                                    }
                                                //}
                                            }
                                        }
                                        //echo "<img src=\"images/mgr.no.avatar.gif\" align=\"absmiddle\" /> ";
                                        echo "&nbsp;<a href='mgr.galleries.edit.php?edit=".$folders['folder_id'][$x]."' class='editlink' onMouseOver=\"show_details_win('galdet_".$folders['folder_id'][$x]."','mgr.galleries.details.php?id=".$folders['folder_id'][$x]."');\" onMouseOut=\"hide_gdetails('galdet_".$folders['folder_id'][$x]."');\">";
										if($folders['password'][$x]) echo "<img src='images/mgr.icon.padlock.gif' style='vertical-align: middle' />&nbsp;";
										echo $folders['name'][$x] . "</a>";
										if($folders['expire_type'][$x] == 1 and $folders['expire_date'][$x] < $cur_adjusted_date){ echo " &nbsp;<span class='mtag_bad'>$mgrlang[gen_expired]</span>"; }										
										if($folders['active_type'][$x] == 1 and $folders['active_date'][$x] > $cur_adjusted_date){ echo " &nbsp;<span class='mtag_good'>$mgrlang[gen_publish]: ". $gdate->showdate($folders['active_date'][$x]) ."</span>"; }
										echo "</div>";
										//echo "<div id='galdet_".$folders['folder_id'][$x]."' class='galdet_win' style='display: none;'>Loading Details</div>";
										
										//echo "show_details_win('galdet_".$folders['folder_id'][$x]."','mgr.galleries.details.php?id=".$folders['folder_id'][$x]."');\" onMouseOut=\"hide_gdetails('galdet_".$folders['folder_id'][$x]."');\">" . $foldername . "</a><div id='fadeup_".$folders['folder_id'][$x]."' style='margin: -5px 0px 0px 10px; position: absolute; z-index: 75; display: none; background-color: #eeeeee; width: 200px; height: 200px;'>Loading Details</div>
										
										echo "<div style='float: left; overflow: visible;'>";
											echo "<div id='galdet_".$folders['folder_id'][$x]."' class='galdet_win' style='display: none;'>Loading Details</div>";
										echo "</div>";
										
                                    echo "</td>";
									echo "<td align='center'>";
									echo "<div id='ac".$folders['folder_id'][$x]."'><a href=\"javascript:switch_status('ac','".$folders['folder_id'][$x]."');\"><img src='images/mgr.small.check.".$folders['active'][$x].".png' border='0' /></a></div>";
									echo "</td>";
                                    echo "<td align='center' valign='middle' nowrap>";
                                        // WITH TEXT echo "<a href='mgr.assets.php?gallery=" . $folders['folder_id'][$x] . "' style='border: 1px solid #d6e1f2; padding: 2px; background-color: #ffffff'><img src='images/mgr.button.small.assets.gif' align='absmiddle' alt='" . $mgrlang['gen_assets'] . "' border='0' />View</a>&nbsp;";
                                    echo "<a href='mgr.media.php?dtype=gallery&galid=" . $folders['folder_id'][$x] . "' class='actionlink'><img src='images/mgr.button.small.assets.gif' align='texttop' alt='" . $mgrlang['gen_assets'] . "' border='0' />$mgrlang[gen_short_view]</a>&nbsp;";
                                    echo "<a href='mgr.add.media.php?gallery=" . $folders['folder_id'][$x] . "' class='actionlink'><img src='images/mgr.button.small.upload.gif' align='texttop' alt='" . $mgrlang['gen_upload'] . "' border='0' />$mgrlang[gen_short_upload]</a>&nbsp;";
                                    echo "<a href='mgr.galleries.edit.php?edit=" . $folders['folder_id'][$x] . "' class='actionlink'><img src='images/mgr.icon.edit.png' align='texttop' alt='" . $mgrlang['gen_edit'] . "' border='0' />$mgrlang[gen_short_edit]</a>&nbsp;";
                                    echo "<a href='javascript:deleterec(" . $folders['folder_id'][$x] . ");' class='actionlink'><img src='images/mgr.icon.delete.png' align='texttop' alt='" . $mgrlang['gen_delete'] . "' border='0' />$mgrlang[gen_short_delete]</a>&nbsp;";
                                    echo "</td><td align='center'>";
                                    /*
                                    echo "<a href='mgr.assets.php?gallery=" . $folders['folder_id'][$x] . "'><img src='images/mgr.button.small.assets.gif' align='absmiddle' alt='" . $mgrlang['gen_assets'] . "' border='0' /></a>";
                                    echo "<a href='mgr.add.files.php?gallery=" . $folders['folder_id'][$x] . "'><img src='images/mgr.button.small.upload.gif' align='absmiddle' alt='" . $mgrlang['gen_upload'] . "' border='0' /></a>";
                                    echo "<a href='mgr.galleries.edit.php?edit=" . $folders['folder_id'][$x] . "'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='" . $mgrlang['gen_edit'] . "' border='0' /></a>";
                                    echo "<a href='javascript:deleterec(" . $folders['folder_id'][$x] . ");'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='" . $mgrlang['gen_delete'] . "' border='0' /></a> ";
                                    */
                                    echo "<input type='checkbox' name='items[]' value='" . $folders['folder_id'][$x] . "' align='absmiddle' />";
                                    echo "</td></tr>\n\n";
                                    output_struc_array($folders['folder_id'][$x],$level+1);																																						
                                }					
                            }
                        }
                        output_struc_array(0);
                    ?>
                </form>
                </table>	
            </div>
			<?php
                } else {
                    notice($mgrlang['gen_empty']);
                }
            ?>
            <!-- END CONTENT -->
            <div class="footer_spacer"></div>
        </div>        
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>
