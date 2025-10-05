<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 3-6-2012
	******************************************************************/
	
	//error_reporting(E_ERROR | E_WARNING | E_PARSE); // Set error reporting
	error_reporting(E_ALL ^ E_NOTICE); // All but notices

	$lang = ''; // Fix for ps3 with register globals on
	//@unset($_SESSION['lang']);

	/*
	* Start page load timer
	*/
	$pltime		= microtime();
	$pltime		= explode(" ", $pltime);
	$plstart	= $pltime[1] + $pltime[0];
	
	if(!defined('BASE_PATH')) die('Error: No BASE_PATH passed to initialize.php'); // Check for a defined base path
	if(!DIRECTORY_SEPARATOR) define('DIRECTORY_SEPARATOR','/'); // Make sure DIRECTORY_SEPARATOR exists
	
	ini_set('mysql.trace_mode','Off'); // Just in case
	
	//if(!in_array(BASE_PATH.'/assets/includes/tweak.php',get_included_files())) // Check to make sure tweak wasn't already included
	require_once BASE_PATH.'/assets/includes/tweak.php';
	require_once BASE_PATH.'/assets/includes/db.config.php';
	require_once BASE_PATH.'/assets/includes/public.functions.php';
	require_once BASE_PATH.'/assets/includes/shared.functions.php';
	require_once BASE_PATH.'/assets/includes/db.conn.php';
	require_once BASE_PATH.'/assets/includes/clean.data.php';
	$lang = ''; // Fix for 2CO passing lang=en in the URL
	require_once BASE_PATH.'/assets/classes/membertools.php';
	require_once BASE_PATH.'/assets/classes/gallerytools.php';
	require_once BASE_PATH.'/assets/classes/browser.detect.php';
	
	$inc = 1; // Include the version information
	require_once BASE_PATH.'/assets/includes/version.php';
	
	// Detect browser
	$browserObj = new Browser();	
	$browser['mobile'] = ($browserObj->isMobile()) ? 1 : 0;
	$browser['iOS'] = ($browserObj->getPlatform() == Browser::BROWSER_IPHONE or $browserObj->getPlatform() == Browser::BROWSER_IPAD) ? 1 : 0;

	$cookieHost = explode(':',$_SERVER['HTTP_HOST']); // Get the host without the port
	
	$s2TableExist = mysqli_query($db,"show tables like '{$dbinfo[pre]}settings2'"); // Check for settings 2
	if(mysqli_fetch_array($s2TableExist))
		$settingsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}settings, {$dbinfo[pre]}settings2  WHERE {$dbinfo[pre]}settings.settings_id = 1 AND {$dbinfo[pre]}settings2.settings_id = 1");
	else
		$settingsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}settings WHERE settings_id = 1");		

	$config['settings'] = mysqli_fetch_assoc($settingsResult);  // Select settings

	// Check version and redirect to upgrade.php if needed
	if($config['settings']['db_version'] < $config['productVersion'] and PAGE_ID != 'upgrade')
	{
		header("location: upgrade.php");
		exit;
	}
	
	require_once BASE_PATH.'/assets/includes/addons.php'; // Must come after settings are selected to work
	
	@include '/assets/themes/'.$config['settings']['style'].'/config.php'; // Include theme config file
	
	// Fix for settings when an add-on is not present - mainly for demo mode
	if(!addon('rss'))
	{
		$config['settings']['rss_newest'] = 0;
		$config['settings']['rss_popular'] = 0;
		$config['settings']['rss_news'] = 0;
		$config['settings']['rss_galleries'] = 0;
	}
	if(!addon('rating'))
	{
		$config['settings']['rating_system'] = 0;
	}
	
	$librayFolders	= array('originals','samples','thumbs','icons','variations'); // library folders
	
	// Logo for clips
	//$config['settings']['logo'] = '<img src="'.$config['settings']['site_url'].'/assets/logos/'.$config['settings']['mainlogo'].'" class="clipLogo">'; // Now function logo()
	
	/*
	* Get the colorShceme selected and also the images directory
	*/
	$colorScheme = ($config['settings']['color_scheme']) ? $colorScheme = 'style.'.$config['settings']['color_scheme'] : 'style.main';
	$colorSchemeImagesDirectory = (file_exists(BASE_PATH.'/assets/themes/'.$config['settings']['style'].'/'.$colorScheme)) ? $colorScheme : 'images';
	
	date_default_timezone_set('GMT'); // Set the default timezone
	
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
	$siteURL = $protocol . '://' . $_SERVER['HTTP_HOST'];
	$modRewrite = $config['settings']['mod_rewrite']; // Shortcut	
	$nowGMT = gmt_date(); // Get the date and time right now
	$self = buildSelfLink();
	
	if(!isset($_SESSION["initReferrerURL"])) $_SESSION["initReferrerURL"] = $_SERVER["HTTP_REFERER"]; // Store referrer URL in a session
    
	if($config['settings']['cart'] == '2' or $config['settings']['cart'] == '3') $creditSystem = true; // Check if the cart system is active		
	
	if($config['settings']['lightbox'] and $config['settings']['glightbox'] and in_array('lightbox',$installed_addons)) $lightboxSystem = true; // Check if the lightbox for visitors is active		
	
	if($config['settings']['ticketsystem'] and in_array('ticketsystem',$installed_addons)) $ticketSystem = true; // Check if ticket system is active		
		
	if(addon('commenting') and $config['settings']['comment_system']) $commentSystem = true;		
		
	if(addon('tagging') and $config['settings']['tagging_system']) $taggingSystem = true;
	
	$config['settings']['video_autorepeat'] = ($config['settings']['video_autorepeat']) ? 'always' : 'never'; // Convert setting from tinyint to correct value
		
	$cartStatus = ($config['settings']['cart'] != 0) ? 1 : 0; // The status of the cart system
	
	/*
	* Security
	*/
	$blockedReferrers = explode("\n",$config['settings']['blockreferrer']);
	$blockedIPs = explode("\n",$config['settings']['blockips']);	
	$blockedWords = explode("\n",$config['settings']['blockwords']);
	$securityTimestamp = time();	
	$securityToken = md5($config['settings']['serial_number'].$securityTimestamp);
	
	$thumbDetailFields = explode(",",$config['settings']['thumb_details']); // Detail fields to display with thumbnails
	$rolloverDetailFields = explode(",",$config['settings']['rollover_details']); // Detail fields to display with rollovers
	$previewDetailFields = explode(",",$config['settings']['preview_details']); // Detail fields to display with previews
	$rssDetailFields = array('title','description','owner','views','keywords'); // Details fields to display for rss feeds
	
	$config['settings']['business_state'] = getStateName($config['settings']['business_state']); // Had to add this so this can be used globally
	$config['settings']['business_country'] = getCountryName($config['settings']['business_country']);
	
	if(!$_SESSION['formKey'])
		$_SESSION['formKey'] = create_unique(); // For extra protection create a formKey that can be used to verify forms
	
	/*
	* Get tax names
	*/
	$lang['taxAName'] = ($config['settings']['taxa_name_'.$_SESSION['member']['language']]) ? $config['settings']['taxa_name_'.$_SESSION['member']['language']] : $config['settings']['taxa_name'];
	$lang['taxBName'] = ($config['settings']['taxb_name_'.$_SESSION['member']['language']]) ? $config['settings']['taxb_name_'.$_SESSION['member']['language']] : $config['settings']['taxb_name'];
	$lang['taxCName'] = ($config['settings']['taxc_name_'.$_SESSION['member']['language']]) ? $config['settings']['taxc_name_'.$_SESSION['member']['language']] : $config['settings']['taxc_name'];
	$lang['taxMessage'] = ($config['settings']['tax_stm_'.$_SESSION['member']['language']]) ? $config['settings']['tax_stm_'.$_SESSION['member']['language']] : $config['settings']['tax_stm'];
	
	$pageCacheID = md5($_SERVER['REQUEST_URI']); // Create a cache ID for this page
	
	/*
	* Tweak settings
	*/
	$config['digitalSizeCalc'] = $config['settings']['measurement'];
	
	/*
	* Initialize Smarty
	*/
	if(INIT_SMARTY)
	{
		try
		{
			require_once BASE_PATH.'/assets/smarty/Smarty.class.php';
			$smarty = new Smarty;		
			$smarty->compile_dir = BASE_PATH.'/assets/tmp';	
			$smarty->cache_dir = BASE_PATH.'/assets/cache';	
			$smarty->template_dir = BASE_PATH.'/assets/themes/'.$config['settings']['style'];
			$smarty->compile_check = true;
			$smarty->allow_php_templates= true;
			//$smarty->setCaching(true);
			//$smarty->force_compile = false; // If $force_compile is enabled, the cache files will be regenerated every time, effectively disabling caching.
			//$smarty->caching = true;
			$smarty->cache_lifetime = 3600;			
			
			$smarty->registerPlugin("function","content", "content");
			$smarty->registerPlugin("function","logo", "logo");
			$smarty->registerPlugin("function","debugOutput", "debugOutput");
			$smarty->registerPlugin("function","displayCurrency", "displayCurrency");
			$smarty->registerPlugin("function","memberAvatar", "memberAvatar");
			$smarty->registerPlugin("function","productShot", "productShot");
			$smarty->registerPlugin("function","mediaImage", "mediaImage");
			$smarty->registerPlugin("function","linkto", "linkto");			
			//$smarty->register_function('content','content');
			//$smarty->register_function('debugOutput','debugOutput');
			//$smarty->register_function('displayCurrency','displayCurrency');
			//$smarty->register_function('memberAvatar','memberAvatar'); // Register the memberAvatar function with smarty
			//$smarty->register_function('productShot','productShot'); // Register the productShot function with smarty
			//$smarty->register_function('mediaImage','mediaImage'); // Register the mediaImage function with smarty			
			
			$smarty->assign('creditSystem',$creditSystem); // Send the status of the credit system to smarty
			$smarty->assign('currencySystem',currencyCartStatus()); // Send the status of the currency system to smarty
			$smarty->assign('cartStatus',$cartStatus); // Send the status of the cart system to smarty
			$smarty->assign('ticketSystem',$ticketSystem); // Send the status of the ticket system to smarty
			$smarty->assign('commentSystem',$commentSystem); // Comment System status
			$smarty->assign('taggingSystem',$taggingSystem); // Tagging System status
			$smarty->assign('pageID',PAGE_ID);
			$smarty->assign('pageMode',$mode);
			$smarty->assign('securityTimestamp',$securityTimestamp);
			$smarty->assign('securityToken',$securityToken);
			$smarty->assign('access',ACCESS);
			$smarty->assign('browser',$browser);
			$smarty->assign('installedAddons',$installed_addons); // Assign an array of installed add-ons to smarty
			//$smarty->assign('settings',$config['settings']); // Assign settings info to smarty so that they can be used in the template
			$smarty->assign('loginPageURL',"{$siteURL}/login.php"); // Direct link to login page
			$smarty->assign('baseURL',$siteURL); // Assign the base URL as a shortcut
			$smarty->assign('theme',$config['settings']['style']); // Assign the theme as a shortcut - same as $setting.style	
			$smarty->assign('colorScheme',$colorScheme); // Assign the colorScheme css file	
			$smarty->assign('imgPath',$siteURL.'/assets/themes/'.$config['settings']['style'].'/'.$colorSchemeImagesDirectory); // Assign an image path for the theme
			$smarty->assign('blankImg',"{$siteURL}/assets/images/blank.png"); // Shortcut to a blank image - template usage: <img src="{$blankImg}" />
			$smarty->assign('transparentImg',"{$siteURL}/assets/images/transparent.png"); // Shortcut to a transparent image - template usage: <img src="{$transparentImg}" />
			$smarty->assign('self',$self); // Assign the page and variables
			$smarty->assign('fullURL',$self['http'].$self['host'].'/'.$self['uri']); // Assign the full url of the page
			$smarty->assign('formKey',$_SESSION['formKey']); // Pass a key to the function that can be used to protect forms
			if(@ini_get('session.gc_maxlifetime')){
				$smarty->assign('loginTimeout',@ini_get('session.gc_maxlifetime') - 60);
			} else {
				$smarty->assign('loginTimeout',5000);
			}
			if(@file_exists('favicon.ico')){
				$smarty->assign('faviconRef',1);
			}
			
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