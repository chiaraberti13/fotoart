<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			//definegatewayfield($gatewaymodule['id'], 'clientid', '');
			//definegatewayfield($gatewaymodule['id'], 'siteid', '');
			definegatewayfield($gatewaymodule['id'], 'productid', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			//$input[] = gateway_input('clientid',$data['clientid'],$lang['chronopay_f_clientid'],$lang['chronopay_f_clientid_d'],'textbox',1);
			//$input[] = gateway_input('siteid',$data['siteid'],$lang['chronopay_f_siteid'],$lang['chronopay_f_siteid_d'],'textbox',1);
			$input[] = gateway_input('productid',$data['productid'],$lang['chronopay_f_productid'],$lang['chronopay_f_productid_d'],'textbox',1);
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
			
			$formSubmitURL 												= "https://secure.chronopay.com/index_shop.cgi";
			$formData['product_id'] 							= $gatewaySetting['productid'];
			$formData['product_name'] 						= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['product_price'] 						= $cartTotals['cartGrandTotal']; // Total
			$formData['product_price_currency'] 	= $currency['code']; // The currency code of the checkout
			$formData['language'] 								= "EN";
			$formData['cs1']											= $cartInfo['uniqueOrderID'];
			
			$formData['cb_url'] 									= "{$config[settings][site_url]}/assets/gateways/chronopay/ipn.php";;
			$formData['cb_type'] 									= "P"; // G = GET / P = POST
			$formData['decline_url'] 							= "{$config[settings][site_url]}/assets/gateways/chronopay/ipn.php";; // Order number
			
			$formData['email'] 										= $billingAddress['email'];
			$formData['f_name'] 									= $billingAddress['firstName'];
			$formData['s_name'] 									= $billingAddress['lastName'];
			$formData['street'] 									= $billingAddress['address']." ".$billingAddress['address2'];
			$formData['city'] 										= $billingAddress['city'];
			$formData['state'] 										= $billingAddress['state'];
			$formData['zip'] 											= $billingAddress['postalCode'];
			$formData['country']  								= $billingAddress['countryID'];
			$formData['email']    								= $billingAddress['email'];
			$formData['phone']    								= $billingAddress['phone'];
		break;
	}
?>