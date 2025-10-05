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

	$gatewaySetting = getGatewayInfoFromDB('worldpay'); // Get the gateway settings from the db
	
	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_POST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	$ipnValue['paymentTotal'] 		= $_POST['authAmount']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['transStatus']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case 'Y':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
	}

	$ipnValue['cartName'] 			= $_POST['desc']; 				// Cart name or description
	$ipnValue['paymentCurrency'] 	= $_POST['authCurrency']; 			// Currency that payment was made in
	$ipnValue['orderID'] 			= $_POST['cartId']; 			// The order ID number (*REQUIRED*)
	$ipnValue['payerName'] 			= $_POST['name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerEmail'] 		= $_POST['email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountry']		= $_POST['country']; 		// Payer country
	$ipnValue['payerAddress'] 	= $_POST['address']." ".$_POST['address2']; 		// Payer address
	$ipnValue['payerCity'] 			= $_POST['town']; 			// Payer city
	$ipnValue['payerState'] 		= $_POST['region']; 			// Payer state
	$ipnValue['payerZip'] 			= $_POST['postcode']; 			// Payer zip
	$ipnValue['postVars'] 			= $_POST; 							// The entire post array (*REQUIRED*)

  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	/*
	* WORLDPAY IPN
	*/
	if($_POST['transStatus'] == "Y"){
		//yes the order is completed send user to details page
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['cartId'];
		header("location: ".$return);
	} else {
		//no the order failed, leave a message or log it, etc..
		echo "The transaction failed, please contact us.";
	}
?>