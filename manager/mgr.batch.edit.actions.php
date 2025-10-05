<?php
	//sleep(1);
	
	require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
	//require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	

	require_once('../assets/includes/clean.data.php');

	switch($mode)
	{
		default:
		break;
		case "title":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($batchTitle)
				mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET title='{$batchTitle}' WHERE media_id IN ({$itemsFlat})");
			
			foreach($active_langs as $value)
			{
				if(${'batchTitle_'.$value})	
				{
					$langTitle = ${'batchTitle_'.$value};
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET title_{$value}='{$langTitle}' WHERE media_id IN ({$itemsFlat})");
				}
			}
			
		break;
		case "description":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($batchDesc)
				mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET description='{$batchDesc}' WHERE media_id IN ({$itemsFlat})");
			
			foreach($active_langs as $value)
			{
				if(${'batchDesc_'.$value})	
				{
					$langDesc = ${'batchDesc_'.$value};
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET description_{$value}='{$langDesc}' WHERE media_id IN ({$itemsFlat})");
				}
			}
			
		break;
		
		case "products":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beProducts)
				$productsFlat = implode(',',$beProducts);
			else
				$productsFlat = '0';
				
			if($beProductGroups)
				$productGroupsFlat = implode(',',$beProductGroups);
			else
				$productGroupsFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($beProducts as $product)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT prod_id FROM {$dbinfo[pre]}media_products WHERE media_id = '{$item}' AND prod_id = '{$product}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_products (media_id,prod_id) VALUES ('{$item}','{$product}')");									
						}
						
						foreach($beProductGroups as $productGroup)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT prod_id FROM {$dbinfo[pre]}media_products WHERE media_id = '{$item}' AND prodgrp_id = '{$productGroup}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_products (media_id,prodgrp_id) VALUES ('{$item}','{$productGroup}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_products WHERE media_id IN ({$itemsFlat}) AND prod_id IN ({$productsFlat})"); // Remove prods
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_products WHERE media_id IN ({$itemsFlat}) AND prodgrp_id IN ({$productGroupsFlat})"); // Remove groups
				break;
			}			
		break;
		
		case "prints":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($bePrints)
				$printsFlat = implode(',',$bePrints);
			else
				$printsFlat = '0';
				
			if($bePrintGroups)
				$printGroupsFlat = implode(',',$bePrintGroups);
			else
				$printGroupsFlat = '0';
			
			//test($bePrints,'prints');
			//test($bePrintGroups,'printsgroups');
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($bePrints as $print)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT print_id FROM {$dbinfo[pre]}media_prints WHERE media_id = '{$item}' AND print_id = '{$print}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_prints (media_id,print_id) VALUES ('{$item}','{$print}')");									
						}
						
						foreach($bePrintGroups as $printGroup)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT print_id FROM {$dbinfo[pre]}media_prints WHERE media_id = '{$item}' AND printgrp_id = '{$printGroup}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_prints (media_id,printgrp_id) VALUES ('{$item}','{$printGroup}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_prints WHERE media_id IN ({$itemsFlat}) AND print_id IN ({$printsFlat})"); // Remove prods
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_prints WHERE media_id IN ({$itemsFlat}) AND printgrp_id IN ({$printGroupsFlat})"); // Remove groups
				break;
			}			
		break;
		
		case "packages":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($bePack)
				$packFlat = implode(',',$bePack);
			else
				$packFlat = '0';
				
			if($bePackGroups)
				$packGroupsFlat = implode(',',$bePackGroups);
			else
				$packGroupsFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($bePack as $pack)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT pack_id FROM {$dbinfo[pre]}media_packages WHERE media_id = '{$item}' AND pack_id = '{$pack}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_packages (media_id,pack_id) VALUES ('{$item}','{$pack}')");									
						}
						
						foreach($bePackGroups as $packGroup)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT pack_id FROM {$dbinfo[pre]}media_packages WHERE media_id = '{$item}' AND packgrp_id = '{$packGroup}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_packages (media_id,packgrp_id) VALUES ('{$item}','{$packGroup}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_packages WHERE media_id IN ({$itemsFlat}) AND pack_id IN ({$packFlat})"); // Remove prods
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_packages WHERE media_id IN ({$itemsFlat}) AND packgrp_id IN ({$packGroupsFlat})"); // Remove groups
				break;
			}			
		break;
		
		case "collections":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beCollections)
				$beCollectionsFlat = implode(',',$beCollections);
			else
				$beCollectionsFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($beCollections as $collection)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT coll_id FROM {$dbinfo[pre]}media_collections WHERE cmedia_id = '{$item}' AND coll_id = '{$collection}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_collections (cmedia_id,coll_id) VALUES ('{$item}','{$collection}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_collections WHERE cmedia_id IN ({$itemsFlat}) AND coll_id IN ({$beCollectionsFlat})"); // Remove collections
				break;
			}			
		break;
		
		case "mediaTypes":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beMediaTypes)
				$beMediaTypesFlat = implode(',',$beMediaTypes);
			else
				$beMediaTypesFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($beMediaTypes as $mediaType)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT mtr_id FROM {$dbinfo[pre]}media_types_ref WHERE media_id = '{$item}' AND mt_id = '{$mediaType}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_types_ref (media_id,mt_id) VALUES ('{$item}','{$mediaType}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_types_ref WHERE media_id IN ({$itemsFlat}) AND mt_id IN ({$beMediaTypesFlat})"); // Remove media types
				break;
			}			
		break;
		
		case "galleries":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beMediaGalleries)
				$beMediaGalleriesFlat = implode(',',$beMediaGalleries);
			else
				$beMediaGalleriesFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($beMediaGalleries as $gallery)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT mg_id FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id = '{$item}' AND gallery_id = '{$gallery}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_galleries (gmedia_id,gallery_id) VALUES ('{$item}','{$gallery}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id IN ({$itemsFlat}) AND gallery_id IN ({$beMediaGalleriesFlat})"); // Remove galleries
				break;
			}			
		break;
		
		case "copyright":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			//if($beCopyright)
				mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET copyright='{$beCopyright}' WHERE media_id IN ({$itemsFlat})");
			
			/*
			foreach($active_langs as $value)
			{
				if(${'batchTitle_'.$value})	
				{
					$langTitle = ${'batchTitle_'.$value};
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET title_{$value}='{$langTitle}' WHERE media_id IN ({$itemsFlat})");
				}
			}
			*/
			
		break;
		
		case "usageRestrictions":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beUsageRestrictions)
				mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET usage_restrictions='{$beUsageRestrictions}' WHERE media_id IN ({$itemsFlat})");
			
			/*
			foreach($active_langs as $value)
			{
				if(${'batchTitle_'.$value})	
				{
					$langTitle = ${'batchTitle_'.$value};
					mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET title_{$value}='{$langTitle}' WHERE media_id IN ({$itemsFlat})");
				}
			}
			*/
			
		break;
		
		case "dateAdded":
			
			if($beAddedYear > 0 and $beAddedMonth > 0 and $beAddedDay > 0)
			{
				if(!$beAddedHour) $beAddedHour = '00';
				if(!$beAddedMinute) $beAddedMinute = '00';
				if(!$beAddedSecond) $beAddedSecond = '00';
				$dateAddedString = "{$beAddedYear}-{$beAddedMonth}-{$beAddedDay} {$beAddedHour}:{$beAddedMinute}:{$beAddedSecond}";
				
				$addedDateObj = new kdate;
				$dateAdded = $addedDateObj->formdate_to_gmt($dateAddedString);				
			}
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET date_added='{$dateAdded}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "dateCreated":
			
			if($beCreatedYear > 0 and $beCreatedMonth > 0 and $beCreatedDay > 0)
			{
				if(!$beCreatedHour) $beCreatedHour = '00';
				if(!$beCreatedMinute) $beCreatedMinute = '00';
				if(!$beCreatedSecond) $beCreatedSecond = '00';
				$dateCreatedString = "{$beCreatedYear}-{$beCreatedMonth}-{$beCreatedDay} {$beCreatedHour}:{$beCreatedMinute}:{$beCreatedSecond}";
				
				$createdDateObj = new kdate;
				$dateCreated = $createdDateObj->formdate_to_gmt($dateCreatedString);				
			}
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET date_created='{$dateCreated}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "modelRelease":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET model_release_status='{$beModelRelease}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "propRelease":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET prop_release_status='{$bePropRelease}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "active":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET active='{$beActive}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "featured":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET featured='{$beFeatured}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "originalCopy":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			$licParts = explode('-',$beOriginalCopy);
				
			if($licParts[1])
				$beOriginalCopy = $licParts[1];
			else
				$beOriginalCopy = $licParts[0];
			
			//test($beOriginalCopy);
			
			if($beOriginalCopy)
				mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET license='{$beOriginalCopy}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "filetype":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET format='{$beFiletype}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "price":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			require_once('mgr.defaultcur.php');
				
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			$priceClean = $cleanvalues->currency_clean($bePrice);

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET price='{$priceClean}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "credits":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';

			mysqli_query($db,"UPDATE {$dbinfo[pre]}media SET credits='{$beCredits}' WHERE media_id IN ({$itemsFlat})");
			
		break;
		
		case "digitalSizes":
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($beDigitalSize)
				$beDigitalSizeFlat = implode(',',$beDigitalSize);
			else
				$beDigitalSizeFlat = '0';
			
			switch($mode2)
			{
				case "add":
					
					// If doesn't exist add
					foreach($items as $item)
					{
						foreach($beDigitalSize as $digitalSize)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT mtr_id FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id = '{$item}' AND ds_id = '{$digitalSize}'")) == 0)
								mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_digital_sizes (media_id,ds_id) VALUES ('{$item}','{$digitalSize}')");									
						}
					}
					
				break;
				case "remove":
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id IN ({$itemsFlat}) AND ds_id IN ({$beDigitalSizeFlat})"); // Remove media types
				break;
			}	
		break;
		
		case "grabKeywords":
		
			require_once('../assets/classes/json.php');
			$json = new Services_JSON();
			
			$data['junk']['name'][] = "test";
		
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($itemsFlat)
			{
				
				$defaultLang = strtoupper($config['settings']['lang_file_mgr']);
				
				$keywordResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE media_id IN ({$itemsFlat}) AND (language = '' OR language = '{$defaultLang}') GROUP BY keyword");
				$keywordRows = mysqli_num_rows($keywordResult);
				while($keyword = mysqli_fetch_array($keywordResult))
				{
					$lang = ($keyword['language']) ? $keyword['language'] : $defaultLang;
					$data['keywords'][$lang][] = array('name' => $keyword['keyword'],'lang' => $lang);
				}
								
				foreach($active_langs as $value)
				{				
					$valueUpper = strtoupper($value);
					$keywordResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE media_id IN ({$itemsFlat}) AND language = '{$valueUpper}' GROUP BY keyword");
					$keywordRows = mysqli_num_rows($keywordResult);
					while($keyword = mysqli_fetch_array($keywordResult))
						$data['keywords'][$valueUpper][] = array('name' => $keyword['keyword'],'lang' => $valueUpper);
				}
			}
			
			echo $json->encode($data);
			
			//test($data);
			
		break;
		
		case "addKeyword":
			
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			if($defaultKeyword = ${'beNewKeyword_'.$config['settings']['lang_file_mgr']}) // Check if a keyword is being added for this language
			{	
				//$defaultKeyword = strtolower($defaultKeyword); // Make sure it is lower case
				
				if($langset['id'] == 'russian')
					$defaultKeyword = mb_convert_case($defaultKeyword, MB_CASE_LOWER, "UTF-8");
				else
					$defaultKeyword = strtolower($defaultKeyword);	
				
				$defaultLang = strtoupper($config['settings']['lang_file_mgr']);
				
				$splitKeywords = explode(",",$defaultKeyword);
				
				foreach($items as $mediaID)
				{
					foreach($splitKeywords as $singleKeyword)
					{
						if(mysqli_num_rows(mysqli_query($db,"SELECT key_id FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaID}' AND keyword = '{$defaultKeyword}' AND (language = '{$defaultLang}' OR language = '')")) == 0) // Prevent duplicates
							@mysqli_query($db,"INSERT INTO {$dbinfo[pre]}keywords (keyword,media_id,language) VALUES ('{$singleKeyword}','{$mediaID}','')"); // Default left blank
					}
				}
			}
			
			foreach($active_langs as $value)
			{				
				if(${'beNewKeyword_'.$value}) // Check if a keyword is being added for this language
				{
					
					if($config['keywordsToLower'])
					{
						
						if($langset['id'] == 'russian')
							$thisKeyword = mb_convert_case(${'beNewKeyword_'.$value}, MB_CASE_LOWER, "UTF-8");
						else
							$thisKeyword = strtolower(${'beNewKeyword_'.$value});	
						//$thisKeyword = strtolower(${'beNewKeyword_'.$value}); // Make sure it is lower case
					}
					else
						$thisKeyword = ${'beNewKeyword_'.$value};					
					
					$thisLang = strtoupper($value);
					
					$splitKeywordsOtherLang = explode(",",$thisKeyword);
				
					foreach($items as $mediaID)
					{
						foreach($splitKeywordsOtherLang as $singleKeywordOtherLang)
						{
							if(mysqli_num_rows(mysqli_query($db,"SELECT key_id FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaID}' AND keyword = '{$thisKeyword}' AND language = '{$thisLang}'")) == 0) // Prevent duplicates
								@mysqli_query($db,"INSERT INTO {$dbinfo[pre]}keywords (keyword,media_id,language) VALUES ('{$singleKeywordOtherLang}','{$mediaID}','{$thisLang}')");
						}
					}
				}
			}
			
		break;
		
		case "removeKeyword":
			if($items)
				$itemsFlat = implode(',',$items);
			else
				$itemsFlat = '0';
			
			$defaultLang = strtoupper($config['settings']['lang_file_mgr']);
			
			if($language == $defaultLang)
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}keywords WHERE media_id IN ({$itemsFlat}) AND keyword = '{$keyword}' AND (language = '{$language}' OR language = '')"); // Remove keywords
			else
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}keywords WHERE media_id IN ({$itemsFlat}) AND keyword = '{$keyword}' AND language = '{$language}'"); // Remove keywords
			
			//test($defaultLang,'dlang');
			//test($language,'lang');
			//test($keyword,'keyword');
			
		break;
				
	}

?>