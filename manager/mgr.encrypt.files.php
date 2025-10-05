<?php
	//session_start();
	sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	//if(!$_SESSION['array_of_files']){
	//	session_register('array_of_files');
	//}
	
	//$_SESSION['array_of_files'][] = $_GET['filename'];
	
	
	//foreach($_SESSION['array_of_files'] as $value){
	//	echo "Replacing $value... Success<br />";
	//}
	
	/*
	if(file_exists('../assets/upgrades/'.$_GET['filename'])){
		if(@copy('../assets/upgrades/'.$_GET['filename'],'../'.$_GET['filename'])){
			$status = 1;
		} else {
			$status = 0;
		}		
	} else {
		$status = 2;
	}
	*/
	
	
	//echo $_GET['filename'] . "|" . $status;
	echo $_GET['filename'] . "|" . 1;
?>
<?php /*<script language="javascript">upgrade_files();</script>*/ ?>
