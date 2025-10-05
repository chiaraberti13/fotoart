<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-16-2012
	******************************************************************/
	
	/* Build Country List */
	function getCountryList($selectedLanguage)
	{
		global $dbinfo, $db;
		$countryResult = mysqli_query($db,"SELECT *	FROM {$dbinfo[pre]}countries WHERE active = 1 AND deleted = 0"); // Select countries
		while($country = mysqli_fetch_array($countryResult))
		{
			$countries[$country['country_id']] = ($country['name_'.$selectedLanguage]) ? $country['name_'.$selectedLanguage] : $country['name'];	
		}
		asort($countries);
		return $countries;
	}
	
	/*
	* Read file in chunks to prevent errors
	*/
	function readfileChunked($filename,$retbytes=true)
	{ 
   		$chunksize = 1*(1024*1024); // how many bytes per chunk 
   		$buffer = ''; 
   		$cnt =0; 
   		// $handle = fopen($filename, 'rb'); 
   		$handle = fopen($filename, 'rb'); 
   		
		if($handle === false)
      		return false; 

   		while (!feof($handle))
		{ 
       		$buffer = fread($handle, $chunksize); 
       		echo $buffer; 
       		ob_flush(); 
       		flush(); 
       		
			if($retbytes)
	        	$cnt += strlen($buffer); 
    	} 
      
		$status = fclose($handle); 
		if($retbytes && $status)
			return $cnt; // return num. bytes delivered like readfile() does. 
			
		return $status; 
	}
	
	/*
	* Redirect to error page on non numeric ID
	*/
	function idCheck($idCheck,$messageLang=NULL)
	{
		global $siteURL;
		if(!is_numeric($idCheck))
		{
			//echo "non numeric - {$idCheck} - ";
			header("location: {$siteURL}/error.php");
			exit;
		}
	}
	
	/*
	* Add a commission record to the db
	*/
	function addCommissionRecord($commission)
	{
		global $dbinfo, $config, $nowGMT, $db;
		
		// comtype - cur | cred | sub
		
		$commission['perCreditValue'] = $config['settings']['credit_com'];
		
		if(!$commission['quantity']) $commission['quantity'] = 1;
		
		if(!$commission['comtype'] or empty($commission['comtype'])) $commission['comtype'] = 'cur';
		
		if($commission['owner'])
		{
			mysqli_query($db,
			"
				INSERT INTO {$dbinfo[pre]}commission   
				(
					contr_id,
					oitem_id,
					omedia_id,
					com_total,
					com_credits,
					item_percent,
					mem_percent,
					comtype,
					item_qty,
					per_credit_value,
					order_date
				) 
				VALUES 
				(
					'{$commission[owner]}',
					'{$commission[oitemID]}',
					'{$commission[mediaID]}',
					'{$commission[comTotal]}',
					'{$commission[comCredits]}',
					'{$commission[itemPercent]}',
					'{$commission[memPercent]}',
					'{$commission[comtype]}',
					'{$commission[quantity]}',
					'{$commission[perCreditValue]}',
					'{$nowGMT}'
				 )
				"
			);
			return true;
		}
		else
			return false;
	}
		
	/*
	* Check to see if contributor directories exist / if not create them
	*/
	function checkContrDirectories()
	{
		global $config, $dbinfo, $librayFolders, $db;		
		if(!$_SESSION['member']['mem_id']) // Check to make sure a member ID is available
		{
			$erorrStatus = true;
			$erorr = 'No session member ID.';
		}
		
		$memID = $_SESSION['member']['mem_id']; // Shortcut
		
		$contrFID = zerofill($_SESSION['member']['mem_id'],5);
		$incomingFolder = BASE_PATH.'/assets/contributors/contr'.$contrFID;
		$libraryFolder = $config['settings']['library_path'].'/contr'.$contrFID;
		
		if(!file_exists($incomingFolder)) // See if incoming folder exists
		{
			@mkdir($incomingFolder); // Create contributors import folder
			@chmod($incomingFolder,0777);
			@copy(BASE_PATH.'/assets/index.html',$incomingFolder.'/index.html'); // copy an index.html into that dir
		}
		
		if(!file_exists($incomingFolder)) // Recheck if folder exists
		{
			$erorrStatus = true; // die('No import folder exists for this member.');
			$erorr = 'No import folder exists for this member.';
		}
			
		if(!file_exists($libraryFolder)) // See if library folder exists
		{
			@mkdir($libraryFolder); // Create contributors library folder
			@chmod($libraryFolder,0777);
			
			foreach($librayFolders as $folderName) // Create each sub folder
			{
				@mkdir($libraryFolder.'/'.$folderName); // Create contributors library sub folder
				@chmod($libraryFolder.'/'.$folderName,0777);					
				@copy(BASE_PATH.'/assets/index.html',$libraryFolder.'/'.$folderName.'/index.html'); // copy an index.html into that dir
			}
		}
		
		if(!file_exists($libraryFolder)) // Recheck if folder exists
		{
			$erorrStatus = true; //die('No library folder exists for this member.');
			$error = 'No library folder exists for this member.';
		}
		
		// Check for contributors folder ID
		$folderCheck = mysqli_query($db,"SELECT folder_id FROM {$dbinfo[pre]}folders WHERE owner = '{$memID}' AND name = 'contr{$contrFID}'");
		if($folderCheckRows = mysqli_num_rows($folderCheck))
		{
			$folder = mysqli_fetch_assoc($folderCheck);
			$folderDBID = $folder['folder_id'];
		}
		else // Need to create entry in DB
		{
			$ufolderID = create_unique2(); // Unique folder ID			
			$encFolderName = md5('contr'.$contrFID.$config['settings']['serial_number']); // Encrypted folder ID
			
			// Insert
			mysqli_query($db,
				"
				INSERT INTO {$dbinfo[pre]}folders  
				(
					ufolder_id,
					name,
					owner,
					enc_name
				)
				VALUES
				(
					'{$ufolderID}',
					'contr{$contrFID}',
					'{$memID}',
					'{$encFolderName}'
				)
				"
			);
			$folderDBID = mysqli_insert_id($db); // New folder ID
		}
		
		//echo $folderDBID; exit; // Testing
		
		$contrDir['folderDBID'] = $folderDBID;
		$contrDir['erorrStatus'] = $erorrStatus;
		$contrDir['error'] = $error;
		$contrDir['contrFID'] = $contrFID;
		$contrDir['contrFolderName'] = 'contr'.$contrFID;
		$contrDir['incomingFolder'] = $incomingFolder;
		$contrDir['libraryFolder'] = $libraryFolder;
		$contrDir['contrID'] = $_SESSION['member']['mem_id'];
		
		return $contrDir;
	}			
	
	/*
	* Get the contributor album ID from the passed unique ID
	*/
	function getAlbumID($uAlbumID)
	{
		foreach($_SESSION['member']['contrAlbumsData'] as $key => $album)
		{
			if($album['ugallery_id'] == $uAlbumID)
				return $album['gallery_id'];
		}
	}
	
	/*
	* Output a debug DIV
	*/
	function debugOutput($parms,&$smarty)
	{
		if(is_array($parms['value']))
		{
			echo "<div class='debug'>";
				echo "<h1>Debug: {$parms[title]}</h1>";
				echo "<ul>";
				foreach($parms['value'] as $key => $value)
				{
					echo "<li>";
						echo "<strong>{$key}:</strong>";
						if(is_array($value))
						{
							echo "<ul>";
								foreach($value as $subKey => $subValue)	echo "<li><strong>{$subKey}:</strong> {$subValue}</li>";
							echo "</ul>";
						}
						else
							echo "{$value}";					
					echo "</li>";
				}
				echo "</ul>";
			echo "</div>";
		}
		else
		{
			echo "<div class='debug'><strong>{$parms[title]}:</strong> {$parms[value]}</div>";
		}
	}
	
	/*
	* Build a tag cloud
	*/
	function tagCloud($random=true,$sort)
	{
		global $dbinfo, $installed_addons, $db, $selectedLanguage, $config;
	
		$tagCloudResult = mysqli_query($db,
			"
			SELECT * 
			FROM {$dbinfo[pre]}keywords 
			LEFT JOIN {$dbinfo[pre]}media 
			ON {$dbinfo[pre]}keywords.media_id = {$dbinfo[pre]}media.media_id 
			WHERE {$dbinfo[pre]}keywords.memtag = 0 
			AND {$dbinfo[pre]}media.media_id != ''
			"
		);
		while($tag = mysqli_fetch_assoc($tagCloudResult))
		{
			if(strtoupper($selectedLanguage) == $tag['language'] or ($selectedLanguage == $config['settings']['lang_file_mgr'] and !$tag['language']))
			{			
				$cleanKeyword = cleanString($tag['keyword']);
				$tagsArray[$cleanKeyword]['keyword'] = $tag['keyword'];
				$tagsArray[$cleanKeyword]['count']++; // This only shows the count for keywords - the search page will show results from searching title, description, etc
			}
		}
		
		/*
		if(in_array("tagging",$installed_addons))
		{
			$tagCloudResult = mysqli_query($db,
				"
				SELECT * 
				FROM {$dbinfo[pre]}media_tags  
				"
			); // xxxxxxxxx Language
			while($tag = mysqli_fetch_assoc($tagCloudResult))
			{
				$cleanKeyword = cleanString($tag['tag']);
				$tagsArray[$cleanKeyword]['keyword'] = $tag['tag'];
				$tagsArray[$cleanKeyword]['count']++;
			}
		}
		*/
		
		if(count($tagsArray) > 1)
		{
			$smallFont = 10;
			$largeFont = 36;
			
			$spread = $largeFont - $smallFont;
			$numOfTags = count($tagsArray);
			
			if($numOfTags > $spread)
				$step = round($numOfTags/$spread);
			else
				$step = round($spread/$numOfTags);
			
			foreach($tagsArray as $key => $tag)
				$newTagArray[$key] = $tag['count']; // Make a new array just to get the counts in the correct order
			
			if($sort == "keyword"){
				ksort($tagsArray); // Order by keyword
			} else {
				asort($newTagArray); // Order by count
			}
		
			$currentSize = $smallFont; // Set the starting font size
			foreach($newTagArray as $key => $tag)
			{
				$tagsArray[$key]['fontSize'] = $currentSize;
				$currentSize+=$step; // Next font size up
			}
			
			if($random) shuffle($tagsArray);
						
			return $tagsArray;
		}
		else
			return false;
	}
	
	/*
	* Clear the cart sessions
	*/
	function clearCartSession()
	{
		unset($_SESSION['cartTotalsSession']);
		unset($_SESSION['cartInfoSession']);
		unset($_SESSION['uniqueOrderID']);
		unset($_SESSION['cartCouponsArray']);
		unset($_SESSION['packagesInCartSession']);
		
		if($_COOKIE['cart']){ unset($_COOKIE['cart']); } // Unset the cart cookie
	}
	
	/*
	* Pull the currency info from the db
	*/
	function getCurrencyInfo($id)
	{
		global $config, $dbinfo, $db;
		$currencyResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}currencies WHERE currency_id = '{$id}'"); // Select currency
		$currency = mysqli_fetch_assoc($currencyResult);
		// xxxxxxxxxxxxxxxxxx check language
		return $currency;	
	}
	
	/*
	* Clear the member session
	*/
	function memberSessionDestroy()
	{
		unset($_SESSION['member']); // Clear all member data
		unset($_SESSION['galleriesData']); // Clear all gallery data
		unset($_SESSION['tax']); // Clear all tax data
		unset($_SESSION['lightboxItems']); // Clear the lightbox items session
		unset($_SESSION['selectedLightbox']); // Clear the selected lightbox
		//unset($_SESSION['selectedLanguageSession']);
		unset($_SESSION['selectedCurrencySession']);
		$_SESSION['loggedIn'] = 0;
	}
	
	/*
	* Build gateway form
	*/
	function buildGatewayForm($submitURL, $fields, $formSubmitMethod='post')
	{
		if($submitURL == 'stripe'){
			if($fields) foreach($fields as $fieldKey => $field)	$form .= "<input type='hidden' name='{$fieldKey}' value='{$field}' />\n";
		}
		else
		{
			if(!$formSubmitMethod)
				$formSubmitMethod = 'post'; // make sure $formSubmitMethod is set
			
			$form = "<form action='{$submitURL}' method='{$formSubmitMethod}' name='gatewayForm' id='gatewayForm'>";
			if($fields)
			   foreach($fields as $fieldKey => $field)	$form .= "<input type='hidden' name='{$fieldKey}' value='{$field}' />";
			$form .= "</form>";	
		}
		return $form;
	}
	
	/*
	* Get gateway settings from db
	*/
	function getGatewayInfoFromDB($gateway)
	{
		global $config, $dbinfo, $db;
		
		$gatewayInfoResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}paymentgateways WHERE gateway = '{$gateway}'");
		while($gatewayInfo = mysqli_fetch_array($gatewayInfoResult))
			$gatewaySetting[$gatewayInfo['setting']] = $gatewayInfo['value'];
		return $gatewaySetting;
	}
	
	/*
	* Get the name and url variables from the current page
	*/
	function buildSelfLink()
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
		$pageURL .= "://";
		$self['http'] = $pageURL;
		$self['uri'] =  substr($_SERVER['REQUEST_URI'],1);
		$self['page'] =  substr($_SERVER['PHP_SELF'],1);
		$self['vars'] =  $_SERVER['QUERY_STRING'];
		$self['host'] = $_SERVER['HTTP_HOST'];
		$self['sepChar'] = (strpos($self['uri'],'?')) ? '&': '?';		
		return $self;
	}
	
	
	/*
	* Current page URL
	*/
	function curPageURL()
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
		$pageURL .= "://";
		
		$directories = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/'));
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$directories; //$_SERVER["REQUEST_URI"]
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$directories; //$_SERVER["REQUEST_URI"]
		
		$pageURL =str_replace($_SERVER['PHP_SELF'],'',$pageURL);
		
		return $pageURL;
	}
	
	function pageLink()
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
		$pageURL .= "://";
		return $pageURL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/*
	* Find the difference in seconds from one date to another
	*/
	function secondsDiff($startDate,$endDate)
	{
		$startDate = date("U",strtotime($startDate));
		$endDate = date("U",strtotime($endDate));
		$secondsDiff = $endDate - $startDate;
		return $secondsDiff;
	}
	
	/*
	* Pull content from the database to be used in the template
	* Usage in template: {content id=1234} (where 1234 is the ID of the content area found in the manager)
	* Use striphtml=1 to remove HTML from the content
	* Use titleOnly=1 to only return the title
	*/
	function content($params,&$smarty)
	{	
		global $config, $member;
		//$content = new databaseContent($params[id]);
		//$dbcontent = $content->getContent();
		
		$content = getDatabaseContent($params[id]);
		$content['name'] = $smarty->fetch('eval:'.$content['name']);
		$content['body'] = $smarty->fetch('eval:'.$content['body']);
		
		//$dbcontent = getContent($params[id]);
		
		if($params['titleOnly'])
			$returnContent = $content['name'];
		else
			$returnContent = $content['body'];
		
		if($params['striphtml'])
			return strip_tags($returnContent); // Strip html tags
		else
			return $returnContent;
	}
	
	/*
	* Digital Prep
	* Get additional information, license, size, etc from an item in the cart
	*/
	function digitalPrep($dsp,$media)
	{
		global $config, $dbinfo, $lang, $selectedLanguage, $db;
		
		if($dsp == 0)
		{
			$digital['ds_id'] = 0;
			$digital['width'] = $media['width'];
			$digital['height'] = $media['height'];
			$digital['format'] = $media['format'];
			$digital['license'] = $media['license'];
			$digital['name'] = $lang['original'];
			
			$digital['licenseLang'] = ($media['lic_name_'.$selectedLanguage]) ? $media['lic_name_'.$selectedLanguage] : $media['lic_name'];
			
			/*
			// License type and name
			switch($media['license'])
			{
				case "cu": // Contact us
					$digital['licenseLang'] = 'mediaLicenseCU';
				break;
				case "rf": // Royalty Free
					$digital['licenseLang'] = 'mediaLicenseRF';
				break;
				case "ex": // Extended License
					$digital['licenseLang'] = 'mediaLicenseEX';
				break;
				case "eu": // Editorial Use
					$digital['licenseLang'] = 'mediaLicenseEU';
				break;
				case "rm": // Rights Managed
					$digital['licenseLang'] = 'mediaLicenseRM';
				break;
				case "fr": // Free Download
					$digital['licenseLang'] = 'mediaLicenseFR';
				break;						
			}
			*/
			
			// File/profile type
			switch($media['dsp_type'])
			{
				case "photo":
					// Get print sizes
					if($config['digitalSizeCalc'] == 'i')
					{
						$digital['widthIC'] = round($media['width']/$config['dpiCalc'],1).'"';
						$digital['heightIC'] = round($media['height']/$config['dpiCalc'],1).'"';
					}
					else
					{
						$digital['widthIC'] = round(($media['width']/$config['dpiCalc']*2.54),1).'cm';
						$digital['heightIC'] = round(($media['height']/$config['dpiCalc']*2.54),1).'cm';
					}
				break;
				case "video":
					// Print sizes not needed
				break;
				case "other":
					// Print sizes not needed
				break;
			}
		}
		else
		{	
			// Check for a customized record
			$customizedResult = mysqli_query($db,
				"
				SELECT *
				FROM {$dbinfo[pre]}media_digital_sizes 
				WHERE ds_id = '{$dsp}' 
				AND media_id = '{$media[media_id]}'
				"
			);
			if($customizedRows = mysqli_num_rows($customizedResult))
				$customized = mysqli_fetch_array($customizedResult);
			
			$digitalResult = mysqli_query($db,
				"
				SELECT * FROM {$dbinfo[pre]}digital_sizes 
				WHERE ds_id = '{$dsp}'
				"
			); // Select digital profile here
			$digital = mysqli_fetch_assoc($digitalResult);
		
			if($customized['customized'])
			{
				$digital['width'] = ($customized['width']) ? $customized['width'] : $digital['width'];
				$digital['height'] = ($customized['height']) ? $customized['height'] : $digital['height'];				
				$digital['license'] = $customized['license'];
			}
			
			//echo $customizedRows; 
	
			// If real_sizes is set then calculate the real width and height of this size after it is scaled from the original
			if($digital['real_sizes'] && $digital['delivery_method'] != 3)
			{
				// Landscape
				if($media['width'] >= $media['height'])
				{
					$scaleRatio = $digital['width']/$media['width'];									
					$width = $digital['width'];
					$height = round($media['height']*$scaleRatio);
				}
				// Portrait
				else
				{
					$scaleRatio = $digital['height']/$media['height'];									
					$width = round($media['width']*$scaleRatio);
					$height = $digital['height'];
				}
			}
			else
			{
				if($digital['delivery_method'] == 3){
					$width = $media['width'];
					$height = $media['height'];
				} else {
					$width = $digital['width'];
					$height = $digital['height'];
				}	
			}
			
			
			$digital['width'] = $width;
			$digital['height'] = $height;
			
			// License
			$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses WHERE license_id = '{$digital[license]}'");
			$license = mysqli_fetch_assoc($licenseResult);					
			$digital['license'] = $license['lic_purchase_type'];
			$digital['licenseLang'] = ($license['lic_name_'.$selectedLanguage]) ? $license['lic_name_'.$selectedLanguage] : $license['lic_name'];
			
			/*
			// License type and name
			switch($digital['license'])
			{
				case "cu": // Contact us
					$digital['licenseLang'] = 'mediaLicenseCU';
				break;
				case "rf": // Royalty Free
					$digital['licenseLang'] = 'mediaLicenseRF';
				break;
				case "ex": // Extended License
					$digital['licenseLang'] = 'mediaLicenseEX';
				break;
				case "eu": // Editorial Use
					$digital['licenseLang'] = 'mediaLicenseEU';
				break;
				case "rm": // Rights Managed
					$digital['licenseLang'] = 'mediaLicenseRM';
				break;
				case "fr": // Free Download
					$digital['licenseLang'] = 'mediaLicenseFR';
				break;						
			}
			*/
			
			//echo "test-".$digital['license'];
			
			// File/profile type
			switch($digital['dsp_type'])
			{
				case "photo":
					if($config['digitalSizeCalc'] == 'i')
					{
						$digital['widthIC'] = round($width/$config['dpiCalc'],1).'"';
						$digital['heightIC'] = round($height/$config['dpiCalc'],1).'"';
					}
					else
					{
						$digital['widthIC'] = round(($width/$config['dpiCalc']*2.54),1).'cm';
						$digital['heightIC'] = round(($height/$config['dpiCalc']*2.54),1).'cm';
					}
				break;
				case "video":										
				break;
				case "other":										
				break;
			}
	
		}
		return $digital;
	}
	
	/*
	* Get content from the DB and prep it for display
	*/
	class databaseContent
	{
		public $dbinfo;
		public $config;
		public $dbcontent;
		public $languageID;
		public $memberInfo;
		public $mgrlang;
		public $siteURL;
		public $invoiceInfo;
		
		public function __construct($contentID)
		{
			global $config;
			global $siteURL;
			global $db;
			
			$this->config = $config;
			
			$this->dbinfo = getDBInfo();
			
			$this->languageID = ''; // Get members language
			
			$this->siteURL = $siteURL;
			
			$dbcontent_result = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}content WHERE content_id = {$contentID}"); // Select content
			// xxxx rows
			$this->dbcontent = mysqli_fetch_assoc($dbcontent_result);
			
			//if($_SESSION['member']) $this->memberInfo = $_SESSION['member']; // Use session member info if it exists
		}
		
		/*
		* Pass member info that can be used for replacing clips
		*/
		public function setMemberInfo($memberInfo)
		{
			$this->memberInfo = $memberInfo;
		}
		
		/*
		* Pass invoice info that can be used for replacing clips
		*/
		public function setInvoiceInfo($invoiceInfo)
		{
			$this->invoiceInfo = $invoiceInfo;
		}
		
		/*
		* Pass order info that can be used for replacing clips
		*/
		public function setOrderInfo()
		{
			
		}
		
		/*
		* Set the language
		*/
		public function setLanguage($useLanguage='')
		{
			if($useLanguage)
				$this->languageID = $useLanguage;
			else
				$this->languageID = $this->config['settings']['lang_file_mgr']; // Get members language
		}
		
		/*
		* Include manager language files
		*/
		public function includeManagerLang($useLanguage='')
		{			
			if($useLanguage)
				$activeMgrLanguage = $useLanguage;
			else
				$activeMgrLanguage = $this->config['settings']['lang_file_mgr']; // Get members language
			
			require_once BASE_PATH.'/assets/includes/reglang.php';	// Get the registered language files to read in
			foreach($regmgrfiles as $value)
			{
				if(file_exists(BASE_PATH."/assets/languages/{$activeMgrLanguage}/{$value}"))
					@include BASE_PATH."/assets/languages/{$activeMgrLanguage}/{$value}";
				else
					@include BASE_PATH."/assets/languages/english/{$value}";
			}
			$this->mgrlang = $mgrlang;
		}
		
		/*
		* Replace clips with member details
		*/
		public function updateMemberContent($content)
		{
			switch($this->memberInfo['status'])
			{
				case '0':
					$statusLang = $this->mgrlang['gen_closed'];
				break;
				case '1':
					$statusLang = $this->mgrlang['gen_active'];
				break;
				case '2':
					$statusLang = $this->mgrlang['gen_pending'];
				break;
			}
			
			$content = str_replace('{$member.status}',$statusLang,$content); // Language for active/pending
			
			//if($this->memberInfo['primaryAddress']['address_2'])
			//{
			//}
			
			if($this->memberInfo['primaryAddress'])
			{
				foreach($this->memberInfo['primaryAddress'] as $key => $value)
					$content = str_replace('{$member.primaryAddress.'.$key.'}',$value,$content);
			}
			
			if($this->memberInfo['primaryAddress'])
			{
				foreach($this->memberInfo as $key => $value)
					$content = str_replace('{$member.'.$key.'}',$value,$content);
			}
				
			$content = str_replace('{$member.unencryptedPassword}','xxxxxx',$content); // Unencrypted Pass
			
			return $content;
		}
		
		/*
		* Replace clips with invoice details
		*/
		public function updateInvoiceContent($content)
		{
			$itemsTable = '<table class="invoiceItems">';
			$itemsTable.="<tr><th>Quantity</th><th>Description</th><th>Cost</th></tr>";
			
			foreach($this->invoiceInfo['items'] as $itemKey => $item)
			{
				$itemsTable.="<tr><td>{$item[quantity]}</td><td>{$item[description]}</td><td>{$item[price_total][display]}</td></tr>";
			}
			
			$itemsTable.='</table>';
			
			$content = str_replace('{$invoice.items}',$itemsTable,$content);
			
			foreach($this->invoiceInfo as $key => $value)
				$content = str_replace('{$invoice.'.$key.'}',$value,$content);
			
			return $content;
		}
		
		public function updateConfigContent($content)
		{			
			
			$logo = "<img src='".$this->siteURL.'/assets/logos/'.$this->config['settings']['mainlogo']."' />";
			
			$content = str_replace('{$config.settings.url}',$this->siteURL,$content); // Site URL			
			$content = str_replace('{$config.settings.logo}',$logo,$content); // Logo
			
			foreach($this->config['settings'] as $key => $value)
				$content = str_replace('{$config.settings.'.$key.'}',$value,$content);
		
			return $content;
		}
		
		public function updateLanguageContent($content)
		{
			// xxxxx Replace content from the language file - {$lang.something}
			return $content;
		}
		
		/*
		* Output the cleaned up content
		*/
		public function getContent()
		{
			$content = $this->dbcontent['content']; // xxx Get correct language content
			$name = $this->dbcontent['name']; // xxx Get correct language title/name
			
			if($this->memberInfo) // Only do this if memberInfo exists to replace
			{
				$content = $this->updateMemberContent($content);
				$name = $this->updateMemberContent($name);
			}
			
			if($this->invoiceInfo) // Only do this if invoiceInfo exists to replace
			{
				$content = $this->updateInvoiceContent($content);
			}
			
			$content = $this->updateLanguageContent($content);
			
			$content = $this->updateConfigContent($content);
			$name = $this->updateConfigContent($name);
			
			$this->dbcontent['content'] = $content; // Add the fixed content back to the array
			$this->dbcontent['name'] = $name; // Add the fixed name back to the array
			
			return $this->dbcontent;
		}
	}
	
	/*
	function getContent($id,$memberInfo=0,$useLanguage=0)
	{
		global $dbinfo, $config, $db;
		// need to grab the correct language xxxxxxxxxxxxxxxxxxxxxxx
		if($useLanguage)
		{
			// xxxx use the language that was passed
		}
		else
		{
			// xxxx Get the language currently selected
		}
		
		$dbcontent_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content WHERE content_id = {$id}"); // Select content
		$dbcontent = mysqli_fetch_array($dbcontent_result);
		
		// need to replace variables xxxxxxxxxxxxxxxxx		
		$dbcontent['content'] = str_replace('search','replace',$dbcontent['content']);
		
		if($memberInfo)
		{
			$dbcontent['content'] = str_replace('{memberName}',$memberInfo['f_name'].' '.$memberInfo['l_name'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberFirstName}',$memberInfo['f_name'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberLastName}',$memberInfo['l_name'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberEmail}',$memberInfo['email'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberPhone}',$memberInfo['phone'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberCompanyName}',$memberInfo['comp_name'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberWebsite}',$memberInfo['website'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberAddress}',$memberInfo['address'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberAddress2}',$memberInfo['address_2'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberCity}',$memberInfo['city'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberState}',$memberInfo['state'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberPostalCode}',$memberInfo['postal_code'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberCountry}',$memberInfo['country'],$dbcontent['content']);
			$dbcontent['content'] = str_replace('{memberAccountStatus}','xxxx',$dbcontent['content']); // xxxx Language for active/pending?
		}
		
		return $dbcontent;
	}
	*/
	
	/*
	* Wrap a value in single quotes
	*/
	function wrapSingleQuotes($val)
	{
		return "'".$val."'";
	}
	
	/*
	* Create img link to product shot
	*/
	function productShot($params)
	{
		global $siteURL;		
		$imglink = $siteURL."/productshot.php?";		
		foreach($params as $key => $value)
			$imglink.= "{$key}=$value&";
		$imglink = substr($imglink,0,strlen($imglink)-1);
		return $imglink;
	}
	
	/*
	* Create img link for avatar
	*/
	function memberAvatar($params)
	{
		global $siteURL;		
		$imglink = $siteURL."/avatar.php?";		
		foreach($params as $key => $value)
			$imglink.= "{$key}=$value&";
		$imglink = substr($imglink,0,strlen($imglink)-1);
		return $imglink;
	}
	
	/*
	* Split variables from a link into attributes to use them in the linkto function
	* No longer used - using parse_str instead
	function splitPageLink($attrString)
	{
		$attrString = explode('&',$attrString);
		foreach($attrString as $value)
		{
			$attrParts = explode('=',$value);
			
			$attr[$attrParts[0]] = $attrParts[1];
		}
		return $attr;
	}
	*/
	
	/*
	* Create mod rewrite links if it is turned on
	* Use in Template: {linkto page="print.details.php?id={$print.print_id}"}
	* Use in PHP: $parms['page'] = 'yourPageLink.php?id=222' | linkto($parms)
	* Attributes from the link can be used like $attr['id']
	*/
	function linkto($parms)
	{
		global $config, $siteURL, $modRewrite;		
		if($modRewrite)
		{
			$page = explode('?',$parms['page']);
			//if($page[1]) $attr = splitPageLink($page[1]); // Old
			if($page[1]) parse_str($page[1],$attr);
			
			switch($page[0])
			{
				case "media.details.php": // Create media details SEO links
					if(!$attr['mediaType']) $attr['mediaType'] = 'photo'; // Make sure there is a mediaType just in case
					if($attr['mediaType'] == 'other') $attr['mediaType'] = 'file'; // Just to make things look nicer if mediaType is set to other set it to file
					if(!$attr['seoName']) $attr['seoName'] = "{$attr[mediaType]}-details"; // Make sure there is a seoName just in case					
					$replacethese = array("#");
					$attr['seoName'] = str_replace($replacethese,"",html_entity_decode($attr['seoName']));
					return "{$siteURL}/{$attr[mediaType]}/{$attr[mediaID]}/{$attr[seoName]}.html";
				break;
				case 'gallery.php':
					if($config['EncryptIDs'])
						$arrayKeyID = k_decrypt($attr['id']); // Decrypt the ID if it was passed encrypted
					else
						$arrayKeyID = $attr['id'];

					$gallerySEOName = ($config['gallerySEOName']) ? $config['gallerySEOName'] : 'gallery';
					$galleriesSEOName = ($config['galleriesSEOName']) ? $config['galleriesSEOName'] : 'galleries';
					
					$attr['seoName'] = ($attr['seoName']) ? $attr['seoName'] : $_SESSION['galleriesData'][$arrayKeyID]['seoName']; // Fix for this not being passed every time
					
					if(!$attr['seoName']) $attr['seoName'] = 'unnamed'; // Set to unnamed just in case
					
					switch($attr['mode'])
					{
						default:
							return "{$siteURL}/{$attr[mode]}/page{$attr[page]}/";
						break;
						case 'gallery':
							if($attr['id'])
							{
								if($attr['gpage'])
									return "{$siteURL}/{$gallerySEOName}/{$attr[seoName]}/{$attr[id]}/gpage{$attr[gpage]}/";
								else
									return "{$siteURL}/{$gallerySEOName}/{$attr[seoName]}/{$attr[id]}/page{$attr[page]}/";
							}
							else
							{
								if($attr['gpage'])
									return "{$siteURL}/{$galleriesSEOName}/gpage{$attr[gpage]}/";
								else
									return "{$siteURL}/{$galleriesSEOName}/";
							}
						break;
						case 'collection':
							return "{$siteURL}/{$attr[mode]}/{$attr[seoName]}/{$attr[id]}/page{$attr[page]}/";
						break;
						case 'lightbox':
							return "{$siteURL}/{$attr[mode]}/{$attr[id]}/page{$attr[page]}/";
						break;
						case 'contributor-media':
							return "{$siteURL}/{$attr[mode]}/{$attr[id]}/page{$attr[page]}/";
						break;						
					}
					
					/*
					if($attr['mode'] == 'gallery')
					{	
						if($attr['id'])
						{
							if($attr['gpage'])
								return "{$siteURL}/{$gallerySEOName}/{$attr[seoName]}/{$attr[id]}/gpage{$attr[gpage]}/";
							else
								return "{$siteURL}/{$gallerySEOName}/{$attr[seoName]}/{$attr[id]}/page{$attr[page]}/";
						}
						else
						{
							if($attr['gpage'])
								return "{$siteURL}/{$galleriesSEOName}/gpage{$attr[gpage]}/";
							else
								return "{$siteURL}/{$galleriesSEOName}/";
						}
					}
					else if($attr['mode'] == 'collection')
						return "{$siteURL}/{$attr[mode]}/{$attr[seoName]}/{$attr[id]}/page{$attr[page]}/";
					else if($attr['mode'] == 'lightbox')
						return "{$siteURL}/{$attr[mode]}/{$attr[id]}/page{$attr[page]}/";
					else if($attr['mode'] == 'contributor-media')
						
					else
						return "{$siteURL}/{$attr[mode]}/page{$attr[page]}/";
					*/
					
					//gallery.php?mode=gallery&id=OTAyMUFBQzdENjczQzg2Njg=&page=1
					//gallery.php?mode=gallery&id=0&gpage=1
					//gallery.php?mode=popular-media&page=1
				break;
				case 'featured.php':					
					return "{$siteURL}/featured-{$attr[mode]}/";
				break;
				case 'print.php':
					if(!$attr['seoName']) $attr['seoName'] = 'printname';
					if(!$attr['mediaID']) $attr['mediaID'] = '0';
					return "{$siteURL}/prints/{$attr[id]}/{$attr[mediaID]}/{$attr[seoName]}.html";
				break;
				case 'product.php':
					if(!$attr['seoName']) $attr['seoName'] = 'productname';
					if(!$attr['mediaID']) $attr['mediaID'] = '0';
					return "{$siteURL}/products/{$attr[id]}/{$attr[mediaID]}/{$attr[seoName]}.html";
				break;
				case 'package.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'packagename';
					
					if(!$attr['mediaID'])
						$attr['mediaID'] = '0';
					
					return "{$siteURL}/packages/{$attr[id]}/{$attr[mediaID]}/{$attr[seoName]}.html";
				break;
				case 'promo.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'promoname';
					return "{$siteURL}/promotions/{$attr[id]}/{$attr[seoName]}.html";
				break;
				case 'credits.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'creditsname';
					return "{$siteURL}/credits/{$attr[id]}/{$attr[seoName]}.html";
				break;
				case 'subscription.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'subname';
					return "{$siteURL}/subscriptions/{$attr[id]}/{$attr[seoName]}.html";
				break;
				case 'collection.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'collname';
					return "{$siteURL}/collections/{$attr[id]}/{$attr[seoName]}.html";
				break;
				case 'digital.php':
					if(!$attr['id']) $attr['id'] = '0'; // Make sure an ID exists - doesn't on original
					if(!$attr['seoName']) $attr['seoName'] = 'dspname';
					if(!$attr['mediaID']) $attr['mediaID'] = '0';
					return "{$siteURL}/digitals/{$attr[id]}/{$attr[mediaID]}/{$attr[seoName]}.html";
				break;
				case 'news.php':
					if(!$attr['seoTitle']) $attr['seoTitle'] = 'article';
					if($attr['id'])
						return "{$siteURL}/news/{$attr[id]}/{$attr[seoTitle]}.html";
					else
						return "{$siteURL}/news/";
				break;
				case 'rss.php':
					if($attr['id'])
						return "{$siteURL}/rss/{$attr[mode]}/{$attr[id]}/";
					else
						return "{$siteURL}/rss/{$attr[mode]}/";
				break;
				case 'contributors.php':
					if(!$attr['seoName'])
						$attr['seoName'] = 'member';
					
					if($attr['id'])
						return "{$siteURL}/contributor/{$attr[seoName]}/{$attr[id]}/";
					else
						return "{$siteURL}/contributors/";
				break;
				default:			
					return "{$siteURL}/{$parms[page]}";
				break;
			}
		}
		else
			return "{$siteURL}/{$parms[page]}";

	}
	
	/*
	* Clean titles for SEO links
	*/
	function cleanForSEO($input)
	{
		//$umlaute = array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/","/ě/","/š/","/č/","/ř/","/ž/","/ý/","/á/","/í/","/é/","/ú/","/ů/","/ó/","/ň/","/ť/","/ď/","/Ě/","/Š/","/Č/","/Ř/","/Ž/","/Ý/","/Á/","/Í/","/É/","/Ú/","/Ů/","/Ó/","/Ň/","/Ť/","/Ď/");
		//$replace = array("ae","oe","ue","Ae","Oe","Ue","ss","e","s","c","r","z","y","a","i","e","u","u","o","n","t","d","e","s","c","r","z","y","a","i","e","u","u","o","n","t","d");
		//$cleanInput = preg_replace($umlaute, $replace, $input);
		//$input = strtolower($cleanInput);
		$clean = str_replace("/","",$input);
		$clean = str_replace(" ","-",$clean);
		$clean = str_replace("'","",$clean);
		$clean = str_replace("%","",$clean);
		$clean = str_replace('"',"",$clean);
		//$clean = html_entity_decode($clean);
		//$clean = preg_replace("/[^A-Za-z0-9_-]/", "", $clean);
		return $clean;
	}
	
	/*
	* Run an exchange rate on the currency entered
	*/
	function doExchangeRate($price,$rate)
	{
		global $exchangeRate;
		
		if($rate)
			$localExchangeRate = $rate; // If a rate is passed use that instead
		else
			$localExchangeRate = $exchangeRate;
		
		@$price/=$localExchangeRate;
		return $price;
	}
	
	/*
	* Check for add-on
	*/
	function addon($addonName)
	{
		global $installed_addons;
		
		if(@in_array($addonName,$installed_addons))
			return true;
		else
			return false;
	}
	
	/*
	* Add the currency settings for a certain currency to the $config array
	
	function setupSelectedCurrency($currency_id)
	{
		global $dbinfo, $config, $db;
		$currency_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}currencies WHERE currency_id = '{$currency_id}'");
		$currency = mysqli_fetch_assoc($currency_result);
		$config['settings']['cur_currency_id']			= $currency['currency_id'];
		$config['settings']['cur_code']					= $currency['code'];
		$config['settings']['cur_name'] 				= $currency['name'];
		$config['settings']['cur_denotation'] 			= $currency['denotation'];
		$config['settings']['cur_decimal_places'] 		= $currency['decimal_places'];
		$config['settings']['cur_decimal_separator'] 	= $currency['decimal_separator'];
		$config['settings']['cur_thousands_separator'] 	= $currency['thousands_separator'];
		$config['settings']['cur_neg_num_format'] 		= $currency['neg_num_format'];
		$config['settings']['cur_pos_num_format'] 		= $currency['pos_num_format'];
		$config['settings']['cur_exchange_rate'] 		= $currency['exchange_rate'];
	}
	
	/*
	* Get the active currencies and set $activeCurrencies and $priCurrency
	
	function getActiveCurrencies()
	{
		global $dbinfo, $db;
		global $activeCurrencies;
		global $priCurrency;
		$activeCurrencyResult = mysqli_query($db,"SELECT currency_id,name,code FROM {$dbinfo[pre]}currencies WHERE active = 1 AND deleted = 0"); // Get all active currencies
		while($activeCurrency = mysqli_fetch_array($activeCurrencyResult))
		{
			if($activeCurrency['defaultcur']) $priCurrency = $activeCurrency; // set the primary currency
			$activeCurrencies[$activeCurrency['currency_id']] = "{$activeCurrency[name]} ({$activeCurrency['code']})";
		}
	}
	*/
	
	/*
	* Setup currency
	*/
	class currencySetup
	{
		public $dbinfo;
		public $priCurrency;
		public $activeCurrencies;
		public $adminCurrency;
		
		public function __construct()
		{
			global $db;
			$this->dbinfo = getDBInfo();
			$activeCurrencyResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}currencies WHERE active = 1 AND deleted = 0"); // Get all active currencies
			//echo mysqli_num_rows($activeCurrencyResult); // Testing
			while($activeCurrency = mysqli_fetch_array($activeCurrencyResult))
			{
				if($activeCurrency['defaultcur'])
				{
					$this->priCurrency = $activeCurrency; // set the primary currency
				}				
				$this->activeCurrencies[$activeCurrency['currency_id']] = $activeCurrency;
			}
		}
		
		/*
		* Return the details about the primary currency
		*/
		public function getPrimaryCurrency()
		{
			return $this->priCurrency;
		}
		
		/*
		* Return an array with details for the displayed currencies dropdown only
		*/
		public function getDisplayCurrencies($disCode=0)
		{
			global $selectedLanguage;
			foreach($this->activeCurrencies as $key => $value)
			{
				$displayCurrencies[$key] = ($value['name_'.$selectedLanguage]) ? $value['name_'.$selectedLanguage] : $value['name'];
				if($disCode) $displayCurrencies[$key].= " ({$value[code]})";
			}
			return $displayCurrencies;
		}
		
		/*
		* Return an array with details for all active currencies
		*/
		public function getActiveCurrencies()
		{
			return $this->activeCurrencies;
		}
		
		/*
		* Return the details of the admin currency - Just grabs priCurrency
		*/
		public function getAdminCurrency()
		{
			return $this->priCurrency;
		}
		
		/*
		* Update the config settings to the selected currency
		*/
		public function setSelectedCurrency($currencyID)
		{
			global $config;
			$config['settings']['cur_currency_id']			= $this->activeCurrencies[$currencyID]['currency_id'];
			$config['settings']['cur_code']					= $this->activeCurrencies[$currencyID]['code'];
			$config['settings']['cur_name'] 				= $this->activeCurrencies[$currencyID]['name'];
			$config['settings']['cur_denotation'] 			= $this->activeCurrencies[$currencyID]['denotation'];
			$config['settings']['cur_decimal_places'] 		= $this->activeCurrencies[$currencyID]['decimal_places'];
			$config['settings']['cur_decimal_separator'] 	= $this->activeCurrencies[$currencyID]['decimal_separator'];
			$config['settings']['cur_thousands_separator'] 	= $this->activeCurrencies[$currencyID]['thousands_separator'];
			$config['settings']['cur_neg_num_format'] 		= $this->activeCurrencies[$currencyID]['neg_num_format'];
			$config['settings']['cur_pos_num_format'] 		= $this->activeCurrencies[$currencyID]['pos_num_format'];
			$config['settings']['cur_exchange_rate'] 		= $this->activeCurrencies[$currencyID]['exchange_rate'];
		}
	}
	
	/*
	* Smarty function for displaying currency
	*/
	function displayCurrency($parms)
	{
		global $config;
		global $cleanCurrency;
		$value = ($parms['exchange']) ? doExchangeRate($parms['value']) : $parms['value'];
		echo $cleanCurrency->currency_display($value,1);
	}
	
	/*
	* Add prefix to group array
	*/
	function addPermPrefixGrp($val)
	{
		return 'grp'.$val;
	}
	
	/*
	* Get the items product photo ID from the database
	*/
	function getProductPhotoFromDB($mgrArea,$itemID)
	{
		global $dbinfo, $db;
		$itemPhotoResult = mysqli_query($db,
			"
			SELECT ip_id
			FROM {$dbinfo[pre]}item_photos
			WHERE mgrarea = '{$mgrArea}'
			AND item_id = '{$itemID}'
			ORDER BY ip_id
			"
		); // Check for a product photo
		if(mysqli_num_rows($itemPhotoResult))
		{
			while($itemPhoto = mysqli_fetch_array($itemPhotoResult))
			{
				$itemPhotos[] = array('id' => $itemPhoto['ip_id']);
			}
			return $itemPhotos;	
		}
		else
			return '';
	}
	
	/*
	* Get the corrected price of a item
	*/
	function getCorrectedPrice($value,$parms)
	{
		global $config, $cleanCurrency;
		
		if(!$cleanCurrency or !$config) throw new Exception('getCorrectedPrice : No config or cleanCurrency values are available to this function.');

		if($parms['noDefault'] == true)
			$price = ($value > 0) ? $value : 0;  // Added this incase a default price should not be assigned
		else
			$price = ($value > 0) ? $value : $config['settings']['default_price']; // Added a check for greater than 0			
		
		//echo $price .'<br>';
		$cleanPrice['preConvNoTax'] = $price;
		
		if($parms['taxInc']) // Include tax in price
		{
			switch($parms['prodType'])
			{
				default:
					$tax = (round($price*($_SESSION['tax']['tax_a_default']/100),2)) + (round($price*($_SESSION['tax']['tax_b_default']/100),2)) + (round($price*($_SESSION['tax']['tax_c_default']/100),2));
				break;
				case 'coll':
				case 'ms':
				case 'sub':
				case 'credit':
				case 'digital':
					$tax = (round($price*($_SESSION['tax']['tax_a_digital']/100),2)) + (round($price*($_SESSION['tax']['tax_b_digital']/100),2)) + (round($price*($_SESSION['tax']['tax_c_digital']/100),2));
				break;
			}
			
			$price+=$tax;
			$cleanPrice['taxInc'] = true;
		}
		
		//if($price)
		//{
			$cleanPrice['display'] = $cleanCurrency->currency_display(doExchangeRate($price,$parms['rate'],$config['settings']['cur_decimal_places']),1);
			$cleanPrice['raw'] = round(doExchangeRate($price,$parms['rate']),$config['settings']['cur_decimal_places']);
			/* OLD
				$cleanPrice['display'] = $cleanCurrency->currency_display(doExchangeRate($price,$parms['rate']),1);
				$cleanPrice['raw'] = round(doExchangeRate($price,$parms['rate']),2);
			*/
		///}
		//else
		//{
		//	$cleanPrice['display'] = '--';
		//	$cleanPrice['raw'] = 0;
		//}
		return $cleanPrice;
	}
	
	/*
	* Get the corrected credits of a item
	*/
	function getCorrectedCredits($credits)
	{
		global $config;
		$credits = ($credits) ? $credits : $config['settings']['default_credits'];
		return $credits;
	}
	
	/*
	* Check if the currency based cart is active
	*/
	function currencyCartStatus()
	{
		global $config;
		if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
			return true;
		else
			return false;
	}
	
	/*
	* Check if the credits based cart is active $itemType needs to be print, prod, pack, coll, sub
	*/
	function creditsCartStatus($itemType)
	{
		global $config;
		if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_'.$itemType] == 1)
			return true;
		else
			return false;
	}
	
	/*
	* Get media details for thumbnails and rollover images
	*/
	function getMediaDetails($field,$media)
	{
		global $lang;
		global $config;
		global $mediaDate;
		global $dbinfo, $db;
		switch($field)
		{	
			case 'title':
				$useTitle = ($media['title_'.$_SESSION['selectedLanguageSession']]) ? $media['title_'.$_SESSION['selectedLanguageSession']] : $media['title']; 				
				return array('lang' => $lang['mediaLabelTitle'],'value' => $useTitle);
			break;
			case 'description':
				$useDescription = ($media['description_'.$_SESSION['selectedLanguageSession']]) ? $media['description_'.$_SESSION['selectedLanguageSession']] : $media['description']; 
				return array('lang' => $lang['mediaLabelDesc'],'value' => $useDescription);
			break;
			case 'copyright':
				return array('lang' => $lang['mediaLabelCopyright'],'value' => $media['copyright']);
			break;
			case 'usage_restrictions':
				return array('lang' => $lang['mediaLabelRestrictions'],'value' => $media['usage_restrictions']);
			break;
			case 'model_release':
				if($media['model_release_status'])	$mrLang = $lang['yes'];
				return array('lang' => $lang['mediaLabelRelease'],'value' => $mrLang);
			break;
			case 'prop_release':
				if($media['prop_release_status'])	$mrLang = $lang['yes'];
				return array('lang' => $lang['mediaLabelPropRelease'],'value' => $mrLang);
			break;
			case 'keywords':
				if($_SESSION['selectedLanguageSession'] == $config['settings']['lang_file_mgr']) // If language is default (matches mgr lang) don't require a keyword language - empty in the db
					$keyLang = '';
				else
					$keyLang = strtoupper($_SESSION['selectedLanguageSession']); // members language
				
				@$keywordsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE media_id = '{$media[media_id]}' AND language = '{$keyLang}' AND memtag = 0 order by keyword");
				@$keywordsRows = mysqli_num_rows($keywordsResult);
				
				if(!$keywordsRows) // If no keywords in the selected language then show all keywords
					$keywordsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE media_id = '{$media[media_id]}' AND memtag = 0");
				
				while(@$keyword = mysqli_fetch_array($keywordsResult))
					$keywordsArray[] = $keyword['keyword'];
				
				return array('lang' => $lang['mediaLabelKeys'],'value' => $keywordsArray);
			break;
			case 'filename':
				return array('lang' => $lang['mediaLabelFilename'],'value' => $media['filename']);
			break;
			case 'id':
				return array('lang' => $lang['mediaLabelID'],'value' => $media['media_id']);
			break;
			case 'date':
				$customDate = $mediaDate->showdate($media['date_added']);			
				return array('lang' => $lang['mediaLabelDate'],'value' => $customDate);
			break;
			case 'created':		
				if($media['date_created'] != '0000-00-00 00:00:00')
					$customDate = $mediaDate->showdate($media['date_created']);	
				else
					$customDate = $lang['na'];	
				return array('lang' => $lang['mediaLabelDateC'],'value' => $customDate);
			break;
			case 'downloads':
				return array('lang' => $lang['mediaLabelDownloads'],'value' => 'xxxxxxxxx');
			break;
			case 'views':
				return array('lang' => $lang['mediaLabelViews'],'value' => $media['views']);
			break;
			case 'resolution':
				return array('lang' => $lang['mediaLabelResolution'],'value' => $media['width'].'x'.$media['height']);
			break;
			case 'filesize':
				return array('lang' => $lang['mediaLabelFilesize'],'value' => convertFilesizeToMB($media['filesize']).$lang['megabytesAbv']);
			break;
			case 'purchases':
				return array('lang' => $lang['mediaLabelPurchases'],'value' => 'xxxxxxxxx');
			break;
			case "mediatypes":
				$mediaTypesResult = mysqli_query($db,
					"
					SELECT * FROM {$dbinfo[pre]}media_types_ref 
					LEFT JOIN {$dbinfo[pre]}media_types 
					ON {$dbinfo[pre]}media_types_ref.mt_id = {$dbinfo[pre]}media_types.mt_id 
					WHERE {$dbinfo[pre]}media_types_ref.media_id = '{$media[media_id]}' 
					AND {$dbinfo[pre]}media_types.active = 1 
					"
				);
				$mediaTypeRows = mysqli_num_rows($mediaTypesResult);
				while($mediaType = mysqli_fetch_assoc($mediaTypesResult))
				{
					$mTypeName[] = ($mediaType['name_'.$_SESSION['selectedLanguageSession']]) ? $mediaType['name_'.$_SESSION['selectedLanguageSession']] : $mediaType['name'];
				}
				if($mediaTypeRows)
				{
					$mTypes = implode(", ",$mTypeName);
					return array('lang' => $lang['mediaLabelMediaTypes'],'value' => $mTypes);
				}
			break;
			case 'colorPalette':
				$colorPaletteResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}color_palettes WHERE media_id = '{$media[media_id]}' ORDER BY percentage DESC");
				$colorPaletteRows = mysqli_num_rows($colorPaletteResult);
				if($colorPaletteRows)
				{
					while($colorPalette = mysqli_fetch_assoc($colorPaletteResult))
					{
						if($colorPalette['percentage'] >= $config['colorSearchMinimum']) $colors[] = $colorPalette;
					}
					return array('lang' => $lang['mediaLabelColors'],'value' => $colors);
					
					/*
					echo "<div style='margin-top: 6px;' id='colorPalette'>";
					while($colorPalette = mysqli_fetch_array($colorPaletteResult))
					{
						$colorPercentage = round($colorPalette['percentage']*100);
						if($colorPercentage < 1) $colorPercentage = '< 1';
						echo "<div style='float: left; width: 15px; height: 6px; margin-right: 2px; background-color: #{$colorPalette[hex]};' title='#{$colorPalette[hex]} ({$colorPercentage}%)'></div>";
					}
					echo "</div>";
					*/
				}
				else
					return false;
			break;
			case 'owner':
				if($media['owner'] == 0)
				{
					$owner['displayName'] = $config['settings']['business_name'];
				}
				else
				{	
					$ownerResult = mysqli_query($db,"SELECT f_name,l_name,display_name,avatar_status,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '{$media[owner]}'");
					$ownerRows = mysqli_num_rows($ownerResult);
					$owner = mysqli_fetch_array($ownerResult);
					if($ownerRows)
					{	
						if($owner['avatar_status'] == 1) // Avatar Status
							$owner['avatar'] = true;
						else
							$owner['avatar'] = false;
							
						if(!$owner['display_name']) // Set display name if none exists
							$owner['displayName'] = $owner['f_name'].' '.$owner['l_name']; 
						else
							$owner['displayName'] = $owner['display_name'];
						
						if($config['EncryptIDs']) // Get usable ID
							$owner['useID'] = k_encrypt($owner['mem_id']); 
						else
							$owner['useID'] = $owner['mem_id'];
							
						$owner['seoName'] = cleanForSEO($owner['display_name']);
					}
					else
					{
						$owner['displayName'] = $lang['mediaLabelUnknown'];
					}
				}
				return array('lang' => $lang['mediaLabelOwner'],'value' => $owner);
			break;
			case 'price':
				if(currencyCartStatus())
				{
					// Grab license information if none exists already
					if(!$media['lic_purchase_type'])
					{
						$licenseResult = mysqli_query($db,"SELECT lic_purchase_type FROM {$dbinfo[pre]}licenses WHERE license_id = '{$media[license]}'");
						$license = mysqli_fetch_assoc($licenseResult);						
						$media['lic_purchase_type'] = $license['lic_purchase_type']; // Might be better to push the license array elements to the end of the media array so we get all the info?
					}
					
					switch($media['lic_purchase_type'])
					{
						case "nfs":
							$price = $lang['mediaLicenseNFS'];
						break;
						case "rf":
							if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_digital']) // See if tax should be included in prices
								$priceParms['taxInc'] = true;
							
							$priceArray = getCorrectedPrice($media['price'],$priceParms);
							$price = $priceArray['display'];
						break;
						case "rm":
							$price =  $lang['mediaLicenseRM'];
						break;
						case "fr":
							$price =  $lang['mediaLicenseFR'];
						break;
						case "cu":
							$price =  $lang['mediaLicenseCU'];
						break;
					}
				}
				else
					$price = false;
					
				return array('lang' => $lang['mediaLabelPrice'],'value' => $price);
			break;
			case 'credits':
				if(creditsCartStatus('digital'))
				{
					switch($media['license'])
					{
						case "nfs":
							$credits = $lang['mediaLicenseNFS'];
						break;
						case "ex":
						case "eu":
						case "rf":
							$credits = (creditsCartStatus('digital')) ? getCorrectedCredits($media['credits']) : false;
						break;
						case "rm":
							$credits =  $lang['mediaLicenseRM'];
						break;
						case "fr":
							$credits =  $lang['mediaLicenseFR'];
						break;
						case "cu":
							$credits =  $lang['mediaLicenseCU'];
						break;
					}
				}
				else
					$credits = false;
				return array('lang' => $lang['mediaLabelCredits'],'value' => $credits);
			break;
		}
	}
	
	/*
	* Get array for rating stars
	*/
	function getRatingArray($mediaID)
	{
		global $dbinfo, $db;
		global $config;
		$ratingResult = mysqli_query($db,
		"
			SELECT COUNT(mr_id) as ratingTotal,AVG(rating) as ratingAverage 
			FROM {$dbinfo[pre]}media_ratings 
			WHERE media_id = '{$mediaID}' 
			AND status = 1
		"
		);
		$rating = mysqli_fetch_assoc($ratingResult);
		//$ratingPercentage = $rating['ratingSum']/$rating['ratingRecords'];
		$adjustment = 10/$config['RatingStars'];
		$ratingAverage = $rating['ratingAverage']/$adjustment;
		$ratingForStars = ($config['RatingStarsRoundUp']) ? ceil($ratingAverage) : round($ratingAverage);
		for($x=1;$x<=$config['RatingStars'];$x++)
		{
			$ratingArray['stars'][] = ($x <= $ratingForStars) ? 1 : 0; // Get an array of stars
		}
		$ratingArray['average'] = round($ratingAverage,1); // Get the percentage
		$ratingArray['votes'] = $rating['ratingTotal']; // Get the amount of votes cast
		return $ratingArray;
	}
	
	/*
	* Create thumbnail link
	*/
	//mediaImage(array('type'=>'rollover','mediaID'=>'xxxx','folderID'=>'xxxx','size'=>'xxxx','seo'=>'xxxx'));
	function mediaImage($params)
	{
		global $siteURL, $modRewrite;	
		
		if($modRewrite)
		{
			if(!$params['seo']) $params['seo'] = 'photo';
			
			$replacethese = array("#");
			$params['seo'] = str_replace($replacethese,"",html_entity_decode($params['seo']));
				
			foreach($params as $key => $param) // Attach any additional parameters other than the norm
			{
				if($key != 'type' and $key != 'mediaID' and $key != 'seo' and $key != 'folderID')
					$additionalParams.= "&{$key}={$param}";
			}
			
			$imglink = "{$siteURL}/{$params[type]}s/{$params[mediaID]}/{$params[folderID]}/{$params[seo]}.jpg";
			
			if($additionalParams) // Add any additional params
				$imglink.= $additionalParams;
		}
		else
		{
			$imglink = $siteURL."/image.php?";		
			foreach($params as $key => $value)
				$imglink.= "{$key}=$value&";
			$imglink = substr($imglink,0,strlen($imglink)-1);
		}
		
		switch($params['mode']) // Allow different modes on the return
		{
			default:
				return $imglink;
			break;
			case 'imgtag':
				return "<img src='{$imglink}' />";
			break;
		}
	}
	
	/*
	* Create media list arrays
	* $featuredMedia = new mediaList($sql);
	* $featuredMedia->getMediaDetails();
	* $featuredMediaArray = $featuredMedia->getMediaArray();
	* $thumbMediaDetailsArray = $featuredMedia->getThumbMediaDetailsArray();
	*/
	class mediaList
	{
		private $dbinfo;
		private $result;
		private $variable;
		private $returnRows;
		private $mediaArray;
		private $media;
		private $thumbMediaDetailsArray;
		
		public $addThumbDetails = false;
		
		private $galleryID;
		private $galleryMode;
		
		public function __construct($sql)
		{			
			global $db;
			$this->dbinfo = getDBInfo();
			if(!$this->result = mysqli_query($db,$sql))
				throw new Exception("mediaList __construct : Query was not successful : ".mysqli_error($db));
				//throw new Exception(mysqli_error());				
				//echo 'sql: '.$sql; exit; // Testing				
				//throw new Exception("mediaList __construct : Query was not successful");
		}
		
		public function getRows()
		{
			global $db;
			$rows = mysqli_fetch_row(mysqli_query($db,"SELECT FOUND_ROWS()"));
			$this->returnRows = $rows[0];
			//$this->returnRows = mysqli_num_rows($this->result);
			return $this->returnRows;
		}
		
		/*
		* Set gallery details to be used in the linkto
		*/
		public function setGalleryDetails($galleryID,$galleryMode)
		{
			$this->galleryID = $galleryID;
			$this->galleryMode = $galleryMode;
		}
		
		/*
		* Get details fields for specific type
		*/
		public function getDetailsFields($detailsType='thumb')
		{
			global $thumbDetailFields;
			global $rolloverDetailFields;
			global $previewDetailFields;
			global $rssDetailFields;
			//global $thumbMediaDetailsArray;
			
			if(!$this->mediaArray)
				throw new Exception('getDetailsFields: No media object exists. You must call getMediaDetails first');
							
			switch($detailsType)
			{
				case 'thumb':
					foreach($this->mediaArray as $mediaID => $media)
					{
						foreach($thumbDetailFields as $field)
						{
							if($field)
								$thumbMediaDetailsArray[$mediaID][$field] = getMediaDetails($field,$media);
						}
					}
					return $thumbMediaDetailsArray;
				break;
				case 'rollover':
					foreach($this->mediaArray as $mediaID => $media)
					{
						foreach($rolloverDetailFields as $field)
						{
							if($field)
								$rolloverMediaDetailsArray[$mediaID][$field] = getMediaDetails($field,$media);
						}
					}
					return $rolloverMediaDetailsArray;
				break;
				case 'preview':
					foreach($this->mediaArray as $mediaID => $media)
					{
						foreach($previewDetailFields as $field)
						{
							if($field)
								$previewMediaDetailsArray[$mediaID][$field] = getMediaDetails($field,$media);
						}
					}
					return $previewMediaDetailsArray;
				break;
				case 'rss':
					foreach($this->mediaArray as $mediaID => $media)
					{
						foreach($rssDetailFields as $field)
						{
							if($field)
								$rssMediaDetailsArray[$mediaID][$field] = getMediaDetails($field,$media);
						}
					}
					return $rssMediaDetailsArray;
				break;
			}
		}
		
		/*
		* Setup media details from the query
		*/
		public function getMediaDetails()
		{
			global $config, $modRewrite, $dbinfo, $db, $selectedLanguage, $lightboxSystem;
			while($this->media = mysqli_fetch_assoc($this->result))
			{	
				$this->media['showLightbox'] = ($lightboxSystem and $config['settings']['thumbDetailsLightbox']) ? 1 : 0; // Set if the lightbox button should show // $config['settings']['lightbox'] replaced by $lightboxSystem
				/*
				* Only grab rating information if rating system is turned on and thumbnail ratings are turned on
				*/
				if($config['settings']['rating_system'] and $config['settings']['thumbDetailsRating'])
				{
					$this->media['showRating'] = 1; // Set if the rating system is turned on
					@$this->media['allowRating'] = ((($_SESSION['loggedIn'] and $_SESSION['member']['membershipDetails']['rating']) or $config['settings']['rating_system_lr']) and !in_array($this->media['media_id'],$_SESSION['ratedMedia']) and $this->media['owner'] != $_SESSION['member']['mem_id']) ? 1 : 0; // Check to see if they already rated this
					$this->media['rating'] = getRatingArray($this->media['media_id']); // Grab the rating array
				}
				
				if($config['EncryptIDs'])
				{
					$parms['page'] = "media.details.php?mediaID=".k_encrypt($this->media['media_id']); // Link to page
					$galleryID = k_decrypt($this->galleryID); // Decrypt gallery ID to use it as array key
				}
				else
				{
					$parms['page'] = "media.details.php?mediaID=".$this->media['media_id']; // Link to page
					$galleryID = $this->galleryID;
				}
				
				$this->media['seoGalleryName'] = $_SESSION['galleriesData'][$galleryID]['seoName'];			

				$useTitle = ($this->media['title_'.$selectedLanguage]) ? $this->media['title_'.$selectedLanguage] : $this->media['title'];				
				$this->media['seoName'] = cleanForSEO($useTitle); // $this->media['title']	
				
				if($modRewrite) $parms['page'].="&seoName=".$this->media['seoName'].'&seoGalleryName='.$this->media['seoGalleryName'].'&mediaType='.$this->media['dsp_type']; // Link to page with seoName and mediaType added
				
				/*
				if($this->galleryID)
					$parms['page'].="&galleryID=".$this->galleryID;
					
				if($this->galleryMode)
					$parms['page'].="&galleryMode=".$this->galleryMode;	
				*/
				
				/*
				* Query for thumb details
				*/
				if($this->addThumbDetails)
				{
					$tmediaResult = mysqli_query($db,"SELECT thumb_width,thumb_height FROM {$dbinfo[pre]}media_thumbnails WHERE media_id = '".$this->media['media_id']."' AND thumbtype='thumb'");
					$tmedia = mysqli_fetch_assoc($tmediaResult);
					$this->media['thumb']['originalWidth'] = $tmedia['thumb_width'];
					$this->media['thumb']['originalHeight'] = $tmedia['thumb_height'];
					
					$crop = ($config['settings']['thumbcrop']) ? $config['settings']['thumbcrop_height'] : 0;
					$scaledSizes = getScaledSizeNoSource($tmedia['thumb_width'],$tmedia['thumb_height'],$config['settings']['thumb_size'],$crop=0);
					
					$this->media['thumb']['resizedWidth'] = $scaledSizes[0];
					$this->media['thumb']['resizedHeight'] = $scaledSizes[1];
				}
				
				$this->media['approvalStatusLang'] = 'approvalStatus'.$this->media['approval_status'];
				
				$this->media['percentage'] = round($this->media['percentage']*100);	
				
				$this->media['linkto'] = linkto($parms); // Create the link using SEO if needed				
				
				$this->media['encryptedFID'] = k_encrypt($this->media['folder_id']); // Encrypted Folder ID
				$this->media['encryptedID'] = k_encrypt($this->media['media_id']); // Encrypted Media ID
				
				if($config['EncryptIDs'])
					$this->media['useMediaID'] = k_encrypt($this->media['media_id']);
				else
					$this->media['useMediaID'] = $this->media['media_id'];
				
				if($_SESSION['lightboxItems']) // Check to see if the session exists
				{
					if(in_array($this->media['media_id'],$_SESSION['lightboxItems'])) // Check whether this is currently in the members lightbox or not
					{
						$lightboxItemKey = array_search($this->media['media_id'],$_SESSION['lightboxItems']);
						$this->media['inLightbox'] = 1;
						$this->media['lightboxItemID'] = $lightboxItemKey;
					}
					else
					{
						$this->media['inLightbox'] = 0;
						$this->media['lightboxItemID'] = 0;
					}
				}
				else
				{
					$this->media['inLightbox'] = 0;
					$this->media['lightboxItemID'] = 0;
				}
				
				// Use a direct cached link
				if($config['useCachedImgLinks'])
				{
					$base_path = dirname(dirname(__FILE__));
					global $siteURL;
					foreach(array('thumb') as $value)
					{
						$cacheFile = "id{$this->media[encryptedID]}-".md5("thumb-{$this->media[encryptedID]}-{$this->media[encryptedFID]}--").'.jpg'; // Name of cached file
						//echo $cacheFile; exit;
						$cachePathFile = "{$base_path}/cache/{$cacheFile}";
						if(file_exists($cachePathFile)) // Make sure the file exists
						{	
							$this->media[$value.'CachedLink'] = "{$siteURL}/assets/cache/".$cacheFile;
						}
					}
				}
				
				if($this->returnRows == 1) $this->singleMedia = $this->media; // If rows are only 1 then put $media in a separate variable so that it can be called independently
				
				$this->mediaArray[$this->media['media_id']] = $this->media;
				
			}
		}
		
		/*
		* Get a single media array instead of the multidemensional array returned with the alternative method
		*/
		public function getMediaSingle()
		{
			if($this->singleMedia and $this->returnRows)
				return $this->singleMedia;
			else
				throw new Exception('getMediaSingle: No rows or media exist to return');
		}	
		
		public function getSingleMediaDetails($type='rollover')
		{
			global $config;
			global $dbinfo, $db;
			global $rolloverDetailFields;
			global $previewDetailFields; 
			global $selectedLanguage;
			global $lightboxSystem;
			$media = mysqli_fetch_assoc($this->result);
			
			if($media['license'] != 'nfs')
			{
				$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses WHERE license_id = '".$media['license']."'"); // Get the correct license
				$licenseRows = mysqli_num_rows($licenseResult);
				$license = mysqli_fetch_assoc($licenseResult);
				
				@$this->media = array_merge($media,$license); // Merge the media and license arrays
				
				if($licenseRows < 1) // If no license rows can be found make the license type nfs
					$this->media['license'] = 'nfs';
			}
			else
				$this->media = $media;

				
			if($this->media['license'] != 'nfs') // Find the correct license code
				$this->media['license'] = $license['lic_purchase_type'];
			
			//echo 'test'.$this->media['license']; // Testing			
			//$this->media['title'] = 'testing123'; // Testing
			
			if($type == 'rollover') // Get details for rollover
			{
				foreach($rolloverDetailFields as $field)
					if($field) $this->media['details'][$field] = getMediaDetails($field,$this->media);
			}
			
			if($type == 'preview') // Get details for details page
			{
				foreach($previewDetailFields as $field)
					if($field) $this->media['details'][$field] = getMediaDetails($field,$this->media);
					
				$this->media['showLightbox'] = ($lightboxSystem) ? 1 : 0; // Set if the lightbox button should show // $config['settings']['lightbox'] replaced by $lightboxSystem
			}
			
			/*
			* Only grab rating information if rating system is turned on and rollover/preview ratings are turned on
			*/
			if($config['settings']['rating_system'] and $config['settings'][$type.'DetailsRating'])
			{
				$this->media['showRating'] = 1; // Set if the rating system is turned on
				//$this->media['allowRating'] = (($_SESSION['loggedIn'] or $config['settings']['rating_system_lr']) and $this->media['owner'] != $_SESSION['member']['mem_id']) ? 1 : 0;
				@$this->media['allowRating'] = ((($_SESSION['loggedIn'] and $_SESSION['member']['membershipDetails']['rating']) or $config['settings']['rating_system_lr']) and !in_array($this->media['media_id'],$_SESSION['ratedMedia']) and $this->media['owner'] != $_SESSION['member']['mem_id']) ? 1 : 0; // Check to see if they already rated this
				$this->media['rating'] = getRatingArray($this->media['media_id']); // Grab the rating array
			}
			
			if($_SESSION['lightboxItems'])
			{
				if(in_array($this->media['media_id'],$_SESSION['lightboxItems'])) // Check whether this is currently in the members lightbox or not
				{
					$lightboxItemKey = array_search($this->media['media_id'],$_SESSION['lightboxItems']);
					$this->media['inLightbox'] = 1;
					$this->media['lightboxItemID'] = $lightboxItemKey;
				}
				else
				{
					$this->media['inLightbox'] = 0;
					$this->media['lightboxItemID'] = 0;
				}
			}
			else
			{
				$this->media['inLightbox'] = 0;
				$this->media['lightboxItemID'] = 0;
			}
			
			$this->media['useMediaID'] = ($config['EncryptIDs']) ? k_encrypt($this->media['media_id']) : $this->media['media_id'];
			
			$useTitle = ($this->media['title_'.$selectedLanguage]) ? $this->media['title_'.$selectedLanguage] : $this->media['title'];				
		
			$this->media['seoName'] = cleanForSEO($useTitle); // Get SEO name // $this->media['title']
				
			$this->media['encryptedFID'] = k_encrypt($this->media['folder_id']); // Encrypted Folder ID
			$this->media['encryptedID'] = k_encrypt($this->media['media_id']); // Encrypted Media ID
			
			$this->media['approvalStatusLang'] = 'approvalStatus'.$this->media['approval_status'];
			
			// Use a direct cached link
			if($config['useCachedImgLinks'])
			{
				$base_path = dirname(dirname(__FILE__));
				global $siteURL;
				foreach(array('sample','rollover') as $value)
				{
					$cacheFile = "id{$this->media[encryptedID]}-".md5("{$value}-{$this->media[encryptedID]}-{$this->media[encryptedFID]}--").'.jpg'; // Name of cached file
					$cachePathFile = "{$base_path}/cache/{$cacheFile}";
					if(file_exists($cachePathFile)) // Make sure the file exists
					{	
						$this->media[$value.'CachedLink'] = "{$siteURL}/assets/cache/".$cacheFile;
					}
				}
				
				if($config['settings']['zoomonoff']) // Check for cached zoom file
				{
					$zoomCachedFile = "id{$this->media[encryptedID]}-".md5("sample-{$this->media[encryptedID]}-{$this->media[encryptedFID]}-1024-").'.jpg'; // Name of cached file
					$zoomCachedPath = "{$base_path}/cache/{$zoomCachedFile}";
					if(file_exists($zoomCachedPath)) // Make sure the file exists
						$this->media['zoomCachedLink'] = "{$siteURL}/assets/cache/".$zoomCachedFile;
				}
			}
			return $this->media;
		}
		
		/*
		* Get an array of gallery IDs that this media displays in
		*/
		public function getMediaGalleryIDs($mediaID=NULL)
		{
			global $config, $db;
			
			if(!$mediaID)
				$mediaID = $this->media['media_id'];
			
			if(!$mediaID)
				throw new Exception('getMediaGalleries: No media ID');
			
			$galleriesResult = mysqli_query($db,"SELECT gallery_id FROM {$this->dbinfo[pre]}media_galleries WHERE gmedia_id = '{$mediaID}'"); // In the future could add to only check galleries the member has access to
			$galleriesRows = mysqli_num_rows($galleriesResult);
			while($gallery = mysqli_fetch_array($galleriesResult))
				$galleries[] = $gallery['gallery_id'];
			
			return $galleries;
		}
		
		public function getMediaArray()
		{
			return $this->mediaArray;
		}
		
		// Not used any longer
		public function getThumbMediaDetailsArray()
		{
			global $thumbMediaDetailsArray;
			return $thumbMediaDetailsArray;
		}
	}
	
	/*
	* Get the new size of a media file without using the source
	*/
	function getScaledSizeNoSource($mediaWidth,$mediaHeight,$scalesize,$crop=0)
	{	
		global $config;	
		
		if(!$mediaWidth) // Just in case width wasn't passed
			$mediaWidth = $scalesize;
		
		if(!$mediaHeight) // Just in case height wasn't passed
			$mediaHeight = round($scalesize*.75);
		
		if($mediaWidth >= $mediaHeight)
		{
			if($mediaWidth > $scalesize)
			{
				$width = $scalesize;
			}
			else
			{
				$width = $mediaWidth;
			}
			$ratio = $width/$mediaWidth;
			$height = $mediaHeight * $ratio;				
		}
		else
		{
			if($mediaHeight > $scalesize)
			{
				$height = $scalesize;	
			}
			else
			{
				$height = $mediaHeight;	
			}
			$ratio = $height/$mediaHeight;
			$width = $mediaWidth * $ratio;
		}
		
		if($crop and $height > $crop)
			$height = $crop; // Change if getting cropped

		return array(floor($width),floor($height)); // Changed to floor because calc seemed one pixel off
	}
	
	/*
	* Get the gallery icon details - all fields from the item_photos table plus width and height calculations
	*/
	function galleryIcon($id)
	{
		global $dbinfo, $config, $db;
		
		if($_SESSION['galleriesData'][$id]['icon'] != 0)
		{
			
			$galleryIcon['type'] = 'existingPhoto';			
			$thumbSize = $config['settings']['gallery_thumb_size']; // Get the thumbnail size setting to find the correct product shot size to use
			
			try
			{
				$mediaObj = new mediaTools($_SESSION['galleriesData'][$id]['icon']);
				$media = $mediaObj->getMediaInfoFromDB(); 
				//$folder = $mediaObj->getFolderInfoFromDB($media['folder_id']);
			}
			catch(Exception $e)
			{
				//echo $e->getMessage();
			}
			
			/*
			$sizeType = 'thumb';
			
			if($thumbSize > 149 and file_exists(BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_med.jpg")) // If size setting is larger then 200 use the medium size instead
				$sizeType = 'med';
			if($size > 500 and file_exists(BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_org.jpg")) // If size setting is larger then 500 use the original size instead
				$sizeType = 'org';
			
			$src = BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_{$sizeType}.jpg";
			$iconSize = getimagesize($src);
			
			$crop = ($config['settings']['gallerythumbcrop']) ? $config['settings']['gallerythumbcrop_height'] : 0;
			
			$sized = getScaledSizeNoSource($iconSize[0],$iconSize[1],$config['settings']['gallery_thumb_size'],$crop); // Figure out the width and height this item will be		
			$galleryIcon['width'] = $sized[0];
			$galleryIcon['height'] = $sized[1];
			*/
			
			if($media) // The media version was found use this
			{
				$mid = k_encrypt($_SESSION['galleriesData'][$id]['icon']);
				$fid = k_encrypt($media['folder_id']);
				
				$mediaIcon = $mediaObj->getIconInfoFromDB();
				
				if($mediaIcon) // Get media icon info
				{
					//print_r($mediaIcon);					
					$iconSize[0] = $mediaIcon['thumb_width'];
					$iconSize[1] = $mediaIcon['thumb_height'];
					
					$crop = ($config['settings']['gallerythumbcrop']) ? $config['settings']['gallerythumbcrop_height'] : 0;				
					$sized = getScaledSizeNoSource($iconSize[0],$iconSize[1],$config['settings']['gallery_thumb_size'],$crop); // Figure out the width and height this item will be		
					$galleryIcon['width'] = $sized[0];
					$galleryIcon['height'] = $sized[1];
				}
				
				$galleryIcon['imgSrc'] = "image.php?mediaID={$mid}&type=thumb&folderID={$fid}&size={$thumbSize}&crop={$config[settings][gallerythumbcrop_height]}";
				return $galleryIcon;
			}			
		}
		
		$galleryIconResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '{$id}' AND mgrarea = 'gallery'");
		$galleryIconRows = mysqli_num_rows($galleryIconResult);
		if($galleryIconRows)
		{
			$galleryIcon = mysqli_fetch_array($galleryIconResult);
			
			$galleryIcon['type'] = 'custom'; // Custom uploaded use product photo
			
			$photoID = zerofill($galleryIcon['ip_id'],4);
			$itemID = zerofill($id,4);
			
			$thumbSize = $config['settings']['gallery_thumb_size']; // Get the thumbnail size setting to find the correct product shot size to use
			$sizeType = 'small';
			
			if($thumbSize > 149 and file_exists(BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_med.jpg")) // If size setting is larger then 200 use the medium size instead
				$sizeType = 'med';
			if($size > 500 and file_exists(BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_org.jpg")) // If size setting is larger then 500 use the original size instead
				$sizeType = 'org';
			
			$src = BASE_PATH."/assets/item_photos/gallery{$itemID}_ip{$photoID}_{$sizeType}.jpg";
			@$iconSize = getimagesize($src);
			
			$crop = ($config['settings']['gallerythumbcrop']) ? $config['settings']['gallerythumbcrop_height'] : 0;
			
			$sized = getScaledSizeNoSource($iconSize[0],$iconSize[1],$config['settings']['gallery_thumb_size'],$crop); // Figure out the width and height this item will be		
			$galleryIcon['width'] = $sized[0];
			$galleryIcon['height'] = $sized[1];
			
			$galleryIcon['imgSrc'] = "productshot.php?itemID={$id}&itemType=gallery&photoID={$photoID}&size={$thumbSize}"; // Src for the gallery icon
			
			return $galleryIcon;
		}
		else
			return false;
	}
	
	/*
	* Create an array of gallery crumbs
	*/
	function galleryCrumbs($id,$parms=NULL)
	{
		global $config;
		
		if($id != 0)
		{
			//$crumbs[] = $id;
			$crumbs[$id] = ($config['EncryptIDs']) ? k_encrypt($id) : $id; // Only do this if you are in a subgallery
		}
		
		$parentGallery = $_SESSION['galleriesData'][$id]['parent_gal'];
		while($parentGallery != 0)
		{
			if($_SESSION['galleriesData'][$parentGallery]) // Check if the upline item exists
			{
				//$crumbs[] = $parentGallery;
				$crumbs[$parentGallery] = ($config['EncryptIDs']) ? k_encrypt($parentGallery) : $parentGallery;
				$parentGallery = $_SESSION['galleriesData'][$parentGallery]['parent_gal'];
			}
			else
				break; // User doesn't have access to parent - break process
		}
		
		$crumbs[0] = 0; // Add galleries link				
		$crumbs = array_reverse($crumbs,true);
		
		return $crumbs;
	}
	
	/*
	* Create an array of gallery crumbs with full info
	*/
	function galleryCrumbsFull($id,$parms=NULL)
	{
		global $config;
		
		if($id != 0)
		{
			//$crumbs[] = $id;
			$crumbs[] = $_SESSION['galleriesData'][$id]; // Only do this if you are in a subgallery
		}
		
		$parentGallery = $_SESSION['galleriesData'][$id]['parent_gal'];
		while($parentGallery != 0)
		{
			if($_SESSION['galleriesData'][$parentGallery]) // Check if the upline item exists
			{
				//$crumbs[] = $parentGallery;
				$crumbs[] = $_SESSION['galleriesData'][$parentGallery];
				$parentGallery = $_SESSION['galleriesData'][$parentGallery]['parent_gal'];
			}
			else
				break; // User doesn't have access to parent - break process
		}
		
		$crumbs[] = $_SESSION['galleriesData'][0]; // Add galleries link				
		$crumbs = array_reverse($crumbs,true);
		
		return $crumbs;
	}
	
	/*
	* Get prints list
	*/
	function printsList($prints,$mediaID='')
	{
		global $selectedLanguage, $config, $modRewrite;
		$prints['photos'] = getProductPhotoFromDB('print',$prints['print_id']); // Get product photo ids
		$prints['photo'] = $prints['photos'][0]; // Get first product photo id
		$prints['name'] = ($prints['item_name_'.$selectedLanguage]) ? $prints['item_name_'.$selectedLanguage] : $prints['item_name']; // Choose the correct language
		$prints['description'] = ($prints['description_'.$selectedLanguage]) ? $prints['description_'.$selectedLanguage] : $prints['description']; // Choose the correct language
		$prints['seoName'] = cleanForSEO($prints['name']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
		{
			$parms['page'] = "print.php?id=".k_encrypt($prints['print_id']); // Link to page
			if($mediaID) $parms['page'].="&mediaID=".k_encrypt($mediaID); // Add media ID to link if one was passed
		}
		else
		{
			$parms['page'] = "print.php?id=".$prints['print_id']; // Link to page
			if($mediaID) $parms['page'].="&mediaID=".$mediaID; // Add media ID to link if one was passed
		}
		
		$prints['cartEditLink'] = $parms['page'];
		
		$prints['usePrintID'] = ($config['EncryptIDs']) ? k_encrypt($prints['print_id']) : $prints['print_id'];
		
		if($modRewrite) $parms['page'].="&seoName={$prints[seoName]}"; // Link to page with seoName added
		$prints['linkto'] = linkto($parms); // Create the link using SEO if needed
		
		$prints['encryptedID'] = k_encrypt($prints['print_id']);
		
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_prints'] and $prints['taxable']) // See if tax should be included in prices
		{
			$priceParms['taxInc'] = true;
			$prints['taxInc'] = true;
		}
		
		$priceParms['prodType'] = 'print';
		
			//$tax = ($prints['price']*($_SESSION['tax']['tax_a_default']/100)) + ($prints['price']*($_SESSION['tax']['tax_b_default']/100)) + ($prints['price']*($_SESSION['tax']['tax_c_default']/100));
			//$prints['nativePrice'] = $prints['price'] + $tax;

		$prints['nativePrice'] = $prints['price']; // Get the native price the admin has set
		
		$prints['price'] = (currencyCartStatus()) ? getCorrectedPrice($prints['price'],$priceParms) : false; // Check if the currency cart is on first
		$prints['credits'] = (creditsCartStatus('print')) ? getCorrectedCredits($prints['credits']) : false; // Check if the currency cart is on first
		return $prints;
	}
	
	/*
	* Get products list
	*/
	function productsList($products,$mediaID='')
	{
		global $selectedLanguage, $config, $modRewrite;
		$products['photos'] = getProductPhotoFromDB('prod',$products['prod_id']); // Get product photo id
		$products['photo'] = $products['photos'][0]; // Get first product photo id
		$products['name'] = ($products['item_name_'.$selectedLanguage]) ? $products['item_name_'.$selectedLanguage] : $products['item_name']; // Choose the correct language
		$products['description'] = ($products['description_'.$selectedLanguage]) ? $products['description_'.$selectedLanguage] : $products['description']; // Choose the correct language
		$products['seoName'] = cleanForSEO($products['name']); // Name cleaned for SEO usage
		$products['encryptedID'] = k_encrypt($products['prod_id']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
		{
			$parms['page'] = "product.php?id=".k_encrypt($products['prod_id']); // Link to page
			if($mediaID) $parms['page'].="&mediaID=".k_encrypt($mediaID); // Add media ID to link if one was passed
		}
		else
		{
			$parms['page'] = "product.php?id=".$products['prod_id']; // Link to page
			if($mediaID) $parms['page'].="&mediaID=".$mediaID; // Add media ID to link if one was passed
		}
		
		$products['cartEditLink'] = $parms['page'];
		
		$products['useProductID'] = ($config['EncryptIDs']) ? k_encrypt($products['prod_id']) : $products['prod_id'];
		
		//$parms['page'] = "product.php?id={$products[prod_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$products[seoName]}"; // Link to page with seoName added
		$products['linkto'] = linkto($parms); // Create the link using SEO if needed				
			
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_prints'] and $products['taxable']) // See if tax should be included in prices
		{
			$priceParms['taxInc'] = true;
			$products['taxInc'] = true;
		}
		
		$priceParms['prodType'] = 'prod';
		
			//$tax = ($products['price']*($_SESSION['tax']['tax_a_default']/100)) + ($products['price']*($_SESSION['tax']['tax_b_default']/100)) + ($products['price']*($_SESSION['tax']['tax_c_default']/100));
			//$products['nativePrice'] = $products['price'];

		$products['nativePrice'] = $products['price']; // Get the native price the admin has set

		$products['price'] = (currencyCartStatus()) ? getCorrectedPrice($products['price'],$priceParms) : false; // Check if the currency cart is on first
		$products['credits'] = (creditsCartStatus('prod')) ? getCorrectedCredits($products['credits']) : false; // Check if the currency cart is on first
	
		return $products;
	}
	
	/*
	* Get packages list
	*/
	function packagesList($packages,$mediaID='')
	{
		global $selectedLanguage, $config, $modRewrite;
		$packages['photos'] = getProductPhotoFromDB('pack',$packages['pack_id']); // Get product photo id
		$packages['photo'] = $packages['photos'][0]; // Get first product photo id
		$packages['name'] = ($packages['item_name_'.$selectedLanguage]) ? $packages['item_name_'.$selectedLanguage] : $packages['item_name']; // Choose the correct language
		$packages['description'] = ($packages['description_'.$selectedLanguage]) ? $packages['description_'.$selectedLanguage] : $packages['description']; // Choose the correct language
		$packages['seoName'] = cleanForSEO($packages['name']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
		{
			$parms['page'] = "package.php?id=".k_encrypt($packages['pack_id']); // Link to page
			if($mediaID) $parms['page'].="&mediaID=".k_encrypt($mediaID); // Add media ID to link if one was passed
			
		}
		else
		{
			$parms['page'] = "package.php?id=".$packages['pack_id']; // Link to page
			if($mediaID) $parms['page'].="&mediaID=".$mediaID; // Add media ID to link if one was passed
		}
		
		$packages['cartEditLink'] = $parms['page'];
		
		$packages['usePackageID'] = ($config['EncryptIDs']) ? k_encrypt($packages['pack_id']) : $packages['pack_id'];
		
		//$parms['page'] = "package.php?id={$packages[pack_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$packages[seoName]}"; // Link to page with seoName added
		$packages['linkto'] = linkto($parms); // Create the link using SEO if needed
		
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_prints'] and $packages['taxable']) // See if tax should be included in prices
		{
			$priceParms['taxInc'] = true;
			$packages['taxInc'] = true;
		}
		
		$priceParms['prodType'] = 'pack';
		
		$packages['nativePrice'] = $packages['price']; // Get the native price the admin has set
		
		//$price = ($packages['price']) ? $packages['price'] : $config['settings']['default_price']; // Find starting price
		//$packages['price'] = $cleanCurrency->currency_display(doExchangeRate($price),1); // Format the correct price
		$packages['price'] = (currencyCartStatus()) ? getCorrectedPrice($packages['price'],$priceParms) : false; // Check if the cart is on first
		$packages['credits'] = (creditsCartStatus('pack')) ? getCorrectedCredits($packages['credits']) : false; // Check if the currency cart is on first
		return $packages;
	}
	
	/*
	* Get collections list
	*/
	function collectionsList($collections)
	{
		global $selectedLanguage, $config, $modRewrite;
		$collections['photos'] = getProductPhotoFromDB('coll',$collections['coll_id']); // Get product photo id
		$collections['photo'] = $collections['photos'][0]; // Get first product photo id
		$collections['name'] = ($collections['item_name_'.$selectedLanguage]) ? $collections['item_name_'.$selectedLanguage] : $collections['item_name']; // Choose the correct language
		$collections['description'] = ($collections['description_'.$selectedLanguage]) ? $collections['description_'.$selectedLanguage] : $collections['description']; // Choose the correct language
		$collections['seoName'] = cleanForSEO($collections['name']); // Name cleaned for SEO usage

		if($config['EncryptIDs'])
		{
			$parms['page'] = "collection.php?id=".k_encrypt($collections['coll_id']); // Link to page
			if($mediaID) $parms['page'].="&mediaID=".k_encrypt($mediaID); // Add media ID to link if one was passed
			$collections['addToCartLink'] = "cart.php?mode=add&type=collection&id=".k_encrypt($collections['coll_id']); // Direct to cart // {$siteURL}/
			$collections['viewCollectionLink'] = "gallery.php?mode=collection&id=".k_encrypt($collections['coll_id'])."&page=1"; // View collection link
		}
		else
		{
			$parms['page'] = "collection.php?id=".$collections['coll_id']; // Link to page
			if($mediaID) $parms['page'].="&mediaID=".$mediaID; // Add media ID to link if one was passed
			$collections['addToCartLink'] = "cart.php?mode=add&type=collection&id=".$collections['coll_id']; // Direct to cart // {$siteURL}
			$collections['viewCollectionLink'] = "gallery.php?mode=collection&id=".$collections['coll_id']."&page=1"; // View collection link
		}
		
		$addToCartLink['page'] = $collections['addToCartLink']; // Convert to corrected link
		$collections['addToCartLink'] = linkto($addToCartLink);
		
		$passViewCollParams['page'] = $collections['viewCollectionLink'];
		if($modRewrite) $passViewCollParams['page'].="&seoName={$collections[seoName]}"; // Link to page with seoName added
		$collections['viewCollectionLink'] = linkto($passViewCollParams); // Get the SEO or direct link for the view gallery
		
		$collections['cartEditLink'] = $parms['page'];
		
		$collections['useCollectionID'] = ($config['EncryptIDs']) ? k_encrypt($collections['coll_id']) : $collections['coll_id'];
		
		//$parms['page'] = "collection.php?id={$collections[coll_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$collections[seoName]}"; // Link to page with seoName added
		$collections['linkto'] = linkto($parms); // Create the link using SEO if needed

		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_digital'] and $collections['taxable']) // See if tax should be included in prices
			$priceParms['taxInc'] = true;
			
		$priceParms['prodType'] = 'coll';

		$collections['price'] = (currencyCartStatus()) ? getCorrectedPrice($collections['price'],$priceParms) : false; // Check if the cart is on first
		$collections['credits'] = (creditsCartStatus('coll')) ? getCorrectedCredits($collections['credits']) : false; // Check if the currency cart is on first
		return $collections;
	}
	
	/*
	* Get subscriptions list
	*/
	function subscriptionsList($subscriptions)
	{
		global $selectedLanguage, $config, $modRewrite;
		$subscriptions['photos'] = getProductPhotoFromDB('sub',$subscriptions['sub_id']); // Get product photo id
		$subscriptions['photo'] = $subscriptions['photos'][0]; // Get first product photo id
		$subscriptions['name'] = ($subscriptions['item_name_'.$selectedLanguage]) ? $subscriptions['item_name_'.$selectedLanguage] : $subscriptions['item_name']; // Choose the correct language
		$subscriptions['description'] = ($subscriptions['description_'.$selectedLanguage]) ? $subscriptions['description_'.$selectedLanguage] : $subscriptions['description']; // Choose the correct language
		$subscriptions['seoName'] = cleanForSEO($subscriptions['name']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
			$parms['page'] = "subscription.php?id=".k_encrypt($subscriptions['sub_id']); // Link to page
		else
			$parms['page'] = "subscription.php?id=".$subscriptions['sub_id']; // Link to page
		
		$subscriptions['cartEditLink'] = $parms['page'];
		
		$subscriptions['useSubscriptionID'] = ($config['EncryptIDs']) ? k_encrypt($subscriptions['sub_id']) : $subscriptions['sub_id'];
		
		//$parms['page'] = "subscription.php?id={$subscriptions[sub_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$subscriptions[seoName]}"; // Link to page with seoName added
		$subscriptions['linkto'] = linkto($parms); // Create the link using SEO if needed
		
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_subs'] and $subscriptions['taxable']) // See if tax should be included in prices
			$priceParms['taxInc'] = true;
		
		$priceParms['prodType'] = 'sub';
		
		$subscriptions['price'] = (currencyCartStatus()) ? getCorrectedPrice($subscriptions['price'],$priceParms) : false; // Check if the cart is on first
		$subscriptions['credits'] = (creditsCartStatus('sub')) ? getCorrectedCredits($subscriptions['credits']) : false; // Check if the currency cart is on first
		return $subscriptions;
	}
	
	/*
	* Get credits list
	*/
	function creditsList($credits)
	{
		global $selectedLanguage, $config, $modRewrite;
		$credits['photos'] = getProductPhotoFromDB('credit',$credits['credit_id']); // Get product photo id
		$credits['photo'] = $credits['photos'][0]; // Get first product photo id
		$credits['name'] = ($credits['name_'.$selectedLanguage]) ? $credits['name_'.$selectedLanguage] : $credits['name']; // Choose the correct language
		$credits['description'] = ($credits['description_'.$selectedLanguage]) ? $credits['description_'.$selectedLanguage] : $credits['description']; // Choose the correct language
		$credits['seoName'] = cleanForSEO($credits['name']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
			$parms['page'] = "credits.php?id=".k_encrypt($credits['credit_id']); // Link to page
		else
			$parms['page'] = "credits.php?id=".$credits['credit_id']; // Link to page
		
		$credits['cartEditLink'] = $parms['page'];
		
		$credits['useCreditsID'] = ($config['EncryptIDs']) ? k_encrypt($credits['credit_id']) : $credits['credit_id'];
		
		//$parms['page'] = "credits.php?id={$credits[credit_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$credits[seoName]}"; // Link to page with seoName added
		$credits['linkto'] = linkto($parms); // Create the link using SEO if needed

		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_credits'] and $credits['taxable']) // See if tax should be included in prices
			$priceParms['taxInc'] = true;
			
		$priceParms['prodType'] = 'credit';

		$credits['price'] = getCorrectedPrice($credits['price'],$priceParms); // Get corrected price
		return $credits;
	}
	
	/*
	* Get promotions list
	*/
	function promotionsList($promotions)
	{
		global $selectedLanguage, $config, $modRewrite;
		$promotions['photos'] = getProductPhotoFromDB('promo',$promotions['promo_id']); // Get product photo id
		$promotions['photo'] = $promotions['photos'][0]; // Get first product photo id
		$promotions['name'] = ($promotions['name_'.$selectedLanguage]) ? $promotions['name_'.$selectedLanguage] : $promotions['name']; // Choose the correct language
		$promotions['description'] = ($promotions['description_'.$selectedLanguage]) ? $promotions['description_'.$selectedLanguage] : $promotions['description']; // Choose the correct language
		$promotions['seoName'] = cleanForSEO($promotions['name']); // Name cleaned for SEO usage
		
		if($config['EncryptIDs'])
			$parms['page'] = "promo.php?id=".k_encrypt($promotions['promo_id']); // Link to page
		else
			$parms['page'] = "promo.php?id=".$promotions['promo_id']; // Link to page
		
		//$parms['page'] = "promo.php?id={$promotions[promo_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$promotions[seoName]}"; // Link to page with seoName added
		$promotions['linkto'] = linkto($parms); // Create the link using SEO if needed	
		return $promotions;
	}
	
	/*
	* Get memberships list
	*/
	function membershipsList($memberships)
	{
		global $selectedLanguage, $config, $modRewrite;
		//$memberships['photos'] = getProductPhotoFromDB('membership',$memberships['ms_id']); // Get product photo id
		$memberships['photo'] = $memberships['photos'][0]; // Get first product photo id
		$memberships['name'] = ($memberships['name_'.$selectedLanguage]) ? $memberships['name_'.$selectedLanguage] : $memberships['name']; // Choose the correct language
		$memberships['description'] = ($memberships['description_'.$selectedLanguage]) ? $memberships['description_'.$selectedLanguage] : $memberships['description']; // Choose the correct language
		$memberships['seoName'] = cleanForSEO($memberships['name']); // Name cleaned for SEO usage
	
		if($_SESSION['member']['trialed_memberships'])
		{
			if(@in_array($memberships['ums_id'],$_SESSION['member']['trialed_memberships']))
				$memberships['trialUsed'] = true; // Check if the trial has already been used
		}
		
		if($_SESSION['member']['fee_memberships'])
		{
			if(@in_array($memberships['ums_id'],$_SESSION['member']['fee_memberships']))
				$memberships['feePaid'] = true; // Check if a setup fee has already been paid
		}
	
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_ms'] and $memberships['taxable']) // See if tax should be included in prices
			$priceParms['taxInc'] = true;
			
		$priceParms['prodType'] = 'ms';
	
		switch($memberships['mstype'])
		{
			case "free": // Free
				$memberships['setupfee'] = false;
				$memberships['price'] = false;
			break;
			case "onetime":
				if(!$memberships['setupfee'] or $memberships['setupfee'] == '0.00')
				{
					$memberships['setupfee'] = false;
					$memberships['mstype'] = 'free';
				}
				else
					$memberships['setupfee'] = getCorrectedPrice($memberships['setupfee'],$priceParms);
					
				$memberships['price'] = false;
			break;
			case "recurring": // Recurring
				if(!$memberships['setupfee'] or $memberships['setupfee'] == '0.00')
					$memberships['setupfee'] = false;
				else
					$memberships['setupfee'] = getCorrectedPrice($memberships['setupfee'],$priceParms);
				
				if(!$memberships['price'] or $memberships['price'] == '0.00')
					$memberships['price'] = false;
				else
					$memberships['price'] = getCorrectedPrice($memberships['price'],$priceParms);
					
				if((!$memberships['setupfee'] or $memberships['setupfee'] == '0.00') and (!$memberships['price'] or $memberships['price'] == '0.00'))
					$memberships['mstype'] = 'free';
			break;
		}
		
		if($config['EncryptIDs'])
			$parms['page'] = "memberships.php?id=".k_encrypt($memberships['ms_id']); // Link to page
		else
			$parms['page'] = "memberships.php?id=".$memberships['ms_id']; // Link to page
		
		//$parms['page'] = "memberships.php?id={$memberships[ums_id]}"; // Link to page
		if($modRewrite) $parms['page'].="&seoName={$memberships[seoName]}"; // Link to page with seoName added
		$memberships['linkto'] = linkto($parms); // Create the link using SEO if needed	
		return $memberships;
	}
	
	/*
	* Get digitals list
	*/
	function digitalsList($digital,$mediaID,$original=false)
	{
		global $selectedLanguage, $config, $modRewrite, $lang, $dbinfo, $db;

		if(!$original)
			$digital['name'] = ($digital['name_'.$selectedLanguage]) ? $digital['name_'.$selectedLanguage] : $digital['name']; // Choose the correct language
				
		//$digital['description'] = ($products['description_'.$selectedLanguage]) ? $products['description_'.$selectedLanguage] : $products['description']; // Choose the correct language
		//$digital['seoName'] = cleanForSEO($products['name']); // Name cleaned for SEO usage
		$digital['encryptedID'] = k_encrypt($digital['ds_id']); // Name cleaned for SEO usage
		
		// License info
		if(!$digital['lic_purchase_type']) // License hasn't been grabbed yet
		{	
			if($digital['license'] != 'nfs' and is_numeric($digital['license']))
			{
				$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses WHERE license_id = '".$digital['license']."'"); // Get the correct license
				$licenseRows = mysqli_num_rows($licenseResult);
				$license = mysqli_fetch_assoc($licenseResult);
				
				if($licenseRows < 1) // If no license rows can be found make the license type nfs
					$digital['license'] = 'nfs';
				else
				{
					$digital['license'] = $license['lic_purchase_type'];					
					$digital = array_merge($digital,$license); // Merge the media and license arrays
					//$digital = $digitalMerge;
				}
			}
		}
			
		if($config['EncryptIDs'])
		{
			$parms['page'] = "digital.php?id=".k_encrypt($digital['ds_id']); // Link to page
			if($mediaID) $parms['page'].="&mediaID=".k_encrypt($mediaID); // Add media ID to link if one was passed			
			//$parms['page'].= "&customizeID=".k_encrypt($digital['customizeID']);
		}
		else
		{
			$parms['page'] = "digital.php?id=".$digital['ds_id']; // Link to page
			if($mediaID) $parms['page'].="&mediaID=".$mediaID; // Add media ID to link if one was passed
			//$parms['page'].= "&customizeID=".$digital['customizeID'];
		}
		
		$digital['cartEditLink'] = $parms['page'];
		
		$digital['useDigitalID'] = ($config['EncryptIDs']) ? k_encrypt($digital['ds_id']) : $digital['ds_id'];
		
		//$parms['page'] = "product.php?id={$products[prod_id]}"; // Link to page
		//if($modRewrite) $parms['page'].="&seoName={$products[seoName]}"; // Link to page with seoName added
		
		$digital['linkto'] = linkto($parms); // Create the link using SEO if needed	
		
		if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_digital']) // See if tax should be included in prices // and $products['taxable']
		{
			$priceParms['taxInc'] = true;
			$digital['taxInc'] = true;
		}
		
		$digital['fps'] = trim($digital['fps'],"0,."); // Strip trailing zeros and period from FPS
		
		$priceParms['prodType'] = 'digital';

		$digital['nativePrice'] = $digital['price']; // Get the native price the admin has set
		
		$digital['price'] = (currencyCartStatus()) ? getCorrectedPrice($digital['price'],$priceParms) : false; // Check if the currency cart is on first
		$digital['credits'] = (creditsCartStatus('digital')) ? getCorrectedCredits($digital['credits']) : false; // Check if the currency cart is on first
		return $digital;
	}
	
	/*
	* Get contributors list
	*/
	function contrList($contributor)
	{
		global $selectedLanguage, $config, $modRewrite;
		
		if(!$contributor['display_name']) // Set display name if none exists
			$contributor['display_name'] = $contributor['f_name'].' '.$contributor['l_name']; 
		
		if($contributor['avatar_status'] == 1) // Avatar Status
			$contributor['avatar'] = true;
		else
			$contributor['avatar'] = false;
		
		if($config['EncryptIDs']) // Get usable ID
			$contributor['useID'] = k_encrypt($contributor['mem_id']); 
		else
			$contributor['useID'] = $contributor['mem_id']; 
			
		$contributor['seoName'] = cleanForSEO($contributor['display_name']);

		$parms['page'] = 'contributors.php?id='.$contributor['useID'].'&seoName='.$contributor['seoName']; // Portfolio Link
		$contributor['profileLinkto'] = linkto($parms);
		
		$parmsMed['page'] = 'gallery.php?mode=contributor-media&id='.$contributor['useID'].'&page=1'; // All media link
		$contributor['allMediaLinkto'] = linkto($parmsMed);
		
		return $contributor;
	}
	
	/*
	* Get an array of product options
	*/ 
	function getProductOptions($type,$id,$taxable)
	{
		global $dbinfo, $config, $selectedLanguage, $db;
		$optionGroupResults = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}option_grp WHERE parent_type = '{$type}' AND parent_id = '{$id}' AND deleted = 0 AND active = 1 ORDER BY sortorder");
		while($optionsGroup = mysqli_fetch_assoc($optionGroupResults))
		{
			$optionsGroup['name'] = ($optionsGroup['name_'.$selectedLanguage]) ? $optionsGroup['name_'.$selectedLanguage] : $optionsGroup['name']; // Choose the correct language			
			$optionGroups[$optionsGroup['og_id']] = $optionsGroup;
			
			$optionResults = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}options WHERE parent_id = '{$optionsGroup[og_id]}' AND deleted = 0 ORDER BY sortorder");
			while($option = mysqli_fetch_assoc($optionResults))
			{
				
				$option['name'] = ($option['name_'.$selectedLanguage]) ? $option['name_'.$selectedLanguage] : $option['name']; // Choose the correct language
				
				$option['priceMod'] = ($option['price_mod'] == 'add') ? '+' : '-';
				$option['creditsMod'] = ($option['credits_mod'] == 'add') ? '+' : '-';
				
				if($_SESSION['tax']['tax_inc'] and $_SESSION['tax']['tax_prints'] and $taxable) // See if tax should be included in prices
					$priceParms['taxInc'] = true;
				
				$option['nativePrice'] = $option['price']; // Get the native price the admin has set
				
				$option['price'] = (currencyCartStatus() and $option['price'] > 0) ? getCorrectedPrice($option['price'],$priceParms) : false; // Check if the currency cart is on first
				
				$option['credits'] = (creditsCartStatus('prod') and $option['credits'] > 0) ? getCorrectedCredits($option['credits']) : false; // Check if the currency cart is on first
				$optionGroups[$optionsGroup['og_id']]['options'][$option['op_id']] = $option;
			}
		}
		return $optionGroups;
	}
	
	/*
	* Convert a bill status number to the correct text label/language
	*/ 
	function billStatusNumToText($paymentStatus)
	{
		switch($paymentStatus) // Payment status language to use
		{
			case 0: // PROCCESSING
				$textMatch = 'processing';
			break;
			case 1: // PAID/APPROVED
				$textMatch = 'paid';
			break;
			case 2: // INCOMPLETE/NONE/UNPAID
				$textMatch = 'unpaid';
			break;
			case 4: // FAILED
				$textMatch = 'failed';
			break;
			case 5: // REFUNDED
				$textMatch = 'refunded';
			break;
			case 6: // CANCELLED
				$textMatch = 'cancelled';
			break;
		}
		return $textMatch;
	}
	
	/*
	* Convert shipping status to correct language tax
	*/ 
	function shippingStatusNumToText($shippingStatus)
	{
		switch($shippingStatus) // Payment status language to use
		{
			case 0: // PROCCESSING
				$textMatch = 'processing';
			break;
			case 1: // SHIPPED
				$textMatch = 'shipped';
			break;
			case 2: // NOT SHIPPED
				$textMatch = 'notShipped';
			break;
			case 4: // BACKORDERED
				$textMatch = 'backordered';
			break;
		}
		return $textMatch;
	}
	
	/*
	* Convert an order status number to the correct text label/language
	*/ 
	function orderStatusNumToText($orderStatus)
	{
		switch($orderStatus) // Order status language to use
		{
			case 0: // PENDING
				$textMatch = 'pending';
			break;
			case 1: // APPROVED
				$textMatch = 'approved';
			break;
			case 2: // INCOMPLETE
				$textMatch = 'incomplete';
			break;
			case 3: // CANCELLED
				$textMatch = 'cancelled';
			break;
			case 4: // FAILED
				$textMatch = 'failed';
			break;
		}
		return $textMatch;
	}
	
	/*
	* Convert an order payment number to the correct text label/language
	*/ 
	function orderPaymentNumToText($orderPayment)
	{
		switch($orderPayment) // Payment status language to use
		{
			case 0: // PROCCESSING
				$textMatch = 'processing';
			break;
			case 1: // PAID/APPROVED
				$textMatch = 'paid';
			break;
			case 2: // INCOMPLETE/NONE/UNPAID
				$textMatch = 'unpaid';
			break;
			case 3: // BILL LATER
				$textMatch = 'billLater';
			break;
			case 4: // FAILED
				$textMatch = 'failed';
			break;
			case 5: // REFUNDED
				$textMatch = 'refunded';
			break;
		}
		return $textMatch;
	}
	
	// Put lightbox items in a session
	function getSessionLightboxItems($umemID)
	{
		global $config, $dbinfo, $db;
		$_SESSION['lightboxItems'] = array();
		$lightboxItemsResult = mysqli_query($db,
			"
			SELECT *
			FROM {$dbinfo[pre]}lightboxes 
			JOIN {$dbinfo[pre]}lightbox_items
			ON {$dbinfo[pre]}lightboxes.lightbox_id = {$dbinfo[pre]}lightbox_items.lb_id
			WHERE {$dbinfo[pre]}lightboxes.umember_id = '{$umemID}'
			"
		);		
		if(mysqli_num_rows($lightboxItemsResult))
		{
			while($lightboxItem = mysqli_fetch_array($lightboxItemsResult))
				$_SESSION['lightboxItems'][$lightboxItem['item_id']] = $lightboxItem['media_id'];			
		}
	}
	
	/*
	* Find the correct media price from the passed $media array
	*/
	function getMediaPrice($media)
	{
		global $config;
		
		//$mediaPrice = $media['price'];				
		switch($media['license'])
		{
			case 'cu':
			case 'fr':
			case 'nfs':
				$mediaPrice = 0;
			break;
			case 'rm':
			case 'ex':
			case 'eu':
			case 'rf':
				if($media['price'] > 0)
					$mediaPrice = $media['price'];
				else
					$mediaPrice = $config['settings']['default_price'];
			break;
		}
		
		if(!$mediaPrice) // Backup just in case
			$mediaPrice = 0;
		
		return $mediaPrice;
	}
	
	/*
	* Find the correct media credits from the passed $media array
	*/
	function getMediaCredits($media)
	{
		global $config;
		
		//echo 'lic '.$media['license']; // Testing
		
		//$mediaPrice = $media['price'];				
		switch($media['license'])
		{
			case 'cu':
			case 'fr':
			case 'nfs':
				$mediaCredits = 0;
			break;
			case 'rm':
			case 'ex':
			case 'eu':
			case 'rf':
				if($media['credits'] > 0)
					$mediaCredits = $media['credits'];
				else
					$mediaCredits = $config['settings']['default_credits'];
			break;
		}
		
		if(!$mediaCredits) // Backup just in case
			$mediaCredits = 0;
		
		return $mediaCredits;
	}
	
	/*
	* Check a price and if needed convert it to the default price
	*/
	function defaultPrice($price)
	{
		global $config;								
		return ($price > 0) ? $price : $config['settings']['default_price'];							
	}
	
	/*
	* Check credits and if needed convert it to the default credits
	*/
	function defaultCredits($credits)
	{
		global $config;								
		return ($credits > 0) ? $credits : $config['settings']['default_credits'];							
	}
	
	/*
	* Get media details for the cart photo
	*/
	function getMediaDetailsForCart($mediaID)
	{
		global $dbinfo, $config, $db;
		// select the media details
		$sql = "SELECT * FROM {$dbinfo[pre]}media WHERE media_id = '{$mediaID}'";
		$mediaInfo = new mediaList($sql);
		$media = $mediaInfo->getSingleMediaDetails('thumb');		
		$media['useMediaID'] =($config['EncryptIDs']) ? k_encrypt($media['media_id']) : $media['media_id']; // See if media ID should be encrypted or not							
		return $media;
	}
	
	/*
	* Find the galleries a media file exists in
	*/
	function getMediaGalleries($mediaID,$html=false)
	{
		global $dbinfo, $db;
				
		if($mediaID)
		{
			$galleriesResult = mysqli_query($db,
			"
				SELECT gallery_id FROM {$dbinfo[pre]}media_galleries 
				WHERE gmedia_id = '{$mediaID}'
			");
			$galleryRows = mysqli_num_rows($galleriesResult);
			while($gallery = mysqli_fetch_assoc($galleriesResult))
				$galleries[] = get_gallery_path($gallery['gallery_id']);
			
			if($html)
				return implode("<br />",$galleries);// Return as HTML with line breaks
			else
				return $galleries; // Return as array
		}
	}
	
	/*
	* Find the lowest price cart items to be used with bulk discounts
	*/
	function findLowestCartItem($type,$bulkFree)
	{
		global $cartItemsArray;
						
		foreach($cartItemsArray as $cartItemKey => $cartItemValue) // Loop through all array items
		{
			if($cartItemValue['item_type'] == $type)
			{	
				if($cartItemValue['usePayType'] == 'cur') // Check for credits or cur
				{
					for($x=0; $x<$cartItemValue['quantity']; $x++) // Add one for each quantity to the assign array
					{
						$assignPrices[] = $cartItemValue['lineItemPriceEach'];
						$assignPriceIDs[] = $cartItemKey;
					}
				}
				else
				{
					for($x=0; $x<$cartItemValue['quantity']; $x++) // Add one for each quantity to the assign array
					{
						$assignCredits[] = $cartItemValue['lineItemCreditsEach'];
						$assignCreditsIDs[] = $cartItemKey;
					}
				}
				//$lowestPrices = $cartItemValue['lineItemPriceEach'];
			}
		}
		// Order credits by amount
		if($assignCredits and $assignCreditsIDs)
			array_multisort($assignCredits,$assignCreditsIDs);
		
		//print_r($assignPrices);
		//print_r($assignCredits);
		
		// Order price by amount
		if($assignPrices and $assignPriceIDs)
			array_multisort($assignPrices,$assignPriceIDs);
		
		for($x=0; $x<$bulkFree; $x++) // Get free credits
		{
			if($assignCredits[$x]) // Check to make sure it exists in the credits array
			{
				$creditsFree[] = $assignCredits[$x];
				$freeItemsArray[$assignCreditsIDs[$x]]++;
			}
		}
		
		$remainingFree = $bulkFree-(count($creditsFree));
		
		// Do the rest in prices
		for($x=0; $x<$remainingFree; $x++) // Get free prices
		{
			if($assignPrices[$x]) // Check to make sure it exists in the credits array
			{
				$pricesFree[] = $assignPrices[$x];
				$freeItemsArray[$assignPriceIDs[$x]]++;
			}
		}
		
		//echo "testing: ". count($creditsFree) . " | $bulkFree<br /><br />";
		
		foreach($freeItemsArray as $cartItemID => $quantity) // Assign free items to the cartItemsArray
			$cartItemsArray[$cartItemID]['freeItems'] = $quantity;
		
		$returnValues['creditsFreeArray'] = $creditsFree;
		$returnValues['pricesFreeArray'] = $pricesFree;
		@$returnValues['creditsTotal'] = array_sum($creditsFree);
		@$returnValues['pricesTotal'] = array_sum($pricesFree);
		$returnValues['freeItemsArray'] = $freeItemsArray;
		
		return $returnValues;
	}
	
	/*
	* BUILD XML OUTPUT
	*/
	function getXML($table,$query,$limit,$sortBy,$sortOrder,$title,$mode,$case,$size)
	{
		//GET VARIOUS SETTINGS OF BUILD DATE
		global $dbinfo, $config, $langset, $db;
		header("Content-type: application/rss+xml; ".$langset['lang_charset']);
		$replace = array("~","?","'");
		$today = date(DATE_RFC2822);
		
		//BUILD XML HEADERS
		$xmlOutput.= "<?xml version=\"1.0\" encoding=\"".$langset['lang_charset']."\"?>\n";
		$xmlOutput.= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$xmlOutput.= "<channel>\n";
		$xmlOutput.= "<atom:link href=\"".curPageURL()."/\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$xmlOutput.= "<cf:treatAs xmlns:cf=\"http://www.microsoft.com/schemas/rss/core/2005\">list</cf:treatAs>";
		$xmlOutput.= "<title>".htmlspecialchars(str_replace($replace,"",$config['settings']['site_title']))." - ".$title."</title>\n";
		$xmlOutput.= "<lastBuildDate>".$today."</lastBuildDate>\n";
		$xmlOutput.= "<pubDate>".$today."</pubDate>\n";
		$xmlOutput.= "<language>".$langset['xmlLangCode']."</language>\n";
		$xmlOutput.= "<link>".$config['settings']['site_url']."</link>\n";
		$xmlOutput.= "<description>".htmlspecialchars(str_replace($replace,"",$config['settings']['meta_desc']))."</description>\n";
		$sql = "SELECT * FROM {$dbinfo[pre]}".$table." ".$query." ORDER BY ".$sortBy." ".$sortOrder." LIMIT ".$limit;
		$runSQL = mysqli_query($db,$sql);
		
		//BUILD XML BODY
		while($results = mysqli_fetch_object($runSQL))
		{
			$xmlOutput.= "<item>\n";
			if($mode == 1){
				$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$dbinfo[pre]}media WHERE media_id = '$results->media_id'";
				$mediaObject = new mediaList($sql);
     
       			if($returnRows = $mediaObject->getRows())
				{
					$mediaObject->getMediaDetails(); // Run the getMediaDetails function to grab all the media file details
					$mediaArray = $mediaObject->getMediaArray(); // Get the array of media
					$detailsArray = $mediaObject->getDetailsFields('rss'); // Get the RSS detail fields
				}
					$xmlOutput.= "<title>Photo: ".htmlspecialchars(str_replace($replace,"",trim($detailsArray[$results->media_id]['title']['value'])))."</title>\n";
					$xmlOutput.= "<link>".$mediaArray[$results->media_id]['linkto']."</link>\n";
					$xmlOutput.= "<description>&lt;p&gt;&lt;img src='";
					
					//BUILD ACTUAL PHOTO URL
					$params['type'] = "rollover";
					$params['mediaID'] = $mediaArray[$results->media_id]['encryptedID'];
					$params['folderID'] = $mediaArray[$results->media_id]['encryptedFID'];
					$params['size'] = $size;
					$params['seo']	= htmlspecialchars(str_replace($replace,"",$mediaArray[$results->media_id]['seoName']));
					$xmlOutput.= str_replace('&','&amp;',mediaImage($params));
					$xmlOutput.= "' align='left' /&gt;&lt;br&gt;";
					if($detailsArray[$results->media_id]['description']['value']){
						$xmlOutput.= $detailsArray[$results->media_id]['description']['lang'].":&lt;br&gt;".htmlspecialchars(str_replace($replace,"",trim($detailsArray[$results->media_id]['description']['value'])))."";
					}
					if($detailsArray[$results->media_id]['keywords']['value']){
						$xmlOutput.= "&lt;br&gt;".$detailsArray[$results->media_id]['keywords']['lang'].":";
						foreach($detailsArray[$results->media_id]['keywords']['value'] as $key => $data){
							$xmlOutput.= " &lt;a href='".$config['settings']['site_url']."/search.php?clearSearch=true&amp;searchPhrase=".htmlspecialchars(str_replace($replace,"",trim($data)))."' /&gt;".htmlspecialchars(str_replace($replace,"",trim($data)))."&lt;/a&gt;";
						}
					}
					$xmlOutput.= "&lt;br&gt;".$detailsArray[$results->media_id]['views']['lang'].": ".$detailsArray[$results->media_id]['views']['value'];
					$xmlOutput.= "&lt;/p&gt;</description>\n";
					$xmlOutput.= "<guid>".$mediaArray[$results->media_id]['linkto']."</guid>\n";
					$date = date_create($mediaArray[$results->media_id]['date_added']);
					$xmlOutput.= "<pubDate>".date_format($date,'D, d M Y H:i:s O')."</pubDate>\n";
			} else {
				$xmlOutput.= "<title>".htmlspecialchars(str_replace($replace,"",trim($results->title)))."</title>\n";
				$xmlOutput.= "<link>".$config['settings']['site_url']."/news.php?id=".$results->news_id."</link>\n";
				$xmlOutput.= "<description>".htmlspecialchars(str_replace($replace,"",trim($results->article)))."</description>\n";
				$xmlOutput.= "<guid>".$config['settings']['site_url']."/news.php?id=".$results->news_id."</guid>\n";
				$date = date_create($results->add_date);
				$xmlOutput.= "<pubDate>".date_format($date,'D, d M Y H:i:s O')."</pubDate>\n";
			}
			$xmlOutput.= "</item>\n";
		}
		
		//BUILD XML FOOTER (END XML OUTPUT)
		$xmlOutput.= "</channel>\n";
		$xmlOutput.= "</rss>\n";
		return $xmlOutput;
	}
	
	function readGPSinfoEXIF($lat_ref,$lat0,$lat1,$lat2,$lon_ref,$lon0,$lon1,$lon2){
		
		//echo "GPSLatitudeRef: $lat_ref<br>GPSLatitude_0: $lat0<br>GPSLatitude_1: $lat1<br>GPSLatitude_2: $lat2<br>GPSLongitudeRef: $lon_ref<br>GPSLongitude_0: $lon0<br>GPSLongitude_1: $lon1<br>GPSLongitude_2: $lon2"; exit;
		
		//FORM LATITUDE
		list($num, $dec) = explode('/', $lat0);
		if($num == 0 && $dec > 0){
			$lat_s = $dec;
		}
		if($dec == 0 && $num > 0){
			$lat_s = $num;
		}
		if($dec > 0 && $num > 0){
			$lat_s = $num / $dec;
		}
		list($num, $dec) = explode('/', $lat1);
		if($num == 0 && $dec > 0){
			$lat_m = $dec;
		}
		if($dec == 0 && $num > 0){
			$lat_m = $num;
		}
		if($dec > 0 && $num > 0){
			$lat_m = $num / $dec;
		}
		list($num, $dec) = explode('/', $lat2);
		if($num == 0 && $dec > 0){
			$lat_v = $dec;
		}
		if($dec == 0 && $num > 0){
			$lat_v = $num;
		}
		if($dec > 0 && $num > 0){
			$lat_v = $num / $dec;
		}
		//FORM LONGITUDE
		list($num, $dec) = explode('/', $lon0);
		if($num == 0 && $dec > 0){
			$lon_s = $dec;
		}
		if($dec == 0 && $num > 0){
			$lon_s = $num;
		}
		if($dec > 0 && $num > 0){
			$lon_s = $num / $dec;
		}
		list($num, $dec) = explode('/', $lon1);
		if($num == 0 && $dec > 0){
			$lon_m = $dec;
		}
		if($dec == 0 && $num > 0){
			$lon_m = $num;
		}
		if($dec > 0 && $num > 0){
			$lon_m = $num / $dec;
		}
		list($num, $dec) = explode('/', $lon2);
		if($num == 0 && $dec > 0){
			$lon_v = $dec;
		}
		if($dec == 0 && $num > 0){
			$lon_v = $num;
		}
		if($dec > 0 && $num > 0){
			$lon_v = $num / $dec;
		}
		
		$lat_int = ($lat_s + $lat_m / 60.0 + $lat_v / 3600.0);
    	// check orientaiton of latitude and prefix with (-) if S
    	$lat_int = ($lat_ref == "S") ? '-' . $lat_int : $lat_int;

		$lon_int = ($lon_s + $lon_m / 60.0 + $lon_v / 3600.0);
    	// check orientation of longitude and prefix with (-) if W
    	$lon_int = ($lon_ref == "W") ? '-' . $lon_int : $lon_int;

		$gps_int = array($lat_int, $lon_int);
		return $gps_int;
	}
	
	function readGPSinfoEXIFalt($lat_ref,$lat0,$lat1,$lat2,$lon_ref,$lon0,$lon1,$lon2){

		$GPSLatitudeRef  = strtolower(trim($lat_ref));
		$GPSLongitudeRef = strtolower(trim($lon_ref));

		$lat_degrees_a = explode('/',$lat0);
		$lat_minutes_a = explode('/',$lat1);
		$lat_seconds_a = explode('/',$lat2);
		$lng_degrees_a = explode('/',$lon0);
		$lng_minutes_a = explode('/',$lon1);
		$lng_seconds_a = explode('/',$lon2);

		$lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
		$lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
		$lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
		$lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
		$lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
		$lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

		$lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
		$lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

		//If the latitude is South, make it negative. 
		//If the longitude is west, make it negative
		$GPSLatitudeRef  == 's' ? $lat *= -1 : '';
		$GPSLongitudeRef == 'w' ? $lng *= -1 : '';

		return array($lat,$lng);
		
	}
?>