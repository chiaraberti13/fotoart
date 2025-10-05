<?php
	###################################################################
	####	PAGE CONTENT EDITOR   	                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		$page = "page_content";
		$lnav = "content";		
		$supportPageID = '358';
		
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
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

		# ACTIONS
		switch($_REQUEST['action'])
		{		
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
					$log_result = mysqli_query($db,"SELECT name,content_id FROM {$dbinfo[pre]}content WHERE content_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result))
					{
						$log_titles.= "$log->name ($log->content_id), ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", ")
					{
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# DELETE
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}content WHERE content_id IN ($delete_array)");
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_page_content'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
				
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_']; ?></title>
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
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.page.content.edit.php?edit=new';
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
				var loadpage = "mgr.page.content.actions.php?mode=" + item_type + "&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
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
			$sortprefix="page_content";
			$datasorts = new data_sorting;
			$datasorts->prefix = $sortprefix;
            $datasorts->clear_sorts($_GET['ep']);
			$id_field_name = "content_id";			
			require_once('mgr.datasort.logic.php');				
            			
			$ca_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content_areas WHERE mgrarea = '$page' ORDER BY sortorder");
            $ca_rows = mysqli_num_rows($ca_result);
			
			//echo $_rows; exit;
        ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.content.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_page_content']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                    <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
            </div>
                                
            <!-- START CONTENT -->
            <div id="content">
                <form name="datalist" id="datalist" method="post">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <!-- DATA TABLE HEADER -->
                    <tr>
                        <?php $header_name = "content_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                        <?php $header_name = "name";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="left" width="100%"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_name']; ?></a></div></div></td>
                        <?php $header_name = "active";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_active']; ?></a></div></div></td>
						<td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                        <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                    </tr>
                    <!-- DATA TABLE CONTENT -->
                    <?php                        
                        # SELECT LOOP THRU ITEMS									
                        while($ca = mysqli_fetch_object($ca_result))
                        {
							$row_color = 0;
							echo "<tr><td colspan='5' style='background-color: #CCC; padding: 10px; color: #333; font-weight: bold; font-size: 12px; text-align: center;'>".$mgrlang[$ca->langid]."</td></tr>";
							
							$content_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content WHERE ca_id = '$ca->ca_id' ORDER BY $listby $listtype");
            				$content_rows = mysqli_num_rows($content_result);
							while($content = mysqli_fetch_object($content_result))
                        	{							
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
                                                    
                    ?>
                            <tr><td height="1" colspan="5" bgcolor="ffffff" style="background-color: #FFFFFF;"></td></tr>
                            <tr class="<?php echo $row_class; ?>" onMouseOver="cellover(this,'#<?php echo $color_fade; ?>',32);" onMouseOut="cellout(this,'#<?php echo $color_fade; ?>');">
                                <td align="center" onclick="window.location.href='mgr.page.content.edit.php?edit=<?php echo $content->content_id; ?>'"><a name="row_<?php echo $content->content_id; ?>"></a><?php echo $content->content_id; ?></td>
                                <td align="left" onclick="window.location.href='mgr.page.content.edit.php?edit=<?php echo $content->content_id; ?>'"><a href="mgr.page.content.edit.php?edit=<?php echo $content->content_id; ?>" class="editlink"><?php echo $content->name; ?></a></td>
                                <td align="center"><?php if($content->ca_id == '2' or $content->ca_id == '3'){ ?><div id="ac<?php echo $content->content_id; ?>"><a href="javascript:switch_status('ac','<?php echo $content->content_id; ?>');"><img src="images/mgr.small.check.<?php echo $content->active; ?>.png" border="0" /></a></div><?php } ?></td>
								<td align="center" valign="middle" nowrap>
                                    <a href="mgr.page.content.edit.php?edit=<?php echo $content->content_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
									<?php
										if($content->locked)
										{
											echo "&nbsp;";
										}
										else
										{
									?>
                                    	<a href="javascript:deleterec(<?php echo $content->content_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                                    <?php
										}
									?>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <input type="checkbox" name="items[]" value="<?php echo $content->content_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" <?php if($content->locked){ echo "disabled='disabled'"; } ?> />
                                </td>
                            </tr>
                    <?php
							}
                        }
                    ?>                        
                </table>
                </form>					
            </div>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>
	</div>
		
</body>
</html>
<?php mysqli_close($db); ?>