<?php
	###################################################################
	####	BILLINGS ACTIONS      	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-18-2010                                    ####
	####	Modified: 11-18-2010                                   #### 
	###################################################################

		# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');							
	
		$page = "billings";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		function item_row($id,$haf=0)
		{
			global $config, $db, $dbinfo, $active_langs, $mgrlang, $cleanvalues;
			
			$item_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}invoice_items WHERE oi_id = '$id'");
			$item = mysqli_fetch_object($item_result);
			
			if($item->taxed)
			{
				$checked="checked='checked'";
			}
			else
			{
				$checked='';
			}
			
			$rowdis = ($haf) ? "style='display: none;'" : "";
			
			echo "
				<tr class='invoice_item_row' id='item_row_$id' $rowdis>
					<td><input type='hidden' name='item_id[]' value='$id' /><input type='text' name='item_desciption[]' id='item_description_$id' value='$item->description' style='width: 330px;' /></td>
					<td align='center'><input type='text' name='item_cost[]' id='item_cost_$id' style='width: 80px;' onblur=\"update_input_cur('item_cost_$id');\" value='".$cleanvalues->currency_display($item->price_total)."' /></td>
					<!--<td align='center' valign='middle'><input type='checkbox' name='item_taxed[]' id='item_taxed_$id' style='padding: 0; margin: 0;' value='1' $checked /></td>-->
					<td align='center' valign='middle'><a href='javascript:delete_invoice_item($id)' class='actionlink'><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' id='item_delete_$id' width='14' />".$mgrlang['gen_short_delete']."</a></td>
				</tr>
				";
		}				
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			# NO MODE
			default:			
				exit;
			break;
			# SET PAYMENT STATUS
			case "payment_status":
				//echo "test"; exit;
				$billResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}invoices LEFT JOIN {$dbinfo[pre]}billings ON {$dbinfo[pre]}invoices.bill_id = {$dbinfo[pre]}billings.bill_id WHERE {$dbinfo[pre]}invoices.bill_id = '$_REQUEST[id]'");
				$bill = mysqli_fetch_array($billResult);

				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:					
					case 0:
						$save_type = $mgrlang['gen_processing'];
						$mtag = 'mtag_processing';
					break;
					case 1:
						$save_type = $mgrlang['gen_paid'];
						$mtag = 'mtag_paid';
						
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
						
					break;
					case 2:
						$save_type = $mgrlang['gen_unpaid'];
						$mtag = 'mtag_unpaid';
					break;
					case 4:
						$save_type = $mgrlang['gen_failed'];
						$mtag = 'mtag_failed';
					break;
					case 5:
						$save_type = $mgrlang['gen_refunded'];
						$mtag = 'mtag_refunded';
					break;
					case 6:
						$save_type = $mgrlang['gen_cancelled'];
						$mtag = 'mtag_cancelled';
					break;
				}
				
				echo "<div class='{$mtag} mtag' onmouseover=\"show_bill_sp('billstatus_sp_$_REQUEST[id]');write_bill_status('billstatus','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
				
				# UPDATE ORDERS TO PAID
				if($bill['bill_type'] == 2)
				{				
					# IF THE VALUE IS 1 SET THEM TO PAID
					if($new_value == 1)
					{
						#
						//$order_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}invoices.bill_id = '$bill_id'");
						//$order = mysqli_fetch_object($order_result);
					}
					else
					{
						
						
					}
				}
				/*
				if($_REQUEST['newstatus'] == 0 or $_REQUEST['newstatus'] == 1)
				{
					$currencyID = $config['settings']['defaultcur'];
					$sql = "UPDATE {$dbinfo[pre]}billings SET currency_id='{$currencyID}',exchange_rate='1' WHERE bill_id = '$_REQUEST[id]'";
					$result = mysqli_query($db,$sql);
				}
				*/
				
				$sql = "UPDATE {$dbinfo[pre]}invoices SET payment_status='$_REQUEST[newstatus]' WHERE bill_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_billings'],1,$save_type . " > <strong>{$invoice[invoice_number]} ({$_REQUEST[id]})</strong>");
			break;
			
			case "list_invoice_items":
				
				$bill_type = $_GET['bill_type'];
				$bill_id = $_GET['bill_id'];
				$mem_id = $_GET['mem_id'];
				
				$display = ($bill_id == 'new') ? 'none' : 'block';
				
				echo "<a href='javascript:add_invoice_item();' class='actionlink'><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' />".$mgrlang['gen_add_new']."</a><br /><br /><div class='fs_row_part2' id='items_div' style='margin: 0; padding: 0; width: 100%; display: $display'>
						<table width='100%'>
							<tr>
								<th align='left'>{$mgrlang[gen_desc_caps]}</th>
								<th>{$mgrlang[gen_cost_caps]}</th>
								<th>&nbsp;</th>
							</tr>
							<tr class='invoice_item_row' id='blank_row' style='display: none'><td colspan='4'></td></tr>";
				if($bill_id == 'new')
				{
					//item_row('new');
					echo "<script>$('items_div').hide();</script>";
				}
				else
				{
					$invoice_result = mysqli_query($db,"SELECT invoice_id FROM {$dbinfo[pre]}invoices WHERE bill_id = '$bill_id'");
					$invoice = mysqli_fetch_object($invoice_result);

					$invitem_result = mysqli_query($db,"SELECT oi_id FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '$invoice->invoice_id'");
					$invitem_rows = mysqli_num_rows($invitem_result);
					while($invitem = mysqli_fetch_object($invitem_result))
					{
						item_row($invitem->oi_id);
					}
				}
				echo "</table></div>";
			break;
			
			case "add_invoice_item":
				$bill_id = $_GET['bill_id']; 
				if($_GET['bill_id'] == 'new')
				{
					$invoice_id = '0';
				}
				else
				{
					$invoice_result = mysqli_query($db,"SELECT invoice_id FROM {$dbinfo[pre]}invoices WHERE bill_id = '$bill_id'");
					$invoice = mysqli_fetch_object($invoice_result);
					
					$invoice_id = $invoice->invoice_id;
				}
				
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}invoice_items (invoice_id,item_added) VALUES ('$invoice_id','".gmt_date()."')");
				$saveid = mysqli_insert_id($db);
				
				item_row($saveid,1);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_bill_item'],1,$mgrlang['gen_b_new'] . " > <strong>($saveid)</strong>");
			break;
			
			case "delete_invoice_item":
			
				# DELETE ALL PREVIOUS UNASSIGNED INVOICE ITEMS
				$sql="DELETE FROM {$dbinfo[pre]}invoice_items WHERE oi_id = '$_GET[delete]'";
				$result = mysqli_query($db,$sql);
			
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_bill_item'],1,$mgrlang['gen_b_del'] . " > <strong>($_GET[delete])</strong>");
			break;	
			
			case "bml_members":
				$invoice_result = mysqli_query($db,"SELECT invoice_id,invoice_mem_id FROM {$dbinfo[pre]}invoices WHERE order_id != '0' AND payment_status = '3' AND deleted = '0' GROUP BY invoice_mem_id");
				$invoice_rows = mysqli_num_rows($invoice_result);
				
				if($invoice_rows)
				{
					echo "<select style='width: 340px;' name='bmlmember' id='bmlmember' onchange='load_bml_orders();'>";
					echo "<option value=''></option>";
					while($invoice = mysqli_fetch_object($invoice_result))
					{
						if($invoice->invoice_mem_id)
						{
							$member_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '$invoice->invoice_mem_id'");
							$mgrMemberInfo = mysqli_fetch_object($member_result);
						
							echo "<option value='$mgrMemberInfo->mem_id'>{$mgrMemberInfo->f_name} {$mgrMemberInfo->l_name} ($mgrMemberInfo->email)</option>";
						}
					}
					echo "</select>";
				}
				else
				{
					echo $mgrlang['billings_no_bill_later'];
				}
			break;
			
			case "bml_orders":
				$bill_id = $_GET['bill_id'];
				$mem_id = $_GET['mem_id'];
				//echo "bill $bill_id / $mem_id";
				
				$ndate = new kdate;
				$ndate->distime = 0;
				
				if($bill_id == 'new')
				{
					$orders_result = mysqli_query($db,"SELECT *,{$dbinfo[pre]}orders.bill_id AS ordbill_id FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}invoices.order_id = {$dbinfo[pre]}orders.order_id WHERE {$dbinfo[pre]}orders.member_id = '$mem_id' AND {$dbinfo[pre]}invoices.payment_status = '3' AND {$dbinfo[pre]}orders.deleted = 0 ORDER BY order_date");
					$order_rows = mysqli_num_rows($orders_result);
				}
				else
				{
					$orders_result = mysqli_query($db,"SELECT *,{$dbinfo[pre]}orders.bill_id AS ordbill_id FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}invoices.order_id = {$dbinfo[pre]}orders.order_id WHERE {$dbinfo[pre]}orders.member_id = '$mem_id' AND ({$dbinfo[pre]}invoices.payment_status = '3' OR {$dbinfo[pre]}orders.bill_id = '$bill_id') AND {$dbinfo[pre]}orders.deleted = 0 ORDER BY order_date");
					$order_rows = mysqli_num_rows($orders_result);
				}
				
				if($order_rows)
				{
					echo "<table width='100%'>
							<tr>
								<th align='left'>{$mgrlang[gen_order_num_caps]}</th>
								<th align='left'>{$mgrlang[gen_order_date_caps]}</th>
								<th>{$mgrlang[gen_in_bill_caps]}</th>
							</tr>";
					while($orders = mysqli_fetch_object($orders_result))
					{
						echo "<tr class='order_rows'>";
							echo "<td><a href='mgr.orders.edit.php?edit=$orders->order_id'>$orders->order_number</a></td>";
							echo "<td>".$ndate->showdate($orders->order_date)."</td>";
							echo "<td align='center'><input type='checkbox' value='$orders->order_id' name='orders[]' class='orders_checkboxes' style='margin: 0;'";
							
							if($orders->ordbill_id == $bill_id)
							{
								echo "checked='checked'";
							}
							
							if($orders->ordbill_id != $bill_id and $orders->ordbill_id != '0')
							{
								echo "disabled='disabled'";
							}
							
							echo "/></td>";
						echo "</tr>";
					}
					echo "</table>";
				}
				else
				{
					echo $mgrlang['error'];	
				}
				
			break;
		}	
?>
