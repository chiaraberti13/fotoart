<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'apiid', '');
			definegatewayfield($gatewaymodule['id'], 'transkey', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('apiid',$data['apiid'],$lang['authorize_f_apiid'],$lang['authorize_f_apiid_d'],'textbox',1);
			$input[] = gateway_input('transkey',$data['transkey'],$lang['authorize_f_transkey'],$lang['authorize_f_transkey_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['authorize_f_testmode'],$lang['authorize_f_testmode_d'],'checkbox',0);
		break;
		
		case "publicForm":
		
			/* 
				Cart totals array items ($cartTotals)
				shippingTotal 					- total cost of shipping
				cartGrandTotal					- grand total including shipping, taxes, subtotal and discounts
				taxTotal 								- total of all taxes charged
				subtotalMinusDiscounts 	- subtotal minus any discounts
				priceSubTotal 					- subtotal before any discounts, taxes or shipping
				taxablePrice 						- total of taxable items
				shippableTotal 					- total of shippable items
				shippableWeight 				- total shipping weight
				taxA 										- total for tax A 
				taxB										- total for tax B
				taxC										- total for tax C
				
				Cart info array items ($cartInfo)
				orderNumber			- order number created for this order
				uniqueOrderID		- unique id for this order
				cartID					- database id of this order
				invoiceID				- database id of the invoice
				cartItemRows		- number of items in the cart
				
				Shipping info array items ($shippingAddress)
				countryID				- shipping country id from the db
				country					- shipping country name
				name						- shipping name
				firstName				- shipping first name
				lastName				- shipping last name
				address					- shipping address
				address2				- shipping address line 2
				city						- shipping city
				stateID					- shipping state id from the db
				postalCode			- shipping postal or zip code
				email						- shipping email address
				phone						- shipping phone
				
				Billing info array items ($billingAddress)
				countryID				- billing country id from the db
				country					- billing country name
				name						- billing name
				firstName				- billing first name
				lastName				- billing last name
				address					- billing address
				address2				- billing address line 2
				city						- billing city
				stateID					- billing state id from the db
				postalCode			- billing postal or zip code
				email						- billing email address
				phone						- billing phone
				
				Currency info array items ($currency)
				currency_id			- database id of the currency selected
				name 						- actual name of the currency selected
				code						- code of the currency selected
				denotation 			- denotation used by the currency
			*/
			
			//VARIABLES ONLY SPECIFIC TO AUTHORIZE.NET
			// Transaction Key
			$trans_key = $gatewaySetting['transkey'];
			// a sequence number is randomly generated
			$sequence	= rand(1, 1000);
			// a timestamp is generated
			$timestamp	= time();
			// The following lines generate the SIM fingerprint.  PHP versions 5.1.2 and
			// newer have the necessary hmac function built in.  For older versions, it
			// will try to use the mhash library.
			if(function_exists(hash_hmac)){
				$fingerprint = hash_hmac("md5",$gatewaySetting['apiid']."^".$sequence."^".$timestamp."^".$cartTotals['cartGrandTotal']."^",$gatewaySetting['transkey']);
			} else {
				if(function_exists(mhash)){
					$fingerprint = bin2hex(mhash(MHASH_MD5,$gatewaySetting['apiid']."^".$sequence."^".$timestamp."^".$cartTotals['cartGrandTotal']."^",$gatewaySetting['transkey'])); 
				} else {
					echo "Sorry but we can't generate a fingerprint on this server, your PHP must have either hash_hmac or mhash functions to do this";
				}
			}
			
			if($gatewaySetting['testmode']) // Testing mode
			{
				$formSubmitURL 			= "https://test.authorize.net/gateway/transact.dll";
			}
			else
			{
				$formSubmitURL 			= "https://secure.authorize.net/gateway/transact.dll";
			}
			
			$formData['x_login'] 			= $gatewaySetting['apiid'];
			$formData['x_amount'] 		= $cartTotals['cartGrandTotal']; // Unique order number
			$formData['x_description'] 		= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['x_invoice_num'] 	= $cartInfo['orderNumber']; // Order number
			$formData['x_uniqueorderid'] = $cartInfo['uniqueOrderID']; // Order number
			$formData['x_fp_sequence'] 			= $sequence;
			$formData['x_fp_timestamp'] 			= $timestamp;
			$formData['x_fp_hash'] 		= $fingerprint;
			$formData['x_tax'] 			= $cartTotals['taxTotal']; // Tax total
			$formData['x_freight'] 		= $cartTotals['shippingTotal']; // Shipping total
			
			$formData['x_receipt_link_method'] 		= "POST";
			$formData['x_receipt_link_url'] 	= "{$config[settings][site_url]}/assets/gateways/authorize/ipn.php"; // The currency code of the checkout
			$formData['x_receipt_link_text'] 		= $lang['authorize_f_completeOrder']; // Page to return to after payment is made
			
			$formData['x_test_request'] 	= "FALSE"; // Page to return to if payment is cancelled
			$formData['x_relay_response'] 	= "TRUE"; // Page to notify on payment approval
			$formData['x_show_form'] 			= "PAYMENT_FORM";
			$formData['x_duplicate_window'] = "28800";
			
			$formData['x_first_name'] 			= $billingAddress['firstName'];
			$formData['x_last_name'] 			= $billingAddress['lastName'];
			$formData['x_address'] 	= $billingAddress['address']."-".$billingAddress['address2'];
			$formData['x_city'] 		= $billingAddress['city'];
			$formData['x_state'] 			= $billingAddress['stateID'];
			$formData['x_zip'] 			= $billingAddress['postalCode'];
			$formData['x_email']    = $billingAddress['email'];
			
			/* Not used at this time
			$data['custom'] 		= "";
			$data['day_phone_a'] 	= "";
			$data['day_phone_b'] 	= "";
			$data['day_phone_c'] 	= "";
			$data['night_phone_a'] 	= "";
			$data['night_phone_b'] 	= "";
			$data['night_phone_c'] 	= "";
			$data['day_phone_c'] 	= "";
			*/
			
		break;
	}
?>