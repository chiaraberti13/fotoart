<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-26-2011
	*  Modified: 4-26-2011
	******************************************************************/	
	
	/*
	* Update the members session language
	
	if($_GET['changeLanguage'])
	{
		$_SESSION['selectedLanguageSession'] = $_GET['changeLanguage'];
		$_SESSION['member']['language'] = $_GET['changeLanguage'];
	}
	*/
	
	/*
	* Update the members session currency
	
	if($_GET['changeCurrency'])
	{
		$_SESSION['selectedCurrencySession'] = $_GET['changeCurrency'];
		$_SESSION['member']['currency'] = $_GET['changeCurrency'];
	}
	*/
	
	/*
	* Destroy the previous session
	
	if($_GET['cmd'] == 'sessionDestroy')
	{
		session_destroy();
		header('location: index.php');
		exit;
	}
	*/
	
	/*
	* Debug mode sessions
	*/
	if($_GET['debugMode'] == 'on')
		$_SESSION['debugMode'] = 1;
		
	if($_GET['debugMode'] == 'off')
		unset($_SESSION['debugMode']);
		
	$smarty->assign('debugMode',$_SESSION['debugMode']); // Pass the debug mode to smarty
?>