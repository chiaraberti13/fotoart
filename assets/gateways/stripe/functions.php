<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'skey', '');
			definegatewayfield($gatewaymodule['id'], 'pkey', '');
			//definegatewayfield($gatewaymodule['id'], 'testmode', '');
			//definegatewayfield($gatewaymodule['id'], 'testemail', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('skey',$data['skey'],$lang['stripe_skey'],$lang['stripe_skey_d'],'textbox',1); // Secret Key
			$input[] = gateway_input('pkey',$data['pkey'],$lang['stripe_pkey'],$lang['stripe_pkey_d'],'textbox',1); // Publishable Key			
		break;
		
		case "publicForm":

			$formData['uniqueOrderID'] 	= $uniqueOrderID; // Unique order number
			$formData['invoiceNumber'] 	= $cartInfo['orderNumber']; // Order number
			$formData['description'] 	= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['amount'] 		= $cartTotals['cartGrandTotal'];
			$formData['shipping'] 		= $cartTotals['shippingTotal']; // Shipping total
			$formData['currency_code'] 	= $currency['code']; // The currency code of the checkout
			$formData['tax'] 			= $cartTotals['taxTotal']; // Tax total
			$formData['email'] 			= $billingAddress['email'];
			$formData['first_name'] 	= $billingAddress['firstName'];
			$formData['last_name'] 		= $billingAddress['lastName'];
			$formData['address1'] 		= $billingAddress['address'];
			$formData['address2'] 		= $billingAddress['address2'];
			$formData['city'] 			= $billingAddress['city'];
			$formData['state'] 			= $billingAddress['state'];
			$formData['zip'] 			= $billingAddress['postalCode'];			

		break;
	}
?>