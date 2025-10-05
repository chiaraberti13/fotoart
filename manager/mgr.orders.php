<?php
	###################################################################
	####	ORDERS			                             		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 6-12-2008                                     ####
	####	Modified: 5-5-2012                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "orders";
		$lnav = "sales";
		
		$supportPageID = '352';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		} else { 											
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
					
					//echo $delete_array; exit;
					
					# GET TITLES FOR LOG
					$log_result = mysqli_query($db,"SELECT order_number,order_id FROM {$dbinfo[pre]}orders WHERE order_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result)){
						$log_titles.= "$log->order_number ($log->order_id), ";
						
						# MARK ORDER AS DELETED
						@$sql = "UPDATE {$dbinfo[pre]}orders SET deleted='1' WHERE order_id = '$log->order_id'";
						@$result = mysqli_query($db,$sql);
						
						# MARK INVOICE AS DELETED
						@$sql = "UPDATE {$dbinfo[pre]}invoices SET deleted='1' WHERE order_id = '$log->order_id'";
						@$result = mysqli_query($db,$sql);
						
						// Mark order items as deleted????????????????
						
					}
					
					if(substr($log_titles,strlen($log_titles)-2,2) == ", "){
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_order'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
					
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_delsuc'];
				} else {
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
			# UPDATE HEADERS
			case "upheaders":	
				/*
				foreach($_POST['order_headers'] as $value)
				{
					@$headers_full.= "$value,";
				}
				*/
				# CONVERT TO STRING
				$headers_full = implode(",",$_POST['order_headers']);
				# UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET odr_headers='$headers_full' WHERE settings_id = '1'");
				# REFRESH SETTINGS
				include('mgr.select.settings.php');
			break;
			# UPDATE FILTERS
			case "upfilters":
				/*				
				foreach($_POST['order_filters'] as $value)
				{
					@$filters_full.= "$value,";
				}
				*/
				# CONVERT TO STRING
				if($_POST['order_filters'])
				{
					$filters_full = implode(",",$_POST['order_filters']);
				}
				# UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET odr_filters='$filters_full' WHERE settings_id = '1'");
				# REFRESH SETTINGS
				include('mgr.select.settings.php');
			break;
		}
		
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO"){
			$delete_link = "DEMO_";
		} else {
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		if($_GET['mes'] == "new"){
			$vmessage = $mgrlang['gen_mes_newsave'];
		}
		if($_GET['mes'] == "edit"){
			$vmessage = $mgrlang['gen_mes_changesave'];
		}
		
		
		# INCLUDE DATASORTS CLASS
		require_once("mgr.class.datasort.php");			
		$sortprefix="orders";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "order_id";			
		require_once('mgr.datasort.logic.php');	
		
		# IF THIS IS AN ENTRY PAGE OR ordergroups IS BLANK RESET THE ordergroups SESSION	
		if($_GET['ep'] or empty($_SESSION['ordergroups']))
		{
			$_SESSION['ordergroups'] = array('all');
		}			
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_REQUEST['setgroups'])
		{
			if(is_array($_REQUEST['setgroups']))
			{
				$_SESSION['ordergroups'] = $_REQUEST['setgroups'];
			}
			else
			{				
				$_SESSION['ordergroups'] = array($_REQUEST['setgroups']);
			}
		}
		
		/*
		# IF THIS IS AN ENTRY PAGE OR memgroups IS BLANK RESET THE memgroups SESSION	
		if($_GET['ep'] or empty($_SESSION['mem_ms']) or $_REQUEST['dtype'] != 'filters')
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
		*/
		
		# UPDATE FILTERS
		if($_GET['dtype'] == "filters")
		{
			
		}
		
		# SEE IF ANY SEARCH HAS BEEN PASSED
		if(!empty($_REQUEST['search']))
		{
			$_SESSION['orders_search'] = $_REQUEST['search'];
		}
		
		# CHECK TO SEE IF THE SEARCH FIELDS ARE GETTING SET
		if($_REQUEST['setsearchfields'])
		{
			$_SESSION['sfo_array'] = $_REQUEST['setsearchfields'];
		}
		else
		{
			// set it to all?
		}
		
		# IF THIS IS AN ENTRY PAGE RESET SESSION	
		if($_GET['ep'] or empty($_SESSION['sfo_array']) or $_REQUEST['dtype'] != 'search')
		{
			$_SESSION['sfo_array'] = array('all');
		}
		
		# SET TO LOCAL VALUE
		$sfo_array = $_SESSION['sfo_array'];
		
		# FOR EASE MAKE THE VARIABLE LOCAL
		$insearch = $_SESSION['orders_search'];
		
		# PUT THE HEADER VALUES INTO AN ARRAY
		if($config['settings']['odr_headers'])
		{
			$headers_array = explode(",",$config['settings']['odr_headers']);
		}
		else
		{
			$headers_array = array("order_id","order_number","l_name","invoice_number","order_date","payment_status","order_status","total");   
		}
		
		# PUT THE FILTER VALUES INTO AN ARRAY
		if($config['settings']['odr_filters'])
		{
			
			$filters_array = explode(",",$config['settings']['odr_filters']);
		}
		else
		{
			$filters_array = array(0,1,3,4);	
		}
		
		//echo print_r($filters_array); exit;
					
		# MAKE SEARCH, LIST BY OR GROUP CHANGE GO BACK TO THE MAIN PAGE
		if($_REQUEST['search'] or $_GET['mgroup'] or $_GET['listby'])
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# IF THIS IS AN ENTRY PAGE RESET THE SESSION DISPLAY TYPE
		if($_GET['ep'] == '1')
		{
			$_SESSION['order_dtype'] = 'default';
		}
		
		# IF THE DISPLAY TYPE IS RESET THROUGH GET UPDATE THE SESSION
		if($_REQUEST['dtype'])
		{
			$_SESSION['order_dtype'] = $_REQUEST['dtype'];
			# RESET THE CURRENT PAGE
			if(isset($_SESSION['currentpage']))
			{
				$_SESSION['currentpage'] = 1;
			}
							
		}
		
		# SET THE DEFAULT SESSION DISPLAY TYPE - MIGHT NOT BE NEEDED
		if(!$_SESSION['order_dtype'])
		{
			$_SESSION['order_dtype'] = 'default';
		}
		
		# GET THE TOTAL NUMBER OF ROWS
		# DECIDE WHICH TYPE OF RECORDS TO PULL
		switch($_SESSION['order_dtype'])
		{
			default:
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(order_id) FROM {$dbinfo[pre]}orders WHERE deleted = 0 AND order_status IN (".implode(",",$filters_array).")"));
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
						$sql_search.= " {$dbinfo[pre]}members.f_name LIKE '%$value%'";
						if(in_array("sfo_lastname",$sfo_array) or in_array('all',$sfo_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.l_name LIKE '%$value%'"; 	}
						if(in_array("sfo_id",$sfo_array) or in_array('all',$sfo_array)){			$sql_search.= " or {$dbinfo[pre]}members.mem_id LIKE '%$value%'"; 	}
						if(in_array("sfo_uid",$sfo_array) or in_array('all',$sfo_array)){ 			$sql_search.= " or {$dbinfo[pre]}members.umem_id LIKE '%$value%'"; 	}
						if(in_array("sfo_email",$sfo_array) or in_array('all',$sfo_array)){			$sql_search.= " or {$dbinfo[pre]}members.email LIKE '%$value%'"; 	}
						if(in_array("sfo_order_number",$sfo_array) or in_array('all',$sfo_array)){	$sql_search.= " or {$dbinfo[pre]}orders.order_number LIKE '%$value%'";	}
						if(in_array("sfo_invoice_number",$sfo_array) or in_array('all',$sfo_array)){$sql_search.= " or {$dbinfo[pre]}invoices.invoice_number LIKE '%$value%'";	}
						
						$sql_search.= " or {$dbinfo[pre]}orders.order_id LIKE '%$value%'"; // Added in 4.7.5
						$sql_search.= " or {$dbinfo[pre]}orders.uorder_id LIKE '%$value%'";
						
						//if(in_array("sfo_invoice_id",$sfo_array) or in_array('all',$sfo_array)){	$sql_search.= " or {$dbinfo[pre]}orders.invoice_id LIKE '%$value%'";	}
						//if(in_array("sf_notes",$sfo_array) or in_array('all',$sfo_array)){		$sql_search.= " or notes LIKE '%$value%'";		}
						//if(in_array("sf_comp_name",$sfo_array) or in_array('all',$sfo_array)){	$sql_search.= " or comp_name LIKE '%$value%'";	}
						//if(in_array("sf_address",$sfo_array) or in_array('all',$sfo_array)){	$sql_search.= " or address LIKE '%$value%' or address_2 LIKE '%$value%' or city LIKE '%$value%'"; }
						$snext++;
					}
				}
				//echo $sql_search; exit;
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(uorder_id) FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND $sql_search"));
			break;
			
			case "groups":
				$order_result2 = "SELECT COUNT(order_id) FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['ordergroups']).")";
				$r_rows = mysqli_result_patch(mysqli_query($db,$order_result2));
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
		
		/*
		switch($listby)
		{
			default:
				$listByDB = 'orders';
			break;
			case "invoice_number":
				$listByDB = 'orders';
			break;
		}
		*/
		
		if($listby == 'order_id')
			$listbyDB = "{$dbinfo[pre]}orders.order_id";
		else
			$listbyDB = $listby;
		
		switch($_SESSION['order_dtype'])
		{
			default: // all
				//echo  $listby.'-'.$listtype; exit;
				
				//echo "SELECT *,{$dbinfo[pre]}orders.order_id AS order_id FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND {$dbinfo[pre]}orders.order_status IN (".implode(",",$filters_array).") ORDER BY {$dbinfo[pre]}orders.{$listby} {$listtype}"; exit;
				$order_result = mysqli_query($db,"SELECT *,{$dbinfo[pre]}orders.order_id AS order_id FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND {$dbinfo[pre]}orders.order_status IN (".implode(",",$filters_array).") ORDER BY {$listbyDB} {$listtype} LIMIT $startrecord,$perpage"); // had to add ' ' because using order_id errored for some reason //{$dbinfo[pre]}orders.
			break;
			
			case "search":
				$order_result = mysqli_query($db,"SELECT *,{$dbinfo[pre]}orders.order_id AS order_id FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND $sql_search ORDER BY $listbyDB $listtype LIMIT $startrecord,$perpage");
			break;
			
			case "groups":
				$order_result = mysqli_query($db,"SELECT *,{$dbinfo[pre]}orders.order_id AS order_id FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}groupids.item_id) LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.deleted = 0 AND {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['ordergroups']).") GROUP BY {$dbinfo[pre]}orders.order_id ORDER BY $listbyDB $listtype LIMIT $startrecord,$perpage");
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_orders']; ?></title>
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
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		
		function submit_groups()
		{
			$('grouplist').submit();
		}
		
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.orders.edit.php?edit=new';
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
						$('filters_selector').hide();
						$('orders_adv_search').hide();
						$('orders_details_sel').hide();
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
			
			// SEARCH BUTTON
			if($('abutton_search')!=null)
				{
				$('abutton_search').observe('click', function()
					{
						$('orders_adv_search').toggle();
						$('search_field').focus();
						$('group_selector').hide();
						$('orders_details_sel').hide();
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
						$('orders_details_sel').toggle();
						$('group_selector').hide();
						$('orders_adv_search').hide();
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
						$('orders_adv_search').hide();
						$('orders_details_sel').hide();
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
			<?php
				// LOAD THE GROUPLIST AREA
				if($_SESSION['order_dtype'] == 'groups' or $_GET['dtype'] == 'groups')
				{
					echo "load_group_selector();";
				}
			?>
		});
		
		// LOAD GROUPS AREA
		function load_group_selector()
		{
			show_loader('group_selector');
			var loadpage = "mgr.groups.actions.php?mode=grouplist&mgrarea=<?php echo $page; ?>&ingroups=<?php if($_SESSION['order_dtype'] == 'groups'){ echo 1; } else { echo 0; } ?>&exitpage=<?php echo $_SERVER['PHP_SELF']; ?>&sessname=ordergroups";
			var updatecontent = 'group_selector';
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
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
		
		// SWITCH ORDER STATUS
		function switch_order_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('order_sp_'+item_id).hide();
				hide_sp();
				$('orderstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'orderstatuscheck' + item_id;
				var loadpage = "mgr.orders.actions.php?mode=order_status&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// SWITCH PAYMENT STATUS
		function switch_payment_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				//$('payment_sp_'+item_id).fade({ duration: 0.7 });
				//$('payment_sp_'+item_id).hide();
				$('payment_sp_'+item_id).hide();
				hide_sp();
				$('paymentstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'paymentstatuscheck' + item_id;
				var loadpage = "mgr.orders.actions.php?mode=payment_status&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// SWITCH SHIPPING STATUS
		function switch_ship_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('ship_sp_'+item_id).hide();
				hide_sp();
				$('shippingstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'shippingstatuscheck' + item_id;
				var loadpage = "mgr.orders.actions.php?mode=shipping_status&id=" + item_id + "&newstatus=" + newstatus;
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
				case "payment":
					div_id = "payment_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_processing mtag' onclick=\"switch_payment_status('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_processing']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_paid mtag' onclick=\"switch_payment_status('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_paid']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_unpaid mtag' onclick=\"switch_payment_status('"+id+"',2);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_unpaid']; ?></div>"; }
					<?php if(in_array("pro",$installed_addons)){ ?>if(curstatus != 3){ content+= "<div class='mtag_bill mtag' onclick=\"switch_payment_status('"+id+"',3);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_bill']; ?></div>"; }<?php } ?>
					if(curstatus != 4){ content+= "<div class='mtag_failed mtag' onclick=\"switch_payment_status('"+id+"',4);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_failed']; ?></div>"; }
					if(curstatus != 5){ content+= "<div class='mtag_refunded mtag' onclick=\"switch_payment_status('"+id+"',5);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_refunded']; ?></div>"; }
				break;
				case "orders":
					div_id = "order_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' onclick=\"switch_order_status('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' onclick=\"switch_order_status('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_approved']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_incomplete mtag' onclick=\"switch_order_status('"+id+"',2);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_incomplete']; ?></div>"; }
					if(curstatus != 3){ content+= "<div class='mtag_cancelled mtag' onclick=\"switch_order_status('"+id+"',3);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_cancelled']; ?></div>"; }
					if(curstatus != 4){ content+= "<div class='mtag_failed mtag' onclick=\"switch_order_status('"+id+"',4);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_failed']; ?></div>"; }
				break;
				case "shipping":
					div_id = "ship_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_shippingna mtag' style='width: 100px;' onclick=\"switch_ship_status('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_shipnone']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_shipped mtag' style='width: 100px;' onclick=\"switch_ship_status('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_shipped']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_notshipped mtag' style='width: 100px;' onclick=\"switch_ship_status('"+id+"',2);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_notshipped']; ?></div>"; }
					if(curstatus != 3){ content+= "<div class='mtag_partshipped mtag' style='width: 100px;' onclick=\"switch_ship_status('"+id+"',3);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pshipped']; ?></div>"; }
					if(curstatus != 4){ content+= "<div class='mtag_backordered mtag' style='width: 100px;' onclick=\"switch_ship_status('"+id+"',4);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_backordered']; ?></div>"; }
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
                    <img src="./images/mgr.badge.orders.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_orders']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <!--<div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>-->
                    <?php if(!empty($r_rows)){ ?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                    	<div style="float: left;" class="abuttons" id="abutton_headers"><img src="./images/mgr.button.details.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_headers']; ?>" id="img_headers" /><br /><?php echo $mgrlang['gen_b_headers']; ?></div>
					<?php } ?>
                    <div style="float: left;" class="abuttons" id="abutton_search"><img src="./images/mgr.button.search.off.png" align="absmiddle" border="0" id="img_search" /><br /><?php echo $mgrlang['gen_b_search']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_filters"><img src="./images/mgr.button.filter.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_filters']; ?>" id="img_filters" /><br /><?php echo $mgrlang['gen_b_filters']; ?></div>
                    <?php if(in_array("pro",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_group"><img src="./images/mgr.button.group.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_group" /><br /><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <!--
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;"> 
                    <?php
						if($r_rows){
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
            
            <?php
				//$order_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				//$order_group_rows = mysqli_num_rows($order_group_result);
			?>
            
            <!-- GROUPS WINDOW -->
			<form name="grouplist" id="grouplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            	<input type="hidden" name="dtype" value="groups" />
				<div style="<?php if($_SESSION['order_dtype'] == 'groups'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="group_selector"></div>
			</form>
            
            <!-- ORDER FILTERS WINDOW AREA -->
            <form name="filterlist" id="filterlist" action="mgr.orders.php?action=upfilters" method="post">
			<input type="hidden" name="dtype" value="filter" />
            <div style="display: none;" class="options_area" id="filters_selector">
				<div class="opbox_buttonbar">
					<p><?php echo $mgrlang['order_dis_statuses']; ?>:</p>
					<a href="#" class='actionlink' id="button_filters_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>                
                <div class="options_area_box">                  
                    <div class='opbox_list'><input type="checkbox" name="order_filters[]" id="order_filter_2" value="2" <?php if(in_array("2",$filters_array)){ echo "checked='checked'"; } ?> /><label for="order_filter_2"> <?php echo $mgrlang['gen_incomplete']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_filters[]" id="order_filter_1" value="1" <?php if(in_array("1",$filters_array)){ echo "checked='checked'"; } ?> /><label for="order_filter_1"> <?php echo $mgrlang['gen_approved']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_filters[]" id="order_filter_0" value="0" <?php if(in_array("0",$filters_array)){ echo "checked='checked'"; } ?> /><label for="order_filter_0"> <?php echo $mgrlang['gen_pending']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_filters[]" id="order_filter_3" value="3" <?php if(in_array("3",$filters_array)){ echo "checked='checked'"; } ?> /><label for="order_filter_3"> <?php echo $mgrlang['gen_cancelled']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_filters[]" id="order_filter_4" value="4" <?php if(in_array("4",$filters_array)){ echo "checked='checked'"; } ?> /><label for="order_filter_4"> <?php echo $mgrlang['gen_failed']; ?></label></strong></div>
				</div>                                      
			</div>
			</form>
            
            <!-- SELECT DETAILS TO SHOW ON LIST VIEW -->
            <form action="mgr.orders.php?action=upheaders" id="headers_form" method="post">
            <input type="hidden" name="order_headers[]" value="l_name" />
            <input type="hidden" name="dtype" value="<?php echo $_SESSION['order_dtype']; ?>" />
            <div style="display: none;" class="options_area" id="orders_details_sel">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['order_dis_data']; ?>:</p>
                    <a href="#" class='actionlink' id="button_headers_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>
                <div class="options_area_box">                    
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="order_id" value="order_id" <?php if(in_array("order_id",$headers_array)){ echo "checked='checked'"; } ?> /><label for="order_id"> <?php echo $mgrlang['gen_t_id']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="l_name" value="l_name" checked="checked" disabled="disabled" /><label for="l_name"> <?php echo $mgrlang['customer']; ?></label></strong></div>
                    <!--<div class='opbox_list'><input type="checkbox" name="order_headers[]" id="email" value="email" <?php if(in_array("email",$headers_array)){ echo "checked='checked'"; } ?> /><label for="email"> Email</label></strong></div>-->
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="order_number" value="order_number" <?php if(in_array("order_number",$headers_array)){ echo "checked='checked'"; } ?> /><label for="order_number"> <?php echo $mgrlang['order_f_ordernum']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="invoice_number" value="invoice_number" <?php if(in_array("invoice_number",$headers_array)){ echo "checked='checked'"; } ?> /><label for="invoice_number"> <?php echo $mgrlang['gen_invoice_number']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="order_status" value="order_status" <?php if(in_array("order_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="order_status"> <?php echo $mgrlang['order_f_status']; ?><?php if($_SESSION['pending_orders'] > 0){ echo "<span class='pending_number' id='header_orderstatus'>".$_SESSION['pending_orders']."</span>"; } ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="order_date" value="order_date" <?php if(in_array("order_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="order_date"> <?php echo $mgrlang['gen_order_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="payment_status" value="payment_status" <?php if(in_array("payment_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="payment_status"> <?php echo $mgrlang['gen_payment_status']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="payment_date" value="payment_date" <?php if(in_array("payment_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="payment_date"> <?php echo $mgrlang['gen_payment_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="total" value="total" <?php if(in_array("total",$headers_array)){ echo "checked='checked'"; } ?> /><label for="total"> <?php echo $mgrlang['gen_total']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="shipping_status" value="shipping_status" <?php if(in_array("shipping_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="shipping_status"> <?php echo $mgrlang['gen_shipping_status']; ?></label></strong></div>
                    <!--
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="notes" value="notes" <?php if(in_array("notes",$headers_array)){ echo "checked='checked'"; } ?> /><label for="notes"> Notes</label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="address" value="address" <?php if(in_array("address",$headers_array)){ echo "checked='checked'"; } ?> /><label for="address"> Address</label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="membership" value="membership" <?php if(in_array("membership",$headers_array)){ echo "checked='checked'"; } ?> /><label for="membership"> Membership</label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="memgroup" value="memgroup" <?php if(in_array("memgroup",$headers_array)){ echo "checked='checked'"; } ?> /><label for="memgroup"> Groups</label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="avatar_status" value="avatar_status" <?php if(in_array("avatar_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="avatar_status"> Avatar<?php if($_SESSION['pending_member_avatars'] > 0){ echo "<span class='pending_number' id='header_avatar'>".$_SESSION['pending_member_avatars']."</span>"; } ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="order_headers[]" id="bio_status" value="bio_status" <?php if(in_array("bio_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="bio_status"> Bio<?php if($_SESSION['pending_member_bios'] > 0){ echo "<span class='pending_number' id='header_bio'>".$_SESSION['pending_member_bios']."</span>"; } ?></label></strong></div>
                    -->
                </div>
            </div>
            </form>
            <!-- ADVANCED SEARCH AREA -->
            <form action="mgr.orders.php" method="post" id="search_from">
            <input type="hidden" name="dtype" value="search" /> 
            <div style="<?php if($_SESSION['order_dtype'] == 'search'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="orders_adv_search">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['gen_search']; ?>:</p>
                    <a href="#" class='actionlink' id="button_search"><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_search']; ?></a><?php if($_SESSION['order_dtype'] == 'search'){ ?><a href="mgr.orders.php?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_exit_search']; ?></a><?php } ?>
                </div>
                <div class="options_area_box">
					<?php
						if($search_words)
						{
							echo "{$mgrlang[gen_search_results]}: ";
							$x = count($search_words);
							$y = 1;
							foreach($search_words as $value)
							{
								echo "<strong>$value</strong>";
								if($y < $x)
								{
									echo ", ";
								}
								$y++;
							}
							echo "<br />";
						}
                    ?>
                    <p style="clear: both; margin: 6px 0 6px 0;"><input type="text" name="search" id='search_field' value="<?php if($_SESSION['order_dtype'] == 'search'){ echo $_SESSION['orders_search']; } ?>" style="width: 250px;" /></p>
                    <div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfo_id" value="sfo_id" <?php if(in_array("sfo_id",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_id"> <?php echo $mgrlang['gen_t_id']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_firstname" value="sfo_firstname" <?php if(in_array("sfo_firstname",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_firstname"> <?php echo $mgrlang['mem_f_fname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_lastname" value="sfo_lastname" <?php if(in_array("sfo_lastname",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_lastname"> <?php echo $mgrlang['mem_f_lname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_email" value="sfo_email" <?php if(in_array("sfo_email",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_email"> <?php echo $mgrlang['mem_f_email']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_order_number" value="sfo_order_number" <?php if(in_array("sfo_order_number",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_order_number"> <?php echo $mgrlang['order_f_ordernum'] ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfo_invoice_number" value="sfo_invoice_number" <?php if(in_array("sfo_invoice_number",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sfo_invoice_number"> <?php echo $mgrlang['gen_invoice_number']; ?></label></strong></div>
                    <!--<div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_website" value="sf_website" <?php if(in_array("sf_website",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sf_website"> Website</label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_notes" value="sf_notes" <?php if(in_array("sf_notes",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sf_notes"> Notes</label></strong></div>  
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_uid" value="sf_uid" <?php if(in_array("sf_uid",$sfo_array) or in_array('all',$sfo_array)){ echo "checked='checked'"; } ?> /><label for="sf_uid"> Unique ID</label></strong></div>
                    -->
                </div>                                
            </div>
            </form>
            
            <!-- START CONTENT -->
            <?php
                # CHECK TO MAKE SURE THERE ARE RECORDS
                if(!empty($r_rows)){
					if($r_rows > 10 and $perpage > 10)
					{
						include('mgr.perpage.php');	
					}
            ?>
                <div id="content" style="border-top: 0px; background-color: #F00">						
                    <form name="datalist" id="datalist" method="post">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- DATA TABLE HEADER -->
                        <tr>
                            <?php $header_name = "order_id";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "order_number";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_ordernum']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "invoice_number";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"  width="100"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_invoicenum']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "order_date";		if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_order_date_caps']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "member_id";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_customer']; ?></a></div></div></td>
                            <?php $header_name = "payment_date";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"  width="100"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_pd_caps']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "payment_status";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"  width="110"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_payment']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "order_status";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center"  width="110" nowrap="nowrap"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_status']; ?></a><span class='pending_number' id='ph_status' <?php if($_SESSION['pending_orders'] == 0){ echo "style='display: none;'"; } ?> onclick="window.location.href='mgr.orders.php?listby=order_status&listtype=asc'"><?php echo $_SESSION['pending_orders']; ?></span></div></div></td><?php } ?>
                            <?php $header_name = "shipping_status";	if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" nowrap="nowrap" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"  width="130"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_shipping']; ?></a></div></div></td><?php } ?>
							<?php $header_name = "total";			if(in_array($header_name,$headers_array)){ if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"  width="100"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_total']; ?></a></div></div></td><?php } ?>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # CREATE A DATE OBJECT
							$orderdate = new kdate;
							$orderdate->distime = 1;
							
							$zindex = 1000;
							
							# SELECT LOOP THRU ITEMS									
                            while($order = mysqli_fetch_object($order_result)){
							
                                # SET THE ROW COLOR
                                @$row_color++;
                                if ($row_color%2 == 0) {
                                    $row_class = "list_row_on";
                                    $color_fade = "EEEEEE";
                                } else {
                                    $row_class = "list_row_off";
                                    $color_fade = "FFFFFF";
                                }
								
								# SEE IF THIS WAS POSTED BY A MEMBER OR A VISITOR
								if($order->member_id != 0)
								{
									$member_name = "$order->f_name $order->l_name";
									if($order->email) $member_name.= "<!-- ($order->email)-->";
									$mem_id = $order->member_id;
								}
								else
								{
									$member_name = "$mgrlang[gen_visitor]";
									$mem_id = 0;
								}                       
                        ?>
                            <tr><td height="1" colspan="6" bgcolor="ffffff" style="background-color: #FFFFFF;"></td></tr>
                            <tr class="<?php echo $row_class; ?>" onMouseOver="cellover(this,'#<?php echo $color_fade; ?>',32);" onMouseOut="cellout(this,'#<?php echo $color_fade; ?>');">
                                <?php if(in_array("order_id",$headers_array)){ ?><td align="center" onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'"><a name="row_<?php echo $order->order_id; ?>"></a><?php echo $order->order_id; ?></td><?php } ?>
                                <?php if(in_array("order_number",$headers_array)){ ?><td align="center" onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'"><?php if($order->order_number){ echo $order->order_number; } else { echo "--"; } ?></td><?php } ?>
								<?php if(in_array("invoice_number",$headers_array)){ ?><td align="center" onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'" nowrap="nowrap"><?php if($order->invoice_number){ echo $order->invoice_number; } else { echo "--"; } ?></td><?php } ?>
								<?php if(in_array("order_date",$headers_array)){ ?><td align="center" nowrap="nowrap" onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'"><?php echo $orderdate->showdate($order->order_date); ?></td><?php } ?>
                                <td onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'">
                                	<div style="float: left;">
                                    <?php
                                        if(file_exists("../assets/avatars/" . $mem_id . "_small.png"))
                                        {
                                            echo "<img src='../assets/avatars/" . $mem_id . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                                        }
                                        else
                                        {
                                            echo "<img src='images/mgr.no.avatar.gif' width='19' style='border: 2px solid #$border_color; vertical-align: middle; margin-right: 8px;' class='mediaFrame' />";
                                        }
                                        if($order->member_id){
                                    ?>
                                            <a href="<?php if(in_array("members",$_SESSION['admin_user']['permissions'])){ echo "mgr.members.edit.php?edit=$order->mem_id"; } else { echo "#"; } ?>" class="editlink" style="margin-right: 10px;" onmouseover="start_mem_panel(<?php echo $order->order_id; ?>,<?php echo $order->mem_id; ?>);" onmouseout="cancel_mem_panel(<?php echo $order->order_id; ?>,<?php echo $order->mem_id; ?>);"><strong><?php echo $member_name; ?></strong></a>
                                            </div>
                                            <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                                <div id="more_info_<?php echo $order->mem_id; ?>-<?php echo $order->order_id; ?>" style="display: none;" class="mem_details_win">
                                                    <div class="mem_details_win_inner">
                                                        <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                        <div id="more_info_<?php echo $order->mem_id; ?>-<?php echo $order->order_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
										}
										else
										{
                                            echo "<strong>{$order->bill_name}</strong> ($member_name)</div>";
                                        }
                                    ?>
                                </td>
                                <?php if(in_array("payment_date",$headers_array)){ ?><td align="center" onclick="window.location.href='mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>'" nowrap="nowrap"><?php if($order->payment_date != '0000-00-00 00:00:00'){ echo $orderdate->showdate($order->payment_date); } else { echo "--"; } ; ?></td><?php } ?>
								<?php if(in_array("payment_status",$headers_array)){ ?>
                                <td align="center" width="110">
                                	<div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='payment_sp_<?php echo $order->order_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                    <div id="paymentstatuscheck<?php echo $order->order_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
										<?php
                                            switch($order->payment_status)
                                            {
                                                case 0: // PROCESSING
                                                    $tag_label = $mgrlang['gen_processing'];
                                                    $mtag = 'mtag_processing';
                                                break;
                                                case 1: // APPROVED
                                                    $tag_label = $mgrlang['gen_paid'];
                                                    $mtag = 'mtag_paid';
                                                break;
                                                case 2: // INCOMPLETE/NONE
                                                    $tag_label = $mgrlang['gen_unpaid'];
                                                    $mtag = 'mtag_unpaid';
                                                break;
                                                case 3: // BILL/LATER
                                                    $tag_label = $mgrlang['gen_bill'];
                                                    $mtag = 'mtag_bill';
                                                break;
                                                case 4: // FAILED/CANCELLED
                                                    $tag_label = $mgrlang['gen_failed'];
                                                    $mtag = 'mtag_failed';
                                                break;
                                                case 5: // REFUNDED
                                                    $tag_label = $mgrlang['gen_refunded'];
                                                    $mtag = 'mtag_refunded';
                                                break;
                                            }
                                      	?>
                                   	  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_sp('payment_sp_<?php echo $order->order_id; ?>');write_status('payment','<?php echo $order->order_id; ?>',<?php echo $order->payment_status; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <?php } ?>
                                <?php if(in_array("order_status",$headers_array)){ ?>
                                <td align="center">
                                    <div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='order_sp_<?php echo $order->order_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                    <div id="orderstatuscheck<?php echo $order->order_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
										<?php
                                            switch($order->order_status)
                                            {
                                                case 0: // PENDING
                                                    $tag_label = $mgrlang['gen_pending'];
                                                    $mtag = 'mtag_pending';
                                                break;
                                                case 1: // APPROVED
                                                    $tag_label = $mgrlang['gen_approved'];
                                                    $mtag = 'mtag_approved';
                                                break;
                                                case 2: // INCOMPLETE
                                                    $tag_label = $mgrlang['gen_incomplete'];
                                                    $mtag = 'mtag_incomplete';
                                                break;
                                                case 3: // CANCELLED
                                                    $tag_label = $mgrlang['gen_cancelled'];
                                                    $mtag = 'mtag_cancelled';
                                                break;
                                                case 4: // FAILED
                                                    $tag_label = $mgrlang['gen_failed'];
                                                    $mtag = 'mtag_failed';
                                                break;
                                            }
                                        ?>
                                   	  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_sp('order_sp_<?php echo $order->order_id; ?>');write_status('orders','<?php echo $order->order_id; ?>',<?php echo $order->order_status; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <?php } ?>
                                <?php if(in_array("shipping_status",$headers_array)){ ?>
                                <td align="center">
                                    <div style='width: 130px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='ship_sp_<?php echo $order->order_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none; width: 120px; margin-left: 3px;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
                                    <div id="shippingstatuscheck<?php echo $order->order_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 11px; margin-top: -10px">
										<?php
                                            switch($order->shipping_status)
                                            {
                                                case 0: // NA
                                                    $tag_label = $mgrlang['gen_shipnone'];
                                                    $mtag = 'mtag_shippingna';
                                                break;
                                                case 1: // SHIPPED
                                                    $tag_label = $mgrlang['gen_shipped'];
                                                    $mtag = 'mtag_shipped';
                                                break;
                                                case 2: // NOT SHIPPED
                                                    $tag_label = $mgrlang['gen_notshipped'];
                                                    $mtag = 'mtag_notshipped';
                                                break;
                                                case 3: // PARTIALLY SHIPPED
                                                    $tag_label = $mgrlang['gen_pshipped'];
                                                    $mtag = 'mtag_partshipped';
                                                break;
                                                case 4: // BACKORDERED
                                                    $tag_label = $mgrlang['gen_backordered'];
                                                    $mtag = 'mtag_backordered';
                                                break;
                                            }
                                        ?>
                                   	  <div class='<?php echo $mtag; ?> mtag' style="width: 100px;" onmouseover="show_sp('ship_sp_<?php echo $order->order_id; ?>');write_status('shipping','<?php echo $order->order_id; ?>',<?php echo $order->shipping_status; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <?php } ?>
                                <?php if(in_array("total",$headers_array)){ ?><td align="center" nowrap="nowrap"><strong><?php if($order->credits_total > 0){ echo "<span style='font-size: 12px;'>$order->credits_total</span> <span style='color: #797979;' >{$mgrlang[gen_credits]}</span><br />";  } ?><?php if($order->total){ echo $cleanvalues->currency_display($order->total,1); } ?></strong></td><?php } ?>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                    <a href="javascript:deleterec(<?php echo $order->order_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a> 
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <input type="checkbox" name="items[]" value="<?php echo $order->order_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
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
                } else {
                    notice($mgrlang['gen_no_orders']);
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