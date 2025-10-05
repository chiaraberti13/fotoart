<?php
	###################################################################
	####	PRINTS EDITOR				                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-31-2008                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "prints";
		$lnav = "library";
		
		$supportPageID = '324';
	
		$profile_vars = 1;
	
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
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
	
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$print_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}prints WHERE print_id = '$_GET[edit]'");
			$print_rows = mysqli_num_rows($print_result);
			$print = mysqli_fetch_object($print_result);
			
			$_SESSION['item_id'] = $print->print_id;
			$_SESSION['mgrarea'] = 'print';
		}
		
		if($_GET['edit'] == "new")
		{
			# DELETE ANY ORPHANED ITEM PHOTOS
			delete_orphaned_item_photos('print');
			
			# DELETE ANY ORPHANED OPTION GROUPS
			delete_orphaned_optiongroups();
			
			# DELETE ANY ORPHANED DISCOUNTS
			delete_orphaned_discounts();
			
			# ASSIGN A DEFAULT item_id AND mgrarea
			$_SESSION['item_id'] = 0;
			$_SESSION['mgrarea'] = 'print';
		}
		
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" ){
							
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			# SET PERM
			if(empty($perm) or $perm == 'everyone'){
				$everyone = "1";
			} else {
				$everyone = '0';
			}
			
			$price_clean = $cleanvalues->currency_clean($price);
			$my_cost_clean = $cleanvalues->currency_clean($my_cost);
			$min_contr_price_clean = $cleanvalues->currency_clean($min_contr_price);
			$max_contr_price_clean = $cleanvalues->currency_clean($max_contr_price);
			$commission_dollar_clean = $cleanvalues->currency_clean($commission_dollar);
			
			$addshipping_clean = $cleanvalues->currency_clean($addshipping);
			
			$all_galleries = (count($selected_galleries) > 0) ? '0' : $all_galleries;
			
			# IMPLODE GROUPS
			//$plangroups = ($setgroups) ? "," . implode(",",$setgroups) . "," : "";
			
			if($_REQUEST['action'] == "save_edit"){
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$item_name_val = ${"item_name_" . $value};
					$addsql.= "item_name_$value='$item_name_val',";
					$description_val = ${"description_" . $value};
					$addsql.= "description_$value='$description_val',";
				}
				
				if($attachment == 'none') $all_galleries = 0;	
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}prints SET 
							item_name='$item_name',
							description='$description',";
				$sql.= $addsql;
				$sql.= "	item_code='$item_code',
							price='$price_clean',
							my_cost='$my_cost_clean',
							credits='$credits',
							price_calc='$price_calc',
							credits_calc='$credits_calc',
							taxable='$taxable',
							multiple='$multiple',
							active='$active',
							homepage='$homepage',
							featured='$featured',
							weight='$weight',
							addshipping='$addshipping_clean',
							notify_lab='$notify_lab',
							lab_code='$lab_code',
							everyone='$everyone',
							commission='$commission',
							min_contr_price='$min_contr_price_clean',
							max_contr_price='$max_contr_price_clean',
							min_contr_credits='$min_contr_credits',
							max_contr_credits='$max_contr_credits',							
							contr_sell='$contr_sell',
							commission_type='$commission_type',
							commission_dollar='$commission_dollar_clean',
							notes='$notes',
							all_galleries='$all_galleries',
							attachment='$attachment'
							where print_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# DELETE ITEM GALLERIES FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}item_galleries WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD ITEM GALLERIES
				if($selected_galleries and $attachment != 'none'){
					foreach($selected_galleries as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}item_galleries (mgrarea,item_id,gallery_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# INCLUDE THE CODE TO SAVE THE OPTION GROUPS
				include('mgr.optionsbox.savegroups.php');
				
				# INCLUDE THE CODE TO SAVE THE DISCOUNTS
				include('mgr.discountsbox.save.php');
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_prints'],1,$mgrlang['gen_b_ed'] . " > <strong>$item_name</strong>");
			}
			
			if($_REQUEST['action'] == "save_new"){
				
				$uprint_id = create_unique2();
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$item_name_val = ${"item_name_" . $value};
					$addsqla.= ",item_name_$value";
					$addsqlb.= ",'$item_name_val'";					
					$description_val = ${"description_" . $value};
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}prints (
						item_name,
						description,
						uprint_id,
						item_code,
						price,
						my_cost,
						credits,
						price_calc,
						credits_calc,
						taxable,
						multiple,
						weight,
						addshipping,
						active,
						homepage,
						featured,
						notify_lab,
						lab_code,
						everyone,
						commission,
						min_contr_price,
						max_contr_price,
						min_contr_credits,
						max_contr_credits,
						contr_sell,
						commission_type,
						commission_dollar,
						notes,
						all_galleries,
						attachment";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$item_name',
						'$description',
						'$uprint_id',
						'$item_code',
						'$price_clean',
						'$my_cost_clean',
						'$credits',
						'$price_calc',
						'$credits_calc',
						'$taxable',
						'$multiple',
						'$weight',
						'$addshipping',
						'$active',
						'$homepage',
						'$featured',
						'$notify_lab',
						'$lab_code',
						'$everyone',
						'$commission',
						'$min_contr_price_clean',
						'$max_contr_price_clean',
						'$min_contr_credits',
						'$max_contr_credits',
						'$contr_sell',
						'$commission_type',
						'$commission_dollar_clean',
						'$notes',
						'$all_galleries',
						'$attachment'";
				$sql.= $addsqlb;
				$sql.= ")";				
				$result = mysqli_query($db,$sql);				
				$saveid = mysqli_insert_id($db);
				
				/*
				# SAVE OPTIONGROUPS
				foreach($optiongroup as $key2 => $value2)
				{
					//echo $optiongrpname[$key]."<br />";
					
					# ADD SUPPORT FOR ADDITIONAL LANGUAGES
					foreach($active_langs as $value3){ 
						$name_val = ${"langname_" . $value3}[$key2];
						$addsql2.= "name_$value3='$name_val',";
					}
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}option_grp SET 
								name='".$optiongrpname[$key2]."',
								ltype='".$optiongrpltype[$key2]."',";
					$sql.= $addsql2;
					$sql.= "	active='".$optiongrpactive[$key2]."',
								required='".$optiongrprequired[$key2]."',
								parent_id='$saveid',
								parent_type='$page'
								where og_id  = '$value2'";
					$result = mysqli_query($db,$sql);
					
				}
				*/
				# INCLUDE THE CODE TO SAVE THE OPTION GROUPS
				include('mgr.optionsbox.savegroups.php');
				
				# INCLUDE THE CODE TO SAVE THE DISCOUNTS
				include('mgr.discountsbox.save.php');
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# RENAME ORPHANED ITEM PHOTOS UNDER THIS PRINT
				save_new_item_photos('print',$saveid);
				
				# ADD ITEM GALLERIES
				if($selected_galleries and $attachment != 'none'){
					foreach($selected_galleries as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}item_galleries (mgrarea,item_id,gallery_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_prints'],1,$mgrlang['gen_b_new'] . " > <strong>$item_name</strong>");
			}				
			
			header("location: mgr.prints.php?mes=edit"); exit;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_prints']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
    <!-- LOAD ADDITIONAL STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.optionsbox.css" />
    <!-- LOAD THE SLIDER STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.slider.css" />
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
	<!-- DISCOUNTS BOX JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.discounts.js"></script>
    <!-- LOAD SLIDER CODE -->
    <script type="text/javascript" src="../assets/javascript/slider.js"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>
    
	
	<link rel="stylesheet" href="../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" />
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<style>
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
	</style>
	
	
	
	<script language="javascript" type="text/javascript">	
		// RUN ON PAGE LOAD
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
			
			update_fsrow('tab6_group');
			//bringtofront('2');
			load_gals();
		});
		
		// LOAD PARENT GALLERIES BASED OFF OF PERMISSIONS
		function load_gals()
		{
			show_loader('gals');
			//alert($F('permowner'));
			var pars = 'pmode=galleries&id=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('gals', 'mgr.prints.actions.php', {method: 'get', parameters: pars, onComplete: display_in_all_check});
		}
		
		function display_in_all_check()
		{	
			if($F('all_galleries'))
			{
				$$('#gals .checkbox').each(function(e)
											  {
												e.checked = false;
												e.disable();  
											  });
			}
			else
			{
				$$('#gals .checkbox').each(function(e)
											  {
												e.enable();  
											  });
			}
		}
		
		function form_submitter(){
			// REVERT BACK
			$('item_name_div').className='fs_row_off';

			// CHECK FOR OPTION NAME
			//var curoption = 0;
			//var optionerror = 0;
			//$$('input.print_option_name').each(
			//	function (){
			//		if($F($$('input.print_option_name')[curoption]) == "" || $F($$('input.print_option_name')[curoption]) ==  null){
			//			optionerror = 1;
			//		}
			//		curoption++;
			//	}				
			//);		
			//if(optionerror == 1){
			//	simple_message_box('<?php echo $mgrlang['prints_mes_04']; ?>','');
			//	return false;
			//}
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.prints.edit.php?action=save_new" : "mgr.prints.edit.php?action=save_edit";
			
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("item_name","prints_f_name",1);
				}
			?>
		}
		
		function quantity_box(onoff){
			if(onoff == '0'){
				$('quantity').setStyle({display: 'none'});
				$('quantity').value='unlimited';
			}
			if(onoff == '1'){
				$('quantity').setStyle({display: 'block'});
				$('quantity').value='';
			}
		}
		
		// THE COMMISSION TYPE BOX HAS BEEN CHANGED
		function commission_type_change()
		{
			var selecteditem = $('commission_type').options[$('commission_type').selectedIndex].value;
			
			if(selecteditem == '1')
			{
				show_div('com_percentage');
				hide_div('com_dollar');
				update_fsrow('tab6_group');
			}
			else
			{
				hide_div('com_percentage');
				show_div('com_dollar');
				update_fsrow('tab6_group');
			}
		}
		
		// Java File Uploader
		function getUploader()
		{
			return document.jumpLoaderApplet.getUploader();
		}
		
		function getUploaderConfig()
		{
			return document.jumpLoaderApplet.getUploaderConfig();
		}
		
		function getUploadView()
		{
			return getMainView().getUploadView();
		}
		
		function getMainView()
		{
			return getApplet().getMainView();
		}
		
		function getApplet()
		{
			return document.jumpLoaderApplet;
		}
		
		function getViewConfig()
		{
			return getApplet().getViewConfig();
		}
		
		function startJavaUpload( index )
		{
		var error = getUploader().startUpload();
			if( error != null )
			{
				//alert( error );
			}
		}
		
		function stopJavaUpload( index )
		{
			var error = getUploader().stopUpload();
			if( error != null )
			{
				//alert( error );
			}
		}
		
		function uploaderFileAdded( uploader, file )
		{
			$('start_button').enable();
		}
		
		function uploaderFileStatusChanged( uploader, file )
		{ 
			var status = file.getStatus(); 
			if(status == 2)
			{ 
				if ((file.getIndex()+1) == uploader.getFileCount())
				{
					close_workbox();
					load_ip();
				}
			} 
		}
		
		function uploaderFileRemoved( uploader, file )
		{
			if(uploader.getFileCount() == 0)
			{
				$('start_button').disable();
			}
		}
		
		function disable_start_button()
		{
			$('start_button').disable();
		}
		
		uploadobj = {};
		<?php
			switch($config['settings']['uploader'])
			{
				default;
				case "1":
					echo "uploadobj.mode = 'java_upload';";
				break;
				case "2":
					echo "uploadobj.mode = 'flash_upload';";
				break;
				case "3":
					echo "uploadobj.mode = 'html_upload';";
				break;
				case "4":
					echo "uploadobj.mode = 'plupload';";
				break;
			}	
		?>
		uploadobj.id = '<?php echo $print->print_id; ?>'; // THE ID OF THE ITEM
		uploadobj.page = 'item_photos'; // WHICH PAGE/AREA THE UPLOADS ARE HAPPENING FROM
		uploadobj.mgrarea = '<?php echo $_SESSION['mgrarea']; ?>'; // MGR AREA UPLOADING PHOTOS

		// CHECK TO MAKE SURE NO MORE THAN X PHOTOS HAVE ALREADY BEEN UPLOADED
		function open_upload_box()
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					echo "return false;";
				}
				else
				{
			?>
				var ip_length = $$('.ip_div').length;
				
				uploadobj.maxfiles = <?php echo $config['MaxProductShots']; ?> - ip_length;
				
				//alert(uploadobj.maxfiles);
				
				if(ip_length < <?php echo $config['MaxProductShots']; ?>)
				{
					<?php
						if($config['settings']['uploader'] == '4xxx')
							echo "window.open('mgr.plupload.php?itemPhotos=1&mgrarea={$_SESSION[mgrarea]}&id={$print->print_id}', 'Plupload', 'width=800,height=380,scrollbars=yes,menubar=no,titlebar=no');";
						else
							echo "workbox(uploadobj);";
					?>
				}
				else
				{
					simple_message_box('<?php echo addslashes($mgrlang['max_prod_shots']); ?>','')
				}
			<?php
				}
			?>
		}
		
		<?php
			if($config['settings']['uploader'] == '4xxx')
			{
		?>
			Event.observe(window, 'focus', function()
			{
				load_ip();			
			});
		<?php
			}
		?>
		
		// LOAD PRODUCT SHOTS
		function load_ip()
		{
			show_loader('ip_div');
			//alert($F('permowner'));
			var pars = 'pmode=display_ip_list&id=<?php echo $_SESSION['item_id']; ?>';
			var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars});
		}
		
		// DO DELETE PRODUCT SHOTS
		function do_delete_ip(ip_id)
		{
			//show_loader('ip_div');
			//alert($F('permowner'));
			Effect.Fade('ip_'+ip_id,{ duration: 0.5 });
			
			setTimeout(function(){
					var pars = 'pmode=delete_ip&print=<?php echo $_SESSION['item_id']; ?>&ip_id='+ip_id;
					//var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars, evalScripts: true});
					new Ajax.Request('mgr.prints.actions.php', {method: 'get', parameters: pars, onSuccess: function() {
						$('ip_'+ip_id).remove();
					} });					
				},500);
		}
		
		// DELETE PRODUCT SHOT
		function delete_ip(ip_id){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				<?php
					// IF VERIFT BEFORE DELETE IS ON
					if($config['settings']['verify_before_delete'])
					{
				?>
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_ip(\""+ip_id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_ip(ip_id);";
					}
				?>
			}
		}
		
		function attachment_update()
		{
			if($F('attachment') == 'none')
			{
				$('gals_div').hide();
			}
			else
			{
				$('gals_div').show();
			}
		}
		
		// Flash upload is complete. Close workbox and refresh product shots window.
		function flashUploadComplete()
		{
			load_ip();
			close_workbox();
		}
	</script>
	<?php include('mgr.optionsbox.js.php'); ?>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # CHECK FOR DEMO MODE
            //demo_message($_SESSION['admin_user']['admin_id']);					
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.prints.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['prints_new_header'] : $mgrlang['prints_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['prints_new_message'] : $mgrlang['prints_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div> 
                <?php
					# PULL GROUPS
					$print_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$print_group_rows = mysqli_num_rows($print_group_result);
				?>      
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['gen_tab_details']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('8');" id="tab8"><?php echo $mgrlang['gen_tab_pricing']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('9');" id="tab9"><?php echo $mgrlang['gen_tab_attach']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('7');load_ip();" id="tab7"><?php echo $mgrlang['gen_tab_prod_shots']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');load_optionsbox();" id="tab2"><?php echo $mgrlang['gen_tab_options']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['gen_tab_shipping']; ?></div>  
					<?php if(in_array("proXXX",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('11');loadDiscountsBox();" id="tab11"><?php echo $mgrlang['gen_tab_discounts']; ?></div><?php } ?>                 
                    <?php if($print_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['gen_tab_groups']; ?></div><?php } ?>
                    <?php if($config['settings']['cart'] and in_array("contr",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('6');" id="tab6"><?php echo $mgrlang['gen_tab_contributors']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('10');" id="tab10"><?php echo $mgrlang['tab_advertise']; ?></div> 
                    <div class="subsuboff" onclick="bringtofront('5');" id="tab5" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
               	</div>
                
				<?php if(in_array("pro",$installed_addons)){ ?>
				<?php $row_color = 0; ?>
					<div id="tab11_group" class="group" style="padding: 20px 20px 20px 20px;">  
						<div style="text-align: left; margin-bottom: 10px;"><a href="javascript:addDiscountRange();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
						<div id="discounts_div" itemType="<?php echo $page; ?>" itemID="<?php echo $_GET['edit']; ?>" style="font-size: 12px; overflow: auto; border: 1px dotted #CCC; padding: 1px;"></div>
					</div>
				<?php } ?>
				
                <?php $row_color = 0; ?>
                <div id="tab10_group" class="group">  
                	<div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['hpprints'] == 0){
						?>
                            <div style="position: absolute; margin: 0 0 0 300px; vertical-align: middle">
                                <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['gen_hpfeaturearea']; ?></div>
                            </div>
                        <?php
							}
						?>
						
						<img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_adv_hp']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_adv_hp_d']; ?></span>
                        </p>
                        <input type="checkbox" name="homepage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->homepage){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['printpage'] == 0){
						?>
                            <div style="position: absolute; margin: 0 0 0 300px; vertical-align: middle">
                                <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['gen_featurepage']; ?></div>
                            </div>
                        <?php
							}
						?>
                        
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_adv_fp']; ?>:<br />
                        	<span><?php echo $mgrlang['prints_f_adv_d']; ?></span>
                        </p>
                        <input type="checkbox" name="featured" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->featured){ echo "checked"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding-bottom: 5px;">                    
                    <div class="<?php fs_row_color(); ?>" id="item_name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['prints_f_name']; ?>:<br />
                        	<span><?php echo $mgrlang['prints_f_name_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <input type="text" name="item_name" id="item_name" style="width: 290px;" maxlength="50" value="<?php echo @htmlentities(stripslashes($print->item_name)); ?>" />
                            <?php
								if(in_array('multilang',$installed_addons)){
							?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
								<!--<br /><a href="javascript:displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.gif" id="plusminus01" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_title','','','','plusminus-01');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />-->
								<div id="lang_title" style="display: none;">
								<ul>
								<?php
									foreach($active_langs as $value){
								?>
									<li><input type="text" name="item_name_<?php echo $value; ?>" id="item_name_<?php echo $value; ?>" style="width: 290px;" maxlength="100" value="<?php echo @htmlentities(stripslashes($print->{"item_name" . "_" . $value})); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
							<?php
									}
									echo "</ul></div>";
								}
							?>
                    	</div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_description']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['prints_f_desc_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($print->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons))
                                {
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <!--<br /><a href="javascript:displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.gif" id="plusminus02" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_description','','','','plusminus-02');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />-->
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value)
                                    {
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($print->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_active']; ?>:<br />
                            <span><?php echo $mgrlang['gen_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->active or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
            	</div>     
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">
                	<?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['prints_f_price']; ?>:<br />
                                <span><?php echo $mgrlang['prints_f_price_d']; ?></span><br /><br />
                            </p>
                            <div style="float: left;">
								<?php
									if($config['settings']['flexpricing'] == 1){
								?>
								<select id="price_calc" name="price_calc" style="width: 140px;">
									<option value="norm" <?php if(@$print->price_calc == 'norm'){ echo "selected"; } ?>><?php echo $config['settings']['cur_denotation']; ?></option>
									<option value="add" <?php if(@$print->price_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( + )</option>
									<option value="sub" <?php if(@$print->price_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( - )</option>
									<option value="mult" <?php if(@$print->price_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photoprice']; ?> ( x )</option>
								</select>
								<?php
									} else {
										echo "<input type='hidden' name='price_calc' value='norm' />";
									}
								?>                        
                            	<input type="text" name="price" id="price" class="priceInput" style="width: 90px;" maxlength="50" onblur="update_input_cur('price');" value="<?php if($print->price > 0) { echo @$cleanvalues->currency_display($print->price); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                            	<div class="leaveBlankMessage"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></div>
							</div>
                        </div>
                        <?php
							/*
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['prints_f_my_cost']; ?>:<br />
                                <span><?php echo $mgrlang['prints_f_my_cost_d']; ?></span>
                            </p>
                            <input type="text" name="my_cost" id="my_cost" style="width: 80px;" maxlength="50" onblur="update_input_cur('my_cost');" value="<?php echo @$cleanvalues->currency_display($print->my_cost); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                        </div>
							*/
						?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['prints_f_taxable']; ?>:<br />
                                <span><?php echo $mgrlang['prints_f_taxable_d']; ?></span>
                            </p>
                            <input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->taxable or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                        </div> 
                    <?php
						}
						if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
						{
					?>
                    	<div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['prints_f_credits']; ?>:<br />
                                <span><?php echo $mgrlang['prints_f_credits_d']; ?></span><br /><br />
                            </p>
                            <div style="float: left;">
								<?php
									if($config['settings']['flexpricing'] == 1){
								?>
								<select id="credits_calc" name="credits_calc" style="width: 140px;">
									<option value="norm" <?php if(@$print->credits_calc == 'norm'){ echo "selected"; } ?>></option>
									<option value="add" <?php if(@$print->credits_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( + )</option>
									<option value="sub" <?php if(@$print->credits_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( - )</option>
									<option value="mult" <?php if(@$print->credits_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_photocredits']; ?> ( x )</option>
								</select>
								<?php
									} else {
										echo "<input type='hidden' name='credits_calc' value='norm' />";
									}
								?> 
								<input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" value="<?php echo $cleanvalues->number_display($print->credits); ?>" />
								<div class="leaveBlankMessage"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></div>
							</div>
                        </div>
                    <?php
						}
					?>                    
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['prints_f_multiple']; ?>:<br />
                            <span><?php echo $mgrlang['prints_f_multiple_d']; ?></span>
                        </p>
                        <input type="checkbox" name="multiple" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->multiple or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="padding: 20px 20px 20px 20px;">
                    <?php $id = $print->print_id; ?>
                    <div style="text-align: left; margin-bottom: 10px;"><a href="javascript:add_optiongrp();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    <div id="optionsbox" style="border: 1px dotted #CCC; padding: 1px; display: none;"></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['prints_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['prints_f_groups_d']; ?></span>
                        </p>
                        <?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$print_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$print->print_id' AND item_id != 0");
							while($print_groupids = mysqli_fetch_object($print_groupids_result)){
								$plangroups[] = $print_groupids->group_id;
							}
							
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($print_group = mysqli_fetch_object($print_group_result)){
								echo "<li><input type='checkbox' id='$print_group->gr_id' class='permcheckbox' name='setgroups[]' value='$print_group->gr_id' "; if(in_array($print_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($print_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' /> "; } echo "<label for='$print_group->gr_id'>".substr($print_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
						?>
                    </div>
            	</div> 
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
                	<?php
						/*
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['prints_f_shipping']; ?>:<br />
                        	<span><?php echo $mgrlang['prints_f_shipping_d']; ?></span>
                        </p>
                        
                    </div>
						*/
					?>
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_weight']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_weight_d']; ?></span>
                        </p>
                        <input type="text" name="weight" id="weight" style="width: 100px;" maxlength="50" value="<?php echo @stripslashes($print->weight); ?>" />
                        <span class="mtag_grey" style="font-weight: bold; color: #fff;"><?php $sel_lang = $config['settings']['lang_file_mgr']; if($config['settings']['weight_tag_' . $sel_lang]){ echo $config['settings']['weight_tag_' . $sel_lang]; } else { echo $config['settings']['weight_tag']; } ?></span>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_add_ship']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_add_ship_d']; ?></span>
                        </p>
                        <input type="text" name="addshipping" id="addshipping" style="width: 100px;" maxlength="50" onblur="update_input_cur('addshipping');" value="<?php if($print->addshipping > 0){ echo $cleanvalues->currency_display($print->addshipping); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_itemcode']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_itemcode_d']; ?></span>
                        </p>
                        <input type="text" name="item_code" id="item_code" style="width: 80px;" maxlength="50" value="<?php echo @stripslashes($print->item_code); ?>" />
                    </div>
					<!--
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_lab']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_lab_d']; ?></span>
                        </p>
                        <select class='select' name='notify_lab' style="width: 298px; margin-top: 12px;">
							<option><?php echo $mgrlang['gen_none']; ?></option>
							<?php
								# SELECT LABS
                        		$lab_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}lab_contacts ORDER BY lab_id");
								$lab_rows = mysqli_num_rows($lab_result);
	                        	while($lab = mysqli_fetch_object($lab_result)){
									echo "<option value='$lab->lab_id'";
									if($lab->lab_id == $print->notify_lab){ echo " selected"; }
									echo ">$lab->name ($lab->email)</option>";
								}
							?>
						</select>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_lab_code']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_lab_code_d']; ?></span>
                        </p>
                        <input type="text" name="lab_code" id="lab_code" style="width: 80px;" maxlength="50" value="<?php echo @stripslashes($print->lab_code); ?>" />
                    </div>
                    -->
                    <?php
						if(in_array("pro",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>">
					<?php
						}
						else
						{
							echo "<div style='display: none;'>";	
						}
					?>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_f_perm']; ?>: <br />
                            <span><?php echo $mgrlang['gen_f_perm_d']; ?></span>
                        </p>
                        <?php
							if($_GET['edit'] != 'new' and $print->everyone == '0'){
								$perms_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}perms WHERE perm_area = '$page' AND item_id = '$_GET[edit]'");
								$perms_rows = mysqli_num_rows($perms_result);
								if($perms_rows){
									while($perms = mysqli_fetch_object($perms_result)){
										$perm_value.= ','.$perms->perm_value;
									}
								} else {
									$perm_value = 'everyone';
								}
							} else {
								$perm_value = 'everyone';
							}
						?>
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($print->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($print->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $print->print_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $print->print_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" align="middle" /></a></div>
                    </div> 
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['int_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['int_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" id="notes" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($print->notes); ?></textarea>
                    </div>
                </div>
                
                <?php
                	$row_color = 0;
                	if($config['settings']['cart'] and in_array("contr",$installed_addons))
                    {
                ?>
                	<div id="tab6_group" class="group">                	
						<div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_contr_sell']; ?>:<br />
                                <span><?php echo $mgrlang['gen_contr_sell_d']; ?></span>
                            </p>
                            <input type="checkbox" name="contr_sell" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($print->contr_sell){ echo "checked"; } ?> onclick="$('contr_settings').toggle()" />
                        </div>
                        <div id="contr_settings" style="display: <?php if($print->contr_sell){ echo "blcok"; } else { echo "none"; } ?>;">
                            <div class="<?php fs_row_color(); ?>" fsrow='1'>
                                <img src="images/mgr.ast.off.gif" class="ast" />
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['gen_com_type']; ?>:<br />
                                    <span><?php echo $mgrlang['gen_com_type_d']; ?></span>
                                </p>
                                <select style="width: 200px;" id="commission_type" name="commission_type" onchange="commission_type_change();">
                                    <option value="1" <?php if($print->commission_type == '1' or $_GET['edit'] == "new"){ echo "selected"; } ?>><?php echo $mgrlang['percentage']; ?></option>
                                    <option value="2" <?php if($print->commission_type == '2'){ echo "selected"; } ?>><?php echo $mgrlang['dollar_value']; ?></option>
                                </select>
                            </div>
                            <div class="<?php fs_row_color(); ?>" id="com_dollar" fsrow='1' style="<?php if($print->commission_type == '1' or $_GET['edit'] == "new"){ echo "display: none;"; } ?>">
                                <img src="images/mgr.ast.off.gif" class="ast" />
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['gen_com_value']; ?>:<br />
                                    <span><?php echo $mgrlang['gen_com_value_d']; ?></span>
                                </p>
                                <input type="text" name="commission_dollar" id="commission_dollar" onblur="update_input_cur('commission_dollar');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($print->commission_dollar); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                            </div>
                            <div class="<?php fs_row_color(); ?>" fsrow='1' id="com_percentage" style="<?php if($print->commission_type == '2'){ echo "display: none;"; } ?>">
                                <img src="images/mgr.ast.off.gif" class="ast" />
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['gen_com_level']; ?>:<br />
                                    <span><?php echo $mgrlang['gen_com_level_d']; ?></span>
                                </p>
                                <?php
                                    # SLIDER POSITION
                                    //$config['settings']['avatar_size']
                                    $sb_multiplier = (135/100);
                                    
                                    $commission = ($_GET['edit'] == 'new') ? 0 : $print->commission;
                                    
                                    $sb_position = round($commission*$sb_multiplier);
                                ?>
                                <div style="margin-top: 10px;">
                                    <div class="carpe_horizontal_slider_track" style="width: 145px">
                                        <div class="carpe_slider_slit" style="width: 140px">&nbsp;</div>
                                        <div class="carpe_slider"
                                            id="commission_slider"
                                            orientation="horizontal"
                                            distance="135"
                                            display="disthumbslider"
                                            style="left: <?php echo $sb_position; ?>px;" >&nbsp;</div><!-- HERE IS WHERE YOU CAN DEFINE THE STARTING POINT -->
                                    </div>
                                    <div class="carpe_slider_display_holder" style="display: inline; white-space: nowrap">
                                        <input class="carpe_slider_display"
                                            id="disthumbslider"
                                            name="commission"
                                            type="text" 
                                            from="0" 
                                            to="100" 
                                            valuecount="60"
                                            value="<?php echo $commission; ?>"
                                            name="avatar_size" 
                                            typelock="off"
                                            slide_action="preview"
                                            drop_action="render_preview" />&nbsp;%
                                    </div>
                                </div>
                            </div>
							<?php
                                if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
                                {
								/*
                            ?>
                                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                                	<div style="position: absolute; margin: 0 0 0 580px; vertical-align: middle">
                                        <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 17px 0px 0px -8px;' /><div class='notes'><?php echo $mgrlang['prints_mes_05']; ?></div>
                                    </div>
                                
                                    <img src="images/mgr.ast.off.gif" class="ast" />
                                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                        <?php echo $mgrlang['gen_mmprice']; ?>:<br />
                                        <span><?php echo $mgrlang['gen_mmprice_d']; ?></span>
                                    </p>
                                    <div style="float: left;  font-size: 11px;"><strong><?php echo $mgrlang['min']; ?></strong><br /><input type="text" name="min_contr_price" id="min_contr_price" onblur="update_input_cur('min_contr_price');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($print->min_contr_price); ?>" /></div>
                                    <div style="float: left;  font-size: 11px; margin-left: 5px;"><strong><?php echo $mgrlang['max']; ?></strong><br /><input type="text" name="max_contr_price" id="max_contr_price" onblur="update_input_cur('max_contr_price');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($print->max_contr_price); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?></div>
                                </div>
                            <?php
								*/
                                }
                                if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
                                {
								/*
                            ?>
                                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                                    <div style="position: absolute; margin: 0 0 0 580px; vertical-align: middle">
                                        <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 17px 0px 0px -8px;' /><div class='notes'><?php echo $mgrlang['prints_mes_05']; ?></div>
                                    </div>
                                    
                                    <img src="images/mgr.ast.off.gif" class="ast" />
                                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                        <?php echo $mgrlang['gen_mmcredits']; ?>:<br />
                                        <span><?php echo $mgrlang['gen_mmcredits_d']; ?></span>
                                    </p>
                                    <div style="float: left;  font-size: 11px;"><strong><?php echo $mgrlang['min']; ?></strong><br /><input type="text" name="min_contr_credits" id="min_contr_credits" style="width: 70px;" maxlength="50" value="<?php echo $cleanvalues->number_display($print->min_contr_credits); ?>" /></div>
                                    <div style="float: left;  font-size: 11px; margin-left: 5px;"><strong><?php echo $mgrlang['max']; ?></strong><br /><input type="text" name="max_contr_credits" id="max_contr_credits" style="width: 70px;" maxlength="50" value="<?php echo $cleanvalues->number_display($print->max_contr_credits); ?>" /></div>
                                </div>
                            <?php
								*/
                                }
                            ?>
                        </div>
                	</div>
                <?php
					}						
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group" style="padding: 20px 20px 20px 20px;">
	                <div style="text-align: left; margin-bottom: 10px;"><a href="javascript:open_upload_box();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    <div id="ip_div" style="font-size: 11px; overflow: auto;">product shots here</div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab9_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['attachment']; ?>:<br />
                        	<span><?php echo $mgrlang['prints_f_attach_d']; ?></span>
                        </p>
                        <select name="attachment" id="attachment" onchange="attachment_update();">
                        	<option value="none" <?php if($print->attachment == 'none'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_none']; ?></option>
                            <option value="galleries" <?php if($print->attachment == 'galleries'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_sel_gal']; ?></option>
                            <option value="media" <?php if($print->attachment == 'media'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_media_in_gal']; ?></option>
                            <option value="both" <?php if($print->attachment == 'both'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_both']; ?></option>
                        </select>
                    </div> 
                    <div class="<?php fs_row_color(); ?>" id="gals_div" style="display: <?php if($print->attachment == 'none' or $_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; } ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['galleries']; ?>:<br />
                        	<span><?php echo $mgrlang['galleries_attach']; ?></span>
                        </p>
                        <div style="float: left; width: 415px;">
                        	<div name="gals" id="gals" style="border: 1px solid #d9d9d9; font-size: 11px; padding: 5px;"></div>
                        </div>
                    </div>                  
                </div>
                             
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.prints.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>        
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>