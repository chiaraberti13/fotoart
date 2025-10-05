<?php
	/*
	* Class and functions for working with media
	*/
	class mediaTools
	{
		private $configArray;
		private $mediaID;
		private $dbconn;
		private $db_pre;
		private $mediaInfo;
		private $iconInfo;
		private $thumbInfo;
		private $sampleInfo;
		private $dspInfo;
		private $folderInfo;
		private $folderID;
		private $storageInfo;
		private $localpath;
		
		/*
		* Construct and get the media info
		*/
		public function __construct($mediaID)
		{
			global $config, $db, $dbinfo;
			$this->configArray = $config;
			$this->mediaID = $mediaID;
			$this->dbconn = $db;
			$this->db_pre = $dbinfo['pre'];

			if(!$this->mediaID)
				throw new Exception('No media ID was passed');	
		}
		
		/*
		* Get the details of the folder and the storage location
		*/
		public function getFolderStorageInfoFromDB($folderID)
		{
			global $db;
			
			if(!$folderID)
			{
				throw new Exception('getFolderStorageInfoFromDB : No folderID passed.');
				return false;	
			}
			
			// If folderInfo already exists then skip this
			if(!$this->folderInfo)
			{
				$folderResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}folders WHERE folder_id = '{$folderID}'");
				$this->folderRows = mysqli_num_rows($folderResult);
				$this->folderInfo = mysqli_fetch_assoc($folderResult);
			}
		
			if($this->folderRows)
			{
				# Check if storage is local or remote
				if($this->folderInfo['storage_id'] == 0)
				{
					$this->storageInfo['storage_type'] = 'locallib';
					$this->folderInfo['storageInfo']['storage_type'] = $this->storageInfo['storage_type']; // Put the storageInfo into the folderInfo array so it can be used elsewhere through the folderInfo array
				}
				else
				{
					$storageResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}storage WHERE storage_id = '{$this->folderInfo[storage_id]}'");
					$this->storageInfo = mysqli_fetch_assoc($storageResult);
					$this->folderInfo['storageInfo'] = $this->storageInfo; // Put the storageInfo into the folderInfo array so it can be used elsewhere through the folderInfo array
				}
				
				return $this->folderInfo;
			}
			else
			{
				throw new Exception('getFolderStorageInfoFromDB : folderID not found in the DB.');
				return false;
			}
		}
		
		/*
		* Get the folder details in an array
		*/
		public function getFolderInfoFromDB($folderID)
		{
			global $db;
			
			if(!$folderID)
			{
				throw new Exception('getFolderInfoFromDB : No folderID passed.');
				return false;	
			}
			
			$folderResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}folders WHERE folder_id = '{$folderID}'");
			$this->folderRows = mysqli_num_rows($folderResult);
			$this->folderInfo = mysqli_fetch_assoc($folderResult);
		
			if($this->folderRows)
			{	
				return $this->folderInfo;
			}
			else
			{
				throw new Exception('getFolderInfoFromDB : folderID not found in the DB.');
				return false;
			}
		}
		
		/*
		* Get the folder name
		*/
		public function getFolderName()
		{
			if(!$this->folderInfo)
				throw new Exception('getFolderName : folderInfo doesnt exists');
			else
				return ($this->folderInfo['encrypted']) ? $this->folderInfo['enc_name']: $this->folderInfo['name'];
		}
		
		/*
		* Verify that a media file exists
		*/
		public function verifyMediaFileExists()
		{
			// Get media info if it doesn't already exists
			if(!$this->mediaInfo)
			{
				$this->getMediaInfoFromDB(); // No media info exists - Grab info
				//echo "error"; exit;
			}
			
			// Get folder info if it doesn't already exits
			if(!$this->folderInfo)
			{	
				$this->getFolderStorageInfoFromDB($this->mediaInfo['folder_id']);
			}
			
			// Backup check for folder info - if none then throw exception
			if(!$this->folderInfo)
			{
				throw new Exception('verifyMediaFileExists : No folderInfo - Call getFolderStorageInfoFromDB first');
				return false;
			}
			
			$folder_name = ($this->folderInfo['encrypted']) ? $this->folderInfo['enc_name']: $this->folderInfo['name'];
			$verify['filename'] = $this->mediaInfo['filename'];

			if($this->folderInfo['storage_id'] == 0)
			{
				$directory = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR;
				$verify['status'] = (file_exists($directory . $this->mediaInfo['filename'])) ? 1: 0;
				$verify['path'] = $directory;
			}
			else
			{
				$directory = 'External xxxxxxx' . DIRECTORY_SEPARATOR;
				$verify['status'] = 0;
				$verify['path'] = $directory;
			}
			
			return $verify;
		}
		
		/*
		* Verify that a digital profile file exists
		*/
		public function verifyMediaDPFileExists($dspID)
		{	
			// Check for dspID - if none then throw exception
			if(!$dspID)
			{
				throw new Exception('verifyMediaDPFileExists : No dspID Passed');
				return false;
			}
			
			// Get media info if it doesn't already exists
			if(!$this->mediaInfo)
			{
				$this->getMediaInfoFromDB(); // No media info exists - Grab info
			}
			
			// Get folder info if it doesn't already exits
			if(!$this->folderInfo)
			{
				$this->getFolderStorageInfoFromDB($this->mediaInfo['folder_id']);
			}
			
			// Backup check for folder info - if none then throw exception
			if(!$this->folderInfo)
			{
				throw new Exception('verifyMediaDPFileExists : No folderInfo - Call getFolderStorageInfoFromDB first');
				return false;
			}
			
			if(!$this->dspInfo)
			{
				$this->getDSPInfoFromDB();
				$filename = ($this->dspInfo[$dspID]['filename']) ? $this->dspInfo[$dspID]['filename'] : 'noFile'; // Added no file so that it doesn't check the dir and actually think the file itself exists
			}
			else
			{
				$filename = ($this->dspInfo[$dspID]['filename']) ? $this->dspInfo[$dspID]['filename'] : 'noFile';
			}
			
			
			$type = "variations";
			
			$folder_name = ($this->folderInfo['encrypted']) ? $this->folderInfo['enc_name']: $this->folderInfo['name'];
			$verify['filename'] = $filename;

			$directory = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
			$verify['status'] = (file_exists($directory . $filename)) ? 1: 0;
			$verify['path'] = $directory;

			return $verify;			
		}
		
		/*
		* Verify that a sample video file exists
		*/
		public function verifyVidSampleExists()
		{
			global $db;
			
			// Get media info if it doesn't already exists
			if(!$this->mediaInfo)
			{
				$this->getMediaInfoFromDB(); // No media info exists - Grab info
			}
			
			// Get folder info if it doesn't already exits
			if(!$this->folderInfo)
			{
				$this->getFolderStorageInfoFromDB($this->mediaInfo['folder_id']);
			}
			
			// Backup check for folder info - if none then throw exception
			if(!$this->folderInfo)
			{
				throw new Exception('verifyMediaFileExists : No folderInfo - Call getFolderStorageInfoFromDB first');
				return false;
			}
			
			if(!$this->vidSampleInfo)
			{
				$this->getVidSampleInfoFromDB();
				$filename = $this->vidSampleInfo['vidsample_filename'];
			}
			else
			{
				$filename = $this->vidSampleInfo['vidsample_filename'];
			}
			
			$type = "samples";
			
			$folder_name = ($this->folderInfo['encrypted']) ? $this->folderInfo['enc_name']: $this->folderInfo['name'];
			$verify['filename'] = $filename;

			$directory = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
			$verify['status'] = (file_exists($directory . $filename)) ? 1: 0;
			$verify['path'] = $directory;
			
			if(file_exists('./assets/library/'.$folder_name.'/samples/'.$filename)) // Only valid if using the default library path
				$verify['url'] = $this->configArray['settings']['site_url'].'/assets/library/'.$folder_name.'/samples/'.$filename; 

			return $verify;
		}
		
		/*
		* Verify that a media file exists
		*/
		public function verifyMediaSubFileExists($type='icons')
		{
			global $db;
			
			// Get media info if it doesn't already exists
			if(!$this->mediaInfo)
			{
				$this->getMediaInfoFromDB(); // No media info exists - Grab info
			}
			
			// Get folder info if it doesn't already exits
			if(!$this->folderInfo)
			{
				$this->getFolderStorageInfoFromDB($this->mediaInfo['folder_id']);
			}
			
			// Backup check for folder info - if none then throw exception
			if(!$this->folderInfo)
			{
				throw new Exception('verifyMediaFileExists : No folderInfo - Call getFolderStorageInfoFromDB first');
				return false;
			}
			
			// Determine the type to look for
			switch($type)
			{
				case "icons":
					if(!$this->iconInfo)
					{
						$this->getIconInfoFromDB();
						$filename = $this->iconInfo['thumb_filename'];
					}
					else
					{
						$filename = $this->iconInfo['thumb_filename'];
					}
				break;
				case "thumbs":
					if(!$this->thumbInfo)
					{
						$this->getThumbInfoFromDB();
						$filename = $this->thumbInfo['thumb_filename'];
					}
					else
					{
						$filename = $this->thumbInfo['thumb_filename'];
					}
				break;
				case "samples":
					if(!$this->sampleInfo){
						$this->getSampleInfoFromDB();
						$filename = $this->sampleInfo['sample_filename'];
					}
					else
					{
						$filename = $this->sampleInfo['sample_filename'];
					}
				break;
			}
			
			$folder_name = ($this->folderInfo['encrypted']) ? $this->folderInfo['enc_name']: $this->folderInfo['name'];
			$verify['filename'] = $filename;

			$directory = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
			$verify['status'] = (file_exists($directory . $filename)) ? 1: 0;
			$verify['path'] = $directory;

			return $verify;
		}
		
		/*
		* Get the info about a media file from the database
		*/
		public function getMediaInfoFromDB($mediaID=NULL,$passArray=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			if($passArray) // Used to pass a media array already created
			{
				$this->mediaInfo = $passArray;
				$this->mediaRows = 1;
				return 	$this->mediaInfo;
			}
			else
			{
				$mediaResult = mysqli_query($db,
				"
					SELECT * FROM {$this->db_pre}media 
					LEFT JOIN {$this->db_pre}licenses 
					ON {$this->db_pre}media.license = {$this->db_pre}licenses.license_id 
					WHERE media_id = '{$this->mediaID}'
				");
				$this->mediaRows = mysqli_num_rows($mediaResult);
				$this->mediaInfo = mysqli_fetch_assoc($mediaResult);
				
				if($this->mediaInfo['license'] != 'nfs') // Find the correct license code
					$this->mediaInfo['license'] = $this->mediaInfo['lic_purchase_type'];
				
				if(!$this->mediaRows)
				{
					throw new Exception('getMediaInfoFromDB : This media ID could not be found in the DB.');
					return false;
				}
				else
				{
					return 	$this->mediaInfo;
				}
			}
		}
		
		/*
		* Get the galleries media exists in and return them as an array
		*/
		public function getMediaGalleries()
		{
			global $db;
			
			if(!$this->mediaID)
			{
				throw new Exception('getMediaGalleries : No media ID passed');
				return false;
			}
			else
			{
				$galleriesResult = mysqli_query($db,
				"
					SELECT gallery_id FROM {$this->db_pre}media_galleries 
					WHERE gmedia_id = '{$this->mediaID}'
				");
				$galleryRows = mysqli_num_rows($galleriesResult);
				while($gallery = mysqli_fetch_assoc($galleriesResult))
					$galleries[] = $gallery['gallery_id'];
				
				return $galleries;
			}
		}
		
		
		/*
		* Get the details of the icon for the media file
		*/
		public function getIconInfoFromDB($mediaID=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			$iconResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}media_thumbnails WHERE media_id = '{$this->mediaID}' AND thumbtype = 'icon'");
			$this->iconRows = mysqli_num_rows($iconResult);
			$this->iconInfo = mysqli_fetch_assoc($iconResult);
			
			if($this->iconRows)
			{
				return $this->iconInfo;
			}
			else
			{
				//throw new Exception('getIconInfoFromDB: Icon info could not be found');
				return false;
			}
		}
		
		/*
		* Get the details of the thumbnail for the media file
		*/
		public function getThumbInfoFromDB($mediaID=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			$thumbResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}media_thumbnails WHERE media_id = '{$this->mediaID}' AND thumbtype = 'thumb'");
			$this->thumbRows = mysqli_num_rows($thumbResult);
			$this->thumbInfo = mysqli_fetch_assoc($thumbResult);
			
			if($this->thumbRows)
			{
				//echo "test"; exit;
				return $this->thumbInfo;
			}
			else
			{
				//echo "test2"; exit;
				return false;
			}
		}
		
		/*
		* Get the details of the additional sizes and variations of this media
		*/
		public function getDSPInfoFromDB($mediaID=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			$dspResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}media_digital_sizes WHERE media_id = '{$this->mediaID}'");
			$this->dspRows = mysqli_num_rows($dspResult);
			
			while($dspInfoTemp = mysqli_fetch_assoc($dspResult))
			{
				$this->dspInfo[$dspInfoTemp['ds_id']] = $dspInfoTemp;
			}
			
			if($this->dspInfo)
			{
				return $this->dspInfo;
			}
			else
			{
				return false;
			}
		}
		
		/*
		* Get the details of the video sample for the media file
		*/
		public function getVidSampleInfoFromDB($mediaID=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			$vidSampleResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}media_vidsamples WHERE media_id = '{$this->mediaID}' AND vidsampletype = 'sample'");
			$this->vidSampleRows = mysqli_num_rows($vidSampleResult);
			$this->vidSampleInfo = mysqli_fetch_assoc($vidSampleResult);
			
			if($this->vidSampleRows)
			{
				return $this->vidSampleInfo;
			}
			else
			{
				return false;
			}
		}
		
		/*
		* Get the details of the sample for the media file
		*/
		public function getSampleInfoFromDB($mediaID=NULL)
		{
			global $db;
			
			if($mediaID) $this->mediaID = $mediaID;
			
			$sampleResult = mysqli_query($db,"SELECT * FROM {$this->db_pre}media_samples WHERE media_id = '{$this->mediaID}'");
			$this->sampleRows = mysqli_num_rows($sampleResult);
			$this->sampleInfo = mysqli_fetch_assoc($sampleResult);
			
			if($this->sampleRows)
			{
				return $this->sampleInfo;
			}
			else
			{
				//throw new Exception('getSampleInfoFromDB: Sample info could not be found');
				return false;
			}
		}
		
		/*
		* Delete media from the database and from the storage location
		*/
		public function deleteMedia($mediaID='')
		{
			if($mediaID) $this->mediaID = $mediaID;
			
			if(!$this->mediaID)
				throw new Exception('deleteMedia: No media ID was passed to the delete function');
	
			$this->getMediaInfoFromDB();
	
			$this->deleteLocalAssets();
			
			$this->deleteMediaFromDB();
		}
		
		/*
		* Delete all the database entries related to this media file
		*/
		public function deleteMediaFromDB()
		{
			global $db;
			
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_galleries WHERE gmedia_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_collections WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_types_ref WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_packages WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_products WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_prints WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_digital_sizes WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_iptc WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_exif WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}keywords WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_thumbnails WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_samples WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_ratings WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_comments WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_tags WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_vidsamples WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_digital_sizes WHERE media_id = '{$this->mediaID}'");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}color_palettes WHERE media_id = '{$this->mediaID}'");
			return true;
		}
		
		/*
		* Testing function for clearing all media tables - Delete when finished
		*/
		public function clearMediaTablesxxxxxx()
		{	
			global $db;
			
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_galleries WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_collections WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_types_ref WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_packages WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_products WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_prints WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_digital_sizes media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_iptc WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_exif WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}keywords WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_thumbnails WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media_samples WHERE media_id > 0");
			@mysqli_query($db,"DELETE FROM {$this->db_pre}media WHERE media_id > 0");					
			return true;
		}
		
		private function setLocalFolderPath()
		{
			if(!$this->folderInfo)
			{
				throw new Exception('setLocalFolderPath : Must get folder info first');
			}			
			if($this->folderInfo['encrypted'])
				$this->path = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $this->folderInfo['enc_name'];
			else
				$this->path = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $this->folderInfo['name'];
		}
		
		/*
		* Delete local instances of the media file
		*/
		private function deleteLocalAssets()
		{
			$this->getIconInfoFromDB();
			$this->getThumbInfoFromDB();
			$this->getSampleInfoFromDB();
			$this->getVidSampleInfoFromDB();
			$this->getDSPInfoFromDB();
			
			$this->getFolderStorageInfoFromDB($this->mediaInfo['folder_id']);
			$this->setLocalFolderPath();
			
			if($this->folderInfo['encrypted'])
			{
				//$this->path = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $this->folderInfo['enc_name'];
				$this->folderInfo['useName'] = $this->folderInfo['enc_name'];
			}
			else
			{
				//$this->path = $this->configArray['settings']['library_path'] . DIRECTORY_SEPARATOR . $this->folderInfo['name'];
				$this->folderInfo['useName'] = $this->folderInfo['name'];
			}
			
			// Delete dsp files
			if(@$this->dspInfo)
			{
				foreach($this->dspInfo as $dsp)
				{
					$dspPath = $this->path . DIRECTORY_SEPARATOR . "variations" . DIRECTORY_SEPARATOR . $dsp['filename'];
					if(file_exists($dspPath))
						@unlink($dspPath);
				}
			}
			
			$iconPath = $this->path . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR . $this->iconInfo['thumb_filename'];
			$thumbPath = $this->path . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . $this->thumbInfo['thumb_filename'];
			$samplePath = $this->path . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR . $this->sampleInfo['sample_filename'];
			$vidSamplePath = $this->path . DIRECTORY_SEPARATOR . "samples" . DIRECTORY_SEPARATOR . $this->vidSampleInfo['vidsample_filename'];
			
			//throw new Exception('test: ' . $iconPath);
			
			# Delete icon, thumb and sample
			if(file_exists($iconPath) and $this->iconInfo['thumb_filename'])
				@unlink($iconPath);	
	
			if(file_exists($thumbPath) and $this->thumbInfo['thumb_filename'])
				@unlink($thumbPath);	
			
			if(file_exists($samplePath) and $this->sampleInfo['sample_filename'])
				@unlink($samplePath);
				
			if(file_exists($vidSamplePath) and $this->vidSampleInfo['vidsample_filename'])
				@unlink($vidSamplePath);
			
			// Delete model release form	
			if($this->mediaInfo['model_release_form'] and file_exists("../assets/files/releases/".$this->mediaInfo['model_release_form']))
				@unlink("../assets/files/releases/".$this->mediaInfo['model_release_form']);
			
			// Delete property release form
			if($this->mediaInfo['prop_release_form'] and file_exists("../assets/files/releases/".$this->mediaInfo['prop_release_form']))
				@unlink("../assets/files/releases/".$this->mediaInfo['prop_release_form']);
				
			$this->deleteOriginals();
			
			return true;				
		}
		
		/*
		* Delete original file
		*/
		private function deleteOriginals()
		{
			switch($this->storageInfo['storage_type'])
			{
				case 'locallib':
					$originalPath = $this->path . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $this->mediaInfo['filename'];
					
					if(file_exists($originalPath))
						unlink($originalPath);
				break;
				case 'local':
					$originalPath = k_decrypt($this->storageInfo['path']) . DIRECTORY_SEPARATOR . $this->folderInfo['useName'] .  DIRECTORY_SEPARATOR . $this->mediaInfo['filename'];
					
					if(file_exists($originalPath))
						unlink($originalPath);
				break;
				case 'ftp':
					require_once($this->configArray['base_path'] . 'assets/classes/ftp.php');	
					
					$ftp = new ftp_connection(stripslashes(k_decrypt($this->storageInfo['host'])),stripslashes(k_decrypt($this->storageInfo['username'])),stripslashes(k_decrypt($this->storageInfo['password'])),$this->storageInfo['port']);
					
					$filename = stripslashes(k_decrypt($this->storageInfo['path'])) . "/" . $this->mediaInfo['filename'];
					
					$ftp->delete_file($filename);					
					
					$ftp->close_conn();
					
				break;
				case 'amazon_s3':
					require_once($this->configArray['base_path'] . 'assets/classes/amazonS3/as3.php');

					# Define the connection details
					if(!defined('awsAccessKey')) define('awsAccessKey', stripslashes(k_decrypt($this->storageInfo['username'])));
					if(!defined('awsSecretKey')) define('awsSecretKey', stripslashes(k_decrypt($this->storageInfo['password'])));
					
					# Initiate the S3 class
					$s3 = new S3(awsAccessKey, awsSecretKey);					
					S3::$useSSL = false;

					# Delete file from S3
					$s3->deleteObject($this->folderInfo['enc_name'],$this->mediaInfo['filename']);
					
				break;
				case 'cloudfiles':
					require_once($this->configArray['base_path'] . 'assets/classes/rackspace/cloudfiles.php');
									
					$username = stripslashes(k_decrypt($this->storageInfo['username']));
					$api_key = stripslashes(k_decrypt($this->storageInfo['password']));
					
					$auth = new CF_Authentication($username, $api_key);
					$auth->authenticate();
					
					if($auth->authenticated())
					{
						$conn = new CF_Connection($auth);
						$container = $conn->get_container($this->folderInfo['useName']);
						$container->delete_object($this->mediaInfo['filename']);
					}
					$conn->close();
				break;
			}
		}
	}
?>