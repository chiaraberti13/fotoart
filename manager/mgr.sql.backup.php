<?php
# SET THE TIME LIMIT JUST IN CASE
if(function_exists('set_time_limit')) set_time_limit(200);

if($_GET['backupmode']){ $backupmode = $_GET['backupmode']; }
//if($_GET['perm']){ $perm = $_GET['perm']; }
	
switch($backupmode){
	case "backup":
		# RUN A MANUAL BACKUP
		if(empty($backup_inc)){
			# INCLUDE MANAGER CONFIG FILE
			require_once('mgr.config.php');
			
			# INCLUDE DATABASE CONFIG FILE
			require_once('../assets/includes/db.config.php');
			
			# INCLUDE DATABASE CONNECTION FILE
			require_once('../assets/includes/db.conn.php');
			
			# SELECT THE SETTINGS DATABASE
			require_once('mgr.select.settings.php');
			
			# MAKE SURE TO CHECK TO SEE IF THERE IS PERMISSION TO RUN THE BACKUP
			//if($perm != md5($config['settings']['serial_number'])){
			//	exit;
			//}
						
			# CHECK IF THE DATABASE NEEDS TO BE BACKED UP
			$next_backup = date("Y-m-d H:i:00",strtotime($config['settings']['last_backup']." +".$config['settings']['backup_days']." day"));
			if($next_backup < gmdate("Y-m-d H:i:00") and $config['settings']['backup_days'] > 0){
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}settings SET last_backup='" . gmt_date() . "' where settings_id  = '1' LIMIT 1";
				$result = mysqli_query($db,$sql);
				
				//save_activity(0,$mgrlang['subnav_software_setup'],1,"<strong>".$mgrlang['gen_b_sav']."</strong>");		
			}					
		}
		
		# MAKE SURE TO CHECK TO SEE IF THERE IS PERMISSION TO RUN THE BACKUP
		//if($perm != md5($config['settings']['serial_number'])){
		//	exit;
		//}
		
		$dbname = $dbinfo['name'];
		function data($dbname){
			global $dbinfo,$db;
			//mysql_select_db($db); 
			//$tables = mysql_list_tables($db); 
			$tables = mysqli_query($db,"SHOW TABLES FROM {$dbinfo['name']}");
			while ($td = mysqli_fetch_array($tables)){
				$table = $td[0]; 
				$r = mysqli_query($db,"SHOW CREATE TABLE `$table`"); 
				if ($r){ 
					$insert_sql = ""; 
					$d = mysqli_fetch_array($r); 
					$d[1] .= ";"; 
					$sql[] = str_replace("\n", "", $d[1]); 
					$table_query = mysqli_query($db,"SELECT * FROM `$table`"); 
					$num_fields = mysqli_num_fields($table_query);
					while ($fetch_row = mysqli_fetch_array($table_query)){ 
						$insert_sql .= "INSERT INTO $table VALUES("; 
						for ($n=1;$n<=$num_fields;$n++){ 
							$m = $n - 1; 
							$insert_sql .= "'".mysqli_real_escape_string($db,$fetch_row[$m])."', "; 
						} 
						$insert_sql = substr($insert_sql,0,-2); 
						$insert_sql .= ");\n"; 
					} 
					if ($insert_sql!= ""){ 
						$sql[] = $insert_sql; 
					} 
				} 
			} 
			return implode("\r", $sql);
		}

		 $string = data($db);
		 $date = date(Y_m_d);
		 $ext = "sql";
		 
		 # "(" . $config['product_code'] . str_replace(".","-",$config['product_version']) . ")";
		 //$filename = "../assets/backups/" . $db . "_" . $date . ".sql";
		 $filename = "../assets/backups/" . $date . "-" . date("U") . "-" . $config['productCode'] . str_replace(".","",$config['productVersion']) . ".sql";
		 $filenamehandle = fopen($filename, 'w') or die("can't open file for writing");
		 fwrite($filenamehandle, $string);
		 fclose($filenamehandle);
		 
		// echo "tedt"; exit;
	break;
	case "download":
		$tfilename = explode("-",$_GET['filename']);
		$filename = $tfilename[0] . ".sql";
		$file = "../assets/backups/" . $_GET['filename'];
		$ctype="applicatoin/txt";
		
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
}
?>