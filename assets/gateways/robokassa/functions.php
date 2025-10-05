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
			definegatewayfield($gatewaymodule['id'], 'merchantpass', '');
		break;
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('merchantid',$data['merchantid'],$lang['robokassa_f_merchantid'],$lang['robokassa_f_merchantid_d'],'textbox',1);
			$input[] = gateway_input('merchantpass',$data['merchantpass'],$lang['robokassa_f_merchantpass'],$lang['robokassa_f_merchantpass_d'],'textbox',1);
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
			$formSubmitURL 								= "https://merchant.roboxchange.com/Index.aspx";
			$formSubmitMethod							= "get";
			$formData['MrchLogin']				= $gatewaySetting['merchantid'];
			$formData['InvId'] 						= $cartInfo['cartID'];
			$formData['OutSum'] 					= $cartTotals['cartGrandTotal'];
			$formData['Desc'] 						= $config['settings']['site_title']." ".$lang['order'];
			$formData['SignatureValue']		= md5("".$gatewaySetting['merchantid'].":".$cartTotals['cartGrandTotal'].":".$cartInfo['cartID'].":".$gatewaySetting['merchantpass']);
		break;
	}
?>