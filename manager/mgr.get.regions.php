<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON

	//echo "test: " . $_GET['rid'];
	
	# COUNTRY ID
	$cid = $_GET['cid'];
	$sid = $_GET['sid'];
	
	//echo $sid; exit;
	
	$state_result = mysqli_query($db,"SELECT state_id,country_id,name FROM {$dbinfo[pre]}states WHERE country_id = '$cid' AND deleted = '0' ORDER BY name");
	$state_rows = mysqli_num_rows($state_result);
	
	if($state_rows){
		echo "<select name='state' id='state' class='select' style='width: 311px;'>";
		echo "<option>{$mgrlang[gen_none]}/{$mgrlang[gen_other]}</option>\n";
		while($state = mysqli_fetch_object($state_result)){
			echo "<option value='$state->state_id'";
				if($sid == $state->state_id){ echo " selected"; }
			echo ">";
			# PULL CORRECT LANGUAGE
			if($state->{"name_" . $config['settings']['lang_file_mgr']}){
				echo $state->{"name_" . $config['settings']['lang_file_mgr']};
			} else {
				echo $state->name;
			}
			echo "</option>\n";		
		}
		echo "</select>";
	} else {
		echo "<div style='color: #c00404; padding-top: 6px;'>$mgrlang[mem_country_warn] <a href='mgr.states.php?ep=1&country=$cid'>$mgrlang[subnav_states]</a></div>";
	}
	//echo "---" . $_GET['sid'];
	//echo "<option>end</option>";
?>
