<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	header('Content-type: application/x-json');
	//sleep(1);
	
	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	//include_lang();												# INCLUDE THE LANGUAGE FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	require_once('../assets/classes/json.php');						# INCLUDE JSON CLASS FILE
	
	$json = new Services_JSON();

	# MEMBERS
	if(empty($_GET['startletter'])){
		$startletter = "a";
	} else {
		$startletter = $_GET['startletter'];
	}
	
	// USED TO TRICK JSON INTO PULLING THE RECORD IF NOTHING IS RETURNED
	$data['junk']['name'][] = "test";
	
	$member_result = mysqli_query($db,"SELECT mem_id,f_name,l_name,email FROM {$dbinfo[pre]}members WHERE l_name LIKE '{$startletter}%' ORDER BY l_name");
    $member_rows = mysqli_num_rows($member_result);
	while($mgrMemberInfo = mysqli_fetch_object($member_result)){	
		$data['members']['name'][] = "$mgrMemberInfo->l_name, $mgrMemberInfo->f_name";
		$data['members']['fname'][] = "$mgrMemberInfo->f_name";
		$data['members']['lname'][] = "$mgrMemberInfo->l_name";
		$data['members']['email'][] = "$mgrMemberInfo->email";
		$data['members']['id'][] = "$mgrMemberInfo->mem_id";
		$data['members']['flag'][] = "icon.none.gif";
	}
	
	if($_GET['members_only'] != 1){		
		# GROUPS
		$grp_result = mysqli_query($db,"SELECT gr_id,name,flagtype FROM {$dbinfo[pre]}groups WHERE mgrarea = 'members' ORDER BY name");
		$grp_rows = mysqli_num_rows($grp_result);
		while($grp = mysqli_fetch_object($grp_result)){			
			$data['groups']['name'][] = "$grp->name";
			$data['groups']['id'][] = "$grp->gr_id";
			$data['groups']['flag'][] = "$grp->flagtype";		
		}
		
		# MEMBERSHIPS	
		$ms_result = mysqli_query($db,"SELECT ms_id,name,flagtype FROM {$dbinfo[pre]}memberships ORDER BY name");
		$ms_rows = mysqli_num_rows($ms_result);
		while($ms = mysqli_fetch_object($ms_result)){	
			$data['memberships']['name'][] = "$ms->name";
			$data['memberships']['id'][] = "$ms->ms_id";
			$data['memberships']['flag'][] = "$ms->flagtype";
		}
	}
	
	echo $json->encode($data);
	
	//array ( ’someKey’=>”someValue”, ‘number’ => 43, ‘array’ =>array(1,2,array(’this’=>’that’)));
?> 
