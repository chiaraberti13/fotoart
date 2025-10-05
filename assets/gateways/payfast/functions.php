<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'merchantid', '');
			definegatewayfield($gatewaymodule['id'], 'merchantkey', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('merchantid',$data['merchantid'],$lang['payfast_merchantid'],$lang['payfast_merchantid_d'],'textbox',1);
			$input[] = gateway_input('merchantkey',$data['merchantkey'],$lang['payfast_merchantkey'],$lang['payfast_merchantkey_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['payfast_testmode'],$lang['payfast_testmode_d'],'checkbox',0);
		break;
		
		case "publicForm":
			/* 
				Cart totals array items ($cartTotals)
				shippingTotal 			- total cost of shipping
				cartGrandTotal			- grand total including shipping, taxes, subtotal and discounts
				taxTotal 				- total of all taxes charged
				subtotalMinusDiscounts 	- subtotal minus any discounts
				priceSubTotal 			- subtotal before any discounts, taxes or shipping
				taxablePrice 			- total of taxable items
				shippableTotal 			- total of shippable items
				shippableWeight 		- total shipping weight
				taxA 					- total for tax A 
				taxB					- total for tax B
				taxC					- total for tax C
				
				Cart info array items ($cartInfo)
				orderNumber				- order number created for this order
				uniqueOrderID			- unique id for this order
				cartID					- database id of this order
				invoiceID				- database id of the invoice
				cartItemRows			- number of items in the cart
				
				Shipping info array items ($shippingAddress)
				countryID				- shipping country id from the db
				country					- shipping country name
				name					- shipping name
				firstName				- shipping first name
				lastName				- shipping last name
				address					- shipping address
				address2				- shipping address line 2
				city					- shipping city
				stateID					- shipping state id from the db
				postalCode				- shipping postal or zip code
				email					- shipping email address
				phone					- shipping phone
				
				Billing info array items ($billingAddress)
				countryID				- billing country id from the db
				country					- billing country name
				name					- billing name
				firstName				- billing first name
				lastName				- billing last name
				address					- billing address
				address2				- billing address line 2
				city					- billing city
				stateID					- billing state id from the db
				postalCode				- billing postal or zip code
				email					- billing email address
				phone					- billing phone
				
				Currency info array items ($currency)
				currency_id				- database id of the currency selected
				name 					- actual name of the currency selected
				code					- code of the currency selected
				denotation 				- denotation used by the currency
			*/
			
			if($gatewaySetting['testmode']) // Testing mode
			{
				$formSubmitURL 			= "https://sandbox.payfast.co.za/eng/process";
			}
			else
			{
				$formSubmitURL 			= "https://www.payfast.co.za/eng/process";
			}
			
			//ACCOUNT DETAILS AND URL'S
			$formData['merchant_id'] 		= $gatewaySetting['merchantid'];
			$formData['merchant_key'] 	= $gatewaySetting['merchantkey'];
			$formData['return_url'] 		= "{$config[settings][site_url]}/pay.return.php?orderID={$uniqueOrderID}"; // Page to return to after payment is made
			$formData['cancel_url'] 		= "{$config[settings][site_url]}"; // Page to return to if payment is cancelled
			$formData['notify_url'] 		= "{$config[settings][site_url]}/assets/gateways/payfast/ipn.php"; // Page to notify on payment approval
			//PAYER DETAILS
			$formData['name_first'] 		= $billingAddress['firstName'];
			$formData['name_last'] 			= $billingAddress['lastName'];
			$formData['email_address'] 	= $billingAddress['email'];
			//TRANSACTION DETAILS
			$formData['m_payment_id'] 	= $uniqueOrderID; // Unique order number
			$formData['amount'] 				= $cartTotals['cartGrandTotal']; // Overall Total
			$formData['item_name'] 			= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			//$formData['custom_str1'] 		= $cartInfo['orderNumber']; // Order number
			
			//SECURITY MD5 HASH OF DATA
			$hash = md5("merchant_id=".urlencode($gatewaySetting['merchantid'])."&merchant_key=".urlencode($gatewaySetting['merchantkey'])."&return_url=".urlencode($config['settings']['site_url']."/pay.return.php?orderID=".$uniqueOrderID)."&cancel_url=".urlencode($config['settings']['site_url'])."&notify_url=".urlencode($config['settings']['site_url']."/assets/gateways/payfast/ipn.php")."&name_first=".urlencode($billingAddress['firstName'])."&name_last=".urlencode($billingAddress['lastName'])."&email_address=".urlencode($billingAddress['email'])."&m_payment_id=".urlencode($uniqueOrderID)."&amount=".urlencode($cartTotals['cartGrandTotal'])."&item_name=".urlencode($config['settings']['site_title']." ".$lang['order']));
			$formData['signature'] 		= $hash; // MD5 HASH OF DETAILS ABOVE
		break;
	}
?>