<?php
	###################################################################
	####	SETTINGS DB SELECT FILE                                ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 4-11-2006                                     ####
	####	Modified: 4-11-2006                                    #### 
	###################################################################

	$settings_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}settings,{$dbinfo[pre]}settings2 where {$dbinfo[pre]}settings.settings_id = 1 AND {$dbinfo[pre]}settings2.settings_id = 1");
	//$returned_rows = mysqli_num_rows($settings_result);
	$config['settings'] = mysqli_fetch_assoc($settings_result);//mysqli_fetch_array
	
	if(!defined('BASE_PATH')) define('BASE_PATH',dirname(dirname(__FILE__))); // Check for a defined base path
	//die('Error: No BASE_PATH passed to mgr.select.settings.php'); 
	
	/*
	* Initialize Smarty
	*/
	if(INIT_SMARTY)
	{
		try
		{
			require_once BASE_PATH.'/assets/smarty/Smarty.class.php';
			$smarty = new Smarty;
			$smarty->assign('config',$config);
			//$smarty->compile_dir = BASE_PATH.'/assets/tmp';	
			//$smarty->cache_dir = BASE_PATH.'/assets/cache';	
			//$smarty->template_dir = BASE_PATH.'/assets/themes/'.$config['settings']['style'];
			//$smarty->compile_check = true;
			//$smarty->allow_php_templates= true;
			//$smarty->setCaching(true);
			//$smarty->force_compile = false; // If $force_compile is enabled, the cache files will be regenerated every time, effectively disabling caching.
			//$smarty->caching = true;
			//$smarty->cache_lifetime = 3600;			
			
			//$smarty->registerPlugin("function","content", "content");
			$smarty->registerPlugin("function","logo", "logo");
			//$smarty->registerPlugin("function","debugOutput", "debugOutput");
			//$smarty->registerPlugin("function","displayCurrency", "displayCurrency");
			//$smarty->registerPlugin("function","memberAvatar", "memberAvatar");
			//$smarty->registerPlugin("function","productShot", "productShot");
			//$smarty->registerPlugin("function","mediaImage", "mediaImage");
			//$smarty->registerPlugin("function","linkto", "linkto");			
			//$smarty->register_function('content','content');
			//$smarty->register_function('debugOutput','debugOutput');
			//$smarty->register_function('displayCurrency','displayCurrency');
			//$smarty->register_function('memberAvatar','memberAvatar'); // Register the memberAvatar function with smarty
			//$smarty->register_function('productShot','productShot'); // Register the productShot function with smarty
			//$smarty->register_function('mediaImage','mediaImage'); // Register the mediaImage function with smarty			
			
			//$smarty->assign('creditSystem',$creditSystem); // Send the status of the credit system to smarty
			//$smarty->assign('currencySystem',currencyCartStatus()); // Send the status of the currency system to smarty
			//$smarty->assign('cartStatus',$cartStatus); // Send the status of the cart system to smarty
			//$smarty->assign('ticketSystem',$ticketSystem); // Send the status of the ticket system to smarty
			//$smarty->assign('commentSystem',$commentSystem); // Comment System status
			//$smarty->assign('taggingSystem',$taggingSystem); // Tagging System status
			//$smarty->assign('pageID',PAGE_ID);
			//$smarty->assign('pageMode',$mode);
			//$smarty->assign('access',ACCESS);
			//$smarty->assign('installedAddons',$installed_addons); // Assign an array of installed add-ons to smarty
			//$smarty->assign('settings',$config['settings']); // Assign settings info to smarty so that they can be used in the template
			//$smarty->assign('loginPageURL',"{$siteURL}/login.php"); // Direct link to login page
			//$smarty->assign('baseURL',$siteURL); // Assign the base URL as a shortcut
			//$smarty->assign('theme',$config['settings']['style']); // Assign the theme as a shortcut - same as $setting.style	
			//$smarty->assign('colorScheme',$colorScheme); // Assign the colorScheme css file	
			//$smarty->assign('imgPath',$siteURL.'/assets/themes/'.$config['settings']['style'].'/'.$colorSchemeImagesDirectory); // Assign an image path for the theme
			//$smarty->assign('blankImg',"{$siteURL}/assets/images/blank.png"); // Shortcut to a blank image - template usage: <img src="{$blankImg}" />
			//$smarty->assign('transparentImg',"{$siteURL}/assets/images/transparent.png"); // Shortcut to a transparent image - template usage: <img src="{$transparentImg}" />
			//$smarty->assign('self',$self); // Assign the page and variables
			//$smarty->assign('fullURL',$siteURL.'/'.$self['uri']); // Assign the full url of the page
			//$smarty->assign('formKey',$_SESSION['formKey']); // Pass a key to the function that can be used to protect forms
			
			if($_GET['message']) // Assign messages to at the top level smarty
			{
				$messageArray = explode(',',$_GET['message']);
				$smarty->assign('message',$messageArray);
			}
		}
		catch(Exception $e){
			die(exceptionError($e)); // Kill the process and output the error
		}
	}
?>