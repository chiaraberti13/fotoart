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
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['2checkout_f_accountid'],$lang['2checkout_f_accountid_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['2checkout_f_testmode'],$lang['2checkout_f_testmode_d'],'checkbox',0);
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
			
		$formSubmitURL 	= "https://www2.2checkout.com/2co/buyer/purchase";
		
		if($gatewaySetting['testmode']) // Testing mode
			$formData['demo'] 	= "Y"; // Y is yes, and N is no
			
			// Info: https://www.2checkout.com/documentation/checkout/parameter-sets/pass-through-products
			
			$formData['sid'] 						= $gatewaySetting['accountid'];
			$formData['mode'] 						= '2CO';
			$formData['co_id'] 						= $cartInfo['orderNumber']; // Order Number
			$formData['merchant_order_id'] 			= $uniqueOrderID; // Order Number
			$formData['lang'] 						= "EN";
			$formData['order_id'] 					= $uniqueOrderID;			
			
			//$formData['c_prod'] 					= 1;
			//$formData['c_name'] 					= $lang['2checkout_displayName'];
			//$formData['c_description'] 			= $config['settings']['site_title']." ".$lang['order'];
			//$formData['c_price'] 					= $cartTotals['cartGrandTotal'];
			//$formData['total'] 					= $cartTotals['cartGrandTotal'];
			//$formData['shippingtotal'] 			= $cartTotals['shippingTotal'];
			//$formData['taxtotal'] 				= $cartTotals['taxTotal'];
			
			$formData['li_0_type'] 					= 'product';
			$formData['li_0_name'] 					= $config['settings']['site_title']." ".$lang['order'];
			//$formData['li_0_description'] 			= $config['settings']['site_title']." ".$lang['order'];
			$formData['li_0_quantity'] 				= 1;			
			$formData['li_0_price'] 				= $cartTotals['cartGrandTotal'];
			$formData['li_0_tangible'] 				= 'N';
			
			$formData['x_receipt_link_url'] 		= "{$config[settings][site_url]}/assets/gateways/2checkout/ipn.php";
			
			$formData['card_holder_name'] 			= $billingAddress['name'];
			$formData['street_address'] 			= $billingAddress['address'];
			$formData['street_address2'] 			= $billingAddress['address2'];
			$formData['city'] 						= $billingAddress['city'];
			$formData['state'] 						= $billingAddress['stateID'];
			$formData['zip'] 						= $billingAddress['postalCode'];
			$formData['country']  					= $billingAddress['countryID'];
			$formData['email']    					= $billingAddress['email'];
			$formData['phone']    					= $billingAddress['phone'];
			
			// Only if tangible item
			//$formData['ship_name'] 					= $shippingAddress['name'];
			//$formData['ship_address'] 				= $shippingAddress['address'];
			//$formData['ship_address2'] 				= $shippingAddress['address2'];
			//$formData['ship_city'] 					= $shippingAddress['city'];
			//$formData['ship_state'] 				= $shippingAddress['stateID'];
			//$formData['ship_zip'] 					= $shippingAddress['postalCode'];
			//$formData['ship_country']  				= $shippingAddress['countryID'];
		break;
	}
?>