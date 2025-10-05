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

	$gatewaySetting = getGatewayInfoFromDB('skrill'); // Get the gateway settings from the db

	/*
	//FOR CHECKING WHAT WAS POSTED
	$message = "";
	foreach ($_POST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	$ipnValue['paymentTotal'] 		= $_POST['mb_amount']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['status']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case '0':
			$ipnValue['paymentStatus'] = 0; 							// Payment is still pending
		break;
		case '2':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
		case '-2':
			$ipnValue['paymentStatus'] = 4; 							// Payment has failed
		break;
	}	

	$ipnValue['paymentCurrency'] 			= $_POST['mb_currency']; 			// Currency that payment was made in
	$ipnValue['orderID'] 					= $_POST['transaction_id']; 		// The order ID number (*REQUIRED*)
	$ipnValue['payerEmail'] 				= $_POST['pay_from_email']; 		// Email address of payer (*REQUIRED*)
	$ipnValue['postVars'] 					= $_POST; 							// The entire post array (*REQUIRED*)

	/*
	* Skrill IPN
	*/
	/*
	if($_POST['status'] == 2){
		//yes the order is completed send user to details page
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['transaction_id'];
		header("location: ".$return);
	} else {
		//no the order failed, leave a message or log it, etc..
		echo "The transaction failed, please contact us.";
	}
	*/
	
  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
?>