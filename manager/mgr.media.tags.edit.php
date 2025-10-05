<?php
	###################################################################
	####	MEDIA TAGS EDIT AREA                                   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "media_tags";
		$lnav = "library";
		
		$supportPageID = '337';
	
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
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		require_once('../assets/classes/mediatools.php');				# INCLUDE MEDIA TOOLS CLASS
		
		$ndate = new kdate;
		$ndate->distime = 0;
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$mt_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE key_id = '$_GET[edit]'");
			$mt_rows = mysqli_num_rows($mt_result);
			$mt = mysqli_fetch_object($mt_result);
			
			# SEE IF THIS WAS POSTED BY A MEMBER OR A VISITOR
			if($mt->member_id)
			{
				# FIND MEMBER DETAILS
				$member_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '$mt->member_id'");
				$member_rows = mysqli_num_rows($member_result);
				$mgrMemberInfo = mysqli_fetch_object($member_result);
				if($member_rows > 0)
				{
					if(in_array("members",$_SESSION['admin_user']['permissions']))
					{
						$link = "<a href='mgr.members.edit.php?edit=$mgrMemberInfo->mem_id' class='editlink' style='' onmouseover='start_mem_panel($mgrMemberInfo->mem_id);' onmouseout='cancel_mem_panel($mgrMemberInfo->mem_id);'>";
					}
					else
					{
						$link = "<a href='#' onmouseover='start_mem_panel($mgrMemberInfo->mem_id);' onmouseout='cancel_mem_panel($mgrMemberInfo->mem_id);'>";
					}
					$member_name = "$link<strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong></a>";
					if($mgrMemberInfo->email) $member_name.= " (<a href='mailto:$mgrMemberInfo->email'>$mgrMemberInfo->email</a>)";
					$mem_id = $mt->member_id;
					$member_name.= "<img src='images/mgr.icon.email.gif' align='absmiddle' style='cursor: pointer; margin-left: 6px;' onclick='message_window($mem_id);' />";
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
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":							
				$save_date = $_POST['posted_year']."-".$_POST['posted_month']."-".$_POST['posted_day']." " .$_POST['posted_hour']. ":" .$_POST['posted_minute']. ":00";	
				
				$save_date = $ndate->formdate_to_gmt($save_date);
				
				//echo $save_date; exit;
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# STIP ANY HTML THAT IS INSERTED
				
				if($langset['id'] == 'russian')
					$tag = mb_convert_case($tag, MB_CASE_LOWER, "UTF-8");
				else
					$tag = strtolower($tag); 
				
				$tag = trim(strip_tags($tag));
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}keywords SET 
							keyword='$tag',
							posted='$save_date',
							status='$status'
							where key_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# FIND OUT HOW MANY MORE ARE PENDING
				$_SESSION['pending_media_tags'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(key_id) FROM {$dbinfo[pre]}keywords WHERE status = '0' AND memtag = 1"));
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media_tags'],1,$mgrlang['gen_b_ed'] . " > <strong>$mgrlang[media_tags_f_com] ($saveid)</strong>");
				
				header("location: mgr.media.tags.php?mes=edit"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_tags']; ?></title>
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<script language="javascript">	
		function form_sumbit(){
			// REVERT BACK
			$('tag_div').className='fs_row_on';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.media.tags.edit.php?action=save_new" : "mgr.media.tags.edit.php?action=save_edit";

					# CHECK FIELD AND OUTPUT MESSAGE
					js_validate_field("tag","media_tags_f_tag",0);
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
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.media.tags.png" class="badge" />
                <p><strong><?php echo $mgrlang['media_tags_edit_header']; ?></strong><br /><span><?php echo $mgrlang['media_tags_edit_message']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <?php
						if($mt->media_id)
						{
							try
							{
								$media = new mediaTools($mt->media_id);
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
					?>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_tags_f_med']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_tags_f_med_d']; ?></span></p>
                        <p style="width: 300px;">
							<?php
								if($mediaStatus == 1)
								{
							?>
								<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=150" class="mediaFrame" style="float: left; margin-right: 10px;" />
							<?php
								}
								else
								{
									echo "<img src='images/mgr.theme.blank.gif' style='width: 150px;' class='mediaFrame' style='float: left; margin-right: 10px;' />";
								}
							?>
						</p>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="tag_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_tags_f_tag']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_tags_f_tag_d']; ?></span></p>
                        <input type="text" name="tag" id="tag" style="width: 200px; vertical-align: middle" value="<?php echo @stripslashes($mt->keyword); ?>" /><?php if(in_array('multilang',$installed_addons)){ ?>&nbsp;<span class="mtag_dblue" style="color: #FFF;"><?php echo ucfirst(strtolower($mt->language)); ?></span><?php } ?>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_tags_f_mem']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_tags_f_mem_d']; ?></span></p>
                        <div style="white-space: nowrap; margin-top: 20px; float: left;">
                        <?php
                            if(file_exists("../assets/avatars/" . $mem_id . "_small.png"))
                            {
                                echo "<img src='../assets/avatars/" . $mem_id . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                            }
                            else
                            {
                                echo "<img src='images/mgr.no.avatar.gif' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                            }
                        ?>
                        <?php echo $member_name; ?>
                        </div>
                        
                        <div style="float: left; margin-top: 20px;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                            <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>" style="display: none; margin-left: -14px" class="mem_details_win">
                                <div class="mem_details_win_inner">
                                    <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                    <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_tags_f_postd']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_tags_f_postd_d']; ?></span></p>
                        <?php 
							$form_posted = $ndate->date_to_form($mt->posted);
						?>
                        <select style="width: 55px;" name="posted_month">
							<?php
                                for($i=1; $i<13; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_posted['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 55px;" name="posted_day">
                            <?php
                                for($i=1; $i<=31; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_posted['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 75px;" name="posted_year">
                            <?php
                                for($i=2005; $i<(date("Y")+6); $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_posted['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        &nbsp;
                        <select style="width: 55px;" name="posted_hour">
                            <?php
                                for($i=0; $i<24; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_posted['hour'] == $dis_i_as or ($_GET['edit'] == "new" and date("H") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        :
                        <select style="width: 55px;" name="posted_minute">
                            <?php
                                for($i=1; $i<60; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_posted['minute'] == $dis_i_as or ($_GET['edit'] == "new" and date("i") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_tags_f_status']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_tags_f_status_d']; ?></span></p>
                        <select name="status">
                        	<option value="0" <?php if($mt->status == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_pending']; ?></option>
                            <option value="1" <?php if($mt->status == 1){ echo "selected"; } ?> ><?php echo $mgrlang['gen_b_approved']; ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.media.tags.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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