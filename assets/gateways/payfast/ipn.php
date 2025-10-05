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
	
	$gatewaySetting = getGatewayInfoFromDB('payfast'); // Get the gateway settings from the db
	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_REQUEST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	$ipnValue['paymentTotal'] 		= $_POST['mc_gross']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['payment_status']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case 'PENDING':
			$ipnValue['paymentStatus'] = 0; 							// Payment is still pending
		break;
		case 'COMPLETE':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
		case 'FAILED':
			$ipnValue['paymentStatus'] = 4; 							// Payment has failed
		break;
	}
	$fullName = $_POST['name_first']." ".$_POST['name_last'];
	$ipnValue['gatewayFee'] 			= $_POST['amount_fee'];						// Gate fee
	$ipnValue['cartName'] 				= $_POST['item_name']; 						// Cart name or description
	$ipnValue['orderID'] 					= $_POST['m_payment_id']; 				// The order ID number (*REQUIRED*)
	$ipnValue['payerName'] 				= $fullName; 											// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_POST['name_first']; 			// First name of payer
	$ipnValue['payerLastName'] 		= $_POST['name_last']; 				// Last name of payer
	$ipnValue['payerEmail'] 			= $_POST['email_address']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['postVars'] 				= $_POST; 							// The entire post array (*REQUIRED*)
  
  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
?>