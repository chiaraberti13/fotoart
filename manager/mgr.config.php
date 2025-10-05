<?php	
	###################################################################
	####	MANAGER CONFIG FILE                                    ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 3-5-2008                                     #### 
	###################################################################
	
	# WARNING: DO NOT EDIT ANYTHING BELOW THIS LINE / CHANGING THIS FILE MAY CAUSE THIS SCRIPT TO FUNCTION INCORRECTLY
	
	//error_reporting(E_ALL ^ E_NOTICE); // All but notices
	
	if(!DIRECTORY_SEPARATOR) define('DIRECTORY_SEPARATOR','/'); // Make sure DIRECTORY_SEPARATOR exists
	
	# START THE PAGE LOAD TIMER
	$pltime 						= microtime();
	$pltime 						= explode(" ", $pltime);
	$plstart 						= $pltime[1] + $pltime[0];
	
	# CREATE CONFIG ARRAY
	$config = array();	
	$config['debug_u']				= "ea415e2c68358bedfebb4200bf9706c6"; // DEBUG USERNAME
	
	$config['server_url']			= $_SERVER['HTTP_HOST']; // DOMAIN NAME FOR THE SITE

	$config['server_ip_address']	= $_SERVER['SERVER_ADDR']; // SERVER IP ADDRESS
	$config['manager_dir_name']		= basename(dirname(__FILE__)).DIRECTORY_SEPARATOR; // MANAGER DIRECTORY NAME WITH TRAILING SLASH
	
	$config['base_path']			= dirname(dirname(__FILE__)); // FIND THE BASE PATH TO WHERE THE STORE IS INSTALLED
	define('BASE_PATH',$config['base_path']); // Alias of $config['base_path']
	$config['base_mgr_path']		= $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/";	 // FULL SERVER PATH TO MANAGER WITH TRAILING SLASH
	$finddir 						= explode(DIRECTORY_SEPARATOR,dirname($_SERVER['PHP_SELF'])); array_pop($finddir);
	$pspath 						= implode(DIRECTORY_SEPARATOR,$finddir);
	$config['base_url']				= "http://" . $_SERVER['HTTP_HOST'] . $pspath; // BASE URL FOR PHOTOSTORE - NO TRAILING SLASH
	
	# FOLDERS - GALLERY FOLDERS TO CREATE
	$create_gal_folders 			= array('originals','samples','thumbs','icons','variations');
	
	# INCLUDE VERSION INFORMATION
	$inc=1; include($config['base_path'] . '/assets/includes/version.php'); // SET VERSION FILE AS AN INCLUDE INSTEAD OF DISPLAY
	
	# INCLUDE FILE TO REGISTER THE LANGUAGE FILES FOR THE MANAGER AREA
	include($config['base_path'] . '/assets/includes/reglang.php');
	
	# CALCULATE THE TIMEOUT
	$config['timeout'] = ini_get('session.gc_maxlifetime') + 600;
	
	# CREATABLE FILETYPE
	$createableFiletypes = array('jpg','jpeg','jpe'); // Types of files that can be created by the system
	
	/*
	* Setup wizard
	*/
	if($_GET['wizStep'])
	{
		$_SESSION['wizardCurrentStep'] = $_GET['wizStep'];
	}
	
	if(isset($_GET['wizard']))
	{
		if($_GET['wizard'] == 'on')
		{
			$_SESSION['wizardStatus'] = true;
			$_SESSION['wizardCurrentStep'] = 1;	
			$_SESSION['nextWizardStep'] = 2;		
		}
		else
		{
			$_SESSION['wizardStatus'] = false;
			$_SESSION['admin_user']['wizard'] = 0;
		}
	}
	
	if($_SESSION['wizardStatus'])
	{
		$wizardStep[1] = array('link'=>'mgr.administrators.edit.php?edit=460B1BDD209A534EF0AF1EVYKHF04EE9&wizard=on','text'=>"First we will setup your administrator username and password for the management area. Click <strong>Unencrypt</strong> to see the password. Now enter the username, password and email address you would like to use for your administrator account login and click <strong>Save Changes</strong>. This information can always be changed later. Now click <strong>Next</strong> in the wizard guide area to proceed to the next step.");
		$wizardStep[2] = array('link'=>'mgr.website.settings.php?ep=1','text'=>"Next you will enter your business contact info. Click on the <strong>Contact Info</strong> tab. Enter your business information and click <strong>Save Changes</strong>. Now click <strong>Next</strong> in the wizard guide area to proceed to the next step.");
		$wizardStep[3] = array('link'=>'mgr.software.setup.php?ep=1','text'=>"Next you will setup your preferred number and date formats. Click on the <strong>Numbers</strong> tab and select your preferred settings. Then click on the <strong>Date & Time</strong> tab and make your selections . When done click <strong>Save Changes</strong>. Now click <strong>Next</strong> in the wizard guide area to proceed to the next step.");	
		$wizardStep[4] = array('link'=>'mgr.currencies.php?ep=1','text'=>"Next you will select your preferred currency. Under <strong>Primary</strong> click the check mark next to the currency you would like to use for your store. Now click <strong>Next</strong> in the wizard guide area to proceed to the next step.");
		$wizardStep[5] = array('link'=>'mgr.payment.gateways.php?ep=1','text'=>"Now you will setup a payment gateway to use for your store. Choose a gateway from the <strong>Activate Gateway</strong> dropdown and click the <strong>Activate</strong> button to activate it. Once it loads enter your information for this gateway and click <strong>Save Changes</strong>.");
		$wizardStep[6] = array('link'=>'mgr.library.php?ep=1','text'=>"You are now done setting up your PhotoStore. Please click the <strong>Done</strong> button to close the setup wizard and return to the welcome page.");
		$wizardStep[7] = array('link'=>'mgr.welcome.php?wizard=off','text'=>"");
	}
?>