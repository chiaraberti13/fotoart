<?php
	# SET THE TIME LIMIT JUST IN CASE
	if(function_exists('set_time_limit')) set_time_limit(200);

	# INITIAL INSTALL DATABASE SETUP
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
	
	# VERSION 4.1 DATEBASE UPDATES EXAMPLE
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
		$modfiles[] = "manager/mgr.welcome.php";
		$modfiles[] = "manager/mgr.test.php";
		$modfiles[] = "assets/test.php";
		$modfiles[] = "assets/test.php";
		$modfiles[] = "assets/test.php";
										
	}
	
	//echo $config['product_version']; exit;
	
	# ALWAYS INCLUDE THIS AS AN UPDATED FILE
	$modfiles[] = "assets/includes/version.php";
	
	$modfiles[] = "assets/test1.php";
	$modfiles[] = "assets/test2.php";
	$modfiles[] = "assets/test3.php";
	$modfiles[] = "manager/test4.php";
	
	
	# ONLY SHOW UNIQUE FILES
	$modfiles = array_unique($modfiles);
	
	# EXECUTE ALL OF THE DATABASE COMMANDS
	if(count($sql) > 0){
		foreach($sql as $value){
			$results = mysqli_query($db,$value);
			//$results = mysqli_query($db,$value,$db_conn);
			if(!results){
				$error[]= "Create table failed.";
			}
		}
	}
?>
