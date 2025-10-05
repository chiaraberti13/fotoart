<?php
	###################################################################
	####	SUPPORT TICKETS EDIT AREA                              ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "support_tickets";
		$lnav = "users";
		
		$supportPageID = '349';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
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
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		$ticket_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}tickets WHERE ticket_id = '$_GET[edit]'");
		$ticket_rows = mysqli_num_rows($ticket_result);
		$ticket = mysqli_fetch_object($ticket_result);
		
		# FIND MEMBER DETAILS
		$member_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id,phone,umem_id,language FROM {$dbinfo[pre]}members WHERE mem_id = '$ticket->member_id'");
		$member_rows = mysqli_num_rows($member_result);
		$mgrMemberInfo = mysqli_fetch_object($member_result);

		if(in_array("members",$_SESSION['admin_user']['permissions']))
		{
			$link = "<a href='mgr.members.edit.php?edit=$mgrMemberInfo->mem_id' class='editlink' onmouseover='start_mem_panel($mgrMemberInfo->mem_id);' onmouseout='cancel_mem_panel($mgrMemberInfo->mem_id);'>";
		}
		else
		{
			$link = "<a href='#'>";
		}
		$member_name = "$link<strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong></a>";
		//if($mgrMemberInfo->email) $member_name.= " ($mgrMemberInfo->email)</a>";
		$mem_id = $ticket->member_id;
		
		# GET THE MEMBERS ADDRESS
		$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '$mgrMemberInfo->mem_id'");
		$address_rows = mysqli_num_rows($address_result);
		$address = mysqli_fetch_object($address_result);
		
		$ndate = new kdate;
		$ndate->distime = 1;
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":
			
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}tickets SET 
							status='$status',
							lastupdated='" . gmt_date() . "',
							viewed='0',
							updatedby='".$_SESSION['admin_user']['admin_id']."'
							where ticket_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				if($reply)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}ticket_messages (
							ticket_id,
							message,
							submit_date,
							admin_response,
							admin_id
							) VALUES (
							'$saveid',
							'$reply',
							'" . gmt_date() . "',
							'1',
							'".$_SESSION['admin_user']['admin_id']."'
							)";
					$result = mysqli_query($db,$sql);
					//$saveid = mysqli_insert_id($db);
				}
				
				if($_FILES['fileattach']['name'])
				{
					$newfilename = date("U") . "." . findexts($_FILES['fileattach']['name']);
					move_uploaded_file($_FILES['fileattach']['tmp_name'],"../assets/files/". $newfilename);
					# INSERT FILE INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}ticket_files (
							ticket_id,
							original_name,
							saved_name,
							uploaddate,
							admin_id
							) VALUES (
							'$saveid',
							'".$_FILES['fileattach']['name']."',
							'$newfilename',
							'" . gmt_date() . "',
							'".$_SESSION['admin_user']['admin_id']."'
							)";
					$result = mysqli_query($db,$sql);
				}
				
				if($notify && $email != "")
				{
					// Build email
					$toEmail = $email;
					$content = getDatabaseContent('newMemberTicketResponse'); // Get content from db				
					
					$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members where mem_id = '{$ticket_member_id}'");
					$member = mysqli_fetch_assoc($member_result);
					
					# GET THE MEMBERS ADDRESS
					$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '{$ticket_member_id}'");
					$address_rows = mysqli_num_rows($address_result);
					$address = mysqli_fetch_assoc($address_result);					
					$member['primaryAddress'] = $address;
					
					//print_r($member);
					//exit;
					
					$member['unencryptedPassword'] = k_decrypt($member['password']);
					
					$smarty->assign('member',$member);
					
					$content['name'] = $smarty->fetch('eval:'.$content['name']);
					$content['body'] = $smarty->fetch('eval:'.$content['body']);
					$options['replyEmail'] = $config['settings']['support_email'];
					$options['replyName'] = $config['settings']['business_name'];
					kmail($toEmail,$toEmail,$config['settings']['support_email'],$config['settings']['business_name'],$content['name'],$content['body'],$options); // Send email
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_support_tickets'],1,$mgrlang['gen_b_ed'] . " > <strong>$mgrlang[tickets_f_com] ($saveid)</strong>");
				
				# NUMBER OF SUPPORT TICKETS PENDING
				$_SESSION['pending_support_tickets'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(ticket_id) FROM {$dbinfo[pre]}tickets WHERE status = '2'"));
				
				header("location: mgr.support.tickets.php?mes=edit"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_support_tickets']; ?></title>
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
    <!-- MESSAGE WINDOW JS -->
	<script type="text/javascript" src="mgr.js.messagewin.php"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
    <!-- INCLUDE THE EDITOR JS -->
	<?php include_editor_js(); ?>
	<script language="javascript">	

		function form_sumbit(){
			// REVERT BACK
			//$('tag_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.support.tickets.edit.php?action=save_new" : "mgr.support.tickets.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					//js_validate_field("tag","tickets_f_tag",1);			
			?>
				// FIX SPACES
				fixspaces();

				//$('data_form').action = "<?php echo $action_link; ?>";
				//$('data_form').submit();
			<?php
				}
			?>
		}
		Event.observe(window, 'load', function()
		{
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
			resizefsdiv('messages',360);
			resizefsdiv('reply_div',365);
			resizefsdiv('ticket_files',365);
		});	
		
		Event.observe(window, 'resize', function()
			{
				resizefsdiv('messages',360);
				resizefsdiv('reply_div',365);
				resizefsdiv('ticket_files',365);
			});
		
		// START MEMBER DETAILS PANEL
		var start_panel;
		function start_mem_panel(id)
		{
			var mem_panel = 'more_info_' + id;
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
			start_panel = setTimeout("show_div_fade_load('" + mem_panel + "','mgr.members.dwin.php?id="+id+"','_content')",'550');
		}
		
		// BRING THE PANEL TO THE FRONT
		function mem_details_tofront(id)
		{
			var mem_panel = 'more_info_' + id;
			z_index++;
			$(mem_panel).setStyle({
				zIndex: z_index
			});
		}
		
		// CANCEL LOAD AND CLOSE ALL PANELS
		function cancel_mem_panel(id)
		{
			clearTimeout(start_panel);
			$$('.mem_details_win').each(function(s) { s.setStyle({display: "none"}) });
			$("more_info_" + id + "_content").update('<img src="images/mgr.loader.gif" style="margin: 40px;" />');
		}
		
		// DELETE MESSAGE
		function deletemes(item_id)
		{
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				<?php
					// IF VERIFT BEFORE DELETE IS ON
					if($config['settings']['verify_before_delete'])
					{
				?>
					message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_deletemes("+item_id+");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_deletemes(item_id);";
					}
				?>
			}
		}
		
		function do_deletemes(item_id)
		{
			//$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
			var updatecontent = 'hidden_box';
			var loadpage = "mgr.support.tickets.actions.php?mode=deletemes&id=" + item_id;
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars, evalScripts: true});
		}
		
		// DELETE FILES
		function deletefile(item_id)
		{
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				<?php
					// IF VERIFT BEFORE DELETE IS ON
					if($config['settings']['verify_before_delete'])
					{
				?>
					message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_deletefile("+item_id+");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_deletefile(item_id);";
					}
				?>
			}
		}
		
		function do_deletefile(item_id)
		{
			//$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
			var updatecontent = 'hidden_box';
			var loadpage = "mgr.support.tickets.actions.php?mode=deletefile&id=" + item_id;
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars, evalScripts: true});
		}
	</script>
    
    <style>
		.message_admin{
			margin: 3px;
			overflow: auto;
			border: 1px dotted #CCC;
			background-color: #dfe6ec;
			padding: 20px;
		}
		.message_member{
			margin: 3px;
			overflow: auto;
			border: 1px dotted #CCC;
			padding: 20px;
			background-color: #fff;
		}
		.message_member p, .message_admin p{
			cursor: auto;
			font-weight: normal;
			width: 100%
		}
		p.message_details{
			text-align: left;
			font-size: 11px;
			margin-bottom: 10px;
			float: left;
			width: 300px;
		}
	</style>
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
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();" enctype="multipart/form-data">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <input type="hidden" name="email" value="<?PHP echo $mgrMemberInfo->email; ?>" />
			<input type="hidden" name="ticket_member_id" value="<?PHP echo $mgrMemberInfo->mem_id; ?>" />
            
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.support.tickets.png" class="badge" />
                <p><strong><?php echo $mgrlang['tickets_edit_header']; ?></strong><br /><span><?php echo $mgrlang['tickets_edit_message']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div style="padding: 10px 20px 0 20px; margin-bottom: 5px; overflow: auto;">                        
                    <div class="tg_header_info" style="height: 120px;">                       
                        <?php
                            $avatar_width2 = 100;
                        ?>
                        <div id="avatar_summary" style="float: left; background-image: url(images/mgr.loader.gif); background-repeat: no-repeat; background-position: center; min-height: 50px;">
                            <?php
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
                                        echo "<img src='mgr.display.avatar.php?mem_id=$mgrMemberInfo->mem_id&size=$avatar_width2&ext=$mgrMemberInfo->avatar' style='border: 4px solid #FFF; margin-right: 1px;' class='mediaFrame' />";
                                    }
                                    else
                                    {
                                        echo "<div style='margin: 4px 0 0 10px; padding: 10px; background-color: #fae8e8; width: 200px; border: 1px solid #ba0202;'><img src='images/mgr.icon.mem.summary2.gif' style='border: 2px solid #eeeeee; margin-left: 10px; margin-right: 10px;' width='40' align='left' />$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></div>";
                                    }
                                }
                                else
                                { 
                                    echo "<img src='images/mgr.icon.mem.summary.gif' style='border: 4px solid #FFF; margin-right: 1px;' width='$avatar_width2' class='mediaFrame' />";
                                }
                            ?>
                        </div>
                        <div style="float: left; margin-left: 10px;">
                            <?php
                                $country_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}countries WHERE country_id = '$address->country'");
                                $country_rows = mysqli_num_rows($country_result);
                                $country = mysqli_fetch_object($country_result);
                                
                                $state_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}states WHERE state_id = '$address->state'");
                                $state_rows = mysqli_num_rows($state_result);
                                $state = mysqli_fetch_object($state_result);
                            ?>
                            
                            <table cellpadding="0" cellspacing="4">
                                <tr>
                                    <td rowspan="<?php if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language){ echo 4; } else { echo 3; } ?>" nowrap valign="top">
                                        <div style="float: left; margin-right: 5px;"><?php echo $member_name; ?></div>
                                        <div style="float: left; margin-top: -106px;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                            <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>" style="display: none;" class="mem_details_win">
                                                <div class="mem_details_win_inner">
                                                    <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 123px 0 0 -9px;" />
                                                    <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                </div>
                                            </div>
                                        </div>
                                        <br /><a href="mailto:<?php echo @stripslashes($mgrMemberInfo->email); ?>"><?php echo @stripslashes($mgrMemberInfo->email); ?></a> <img src="images/mgr.icon.email.gif" align="absmiddle" style="cursor: pointer; margin-left: 3px;" onclick="message_window('<?php echo $mgrMemberInfo->mem_id; ?>');" />
                                        <br />
                                        <?php
                                            echo $address->address . "<br />";
                                            if($address->address_2){ echo $address->address_2 . "<br />"; }
                                            echo $address->city;											
                                            if($state_rows){ echo ", " . $state->name; }
                                            echo " " . $address->postal_code . "<br />";
                                            if($country_rows){ echo $country->name; }
                                            if($mgrMemberInfo->phone){ echo "<br /><br />".$mgrMemberInfo->phone; }
                                        ?>
                                    </td>
                                    <td rowspan="<?php if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language){ echo 4; } else { echo 3; } ?>" width="10">&nbsp;</td> 
                                    <td nowrap width="80"><strong><?php echo $mgrlang['mem_member_num']; ?>:</strong></td>
                                    <td nowrap><?php echo $mgrMemberInfo->mem_id; ?></td>
                                </tr>
                                <tr>
                                    <td nowrap><strong><?php echo $mgrlang['mem_last_login']; ?>:</strong></td>
                                    <td nowrap><?php if($mgrMemberInfo->signup_date == "0000-00-00 00:00:00"){ echo $mgrlang['mem_never']; } else { echo $ndate->showdate($mgrMemberInfo->last_login); } ?></td>
                                </tr>
                                <tr>
                                    <td nowrap valign="top"><strong><?php echo $mgrlang['mem_signup_date']; ?>:</strong>&nbsp;&nbsp;&nbsp;</td>
                                    <td nowrap valign="top"><?php echo $ndate->showdate($mgrMemberInfo->signup_date); ?></td>
                                </tr>
                                <?php
									if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language)
									{
								?>
                                <tr>
                                    <td nowrap valign="top"><strong><?php echo $mgrlang['gen_language']; ?>:</strong></td>
                                    <td nowrap valign="top"><span class="mtag_dblue" style="color: #FFF;"><?php echo ucfirst($mgrMemberInfo->language); ?></span></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </div>
                        
                    </div>
                    <div class="tg_header_info" style="height: 120px;"> 
                        <table cellpadding="0" cellspacing="4">
                            <tr>
                                <td nowrap><strong><?php echo $mgrlang['tickets_id']; ?>:</strong>&nbsp;&nbsp;&nbsp;</td>
                                <td nowrap><?php echo $ticket->ticket_id; ?></td>
                            </tr>
                            <tr>                                
                                <td nowrap><strong><?php echo $mgrlang['tickets_opened']; ?>:</strong>&nbsp;&nbsp;&nbsp;</td>
                                <td nowrap><?php echo $ndate->showdate($ticket->opened); ?></td>
                            </tr>
                            <tr>
                                <td nowrap><strong><?php echo $mgrlang['tickets_updated']; ?>:</strong>&nbsp;&nbsp;&nbsp;</td>
                                <td nowrap><?php echo $ndate->showdate($ticket->lastupdated); ?></td>
                            </tr>
                            <tr>
                                <td nowrap><strong><?php echo $mgrlang['tickets_status']; ?>:</strong>&nbsp;&nbsp;&nbsp;</td>
                                <td nowrap>
                                    <?php
                                        switch($ticket->status)
                                        {
                                            case 0:
                                                echo "<span class='mtag_bad' style='color: #fff; font-weight: bold;'>$mgrlang[gen_closed]</span>";
                                            break;
                                            case 1:
                                                echo "<span class='mtag_dblue' style='color: #fff; font-weight: bold;'>$mgrlang[gen_open]</span>";
                                            break;
                                            case 2:
                                                echo "<span class='mtag_good' style='color: #fff; font-weight: bold;'>$mgrlang[gen_pending]</span>";
                                            break;
                                        }									
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
				<?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_summary']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_summary_d']; ?></span></p>
                        <div style="padding-top: 10px;"><?php echo $ticket->summary; ?></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_reply']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_reply_d']; ?></span></p>
                        <div style="float: left;" id="reply_div">
							<?php
                                show_editor("100%","200px",'',"reply","editor");
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_tickcon']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_tickcon_d']; ?></span></p>
                        
                        <div style="float: left;" id="messages">
                        	<?php
								$message_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}ticket_messages WHERE ticket_id = '$ticket->ticket_id' ORDER BY submit_date DESC");
								if($message_rows = mysqli_num_rows($message_result))
								{
									while($message = mysqli_fetch_object($message_result))
									{
										$message_class = ($message->admin_response) ? "message_admin" : "message_member";
										echo "<div style='clear: both;' class='$message_class' id='message_$message->message_id'>";
											if($message->admin_response)
											{
												$admin_result = mysqli_query($db,"SELECT username FROM {$dbinfo[pre]}admins WHERE admin_id = '$message->admin_id'");
												$admin_rows = mysqli_num_rows($admin_result);
												$admin = mysqli_fetch_object($admin_result);
												echo "<p class='message_details'><strong>$admin->username</strong><br />".$ndate->showdate($message->submit_date);
											}
											else
											{
												echo "<p class='message_details'><strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong><br />".$ndate->showdate($message->submit_date);
											}
											echo "<br />{$mgrlang[tickets_mes_id]}: $message->message_id</p><p style='float: right; width: 100px; text-align: right; margin-right: -10px'><a href='javascript:deletemes($message->message_id);' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='".$mgrlang['gen_delete']."' border='0' />".$mgrlang['gen_short_delete']."</a></p>";
										echo "<p>$message->message</p></div>";
									}
								}
								else
								{
									echo $mgrlang['tickets_no_mes'];	
								}
							?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_files']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_files']; ?></span></p>
                        <div style="padding: 10px; float: left;">
                            <?php echo $mgrlang['tickets_attach_file']; ?>:
                            <input type="file" name="fileattach" />
                        </div>
                        <?php
							//echo date("U");
							$file_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}ticket_files WHERE ticket_id = '$ticket->ticket_id' ORDER BY uploaddate DESC");
							if($file_rows = mysqli_num_rows($file_result))
							{
						?>
                        <div id="ticket_files" class="fs_row_part2" style="margin-left: 275px;">			
                            <table width="100%">
                                <tr>
                                    <th><?php echo $mgrlang['tickets_file_id']; ?></th>
                                    <th align="left"><?php echo $mgrlang['tickets_filename']; ?></th>
                                    <th align="left"><?php echo $mgrlang['tickets_added']; ?></th>
                                    <th align="center"><?php echo $mgrlang['tickets_upped_by']; ?></th>
                                    <th><?php echo $mgrlang['tickets_size']; ?></th>
                                    <th></th>
                                </tr>
                                <?php
                                    while($file = mysqli_fetch_object($file_result))
                                    {
                                        # SET THE ROW COLOR
                                        @$row_color++;
                                        if ($row_color%2 == 0)
                                        {
                                            $backcolor = "EEEEEE";
                                        }
                                        else
                                        {
                                            $backcolor = "FFFFFF";
                                        }
                                        
                                        if($file->admin_id)
                                        {
                                            $admin_result = mysqli_query($db,"SELECT username FROM {$dbinfo[pre]}admins WHERE admin_id = '$file->admin_id'");
                                            $admin_rows = mysqli_num_rows($admin_result);
                                            $admin = mysqli_fetch_object($admin_result);
                                            $backcolor = "dfe6ec";
                                            $upload_by_name = "$admin->username";
                                        }
                                        else
                                        {
                                            $upload_by_name = "$mgrMemberInfo->f_name $mgrMemberInfo->l_name";
                                        }
                                ?>
                                    <tr style="background-color: #<?php echo $backcolor; ?>" id="file_row_<?php echo $file->file_id; ?>">
                                        <td align="center"><?php echo $file->file_id; ?></td>
                                        <td><a href="../assets/files/<?php echo $file->saved_name; ?>"><?php echo $file->original_name; ?></a></td>
                                        <td><?php echo $ndate->showdate($file->uploaddate) ?></td>
                                        <td align="center"><strong><?php echo $upload_by_name; ?></strong></td>
                                        <td align="center"><?php echo round(filesize("../assets/files/$file->saved_name")/1024) . $mgrlang['gen_kb']; ?></td>
                                        <td align="center"><a href="javascript:deletefile(<?php echo $file->file_id; ?>);" class="actionlink"><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a></td>
                                    </tr>                                
                        <?php
								}
								echo "</table></div>";
							}
						?>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_notify']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_notify_d']; ?></span></p>
                        <input type="checkbox" name="notify" id="notify" checked />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tickets_f_status']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tickets_f_status_d']; ?></span></p>
                        <select name="status">
                        	<option value="0" <?php if($ticket->status == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_closed']; ?></option>
                            <option value="1" <?php if($ticket->status == 2 or $ticket->status == 1){ echo "selected"; } ?> ><?php echo $mgrlang['gen_open']; ?></option>
                            <option value="2" ><?php echo $mgrlang['gen_pending']; ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.support.tickets.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>