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

	$gatewaySetting = getGatewayInfoFromDB('ideal'); // Get the gateway settings from the db
	
	/*
	// FOR TESTING TO SEE WHAT IS BEING POSTED
	$message = "";
	foreach ($_POST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
	
	$ipnValue['paymentTotal'] 		= $_POST['total']; 				// Total amount of payment (*REQUIRED*)
	switch($_POST['credit_card_processed']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
	{
		case 'K':
			$ipnValue['paymentStatus'] = 0; 							// Payment is still pending
		break;
		case 'Y':
			$ipnValue['paymentStatus'] = 1; 							// Payment is completed
		break;
		case 'F':
			$ipnValue['paymentStatus'] = 4; 							// Payment has failed
		break;
	}	
	
	$fee = $_POST['total'] * 0.0550000000000000002775558;
  $pos = strpos ($fee, '.');
  $pos = $pos + 3;
  $fee = substr ($fee, 0, $pos);
  $fee = $fee + 0.45000000000000001110223;
      
	$ipnValue['gatewayFee'] 		= $fee;					// Gate fee
	$ipnValue['cartName'] 			= $_POST['c_description']; 				// Cart name or description
	$ipnValue['paymentCurrency'] 	= "USD"; 			// Currency that payment was made in
	$ipnValue['orderID'] 			= $_POST['order_id']; 			// The order ID number (*REQUIRED*)
	$ipnValue['paymentShipping'] 	= $_POST['shippingtotal']; 				// Total cost of shipping
	$ipnValue['paymentTax'] 		= $_POST['taxtotal']; 					// Total cost of tax
	$ipnValue['payerName'] 			= $_POST['card_holder_name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_POST['first_name']; 			// First name of payer
	$ipnValue['payerLastName'] 		= $_POST['last_name']; 				// Last name of payer
	$ipnValue['payerBusiness'] 		= $_POST['payer_business_name']; 	// Business name of payer 
	$ipnValue['payerEmail'] 		= $_POST['email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountryCode']	= $_POST['address_country_code']; 	// Country code of payer
	$ipnValue['payerCountry']		= $_POST['country']; 		// Payer country
	$ipnValue['payerAddress'] 		= $_POST['street_address']."-".$_POST['street_address2']; 		// Payer address
	$ipnValue['payerCity'] 			= $_POST['city']; 			// Payer city
	$ipnValue['payerState'] 		= $_POST['state']; 			// Payer state
	$ipnValue['payerZip'] 			= $_POST['zip']; 			// Payer zip
	$ipnValue['postVars'] 			= $_POST; 							// The entire post array (*REQUIRED*)

  /*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	/*
	* iDeal IPN
	*/
	if($_POST['credit_card_processed'] == "Y"){
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['purchaseID'];
		header("location: ".$return);
		//yes the order passed, do something here
	} else {
		//no the order failed, leave a message or log it, etc..
	}

?>