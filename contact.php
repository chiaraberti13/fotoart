<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-21-2011
	*  Updated: 2025 - reCAPTCHA v2 semplice per fotoartpuzzle.it
	******************************************************************/
	
	define('BASE_PATH',dirname(__FILE__)); // Define the base path
	define('PAGE_ID','contact'); // Page ID
	define('ACCESS','public'); // Page access type - public|private
	define('INIT_SMARTY',true); // Use Smarty
	
	require_once BASE_PATH.'/assets/includes/session.php';
	require_once BASE_PATH.'/assets/includes/initialize.php';
	require_once BASE_PATH.'/assets/includes/commands.php';
	require_once BASE_PATH.'/assets/includes/init.member.php';
	require_once BASE_PATH.'/assets/includes/security.inc.php';
	require_once BASE_PATH.'/assets/includes/language.inc.php';
	require_once BASE_PATH.'/assets/includes/cart.inc.php';
	require_once BASE_PATH.'/assets/includes/affiliate.inc.php';

	//define('META_TITLE',''); // Override page title, description, keywords and page encoding here
	//define('META_DESCRIPTION','');
	//define('META_KEYWORDS','');
	//define('PAGE_ENCODING','');
	
	define('META_TITLE',$lang['contactUs'].' &ndash; '.$config['settings']['site_title']); // Assign proper meta titles
	
	require_once BASE_PATH.'/assets/includes/header.inc.php';
	require_once BASE_PATH.'/assets/includes/errors.php';
	
	/*
	* Smarty Template
	*/
	try
	{
		if($_POST)
		{
			foreach($_POST as $key => $value)
				$form[$key] = $value; // Create the form prefill values
				
			$error = 0;
			if($config['settings']['contactCaptcha'] == 1)
			{
				// reCAPTCHA v2 - Soluzione semplice per fotoartpuzzle.it
				if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
				{
					$secret_key = '6LfIhrgrAAAAABvNXL9LDLCZq3GES64eajaCnPSV';
					$response = $_POST['g-recaptcha-response'];
					
					// Verifica con Google reCAPTCHA v2
					$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $response . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
					
					// Prova con file_get_contents
					$verify = @file_get_contents($url);
					
					if($verify !== FALSE)
					{
						$result = json_decode($verify, true);
						if(!$result || !$result['success'])
						{
							$error = 1;
						}
					}
					else if(function_exists('curl_init'))
					{
						// Fallback con cURL se file_get_contents non funziona
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_TIMEOUT, 10);
						
						$curl_response = curl_exec($ch);
						$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						curl_close($ch);
						
						if($httpcode == 200 && $curl_response !== FALSE)
						{
							$result = json_decode($curl_response, true);
							if(!$result || !$result['success'])
							{
								$error = 1;
							}
						}
						else
						{
							$error = 1; // cURL failed
						}
					}
					else
					{
						$error = 1; // Neither file_get_contents nor cURL worked
					}
				}
				else
				{
					$error = 1; // reCAPTCHA response missing
				}
			}
			
			if($_POST['form']['email'] && $error == 0)
			{
				try
				{	
					$badChars = array('>','<','script','[',']');
					
					$_POST['form']['question'] = str_replace($badChars,'***',$_POST['form']['question']);
					
					foreach($badChars as $badChar)
					{
						if(strpos($_POST['form']['email'],$badChar) !== false or strpos($_POST['form']['name'],$badChar) !== false)
						{
							header("location: error.php?eType=invalidQuery");
							exit;
						}	
					}
					
					$smarty->assign('form',$_POST['form']);
					//$smarty->assign('formEmail',$_POST['form']['email']);
					//$smarty->assign('formQuestion',$_POST['form']['question']);
						
					$content = getDatabaseContent('contactFormEmailAdmin',$config['settings']['lang_file_mgr']); // Get content and force language for admin
					
					$content['name'] = $smarty->fetch('eval:'.$content['name']);
					$content['body'] = $smarty->fetch('eval:'.$content['body']);
					
					$options['replyEmail'] = $_POST['form']['email'];
					$options['replyName'] = $_POST['form']['name'];
					
					kmail($config['settings']['sales_email'],$config['settings']['business_name'],$config['settings']['sales_email'],$lang['contactFromName'],$content['name'],$content['body'],$options); // Send email to sales email		
					
					$smarty->assign("contactNotice",'contactMessage');
					unset($form);
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
					exit;
				}
			}
			else
				if($error == 1){
					$smarty->assign("contactNotice",'captchaError'); // Incorrect Captcha
				} else {
					$smarty->assign("contactNotice",'contactError'); // No email specified
				}
		}
		
		$smarty->assign('form',$form); // Assign values to prefill the form
		$smarty->assign("businessCountryName",getCountryName($config['settings']['business_country']));
		$smarty->assign("businessStateName",getStateName($config['settings']['business_state']));
		$smarty->display('contact.tpl');
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
	}
	
	include BASE_PATH.'/assets/includes/debug.php';
	if($db) mysqli_close($db); // Close any database connections
?>