<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'accountid', '');
			definegatewayfield($gatewaymodule['id'], 'accountkey', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
			definegatewayfield($gatewaymodule['id'], 'testid', '');
			definegatewayfield($gatewaymodule['id'], 'testkey', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['paygate_f_accountid'],$lang['paygate_f_accountid_d'],'textbox',1);
			$input[] = gateway_input('accountkey',$data['accountkey'],$lang['paygate_f_accountkey'],$lang['paygate_f_accountkey_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['paygate_f_testmode'],$lang['paygate_f_testmode_d'],'checkbox',0);
			$input[] = gateway_input('testid',$data['testid'],$lang['paygate_f_testid'],$lang['paygate_f_testid_d'],'textbox',0);
			$input[] = gateway_input('testkey',$data['testkey'],$lang['paygate_f_testkey'],$lang['paygate_f_testkey_d'],'textbox',0);
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
				orderNumber				- order number created for this order
				uniqueOrderID			- unique id for this order
				cartID						- database id of this order
				invoiceID					- database id of the invoice
				cartItemRows			- number of items in the cart
				
				Shipping info array items ($shippingAddress)
				countryID					- shipping country id from the db
				country						- shipping country name
				name							- shipping name
				firstName					- shipping first name
				lastName					- shipping last name
				address						- shipping address
				address2					- shipping address line 2
				city							- shipping city
				stateID						- shipping state id from the db
				postalCode				- shipping postal or zip code
				email							- shipping email address
				phone							- shipping phone
				
				Billing info array items ($billingAddress)
				countryID					- billing country id from the db
				country						- billing country name
				name							- billing name
				firstName					- billing first name
				lastName					- billing last name
				address						- billing address
				address2					- billing address line 2
				city							- billing city
				stateID						- billing state id from the db
				postalCode				- billing postal or zip code
				email							- billing email address
				phone							- billing phone
				
				Currency info array items ($currency)
				currency_id				- database id of the currency selected
				name 							- actual name of the currency selected
				code							- code of the currency selected
				denotation 				- denotation used by the currency
			*/
			
			$date = date('Y-m-d h:i');
			
			$formSubmitURL 			= "https://www.paygate.co.za/paywebv2/process.trans";
			if($gatewaySetting['testmode']) // Testing mode
			{
				$formData['PAYGATE_ID'] 	= ($gatewaySetting['testid']) ? $gatewaySetting['testid'] : $gatewaySetting['accountid']; // Use regular accountID if testID is not entered
				$key = $gatewaySetting['testkey'];
				$account = $gatewaySetting['testid'];
			}
			else
			{
				$formData['PAYGATE_ID'] 	= $gatewaySetting['accountid'];
				$key = $gatewaySetting['accountkey'];
				$account = $gatewaySetting['accountid'];
			}
			$formData['REFERENCE'] 				= $cartInfo['uniqueOrderID']; // Unique Order number
			$formData['AMOUNT'] 					= round($cartTotals['cartGrandTotal'] * 100,2); // Total minus any discounts FOR PAYGET THIS TOTAL MUST NOT HAVE A DECIMAL SO WE TIMES BY 100 AND THEN ROUND IT
			$formData['CURRENCY'] 				= $currency['code']; // The currency code of the checkout
			$formData['RETURN_URL'] 			= "{$config[settings][site_url]}/assets/gateways/paygate/ipn.php"; // Page to notify on payment approval
			$formData['TRANSACTION_DATE'] = $date;
			$formData['CHECKSUM'] 				= md5($account."|".$cartInfo['uniqueOrderID']."|".$cartTotals['cartGrandTotal']."|".$currency['code']."|{$config[settings][site_url]}/assets/gateways/paygate/ipn.php|".$date."|".$key);
		break;
	}
?>