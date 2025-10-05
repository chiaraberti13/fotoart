<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-26-2011
	*  Modified: 4-26-2011
	******************************************************************/	
	
	/*
	* Include the public area languages
	*/
	$activePublicLanguages = explode(",",$config['settings']['lang_file_pub']);
	
	foreach($activePublicLanguages as $language)
	{
		if(include BASE_PATH."/assets/languages/{$language}/lang.settings.php")
		{
			$displayLanguages[$langset['id']] = $langset['name'];
			$activeLanguages[$langset['id']] = $langset;
			if(file_exists(BASE_PATH."/assets/languages/{$language}/flag.png"))
				$activeLanguages[$langset['id']]['flag'] = true;
				
			unset($langset); // Clear any previous language settings from the last include
		}
	}
	$langset = ''; // Just making sure none of the settings carry over from above
	require_once BASE_PATH.'/assets/includes/reglang.php';	// Get the registered language files to read in
	foreach($regpubfiles as $value)
	{
		if(file_exists(BASE_PATH."/assets/languages/{$selectedLanguage}/{$value}"))
			@include BASE_PATH."/assets/languages/{$selectedLanguage}/{$value}";
		else
			@include BASE_PATH."/assets/languages/english/{$value}";
	}
	
	/*
	* Assign the language to smart if it exists
	*/
	if($smarty)
	{
		try
		{
			$smarty->assign('displayLanguages',$displayLanguages); // Assign display languages to smarty
			$smarty->assign('activeLanguages',$activeLanguages); // Assign active language details to smarty
			
			//$smarty->assign('displayLanguagesCount',count($displayLanguages)); // Assign number of display languages to smarty
			//$smarty->assign('activeLanguagesCount',count($activeLanguages)); // Assign number of active language details to smarty
			
			$smarty->assign('lang',$lang); // Assign language to smarty
			$smarty->assign('langset',$langset); // Assign language settings to smarty
		}
		catch(Exception $e){}
	}
?>