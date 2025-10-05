<?php
	/* This file converts all $_POST and $_GET arrays into local values and cleans them up for mysql insertion */
	foreach($_REQUEST as $key => $value)
	{				
		if(is_array($value))
		{
			foreach($value as $key2 => $value2)
				${$key}[$key2] = quote_smart($value2);
		}
		else
			${$key} = quote_smart($value);
	}
	
	
	/*
	if(get_magic_quotes_gpc()){
		function stripslashes_deep($value){
			$value = is_array($value) ? array_map('stripslashes_deep', $value) :  stripslashes($value); 
			return $value;
		}
	
		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}
	*/
	
?>