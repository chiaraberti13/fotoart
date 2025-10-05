<?php
	###################################################################
	####	MANAGER MEMBERS PAGE                                   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 12-22-09                                     #### 
	###################################################################
		
		$page = "members";
		$lnav = "users";
		$supportPageID = '344';
		
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
		require_once('../assets/classes/mediatools.php');                    # INCLUDE MEDIA TOOLS CLASS	
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
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}members WHERE mem_id IN ($delete_array)");
					
					# DELETE GROUPS
					mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id IN ($delete_array) AND mgrarea = '$page'");
					
					# DELETE MEMBER TAGS
					if($config['delMemTags'])
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}keywords WHERE member_id IN ($delete_array)");
					
					# DELETE ACTIVITY LOG
					if($config['delActLogs'])
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}activity_log WHERE member_id IN ($delete_array) AND manager = 0");
					
					# DELETE MEMBER RATINGS
					if($config['delMemRatings'])
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_ratings WHERE member_id IN ($delete_array)");
						
					# DELETE MEMBER RATINGS
					if($config['delMemComments'])
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_comments WHERE member_id IN ($delete_array)");
					
					# DELETE MEMBER MEDIA	
					if($config['delMemMedia'])
					{
						# DELETE GALLERIES
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}galleries WHERE owner IN ($delete_array)");
						
						# DELETE MEDIA
						$memberMediaResult = mysqli_query($db,"SELECT media_id FROM {$dbinfo[pre]}media WHERE owner IN ($delete_array)");
						while($memberMedia = mysqli_fetch_assoc($memberMediaResult))
						{
							try
							{
								$media = new mediaTools($memberMedia['media_id']);
								$media->deleteMedia();
							}
							catch(Exception $e)
							{
								//echo $e->getMessage();
								//exit;
							}
						}
					}
					
					# DELETE MEMBER COMMISSION
					if($config['delMemCommission'])
						mysqli_query($db,"DELETE FROM {$dbinfo[pre]}commission WHERE contr_id IN ($delete_array)");
					
					
					# FIND OUT HOW MANY MORE ARE PENDING
					$_SESSION['pending_member_bios'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE bio_status = '2'"));			
					$_SESSION['pending_member_avatars'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE avatar_status = '2'"));
					$_SESSION['pending_members_inactive'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE status = '2'"));
					$_SESSION['pending_members'] = $_SESSION['pending_member_bios'] + $_SESSION['pending_member_avatars'] + $_SESSION['pending_members_inactive'];
					
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_delsuc'];
					
					// save to activity log [todo]
				}
				else
				{
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
			# UPDATE DETAILS AREA
			case "upheaders":
				// MAKE DETAILS STRING				
				foreach($_POST['mem_headers'] as $value)
				{
					@$headers_full.= "$value,";
				}
				// UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET mem_headers='$headers_full' WHERE settings_id = '1'");
				# REFRESH SETTINGS
				include('mgr.select.settings.php');
			break;
		}

		# IF AN ENTRY PAGE CLEAR CURRENTPAGE SESSION
		if(!empty($_REQUEST['ep']) && isset($_SESSION['currentpage'])){ $_SESSION['currentpage'] = 1; }
		
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO")
		{
			$delete_link = "DEMO_";
		}
		else
		{
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		# OUTPUT MESSAGES TO MESSAGE BAR
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
		$sortprefix="mem";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "mem_id";		
		require_once('mgr.datasort.logic.php');
		 
		# IF THIS IS AN ENTRY PAGE OR memgroups IS BLANK RESET THE memgroups SESSION	
		if($_GET['ep'] or empty($_SESSION['memgroups']) or $_REQUEST['dtype'] != 'groups')
		{
			$_SESSION['memgroups'] = array('all');
		}
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_REQUEST['setgroups'])
		{
			if(is_array($_REQUEST['setgroups']))
			{
				$_SESSION['memgroups'] = $_REQUEST['setgroups'];
			}
			else
			{				
				$_SESSION['memgroups'] = array($_REQUEST['setgroups']);
			}
		}
		
		# IF THIS IS AN ENTRY PAGE OR memgroups IS BLANK RESET THE memgroups SESSION	
		if($_GET['ep'] or empty($_SESSION['mem_ms']) or $_REQUEST['dtype'] != 'memberships')
		{
			$_SESSION['mem_ms'] = array('all');
		}
		# SEE IF ANY MEMBERSHIPS HAVE BEEN PASSED
		if($_REQUEST['setms'])
		{
			if(is_array($_REQUEST['setms']))
			{
				$_SESSION['mem_ms'] = $_REQUEST['setms'];
			}
			else
			{				
				$_SESSION['mem_ms'] = array($_REQUEST['setms']);
			}
		}
		# IMPLODE TO USE IN SQL
		$inms = implode(",",$_SESSION['mem_ms']);
	
		# SEE IF ANY SEARCH HAS BEEN PASSED
		if(!empty($_REQUEST['search']))
		{
			$_SESSION['mem_search'] = $_REQUEST['search'];
		}
		
		# CHECK TO SEE IF THE SEARCH FIELDS ARE GETTING SET
		if($_REQUEST['setsearchfields'])
		{
			$_SESSION['sf_array'] = $_REQUEST['setsearchfields'];
		}
		else
		{
			// set it to all?
		}
		
		# IF THIS IS AN ENTRY PAGE RESET SESSION	
		if($_GET['ep'] or empty($_SESSION['sf_array']) or $_REQUEST['dtype'] != 'search')
		{
			$_SESSION['sf_array'] = array('all');
		}
		
		# SET TO LOCAL VALUE
		$sf_array = $_SESSION['sf_array'];
		
		# FOR EASE MAKE THE VARIABLE LOCAL
		$insearch = $_SESSION['mem_search'];
		
		# PUT THE HEADER VALUES INTO AN ARRAY
		if(empty($config['settings']['mem_headers']))
		{
			$headers_array = array("mem_id","name","email","signup_date","last_login");
		}
		else
		{
			$headers_array = explode(",",$config['settings']['mem_headers']);
		}
					
		# MAKE SEARCH, LIST BY OR GROUP CHANGE GO BACK TO THE MAIN PAGE
		if($_REQUEST['search'] or $_GET['mgroup'] or $_GET['listby'])
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# IF THIS IS AN ENTRY PAGE RESET THE SESSION DISPLAY TYPE
		if($_GET['ep'] == '1')
		{
			$_SESSION['mem_dtype'] = 'default';
		}
		
		# IF THE DISPLAY TYPE IS RESET THROUGH GET UPDATE THE SESSION
		if($_REQUEST['dtype'])
		{
			$_SESSION['mem_dtype'] = $_REQUEST['dtype'];
			# RESET THE CURRENT PAGE
			if(isset($_SESSION['currentpage']))
			{
				$_SESSION['currentpage'] = 1;
			}
							
		}
		
		# SET THE DEFAULT SESSION DISPLAY TYPE - MIGHT NOT BE NEEDED
		if(!$_SESSION['mem_dtype'])
		{
			$_SESSION['mem_dtype'] = 'default';
		}
		
		# GET THE TOTAL NUMBER OF ROWS
		# DECIDE WHICH TYPE OF RECORDS TO PULL
		switch($_SESSION['mem_dtype'])
		{
			default:
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members"));
			break;
			
			case "search":
				// IF A SEARCH IS PERFORMED
				$search_words = explode(" ",$insearch);
				$search_word_length = 1;
				$snext = 1;
				foreach($search_words as $value){
					if(strlen($value) >= $search_word_length){
						// ADD OR IF YOU ARE ON THE SECOND TERM ON
						if($snext > 1){ $sql_search.= " or "; }
						$sql_search.= " f_name LIKE '%$value%'";
						if(in_array("sf_lastname",$sf_array) or in_array('all',$sf_array)){ 	$sql_search.= " or l_name LIKE '%$value%'"; 	}
						if(in_array("sf_id",$sf_array) or in_array('all',$sf_array)){			$sql_search.= " or mem_id LIKE '%$value%'"; 	}
						if(in_array("sf_uid",$sf_array) or in_array('all',$sf_array)){ 			$sql_search.= " or umem_id LIKE '%$value%'"; 	}
						if(in_array("sf_email",$sf_array) or in_array('all',$sf_array)){		$sql_search.= " or email LIKE '%$value%'"; 		}
						if(in_array("sf_website",$sf_array) or in_array('all',$sf_array)){		$sql_search.= " or website LIKE '%$value%'";	}
						if(in_array("sf_notes",$sf_array) or in_array('all',$sf_array)){		$sql_search.= " or notes LIKE '%$value%'";		}
						if(in_array("sf_comp_name",$sf_array) or in_array('all',$sf_array)){	$sql_search.= " or comp_name LIKE '%$value%'";	}
						//if(in_array("sf_address",$sf_array) or in_array('all',$sf_array)){	$sql_search.= " or address LIKE '%$value%' or address_2 LIKE '%$value%' or city LIKE '%$value%'"; }
						$snext++;
					}
				}
				//echo $sql_search; exit;
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE $sql_search"));
			break;
			
			case "groups":
				$mem_result2 = "SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['memgroups']).")";
				$r_rows = mysqli_result_patch(mysqli_query($db,$mem_result2));
			break;
			
			case "memberships":
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE membership IN ($inms)"));
			break;
		}
		
		$pages = ceil($r_rows/$perpage);
		
		# CHECK TO SEE IF THE CURRENT PAGE IS SET
		if(isset($_SESSION['currentpage']))
		{
			if(!empty($_REQUEST['updatepage']))
			{
				$_SESSION['currentpage'] = $_REQUEST['updatepage'];
			}
			else if($_REQUEST['setgroups'] == "all")
			{
				$_SESSION['currentpage'] = 1;
			}
		}
		else
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# CALCULATE THE STARTING RECORD						
		$startrecord = ($_SESSION['currentpage'] == 1) ? 0 : (($_SESSION['currentpage'] - 1) * $perpage);
		
		# CHOOSE WHICH FIELDS TO SELECT
		//$select_fields = 'mem_id,f_name,l_name,email,comp_name,website,signup_date,last_login,status,membership,credits,notes,avatar,avatar_status';
		$select_fields = '*';
		
		switch($_SESSION['mem_dtype'])
		{
			default: // all
				$mem_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "search":
				$mem_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id WHERE $sql_search ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "groups":
				$mem_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['memgroups']).") GROUP BY {$dbinfo[pre]}members.mem_id ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "memberships":
				$mem_result = mysqli_query($db,"SELECT $select_fields FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id WHERE membership IN ($inms) ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
			break;
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_members']; ?></title>
	<!-- LOAD THE MANAGER STYLE SHEET -->
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
    
	<script language="javascript">
		// DELETE RECORD FUNCION
		function deleterec(idnum)
		{
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		
		function submit_groups()
		{
			$('grouplist').submit();
		}
		
		function submit_memberships()
		{
			$('membershiplist').submit();
		}		
		
		// START NOTES PANEL
		function start_notes_panel(id)
		{
			var notes_panel = 'notes_win_' + id;
			//alert(notes_panel);
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
			show_div_fade_load(notes_panel,'mgr.member.actions.php?pmode=notes&id=' + id + '&pass=<?php echo md5($config['settings']['serial_number']); ?>','_content')
			//start_panel = setTimeout("show_div_fade_load('" + mem_panel + "','mgr.members.dwin.php?id="+id+"','_content')",'550');
		}
		
		// HIDE NOTES PANELS
		function clear_notes_panels()
		{
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
		}
		
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
		
		// START MEMBER DETAILS PANEL
		function start_avatar_panel(id)
		{
			var avatar_panel = 'avatar_win_' + id;
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
			show_div_fade_load(avatar_panel,'mgr.members.awin.php?id='+id,'_content');
		}
				
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.members.edit.php?edit=new';
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
			
			// SEARCH BUTTON
			if($('abutton_search')!=null)
				{
				$('abutton_search').observe('click', function()
					{
						$('mem_adv_search').toggle();
						$('search_field').focus();
						$('group_selector').hide();
						$('mem_details_sel').hide();
						$('ms_selector').hide();
					});
				$('abutton_search').observe('mouseover', function()
					{
						$('img_search').src='./images/mgr.button.search.png';
					});
				$('abutton_search').observe('mouseout', function()
					{
						$('img_search').src='./images/mgr.button.search.off.png';
					});
			}
			
			// HEADERS BUTTON
			if($('abutton_headers')!=null)
			{
				$('abutton_headers').observe('click', function()
					{						
						$('mem_details_sel').toggle();
						$('group_selector').hide();
						$('mem_adv_search').hide();
						$('ms_selector').hide();
					});
				$('abutton_headers').observe('mouseover', function()
					{
						$('img_headers').src='./images/mgr.button.details.png';
					});
				$('abutton_headers').observe('mouseout', function()
					{
						$('img_headers').src='./images/mgr.button.details.off.png';
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
						$('mem_adv_search').hide();
						$('mem_details_sel').hide();
						$('ms_selector').hide();						
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
			
			// MEMBERSHIP BUTTON
			if($('abutton_membership')!=null)
			{
				$('abutton_membership').observe('click', function()
					{
						$('ms_selector').toggle();
						$('mem_adv_search').hide();
						$('mem_details_sel').hide();
						$('group_selector').hide();
					});
				$('abutton_membership').observe('mouseover', function()
					{
						$('img_membership').src='./images/mgr.button.members.png';
					});
				$('abutton_membership').observe('mouseout', function()
					{
						$('img_membership').src='./images/mgr.button.members.off.png';
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
			
			// HEADER UPDATE BUTTON
			if($('button_headers_update')!=null)
			{
				$('button_headers_update').observe('click', function()
					{
						$('headers_form').submit();
					});
			}
			
			// SEARCH BUTTON
			if($('button_search')!=null)
			{
				$('button_search').observe('click', function()
					{
						$('search_from').submit();
					});
			}
			
			<?php
				// LOAD THE GROUPLIST AREA
				if($_SESSION['mem_dtype'] == 'groups' or $_GET['dtype'] == 'groups')
				{
					echo "load_group_selector();";
				}
			?>
		});	
		
		// LOAD GROUPS AREA
		function load_group_selector()
		{
			show_loader('group_selector');
			var loadpage = "mgr.groups.actions.php?mode=grouplist&mgrarea=<?php echo $page; ?>&ingroups=<?php if($_SESSION['mem_dtype'] == 'groups'){ echo 1; } else { echo 0; } ?>&exitpage=<?php echo $_SERVER['PHP_SELF']; ?>&sessname=memgroups";
			var updatecontent = 'group_selector';
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
		}
		
		// SWITCH STATUS ON ACTIVE
		function switch_active(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				//alert(newstatus);
				$('memberstatus_sp_'+item_id).hide();
				hide_sp();
				$('activecheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'activecheck' + item_id;
				var loadpage = "mgr.member.actions.php?pmode=ap&id=" + item_id + "&newstatus=" + newstatus + "&pass=<?php echo md5($config['settings']['serial_number']); ?>";
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// OPEN SUPPORT TICKET WINDOW
		//function support_ticket_window(mem_id)
		//{
		//	workboxobj.id = mem_id;
		//	workbox(workboxobj);
		//}
		
		// DELETE THE AVATAR
		function delete_avatar(mid)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
				}
				else
				{
					// IF VERIFT BEFORE DELETE IS ON
					if($config['settings']['verify_before_delete'])
					{
			?>
						message_box("<?php echo $mgrlang['mem_del_avatar']; ?>","<input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_avatar("+mid+");close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
			<?php
					}
					else
					{
						echo "do_delete_avatar(mid);";
					}
				}
			?>
		}
		// DELETE THE AVATAR
		function do_delete_avatar(mid)
		{
			//alert(mid);
			//show_loader_mt('avatar_box');
			var myAjax = new Ajax.Updater(
				'hidden_box', 
				'mgr.member.actions.php', 
				{
					method: 'get', 
					parameters: 'pmode=delete_avatar&mid='+mid+'&lp=1&pass=<?php echo md5($config['settings']['serial_number']); ?>&aid=<?php echo $_SESSION['admin_user']['admin_id']; ?>',
					evalScripts: true,
					onSuccess: function()
					{
						$('avatar_'+mid).src='images/mgr.no.avatar.gif';
						$('avatarcheck_'+mid).src='images/mgr.tiny.check.trans.png';
						hide_div('avatar_win_'+mid);
					}
				});
		}
		
		// APPROVE AVATAR
		function approve_avatar(mid)
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
				}
				else
				{
					echo "do_approve_avatar(mid);";
				}
			?>
		}
		
		// DELETE THE AVATAR
		function do_approve_avatar(mid)
		{
			//alert(mid);
			//show_loader_mt('avatar_box');
			var myAjax = new Ajax.Updater(
				'hidden_box', 
				'mgr.member.actions.php', 
				{
					method: 'get', 
					parameters: 'pmode=approve_avatar&mid='+mid+'&lp=1&pass=<?php echo md5($config['settings']['serial_number']); ?>&aid=<?php echo $_SESSION['admin_user']['admin_id']; ?>',
					evalScripts: true,
					onSuccess: function()
					{
						$('avatarcheck_'+mid).src='images/mgr.tiny.check.1.png';
						hide_div('avatar_win_'+mid);
					}
				});
		}
		
		// SWITCH STATUS ON ACTIVE
		function switch_status_bio(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('biostatus_sp_'+item_id).hide();
				hide_sp();
				$('biocheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'biocheck' + item_id;
				var loadpage = "mgr.member.actions.php?pmode=bio_status&id=" + item_id + "&newstatus=" + newstatus + '&pass=<?php echo md5($config['settings']['serial_number']); ?>';
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
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
				case "memberstatus":
					div_id = "memberstatus_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_closed mtag' onclick=\"switch_active('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_closed']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_active mtag' onclick=\"switch_active('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_active']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_pending mtag' onclick=\"switch_active('"+id+"',2);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
				break;
				case "biostatus":
					div_id = "biostatus_sp_"+id;
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' onclick=\"switch_status_bio('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_b_approved']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_pending mtag' onclick=\"switch_status_bio('"+id+"',2);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
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
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
			<?php
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
            ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">							
                <div class="sec_bar">
                    <img src="./images/mgr.badge.members.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_members']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                    <?php
                    	if(!empty($r_rows))
						{
					?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_del']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>					
                    <?php
                    	}
					?>
                    	<div style="float: left;" class="abuttons" id="abutton_search"><img src="./images/mgr.button.search.off.png" align="absmiddle" border="0" id="img_search" /><br /><?php echo $mgrlang['gen_b_search']; ?></div>
                    <?php
						if($r_rows)
						{
					?>
						
						<div style="float: left;" class="abuttons" id="abutton_headers"><img src="./images/mgr.button.details.off.png" align="absmiddle" border="0" id="img_headers" /><br /><?php echo $mgrlang['gen_b_headers']; ?></div>
					<?php
						}
					?>
					<?php if(in_array("pro",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_group"><img src="./images/mgr.button.group.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_group" /><br /><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>
                    <div style="float: left;" class="abuttons" id="abutton_membership"><img src="./images/mgr.button.members.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_membership" /><br /><?php echo $mgrlang['gen_b_memberships']; ?></div>
               		<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                
				<!--
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;"> 
                    <select align="absmiddle" id="actions" >
                        <option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                        <option value="">&nbsp; Email Members</option>
                        <option value="">&nbsp; Batch Edit</option>
                        <option value="">&nbsp; Send Email To</option>
                        <option value="">&nbsp; Print</option>
                        <option value="">&nbsp; Download (CSV)</option>
                    </select>
                </div>	
                </form>
				-->
                
            </div>
            <!-- GROUPS WINDOW -->
			<form name="grouplist" id="grouplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            	<input type="hidden" name="dtype" value="groups" />
				<div style="<?php if($_SESSION['mem_dtype'] == 'groups'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="group_selector"></div>
			</form>
            <!-- MEMBERSHIPS WINDOW AREA -->
            <?php
				$mem_ms_result = mysqli_query($db,"SELECT ms_id,flagtype,name FROM {$dbinfo[pre]}memberships ORDER BY name");
				$mem_ms_rows = mysqli_num_rows($mem_ms_result);
			?>
            <form name="membershiplist" id="membershiplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="dtype" value="memberships" />
            <div style="<?php if($_SESSION['mem_dtype'] == 'memberships'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="ms_selector">
				<div class="opbox_buttonbar">
					<p><?php echo $mgrlang['mem_show_only']; ?>:</p>
					<?php
						if($mem_ms_rows)
						{
                    ?>  
                     		<a href="#" onclick="select_all_cb('membershiplist');" class='actionlink'><?php echo $mgrlang['gen_b_sa']; ?></a><a href="#" onclick="deselect_all_cb('membershiplist');" class='actionlink'><?php echo $mgrlang['gen_b_sn']; ?></a><a href="javascript:submit_memberships();" class='actionlink'><?php echo $mgrlang['gen_t_show_sel']; ?></a><?php if(in_array('memberships',$_SESSION['admin_user']['permissions'])){ ?><a href='mgr.memberships.php?ep=1' class='actionlink'><?php echo $mgrlang['mem_edit_ms']; ?></a><?php } if($_SESSION['mem_dtype'] == 'memberships'){?><a href="mgr.members.php?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_exit_msps']; ?></a><?php } ?>
                    <?php
                        }
				    ?>
                </div>                
                <div class="options_area_box">
					<?php
						//echo "<div><input type='checkbox' class='checkbox' name='setgroups[]' value='none'"; if(in_array('none',$_SESSION['msgroups']) or in_array('all',$_SESSION['msgroups'])){ echo "checked "; } echo " /> <a href='mgr.memberships.php?setgroups=none'>None</a></div>";
						while($mem_ms = mysqli_fetch_object($mem_ms_result))
						{
							echo "<div class='opbox_list'><input type='checkbox' class='checkbox' name='setms[]' value='".$mem_ms->ms_id."' "; if(in_array($mem_ms->ms_id,$_SESSION['mem_ms']) or in_array('all',$_SESSION['mem_ms'])){ echo "checked "; } echo "/> "; if($mem_ms->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_ms->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_ms->flagtype' align='absmiddle' /> "; } echo "<a href='mgr.members.php?dtype=memberships&setms=$mem_ms->ms_id'>".substr($mem_ms->name,0,30)."</a></div>";
						}
					?>
				</div>                                      
			</div>
			</form>
            
            <!-- SELECT DETAILS TO SHOW ON LIST VIEW -->
            <form action="mgr.members.php?action=upheaders" id="headers_form" method="post">
            <input type="hidden" name="mem_headers[]" value="l_name" />
            <input type="hidden" name="dtype" value="<?php echo $_SESSION['mem_dtype']; ?>" />
            <div style="display: none;" class="options_area" id="mem_details_sel">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['mem_table_headers']; ?>:</p>
                    <a href="#" class='actionlink' id="button_headers_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>
                <div class="options_area_box">                    
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="mem_id" value="mem_id" <?php if(in_array("mem_id",$headers_array)){ echo "checked='checked'"; } ?> /><label for="mem_id"><?php echo $mgrlang['mem_member_num']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="l_name" value="l_name" checked="checked" disabled="disabled" /><label for="l_name"><?php echo $mgrlang['mem_name']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="email" value="email" <?php if(in_array("email",$headers_array)){ echo "checked='checked'"; } ?> /><label for="email"><?php echo $mgrlang['mem_f_email']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="credits" value="credits" <?php if(in_array("credits",$headers_array)){ echo "checked='checked'"; } ?> /><label for="credits"> <?php echo $mgrlang['gen_credits']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="comp_name" value="comp_name" <?php if(in_array("comp_name",$headers_array)){ echo "checked='checked'"; } ?> /><label for="comp_name"> <?php echo $mgrlang['mem_f_company_name']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="status" value="status" <?php if(in_array("status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="status"> <?php echo $mgrlang['mem_f_status']; ?><?php if($_SESSION['pending_members_inactive'] > 0){ echo "<span class='pending_number' id='header_memstatus'>".$_SESSION['pending_members_inactive']."</span>"; } ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="last_login" value="last_login" <?php if(in_array("last_login",$headers_array)){ echo "checked='checked'"; } ?> /><label for="last_login"> <?php echo $mgrlang['mem_last_login']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="signup_date" value="signup_date" <?php if(in_array("signup_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="signup_date"> <?php echo $mgrlang['mem_signup_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="website" value="website" <?php if(in_array("website",$headers_array)){ echo "checked='checked'"; } ?> /><label for="website"> <?php echo $mgrlang['mem_f_website']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="notes" value="notes" <?php if(in_array("notes",$headers_array)){ echo "checked='checked'"; } ?> /><label for="notes"> <?php echo $mgrlang['mem_f_notes']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="address" value="address" <?php if(in_array("address",$headers_array)){ echo "checked='checked'"; } ?> /><label for="address"> <?php echo $mgrlang['mem_f_address']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="membership" value="membership" <?php if(in_array("membership",$headers_array)){ echo "checked='checked'"; } ?> /><label for="membership"> <?php echo $mgrlang['mem_f_membership']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="memgroup" value="memgroup" <?php if(in_array("memgroup",$headers_array)){ echo "checked='checked'"; } ?> /><label for="memgroup"> <?php echo $mgrlang['gen_b_grps']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="avatar_status" value="avatar_status" <?php if(in_array("avatar_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="avatar_status"> <?php echo $mgrlang['mem_f_avatar']; ?><?php if($_SESSION['pending_member_avatars'] > 0){ echo "<span class='pending_number' id='header_avatar'>".$_SESSION['pending_member_avatars']."</span>"; } ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="mem_headers[]" id="bio_status" value="bio_status" <?php if(in_array("bio_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="bio_status"> <?php echo $mgrlang['mem_f_bio']; ?><?php if($_SESSION['pending_member_bios'] > 0){ echo "<span class='pending_number' id='header_bio'>".$_SESSION['pending_member_bios']."</span>"; } ?></label></strong></div>
                </div>
            </div>
            </form>
            <!-- ADVANCED SEARCH AREA -->
            <form action="mgr.members.php" method="post" id="search_from">
            <input type="hidden" name="dtype" value="search" /> 
            <div style="<?php if($_SESSION['mem_dtype'] == 'search'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="mem_adv_search">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['gen_search']; ?>:</p>
                    <a href="#" class='actionlink' id="button_search"><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_search']; ?></a><?php if($_SESSION['mem_dtype'] == 'search'){ ?><a href="mgr.members.php?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_exit_search']; ?></a><?php } ?>
                </div>
                <div class="options_area_box">
                    <p style="clear: both; margin: 6px 0 6px 0;"><input type="text" name="search" id='search_field' value="<?php if($_SESSION['mem_dtype'] == 'search'){ echo $_SESSION['mem_search']; } ?>" style="width: 250px;" /></p>
                    <div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sf_id" value="sf_id" <?php if(in_array("sf_id",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_id"> <?php echo $mgrlang['gen_t_id']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_firstname" value="sf_firstname" <?php if(in_array("sf_firstname",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_firstname"> <?php echo $mgrlang['mem_f_fname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_lastname" value="sf_lastname" <?php if(in_array("sf_lastname",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_lastname"> <?php echo $mgrlang['mem_f_lname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_email" value="sf_email" <?php if(in_array("sf_email",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_email"> <?php echo $mgrlang['mem_f_email']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_address" value="sf_address" <?php if(in_array("sf_address",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_address"> <?php echo $mgrlang['mem_f_address']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_comp_name" value="sf_comp_name" <?php if(in_array("sf_comp_name",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_comp_name"> <?php echo $mgrlang['mem_f_company_name']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_website" value="sf_website" <?php if(in_array("sf_website",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_website"> <?php echo $mgrlang['mem_f_website']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_notes" value="sf_notes" <?php if(in_array("sf_notes",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_notes"> <?php echo $mgrlang['mem_f_notes']; ?></label></strong></div>  
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_uid" value="sf_uid" <?php if(in_array("sf_uid",$sf_array) or in_array('all',$sf_array)){ echo "checked='checked'"; } ?> /><label for="sf_uid"> <?php echo $mgrlang['mem_unique_id']; ?></label></strong></div>                    
                </div>                                
            </div>
            </form>
            
            <!-- START CONTENT -->
            <?php
                # CHECK TO MAKE SURE THERE ARE RECORDS
                if(!empty($r_rows))
				{
                    $mdate = new kdate;
                    $mdate->distime = 1;
					if($r_rows > 10 and $perpage > 10)
					{
						include('mgr.perpage.php');	
					}
            ?>
                <div id="content">                    
                    <form name="datalist" id="datalist" method="post">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        
                        <!-- DATA TABLE HEADER -->
                        <?php
                            if($r_rows)
							{
                        ?>
                        <tr>
                            <?php $header_name = "mem_id";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "avatar_status";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['news_t_homepages']; ?>AVATAR</a><span class='pending_number' id='ph_avatar_status' <?php if($_SESSION['pending_member_avatars'] == 0){ echo "style='display: none;'"; } ?> onclick="window.location.href='mgr.members.php?listby=avatar_status&listtype=desc'"><?php echo $_SESSION['pending_member_avatars']; ?></span></div></div></td><?php } ?>
							<?php $header_name = "l_name";			if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%">															<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_name']; ?></a></div></div></td>
                            <?php $header_name = "test";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['photographer_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "email";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="left" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['email_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "comp_name";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="left" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['compname_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "website";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['website_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "signup_date";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['signup_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "last_login";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['lastlogin_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "status";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_status']; ?></a><span class='pending_number' id='ph_status' <?php if($_SESSION['pending_members_inactive'] == 0){ echo "style='display: none;'"; } ?> onclick="window.location.href='mgr.members.php?listby=status&listtype=desc'"><?php echo $_SESSION['pending_members_inactive']; ?></span></div></div></td><?php } ?>
                            <?php $header_name = "membership";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['membership_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "credits";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['credits_caps']; ?></a></div></div></td><?php } ?>
                            
							<?php $header_name = "memgroup";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><?php echo $mgrlang['groups_caps']; ?></div></div></td><?php } ?>
                            
							<?php $header_name = "notes";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['notes_caps']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "bio_status";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['news_t_dates']; ?>BIO</a><span class='pending_number' id='ph_bio_status' <?php if($_SESSION['pending_member_bios'] == 0){ echo "style='display: none;'"; } ?> onclick="window.location.href='mgr.members.php?listby=bio_status&listtype=desc'"><?php echo $_SESSION['pending_member_bios']; ?></span></div></div></td><?php } ?>
                            <?php $header_name = "address";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap>		<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['address_caps']; ?></a></div></div></td><?php } ?>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        <?php
                            }
                        ?> 
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # CURRENT DATE AND TIME
                            $datenow 	= date("Y-m-d H:i:s");
                            # SIGNUP DATE MINUS 2 DAYS
                            $signdate 	= date("Y-m-d H:i:s",strtotime("$datenow -2 days"));
                            # LAST LOGIN MINUS 3 HOURS
                            $logindate	= date("Y-m-d H:i:s",strtotime("$datenow -3 hours"));;
                            # CHECK TO SEE IF THE SEARCH RESULTS ARE EMPTY									
                            if(empty($r_rows))
							{
                                echo "<tr><td colspan='" . (count($headers_array)+1) . "' bgcolor='a91513' style='padding: 20px; background-color: #a91513; color: #ffffff; background-image: url(images/mgr.warning.bg.gif); background-repeat: repeat-x; border-bottom: 1px solid #6a0a09;'><img src='images/mgr.notice.icon.small.gif' align='absmiddle' />&nbsp;Your Search Returned 0 Results</td></tr>";
                            }
							else
							{
                                $zindex=1000;
								# SELECT LOOP THRU ITEMS
                                while($mgrMemberInfo = mysqli_fetch_object($mem_result))
								{
                                    # SPLIT THE MEMBER GROUPS
                                    $groups_array = explode(",",$mgrMemberInfo->memgroups);
                                    
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
									                                
                        ?>
                                <tr><td height="1" colspan="<?php echo count($headers_array)+2; ?>" bgcolor="ffffff" style="background-color: #ffffff;"></td></tr>
                                <tr class="<?php echo $row_class; ?>" onMouseOver="cellover(this,'#<?php echo $color_fade; ?>',32);" onMouseOut="cellout(this,'#<?php echo $color_fade; ?>');">
                                    <?php if(in_array("mem_id",$headers_array)){ ?><td align="center"><a name="row_<?php echo $mgrMemberInfo->mem_id; ?>"></a><?php echo $mgrMemberInfo->mem_id; ?></td><?php } ?>
                                    <?php if(in_array("avatar_status",$headers_array)){ ?>
                                    	<td align="center" nowrap>                                            
                                            <div id="avatar_win_<?php echo $mgrMemberInfo->mem_id; ?>" class="details_win" style="display: none; position: absolute; margin: -13px 0 0 44px">                                            	
                                            	<div id="avatar_win_<?php echo $mgrMemberInfo->mem_id; ?>_inner" class="details_win_inner">
                                                	<img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                    <div id="avatar_win_<?php echo $mgrMemberInfo->mem_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                </div>
                                            </div>
                                            <div style="width: 23px;">
                                            	<?php
                                                	if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_small.png"))
													{
														echo "<img src='../assets/avatars/" . $mgrMemberInfo->mem_id . "_small.png?rmd=" . create_unique() . "' width='19' onclick=\"start_avatar_panel('" . $mgrMemberInfo->mem_id . "');\" id='avatar_$mgrMemberInfo->mem_id' class='mediaFrame' />";
														if($mgrMemberInfo->avatar_status == 1)
														{
															echo "<img src='images/mgr.tiny.check.1.png' id='avatarcheck_$mgrMemberInfo->mem_id' style='margin: 0 0 -2px -3px;' />";
														}
														else
														{
															echo "<img src='images/mgr.tiny.check.trans.png' id='avatarcheck_$mgrMemberInfo->mem_id' style='margin: 0 0 -2px -3px;' />";
														}
													}
													else
													{
														echo "<img src='images/mgr.no.avatar.gif' width='19' class='mediaFrame' />";
													}
												?>
                                            </div>
                                   		</td>
									<?php } ?>
                                    <td onclick="window.location.href='mgr.members.edit.php?edit=<?php echo $mgrMemberInfo->mem_id; ?>'" nowrap>
                                    	<div style="float: left;"><a href="mgr.members.edit.php?edit=<?php echo $mgrMemberInfo->mem_id; ?>" class="editlink" style="margin-right: 10px;" onmouseover="start_mem_panel('<?php echo $mgrMemberInfo->mem_id; ?>');" onmouseout="cancel_mem_panel(<?php echo $mgrMemberInfo->mem_id; ?>);"><?php echo $mgrMemberInfo->l_name . ", " , $mgrMemberInfo->f_name; ?></a>&nbsp;</div>
                                        <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                            <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>" style="display: none;" class="mem_details_win">
                                                <div class="mem_details_win_inner">
                                                    <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                    <div id="more_info_<?php echo $mgrMemberInfo->mem_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <?php if(in_array("email",$headers_array)){ ?><td align="left" nowrap><img src="images/mgr.icon.email.gif" align="absmiddle" onclick="message_window('<?php echo $mgrMemberInfo->mem_id; ?>');" /> <a href="mailto:<?php echo $mgrMemberInfo->email; ?>"><?php echo $mgrMemberInfo->email; ?></a></td><?php } ?>
                                    <?php if(in_array("comp_name",$headers_array)){ ?><td align="center" nowrap><?php echo $mgrMemberInfo->comp_name; ?>&nbsp;</td><?php } ?>
                                    <?php if(in_array("website",$headers_array)){ ?><td align="left" nowrap><a href="<?php echo $mgrMemberInfo->website; ?>" target="_blank"><?php echo $mgrMemberInfo->website; ?></a>&nbsp;</td><?php } ?>
                                    <?php if(in_array("signup_date",$headers_array)){ ?><td align="center" nowrap><span style="color: <?php if($mgrMemberInfo->signup_date > $signdate){ echo "#000000"; } else { echo "#666666"; } ?>"><?php echo $mdate->showdate($mgrMemberInfo->signup_date); ?></span></td><?php } ?>
                                    <?php if(in_array("last_login",$headers_array)){ ?><td align="center" nowrap><span style="color: <?php if($mgrMemberInfo->last_login > $logindate){ echo "#000000"; } else { echo "#666666"; } ?>"><?php echo $mdate->showdate($mgrMemberInfo->last_login); ?></span></td><?php } ?>
                                    <?php if(in_array("status",$headers_array)){ ?>
                                    	<td align="center" nowrap>
                                        	<div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                            <div class='status_popup' id='memberstatus_sp_<?php echo $mgrMemberInfo->mem_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                            <div id="activecheck<?php echo $mgrMemberInfo->mem_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
                                                <?php
                                                    switch($mgrMemberInfo->status)
                                                    {
                                                        case 0: // NA
                                                            $tag_label = $mgrlang['gen_closed'];
                                                            $mtag = 'mtag_closed';
                                                        break;
                                                        case 1: // SHIPPED
                                                            $tag_label = $mgrlang['gen_active'];
                                                            $mtag = 'mtag_active';
                                                        break;
                                                        case 2: // NOT SHIPPED
                                                            $tag_label = $mgrlang['gen_pending'];
                                                            $mtag = 'mtag_pending';
                                                        break;
                                                    }
                                                ?>
                                              <div class='<?php echo $mtag; ?> mtag' onmouseover="show_sp('memberstatus_sp_<?php echo $mgrMemberInfo->mem_id; ?>');write_status('memberstatus','<?php echo $mgrMemberInfo->mem_id; ?>',<?php echo $mgrMemberInfo->status; ?>)"><?php echo $tag_label; ?></div>
                                            </div>
										</td>
									<?php } ?>
                                    <?php if(in_array("membership",$headers_array)){ ?><td align="center" nowrap><?php if($mgrMemberInfo->membership){ echo "<img src='images/mgr.icon.check.gif' />"; } else { echo "&nbsp;"; } ?></td><?php } ?>
                                    <?php if(in_array("credits",$headers_array)){ ?><td align="center" nowrap><?php echo $mgrMemberInfo->credits; ?>&nbsp;</td><?php } ?>
                                    <?php if(in_array("memgroup",$headers_array)){ ?>
                                    	<td align="left" nowrap style="font-size: 10px;">
											<?php												
												//$memgroup_result = mysqli_query($db,"SELECT flagtype,name FROM {$dbinfo[pre]}member_groups WHERE hidden != '1' and gr_id IN (" . substr($mgrMemberInfo->memgroup,1,strlen($mgrMemberInfo->memgroup)-2) . ")");
												//$memgroup_rows = mysqli_num_rows($memgroup_result);
												//while($memgroup = mysqli_fetch_object($memgroup_result)){
													//echo "<img src='images/mini_icons/$memgroup->flagtype' align='absmiddle' style='margin: 1px;'/> $memgroup->name<br />";
												//}
												if(empty($memgroup_rows)){ echo "&nbsp;"; }											
											?>
                                        </td>
									<?php } ?>
                                    <?php if(in_array("notes",$headers_array)){ ?>
                                    	<td align="center" nowrap>
											<?php
                                            	if($mgrMemberInfo->notes)
												{
											?>
                                                <div id="notes_win_<?php echo $mgrMemberInfo->mem_id; ?>" class="details_win" style="display: none; position: absolute; margin: -13px 0 0 -300px; width: 300px; text-align: left;">                                            	
                                                    <div id="notes_win_<?php echo $mgrMemberInfo->mem_id; ?>_inner" class="details_win_inner">
                                                        <img src="images/mgr.detailswin.arrow.right.png" style="position: absolute; margin: 13px 0 0 300px;" />
                                                        <div id="notes_win_<?php echo $mgrMemberInfo->mem_id; ?>_content" style="overflow: auto; border: 1px solid #fff; white-space: normal; min-height: 50px;"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                    </div>
                                                </div>
                                                <img src='images/mgr.icon.notes.1.png' onmouseover="start_notes_panel(<?php echo $mgrMemberInfo->mem_id; ?>);" onmouseout="clear_notes_panels();" />
                                            <?php
												}
												else
												{
													echo "&nbsp;";
												}
											?>
                                        </td>
									<?php } ?>
                                    <?php if(in_array("bio_status",$headers_array)){ ?>
                                    	<td align="center" nowrap>
                                        	<div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                            <div class='status_popup' id='biostatus_sp_<?php echo $mgrMemberInfo->mem_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                            <div id="biocheck<?php echo $mgrMemberInfo->mem_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
                                                <?php
                                                    switch($mgrMemberInfo->bio_status)
                                                    {
                                                        case 1: // APPROVED
                                                            $tag_label = $mgrlang['gen_b_approved'];
                                                            $mtag = 'mtag_dblue';
                                                        break;
                                                        case 2: // NOT SHIPPED
                                                            $tag_label = $mgrlang['gen_pending'];
                                                            $mtag = 'mtag_good';
                                                        break;
                                                    }
                                                ?>
                                              	<?php if($mgrMemberInfo->bio_status != '0'){ ?><div class='<?php echo $mtag; ?>' onmouseover="show_sp('biostatus_sp_<?php echo $mgrMemberInfo->mem_id; ?>');write_status('biostatus','<?php echo $mgrMemberInfo->mem_id; ?>',<?php echo $mgrMemberInfo->bio_status; ?>)"><?php echo $tag_label; ?></div><?php } ?>
                                            </div>
                                        </td>
									<?php } ?>
                                    <?php if(in_array("address",$headers_array)){ ?>
                                        <td align="left" nowrap>
                                        <?php if($mgrMemberInfo->address) echo $mgrMemberInfo->address."<br />"; ?>
                                        <?php if($mgrMemberInfo->city) echo $mgrMemberInfo->city.",&nbsp;"; ?>
										<?php 
											if($mgrMemberInfo->state)
											{
												$state_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}states WHERE state_id = '{$mgrMemberInfo->state}'");
                                   	 			$state_rows = mysqli_num_rows($state_result);
                                   				$state = mysqli_fetch_object($state_result);												
												echo $state->name."&nbsp;";
											}
										?>
										<?php if($mgrMemberInfo->postal_code) echo $mgrMemberInfo->postal_code."<br />"; ?>
                                        <?php 
											if($mgrMemberInfo->country)
											{
												$country_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}countries WHERE country_id = '{$mgrMemberInfo->country}'");
			                                    $country_rows = mysqli_num_rows($country_result);
                                  				$country = mysqli_fetch_object($country_result);												
												echo $country->name;
											}
										?>&nbsp;
                                        </td>
                                    <?php } ?>
                                    <td align="center" valign="middle" nowrap>
                                        <a href="mgr.members.edit.php?edit=<?php echo $mgrMemberInfo->mem_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                        <a href="javascript:deleterec(<?php echo $mgrMemberInfo->mem_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a> 
                                    </td>
                                    <td align="center" valign="middle" nowrap>
                                        <input type="checkbox" name="items[]" value="<?php echo $mgrMemberInfo->mem_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
                                    </td>
                                </tr>
                                
                        <?php
                                	$zindex-=2;
								}
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