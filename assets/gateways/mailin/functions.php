<?php
	switch($gatewayMode)
	{
		// Initialize the gateway in the management area
		default:
		case "initialize":			
		break;
		// Define the database fields for the gateway
		case "activate":
			//definegatewayfield($gatewaymodule['id'], 'instructions', '');
		break;
		case "mgrForm":
			// Field name, value, display name, display description, input type [textbox,checkbox], required
			//$input[] = gateway_input('instructions',$data['instructions'],$lang['mailin_f_instructions'],$lang['mailin_f_instructions_d'],'textarea',1);
		break;
		case "publicForm":		
		break;
	}
?>