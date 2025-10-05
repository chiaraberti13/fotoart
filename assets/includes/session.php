<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-21-2011
	******************************************************************/
	
	//session_save_path("path_here");
	
	// Start a session
	session_start();
	
	// Define a login for extra security
	define("DEFINED_LOGIN","LOGGED");
	// FP
	
	function tep_session_id() {
		return session_id();
	}
?>