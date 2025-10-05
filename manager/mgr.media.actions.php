<?php
	# INCLUDE THE SESSION START FILE
	require_once('../assets/includes/session.php');

	$page = "media";
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	error_reporting(0);
	
	# INCLUDE SECURITY CHECK FILE
	require_once('mgr.security.php');
	
	# INCLUDE MANAGER CONFIG FILE
	require_once('mgr.config.php');
	
	# INCLUDE TWEAK FILE
	require_once('../assets/includes/tweak.php');
	
	# INCLUDE DATABASE CONFIG FILE
	if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
	
	# INCLUDE DATABASE CONNECTION FILE
	require_once('../assets/includes/db.conn.php');
	
	# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
	require_once('../assets/includes/shared.functions.php');
	
	# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.functions.php');
	
	# SELECT THE SETTINGS DATABASE
	require_once('mgr.select.settings.php');
	
	# INCLUDE THE LANGUAGE FILE	
	include_lang();
	
	# INCLUDE MANAGER ADDONS FILE
	require_once('../assets/includes/addons.php');	
	
	switch($_REQUEST['mode'])
	{	
		case "download":
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			$media = new mediaTools($_GET['mediaID']);
			$mediaInfo = $media->getMediaInfoFromDB();
			$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
			$filecheck = $media->verifyMediaFileExists(); // Returns array [stauts,path,filename]
			
			$filename = $filecheck['filename'];
			$file = "{$filecheck[path]}{$filecheck[filename]}";
			$ctype = "applicatoin/txt";
			
			if (!file_exists($file)) {
				die("NO FILE HERE");
			}
	
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".$filename."\";");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".@filesize($file));
			if(function_exists('set_time_limit')) set_time_limit(0);
			@readfile("$file") or die("File not found."); 
			//unlink($filename);
			exit;
		break;
		
		case "members":
			echo "<ul>";
			$mem_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE l_name LIKE '$_GET[selchar]%'");
			while($mem = mysqli_fetch_object($mem_result))
			{
				if($_GET['media_owner'] == $mem->mem_id)
				{
					echo "<li><strong><a href='mgr.media.php?owner=$mem->mem_id'>$mem->l_name, $mem->f_name ($mem->email)</a></strong></li>";
				}
				else
				{
					echo "<li><a href='mgr.media.php?owner=$mem->mem_id'>$mem->l_name, $mem->f_name ($mem->email)</a></li>";
				}
			}
			echo "</ul>";
		break;
		case "preview":
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('mgr.defaultcur.php');
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			try
			{
				$media = new mediaTools($_GET['mediaid']);
				$mediaInfo = $media->getMediaInfoFromDB();
			}
			catch(Exception $e)
			{
				echo "<span style='color: #EEE'>" . $e->getMessage() . "</span>";
			}
			
			//echo $mediaInfo['media_id'];
			
			//exit;
		
			//$media_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media WHERE media_id = '$_GET[mediaid]'");
			//$media = mysqli_fetch_object($media_result);
			
			$dateObj = new kdate;
			$dateAdded = $dateObj->showdate($mediaInfo['date_added']);
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			$cleanvalues->cur_hide_denotation = 0;
			
			echo "<div style='position: absolute; right: 0; top: 0; padding: 10px; color: #FFF;'>";
			
			# GET TAGS
			if(in_array("tagging",$installed_addons))
			{	
				$tag_result = mysqli_query($db,"SELECT COUNT(key_id) AS numtags FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1' AND memtag = 1");
				$tag = mysqli_fetch_object($tag_result);
				echo "<p style='float: left; margin-right: 10px; color: #CCC; font-size: 10px; margin-top: -1px;'><img src='images/mgr.icon.tags.png' style='vertical-align: middle; margin-top: -4px;' /> $tag->numtags</p>";
			}
			
			# GET COMMENTS
			if(in_array("commenting",$installed_addons))
			{
				$commnet_result = mysqli_query($db,"SELECT COUNT(mc_id) AS numcomments FROM {$dbinfo[pre]}media_comments WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1'");
				$comment = mysqli_fetch_object($commnet_result);
				echo "<p style='float: left; margin-right: 10px; color: #CCC; font-size: 10px; margin-top: -1px;'><img src='images/mgr.icon.comment.png' style='vertical-align: middle; margin-top: -3px;' /> $comment->numcomments</p>";
			}
			
			# GET RATINGS
			if(in_array("rating",$installed_addons))
			{
				$rating_result = mysqli_query($db,"SELECT AVG(rating) AS avgrating FROM {$dbinfo[pre]}media_ratings WHERE media_id = '{$mediaInfo[media_id]}' AND status = '1'");
				$rating = mysqli_fetch_object($rating_result);			
				
				$adjustment = 10/$config['RatingStars'];
				$ratingAverage = $rating->avgrating/$adjustment;
				$ratingForStars = ($config['RatingStarsRoundUp']) ? ceil($ratingAverage) : round($ratingAverage);
				/*
				if($config['RatingStars'] == 5)
				{
					$on_stars = $rating->avgrating/2;
				}
				else
				{
					$on_stars = $rating->avgrating;
				}
				*/
				for($x=1;$x<=$config['RatingStars'];$x++)
				{
					if($x <= $ratingForStars){ $star_status = "1"; } else { $star_status = "0"; }
					echo "<img src='images/mgr.icon.star.$star_status.png' class='rating_star' style='width: 10px;' />";	
				}
			}
			echo "</div>";
?>
			<ul>
				<!-- [todo] only if contr is installed -->
				<li class="detailheader"><?php echo $mgrlang['gen_owner']; ?></li>
				<li>
					<?php 
						if($mediaInfo['owner'] == 0)
						{
							echo $config['settings']['business_name'];	
						}
						else
						{
							$member_result = mysqli_query($db,"SELECT f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '{$mediaInfo[owner]}'");
							$member_rows = mysqli_num_rows($member_result);
							$mgrMemberInfo = mysqli_fetch_object($member_result);
							if($member_rows)
							{
								echo "{$mgrMemberInfo->f_name} {$mgrMemberInfo->l_name}";
							}
							else
							{
								echo $mgrlang['gen_unknown'];	
							}
						}
					?>
				</li>
				
				<li class="detailheader">Views</li>
				<li><?php echo $mediaInfo['views']; ?></li>
				
				<li class="detailheader">Title</li>
				<li><?php if($mediaInfo['title']){ echo $mediaInfo['title']; } else { echo $mgrlang['gen_none']; } ?></li>

                <li class="detailheader">Description</li>
                <li><?php if($mediaInfo['description']){ echo $mediaInfo['description']; } else { echo $mgrlang['gen_none']; }  ?></li>
				
				<?php
					$keywords_result = mysqli_query($db,"SELECT keyword FROM {$dbinfo[pre]}keywords WHERE media_id = '{$mediaInfo[media_id]}' AND language = ''");
					$keywords_rows = mysqli_num_rows($keywords_result);
					
					echo "<li class='detailheader' style='clear: both;'>Keywords</li>";
					echo "<li style='line-height: 2;'>";
					
					$x = 0;
					$keymax = 15;
					if($keywords_rows)
					{
						$keyremaining = $keywords_rows - $keymax;
											
						while($keywords = mysqli_fetch_object($keywords_result) and $x < $keymax)
						{
							echo "<span class='mtag_grey' style='color: #000'><em>{$keywords->keyword}</em></span> ";
							$x++;
						}
						if($keywords_rows > $keymax)
						{
							echo "<em>+{$keyremaining} {$mgrlang[gen_more]}</em>";
						}						
					}
					else
					{
						echo $mgrlang['gen_none'];
					}
					echo "</li>";
				?>
				
				<li class="detailheader" style="clear: left; float: left;">Media ID</li>
				<li class="detailheader" style="float: left;">Batch ID</li>
				<li class="detailheader" style="float: left;">Added</li>
				<li style="clear: left; float: left; width: 120px;"><?php echo $mediaInfo['media_id']; ?></li>
				<li style="float: left; width: 120px;"><?php echo $mediaInfo['batch_id']; ?></li>
				<li style="float: left; width: 120px;"><?php echo $dateAdded; ?></li>
				
				<li class="detailheader" style="clear: left; float: left;">Dimensions</li>
				<li class="detailheader" style="float: left;">Filesize</li>
				<li class="detailheader" style="float: left;">Color Palette</li>
				<li style="clear: left; float: left; width: 120px;"><?php if($mediaInfo['width'] == 0 or $mediaInfo['height'] == 0){ echo $mgrlang['gen_unknown']; } else { echo "<strong>{$mediaInfo[width]}</strong>x<strong>{$mediaInfo[height]}</strong>"; } ?></li>
				<li style="float: left; width: 120px;"><?php echo convertFilesizeToMB($mediaInfo['filesize']); ?>MB</li>
				<li style="float: left; width: 120px;">
					<?php
						$colorPaletteResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}color_palettes WHERE media_id = '{$mediaInfo[media_id]}' ORDER BY percentage DESC");
						$colorPaletteRows = mysqli_num_rows($colorPaletteResult);
						$x = 0;
						while($colorPalette = mysqli_fetch_array($colorPaletteResult))
						{	
							echo "<p style='float: left; width: 12px; height: 12px; margin-right: 1px; margin-bottom: 1px; border: 1px solid #000; background-color: #{$colorPalette[hex]};";
							if($x == 5){ echo "clear: left;"; $x = 1; } else { $x++; }
							echo "'></p>";
						}
					?>
				</li>
				
				<li class="detailheader" style="clear: left; float: left;">Quantity</li>
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?><li class="detailheader" style="float: left;">Original's Price</li><?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print']){ ?><li class="detailheader" style="float: left;">Credits</li><?php } ?>
				<li style="clear: left; float: left; width: 120px;"><?php if($mediaInfo['quantity'] != ''){ echo $mediaInfo['quantity']; } else { echo $mgrlang['gen_unlimited']; } ?></li>
				
				<?php
					# PRICE
					if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
					{
						echo "<li style='float: left; width: 120px;'>";
						
						switch($mediaInfo['license'])
						{
							case "nfs":
								echo $mgrlang['gen_nfs'];
							break;
							case "ex":
							case "eu":
							case "rf":
								//echo "Royalty Free: <strong>";
								echo ($mediaInfo['price'] > 0) ? $cleanvalues->currency_display($mediaInfo['price'],1) : $cleanvalues->currency_display($config['settings']['default_price'],1);	
								//echo "</strong>";
							break;
							case "rm":
								echo $mediaInfo['lic_name'];
							break;
							case "fr":
								echo $mediaInfo['lic_name'];
							break;
							case "cu":
								echo $mediaInfo['lic_name'];
							break;
						}
						echo "</li>";
					}
					
					# CREDITS
					if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
					{	
						echo "<li style='float: left; width: 120px;'>";
						switch($mediaInfo['license'])
						{
							case "nfs":
								echo $mgrlang['gen_nfs'];
							break;
							case "ex":
							case "eu":
							case "rf":
								//echo "Royalty Free: <strong>";
								echo ($mediaInfo['credits'] > 0) ? $mediaInfo['credits'] : $config['settings']['default_credits'];	
								//echo "</strong>";
							break;
							case "rm":
								echo $mediaInfo['lic_name'];
							break;
							case "fr":
								echo $mediaInfo['lic_name'];
							break;
							case "cu":
								echo $mediaInfo['lic_name'];
							break;
						}
						echo "</li>";
					}
				
					# DIGITAL SIZES
					$mdp_result = mysqli_query($db,"SELECT {$dbinfo[pre]}digital_sizes.name FROM {$dbinfo[pre]}media_digital_sizes JOIN {$dbinfo[pre]}digital_sizes ON {$dbinfo[pre]}media_digital_sizes.ds_id = {$dbinfo[pre]}digital_sizes.ds_id WHERE {$dbinfo[pre]}media_digital_sizes.media_id = '{$mediaInfo[media_id]}'");
					$mdp_rows = mysqli_num_rows($mdp_result);
					
					echo "<li class='detailheader' style='clear: both;'>Digital Sizes</li>";
					echo "<li>";
					if($mdp_rows)
					{
						while($mdp = mysqli_fetch_object($mdp_result))
						{
							echo "&bull; {$mdp->name}&nbsp;&nbsp;&nbsp;";
						}
					}
					else
					{
						echo $mgrlang['gen_none'];
					}
					echo "</li>";
					
					# GALLERIES
					$gal_result = mysqli_query($db,"SELECT {$dbinfo[pre]}galleries.name,{$dbinfo[pre]}galleries.icon FROM {$dbinfo[pre]}media_galleries JOIN {$dbinfo[pre]}galleries ON {$dbinfo[pre]}media_galleries.gallery_id = {$dbinfo[pre]}galleries.gallery_id WHERE {$dbinfo[pre]}media_galleries.gmedia_id = '{$mediaInfo[media_id]}'");
					$gal_rows = mysqli_num_rows($gal_result);
					
					echo "<li class='detailheader' style='clear: both;'>{$mgrlang[gen_tab_galleries]}</li>";
					echo "<li>";
					if($gal_rows)
					{
						while($gal = mysqli_fetch_object($gal_result))
						{
							echo "&bull; ";
							if($mediaInfo['media_id'] == $gal->icon)
							{
								echo "<strong>{$gal->name}</strong>";
							}
							else
							{
								echo "{$gal->name}";
							}
							echo "&nbsp;&nbsp;&nbsp;";
						}
					}
					else
					{
						echo $mgrlang['gen_none'];	
					}
					echo "</li>";
					
					
					//$folder_result = mysqli_query($db,"SELECT name,encrypted,enc_name,storage_id FROM {$dbinfo[pre]}folders WHERE folder_id = '{$mediaInfo[folder_id]}'");
					//$folder_rows = mysqli_num_rows($folder_result);
					//$folder = mysqli_fetch_object($folder_result);
					echo "<li class='detailheader' style='clear: both;'>Folder/Filename</li>";
					
					try
					{
						$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
					}
					catch (Exception $e)
					{
						echo "<li><span style='color: #EEE'>" . $e->getMessage() . "</span></li>";	
					}
					
					//echo "<li><span style='color: #EEE'>" . $folderInfo['storageInfo']['storage_type'] . "</span></li>";
					//exit;
					
					try
					{
						$filecheck = $media->verifyMediaFileExists(); // Returns array [stauts,path,filename]
						$filecheckimg = ($filecheck['status']) ? "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px; vertical-align: middle; width: 10px;' />" : "<img src='images/mgr.notice.icon.small2.png' style='margin-right: 4px; vertical-align: middle; width: 15px; margin-top: -2px;' />";
								
						$fullPathFilename = $filecheck['path'].$filecheck['filename'];
						
						if(strlen($fullPathFilename) > 65)
						{
							$filePathSplit = substr($fullPathFilename,0,65)."<br />";
							$filePathSplit.= substr($fullPathFilename,65,strlen($filecheck['path']));
						}
						else
						{
							$filePathSplit = $fullPathFilename;
						}
						
						//$filePathSplit = str_replace($filecheck['filename'],"<span style='font-weight: bold; color: #EEE'>{$filecheck[filename]}</span>");

						echo "<li>{$filecheckimg} {$filePathSplit}</li>";
						
						//echo "<li>{$filecheck}</span></li>";
					}
					catch (Exception $e)
					{
						echo "<li><span style='color: #EEE'>" . $e->getMessage() . "</span></li>";	
					}
					
					
					/*
					
					$folder_name = ($folder->encrypted) ? $folder->enc_name: $folder->name;
					
					if($folder->storage_id == 0)
					{
						$directory_name = $config['settings']['library_path'];
						$filecheck = (file_exists($directory_name . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $mediaInfo['filename'])) ? 1: 0;
						$filecheckimg = ($filecheck) ? "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px; vertical-align: middle; width: 10px;' />" : "<img src='images/mgr.notice.icon.small2.png' style='margin-right: 4px; vertical-align: middle; width: 15px; margin-top: -2px;' />";
					}
					else
					{
						$folder_result = mysqli_query($db,"SELECT name,encrypted,enc_name,storage_id FROM {$dbinfo[pre]}folders WHERE folder_id = '{$mediaInfo[folder_id]}'");
						$folder_rows = mysqli_num_rows($folder_result);
						$folder = mysqli_fetch_object($folder_result);
						
						$directory_name = '';
					}
					
					echo "<li>{$filecheckimg}{$directory_name}" . DIRECTORY_SEPARATOR . "{$folder_name}" . DIRECTORY_SEPARATOR . "<span style='font-weight: bold; color: #EEE'>{$mediaInfo[filename]}</span></li>";
					
					*/
				
					# PRINTS
					if($config['MediaPopupPrints'])
					{	
						$prints_result = mysqli_query($db,"SELECT print_id,printgrp_id FROM {$dbinfo[pre]}media_prints WHERE media_id = '{$mediaInfo[media_id]}'");
						$prints_rows = mysqli_num_rows($prints_result);
						
						echo "<li class='detailheader' style='clear: both;'>Prints</li>";
						echo "<li>";
						if($prints_rows)
						{
							while($prints = mysqli_fetch_object($prints_result))
							{
								if($prints->print_id)
								{
									$printdetails_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}prints WHERE print_id = '$prints->print_id'");
									$printdetails = mysqli_fetch_object($printdetails_result);
									$print_name = $printdetails->item_name;
								}
								elseif($prints->printgrp_id)
								{
									$printgroup_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}groups WHERE gr_id = '$prints->print_id'");
									$printgroup = mysqli_fetch_object($printgroup_result);
									$print_name = $printgroup->name . " (group)";
								}
								
								echo "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;' />{$print_name}&nbsp;&nbsp;&nbsp;";
							}
						}
						else
						{
							echo $mgrlang['gen_none'];	
						}
						echo "</li>";
					}
					
					# PRODUCTS
					if($config['MediaPopupProducts'])
					{						
						$prod_result = mysqli_query($db,"SELECT prod_id,prodgrp_id FROM {$dbinfo[pre]}media_products WHERE media_id = '{$mediaInfo[media_id]}'");
						$prod_rows = mysqli_num_rows($prod_result);
						
						echo "<li class='detailheader' style='clear: both;'>Products</li>";
						echo "<li>";
						if($prod_rows)
						{
							while($prod = mysqli_fetch_object($prod_result))
							{
								if($prod->prod_id)
								{
									$proddetails_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}products WHERE prod_id = '$prod->prod_id'");
									$proddetails = mysqli_fetch_object($proddetails_result);
									$prod_name = $proddetails->item_name;
								}
								elseif($prod->prodgrp_id)
								{
									$prodgroup_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}groups WHERE gr_id = '$prod->prod_id'");
									$prodgroup = mysqli_fetch_object($printgroup_result);
									$prod_name = $prodgroup->name . " (group)";
								}
								
								echo "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;' />{$prod_name}&nbsp;&nbsp;&nbsp;";
							}
						}
						else
						{
							echo $mgrlang['gen_none'];	
						}
						echo "</li>";
					}
					
					# PACKAGES
					if($config['MediaPopupPackages'])
					{	
						$pack_result = mysqli_query($db,"SELECT pack_id,packgrp_id FROM {$dbinfo[pre]}media_packages WHERE media_id = '{$mediaInfo[media_id]}'");
						$pack_rows = mysqli_num_rows($pack_result);
						
						echo "<li class='detailheader' style='clear: both;'>Packages</li>";
						echo "<li>";
						if($pack_rows)
						{
							while($pack = mysqli_fetch_object($pack_result))
							{
								if($pack->pack_id)
								{
									$packdetails_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}packages WHERE pack_id = '$pack->pack_id'");
									$packdetails = mysqli_fetch_object($packdetails_result);
									$pack_name = $packdetails->item_name;
								}
								elseif($pack->packgrp_id)
								{
									$packgroup_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}groups WHERE gr_id = '$pack->pack_id'");
									$packgroup = mysqli_fetch_object($printgroup_result);
									$pack_name = $packgroup->name . " (group)";
								}
								
								echo "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;' />{$pack_name}&nbsp;&nbsp;&nbsp;";
							}
						}
						else
						{
							echo $mgrlang['gen_none'];	
						}
						echo "</li>";
					}
					
					# COLLECTIONS
					if($config['MediaPopupCollections'])
					{
						$coll_result = mysqli_query($db,"SELECT coll_id FROM {$dbinfo[pre]}media_collections WHERE cmedia_id = '{$mediaInfo[media_id]}'");
						$coll_rows = mysqli_num_rows($coll_result);
						
						echo "<li class='detailheader' style='clear: both;'>Collections</li>";
						echo "<li>";
						if($coll_rows)
						{
							while($coll = mysqli_fetch_object($coll_result))
							{
								$colldetails_result = mysqli_query($db,"SELECT item_name FROM {$dbinfo[pre]}collections WHERE coll_id = '$coll->coll_id'");
								$colldetails = mysqli_fetch_object($colldetails_result);
								$coll_name = $colldetails->item_name;
								echo "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;' />{$coll_name}&nbsp;&nbsp;&nbsp;";
							}
						}
						else
						{
							echo $mgrlang['gen_none']; 	
						}
					}
					
					# MEDIA TYPES
					if($config['MediaPopupMediaTypes'])
					{
						$mediatype_result = mysqli_query($db,"SELECT mt_id FROM {$dbinfo[pre]}media_types_ref WHERE media_id = '{$mediaInfo[media_id]}'");
						$mediatype_rows = mysqli_num_rows($mediatype_result);
						
						echo "<li class='detailheader' style='clear: both;'>Media Types</li>";
						echo "<li>";
						if($mediatype_rows)
						{
							while($mediatype = mysqli_fetch_object($mediatype_result))
							{
								$mediatypedetails_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}media_types WHERE mt_id = '$mediatype->mt_id'");
								$mediatypedetails = mysqli_fetch_object($mediatypedetails_result);
								$mediatype_name = $mediatypedetails->name;
								echo "<img src='images/mgr.tiny.check.1.png' style='margin-right: 4px;' />{$mediatype_name}&nbsp;&nbsp;&nbsp;";
							}
						}
						else
						{
							echo $mgrlang['gen_none']; 
						}
						echo "</li>";
					}
				?>
                
            </ul>
<?php
		break;
		case "featured":
			$media_result = mysqli_query($db,"SELECT title FROM {$dbinfo[pre]}media WHERE media_id = '$_GET[id]'");
			$media = mysqli_fetch_object($media_result);
			
			$sql = "UPDATE {$dbinfo[pre]}media SET featured='{$_GET[newval]}' WHERE media_id = '$_GET[id]'";
			$result = mysqli_query($db,$sql);
			
			$save_type = ($_GET['newval']==1) ? $mgrlang['gen_feature'] : $mgrlang['gen_unfeature'];
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media'],1,"{$save_type} > <strong>{$media->title} ({$_GET[id]})</strong>");
			
			echo "<script>\$('featured_{$_GET[id]}').setAttribute('src','images/mgr.icon.featured.{$_GET[newval]}.png');</script>";
		break;
		case "status":
			$media_result = mysqli_query($db,"SELECT title FROM {$dbinfo[pre]}media WHERE media_id = '$_GET[id]'");
			$media = mysqli_fetch_object($media_result);
			
			$sql = "UPDATE {$dbinfo[pre]}media SET active='{$_GET[newval]}' WHERE media_id = '$_GET[id]'";
			$result = mysqli_query($db,$sql);
			
			$save_type = ($_GET['newval']==1) ? $mgrlang['gen_active'] : $mgrlang['gen_inactive'];
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media'],1,"{$save_type} > <strong>{$media->title} ({$_GET[id]})</strong>");
			
			echo "<script>";
				echo "\$('status_{$_GET[id]}').setAttribute('src','images/mgr.icon.active.{$_GET[newval]}.png');";
				/*
				if(($config['settings']['featured_media'] or $config['settings']['featuredpage']) and $_GET['newval'] == 0)
				{
					echo "\$('featured_{$_GET[id]}').setAttribute('src','images/mgr.icon.featured.0.png');";
				}
				*/
			echo "</script>";
		break;
		case "delete": // Not used
		
		break;
		case "setGalleryAvatar":
			if($_GET['newval']==1)
			{
				$sql = "UPDATE {$dbinfo[pre]}galleries SET icon='{$_GET[id]}' WHERE gallery_id = '$_GET[galleryID]'";
				$result = mysqli_query($db,$sql);
			}
			else
			{
				$sql = "UPDATE {$dbinfo[pre]}galleries SET icon='0' WHERE gallery_id = '$_GET[galleryID]'";
				$result = mysqli_query($db,$sql);
			}
			
			$save_type = ($_GET['newval']==1) ? $mgrlang['gen_set_galicon'] : $mgrlang['gen_unset_galicon'];
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_media'],1,"{$save_type} > {$_GET[id]} ({$mgrlang[gen_gallery]} {$_GET[galleryID]})</strong>");
			
			echo "<script>$('avatar_{$_GET[id]}').setAttribute('src','images/mgr.icon.seticon.{$_GET[newval]}.png');</script>";
		break;
		case "galleries":
			echo "<input type='hidden' name='checkGalLoaded' value='1' />";
			
			$galleries_result = mysqli_query($db,"SELECT gallery_id FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id = '$_GET[mediaID]'");
			while($galleries = mysqli_fetch_object($galleries_result))
			{
				$inGalleries[] = $galleries->gallery_id;	
			}
			
			if($_GET['gal_mem'])
			{
				$gal_mem = $_GET['gal_mem'];
			}
			else
			{
				$gal_mem = 0;	
			}
			//echo $_GET['gal_mem'];
			// CREATE ARRAY TO WORK WITH							
			$folders = array();
			$folders['name'] = array();
			$folders['folder_id'] = array();
			$folders['parent_id'] = array();
			$folders['folder_rows'] = array();
			$folders['pass_protected'] = array();
			$folder_array_id = 1;
			
			// READ STRUCTURE FUNCTION															
			read_gal_structure(0,'name','',$gal_mem);
			//read_gal_structure(0,$listby,$listtype,$_SESSION['galmem']);
			
			/*
			echo "<div style=\"padding: 0 0 0 10px; margin: 0px; background-color: #eee\">";
			echo "<img src=\"images/mgr.folder.icon.small2.gif\" align=\"absmiddle\" /> <input type='radio' name='parent_gal' value='0' class='radio' style='margin-left: -15px;'";
				if($_GET['edit'] == "new" or $gallery->parent_gal == 0){
					echo " checked";
				}
			echo " /> <strong>None</strong></div>";
			*/
		
			//$gallery_parent = $gallery->parent_gal;
			$gallery_current = 0;
			
			# BUILD THE GALLERIES AREA
			$mygalleries = new build_galleries;
			$mygalleries->scroll_offset_id = "gals";
			//$mygalleries->alt_colorA = "efefef";
			$mygalleries->scroll_offset = 1;
			$mygalleries->selected_gals = $inGalleries;
			$mygalleries->options_name = 'media_galleries[]';
			$mygalleries->options = "checkbox";
			$mygalleries->output_struc_array(0);
		break;
		case "addKeyword":
			require_once('../assets/includes/clean.data.php');
			require_once('../assets/classes/json.php');
			
			$json = new Services_JSON();
			
			if($language == 'DEFAULT'){ $language = ''; }
			
			if($config['keywordsToLower'])
			{				
				if($langset['id'] == 'russian')
					$keyword = mb_convert_case($keyword, MB_CASE_LOWER, "UTF-8");
				else
					$keyword = strtolower($keyword);				
			}
			
			$splitBy = ',';
			
			if(strpos($keyword,',') !== false) // Split by commas
				$splitBy = ',';	
			elseif(strpos($keyword,';') !== false)
				$splitBy = ';';
				
			$keywords = explode($splitBy,$keyword);
			
			$_SESSION['testing']['keywords'] = count($keywords);
			
			foreach($keywords as $singleKeyword)
			{
				$singleKeyword = trim($singleKeyword);
				
				// Find if keyword already exists
				$keywordResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}keywords WHERE keyword = '{$singleKeyword}' AND media_id = '{$mediaID}' AND language == '{$language}'");
				$keywordRows = mysqli_num_rows($keywordResult);
				
				if(!$keywordRows)
				{	
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}keywords (
							keyword,
							media_id,
							language
							) VALUES (
							'{$singleKeyword}',
							'{$mediaID}',
							'{$language}'
							)";
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					
					$data['keywords']['name'][] = $singleKeyword;
					$data['keywords']['saveID'][] = $saveid;
				}
			}
			
			$data['saveID'] = $saveid;
			echo $json->encode($data);
			//echo $saveid;
			//var templatedata = "<input type=\"button\" onclick=\"remove_keyword('DEFAULT_key_"+newkeywordid+"')\" keyword=\"\" kwlanguage=\""+language+"\" id=\"DEFAULT_key_"+newkeywordid+"\" value=\""+new_keyword+"\" />";
			//templatedata += "<input type=\"hidden\" name=\"keyword_"+language+"[]\" id=\"DEFAULT_key_"+newkeywordid+"_input\" value=\""+new_keyword+"\" />";
			
			// ACTIVITY LOG [todo]
				
		break;
		case "removeKeyword":
			# DELETE
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}keywords WHERE key_id = '{$_GET[keyID]}'");
			
			// ACTIVITY LOG [todo]
		break;
		case "updateMediaDetails":
			//sleep(1);
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			# GET MEDIA INFORMATION
			$media = new mediaTools($mediaID);
			$mediaInfo = $media->getMediaInfoFromDB();
			$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);			
			$folderName = ($folderInfo['encrypted']) ? $folderInfo['enc_name'] : $folderInfo['name'];
			
			$variationsPath = $config['settings']['library_path'].DIRECTORY_SEPARATOR.$folderName.DIRECTORY_SEPARATOR."variations".DIRECTORY_SEPARATOR;
			
			$fileNameNoExtensionArray = explode(".",$mediaInfo['filename']);
			array_pop($fileNameNoExtensionArray);
			$fileNameNoExtension = implode(".",$fileNameNoExtensionArray);
			
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('mgr.defaultcur.php');
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			$price_clean = $cleanvalues->currency_clean($price);
			
			/*
			if(!$created_year) $created_year = '0000';
			if(!$created_month) $created_month = '00';
			if(!$created_day) $created_day = '00';
			if(!$created_hour) $created_hour = '00';
			if(!$created_minute) $created_minute = '00';
			if(!$created_second) $created_second = '00';
			*/
			
			if($created_year > 0 and $created_month > 0 and $created_day > 0)
			{
				if(!$created_hour) $created_hour = '00';
				if(!$created_minute) $created_minute = '00';
				if(!$created_second) $created_second = '00';
				$dateCreatedString = "{$created_year}-{$created_month}-{$created_day} {$created_hour}:{$created_minute}:{$created_second}";
				
				$createdDateObj = new kdate;
				$dateCreated = $createdDateObj->formdate_to_gmt($dateCreatedString);				
			}
			
			if($added_year > 0 and $added_month > 0 and $added_day > 0)
			{
				if(!$added_hour) $added_hour = '00';
				if(!$added_minute) $added_minute = '00';
				if(!$added_second) $added_second = '00';
				$dateAddedString = "{$added_year}-{$added_month}-{$added_day} {$added_hour}:{$added_minute}:{$added_second}";
				
				$addedDateObj = new kdate;
				$dateAdded = $addedDateObj->formdate_to_gmt($dateAddedString);				
			}
			
			//$ndate->formdate_to_gmt($active_date);
			
			# ADD SUPPORT FOR ADDITIONAL LANGUAGES
			if($active_langs)
			{
				foreach(array_unique($active_langs) as $value){ 
					$title_val = ${"title_" . $value};
					$description_val = ${"description_" . $value};
					$addsql.= "title_$value='{$title_val}',";
					$addsql.= "description_$value='{$description_val}',";
				}
			}
			
			if($approvalStatus == 1 and $approvalStatus != $mediaInfo['approval_status'])
				$approvalDate = gmdate("Y-m-d H:m:s");
			else
				$approvalDate = $mediaInfo['approval_date'];
			
			if(!$mediaInfo['owner']) $approvalStatus = 1; // Make sure approval stays at 1 if the owner is 0
			
			//test($mediaInfo['owner']);
			
			$licParts = explode('-',$original_copy);
				
			if($licParts[1])
				$original_copy = $licParts[1];
			else
				$original_copy = $licParts[0];
			
			# UPDATE MEDIA THE DATABASE
			$sql = "UPDATE {$dbinfo[pre]}media SET 
						approval_status='{$approvalStatus}',
						approval_message='{$approvalMessage}',
						approval_date='{$approvalDate}',
						title='$title',
						description='$description',
						sortorder='$sortorder',
						external_link='$external_link',
						date_created='$dateCreated',
						date_added='$dateAdded',
						active='$active',
						license='$original_copy',
						width='{$width}',
						height='{$height}',
						dsp_type='{$dsp_type}',
						model_release_status='{$model_release_status}',
						prop_release_status='{$prop_release_status}',
						copyright='{$copyright}',
						usage_restrictions='{$usage_restrictions}',
						hd='{$hd}',
						fps='{$fps}',
						format='{$format}',
						running_time='{$running_time}',
						quantity='$quantity',
						price='$price_clean',";
				$sql.= $addsql;				
				$sql.= "credits='$credits'
						WHERE media_id  = '{$mediaID}'";
			$result = mysqli_query($db,$sql);
			
			# MAKE SURE THE GALLERIES LOADED BEFORE UPDATING
			if($checkGalLoaded)
			{
				# DELETE GALLERIES
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_galleries WHERE gmedia_id = '{$mediaID}'");
				
				# SAVE GALLERIES
				foreach($mediaGalleries as $value)
				{
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_galleries (
							gmedia_id,
							gallery_id
							) VALUES (
							'{$mediaID}',
							'{$value}'
							)";
					$result = mysqli_query($db,$sql);
				}
			}
			
			# DELETE GALLERIES
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id  = '{$mediaID}' AND mgrarea = 'media'");
			
			# SAVE MEDIA GROUPS
			foreach($setgroups as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}groupids (
						item_id,
						group_id,
						mgrarea
						) VALUES (
						'{$mediaID}',
						'{$value}',
						'media'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# DELETE MEDIA TYPES
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_types_ref WHERE media_id  = '{$mediaID}'");
			
			# SAVE MEDIA TYPES
			foreach($media_types as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_types_ref (
						media_id,
						mt_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# DELETE MEDIA COLLECTIONS
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_collections WHERE cmedia_id  = '{$mediaID}'");
			
			# SAVE MEDIA TYPES
			foreach($collection as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_collections (
						cmedia_id,
						coll_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# DELETE DIGITAL PROFILE GROUPS
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_digital_sizes WHERE dsgrp_id != '0' AND media_id = '{$mediaID}'");
			
			# SAVE DIGITAL PROFILE GROUPS
			foreach($digitalgroup as $value)
			{	
				# INSERT INFO INTO THE DATABASE
				@mysqli_query($db,"INSERT INTO {$dbinfo[pre]}media_digital_sizes (media_id,dsgrp_id) VALUES ('{$mediaID}','{$value}')");
			}
			//test($sql);
			
			# ORIGINALLY SELECTED DIGITAL SIZES
			$originallySelectedDigitalSizes = explode(",",$originalMediaDS);
			# GET NEW OR UPDATED DIGITAL SP ENTRIES
			foreach($digitalsp as $value)
			{
				// Check to see if temp file exists for this one
				$tempDSPresult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_dsp_temp WHERE media_id = '{$mediaID}' AND dsp_id = '{$value}' ORDER BY tmpid DESC");
				$tempDSProws = mysqli_num_rows($tempDSPresult);
				if($tempDSProws)
				{	
					$tempDSP = mysqli_fetch_array($tempDSPresult);
					//echo "{$variationsPath}{$fileNameNoExtension}<br /><br />";
					
					$dspExt = strtolower(end(explode(".",$tempDSP[filename]))); // Find the extension of the dsp file
					$newDSPname = zerofill($value,3)."_{$fileNameNoExtension}.{$dspExt}";
					
					// Move temp file to correct location
					copy("../assets/tmp/{$tempDSP[filename]}","{$variationsPath}{$newDSPname}");
					/*if(copy("../assets/tmp/{$tempDSP[filename]}","{$variationsPath}{$newDSPname}"))
						$_SESSION['testing']['cpy'] = "{$variationsPath}{$newDSPname}";
					else
						$_SESSION['testing']['cpy'] = 'no';
					*/
					
					// Delete temp file
					unlink("../assets/tmp/{$tempDSP[filename]}");
					
					$origDSPname = $tempDSP['ofilename'];
				}
				else
				{
					$newDSPname = ${'dsp_filename_'.$value};
					$origDSPname = ${'dsp_ofilename_'.$value};
				}
				
				if(in_array($value,$originallySelectedDigitalSizes))
				{
					$dsp_price_clean = $cleanvalues->currency_clean(${'dsp_price_'.$value});
					
					$sql = "UPDATE {$dbinfo[pre]}media_digital_sizes SET 
						license='".${'dsp_license_'.$value}."',
						rm_license='".${'dsp_rm_license_'.$value}."',
						price='{$dsp_price_clean}',
						price_calc='".${'dsp_price_calc_'.$value}."',
						credits='".${'dsp_credits_'.$value}."',
						credits_calc='".${'dsp_credits_calc_'.$value}."',
						customized='".${'dsp_customized_'.$value}."',
						quantity='".${'dsp_quantity_'.$value}."',
						width='".${'dsp_width_'.$value}."',
						height='".${'dsp_height_'.$value}."',
						format='".${'dsp_format_'.$value}."',
						hd='".${'dsp_hd_'.$value}."',
						running_time='".${'dsp_running_time_'.$value}."',
						fps='".${'dsp_fps_'.$value}."',
						auto_create='".${'dsp_autocreate_'.$value}."',
						external_link='".${'dspExternalLink-'.$value}."',
						filename='{$newDSPname}',
						ofilename='{$origDSPname}'
						WHERE media_id = '{$mediaID}' AND ds_id = '{$value}'";
					$result = mysqli_query($db,$sql);
				}
				else
				{
					$dsp_price_clean = $cleanvalues->currency_clean(${'dsp_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_digital_sizes (
							media_id,
							ds_id,
							license,
							rm_license,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity,
							width,
							height,
							format,
							external_link,
							hd,
							running_time,
							fps,
							auto_create,
							filename,
							ofilename
							) VALUES (
							'{$mediaID}',
							'$value',
							'".${'dsp_license_'.$value}."',
							'".${'dsp_rm_license_'.$value}."',
							'$dsp_price_clean',
							'".${'dsp_price_calc_'.$value}."',
							'".${'dsp_credits_'.$value}."',
							'".${'dsp_credits_calc_'.$value}."',
							'".${'dsp_customized_'.$value}."',
							'".${'dsp_quantity_'.$value}."',
							'".${'dsp_width_'.$value}."',
							'".${'dsp_height_'.$value}."',
							'".${'dsp_format_'.$value}."',
							'".${'dspExternalLink-'.$value}."',
							'".${'dsp_hd_'.$value}."',
							'".${'dsp_running_time_'.$value}."',
							'".${'dsp_fps_'.$value}."',
							'".${'dsp_autocreate_'.$value}."',
							'{$newDSPname}',
							'{$origDSPname}'
							)";
					$result = mysqli_query($db,$sql);
				}
			}
			
			# GET REMOVED DIGITAL SP ENTRIES
			foreach($originallySelectedDigitalSizes as $value)
			{
				if(!in_array($value,$digitalsp))
				{
					
					$dspResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id = '{$mediaID}' AND ds_id = '{$value}'"); // Find the details for the media size
					$dspRows = mysqli_num_rows($dspResult);
					if($dspRows)
					{
						$dsp = mysqli_fetch_array($dspResult);
						
						@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id  = '{$mediaID}' AND ds_id = '$value' AND dsgrp_id = 0"); // Delete the record
						
						@unlink("{$variationsPath}{$dsp[filename]}");// Delete the file
					}				
				}
			}
			
			// Check for and delete any temp records
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_dsp_temp WHERE media_id  = '{$mediaID}'");
			
			# DELETE PRODUCT GROUPS
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_products WHERE prodgrp_id != '0' AND media_id = '{$mediaID}'");
			
			# SAVE PRODUCT GROUPS
			foreach($prodgroup as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_products (
						media_id,
						prodgrp_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# ORIGINALLY SELECTED PRODUCTS
			$originallySelectedProducts = explode(",",$originalMediaProd);
			# GET NEW OR UPDATED PRODUCT ENTRIES
			foreach($proditem as $value)
			{
				if(in_array($value,$originallySelectedProducts))
				{
					$prod_price_clean = $cleanvalues->currency_clean(${'prod_price_'.$value});
					
					$sql = "UPDATE {$dbinfo[pre]}media_products SET 
						price='$prod_price_clean',
						price_calc='".${'prod_price_calc_'.$value}."',
						credits='".${'prod_credits_'.$value}."',
						credits_calc='".${'prod_credits_calc_'.$value}."',
						customized='".${'prod_customized_'.$value}."',
						quantity='".${'prod_quantity_'.$value}."'
						WHERE media_id = '{$mediaID}' AND prod_id = '{$value}'";
					$result = mysqli_query($db,$sql);
				}
				else
				{
					$prod_price_clean = $cleanvalues->currency_clean(${'prod_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_products (
							media_id,
							prod_id,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity
							) VALUES (
							'{$mediaID}',
							'$value',
							'$prod_price_clean',
							'".${'prod_price_calc_'.$value}."',
							'".${'prod_credits_'.$value}."',
							'".${'prod_credits_calc_'.$value}."',
							'".${'prod_customized_'.$value}."',
							'".${'prod_quantity_'.$value}."'
							)";
					$result = mysqli_query($db,$sql);
				}
			}
			# GET REMOVED PRODUCT ENTRIES
			foreach($originallySelectedProducts as $value)
			{
				if(!in_array($value,$proditem) and $value)
				{
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_products WHERE media_id  = '{$mediaID}' AND prod_id = '$value'");
				}
			}
			
			# DELETE PRINT GROUPS
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_prints WHERE printgrp_id != '0' AND media_id = '{$mediaID}'");
			
			# SAVE PRINT GROUPS
			foreach($printgroup as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_prints (
						media_id,
						printgrp_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# ORIGINALLY SELECTED PRINTS
			$originallySelectedPrints = explode(",",$originalMediaPrint);
			# GET NEW OR UPDATED PRINT ENTRIES
			foreach($printitem as $value)
			{
				if(in_array($value,$originallySelectedPrints))
				{
					$print_price_clean = $cleanvalues->currency_clean(${'print_price_'.$value});
					
					$sql = "UPDATE {$dbinfo[pre]}media_prints SET 
						price='$print_price_clean',
						price_calc='".${'print_price_calc_'.$value}."',
						credits='".${'print_credits_'.$value}."',
						credits_calc='".${'print_credits_calc_'.$value}."',
						customized='".${'print_customized_'.$value}."',
						quantity='".${'print_quantity_'.$value}."'
						WHERE media_id = '{$mediaID}' AND print_id = '{$value}'";
					$result = mysqli_query($db,$sql);
				}
				else
				{
					$print_price_clean = $cleanvalues->currency_clean(${'print_price_'.$value});
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}media_prints (
							media_id,
							print_id,
							price,
							price_calc,
							credits,
							credits_calc,
							customized,
							quantity
							) VALUES (
							'{$mediaID}',
							'$value',
							'$print_price_clean',
							'".${'print_price_calc_'.$value}."',
							'".${'print_credits_'.$value}."',
							'".${'print_credits_calc_'.$value}."',
							'".${'print_customized_'.$value}."',
							'".${'print_quantity_'.$value}."'
							)";
					$result = mysqli_query($db,$sql);
				}
			}
			# GET REMOVED PRINT ENTRIES
			foreach($originallySelectedPrints as $value)
			{
				if(!in_array($value,$printitem) and $value)
				{
					@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_prints WHERE media_id  = '{$mediaID}' AND print_id = '$value'");
				}
			}
			
			# DELETE PACKAGES AND GROUPS
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_packages WHERE media_id = '{$mediaID}'");
			
			# SAVE PACKAGE GROUPS
			foreach($packgroup as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_packages (
						media_id,
						packgrp_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			# SAVE PACKAGES
			foreach($packages as $value)
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}media_packages (
						media_id,
						pack_id
						) VALUES (
						'{$mediaID}',
						'{$value}'
						)";
				$result = mysqli_query($db,$sql);
			}
			
			if($approvalStatus != $mediaInfo['approval_status'])	
			{		
				//$('approvalStatus{$mediaID}').hide();
				echo "<script>";
				
				echo "\$('approvalStatus{$mediaID}').className = 'approvalStatus{$approvalStatus}'; \$('approvalStatus{$mediaID}').update('".$mgrlang['approvalStatus'.$approvalStatus]."');";
				
				if(in_array("contr",$installed_addons))
				{
					$_SESSION['pending_media'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(media_id) FROM {$dbinfo[pre]}media WHERE owner != '0' AND approval_status = '0'"));					
					echo "\$('contrMediaPendingApproval').update('".$_SESSION['pending_media']."');";	
					echo "\$('hnp_media').update('".$_SESSION['pending_media']."');";					
				}
				
				echo "</script>";
			}
		break;
		case "deleteFileDSP":
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			# CHECK TEMP DB
			$dspTempResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_dsp_temp WHERE media_id = '{$mediaID}' AND dsp_id = '{$dspID}'");
			$dspTempRows = mysqli_num_rows($dspTempResult);
			if($dspTempRows)
			{
				$dspTemp = mysqli_fetch_array($dspTempResult);
				
				@unlink("../assets/tmp/{$dspTemp[filename]}"); // Delete temp file
				
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}media_dsp_temp WHERE tmpid = '{$dspTemp[tmpid]}'"); // Delete from temp db
			}
			
			# CHECK LIBRARY DB
			$dspResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_digital_sizes WHERE media_id = '{$mediaID}' AND ds_id = '{$dspID}'");
			$dspRows = mysqli_num_rows($dspResult);
			if($dspRows)
			{
				$dsp = mysqli_fetch_array($dspResult);
				
				$media = new mediaTools($mediaID);
				$mediaInfo = $media->getMediaInfoFromDB();
				$folderInfo = $media->getFolderStorageInfoFromDB($mediaInfo['folder_id']);
				
				if($folderInfo['encrypted'])
					$folderName = $folderInfo['enc_name'];
				else
					$folderName = $folderInfo['name'];
					
				$folderPath = "{$config[settings][library_path]}/{$folderName}/variations/";
				
				@unlink("{$folderPath}{$dsp[filename]}"); // Delete file
				
				@mysqli_query($db,"UPDATE {$dbinfo[pre]}media_digital_sizes SET filename='',external_link='' WHERE mds_id = '{$dsp[mds_id]}'"); // Update db
			}
			
		break;
		
		case "saveExternalLinkDSP":
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			# MEDIATOOLS CLASS
			require_once('../assets/classes/mediatools.php');
			
			
			@mysqli_query($db,"UPDATE {$dbinfo[pre]}media_digital_sizes SET external_link='{$externalLink}' WHERE media_id = '{$mediaID}' AND ds_id = '{$dspID}'"); // Update db
			
		break;
	}
?>


