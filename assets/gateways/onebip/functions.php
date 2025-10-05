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
			$input[] = gateway_input('merchantid',$data['merchantid'],$lang['onebip_merchantid'],$lang['onebip_merchantid_d'],'textbox',1);
			$input[] = gateway_input('merchantkey',$data['merchantkey'],$lang['onebip_merchantkey'],$lang['onebip_merchantkey_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['onebip_testmode'],$lang['onebip_testmode_d'],'checkbox',0);
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
				$formData['debug'] 			= "true";
			}
			else
			{
				$formData['debug']			= "false";
			}
			
			//ACCOUNT DETAILS AND URL'S
			$formSubmitURL 									= "https://www.onebip.com/otms/";
			$formData['command']						= "express_pay";
			$formData['username'] 					= $gatewaySetting['merchantid'];
			$formData['return_url'] 				= "{$config[settings][site_url]}/pay.return.php?orderID={$uniqueOrderID}"; // Page to return to after payment is made
			$formData['cancel_url'] 				= "{$config[settings][site_url]}"; // Page to return to if payment is cancelled
			$formData['notify_url'] 				= "{$config[settings][site_url]}/assets/gateways/onebip/ipn.php"; // Page to notify on payment approval
			
			//PAYER DETAILS
			$formData['customer_firstname'] = $billingAddress['firstName'];
			$formData['customer_lastname'] 	= $billingAddress['lastName'];
			$formData['customer_email'] 		= $billingAddress['email'];
			$formData['custom[firstname]'] 	= $billingAddress['firstName'];
			$formData['custom[lastname]'] 	= $billingAddress['lastName'];
			$formData['custom[fullname]'] 	= $billingAddress['firstName']." ".$billingAddress['lastName'];
			$formData['custom[email]']			= $billingAddress['email'];
			
			function dollar($amount){ 
				$amount=doubleval($amount); 
				$price = (sprintf("%.2f", $amount));
				return $price;
			}
			//TRANSACTION DETAILS
			$formData['description'] 				= $config['settings']['site_title']." ".$lang['order'];
			$formData['price'] 							= dollar($cartTotals['cartGrandTotal']) * 100; // Overall Total
			$formData['currency']						=	$currency['code'];
			$formData['item_code'] 					= $uniqueOrderID; // Unique order number
			$formData['remote_txid'] 				= $uniqueOrderID; // Unique order number
		break;
	}
?>