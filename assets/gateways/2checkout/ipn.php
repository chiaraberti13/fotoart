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

	$gatewaySetting = getGatewayInfoFromDB('2checkout'); // Get the gateway settings from the db
	
	//FOR CHECKING WHAT WAS POSTED
	/*
	$message = "";
	foreach ($_REQUEST as $key => $value)
	{
		$message.=$key."=".$value."\n";
	}
	kmail('jeff@ktools.net','Jeff','jeff@ktools.net','Jeff','test',$message);
	*/
		
	//echo "Test".$_REQUEST['credit_card_processed']; exit;
	
	$ipnValue['paymentTotal'] 		= $_REQUEST['total']; 				// Total amount of payment (*REQUIRED*)
	switch($_REQUEST['credit_card_processed']) 									// Convert status of the payment to usable number values 0=pending, 1=completed, 2=unpaid, 3=bill later, 4=failed (*REQUIRED*)
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
	
	$fee = $_REQUEST['total'] * 0.0550000000000000002775558;
  	$pos = strpos ($fee, '.');
  	$pos = $pos + 3;
 	$fee = substr ($fee, 0, $pos);
  	$fee = $fee + 0.45000000000000001110223;
      
	$ipnValue['gatewayFee'] 		= $fee;					// Gate fee
	$ipnValue['cartName'] 			= $_REQUEST['c_description']; 				// Cart name or description
	$ipnValue['paymentCurrency'] 	= "USD"; 			// Currency that payment was made in
	$ipnValue['orderID'] 			= $_REQUEST['order_id']; 			// The order ID number (*REQUIRED*)
	$ipnValue['paymentShipping'] 	= $_REQUEST['shippingtotal']; 				// Total cost of shipping
	$ipnValue['paymentTax'] 		= $_REQUEST['taxtotal']; 					// Total cost of tax
	$ipnValue['payerName'] 			= $_REQUEST['card_holder_name']; 			// Full name of payer (*REQUIRED*)
	$ipnValue['payerFirstName'] 	= $_REQUEST['first_name']; 			// First name of payer
	$ipnValue['payerLastName'] 		= $_REQUEST['last_name']; 				// Last name of payer
	$ipnValue['payerBusiness'] 		= $_REQUEST['payer_business_name']; 	// Business name of payer 
	$ipnValue['payerEmail'] 		= $_REQUEST['email']; 			// Email address of payer (*REQUIRED*)
	$ipnValue['payerCountryCode']	= $_REQUEST['address_country_code']; 	// Country code of payer
	$ipnValue['payerCountry']		= $_REQUEST['country']; 		// Payer country
	$ipnValue['payerAddress'] 		= $_REQUEST['street_address']."-".$_REQUEST['street_address2']; 		// Payer address
	$ipnValue['payerCity'] 			= $_REQUEST['city']; 			// Payer city
	$ipnValue['payerState'] 		= $_REQUEST['state']; 			// Payer state
	$ipnValue['payerZip'] 			= $_REQUEST['zip']; 			// Payer zip
	$ipnValue['postVars'] 			= $_REQUEST; 							// The entire post array (*REQUIRED*)

	/*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	/*
	* 2Checkout IPN
	*/
	if($_REQUEST['credit_card_processed'] == 'Y'){
		//yes the order is completed send user to details page
		$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_REQUEST['order_id'];
		header("location: ".$return);
	} else {
		//no the order failed, leave a message or log it, etc..
		echo "The transaction failed, please contact us.";
	}

?>