<?php
	
	require_once BASE_PATH.'/assets/includes/photo.puzzle.inc.php';
	require_once BASE_PATH.'/assets/classes/invoicetools.php';
	$invoice = new invoiceTools; // New invoiceTools object
	$setCurrency = new currencySetup;
	
	$adminCurrency = getCurrencyInfo($config['settings']['defaultcur']); // Get the admin currency info
	$setCurrency->setSelectedCurrency($config['settings']['defaultcur']); // Set the admin currerncy as the selected currency
	$exchangeRate = $config['settings']['cur_exchange_rate']; // Shortcut
	
	$cleanCurrency = new number_formatting;
	$cleanCurrency->set_cur_defaults();	
	$cleanNumber = new number_formatting;
	$cleanNumber->set_num_defaults();
	
	// from IPN
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment
	
	// Get the post values in xml format
	$postBackInfo = "<postback>";
	foreach($_POST as $postKey => $postValue)
	{
		$emailPostInfo.="{$postKey}={$postValue}<br />"; // xxxxxxx just for testing
		$postBackInfo.="<{$postKey}>{$postValue}</{$postKey}>";
	}
	$postBackInfo.= "</postback>";
	
	$explodedOrderID = explode('-',$ipnValue['orderID']); // Explode the value to check for bill
		
	switch($explodedOrderID[0])
	{
		case 'bill':			
			$ubillID = $explodedOrderID[1]; // Get the unique bill id from the exploded string			
			$billInfo = $invoice->getBillDetails($ubillID); // Get the bill info using the passed ubill ID			
			//$invoiceInfo = $invoice->getInvoiceDetailsViaBillDBID($billInfo['bill_id']); // Get invoice details
			mysqli_query($db,
			"
				UPDATE {$dbinfo[pre]}invoices SET 
				payment_status='{$ipnValue[paymentStatus]}',
				post_vars='{$postBackInfo}',
				payment_date='{$nowGMT}'
				WHERE bill_id = '{$billInfo[bill_id]}'
			"); // Update invoices db
			
			// For testing - Email the membership info
			/*
			foreach($billInfo as $billInfoKey => $billInfoValue)
			{
				$billInfoContent.="{$billInfoKey}={$billInfoValue}<br />"; // just for testing
			}			
			kmail('info@ktools.net','Ktools','info@ktools.net','Ktools','Testing IPN - Membership',$billInfoContent);
			*/
			
			if($billInfo['membership']) // This is for a membership
			{
				$membershipResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}memberships WHERE ms_id = '{$billInfo[membership]}'");
				$membershipRows = mysqli_num_rows($membershipResult);
				
				if($membershipRows) // Make sure there are rows
				{
				
					$membership = mysqli_fetch_array($membershipResult);
					
					// Select member details
					$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$billInfo[member_id]}'");
					$memberRows = mysqli_num_rows($memberResult);
					
					if($memberRows)
					{
						$member = mysqli_fetch_array($memberResult);
						
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
						
						mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET ms_end_date='{$msEndDate}',membership='{$billInfo[membership]}' WHERE mem_id = '{$member[mem_id]}'"); // Update member account		
					}
				}
			}
			
			/*
			try
			{
				$memberObj = new memberTools();			
				$member = $memberObj->getMemberInfoFromDB($billInfo['member_id']); // Get all the member info from the database	
				$member['primaryAddress'] = $memberObj->getPrimaryAddress();
				
				$smarty->assign('member',$member);
				$smarty->assign('bill',$billInfo);
				$smarty->assign('invoice',$invoiceDetails);
				
				// Send user email/receipt
				$content = getDatabaseContent(62); // Get content and force language for admin
				$content['name'] = $smarty->fetch('eval:'.$content['name']);
				$content['body'] = $smarty->fetch('eval:'.$content['body']);
				kmail($member['email'],$member['f_name'],$config['settings']['sales_email'],$config['settings']['business_name'],$content['name'],$content['body']); // Send email about new tag submitted
					
				// Send admin email/receipt
				$content = getDatabaseContent(63,$config['settings']['lang_file_mgr']); // Get content and force language for admin
				$content['name'] = $smarty->fetch('eval:'.$content['name']);
				$content['body'] = $smarty->fetch('eval:'.$content['body']);
				kmail($config['settings']['sales_email'],$config['settings']['business_name'],$config['settings']['support_email'],$config['settings']['business_name'],$content['name'],$content['body']); // Send email about new tag submitted
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			*/			
			//kmail('info@ktools.net','PS4','info@ktools.net','PSP','PS4 BILL (Gateway Post Info)',$emailPostInfo."<br /><br />Exp0: {$explodedOrderID[0]} / Exp1: {$ubillID} / Pay Status: {$ipnValue[paymentStatus]}"); //xxxxxxxxxxxxxxxxxx For testing
		break;
		default:
			// Pull order info from db
			//$orderResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders WHERE uorder_id = '{$ipnValue[orderID]}'");
			//$orderRows = mysqli_num_rows($orderResult);
			try
			{
				$orderDetails = $invoice->getOrderDetails($ipnValue['orderID']); // Fetch the order details
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
			
			if($orderDetails)
			{	
				$formattingObj = new number_formatting; // Used to make sure the bills are showing in the admins currency
				$formattingObj->set_custom_cur_defaults($config['settings']['defaultcur']);
				$parms['noDefault'] = true;
				
				//$orderDetails = mysqli_fetch_assoc($orderResult); // Fetch the order details
				//$orderProcessed = 0; // At first set order to not approved - No longer needed
				
				// Payment Status // 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed
				// Order Status // 0=pending, 1=approved, 2=incomplete, 3=cancelled, 4=failed
				
				if($orderDetails['member_id']) // Check if this order was placed by a member // Removed in 4.4  and !$_SESSION['member']
				{
					//$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$orderDetails[member_id]}'"); // Fetch the member details
					//$member = mysqli_fetch_assoc($memberResult);
					try
					{
						$memberObj = new memberTools();			
						$memberDB = $memberObj->getMemberInfoFromDB($orderDetails['member_id']); // Get all the member info from the database	
						$memberDB['primaryAddress'] = $memberObj->getPrimaryAddress();
						
						@$newMemberArray = array_merge($_SESSION['member'],$memberDB);
						$_SESSION['member'] = $newMemberArray;
						//$_SESSION['member'] = $memberDB; // Replaced with the code above
					}
					catch(Exception $e)
					{
						echo $e->getMessage();
					}
				}
				
				//print_r($_SESSION['member']); exit;
				
				//echo "a".count($_SESSION['galleriesData'])."<br>";
				//exit;
				
				$potentialInvoiceNumber = $config['settings']['invoice_prefix'].$config['settings']['invoice_next'].$config['settings']['invoice_suffix'];
				
				switch($ipnValue['paymentStatus'])
				{
					case 0: // Pending
						$orderStatus = 0;
					break;
					case 1: // Completed			
						$orderStatus = ($config['settings']['auto_orders']) ? 1 : 0;
						$invoiceNumber = $potentialInvoiceNumber; // Create invoice number
					break;
					case 2: // Unpaid - For mail in payments			
						$orderStatus = 0;
						$invoiceNumber = $potentialInvoiceNumber; // Create invoice number
					break;
					case 3: // Bill Me Later
						$orderStatus = ($config['settings']['auto_orders']) ? 1 : 0;
						$invoiceNumber = $potentialInvoiceNumber; // Create invoice number
					break;
					case 4: // Failed
						$orderStatus = 0; // Payment failed - order status should be marked as failed also - used to be set to 4 until 4.4.4
					break;
				}
				
				if($invoiceNumber)
				{
					$nextInvoiceNumber = $config['settings']['invoice_next']+1;
					mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET invoice_next='{$nextInvoiceNumber}' WHERE settings_id = 1"); // Update sequential invoice number in settings db
				}
				
				if($orderDetails['member_id']) // Check if this order was placed by a member
				{
					//$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$orderDetails[member_id]}'"); // Fetch the member details
					//$member = mysqli_fetch_assoc($memberResult);
					$memberCreditsAvailable = $memberDB['credits']; // The number of credits a member initiall has avaiable
					//test($memberCreditsAvailable,'memberCreditsAvailable'); // Testing
				}
				
				//$invoiceResult = mysqli_query($db,"SELECT invoice_id FROM {$dbinfo[pre]}invoices WHERE order_id = '{$orderDetails[order_id]}'"); // Fetch the invoice details
				//$invoiceDetails = mysqli_fetch_assoc($invoiceResult);
				$invoiceDetails = $invoice->getInvoiceDetailsViaOrderDBID($orderDetails['order_id']);
				
				$runningUsedCredits = 0; // Initially set credits used to 0
				$orderCreditsAvailable = 0;  // Initially set credits purchased to 0
				
				//test($orderCreditsAvailable,'orderCreditsAvailable'); // Testing
				
				//$invoiceItemsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '{$invoiceDetails[invoice_id]}'"); // Fetch the invoice items details
				try
				{
					$invoiceItemRows = $invoice->queryInvoiceItems($invoiceDetails['invoice_id']); // Fetch the invoice items details
					$invoice->includeGalleries = true; // Include galleries in the result	
					$invoiceItems = $invoice->getAllInvoiceItems(); // Get the invoice items // getInvoiceItemsRaw
					
					//print_r($invoiceItems); exit; // Testing
					//print_r($invoiceItemArray); exit; // Testing
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
				}
				foreach($invoiceItems as $itemKey => $invoiceItem)
				{	
					//print_r($invoiceItem); exit; // Testing					
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
							
							//test($orderCreditsAvailable,'orderCreditsAvailableAfterAdd'); // Testing
							
						break;
						case 'print':
							if($ipnValue['paymentStatus'] != 4 and !$orderDetails['processed_quantities'])
							{
								$printResult = mysqli_query($db,"SELECT mp_id,quantity FROM {$dbinfo[pre]}media_prints WHERE print_id = '{$invoiceItem[item_id]}' AND media_id = '{$invoiceItem[asset_id]}'"); // Fetch the print details
								$print = mysqli_fetch_assoc($printResult);
								
								if($print['quantity'])
								{
									$newPrintQuantity = $print['quantity'] - 1; // Subtract one from the quantity
									mysqli_query($db,"UPDATE {$dbinfo[pre]}media_prints SET quantity='{$newPrintQuantity}' WHERE mp_id = '{$print[mp_id]}'"); // adjust the quantity
								}
								
								/*
								if($print['notify_lab']) // Lab should be notified of this
								{
									$lab[$print['notify_lab']][] = $invoiceItem;
								}
								*/
							}
						break;							
						case 'product':
							if($ipnValue['paymentStatus'] != 4 and !$orderDetails['processed_quantities'])
							{
								$productResult = mysqli_query($db,"SELECT prod_id,product_type FROM {$dbinfo[pre]}products WHERE prod_id = '{$invoiceItem[item_id]}'"); // Fetch the print details
								$product = mysqli_fetch_assoc($productResult);
								/*
								$bPP = is_photo_puzzle($product['prod_id']); //FP 
								if($bPP) break;
								*/
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
							if($ipnValue['paymentStatus'] != 4 and !$orderDetails['processed_quantities'])
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
							if($ipnValue['paymentStatus'] != 4 and !$orderDetails['processed_quantities'])
							{
								if($orderStatus) // Make sure order was approved before adding subscription
								{
									$subscriptionResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}subscriptions WHERE sub_id = '{$invoiceItem[item_id]}'"); // Fetch the subscription details
									$subscription = mysqli_fetch_assoc($subscriptionResult);
								
									$futureDate = date("Y-m-d H:m:s",strtotime("{$nowGMT} +{$subscription[durvalue]} {$subscription[durrange]}")); // Date at which it expires 
									mysqli_query($db,"INSERT INTO {$dbinfo[pre]}memsubs (mem_id,sub_id,expires,start_date,perday,status,total_downloads) VALUES ('{$memberDB[mem_id]}','{$subscription[sub_id]}','{$futureDate}','{$nowGMT}','{$subscription[downloads]}',1,'{$subscription[tdownloads]}')"); // Enter subscription into db
									//mysqli_query($db,"INSERT INTO {$dbinfo[pre]}memsubs (mem_id,sub_id,expires,perday,status) VALUES ('{$member[mem_id]}','{$sub_id[promo_id]}','{$nowGMT}','{$subscription[downloads]}',1)"); // Enter subscription into db
								
									$processedSubs = 1; // Subs have been processed
								}
							}
						break;
						case 'digital':
							if($ipnValue['paymentStatus'] != 4)
							{
								if(!$orderDetails['processed_quantities'])
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
								}
								
								if($config['settings']['expire_download'] > 0) // Only if its other than 0 (never)
								{
									$expireDays = $config['settings']['expire_download']; // Days until the download expires
									$futureDate = date("Y-m-d H:m:s",strtotime("{$nowGMT} +{$expireDays} days")); // Date at which it expires 									
									mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET expires='{$futureDate}' WHERE oi_id = '{$invoiceItem[oi_id]}'"); // Mark download dates in the future
								}
							}
						break;
						case 'collection':
							if($ipnValue['paymentStatus'] != 4)
							{
								if(!$orderDetails['processed_quantities'])
								{
									
								}
								
								if($config['settings']['expire_download'] > 0) // Only if its other than 0 (never)
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
					
					//test($runningUsedCredits,'runningUsedCredits'); // Testing
					
					$processedQuantities = 1; // Quantities were processed
					
					if($orderStatus) // Make sure payment/order was approved before setting the status
					{
						mysqli_query($db,"UPDATE {$dbinfo[pre]}invoice_items SET status='1' WHERE oi_id = '{$invoiceItem[oi_id]}'"); // Set the status of the invoice item to 1
						@mysqli_query($db,"UPDATE {$dbinfo[pre]}commission SET order_status='1' WHERE oitem_id = '{$invoiceItem[oi_id]}'"); // Set the status of the commission to 1
					}
				}
				
				if($ipnValue['paymentStatus'] != 4 and !$orderDetails['processed_coupons']) // If the payment is other than failed then update the coupon usage
				{
					if($invoiceDetails['discount_ids_used']) // Update coupons used, etc - regardless if the order was approved or not
					{
						$couponIDs = explode(',',$invoiceDetails['discount_ids_used']); // Split the ids from the db					
						
						foreach($couponIDs as $key => $id)
						{
							
							$couponsResult = mysqli_query($db,"SELECT promo_id,quantity,oneuse FROM {$dbinfo[pre]}promotions WHERE promo_id = '{$id}'"); // Fetch the coupon/promo details
							$coupon = mysqli_fetch_assoc($couponsResult);
							
							if($coupon['quantity'] > 0) // Update the quantity if it is greater than 0
							{
								$newQuantity = $coupon['quantity'] - 1;								
								mysqli_query($db,"UPDATE {$dbinfo[pre]}promotions SET quantity='{$newQuantity}' WHERE promo_id = '{$coupon[promo_id]}'"); // Update coupon/promo db with new quantity
							}
							
							//echo "oneuse:".$coupon['oneuse'];
							
							if($coupon['oneuse']) // If this is a one time use then add the record to the usedcoupons db
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}usedcoupons (mem_id,promo_id,usedate) VALUES ('{$memberDB[mem_id]}','{$coupon[promo_id]}','{$nowGMT}')"); // Update usage db
						}						
						$processedCoupons = 1; // Set status of processed coupons to 1
					}
					//exit; // testing
				}
				
				//$memberCreditsRemaining = ($memberCreditsAvailable+$orderCreditsAvailable) - $runningUsedCredits; // Calculate credits remaining
				
				if($creditsUsedInOrder and !$orderDetails['processed_credits_used']) // Credits used in this order
				{
					//echo $runningUsedCredits; exit; // Testing
					$memberCreditsRemaining = $memberCreditsAvailable - $invoiceDetails['credits_total']; // Because of possible coupons and discounts that could be used this was changed from using $runningUsedCredits which keeps a running total from the invoice items
					$processedCreditsUsed = 1; // Set the status of credits used to 1
					
					$_SESSION['member']['credits'] = $memberCreditsRemaining; // Update member credits in session
					mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET credits='{$memberCreditsRemaining}' WHERE mem_id = '{$memberDB[mem_id]}'"); // Update member account with new credits
				}
				else
					$memberCreditsRemaining = $memberCreditsAvailable; // No credits were used in this order so set the credits remaining to the initial available
				
				//test($memberCreditsRemaining,'memberCreditsRemaining'); // Testing
				
				if($creditsPurchasedInOrder and !$orderDetails['processed_credits_gained']) // Credits were purchased in this order
				{
					if($orderStatus) // Make sure order was approved before adding credits
					{	
						//$memberCreditsAvailable						
						$memberCreditsRemaining = $memberCreditsRemaining+$orderCreditsAvailable;
						$processedCreditsGained = 1; // Set the status of credits gained to 1
						
						$_SESSION['member']['credits'] = $memberCreditsRemaining; // Update member credits in session
						mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET credits='{$memberCreditsRemaining}' WHERE mem_id = '{$memberDB[mem_id]}'"); // Update member account with new credits
					}
				}
				
				//test($memberCreditsRemaining,'memberCreditsRemaining2'); // Testing
				
				if($creditsPurchasedInOrder or $creditsUsedInOrder) // If credits were purchased
				{	
					/*
					if($orderStatus) // Make sure order was approved before adding credits
					{				
						$memberCreditsRemaining = ($memberCreditsAvailable+$orderCreditsAvailable) - $runningUsedCredits; // Calculate credits remaining
						$_SESSION['member']['credits'] = $memberCreditsRemaining; // Update member credits in session					
						mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET credits='{$memberCreditsRemaining}' WHERE mem_id = '{$member[mem_id]}'"); // Update member account with new credits
					}
					*/
				}
				
				if($orderStatus) // Make sure order was approved
					$orderProcessed = 1; // Mark order as processed because credits were added to the order - No longer used
				
				// Get the name and email if none was already entered in the invoice
				$invoiceDetails['ship_name'] = ($invoiceDetails['ship_name']) ? $invoiceDetails['ship_name'] : $ipnValue['payerName']; // Name
				$invoiceDetails['bill_name'] = ($invoiceDetails['bill_name']) ? $invoiceDetails['bill_name'] : $ipnValue['payerName'];				
				$invoiceDetails['ship_email'] = ($invoiceDetails['ship_email']) ? $invoiceDetails['ship_email'] : $ipnValue['payerEmail']; // Email
				$invoiceDetails['bill_email'] = ($invoiceDetails['bill_email']) ? $invoiceDetails['bill_email'] : $ipnValue['payerEmail'];				
				$invoiceDetails['ship_address'] = ($invoiceDetails['ship_address']) ? $invoiceDetails['ship_address'] : $ipnValue['payerAddress']; // Address
				$invoiceDetails['bill_address'] = ($invoiceDetails['bill_address']) ? $invoiceDetails['bill_address'] : $ipnValue['payerAddress'];
				$invoiceDetails['ship_address2'] = ($invoiceDetails['ship_address2']) ? $invoiceDetails['ship_address2'] : $ipnValue['payerAddress2'];
				$invoiceDetails['bill_address2'] = ($invoiceDetails['bill_address2']) ? $invoiceDetails['bill_address2'] : $ipnValue['payerAddress2'];				
				$invoiceDetails['ship_city'] = ($invoiceDetails['ship_city']) ? $invoiceDetails['ship_city'] : $ipnValue['payerCity']; // City
				$invoiceDetails['bill_city'] = ($invoiceDetails['bill_city']) ? $invoiceDetails['bill_city'] : $ipnValue['payerCity'];				
				$invoiceDetails['ship_state'] = ($invoiceDetails['ship_state']) ? $invoiceDetails['ship_state'] : $ipnValue['payerState']; // State
				$invoiceDetails['bill_state'] = ($invoiceDetails['bill_state']) ? $invoiceDetails['bill_state'] : $ipnValue['payerState'];				
				$invoiceDetails['ship_country'] = ($invoiceDetails['ship_country']) ? $invoiceDetails['ship_country'] : $ipnValue['payerCountry']; // Country
				$invoiceDetails['bill_country'] = ($invoiceDetails['bill_country']) ? $invoiceDetails['bill_country'] : $ipnValue['payerCountry'];				
				$invoiceDetails['ship_zip'] = ($invoiceDetails['ship_zip']) ? $invoiceDetails['ship_zip'] : $ipnValue['payerZip']; // ZIP
				$invoiceDetails['bill_zip'] = ($invoiceDetails['bill_zip']) ? $invoiceDetails['bill_zip'] : $ipnValue['payerZip'];
				
				$invoiceDetails['invoice_number'] = $invoiceNumber; // Put the invoiceNumber in the details array

				if($memberDB)
					$memberDB['name'] = "{$memberDB[name]} {$memberDB[f_name]}";
				else
				{
					$memberDB['name'] = "{$invoiceDetails[ship_name]}";
					$memberDB['email'] = "{$invoiceDetails[ship_email]}";
				}
				
				mysqli_query($db,
				"
					UPDATE {$dbinfo[pre]}invoices SET 
					invoice_number='{$invoiceNumber}',
					ship_name='{$invoiceDetails[ship_name]}',
					bill_name='{$invoiceDetails[bill_name]}',
					ship_email='{$invoiceDetails[ship_email]}',
					bill_email='{$invoiceDetails[bill_email]}',
					ship_address='{$invoiceDetails[ship_address]}',
					bill_address='{$invoiceDetails[bill_address]}',
					ship_address2='{$invoiceDetails[ship_address2]}',
					bill_address2='{$invoiceDetails[bill_address2]}',
					ship_city='{$invoiceDetails[ship_city]}',
					bill_city='{$invoiceDetails[bill_city]}',
					ship_zip='{$invoiceDetails[ship_zip]}',
					bill_zip='{$invoiceDetails[bill_zip]}',
					ship_state_readable='{$invoiceDetails[ship_state]}',
					bill_state_readable='{$invoiceDetails[bill_state]}',
					ship_country_readable='{$invoiceDetails[ship_country]}',
					bill_country_readable='{$invoiceDetails[bill_country]}',
					payment_status='{$ipnValue[paymentStatus]}',
					post_vars='{$postBackInfo}',
					payment_date='{$nowGMT}'
					WHERE order_id = '{$orderDetails[order_id]}'
				"); // Update invoices db	
				mysqli_query($db,
				"
					UPDATE {$dbinfo[pre]}orders SET 
					order_status='{$orderStatus}',
					processed_credits_used='{$processedCreditsUsed}',
					processed_credits_gained='{$processedCreditsGained}',
					processed_quantities='{$processedQuantities}',
					processed_coupons='{$processedCoupons}',
					processed_subs='{$processedSubs}' 
					WHERE uorder_id = '{$ipnValue[orderID]}'
				"); // Upade orders db
				
				foreach($invoiceItems as $invoiceItemKey => $invoiceItem)
				{
						
					$bPP = is_photo_puzzle($invoiceItem['item_id']); //FP 
					if($bPP) {
						$query = tep_db_query("SELECT * FROM ".TABLE_PHOTO_PUZZLE." WHERE photo_puzzle_id = ".$invoiceItem['asset_id']."");
	        			$aCurrent = tep_db_fetch_array($query);
						$photoPuzzleId = $aCurrent['photo_puzzle_id'];
						
		        		$aBoxFile = pathinfo($aCurrent['box_file']);
		        		$boxFile = $aBoxFile['basename'];
		        		if(!isset($aBoxFile['extension'])) $boxFile = $boxFile.'.png'; 
						
						$img = WEB_DIR.$aCurrent['sesskey'].'/'.$photoPuzzleId.'/boxes/'.$boxFile;
						$invoiceItem['photoPuzzleImg'] = $img;
					}
					$invoiceItems[$invoiceItemKey]['price_total'] = $formattingObj->currency_display($invoiceItem['price_total'],1);					
					$invoiceItems[$invoiceItemKey]['cost_value'] = ($invoiceItem['paytype'] == 'cur') ? $formattingObj->currency_display($invoiceItem['price_total'],1) : $invoiceItem['credits_total'];
					
					$invoiceItems[$invoiceItemKey]['name'] = $invoiceItems[$invoiceItemKey]['itemDetails']['name']; // Shortcut to name of item
					
					//if($invoiceItems[$invoiceItemKey]['itemDetails']['media'])
					if($invoiceItems[$invoiceItemKey]['itemDetails']['media'] && !$bPP)
						$invoiceItems[$invoiceItemKey]['thumbnail'] = "<img src='{$siteURL}/image.php?mediaID=".$invoiceItems[$invoiceItemKey]['itemDetails']['media']['encryptedID']."&type=thumb&folderID=".$invoiceItems[$invoiceItemKey]['itemDetails']['media']['encryptedFID']."&size=45' style='vertical-align: middle' />";
					else if($invoiceItems[$invoiceItemKey]['itemDetails']['photo'])
						$invoiceItems[$invoiceItemKey]['thumbnail'] = "<img src='{$siteURL}/productshot.php?itemID=".$invoiceItems[$invoiceItemKey]['item_id']."&itemType=".$invoiceItems[$invoiceItemKey]['itemTypeShort']."&photoID=".$invoiceItems[$invoiceItemKey]['itemDetails']['photo']['id']."&size=45' style='vertical-align: middle' />";
					elseif ($bPP) {
						$invoiceItems[$invoiceItemKey]['thumbnail'] = "<img src='{$siteURL}/{$img}' style='width: 45px; vertical-align: middle' />";
					}

					if($invoiceItem['rm_selections'])
					{	
						foreach(explode(',',$invoiceItem['rm_selections']) as $value)
						{
							if($value)
								$rm = explode(':',$value);
							
							if($rm[0])
							{
								$rmGroupResult = mysqli_query($db,"SELECT og_name FROM {$dbinfo[pre]}rm_option_grp WHERE og_id = '{$rm[0]}'");
								if($rmGroupRows = mysqli_num_rows($rmGroupResult))
								{
									$rmGroup = mysqli_fetch_assoc($rmGroupResult);
									
									$rmOptionResult = mysqli_query($db,"SELECT op_name FROM {$dbinfo[pre]}rm_options WHERE op_id = '{$rm[1]}'");
									$rmOption = mysqli_fetch_assoc($rmOptionResult);
									
									$invoiceItems[$invoiceItemKey]['rm'][] = array('grpName' => $rmGroup['og_name'],'opName' => $rmOption['op_name']);
									//echo "<li style='margin: 4px 0'><strong>{$rmGroup[og_name]}</strong>: {$rmOption[op_name]}</li>";	
								}
							}							
							unset($rm);
						}
					}
				}
				
				$invoiceDetails['subtotal'] = $formattingObj->currency_display($invoiceDetails['subtotal'],1);
				$invoiceDetails['total'] = $formattingObj->currency_display($invoiceDetails['total'],1);
				$invoiceDetails['discounts_total'] = $formattingObj->currency_display($invoiceDetails['discounts_total']*-1,1);
				
				$tax_total = $invoiceDetails['taxa_cost'] + $invoiceDetails['taxb_cost'] + $invoiceDetails['taxc_cost'];
				$invoiceDetails['tax_total'] = $formattingObj->currency_display($tax_total,1);
				
				// Tax
				$invoiceDetails['taxa_cost'] = $formattingObj->currency_display($invoiceDetails['taxa_cost'],1);
				$invoiceDetails['taxb_cost'] = $formattingObj->currency_display($invoiceDetails['taxb_cost'],1);
				$invoiceDetails['taxc_cost'] = $formattingObj->currency_display($invoiceDetails['taxc_cost'],1);
				
				// Credits
				$invoiceDetails['credits_subtotal'] = $invoiceDetails['credits_total']+$invoiceDetails['discounts_credits_total'];
				$invoiceDetails['credits_discounts_total'] = $invoiceDetails['discounts_credits_total']*-1;
				$invoiceDetails['credits_total'] = $invoiceDetails['credits_total'];
				
				$invoiceDetails['shipping_cost'] = $formattingObj->currency_display($invoiceDetails['shipping_cost'],1);
				
				switch($ipnValue['paymentStatus'])
				{
					case 0: // PROCCESSING
					case 1: // PAID/APPROVED
						$invoiceDetails['payment'] = $invoiceDetails['total'];
						$invoiceDetails['balance'] = $formattingObj->currency_display(0,1);
					break;
					case 3: // BILL LATER
						$invoiceDetails['payment'] = $formattingObj->currency_display(0,1);
						$invoiceDetails['balance'] = $formattingObj->currency_display($invoiceDetails['total'],1);
					break;
					case 4: // FAILED
					case 5: // REFUNDED
					case 6: // CANCELLED
						$invoiceDetails['payment'] = $formattingObj->currency_display(0,1);
						$invoiceDetails['balance'] = $formattingObj->currency_display(0,1);
					break;
				}
				
				$invoiceDetails['invoiceLink'] = "invoice.php?orderID={$orderDetails[uorder_id]}"; // Link to this order invoice page
				$orderDetails['orderLink'] = "order.details.php?orderID={$orderDetails[uorder_id]}"; // Link to this order details page
				
				$orderDetails['order_status_lang'] = $lang[orderStatusNumToText($orderStatus)]; // Order status in correct lang
				$invoiceDetails['payment_status_lang'] = $lang[orderPaymentNumToText($ipnValue['paymentStatus'])]; // Invoice status in correct lang
				
				try
				{
					$customDate = new kdate;
					$invoiceDetails['invoice_date_display_admin'] = $customDate->showdate($invoiceDetails['invoice_date'],0); // Get corrected dates before using member data
					$customDate->setMemberSpecificDateInfo();
					$invoiceDetails['invoice_date_display'] = $customDate->showdate($invoiceDetails['invoice_date'],0); // Get corrected dates
					
					$smarty->assign('adminCurrency',$adminCurrency);
					$smarty->assign('member',$memberDB);
					$smarty->assign('order',$orderDetails);
					$smarty->assign('invoice',$invoiceDetails);
					$smarty->assign('invoiceItems',$invoiceItems);
					
					// First check for member language
					if($memberDB['language'])					
						$sendEmailInLang = $memberDB['language'];
						
					// See if a language was set
					// If not then try using the checkout lang
					if(!$sendEmailInLang)
						$sendEmailInLang = $orderDetails['checkout_lang'];
					
					// Use the default language
					if(!$sendEmailInLang)
						$sendEmailInLang = $config['settings']['default_lang'];
					
					//$sendEmailInLang = ($memberDB['language']) ? $memberDB['language'] : '';
					
					// Send user email/receipt
					$content = getDatabaseContent('orderEmail',$sendEmailInLang); // Get content 
					$content['name'] = $smarty->fetch('eval:'.$content['name']);
					$content['body'] = $smarty->fetch('eval:'.$content['body']);
					
					if(!$memberDB['email'])
						$memberDB['email'] = $invoiceDetails['ship_email']; // If no member email then use invoice email
						
					if(!$memberDB['email'])
						$memberDB['email'] = $invoiceDetails['bill_email']; // If still no email then use bill email
						
					if(!$memberDB['name'])
						$memberDB['name'] = $invoiceDetails['ship_name']; // If no member name then use invoice name
						
					if(!$memberDB['name'])
						$memberDB['name'] = $invoiceDetails['bill_name']; // If still no name then use bill name
					
					if(!$memberDB['name'])
						$memberDB['name'] = $memberDB['email']; // Still no name then use the email address
					
					//echo "name:".$memberDB['name']; exit; // Testing
					
					$useCustomerName = ($config['useCustomerNameInEmail']) ? $memberDB['name'] : ''; // Fix for special characters (Russian) breaking the email by including a name
					
					// Send email only if email address exists
					if($memberDB['email']) kmail($memberDB['email'],$useCustomerName,$config['settings']['sales_email'],$config['settings']['business_name'],$content['name'],$content['body']); // Send email about new tag submitted
					
					// Send admin email/receipt
					$content = getDatabaseContent('newOrderEmailAdmin',$config['settings']['lang_file_mgr']); // Get content and force language for admin
					$content['name'] = $smarty->fetch('eval:'.$content['name']);
					$content['body'] = $smarty->fetch('eval:'.$content['body']);
					kmail($config['settings']['sales_email'],$config['settings']['business_name'],$config['settings']['support_email'],$config['settings']['business_name'],$content['name'],$content['body']); // Send email about new tag submitted
				
					// Send lab email
					
				
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
				}
			}
			
			//kmail('info@ktools.net','PS4','info@ktools.net','PSP','PS4 ORDER (Gateway Post Info)',$emailPostInfo); //xxxxxxxxxxxxxxxxxx For testing
		break;
	}
?>