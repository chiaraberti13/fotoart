<?php

	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-21-2011
	******************************************************************/
	
	//print_r($_SESSION['member']['permmissions']); // Testing
	
	/*
	* Class for working with gallery data
	*/
	class galleryTools
	{
		private $dbinfo;
		private $galleryID;
		private $memID;
		
		public function __construct($galleryID)
		{
			$this->dbinfo = getDBInfo();
			if($galleryID) $this->galleryID = $galleryID;
		}
	}
	
	/*
	* Class for getting gallery data and member access
	*/
	class galleryLists
	{
		private $dbinfo;
		private $galleryID;
		private $memID;
		private $galleries;
		private $memberOwnedGalleries;
		private $memberPermGalleries;
		
		public function __construct($ownerID=0)
		{
			global $config;
			global $db;
			$this->dbinfo = getDBInfo();
			$nowGMT = gmt_date();
			if($ownerID == 0)
			{
				$galleriesResult = mysqli_query($db,
					"
					SELECT * 
					FROM {$this->dbinfo[pre]}galleries 
					LEFT JOIN {$this->dbinfo[pre]}perms
					ON ({$this->dbinfo[pre]}galleries.gallery_id = {$this->dbinfo[pre]}perms.item_id AND {$this->dbinfo[pre]}perms.perm_area = 'galleries')
					WHERE ({$this->dbinfo[pre]}perms.perm_area = 'galleries' OR {$this->dbinfo[pre]}galleries.everyone = 1)
					AND ({$this->dbinfo[pre]}galleries.active_type = 0 OR {$this->dbinfo[pre]}galleries.active_date < '{$nowGMT}')
					AND ({$this->dbinfo[pre]}galleries.expire_type = 0 OR {$this->dbinfo[pre]}galleries.expire_date > '{$nowGMT}')
					AND {$this->dbinfo[pre]}galleries.active = 1 
					ORDER BY {$this->dbinfo[pre]}galleries.{$config['settings']['gallerySortBy']} {$config['settings']['gallerySortOrder']},{$this->dbinfo[pre]}galleries.name
					"
				); // 	AND {$this->dbinfo[pre]}galleries.owner = 0 // Removed this so all public galleries would be visible
			}
			else
			{	
				$galleriesResult = mysqli_query($db,
					"
					SELECT * 
					FROM {$this->dbinfo[pre]}galleries 
					WHERE owner = {$ownerID}
					ORDER BY {$config['settings']['gallerySortBy']} {$config['settings']['gallerySortOrder']}
					"
				); // Galleries owned by a certain member	
			}
			while($galleryData = mysqli_fetch_assoc($galleriesResult))
			{	
				if($galleryData['everyone'] == 1 or in_array($galleryData['perm_value'],$_SESSION['member']['permmissions']) or $ownerID != 0) // Added this to only pull the data that the member can actually access instead of everything - also pulls password protected galleries
				{	
					$this->galleries[$galleryData['gallery_id']] = $galleryData; // This will only output 1 set of data for each gallery instead of 1 per permission
					
					if(in_array($galleryData['perm_value'],$_SESSION['member']['permmissions']) and $galleryData['album'] == 0) // This is a member specific gallery //$galleryData['perm_value'] and $galleryData['perm_value'] == 'mem'.$_SESSION['member']['mem_id']
					{
						//echo "gd: ".$galleryData['perm_value']."-".$galleryData['gallery_id']."<br>";
						$this->galleries[$galleryData['gallery_id']]['memSpec'] = $_SESSION['member']['mem_id'];
					}
					
					if($config['EncryptIDs'])
						$parms['page'] = "gallery.php?mode=gallery&id=".k_encrypt($galleryData['gallery_id'])."&page=1";
					else
						$parms['page'] = "gallery.php?mode=gallery&id={$galleryData[gallery_id]}&page=1";
					
					$galDefaultLang = ($_SESSION['selectedLanguageSession']) ? $_SESSION['selectedLanguageSession'] : $config['settings']['default_lang'];  // Support for additional languages
					$galleryData['name'] = ($galleryData['name_'.$galDefaultLang]) ? $galleryData['name_'.$galDefaultLang] : $galleryData['name'];	
					$galleryData['description'] = ($galleryData['description_'.$galDefaultLang]) ? $galleryData['description_'.$galDefaultLang] : $galleryData['description'];					
					$this->galleries[$galleryData['gallery_id']]['name'] = $galleryData['name'];
					$this->galleries[$galleryData['gallery_id']]['description'] = $galleryData['description'];
					
					/*
					if($config['EncryptIDs'])
						$this->galleries[$galleryData['gallery_id']]['linkto'] = "gallery.php?mode=gallery&id=".k_encrypt($galleryData['gallery_id'])."&page=1";
					else
						$this->galleries[$galleryData['gallery_id']]['linkto'] = "gallery.php?mode=gallery&id={$galleryData[gallery_id]}&page=1";
					*/
					
					//$this->galleries[$galleryData['gallery_id']]['testerName'] = cleanForSEO($galleryData['name']); // xxxxxx Languages
					
					$parms['seoName'] = cleanForSEO($galleryData['name']);
					
					$this->galleries[$galleryData['gallery_id']]['seoName'] = $parms['seoName']; // xxxxxx Languages				
					if($config['settings']['mod_rewrite']) $parms['page'].="&seoName=".$parms['seoName']; // Link to page with seoName added // Used config modrewrite setting because shortcut var wasn't working for some reason
					
					$this->galleries[$galleryData['gallery_id']]['linkto'] = linkto($parms); // Create the link using SEO if needed	
					
					
				}
				
				if($_SESSION['member']['mem_id'] and $galleryData['owner'] == $_SESSION['member']['mem_id']) // Make sure the mem_id session exists and check if it equals owner
					$this->memberOwnedGalleries[] = $galleryData['gallery_id'];
				
				if(($galleryData['everyone'] == 1 or in_array($galleryData['perm_value'],$_SESSION['member']['permmissions'])) and $galleryData['password'] == '') // Get galleries that member has access to // add addition to memberPermGalleries array when member logs into a gallery
					$this->memberPermGalleries[] = $galleryData['gallery_id']; //
			}
		}
		
		public function getGalleryListData()
		{
			return $this->galleries;	
		}
		
		/*
		* Set the member ID to be used in gallery functions
		*/
		public function setMemberID($memID)
		{
			$this->memID = $memID;
		}
		
		/*
		* Return galleries owned by this member
		*/
		public function getMemberOwnedGalleries()
		{
			return $this->memberOwnedGalleries;
		}
		
		/*
		* Return galleries this member has access to view
		*/
		public function getMemberPermGalleries()
		{
			return $this->memberPermGalleries;
		}
	}
?>