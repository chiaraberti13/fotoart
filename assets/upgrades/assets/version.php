<?php
	//include('vinfo.php');	
	$config['product_name']			= "PhotoStore";
	$config['product_code']			= "ps";	
	$config['product_type']			= "Alpha"; // ALPHA, BETA, etc.
	$config['product_version']		= "4.0.2"; // VERSION NUMBER
	$config['product_build_date']	= "2008.07.31";
	
	# INCLUDE TYPE
	$inc = ($_GET['inc'])? $_GET['inc']: $inc;
	
	switch($inc){
		# FOR INCLUDING VARIABLES
		case "1":			
		break;
		# FOR OUTPUTING THE VERSION
		default:
		case "2":
			foreach($config as $key => $value){
				if($value) echo "$key: <strong>$value</strong><br />";
			}
			//echo "<img src='../manager/mgr.version.graphic.php?product_version=" . $config['product_version'] . "' />";
		break;
	}
?>