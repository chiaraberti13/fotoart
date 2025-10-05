<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'email', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
			definegatewayfield($gatewaymodule['id'], 'testemail', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('email',$data['email'],$lang['paypal_f_email'],$lang['paypal_f_email_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['paypal_f_testmode'],$lang['paypal_f_testmode_d'],'checkbox',0);
			$input[] = gateway_input('testemail',$data['testemail'],$lang['paypal_f_testemail'],$lang['paypal_f_testemail_d'],'textbox',0);
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
				$formSubmitURL 			= "https://www.sandbox.paypal.com/cgi-bin/webscr";
				$formData['business'] 	= ($gatewaySetting['testemail']) ? $gatewaySetting['testemail'] : $gatewaySetting['email']; // Use regular email if test email is not entered
			}
			else
			{
				$formSubmitURL 			= "https://www.paypal.com/cgi-bin/webscr";
				$formData['business'] 	= $gatewaySetting['email'];
			}
			
			$formData['charset'] 		= "utf-8";
			$formData['cmd'] 			= "_xclick";
			$formData['invoice'] 		= $uniqueOrderID; // Unique order number
			$formData['item_number'] 	= $cartInfo['orderNumber']; // Order number
			$formData['rm'] 			= "2";			
			$formData['item_name'] 		= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['amount'] 		= $cartTotals['subtotalMinusDiscounts']; // Total minus any discounts
			$formData['upload'] 		= "1";
			$formData['shipping'] 		= $cartTotals['shippingTotal']; // Shipping total
			$formData['currency_code'] 	= $currency['code']; // The currency code of the checkout
			$formData['tax'] 			= $cartTotals['taxTotal']; // Tax total
			$formData['return'] 		= "{$config[settings][site_url]}/pay.return.php?orderID={$uniqueOrderID}"; // Page to return to after payment is made
			$formData['cancel_return'] 	= "{$config[settings][site_url]}"; // Page to return to if payment is cancelled
			$formData['notify_url'] 	= "{$config[settings][site_url]}/assets/gateways/paypal/ipn.php"; // Page to notify on payment approval
			$formData['bn'] 			= "Ktoolsnet_SP";
			
			$formData['email'] 			= $billingAddress['email'];
			$formData['first_name'] 	= $billingAddress['firstName'];
			$formData['last_name'] 		= $billingAddress['lastName'];
			$formData['address1'] 		= $billingAddress['address'];
			$formData['address2'] 		= $billingAddress['address2'];
			$formData['city'] 			= $billingAddress['city'];
			$formData['state'] 			= $billingAddress['state'];
			$formData['zip'] 			= $billingAddress['postalCode'];
			
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