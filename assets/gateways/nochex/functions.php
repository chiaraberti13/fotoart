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
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['nochex_f_accountid'],$lang['nochex_f_accountid_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['nochex_f_testmode'],$lang['nochex_f_testmode_d'],'checkbox',0);
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
			
			$formSubmitURL 						= "https://secure.nochex.com";
			if($gatewaySetting['testmode']) // Testing mode
			{
				$formData['test_transaction'] 	= "100"; // 100 = yes
				$formData['test_success_url']	= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php?orderID=".$cartInfo['uniqueOrderID']."&email=".$_POST['email']."&amount=".$cartTotals['cartGrandTotal']; //"{$config[settings][site_url]}/assets/gateways/nochex/ipn.php"; // Page to notify on payment approval
			}
			
			$formData['merchant_id'] 			= $gatewaySetting['accountid'];
			$formData['amount'] 				= $cartTotals['cartGrandTotal']; // Total minus any discounts
			$formData['description'] 			= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['order_id'] 				= $cartInfo['uniqueOrderID']; // Order number
			$formData['callback_url'] 			= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php"; // Page to notify on payment approval
			//$formData['responderurl'] 			= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php";
			$formData['success_url']			= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php?orderID=".$cartInfo['uniqueOrderID']."&email=".$_POST['email']."&amount=".$cartTotals['cartGrandTotal'];
			$formData['cancel_url']				= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php"; // Page to notify on payment approval
			$formData['declined_url']			= "{$config[settings][site_url]}/assets/gateways/nochex/ipn.php"; // Page to notify on payment approval
			$formData['billing_fullname'] 		= $billingAddress['name'];
			$formData['billing_address'] 		= $billingAddress['address']." ".$billingAddress['address2'];
			$formData['billing_postcode'] 		= $billingAddress['postalCode'];
			$formData['delivery_fullname'] 		= $shippingAddress['name'];
			$formData['delivery_address'] 		= $shippingAddress['address']." ".$shippingAddress['address2'];
			$formData['delivery_postcode'] 		= $shippingAddress['postalCode'];
			$formData['email_address']    		= $billingAddress['email'];
			$formData['customer_phone_number']	= $billingAddress['phone'];
			
		break;
	}
?>