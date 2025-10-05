<?php
	# SET THE TIME LIMIT JUST IN CASE
	if(function_exists('set_time_limit')) set_time_limit(200);

	# INITIAL INSTALL DATABASE SETUP #################################################################################################################
		if($config['product_version'] == "new_install" or $config['product_version'] < 2.9){
			$sql[] = "DROP TABLE IF EXISTS testing";
			$sql[] = 
				"CREATE TABLE testing (
				  id int(10) NOT NULL auto_increment,
				  name varchar(30) NOT NULL default '',
				  path varchar(255) NOT NULL default '',
				  adminpath varchar(255) NOT NULL default '',
				  query_string varchar(255) NOT NULL default '',  
				  PRIMARY KEY  (id)
				) 
				";
			$sql[] = "INSERT INTO `testing` (id,name,path) VALUES ('1', '2', '3')";
		}
	
	# VERSION 4.1 DATEBASE UPDATES EXAMPLE ##########################################################################################################
		if($config['product_version'] == "new_install" or $config['product_version'] < 4.1){			
			$sql[] = "DROP TABLE IF EXISTS testing2";
			$sql[] = 
				"CREATE TABLE testing2 (
				  id int(10) NOT NULL auto_increment,
				  name varchar(30) NOT NULL default '',
				  path varchar(255) NOT NULL default '',
				  adminpath varchar(255) NOT NULL default '',
				  query_string varchar(255) NOT NULL default '',  
				  PRIMARY KEY  (id)
				) 
				";
			$sql[] = "INSERT INTO `testing2` (id,name,path) VALUES ('1', '2', '3')";
			
			# FILES THAT WERE MODIFIED IN THIS VERSION
			//$modfiles[] = "assets/test1.php";
			//$modfiles[] = "assets/test2.php";
			//$modfiles[] = "assets/test3.php";
	
			# THE STATEMENT BELOW IS JUST FOR TESTING AND SHOULD BE REMOVED
			for($x=1;$x<100;$x++){
				$modfiles[] = "assets/test".$x.".php";
			}
		}
	
	# ALWAYS INCLUDE THIS AS AN UPDATED FILE
	$modfiles[] = "assets/includes/version.php";
	
	# ONLY SHOW UNIQUE FILES
	$modfiles = array_unique($modfiles);
	
	# EXECUTE ALL OF THE DATABASE COMMANDS
	if(count($sql) > 0){
		foreach($sql as $value){
			$results = mysqli_query($db,$value);
			if(!results){
				$upgrade_error[]= "Something went wrong with the database upgrade.";
				$db_upgrade_error = '1';
			}
		}
	}
	
	# TESTING ERROR
	$upgrade_error[] = 'Unable to create a new directory because of permission issues. Please create the directory /assets/test and give it write permissions [support link?][test dir?]';
?>
