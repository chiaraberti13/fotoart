<?php
	###################################################################
	####	MEDIA COMMENTS EDITOR                                  ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		$page = "media_comments";
		$lnav = "library";		
		$supportPageID = '333';
		
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php"))
		{			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		}
		else
		{ 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		require_once('../assets/classes/mediatools.php');				# INCLUDE MEDIA TOOLS CLASS

		# ACTIONS
		switch($_REQUEST['action'])
		{			
			case "save_approved_status":
				save_approved_status($page,'media_comments','mc_id','status');
				# FIND OUT HOW MANY MORE ARE PENDING
				$_SESSION['pending_media_comments'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments WHERE status = '0'"));
			break;			
			# DELETE
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
					$log_result = mysqli_query($db,"SELECT mc_id,comment FROM {$dbinfo[pre]}media_comments WHERE mc_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result))
					{
						$comment_short = substr($log->comment,0,24)."...";
						$log_titles.= "$comment_short ($log->mc_id), ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", ")
					{
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# DELETE
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_comments WHERE mc_id IN ($delete_array)");
					
					# FIND OUT HOW MANY MORE ARE PENDING
					$_SESSION['pending_media_comments'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments WHERE status = '0'"));
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_comments'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
				
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
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_comments']; ?></title>
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
		function deleterec(idnum)
		{
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		
		Event.observe(window, 'load', function()
			{			
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
		});	
		
		// SWITCH STATUS ON ACTIVE
		function switch_status_comment(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('comment_sp_'+item_id).hide();
				hide_sp();
				$('commentcheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'commentcheck' + item_id;
				var loadpage = "mgr.media.comments.actions.php?mode=ap&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// DO WORKBOX ACTIONS
		function do_actions(){
			var selecteditem = $('actionsdd').options[$('actionsdd').selectedIndex].value;
			// REVERT BACK TO ACTIONS TITLE
			$('actionsdd').options[0].selected = 1;
			
			// CREATE THE WORKBOX OBJECT
			workboxobj = new Object();
			
			switch(selecteditem){
				case "set_approved":					
					workboxobj.mode = 'set_approved';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
			}
		}
		
		// START MEMBER DETAILS PANEL
		var start_panel;
		function start_mem_panel(id,mem)
		{
			var mem_panel = 'more_info_' + mem + '-' + id;
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
			start_panel = setTimeout("show_div_fade_load('" + mem_panel + "','mgr.members.dwin.php?id="+mem+"','_content')",'550');
		}
		
		// BRING THE PANEL TO THE FRONT
		function mem_details_tofront(id,mem)
		{
			var mem_panel = 'more_info_' + mem + '-' + id;
			z_index++;
			$(mem_panel).setStyle({
				zIndex: z_index
			});
		}
		
		// CANCEL LOAD AND CLOSE ALL PANELS
		function cancel_mem_panel(id,mem)
		{
			clearTimeout(start_panel);
			$$('.mem_details_win').each(function(s) { s.setStyle({display: "none"}) });
			$("more_info_" + mem + "-" + id + "_content").update('<img src="images/mgr.loader.gif" style="margin: 40px;" />');
		}
		
		
		function show_sp(id)
		{
			clearTimeout(status_popup_timeout);
			$(id).show();
			$$('.status_popup').each(function(e){ if(id != e.id){ e.hide(); } });
		}
		
		var status_popup_timeout;
		
		function hide_sp()
		{
			clearTimeout(status_popup_timeout);
			status_popup_timeout = setTimeout(function(){$$('.status_popup').each(function(e){ e.hide(); });},200); // e.fade({ duration: 0.3 });
		}
		
		function clear_sp_timeout()
		{
			clearTimeout(status_popup_timeout);
		}
		
		function write_status(mode,id,curstatus)
		{
			var content = ''
			var div_id = ''
			//alert(curstatus);
			switch(mode)
			{
				case "comment":
					div_id = "comment_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' onclick=\"switch_status_comment('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' onclick=\"switch_status_comment('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_b_approved']; ?></div>"; }
				break;
			}
			$(div_id).update(content);
		}
	</script>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?></td>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # OUTPUT MESSAGE IF ONE EXISTS
			verify_message($vmessage);

            # INCLUDE DATASORTS CLASS
			require_once("mgr.class.datasort.php");			
			$sortprefix="media_comments";
			$datasorts = new data_sorting;
			$datasorts->prefix = $sortprefix;
            $datasorts->clear_sorts($_GET['ep']);
			$id_field_name = "mc_id";			
			require_once('mgr.datasort.logic.php');				
            
			$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments"));
            
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
			
			if($startrecord < 0) $startrecord = 0; // Make sure this doesn't become negative
			$media_comments_result = mysqli_query($db,"SELECT mc_id,comment,member_id,media_id,status,posted,language FROM {$dbinfo[pre]}media_comments ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
            $media_comments_rows = mysqli_num_rows($media_comments_result);
        ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.media.comments.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_media_comments']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <?php if(!empty($media_comments_rows)){ ?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                    <?php } ?>                	
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;">
                    <?php
						if($media_comments_rows)
						{
					?>
                    <select align="absmiddle" id="actionsdd" onchange="do_actions();">
                       	<option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                        <?php if($media_comments_rows){ ?><option value="set_approved">&nbsp; <?php echo $mgrlang['gen_toapproved']; ?></option><?php } ?>
                    </select>
                   	<?php
						}
					?>
                </div>	
                </form>
            </div>
                                
            <!-- START CONTENT -->
            <?php
                # CHECK TO MAKE SURE THERE ARE RECORDS
                if(!empty($r_rows))
				{			
					if($r_rows > 10 and $perpage > 10)
					{
						include('mgr.perpage.php');	
					}
            ?>
                <div id="content">
                	<form name="datalist" id="datalist" method="post">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- DATA TABLE HEADER -->
                        <tr>
							<?php $header_name = "mc_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                            <?php $header_name = "media_id";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_media']; ?></a></div></div></td>
                            <?php $header_name = "member_id";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_member']; ?></a></div></div></td>
                            <?php $header_name = "comment";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['media_comments_t_comment']; ?></a></div></div></td>
                            <?php $header_name = "posted";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_submitted']; ?></a></div></div></td>
                            <?php $header_name = "status";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_status']; ?></a><span class='pending_number' id='ph_status' <?php if($_SESSION['pending_media_comments'] == 0){ echo "style='display: none;'"; } ?> onclick="window.location.href='mgr.media.comments.php?listby=status&listtype=asc'"><?php echo $_SESSION['pending_media_comments']; ?></span></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>                        
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # CREATE A DATE OBJECT
							$mcdate = new kdate;	
							
							$zindex = 1000;
							
							# SELECT LOOP THRU ITEMS									
                            while($media_comments = mysqli_fetch_object($media_comments_result))
							{
								
								if($media_comments->media_id)
								{
									try
									{
										$media = new mediaTools($media_comments->media_id);
										$mediaInfo = $media->getMediaInfoFromDB();
										$thumbInfo = $media->getIconInfoFromDB();										
										$verify = $media->verifyMediaSubFileExists('icons');										
										$mediaStatus = $verify['status'];
									}
									catch(Exception $e)
									{
										$mediaStatus = 0;
									}
								}
                            
                                # SET THE ROW COLOR
                                @$row_color++;
                                if ($row_color%2 == 0)
								{
                                    $row_class = "list_row_on";
                                    $color_fade = "EEEEEE";
									$border_color = "FFFFFF";
                                }
								else
								{
                                    $row_class = "list_row_off";
                                    $color_fade = "FFFFFF";
									$border_color = "EEEEEE";
                                }
								
								# SHORTEN THE COMMENT IF IT IS TOO LONG
								$comment_short = (strlen($media_comments->comment) > 100) ? substr($media_comments->comment,0,100)."...": $media_comments->comment;
								
								# SEE IF THIS WAS POSTED BY A MEMBER OR A VISITOR
								if($media_comments->member_id)
								{
									# FIND MEMBER DETAILS
									$member_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '$media_comments->member_id'");
           							$member_rows = mysqli_num_rows($member_result);
									$mgrMemberInfo = mysqli_fetch_object($member_result);
									if($member_rows > 0)
									{
										$member_name = "<strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong>";
										if($mgrMemberInfo->email) $member_name.= "<!-- ($mgrMemberInfo->email)-->";
										$mem_id = $media_comments->member_id;
									}
									else
									{
										$member_name = "<strong>$mgrlang[gen_visitor]</strong>";
										$mem_id = 0;
									}
								}
								else
								{
									$member_name = "<strong>$mgrlang[gen_visitor]</strong>";
									$mem_id = 0;
								}                     
                        ?>
                            <tr><td height="1" colspan="5" bgcolor="ffffff" style="background-color: #FFFFFF;"></td></tr>
                            <tr class="<?php echo $row_class; ?>" onMouseOver="cellover(this,'#<?php echo $color_fade; ?>',32);" onMouseOut="cellout(this,'#<?php echo $color_fade; ?>');">
                                <td align="center"><a name="row_<?php echo $media_comments->mc_id; ?>"></a><?php echo $media_comments->mc_id; ?></td>
                                <td align="center" valign="middle" onclick="window.location.href='mgr.media.comments.edit.php?edit=<?php echo $media_comments->mc_id; ?>'">
									<?php
										if($mediaStatus == 1)
										{
									?>
										<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=30" class="mediaFrame" />
									<?php
										}
										else
										{
											echo "<img src='images/mgr.theme.blank.gif' style='width: 30px;' class='mediaFrame' />";
										}
									?>
								</td>
                                <td align="left" nowrap="nowrap" valign="middle">
									<div style="float: left;">
                                    <?php
                                        if(file_exists("../assets/avatars/" . $mem_id . "_small.png"))
                                        {
                                            echo "<img src='../assets/avatars/" . $mem_id . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                                        }
                                        else
                                        {
                                            echo "<img src='images/mgr.no.avatar.gif' width='19' style='vertical-align: middle; margin-right: 8px;' class='mediaFrame' />";
                                        }
                                        if($member_rows > 0 and $media_comments->member_id){
                                    ?>
                                            <a href="<?php if(in_array("members",$_SESSION['admin_user']['permissions'])){ echo "mgr.members.edit.php?edit=$mgrMemberInfo->mem_id"; } else { echo "#"; } ?>" class="editlink" style="margin-right: 10px;" onmouseover="start_mem_panel(<?php echo $media_comments->mc_id; ?>,<?php echo $mgrMemberInfo->mem_id; ?>);" onmouseout="cancel_mem_panel(<?php echo $media_comments->mc_id; ?>,<?php echo $mgrMemberInfo->mem_id; ?>);"><?php echo $member_name; ?></a>
                                            </div>
                                            <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                                <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>-<?php echo $media_comments->mc_id; ?>" style="display: none;" class="mem_details_win">
                                                    <div class="mem_details_win_inner">
                                                        <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 23px 0 0 -9px;" />
                                                        <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>-<?php echo $media_comments->mc_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
										}
										else
										{
                                            echo $member_name . "</div>";
                                        }
                                    ?>
                                </td>
                                <td onclick="window.location.href='mgr.media.comments.edit.php?edit=<?php echo $media_comments->mc_id; ?>'" style="padding: 10px 10px 10px 13px;"><?php echo $comment_short; ?><?php if(in_array('multilang',$installed_addons)){ ?>&nbsp;&nbsp;<span class="mtag_dblue mtag" style="color: #FFF;"><?php echo ucfirst(strtolower($media_comments->language)); ?></span><?php } ?></td>
                                <td align="center" nowrap="nowrap"><?php echo $mcdate->showdate($media_comments->posted); ?></td>
                                <td align="center" width="110">
                                  	<div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='comment_sp_<?php echo $media_comments->mc_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                    <div id="commentcheck<?php echo $media_comments->mc_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
										<?php
                                            switch($media_comments->status)
                                            {
                                                case 0: // PENDING
                                                    $tag_label = $mgrlang['gen_pending'];
                                                    $mtag = 'mtag_pending';
                                                break;
                                                case 1: // APPROVED
                                                    $tag_label = $mgrlang['gen_b_approved'];
                                                    $mtag = 'mtag_approved';
                                                break;
                                            }
                                        ?>
                                   	  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_sp('comment_sp_<?php echo $media_comments->mc_id; ?>');write_status('comment','<?php echo $media_comments->mc_id; ?>',<?php echo $media_comments->status; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.media.comments.edit.php?edit=<?php echo $media_comments->mc_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                    <a href="javascript:deleterec(<?php echo $media_comments->mc_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <input type="checkbox" name="items[]" value="<?php echo $media_comments->mc_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
                                </td>
                            </tr>
                        <?php
								$zindex-=2;
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
                    notice($mgrlang['gen_empty_short']);
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