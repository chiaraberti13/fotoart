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
			definegatewayfield($gatewaymodule['id'], 'gatewayid', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		// Manager form input fields
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['paystation_f_accountid'],$lang['paystation_f_accountid_d'],'textbox',1);
			$input[] = gateway_input('gatewayid',$data['gatewayid'],$lang['paystation_f_gatewayid'],$lang['paystation_f_gatewayid_d'],'textbox',1);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['paystation_f_testmode'],$lang['paystation_f_testmode_d'],'checkbox',0);
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
			////////////////////////////////////////////////////////////////
			/// PayStation requires posting data to get an XML response  ///
			////////////////////////////////////////////////////////////////
			function directTransaction($url,$params){
				$defined_vars = get_defined_vars();
				//use curl to get reponse	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
				curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				$result=curl_exec ($ch);
				curl_close ($ch);
				return $result;
			}
			
			//LIST OF POST VARIABLES
			$paystationURL 		= "https://www.paystation.co.nz/direct/paystation.dll";
			$pstn_pi 					= $gatewaySetting['accountid'];
			$pstn_gi					= $gatewaySetting['gatewayid'];
			$pstn_ms					= $cartInfo['uniqueOrderID']; // Unique Order number
			$pstn_am 					= $cartTotals['cartGrandTotal']; // Total minus any discounts
			$pstn_cu 					= $currency['code']; // The currency code of the checkout
			$pstn_mr					= $cartInfo['orderNumber']; //public order number
			$pstn_af					= "dollars.cents";
			
			//BUILD LIST OF PARAMETERS FROM ABOVE INTO ONE LINE FOR SUBMISSION TO FUNCTION
			$paystationParams = "paystation&pstn_pi=".$pstn_pi."&pstn_gi=".$pstn_gi."&pstn_ms=".$pstn_ms."&pstn_am=".$pstn_am."&pstn_mr=".$pstn_mr."&pstn_af=".$pstn_af;
			if($gatewaySetting['testmode']){
				$paystationParams = $paystationParams."&pstn_tm=t";
			}
			
			//DO TRANSACTION INIATION POST
			$initiationResult=directTransaction($paystationURL,$paystationParams);
			$p = xml_parser_create();
			xml_parse_into_struct($p, $initiationResult, $vals, $tags);
			xml_parser_free($p);
			for($j=0; $j < count($vals); $j++) {
				if (!strcmp($vals[$j]["tag"],"DIGITALORDER") && isset($vals[$j]["value"])){
					//get digital order URL
					$digitalOrder=$vals[$j]["value"];
				}
				if (!strcmp($vals[$j]["tag"],"PAYSTATIONTRANSACTIONID") && isset($vals[$j]["value"])){
					//get Paystation Transaction ID for reference
					$paystationTransactionID=$vals[$j]["value"];
				}
			}
			/////////////////////////////////////////////////////////////////
			//////////End of getting transaction details/////////////////////
			/////////////////////////////////////////////////////////////////
			
			//SUBMIT FORM LOCALLY
			$formSubmitURL = $digitalOrder;
		break;
	}
?>