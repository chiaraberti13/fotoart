<?php
	/* This file converts all $_POST and $_GET arrays into local values and cleans them up for mysql insertion */
	foreach($_POST as $key => $value){				
		if(is_array($value)){
			foreach($value as $key2 => $value2){
				//${$key}[$key2] = addslashes($value2);
				${$key}[$key2] = quote_smart($value2);
				//echo $key . "<br />";								
			}	
		} else {
			${$key} = quote_smart($value);
		}
	}
	foreach($_GET as $key => $value){				
		if(is_array($value)){
			foreach($value as $key2 => $value2){
				//${$key}[$key2] = addslashes($value2);
				${$key}[$key2] = quote_smart($value2);								
			}	
		} else {
			${$key} = quote_smart($value);
		}
	}
?>
