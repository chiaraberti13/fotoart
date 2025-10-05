<?php
	###################################################################
	####	ORDERS ACTIONS      	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-18-2010                                    ####
	####	Modified: 11-18-2010                                   #### 
	###################################################################
		
		//sleep(3);
		# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');							
	
		$page = "orders";
		
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
		
		# INCLUDE INVOICE TOOLS CLASS FILE
		require_once('../assets/classes/invoicetools.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		$nowGMT = gmdate("Y-m-d H:m:s");
		
		# ACTIONS
		switch($_REQUEST['mode'])
		{
			# SET ORDER STATUS
			default:			
			case "order_status":
				
				$invoice = new invoiceTools; // New invoiceTools object
				
				//$order_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders where order_id = '$_REQUEST[id]'");
				//$order = mysqli_fetch_assoc($order_result);
				
				$order = $invoice->getOrderDetails($_REQUEST['id']);
				
				//test($order,'orderInfo'); exit;
				
				//BUILD SMARTY VARIABLES FOR EMAIL
				$order['orderLink'] = "{$config[settings][site_url]}/order.details.php?orderID={$order[uorder_id]}";
				$smarty->assign('order',$order);
				
				$processedCreditsUsed = $order['processed_credits_used'];
				$processedCreditsGained = $order['processed_credits_gained'];
				$processedQuantities = $order['processed_quantities'];
				$processedCoupons = $order['processed_coupons'];
				$processedSubs = $order['processed_subs'];
				
				//$invoiceResult = mysqli_query($db,"SELECT invoice_number,invoice_id,ship_email FROM {$dbinfo[pre]}invoices WHERE order_id = '$_REQUEST[id]'");
				//$invoice = mysqli_fetch_assoc($invoiceResult);
				
				$invoiceDetails = $invoice->getInvoiceDetailsViaOrderDBID($order['order_id']);
				
				//test('b','step'); exit;
				
				//$invoiceItemRows = $invoice->queryInvoiceItems($invoiceDetails['invoice_id']); // Fetch the invoice items details
				//$invoiceItems = $invoice->getAllInvoiceItems(); // Get the invoice items // getInvoiceItemsRaw
				
				if($order['member_id'])
				{
					$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$order[member_id]}'");
					$member = mysqli_fetch_assoc($memberResult); // Get member details
					$smarty->assign('member',$member);
					$memberCreditsAvailable = $member['credits']; // The number of credits a member initiall has avaiable
				}
				
				if(!$order['order_number'])
				{
					if($config['settings']['order_num_type'] == 1) // Get sequential number
					{
						$orderNumber = $config['settings']['order_num_next'];
						$nextOrderNumber = $orderNumber+1;		
						mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET order_num_next='{$nextOrderNumber}' WHERE settings_id = 1"); // update settings db with next number		
					}
					else // Get random order number
						$orderNumber = create_order_number();
				}
				else
					$orderNumber = $order['order_number'];
					
				$potentialInvoiceNumber = $config['settings']['invoice_prefix'].$config['settings']['invoice_next'].$config['settings']['invoice_suffix'];
				
				if($_REQUEST['newstatus'] == 1 and !$invoiceDetails['invoice_number'])
				{
					$sql = "UPDATE {$dbinfo[pre]}invoices SET invoice_number='{$potentialInvoiceNumber}' WHERE order_id = '$_REQUEST[id]'";
					$result = mysqli_query($db,$sql);
					
					$nextInvoiceNumber = $config['settings']['invoice_next']+1;
					mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET invoice_next='{$nextInvoiceNumber}' WHERE settings_id = 1"); // Update sequential invoice number in settings db
				}
				
				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:
					case 1:
						$save_type = $mgrlang['gen_approved'];
						$mtag = 'mtag_approved';
					break;
					case 2:
						$save_type = $mgrlang['gen_incomplete'];
						$mtag = 'mtag_incomplete';
					break;
					case 3:
						$save_type = $mgrlang['gen_cancelled'];
						$mtag = 'mtag_cancelled';
					break;
					case 4:
						$save_type = $mgrlang['gen_failed'];
						$mtag = 'mtag_failed';
					break;
					case 0:
						$save_type = $mgrlang['gen_pending'];
						$mtag = 'mtag_pending';
					break;
				}
				
				if($_REQUEST['newstatus'] == 1) // Order set to approved
				{	
					//$invoiceItemRows = $invoice->queryInvoiceItems($invoiceDetails['invoice_id']); // Fetch the invoice items details
					//test($invoiceItemRows,'invrows'); exit;
					//$invoiceItems = $invoice->getAllInvoiceItems(); // Get the invoice items // getInvoiceItemsRaw
					//test($invoiceItems,'invitems'); exit;
					
					$invoiceItemsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '{$invoiceDetails[invoice_id]}'"); // Fetch the invoice items details
					$rowTest = mysqli_num_rows($invoiceItemsResult);
					while($invoiceItem = mysqli_fetch_assoc($invoiceItemsResult))
					{					
						switch($invoiceItem['item_type'])
						{
							default:
							break;							
							case 'credits':
								$creditResult = mysqli_query($db,"SELECT credits FROM {$dbinfo[pre]}credits WHERE credit_id = '{$invoiceItem[item_id]}'"); // Fetch the credits details
								$credits = mysqli_fetch_assoc($creditResult);
		
								// Add credits from order
								$orderCreditsAvailable+=($credits['credits']*$invoiceItem['quantity']);
								$creditsPurchasedInOrder = true;	
							break;
							case 'print':
								if(!$order['processed_quantities'])
								{
									$printResult = mysqli_query($db,"SELECT mp_id,quantity FROM {$dbinfo[pre]}media_prints WHERE print_id = '{$invoiceItem[item_id]}' AND media_id = '{$invoiceItem[asset_id]}'"); // Fetch the print details
									$print = mysqli_fetch_assoc($printResult);
									
									if($print['quantity'])
									{
										$newPrintQuantity = $print['quantity'] - 1; // Subtract one from the quantity
										mysqli_query($db,"UPDATE {$dbinfo[pre]}media_prints SET quantity='{$newPrintQuantity}' WHERE mp_id = '{$print[mp_id]}'"); // adjust the quantity
									}
								}
							break;							
							case 'product':
								if(!$order['processed_quantities'])
								{
									$productResult = mysqli_query($db,"SELECT prod_id,product_type FROM {$dbinfo[pre]}products WHERE prod_id = '{$invoiceItem[item_id]}'"); // Fetch the print details
									$product = mysqli_fetch_assoc($productResult);
									
									if(product_type == 1) // Media based
									{
										$mediaProdResult = mysqli_query($db,"SELECT mp_id,quantity FROM {$dbinfo[pre]}media_products WHERE prod_id = '{$invoiceItem[item_id]}' AND media_id = '{$invoiceItem[asset_id]}'"); // Fetch the print details
										$mediaProd = mysqli_fetch_assoc($mediaProdResult);
										
										if($mediaProd['quantity'])
										{
											$newMediaProdQuantity = $print['quantity'] - 1; // Subtract one from the quantity
											mysqli_query($db,"UPDATE {$dbinfo[pre]}media_products SET quantity='{$newMediaProdQuantity}' WHERE mp_id = '{$mediaProd[mp_id]}'"); // adjust the quantity
										}
									}
									else // Stand alone
									{
										if($product['quantity'])
										{
											$newProductQuantity = $product['quantity'] - 1; // Subtract one from the quantity
											mysqli_query($db,"UPDATE {$dbinfo[pre]}products SET quantity='{$newProductQuantity}' WHERE prod_id = '{$product[prod_id]}'"); // adjust the quantity
										}
									}
								}
							break;
							case 'package':
								if(!$order['processed_quantities'])
								{
									$packageResult = mysqli_query($db,"SELECT pack_id,quantity FROM {$dbinfo[pre]}packages WHERE pack_id = '{$invoiceItem[item_id]}'"); // Fetch the package details
									$package = mysqli_fetch_assoc($packageResult);
									if($package['quantity'])
									{
										$newPackageQuantity = $package['quantity'] - 1; // Subtract one from the quantity
										mysqli_query($db,"UPDATE {$dbinfo[pre]}packages SET quantity='{$newPackageQuantity}' WHERE pack_id = '{$package[pack_id]}'"); // adjust the quantity
									}
								}
							break;						
							case 'subscription':
								if(!$order['processed_subs'])
								{
									$subscriptionResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}subscriptions WHERE sub_id = '{$invoiceItem[item_id]}'"); // Fetch the subscription details
									$subscription = mysqli_fetch_assoc($subscriptionResult);
									$nowGMT = gmt_date();
									$futureDate = date("Y-m-d H:m:s",strtotime("{$nowGMT} +{$subscription[durvalue]} {$subscription[durrange]}")); // Date at which it expires 
									mysqli_query($db,"INSERT INTO {$dbinfo[pre]}memsubs (mem_id,sub_id,expires,start_date,perday,status,total_downloads) VALUES ('{$member[mem_id]}','{$subscription[sub_id]}','{$futureDate}','{$nowGMT}','{$subscription[downloads]}',1,'{$subscription[tdownloads]}')"); // Enter subscription into db
									//mysqli_query($db,"INSERT INTO {$dbinfo[pre]}memsubs (mem_id,sub_id,expires,perday,status) VALUES ('{$member[mem_id]}','{$subscription[sub_id]}','{$futureDate}','{$subscription[downloads]}',1)"); // Enter subscription into db
									$processedSubs = 1; // Set status of processed subs to 1
								}
							break;
							case 'digital':
								if(!$order['processed_quantities'])
								{
									if($invoiceItem['item_id']) // Digital size
									{
										$digitalResult = mysqli_query($db,"SELECT mds_id,quantity FROM {$dbinfo[pre]}media_digital_sizes WHERE ds_id = '{$invoiceItem[item_id]}' AND media_id = '{$invoiceItem[asset_id]}'"); // Fetch the digital details
										$digital = mysqli_fetch_assoc($digitalResult);
										if($digital['quantity'])
										{
											$newDigitalQuantity = $digital['quantity'] - 1; // Subtract one from the quantity
											mysqli_query($db,"UPDATE {$dbinfo[pre]}media_digital_sizes SET quantity='{$newDigitalQuantity}' WHERE mds_id = '{$digital[mds_id]}'"); // adjust the quantity
										}	
									}
									else // Original
									{
										$digitalResult = mysqli_query($db,"SELECT media_id,quantity FROM {$dbinfo[pre]}media WHERE media_id = '{$invoiceItem[asset_id]}'"); // Fetch the package details
										$digital = mysqli_fetch_assoc($digitalResult);
										if($digital['quantity'])
										{
											$newDigitalQuantity = $digital['quantity'] - 1; // Subtract one from the quantity
											mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET quantity='{$newDigitalQuantity}' WHERE media_id = '{$digital[media_id]}'"); // adjust the quantity
										}
									}
									
									if($config['settings']['expire_download']) // Only if its other than 0 (never)
									{
										$expireDays = $config['settings']['expire_download']; // Days until the download expires
										$futureDate = date("Y-m-d H:m:s",strtotime("{$nowGMT} +{$expireDays} days")); // Date at which it expires 									
										mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET expires='{$futureDate}' WHERE oi_id = '{$invoiceItem[oi_id]}'"); // Mark download dates in the future
									}
								}
							break;
							case 'collection':
								if(!$order['processed_quantities'])
								{
									if($config['settings']['expire_download']) // Only if its other than 0 (never)
									{
										$expireDays = $config['settings']['expire_download']; // Days until the download expires
										$futureDate = date("Y-m-d H:m:s",strtotime("{$nowGMT} +{$expireDays} days")); // Date at which it expires 									
										mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET expires='{$futureDate}' WHERE oi_id = '{$invoiceItem[oi_id]}'"); // Mark download dates in the future
									}	
								}
							break;
							
						}
						
						if($invoiceItem['credits_total'] and $invoiceItem['paytype'] == 'cred') // See if credits were used to purchase this item
						{
							//echo $invoiceItem['credits_total']; exit; // Testing						
							$creditsUsedInOrder = true;
							$runningUsedCredits+=$invoiceItem['credits_total']; // Running total of credits used
						}
						
						$processedQuantities = 1; // Quantities were processed
						
						mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET status='1' WHERE oi_id = '{$invoiceItem[oi_id]}'"); // Set the status of the invoice item to 1
						@mysqli_query($db,"UPDATE {$dbinfo[pre]}commission SET order_status='1' WHERE oitem_id = '{$invoiceItem[oi_id]}'"); // Set the status of the commission to 1
						
						//$invoiceItems[] = $invoiceItem;
						//test($invoiceItems,'invitems');
					}
					
					if(!$order['processed_credits_used'] and $order['member_id']) // Credits used never processed - do that below - only run if order was placed by member
					{
						//echo $runningUsedCredits; exit; // Testing
						$memberCreditsRemaining = $memberCreditsAvailable - $runningUsedCredits;
						$processedCreditsUsed = 1; // Set the status of credits used to 1
						mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET credits='{$memberCreditsRemaining}' WHERE mem_id = '{$member[mem_id]}'"); // Update member account with new credits
					}
					else
						$memberCreditsRemaining = $memberCreditsAvailable; // No credits were used in this order so set the credits remaining to the initial available
					
					
					
					if(!$order['processed_credits_gained'] and $order['member_id']) // Credits gained never processed - do that below - only run if order was placed by member
					{
						$memberCreditsRemaining = $memberCreditsRemaining+$orderCreditsAvailable;
						$processedCreditsGained = 1; // Set the status of credits gained to 1
						mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET credits='{$memberCreditsRemaining}' WHERE mem_id = '{$member[mem_id]}'"); // Update member account with new credits
					}
					
					if(!$order['processed_quantities']) // Quantities never adjusted - done above
					{
						
					}
					
					if(!$order['processed_coupons']) // Used coupons never processed - do that below
					{
						if($invoiceDetails['discount_ids_used']) // Update coupons used, etc - regardless if the order was approved or not
						{
							$couponIDs = explode(',',$invoiceDetails['discount_ids_used']); // Split the ids from the db					
							foreach($couponIDs as $key => $id)
							{
								$couponsResult = mysqli_query($db,"SELECT promo_id,quantity,oneuse FROM {$dbinfo[pre]}promotions WHERE promo_id = '{$id}'"); // Fetch the coupon/promo details
								$coupon = mysqli_fetch_assoc($creditsResult);
								
								if($coupon['quantity'] > 0) // Update the quantity if it is greater than 0
								{
									$newQuantity = $coupon['quantity'] - 1;								
									mysqli_query($db,"UPDATE {$dbinfo[pre]}promotions SET quantity='{$newQuantity}' WHERE promo_id = '{$coupon[promo_id]}'"); // Update coupon/promo db with new quantity
								}
								
								if($coupon['oneuse']) // If this is a one time use then add the record to the usedcoupons db
									mysqli_query($db,"INSERT INTO {$dbinfo[pre]}usedcoupons (mem_id,promo_id,usedate) VALUES ('{$member[mem_id]}','{$coupon[promo_id]}','{$nowGMT}')"); // Update usage db
							}						
							$processedCoupons = 1; // Set status of processed coupons to 1
						}
					}
					
				}
				
				// If order status is approved and there is an email address to send to
				//test($mtag.'test','loaded5');
				
				if($invoiceDetails['ship_email'] && $_REQUEST['newstatus'] == 1)
				{					
					// Build email
					$toEmail = $invoiceDetails['ship_email'];
					
					// Check for valid email
					if(filter_var($toEmail, FILTER_VALIDATE_EMAIL))
					{
						$content = getDatabaseContent('orderApprovalMessage',$order['checkout_lang']); // Get content from db				
						//test($content);
						$content['name'] = $smarty->fetch('eval:'.$content['name']);
						$content['body'] = $smarty->fetch('eval:'.$content['body']);
						$options['replyEmail'] = $config['settings']['support_email'];
						$options['replyName'] = $config['settings']['business_name'];
						kmail($toEmail,$toEmail,$config['settings']['support_email'],$config['settings']['business_name'],$content['name'],$content['body'],$options); // Send email					
					}
					
					//test($config['settings']['support_email']);
				}
				echo "<div class='{$mtag} mtag' onmouseover=\"show_sp('order_sp_$_REQUEST[id]');write_status('orders','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
				//test('h');
				$test = mysqli_query($db,
				"
					UPDATE {$dbinfo[pre]}orders SET 
					order_status='$_REQUEST[newstatus]',
					order_number='{$orderNumber}',
					processed_credits_used='{$processedCreditsUsed}',
					processed_credits_gained='{$processedCreditsGained}',
					processed_quantities='{$processedQuantities}',
					processed_coupons='{$processedCoupons}',
					processed_subs='{$processedSubs}' 
					WHERE order_id = '$_REQUEST[id]'
				"); // Update order record
				
				/*
				echo "<script>";
					echo "alert('{$test}');";
				echo "</script>";
				exit;
				*/
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,$save_type . " > <strong>{$order[invoice_id]} ($_REQUEST[id])</strong>");
				
				# FIND OUT HOW MANY MORE ARE PENDING
				$_SESSION['pending_orders'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(order_id) FROM {$dbinfo[pre]}orders WHERE order_status = '0' AND deleted = 0"));
				
				
				
				# OUTPUT JS
				echo "<script>";
				# UPDATE THE BIO HEADER PENDING COUNT
				if($_SESSION['pending_orders'] > 0)
				{
					echo "\$('ph_status').show();";
					echo "\$('ph_status').update('$_SESSION[pending_orders]');";				
				}
				else
				{
					echo "\$('ph_status').hide();";
				}
				# UPDATE THE NAV ITEM PENDING COUNT
				if($_SESSION['pending_orders'] > 0)
				{
					echo "\$('hnp_orders').show();";
					echo "\$('hnp_orders').update('$_SESSION[pending_orders]');";
				}
				else
				{
					echo "\$('hnp_orders').hide();";
				}
				echo "</script>";
			break;
			# SET PAYMENT STATUS
			case "payment_status":
				$invoice_result = mysqli_query($db,"SELECT payment_status,order_id,invoice_number FROM {$dbinfo[pre]}invoices WHERE order_id = '$_REQUEST[id]'");
				$invoice = mysqli_fetch_object($invoice_result);
				
				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:
					case 1:
						$save_type = $mgrlang['gen_paid'];
						$mtag = 'mtag_paid';
					break;
					case 2:
						$save_type = $mgrlang['gen_unpaid'];
						$mtag = 'mtag_unpaid';
					break;
					case 3:
						$save_type = $mgrlang['gen_bill'];
						$mtag = 'mtag_bill';
					break;
					case 4:
						$save_type = $mgrlang['gen_failed'];
						$mtag = 'mtag_failed';
					break;
					case 5:
						$save_type = $mgrlang['gen_refunded'];
						$mtag = 'mtag_refunded';
					break;
					case 0:
						$save_type = $mgrlang['gen_processing'];
						$mtag = 'mtag_processing';
					break;
				}
				
				echo "<div class='$mtag mtag' onmouseover=\"show_sp('payment_sp_$_REQUEST[id]');write_status('payment','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";

				$sql = "UPDATE {$dbinfo[pre]}invoices SET payment_status='$_REQUEST[newstatus]' WHERE order_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,$save_type . " > <strong>$invoice->invoice_number ($_REQUEST[id])</strong>");
			break;
			# SET PAYMENT STATUS
			case "shipping_status":
				//echo "test"; exit;
				$invoice_result = mysqli_query($db,"SELECT shipping_status,order_id,invoice_number FROM {$dbinfo[pre]}invoices WHERE order_id = '$_REQUEST[id]'");
				$invoice = mysqli_fetch_object($invoice_result);
				
				# FLIP THE VALUE
				switch($_REQUEST['newstatus'])
				{
					default:
					case 0:
						$save_type = $mgrlang['gen_shipnone'];
						$mtag = 'mtag_shippingna';
					break;
					case 1:
						$save_type = $mgrlang['gen_shipped'];
						$mtag = 'mtag_shipped';
					break;
					case 2:
						$save_type = $mgrlang['gen_notshipped'];
						$mtag = 'mtag_notshipped';
					break;
					case 3:
						$save_type = $mgrlang['gen_pshipped'];
						$mtag = 'mtag_partshipped';
					break;
					case 4:
						$save_type = $mgrlang['gen_backordered'];
						$mtag = 'mtag_backordered';
					break;
				}
				
				echo "<div class='{$mtag} mtag' style='width: 100px;' onmouseover=\"show_sp('ship_sp_$_REQUEST[id]');write_status('shipping','$_REQUEST[id]',$_REQUEST[newstatus]);\">$save_type</div>";
							
				$sql = "UPDATE {$dbinfo[pre]}invoices SET shipping_status='$_REQUEST[newstatus]' WHERE order_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,$save_type . " > <strong>$invoice->invoice_id ($_REQUEST[id])</strong>");
			break;
			# RESET DOWNLOADS
			case "reset_downloads":
				$item_result = mysqli_query($db,"SELECT oi_id,expires,downloads FROM {$dbinfo[pre]}items WHERE oi_id = '$_REQUEST[id]'");
				$item = mysqli_fetch_object($item_result);
				
				# RESET DOWNLOAD COUNT TO 0
				$sql = "UPDATE {$dbinfo[pre]}invoice_items SET downloads='0' WHERE oi_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				if($config['settings']['dl_attempts'] == '0')
				{
					$config['settings']['dl_attempts'] = $mgrlang['webset_f_dl_unlimited'];
				}
				
				echo "<strong>0/" . $config['settings']['dl_attempts'] . "</strong>";
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,"{$mgrlang[order_reset_dls]} > <strong>($_REQUEST[id])</strong>");
				
			break;
			# RESET EXPIRATION
			case "reset_expire":
				
				$ndate = new kdate;
				$ndate->distime = 1;
				
				$item_result = mysqli_query($db,"SELECT oi_id,expires,downloads FROM {$dbinfo[pre]}items WHERE oi_id = '$_REQUEST[id]'");
				$item = mysqli_fetch_object($item_result);
				
				if($config['settings']['expire_download'] == '0')
				{
					$output_expire_date = $mgrlang['webset_time_never'];
					$db_expire_date = '0000-00-00 00:00:00';
				}
				else
				{
					$db_expire_date = date("Y-m-d H:i:00",strtotime(gmt_date()." +".$config['settings']['expire_download']." day"));
					$output_expire_date = $ndate->showdate($db_expire_date);
				}
				
				# RESET EXPIRATION TO CURRENT DATE + EXPIRE SETTING
				$sql = "UPDATE {$dbinfo[pre]}invoice_items SET expires='$db_expire_date' WHERE oi_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
								
				echo $output_expire_date;
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,"{$mgrlang[order_reset_exp]} > <strong>($_REQUEST[id])</strong>");
				
			break;
			# RESET EXPIRATION
			case "delete_item":
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}invoice_items WHERE oi_id = '$_REQUEST[id]'");
				
				# [todo] DELETE ANY OPTIONS FOR THIS ITEM
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,"{$mgrlang[order_del_oi]} > <strong>($_REQUEST[id])</strong>");
				
				echo "<script>$('itemrow_$_REQUEST[id]').hide();</script>";		
			break;
			# CHANGE ITEM SHIPPING STATUS
			case "update_iship_status":
				//$item_result = mysqli_query($db,"SELECT oi_id,shipping_status FROM {$dbinfo[pre]}items WHERE oi_id = '$_REQUEST[id]'");
				//$item = mysqli_fetch_object($item_result);
				
				# UPDATE SHIPPING STATUS OF A PRODUCT
				$sql = "UPDATE {$dbinfo[pre]}invoice_items SET shipping_status='$_REQUEST[status]' WHERE oi_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_orders'],1,"{$mgrlang[order_update_ss]} > <strong>($_REQUEST[id])</strong>");
			break;
			# INVOICE ITEM OPTIONS DISPLAY
			case "invoice_item_options":
				
				# MEDIATOOLS CLASS
				require_once('../assets/classes/mediatools.php');
				
				if($_GET['itemType'] == 'package') // Select package items and then options
				{
					$packageItemsResult = mysqli_query($db,
						"
						SELECT * 
						FROM {$dbinfo[pre]}invoice_items 
						WHERE pack_invoice_id = '{$_REQUEST[invoiceItemID]}'
						"
					);
					if($packageItemsRows = mysqli_num_rows($packageItemsResult))
					{	
						while($packageItem = mysqli_fetch_array($packageItemsResult))
						{
							switch($packageItem['item_type'])
							{
								case "product":
									// Select product name and details
									$productResult = mysqli_query($db,
										"
										SELECT * 
										FROM {$dbinfo[pre]}products 
										WHERE prod_id = '{$packageItem[item_id]}'
										"
									);
									$product = mysqli_fetch_array($productResult);
									if($productRows = mysqli_num_rows($productResult))
									{	
										if($packageItem['asset_id'])
										{
											try
											{
												$media = new mediaTools($packageItem['asset_id']);
												$mediaInfo = $media->getMediaInfoFromDB();
												$thumbInfo = $media->getIconInfoFromDB();										
												$verify = $media->verifyMediaSubFileExists('icons');										
												$mediaStatus = $verify['status'];
											}
											catch(Exception $e)
											{
												$mediaStatus = 0;
											}
											
											$product['thumb'] = $thumbInfo; // Get correct media thumbnail details
											$product['media'] = $mediaInfo; // Get correct media details
											$product['media']['status'] = $mediaStatus; // Set the media status
										}
										else
											$product['media'] = false; // No media assigned - show ? thumb
										
										
										$product['incQuantity'] = $packageItem['quantity'];
										$product['oi_id'] = $packageItem['oi_id']; 
										
										// Find any options
										$productOptionsResult = mysqli_query($db,
											"
											SELECT * 
											FROM {$dbinfo[pre]}invoice_options 
											LEFT JOIN {$dbinfo[pre]}options  
											ON {$dbinfo[pre]}invoice_options.option_id = {$dbinfo[pre]}options.op_id
											WHERE {$dbinfo[pre]}invoice_options.invoice_item_id = '{$product[oi_id]}'
											"
										);
										if($productOptionsRows = mysqli_num_rows($productOptionsResult))
										{
											unset($packageProductOptionsArray); // Clear the array for the next item
											while($productOption = mysqli_fetch_array($productOptionsResult))
											{
												
												if($optionGroupNames[$productOption['option_gid']])
												{
													$productOption['groupName'] = $optionGroupNames[$productOption['option_gid']];
												}
												else
												{
													$optionGroupResult = mysqli_query($db,
														"
														SELECT * 
														FROM {$dbinfo[pre]}option_grp 
														WHERE og_id = '{$productOption[option_gid]}'
														"
													);
													$optionGroup = mysqli_fetch_array($optionGroupResult);
													
													$optionGroupName = $optionGroup['name']; // [todo] Get correct language
													
													$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
													
													$productOption['groupName'] = $optionGroupName;
												}
												
												// [todo] get the correct language name
												$packageProductOptionsArray[] = $productOption;
											}
											
											$options = $packageProductOptionsArray;
										}
										else
											$options = false;
										
										//$packageProductsArray[] = productsList($product,$packageItem['asset_id']);
										//$packageProductsArray[$packageItem['oi_id']]['name'] = $product['item_name'];	
										//$packageProductsArray[$packageItem['oi_id']]['id'] = $product['prod_id'];
										
										$packageProductsArray[$packageItem['oi_id']] = $product;
										$packageProductsArray[$packageItem['oi_id']]['options'] = $options;
									}
								break;
								case "print":
									// Select print name and details
									$printResult = mysqli_query($db,
										"
										SELECT * 
										FROM {$dbinfo[pre]}prints 
										WHERE print_id = '{$packageItem[item_id]}'
										"
									);
									$print = mysqli_fetch_array($printResult);
									if($printRows = mysqli_num_rows($printResult))
									{	
										if($packageItem['asset_id'])
										{
											try
											{
												$media = new mediaTools($packageItem['asset_id']);
												$mediaInfo = $media->getMediaInfoFromDB();
												$thumbInfo = $media->getIconInfoFromDB();										
												$verify = $media->verifyMediaSubFileExists('icons');										
												$mediaStatus = $verify['status'];
											}
											catch(Exception $e)
											{
												$mediaStatus = 0;
											}
											
											$print['thumb'] = $thumbInfo; // Get correct media thumbnail details
											$print['media'] = $mediaInfo; // Get correct media details
											$print['media']['status'] = $mediaStatus; // Set the media status
										}
										else
											$print['media'] = false; // No media assigned - show ? thumb
										
										$print['incQuantity'] = $packageItem['quantity'];
										$print['oi_id'] = $packageItem['oi_id']; 
										
										// Find any options
										$printOptionsResult = mysqli_query($db,
											"
											SELECT * 
											FROM {$dbinfo[pre]}invoice_options 
											LEFT JOIN {$dbinfo[pre]}options  
											ON {$dbinfo[pre]}invoice_options.option_id = {$dbinfo[pre]}options.op_id
											WHERE {$dbinfo[pre]}invoice_options.invoice_item_id = '{$print[oi_id]}'
											"
										);
										if($printOptionsRows = mysqli_num_rows($printOptionsResult))
										{
											unset($packagePrintOptionsArray); // Clear the array for the next item
											while($printOption = mysqli_fetch_array($printOptionsResult))
											{
												if($optionGroupNames[$printOption['option_gid']])
												{
													$printOption['groupName'] = $optionGroupNames[$printOption['option_gid']];
												}
												else
												{
													$optionGroupResult = mysqli_query($db,
														"
														SELECT * 
														FROM {$dbinfo[pre]}option_grp 
														WHERE og_id = '{$printOption[option_gid]}'
														"
													);
													$optionGroup = mysqli_fetch_array($optionGroupResult);
													
													$optionGroupName = $optionGroup['name']; // [todo] Get correct language
													
													$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
													
													$printOption['groupName'] = $optionGroupName;
												}
												
												// [todo] get the correct language name
												$packagePrintOptionsArray[] = $printOption;
											}
											
											$options = $packagePrintOptionsArray;
										}
										else
											$options = false;
										
										//$packagePrintsArray[] = printsList($print,$packageItem['asset_id']);
										//$packagePrintsArray[$packageItem['oi_id']]['name'] = $print['item_name'];	
										//$packagePrintsArray[$packageItem['oi_id']]['id'] = $print['print_id'];										
										$packagePrintsArray[$packageItem['oi_id']] = $print;
										$packagePrintsArray[$packageItem['oi_id']]['options'] = $options;
									}
								break;
								case "collection":
									// Select collection name and details
									$collectionResult = mysqli_query($db,
										"
										SELECT * 
										FROM {$dbinfo[pre]}collections 
										WHERE coll_id = '{$packageItem[item_id]}'
										"
									);
									$collection = mysqli_fetch_array($collectionResult);
									if($collectionRows = mysqli_num_rows($collectionResult))
									{	
										$collection['incQuantity'] = $packageItem['quantity'];
										$collection['oi_id'] = $packageItem['oi_id']; 
										//$packageCollectionsArray[] = collectionsList($collection,$packageItem['asset_id']);
										
										$packageCollectionsArray[$packageItem['oi_id']] = $collection;
										
										//$packageCollectionsArray[$packageItem['oi_id']]['name'] = $collection['item_name'];	
										//$packageCollectionsArray[$packageItem['oi_id']]['id'] = $collection['coll_id'];
									}
								break;
								case "subscription":
									// Select collection name and details
									$subscriptionResult = mysqli_query($db,
										"
										SELECT * 
										FROM {$dbinfo[pre]}subscriptions 
										WHERE sub_id = '{$packageItem[item_id]}'
										"
									);
									$subscription = mysqli_fetch_array($subscriptionResult);
									if($subscriptionRows = mysqli_num_rows($subscriptionResult))
									{	
										$subscription['incQuantity'] = $packageItem['quantity'];
										$subscription['oi_id'] = $packageItem['oi_id']; 
										//$packageCollectionsArray[] = collectionsList($collection,$packageItem['asset_id']);
										
										$packageSubscriptionsArray[$packageItem['oi_id']] = $subscription;
										
										//$packageCollectionsArray[$packageItem['oi_id']]['name'] = $collection['item_name'];	
										//$packageCollectionsArray[$packageItem['oi_id']]['id'] = $collection['coll_id'];
									}
								break;
							}
							
						}
												
						$optionsArray['packagePrintsArray'] = $packagePrintsArray;
						$optionsArray['packageProductsArray'] = $packageProductsArray;
						$optionsArray['packageCollectionsArray'] = $packageCollectionsArray;
						$optionsArray['packageSubscriptionsArray'] = $packageSubscriptionsArray;
						$optionsArray['packageItemsRows'] = $packageItemsRows;
						
						//print_r($optionsArray['packagePrintsArray']);
						//echo count($optionsArray['packagePrintsArray']);
						
						
						
						if($optionsArray['packagePrintsArray'])
						{
							echo "<h1>{$mgrlang[gen_prints]}</h1>";
							echo "<div class='divTable'>";
							foreach($optionsArray['packagePrintsArray'] as $printKey => $print)
							{
								echo "<div class='divTableRow'>";
									echo "<div class='divTableCell'>";
									if($print['media'])
									{
										if($print['media']['status'])
											echo "<img src='mgr.media.preview.php?src={$print[thumb][thumb_filename]}&folder_id={$print[media][folder_id]}&width=50' class='mediaFrame' title='Media ID: {$print[media][media_id]}' />"; // print_r($print['media']);
										else
											echo "<img src='images/mgr.theme.blank.gif' style='width: 50px;' class='mediaFrame' />";
											
										//echo "<br />ID: <a href='mgr.media.php?dtype=search&ep=1&search={$packageItem[asset_id]}'>{$packageItem[asset_id]}</a>";
										//echo "<br />File: <a href='mgr.media.php?dtype=search&ep=1&search={$packageItem[asset_id]}'>{$packageItem[asset_id]}</a>";
										
										//print_r($print['media']);
										
										echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_id]}: <a href='mgr.media.php?dtype=search&ep=1&search={$print[media][media_id]}'>{$print[media][media_id]}</a></span>";
										if($print['media']['filename']) echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_file]}: <a href='mgr.media.php?dtype=search&ep=1&search={$print[media][filename]}'>{$print[media][filename]}</a></span>";
										
									}
									else
										echo "-";
									echo "</div>";
									echo "<div class='divTableCell'><span class='optionQuantity'>{$print[incQuantity]}</span></div>";
									echo "<div class='divTableCell'>";
										echo "<span class='packageItemName'><a href='mgr.prints.edit.php?edit={$print[print_id]}'>{$print[item_name]}</a></span>";
										if($print['media']['filename']) echo " - <span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_file]}: <a href='mgr.media.php?dtype=search&ep=1&search={$print[media][filename]}'>{$print[media][filename]}</a></span>";
										if($print['options'])
										{
											echo "<ul>";
												foreach($print['options'] as $option)
													echo "<li><span class='optionGroupName'>{$option[groupName]}</span>: {$option[name]}</li>";
											echo "</ul>";
										}
									echo "</div>";
								echo "</div>";
							}
							echo "</div>";
						}
						
						if($optionsArray['packageProductsArray'])
						{
							echo "<h1>{$mgrlang[gen_prods]}</h1>";
							echo "<div class='divTable'>";
							foreach($optionsArray['packageProductsArray'] as $productKey => $product)
							{
								echo "<div class='divTableRow'>";
									echo "<div class='divTableCell'>";
									if($product['media'])
									{
										if($product['media']['status'])
											echo "<img src='mgr.media.preview.php?src={$product[thumb][thumb_filename]}&folder_id={$product[media][folder_id]}&width=50' class='mediaFrame' title='Media ID: {$product[media][media_id]}' />"; // print_r($print['media']);
										else
											echo "<img src='images/mgr.theme.blank.gif' style='width: 50px;' class='mediaFrame' />";
											
										echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_id]}: <a href='mgr.media.php?dtype=search&ep=1&search={$product[media][media_id]}'>{$product[media][media_id]}</a></span>";
										if($product['media']['filename']) echo "<br /><span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_file]}: <a href='mgr.media.php?dtype=search&ep=1&search={$product[media][filename]}'>{$product[media][filename]}</a></span>";
										
									}
									else
										echo "-";
									echo "</div>";
									echo "<div class='divTableCell'><span class='optionQuantity'>{$product[incQuantity]}</span></div>";
									echo "<div class='divTableCell'>";
										echo "<span class='packageItemName'><a href='mgr.products.edit.php?edit={$product[prod_id]}'>{$product[item_name]}</a></span>";
										if($product['media']['filename']) echo " - <span style='font-size: 10px;white-space:nowrap;'>{$mgrlang[order_item_file]}: <a href='mgr.media.php?dtype=search&ep=1&search={$product[media][filename]}'>{$product[media][filename]}</a></span>";
										if($product['options'])
										{
											echo "<ul>";
												foreach($product['options'] as $option)
													echo "<li><span class='optionGroupName'>{$option[groupName]}</span>: {$option[name]}</li>";
											echo "</ul>";
										}
									echo "</div>";
								echo "</div>";
							}
							echo "</div>";
						}
						
						if($optionsArray['packageCollectionsArray'])
						{
							echo "<h1>{$mgrlang[gen_colls]}</h1>";
							echo "<div class='divTable'>";
							foreach($optionsArray['packageCollectionsArray'] as $collectionKey => $collection)
							{
								echo "<div class='divTableRow'>";
									echo "<div class='divTableCell'></div>";
									echo "<div class='divTableCell'><span class='optionQuantity'>{$collection[incQuantity]}</span></div>";
									echo "<div class='divTableCell'>";
										echo "<strong><a href='mgr.collections.edit.php?edit={$collection[coll_id]}'>{$collection[item_name]}</a></strong>";
									echo "</div>";
								echo "</div>";
							}
							echo "</div>";							
						}
						
						if($optionsArray['packageSubscriptionsArray'])
						{
							echo "<h1>{$mgrlang[gen_subs]}</h1>";
							echo "<div class='divTable'>";
							foreach($optionsArray['packageSubscriptionsArray'] as $subscriptionKey => $subscription)
							{
								echo "<div class='divTableRow'>";
									echo "<div class='divTableCell'></div>";
									echo "<div class='divTableCell'><span class='optionQuantity'>{$subscription[incQuantity]}</span></div>";
									echo "<div class='divTableCell'>";
										echo "<strong><a href='mgr.subscriptions.edit.php?edit={$subscription[sub_id]}'>{$subscription[item_name]}</a></strong><br>{$subscription[description]}";
									echo "</div>";
								echo "</div>";
							}
							echo "</div>";							
						}
					}
				}
				else // Select options
				{
					// Find any options
					$optionsResult = mysqli_query($db,
						"
						SELECT * 
						FROM {$dbinfo[pre]}invoice_options 
						LEFT JOIN {$dbinfo[pre]}options  
						ON {$dbinfo[pre]}invoice_options.option_id = {$dbinfo[pre]}options.op_id
						WHERE {$dbinfo[pre]}invoice_options.invoice_item_id = '{$_REQUEST[invoiceItemID]}'
						"
					);
					if($optionsRows = mysqli_num_rows($optionsResult))
					{
						unset($optionsArray); // Clear the array for the next item
						
						while($option = mysqli_fetch_array($optionsResult))
						{
							if($optionGroupNames[$option['option_gid']])
							{
								$option['groupName'] = $optionGroupNames[$option['option_gid']];
							}
							else
							{
								$optionGroupResult = mysqli_query($db,
									"
									SELECT * 
									FROM {$dbinfo[pre]}option_grp 
									WHERE og_id = '{$option[option_gid]}'
									"
								);
								$optionGroup = mysqli_fetch_array($optionGroupResult);
								
								$optionGroupName = $optionGroup['name']; // [todo] Get correct language
								
								$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
								
								$option['groupName'] = $optionGroupName;
							}
							
							// [todo] get the correct language name
							$optionsArray[] = $option;
							
							/*
							if($itemType == 'digital' or $itemType == 'collection')
								$digitalInvoiceItems['optionsArray'] = $optionsArray;
							else
								$physicalInvoiceItems['optionsArray'] = $optionsArray;
							*/
						}
						
						echo "<ul>";
							foreach($optionsArray as $key => $option)
								echo "<li><span style='font-weight: bold;'>{$option[groupName]}</span>: {$option[name]}</li>";
						echo "</ul>";
					}
				}
				
				
				
				/*
				$optionsResult = mysqli_query($db,"SELECT SQL_CALC_FOUND_ROWS * FROM {$dbinfo[pre]}invoice_options WHERE invoice_item_id = '$_REQUEST[invoiceItemID]'");
				$optionsRows = getRows();
				$options = mysqli_fetch_assoc($optionsResult);
				*/				
				/*
				try
				{
					$itemType = $_REQUEST['itemType'];
					$invoice = new invoiceTools;
					$invoiceOptions = $invoice->getOnlyItemOptions($itemType,$_REQUEST['invoiceItemID']); // Get the options as a temp array
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
				}
				echo "test";
				
				
				
				if($itemType == 'package')
				{
					
					echo "test";
					
					echo $invoiceOptions[$_REQUEST['invoiceItemID']];
					
					if($invoiceOptions[$_REQUEST['invoiceItemID']]['packagePrintsArray'])
					{
						echo "<h2>Prints</h2>";
						echo "<div class='divTable'>";
						foreach($invoiceOptions[$_REQUEST['invoiceItemID']]['packagePrintsArray'] as $printKey => $print)
						{
							echo "<div class='divTableRow'>";
								echo "<div class='divTableCell'>";
								if($print['media'])
									echo "";
								else
									echo "";
								echo "</div>";
								echo "<div class='divTableCell'>";
									echo "<span class='mtag mtagDarkGrey'>{$print[incQuantity]}</span> {$print[name]}";
									
									
									{if $print.options}
										<ul class="packageOptions">
											{foreach $print.options as $option}
												<li><span>{$option.groupName}</span>: {$option.name}</li>
											{/foreach}
										</ul>
									{/if}
									
								echo "</div>";
							echo "</div>";
						}
						echo "</div>";
					}

					if($invoiceOptions[$_REQUEST['invoiceItemID']]['packageProductsArray'])
					{
						
						<h2>{$lang.products}</h2>
						<div class="divTable">
						{foreach $invoiceItem.packageProductsArray as $productKey => $product}
							<div class="divTableRow">
								<div class="divTableCell">
									{if $product.product_type == '1'}
										{if $product.media}<img src="{mediaImage mediaID=$product.media.encryptedID type=thumb folderID=$product.media.encryptedFID size=40}" />{else}<img src="{$imgPath}/blank.question.png" />{/if}
									{/if}
								</div>
								<div class="divTableCell">
									<div class="mtag mtagDarkGrey">{$product.incQuantity}</div> {$product.name}<!--(DBID: {$product.oi_id})-->
									{if $product.options}
										<ul class="packageOptions">
											{foreach $product.options as $option}
												<li><span>{$option.groupName}</span>: {$option.name}</li>
											{/foreach}
										</ul>
									{/if}
								</div>
							</div>
						{/foreach}
						</div>
						
					}
					
					
					if($invoiceOptions[$_REQUEST['invoiceItemID']]['packageCollectionsArray'])
					{
						
						<h2>{$lang.collections}</h2>
						<div class="divTable">
						{foreach $invoiceItem.packageCollectionsArray as $collectionKey => $collection}
							<div class="divTableRow">
								<div class="divTableCell"></div>
								<div class="divTableCell">
									<div class="mtag mtagDarkGrey">{$collection.incQuantity}</div> {$collection.name}<!-- (DBID: {$collection.oi_id})-->
								</div>
							</div>
						{/foreach}
						</div>
						
					}
				}
				else
				{
					echo "test";
					
					//print_r($invoiceOptions[$_REQUEST['invoiceItemID']]);					
					echo "<ul>";
						foreach($invoiceOptions[$_REQUEST['invoiceItemID']]['optionsArray'] as $key => $option)
							echo "<li><span style='font-weight: bold;'>{$option[groupName]}</span>: {$option[name]}</li>";
					echo "</ul>";
				}
				*/
				
			break;
		}	
?>
