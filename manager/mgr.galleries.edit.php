<?php
	###################################################################
	####	MANAGER GALLERIES EDIT AREA                            ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 11-2-2007                                     ####
	####	Modified: 5-5-2012                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "galleries";
		$lnav = "library";
		
		$supportPageID = '313';
	
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
		# ADDITIONAL GALLERY AREA ERROR CHECKS
		//require_once('mgr.galleries.ec.php');
		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		$ndate = new kdate;
		$ndate->distime = 0;
	
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" ){
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			//echo 'name'.$name; exit;
			
			updateGalleryVersion(); // Something has changed - update the gallery version
			
			$active_date = $_POST['active_year']."-".$_POST['active_month']."-".$_POST['active_day']." 00:00:00";
			$expire_date = $_POST['expire_year']."-".$_POST['expire_month']."-".$_POST['expire_day']." 00:00:00";
			$event_date = $_POST['event_year']."-".$_POST['event_month']."-".$_POST['event_day']." 00:00:00";
			
			$active_date = $ndate->formdate_to_gmt($active_date);
			$expire_date = $ndate->formdate_to_gmt($expire_date);
			$event_date = $ndate->formdate_to_gmt($event_date);
			
			if($password){ $password = k_encrypt($password); }
			
			if(!$parentgal_override)
			{
				$parentgal_override = '0';	
			}
			
			# SET PERM
			if(empty($perm) or $perm == 'everyone')
			{
				$everyone = "1";
			}
			else
			{
				$everyone = '0';
			}
			
			if($permowner == 0)
			{
				$publicgal = 0;				
				$isAlbum = 0;
			}
			else
			{
				if(empty($perm) or $perm == 'everyone')
					$publicgal = 1;
				else
					$publicgal = 0;
					
				$isAlbum = 1;
			}
			
			if($_REQUEST['action'] == "save_edit"){
				
				# PULL PREVIOUS DATA
				$gallery_result = mysqli_query($db,"SELECT folder_name,parent_gal,folder_path,storage_path FROM {$dbinfo[pre]}galleries WHERE gallery_id = '$saveid'");
				$gallery = mysqli_fetch_object($gallery_result);
				
				/*
				# GET THE STORAGE PATH INFO
				$sp_result = mysqli_query($db,"SELECT full_path FROM {$dbinfo[pre]}storage_paths WHERE path_id = '$gallery->storage_path'");
				$sp = mysqli_fetch_object($sp_result);
				*/
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach(array_unique($active_langs) as $value){ 
					$name_val = ${"name_" . $value};
					$description_val = ${"description_" . $value};
					$addsql.= "name_$value='$name_val',";
					$addsql.= "description_$value='$description_val',";
				}
								
				# GET THE EDIT DATE
				$edit_date = gmt_date();
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}galleries SET 
							name='$name',
							description='$description',
							parent_gal='$parentgal_override',
							folder_name='$folder_name',
							sort_number='$sort_number',
							storage_path='$storage_path',
							active_type='$active_type',
							expire_type='$expire_type',
							active_date='$active_date',
							expire_date='$expire_date',
							event_details='$event_details',
							event_date='$event_date',
							event_location='$event_location',
							client_name='$client_name',
							event_code='$event_code',
							allow_uploads='$allow_uploads',
							dsorting='$dsorting',
							dsorting2='$dsorting2',
							owner='$permowner',
							password='$password',
							everyone='$everyone',
							album='{$isAlbum}',
							publicgal='{$publicgal}',
							nowatermark='$nowatermark',
							icon_id='$icon_id',
							feature='$feature',
							active='$active',";
				$sql.= $addsql;				
				$sql.= "	edited='$edit_date'
							where gallery_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
								
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_galleries'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.galleries.php?mes=edit"); exit;
			
			}
			
			# SAVE NEW ITEM
			if($_REQUEST['action'] == "save_new"){
				
				# CREATE A UNIQUE GALLERY ID
				$ugallery_id = create_unique2();
				
				/*
				# MAKE SURE STORAGE PATH IS WRITABLE FIRST
				$sp_result = mysqli_query($db,"SELECT full_path FROM {$dbinfo[pre]}storage_paths WHERE path_id = '$storage_path'");
				$sp = mysqli_fetch_object($sp_result);
				if(!is_writable($sp->full_path)){
					output_error_message($mgrlang['gen_error_02'] . $sp->full_path,1);
				}
				
				# FIX AND ALLOW PERMISSIONS OF 777
				umask(0);
				*/
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach(array_unique($active_langs) as $value){ 
					$name_val = ${"name_" . $value};
					$description_val = ${"description_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				$edit_date = gmt_date();
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}galleries (
						name,
						parent_gal,
						description,
						ugallery_id,
						created,
						edited,
						storage_path,
						active_type,
						expire_type,
						active_date,
						expire_date,
						event_details,
						event_date,
						event_location,
						client_name,
						event_code,
						allow_uploads,
						dsorting,
						dsorting2,
						owner,
						password,
						everyone,
						nowatermark,
						icon_id,
						active,
						sort_number,
						album,
						feature,
						publicgal";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$parentgal_override',
						'$description',
						'$ugallery_id',
						'$edit_date',
						'$edit_date',
						'$storage_path',
						'$active_type',
						'$expire_type',
						'$active_date',
						'$expire_date',
						'$event_details',
						'$event_date',
						'$event_location',
						'$client_name',
						'$event_code',
						'$allow_uploads',
						'$dsorting',
						'$dsorting2',
						'$permowner',
						'$password',
						'$everyone',
						'$nowatermark',
						'$icon_id',
						'$active',
						'$sort_number',
						'{$isAlbum}',
						'{$feature}',
						'{$publicgal}'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# RENAME ORPHANED ITEM PHOTOS UNDER THIS PRINT
				save_new_item_photos('gallery',$saveid);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_galleries'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				# BACK TO THE GALLERIES PAGE
				header("location: mgr.galleries.php?mes=new"); exit;
			}				
		}
		
		# GET THE ACTIVE LANGUAGES
		//$active_langs = explode(",",$config['settings']['lang_file_pub']);
		//$active_langs[] = $config['settings']['lang_file_mgr'];
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$gallery_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}galleries WHERE gallery_id = '$_GET[edit]'");
			$gallery_rows = mysqli_num_rows($gallery_result);
			$gallery = mysqli_fetch_object($gallery_result);
			
			$_SESSION['item_id'] = $gallery->gallery_id;
			$_SESSION['mgrarea'] = 'gallery';
		}
		
		if($_GET['edit'] == "new")
		{
			# DELETE ANY ORPHANED ITEM PHOTOS
			delete_orphaned_item_photos('gallery');
			
			# DELETE ANY ORPHANED OPTION GROUPS
			delete_orphaned_optiongroups('gallery');
			
			# ASSIGN A DEFAULT item_id AND mgrarea
			$_SESSION['item_id'] = 0;
			$_SESSION['mgrarea'] = 'gallery';
		}
		
		# ACTIONS
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_galleries']; ?></title>
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
	<!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INCLUDE THE EDITOR JS -->
	<?php include_editor_js(); ?>	
	
	<link rel="stylesheet" href="../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" />
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<style>
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
	</style>
	
	<script language="javascript" type="text/javascript">
		function form_submitter(){
			$('name_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.galleries.edit.php?action=save_new" : "mgr.galleries.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","galleries_f_name",1);
				}
			?>
		}
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
			load_parent_gals();
		});
		
		function active_type_status()
		{
			var active_selected = $('active_type').options[$('active_type').selectedIndex].value;
			if(active_selected == 1)
			{
				show_div('active_date_div');
			}
			else
			{
				hide_div('active_date_div');
			}
		}
		
		function expire_type_status()
		{
			var expire_selected = $('expire_type').options[$('expire_type').selectedIndex].value;
			if(expire_selected == 1)
			{
				show_div('expire_date_div');
			}
			else
			{
				hide_div('expire_date_div');
			}
		}
		
		ownerobj = {};
		ownerobj.mode = 'owner';
		ownerobj.id = 'owner';
		ownerobj.mo = 'gallery_owner_edit'; // MEMBERS ONLY MODE
		
		workboxobj = {};
		workboxobj.mode = 'memselect';
		workboxobj.id = 'gal';
		
		// LOAD PARENT GALLERIES BASED OFF OF PERMISSIONS
		function load_parent_gals()
		{
			//alert('test');
			show_loader('parentgal');
			//alert('test');
			var pars = 'mode=galleries&gal_mem=' + $F('permowner') + '&curgal=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('parentgal', 'mgr.galleries.actions.php', {method: 'get', parameters: pars});
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
		uploadobj.id = '<?php echo $gallery->gallery_id; ?>'; // THE ID OF THE ITEM
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
				
				uploadobj.maxfiles = 1 - ip_length;
				
				if(ip_length < 1)
				{
					<?php
						if($config['settings']['uploader'] == '4xxx')
							echo "window.open('mgr.plupload.php?itemPhotos=1&mgrarea={$_SESSION[mgrarea]}&id={$gallery->gallery_id}', 'Plupload', 'width=800,height=380,scrollbars=yes,menubar=no,titlebar=no');";
						else
							echo "workbox(uploadobj);";
					?>
				}
				else
				{
					simple_message_box("<?php echo $mgrlang['max_icons']; ?>",'')
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
			var pars = 'mode=display_ip_list&id=<?php echo $_SESSION['item_id']; ?>';
			var myAjax = new Ajax.Updater('ip_div', 'mgr.galleries.actions.php', {method: 'get', parameters: pars});
		}
		
		// DO DELETE PRODUCT SHOTS
		function do_delete_ip(ip_id)
		{
			//show_loader('ip_div');
			//alert($F('permowner'));
			Effect.Fade('ip_'+ip_id,{ duration: 0.5 });
			
			setTimeout(function(){
					var pars = 'mode=delete_ip&gallery=<?php echo $_SESSION['item_id']; ?>&ip_id='+ip_id;
					//var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars, evalScripts: true});
					new Ajax.Request('mgr.galleries.actions.php', {method: 'get', parameters: pars, onSuccess: function() {
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
                <img src="./images/mgr.badge.galleries.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['galleries_new_header'] : $mgrlang['galleries_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['galleries_new_message'] : $mgrlang['galleries_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
        
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['gen_details']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');load_ip();" id="tab2"><?php echo $mgrlang['gen_tab_options']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('7');load_parent_gals();" id="tab7"><?php echo $mgrlang['gen_tab_parent']; ?></div>                    
                    <div class="subsuboff" onclick="bringtofront('8');" id="tab8"><?php echo $mgrlang['gen_tab_event_details']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['gen_tab_permissions']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('6');" id="tab6" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" /></td>
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['galleries_f_name']; ?>: <br />
                        	<span><?php echo $mgrlang['galleries_f_name_d']; ?></span>
                        </p>
                        <?php
							/*
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($gallery->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                <span class="mtag_dblue" style="color: #FFF"><?php echo strtoupper($config['settings']['lang_file_mgr']); ?></span><br />
                                <br /><a href="javascript:displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.gif" id="plusminus01" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_title','','','','plusminus-01');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />
                                <div id="lang_title" style="display: none;">
                                <ul>
								<?php
                                    foreach(array_unique($active_langs) as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($gallery->{"name" . "_" . $value}); ?>" /> <span class="mtag_dblue" style="color: #FFF"><?php echo strtoupper($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                         </div>
						 	*/
						?>
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @htmlspecialchars($gallery->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @htmlspecialchars($gallery->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="description" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['galleries_f_description']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_description_d']; ?></span>
                        </p>
						<div style="float: left;" id="content_div">
                        <?php
                            show_editor("100%","250px",stripslashes($gallery->description),"description","editor");
                            if(in_array('multilang',$installed_addons)){
                            
                            echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'>";
                        ?>
                            <span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_article','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span></div>
                        <?php
                                echo "<div id='lang_article' style='display: none;'>";
                                foreach($active_langs as $value){ 
                                    show_editor("100%","200px",stripslashes($gallery->{"description_".$value}),"description_".$value,"editor_".$value);
                                    //echo "<br clear='both'/>";
                                    echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'><span class='mtag_dblue' style='color: #FFF'>".ucfirst($value)."</span></div>";
                                }
                                echo "</div>";
                            }
                        ?>
                        </div>
						
						<?php
							/*
						
                        <div class="additional_langs">
                            <textarea name="description" style="width: 300px; height: 75px; vertical-align: middle"><?php echo @stripslashes($gallery->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_short','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_short" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 300px; height: 75px; vertical-align: middle"><?php echo @stripslashes($gallery->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
							*/
						?>
                        
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_active']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['gen_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($gallery->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                    </div>                    
                </div>
                   
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group" style="display: none;">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['galleries_f_parent']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_parent_d']; ?></span>
                       	</p>
                        <input type="hidden" name="parentgal_override" id="parentgal_override" value="<?php echo $gallery->parent_gal; ?>" />
                        <div name="parentgal" id="parentgal" style="float: left; border: 1px solid #c7c5c5; font-size: 11px; padding: 5px; overflow: visible;">
                        </div>
                    </div>
                </div>
                   
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="display: none;">                  
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_icon']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_icon_d']; ?></span>
                        </p>
						<div style="text-align: left; margin-bottom: 10px;"><a href="javascript:open_upload_box();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    	<div id="ip_div" style="font-size: 11px; overflow: auto;"></div>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_active_date']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_active_d']; ?></span>
                        </p>
                        <select style="float: left" name="active_type" id="active_type" onchange="active_type_status()">
                        	<option value="0" <?php if($gallery->active_type == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_now']; ?></option>
                            <option value="1" <?php if($gallery->active_type == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_set_date']; ?>...</option>
                        </select>
                        <div style="float: left; padding-left: 15px; <?php if($gallery->active_type == 1){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="active_date_div">
                            <?php 
								$form_active_date = $ndate->date_to_form($gallery->active_date);
							?>
                            <select style="width: 132px;" name="active_year">
                                <?php
                                    for($i=2005; $i<(date("Y")+6); $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_active_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
						    <select style="width: 75px;" name="active_month">
                                <?php
                                    for($i=1; $i<13; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_active_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="active_day">
                                <?php
                                    for($i=1; $i<=31; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_active_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>                        
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_expire_date']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_expire_d']; ?></span>
                        </p>
                        <select style="float: left;" name="expire_type" id="expire_type" onchange="expire_type_status()">
                        	<option value="0" <?php if($gallery->expire_type == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_never']; ?></option>
                            <option value="1" <?php if($gallery->expire_type == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_set_date']; ?>...</option>
                        </select>
                        <div style="float: left; padding-left: 15px; <?php if($gallery->expire_type == 1){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="expire_date_div">
                            <?php 
								$form_expire_date = $ndate->date_to_form($gallery->expire_date);
							?>
                            <select style="width: 132px;" name="expire_year">
                                <?php
                                    for($i=2005; $i<(date("Y")+6); $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="expire_month">
                                <?php
                                    for($i=1; $i<13; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="expire_day">
                                <?php
                                    for($i=1; $i<=31; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>                        
                        </div>
                    </div>
                    <?php
						if($_GET['edit'] != 'new')
						{
							$directGalleryLink = ($config['EncryptIDs']) ? $config['base_url']."/gallery.php?mode=gallery&id=".k_encrypt($_GET['edit'])."&page=1" : $config['base_url']."/gallery.php?mode=gallery&id=".$_GET['edit']."&page=1";
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['get_direct_link']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_dlink_d']; ?></span>
                        </p>
                        <em><a href="<?php echo $directGalleryLink; ?>" target="_blank"><?php echo $directGalleryLink; ?></a></em>
                    </div>
					<?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_owner']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_owner_d']; ?></span>
                        </p>
                        <div style="float: left; padding-right: 10px; font-weight: bold;" id="owner_name_div">
							<?php
                                if($_GET['edit'] == 'new')
								{
									$galowner = ($_GET['premem']) ? $_GET['premem'] : 0;									
								}
								else
								{									
									$galowner = $gallery->owner;
								}
								
								if($galowner == 0)
								{
									echo $config['settings']['business_name'];
								}
								else
								{
									$member_result = mysqli_query($db,"SELECT mem_id,f_name,l_name,email FROM {$dbinfo[pre]}members WHERE mem_id = '$galowner'");
									$mgrMemberInfo = mysqli_fetch_object($member_result);
									echo $mgrMemberInfo->l_name . ", " . $mgrMemberInfo->f_name;
								}
                            ?>
                        </div>
                        <?php if(in_array("contr",$installed_addons) and $_GET['edit'] == 'new'){ ?><div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=member_selector&header=members&style=gal_owner&inputbox=permowner&multiple=0&updatenamearea=owner_name_div'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon_owner" align="middle" /></a></div><?php } ?>
                        <input type="hidden" value="<?php echo $galowner; ?>" name="permowner" id="permowner" />
                    </div>                    
                    <?php if(in_array("contr",$installed_addons)){ ?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['allow_contr']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_contr_d']; ?></span>
                        </p>
                        <input type="checkbox" name="allow_uploads" value="1" <?php if($gallery->allow_uploads or ($_GET['edit'] == 'new' or $_GET['premem'] != 0)){ echo "checked='checked'"; } ?> />
                    </div>
                    <?php } ?>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['feature_gallery']; ?>: <br />
                            <span><?php echo $mgrlang['feature_gallery_d']; ?></span>
                        </p>
                        <input type="checkbox" name="feature" value="1" <?php if($gallery->feature){ echo "checked='checked'"; } ?> />
                    </div>
                    <?php
					/*
					<?php if(in_array("rating",$installed_addons)){ ?>                   
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Allow Rating: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?>Allow members to rate this gallery and media in this gallery.</span>
                        </p>
                        <input type="checkbox" name="allow_rating" value="1" <?php if($gallery->allow_rating or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
                    <?php } ?>
					
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Related Media: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?>Show related photos/files area when viewing photos/files within this gallery.</span>
                        </p>
                        <input type="checkbox" name="related_media" value="1" <?php if($gallery->related_media or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
					*/
					?>
               	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group" style="display: none;">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_ded']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_ded_d']; ?></span>
                        </p>
                        <input type="checkbox" name="event_details" value="1" <?php if($gallery->event_details){ echo "checked='checked'"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_client']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_client_d']; ?></span>
                        </p>
                        <input type="text" name="client_name" style="width: 277px;" value="<?php echo $gallery->client_name; ?>" />
                    </div> 
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_eventid']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_eventid_d']; ?></span>
                        </p>
                        <input type="text" name="event_code" style="width: 277px;" value="<?php echo $gallery->event_code; ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_eventdate']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_eventdate_d']; ?></span>
                        </p>
                        <div style="float: left;">
                            <?php 
								$form_event_date = $ndate->date_to_form($gallery->event_date);
							?>
                            <select style="width: 75px;" name="event_month">
                                <?php
                                    for($i=1; $i<13; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_event_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="event_day">
                                <?php
                                    for($i=1; $i<=31; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_event_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 132px;" name="event_year">
                                <?php
                                    for($i=2005; $i<(date("Y")+6); $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_event_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>                        
                        </div>
                    </div> 
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_eventloc']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_eventloc_d']; ?></span>                            
                        </p>
                        <textarea style="width: 277px; height: 100px;" name="event_location"><?php echo $gallery->event_location; ?></textarea>
                    </div>                  
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['gen_f_perm']; ?>: <br />
                            <span><?php echo $mgrlang['gen_f_perm_d']; ?></span>
                        </p>
                        <?php
							if($_GET['edit'] != 'new' and $gallery->everyone == '0'){
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
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($gallery->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($gallery->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="permgal" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=permgal&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" align="middle" /></a></div>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_password']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_password_d']; ?></span>
                        </p>
                       <input type="text" name="password" value="<?php echo k_decrypt($gallery->password); ?>" style="width: 150px;" />
                    </div>                    
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Actions: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?></span>
                        </p>
                       Create a new member with access to this gallery / show photos / send request to view<br />
                        gallery option: email access information. 
                    </div>                    
                </div>
                
                 <?php $row_color = 0; ?>
                <div id="tab5_group" class="group" style="display: none;">                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_assets']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_assets_d']; ?></span>
                        </p>
                        <div style="float: left;">
                            This Gallery: <strong>0</strong><br />
                            Child Galleries: <strong>0</strong><br />
                            Total: <strong>0</strong>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['galleries_f_views']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_views_d']; ?></span>
                        </p>
                        <div style="float: left;">
                            This Gallery: <strong>0</strong><br />
                            Child Galleries: <strong>0</strong><br />
                            Total: <strong>0</strong>
                        </div>
                        <div style="float: left; margin-left: 40px;"><input type="button" value="Clear Gallery Views" /></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Sales: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?>Sales from this gallery/event and child galleries and events.</span>
                        </p>
                        <div style="float: left;">
                            This Gallery: <strong>$0</strong><br />
                            Child Galleries: <strong>$0</strong><br />
                            Total: <strong>$0</strong>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group" style="display: none;">
                    <?php
						/*
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Storage Location:<br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?>Choose the location you would like to store this gallery in.</span>
                        </p>
                       	<select name="storage_path" style="width: 200px" />
						<?php
                            $storagepath_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}storage_paths ORDER BY default_path DESC");
                            $storagepath_rows = mysqli_num_rows($storagepath_result);
                            while($storagepath = mysqli_fetch_object($storagepath_result)){
                                echo "<option value='$storagepath->path_id' ";
                                if($sp->path_id == $storagepath->path_id){ echo "selected"; }
                                echo ">$storagepath->alias</option>";
                            }
                        ?>
                        </select>
                        <?php if($_GET['edit'] != "new"){ ?><input type="hidden" name="current_storage_path" value="<?php echo $sp->path_id; ?>" /><?php } ?>
                    </div>
						*/
					?>                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_galdsort']; ?>: <br />
                            <span><?php echo $mgrlang['galleries_f_sort_d']; ?></span>
                        </p>
                        <select name="dsorting">
                        	<option value="" <?php if($gallery->dsorting == ''){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_default']; ?></option>
                            <option value="date_added" <?php if($gallery->dsorting == 'date_added'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_added']; ?></option>
							<option value="date_created" <?php if($gallery->dsorting == 'date_created'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['mediadet_datecreated']; ?></option>
                            <option value="media_id" <?php if($gallery->dsorting == 'media_id'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_id']; ?></option>
                            <option value="title" <?php if($gallery->dsorting == 'title'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_title']; ?></option>
                            <option value="filename" <?php if($gallery->dsorting == 'filename'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_filename'] ?></option>
							<option value="filesize" <?php if($gallery->dsorting == 'filesize'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_filesize'] ?></option>
							<!--<option value="ofilename" <?php if($gallery->dsorting == 'ofilename'){ echo "selected='selected'"; } ?>>Original Filename</option>-->
							<option value="width" <?php if($gallery->dsorting == 'width'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_width'] ?></option>
							<option value="height" <?php if($gallery->dsorting == 'height'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_height'] ?></option>
							<option value="sortorder" <?php if($gallery->dsorting == 'sortorder'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_snumber'] ?></option>							
							<option value="batch_id" <?php if($gallery->dsorting == 'batch_id'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_bid'] ?></option>
							<option value="featured" <?php if($gallery->dsorting == 'featured'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_featured'] ?></option>
							<option value="views" <?php if($gallery->dsorting == 'views'){ echo "selected='views'"; } ?>><?php echo $mgrlang['sort_views'] ?></option>
                            <!--
							<option value="rating" <?php if($gallery->dsorting == 'rating'){ echo "selected='selected'"; } ?>>Rating</option>
                            <option value="comments" <?php if($gallery->dsorting == 'comments'){ echo "selected='selected'"; } ?>>Comments</option>
							-->
                        </select>
                        <select name="dsorting2">
                        	<option value="" <?php if($gallery->dsorting2 == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_default']; ?></option>
                            <option value="ASC" <?php if($gallery->dsorting2 == 'ASC'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_asce']; ?></option>
                            <option value="DESC" <?php if($gallery->dsorting2 == 'DESC'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_desc']; ?></option>
                        </select>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['media_f_sn']; ?>: <br />
                            <span><?php echo $mgrlang['media_f_sn_d']; ?></span>
                        </p>
                        <input type="text" name="sort_number" value="<?php echo $gallery->sort_number; ?>" />
                    </div>
					<!--
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	Do Not Watermark: <br />
                            <span><?php echo $mgrlang['galleris_f_parent_d']; ?>Override any global watermark settings and do not watermark media in this gallery.</span>
                        </p>
                        <input type="checkbox" name="nowatermark" value="1" <?php if($gallery->nowatermark){ echo "checked='checked'"; } ?> />
                    </div>
					-->
                </div>
            </div>    
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.galleries.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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