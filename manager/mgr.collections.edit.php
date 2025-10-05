<?php
	###################################################################
	####	COLLECTIONS EDITOR 			                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-31-2008                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "collections";
		$lnav = "library";
		
		$supportPageID = '320';
	
		$profile_vars = 1;
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
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
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
	
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$coll_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}collections WHERE coll_id = '$_GET[edit]'");
			$coll_rows = mysqli_num_rows($coll_result);
			$coll = mysqli_fetch_object($coll_result);
			
			$_SESSION['item_id'] = $coll->coll_id;
			$_SESSION['mgrarea'] = 'coll';
		}
		
		if($_GET['edit'] == "new")
		{
			# DELETE ANY ORPHANED ITEM PHOTOS
			delete_orphaned_item_photos('coll');
			# ASSIGN A DEFAULT item_id AND mgrarea
			$_SESSION['item_id'] = 0;
			$_SESSION['mgrarea'] = 'coll';
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
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}collections SET 
							item_name='$item_name',
							description='$description',";
				$sql.= $addsql;
				$sql.= "	item_code='$item_code',
							price='$price_clean',
							credits='$credits',
							taxable='$taxable',
							active='$active',
							homepage='$homepage',
							featured='$featured',
							quantity='$quantity',
							everyone='$everyone',
							colltype='$colltype',
							notes='$notes'
							where coll_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# DELETE ITEM GALLERIES FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}item_galleries WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD ITEM GALLERIES
				if($selected_galleries and $colltype == '1'){
					foreach($selected_galleries as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}item_galleries (mgrarea,item_id,gallery_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_collections'],1,$mgrlang['gen_b_ed'] . " > <strong>$item_name</strong>");
			}
			
			if($_REQUEST['action'] == "save_new"){
				
				$ucoll_id = create_unique2();
				
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
				$sql = "INSERT INTO {$dbinfo[pre]}collections (
						item_name,
						description,
						ucoll_id,
						item_code,
						price,
						credits,
						taxable,
						active,
						homepage,
						featured,
						quantity,
						everyone,
						colltype,
						notes";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$item_name',
						'$description',
						'$ucoll_id',
						'$item_code',
						'$price_clean',
						'$credits',
						'$taxable',
						'$active',
						'$homepage',
						'$featured',
						'$quantity',
						'$everyone',
						'$colltype',
						'$notes'";
				$sql.= $addsqlb;
				$sql.= ")";				
				$result = mysqli_query($db,$sql);
				
				$saveid = mysqli_insert_id($db);
				
				# RENAME ORPHANED ITEM PHOTOS UNDER THIS collUCT
				save_new_item_photos('coll',$saveid);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# ADD ITEM GALLERIES
				if($selected_galleries and $colltype == '1'){
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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_collections'],1,$mgrlang['gen_b_new'] . " > <strong>$item_name</strong>");
			}				
			header("location: mgr.collections.php"); exit;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_collections']; ?></title>
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
    <!-- LOAD SLIDER CODE -->
    <script type="text/javascript" src="../assets/javascript/slider.js"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<!-- JAVASCRIPT FOR LIST -->
	<script type="text/javascript" src="mgr.profilelogic.js"></script>
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
			
			//alert('test');			
			// CORRECT ROW COLORS IN TAB 6
			update_fsrow('tab6_group');
			// LOAD THE GALLERIES
			load_gals();
		});
		
		function form_submitter(){
			// REVERT BACK
			$('item_name_div').className='fs_row_off';

			// CHECK FOR OPTION NAME
			//var curoption = 0;
			//var optionerror = 0;
			//$$('input.coll_option_name').each(
			//	function (){
			//		if($F($$('input.coll_option_name')[curoption]) == "" || $F($$('input.coll_option_name')[curoption]) ==  null){
			//			optionerror = 1;
			//		}
			//		curoption++;
			//	}				
			//);		
			//if(optionerror == 1){
			//	simple_message_box('<?php echo addslashes($mgrlang['collections_mes_04']); ?>','');
			//	return false;
			//}
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.collections.edit.php?action=save_new" : "mgr.collections.edit.php?action=save_edit";
			
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("item_name","collections_f_name",1);
				}
			?>
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
		uploadobj.id = '<?php echo $coll->coll_id; ?>'; // THE ID OF THE ITEM
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
							echo "window.open('mgr.plupload.php?itemPhotos=1&mgrarea={$_SESSION[mgrarea]}&id={$coll->coll_id}', 'Plupload', 'width=800,height=380,scrollbars=yes,menubar=no,titlebar=no');";
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
			var myAjax = new Ajax.Updater('gals', 'mgr.collections.actions.php', {method: 'get', parameters: pars});
		}
				
		// LOAD COLLECTION SHOTS
		function load_ip()
		{
			show_loader('ip_div');
			//alert($F('permowner'));
			var pars = 'mode=display_ip_list&id=<?php echo $_SESSION['item_id']; ?>';
			var myAjax = new Ajax.Updater('ip_div', 'mgr.collections.actions.php', {method: 'get', parameters: pars});
		}
		
		// DO DELETE COLLECTION SHOTS
		function do_delete_ip(ip_id)
		{
			//show_loader('ip_div');
			//alert($F('permowner'));
			Effect.Fade('ip_'+ip_id,{ duration: 0.5 });
			
			setTimeout(function(){
					var pars = 'mode=delete_ip&coll=<?php echo $_SESSION['item_id']; ?>&ip_id='+ip_id;
					//var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars, evalScripts: true});
					new Ajax.Request('mgr.collections.actions.php', {method: 'get', parameters: pars, onSuccess: function() {
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
		
		function update_colltype()
		{
			if($F('colltype') == '1')
			{
				$('gals_div').show();
				$('media_div').hide();
			}
			else
			{
				$('gals_div').hide();
				$('media_div').show();
			}
			update_fsrow('tab4_group');
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
                <img src="./images/mgr.badge.collection.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['collections_new_header'] : $mgrlang['collections_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['collections_new_message'] : $mgrlang['collections_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div> 
                <?php
					# PULL GROUPS
					$coll_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$coll_group_rows = mysqli_num_rows($coll_group_result);
				?>      
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['tab_details']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('7');" id="tab7"><?php echo $mgrlang['tab_pricing']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['tab_contents']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('5');load_ip();" id="tab5"><?php echo $mgrlang['tab_prod_shots']; ?></div>
                    <?php if($coll_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['tab_groups']; ?></div><?php } ?>                  
                    <div class="subsuboff" onclick="bringtofront('8');" id="tab8"><?php echo $mgrlang['tab_advertise']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['tab_advanced']; ?></div>
               	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding-bottom: 5px;">                    
                    
                    <div class="<?php fs_row_color(); ?>" id="item_name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['collections_f_name']; ?>:<br />
                        	<span><?php echo $mgrlang['collections_f_name_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <input type="text" name="item_name" id="item_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($coll->item_name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="item_name_<?php echo $value; ?>" id="item_name_<?php echo $value; ?>" style="width: 290px;" maxlength="100" value="<?php echo @stripslashes($coll->{"item_name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
                            <span class="input_label_subtext"><?php echo $mgrlang['gen_description_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($coll->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($coll->{"description" . "_" . $value}); ?></textarea> <span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="quantity_div" <?php if($prod->product_type == 1 or $_GET['edit'] == 'new'){ echo "style='display: none;'"; } ?> fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_quantity']; ?>:<br />
                        	<span><?php echo $mgrlang['leave_blank_quan']; ?></span>
                        </p>
                        <input type="text" name="quantity" id="quantity" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($coll->quantity); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_active']; ?>:<br />
                            <span><?php echo $mgrlang['collections_f_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($coll->active or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
            	</div>                
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['collections_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['collections_f_groups_d']; ?></span>
                        </p>
                        <?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$coll_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$coll->coll_id' AND item_id != 0");
							while($coll_groupids = mysqli_fetch_object($coll_groupids_result)){
								$plangroups[] = $coll_groupids->group_id;
							}
							
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($coll_group = mysqli_fetch_object($coll_group_result)){
								echo "<li><input type='checkbox' id='$coll_group->gr_id' class='permcheckbox' name='setgroups[]' value='$coll_group->gr_id' "; if(in_array($coll_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($coll_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$coll_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$coll_group->flagtype' align='absmiddle' /> "; } echo "<label for='$coll_group->gr_id'>".substr($coll_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
						?>
                    </div>
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group"> 
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['collections_f_price']; ?>:<br />
                                <span><?php echo $mgrlang['collections_f_price_d']; ?></span><br /><br />
                            </p>
                            <div style="float: left;">
                            	<input type="text" name="price" id="price" style="width: 80px;" maxlength="50" onblur="update_input_cur('price');" value="<?php if($coll->price > 0){ echo @$cleanvalues->currency_display($coll->price); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                            	<br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
                            </div>
                        </div>
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['collections_f_taxable']; ?>:<br />
                                <span><?php echo $mgrlang['collections_f_taxable_d']; ?></span>
                            </p>
                            <input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($coll->taxable or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                        </div>
                    <?php
						}
                        if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_coll'])
						{
                    ?>
                            <div class="<?php fs_row_color(); ?>" id="name_div">
                                <img src="images/mgr.ast.off.gif" class="ast" />
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['collections_f_credits']; ?>:<br />
                                    <span><?php echo $mgrlang['collections_f_credits_d']; ?></span>
                                </p>
                                <div style="float: left;">
                                	<input type="text" name="credits" id="credits" style="width: 80px;" maxlength="50" value="<?php echo $cleanvalues->number_display($coll->credits); ?>" />
                                	<br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
                                </div>
                            </div>
                    <?php
						}
					?>
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_f_itemcode']; ?>:<br />
                        	<span><?php echo $mgrlang['gen_f_itemcode_d']; ?></span>
                        </p>
                        <input type="text" name="item_code" id="item_code" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($coll->item_code); ?>" />
                    </div>
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
							if($_GET['edit'] != 'new' and $coll->everyone == '0'){
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
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($coll->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($coll->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $coll->coll_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $coll->coll_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" /></a></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['int_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['int_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" id="notes" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($coll->notes); ?></textarea>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">  
                	<div class="<?php fs_row_color(); ?>">
						<?php
							if($config['settings']['hpcolls'] == 0){
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
                        <input type="checkbox" name="homepage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($coll->homepage){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['collpage'] == 0){
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
                        	<span><?php echo $mgrlang['collections_adv_d']; ?></span>
                        </p>
                        <input type="checkbox" name="featured" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($coll->featured){ echo "checked"; } ?> />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
                    <div class="<?php fs_row_color(); ?>" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['collections_f_toc']; ?>:<br />
                        	<span><?php echo $mgrlang['collections_f_toc_d']; ?></span>
                        </p>
                        <div style="float: left">
							<?php if($_GET['edit'] != 'new'){ echo "<input type='hidden' value='$coll->colltype' name='colltype' />"; } ?>
							<select name="colltype" id="colltype" onchange="update_colltype();" <?php if($_GET['edit'] != 'new'){ echo "disabled='disabled'"; } ?>>
								<option value="1" <?php if($coll->colltype == '1'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['collections_cfg']; ?></option>
								<option value="2" <?php if($coll->colltype == '2'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['collections_cfim']; ?></option>
								<!--<option value="3">Create Collection From Search Or Keyword</option>-->
							</select>
							<div id="media_div" style="padding-top: 10px; font-size: 11px; display: <?php if($coll->colltype == '2'){ echo "block"; } else { echo "none"; } ?>"><?php echo $mgrlang['collections_media_added']; ?></div>
						</div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="gals_div" style="display: <?php if($coll->colltype == '1' or $_GET['edit'] == 'new'){ echo "block"; } else { echo "none"; } ?>" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['gen_tab_galleries']; ?>:<br />
                        	<span><?php echo $mgrlang['collections_choose_gal']; ?></span>
                        </p>
                        <div style="float: left; width: 415px;">
                        	<div name="gals" id="gals" style="border: 1px solid #d9d9d9; font-size: 11px; padding: 5px;"></div>
                        </div>
                    </div>               
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group" style="padding: 20px 20px 20px 20px;">
	                <div style="text-align: left; margin-bottom: 10px;"><a href="javascript:open_upload_box();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    <div id="ip_div" style="font-size: 11px; overflow: auto;"></div>
                </div>
                             
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.collections.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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