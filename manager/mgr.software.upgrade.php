<?php
	###################################################################
	####	MANAGER SOFTWARE UPGRAE PAGE                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-1-2006                                     ####
	####	Modified: 9-23-2008                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "software_upgrade";
		$lnav = "help";
	
		$supportPageID = 0;
	
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
		// MOVED ADD-ON INCLUDE BELOW
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		
		if($_GET['update_perms'] == 1){
			# UPDATE ADMIN PERMISSIONS
			$_SESSION['admin_user']['permissions'] = create_admin_permissions(1);
			unset($comp);
			$auto_install_done = true;
		}
		
		if(!empty($_POST)){
			$extension = explode(".",$_FILES['installer_file']['name']); 
			if($extension[count($extension)-1] == "installer"){				
				move_uploaded_file($_FILES['installer_file']['tmp_name'],"../assets/tmp/" . $_FILES['installer_file']['name']);
				# IF UPLOAD/MOVE WAS SUCCESSFUL THEN INCLUDE THE INSTALL FILE
				if(file_exists('../assets/tmp/' . $_FILES['installer_file']['name'])){
					$instfile = fopen('../assets/tmp/' . $_FILES['installer_file']['name'], 'r');
					$instdata = fread($instfile, 1024);
					$instdata_array = explode("\n",$instdata);					
					//echo trim($instdata_array[2]) . "-" . strrev(strtoupper(md5($config['settings']['serial_number'] . 'contr2'))); exit;
					$writeaddon = write_addon(trim($instdata_array[3]),trim($instdata_array[2]),'../assets/addons/',trim($instdata_array[1]),trim($instdata_array[4]));
					if($writeaddon == 1){
						$success = true;
					# THERE WAS A PROBLEM WITH THE ADD-ON
					} else {
						$instmessage = $writeaddon;
					}
				} else {
					$instmessage = "The upgrade did not complete successfully.";
				}				
				fclose($instfile);
				# DELETE THE INSTALL FILE AFTER THINGS ARE DONE
				if(file_exists('../assets/tmp/' . $_FILES['installer_file']['name'])){ unlink('../assets/tmp/' . $_FILES['installer_file']['name']); }
				
				# UPDATE ADMIN PERMISSIONS
				$_SESSION['admin_user']['permissions'] = create_admin_permissions(1);
				unset($comp);
			} else {
				$instmessage = $mgrlang['softup_mes_01'];
			}
		}
		
		# MOVED LOAD ADD-ONS BELOW SO THAT IT LOADS THEM AFTER THE NEW PERMISSIONS HAVE BEEN CREATED
		require_once('../assets/includes/addons.php'); # INCLUDE MANAGER ADDONS FILE
		
		if($_GET['mes']){
			$vmessage = $mgrlang['softup_mes_02'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_software_upgrade']; ?></title>
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
	
	<script language="javascript">
		function upgrade_submit(){
			window.open('','upgrade','menubar=0,resizable=1,scrollbars=1,width=650,height=550');
			document.upgrade_form.target = 'upgrade';
			document.upgrade_form.submit();
		}
		
		// LOAD THE UPGRADE WINDOW
		function load_upgrade_win(url,pars){
			//var url = "mgr.software.upgrade.actions.php";
			var updatebox = "upgrade_window";
			//var pars;
			//var pars = "set_default=" + id;
			var myAjax = new Ajax.Updater(
				updatebox, 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		// RUN THROUGH THE FILE UPGRADE PROCESS
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
		function upgrade_files(){
			if(stopped != 1){
				if(current_file_number == 1){
					$('file_window').update('Starting...<br />');
					$('current_file_window').setStyle({display: "block"});
					$('status_bar').setStyle({display: "block"});
				}						
				
				var url = "mgr.upgrade.files.php";
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
							
							upgrade_files();
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
		
		// STOP UPGRADE FUNCTION
		function stop_upgrade(){
			stopped = 1;
			$('stop_button').disable();			
			$('start_button').enable();	
			$('cont_button').enable();
			$('start_button').value = 'Resume';		
		}
		// START UPGRADE FUNCTION
		function start_upgrade(){
			stopped = 0;
			$('start_button').disable();
			$('stop_button').enable();
			$('cont_button').disable();		
		}
		
		Event.observe(window, 'load', function() {
			//show_loader('upgrade_window');
			load_upgrade_win('mgr.software.upgrade.actions.php','');
		});
		
		function enable_start(){
			if($('agree_checkbox').checked == true){
				$('start_button').enable();
			} else {
				$('start_button').disable();
			}
		}
		
		// LOAD UPLOAD .INSTALLER WINDOW
		function load_installer(){
			var url = "mgr.software.upgrade.actions.php";
			var updatebox = "installer_window";
			var pars = "pmode=upload_installer";
			//var pars = "set_default=" + id;
			var myAjax = new Ajax.Updater(
				updatebox, 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		function open_marketplace(){
			//overlay_height();
			//Effect.Appear('overlay',{ duration: 0.5, from: 0.0, to: 0.7});
			//$('messagebox').setStyle({
			//	display: "block"
			//});
			//$('innermessage').update("<p style='padding: 5px 0 0 0; margin: 0px; font-weight: bold; line-height: 1.2;'><img src='images/mgr.notice.icon.gif' border='0' align='left' style='margin-right: 10px; margin-top: -5px;' />After you have completed installing your new add-ons please click the continue button.</p><p align='right' style='padding: 0px; margin: 0px; clear: both;'><input type='button' value='Continue' id='closebutton' class='button' onclick='window.location=\"mgr.software.upgrade.php?update_perms=1\"' /></p>");
			// JUMP TO THE TOP OF THE BROWSER WINDOW
			//scroll(0,0);
		}
		
		<?php
			if($success == true or $instmessage or $_GET['update_perms']){
		?>
			Event.observe(window, 'load', function(){
				bringtofront('2');
			});
		<?php
		 	}
		?>
	</script>
	
</head>
<body>
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
            
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.website.settings2.gif" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_software_upgrade']; ?></strong><br /><span><?php echo $mgrlang['subnav_software_upgrade_d']; ?></span></p>
            </div>

            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <!-- -->
                <div id="spacer_bar"></div>
        
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['softup_tab1-']; ?>Upgrade Software</div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2" style="border-right: 1px solid #6b6b6b;"><?php echo $mgrlang['softup_tab1-']; ?>Install Add-ons</div>
                </div>
                <!-- -->
                
                <div id="tab1_group" class="group" style="display: block; overflow:auto;">
                  <div id="upgrade_window" style="margin: 20px; border-bottom: 2px solid #999999; border-top: 1px solid #CCC; border-left: 1px solid #CCC; border-right: 2px solid #999999; padding: 15px; font-size: 12px; background-color: #eeeeee; overflow: auto; background-image: url(images/mgr.upgrade.bg.gif); background-repeat: repeat-x">                        
                        <img src='images/mgr.loader2.gif' align='absmiddle' style='margin: 10px;' /> Connecting to ktools.net to check for the newest version...
               	  </div>
                  <div></div>
             	</div>
                
                <div id="tab2_group" class="group" style="display: none; overflow:auto;">
                    <div id="installer_window" style="margin: 20px; border-bottom: 2px solid #999999; border-top: 1px solid #CCC; border-left: 1px solid #CCC; border-right: 2px solid #999999; padding: 15px; font-size: 12px; background-color: #eeeeee; overflow: auto; background-image: url(images/mgr.upgrade.bg.gif); background-repeat: repeat-x">
                    	<?php
							if($success == true){
								echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Add-on installed successfully (" . trim($instdata_array[0]) . ").</span><br /><br />";
							}
							if($instmessage){
								echo "<img src='images/mgr.notice.icon.white.small.png' align='absmiddle' style='margin-right: 5px;' /><span style='color: #cc0000; font-weight: bold;'>$instmessage</span><br /><br />";
							}
							if($auto_install_done == true){
								echo "<img src='images/mgr.green.check.gif' align='absmiddle' style='margin-right: 5px;' /><span style='color: #379d04; font-weight: bold;'>Any new add-ons you installed should now be ready for use.</span><br /><br />";
							}
							
						?>
                        Please choose which installation method you would like to use to install your add-on.<br /><br />
                        <img src="images/mgr.icon.addon.png" align="absmiddle" /> <a href="#" onClick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "open_marketplace();window.open('http://www.ktools.net/marketplace/index.php?sn=" . $config['settings']['serial_number'] . "&pmode=mypurchases&url=" . $config['base_url'] . "&addons=" . implode(",",$installed_addons) . "', 'psExtras', 'toolbar=no, directories=no, location=no, status=yes, menubar=no, resizable=yes, scrollbars=yes, width=650, height=600'); return false;"; } ?>">Automatically install an add-on from a list of add-ons you have purchased.</a><br />
                    	<img src="images/mgr.icon.addon.png" align="absmiddle" /> <a href="javascript:<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "load_installer();"; } ?>">Install an add-on from an installer file you've downloaded.</a>
                    </div>
             	</div>
                <!-- ACTIONS BAR AREA -->
                <div id="save_bar">							
                    <div style="float: right;"><input type="button" value="Cancel" onclick="cancel_edit('mgr.settings.php?ep=1');" /><!--<input type="submit" value="Done" onclick="cancel_edit('mgr.software.upgrade.php?mes=complete');" />--></div>
                </div>
                
                <div style="border-bottom: 3px solid #949494;"></div>
                <!-- END CONTENT -->
        	</div>
        </div>
        <div class="footer_spacer"></div>
        <!-- END CONTENT CONTAINER -->
        </form>
        <?php include("mgr.footer.php"); ?>					
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>
