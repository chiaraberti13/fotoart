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

	$gatewaySetting = getGatewayInfoFromDB('mygate'); // Get the gateway settings from the db

	
	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_POST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	$ipnValue['paymentTotal'] 		= $_POST['txtPrice']; 				// Total amount of payment (*REQUIRED*)
	
	if($_POST['_RESULT'] >= 0)
		$ipnValue['paymentStatus'] = 1; // Payment is completed
	else
		$ipnValue['paymentStatus'] = 4; // Payment has failed
	
	$ipnValue['paymentCurrency'] 	= $_POST['txtDisplayCurrencyCode']; 			// Currency that payment was made in
	$ipnValue['orderID'] 			= $_POST['VARIABLE1']; 			// The order ID number (*REQUIRED*)
	$ipnValue['payerEmail'] 		= $_POST['VARIABLE2']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['postVars'] 			= $_POST; 							// The entire post array (*REQUIRED*)

	//echo $ipnValue['paymentStatus']; exit; // Testing

  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	/*
	* MyGate IPN
	*/
	if($_POST['_RESULT'] >= 0){
		//yes the order is completed send user to details page
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['VARIABLE1'];
		header("location: ".$return);	
	} else {
		//no the order failed, leave a message or log it, etc..
		echo "The transaction failed, please contact us.";
	}
?>