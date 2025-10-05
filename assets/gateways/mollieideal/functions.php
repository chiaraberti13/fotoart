<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'partnerid', '');
			definegatewayfield($gatewaymodule['id'], 'profilekey', '');
			definegatewayfield($gatewaymodule['id'], 'testmode', '');
		break;
		
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('partnerid',$data['partnerid'],$lang['mollieideal_f_partnerid'],$lang['mollieideal_f_partnerid_d'],'textbox',1);
			$input[] = gateway_input('profilekey',$data['profilekey'],$lang['mollieideal_f_profilekey'],$lang['mollieideal_f_profilekey_d'],'textbox',0);
			$input[] = gateway_input('testmode',$data['testmode'],$lang['mollieideal_f_testmode'],$lang['mollieideal_f_testmode_d'],'checkbox',0);
		break;

		case "redirectUser":
			require_once 'library.php';

			$partner_id  = $gatewaySetting['partnerid']; // Uw mollie partner ID
			$amount      = $cartTotals['cartGrandTotal'] * 100;    // Het af te rekenen bedrag in centen (!!!)
			$description = $config['settings']['site_title'].' '.$lang['order']; // Beschrijving die consument op zijn/haar afschrift ziet.

			$return_url  = $config['settings']['site_url'].'/pay.return.php?orderID='.$uniqueOrderID; // URL waarnaar de consument teruggestuurd wordt na de betaling
			$report_url  = $config['settings']['site_url'].'/assets/gateways/mollieideal/ipn.php?orderID='.$uniqueOrderID; // URL die Mollie aanvraagt (op de achtergrond) na de betaling om de status naar op te sturen

			if (!in_array('ssl', stream_get_transports())) {
				echo "<h1>Foutmelding</h1>";
				echo "<p>Uw PHP installatie heeft geen SSL ondersteuning. SSL is nodig voor de communicatie met de Mollie iDEAL API.</p>";
				exit;
			}

			$iDEAL = new Mollie_iDEAL_Payment($partner_id);

			if ($gatewaySetting['testmode']) {
				$iDEAL->setTestMode();
			}

			if (isset($_POST['bank_id']) and !empty($_POST['bank_id'])) {
				if ($iDEAL->createPayment($_POST['bank_id'], $amount, $description, $return_url, $report_url))  {
					// [todo] gebruik $iDEAL->getTransactionId() om transactie op te slaan in daba
					header('Location: '.$iDEAL->getBankURL());
				} else  {
					echo '<p>De betaling kon niet aangemaakt worden.</p>';
					echo '<p><strong>Foutmelding:</strong> ', htmlspecialchars($iDEAL->getErrorMessage()), '</p>';
					exit;
				}
			}
		break;
		
		case "publicForm":
			/*
				Cart totals array items ($cartTotals)
				shippingTotal 					- total cost of shipping
				cartGrandTotal					- grand total including shipping, taxes, subtotal and discounts
				taxTotal 								- total of all taxes charged
				subtotalMinusDiscounts 	- subtotal minus any discounts
				priceSubTotal 					- subtotal before any discounts, taxes or shipping
				taxablePrice 						- total of taxable items
				shippableTotal 					- total of shippable items
				shippableWeight 				- total shipping weight
				taxA 										- total for tax A 
				taxB										- total for tax B
				taxC										- total for tax C
				
				Cart info array items ($cartInfo)
				orderNumber				- order number created for this order
				uniqueOrderID			- unique id for this order
				cartID						- database id of this order
				invoiceID					- database id of the invoice
				cartItemRows			- number of items in the cart
				
				Shipping info array items ($shippingAddress)
				countryID					- shipping country id from the db
				country						- shipping country name
				name							- shipping name
				firstName					- shipping first name
				lastName					- shipping last name
				address						- shipping address
				address2					- shipping address line 2
				city							- shipping city
				stateID						- shipping state id from the db
				postalCode				- shipping postal or zip code
				email							- shipping email address
				phone							- shipping phone
				
				Billing info array items ($billingAddress)
				countryID					- billing country id from the db
				country						- billing country name
				name							- billing name
				firstName					- billing first name
				lastName					- billing last name
				address						- billing address
				address2					- billing address line 2
				city							- billing city
				stateID						- billing state id from the db
				postalCode				- billing postal or zip code
				email							- billing email address
				phone							- billing phone
				
				Currency info array items ($currency)
				currency_id				- database id of the currency selected
				name 							- actual name of the currency selected
				code							- code of the currency selected
				denotation 				- denotation used by the currency
			*/

			require_once 'library.php';

			$partner_id  = $gatewaySetting['partnerid']; // Uw mollie partner ID

			$iDEAL = new Mollie_iDEAL_Payment($partner_id);

			if ($gatewaySetting['testmode']) {
				$iDEAL->setTestMode();
			}

			$banks = $iDEAL->getBanks();
		break;
	}
?>