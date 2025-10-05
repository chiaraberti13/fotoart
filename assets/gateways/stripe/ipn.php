<?php
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$errors = array();
		if(isset($_POST['stripeToken'])) {
			$token = $_POST['stripeToken'];
		} else {
			$errors['token'] = 'The order cannot be processed. You have not been charged. Please confirm that you have JavaScript enabled and try again.';
		}
	} // End of form submission conditional.
	
	if($_SESSION['sessToken'] == $token){
		echo "Duplicate payment. Stopping process.";
		exit;
	} else
		$_SESSION['sessToken'] = $token;
	
	/*
	* Gateway setup
	*/
	$gatewayID = basename(dirname(__FILE__)); // Get the gateway ID - which is just the folder name for the gateway
	define('BASE_PATH',dirname(dirname(dirname(dirname(__FILE__))))); // Define the base path

	require_once BASE_PATH.'/assets/includes/session.php';
	require_once BASE_PATH.'/assets/includes/initialize.php';
	require_once BASE_PATH.'/assets/includes/commands.php';
	require_once BASE_PATH.'/assets/includes/init.member.php';
	require_once BASE_PATH.'/assets/includes/security.inc.php';
	require_once BASE_PATH.'/assets/includes/language.inc.php';
	require_once BASE_PATH.'/assets/includes/cart.inc.php';
	require_once BASE_PATH.'/assets/includes/affiliate.inc.php';
	
	if(file_exists(BASE_PATH.'/assets/languages/'.$config['settings']['lang_file_mgr'].'/lang.manager.php')) // Include manager language file
		include(BASE_PATH.'/assets/languages/'.$config['settings']['lang_file_mgr'].'/lang.manager.php');
	else
		include(BASE_PATH.'/assets/languages/english/lang.manager.php');
		
	require_once BASE_PATH.'/assets/includes/header.inc.php';
	require_once BASE_PATH.'/assets/includes/errors.php';
	
	$gatewaySetting = getGatewayInfoFromDB('stripe'); // Get the gateway settings from the db

	require(BASE_PATH.'/assets/gateways/stripe/init.php'); // Stripe init include

	if(!$gatewaySetting['skey']){
		echo "There is no secret key set for your stripe account.";
		exit;
	}
	
	$stripeTotal = $_POST['amount']*100;
	$description = $_POST['description'];

	//echo $priCurrency['code'];
	//print_k($_SESSION['cartTotalsSession']); exit;

	if(!class_exists('\Stripe\Stripe')){
		echo "No Stripe class found";
		exit;
	}
	
	try {
		
		\Stripe\Stripe::setApiKey($gatewaySetting['skey']); // Set secret api key
		
		//echo \Stripe\Stripe::getApiKey();
		
		$charge = \Stripe\Charge::create(array(
			'amount' => $stripeTotal, // Amount in cents!
			'currency' => strtolower($priCurrency['code']),
			'card' => $token,
			'description' => $description
		));
		
		
	} catch(\Stripe\Error\Card $e) {
	  // Since it's a decline, \Stripe\Error\Card will be caught
	  $body = $e->getJsonBody();
	  $err  = $body['error'];
	
	  print('Status is:' . $e->getHttpStatus() . "\n");
	  print('Type is:' . $err['type'] . "\n");
	  print('Code is:' . $err['code'] . "\n");
	  // param is '' in this case
	  print('Param is:' . $err['param'] . "\n");
	  print('Message is:' . $err['message'] . "\n");
	
	} catch (\Stripe\Error\InvalidRequest $e) {
	  // Invalid parameters were supplied to Stripe's API
	  echo "Error: a";
	} catch (\Stripe\Error\Authentication $e) {
	  // Authentication with Stripe's API failed
	  // (maybe you changed API keys recently)
	  echo "Error: b";
	} catch (\Stripe\Error\ApiConnection $e) {
	  // Network communication with Stripe failed
	  echo "Error: c";
	} catch (\Stripe\Error\Base $e) {
	  // Display a very generic error to the user, and maybe send
	  // yourself an email
	  echo "Error: d";
	} catch (Exception $e) {
	  // Something else happened, completely unrelated to Stripe
	  echo "Error: e";
	}
	
	//echo $charge->paid; exit;
	
	if ($charge->paid == true) {
		$ipnValue['paymentStatus'] = 1;		
		$ipnValue['paymentTotal'] = $_POST['amount'];		
		$ipnValue['orderID'] = $_POST['uniqueOrderID'];		
		$ipnValue['payerEmail'] = $_POST['email']; // Email address of payer (*REQUIRED*)
		
	} else {
		$ipnValue['paymentStatus'] = 4;	
		echo "Payment failed";
	}
	
	/*
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
	*/

  	/*
	* Pass all of the details to the universal ipn include file
	*/
	require_once BASE_PATH.'/assets/includes/ipn.inc.php'; // ipn include file
	
	//echo $_POST['uniqueOrderID']; exit;
	
	$return = "{$config[settings][site_url]}/pay.return.php?orderID=".$_POST['uniqueOrderID'];
	header("location: ".$return);

?>