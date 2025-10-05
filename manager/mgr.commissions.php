<?php
	###################################################################
	####	BILLINGS			  	                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-31-2011                                     ####
	####	Modified: 1-31-2011                                    #### 
	###################################################################
	
		$page = "contrsales";
		$lnav = "sales";		
		$supportPageID = '356'; //http://www.ktools.net/wiki/index.php?action=artikel&cat=95&id=293&artlang=en
		
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
		require_once('../assets/includes/photo.puzzle.inc.php');							# FP
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	

		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS

		# ACTIONS
		switch($_REQUEST['action']){
			# DELETE
			case "del":
				if(!empty($_REQUEST['items'])){
					$items = $_REQUEST['items'];
					if(!is_array($items)){
						$items = explode(",",$items);
					}				
					$delete_array = implode(",",$items);
					
					# DELETE
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}commission WHERE com_id IN ($delete_array)");
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_contr_sales'],1,$mgrlang['gen_b_del'] . " > <strong>$delete_array</strong>");
					
				} else {
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
			# UPDATE FILTERS
			case "upfilters":
				# CONVERT TO STRING
				$filters_full = implode(",",$_POST['commission_filters']);
				# UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET commission_filters='$filters_full' WHERE settings_id = '1'");
				# REFRESH SETTINGS
				include('mgr.select.settings.php');
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
		require_once("mgr.class.datasort.php");			
		$sortprefix="commissions";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "com_id";			
		require_once('mgr.datasort.logic.php');				
		
		# UPDATE FILTERS
		if($_GET['dtype'] == "filters")
		{
			
		}
		
		# IF THIS IS AN ENTRY PAGE RESET SESSION	
		if($_GET['ep'] or empty($_SESSION['sfb_array']) or $_REQUEST['dtype'] != 'search')
		{
			$_SESSION['sfb_array'] = array('all');
		}
		
		# SET TO LOCAL VALUE
		$sfb_array = $_SESSION['sfb_array'];
		
		# PUT THE FILTER VALUES INTO AN ARRAY
		if(strlen($config['settings']['commission_filters']) > 0)
		{	
			$filters_array = explode(",",$config['settings']['commission_filters']);
		}
		else
		{
			$filters_array = array(0,1);	
		}
		
		//echo $config['settings']['commission_filters']; exit;
		
		if(count($filters_array) == 0)
			$filters_array = array(0,1);
		
		//print_r($filters_array); exit;
		
		# MAKE SEARCH, LIST BY OR GROUP CHANGE GO BACK TO THE MAIN PAGE
		if($_REQUEST['search'] or $_GET['mgroup'] or $_GET['listby'])
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# IF THIS IS AN ENTRY PAGE RESET THE SESSION DISPLAY TYPE
		if($_GET['ep'] == '1')
		{
			$_SESSION['commissions_dtype'] = 'default';
		}
		
		# IF THE DISPLAY TYPE IS RESET THROUGH GET UPDATE THE SESSION
		if($_REQUEST['dtype'])
		{
			$_SESSION['commissions_dtype'] = $_REQUEST['dtype'];
			# RESET THE CURRENT PAGE
			if(isset($_SESSION['currentpage']))
			{
				$_SESSION['currentpage'] = 1;
			}
							
		}
		
		# SET THE DEFAULT SESSION DISPLAY TYPE - MIGHT NOT BE NEEDED
		if(!$_SESSION['commissions_dtype'])
		{
			$_SESSION['commissions_dtype'] = 'default';
		}
		
		# DECIDE WHICH TYPE OF RECORDS TO PULL
		switch($_SESSION['commissions_dtype'])
		{
			default:
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(com_id) FROM {$dbinfo[pre]}commission WHERE order_status = '1' AND compay_status IN (".implode(",",$filters_array).")"));				
				//echo $r_rows; exit; // Testing
			break;
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
		
		# CHOOSE WHICH FIELDS TO SELECT
		//$select_fields = 'member_id,f_name,l_name,email,comp_name,website,signup_date,last_login,status,membership,credits,notes,avatar,avatar_status';
		$select_fields = '*';
		
		switch($_SESSION['commissions_dtype'])
		{
			default:
				//$commissions_result = mysqli_query($db,"SELECT * FROM ({$dbinfo[pre]}commission LEFT JOIN {$dbinfo[pre]}invoice_items ON {$dbinfo[pre]}commission.oitem_id = {$dbinfo[pre]}invoice_items.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.com_id = {$dbinfo[pre]}invoices.com_id WHERE {$dbinfo[pre]}invoices.payment_status IN (".implode(",",$filters_array).") AND {$dbinfo[pre]}billings.deleted='0' ORDER BY $listby $listtype");
				$commissions_result = mysqli_query($db,
				"
					SELECT * FROM {$dbinfo[pre]}commission 
					LEFT JOIN {$dbinfo[pre]}invoice_items 
					ON {$dbinfo[pre]}commission.oitem_id = {$dbinfo[pre]}invoice_items.oi_id  
					WHERE {$dbinfo[pre]}commission.order_status = '1' 
					AND {$dbinfo[pre]}commission.compay_status IN (".implode(",",$filters_array).")   
					ORDER BY $listby $listtype
				");
				//$billings_rows = mysqli_num_rows($commissions_result);
				//echo "tests: ".$billings_rows; exit;
			break;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_contr_sales']; ?></title>
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
						window.location.href='mgr.commissions.edit.php?edit=new';
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
						$('filters_selector').hide();
						$('billings_adv_search').hide();
						$('billings_details_sel').hide();
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
			// SEARCH BUTTON
			if($('abutton_search')!=null)
				{
				$('abutton_search').observe('click', function()
					{
						$('billings_adv_search').toggle();
						$('search_field').focus();
						$('group_selector').hide();
						$('billings_details_sel').hide();
						$('filters_selector').hide();
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
						$('billings_details_sel').toggle();
						$('group_selector').hide();
						$('billings_adv_search').hide();
						$('filters_selector').hide();
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
			
			// MEMBERSHIP BUTTON
			if($('abutton_filters')!=null)
			{
				$('abutton_filters').observe('click', function()
					{
						$('filters_selector').toggle();
						$('billings_adv_search').hide();
						$('billings_details_sel').hide();
						$('group_selector').hide();
					});
				$('abutton_filters').observe('mouseover', function()
					{
						$('img_filters').src='./images/mgr.button.filter.png';
					});
				$('abutton_filters').observe('mouseout', function()
					{
						$('img_filters').src='./images/mgr.button.filter.off.png';
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
			
			// HEADER UPDATE BUTTON
			if($('button_filters_update')!=null)
			{
				$('button_filters_update').observe('click', function()
					{
						$('filterlist').submit();
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
			
		});
		
		// LOAD GROUPS AREA
		function load_group_selector()
		{
			show_loader('group_selector');
			var loadpage = "mgr.groups.actions.php?mode=grouplist&mgrarea=<?php echo $page; ?>&ingroups=<?php if($_SESSION['billings_dtype'] == 'groups'){ echo 1; } else { echo 0; } ?>&exitpage=<?php echo $_SERVER['PHP_SELF']; ?>&sessname=billingsgroups";
			var updatecontent = 'group_selector';
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
		}
		
		// SWITCH PAYMENT STATUS
		function switch_payment_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('comstatus_sp_'+item_id).hide();
				hide_sp();
				$('paymentstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'paymentstatuscheck' + item_id;
				var loadpage = "mgr.billings.actions.php?mode=payment_status&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
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
				case "comstatus":
					div_id = "comstatus_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' style='cursor: pointer' onclick=\"switch_status_compay('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_unpaid']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' style='cursor: pointer' onclick=\"switch_status_compay('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_paid']; ?></div>"; }
				break;
			}
			$(div_id).update(content);
		}
		
		// Open commission window
		function openCompayWorkbox(amount,comID,memID)
		{
			workbox2({'page' : 'mgr.workbox.php?box=compay&memID='+memID+'&comID='+comID+'&amount='+amount});
		}
		
		// SWITCH STATUS ON COMMISSIONS
		function switch_status_compay(item_id,newstatus){
			//alert(newstatus);
			
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				//$('compayment_sp_'+item_id).hide();
				hide_sp();				
				var updatecontent = 'paymentstatuscheck' + item_id;				
				var loadpage = "mgr.commissions.actions.php?mode=updateCompayStatus&id=" + item_id + "&newstatus=" + newstatus + "&pass=<?php echo md5($config['settings']['serial_number']); ?>";
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});				
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
        ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.quote.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_contr_sales']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <!--<div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>-->
					<?php if(!empty($r_rows)){ ?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                    <?php } ?>
                    <!--<div style="float: left;" class="abuttons" id="abutton_search"><img src="./images/mgr.button.search.off.png" align="absmiddle" border="0" id="img_search" /><br /><?php echo $mgrlang['gen_b_search']; ?></div>-->
                    <div style="float: left;" class="abuttons" id="abutton_filters"><img src="./images/mgr.button.filter.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_filters']; ?>" id="img_filters" /><br /><?php echo $mgrlang['gen_b_filters']; ?></div>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
				<!--
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;">
                    <?php
						if($r_rows)
						{
					?>
                    <select align="absmiddle" id="actionsdd" onchange="do_actions();">
                       	<option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                    </select>
                   	<?php
						}
					?>
                </div>	
                </form>
				-->
            </div>
            
            <!-- BILLINGS FILTERS WINDOW AREA -->
            <form name="filterlist" id="filterlist" action="mgr.commissions.php?action=upfilters" method="post">
			<input type="hidden" name="dtype" value="filter" />
            <div style="display: none;" class="options_area" id="filters_selector">
				<div class="opbox_buttonbar">
					<p><?php echo $mgrlang['commission_display']; ?>:</p>
					<a href="#" class='actionlink' id="button_filters_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>                
                <div class="options_area_box">               
                    <div class='opbox_list'><input type="checkbox" name="commission_filters[]" id="commission_filter_1" value="1" <?php if(in_array("1",$filters_array)){ echo "checked='checked'"; } ?> /><label for="commission_filter_1"> <?php echo $mgrlang['gen_paid']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="commission_filters[]" id="commission_filter_2" value="0" <?php if(in_array("0",$filters_array)){ echo "checked='checked'"; } ?> /><label for="commission_filter_2"> <?php echo $mgrlang['gen_unpaid']; ?></label></strong></div>
				</div>                                      
			</div>
			</form>
                                
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
							<?php $header_name = "com_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                            <?php $header_name = "contr_id";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_member']; ?></a></div></div></td>
							<!--<?php $header_name = "media_id";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_media']; ?></a></div></div></td>-->
                            <!--<?php $header_name = "oitem_id";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_order_num_caps']; ?></a></div></div></td>-->
                            <?php $header_name = "com_total";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_commission']; ?></a></div></div></td>
							<?php $header_name = "order_date";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_date']; ?></a></div></div></td>
							<?php $header_name = "compay_status";if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_status']; ?></a></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # CREATE A DATE OBJECT
							$mcdate = new kdate;	
							
							$zindex = 1000;
							
							# SELECT LOOP THRU ITEMS									
                            while($commission = mysqli_fetch_assoc($commissions_result))
							{
								
								//FP
								$queryProduct = tep_db_query("SELECT b.item_id as product_id
															FROM {$dbinfo['pre']}commission as a
															LEFT JOIN
															{$dbinfo['pre']}invoice_items as b ON a.oitem_id = b.oi_id
															where invoice_id = ".$commission['invoice_id']);
								$aQueryProduct = tep_db_fetch_array($queryProduct);
								$bPP = is_photo_puzzle($aQueryProduct['product_id']);
																				   
								$memberResult = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '{$commission[contr_id]}'");
           						$memberRows = mysqli_num_rows($memberResult);
								$member = mysqli_fetch_assoc($memberResult);
								
								switch($commission['comtype']) // Type of purchase or download
								{
									default:
									case "cur": // Currency based payment
										$total = ($commission['com_total']*$commission['item_qty']);
										
										if($commission['item_percent'] == 0) // Change a 0 to a 100%
											$commission['item_percent'] = 100;
										//FP
										if(!$bPP) $itemCommission = round(($total*($commission['item_percent']/100)*($commission['mem_percent']/100)),2);
										else $itemCommission = round(($total*($commission['mem_percent']/100)),2);
																		
									break;
									case "cred": // Credit based commission
										$itemCommission = round(($commission['com_credits']*$commission['item_qty'])*$commission['per_credit_value'],2);
									break;
									case "sub": // Subscription download commission
										$itemCommission = $commission['com_total'];
									break;
								}
								
								$commissionTotal = $itemCommission;
								
								if($contrSales['compay_status'] == 1)
									$runningPaidTotal+= $itemCommission;
								else
									$runningUnpaidTotal+= $itemCommission;	
								
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
                                <td align="center" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'"><a name="row_<?php echo $commission['com_id']; ?>"></a><?php echo $commission['com_id']; ?></td>
                                <td align="left" nowrap="nowrap" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'">
									<div style="float: left;">
                                    <?php
                                        if(file_exists("../assets/avatars/" . $commission['contr_id'] . "_small.png"))
                                        {
                                            echo "<img src='../assets/avatars/" . $commission['contr_id'] . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                                        }
                                        else
                                        {
                                            echo "<img src='images/mgr.no.avatar.gif' width='19' style='vertical-align: middle; margin-right: 8px;' class='mediaFrame' />";
                                        }
                                    ?>
                                    <a href="<?php if(in_array("members",$_SESSION['admin_user']['permissions'])){ echo "mgr.members.edit.php?edit={$commission[contr_id]}"; } else { echo "#"; } ?>" class="editlink" style="margin-right: 10px;" onmouseover="start_mem_panel(<?php echo $commission['com_id']; ?>,<?php echo $commission['contr_id']; ?>);" onmouseout="cancel_mem_panel(<?php echo $commission['com_id']; ?>,<?php echo $commission['contr_id']; ?>);"><?php echo $member['f_name'] ." ". $member['l_name']; ?></a>
                                    </div>
                                    <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                        <div id="more_info_<?php echo $commission['contr_id']; ?>-<?php echo $commission['com_id']; ?>" style="display: none;" class="mem_details_win">
                                            <div class="mem_details_win_inner">
                                                <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                <div id="more_info_<?php echo $commission['contr_id']; ?>-<?php echo $commission['com_id']; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!--<td align="center" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'">media</td>
								<td align="center" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'">order num</td>-->
								<td align="center" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'"><strong><?php echo $cleanvalues->currency_display($commissionTotal,1); ?></strong></td>
								<td nowrap="nowrap" onclick="window.location.href='mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>'"><?php echo $mcdate->showdate($commission['order_date']); ?></td>
                                <td align="center">
                                    <div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='comstatus_sp_<?php echo $commission['com_id']; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                    <div id="paymentstatuscheck<?php echo $commission['com_id']; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
										<?php
                                            switch($commission['compay_status'])
                                            {
                                                case 0: // UNPAID
                                                    $tag_label = $mgrlang['gen_unpaid'];
                                                    $mtag = 'mtag_processing';
                                                break;
                                                case 1: // PAID
                                                    $tag_label = $mgrlang['gen_paid'];
                                                    $mtag = 'mtag_paid';
                                                break;
                                            }
                                        ?>
                                   	  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_sp('comstatus_sp_<?php echo $commission['com_id']; ?>');write_status('comstatus','<?php echo $commission['com_id']; ?>',<?php echo $commission['compay_status']; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.commissions.edit.php?edit=<?php echo $commission['com_id']; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                    <a href="javascript:deleterec(<?php echo $commission['com_id']; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
									<a href="#" class='actionlink' onclick="openCompayWorkbox('<?php echo $itemCommission; ?>','<?php echo $commission['com_id']; ?>','<?php echo $commission['contr_id']; ?>');"><img src="images/mgr.icon.pay.png" align="absmiddle" alt="<?php echo $mgrlang['gen_pay']; ?>" border="0" /><?php echo $mgrlang['gen_pay']; ?></a>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <input type="checkbox" name="items[]" value="<?php echo $commission['com_id']; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
                                </td>
                            </tr>
                        <?php
								$zindex-=2;
                            }
                        ?>                        
                        
                    </table>
                    </form>					
                </div>
				<!--<div class="contrSalesFooter" style="padding-right: 20px;">Paid[t]: <span class="paid"><?php echo $cleanvalues->currency_display($runningPaidTotal,1); ?></span> Unpaid[t]: <span class="unpaid"><?php echo $cleanvalues->currency_display($runningUnpaidTotal,1); ?></span></div>-->
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