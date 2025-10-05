<?php
	# INCLUDE THE SESSION START FILE
	require_once('../assets/includes/session.php');
	//sleep(2);
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	# INCLUDE MANAGER CONFIG FILE
	require_once('mgr.config.php');									
	
	# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/db.config.php');
	
	# INCLUDE DATABASE CONNECTION FILE
	require_once('../assets/includes/db.conn.php');
	
	# INCLUDE SHARED FUNCTIONS FILE
	require_once('../assets/includes/shared.functions.php');
		
	# SELECT THE SETTINGS DATABASE
	require_once('mgr.select.settings.php');
	
	# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.functions.php');
	
	switch($_REQUEST['pmode'])
	{
		# SET THE WIZARD TO OFF
		case "wizardoff":
			$sadmin = $_SESSION['admin_user']['admin_id'];
			$sql = "UPDATE {$dbinfo[pre]}admins SET wizard='0' WHERE admin_id = '$sadmin'";
			$result = mysqli_query($db,$sql);			
			$_SESSION['admin_user']['wizard'] = '0';			
		break;
		# TEMPORARILY SET THE WIZARD OFF BUT SHOW IT NEXT TIME
		case "wizardtempoff":			
			$_SESSION['admin_user']['wizard'] = '0';			
		break;
		case "wpanel":
		default:
		// INCLUDE LANGUAGE FILE(S)
		include_lang();		
		
		$sadmin = $_SESSION['admin_user']['admin_id'];
		$sql = "UPDATE {$dbinfo[pre]}admins SET wp_left='$_GET[lpanels]',wp_right='$_GET[rpanels]' where admin_id = '$sadmin'";
		$result = mysqli_query($db,$sql);
		
		# RELOAD THESE SESSIONS
		$_SESSION['admin_user']['wp_left'] = $_GET['lpanels'];
		$_SESSION['admin_user']['wp_right'] = $_GET['rpanels'];
	
		//echo "<img src='images/mgr.icon.lnpanels.gif' align='absmiddle' /> " . $mgrlang['gen_welpanel'];
?>
<script language="javascript" type="text/javascript">
	// SET THE BUTTON BACK TO NORMAL
	$('img_panels').src = "images/mgr.button.group.off.png";
	$('img_panels').setStyle({paddingTop: '0',paddingBottom: '0'});
</script>
<?php
		break;
	}
?>