<?php
	###################################################################
	####	PROMOTIONS EDIT AREA                                   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "promotions";
		$lnav = "settings";
		
		$supportPageID = '387';
	
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
			$promo_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}promotions WHERE promo_id = '$_GET[edit]'");
			$promo_rows = mysqli_num_rows($promo_result);
			$promo = mysqli_fetch_object($promo_result);
			
			$_SESSION['item_id'] = $promo->promo_id;
			$_SESSION['mgrarea'] = 'promo';
		}
		
		if($_GET['edit'] == "new")
		{
			# DELETE ANY ORPHANED ITEM PHOTOS
			delete_orphaned_item_photos('promo');
			$_SESSION['item_id'] = '0';
			$_SESSION['mgrarea'] = 'promo';
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
			
			$minpurchase_clean = $cleanvalues->currency_clean($minpurchase);
			$dollaroff_clean = $cleanvalues->currency_clean($dollaroff);
			
			$promo_code = fullclean($promo_code);
			$promo_code = strtoupper($promo_code);
			
			//$price_clean = $cleanvalues->currency_clean($price);
			
			if($_REQUEST['action'] == "save_edit"){
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$name_val = ${"name_" . $value};
					$addsql.= "name_$value='$name_val',";
					$description_val = ${"description_" . $value};
					$addsql.= "description_$value='$description_val',";
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}promotions SET 
							name='$name',
							description='$description',";
				$sql.= $addsql;
				$sql.= "	active='$active',
							quantity='$quantity',
							autoapply='$autoapply',
							oneuse='$oneuse',
							promo_code='$promo_code',
							everyone='$everyone',
							minpurchase='$minpurchase_clean',
							promotype='$promotype',							
							peroff='$peroff',
							dollaroff='$dollaroff_clean',
							bulkbuy='$bulkbuy',
							bulktype='$bulktype',
							bulkfree='$bulkfree',
							homepage='$homepage',
							cartpage='$cartpage',
							promopage='$promopage',
							notes='$notes'
							WHERE promo_id  = '$saveid'";
				$result = mysqli_query($db,$sql);

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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['promonav_promotions'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
			}
			
			if($_REQUEST['action'] == "save_new"){
				
				$upromo_id = create_unique2();
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$name_val = ${"name_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";					
					$description_val = ${"description_" . $value};
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}promotions (
						name,
						description,
						upromo_id,
						active,
						everyone,
						promo_code,
						oneuse,
						autoapply,
						quantity,
						minpurchase,
						promotype,						
						peroff,
						dollaroff,
						bulkbuy,
						bulktype,
						bulkfree,
						homepage,
						cartpage,
						promopage,
						notes";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$description',
						'$upromo_id',
						'$active',
						'$everyone',
						'$promo_code',
						'$oneuse',
						'$autoapply',
						'$quantity',
						'$minpurchase_clean',
						'$promotype',
						'$peroff',
						'$dollaroff_clean',
						'$bulkbuy',
						'$bulktype',
						'$bulkfree',
						'$homepage',
						'$cartpage',
						'$promopage',
						'$notes'";
				$sql.= $addsqlb;
				$sql.= ")";				
				$result = mysqli_query($db,$sql);
				
				$saveid = mysqli_insert_id($db);
				
				# SAVE THE PERMISSIONS
				save_mem_permissions();
				
				# RENAME ORPHANED ITEM PHOTOS UNDER THIS PROMOTION
				save_new_item_photos('promo',$saveid);

				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['promonav_promotions'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
			}				
			header("location: mgr.promotions.php"); exit;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['promonav_promotions']; ?></title>
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
	<link rel="stylesheet" href="../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" />
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<style>
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
	</style>
	
	<script language="javascript">	
		function form_sumbit(){
			// REVERT BACK
			$('name_div').className='fs_row_off';
			$('promo_code_div').className='fs_row_on';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.promotions.edit.php?action=save_new" : "mgr.promotions.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","promotions_f_name",1);
					js_validate_field("promo_code","promotions_f_code",1);
			?>
				// FIX SPACES
				fixspaces();

				//$('data_form').action = "<?php echo $action_link; ?>";
				//$('data_form').submit();
			<?php
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
		});
		
		function promotype_change()
		{	
			switch($F('promotype'))
			{
				default:
					$('promotype_peroff').hide();
					$('promotype_dollaroff').hide();
					$('promotype_bulk').hide();
					$('minPurchaseDiv').show();
				break;
				case "peroff":
					$('promotype_bulk').hide();
					$('promotype_peroff').show();
					$('promotype_dollaroff').hide();
					$('minPurchaseDiv').show();
				break;
				case "dollaroff":
					$('promotype_bulk').hide();
					$('promotype_peroff').hide();
					$('promotype_dollaroff').show();
					$('minPurchaseDiv').show();
				break;
				case "bulk":
					$('promotype_bulk').show();
					$('promotype_peroff').hide();
					$('promotype_dollaroff').hide();
					$('minPurchaseDiv').hide();
				break;
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
		uploadobj.id = '<?php echo $promo->promo_id; ?>'; // THE ID OF THE ITEM
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
					workbox(uploadobj);
				}
				else
				{
					simple_message_box('<?php echo addslashes($mgrlang['max_prod_shots']); ?>','')
				}
			<?php
				}
			?>
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
		
			
		// LOAD collUCT SHOTS
		function load_ip()
		{
			show_loader('ip_div');
			//alert($F('permowner'));
			var pars = 'pmode=display_ip_list&id=<?php echo $_SESSION['item_id']; ?>';
			var myAjax = new Ajax.Updater('ip_div', 'mgr.promotions.actions.php', {method: 'get', parameters: pars});
		}
		
		// DO DELETE PRODUCT SHOTS
		function do_delete_ip(ip_id)
		{
			Effect.Fade('ip_'+ip_id,{ duration: 0.5 });
			
			setTimeout(function(){
					var pars = 'pmode=delete_ip&sub=<?php echo $_SESSION['item_id']; ?>&ip_id='+ip_id;
					//var myAjax = new Ajax.Updater('ip_div', 'mgr.prints.actions.php', {method: 'get', parameters: pars, evalScripts: true});
					new Ajax.Request('mgr.promotions.actions.php', {method: 'get', parameters: pars, onSuccess: function() {
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.promo.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['promotions_new_header'] : $mgrlang['promotions_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['promotions_new_message'] : $mgrlang['promotions_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
				<?php
					# PULL GROUPS
					$promo_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$promo_group_rows = mysqli_num_rows($promo_group_result);
				?>
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['promotions_tab1']; ?></div>
                    <?php if($promo_group_rows){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['promotions_tab2']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('4');load_ip();" id="tab4"><?php echo $mgrlang['gen_tab_prod_shots']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['tab_advertise']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['tab_advanced']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['promotions_f_name']; ?>:<br />
                        <span class="input_label_promotext"><?php echo $mgrlang['promotions_f_name_d']; ?></span></p>
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($promo->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons))
								{
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_name','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value)
									{
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($promo->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
                            <span class="input_label_promotext"><?php echo $mgrlang['gen_description_d']; ?></span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 300px; height: 50px; vertical-align: middle"><?php echo @stripslashes($promo->description); ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons))
								{
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value)
									{
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 300px; height: 50px; vertical-align: middle"><?php echo @stripslashes($promo->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="promotype_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_f_type']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_f_type_d']; ?></span>
                        </p>
                        <div style="float: left;">
                            <select style="width: 142px;" name="promotype" id="promotype" onchange="promotype_change();">
                                <option value="peroff" <?php if($promo->promotype == 'peroff'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_type_op1']; ?></option>
                                <option value="dollaroff" <?php if($promo->promotype == 'dollaroff'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_type_op2']; ?></option>
                                <option value="notax" <?php if($promo->promotype == 'notax'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_type_op3']; ?></option>
                                <option value="freeship" <?php if($promo->promotype == 'freeship'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_type_op4']; ?></option>
                                <?php if(in_array("pro",$installed_addons)){ ?><option value="bulk" <?php if($promo->promotype == 'bulk'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_type_op5']; ?></option><?php } ?>
                            </select>
                        </div>
                        <div style="float: left; margin-left: 10px; margin-top: -1px; <?php if($promo->promotype == 'peroff' or $_GET['edit'] == 'new'){ echo "display: block"; } else { echo "display: none;"; } ?>" id="promotype_peroff">
                            <input type="text" name="peroff" id="peroff" style="width: 50px;" maxlength="50" value="<?php echo $promo->peroff; ?>" /> %
                        </div>
                        <div style="float: left; margin-left: 10px; margin-top: -1px; <?php if($promo->promotype != 'dollaroff'){ echo "display: none"; } ?>" id="promotype_dollaroff">
                            <input type="text" name="dollaroff" id="dollaroff" style="width: 50px;" maxlength="50" onblur="update_input_cur('dollaroff');" value="<?php echo $cleanvalues->currency_display($promo->dollaroff); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                        </div>
                        <div style="float: left; margin-left: 10px; line-height: 2; margin-top: -1px; <?php if($promo->promotype != 'bulk'){ echo "display: none"; } ?>" id="promotype_bulk">
                        	<strong><?php echo $mgrlang['promotions_buy']; ?> 
                            <input type="text" name="bulkbuy" id="bulkbuy" style="width: 50px; text-align: center" maxlength="50" value="<?php if($promo->bulkbuy){ echo $promo->bulkbuy; } else { echo "0"; } ?>" /> 
                            <select style="width: 142px;" name="bulktype" id="bulktype">
                                <option value="digital" <?php if($promo->bulktype == 'digital'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['promotions_digitalfile']; ?></option>
                                <option value="print" <?php if($promo->bulktype == 'print'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_prints']; ?></option>
                                <option value="prod" <?php if($promo->bulktype == 'prod'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_prods']; ?></option>
                            </select>
                            <?php echo $mgrlang['promotions_get']; ?> 
                            <input type="text" name="bulkfree" id="bulkfree" style="width: 50px; text-align: center" maxlength="50" value="<?php if($promo->bulkfree){ echo $promo->bulkfree; } else { echo "0"; } ?>" />
                            <?php echo $mgrlang['promotions_free']; ?></strong><br /><span><?php echo $mgrlang['promotions_lowestprice']; ?></span>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="promo_code_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_f_code']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_f_code_d']; ?></span>
                        </p>
                        <input type="text" name="promo_code" id="promo_code" style="width: 130px;" maxlength="50" value="<?php echo @stripslashes($promo->promo_code); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="minPurchaseDiv" <?php if($promo->promotype == 'bulk'){ echo "style='display: none;';"; } ?>>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_f_minpur']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_f_minpur_d']; ?></span>
                        </p>
                        <input type="text" name="minpurchase" id="minpurchase" style="width: 80px;" maxlength="50" onblur="update_input_cur('minpurchase');" value="<?php if($promo->minpurchase > 0){ echo $cleanvalues->currency_display($promo->minpurchase); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_f_quanre']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_f_quanre_d']; ?></span>
                        </p>
                        <input type="text" name="quantity" id="quantity" style="width: 80px;" maxlength="50" value="<?php echo $promo->quantity; ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['promotions_f_oneuse']; ?>:<br />
                            <span><?php echo $mgrlang['promotions_f_oneuse_d']; ?></span>
                        </p>
                        <input type="checkbox" name="oneuse" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->oneuse){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['promotions_f_autoap']; ?>:<br />
                            <span><?php echo $mgrlang['promotions_f_autoap_d']; ?></span>
                        </p>
                        <input type="checkbox" name="autoapply" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->autoapply){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_active']; ?>:<br />
                            <span><?php echo $mgrlang['gen_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->active or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                    </div>
            	</div>
                
                <?php
            	if($promo_group_rows)
				{
						$row_color = 0;
				?>
					<div id="tab2_group" class="group"> 
						<div class="<?php fs_row_color(); ?>" id="name_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['promotions_f_groups']; ?>:<br />
								<span><?php echo $mgrlang['promotions_f_groups_d']; ?></span>
							</p>
							<?php
								$plangroups = array();
								# FIND THE GROUPS THAT THIS ITEM IS IN
								$promo_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$promo->promo_id' AND item_id != 0");
								while($promo_groupids = mysqli_fetch_object($promo_groupids_result))
								{
									$plangroups[] = $promo_groupids->group_id;
								}
								echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
								while($promo_group = mysqli_fetch_object($promo_group_result))
								{
									echo "<li><input type='checkbox' id='grp_$promo_group->gr_id' class='permcheckbox' name='setgroups[]' value='$promo_group->gr_id' "; if(in_array($promo_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($promo_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$promo_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$promo_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$promo_group->gr_id'>" . substr($promo_group->name,0,30)."</label></li>";
								}
								echo "</ul>";
							?>
						</div>
					</div>
				<?php
					}
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
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
							if($_GET['edit'] != 'new' and $promo->everyone == '0'){
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
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($promo->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($promo->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $promo->promo_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $promo->promo_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" align="middle" /></a></div>
                    </div> 
                    
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['int_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['int_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" id="notes" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($promo->notes); ?></textarea>
                    </div>
					<?php
						if($_GET['edit'] != 'new')
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['promotions_directlink']; ?>:<br />
								<span><?php echo $mgrlang['promotions_directlink_d']; ?></span>
							</p>
							<em><a href="<?php echo $config['settings']['site_url']; ?>/promotions.php?promoID=<?php echo k_encrypt($promo->promo_id); ?>" target="_blank"><?php echo $config['settings']['site_url']; ?>/promotions.php?promoID=<?php echo k_encrypt($promo->promo_id); ?></a></em>
						</div>
					<?php
						}
					?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group" style="padding: 20px 20px 20px 20px;">
	                <div style="text-align: left; margin-bottom: 10px;"><a href="javascript:open_upload_box();" class="actionlink"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['gen_add_new']; ?></a></div>
                    <div id="ip_div" style="font-size: 11px; overflow: auto;">product shots here</div>
                </div> 
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
	                <div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['hppromos'] == 0){
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
                        <input type="checkbox" name="homepage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->homepage){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_advoncart']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_advoncart_d']; ?></span>
                        </p>
                        <input type="checkbox" name="cartpage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->cartpage){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <?php
							if($config['settings']['promopage'] == 0){
						?>
                            <div style="position: absolute; margin: 0 0 0 300px; vertical-align: middle">
                                <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 200px"><?php echo $mgrlang['gen_featurepage']; ?></div>
                            </div>
                        <?php
							}
						?>
                        
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['promotions_advonpp']; ?>:<br />
                        	<span><?php echo $mgrlang['promotions_advonpp_d']; ?></span>
                        </p>
                        <input type="checkbox" name="promopage" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($promo->promopage){ echo "checked"; } ?> />
                    </div>
                </div>  
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.promotions.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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