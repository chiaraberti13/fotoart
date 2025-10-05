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

	$gatewaySetting = getGatewayInfoFromDB('mollieideal'); // Get the gateway settings from the db

	require_once 'library.php';
	
	$partner_id = $gatewaySetting['partnerid'];

	$ipnValue['orderID'] = $_GET['orderID']; // The order ID number (*REQUIRED*)

	if (isset($_GET['transaction_id'])) {
		$iDEAL = new Mollie_iDEAL_Payment($partner_id);
		if ($gatewaySetting['testmode']) {
			$iDEAL->setTestMode();
		}

		$iDEAL->checkPayment($_GET['transaction_id']);

		if ($iDEAL->getPaidStatus()) {
			$consumer = $iDEAL->getConsumerInfo();
			$amount = $iDEAL->getAmount();

			$ipnValue['payerName']		= $consumer['consumerName'];	// Full name of payer (*REQUIRED*)
			$ipnValue['paymentTotal']	= $amount;						// Total amount of payment (*REQUIRED*)
			$ipnValue['paymentStatus']	= 1;							// Payment is completed
		} else {
			if ($iDEAL->getBankStatus() != 'CheckedBefore') {
				if ($iDEAL->getBankStatus() == 'Open')
					$ipnValue['paymentStatus'] = 0; // Payment is still pending
				else
					$ipnValue['paymentStatus'] = 4; // Payment has failed
			} else {
				exit; // Prevent from updating order
			}
		}

		// Debug
		echo 'getPaidStatus: '.$iDEAL->getPaidStatus().'<br />';
		echo 'getBankStatus: '.$iDEAL->getBankStatus().'<br />';
	}

	// Pass all of the details to the universal ipn include file
	require_once BASE_PATH.'/assets/includes/ipn.inc.php';
?>