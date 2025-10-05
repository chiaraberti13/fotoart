<?php
	
# MANAGER TWEAKS --------------------------------------------------------------------------------------------------------

	$config['dragables']			= 1; 			// Dragable elements can be sorted by dragging them - 1 for on | 0 for off
	$config['popups']				= 1; 			// Popups display when rolling over elements - 1 for on | 0 for off
	$config['QuickEditSave']		= 1; 			// Keep the asset details window open after saving - 1 for save and close | 0 for save and keep open
	$config['AssetThumbSize']		= 100; 			// Size of the thumbnails in the assets area / if too high will need to use thumbnail instead / changing this value will slow down loading
	$config['SaveThumbQuality']		= 100;			// Quality to save the thumbnails at 1-100
	$config['SaveAvatarQuality']	= 9;			// Quality to save avatars at 1-9
	$config['DisplayAvatarQuality']	= 9;			// Quality to display avatars at 1-9
	$config['SaveSampleQuality']	= 100;			// Quality to save the samples at
	$config['SetFilePermissions']	= 0777;			// Select the permissions to assign to new folders and files - 0777 or 0755 are recomended
	$config['DefaultMemory']		= 256;			// Default PHP memory limit if none is specified.
	$config['RetainSorting']		= 1;			// Retain the sorting of your data in lists when moving from one area in the manager to another. 1 for on | 0 for off
	$config['SharpenCode']			= 90;			// 
	$config['DisplayRatesAsNew']	= 24;			// Display currency rates as new as long as they are not older than X amount of hours
	$config['OffsiteStogageLimit']	= 20;			// This is the maximum MB for each file that can be transfered to a storage location other than the local server
	$config['MaxBatchUpload']		= 1000;			// This is the maximum number of files that can be uploaded in a single batch
	
	#BACKUPS
	$config['BU_Templates']			= 1;			// During an upgrade backup the template files first -  1 for on | 0 for off
	$config['BU_Languages']			= 1;			// During an upgrade backup the language files first -  1 for on | 0 for off
	$config['BU_Database']			= 1;			// During an upgrade backup the database first -  1 for on | 0 for off 	
	
	$config['colorProfile']			= 'sRGB.icm';	// Color profile to use with ImageMagick - located in /assets/colorprofiles/
	$config['IconDefaultSize']		= 150;			// Size that the icon gets stored at on the server
	$config['ThumbDefaultSize']		= 500;			// Size that the thumbnail gets stored at on the server
	$config['SampleDefaultSize']	= 1600;			// Size that the sample gets stored at on the server // Changed to 1600 for 4.6
	$config['ShowImportIcons']		= 1;			// Show thumbnails next to files in import window 1 for on | 0 for off. Turning this off will speed things up and put less load on the server
	$config['ImportRest']			= 0;			// How long to rest for inbetween importing files in Javascript
	$config['ImportSleep']			= 0;			// How long to sleep before starting the next file import in PHP / may be required if the server is having problems with quick importing
	$config['ImportPreviewSizeA']	= 19;			// Size that the icon in step 2 of the import process gets shown at
	$config['MaxProductShots']		= 5;			// The maximum number of product shots that can be attached to any print, product, package, etc.
	$config['BillMeLaterStats']		= 1;			// Inclued unpaid 'Bill Me Later' orders in stats - 1 for yes | 0 for no
	$config['MediaIconPreviewSize'] = 100;			// Size of the preview in the media area in the manager - Max of 150	
	$config['MediaPopupPrints'] 	= 0;			// Show print details on the media popup in the management area
	$config['MediaPopupProducts'] 	= 0;			// Show product details on the media popup in the management area
	$config['MediaPopupPackages'] 	= 0;			// Show packages details on the media popup in the management area
	$config['MediaPopupCollections']= 0;			// Show collections details on the media popup in the management area
	$config['MediaPopupMediaTypes'] = 0;			// Show media types details on the media popup in the management area
	
	$config['showMemPasswords'] 	= true;			// Show member passwords in the management area - true | false
	
	//$config['MaxImportFiles']		= 200;			// Maximum number of files that can be imported at once 
	$mgrlang['alphabet']			= "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"; // Alphabet to use
	
	# DATA REMOVAL
	$config['delMemTags'] 			= true;			// Delete a member's tags when deleting the member - true | false
	$config['delActLogs'] 			= true;			// Delete a member's activity logs when deleting the member - true | false
	$config['delMemRatings'] 		= true;			// Delete a member's media ratings when deleting the member  - true | false
	$config['delMemComments'] 		= true;			// Delete a member's comments on media when deleting the member - true | false
	$config['delMemMedia'] 			= true;			// Delete a member's media when deleting the member - true | false
	$config['delMemCommission'] 	= true;			// Delete a member's commission on media when deleting the member - true | false
		
# GLOBAL TWEAKS -----------------------------------------------------------------------------------------------------------

	# BRANDING TWEAKS
	$config['BrandAdWidgets'] 		= 1; 			// Show the Ktools.net related widgets in the dashboard widget selector - 1 for on | 0 for off
	$config['BrandForum'] 			= 1;			// Show the Support Forum option under the help tab - 1 for on | 0 for off
	$config['BrandExtras'] 			= 1;			// Show the Extras & Add-ons option under the help tab - 1 for on | 0 for off
	$config['BrandFAQs'] 			= 1;			// Show the FAQs option under the help tab - 1 for on | 0 for off
	$config['BrandManual'] 			= 1;			// Show the Online Manual option under the help tab - 1 for on | 0 for off
	$config['BrandWizard'] 			= 0;			// Enable the setup wizard and option under the help tab - 1 for on | 0 for off
	$config['BrandAboutSoft'] 		= 1;			// Show the about tab and information under Settings > Software Setup - 1 for on | 0 for off
	$config['BrandUpgrades'] 		= 0;			// Enable software upgrades - 1 for on | 0 for off
	
	$config['CheckMembersOnline'] 	= 6;			// Minutes to wait before checking what members are online again - higher numbers reduce load
	
	$config['ShowZeroCounts'] 		= 0;			// Show the count after a category name even if it contains no media - 1 for on | 0 for off
	
	$config['RatingStars'] 			= 5;			// Number of rating stars visible - 5 or 10 are the only values to use
	$config['RatingStarsRoundUp']	= 0;			// Set this to 1 to round the ratings up to the next number if they there is a decimal. Example: 3.2 would become 4. Otherwise normal rounding is done.
	
	$config['iptcTitleHandler'] 	= 'R';			// How to handle the IPTC title - M = Merge with already existing title | R = Replace already existing title
	$config['iptcCopyRightHandler'] = 'R';			// How to handle the IPTC copyright - M = Merge with already existing title | R = Replace already existing title
	$config['iptcDescHandler'] 		= 'R';			// How to handle the IPTC description - M = Merge with already existing description | R = Replace already existing description
	$config['iptcSepChar'] 			= ' : ';		// Character that will separate the title or description and IPTC title or descirption if you use Megre (M) on either of the above settings
	$config['iptcTitleField'] 		= 'headline';	// Field to read from IPTC to fill media title - headline | title
	
	$config['mysqlUTF8Settings'] 	= true;			// Use UTF8 settings when querying the db - true | false
	
	//$config['CropPercentage']		= .75;			// When cropping of thumbnails or rollovers is turned on use this percentage to crop them - This will be multiplied by the width to create the cropping height
	
	$config['cacheImages'] 			= 1; 			// Cache images 1 = on | 0 = off
	//$config['cacheImages'] 			= 0; 			// Cache images 1 = on | 0 = off
	$config['cleanupCacheImages'] 	= 1; 			// Delete cached images older than the cached image time
	$config['cacheImagesTime'] 		= 604800; 		// Time in seconds to cache images - if older create new cache - Default 604800
	$config['disableLinking']		= false;		// Attempt to prevent external sites from linking to your thumbnails
	$config['useCachedImgLinks']	= true; 		// Use links directly to cached images instead of passing them through image.php - true | false
													// Will only work if cacheImages is on
													// Will not allow blocking by IP or prevent linking or tracking of any kind
	$config['passVideoThroughPHP']	= true; 		// Pass video files through PHP to disguise the direct link
	
	$config['colorSearchMinimum']	= 0.01;			// Minimum amount of a color in a photo to be considered for color searches | Default is 0.01 (1%)
	$config['colorSearchVariance']	= 10;			// Variance in rgb values to still consider a match | Default is 10
	$config['cpDelta']				= 18;			// Delta setting for color palette detector | Default 16
	$config['cpReduceBrightness']	= true;			// Reduce brightness setting for color palette detector | Default true
	$config['cpReduceGradients']	= true;			// Reduce gradients setting for color palette detector | Default true
	$config['cpResults']			= 15;			// Number of colors for the color palette to store in the db for each photo | Default 15
	
	$config['emailNames']			= 1;			// Use real names in to and from email headers - Might have to turn this off if you are using accented characters
	
	$config['keywordsToLower']		= true;			// Convert all keywords to lowercase when they are entered - true | false
	
	$config['mediaMoveFunction']	= 'copy';		// Function to use for moving media from incoming directory to the library - copy | rename - Default is copy
	
# PUBLIC TWEAKS -----------------------------------------------------------------------------------------------------------
	
	//$config['pubuploader']		= 2;		
	
	$config['charset']				= "utf-8";
	$config['AvatarWidth']			= "100"; 		// Width of member avatar - Enter 0 for no resize
	$config['AvatarHeight']			= "75"; 		// Height of member avatar - Enter 0 for no resize
	$config['OverrideFMPerms']		= 0; 			// Override featured homepage media permissions and show anything marked as featured
	$config['EncryptIDs']			= 0; 			// Encrypt link IDs for extra security
	//$config['digitalSizeCalc']		= 'i'; 			// i = inches or c = centimeters
	$config['dpiCalc']				= 300; 			// Calculate print sizes of digital photos at what dpi
	
	$config['filenameTrim']			= 20;			// The maximum amount of characters that will display in a filename.
	
	$config['autoPlayFeaturedVid']	= true; 		// Should featured videos on the homepage auto play true | false
	$config['featuredVideoVolume']	= 0; 			// Volume on featured videos 0-100
	$config['featuredVideoOverVol']	= 50; 			// Volume set on a mouse over on featured videos 0-100
	$config['featuredVideoStretch']	= 'uniform'; 	// How to stretch the video in the player - none | exactfit | uniform | fill
	$config['forceFlashVideoPlayer']= false; 		// Force the flash video player to play all files instead of switching between Flash and HTML5 player // Added in 4.6.5
		

	$config['gallerySEOName']		= 'gallery';	// Either gallery or category - anything else must be changed in the .htaccess file
	$config['galleriesSEOName']		= 'galleries';	// Either galleries or categories - anything else must be changed in the .htaccess file
	
	$config['minSearchWordLength']	= 2;			// Minimum characters in a search word - Default 2
	$config['searchResultLimit']	= 100;			// Max number of search result pages that can be returned
	$config['requireSearchKeyword']	= false;		// Require a keyword be entered for non direct color searches - false | true
	$config['searchRelevance']		= 70;			// Minimum amount of relevance from a search keyword for it to display
	$config['exactNumericSearch']	= false;		// Makes any numeric search and exact search by default - true | false
	
	$config['dateSearchField']		= 'date_added';	// Field to search when doing a date range search - date_added | date_created
													// date_added = date added to library
													// date_created = date file was created or photo was taken
	
	$config['specMediaPageLimit']	= 20;			// Max number of newest, popular and featured media pages that can be returned

	$config['captcha']['publickey']	= '6Ldm9cUSAAAAANQ4sTG69g5Yu5Tr6xTNAkc6-k7c'; // reCaptcha Public Key - http://www.google.com/recaptcha
	$config['captcha']['privatekey']= '6Ldm9cUSAAAAAJ6hMbEUXAbM6YCw203hl97eD17r'; // reCaptcha Private Key
	
	$config['contrPasswordAlbums']	= 1;			// Allow contributors to give public albums passwords
	
	$config['useCustomerNameInEmail'] = 1;			// Use a customers name in the headers when formatting the email
	
	$config['useCookies']			= true;			// Use cookies to remember lightbox and cart data
	
?>
