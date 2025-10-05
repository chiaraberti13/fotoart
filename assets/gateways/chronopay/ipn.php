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

	$ipnValue['paymentTotal'] 		= $_POST['mc_gross']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['payment_status']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
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
	$ipnValue['gatewayFee'] 		= $_POST['mc_fee'];					// Gate fee
	$ipnValue['cartName'] 			= $_POST['item_name']; 				// Cart name or description
	$ipnValue['paymentCurrency'] 	= $_POST['mc_currency']; 			// Currency that payment was made in
	$ipnValue['orderID'] 			= $_POST['item_number']; 			// The order ID number (*REQUIRED*)
	$ipnValue['paymentShipping'] 	= $_POST['shipping']; 				// Total cost of shipping
	$ipnValue['paymentTax'] 		= $_POST['tax']; 					// Total cost of tax
	$ipnValue['payerName'] 			= $_POST['address_name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_POST['first_name']; 			// First name of payer
	$ipnValue['payerLastName'] 		= $_POST['last_name']; 				// Last name of payer
	$ipnValue['payerBusiness'] 		= $_POST['payer_business_name']; 	// Business name of payer 
	$ipnValue['payerEmail'] 		= $_POST['payer_email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountryCode']	= $_POST['address_country_code']; 	// Country code of payer
	$ipnValue['payerCountry']		= $_POST['address_country']; 		// Payer country
	$ipnValue['payerAddress'] 		= $_POST['address_street']; 		// Payer address
	$ipnValue['payerCity'] 			= $_POST['address_city']; 			// Payer city
	$ipnValue['payerState'] 		= $_POST['address_state']; 			// Payer state
	$ipnValue['payerZip'] 			= $_POST['address_zip']; 			// Payer zip
	$ipnValue['postVars'] 			= $_POST; 							// The entire post array (*REQUIRED*)
  
  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
?>