<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		// Define the database fields for the gateway
		case "activate":
			definegatewayfield($gatewaymodule['id'], 'accountid', '');
		break;
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			$input[] = gateway_input('accountid',$data['accountid'],$lang['plugnpay_f_accountid'],$lang['plugnpay_f_accountid_d'],'textbox',1);
		break;
		case "publicForm":		
		break;
	}
?>