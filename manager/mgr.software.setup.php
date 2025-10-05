<?php
	###################################################################
	####	MANAGER SOFTWARE SETTUP PAGE                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 5-5-2012                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "software_setup";
		$lnav = "settings";
	
		$supportPageID = '368';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		
		switch($_GET['pmode'])
		{
			case "run_backup":
				$backup_inc = 1;
				$backupmode = "backup";
				include('mgr.sql.backup.php');		
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}settings SET last_backup='" . gmt_date() . "' where settings_id  = '1' LIMIT 1";
				$result = mysqli_query($db,$sql);				
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_software_setup'],1,"<strong>".$mgrlang['gen_dbbackup2']."</strong>");
				$vmessage = $mgrlang['util_mes_05'];
			break;
		}
	
		if($_POST){
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			# GET THE REAL PATH IN CASE THEY ENTERED A RELATIVE PATH
			$incoming_path = stripslashes($incoming_path);	
			# REPLACE ANY WRONG DIRECTORY SEPARATORS
			$incoming_path = str_replace("/",DIRECTORY_SEPARATOR,$incoming_path);
			# GET THE REALPATH AND REMOVE THE 
			$incoming_path = addslashes(realpath($incoming_path));
			
			# GET THE REAL PATH IN CASE THEY ENTERED A RELATIVE PATH
			$library_path = stripslashes($library_path);	
			# REPLACE ANY WRONG DIRECTORY SEPARATORS
			$library_path = str_replace("/",DIRECTORY_SEPARATOR,$library_path);
			# GET THE REALPATH AND REMOVE THE 
			$library_path = addslashes(realpath($library_path));
			
			$site_url = rtrim($site_url,"/");
			$site_url = rtrim($site_url,"\\");
			/*
			if(substr($site_url,strlen($site_url)-1,strlen($site_url)) == "/"){
				$site_url = substr($site_url,0,strlen($site_url)-1);
			}
			*/
			
			# MAKE SURE THAT GD IS SELECTED IF IMAGEMAGICK IS DISABLED
			if(!$imageproc)
			{
				$imageproc = 1;
			}
			
			/*
			// Check to see if disable linking has changed
			if(!$disable_linking) $disable_linking = 0; 
			if(($config['settings']['disable_linking'] != $disable_linking) and $disable_linking == 1)
			{
				echo $config['settings']['disable_linking'].'-'.$disable_linking; exit; // Testing
			}
			*/
			
			/*
			if(substr($incoming_path,strlen($incoming_path)-1,strlen($incoming_path)) != DIRECTORY_SEPARATOR){
				$incoming_path.= DIRECTORY_SEPARATOR;
			}
			*/
			
			# JUST AS EXTRA SECURITY BECAUSE OF JAVASCRIPT ERRORS
			if(!file_exists($incoming_path)){				
				echo "<span style=\"font-family: verdana; font-size: 12px; color: #ff0000;\"><strong>". $mgrlang['gen_error_11'] ."</strong><br />".$mgrlang['setup_mes_02']."</span> ";
				exit;
			}
			
			# MAKE A BACKUP
			if($config['settings']['auto_rp']){
				//DuplicateMySQLRecord($dbinfo[pre].'settings', 'settings_id', '1');
				DuplicateSettings($dbinfo[pre].'settings', 'settings_id', '1');
			}
			
			if($config['settings']['mod_rewrite'] != $mod_rewrite)
				updateGalleryVersion(); // Something has changed - update the gallery version
			
			# CLEAN blockips
			$blockips = explode('\n',$blockips);
			$blockips_cleaned = array();
			foreach($blockips as $value){
				$blockips_cleaned[] = trim(str_replace('\r',"",$value));
			}
			$blockips = implode("\n",$blockips_cleaned);
			
			# CLEAN blockwords
			$blockwords = explode('\n',$blockwords);
			$blockwords_cleaned = array();
			foreach($blockwords as $value){
				$blockwords_cleaned[] = trim(str_replace('\r',"",$value));
			}
			$blockwords = implode("\n",$blockwords_cleaned);
			
			# CLEAN blockreferrer
			$blockreferrer = explode('\n',$blockreferrer);
			$blockreferrer_cleaned = array();
			foreach($blockreferrer as $value){
				$blockreferrer_cleaned[] = trim(str_replace('\r',"",$value));
			}
			$blockreferrer = implode("\n",$blockreferrer_cleaned);
			
			# CLEAN blockreferrer
			$blockemails = explode('\n',$blockemails);
			$blockemails_cleaned = array();
			foreach($blockemails as $value){
				$blockemails_cleaned[] = trim(str_replace('\r',"",$value));
			}
			$blockemails = implode("\n",$blockemails_cleaned);

			# ADD SUPPORT FOR ADDITIONAL LANGUAGES
			foreach($active_langs as $value){ 
				$weight_tag_val = ${"weight_tag_" . $value};
				$addsql.= "weight_tag_$value='$weight_tag_val',";
			}
			
			# MAKE SURE SMTP PORT IS NOT LEFT BLANK
			if(empty($smtp_port) or $smtp_port == 0)
			{
				$smtp_port = 25;
			}
			
			//echo $addsql; exit;

			# UPDATE THE SETTINGS DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings SET 
						site_url='$site_url',
						incoming_path='$incoming_path',
						library_path='$library_path',
						verify_before_delete='$verify_before_delete',
						allow_debug_access='$allow_debug_access',
						infoshare='$infoshare',
						member_activity='$member_activity',
						admin_activity='$admin_activity',
						time_zone='$time_zone',
						daylight_savings='$daylight_savings',
						date_format='$date_format',
						date_display='$date_display',
						clock_format='$clock_format',
						date_sep='$date_sep',
						dt_lang_override='$dt_lang_override',
						dt_member_override='$dt_member_override',
						demo_mode='$demo_mode',
						mod_rewrite='$mod_rewrite',
						auto_folders='$auto_folders',
						enc_folders='$enc_folders',
						debugpanel='$debugpanel',
						backup_days='$backup_days',
						display_alerts='$display_alerts',
						blockips='$blockips',
						blockreferrer='$blockreferrer',
						blockemails='$blockemails',
						blockwords='$blockwords',
						imageproc='$imageproc',
						mailproc='$mailproc',
						flexpricing='$flexpricing',
						customizer='$customizer',
						readiptc='$readiptc',
						readexif='$readexif',
						smtp_port='$smtp_port',
						smtp_host='$smtp_host',
						smtp_username='$smtp_username',
						smtp_password='$smtp_password',
						copy_messages='$copy_messages',
						disable_right_click='$disable_right_click',
						disable_copy_paste='$disable_copy_paste',
						disable_printing='$disable_printing',
						disable_linking='$disable_linking',
						stats_html='$stats_html',
						cache_pages='{$cache_pages}',
						cache_pages_time='{$cache_pages_time}',
						weight_tag='$weight_tag',
						decimal_separator='$decimal_separator',
						thousands_separator='$thousands_separator',
						neg_num_format='$neg_num_format',
						lang_num_override='$lang_num_override',
						mem_num_override='$mem_num_override',
						content_editor='$content_editor',
						uploader='$uploader',
						iptc_utf8='$iptc_utf8',
						measurement='$measurement',";
			$sql.= $addsql;
			$sql.= 		"captcha='$captcha',
						contactCaptcha='$contactCaptcha',
						email_conf='$email_conf',
						auto_rp='$auto_rp',
						purge_logs='$purge_logs'
						where settings_id  = '1'";
			//echo $sql; exit;
			$result = mysqli_query($db,$sql);
			
			# UPDATE THE SETTINGS2 DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings2 SET pubuploader='{$pubuploader}' WHERE settings_id  = '1'";
			$result = mysqli_query($db,$sql);
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_software_setup'],1,"<strong>".$mgrlang['gen_b_sav']."</strong>");
				
			header("location: mgr.software.setup.php?ep=1&mes=saved");
			exit;			
		}
		# OUTPUT MESSAGES
		if($_GET['mes'] == "saved"){
			$vmessage = $mgrlang['changes_saved'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_software_setup']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
    <link rel="stylesheet" media="print" type="text/css" href="mgr.style.print.css">
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<script>
		function update_time(){
			$('clock_preview').innerHTML = "<img src='images/mgr.loader2.gif' align='absmiddle' style='margin-top: 2px;' />";
			var time_zone = $('time_zone').options[$('time_zone').selectedIndex].value;
			var daylight_savings = $('daylight_savings').checked;
			var date_format = $('date_format').options[$('date_format').selectedIndex].value;
			var date_display = $('date_display').options[$('date_display').selectedIndex].value;
			var clock_format = $('clock_format').options[$('clock_format').selectedIndex].value;
			
			var date_sep = $('date_sep').options[$('date_sep').selectedIndex].value;
			var pars = "time_zone=" + time_zone + "&daylight_savings=" + daylight_savings + "&date_format=" + date_format + "&clock_format=" + clock_format + "&date_sep=" + date_sep + "&date_display=" + date_display;
			var myAjax = new Ajax.Updater(
				'clock_preview', 
				'mgr.clock.preview.php', 
				{
					method: 'get', 
					parameters: pars
				});

		}
		
		Event.observe(window, 'load', function()
		{
			// HELP BUTTON
			if($('abutton_help')!=null)
			{
				$('abutton_help').observe('click', function()
					{
						support_popup('<?php echo $supportPageID; ?>');
					});
				$('abutton_help').observe('mouseover', function()
					{
						$('img_help').src='./images/mgr.button.help.png';
					});
				$('abutton_help').observe('mouseout', function()
					{
						$('img_help').src='./images/mgr.button.help.off.png';
					});
			}
			
			<?php
				if($_GET['about'])
					echo "bringtofront('6');";
			?>
		});
		
		function form_sumbitter(){
			// REVERT BACK
			$('site_url_div').className='fs_row_off';
			$('incoming_path_div').className='fs_row_on';
			$('library_path_div').className='fs_row_off';
			<?php				
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = "mgr.software.setup.php";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("site_url","setup_f_url",1);
					js_validate_field("incoming_path","setup_f_idn",1);
					js_validate_field("library_path","setup_f_lp",1);
			?>	
			
				if($('mailproc_smtpmail').checked)
				{
					if($F('smtp_port') == '' || $F('smtp_port') == null){
						simple_message_box("<?php echo $mgrlang['gen_pleaseenter'] . " <span style='color: #971111;'>" . $mgrlang['setup_f_smtp_port'] . "</span> " . $mgrlang['gen_beforesave']; ?>","smtp_port");
						$('smtp1').className='fs_row_error';
						bringtofront('8');
						return false;
					}
				
					if($F('smtp_host') == '' || $F('smtp_host') == null){
						simple_message_box("<?php echo $mgrlang['gen_pleaseenter'] . " <span style='color: #971111;'>" . $mgrlang['setup_f_smtp_host'] . "</span> " . $mgrlang['gen_beforesave']; ?>","smtp_host");
						$('smtp2').className='fs_row_error';
						bringtofront('8');
						return false;
					}
				}
			
				// AJAX CHECK FORM DETAILS
				var url = "mgr.software.setup.actions.php";
				var updatebox = "hidden_box";
				var pars = "site_url=" + $F('site_url') + "&incoming_path=" + $F('incoming_path') + "&library_path=" + $F('library_path');
				var myAjax = new Ajax.Updater(
					updatebox, 
					url, 
					{
						method: 'post', 
						parameters: pars,
						evalScripts: true
					});	
					
				return false;					
			<?php
				}
			?>
		}
		
		// Test the email settings
		function testEmailWorkbox()
		{
			workbox2({'page':'mgr.workbox.php?box=test_email'});
		}
		
		function submit_form(){
			$('data_form').action = "<?php echo $action_link; ?>";
			$('data_form').submit();
		}
		
		// SHOW SMTP SETTINGS/FIELDS
		function show_smtp(){
			show_div('smtp1');
			show_div('smtp2');
			show_div('smtp3');
			show_div('smtp4');
		}
		// HIDE SMTP SETTINGS/FIELDS
		function hide_smtp(){
			hide_div('smtp1');
			hide_div('smtp2');
			hide_div('smtp3');
			hide_div('smtp4');
		}
		
		// LOAD THE ACTIVITY LOG
		function load_al(startat){			
			if($('activity_window') != null){
				show_loader('activity_window');
			} else {
				show_loader('activity_log');
			}
			var pars = 'mid=0&manager=1&start=' + startat + get_to_date() + get_from_date();
			var myAjax = new Ajax.Updater(
				'activity_log', 
				'mgr.activity.log.php', 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		<?php
			# JUMP TO AND LOAD ACTIVITY LOG
			if($_GET['al']){
		?>
			Event.observe(window, 'load', function() {
				bringtofront('10');
				load_al(0);
			});
		<?php
		 	}
			# JUMP TO TAB
			if($_GET['jump']){
		?>
			Event.observe(window, 'load', function() {
				bringtofront('<?php echo $_GET['jump']; ?>');
			});
		<?php
			}
			# JUMP TO TAB
			if($_GET['pmode'] == "run_backup"){
		?>
			Event.observe(window, 'load', function() {
				bringtofront('4');
			});
		<?php
			}
		?>
		
		// PURGE ACTIVITY LOGS
		function purge_activity_log(){
			var url = "mgr.activity.log.php";
			var updatebox = "activity_log";
			//var pars;
			var pday = $('purge_day').options[$('purge_day').selectedIndex].value;
			var pmonth = $('purge_month').options[$('purge_month').selectedIndex].value;
			var pyear = $('purge_year').options[$('purge_year').selectedIndex].value;
			var pars = "purge=1&manager=1&mid=0&pday="+pday+"&pmonth="+pmonth+"&pyear="+pyear;
			var myAjax = new Ajax.Updater(
				updatebox, 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		// DOWNLOAD CSV
		function download_csv(){
			location.href='mgr.activity.log.php'+'?displaymode=download&manager=1&mid=0' + get_to_date() + get_from_date();
		}
		
		// DO DB BACKUP
		function backup_db(){
			location.href='mgr.software.setup.php?pmode=run_backup';
		}
		
		// CREATE THE PRINT WINDOW AND INVOKE PRINTING
		function prep_printing(){
			var print_details = new Object();
			print_details.updatecontent = 'print_window_inner';
			print_details.loadpath = 'mgr.activity.log.php';
			print_details.pars = 'displaymode=print&manager=1&mid=0';
			print_details.pars = print_details.pars + get_to_date() + get_from_date();
			do_printing(print_details);
		}
		
		workboxobj = new Object();
		workboxobj.mode = 'encrypt';
		
		// OPEN THE WORKBOX
		function encryption_wb(wbmode){			
			switch(wbmode){
				case "encfiles":					
					workboxobj.mode = 'encrypt';
					workboxobj.page = 'encfiles';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
				case "encfolders":
					workboxobj.mode = 'encrypt';
					workboxobj.page = 'encfolders';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
			}
		}
		
		// RUN THROUGH THE ENCRYPTION PROCESS
		var current_file_number = 1;
		var stopped = 0;
		var start;
		var end;
		var f_start_time;
		var f_end_time;				
		var diff_time;
		var total_time;
		var add_time = 0;
		var show_time;
		var min_time = 0;
		var sec_time = 0;
		var est_time = 0;
		var est_calc = 0;
		function encrypt_files(){
			if(stopped != 1){
				if(current_file_number == 1){
					$('file_window').update('Starting...<br />');
					$('current_file_window').setStyle({display: "block"});
					$('status_bar').setStyle({display: "block"});
				}						
				
				var url = "mgr.encrypt.files.php";
				var updatebox = "hidden_window";
				var rf_length = replacement_files.length-1;
				
				//alert(rf_length);
				
				if(current_file_number <= rf_length){				
					var pars = "filename=" + replacement_files[current_file_number];				
					//var myAjax = new Ajax.Updater(
					//updatebox, 
					
					start = new Date();
    				f_start_time = start.valueOf();	
					
					var myAjax = new Ajax.Request( 
					url, 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true,
						onSuccess: function(transport){
							var response_info = transport.responseText;
							var cur_file = response_info.split("|");
							//var cur_file = $('hidden_window').innerHTML;
							$('current_file_window').update("<?php echo $mgrlang['gen_processing']; ?> " + cur_file[0]);						
							
							switch(cur_file[1]){
								case "1":						
									var file_status = "<span style='color: #33af02; font-weight: bold;'>Success!</span>";
								break
								case "0":
									var file_status = "<span style='color: #CC0000;'><strong>Failed!</strong> (possibly a permissions issue)</span>";
								break;
								case "2":
									var file_status = "<span style='color: #CC0000; font-weight: bold;'>File missing from upgrade!</span>";
								break;
							}
							$('file_window').update($('file_window').innerHTML + 'Replacing ' + cur_file[0] + '...' + file_status + '<br />');						
							$('files_processed').update('<?php echo $mgrlang['files_processed']; ?> ' + (current_file_number) + '/' + rf_length);
					
							// CALCULATE PERCENTAGE
							var percentage =  Math.round((current_file_number/rf_length)*100);
							//$('progress_bar').style.width = Math.round(percentage*1.5);							
							$('progress_bar').setStyle({width: Math.round(percentage*1.5) + 'px'});							
							$('show_perc').innerHTML = percentage + "%";
							
							var objDiv = $('file_window');
							objDiv.scrollTop = objDiv.scrollHeight;
							
							// END TIME
							end = new Date();
							f_end_time = end.valueOf();				
							diff_time = f_end_time - f_start_time;
							add_time+=diff_time;
							show_time = calc_time(add_time);							
							est_calc = (add_time/current_file_number)*(rf_length-current_file_number);
							est_time = calc_time(est_calc);							
							// SHOW PROCESS TIME FOR CURRENT FILE
							//$('fprocesstime_' + i).innerHTML = calc_time(diff_time);							
							// SHOW PROCESS TIME ELAPSED AND REMAINING			
							$('time_calc').innerHTML = "<?php echo $mgrlang['elapsed']; ?>: " + show_time + " - <?php echo $mgrlang['remaining']; ?>: ~" + est_time;
							
							current_file_number++;
							
							encrypt_files();
						},
						onFailure: function(){
							alert('Something went wrong!');
						}
					});
					
					//var myAjax = new Ajax.Request(
					//	url, 
					//	{
					//		method: 'get', 
					//		parameters: pars
					//	});
					//,onSuccess: status_good,
					//onFailure: status_bad
					
					
					// MOVE TO ON COMPLETE
					//$('file_window').update(current_window + replacement_files[current_file_number]);
					
								
				} else {
					var objDiv = $('file_window');
					objDiv.scrollTop = objDiv.scrollHeight;
					$('current_file_window').update('Complete');
					$('cont_button').enable();
					$('stop_button').disable();
					simple_message_box('Replacing files has completed. If there are any errors you will need to replace these files manually. Please click the Done button when complete.','cont_button')
					//alert("complete");		
				}
			} else {
				$('file_window').update($('file_window').innerHTML + '<span style="color: #CC0000; font-weight: bold;">STOPPED!</span><br />');
			}
		}
		
		// CALCULATE TIME
		function calc_time(new_time){
			if(new_time > 1000){
				sec_time = Math.round(new_time/1000);
				if(sec_time > 60){
					min_time = Math.round(sec_time/60);
					return min_time + "min";
				} else {
					return sec_time + "sec";
				}				
			} else {
				return "0sec";
			}
		}		
		
		// START UPGRADE FUNCTION
		function start_encryption()
		{
			stopped = 0;
			$('start_button').disable();
			$('cancel_button').disable();
			show_div('enclist');		
		}
		
		// UPDATE NUMBER PREVIEW
		function update_number_preview()
		{
			// 1234568.00
			var my_number = '12';			
			
			var thousands_separator = $('thousands_separator').options[$('thousands_separator').selectedIndex].value;
			if(thousands_separator == 'none')
			{
				thousands_separator = "";
			}
			else if(thousands_separator == "space")
			{
				thousands_separator = " ";
			}
			my_number = my_number + thousands_separator + '345';
			my_number = my_number + thousands_separator + '678';
			
			var decimal_separator = $('decimal_separator').options[$('decimal_separator').selectedIndex].value;
			
			//var decimal_places = $('decimal_places').options[$('decimal_places').selectedIndex].value;
			var decimal_places = numset.decimal_places;
			var decimal = "";
			for(var y=0;y<decimal_places;y++)
			{
				decimal = decimal + "0";	
			}
			
			if(decimal_places != "0")
			{
				my_number = my_number + decimal_separator + decimal;
			}			
			
			// NEGATIVE NUMBER
			var neg_num_format = $('neg_num_format').options[$('neg_num_format').selectedIndex].value;
			var neg_number;
			switch(neg_num_format)
			{
				case "1":
					neg_number = "-" + my_number;
				break;
				case "2":
					neg_number = "- " + my_number;
				break
				case "3":
					neg_number = "(" + my_number + ")";
				break;
				case "4":
					neg_number = my_number + "-";
				break;
				case "5":
					neg_number = my_number + " -";
				break;
			}			
			
			$('number_preview').update("<?php echo $mgrlang['setup_f_num_pos']; ?>: " + my_number + "<br /><?php echo $mgrlang['setup_f_num_neg']; ?>: " + neg_number);
		}
		
	</script>
</head>
<body onload="<?php if($_GET['wizard']){ echo "bringtofront('2');"; } ?>;update_number_preview();">
	<?php include("mgr.print.window.php"); ?>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
    <div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
            <?php
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
            ?>
            <form id="data_form" name="data_form" method="post" action="<?php echo $action_link; ?>" onsubmit="return form_sumbitter();">
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.setup.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_software_setup']; ?></strong><br /><span><?php echo $mgrlang['subnav_software_setup_d']; ?></span></p>
				<div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>

            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div>
        
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['setup_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['setup_tab5']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['setup_tab2']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['setup_tab4']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('7');" id="tab7"><?php echo $mgrlang['setup_tab7']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('9');" id="tab9"><?php echo $mgrlang['setup_tab9']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('8');" id="tab8"><?php echo $mgrlang['setup_tab8']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('10');load_al(0);" id="tab10"><?php echo $mgrlang['setup_tab10']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3" <?php if(empty($config['BrandAboutSoft'])){ echo "style='border-right: 1px solid #d8d7d7;'"; } ?>><?php echo $mgrlang['setup_tab3']; ?></div>
                    <?php if($config['BrandAboutSoft']){ ?><div class="subsuboff" onclick="bringtofront('6');" id="tab6" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['setup_tab6']; ?></div><?php } ?>
                </div>
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="site_url_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_url']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_url_d']; ?></span>
                        </p>
                        <?php
                            $get_url = $config['base_url'];
							$base_url = dirname(curPageURL()); // Remove www.
                        ?>
                        <div style="float: left">
							<input type="text" id="site_url" name="site_url" value="<?php echo $config['settings']['site_url']; ?>" style="width: 300px;" />
							<?php if($_SESSION['admin_user']['admin_id'] != "DEMO"){ ?><br /><span style="color: #666"><strong><?php echo $mgrlang['recommended']; ?>:</strong> <?php echo $base_url; ?></span><?php } ?>
                    	</div>
					</div>
                    
                    <div class="<?php fs_row_color(); ?>" id="incoming_path_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_idn']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_idn_d']; ?></span>
                        </p>
						<div style="float: left">
                        	<input type="text" name="incoming_path" id="incoming_path" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "- ".$mgrlang['gen_hidden']." -"; } else { echo $config['settings']['incoming_path']; } ?>" style="width: 300px;" /> <?php if($_SESSION['admin_user']['admin_id'] != "DEMO"){ ?><br /><span style="color: #666"><strong><?php echo $mgrlang['recommended']; ?>:</strong> <?php echo $config['base_path'].DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'incoming'; ?></span><?php } ?>
						</div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="library_path_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_lp']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_lp_d']; ?></span>
                        </p>
						<div style="float: left">
                        	<input type="text" name="library_path" id="library_path" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "- ".$mgrlang['gen_hidden']." -"; } else { echo $config['settings']['library_path']; } ?>" style="width: 300px;" /> <?php if($_SESSION['admin_user']['admin_id'] != "DEMO"){ ?><br /><span style="color: #666"><strong><?php echo $mgrlang['recommended']; ?>:</strong> <?php echo $config['base_path'].DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'library'; ?></span><?php } ?>
						</div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_measurement']; ?>: <br />
                            <span><?php echo $mgrlang['setup_measurement_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<select name="measurement" style="width: 250px">
                            <option value="i" id="inches" <?php if($config['settings']['measurement'] == 'i'){ echo "selected"; } ?>><?php echo $mgrlang['setup_measurement_i']; ?></option>
                            <option value="c" id="centimeters" <?php if($config['settings']['measurement'] == 'c'){ echo "selected"; } ?>><?php echo $mgrlang['setup_measurement_c']; ?></option>
                          </select>
                        </div>
                    </div>	
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_weight']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_weight_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <input type="text" name="weight_tag" id="weight_tag" style="width: 300px;" maxlength="200" value="<?php echo $config['settings']['weight_tag']; ?>" />
							<?php
                            if(in_array('multilang',$installed_addons)){
							?>
								&nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_weight','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
								<div id="lang_weight" style="display: none;">
								<ul>
								<?php
									foreach($active_langs as $value){
								?>
									<li><input type="text" name="weight_tag_<?php echo $value; ?>" id="site_title_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($config['settings']['weight_tag' . "_" . $value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
							<?php
									}
									echo "</ul></div>";
								}
							?>
                        </div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_dv']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_dv_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="verify_before_delete" <?php if($config['settings']['verify_before_delete']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_alerts']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_alerts_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="display_alerts" <?php if($config['settings']['display_alerts']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_aka']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_aka_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="allow_debug_access" <?php if($config['settings']['allow_debug_access']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_is']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_is_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="infoshare" <?php if($config['settings']['infoshare']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_admin_ac']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_admin_ac_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="admin_activity" <?php if($config['settings']['admin_activity']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_mem_ac']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_mem_ac_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="member_activity" <?php if($config['settings']['member_activity']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_ceditor']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_ceditor_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<select name="content_editor" style="width: 300px">
                            	<option value="1" id="editor1" <?php if($config['settings']['content_editor'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_ceditor_op1']; ?></option>
                                <option value="2" id="editor2" <?php if($config['settings']['content_editor'] == 2){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_ceditor_op2']; ?></option>
                                <option value="0" id="editor0" <?php if($config['settings']['content_editor'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_ceditor_op0']; ?></option>
                            </select>
                        </div>
                        
                    </div>	
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_uploader']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_uploader_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<select name="uploader" style="width: 300px">
                            	<option value="1" id="uploader1" <?php if($config['settings']['uploader'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_uploader_op1']; ?></option>
                                <option value="2" id="uploader2" <?php if($config['settings']['uploader'] == 2){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_uploader_op2']; ?></option>
                                <option value="4" id="uploader4" <?php if($config['settings']['uploader'] == 4){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_uploader_op4']; ?></option>
                            </select>
                        </div>                       
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_pubuploader']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_pubuploader_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<select name="pubuploader" style="width: 300px">
                            	<option value="1" id="pubuploader1" <?php if($config['settings']['pubuploader'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_uploader_op1']; ?></option>
                                <option value="2" id="pubuploader2" <?php if($config['settings']['pubuploader'] == 2){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_uploader_op4']; ?></option>
                            </select>
                        </div>                       
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_imageproc']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_imageproc_d']; ?></span>
                        </p>
                        <input type="radio" value="1" name="imageproc" id="imageproc_gd" <?php if($config['settings']['imageproc'] == 1){ echo "checked"; } ?> /> <label for="imageproc_gd"><?php echo $mgrlang['setup_gd']; ?></label><br />
                        <input type="radio" value="2" name="imageproc" id="imageproc_im" <?php if($config['settings']['imageproc'] == 2){ echo "checked"; } ?> <?php if(!class_exists('Imagick')){ echo "disabled='disabled'"; } ?> /> <label for="imageproc_im"><?php echo $mgrlang['setup_imagemagick']; ?></label> <?php if(!class_exists('Imagick')){ echo "<span>({$mgrlang[setup_imagemagickerr]})</span>"; } ?>
                    </div>
                     <?php
						/*
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_sharpen']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_sharpen_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="sharpen" <?php if(!function_exists(imageconvolution)){ echo "disabled"; } ?> <?php if($config['settings']['sharpen'] == 1 and function_exists(imageconvolution)){ echo "checked"; } ?> /> <?php if(!function_exists(imageconvolution)){ echo "<span style='color: #ff0000;'><em>$mgrlang[setup_mes_07]</em></span>"; } ?>
                	</div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_sharpenthumb']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_sharpenthumb_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="sharpenthumb" <?php if(!function_exists(imageconvolution)){ echo "disabled"; } ?> <?php if($config['settings']['sharpenthumb'] == 1 and function_exists(imageconvolution)){ echo "checked"; } ?> /> <?php if(!function_exists(imageconvolution)){ echo "<span style='color: #ff0000;'><em>$mgrlang[setup_mes_07]</em></span>"; } ?>
                	</div>
                    	*/
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_iptc']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_iptc_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="readiptc" <?php if($config['settings']['readiptc']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_exif']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_exif_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="readexif" <?php if($config['settings']['readexif']){ echo "checked"; } ?> />
                    </div>
					
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_image_caching']; ?>: <br />
                            <span><?php echo $mgrlang['setup_image_caching_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="cacheImages" <?php if($config['cacheImages']){ echo "checked"; } ?> disabled="disabled" />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_ic_time']; ?>: <br />
                            <span><?php echo $mgrlang['setup_ic_time_d']; ?></span>
                        </p>
                        <p style="float: left; margin-top: 20px; color: #999"><?php echo $config['cacheImagesTime']; ?> <?php echo $mgrlang['setup_seconds']; ?></p>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab9_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_captcha']; ?>:<br />
                            <span><?php echo $mgrlang['setup_f_captcha_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="captcha" <?php if($config['settings']['captcha']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['contact_captcha']; ?>:<br />
                            <span><?php echo $mgrlang['contact_captcha_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contactCaptcha" <?php if($config['settings']['contactCaptcha']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_email_conf']; ?>:<br />
                            <span><?php echo $mgrlang['setup_f_email_conf_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="email_conf" <?php if($config['settings']['email_conf']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_drc']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_drc_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="disable_right_click" <?php if($config['settings']['disable_right_click']){ echo "checked"; } ?> />
                    </div>
					<?php
						if(in_array("pro",$installed_addons))
						{
						/*
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_dpl']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_dpl_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="disable_linking" <?php if($config['settings']['disable_linking']){ echo "checked"; } ?> />
						</div>
                    <?php
						*/
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_blockip']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_blockip_d']; ?></span>
							</p>
							<textarea name="blockips" style="width: 290px; height: 50px;"><?php echo $config['settings']['blockips']; ?></textarea>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_blockreferrer']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_blockreferrer_d']; ?></span>
							</p>
							<textarea name="blockreferrer" style="width: 290px; height: 50px;"><?php echo $config['settings']['blockreferrer']; ?></textarea>
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_blockemail']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_blockemail_d']; ?></span>
							</p>
							<textarea name="blockemails" style="width: 290px; height: 50px;"><?php echo $config['settings']['blockemails']; ?></textarea>
						</div>
					<?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_blockwords']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_blockwords_d']; ?></span>
                        </p>
                        <textarea name="blockwords" style="width: 290px; height: 100px;"><?php echo $config['settings']['blockwords']; ?></textarea>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group" style="display: none;">
                	<?php
						/*
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_copy_messages']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_copy_messages_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="copy_messages" <?php if($config['settings']['copy_messages']){ echo "checked"; } ?> />
                    </div>
						*/
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <div style="position: absolute; margin: 0 0 0 400px; vertical-align: middle">
							<img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['setup_testemailat_mes']; ?></div>
						</div>
						
						<img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_mailproc']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_mailproc_d']; ?></span>
                        </p>
                        <input type="radio" value="1" name="mailproc" id="mailproc_phpmail" <?php if($config['settings']['mailproc'] == 1){ echo "checked"; } ?> onclick="hide_smtp()" /> <?php echo $mgrlang['setup_phpmail']; ?><br />
                        <input type="radio" value="2" name="mailproc" id="mailproc_smtpmail" <?php if($config['settings']['mailproc'] == 2){ echo "checked"; } ?> onclick="show_smtp();" /> <?php echo $mgrlang['setup_smtp']; ?>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="smtp1" <?php if($config['settings']['mailproc'] == 1){ echo "style='display: none;'"; } ?>>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_smtp_port']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_smtp_port_d']; ?></span>
                        </p>
                        <input type="text" name="smtp_port" id="smtp_port" value="<?php echo @$config['settings']['smtp_port']; ?>" style="width: 50px;" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="smtp2" <?php if($config['settings']['mailproc'] == 1){ echo "style='display: none;'"; } ?>>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_smtp_host']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_smtp_host_d']; ?></span>
                        </p>
                        <input type="text" name="smtp_host" id="smtp_host" value="<?php echo @$config['settings']['smtp_host']; ?>" style="width: 200px;" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="smtp3" <?php if($config['settings']['mailproc'] == 1){ echo "style='display: none;'"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_smtp_username']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_smtp_username_d']; ?></span>
                        </p>
                        <input type="text" name="smtp_username" id="smtp_username" value="<?php echo @$config['settings']['smtp_username']; ?>" style="width: 200px;" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="smtp4" <?php if($config['settings']['mailproc'] == 1){ echo "style='display: none;'"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_smtp_password']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_smtp_password_d']; ?></span>
                        </p>
                        <input type="text" name="smtp_password" id="smtp_password" value="<?php echo @$config['settings']['smtp_password']; ?>" style="width: 200px;" />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab10_group" class="group" style="display: none; padding: 0;">							
                    <div id="activity_log"></div>                
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7;">                        
                        <img src="images/mgr.icon.clock.png" align="left" style="margin-right: 10px; margin-left: 10px;" />
                        <div style="padding-top: 15px;"><?php echo $mgrlang['setup_f_dtb']; ?>:</div>
                        <div id="clock_preview">
                        <?php
                            if($config['settings']['date_display'] == "long"){
                                $ndate = new kdate;
                                $ndate->distime = 1;
                                $ndate->diswords = 1;
                                echo "<strong>" . $ndate->showdate(gmdate("Y-m-d H:i:s")) . "</strong>";
                            } else {
                                $ndate2 = new kdate;
                                $ndate2->distime = 1;
                                echo "<strong>" . $ndate2->showdate(gmdate("Y-m-d H:i:s")) . "</strong>";
                            }
                        ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_tz']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_tz_d']; ?></span>
                        </p>
                        <div style="float: left; padding-right: 10px;">
                            <select id="time_zone" name="time_zone" onchange="update_time();" >
                                <option value='-12' <?php if($config['settings']['time_zone'] == "-12"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 12:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_01']; ?></option>
                                <option value='-11' <?php if($config['settings']['time_zone'] == "-11"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 11:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_02']; ?></option>
                                <option value='-10' <?php if($config['settings']['time_zone'] == "-10"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 10:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_03']; ?></option>
                                <option value='-9' <?php if($config['settings']['time_zone'] == "-9"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 9:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_04']; ?></option>
                                <option value='-8' <?php if($config['settings']['time_zone'] == "-8"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 8:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_05']; ?></option>
                                <option value='-7' <?php if($config['settings']['time_zone'] == "-7"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 7:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_06']; ?></option>
                                <option value='-6' <?php if($config['settings']['time_zone'] == "-6"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 6:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_07']; ?></option>
                                <option value='-5' <?php if($config['settings']['time_zone'] == "-5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 5:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_08']; ?></option>
                                <option value='-4' <?php if($config['settings']['time_zone'] == "-4"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 4:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_09']; ?></option>
                                <option value='-3.5' <?php if($config['settings']['time_zone'] == "-3.5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 3:30 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_10']; ?></option>
                                <option value='-3' <?php if($config['settings']['time_zone'] == "-3"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 3:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_11']; ?></option>
                                <option value='-2' <?php if($config['settings']['time_zone'] == "-2"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 2:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_12']; ?></option>
                                <option value='-1' <?php if($config['settings']['time_zone'] == "-1"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> - 1:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_13']; ?></option>
                                <option value='0.0' <?php if($config['settings']['time_zone'] == "-0"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?>) <?php echo $mgrlang['setup_timezone_14']; ?></option>
                                <option value='1' <?php if($config['settings']['time_zone'] == "1"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 1:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_15']; ?></option>
                                <option value='2' <?php if($config['settings']['time_zone'] == "2"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 2:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_16']; ?></option>
                                <option value='3' <?php if($config['settings']['time_adj'] == "3"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 3:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_17']; ?></option>
                                <option value='3.5' <?php if($config['settings']['time_zone'] == "3.5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 3:30 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_18']; ?></option>
                                <option value='4' <?php if($config['settings']['time_zone'] == "4"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 4:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_19']; ?></option>
                                <option value='4.5' <?php if($config['settings']['time_zone'] == "4.5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 4:30 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_20']; ?></option>
                                <option value='5' <?php if($config['settings']['time_zone'] == "5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 5:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_21']; ?></option>
                                <option value='5.5' <?php if($config['settings']['time_zone'] == "5.5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 5:30 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_22']; ?></option>
                                <option value='6' <?php if($config['settings']['time_zone'] == "6"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 6:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_23']; ?></option>
                                <option value='7' <?php if($config['settings']['time_zone'] == "7"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 7:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_24']; ?></option>
                                <option value='8' <?php if($config['settings']['time_zone'] == "8"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 8:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_25']; ?></option>
                                <option value='9' <?php if($config['settings']['time_zone'] == "9"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 9:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_26']; ?></option>
                                <option value='9.5' <?php if($config['settings']['time_zone'] == "9.5"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 9:30 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_27']; ?></option>
                                <option value='10' <?php if($config['settings']['time_zone'] == "10"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 10:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_28']; ?></option>
                                <option value='11' <?php if($config['settings']['time_zone'] == "11"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 11:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_29']; ?>.</option>
                                <option value='12' <?php if($config['settings']['time_zone'] == "12"){ echo "selected"; } ?>>(<?php echo $mgrlang['setup_gmt']; ?> + 12:00 <?php echo $mgrlang['setup_hours']; ?>) <?php echo $mgrlang['setup_timezone_30']; ?></option>
                            </select>
                            <!--<a href="#"><img src="images/mgr.icon.plus.gif" onclick="update_time(1);" border="0" /></a><a href="#"><img src="images/mgr.icon.minus.gif" onclick="update_time(-1);" border="0" /></a><br />-->
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_ds']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_ds_d']; ?></span>
                        </p>
                        <input type="checkbox" id="daylight_savings" name="daylight_savings" value="1" onclick="update_time();" <?php if($config['settings']['daylight_savings']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_df']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_df_d']; ?></span>
                        </p>
                        <select id="date_format" name="date_format" onchange="update_time();" style="width: 100px;">
                            <option value="US" <?php if($config['settings']['date_format'] == "US"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_df1_d']; ?></option>
                            <option value="EURO" <?php if($config['settings']['date_format'] == "EURO"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_df2_d']; ?></option>
                            <option value="INT" <?php if($config['settings']['date_format'] == "INT"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_df3_d']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_dd']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_dd_d']; ?></span>
                        </p>                        
                        <select id="date_display" name="date_display" onchange="update_time();" style="width: 100px;">
                            <option value="long" <?php if($config['settings']['date_display'] == "long"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_dd1_d']; ?></option>
                            <option value="short" <?php if($config['settings']['date_display'] == "short"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_dd2_d']; ?></option>
                            <option value="numb" <?php if($config['settings']['date_display'] == "numb"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_dd3_d']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_cf']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_cf_d']; ?></span>
                        </p>
                        <select id="clock_format" name="clock_format" onchange="update_time();" style="width: 100px;">
                            <option value="12" <?php if($config['settings']['clock_format'] == "12"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_cf1_d']; ?></option>
                            <option value="24" <?php if($config['settings']['clock_format'] == "24"){ echo "selected"; } ?>><?php echo $mgrlang['setup_f_cf2_d']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['setup_f_nds']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_nds_d']; ?></span>
                        </p>
                        <select id="date_sep" name="date_sep" onchange="update_time();" style="width: 100px;">
                            <option value="/" <?php if($config['settings']['date_sep'] == "/"){ echo "selected"; } ?>><?php echo $mgrlang['gen_chr_slash']; ?> ( / )</option>
                            <option value="." <?php if($config['settings']['date_sep'] == "."){ echo "selected"; } ?>><?php echo $mgrlang['gen_chr_period']; ?> ( . )</option>
                            <option value="-" <?php if($config['settings']['date_sep'] == "-"){ echo "selected"; } ?>><?php echo $mgrlang['gen_chr_dash']; ?> ( - )</option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_lfo']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_lfo_d']; ?></span>
                        </p>
                        <input type="checkbox" id="dt_lang_override" name="dt_lang_override" value="1" onclick="update_time();" <?php if($config['settings']['dt_lang_override']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_mo']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_mo_d']; ?></span>
                        </p>
                        <input type="checkbox" id="dt_member_override" name="dt_member_override" value="1" onclick="update_time();" <?php if($config['settings']['dt_member_override']){ echo "checked"; } ?> />
                    </div>				
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['setup_en_cache']; ?>: <br />
							<span><?php echo $mgrlang['setup_en_cache_d']; ?></span>
						</p>
						<input type="checkbox" value="1" name="cache_pages" <?php if($config['settings']['cache_pages'] == 1){ echo "checked"; } ?> />
					</div>
					
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['setup_pc_period']; ?>: <br />
							<span><?php echo $mgrlang['setup_pc_period']; ?></span>
						</p>
						<input type="text" value="<?php echo $config['settings']['cache_pages_time']; ?>" name="cache_pages_time" style="width: 100px" />
					</div>
						
					
					<?php
						if(in_array("pro",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_flexpricing']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_flexpricing_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="flexpricing" <?php if($config['settings']['flexpricing'] == 1){ echo "checked"; } ?> />
						</div>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_customizer']; ?>: <br />
								<span><?php echo $mgrlang['setup_customizer_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="customizer" <?php if($config['settings']['customizer'] == 1){ echo "checked"; } ?> />
						</div>
					<?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_mwr']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_mwr_d']; ?></span>
                        </p>
                        <?php
							$htaccess = (file_exists('../.htaccess')) ? true : false;
						?>
						<input type="checkbox" value="1" name="mod_rewrite" <?php if($config['settings']['mod_rewrite'] and $htaccess){ echo "checked"; } ?> <?php if(!$htaccess){ echo "disabled='disabled'"; } ?> />
						
						<?php if(!$htaccess){ ?>
						<div style="position: absolute; margin: -18px 0 0 300px; vertical-align: middle">
							<img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['setup_htaccess']; ?></div>
						</div>
						<?php } ?>
						
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_iptcutf']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_iptcutf_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="iptc_utf8" <?php if($config['settings']['iptc_utf8']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_debug']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_debug_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="debugpanel" <?php if($config['settings']['debugpanel']){ echo "checked"; } ?> />
                    </div>	
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_demo']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_demo_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="demo_mode" <?php if($config['settings']['demo_mode']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_amfolders']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_amfolders_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="auto_folders" <?php if($config['settings']['auto_folders']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_autoenc']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_autoenc_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="enc_folders" <?php if($config['settings']['enc_folders']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_purge_ac']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_purge_ac_d']; ?></span>
                        </p>
                        <select name="purge_logs" style="width: 120px;">
                            <option value="0" <?php if($config['settings']['purge_logs'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_never']; ?></option>
                            <option value="1" <?php if($config['settings']['purge_logs'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_1d']; ?></option>
                            <option value="3" <?php if($config['settings']['purge_logs'] == 3){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_3d']; ?></option>
                            <option value="5" <?php if($config['settings']['purge_logs'] == 5){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_5d']; ?></option>
                            <option value="7" <?php if($config['settings']['purge_logs'] == 7){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_1w']; ?></option>
                            <option value="14" <?php if($config['settings']['purge_logs'] == 14){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_2w']; ?></option>
                            <option value="30" <?php if($config['settings']['purge_logs'] == 30){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_1m']; ?></option>
                            <option value="60" <?php if($config['settings']['purge_logs'] == 60){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_2m']; ?></option>
                            <option value="90" <?php if($config['settings']['purge_logs'] == 90){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_3m']; ?></option>
                            <option value="180" <?php if($config['settings']['purge_logs'] == 180){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_6m']; ?></option>
                            <option value="365" <?php if($config['settings']['purge_logs'] == 365){ echo "selected"; } ?>><?php echo $mgrlang['setup_time_1y']; ?></option>
                        </select>
                    </div>
                    <?php
					/*
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_cron']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_cron_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><?php echo dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "cron.php"; ?></em></span>
                    </div>
					*/
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_statshtml']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_statshtml_d']; ?></span>
                        </p>
                        <textarea name="stats_html" style="width: 290px; height: 100px;"><?php echo $config['settings']['stats_html']; ?></textarea>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
                    <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7; padding-bottom: 20px;">                        
                        <img src="images/mgr.icon.tips.png" align="absmiddle" style="margin-right: 3px;margin-left: 10px" />
                        <strong><?php echo $mgrlang['gen_tips']; ?>:</strong> <span style="font-size: 11px; color: #666"><?php echo $mgrlang['setup_tip_01']; ?></span>                        
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_lbu']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_lbu_d']; ?></span>
                        </p>
                        <div class="groupinfo"><em>
						<?php
							if($config['settings']['last_backup'] != "0000-00-00 00:00:00"){
								$ndate = new kdate;								
								$ndate->distime = 1;
								echo $ndate->showdate($config['settings']['last_backup']);
								
								//echo $config['settings']['last_backup'];
							} else {
								echo $mgrlang['setup_f_dbbackup_o4']; // NEVER
							}
						?>
                        </em></div>
                    </div>
					<?php
						if(in_array("pro",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_nbu']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_nbu_d']; ?></span>
							</p>
							<div class="groupinfo"><em>
							<?php
								if($config['settings']['backup_days'] == "0"){
									echo $mgrlang['setup_f_dbbackup_o4']; // NEVER
								} else {
									if($config['settings']['last_backup'] == "0000-00-00 00:00:00"){
										$budate = date("Y-m-d H:i");
									} else {
										$budate = substr($config['settings']['last_backup'],0,-3);
									}
									$budate = date("Y-m-d H:i",strtotime("$budate +".$config['settings']['backup_days']." day"));
									
									//echo $budate;
									
									$ndate = new kdate;	
									$ndate->distime = 1;
									echo $ndate->showdate($budate);
								}
							?>
							</em></div>
						</div>
					
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['setup_f_dbbackup']; ?>: <br />
								<span><?php echo $mgrlang['setup_f_dbbackup_d']; ?></span>
							</p>
							<select name="backup_days" style="width: 230px; margin-top: 13px;">
								<option <?php if($config['settings']['backup_days'] == 1){ echo "selected"; } ?> value="1"><?php echo $mgrlang['setup_f_dbbackup_o1']; ?></option>
								<option <?php if($config['settings']['backup_days'] == 7){ echo "selected"; } ?> value="7"><?php echo $mgrlang['setup_f_dbbackup_o2']; ?></option>
								<option <?php if($config['settings']['backup_days'] == 30){ echo "selected"; } ?> value="30"><?php echo $mgrlang['setup_f_dbbackup_o3']; ?></option>
								<option <?php if($config['settings']['backup_days'] == 0){ echo "selected"; } ?> value="0"><?php echo $mgrlang['setup_f_dbbackup_o4']; ?></option>
							</select>
						</div>
					<?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_savedbackup']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_savedbackup_d']; ?></span>
                        </p>                        
                            <?php
								# READ IN ALL NAV.* FILES
								$sqlfiles = array();
								$real_dir = realpath("../assets/backups/");
								$dir = opendir($real_dir);
								# LOOP THROUGH THE NAV DIRECTORY
								while($file = readdir($dir)){
									// MAKE SURE IT IS A VALID FILE
									$issql = explode(".", $file);
									if($file != ".." && $file != "." && is_file("../assets/backups/" . $file) && @$issql[count($issql) - 1] == "sql"){
										$sqlfiles[] = $file;
									}
									unset($issql);
								}
								closedir($dir);
																
								if($sqlfiles){
									$sqlfiles = array_reverse($sqlfiles);
									echo "<select id='sqlbackup' style='width: 163px; margin-top: 13px;' class='select'>";
									foreach($sqlfiles as $value){
										$fake_name = explode("-",$value);
										echo "<option value='mgr.sql.backup.php?backupmode=download&filename=$value'>" . str_replace("_","-",$fake_name[0]) . " ( " . str_replace(".sql","",$fake_name[2]) . " )</option>";
									}
							?>
                                	</select><?php if($sqlfiles){ ?><input type="button" value="<?php echo $mgrlang['setup_f_savedbackup_b']; ?>" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "location.href=$('sqlbackup').options[$('sqlbackup').selectedIndex].value"; } ?>" /><?php } ?>
                          	<?php
								} else {
									echo "<div style='color: #c3082c; font-weight: bold; padding-top: 14px;'>$mgrlang[setup_f_nobackup]</div>";
								}

							?>
                    	
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['util_f_budb']; ?>:<br />
                            <span><?php echo $mgrlang['util_f_budb_d']; ?></span>
                        </p>
						<?php
                            if($_SESSION['admin_user']['admin_id'] == "DEMO"){
                                echo "<input type='button' value='" . $mgrlang['util_f_budb_b'] . "' class='small_button' onclick='demo_message();' style='width: 210px; margin-top: 12px;' />";
                            } else {
                                echo "<input type='submit' value='" . $mgrlang['util_f_budb_b'] . "' class='small_button' onclick='backup_db();' style='width: 230px; margin-top: 12px;' />";
                            }
                        ?> 
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_srp']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_srp_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" style="margin-top: 30px;" name="auto_rp" <?php if($config['settings']['auto_rp']){ echo "checked"; } ?> />
                    </div>	
                </div> 
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                    <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7;">                        
                        <img src="images/mgr.icon.numbers.png" align="left" style="margin-right: 10px; margin-left: 10px;" />
                        <div style="padding-top: 1px;"><?php echo $mgrlang['setup_f_num_preview']; ?>:</div>
                        <div id="number_preview" style="font-weight: bold;">
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_dec_separator']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_dec_separator_d']; ?></span>
                        </p>
                        <select name="decimal_separator" id="decimal_separator" style="width: 120px;" onchange="update_number_preview();">
                        	<option <?php if($config['settings']['decimal_separator'] == "."){ echo "selected"; } ?> value="."><?php echo $mgrlang['gen_chr_period']; ?> ( . )</option>
                            <option <?php if($config['settings']['decimal_separator'] == ","){ echo "selected"; } ?> value=","><?php echo $mgrlang['gen_chr_comma']; ?> ( , )</option>
                            <option <?php if($config['settings']['decimal_separator'] == "-"){ echo "selected"; } ?> value="-"><?php echo $mgrlang['gen_chr_dash']; ?> ( - )</option>
                            <option <?php if($config['settings']['decimal_separator'] == "="){ echo "selected"; } ?> value="="><?php echo $mgrlang['gen_chr_equals']; ?> ( = )</option>
                            <option <?php if($config['settings']['decimal_separator'] == "/"){ echo "selected"; } ?> value="/"><?php echo $mgrlang['gen_chr_slash']; ?> ( / )</option>
                            <option <?php if($config['settings']['decimal_separator'] == ";"){ echo "selected"; } ?> value=";"><?php echo $mgrlang['gen_chr_semicolon']; ?> ( ; )</option>
                            <option <?php if($config['settings']['decimal_separator'] == ":"){ echo "selected"; } ?> value=":"><?php echo $mgrlang['gen_chr_colon']; ?> ( : )</option>
                            <option <?php if($config['settings']['decimal_separator'] == "'"){ echo "selected"; } ?> value="'"><?php echo $mgrlang['gen_chr_apostrophe']; ?> ( ' )</option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_thou_separator']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_thou_separator_d']; ?></span>
                        </p>
                        <select name="thousands_separator" id="thousands_separator" style="width: 120px;" onchange="update_number_preview();">
                        	<option <?php if($config['settings']['thousands_separator'] == "none"){ echo "selected"; } ?> value="none"><?php echo $mgrlang['setup_f_thou_sep_none']; ?></option>
                            <option <?php if($config['settings']['thousands_separator'] == ","){ echo "selected"; } ?> value=","><?php echo $mgrlang['gen_chr_comma']; ?> ( , )</option>
                            <option <?php if($config['settings']['thousands_separator'] == "space"){ echo "selected"; } ?> value="space"><?php echo $mgrlang['setup_f_thou_sep_space']; ?></option>
                            <option <?php if($config['settings']['thousands_separator'] == "."){ echo "selected"; } ?> value="."><?php echo $mgrlang['gen_chr_period']; ?> ( . )</option>
                            <option <?php if($config['settings']['thousands_separator'] == "'"){ echo "selected"; } ?> value="'"><?php echo $mgrlang['gen_chr_apostrophe']; ?> ( , )</option>
                        </select>
                    </div>
                    <?php
					/*
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_num_after']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_num_after_d']; ?></span>
                        </p>
                        <select name="decimal_places" id="decimal_places" style="width: 120px;" onchange="update_number_preview();">
                        	<option <?php if($config['settings']['decimal_places'] == "0"){ echo "selected"; } ?> value="0">0</option>
                            <option <?php if($config['settings']['decimal_places'] == "1"){ echo "selected"; } ?> value="1">1</option>
                            <option <?php if($config['settings']['decimal_places'] == "2"){ echo "selected"; } ?> value="2">2</option>
                            <option <?php if($config['settings']['decimal_places'] == "3"){ echo "selected"; } ?> value="3">3</option>
                            <option <?php if($config['settings']['decimal_places'] == "4"){ echo "selected"; } ?> value="4">4</option>
                            <option <?php if($config['settings']['decimal_places'] == "5"){ echo "selected"; } ?> value="5">5</option>
                            <option <?php if($config['settings']['decimal_places'] == "6"){ echo "selected"; } ?> value="6">6</option>
                            <option <?php if($config['settings']['decimal_places'] == "7"){ echo "selected"; } ?> value="7">7</option>
                            <option <?php if($config['settings']['decimal_places'] == "8"){ echo "selected"; } ?> value="8">8</option>
                            <option <?php if($config['settings']['decimal_places'] == "9"){ echo "selected"; } ?> value="9">9</option>
                        </select>
                    </div>
					*/
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_neg_format']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_neg_format_d']; ?></span>
                        </p>
                        <select name="neg_num_format" id="neg_num_format" style="width: 120px;" onchange="update_number_preview();">
                        	<option <?php if($config['settings']['neg_num_format'] == "1"){ echo "selected"; } ?> value="1">-25.00</option>
                            <option <?php if($config['settings']['neg_num_format'] == "2"){ echo "selected"; } ?> value="2">- 25.00</option>
                            <option <?php if($config['settings']['neg_num_format'] == "3"){ echo "selected"; } ?> value="3">(25.00)</option>
                            <option <?php if($config['settings']['neg_num_format'] == "4"){ echo "selected"; } ?> value="4">25.00-</option>
                            <option <?php if($config['settings']['neg_num_format'] == "5"){ echo "selected"; } ?> value="5">25.00 -</option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_lang_override']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_lang_override_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="lang_num_override" <?php if($config['settings']['lang_num_override']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_mem_override']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_mem_override_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="mem_num_override" <?php if($config['settings']['mem_num_override']){ echo "checked"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <?php
					$media_result = mysqli_query($db,"SELECT COUNT(media_id) AS mediaCount, SUM(filesize) AS originalFilesize FROM {$dbinfo[pre]}media");
					$media = mysqli_fetch_object($media_result);
					
					$smedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_samples.sample_filesize) AS sampleFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_samples ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_samples.media_id");
					$smedia = mysqli_fetch_object($smedia_result);
					
					$tmedia_result = mysqli_query($db,"SELECT SUM({$dbinfo[pre]}media_thumbnails.thumb_filesize) AS thumbFilesize FROM {$dbinfo[pre]}media JOIN {$dbinfo[pre]}media_thumbnails ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_thumbnails.media_id");
					$tmedia = mysqli_fetch_object($tmedia_result);
					
					$total_fs = $media->originalFilesize + $smedia->sampleFilesize + $tmedia->thumbFilesize;
				?>
                <div id="tab6_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_libsize']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_libsize_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><?php echo "<strong>" . $media->mediaCount . "</strong> " . $mgrlang['gen_files']; ?> / <?php echo "<strong>" . convertFilesizeToMB($total_fs) . "</strong>" . $mgrlang['gen_mb']; ?></em></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_serial']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_serial_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "[" . $mgrlang['gen_hidden'] . "]"; } else { echo $config['settings']['serial_number']; } ?></em></span><br />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_actkey']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_actkey_d']; ?>.</span>
                        </p>
                       	<span class="groupinfo"><em><?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "[" . $mgrlang['gen_hidden'] . "]"; } else {  echo $actkey; } ?></em></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_valkey']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_valkey_d']; ?></span>
                        </p>
                       	<span class="groupinfo"><em><?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "[" . $mgrlang['gen_hidden'] . "]"; } else { echo $config['settings']['newkey']; } ?></em></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_sfiletype']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_sfiletype_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><?php foreach(getAlldTypeExtensions() as $ext){ echo "{$ext}, "; } ?></em></span>
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_addons']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_addons_d']; ?></span>
                        </p>
                       	<span class="groupinfo" style="line-height: 1.5;">
                        	<em>
                            <?php
								/*
								# READ IN ALL NAV.* FILES
								$real_dir = realpath("../assets/addons/");
								$dir = opendir($real_dir);
								$i = 0;
								# LOOP THROUGH THE NAV DIRECTORY
								while($file = readdir($dir)){
									// MAKE SURE IT IS A VALID FILE
									$isaddon = explode(".", $file);
									if($file != ".." && $file != "." && is_file("../assets/addons/" . $file) && @$isaddon[count($isaddon) - 1] == "addon"){
										$i++;
										echo $isaddon[0] . "<br />";
									}
									unset($isaddon);
								}
								closedir($dir);
								*/
								sort($installed_addons);
								foreach($installed_addons as $value){
									echo $value.", ";
								}
							?>
							</em>
                        </span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_version']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_version_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em>
						<?php 
							echo $config['productName'] . " ";
							if(in_array('pro',$installed_addons)) echo "Pro ";
							echo $config['productVersion'] . " " . $config['productType'];
						?></em></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_servinfo']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_servinfo_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><a href="../phpinfo.php?phpinfo" target="_blank"><?php echo $mgrlang['setup_f_opensi']; ?></a></em></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_licagree']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_licagree_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><a href="http://www.ktools.net/purchase.agreement.php" target="_blank"><?php echo $mgrlang['setup_f_openla']; ?></a></em></span>
                    </div>
                    <?php
						//include('../assets/classes/encryption.php');
						//$crypt = new encryption_class;
						//$decrypt_result = $crypt->decrypt($config['settings']['serial_number'],$config['settings']['api_pass']);
						//$errors = $crypt->errors;
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['setup_f_api']; ?>: <br />
                            <span><?php echo $mgrlang['setup_f_api_d']; ?></span>
                        </p>
                        <span class="groupinfo"><em><?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "[" . $mgrlang['gen_hidden'] . "]"; } else { echo k_decrypt($config['settings']['api_pass']); } ?></em></span>
                    </div> 
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            Ioncube: <br />
                        </p>
                        <span class="groupinfo"><em><?php echo $config['ioncubeVersion']; ?></em></span>
                    </div>                   
                </div>
                <!-- ACTIONS BAR AREA -->
                <div id="save_bar">							
                    <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.settings.php?ep=1');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
                </div>
            </div>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTENT CONTAINER -->
        </form>
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>
