<?php
	# SELECT THE PRIMARY CURRENCY DETAILS
	$pricur_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}currencies where defaultcur = '1'");
	$pricur = mysqli_fetch_object($pricur_result);
	
	$config['settings']['cur_currency_id']			= $pricur->currency_id;
	$config['settings']['cur_code']					= $pricur->code;
	$config['settings']['cur_name'] 				= $pricur->name;
	$config['settings']['cur_denotation'] 			= $pricur->denotation;
	$config['settings']['cur_decimal_places'] 		= $pricur->decimal_places;
	$config['settings']['cur_decimal_separator'] 	= $pricur->decimal_separator;
	$config['settings']['cur_thousands_separator'] 	= $pricur->thousands_separator;
	$config['settings']['cur_neg_num_format'] 		= $pricur->neg_num_format;
	$config['settings']['cur_pos_num_format'] 		= $pricur->pos_num_format;
?>