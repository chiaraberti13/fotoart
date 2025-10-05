<?php
	###################################################################
	####	FTP CONNECTION CLASS                                   ####
	####	Copyright 2003-2008 Ktools.net. All Rights Reserved    ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-4-2008                                      ####
	####	Modified: 3-4-2008                                     #### 
	###################################################################
	class ftp_connection{
		var $error = false;
		
		# MAKE THE FTP CONNECTION
		function ftp_connection($ftphost,$ftpusername,$ftppassword,$ftpport=21)
		{
			$ftpip = gethostbyname($ftphost);
			if(!$this->connection = @ftp_connect($ftpip, $ftpport, 5))
			{
				$this->error = 'FTP: Cannot Connect';
				return false;
			}
			if(!$this->login($ftpusername,$ftppassword))
			{
				$this->error = 'FTP: Cannot Login';
				return false;
			}
		}
		
		# LOGIN
		function login($ftpusername,$ftppassword)
		{
			if(@ftp_login($this->connection, $ftpusername, $ftppassword))
				return true;
		}
		
		# CHANGE DIRECTORIES
		function change_dir($path)
		{
			if(!ftp_chdir($this->connection,$path))
			{
				// COULD NOT CONNECT - PATH INCORRECT
				$this->error = "FTP: Incorrect Path ($path)";
				return false;
			}
		}
		
		# CREATE A DIRECTORY
		function create_dir($newdir)
		{
			if(!ftp_mkdir($this->connection, $newdir))
			{
				$this->error = 'FTP: Cannot Create New Folder';
			}
		}
		
		# REMOVE A DIRECTORY
		function remove_dir($newdir)
		{
			if($newdir)
			{
				if(!ftp_rmdir($this->connection, $newdir))
				{
					$this->error = 'FTP: Cannot Remove Folder';
				}
			}
			else
			{
				$this->error = 'FTP: No new directory path passed';
			}
		}
		
		# DELETE A FILE
		function delete_file($filename)
		{
			if(!ftp_delete($this->connection, $filename))
			{
				$this->error = 'FTP: Cannot Delete File';
			}
		}
		
		# RENAME A DIRECTORY
		function rename_dir($olddir,$newdir)
		{
			if($olddir and $newdir)
			{
				if(!ftp_rename($this->connection, $olddir, $newdir))
				{
					$this->error = 'FTP: Cannot Rename Folder';
				}
			}
			else
			{
				$this->error = 'FTP: No old and new directory paths passed';
			}
		}
		
		# LIST FILES IN A DIR
		function list_files($directory){
			return ftp_nlist($this->connection,$directory);
		}
		
		# PUT A FILE ON THE FTP SITE
		function put_file($localFile,$fileName)
		{
			
			# CHANGE DIR
			//$this->change_dir($directory);
			
			# OPEN THE FILE
			$fp = fopen($localFile, 'r');
			if(ftp_fput($this->connection, $fileName, $fp, FTP_ASCII)) {
				
			} else {
				$this->error = 'FTP: Could not upload file.';
			}
			fclose($fp);
		}
		
		# CAPTURE ERRORS
		function ftp_errors()
		{
			if($this->error)
				return $this->error;
			else
				return false;				
		}
		
		# CLOSE FTP CONNECTION
		function close_conn()
		{
			ftp_close($this->connection);
		}
	}
?>