<?php
	/*
	* Gateway setup
	*/
	$gatewayID = basename(dirname(__FILE__)); // Get the gateway ID - which is just the folder name for the gateway
	define('BASE_PATH',dirname(dirname(dirname(dirname(__FILE__))))); // Define the base path

	require_once BASE_PATH.'/assets/includes/initialize.php';
	require_once BASE_PATH.'/assets/includes/language.inc.php';
	
	if(file_exists(BASE_PATH.'/assets/languages/'.$config['settings']['lang_file_mgr'].'/lang.manager.php')) // Include manager language file
		include(BASE_PATH.'/assets/languages/'.$config['settings']['lang_file_mgr'].'/lang.manager.php');
	else
		include(BASE_PATH.'/assets/languages/english/lang.manager.php');
	
	$gatewaySetting = getGatewayInfoFromDB('paypal'); // Get the gateway settings from the db

	$ipnValue['paymentTotal'] 		= $_REQUEST['mc_gross']; 				// Total amount of payment (*REQUIRED*)
	switch($_REQUEST['payment_status']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case 'Pending':
			$ipnValue['paymentStatus'] = 0; 							// Payment is still pending
		break;
		case 'Completed':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
		case 'Failed':
			$ipnValue['paymentStatus'] = 4; 							// Payment has failed
		break;
	}	
	$ipnValue['gatewayFee'] 		= $_REQUEST['mc_fee'];					// Gate fee
	$ipnValue['cartName'] 			= $_REQUEST['item_name']; 				// Cart name or description
	$ipnValue['paymentCurrency'] 	= $_REQUEST['mc_currency']; 			// Currency that payment was made in
	$ipnValue['item_number'] 		= $_REQUEST['item_number'];				// Item number passed to paypal - usually the invoice or order number
	$ipnValue['orderID'] 			= $_REQUEST['invoice']; 				// The order ID number (*REQUIRED*)
	$ipnValue['paymentShipping'] 	= $_REQUEST['shipping']; 				// Total cost of shipping
	$ipnValue['paymentTax'] 		= $_REQUEST['tax']; 					// Total cost of tax
	$ipnValue['payerName'] 			= $_REQUEST['address_name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_REQUEST['first_name']; 				// First name of payer
	$ipnValue['payerLastName'] 		= $_REQUEST['last_name']; 				// Last name of payer
	$ipnValue['payerBusiness'] 		= $_REQUEST['payer_business_name']; 	// Business name of payer 
	$ipnValue['payerEmail'] 		= $_REQUEST['payer_email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountryCode']	= $_REQUEST['address_country_code']; 	// Country code of payer
	$ipnValue['payerCountry']		= $_REQUEST['address_country']; 		// Payer country
	$ipnValue['payerAddress'] 		= $_REQUEST['address_street']; 			// Payer address
	$ipnValue['payerCity'] 			= $_REQUEST['address_city']; 			// Payer city
	$ipnValue['payerState'] 		= $_REQUEST['address_state']; 			// Payer state
	$ipnValue['payerZip'] 			= $_REQUEST['address_zip']; 			// Payer zip
	$ipnValue['postVars'] 			= $_REQUEST; 							// The entire post array (*REQUIRED*)

	/*
	* PayPal IPN
	*/
	$req = 'cmd=_notify-validate'; // Read the post from PayPal system and add 'cmd'

	foreach(@$_REQUEST as $key => $value)
	{
		@$value=urlencode(stripslashes($value));
		$req.="&{$key}={$value}";
	}
	
	// Post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Host: 'www.paypal.com\r\n'";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

	// Check for test order or not
	if($gatewaySetting['testmode'])
		$fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
	else
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
	
	$payTo = $_REQUEST['business'];
	$payType = $_REQUEST['payment_type']; // instant
	
	if(!$fp)
	{
		// HTTP ERROR
	}
	else
	{
		fputs($fp, $header . $req);
		while(!feof($fp))
		{
			$res = fgets($fp, 1024);
			if (strcmp($res, "VERIFIED") == 0) {
				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment
				
			}
			else if(strcmp($res, "INVALID") == 0)
			{
				// log for manual investigation
			}
		}
		fclose ($fp);
	}
  
  	/*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
?>