<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	$page = "add_media";
	$lnav = "library";
	
	$supportPageID = '318';

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
	require_once('../assets/classes/encryption.php');				# INCLUDE ENCRYPTION CLASS
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	if($_GET['itemPhotos'])
	{	
		$uploadURL = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=item_photos_plupload&item_id={$_GET[id]}&mgrarea={$_GET[mgrarea]}";
		$allowedFiletypes = "jpg,jpeg,jpe";
		
		// echo $uploadURL; exit; // Testing
	}
	else
	{
		foreach(getAlldTypeExtensions() as $value)
		{
			$allowedFiletypes .= "{$value},";
		}
		
		$uploadURL = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=add_media_plupload";
	}
	
	$uploadMaxFilesize = str_replace('M','',ini_get('upload_max_filesize'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Plupload</title>
	<script type="text/javascript" src="../assets/javascript/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<link rel="stylesheet" href="mgr.style.css" />
	<style type="text/css">
		@import url(../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
		
		body{
			background-color: #ddd;	
			padding: 20px;
		}
		
		.workboxActionButtons{
			text-align: right;	
		}
	</style>
	
	<script>
		$(function()
		{
			plupload.addI18n({
					'Select files' : '<?php echo $mgrlang['plupAddFilesToQueue']; ?>',
					'Add files to the upload queue and click the start button.' : '<?php echo $mgrlang['plupAddFilesToQueue']; ?>',
					'Filename' : '<?php echo $mgrlang['plupFilename']; ?>',
					'Status' : '<?php echo $mgrlang['plupStatus']; ?>',
					'Size' : '<?php echo $mgrlang['plupSize']; ?>',
					'Add files' : '<?php echo $mgrlang['plupAddFiles']; ?>',
					'Start upload':'<?php echo $mgrlang['plupStartUplaod']; ?>',
					'Stop current upload' : '<?php echo $mgrlang['plupStopUpload']; ?>',
					'Start uploading queue' : '<?php echo $mgrlang['plupStartQueue']; ?>',
					'Drag files here.' : '<?php echo $mgrlang['plupDragFilesHere']; ?>'
			});
			
			$('#cancelUpload').click(function(event)
			{					
				window.close();
				// Reload parent
			});
								
			$("#uploadContainer").pluploadQueue({
				// General settings
				runtimes : 'gears,html5,flash,silverlight,browserplus',
				url : '<?php echo $uploadURL; ?>',
				max_file_size : '<?php echo $uploadMaxFilesize; ?>mb',
				chunk_size : '1mb',
				unique_names : false,				
				// Specify what files to browse for
				filters : [
					{ title : "Files", extensions : "<?php echo $allowedFiletypes; ?>" }
				],
				// Flash settings
				flash_swf_url : '../assets/plupload/plupload.flash.swf',				
				// Silverlight settings
				silverlight_xap_url : '../assets/plupload/plupload.silverlight.xap'
			});	
			
			var uploader = $("#uploadContainer").pluploadQueue();
			
			// preinit: attachCallbacks,
			
			$('#startContrUpload').click(function(event)
			{					
				uploader.start();
			});
			
			uploader.bind('UploadComplete', function(Up, File, Response)
			{
				//$('#uploadMediaStep1').hide();
				//var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=newUpload';
				//workbox({ page : workboxPage, skipOverlay : true });						
				window.close();
			});
			
			uploader.bind('FilesAdded', function(Up, File, Response)
			{
				if(uploader.files.length > 0)
				{
					$('#startContrUpload').removeAttr('disabled');
				}
				else
				{
					$('#startContrUpload').attr('disabled','disabled');
				}
			});
			
			uploader.bind('FilesRemoved', function(Up, File, Response)
			{
				if(uploader.files.length > 0)
				{
					$('#startContrUpload').removeAttr('disabled');
				}
				else
				{
					$('#startContrUpload').attr('disabled','disabled');
				}
			});
		});
	</script>
</head>

<body>
	<div style="margin-bottom: 30px;" id="uploadContainer">
	
	</div>
	<?php 
		echo "<div id='wbfooter' style='padding: 0px; margin: 0;'><img src='images/mgr.notice.icon.small2.png' style='float: left; margin: 5px 5px 0 0;' /><p style='float: left; width: 300px; text-align: left; font-size: 10px; color: #6c6c6c'><strong>{$mgrlang[setup_f_uploader]}:</strong> {$mgrlang[change_batch]} $mgrlang[nav_settings] > ";
			# SEE IF ACCESS TO SOFTWARE SETUP IS ALLOWED
			if(in_array('software_setup',$_SESSION['admin_user']['permissions']))
			{
				//echo "<a href='mgr.software.setup.php?ep=1' target='_parent'>$mgrlang[subnav_software_setup]</a>";
				echo "{$mgrlang[subnav_software_setup]}";
			}
			else
			{
				echo "<strong>$mgrlang[subnav_software_setup]</strong>";
			}
			echo "</p><p style='float: right; margin: 0; padding: 0;'><input type='button' value='{$mgrlang[gen_b_close]}' id='cancelUpload' class='small_button'> <input type='button' value='{$mgrlang['start_upload']}' id='startContrUpload' disabled='disabled' class='small_button'></p></div>";
	?>
</body>
</html>