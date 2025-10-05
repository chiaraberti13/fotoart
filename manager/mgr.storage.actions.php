<?php
	###################################################################
	####	STORAGE COUNTRIES ACTIONS                              ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-2-2010                                      ####
	####	Modified: 2-2-2010                                     #### 
	###################################################################

		# INCLUDE THE SESSION START FILE
		require_once('../assets/includes/session.php');
	
		$page = "storage";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MGR FUNCTIONS
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE	
		include_lang();
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SET ACTIVE STATUS
			case "ac":
				$storage_result = mysqli_query($db,"SELECT active FROM {$dbinfo[pre]}storage where storage_id = '$_REQUEST[id]'");
				$storage = mysqli_fetch_object($storage_result);
				
				# FLIP THE VALUE
				$new_value = (empty($storage->active) ? 1 : 0);	
				
				# UPDATE THE DATABASE WITH A NEW VALUE
				$sql = "UPDATE {$dbinfo[pre]}storage SET active='$new_value' where storage_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);

				echo "<a href=\"javascript:switch_status('ac','$_REQUEST[id]');\"><img src=\"images/mgr.small.check." . $new_value . ".png\" border=\"0\" /></a>";
			break;
			# CHECK FTP
			case "check_ftp":
				$ftp_status = get_ftp_status($_GET['ftp_host'], $_GET['ftp_port'], $_GET['ftp_username'], $_GET['ftp_password'], 5, $_GET['ftp_path']);
			?>
            	<script>
					//alert('online');
					<?php
						switch($ftp_status)
						{
							case "1":
								if($_GET['dosubmit'] == '1')
								{
									echo "$('storage_type').enable();";
									echo "\$('data_form').submit();";
								}
								else
								{
									echo "simple_message_box('$mgrlang[storage_mes_ftp1]','');";
								}
							break;
							case "2":
								echo "simple_message_box('$mgrlang[storage_mes_ftp2]','');";
								echo "$('ftp_host_div').className='fs_row_error';";
								echo "$('ftp_port_div').className='fs_row_error';";
							break;
							case "3":
								echo "simple_message_box('$mgrlang[storage_mes_ftp3]','');";
								echo "$('ftp_username_div').className='fs_row_error';";
								echo "$('ftp_password_div').className='fs_row_error';";
							break;
							case "4":
								echo "simple_message_box('$mgrlang[storage_mes_ftp4]','');";
								echo "$('ftp_path_div').className='fs_row_error';";
							break;
							default:
								echo "simple_message_box('$mgrlang[storage_mes_ftp5]','');";
							break;
						}
					?>
					$('ftp_test_button').setValue('<?php echo $mgrlang['storage_b_test']; ?>');
					$('ftp_test_button').enable();
				</script>
			<?php

			break;
			# CHECK LOCAL
			case "check_local":
				
				$path = $_GET['local_path'];
				@$realpath = realpath($_GET['local_path']);
				$realpaths = addslashes($realpath);
				
				echo "<script>";				
				
				# CHECK FOR RELATIVE PATH
				if(strpos($path,'./') or strpos($path,'.\\'))
				{
					echo "simple_message_box('$mgrlang[storage_mes_local1]','');";
					echo "$('local_path_div').className='fs_row_error';";
				}
				else
				{
					# CHECK TO MAKE SURE IT IS A DIRECTORY
					if(is_dir($realpath))
					{
						# CHECK TO MAKE SURE IT IS WRITABLE
						if(is_writable($realpath))
						{
							echo "\$('local_path').setValue('$realpaths');";
							if($_GET['dosubmit'] == '1')
							{						
								echo "$('storage_type').enable();";
								echo "\$('data_form').submit();";
							}
							else
							{
								echo "simple_message_box('$mgrlang[storage_mes_local2]','');";
								//($realpaths)
							}
						}
						else
						{
							echo "simple_message_box('$mgrlang[storage_mes_local3]','');";
							echo "$('local_path_div').className='fs_row_error';";
						}
					}
					# DIRECTORY DOESN'T EXIST
					else
					{
						echo "simple_message_box('$mgrlang[storage_mes_local4]','');";
						echo "$('local_path_div').className='fs_row_error';";
					}
				}
				echo "
					$('local_test_button').setValue('$mgrlang[storage_b_test]');
					$('local_test_button').enable();
					</script>";
			break;
			# CHECK AMAZON S3
			case "check_as3":
				echo "<script>";
				// http://undesigned.org.za/2007/10/22/amazon-s3-php-class
				
				if(!class_exists('S3')) require_once '../assets/classes/amazonS3/as3.php';

				if(!defined('awsAccessKey')) define('awsAccessKey', $_GET['as3_username']);
				if(!defined('awsSecretKey')) define('awsSecretKey', $_GET['as3_password']);
				
				# INITIATE THE CLASS
				$s3 = new S3(awsAccessKey, awsSecretKey);
				
				S3::$useSSL = false;
				
				//echo "S3::listBuckets(): ".print_r($s3->listBuckets(), 1)."\n";
				
				# TEST CREATING A BUCKET
				//$bucketName = 'ps' . create_unique2(); // TEMP BUCKET NAME
				//if($s3->putBucket($bucketName, S3::ACL_PUBLIC_READ))
				if($s3->listBuckets())
				{
					# DELETE THE BUCKET WE JUST CREATED
					//$s3->deleteBucket($bucketName);
					
					# SEE IF THE FORM SHOULD BE SUBMITTED
					if($_GET['dosubmit'] == '1')
					{						
						echo "$('storage_type').enable();";
						echo "\$('data_form').submit();";
					}
					else
					{
						echo "simple_message_box('$mgrlang[storage_mes_as3_1]','');";
					}
				}
				else
				{
					echo "simple_message_box('$mgrlang[storage_mes_as3_2]','');";
					echo "$('as3_username_div').className='fs_row_error';";
					echo "$('as3_password_div').className='fs_row_error';";
				}
				echo "
					$('as3_test_button').setValue('$mgrlang[storage_b_test]');
					$('as3_test_button').enable();
					</script>";
			break;
			
			case "cloudfiles":
				echo "<script>";				
				require_once('../assets/classes/rackspace/cloudfiles.php');
					
				//$username = 'ktools';
				//$api_key = '59612b31de4bd1feaa6c84bdac892f5a';
				
				$username = $_GET['cloudfiles_username'];
				$api_key = $_GET['cloudfiles_password'];
				
				$auth = new CF_Authentication($username, $api_key);
				# $auth->ssl_use_cabundle();  # bypass cURL's old CA bundle
				echo "//";
				$auth->authenticate();
				echo "\n\n";
				
				/*echo "alert('" . $auth->authenticated() . "rr'); </script>"; exit;*/
				
				if($auth->authenticated())
				{
					# SEE IF THE FORM SHOULD BE SUBMITTED
					if($_GET['dosubmit'] == '1')
					{						
						echo "$('storage_type').enable();";
						echo "\$('data_form').submit();";
					}
					else
					{
						echo "simple_message_box('$mgrlang[storage_mes_cf_1]','');";
					}
				}
				else
				{
					echo "simple_message_box('$mgrlang[storage_mes_cf_2]','');";
					echo "$('cloudfiles_username_div').className='fs_row_error';";
					echo "$('cloudfiles_password_div').className='fs_row_error';";
				}
				echo "
					$('cloudfiles_test_button').setValue('$mgrlang[storage_b_test]');
					$('cloudfiles_test_button').enable();
					</script>";
			break;
		}	
?>
