<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 8-1-2012
	******************************************************************/	
	
	try
	{
		/*
		* Check for a visit session and update the database with a new visit
		*/
		if(!$_SESSION['visited'])
		{
			$visitCount = $config['settings']['site_visits']+1;
			mysqli_query($db,"UPDATE {$dbinfo[pre]}settings SET site_visits = {$visitCount} WHERE settings_id  = 1");
			$_SESSION['visited'] = 1;	
		}
		
		/*
		* Do any member initialization that needs to be done on private pages
		*/
		if(ACCESS == 'private')
		{
			if(addon('contr')) // Only if contributors add-on is installed
			{
				# Grab contributors presonal galleries
				if(!$_SESSION['member']['contrAlbumsQueried'])
				{
					if($_SESSION['member']['mem_id']) // Make sure a member id exists to keep it from grabbing default galleries
					{
						$contrAblumsObj = new galleryLists($_SESSION['member']['mem_id']);
						$_SESSION['member']['contrAlbumsData'] = $contrAblumsObj->getGalleryListData();
						
						$_SESSION['member']['contrAlbumsQueried'] = true; // Set a session that tells that the galleries have already been queried
						
						//print_r($_SESSION['contrGalleriesData']); // Testing
					}
				}			
				$smarty->assign('contrAlbums',$_SESSION['member']['contrAlbumsData']); // Assign contributor albums to smarty
			}
		}
		
		/*
		* Check to see if any member session is available
		*/	
		if(!$_SESSION['member'])
		{
			$memberSess = new memberTools;
			$memberSess->setMemberSession();
			
			$_SESSION['member']['galleryVersion'] = $config['settings']['gal_version']; // Set the gallery version in the member session
		}
		
		if(!$_SESSION['member']) die('No member session exists'); // Make sure there is a member session before going on
		
		/*
		* Check if the member permissions array exists and if not them create an empty array
		*/
		if(!$_SESSION['member']['permmissions'])
			$_SESSION['member']['permmissions'] = array('guest');
		$memberPermissionsArray = $_SESSION['member']['permmissions'];
		$memberPermissionsForDB = implode(",",array_map("wrapSingleQuotes",$memberPermissionsArray));
	
		//print_r($_SESSION['member']['permmissions']);
	
		$smarty->assign('loggedIn',$_SESSION['loggedIn']); // Assign the login status to smarty
		
		/*
		* Grab gallery data if none exists or if member gallery permissions don't exist
		*/
		if(!$_SESSION['galleriesData'] or !$_SESSION['member']['memberPermGalleries'] or ($_SESSION['member']['galleryVersion'] < $config['settings']['gal_version']))
		{
			$galleries = new galleryLists;
			$_SESSION['galleriesData'] = $galleries->getGalleryListData(); // All gallery data	$galleriesData[id]['field']
			
			//print_r($_SESSION['galleriesData']['223']); // Testing
			
			$_SESSION['member']['memberPermGalleries'] = $galleries->getMemberPermGalleries(); // Galleries member has permission to
			$_SESSION['member']['memberOwnedGalleries'] = $galleries->getMemberOwnedGalleries(); // Galleries member owns
			
			$_SESSION['member']['galleryVersion'] = $config['settings']['gal_version']; // Update the gallery version in the member session
		}
		
		/*
		* Assign the permissions and gallery data to smarty
		*/
		$smarty->assign('galleriesData',$_SESSION['galleriesData']);
		$smarty->assign('memberPermGalleries',$_SESSION['member']['memberPermGalleries']); // Galleries member has permissions to
		$smarty->assign('memberOwnedGalleries',$_SESSION['member']['memberOwnedGalleries']); // Galleries member owns	
		
		if(is_array($_SESSION['member']['memberPermGalleries']))
			$memberPermGalleriesForDB = implode(",",array_map("wrapSingleQuotes",$_SESSION['member']['memberPermGalleries'])); // Galleries that member has permissions to converted for DB use
		else
		{
			$memberPermGalleriesForDB = 0;
		}
		
		/*
		* Set the selectedLanguage
		*/
		if(!$_SESSION['selectedLanguageSession'])
		{
			$_SESSION['selectedLanguageSession'] = $config['settings']['default_lang'];
			$_SESSION['member']['language'] = $config['settings']['default_lang']; // Added this just to keep it in the member session also
			//$selectedLanguage = $config['settings']['default_lang'];
		}
		$selectedLanguage = $_SESSION['selectedLanguageSession'];
		$smarty->assign('selectedLanguage',$selectedLanguage); // Assign the details to smarty
		
		// Correct the site title with the proper language
		$config['settings']['site_title'] = ($config['settings']['site_title_'.$selectedLanguage]) ? $config['settings']['site_title_'.$selectedLanguage] : $config['settings']['site_title'];
		
		//echo "lang: {$config['settings']['default_lang']}"; // Testing
		
		/*
		* Set the selectedCurrency
		*/
		if(!$_SESSION['selectedCurrencySession'])
		{
			$_SESSION['selectedCurrencySession'] = $config['settings']['defaultcur'];
			$_SESSION['member']['currency'] = $config['settings']['defaultcur'];
		}
		$selectedCurrency = $_SESSION['selectedCurrencySession'];
		//print_k($selectedCurrency); // Testing
		$smarty->assign('selectedCurrency',$selectedCurrency); // Assign the details to smarty
		
		/*
		* Taxes
		*/
		if(!$_SESSION['tax'])
		{
			if($config['settings']['tax_type'] == 1) // Tax globally
			{
				$_SESSION['tax']['tax_inc'] = $config['settings']['tax_inc'];
				$_SESSION['tax']['tax_a_default'] = $config['settings']['tax_a_default'];
				$_SESSION['tax']['tax_b_default'] = $config['settings']['tax_b_default'];
				$_SESSION['tax']['tax_c_default'] = $config['settings']['tax_c_default'];
				
				$_SESSION['tax']['tax_a_digital'] = $config['settings']['tax_a_digital'];
				$_SESSION['tax']['tax_b_digital'] = $config['settings']['tax_b_digital'];
				$_SESSION['tax']['tax_c_digital'] = $config['settings']['tax_c_digital'];
				
				$_SESSION['tax']['tax_prints'] = $config['settings']['tax_prints'];
				$_SESSION['tax']['tax_digital'] = $config['settings']['tax_digital'];
				$_SESSION['tax']['tax_ms'] = $config['settings']['tax_ms'];
				$_SESSION['tax']['tax_subs'] = $config['settings']['tax_subs'];
				$_SESSION['tax']['tax_shipping'] = $config['settings']['tax_shipping'];
				$_SESSION['tax']['tax_credits'] = $config['settings']['tax_credits'];
			}
			else
			{
				$_SESSION['tax']['tax_inc'] = 0;
				$_SESSION['tax']['tax_a_default'] = 0;
				$_SESSION['tax']['tax_b_default'] = 0;
				$_SESSION['tax']['tax_c_default'] = 0;
				$_SESSION['tax']['tax_a_digital'] = 0;
				$_SESSION['tax']['tax_b_digital'] = 0;
				$_SESSION['tax']['tax_c_digital'] = 0;
				$_SESSION['tax']['tax_prints'] = 0;
				$_SESSION['tax']['tax_digital'] = 0;
				$_SESSION['tax']['tax_ms'] = 0;
				$_SESSION['tax']['tax_subs'] = 0;
				$_SESSION['tax']['tax_shipping'] = 0;
				$_SESSION['tax']['tax_credits'] = 0;	
			}
		}
		$smarty->assign('tax',$_SESSION['tax']); // Assign the details to smarty
		
		/*
		* Create a session array of items in this members lightboxes
		*/
		if(!$_SESSION['lightboxItems'] and $_SESSION['loggedIn'] and in_array('lightbox',$installed_addons)) // Check if lightbox is even available
			getSessionLightboxItems($_SESSION['member']['umem_id']);
			
		if(!$_SESSION['selectedLightbox'])
			$_SESSION['selectedLightbox'] = 0;
		
		/*
		* Do any member initialization that needs to be done when logged in
		*/
		if($_SESSION['loggedIn'])
		{
			mysqli_query($db,"UPDATE {$dbinfo[pre]}members SET last_activity = '{$nowGMT}' WHERE mem_id  = '{$_SESSION[member][mem_id]}'");	 // Update the members last_activity
			
			$memberUpdates = mysqli_fetch_assoc(mysqli_query($db,"SELECT credits FROM {$dbinfo[pre]}members WHERE mem_id = '{$_SESSION[member][mem_id]}'"));
			if($memberUpdates) $_SESSION['member']['credits'] = $memberUpdates['credits']; // Get member credits so they are up to date
		}
		
		/*
		* Check to see if the visitor should be able to submit comments
		*/
		if($_SESSION['loggedIn'])
			$allowCommenting = ($commentSystem and $_SESSION['member']['membershipDetails']['comments']) ? 1 : 0; // The logged in setting
		else
			$allowCommenting = ($commentSystem and $config['settings']['comment_system_lr']) ? 1 : 0; // The guest setting
		$_SESSION['member']['allowCommenting'] = $allowCommenting;
		
		/*
		* Check to see if the visitor should be able to submit tags
		*/
		if($_SESSION['loggedIn'])
			$allowTagging = ($taggingSystem and $_SESSION['member']['membershipDetails']['tagging']) ? 1 : 0; // The logged in setting
		else
			$allowTagging = ($taggingSystem and $config['settings']['tagging_system_lr']) ? 1 : 0; // The guest setting
		$_SESSION['member']['allowTagging'] = $allowTagging;
		
		$smarty->assign('member',$_SESSION['member']); // Assign the details to smarty
	}
	catch(Exception $e)
	{
		die(exceptionError($e));	
	}
	
	
?>