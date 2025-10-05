<?php
	###################################################################
	####	BILLINGS			  	                               ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-31-2011                                     ####
	####	Modified: 1-31-2011                                    #### 
	###################################################################
	
		$page = "billings";
		$lnav = "sales";		
		$supportPageID = '354';
		
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
					
					# GET TITLES FOR LOG
					$log_result = mysqli_query($db,"SELECT invoice_number FROM {$dbinfo[pre]}invoices WHERE bill_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result))
					{
						$log_titles.= "$log->invoice_number ($log->bill_id), ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", ")
					{
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# SET BiLLINGS TO DELETED
					@mysqli_query($db,"UPDATE {$dbinfo[pre]}billings SET deleted='1' WHERE bill_id IN ($delete_array)");
					
					# FIND INVOICE ITEMS AND SET THEM TO DELETED
					$invoice_result = mysqli_query($db,"SELECT invoice_id FROM {$dbinfo[pre]}invoices WHERE bill_id IN ($delete_array)");
					while($invoice = mysqli_fetch_object($invoice_result))
					{
						@mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET deleted='1' WHERE invoice_id = '$invoice->invoice_id'");
					}
					
					# SET INVOICES TO DELETED
					@mysqli_query($db,"UPDATE {$dbinfo[pre]}invoices SET deleted='1' WHERE bill_id IN ($delete_array)");
					
					# UPDATE ORDERS IF BILL ME LATER BILL
					@mysqli_query($db,"UPDATE {$dbinfo[pre]}orders SET bill_id='0' WHERE bill_id IN ($delete_array)");
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_bill_item'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
					
				} else {
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}
			break;
			# UPDATE HEADERS
			case "upheaders":	
				# CONVERT TO STRING
				$headers_full = implode(",",$_POST['billings_headers']);
				# UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET billings_headers='$headers_full' WHERE settings_id = '1'");
				# REFRESH SETTINGS
				include('mgr.select.settings.php');
			break;
			# UPDATE FILTERS
			case "upfilters":
				# CONVERT TO STRING
				$filters_full = implode(",",$_POST['billings_filters']);
				# UPDATE GROUP DATABASE
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET billings_filters='$filters_full' WHERE settings_id = '1'");
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
		$sortprefix="billings";
		$datasorts = new data_sorting;
		$datasorts->prefix = $sortprefix;
		$datasorts->clear_sorts($_GET['ep']);
		$id_field_name = "bill_id";			
		require_once('mgr.datasort.logic.php');				
		
		# IF THIS IS AN ENTRY PAGE OR billingsgroups IS BLANK RESET THE billingsgroups SESSION	
		if($_GET['ep'] or empty($_SESSION['billingsgroups']))
		{
			$_SESSION['billingsgroups'] = array('all');
		}			
		# SEE IF ANY GROUPS HAVE BEEN PASSED
		if($_REQUEST['setgroups'])
		{
			if(is_array($_REQUEST['setgroups']))
			{
				$_SESSION['billingsgroups'] = $_REQUEST['setgroups'];
			}
			else
			{				
				$_SESSION['billingsgroups'] = array($_REQUEST['setgroups']);
			}
		}
		
		# UPDATE FILTERS
		if($_GET['dtype'] == "filters")
		{
			
		}
		
		# SEE IF ANY SEARCH HAS BEEN PASSED
		if(!empty($_REQUEST['search']))
		{
			$_SESSION['billings_search'] = $_REQUEST['search'];
		}
		
		# CHECK TO SEE IF THE SEARCH FIELDS ARE GETTING SET
		if($_REQUEST['setsearchfields'])
		{
			$_SESSION['sfb_array'] = $_REQUEST['setsearchfields'];
		}
		else
		{
			// set it to all?
		}
		
		# IF THIS IS AN ENTRY PAGE RESET SESSION	
		if($_GET['ep'] or empty($_SESSION['sfb_array']) or $_REQUEST['dtype'] != 'search')
		{
			$_SESSION['sfb_array'] = array('all');
		}
		
		# SET TO LOCAL VALUE
		$sfb_array = $_SESSION['sfb_array'];
		
		# FOR EASE MAKE THE VARIABLE LOCAL
		$insearch = $_SESSION['billings_search'];
		
		//echo $_SESSION['billings_search']; exit;
		
		# PUT THE HEADER VALUES INTO AN ARRAY
		if($config['settings']['billings_headers'])
		{
			$headers_array = explode(",",$config['settings']['billings_headers']);
		}
		else
		{
			$headers_array = array("bill_id","bill_number","l_name","invoice_number","invoice_date","due_date","payment_status","total");   
		}
		
		# PUT THE FILTER VALUES INTO AN ARRAY
		if($config['settings']['billings_filters'])
		{
			
			$filters_array = explode(",",$config['settings']['billings_filters']);
		}
		else
		{
			$filters_array = array(0,1,3,4,2);	
		}
		
		# MAKE SEARCH, LIST BY OR GROUP CHANGE GO BACK TO THE MAIN PAGE
		if($_REQUEST['search'] or $_GET['mgroup'] or $_GET['listby'])
		{
			$_SESSION['currentpage'] = 1;
		}
		
		# IF THIS IS AN ENTRY PAGE RESET THE SESSION DISPLAY TYPE
		if($_GET['ep'] == '1')
		{
			$_SESSION['billings_dtype'] = 'default';
		}
		
		# IF THE DISPLAY TYPE IS RESET THROUGH GET UPDATE THE SESSION
		if($_REQUEST['dtype'])
		{
			$_SESSION['billings_dtype'] = $_REQUEST['dtype'];
			# RESET THE CURRENT PAGE
			if(isset($_SESSION['currentpage']))
			{
				$_SESSION['currentpage'] = 1;
			}
							
		}
		
		# SET THE DEFAULT SESSION DISPLAY TYPE - MIGHT NOT BE NEEDED
		if(!$_SESSION['billings_dtype'])
		{
			$_SESSION['billings_dtype'] = 'default';
		}
		
		# DECIDE WHICH TYPE OF RECORDS TO PULL
		switch($_SESSION['billings_dtype'])
		{
			default:
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT({$dbinfo[pre]}billings.bill_id) FROM {$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE {$dbinfo[pre]}invoices.payment_status IN (".implode(",",$filters_array).") AND {$dbinfo[pre]}billings.deleted='0'"));
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
						if(in_array("sfb_lastname",$sfb_array) or in_array('all',$sfb_array)){ 		$sql_search.= " or {$dbinfo[pre]}members.l_name LIKE '%$value%'"; 	}
						if(in_array("sfb_id",$sfb_array) or in_array('all',$sfb_array)){			$sql_search.= " or {$dbinfo[pre]}billings.bill_id LIKE '%$value%'"; 	}
						if(in_array("sfb_uid",$sfb_array) or in_array('all',$sfb_array)){ 			$sql_search.= " or {$dbinfo[pre]}members.umember_id LIKE '%$value%'"; 	}
						if(in_array("sfb_email",$sfb_array) or in_array('all',$sfb_array)){			$sql_search.= " or {$dbinfo[pre]}members.email LIKE '%$value%'"; 	}
						if(in_array("sfb_bill_number",$sfb_array) or in_array('all',$sfb_array)){	$sql_search.= " or {$dbinfo[pre]}billings.bill_number LIKE '%$value%'";	}
						if(in_array("sfb_invoice_number",$sfb_array) or in_array('all',$sfb_array)){$sql_search.= " or {$dbinfo[pre]}invoices.invoice_number LIKE '%$value%'";	}
						//if(in_array("sfb_invoice_id",$sfb_array) or in_array('all',$sfb_array)){	$sql_search.= " or {$dbinfo[pre]}orders.invoice_id LIKE '%$value%'";	}
						//if(in_array("sf_notes",$sfb_array) or in_array('all',$sfb_array)){		$sql_search.= " or notes LIKE '%$value%'";		}
						//if(in_array("sf_comp_name",$sfb_array) or in_array('all',$sfb_array)){	$sql_search.= " or comp_name LIKE '%$value%'";	}
						//if(in_array("sf_address",$sfb_array) or in_array('all',$sfb_array)){	$sql_search.= " or address LIKE '%$value%' or address_2 LIKE '%$value%' or city LIKE '%$value%'"; }
						$snext++;
					}
				}
				//echo $sql_search; exit;
				$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(ubill_id) FROM ({$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}billings.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE $sql_search AND {$dbinfo[pre]}billings.deleted='0'"));
			break;
			
			case "groups":
				$billings_result2 = "SELECT COUNT(ubill_id) FROM {$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['billingsgroups']).") AND {$dbinfo[pre]}billings.deleted='0'";
				$r_rows = mysqli_result_patch(mysqli_query($db,$billings_result2));
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
		
		
		
		switch($listby)
		{
			default:
			case 'member_id':
				$orderByTable = "{$dbinfo[pre]}billings.";
			break;
			case 'payment_status':
			case 'total':
			case 'due_date':
			case 'invoice_number':
			case 'invoice_date':
				$orderByTable = "{$dbinfo[pre]}invoices.";
			break;
		}
		
		switch($_SESSION['billings_dtype'])
		{
			default:
				$billings_result = mysqli_query($db,"SELECT * FROM ({$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}billings.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE {$dbinfo[pre]}invoices.payment_status IN (".implode(",",$filters_array).") AND {$dbinfo[pre]}billings.deleted='0' ORDER BY {$orderByTable}$listby $listtype");
				$billings_rows = mysqli_num_rows($billings_result);
				//echo "tests: ".$billings_rows; exit;
			break;
			case "search":
				$billings_result = mysqli_query($db,"SELECT $select_fields FROM ({$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}billings.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE $sql_search AND {$dbinfo[pre]}billings.deleted='0' ORDER BY {$orderByTable}$listby $listtype LIMIT $startrecord,$perpage");
				//$billings_rows = mysqli_num_rows($billings_result);
				//echo "tests: ".$billings_rows; exit;
			break;
			
			case "groups":
				$billings_result = mysqli_query($db,"SELECT * FROM ({$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}groupids.item_id) LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}billings.member_id = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE {$dbinfo[pre]}groupids.group_id IN (".implode(",",$_SESSION['billingsgroups']).") AND {$dbinfo[pre]}billings.deleted='0' GROUP BY {$dbinfo[pre]}billings.bill_id ORDER BY {$orderByTable}$listby $listtype");
			break;
		}
		
		/*
		# FIX FOR RECORDS GETTING DELETED
		if($startrecord > ($r_rows - 1))
		{
			$startrecord-=$perpage;
		}
		
		$billings_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}billings.member_id = {$dbinfo[pre]}members.member_id ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
		$billings_rows = mysqli_num_rows($billings_result);
		*/
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_billings']; ?></title>
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
						window.location.href='mgr.billings.edit.php?edit=new';
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
			<?php
				// LOAD THE GROUPLIST AREA
				if($_SESSION['billings_dtype'] == 'groups' or $_GET['dtype'] == 'groups')
				{
					echo "load_group_selector();";
				}
			?>
			
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
		function switch_bill_payment_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				$('billstatus_sp_'+item_id).hide();
				hide_bill_sp();
				$('billpaymentstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'billpaymentstatuscheck' + item_id;
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
		
		function show_bill_sp(id)
		{
			clearTimeout(status_bill_popup_timeout);
			$(id).show();
			$$('.status_popup').each(function(e){ if(id != e.id){ e.hide(); } });
		}
		
		var status_bill_popup_timeout;
		
		function hide_bill_sp()
		{
			clearTimeout(status_bill_popup_timeout);
			status_bill_popup_timeout = setTimeout(function(){$$('.status_popup').each(function(e){ e.hide(); });},200); // e.fade({ duration: 0.3 });
		}
		
		function clear_bill_sp_timeout()
		{
			clearTimeout(status_bill_popup_timeout);
		}
		
		function write_bill_status(mode,id,curstatus)
		{
			var content = ''
			var div_id = ''
			//alert(curstatus);
			switch(mode)
			{
				case "billstatus":
					div_id = "billstatus_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_processing mtag' onclick=\"switch_bill_payment_status('"+id+"',0);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_processing']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_paid mtag' onclick=\"switch_bill_payment_status('"+id+"',1);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_paid']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_unpaid mtag' onclick=\"switch_bill_payment_status('"+id+"',2);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_unpaid']; ?></div>"; }
					//if(curstatus != 3){ content+= "<div class='mtag_purple' onclick=\"switch_payment_status('"+id+"',3);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_bill']; ?></div>"; }
					if(curstatus != 4){ content+= "<div class='mtag_failed mtag' onclick=\"switch_bill_payment_status('"+id+"',4);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_failed']; ?></div>"; }
					if(curstatus != 5){ content+= "<div class='mtag_refunded mtag' onclick=\"switch_bill_payment_status('"+id+"',5);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_refunded']; ?></div>"; }
					if(curstatus != 6){ content+= "<div class='mtag_cancelled mtag' onclick=\"switch_bill_payment_status('"+id+"',6);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_cancelled']; ?></div>"; }
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
        ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">	
            	<div class="sec_bar">
                    <img src="./images/mgr.badge.invoice.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_billings']; ?></span> &nbsp; 
                </div>                
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
            						
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
					<?php if(!empty($r_rows)){ ?>
                        <div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_delete_sel']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>
                    <?php } ?>
                    <div style="float: left;" class="abuttons" id="abutton_search"><img src="./images/mgr.button.search.off.png" align="absmiddle" border="0" id="img_search" /><br /><?php echo $mgrlang['gen_b_search']; ?></div>
                    <div style="float: left;" class="abuttons" id="abutton_filters"><img src="./images/mgr.button.filter.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_filters']; ?>" id="img_filters" /><br /><?php echo $mgrlang['gen_b_filters']; ?></div>
                    <?php if(in_array("pro",$installed_addons)){ ?><div style="float: left;" class="abuttons" id="abutton_group"><img src="./images/mgr.button.group.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt']; ?>" id="img_group" /><br /><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
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
            </div>
            
            <!-- GROUPS WINDOW -->
			<form name="grouplist" id="grouplist" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            	<input type="hidden" name="dtype" value="groups" />
				<div style="<?php if($_SESSION['billings_dtype'] == 'groups'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="group_selector"></div>
			</form>
            
            <!-- BILLINGS FILTERS WINDOW AREA -->
            <form name="filterlist" id="filterlist" action="mgr.billings.php?action=upfilters" method="post">
			<input type="hidden" name="dtype" value="filter" />
            <div style="display: none;" class="options_area" id="filters_selector">
				<div class="opbox_buttonbar">
					<p><?php echo $mgrlang['billings_display']; ?>:</p>
					<a href="#" class='actionlink' id="button_filters_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>                
                <div class="options_area_box">                  
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_0" value="0" <?php if(in_array("0",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_0"> <?php echo $mgrlang['gen_processing']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_1" value="1" <?php if(in_array("1",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_1"> <?php echo $mgrlang['gen_paid']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_2" value="2" <?php if(in_array("2",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_2"> <?php echo $mgrlang['gen_unpaid']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_4" value="4" <?php if(in_array("4",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_4"> <?php echo $mgrlang['gen_failed']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_5" value="5" <?php if(in_array("5",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_5"> <?php echo $mgrlang['gen_refunded']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_filters[]" id="billings_filter_6" value="6" <?php if(in_array("6",$filters_array)){ echo "checked='checked'"; } ?> /><label for="billings_filter_6"> <?php echo $mgrlang['gen_cancelled']; ?></label></strong></div>
				</div>                                      
			</div>
			</form>
            
            <!-- SELECT DETAILS TO SHOW ON LIST VIEW -->
            <form action="mgr.billings.php?action=upheaders" id="headers_form" method="post">
            <input type="hidden" name="billings_headers[]" value="l_name" />
            <input type="hidden" name="dtype" value="<?php echo $_SESSION['billings_dtype']; ?>" />
            <div style="display: none;" class="options_area" id="billings_details_sel">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['display_data']; ?>:</p>
                    <a href="#" class='actionlink' id="button_headers_update"><?php echo $mgrlang['gen_b_update']; ?></a>
                </div>
                <div class="options_area_box">                    
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="bill_id" value="bill_id" <?php if(in_array("bill_id",$headers_array)){ echo "checked='checked'"; } ?> /><label for="bill_id"> <?php echo $mgrlang['gen_t_id']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="l_name" value="l_name" checked="checked" disabled="disabled" /><label for="l_name"> <?php echo $mgrlang['customer']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="invoice_number" value="invoice_number" <?php if(in_array("invoice_number",$headers_array)){ echo "checked='checked'"; } ?> /><label for="invoice_number"> <?php echo $mgrlang['gen_invoice_number']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="payment_status" value="payment_status" <?php if(in_array("payment_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="payment_status"> <?php echo $mgrlang['gen_payment_status']; ?></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="invoice_date" value="invoice_date" <?php if(in_array("invoice_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="invoice_date"> <?php echo $mgrlang['gen_invoice_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="due_date" value="due_date" <?php if(in_array("due_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="due_date"> <?php echo $mgrlang['gen_due_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="payment_status" value="payment_status" <?php if(in_array("payment_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="payment_status"> <?php echo $mgrlang['gen_t_status']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="payment_date" value="payment_date" <?php if(in_array("payment_date",$headers_array)){ echo "checked='checked'"; } ?> /><label for="payment_date"> <?php echo $mgrlang['gen_payment_date']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="total" value="total" <?php if(in_array("total",$headers_array)){ echo "checked='checked'"; } ?> /><label for="total"> <?php echo $mgrlang['gen_total']; ?></label></strong></div>
                    <div class='opbox_list'><input type="checkbox" name="billings_headers[]" id="shipping_status" value="shipping_status" <?php if(in_array("shipping_status",$headers_array)){ echo "checked='checked'"; } ?> /><label for="shipping_status"> <?php echo $mgrlang['gen_shipping_status']; ?></label></strong></div>
                </div>
            </div>
            </form>
            
            <!-- ADVANCED SEARCH AREA -->
            <form action="mgr.billings.php" method="post" id="search_from">
            <input type="hidden" name="dtype" value="search" /> 
            <div style="<?php if($_SESSION['billings_dtype'] == 'search'){ echo "display: block;"; } else { echo "display: none;"; } ?>" class="options_area" id="billings_adv_search">
            	<div class="opbox_buttonbar">
                    <p><?php echo $mgrlang['gen_b_search']; ?>:</p>
                    <a href="#" class='actionlink' id="button_search"><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_b_search']; ?></a><?php if($_SESSION['billings_dtype'] == 'search'){ ?><a href="mgr.billings.php?ep=1" class='actionlink'><?php echo $mgrlang['gen_b_updatedddddd']; ?><?php echo $mgrlang['gen_exit_search']; ?></a><?php } ?>
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
                    <p style="clear: both; margin: 6px 0 6px 0;"><input type="text" name="search" id='search_field' value="<?php if($_SESSION['billings_dtype'] == 'search'){ echo $_SESSION['billings_search']; } ?>" style="width: 250px;" /></p>
                    <div class='opbox_list' style="width: auto; padding-left: 1px;"><input type="checkbox" name="setsearchfields[]" id="sfb_id" value="sfb_id" <?php if(in_array("sfb_id",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_id"> <?php echo $mgrlang['gen_t_id']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfb_firstname" value="sfb_firstname" <?php if(in_array("sfb_firstname",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_firstname"> <?php echo $mgrlang['mem_f_fname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfb_lastname" value="sfb_lastname" <?php if(in_array("sfb_lastname",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_lastname"> <?php echo $mgrlang['mem_f_lname']; ?></label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfb_email" value="sfb_email" <?php if(in_array("sfb_email",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_email"> <?php echo $mgrlang['mem_f_email']; ?></label></strong></div>
                    <!--<div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfb_bill_number" value="sfb_bill_number" <?php if(in_array("sfb_bill_number",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_bill_number"> Bill Number</label></strong></div>-->
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sfb_invoice_number" value="sfb_invoice_number" <?php if(in_array("sfb_invoice_number",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sfb_invoice_number"> <?php echo $mgrlang['gen_invoice_number']; ?></label></strong></div>
                    <!--<div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_website" value="sf_website" <?php if(in_array("sf_website",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sf_website"> Website</label></strong></div>
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_notes" value="sf_notes" <?php if(in_array("sf_notes",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sf_notes"> Notes</label></strong></div>  
                    <div class='opbox_list' style="width: auto;"><input type="checkbox" name="setsearchfields[]" id="sf_uid" value="sf_uid" <?php if(in_array("sf_uid",$sfb_array) or in_array('all',$sfb_array)){ echo "checked='checked'"; } ?> /><label for="sf_uid"> Unique ID</label></strong></div>
                    -->
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
							<?php $header_name = "bill_id";			if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                            <!--<?php $header_name = "bill_number";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>">BILL NUMBER</a></div></div></td>-->
                            <?php $header_name = "invoice_number";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['order_t_invoicenum']; ?></a></div></div></td>
                            <?php $header_name = "member_id";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%" align="left"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_member']; ?></a></div></div></td>
                            <?php $header_name = "invoice_date";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['bill_date_caps']; ?></a></div></div></td>
							<?php $header_name = "due_date";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_due_date_caps']; ?></a></div></div></td>
                            <?php $header_name = "total";			if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_total_caps']; ?></a></div></div></td>
                            <?php $header_name = "payment_status";			if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" align="center"><div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_status']; ?></a><span class='pending_number' id='ph_status' <?php if($_SESSION['pending_billings'] == 0){ echo "style='display: none;'"; } ?>  onclick="window.location.href='mgr.billings.php?listby=status&listtype=asc'"><?php echo $_SESSION['pending_billings']; ?></span></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            # CREATE A DATE OBJECT
							$mcdate = new kdate;	
							
							$zindex = 1000;
							
							# SELECT LOOP THRU ITEMS									
                            while($billings = mysqli_fetch_object($billings_result))
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
                                <td align="center" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><a name="row_<?php echo $billings->bill_id; ?>"></a><?php echo $billings->bill_id; ?></td>
                                <!--<td align="center" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><?php echo $billings->bill_number; ?></td>-->
                                <td align="center" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><?php echo $billings->invoice_number; ?></td>
                                <td align="left" nowrap="nowrap" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'">
									<div style="float: left;">
                                    <?php
                                        if(file_exists("../assets/avatars/" . $billings->member_id . "_small.png"))
                                        {
                                            echo "<img src='../assets/avatars/" . $billings->member_id . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                                        }
                                        else
                                        {
                                            echo "<img src='images/mgr.no.avatar.gif' width='19' style='vertical-align: middle; margin-right: 8px;' class='mediaFrame' />";
                                        }
                                    ?>
                                    <a href="<?php if(in_array("members",$_SESSION['admin_user']['permissions'])){ echo "mgr.members.edit.php?edit=$billings->member_id"; } else { echo "#"; } ?>" class="editlink" style="margin-right: 10px;" onmouseover="start_mem_panel(<?php echo $billings->bill_id; ?>,<?php echo $billings->member_id; ?>);" onmouseout="cancel_mem_panel(<?php echo $billings->bill_id; ?>,<?php echo $billings->member_id; ?>);"><?php echo $billings->f_name ." ". $billings->l_name; ?></a>
                                    </div>
                                    <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                                        <div id="more_info_<?php echo $billings->member_id; ?>-<?php echo $billings->bill_id; ?>" style="display: none;" class="mem_details_win">
                                            <div class="mem_details_win_inner">
                                                <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                <div id="more_info_<?php echo $billings->member_id; ?>-<?php echo $billings->bill_id; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td nowrap="nowrap" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><?php echo $mcdate->showdate($billings->invoice_date); ?></td>
                                <td nowrap="nowrap" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><?php echo $mcdate->showdate($billings->due_date); ?></td>
                                <td align="center" nowrap="nowrap" onclick="window.location.href='mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>'"><?php echo $cleanvalues->currency_display($billings->total,1); ?></td>
                                <td align="center">
                                	<?php
										/*
                                    <div id="paymentstatuscheck<?php echo $billings->bill_id; ?>">
                                    <?php
                                        switch($billings->payment_status)
                                        {
                                            case 0: // PENDING                                                
												echo "<div class='mtag_good' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_processing]</div>";
                                            break;
                                            case 1: // APPROVED
                                                echo "<div class='mtag_dblue' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_paid]</div>";
                                            break;
                                            case 2: // INCOMPLETE/NONE
                                                echo "<div class='mtag_grey' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_unpaid]</div>";
                                            break;
											case 3: // BILL/LATER
                                                //echo "<div class='mtag_purple' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_bill]</div>";
                                            break;
											case 4: // FAILED/CANCELLED
                                                echo "<div class='mtag_bad' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_failed]</div>";
                                            break;
											case 5: // REFUNDED
                                                echo "<div class='mtag_black' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_refunded]</div>";
                                            break;
											case 6: // REFUNDED
                                                echo "<div class='mtag_grey' onclick='switch_payment_status($billings->bill_id);'>$mgrlang[gen_cancelled]</div>";
                                            break;
                                        }									
                                    ?>
                                    </div>
										*/
									?>
                                    <div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
                                    <div class='status_popup' id='billstatus_sp_<?php echo $billings->bill_id; ?>' style="z-index: <?php echo $zindex-1; ?>; display: none;" onmouseout="hide_bill_sp();" onmouseover="clear_bill_sp_timeout();"></div>
                                    <div id="billpaymentstatuscheck<?php echo $billings->bill_id; ?>" style="position: absolute; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px">
										<?php
                                            switch($billings->payment_status)
                                            {
                                                case 0: // PROCESSING
                                                    $tag_label = $mgrlang['gen_processing'];
                                                    $mtag = 'mtag_processing';
                                                break;
                                                case 1: // APPROVED
                                                    $tag_label = $mgrlang['gen_paid'];
                                                    $mtag = 'mtag_paid';
                                                break;
                                                case 2: // INCOMPLETE
                                                    $tag_label = $mgrlang['gen_unpaid'];
                                                    $mtag = 'mtag_unpaid';
                                                break;
                                                case 3: 
													// BILL LATER
                                                break;
                                                case 4: // FAILED
                                                    $tag_label = $mgrlang['gen_failed'];
                                                    $mtag = 'mtag_failed';
                                                break;
												case 5: // REFUNDED
                                                    $tag_label = $mgrlang['gen_refunded'];
                                                    $mtag = 'mtag_refunded';
                                                break;
												case 6: // FAILED
                                                    $tag_label = $mgrlang['gen_cancelled'];
                                                    $mtag = 'mtag_cancelled';
                                                break;
                                            }
                                        ?>
                                   	  <div class='<?php echo $mtag; ?> mtag' onmouseover="show_bill_sp('billstatus_sp_<?php echo $billings->bill_id; ?>');write_bill_status('billstatus','<?php echo $billings->bill_id; ?>',<?php echo $billings->payment_status; ?>)"><?php echo $tag_label; ?></div>
                                    </div>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.billings.edit.php?edit=<?php echo $billings->bill_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a>
                                    <a href="javascript:deleterec(<?php echo $billings->bill_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                                </td>
                                <td align="center" valign="middle" nowrap>
                                    <input type="checkbox" name="items[]" value="<?php echo $billings->bill_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" />
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