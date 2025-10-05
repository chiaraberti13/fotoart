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
	
	$gatewaySetting = getGatewayInfoFromDB('onebip'); // Get the gateway settings from the db
	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_REQUEST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	if(isset($_REQUEST['hash'])){ 
  	$my_api_key = $gatewaySetting['merchantkey']; // stored in your account settings 
  	$basename = basename($_SERVER['REQUEST_URI']); 
  	$pos = strrpos($basename, "&hash"); 
  	$basename_without_hash = substr($basename, 0, $pos); 
  	$my_hash = md5($my_api_key . $basename_without_hash); 
  	if($my_hash != $_REQUEST['hash']){ 
  		echo "ERROR: Invalid Submission"; 
  		exit(); 
  	} 
  } else {
  	echo "ERROR: No Submission";
  	exit();
  }
  
  //IF ALL IS GOOD ABOVE ON THE HASH CHECK THEN PROCESS ORDER
  echo "OK";
  $ipnValue['paymentStatus'] = 1;
	$ipnValue['paymentTotal'] 		= $_REQUEST['original_price']; 				// Total amount of payment (*REQUIRED*)
	$ipnValue['gatewayFee'] 			= $_REQUEST['commission'];	// Gateway fee
	$ipnValue['orderID'] 					= $_REQUEST['item_code']; 	// The order ID number (*REQUIRED*)
	$ipnValue['payerName'] 				= $_REQUEST['fullname']; 		// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_REQUEST['firstname']; 	// First name of payer
	$ipnValue['payerLastName'] 		= $_REQUEST['lastname']; 		// Last name of payer
	$ipnValue['payerEmail'] 			= $_REQUEST['email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['postVars'] 				= $_REQUEST; 								// The entire post array (*REQUIRED*)
  
  
  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
?>