<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'installid', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('installid',$data['installid'],$lang['worldpay_f_installid'],$lang['worldpay_f_installid_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['worldpay_f_testmode'],$lang['worldpay_f_testmode_d'],'checkbox',0);
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
			
			
		if($gatewaySetting['testmode']) // Testing mode
			{
				$formSubmitURL				= "https://select-test.worldpay.com/wcc/purchase";
				$formData['testMode'] = "100"; // anything greater than 0 is yes, and 0 is no
				$formData['name'] 		= "AUTHORISED";
			} else {
				$formSubmitURL 				= "https://secure.worldpay.com/wcc/purchase";
			}
			
			$formData['instId'] 		= $gatewaySetting['installid'];
			$formData['cartId'] 		= $cartInfo['uniqueOrderID'];
			$formData['amount'] 		= $cartTotals['cartGrandTotal'];
			$formData['currency'] 	= $currency['code'];
			$formData['desc'] 			= $config['settings']['site_title']." ".$lang['order'];
			$formData['email']    	= $billingAddress['email'];
			$formData['name'] 			= $billingAddress['name'];
			$formData['address1'] 	= $billingAddress['address'];
			$formData['address2'] 	= $billingAddress['address2'];
			$formData['town'] 			= $billingAddress['city'];
			$formData['region'] 		= $billingAddress['stateID'];
			$formData['postcode'] 	= $billingAddress['postalCode'];
			$formData['country']  	= $billingAddress['countryID'];
			$formData['tel']    		= $billingAddress['phone'];
		break;
	}
?>