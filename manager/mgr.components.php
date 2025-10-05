<?php
	###################################################################
	####	MANAGER COMPONENTS PAGE                                ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 7-14-2006                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_components']; ?></title>
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
	<!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0" onload="shortcuts_height();" onresize="shortcuts_height();">
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="2"><?php include('mgr.header.php'); ?></td>
			</tr>			
			<tr>
				<td colspan="2"><?php include('mgr.support.bar.php'); ?></td>
			</tr>
			<tr>	
				<td valign="top" width="174" bgcolor="5A5A5A" style="background-color: #5A5A5A;"><?php if($config['LeftSubnav']){ include('mgr.nav.php'); } ?></td>
				<td valign="top" width="100%" style="border-left: 1px solid #4B4B4B; background-color: #F9F8F8">
					<?php include('mgr.shortcuts.cont.php'); ?>
					
					<!-- START CONTENT CONTAINER -->
					<div id="content_container">
						<!-- TITLE BAR AREA -->
						<div id="title_bar">
							<img src="./images/mgr.badge.welcome.gif" align="left" style="margin-top: 1px;" />
							<div style="padding-top: 5px;"><strong>Components</strong></div>
							<div style="padding-top: 7px;"><?php create_info_button(); ?></div>
						</div>
						
						<!-- START CONTENT -->
						<div id="content">							
							<div style="padding: 25px;">
								Components (may be renamed) are snippets of code that you can add to your photostore.<br /><br />
								Select a component to add<br />								
								<select>
									<option></option>
									<option>Featured Photos/Files</option>
									<option>Random Photos/Files</option>									
									<option></option>
									<option></option>
									<option></option>
									<option></option>
								</select>
								
								<br /><br />
								Example: Featured Photos - options<br />Featured Photos are based on rating, newest, random. Show X number of photos.
							</div>		
						</div>
						<!-- END CONTENT -->
					</div>
					<div class="footer_spacer"></div>
        <!-- END CONTENT CONTAINER -->
				</td>
			</tr>
			<tr>
				<td colspan="2"><?php include("mgr.footer.php"); ?></td>
			</tr>
		</table>		
	</div>
		
</body>
</html>
<?php mysqli_close($db); ?>
