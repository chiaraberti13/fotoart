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

	$gatewaySetting = getGatewayInfoFromDB('authorize'); // Get the gateway settings from the db

	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_POST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	$ipnValue['paymentTotal'] 		= $_POST['x_amount']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['x_response_code']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case '1':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
		case ($_POST['x_response_code'] >= 2):
			$ipnValue['paymentStatus'] = 4; 							// Payment has failed
		break;
	}	
	
	$ipnValue['cartName'] 				= $_POST['x_description']; 				// Cart name or description
	$ipnValue['orderID'] 					= $_POST['x_uniqueorderid']; 			// The order ID number (*REQUIRED*)
	$ipnValue['paymentShipping'] 	= $_POST['x_freight']; 				// Total cost of shipping
	$ipnValue['paymentTax'] 			= $_POST['x_tax']; 					// Total cost of tax
	$ipnValue['payerName'] 				= $_POST['x_first_name']." ".$_POST['x_last_name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_POST['x_first_name']; 			// First name of payer
	$ipnValue['payerLastName'] 		= $_POST['x_last_name']; 				// Last name of payer
	$ipnValue['payerEmail'] 			= $_POST['x_email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountry']			= $_POST['x_country']; 		// Payer country
	$ipnValue['payerAddress'] 		= $_POST['x_address']; 		// Payer address
	$ipnValue['payerCity'] 				= $_POST['x_city']; 			// Payer city
	$ipnValue['payerState'] 			= $_POST['x_state']; 			// Payer state
	$ipnValue['payerZip'] 				= $_POST['x_zip']; 			// Payer zip
	$ipnValue['postVars'] 				= $_POST; 							// The entire post array (*REQUIRED*)

	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	/*
	* Authorize IPN
	*/
	if($_POST['x_response_code'] == 1){
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['x_uniqueorderid'];
		header("location: ".$return);
	} else {
		//no the order failed, leave a message or log it, etc..
	}
?>