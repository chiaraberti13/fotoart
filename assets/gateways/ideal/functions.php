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
			definegatewayfield($gatewaymodule['id'], 'transkey', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['ideal_f_accountid'],$lang['ideal_f_accountid_d'],'textbox',1);
			$input[] = gateway_input('transkey',$data['transkey'],$lang['ideal_f_transkey'],$lang['ideal_f_transkey_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['ideal_f_testmode'],$lang['ideal_f_testmode_d'],'checkbox',0);
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
				$formSubmitURL 			= "https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do";
			}
			else
			{
				$formSubmitURL 			= "https://ideal.secure-ing.com/ideal/mpiPayInitIng.do";
			}
			
			//BUILD HASH AND EXPIRE TIMES FOR THIS TRANSACTION
			$validUntil = date("Y-m-d\TH:i:s", time() + 3600 + date('Z'));
			$validUntil = $validUntil . ".000Z";

			$total = $cartTotals['cartGrandTotal'] * 100;

			$pre_sha = '';
			$shastring = $gatewaySetting['transkey'].$gatewaySetting['accountid']."0"."$total".$cartInfo['uniqueOrderID']."ideal"."$validUntil".$pre_sha;
			$shastring = str_replace(" ", "", $shastring);
			$shastring = str_replace("\t", "", $shastring);
			$shastring = str_replace("\n", "", $shastring);
			$shastring = str_replace("&amp;", "&", $shastring);
			$shastring = str_replace("&lt;", "<", $shastring);
			$shastring = str_replace("&gt;", ">", $shastring);
			$shastring = str_replace("&quot;", "\"", $shastring);
			$shasign = sha1($shastring);
			$counter = 1;
			$com = 0;
			
			//FORM DATA
			$formData['merchantID'] 			= $gatewaySetting['accountid'];
			$formData['subID'] 						= "0"; // Order Number
			$formData['amount'] 					= $cartTotals['cartGrandTotal'];
			$formData['purchaseID'] 			= $cartInfo['uniqueOrderID'];
			$formData['language'] 				= "EN";
			$formData['currency']     		= $currency['code'];
			$formData['description'] 			= $config['settings']['site_title']." ".$lang['order']; // Name of the item that displays on payment page
			$formData['itemNumber1']      = 1;
			$formData['itemDescription1'] = $config['settings']['site_title']." ".$lang['order'];;
			$formData['itemQuantity1'] 		= 1;
			$formData['itemPrice1'] 			= $cartTotals['cartGrandTotal'];;
			$formData['hash']							= $shasign;
			$formData['paymentType']			= "ideal";
			$formData['validUntil']				= $validUntil;
			$formData['urlCancel'] 				= "{$config[settings][site_url]}";
			$formData['urlSuccess'] 			= "{$config[settings][site_url]}/assets/gateways/ideal/ipn.php";
			$formData['urlError'] 				= "{$config[settings][site_url]}/assets/gateways/ideal/ipn.php";
		break;
	}
?>