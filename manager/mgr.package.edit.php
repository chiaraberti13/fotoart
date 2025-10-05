<?php
	###################################################################
	####	PACKAGES EDITOR				                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-31-2008                                     ####
	####	Modified: 6-25-2008                                    #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "packages";
		$lnav = "library";
		
		$supportPageID = '328';
	
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
			$package_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}packages WHERE pack_id = '$_GET[edit]'");
			$package_rows = mysqli_num_rows($package_result);
			$package = mysqli_fetch_object($package_result);
			
			$_SESSION['item_id'] = $package->pack_id;
			$_SESSION['mgrarea'] = 'pack';
			
			$printarray = array();
			$prodarray = array();
			$subarray = array();
			$collarray = array();
			
			$package_items_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}package_items WHERE pack_id = '$_GET[edit]'");
			$package_items_rows = mysqli_num_rows($package_items_result);
			while($package_items = mysqli_fetch_object($package_items_result))
			{
				switch($package_items->item_type)
				{
					case "print":
						$printarray[$package_items->item_id] = $package_items->iquantity;
						$printarrayGroup[$package_items->item_id] = $package_items->groupmult;
					break;
					case "prod":
						$prodarray[$package_items->item_id] = $package_items->iquantity;
						$prodarrayGroup[$package_items->item_id] = $package_items->groupmult;
					break;
					case "sub":
						$subarray[] = $package_items->item_id;
					break;
					case "coll":
						$collarray[] = $package_items->item_id;
					break;
				}
			}
		}
		
		if($_GET['edit'] == "new")
		{
			# DELETE ANY ORPHANED ITEM PHOTOS
			delete_orphaned_item_photos('pack');
			
			# DELETE ANY ORPHANED DISCOUNTS
			delete_orphaned_discounts();
			
			# ASSIGN A DEFAULT item_id AND mgrarea
			$_SESSION['item_id'] = 0;
			$_SESSION['mgrarea'] = 'pack';
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
			$addshipping_clean = $cleanvalues->currency_clean($addshipping);
			
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
				$sql = "UPDATE {$dbinfo[pre]}packages SET 
							item_name='$item_name',
							description='$description',";
				$sql.= $addsql;			
				$sql.= "	active='$active',
							homepage='$homepage',
							featured='$featured',
							pack_code='$pack_code',
							weight='$weight',
							price='$price_clean',
							my_cost='$my_cost_clean',
							everyone='$everyone',
							taxable='$taxable',
							all_galleries='$all_galleries',
							addshipping='$addshipping_clean',
							allowoptions='$allowoptions',
							credits='$credits',
							quantity='$quantity',
							notes='$notes',
							attachment='$attachment',
							notify_lab='$notify_lab'							
							where pack_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				
				# DELETE OLD PACKAGE ITEMS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}package_items WHERE pack_id = '$saveid'");
				# ADD PACKAGE ITEMS
				if($packitemid)
				{
					foreach($packitemid as $key => $value)
					{
						if($packitemquantity[$key] > 0)
						{
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity,groupmult) VALUES ('$saveid','$value','".$packitemtype[$key]."','".$packitemquantity[$key]."','".$packitemgroupmult[$packitemtype[$key].'-'.$value]."')");
						}
					}
				}
				
				
				# ADD SUBSCRIPTION IF ANY WAS CHOSEN
				if($packageSubscription)
					mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity) VALUES ('$saveid','$packageSubscription','sub','1')");
				
				
				# ADD COLLECTIONS IF ANY WAS CHOSEN
				if($packageCollection)
				{
					foreach($packageCollection as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity) VALUES ('$saveid','$value','coll','1')");
					}
				}
				
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
				
				# INCLUDE THE CODE TO SAVE THE DISCOUNTS
				include('mgr.discountsbox.save.php');
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_packages'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				$headerloc = "mgr.packages.php?mes=edit";
				
				//$save_parent_pack = $saveid;
			}
			
			if($_REQUEST['action'] == "save_new"){
				
				$upack_id = create_unique2();
				
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
				$sql = "INSERT INTO {$dbinfo[pre]}packages(
						item_name,
						description,
						upack_id,
						pack_code,
						weight,
						price,
						my_cost,
						taxable,
						credits,
						quantity,
						notify_lab,
						active,
						all_galleries,
						homepage,
						featured,
						allowoptions,
						addshipping,
						notes,
						everyone,
						attachment";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$item_name',
						'$description',
						'$upack_id',
						'$pack_code',
						'$weight',
						'$price_clean',
						'$my_cost_clean',
						'$taxable',
						'$credits',
						'$quantity',
						'$notify_lab',
						'$active',
						'$all_galleries',
						'$homepage',
						'$featured',
						'$allowoptions',
						'$addshipping_clean',
						'$notes',
						'$everyone',
						'$attachment'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);
				
				$saveid = mysqli_insert_id($db);
				
				# ADD PACKAGE ITEMS
				if($packitemid)
				{
					foreach($packitemid as $key => $value)
					{
						if($packitemquantity[$key] > 0)
						{
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity,groupmult) VALUES ('$saveid','$value','".$packitemtype[$key]."','".$packitemquantity[$key]."','".$packitemgroupmult[$packitemtype[$key].'-'.$value]."')");
						}
					}
				}
				
				
				# ADD SUBSCRIPTION IF ANY WAS CHOSEN
				if($packageSubscription)
					mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity) VALUES ('$saveid','$packageSubscription','sub','1')");
				
				
				# ADD COLLECTIONS IF ANY WAS CHOSEN
				if($packageCollection)
				{
					foreach($packageCollection as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}package_items (pack_id,item_id,item_type,iquantity) VALUES ('$saveid','$value','coll','1')");
					}
				}
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# ADD ITEM GALLERIES
				if($selected_galleries and $attachment != 'none'){
					foreach($selected_galleries as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}item_galleries (mgrarea,item_id,gallery_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# RENAME ORPHANED ITEM PHOTOS UNDER THIS PRINT
				save_new_item_photos('package',$saveid);
				
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# INCLUDE THE CODE TO SAVE THE DISCOUNTS
				include('mgr.discountsbox.save.php');
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_packages'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				$headerloc = "mgr.packages.php?mes=new";				
			}
			header("location: $headerloc"); exit;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_packages']; ?></title>
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
	<!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
	<link rel="stylesheet" href="../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" />
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<style>
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
	</style>
	
	<!-- JAVASCRIPT FOR LIST -->
	<script type="text/javascript" src="mgr.profilelogic.js"></script>
	<script language="javascript" type="text/javascript">	
		function form_submitter(){
			// REVERT BACK
			$('name_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.package.edit.php?action=save_new" : "mgr.package.edit.php?action=save_edit";
			
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","packages_f_name",1);
				}
			?>
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
			
			load_gals();
		});
		
		// LOAD PARENT GALLERIES BASED OFF OF PERMISSIONS
		function load_gals()
		{
			show_loader('gals');
			//alert($F('permowner'));
			var pars = 'mode=galleries&id=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('gals', 'mgr.packages.actions.php', {method: 'get', parameters: pars, onComplete: display_in_all_check});
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
		uploadobj.id = '<?php echo $package->pack_id; ?>'; // THE ID OF THE ITEM
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
							echo "window.open('mgr.plupload.php?itemPhotos=1&mgrarea={$_SESSION[mgrarea]}&id={$package->pack_id}', 'Plupload', 'width=800,height=380,scrollbars=yes,menubar=no,titlebar=no');";
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
		
		// LOAD PARENT GALLERIES BASED OFF OF PERMISSIONS
		function load_gals()
		{
			show_loader('gals');
			//alert($F('permowner'));
			var pars = 'mode=galleries&id=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('gals', 'mgr.packages.actions.php', {method: 'get', parameters: pars});
		}
		
		
		// LOAD PRODUCT SHOTS
		function load_ip()
		{
			show_loader('ip_div');
			//alert($F('permowner'));
			var pars = 'mode=display_ip_list&id=<?php echo $_SESSION['item_id']; ?>';
			var myAjax = new Ajax.Updater('ip_div', 'mgr.packages.actions.php', {method: 'get', parameters: pars});
		}
		
		// DO DELETE PRODUCT SHOTS
		function do_delete_ip(ip_id)
		{
			//show_loader('ip_div');
			//alert($F('permowner'));
			Effect.Fade('ip_'+ip_id,{ duration: 0.5 });
			
			setTimeout(function(){
					var pars = 'mode=delete_ip&pack=<?php echo $_SESSION['item_id']; ?>&ip_id='+ip_id;
					//var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars, evalScripts: true});
					new Ajax.Request('mgr.packages.actions.php', {method: 'get', parameters: pars, onSuccess: function() {
						$('ip_'+ip_id).remove();
					} });					
				},500);
		}
		
		// DELETE ORDER ITEM
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
                <img src="./images/mgr.badge.packages.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['packages_new_header'] : $mgrlang['packages_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['packages_new_message'] : $mgrlang['packages_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div> 
                <?php
					# PULL GROUPS
					$pack_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$pack_group_rows = mysqli_num_rows($pack_group_result);
				?>       
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['packages_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('12');" id="tab12"><?php echo $mgrlang['tab_pricing']	; ?></div>
                    <div class="subsuboff" onclick="bringtofront('13');display_in_all_check();" id="tab13"><?php echo $mgrlang['gen_tab_attach']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('6');load_ip();" id="tab6"><?php echo $mgrlang['tab_prod_shots']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['packages_tab2']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['packages_tab3']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('10');" id="tab10"><?php echo $mgrlang['digital_collections']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('11');" id="tab11"><?php echo $mgrlang['gen_subs']; ?></div>
                    <!--<div class="subsuboff" onclick="bringtofront('9');" id="tab9">Options</div>-->
                    <div class="subsuboff" onclick="bringtofront('7');" id="tab7"><?php echo $mgrlang['gen_tab_shipping']; ?></div>
					<?php if(in_array("proXXX",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('14');loadDiscountsBox();" id="tab14"><?php echo $mgrlang['gen_tab_discounts']; ?></div><?php } ?>                 
                    <?php if($pack_group_rows){ ?><div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['packages_tab5']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['tab_advertise']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('8');" id="tab8" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
                </div>
                
				<?php if(in_array("pro",$installed_addons)){ ?>
				<?php $row_color = 0; ?>
					<div id="tab14_group" class="group" style="padding: 20px 20px 20px 20px;">  
						<div style="text-align: left; margin-bottom: 10px;"><a href="javascript:addDiscountRange();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
						<div id="discounts_div" itemType="<?php echo $page; ?>" itemID="<?php echo $_GET['edit']; ?>" style="font-size: 12px; overflow: auto; border: 1px dotted #CCC; padding: 1px;"></div>
					</div>
				<?php } ?>
				
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding-bottom: 5px;">                    
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_name']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_name_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <input type="text" name="item_name" id="item_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($package->item_name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="item_name_<?php echo $value; ?>" id="item_name_<?php echo $value; ?>" style="width: 290px;" maxlength="100" value="<?php echo @stripslashes($package->{"item_name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
                            <span class="input_label_subtext"><?php echo $mgrlang['packages_f_desc_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($package->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($package->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
							<?php echo $mgrlang['packages_f_quantity']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_quantity_d']; ?></span>
                        </p>
                        <input type="text" name="quantity" id="quantity" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($package->quantity); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_active']; ?>:<br />
                            <span><?php echo $mgrlang['packages_f_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($package->active or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab12_group" class="group">
                	<?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['packages_f_price']; ?>:<br />
                                <span><?php echo $mgrlang['packages_f_price_d']; ?></span><br /><br />
                            </p>
                            <input type="text" name="price" id="price" style="width: 90px;" maxlength="50" onblur="update_input_cur('price');" value="<?php if($package->price > 0){ echo @$cleanvalues->currency_display($package->price); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                            <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
                        </div>
                        <?php
							/*
						<div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_f_discount']; ?>:<br />
                                <span><?php echo $mgrlang['gen_f_discount_d']; ?></span>
                            </p>
                            <input type="text" name="discount" id="discount" style="width: 90px;" maxlength="50" onblur="update_input_cur('discount');" value="<?php if($package->discount > 0){ echo @$cleanvalues->currency_display($package->discount); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                        </div>
						
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['packages_f_cost']; ?>:<br />
                                <span><?php echo $mgrlang['packages_f_cost_d']; ?></span><br /><br />
                            </p>
                            <input type="text" name="my_cost" id="my_cost" style="width: 90px;" onblur="update_input_cur('my_cost');" maxlength="50" value="<?php echo @$cleanvalues->currency_display($package->my_cost); ?>" />  <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                        </div>
							*/
						?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['packages_f_taxable']; ?>:<br />
                                <span><?php echo $mgrlang['packages_f_taxable_d']; ?></span>
                            </p>
                            <input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($package->taxable or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                        </div>
					<?php
                        }
                        if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_pack'])
                        {
                    ?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['packages_f_credits']; ?>:<br />
                                <span><?php echo $mgrlang['packages_f_credits_d']; ?></span><br /><br />
                            </p>
                            <input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" value="<?php echo $package->credits; ?>" />
                            <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
                        </div>
						<?php
						/*
                        <div class="<?php fs_row_color(); ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_f_discountc']; ?>:<br />
                                <span><?php echo $mgrlang['gen_f_discountc_d']; ?></span>
                            </p>
                            <input type="text" name="discountc" id="discountc" style="width: 90px;" maxlength="50" value="<?php echo $cleanvalues->number_display($package->discountc); ?>" />
                        </div>
                    <?php
						*/
						}
					?>
                </div>
                  
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_prints']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_prints_d']; ?></span>
                        </p>
                        <div style="float: left;">
							<?php
								$print_result = mysqli_query($db,"SELECT print_id,item_name,active FROM {$dbinfo[pre]}prints WHERE deleted='0' ORDER BY sortorder,item_name");
								$print_rows = mysqli_num_rows($print_result);
								
								if($print_rows)
								{
							?>
								<div class="divTable packageContentsTable">
									<div class="divTableRow">
										<div class="divTableCell">
											<?php echo $mgrlang['gen_quantity']; ?>
										</div>
										<div class="divTableCell">
											<?php echo $mgrlang['gen_print']; ?>
										</div>
										<div class="divTableCell">
											<?php echo $mgrlang['gen_group_mult']; ?>
										</div>
									</div>
							<?php
									while($print = mysqli_fetch_object($print_result))
									{
										$link_color = ($print->active) ? "333" : "999";
										$quantity = ($printarray[$print->print_id]) ? $printarray[$print->print_id]: 0;
										//$groupmult = ($printarrayGroup[$print->print_id]) ? 1 : 0;
							?>
									<div class="divTableRow">
										<div class="divTableCell">
											<input type="hidden" name="packitemid[]" value="<?php echo $print->print_id; ?>" style="width: 22px;" >
											<input type="hidden" name="packitemtype[]" value="print" style="width: 22px;" >
											<input type="text" name="packitemquantity[]" value="<?php echo $quantity; ?>" style="width: 22px;" >
										</div>
										<div class="divTableCell">
											<div style="float: left;"><a href="#" style="color: #<?php echo $link_color; ?>" class="editlink" onMouseOver="show_details_win('print_details_<?php echo $print->print_id; ?>','mgr.prints.actions.php?pmode=details&id=<?php echo $print->print_id; ?>');" onMouseOut="hide_gdetails('print_details_<?php echo $print->print_id; ?>');"><strong><?php echo $print->item_name; ?></strong></a></div>
											<div style="float: left; overflow: visible; margin-top: -6px;">
												<div id="print_details_<?php echo $print->print_id; ?>" class="galdet_win" style="display: none;">Loading Details</div>
											</div>
										</div>
										<div class="divTableCell">
											<input type="checkbox" name="packitemgroupmult[print-<?php echo $print->print_id; ?>]" value="1" <?php if($printarrayGroup[$print->print_id]){ echo "checked='checked'"; } ?> /> 
										</div>
									</div>
                            <?php
									}
								echo "</div>";
								}
							?>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_prod']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_prod_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        	<?php
								$prod_result = mysqli_query($db,"SELECT prod_id,item_name,active FROM {$dbinfo[pre]}products WHERE deleted='0' ORDER BY sortorder,item_name");
								$prod_rows = mysqli_num_rows($prod_result);
								
								if($prod_rows)
								{
							?>
								<div class="divTable packageContentsTable">
									<div class="divTableRow">
										<div class="divTableCell">
											<?php echo $mgrlang['gen_quantity']; ?>
										</div>
										<div class="divTableCell">
											<?php echo $mgrlang['gen_prod']; ?>
										</div>
										<div class="divTableCell">
											<?php echo $mgrlang['gen_group_mult']; ?>
										</div>
									</div>
                        	<?php
									while($prod = mysqli_fetch_object($prod_result))
									{
										$link_color = ($prod->active) ? "333" : "999";
										$quantity = ($prodarray[$prod->prod_id]) ? $prodarray[$prod->prod_id]: 0;
							?>
									<div class="divTableRow">
										<div class="divTableCell">
											<input type="hidden" name="packitemid[]" value="<?php echo $prod->prod_id; ?>" style="width: 22px;" >
											<input type="hidden" name="packitemtype[]" value="prod" style="width: 22px;" >
											<input type="text" name="packitemquantity[]" value="<?php echo $quantity; ?>" style="width: 22px;" >
										</div>
										<div class="divTableCell">
											<div style="float: left;"><a href="#" style="color: #<?php echo $link_color; ?>" class="editlink" onMouseOver="show_details_win('prod_details_<?php echo $prod->prod_id; ?>','mgr.products.actions.php?mode=details&id=<?php echo $prod->prod_id; ?>');" onMouseOut="hide_gdetails('prod_details_<?php echo $prod->prod_id; ?>');"><strong><?php echo $prod->item_name; ?></strong></a></div>
											<div style="float: left; overflow: visible; margin-top: -6px;">
												<div id="prod_details_<?php echo $prod->prod_id; ?>" class="galdet_win" style="display: none;">Loading Details</div>
											</div>
										</div>
										<div class="divTableCell">
											<input type="checkbox" name="packitemgroupmult[prod-<?php echo $prod->prod_id; ?>]" value="1" <?php if($prodarrayGroup[$prod->prod_id]){ echo "checked='checked'"; } ?> />  
										</div>
									</div>
                            <?php
									}
								echo "</div>";
								}
							?>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$pack_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$package->pack_id' AND item_id != 0");
							while($pack_groupids = mysqli_fetch_object($pack_groupids_result)){
								$plangroups[] = $pack_groupids->group_id;
							}
							
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($pack_group = mysqli_fetch_object($pack_group_result)){
								echo "<li><input type='checkbox' id='group_$pack_group->gr_id' class='permcheckbox' name='setgroups[]' value='$pack_group->gr_id' "; if(in_array($pack_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($pack_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' /> "; } echo "<label for='group_$pack_group->gr_id'>".substr($pack_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
						?>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group">
                    <?php
						/*
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_shipping']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_shipping_d']; ?></span>
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
                        <input type="text" name="weight" id="weight" style="width: 100px;" maxlength="50" value="<?php echo @stripslashes($package->weight); ?>" />
                        (<?php $sel_lang = $config['settings']['lang_file_mgr']; if($config['settings']['weight_tag_' . $sel_lang]){ echo $config['settings']['weight_tag_' . $sel_lang]; } else { echo $config['settings']['weight_tag']; } ?>)
                    </div> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_add_ship']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_add_ship_d']; ?></span>
                        </p>
                        <input type="text" name="addshipping" id="addshipping" style="width: 100px;" maxlength="50" onblur="update_input_cur('addshipping');" value="<?php if($package->addshipping > 0){ echo $cleanvalues->currency_display($package->addshipping); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                    </div>         
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group" style="padding: 20px 20px 20px 20px;">
	                <div style="text-align: left; margin-bottom: 10px;"><a href="javascript:open_upload_box();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    <div id="ip_div" style="font-size: 11px; overflow: auto;">package shots here</div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_itemcode']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_itemcode_d']; ?></span>
                        </p>
                        <input type="text" name="pack_code" id="pack_code" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($package->pack_code); ?>" />
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
									if($lab->lab_id == $package->notify_lab){ echo " selected"; }
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
                        <input type="text" name="lab_code" id="lab_code" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($package->lab_code); ?>" />
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
							if($_GET['edit'] != 'new' and $package->everyone == '0'){
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
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($package->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($package->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $package->pack_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $package->pack_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" /></a></div>
                    </div> 
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['int_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['int_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" id="notes" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($package->notes); ?></textarea>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_options']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_options_d']; ?></span>
                        </p>
                        <input type="checkbox" name="allowoptions" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($package->allowoptions or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">  
                	<div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['hppacks'] == 0){
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
                        <input type="checkbox" name="homepage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($package->homepage){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['packpage'] == 0){
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
                        	<span><?php echo $mgrlang['packages_f_adv_d']; ?></span>
                        </p>
                        <input type="checkbox" name="featured" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($package->featured){ echo "checked"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab9_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_options2']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_options2_d']; ?></span>
                        </p>
                        
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab10_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_colls']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_colls_d']; ?></span>
                        </p>
                        <div style="float: left;">
                        <?php
							echo "<ul style='padding: 10px 10px 10px 3px; margin: 0; border-top: 1px solid #fff'>";
							$coll_result = mysqli_query($db,"SELECT coll_id,item_name,active FROM {$dbinfo[pre]}collections WHERE deleted='0' ORDER BY sortorder,item_name");
							$coll_rows = mysqli_num_rows($coll_result);
							
							if(!$collarray) $collarray = array(); // Fix for being blank
							
							while($coll = mysqli_fetch_object($coll_result))
							{
								$link_color = ($coll->active) ? "333" : "999";
						?>
							<li style="list-style:none; margin-bottom: 6px; clear: both; overflow: auto;">
                                <div style="float: left;"><input type="checkbox" name="packageCollection[]" id="coll_<?php echo $coll->coll_id; ?>" value="<?php echo $coll->coll_id; ?>" style="padding: 0; margin: 0 0 4px 0; vertical-align: middle" <?php if(in_array($coll->coll_id,$collarray)){ echo "checked='checked'"; } ?> /> &nbsp; <label for="coll_<?php echo $coll->coll_id; ?>"><a href="#" style="color: #<?php echo $link_color; ?>" class="editlink" onMouseOver="show_details_win('coll_details_<?php echo $coll->coll_id; ?>','mgr.collections.actions.php?mode=details&id=<?php echo $coll->coll_id; ?>');" onMouseOut="hide_gdetails('coll_details_<?php echo $coll->coll_id; ?>');"><strong><?php echo $coll->item_name; ?></strong></a></label></div>
								<div style="float: left; overflow: visible;">
									<div id="coll_details_<?php echo $coll->coll_id; ?>" class="galdet_win" style="display: none;"><?php echo $mgrlang['gen_loading']; ?></div>
								</div>
							</li>
						
						<?php
								//echo "<li style='list-style:none; margin-bottom: 6px; font-weight: bold;'> <input type='text' name='' value='0' style='width: 22px;' > &nbsp;" . $print->item_name;
								//echo "</li>";	
							}
							echo "</ul>";
						?>
                        </div> 
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab11_group" class="group">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['packages_f_sub']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_sub_d']; ?></span>
                        </p>
                        <div style="float: left;">
							<?php
                                echo "<ul style='padding: 10px 10px 10px 3px; margin: 0; border-top: 1px solid #fff'>";
                            ?>
                                <li style="list-style:none; margin-bottom: 3px; clear: both; overflow: auto;">
                                    <div style="float: left;"><input type="radio" name="packageSubscription" id="sub_none" value="0" checked /> &nbsp; <label for="sub_none"><strong><?php echo $mgrlang['gen_none']; ?></strong></label></div>
                                </li>
                                
                            <?php							   
							   	$sub_result = mysqli_query($db,"SELECT sub_id,item_name,active FROM {$dbinfo[pre]}subscriptions WHERE deleted='0' ORDER BY sortorder,item_name");
                                $sub_rows = mysqli_num_rows($sub_result);
                               
							   if(!$subarray) $subarray = array(); // Fix for being blank
							   
							    while($sub = mysqli_fetch_object($sub_result))
                                {
                                    $link_color = ($sub->active) ? "333" : "999";
                            ?>
                                <li style="list-style:none; margin-bottom: 3px; clear: both; overflow: auto;">
                                    <div style="float: left;">
										<input type="radio" name="packageSubscription" id="sub_<?php echo $sub->sub_id; ?>" value="<?php echo $sub->sub_id; ?>" <?php if(in_array($sub->sub_id,$subarray)){ echo "checked='checked'"; } ?> > &nbsp; 
										<label for="sub_<?php echo $sub->sub_id; ?>"><a href="#" style="color: #<?php echo $link_color; ?>" class="editlink" onMouseOver="show_details_win('sub_details_<?php echo $sub->sub_id; ?>','mgr.subscriptions.actions.php?pmode=details&id=<?php echo $sub->sub_id; ?>');" onMouseOut="hide_gdetails('coll_details_<?php echo $sub->sub_id; ?>');"><strong><?php echo $sub->item_name; ?></strong></a></label>
									</div>
                                    <div style="float: left; overflow: visible;">
                                        <div id="sub_details_<?php echo $sub->sub_id; ?>" class="galdet_win" style="display: none;">Loading Details</div>
                                    </div>
                                </li>                            
                            <?php
                                    //echo "<li style='list-style:none; margin-bottom: 6px; font-weight: bold;'> <input type='text' name='' value='0' style='width: 22px;' > &nbsp;" . $print->item_name;
                                    //echo "</li>";	
                                }
                                echo "</ul>";
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab13_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['attachment']; ?>:<br />
                        	<span><?php echo $mgrlang['packages_f_attach_d']; ?></span>
                        </p>
                        <select name="attachment" id="attachment" onchange="attachment_update();">
                        	<option value="none" <?php if($package->attachment == 'none'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_none']; ?></option>
                            <option value="galleries" <?php if($package->attachment == 'galleries'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_sel_gal'] ?></option>
                            <option value="media" <?php if($package->attachment == 'media'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_media_in_gal']; ?></option>
                            <option value="both" <?php if($package->attachment == 'both'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attach_both']; ?></option>
                        </select>
                    </div> 
                    <div class="<?php fs_row_color(); ?>" id="gals_div" style="display: <?php if($package->attachment == 'none' or $_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; } ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_tab_galleries']; ?>:<br />
                        	<span><?php echo $mgrlang['galleries_attach']; ?></span>
                        </p>
                        <div style="float: left; width: 415px;">
                        	<div name="gals" id="gals" style="border: 1px solid #d9d9d9; font-size: 11px; padding: 5px;"></div>
                        </div>
                    </div>                    
                </div>
                           
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.packages.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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