<?php
	# BUILD GALLERY DIRECTORIES
	class folder_builder
	{
		public $error = false;
		
		# INITIALLIZE GALLERY BUILDER
		public function __construct($foldername=NULL,$encrypt=0,$storage_id=NULL)
		{			
			global $config,$create_gal_folders;			
			
			$this->library_path = $config['settings']['library_path'] . DIRECTORY_SEPARATOR;
			$this->originalfoldername  = $foldername;
			$this->encrypt = $encrypt;
			$this->create_gal_folders = $create_gal_folders;
			$this->storage_name;
			$this->encrypt_name = md5($foldername . $config['settings']['serial_number']);
			
			# USE ENCRYPTED FILENAMES
			if($encrypt){
				$this->newfoldername = $this->encrypt_name;
			}
			else
			{
				$this->newfoldername = $foldername;
			}
			//$this->is_public() ? "Yes" : "No",
		}
		
		# CREATE LOCAL DIRECTORIES
		public function create_local_directories()
		{
			global $config;
			//global $db, $db_pre, $config, $mgrlang, $create_gal_folders;
			
			# MAKE SURE THE LIBRARY PATH IS WRITABLE
			if(is_writable($this->library_path))
			{
				# CREATE DIRECTORIES - WHILE CHECKING FOR ERRORS					
				if(!file_exists($this->library_path . $this->newfoldername))
				{
					if(mkdir($this->library_path . $this->newfoldername,$config['SetFilePermissions']))
					{ 
						chmod($this->library_path . $this->newfoldername,$config['SetFilePermissions']);
						
						# ADD INDEX.HTML PAGE TO AVOID DIRECORY VIEWING
						@copy($config['base_path'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'index.html',$this->library_path . $this->newfoldername . DIRECTORY_SEPARATOR . 'index.html');
					}
				}
				# CREATE SUB DIRECTORIES - WHILE CHECKING FOR ERRORS
				foreach($this->create_gal_folders as $value)
				{
					if(!file_exists($this->library_path . $this->newfoldername . DIRECTORY_SEPARATOR . "$value"))
					{
						if(mkdir($this->library_path . $this->newfoldername . DIRECTORY_SEPARATOR . "$value",$config['SetFilePermissions']))
						{
							chmod($this->library_path . $this->newfoldername . DIRECTORY_SEPARATOR . "$value",$config['SetFilePermissions']);
							
							# ADD INDEX.HTML PAGE TO AVOID DIRECORY VIEWING
							@copy($config['base_path'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'index.html',$this->library_path . $this->newfoldername . DIRECTORY_SEPARATOR . "$value" . DIRECTORY_SEPARATOR . 'index.html');
						}
					}
				}
				return true;
			}
			else
			{
				# NOT WRITABLE - OUTPUT A MESSAGE
				$this->error = 'Not Writable';
				return false;
			}
		}
		
		# CREATE A CLOUD STORAGE NAME
		public function create_storage_name()
		{
			global $config;
			//return $creator->storage_name = $config['product_code'] . "-" . $this->encrypt_name;
			return $creator->storage_name = $this->encrypt_name;
		}
		
		# CHECK FOR ERRORS
		public function check_errors()
		{
			if($this->error)
				return true;
			else
				return false;
		}
		
		# RETURN ERRORS
		public function return_errors()
		{
			if($this->check_errors())
				echo $this->error;
				
			exit;
		}
		
		
		# MANAGE STORAGE DIRECTORIES
		public function storage_directory($storage_id,$mode='create',$oldname=NULL)
		{
			global $config, $db, $dbinfo;

			# MAKE SURE A STORAGE_ID WAS PASSED
			if($storage_id)
			{
				# GRAB THE STORAGE INFORMATION FROM THE DB
				$storage_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage WHERE storage_id = '$storage_id'");
				$storage_rows = mysqli_num_rows($storage_result);
				$storage = mysqli_fetch_object($storage_result);	
				
				# ONLY IF WE GET SOMETHING FROM THE DB
				if($storage_rows)
				{
					switch($storage->storage_type)
					{
						case "ftp":
							# REQUIRE THE FTP CLASS FILE
							require_once($config['base_path'] . '/assets/classes/ftp.php');	
							
							# FTP CONNECTION
							$ftp = new ftp_connection(stripslashes(k_decrypt($storage->host)),stripslashes(k_decrypt($storage->username)),stripslashes(k_decrypt($storage->password)),$storage->port);
							
							$cleanpath = stripslashes(k_decrypt($storage->path)) . "/";
							
							switch($mode)
							{
								case "create":
									# CREATE FOLDER
									$ftp->create_dir($cleanpath . $this->newfoldername);
								break;								
								case "rename":	
									# RENAME FOLDER
									$ftp->rename_dir($cleanpath . ($this->encrypt ? md5($oldname) : $oldname),$cleanpath . $this->newfoldername);
								break;
								case "remove":	
									foreach($ftp->list_files($cleanpath . $this->newfoldername) as $value)
									{
										$ftp->delete_file($cleanpath . $this->newfoldername . "/" . $value);
									}
								
									# REMOVE FOLDER
									$ftp->remove_dir($cleanpath . $this->newfoldername);
								break;
								case "verify":	
									# VERIFY FOLDER
									
								break;
							}
							
							# CLOSE FTP CONNECTION
							$ftp->close_conn();
							
							# PASS ANY FTP ERRORS TO LOCAL CLASS
							$this->error = $ftp->ftp_errors();
						break;
						case "local":
							switch($mode)
							{
								case "create":
									# DECRYPT THE PATH
									$path = stripslashes(k_decrypt($storage->path));							
									
									# MAKE THE STORAGE DIRECTORY
									if(mkdir($path . DIRECTORY_SEPARATOR . $this->newfoldername . DIRECTORY_SEPARATOR . "$value",$config['SetFilePermissions']))
									{
										chmod($path . $this->newfoldername . DIRECTORY_SEPARATOR . "$value",$config['SetFilePermissions']);
									}
									else
									{	
										# COULD NOT CREAETE REMOTE FOLDER
										$this->error  = 'Could Not Create Remote Directory. You should delete this folder.';										
									}
								break;
								case "rename":							
									# DECRYPT THE PATH
									$path = stripslashes(k_decrypt($storage->path));
									
									# RENAME THE STORAGE DIRECTORY
									if(!rename($path . DIRECTORY_SEPARATOR . ($this->encrypt ? md5($oldname) : $oldname),$path . DIRECTORY_SEPARATOR . $this->newfoldername))
										# FAILED TO RENAME REMOTE FOLDER
										$this->error  = 'Could Not Rename Remote Folder';
								break;
								case "remove":
									# DECRYPT THE PATH
									$path = stripslashes(k_decrypt($storage->path));
									
									# REMOVE THE STORAGE DIRECTORY
									if(!rmdir($path . DIRECTORY_SEPARATOR . $this->newfoldername))
										# FAILED TO REMOVE REMOTE FOLDER
										$this->error  = 'Could Not Delete Remote Directory';
								break;
							}
						break;
						case "amazon_s3":
							# INCLUDE AMAZON CLASS FILE
							require_once($config['base_path'] . 'assets/classes/amazonS3/as3.php');
							
							# DEFINE THE CONNECTION KEYS
							if(!defined('awsAccessKey')) define('awsAccessKey', stripslashes(k_decrypt($storage->username)));
							if(!defined('awsSecretKey')) define('awsSecretKey', stripslashes(k_decrypt($storage->password)));
							
							# INITIATE THE CLASS
							$s3 = new S3(awsAccessKey, awsSecretKey);
							
							# TURN OFF SSL
							S3::$useSSL = false;
							
							switch($mode)
							{
								case "create":
									# CREATE BUCKET
									if(!$s3->putBucket($this->create_storage_name(), S3::ACL_PUBLIC_READ))
									{
										$this->error = 'AS3: Create Bucket Failed';
									}
								break;								
								case "rename":	
									# RENAME FOLDER
									
									
									// Simple copy:    if (S3::copyObject($sourceBucket, $sourceFile, $destinationBucket, $destinationFile, S3::ACL_PRIVATE)) {        echo "Copied file";    } else {        echo "Failed to copy file";    }
									
								break;
								case "remove":	
									# FIRST REMOVE CONTENTS OF BUCKET
									foreach($s3->getBucket($this->create_storage_name()) as $value)
									{
										$s3->deleteObject($this->create_storage_name(),$value['name']);
									}
									
									# REMOVE BUCKET
									if(!$s3->deleteBucket($this->create_storage_name()))
									{
										$this->error = 'AS3: Delete Bucket Failed';
									}
								break;
							}
						break;
						case "cloudfiles":
							require_once($config['base_path'] . 'assets/classes/rackspace/cloudfiles.php');
					
							//$username = 'ktools';
							//$api_key = '59612b31de4bd1feaa6c84bdac892f5a'; xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
							
							$username = stripslashes(k_decrypt($storage->username));
							$api_key = stripslashes(k_decrypt($storage->password));
							
							try
							{
								$auth = new CF_Authentication($username, $api_key);
								# $auth->ssl_use_cabundle();  # bypass cURL's old CA bundle

								$auth->authenticate();
							}
							catch (Exception $e)
							{
								$this->error =  $e->getMessage();
							}
							
							if($auth->authenticated())
							{
								try
								{
									$conn = new CF_Connection($auth);
								}
								catch (Exception $e)
								{
									$this->error =  $e->getMessage();
								}
								
								switch($mode)
								{
									case "create":
										# CREATE BUCKET
										try
										{
											$conn->create_container($this->newfoldername);
										}
										catch (Exception $e)
										{
											$this->error =  $e->getMessage();
										}
									break;								
									case "rename":	
										# RENAME
										
									break;
									case "remove":	
										# REMOVE BUCKET
										try
										{
											$container = $conn->get_container($this->newfoldername);
											//print_r($container->list_objects());
											
											foreach($container->list_objects() as $value)
											{
												$container->delete_object($value);
											}
											
											$conn->delete_container($this->newfoldername);
										}
										catch (Exception $e)
										{
											$this->error =  $e->getMessage();
										}
									break;
								}
								$conn->close();
							}
							else
							{
								$this->error = 'CloudFiles: Authentication Failed';	
							}
						break;
					}
				}
				if($this->check_errors())
					return false;
				else
					return true;
			}
			else
			{
				$this->error = 'No Storage ID Passed';
				return false;
			}
		}
		
		# RENAME LOCAL DIRECTORY
		public function rename_local($oldname)
		{
			# RENAME LOCAL FOLDER
			if(rename($this->library_path . ($this->encrypt ? md5($oldname) : $oldname),$this->library_path . $this->newfoldername))
			{
				return true;
			}
			else
			{
				$this->error = 'Unable To Rename Local Folder';
				return false;
			}
		}
		
		# REMOVE LOCAL DIRECTORY
		public function remove_local()
		{
			clean_directory($this->library_path . $this->newfoldername);
			if(!rmdir($this->library_path . $this->newfoldername));
				# FAILED TO REMOVE FOLDER
				$this->error  = 'Could Not Delete Remote Directory';
		}
		
		# CHECK TO MAKE SURE THE DIRECTORY DOESN'T ALREADY EXIST
		public function check_for_dup($checkname='')
		{
			global $db, $dbinfo, $config;
			
			if(!$checkname)
				$checkname = $this->originalfoldername;

			$folders_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}folders WHERE name = '$checkname'");
			$folders_rows = mysqli_num_rows($folders_result);
			
			if($folders_rows)
			{
				$this->error = 'Directory Already Exists';
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>