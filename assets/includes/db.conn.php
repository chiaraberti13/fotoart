<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-21-2011
	******************************************************************/

	$db = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);

	if(mysqli_connect_errno()){
		echo 'MySQLi Error: ' . mysqli_connect_error(); exit;
	}
	
	/*
	if(!mysqli_select_db($db,$dbinfo['name'])){
		echo "Unable to select database. Please make sure the database name is correct."; exit;
	}
	*/
	
	if($config['mysqlUTF8Settings'] === true)
	{	
		@mysqli_query($db,'SET NAMES utf8');
		//@mysqli_query($db,'SET CHARACTER SET utf8'); // CHARACTER_SET
	}
	else
	{
		if($page != 'add_media')
			@mysqli_query($db,'SET NAMES utf8');
	}
	
	@mysqli_query($db,"SET sql_mode = ''");
?>