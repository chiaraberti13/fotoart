<?php
	###################################################################
	####	BILLINGS EDIT AREA                                     ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
		$page = "billings";
		$lnav = "sales";
		
		$supportPageID = '355';
	
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
		
		$ndate = new kdate;
		$ndate->distime = 0;
		
		//$ndate->date_format = 'NONE';
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$billing_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}billings LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}billings.bill_id = {$dbinfo[pre]}invoices.bill_id WHERE {$dbinfo[pre]}billings.bill_id = '$_GET[edit]'");
			$billing_rows = mysqli_num_rows($billing_result);
			$billing = mysqli_fetch_object($billing_result);
			
			# MEMBER INFO
			$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '$billing->member_id'");
            $memship_rows = mysqli_num_rows($member_result);
            $mgrMemberInfo = mysqli_fetch_object($member_result);
			
			# GET THE MEMBERS ADDRESS
			$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '$mgrMemberInfo->mem_id'");
			$address_rows = mysqli_num_rows($address_result);
			$address = mysqli_fetch_object($address_result);
		}
		if($_GET['edit'] == "new")
		{
			# DELETE ALL PREVIOUS UNASSIGNED INVOICE ITEMS
			$sql="DELETE FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '0'";
			$result = mysqli_query($db,$sql);
		}
		
		if($_REQUEST['action'])
		{
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			$bill_date = $_POST['bill_year']."-".$_POST['bill_month']."-".$_POST['bill_day']." 00:00:00";
			$due_date = $_POST['due_year']."-".$_POST['due_month']."-".$_POST['due_day']." 00:00:00";
			
			$bill_date = $ndate->formdate_to_gmt($bill_date);
			$due_date = $ndate->formdate_to_gmt($due_date);
		}
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SAVE EDIT				
			case "save_edit":
								
				# SAVE INVOICE ITEMS
				if($bill_type == 1)
				{	
					if($originalPaymentStatus != $payment_status and $payment_status == 1) // Only do updates if the status has switched to paid
					{
						$billResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}invoices LEFT JOIN {$dbinfo[pre]}billings ON {$dbinfo[pre]}invoices.bill_id = {$dbinfo[pre]}billings.bill_id WHERE {$dbinfo[pre]}invoices.invoice_id = '{$invoice_id}'");
						$bill = mysqli_fetch_array($billResult);
						
						if($bill['membership']) // This is for a membership
						{
							$membershipResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}memberships WHERE ms_id = '{$bill[membership]}'");
							$membershipRows = mysqli_num_rows($membershipResult);
							
							if($membershipRows) // Make sure there are rows
							{
								$membership = mysqli_fetch_array($membershipResult);
								
								//test($membershipRows,'msRows');
								
								// Select member details
								$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$bill[member_id]}'");
								$memberRows = mysqli_num_rows($memberResult);
								
								if($memberRows)
								{
									$member = mysqli_fetch_array($memberResult);
									
									// Do calculations
									
									if($membership['mstype'] == 'onetime') // One time payment
									{
										$msEndDate = '';
									}
									else if($membership['mstype'] == 'recurring') // Recurring
									{
										switch($membership['period'])
										{
											case "weekly":
												$days = 7;
											break;
											case "monthly":
												$days = 30;
											break;
											case "quarterly":
												$days = 90;
											break;
											case "semi-annually":
												$days = 180;
											break;
											case "annually":
												$days = 365;
											break;
										}
										
										$msEndDate = gmdate("Y-m-d h:i:s",strtotime("+{$days} days"));
									}
									
									mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET ms_end_date='{$msEndDate}',membership='{$bill[membership]}' WHERE mem_id = '{$member[mem_id]}'"); // Update member account		
									
									//test($memberRows,'memRows');
								}
							}
						}
					}
					
					foreach($item_id as $key => $value)
					{
						$item_cost_clean[$key] = $cleanvalues->currency_clean($item_cost[$key]);	
						
						# UPDATE INVOICE ITEMS
						$sql = "UPDATE {$dbinfo[pre]}invoice_items SET 
								description='$item_desciption[$key]',
								price_total='$item_cost_clean[$key]',
								taxed='$item_taxed[$key]' 
								WHERE oi_id  = '$value'";
						$result = mysqli_query($db,$sql);
					}
					
					$subtotal = array_sum($item_cost_clean);					
					$total = $subtotal;
				}
				# SAVE ORDER ITEMS
				else
				{
					# RESET ORDERS
					$sql = "UPDATE {$dbinfo[pre]}orders SET bill_id='0' WHERE bill_id  = '$saveid'";
					$result = mysqli_query($db,$sql);
					
					# GRAB SELECTED ORDERS
					foreach($orders as $key => $value)
					{
						# QUERY SELECTED ORDERS
						$order_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.order_id = '{$value}'");
						$order = mysqli_fetch_object($order_result);
						
						$tax_a+=$order->taxa_cost;
						$tax_b+=$order->taxb_cost;
						$tax_c+=$order->taxc_cost;
						$subtotal+=$order->subtotal;						
						$discounts+=$order->discounts_total;						
						$shipping+=$order->shipping_cost;
						$total+=$order->total;
						
						# UPDATE ORDER
						$sql = "UPDATE {$dbinfo[pre]}orders SET bill_id='$saveid' WHERE order_id  = '$value'";
						$result = mysqli_query($db,$sql);							
					}
				}
				
				# MEMBER INFO
				$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id WHERE mem_id = '$permpurchaser'");
           		$memship_rows = mysqli_num_rows($member_result);
            	$mgrMemberInfo = mysqli_fetch_object($member_result);
				
				# UPDATE INVOICE
				$sql = "UPDATE {$dbinfo[pre]}invoices SET 
						invoice_date='$bill_date',
						due_date='$due_date',
						payment_status='$payment_status',
						subtotal='$subtotal',
						total='$total',
						taxa_cost='$tax_a',
						taxb_cost='$tax_b',
						taxc_cost='$tax_c',
						shipping_cost='$shipping',
						discounts_total='$discounts',
						bill_address='$mgrMemberInfo->address',
						bill_address2='$mgrMemberInfo->address_2',
						bill_city='$mgrMemberInfo->city',
						bill_country='$mgrMemberInfo->country',
						bill_state='$mgrMemberInfo->state',
						bill_zip='$mgrMemberInfo->postal_code',
						ship_address='$mgrMemberInfo->address',
						ship_address2='$mgrMemberInfo->address_2',
						ship_city='$mgrMemberInfo->city',
						ship_country='$mgrMemberInfo->country',
						ship_state='$mgrMemberInfo->state',
						ship_zip='$mgrMemberInfo->postal_code',
						ship_phone='$mgrMemberInfo->phone',
						bill_phone='$mgrMemberInfo->phone'
						WHERE invoice_id  = '$invoice_id'";
				$result = mysqli_query($db,$sql);
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_billings'],1,$mgrlang['gen_b_ed'] . " > <strong>($saveid)</strong>");
				
				header("location: mgr.billings.php?mes=edit"); exit;
			break;
			case "save_new":
				
				# CREATE UBILL ID
				$ubill_id = create_unique2();
				
				# GET MEMBER ID
				if($bill_type == 1)
				{
					$mem_id = $permpurchaser;
				}
				else
				{
					$mem_id = $bmlmember;
				}
				
				# GET NEW INVOICE NUMBER
				$invoice_number = $config['settings']['invoice_prefix'] . $config['settings']['invoice_next'] . $config['settings']['invoice_suffix'];
				$cur_inv = $config['settings']['invoice_next'];
				$next_inv = $cur_inv+1;
				
				# UPDATE SETTINGS WITH NEXT INVOICE NUMBER
				$sql = "UPDATE {$dbinfo[pre]}settings SET invoice_next = '$next_inv' WHERE settings_id  = '1'";
				$result = mysqli_query($db,$sql);
				
				# CREATE BILLING RECORD
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}billings 
					(
						ubill_id,
						member_id,
						bill_type
					) VALUES (
						'$ubill_id',
						'$mem_id',
						'$bill_type'
					)");
				$saveid = mysqli_insert_id($db);
				
				# MEMBER INFO
				$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members LEFT JOIN {$dbinfo[pre]}members_address ON {$dbinfo[pre]}members.mem_id = {$dbinfo[pre]}members_address.member_id WHERE mem_id = '$mem_id'");
           		$memship_rows = mysqli_num_rows($member_result);
            	$mgrMemberInfo = mysqli_fetch_object($member_result);
				
				# CREATE INVOICE RECORD
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}invoices 
					(
						invoice_number,
						invoice_mem_id,
						bill_id,
						invoice_date,
						due_date,
						payment_status,
						ship_address,
						ship_address2,
						ship_city,
						ship_country,
						ship_state,
						ship_zip,
						bill_address,
						bill_address2,
						bill_city,
						bill_country,
						bill_state,
						bill_zip
					) VALUES (
						'$invoice_number',
						'$mem_id',
						'$saveid',
						'$bill_date',
						'$due_date',
						'$payment_status',
						'$mgrMemberInfo->address',
						'$mgrMemberInfo->address_2',
						'$mgrMemberInfo->city',
						'$mgrMemberInfo->country',
						'$mgrMemberInfo->state',
						'$mgrMemberInfo->zip',
						'$mgrMemberInfo->address',
						'$mgrMemberInfo->address_2',
						'$mgrMemberInfo->city',
						'$mgrMemberInfo->country',
						'$mgrMemberInfo->state',
						'$mgrMemberInfo->zip'
					)");
				$saveid2 = mysqli_insert_id($db);
				
				# SAVE INVOICE ITEMS
				if($bill_type == 1)
				{	
					# INSERT INVOICE ITEMS
					foreach($item_id as $key => $value)
					{
						$item_cost_clean[$key] = $cleanvalues->currency_clean($item_cost[$key]);	
						
						# UPDATE INVOICE ITEMS
						$sql = "UPDATE {$dbinfo[pre]}invoice_items SET 
								description='$item_desciption[$key]',
								price_total='$item_cost_clean[$key]',
								taxed='$item_taxed[$key]', 
								invoice_id='$saveid2'
								WHERE oi_id  = '$value'";
						$result = mysqli_query($db,$sql);
					}
					$subtotal = array_sum($item_cost_clean);					
					$total = $subtotal;
					
					# calculate tax - moved to PS4.1 [todo]
					
					# UPDATE INVOICE
					$sql = "UPDATE {$dbinfo[pre]}invoices SET 
							invoice_date='$bill_date',
							due_date='$due_date',
							payment_status='$payment_status',
							subtotal='$subtotal',
							total='$total',
							taxa_cost='',
							taxb_cost='',
							taxc_cost=''
							WHERE invoice_id  = '$saveid2'";
					$result = mysqli_query($db,$sql);
					
				}
				# SAVE ORDER ITEMS
				else
				{
					# GRAB SELECTED ORDERS
					foreach(@$orders as $key => $value)
					{
						
						//echo $value."<br />";
						
						# QUERY SELECTED ORDERS
						$order_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.order_id = '$value'");
						$order = mysqli_fetch_object($order_result);
						
						$tax_a+=$order->taxa_cost;
						$tax_b+=$order->taxb_cost;
						$tax_c+=$order->taxc_cost;
						$subtotal+=$order->subtotal;
						$shipping+=$order->shipping_cost;
						$total+=$order->total;
						
						# UPDATE ORDER
						$sql = "UPDATE {$dbinfo[pre]}orders SET bill_id='$saveid' WHERE order_id  = '$value'";
						$result = mysqli_query($db,$sql);
							
					}
					
					# UPDATE INVOICE
					$sql = "UPDATE {$dbinfo[pre]}invoices SET 
							invoice_date='$bill_date',
							due_date='$due_date',
							payment_status='$payment_status',
							subtotal='$subtotal',
							total='$total',
							shipping_cost='$shipping',
							taxa_cost='$tax_a',
							taxb_cost='$tax_b',
							taxc_cost='$tax_c'
							WHERE invoice_id  = '$saveid2'";
					$result = mysqli_query($db,$sql);
				}
				
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_billings'],1,$mgrlang['gen_b_new'] . " > <strong>($saveid)</strong>");
				
				header("location: mgr.billings.php?mes=new"); exit;
			break;
		}		
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
    <!-- MESSAGE WINDOW JS -->
	<script type="text/javascript" src="mgr.js.messagewin.php"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<script language="javascript">	
		function form_sumbit(){
			// REVERT BACK
			$('bill_type_div').className='fs_row_on';
			$('bill_type_div').className='fs_row_on';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.billings.edit.php?action=save_new" : "mgr.billings.edit.php?action=save_edit";

					if($_GET['edit'] == "new")
					{
						js_validate_field("bill_type","billings_f_method",0);
			?>
						var selected_value = $('bill_type').options[$('bill_type').selectedIndex].value;
						if(selected_value == 1)
						{
							<?php js_validate_field("permpurchaser","billings_f_member",0); ?>
							
							
						}
						if(selected_value == 2)
						{
							<?php js_validate_field("bmlmember","billings_f_member",0); ?>
						}
			<?php
						# CHECK FIELD AND OUTPUT MESSAGE
						//js_validate_field("bill_type","billings_f_method",0);
					}
					
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
			// LOAD THE INVOICE ITEMS
			<?php 
				if($_GET['edit'] == 'new' or $billing->bill_type == '1')
				{
					echo "load_invoice_items();";
					echo "\$('orders_div').hide();";
				}
				if($billing->bill_type == '2')
				{
					echo "\$('item_div').hide();";
					echo "load_bml_orders();";
				}
			?>			
			
			// FIX ROW COLORS
			update_fsrow('tab1_group');
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
		
		// LOAD THE INVOICE ITEMS WINDOW
		function load_invoice_items()
		{
			show_loader('invoice_items');
			var loadpage = "mgr.billings.actions.php?mode=list_invoice_items&bill_id=<?php echo $_GET['edit']; ?>&mem_id=" + $F('permpurchaser');
			var pars = "";
			var myAjax = new Ajax.Updater('invoice_items', loadpage, {evalScripts: true, method: 'get', parameters: pars, onComplete: function(){ updaterowcolors('.invoice_item_row','#fff','#f8f8f8'); } });
		}
		
		// LOAD THE BML ORDERS
		function load_bml_orders()
		{
			<?php
				if($_GET['edit'] == 'new')
				{
			?>
				var selected_value = $('bmlmember').options[$('bmlmember').selectedIndex].value;
			<?php
				}
				else
				{
					echo "var selected_value = $mgrMemberInfo->mem_id;";
				}
			?>
			
			if(selected_value == '0')
			{
				$('item_div').hide();
				$('orders_div').hide();
				// FIX ROW COLORS
				update_fsrow('tab1_group');
			}
			else
			{
				$('orders_div').show();
				
				// FIX ROW COLORS
				update_fsrow('tab1_group');
				
				show_loader('orders_items');
				
				var loadpage = "mgr.billings.actions.php?mode=bml_orders&bill_id=<?php echo $_GET['edit']; ?>&mem_id=" + <?php if($billing->bill_type == '2'){ echo $mgrMemberInfo->mem_id; } else { echo "selected_value"; } ?>;
				var pars = "";
				var myAjax = new Ajax.Updater('orders_items', loadpage, {evalScripts: true, method: 'get', parameters: pars, onComplete: function(){ updaterowcolors('.order_rows','#fff','#f8f8f8'); }});
			}
		}
		
		// BILL TYPE CHANGE DURING NEW BILL
		function bill_type_change()
		{
			var selected_value = $('bill_type').options[$('bill_type').selectedIndex].value;
			
			// ALLOW NEW ITEMS TO BE CREATED/ADDED
			
			switch(selected_value)
			{
				case "":
					$('item_div').hide();
					$('orders_div').hide();
					$('permpurchaser_div').hide();
					$('bmlmember_div').hide();
				break;				
				case "1":
					$('item_div').show();
					$('orders_div').hide();
					$('permpurchaser_div').show();
					$('bmlmember_div').hide();
				break;
				case "2":				
						$('permpurchaser_div').hide();
						$('bmlmember_div').show();
						$('item_div').hide();
						
						show_loader('bml_members');
						
						var loadpage = "mgr.billings.actions.php?mode=bml_members";
						var pars = "";
						var myAjax = new Ajax.Updater('bml_members', loadpage, {evalScripts: true, method: 'get', parameters: pars});
				break;
			}
			// FIX ROW COLORS
			update_fsrow('tab1_group');
		}
		
		// ADD INVOICE ITEM
		function add_invoice_item()
		{			
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{				
				$('items_div').show();
				
				// CREATE NEW
				var numrows = $$('.invoice_item_row').length;
				var rowname = $$('.invoice_item_row')[numrows-1].id;
				//alert(rowname);
				var url = 'mgr.billings.actions.php';
				var pars = 'mode=add_invoice_item&bill_id=<?php echo $_GET['edit']; ?>';
				var myAjax = new Ajax.Request( 
					url, 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true,
						onComplete: function(transport){					
							transport.responseText.evalScripts();					
							//alert(transport.responseText);
							//eval(transport.responseText);
							//var rowTemplate = new Template(templatedata);	
							//alert(rowname);	
							$(rowname).insert({after: transport.responseText});
							
							// FADE IN ROW THAT WAS JUST ADDED
							rowname = $$('.invoice_item_row')[numrows].id;							
							Effect.Appear(rowname,{ duration: 0.5, from: 0.0, to: 1.0 });
							setTimeout(function(){ updaterowcolors('.invoice_item_row','#fff','#f8f8f8'); },200);
						}
					});
			}
		}
		
		// DELETE INVOICE ITEM
		function delete_invoice_item(id)
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
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_invoice_item(\""+id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_invoice_item(id);";
					}
				?>
			}
		}
		
		// DO DELETE
		function do_delete_invoice_item(id)
		{
			var rowname = 'item_row_'+id;
			//show_loader(rowname);
			
			$('item_delete_'+id).src='images/mgr.loader.gif';
			
			//setTimeout(function(){
					var pars = 'mode=delete_invoice_item&delete='+id;
					new Ajax.Request('mgr.billings.actions.php', {method: 'get', parameters: pars, onComplete: function() {
						Effect.Fade('item_row_'+id,{ duration: 0.5 });
						setTimeout(function(){ $(rowname).remove(); updaterowcolors('.invoice_item_row','#fff','#f8f8f8'); },500);					
					} });
				//},100);
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
                <img src="./images/mgr.badge.invoice.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['billings_new_header'] : $mgrlang['billings_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['billings_new_message'] : $mgrlang['billings_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <?php
					if($_GET['edit'] != 'new')
					{
				?>
                    <div style="padding: 10px 20px 0 20px; margin-bottom: 15px; overflow: auto;">   
                        <div class="tg_header_info" style="height: 170px;">
                            <?php
                                $avatar_width2 = 100;
                            ?>
                            <div id="avatar_summary" style="float: left; background-image: url(images/mgr.loader.gif); background-repeat: no-repeat; background-position: center; min-height: 50px;">
                                <?php
                                    if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png"))
                                    {
                                        //echo "<img src='../assets/avatars/" . $order->mem_id . "_large.png" . "' style='border: 2px solid #ffffff; margin-right: 1px;' width='$avatar_width2' />";
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
                                            <div style="float: left; margin-right: 5px;"><a href='mgr.members.edit.php?edit=<?php echo $mgrMemberInfo->mem_id; ?>' class='editlink' onmouseover='start_mem_panel(<?php echo $mgrMemberInfo->mem_id; ?>);' onmouseout='cancel_mem_panel(<?php echo $mgrMemberInfo->mem_id; ?>);'><?php echo $mgrMemberInfo->f_name . " " . $mgrMemberInfo->l_name; ?></a></div>
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
                                        <td nowrap valign="top"><strong><?php echo $mgrlang['mem_signup_date']; ?>:</strong></td>
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
                        
                        <?php
						/*
                        <div class="tg_header_info" style="height: 150px;">
                            <table cellpadding="0" cellspacing="4">
                                <tr>
                                    <td nowrap="nowrap"><strong>Order Date: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap"><?php echo $ndate->showdate($mgrMemberInfo->order_date); ?></td>
                                </tr>
                                <tr>
                                    <td nowrap="nowrap"><strong>Order Number: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap"><?php echo $mgrMemberInfo->order_number; ?></td>
                                </tr>
                                
                                <tr>
                                    <td nowrap="nowrap"><strong>Status: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap">
                                    <?php
                                        switch($order->order_status)
                                        {
                                            case 0: // PENDING
                                                echo "<div class='mtag_good'>$mgrlang[gen_pending]</div>";
                                            break;
                                            case 1: // APPROVED
                                                echo "<div class='mtag_dblue'>$mgrlang[gen_approved]</div>";
                                            break;
                                            case 2: // INCOMPLETE                                                
                                                echo "<div class='mtag_grey'>$mgrlang[gen_incomplete]</div>";
                                            break;										
                                            case 3: // BILL LATER
                                                echo "<div class='mtag_grey'>$mgrlang[gen_cancelled]</div>";
                                            break;
                                            case 4: // FAILED/CANCELLED
                                                echo "<div class='mtag_bad'>$mgrlang[gen_failed]</div>";
                                            break;
                                        }									
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
						*/
						?>
                        <div class="tg_header_info" style="height: 170px; margin-right: 0;">
                            <table cellpadding="0" cellspacing="4">
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_invoice_number']; ?>:</strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><a href="../invoice.php?billID=<?php echo $billing->ubill_id ; ?>" target="_blank"><!-- popup invoice page --><?php echo $billing->invoice_number; ?></a></td>
                                </tr>                                                                
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['billings_bill_date']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><?php echo $ndate->showdate($billing->invoice_date); ?></td>
                                </tr>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_due_date']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><?php if((gmt_date() > $billing->due_date) and $billing->payment_status == '2'){ echo "<span style='color: #ad2013'>".$ndate->showdate($billing->due_date)."</span>"; } else { echo $ndate->showdate($billing->due_date); } ?></td>
                                </tr>
                                <?php
                                    if($billing->subtotal > 0)
                                    {
                                ?>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_subtotal']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><?php echo $cleanvalues->currency_display($billing->subtotal,1); ?></td>
                                </tr>
                                <?php
                                    }
                                    if($billing->taxa_cost > 0 or $billing->taxb_cost > 0 or $billing->taxc_cost > 0)
                                    {
                                        $tax_cost = $billing->taxa_cost + $billing->taxb_cost + $billing->taxc_cost;
                                ?>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_tax']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><?php echo $cleanvalues->currency_display($tax_cost,1); ?></td>
                                </tr>
                                <?php
                                    }
                                    if($billing->shipping_cost > 0)
                                    {
                                ?>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_shipping']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><?php echo $cleanvalues->currency_display($billing->shipping_cost,1); ?></td>
                                </tr>
                                <?php
                                    }
                                    if($billing->total > 0)
                                    {
                                ?>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_total']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right" style="border-top: 1px dashed #999; padding-top: 2px;"><span style="font-weight: bold; font-size: 14px; color: #333"><?php echo $cleanvalues->currency_display($billing->total,1); ?></span></td>
                                </tr>
                                <?php
                                    }
                                    if($billing->credits_total)
                                    {
                                ?>                            
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_credits']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap" align="right"><span style="font-weight: bold; font-size: 14px; color: #333"><?php echo $billing->credits_total; ?></span></td>
                                </tr>
                                <?php
                                    }
                                ?>
                                <tr>
                                    <td nowrap="nowrap"><strong><?php echo $mgrlang['gen_payment_status']; ?>: </strong></td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap">
                                    <?php
                                        switch($billing->payment_status)
                                        {
                                            case 0: // PENDING                                                
                                                echo "<div class='mtag_processing mtag' style='float: right;'>$mgrlang[gen_processing]</div>";
                                            break;
                                            case 1: // APPROVED
                                                echo "<div class='mtag_paid mtag' style='float: right;'>$mgrlang[gen_paid]</div>";
                                            break;
                                            case 2: // INCOMPLETE/NONE
                                                echo "<div class='mtag_unpaid mtag' style='float: right;'>$mgrlang[gen_unpaid]</div>";
                                            break;
                                            case 4: // FAILED
                                                echo "<div class='mtag_failed mtag' style='float: right;'>$mgrlang[gen_failed]</div>";
                                            break;
                                            case 5: // REFUNDED
                                                echo "<div class='mtag_refunded mtag' style='float: right;'>$mgrlang[gen_refunded]</div>";
                                            break;
											case 6: // CANCELLED
                                                echo "<div class='mtag_cancelled mtag' style='float: right;'>$mgrlang[gen_cancelled]</div>";
                                            break;
                                        }
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
				<?php
					}
					# PULL GROUPS
					$bill_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$bill_group_rows = mysqli_num_rows($bill_group_result);
				?>   
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['gen_details']; ?></div>
                    <!--<?php if($_GET['edit'] != 'new'){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2" <?php if($_GET['edit'] == 'new' and !$bill_group_rows){ echo "style='border-right: 1px solid #d8d7d7;'"; } ?>>Transactions</div><?php } ?>-->
                    <?php if($bill_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_b_grps']; ?></div><?php } ?>                    
                    <!--<?php if($_GET['edit'] != 'new'){ ?><div class="subsuboff" onclick="bringtofront('4');" id="tab4" style="border-right: 1px solid #d8d7d7;">Advanced</div><?php } ?>-->
               	</div>
				
				<?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">					
					<?php 
						if($_GET['edit'] != "new")
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="tag_div" fsrow="1">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['gen_invoice_number']; ?>:<br />
                            <span class="input_label_subtext"><?php echo $mgrlang['billings_invnum_bill']; ?></span></p>
                            <span style="font-size: 14px; font-weight: bold; color: #666;"><?php echo $billing->invoice_number; ?></span>
                            <input type="hidden" value="<?php echo $billing->invoice_id; ?>" name="invoice_id"  />
                        </div>
                    <?php
						}
					?>
                    <?php
						if($_GET['edit'] == 'new')
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="bill_type_div" fsrow="1">
                            <img src="images/mgr.ast.gif" class="ast" />
                            <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_f_method']; ?>:<br />
                            <span class="input_label_subtext"><?php echo $mgrlang['billings_f_method_d']; ?></span></p>
                            <select style="width: 340px;" name="bill_type" id="bill_type" onchange="bill_type_change();">
                                <option value=""></option>
                                <option value="1"><?php echo $mgrlang['createBillScratch']; ?></option>
                                <?php if(in_array("pro",$installed_addons)){ ?><option value="2"><?php echo $mgrlang['createBillLater']; ?></option><?php } ?>
                            </select>
                        </div>
                    <?php
						}
						else
						{
							echo "<input type='hidden' value='$billing->bill_type' name='bill_type'  />";	
						}
					?>
                    <div class="<?php fs_row_color(); ?>" id="permpurchaser_div" <?php if($_GET['edit'] == 'new'){ echo "style='display: none;'"; } ?> fsrow="1">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['billings_f_member']; ?>:<br />
                            <span><?php echo $mgrlang['billings_f_member_d']; ?></span>
                        </p>
                        <div style="float: left; padding-right: 10px;" id="owner_name_div">
							<?php
								if($_GET['edit'] == 'new')
								{
									echo "{$mgrlang[please_choose_mem]}";
								}
								else
								{
									echo "<strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong> (<a href='mailto:$mgrMemberInfo->email'>$mgrMemberInfo->email</a>)";
								}
                            ?>
                        </div>
                        <?php if($_GET['edit'] == 'new'){ ?><div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=member_selector&header=members&style=membersonly&inputbox=permpurchaser&multiple=0&updatenamearea=owner_name_div'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon_owner" align="middle" /></a></div><?php } ?>
                        <input type="hidden" value="<?php echo $billing->member_id; ?>" name="permpurchaser" id="permpurchaser" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="bmlmember_div" style='display: none;' fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_for_mem']; ?>:<br />
                        <span class="input_label_subtext"></span></p>
                        <div style="float: left; width: 600px" id="bml_members">mem list</div>                   
                    </div>                  
                    <div class="<?php fs_row_color(); ?>" id="item_div" style='display: <?php if($_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; } ?>;' fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['gen_items2']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['billings_f_items_d']; ?></span></p>
                        <div style="float: left; width: 600px" id="invoice_items"></div>                   
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="orders_div" style='display: <?php if($_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; } ?>;' fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_f_orders']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['billings_f_orders_d']; ?></span></p>
                        <div style="float: left; width: 600px" id="orders_items" class="fs_row_part2"></div>                   
                    </div>
                    <?php
						/*
						<div class="<?php fs_row_color(); ?>" id="tag_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');">Tax:<br />
							<span class="input_label_subtext">Taxes added to this bill. Leave blank for none.</span></p>
							<div style="float: left;">
								tax<br />
								<input type="text" name="tax_a" id="tax_a" style="width: 100px;" /><br /><br />
								tax<br />
								<input type="text" name="tax_b" id="tax_b" style="width: 100px;" /><br /><br />
								tax<br />
								<input type="text" name="tax_c" id="tax_c" style="width: 100px;" /><br /><br />
							</div>
						</div>
						
						<div class="<?php fs_row_color(); ?>" id="tag_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');">Shipping:<br />
							<span class="input_label_subtext">Shipping cost added to this bill. Leave blank for none.</span></p>
							<input type="text" name="shipping" id="shipping" style="width: 100px;" />
						</div>
						*/
					?>
                    <div class="<?php fs_row_color(); ?>" id="tag_div" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_f_status']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['billings_f_status_d']; ?></span></p>
                        <input type="hidden" name="originalPaymentStatus" value="<?php echo $billing->payment_status; ?>" />
						<select name="payment_status" style="margin-top: 2px;">
                            <option value="1" <?php if($billing->payment_status == 1){ echo "selected"; } ?> ><?php echo $mgrlang['gen_paid']; ?></option>
                            <option value="2" <?php if($billing->payment_status == 2 or $_GET['edit'] == 'new'){ echo "selected"; } ?>><?php echo $mgrlang['gen_unpaid']; ?></option>
                            <!--<option value="3" <?php if($billing->payment_status == 3){ echo "selected"; } ?>><?php echo $mgrlang['gen_bill']; ?></option>-->
                            <option value="4" <?php if($billing->payment_status == 4){ echo "selected"; } ?>><?php echo $mgrlang['gen_failed']; ?></option>
                            <option value="5" <?php if($billing->payment_status == 5){ echo "selected"; } ?>><?php echo $mgrlang['gen_refunded']; ?></option>
                            <option value="6" <?php if($billing->payment_status == 6){ echo "selected"; } ?>><?php echo $mgrlang['gen_cancelled']; ?></option>
                        </select>
                    </div>
                    <?php 
						$form_bill_date = $ndate->date_to_form($billing->invoice_date);
					?>
                    <div class="<?php fs_row_color(); ?>" id="tag_div" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_bill_date']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['billings_f_bill_date_d']; ?></span></p>
                        <select style="width: 50px;" name="bill_month">
							<?php
                                for($i=1; $i<13; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_bill_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 50px;" name="bill_day">
                            <?php
                                for($i=1; $i<=31; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_bill_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 65px;" name="bill_year">
                            <?php
                                for($i=2005; $i<(date("Y")+6); $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_bill_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <?php 
						$form_due_date = $ndate->date_to_form($billing->due_date);
					?>
                    <div class="<?php fs_row_color(); ?>" id="tag_div" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['billings_f_due_date']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['billings_f_due_date_d']; ?></span></p>
                        <select style="width: 50px;" name="due_month">
							<?php
                                for($i=1; $i<13; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_due_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 50px;" name="due_day">
                            <?php
                                for($i=1; $i<=31; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_due_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 65px;" name="due_year">
                            <?php
                                for($i=2005; $i<(date("Y")+6); $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_due_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group">
                    <div style="padding: 20px; font-weight: bold; font-size: 16px; color: #999">Area Coming Soon!</div>
                </div>
                                
                <?php
            	if($bill_group_rows)
				{
						$row_color = 0;
				?>
					<div id="tab3_group" class="group"> 
						<div class="<?php fs_row_color(); ?>" id="name_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['billings_f_groups']; ?>:<br />
								<span><?php echo $mgrlang['billings_f_groups_d']; ?></span>
							</p>
							<?php
								$plangroups = array();
								# FIND THE GROUPS THAT THIS ITEM IS IN
								$bill_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$billing->bill_id' AND item_id != 0");
								while($bill_groupids = mysqli_fetch_object($bill_groupids_result))
								{
									$plangroups[] = $bill_groupids->group_id;
								}
								echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
								while($bill_group = mysqli_fetch_object($bill_group_result))
								{
									echo "<li><input type='checkbox' id='grp_$bill_group->gr_id' class='permcheckbox' name='setgroups[]' value='$bill_group->gr_id' "; if(in_array($bill_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($bill_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$bill_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$bill_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$bill_group->gr_id'>" . substr($bill_group->name,0,30)."</label></li>";
								}
								echo "</ul>";
							?>
						</div>
					</div>
				<?php
					}
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
                    <div style="padding: 20px; font-weight: bold; font-size: 16px; color: #999">Area Coming Soon! [todo]</div>
					<!--
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');">Link To Bill:<br />
                        <span class="input_label_subtext"></span></p>                   
                    </div>
					-->
                </div>
                                
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.billings.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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