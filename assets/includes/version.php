<?php	
	$config['productName']			= "PhotoStore"; 	// Product name
	$config['productCode']			= "ps";				// Code for this product
	$config['productType']			= ""; 				// Alph, Beta
	$config['productVersion']		= "4.7.5"; 			// Version Number
	$config['productBuildDate']		= "2015.08.25"; 	// Build date
	$config['ioncubeVersion']		= "php52"; 			// Ionbube Version
	
	if(!$inc)
	{
		foreach($config as $key => $value)
			if($value) echo "{$key}: <strong>{$value}</strong><br />";
	}
?>