<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 2-10-2012
	*  Modified: 2-10-2012
	******************************************************************/
	
	/*
	* Load invoice
	*/
	class invoiceTools
	{
		private $orderID;
		private $invoiceID;
		private $ubillID;
		private $dbinfo;
		private $physicalInvoiceItems;
		private $digitalInvoiceItems;
		private $invoiceItems;
		public $options = false;
		public $orderInfo;
		public $includeGalleries = false;
		
		public function __construct()
		{
			$this->dbinfo = getDBInfo(); // Get db info
		}
		
		/*
		* Set an order ID
		*/
		public function setOrderID($orderID)
		{
			if($orderID) // Check if the order ID was passed
				$this->orderID = $orderID;
			else // No order ID throw exception
				throw new Exception('No order ID was passed');
		}
		
		/*
		* Set an order ID
		*/
		public function setUBillID($ubillID)
		{
			if($ubillID) // Check if the order ID was passed
				$this->ubillID = $ubillID;
			else // No order ID throw exception
				throw new Exception('No ubillID was passed');
		}
		
		/*
		* Set an invoice ID
		*/
		public function setInvoiceID($invoiceID)
		{
			if($invoiceID) // Check if the invoice ID was passed
				$this->invoiceID = $invoiceID;
			else // No invoice ID throw exception
				throw new Exception('No invoice ID was passed');
		}
		
		/*
		* Set a single invoice ID
		*/
		public function getSingleInvoiceItem($invoiceItemID)
		{
			global $db;
			
			if(!$invoiceItemID) // Check if the invoice ID was passed
				throw new Exception('No invoice item ID was passed');
			
			$itemResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}invoice_items WHERE oi_id = '{$invoiceItemID}'");
			if($itemRows = mysqli_num_rows($itemResult))
			{
				$item = mysqli_fetch_assoc($itemResult);
				return $item;
			}
			else
				return false;
			
		}	
		
		/*
		* Get order details
		*/
		public function getOrderDetails($orderID='')
		{
			global $db;
			
			if($orderID) // Set the order id
				$this->orderID = $orderID;
				
			if(!$this->orderID) // Check to make sure an order ID is present
				throw new Exception('getOrderDetails: No order id is set - Call setOrderID first or pass order id in function');
			
			// Get invoice details from db
			if(is_numeric($this->orderID))
				$orderResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}orders WHERE order_id = '{$this->orderID}'");
			else			
				$orderResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}orders WHERE uorder_id = '{$this->orderID}'");
				
			if($orderRows = mysqli_num_rows($orderResult))
			{
				$order = mysqli_fetch_assoc($orderResult);
				if(function_exists('orderStatusNumToText')) $order['orderStatusLang'] = orderStatusNumToText($order['order_status']); // Get order status language tag
				$this->orderInfo = $order;
				return $this->orderInfo;
			}
			else
				return false;
		}
		
		/*
		* Get the bill details by passing ubillID
		*/
		public function getBillDetails($ubillID='')
		{	
			global $db;
			
			if($ubillID) // Set the order id
				$this->ubillID = $ubillID;
			
			if(!$this->ubillID) // Check to make sure an invoice ID is present
				throw new Exception('getBillDetails: No ubillid was passed');
			
			// Get bill details from db
			$billResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}billings WHERE ubill_id = '{$this->ubillID}'");
			if($billRows = mysqli_num_rows($billResult))
			{
				$bill = mysqli_fetch_assoc($billResult);
				$this->ubillID = $bill['bill_id']; // Set the bill db id
			
				return $bill;
			}
			else
				return false;
		}
		
		/*
		* Get the invoice details by using the bills's database id
		*/
		public function getInvoiceDetailsViaBillDBID($billDBID)
		{	
			global $db;
			
			if(!$billDBID) // Check to make sure a bill ID is present
				throw new Exception('getInvoiceDetailsViaBillDBID: No bill database id was passed');
			
			// Get invoice details from db
			$invoiceResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}invoices WHERE bill_id = '{$billDBID}'");
			if($invoiceRows = mysqli_num_rows($invoiceResult))
			{
				$invoice = mysqli_fetch_assoc($invoiceResult);
				$this->invoiceID = $invoice['invoice_id']; // Set the invoice id
				$invoice['paymentStatusLang'] = orderPaymentNumToText($invoice['payment_status']); // Get payment status language tag
				
				// Get real names
				$invoice['ship_state'] = getStateName($invoice['ship_state']); //
				$invoice['ship_country'] = getCountryName($invoice['ship_country']); //
				$invoice['bill_state'] = getStateName($invoice['bill_state']); //
				$invoice['bill_country'] = getCountryName($invoice['bill_country']); //
				
				return $invoice;
			}
			else
				return false;
		}
		
		/*
		* Get the invoice details by using the order's database id
		*/
		public function getInvoiceDetailsViaOrderDBID($orderDBID)
		{			
			global $db;
			
			if(!$orderDBID) // Check to make sure an invoice ID is present
				throw new Exception('getInvoiceDetailsViaOrderDBID: No order database id was passed');
			
			// Get invoice details from db
			$invoiceResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}invoices WHERE order_id = '{$orderDBID}'");
			if($invoiceRows = mysqli_num_rows($invoiceResult))
			{	
				$invoice = mysqli_fetch_assoc($invoiceResult);
				$this->invoiceID = $invoice['invoice_id']; // Set the invoice id
				if(function_exists('orderPaymentNumToText')) $invoice['paymentStatusLang'] = orderPaymentNumToText($invoice['payment_status']); // Get payment status language tag
			
				// Get real names
				$invoice['ship_state'] = getStateName($invoice['ship_state']); //
				$invoice['ship_country'] = getCountryName($invoice['ship_country']); //
				$invoice['bill_state'] = getStateName($invoice['bill_state']); //
				$invoice['bill_country'] = getCountryName($invoice['bill_country']); //
			
				return $invoice;
			}
			else
				return false;
		}
		
		/*
		* Get invoice details
		*/
		public function getInvoiceDetails($invoiceID='')
		{
			global $db;
			
			if($invoiceID) // Set the order id
				$this->invoiceID = $invoiceID;
			
			if(!$this->invoiceID) // Check to make sure an invoice ID is present
				throw new Exception('getInvoiceDetails: No invoice id - Must call setInvoiceID first');
			
			// Get invoice details from db
			$invoiceResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}invoices WHERE invoice_id = '{$this->invoiceID}'");
			if($invoiceRows = mysqli_num_rows($invoiceResult))
			{
				$invoice = mysqli_fetch_assoc($invoiceResult);
				$invoice['paymentStatusLang'] = orderPaymentNumToText($invoice['payment_status']); // Get payment status language tag
				
				// Get real names
				$invoice['ship_state'] = getStateName($invoice['ship_state']); //
				$invoice['ship_country'] = getCountryName($invoice['ship_country']); //
				$invoice['bill_state'] = getStateName($invoice['bill_state']); //
				$invoice['bill_country'] = getCountryName($invoice['bill_country']); //
				
				return $invoice;
			}
			else
				return false;
		}
		
		/*
		* Query all items in an invoice and create an array for invoiceItems
		* Return the number of items
		*/
		public function queryInvoiceItems($invoiceID='')
		{
			global $db;
			
			if($invoiceID) // Set the invoice id
				$this->invoiceID = $invoiceID;
			
			//return 'test';
				
			if(!$this->invoiceID) // Check to make sure an invoice ID is present
				throw new Exception('queryInvoiceItems: No invoice id is set - Call setInvoiceID first or pass invoice id in function');
			
			// Get invoice items from db
			$invoiceItemsResult = mysqli_query($db,
			 	"
				SELECT * FROM {$this->dbinfo[pre]}invoice_items 
				WHERE invoice_id = '{$this->invoiceID}'
				AND deleted = 0 
				AND pack_invoice_id = 0
				"
			);
			$invoiceItemsRows = mysqli_num_rows($invoiceItemsResult);
			if($invoiceItemsRows)
			{
				while($invoiceItem = mysqli_fetch_assoc($invoiceItemsResult))
					$this->invoiceItems[$invoiceItem['oi_id']] = $invoiceItem; // Array with all invoice items
				return $invoiceItemsRows;
			}
			else
				return false;
		}
		
		/*
		* Find a list of orders for bill
		* Return the number of orders
		
		public function queryOrderAsItems($billID='')
		{
			global $db;
			
			if($invoiceID) // Set the invoice id
				$this->invoiceID = $invoiceID;
				
			if(!$this->invoiceID) // Check to make sure an invoice ID is present
				throw new Exception('queryInvoiceItems: No invoice id is set - Call setInvoiceID first or pass invoice id in function');
			
			// Get invoice items from db
			$invoiceItemsResult = mysqli_query($db,
			 	"
				SELECT * FROM {$this->dbinfo[pre]}invoice_items 
				WHERE invoice_id = '{$this->invoiceID}'
				AND deleted = 0 
				AND pack_invoice_id = 0
				"
			);
			$invoiceItemsRows = mysqli_num_rows($invoiceItemsResult);
			if($invoiceItemsRows)
			{
				while($invoiceItem = mysqli_fetch_assoc($invoiceItemsResult))
					$this->invoiceItems[$invoiceItem['oi_id']] = $invoiceItem; // Array with all invoice items
				return $invoiceItemsRows;
			}
			else
				return false;
		}
		*/
		
		/*
		* Return an array of all invoice items without any processing
		*/
		public function getInvoiceItemsRaw()
		{
			if($this->invoiceItems)
				return $this->invoiceItems;
			else
				return false;
		}
		
		/*
		* Return an array of all only digital invoice items without any processing
		*/
		public function getDigitalItemsRaw()
		{	
			if($this->invoiceItems)
			{
				foreach($this->invoiceItems as $itemKey => $invoiceItem)
				{
					if($invoiceItem['item_type'] == 'digital' or $invoiceItem['item_type'] == 'collection')
						$this->digitalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;
				}
				return $this->digitalInvoiceItems;
			}	
			else
				return false;
		}
		
		/*
		* Return an array of only physical invoice items without any processing
		*/
		public function getPhysicalItemsRaw()
		{		
			if($this->invoiceItems)
			{
				foreach($this->invoiceItems as $itemKey => $invoiceItem)
				{
					if($invoiceItem['item_type'] != 'digital' and $invoiceItem['item_type'] != 'collection')
						$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;
				}
				return $this->physicalInvoiceItems;
			}	
			else
				return false;
		}
		
		/*
		* Get all invoice items
		*/
		function getAllInvoiceItems()
		{
			$physicalItems = $this->getPhysicalItems();
			$digitalItems = $this->getDigitalItems();
			
			if($digitalItems and $physicalItems)
				$invoiceItems = array_merge($digitalItems,$physicalItems); // Both digital and physical exist - combine the arrays
			else
			{
				if($physicalItems)
					$invoiceItems = $physicalItems; // Only physical items exist
					
				if($digitalItems)
					$invoiceItems = $digitalItems; // Only digital items exist
			}
			return $invoiceItems;
		}
		
		/*
		* Get the options for a single item
		*/
		public function getOnlyItemOptions($itemType,$itemID)
		{
			if(!$itemType or !$itemID)
				throw new Exception('getItemOptions: itemType or itemID not passed');
			
			$this->getItemOptions($itemType,$itemID);
			
			if($itemType == 'digital' or $itemType == 'collection')
			{
				return $this->digitalInvoiceItems;
			}
			else
			{
				//$this->physicalInvoiceItems[$itemID]['tester'] = 'testing';
				return $this->physicalInvoiceItems;
			}
		}
		
		/*
		* Get an array of options for this item
		*/
		public function getItemOptions($itemType,$itemID)
		{
			global $dbinfo, $config, $db;
			
			if(!$itemType or !$itemID)
				throw new Exception('getItemOptions: itemType or itemID not passed');
			
			if($itemType == 'package') // Select package items and then options
			{
				$packageItemsResult = mysqli_query($db,
					"
					SELECT * 
					FROM {$dbinfo[pre]}invoice_items 
					WHERE pack_invoice_id = '{$itemID}'
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
										$product['media'] = getMediaDetailsForCart($packageItem['asset_id']); // Get correct media thumbnail details
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
												
												$optionGroupName = $optionGroup['name']; // xxxxxxxxxx Get correct language
												
												$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
												
												$productOption['groupName'] = $optionGroupName;
											}
											
											// xxxx get the correct language name
											$packageProductOptionsArray[] = $productOption;
										}
										
										$product['options'] = $packageProductOptionsArray;
									}
									else
										$product['options'] = false;
									
									$packageProductsArray[] = productsList($product,$packageItem['asset_id']);
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
										$print['media'] = getMediaDetailsForCart($packageItem['asset_id']); // Get correct media thumbnail details
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
												
												$optionGroupName = $optionGroup['name']; // xxxxxxxxxx Get correct language
												
												$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
												
												$printOption['groupName'] = $optionGroupName;
											}
											
											// xxxx get the correct language name
											$packagePrintOptionsArray[] = $printOption;
										}
										
										$print['options'] = $packagePrintOptionsArray;
									}
									else
										$print['options'] = false;
									
									$packagePrintsArray[] = printsList($print,$packageItem['asset_id']);
									
									
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
									
									// Add additional properties only if this is an order details request
									if($this->orderInfo)
									{
										//$collectionID = $packageItem['item_id'];
										//$uorderID = $this->orderInfo['uorder_id'];
										//$invoiceItemID = $collection['oi_id'];
										
										if($config['EncryptIDs']) // Encrypt IDs
										{
											$collectionID = k_encrypt($packageItem['item_id']); // Collection ID
											$uorderID = k_encrypt($this->orderInfo['uorder_id']); // Order ID
											$invoiceItemID = k_encrypt($collection['oi_id']); // Invoice Item ID
										}
										else
										{
											$collectionID = $packageItem['item_id']; // Collection ID
											$uorderID = $this->orderInfo['uorder_id']; // Order ID
											$invoiceItemID = $collection['oi_id']; // Invoice Item ID
										}
										
										$collection['downloadableStatus'] = 5; // Collection
										$collection['downloadKey'] = k_encrypt("collectionID={$collectionID}&uorderID={$uorderID}&invoiceItemID={$invoiceItemID}"); //k_encrypt 
									}
									
									$packageCollectionsArray[] = collectionsList($collection,$packageItem['asset_id']);
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
									
									$packageSubscriptionsArray[] = subscriptionsList($subscription,$packageItem['asset_id']);
								}
							break;
						}
						
					}
					
					$this->physicalInvoiceItems[$itemID]['packagePrintsArray'] = $packagePrintsArray;
					$this->physicalInvoiceItems[$itemID]['packageProductsArray'] = $packageProductsArray;
					$this->physicalInvoiceItems[$itemID]['packageCollectionsArray'] = $packageCollectionsArray;
					$this->physicalInvoiceItems[$itemID]['packageSubscriptionsArray'] = $packageSubscriptionsArray;
					$this->physicalInvoiceItems[$itemID]['packageItemsRows'] = $packageItemsRows;
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
					WHERE {$dbinfo[pre]}invoice_options.invoice_item_id = '{$itemID}'
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
							
							$optionGroupName = $optionGroup['name']; // xxxxxxxxxx Get correct language
							
							$optionGroupNames[$optionGroup['og_id']] = $optionGroupName; // Add to array to prevent having to get the group every time
							
							$option['groupName'] = $optionGroupName;
						}
						
						// xxxx get the correct language name
						$optionsArray[] = $option;
						
						if($itemType == 'digital' or $itemType == 'collection')
							$this->digitalInvoiceItems[$itemID]['optionsArray'] = $optionsArray;
						else
							$this->physicalInvoiceItems[$itemID]['optionsArray'] = $optionsArray;
					}
				}
				else
					return false;
			}
		}
		
		/*
		public function assignItemOptions()
		{	
			if(!$this->physicalInvoiceItems and !$this->digitalInvoiceItems)
				throw new Exception('assignItemOptions: No invoice items are present');
			
			foreach($this->physicalInvoiceItems as $physicalItemKey => $physicalItem)
			{
				$this->physicalInvoiceItems[$physicalItemKey]['itemOptions'] = $this->getItemOptions($this->physicalInvoiceItems[$physicalItemKey]['item_type'],$physicalItemKey);
			}
			
			foreach($this->digitalInvoiceItems as $digitalItemKey => $digitalItem)
			{
				$this->digitalInvoiceItems[$digitalItemKey]['itemOptions'] = $this->getItemOptions($this->digitalInvoiceItems[$digitalItemKey]['item_type'],$digitalItemKey);
			}
		}
		*/
		
		/*
		* Return an array of only physical invoice items without any processing
		*/
		public function getPhysicalItems()
		{
			global $lang, $config;			
			global $db;
			if($this->invoiceItems)
			{
				foreach($this->invoiceItems as $itemKey => $invoiceItem)
				{
					switch($invoiceItem['item_type'])
					{
						case 'print':
							$printResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}prints 
								WHERE print_id = '{$invoiceItem[item_id]}'
								"
							); // Select print
							$print = mysqli_fetch_assoc($printResult);							
							$printDetails = printsList($print,$invoiceItem['asset_id']);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $printDetails;
							$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$invoiceItem['itemTypeShort'] = 'print';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;
							
						break;
						case 'product':
							$productResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}products 
								WHERE prod_id = '{$invoiceItem[item_id]}'
								"
							); // Select product here
							$product = mysqli_fetch_assoc($productResult);
							
							$productDetails = productsList($product,$invoiceItem['asset_id']);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $productDetails;
							
							if($product['product_type'] == 1) // Check to see if this is a media based product before finding the media thumb
								$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$invoiceItem['itemTypeShort'] = 'prod';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$bPP = is_photo_puzzle($invoiceItem['item_id']); //FP 
							if($bPP) {
								$query = tep_db_query("SELECT * FROM ".TABLE_PHOTO_PUZZLE." WHERE photo_puzzle_id = ".$invoiceItem['asset_id']."");
			        			$aCurrent = tep_db_fetch_array($query);
								$photoPuzzleId = $aCurrent['photo_puzzle_id'];
								
				        		$aBoxFile = pathinfo($aCurrent['box_file']);
				        		$boxFile = $aBoxFile['basename'];
				        		if(!isset($aBoxFile['extension'])) $boxFile = $boxFile.'.png'; 
								
								$img = WEB_DIR.$aCurrent['sesskey'].'/'.$aCurrent['photo_puzzle_id'].'/boxes/'.$boxFile;
								$invoiceItem['photoPuzzleImg'] = $img;
								
								//foreach ( $physicalInvoiceItems as $k => $array ){
									//var_dump($invoiceItem);
									$invoiceOptions = $this->getOnlyItemOptions('product', $invoiceItem['oi_id']);
									foreach ($invoiceOptions[$invoiceItem['oi_id']]['optionsArray'] as $i => $aOpzione) {
										//var_dump($aOpzione);
										$invoiceItem['photoPuzzleGruppoOpzione'][] = $aOpzione['groupName'];
										$invoiceItem['photoPuzzleOpzione'][] = $aOpzione['name'];
									}
								//}
								//var_dump($physicalInvoiceItems[$invoiceItem['oi_id']]);
							}

							$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;							
						break;
						case 'package':
							$packageResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}packages 
								WHERE pack_id = '{$invoiceItem[item_id]}'
								"
							); // Select package here
							$package = mysqli_fetch_assoc($packageResult);
							
							$packageDetails = packagesList($package,0);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $packageDetails;
							//$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$invoiceItem['itemTypeShort'] = 'pack';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;							
						break;
						case 'subscription':
							$subscriptionResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}subscriptions 
								WHERE sub_id = '{$invoiceItem[item_id]}'
								"
							); // Select subscription here
							$subscription = mysqli_fetch_assoc($subscriptionResult);							
							$subscriptionDetails = subscriptionsList($subscription,0);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $subscriptionDetails;
							//$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$invoiceItem['itemTypeShort'] = 'sub';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;							
						break;
						case "credits":
							$creditsResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}credits 
								WHERE credit_id = '{$invoiceItem[item_id]}'
								"
							); // Select credits here
							$credits = mysqli_fetch_assoc($creditsResult);
							
							$creditsDetails = creditsList($credits,0);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $creditsDetails;
							//$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							//$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$invoiceItem['itemTypeShort'] = 'credit';
							
							$this->physicalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;							
						break;
					}
						
					if($this->options) // Add Options to the array
						$this->getItemOptions($invoiceItem['item_type'],$invoiceItem['oi_id']);	
				}
				return $this->physicalInvoiceItems;
			}
			else
				return false;
		}
		
		/*
		* Return an array of all only digital invoice items after processing
		*/
		public function getDigitalItems()
		{
			global $lang, $config;
			global $db;
			if($this->invoiceItems)
			{
				foreach($this->invoiceItems as $itemKey => $invoiceItem)
				{
					switch($invoiceItem['item_type'])
					{
						case 'digital':
							$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->dbinfo[pre]}media WHERE media_id = '{$invoiceItem['asset_id']}'";
							$mediaInfo = new mediaList($sql);
							
							if($mediaInfo->getRows())
								$media = $mediaInfo->getSingleMediaDetails('thumb');
							
							/*
							if($invoiceItem['item_id'] == 0)
							{
								$digital['ds_id'] = 0;
								$digital['width'] = $media['width'];
								$digital['height'] = $media['height'];
								$digital['format'] = $media['format'];
								$digital['license'] = $media['license'];
								$digital['name'] = $lang['original'];
								
								// License type and name
								switch($media['license'])
								{
									case "cu": // Contact us
										$digital['licenseLang'] = 'mediaLicenseCU';
									break;
									case "rf": // Royalty Free
										$digital['licenseLang'] = 'mediaLicenseRF';
									break;
									case "rm": // Rights Managed
										$digital['licenseLang'] = 'mediaLicenseRM';
									break;
									case "fr": // Free Download
										$digital['licenseLang'] = 'mediaLicenseFR';
									break;						
								}
								
								// File/profile type
								switch($media['dsp_type'])
								{
									case "photo":
										// Get print sizes
										if($config['digitalSizeCalc'] == 'i')
										{
											$digital['widthIC'] = round($media['width']/$config['dpiCalc'],1).'"';
											$digital['heightIC'] = round($media['height']/$config['dpiCalc'],1).'"';
										}
										else
										{
											$digital['widthIC'] = round(($media['width']/$config['dpiCalc']*2.54),1).'cm';
											$digital['heightIC'] = round(($media['height']/$config['dpiCalc']*2.54),1).'cm';
										}
									break;
									case "video":
										// Print sizes not needed
									break;
									case "other":
										// Print sizes not needed
									break;
								}
							}
							else
							{							
								$digitalResult = mysqli_query($db,
									"
									SELECT * FROM {$dbinfo[pre]}digital_sizes 
									WHERE ds_id = '{$invoiceItem[item_id]}'
									"
								); // Select digital profile here
								$digital = mysqli_fetch_assoc($digitalResult);
	
								// If real_sizes is set then calculate the real width and height of this size after it is scaled from the original
								if($digital['real_sizes'])
								{
									// Landscape
									if($media['width'] >= $media['height'])
									{
										$scaleRatio = $digital['width']/$media['width'];									
										$width = $digital['width'];
										$height = round($media['height']*$scaleRatio);
									}
									// Portrait
									else
									{
										$scaleRatio = $digital['height']/$media['height'];									
										$width = round($media['width']*$scaleRatio);
										$height = $digital['height'];
									}
								}
								else
								{
									$width = $digital['width'];
									$height = $digital['height'];	
								}
								
								$digital['width'] = $width;
								$digital['height'] = $height;
								
								// License type and name
								switch($digital['license'])
								{
									case "cu": // Contact us
										$digital['licenseLang'] = 'mediaLicenseCU';
									break;
									case "rf": // Royalty Free
										$digital['licenseLang'] = 'mediaLicenseRF';
									break;
									case "rm": // Rights Managed
										$digital['licenseLang'] = 'mediaLicenseRM';
									break;
									case "fr": // Free Download
										$digital['licenseLang'] = 'mediaLicenseFR';
									break;						
								}
								
								// File/profile type
								switch($digital['dsp_type'])
								{
									case "photo":
										if($config['digitalSizeCalc'] == 'i')
										{
											$digital['widthIC'] = round($width/$config['dpiCalc'],1).'"';
											$digital['heightIC'] = round($height/$config['dpiCalc'],1).'"';
										}
										else
										{
											$digital['widthIC'] = round(($width/$config['dpiCalc']*2.54),1).'cm';
											$digital['heightIC'] = round(($height/$config['dpiCalc']*2.54),1).'cm';
										}
									break;
									case "video":										
									break;
									case "other":										
									break;
								}
	
							}
							*/
							
							$digital = digitalPrep($invoiceItem['item_id'],$media);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
							
							$digitalDetails = digitalsList($digital,$mediaID);
							
							$invoiceItem['itemDetails'] = $digitalDetails;
							$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							if($this->includeGalleries)
							{
								$invoiceItem['itemDetails']['media']['galleriesHTML'] = getMediaGalleries($invoiceItem['asset_id'],true);
							}
							
							$invoiceItem['itemTypeShort'] = 'digital';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$this->digitalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;
						break;
						case 'collection':
							$collectionResult = mysqli_query($db,
								"
								SELECT * FROM {$this->dbinfo[pre]}collections 
								WHERE coll_id = '{$invoiceItem[item_id]}'
								"
							); // Select collection here
							$collection = mysqli_fetch_assoc($collectionResult);							
							$collectionDetails = collectionsList($collection,0);
							
							if(!$invoiceItem['paytype'])
								$invoiceItem['paytype'] = 'cur'; // Make sure the payType is set just in case
								
							$invoiceItem['itemDetails'] = $collectionDetails;
							//$invoiceItem['itemDetails']['media'] = getMediaDetailsForCart($invoiceItem['asset_id']);
							
							$invoiceItem['itemTypeShort'] = 'coll';
							
							$parms['noDefault'] = true;
							$invoiceItem['lineItemPriceTotalLocal'] = getCorrectedPrice($invoiceItem['price_total'],$parms);
							$invoiceItem['lineItemCreditsTotal'] = $invoiceItem['credits_total'];
							
							$this->digitalInvoiceItems[$invoiceItem['oi_id']] = $invoiceItem;							
						break;
					}
					
					if($this->options) // Add Options to the array
						$this->getItemOptions($invoiceItem['item_type'],$invoiceItem['oi_id']);
					
				}
				return $this->digitalInvoiceItems;
			}
			else
				return false;
		}
	}
?>