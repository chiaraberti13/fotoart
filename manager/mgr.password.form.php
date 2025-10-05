<?php
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
	<!--[if lt IE 7.]><script defer type="text/javascript" src="../assets/javascript/pngfix.js"></script><![endif]-->
    <!-- PHP TO JAVASCRIPT VARS -->
    <?php include('mgr.javascript.vars.php'); ?>
	<!-- LOAD PUBLIC AND MANAGER SHARED JAVASCRIPT -->	
	<script type="text/javascript" src="../assets/javascript/shared.min.js"></script>
	<!-- LOAD PROTOTYPE LIBRARY -->	
	<script type="text/javascript" src="../assets/javascript/prototype/prototype.js"></script>
	<!-- LOAD jQUERY -->
	<script type="text/javascript" src="../assets/javascript/jquery/jquery.min.js"></script>
	<script>var $j = jQuery.noConflict();</script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>		
	<script language="javascript">
		function updatepass2(){			
			var updatecontent = 'passresult';
			var loadpage = "mgr.password.gen.php";
			var pars = "&length=" + $('chars').value;
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
		}
		
		function tester(){
			var jontest = $('dataform').serialize();
			alert(jontest);
		}
		
		function updatepass(){		
			var url = "mgr.password.gen.php";
			var pars = "&" + $('dataform').serialize();
			new Ajax.Request(url, {   
				method: 'get',
				parameters: pars,   
				onLoading: function(){ $('passresult').value = 'Generating...'; },
				onSuccess: function(transport) {
					var notice = $('passresult');
					$('passresult').value = transport.responseText;
				}
			});
		}
	</script>	
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0" onLoad="updatepass();" style="background-color: #384f6b; background-image: none;">
<div style="background-color: #384f6b; color: #ffffff; padding: 5px;">
<form id="dataform" action="#">
	<div style="padding: 10px; background-color: #18283b; border: 1px solid #5a779c;">
        <strong>Password Type:</strong><br />
        <input type="radio" id="seedtype1" name="seedtype" onClick="updatepass();" value="alphanum" checked /> Alpha-Numeric<br />
        <input type="radio" id="seedtype2" name="seedtype" onClick="updatepass();" value="alpha" /> Alpha<br />
        <input type="radio" id="seedtype3" name="seedtype" onClick="updatepass();" value="num" /> Numeric<br />
        <input type="radio" id="seedtype4" name="seedtype" onClick="updatepass();" value="alphanumchar" /> Alpha-Numeric plus symbols (!@#$%^&amp;*)<br /><br />
        
        <strong>Characters:</strong> 
        <input type="text" id="chars" name="chars" onKeyUp="updatepass();" value="10" style="font-size: 11px; width: 75px;" /><br /><br />
        
       	<strong>Case:</strong><br />
        <input type="radio" id="caset1" name="caset" onClick="updatepass();" value="upper" checked /> Upper<br />
        <input type="radio" id="caset2" name="caset" onClick="updatepass();" value="lower" /> Lower<br />
        <input type="radio" id="caset3" name="caset" onClick="updatepass();" value="both" /> Both<br />
	</div>
    <div style="padding: 10px; background-color: #18283b; border: 1px solid #5a779c;">   
		<strong>Your Password:</strong><br /><input id="passresult" type="text" style="font-size: 11px; width: 200px;" />	<br /><br />
    	<input type="button" value="Generate Again" onClick="updatepass();" /><input type="button" value="Close" onClick="window.close();" /><br />
	</div>
</form>
</div>
</body>
</html>

