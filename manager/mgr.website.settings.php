<?php
	###################################################################
	####	MANAGER WEBSITE SETTINGS PAGE                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 12-19-2009  	                               #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$supportPageID = '366';
		
		$page = "website_settings";
		$lnav = "settings";
	
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
		
		# GET THE ACTIVE LANGUAGES
		//$active_langs = explode(",",$config['settings']['lang_file_pub']);
		//$active_langs[] = $config['settings']['lang_file_mgr'];
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		if($_POST){
			
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			$login_groups = ($login_groups) ? implode(",",$login_groups) : "";
			$signup_groups = ($signup_groups) ? implode(",",$signup_groups) : "";
			
			# MAKE RESTORE POINT
			if($config['settings']['auto_rp']){
				//DuplicateMySQLRecord($dbinfo[pre].'settings', 'settings_id', '1');
				DuplicateSettings($dbinfo[pre].'settings', 'settings_id', '1');
			}
			
			# ADD/UPDATE THE LAB CONTACTS
			if($lab_id){
				foreach($lab_id as $key => $value){
					if($value == 'new'){
						// ADD TO DB
						if($lab_name[$key] or $lab_email[$key]){
							mysqli_query($db,"INSERT INTO {$dbinfo[pre]}lab_contacts (name,email) VALUES ('$lab_name[$key]','$lab_email[$key]')");
						}
					} else {
						// UPDATE DB
						mysqli_query($db,"UPDATE {$dbinfo[pre]}lab_contacts SET name='$lab_name[$key]',email='$lab_email[$key]' WHERE lab_id = '$value'");						
					}
				}
			}
			
			if($avatar_filetypes){ $avatar_filetypes = implode(",",$_POST['avatar_filetypes']); }
			
			# ADD SUPPORT FOR ADDITIONAL LANGUAGES
			foreach($active_langs as $value){ 
				$site_title_val = ${"site_title_" . $value};
				$meta_desc_val = ${"meta_desc_" . $value};
				$meta_keywords_val = ${"meta_keywords_" . $value};
				$compay_other_val = ${"compay_other_" . $value};
				$addsql.= "site_title_$value='$site_title_val',";
				$addsql.= "meta_desc_$value='$meta_desc_val',";
				$addsql.= "meta_keywords_$value='$meta_keywords_val',";
				$addsql.= "compay_other_$value='$compay_other_val',";
			}
			
			if($compay){
				$compay = implode(",",$compay);
			}
			
			$credit_com_clean = $cleanvalues->currency_clean($credit_com);			
			$sub_com = $cleanvalues->currency_clean($sub_com);			
			$default_price = $cleanvalues->currency_clean($default_price);
			$min_total = $cleanvalues->currency_clean($min_total);
			
			//echo $_POST['cart']; exit;
			
			$business_state = $state;
			
			//echo $addsql; exit;
			
			# UPDATE THE SETTINGS DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings SET 
						site_title='$site_title',
						meta_desc='$meta_desc',
						meta_keywords='$meta_keywords',";
			$sql.= $addsql;
			$sql.= 		"site_status='$site_status',
						status_message='$status_message',
						use_gpi='$use_gpi',
						accounts_required='$accounts_required',
						rating_system='$rating_system',
						rating_system_lr='$rating_system_lr',
						comment_system='$comment_system',
						comment_system_lr='$comment_system_lr',
						comment_system_aa='$comment_system_aa',						
						tagging_system='$tagging_system',
						tagging_system_lr='$tagging_system_lr',
						tagging_system_aa='$tagging_system_aa',
						ticketsystem='$ticketsystem',
						request_photo='$request_photo',
						email_friend='$email_friend',
						watch_lists='$watch_lists',
						reg_memberships='$reg_memberships',
						lightbox='$lightbox',
						glightbox='$glightbox',
						cart='{$_POST[cart]}',
						subscriptions='$subscriptions',
						enable_credits='$enable_credits',
						enable_cbp='$enable_cbp',
						delete_carts='$delete_carts',
						expire_download='$expire_download',
						dl_attempts='$dl_attempts',
						download_extensions='$download_extensions',
						default_price='$default_price',
						default_credits='$default_credits',
						auto_orders='$auto_orders',
						min_total='$min_total',
						support_email='$support_email',
						sales_email='$sales_email',
						business_name='$business_name',
						business_address='$business_address',
						business_address2='$business_address2',
						business_country='$business_country',
						business_city='$business_city',
						business_state='$business_state',
						business_zip='$business_zip',
						search_fields='$search_fields',
						search_media_types='$search_media_types',
						search_orientation='$search_orientation',
						search_color='$search_color',
						search_dates='$search_dates',
						search_license_type='$search_license_type',
						search_galleries='$search_galleries',
						notify_sale='$notify_sale',
						notify_account='$notify_account',
						notify_rating='$notify_rating',
						notify_comment='$notify_comment',
						notify_lightbox='$notify_lightbox',
						notify_profile='$notify_profile',
						notify_tags='$notify_tags',
						credit_com='$credit_com_clean',
						member_avatars='$member_avatars',
						compay='$compay',
						captcha='$captcha',
						skip_shipping='{$skip_shipping}',
						purchase_agreement='{$purchase_agreement}',
						customer_taxid='{$customer_taxid}',
						cart_notes='{$cart_notes}',
						compay_other='$compay_other',";
						# CONTRIBUTORS ADD-ON
						if(in_array("contr",$installed_addons)){
							$sql.="
							contr_portfolios='$contr_portfolios',
							contr_showcase='$contr_showcase',
							contr_num='$contr_num',
							contr_fm='$contr_fm',
							sub_com='$sub_com',
							contr_metatags='$contr_metatags',
							contr_samples='$contr_samples',
							contr_cd='$contr_cd',
							contr_cd2='$contr_cd2',
							contr_dvp='$contr_dvp',
							contr_col='$contr_col',
							contr_cd2_mes='$contr_cd2_mes',";
						}
						$sql.="
						login_groups='$login_groups',
						signup_groups='$signup_groups',
						search='$search',
						esearch='$esearch',
						email_conf='$email_conf',
						forum_link='$forum_link',
						com_calc='$com_calc',
						notify_contrup='$notify_contrup',
						print_orders_email='$print_orders_email',
						invoice_prefix='$invoice_prefix',
						invoice_suffix='$invoice_suffix',
						invoice_next='$invoice_next',
						order_num_type='$order_num_type',
						order_num_next='$order_num_next',
						avatar_size='$avatar_size',
						avatar_filetypes='$avatar_filetypes',
						avatar_filesize='$avatar_filesize',
						avatar_approval='$avatar_approval'";			
						# RSS ADD-ON
						if(in_array("rss",$installed_addons)){
							$sql.="
							,rss_news='$rss_news',
							rss_galleries='$rss_galleries',
							rss_search='$rss_search',
							rss_newest='$rss_newest',
							rss_popular='$rss_popular',
							rss_featured_media='$rss_featured_media',
							rss_records='$rss_records'";
						}
						$sql.= " where settings_id  = '1'";
			//echo $sql; exit;			
			$result = mysqli_query($db,$sql);
			
			$sql2 = "UPDATE {$dbinfo[pre]}settings2 SET 
					minicart='$minicart',
					facebook_link='$facebook_link',
					twitter_link='$twitter_link',
					fotomoto='$fotomoto'
					WHERE settings_id  = '1'";
			$result2 = mysqli_query($db,$sql2);
			
			$regFormResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}registration_form WHERE custom = 0");
			while($regFormDB = mysqli_fetch_array($regFormResult))
			{
				$status = ${$regFormDB['field_id']};
				
				//echo $regFormDB['field_id']." - ".$regFormDB['rf_id']." - ".$status."<br />";
				
				$rfsql = 
					"
						UPDATE {$dbinfo[pre]}registration_form SET 
						status='{$status}'
						WHERE rf_id = '{$regFormDB[rf_id]}'
					";
				$rfresult = mysqli_query($db,$rfsql);
			}
			//exit;
			
			# UPDATE FOR CREDIT SYSTEM
			if(in_array("creditsys",$installed_addons))
            {
				# UPDATE THE SETTINGS DATABASE
				$sql = "UPDATE {$dbinfo[pre]}settings SET 
						credits_digital='$credits_digital',
						credits_prof='$credits_prof',
						credits_print='$credits_print',
						credits_prod='$credits_prod',
						credits_pack='$credits_pack',
						credits_coll='$credits_coll',
						credits_sub='$credits_sub'
						WHERE settings_id  = '1'";
				$result = mysqli_query($db,$sql);
			}
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_website_settings'],1,"<strong>".$mgrlang['gen_b_sav']."</strong>");
				
			header("location: mgr.website.settings.php?ep=1&mes=saved");
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_website_settings']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
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
  	<!-- LOAD SLIDER CODE -->
    <script type="text/javascript" src="../assets/javascript/slider.js"></script>
    <!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
    <!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
    <script language="javascript" type="text/javascript">
    	
		// RUN ON PAGE LOAD
		Event.observe(window, 'load', function()
		{
			get_regions();
			
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
		
		// CHECK AND SUBMIT THE FORM
		function form_sumbitter(){
			$('site_title_div').className='fs_row_off';
			$('support_email_div').className='fs_row_off';
			$('sales_email_div').className='fs_row_on';
			$('business_name_div').className='fs_row_off';
			$('business_address_div').className='fs_row_on';
			$('business_country_div').className='fs_row_on';
			$('business_city_div').className='fs_row_off';
			$('business_zip_div').className='fs_row_on';
			$('cart_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = "mgr.website.settings.php";
					js_validate_field("site_title","webset_f_title",1);
					js_validate_field("support_email","webset_f_support_email",3);
					js_validate_field("sales_email","webset_f_sales_email",3);
					js_validate_field("business_name","webset_f_business_name",3);
					//js_validate_field("business_address","webset_f_address",3);
					//js_validate_select("business_country","webset_f_country",3);					
					//js_validate_field("business_city","webset_f_business_city",3);
					//js_validate_field("business_zip","webset_f_business_zip",3);
			?>
					// CHECK TO MAKE SURE AT LEAST 1 CART TYPE IS TURNED ON
					if($('cart').checked == true){
						if($('enable_cbp').checked == false && $('enable_credits').checked == false){
							simple_message_box("<?php echo $mgrlang['webset_mes_cart_warn']; ?>",'');
							$('cart_div').className='fs_row_error';
							bringtofront('2');
							return false;
						}
					}
			<?php
				}
			?>
		}
		
		<?php
			# JUMP TO AND LOAD ACTIVITY LOG
			if($_GET['jump']){
		?>
			Event.observe(window, 'load', function() {
				bringtofront('<?php echo $_GET['jump']; ?>');
			});
		<?php
		 	}
		?>
		
		
		// ADD A NEW LAB ROW
		function add_lab_row(){
			show_div('lab_title_row');
			var numrows = ($$("div.lab_rows").length) - 1;
			//alert(numrows);
			var cur = 0;						
			var last_row_name = $$("div.lab_rows")[numrows].id			
			var templatedata = "<div style='clear: both; padding-top: 4px;' id='lab_row_new" + cur + "' class='lab_rows'>";
            templatedata += "<input type='hidden' name='lab_id[]' value='new' /><input type='text' name='lab_name[]' value='' maxlength='250' class='textbox' style='width: 114px; float: left; margin-right: 5px;' /> <input type='text' name='lab_email[]' value='' maxlength='250' class='textbox' style='width: 114px; float: left; margin-right: 5px;' /> <a href=\"javascript:remove_lab_row('lab_row_new" + cur +"','new');\" class='actionlink' style='float: left; font-weight: normal'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a>";
            templatedata += "</div>";
			var rowTemplate = new Template(templatedata);	
			$(last_row_name).insert({after: 
				rowTemplate.evaluate({
					id: '1'
				})});
			cur++;			
			//alert(last_row_name);
		}
		
		// REMOVE LAB ROW
		function remove_lab_row(lrow,lab_id){
			// CHECK FOR DEMO
			if('<?php echo $_SESSION['admin_user']['admin_id']; ?>' == 'DEMO'){
				demo_message();
				return false;
			} else {
				if('<?php echo $config['settings']['verify_before_delete']; ?>' == '1'){
					Effect.Appear('overlay',{ duration: 0.5, from: 0.0, to: 0.7 });				
					$('messagebox').setStyle({
						display: "block"
					});
					overlay_height();
					$('innermessage').update("<p align='left' style='padding: 0px; margin: 0px; font-weight: bold;'><?php echo $mgrlang['gen_suredelete']; ?></p><p align='right' style='padding: 0px; margin: 0px;'><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_del']; ?>' id='deletebutton' class='button' onclick='do_remove_lab_row(\""+lrow+"\",\""+lab_id+"\");close_message();' /></p>");
				} else{
					do_remove_lab_row(lrow,lab_id);
				}
			}							
		}
		
		// DO REMOVE LAB ROW
		function do_remove_lab_row(lrow,lab_id){
			var numrows = ($$("div.lab_rows").length) - 1;
			if(numrows <= 1){
				hide_div('lab_title_row');
			}
			if(lab_id == 'new'){
				$(lrow).remove();
			} else {
				// DELETE FROM DB
				var myAjax = new Ajax.Request( 
					'mgr.website.settings.actions.php', 
					{
						method: 'get', 
						parameters: 'id=' + lab_id,
						onSuccess: function(){ $(lrow).remove(); }
					});	
			}			
		}
		
		function slide_action(){
				
		}
		function drop_action(){
			
		}
		
		function invoice_preview(){
			var preview = $F('invoice_prefix') + $F('invoice_next') + $F('invoice_suffix'); 
			var old_invoice = Number(<?php echo $config['settings']['invoice_next']; ?>);
			var new_invoice = Number($F('invoice_next'));
			
			if(new_invoice < old_invoice){
				$('invoice_warn').setStyle({
					display: "block"
				});
			} else {
				$('invoice_warn').setStyle({
					display: "none"
				});
			}
			
			$('invoice_preview').update(preview);
		}
		
		function check_cart_dd()
		{
			var selecteditem = $('cart').options[$('cart').selectedIndex].value;
			if(selecteditem == 1 || selecteditem == 0)
			{
				$('credit_selections').hide();	
			}
			else
			{
				$('credit_selections').show();	
			}
		}
		
		function order_num_update()
		{
			var selecteditem = $('order_num_type').options[$('order_num_type').selectedIndex].value;
			if(selecteditem == 1)
			{
				show_div('order_num_ro');
			}
			else
			{
				hide_div('order_num_ro');
			}
		}
		
		//onchange="get_regions();"
		//business_country
		
		// LOAD THE STATE/REGIONS DROPDOWN
		function get_regions()
		{
			$('region_div').update('<img src="./images/mgr.loader2.gif" align="absmiddle" style="margin-top: 8px;" />');						
			var selecteditem = $('business_country').options[$('business_country').selectedIndex].value;
			//alert(selecteditem);
			var updatecontent = "region_div";
			var loadpage = "mgr.get.regions.php";
			var pars = "cid=" + selecteditem + "&sid=<?php echo $config['settings']['business_state']; ?>";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});		
		}
		
	</script>
</head>
<body onresize="shortcuts_height();">
<!-- onload="shortcuts_height();" onresize="shortcuts_height();" -->
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
                <img src="./images/mgr.badge.websettings.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_website_settings']; ?></strong><br /><span><?php echo $mgrlang['subnav_website_settings_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
            <!-- START CONTENT -->
            <div id="content">							
            <div id="spacer_bar"></div>
    
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['webset_tab1']; ?></div>
                <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['webset_tab2']; ?></div>
                <div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['webset_tab3']; ?></div>
                <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['webset_tab4']; ?></div>
                <div class="subsuboff" onclick="bringtofront('8');" id="tab8"><?php echo $mgrlang['webset_tab8']; ?></div>
                <div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['webset_tab5']; ?></div>
                <div class="subsuboff" onclick="bringtofront('9');" id="tab9"><?php echo $mgrlang['webset_tab9']; ?></div>
                <!--<div class="subsuboff" onclick="bringtofront('6');" id="tab6"><?php echo $mgrlang['webset_tab6']; ?></div>-->
                <div class="subsuboff" onclick="bringtofront('10');" id="tab10"><?php echo $mgrlang['webset_tab10']; ?></div>
                <div class="subsuboff" onclick="bringtofront('7');" id="tab7" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['webset_social_set']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group" style="display: block;">
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_status']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_status_d']; ?></span>
                    </p>
                    <div style="float: left; width: 60px;">
                    	<input type="radio" value="1" id="site_status1" name="site_status" <?php if($config['settings']['site_status'] == 1){ echo "checked"; } ?> onclick="hide_div('status_message')" /> <label for="site_status1"><?php echo $mgrlang['webset_on']; ?></label><br />
                    	<input type="radio" value="0" id="site_status2" name="site_status" <?php if($config['settings']['site_status'] == 0){ echo "checked"; } ?> onclick="show_div('status_message');" /> <label for="site_status2"><?php echo $mgrlang['webset_off']; ?></label><br />
                    </div>
                    <div style="float: left; <?php if($config['settings']['site_status'] == 0){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="status_message">
                    	<strong><?php echo $mgrlang['webset_message']; ?>:</strong><br /><textarea style="width: 241px; height: 50px;" name="status_message"><?php echo $config['settings']['status_message']; ?></textarea>
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" id="site_title_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_title']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_title_d']; ?></span>
                    </p>
                    
                    <div class="additional_langs">
                        <input type="text" name="site_title" id="site_title" style="width: 300px;" maxlength="200" value="<?php echo $config['settings']['site_title']; ?>" />
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_sitetitle','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_sitetitle" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="site_title_<?php echo $value; ?>" id="site_title_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($config['settings']['site_title' . "_" . $value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_meta_desc']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_meta_desc_d']; ?></span>
                    </p>
                    
                    <div class="additional_langs">
                        <textarea name="meta_desc" id="meta_desc" style="width: 300px; height: 75px; vertical-align: middle"><?php echo $config['settings']['meta_desc']; ?></textarea>
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_metadesc','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_metadesc" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><textarea name="meta_desc_<?php echo $value; ?>" id="meta_desc_<?php echo $value; ?>" style="width: 300px; height: 75px; vertical-align: middle"><?php echo $config['settings']['meta_desc_'.$value]; ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_meta_keys']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_meta_keys_d']; ?></span>
                    </p>
                    
                    <div class="additional_langs">
                        <textarea name="meta_keywords" id="meta_keywords" style="width: 300px; height: 75px; vertical-align: middle"><?php echo $config['settings']['meta_keywords']; ?></textarea>
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_keywords','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_keywords" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><textarea name="meta_keywords_<?php echo $value; ?>" id="meta_keywords_<?php echo $value; ?>" style="width: 300px; height: 75px; vertical-align: middle"><?php echo $config['settings']['meta_keywords_'.$value]; ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_use_gpi']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_use_gpi_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="use_gpi" <?php if($config['settings']['use_gpi']){ echo "checked"; } ?> />
                </div>
                <?php
					/* Include back in 4.1
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_request_photo']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_request_photo_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="request_photo" <?php if($config['settings']['request_photo']){ echo "checked"; } ?> />
                </div>
                	*/
				?>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_email_friend']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_email_friend_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="email_friend" <?php if($config['settings']['email_friend']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_enable_subs']; ?>:<br />
                        <span><?php echo $mgrlang['webset_enable_subs_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="subscriptions" <?php if($config['settings']['subscriptions']){ echo "checked"; } ?> />
                </div>
                <?php
					/* Include back in 4.1
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_watch_lists']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_watch_lists_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="watch_lists" <?php if($config['settings']['watch_lists']){ echo "checked"; } ?> />
                </div>
                	*/
				?>
                <?php
                    if(in_array("rating",$installed_addons)){
                ?>
                    <div class="fs_header"><?php echo $mgrlang['webset_h_rating_system']; ?></div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_rating_system']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_rating_system_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="rating_system" name="rating_system" onclick="cb_bool('rating_system','rating_system_ro','rating_system_ops');" <?php if($config['settings']['rating_system']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['rating_system']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="rating_system_ro"><a href="javascript:show_div('rating_system_ops');hide_div('rating_system_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="rating_system_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('rating_system_ops');show_div('rating_system_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_rating_system_lr']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_rating_system_lr_d']; ?></span>
                            </p>
                            <input type="checkbox" name="rating_system_lr" id="rating_system_lr" value="1" <?php if($config['settings']['rating_system_lr']){ echo "checked='checked'"; } ?> />
                            </div>                            
                        </div>
                    </div>
                <?php
                    }
                ?>
                
                <?php
                    if(in_array("ticketsystem",$installed_addons)){
                ?>
                    <div class="fs_header"><?php echo $mgrlang['webset_ticket_sys']; ?></div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_enable_ts']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_enable_ts_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" id="ticketsystem" name="ticketsystem" <?php if($config['settings']['ticketsystem']){ echo "checked"; } ?> />
                   	</div>
                <?php
                    }
                ?>
                
                <?php
                    if(in_array("commenting",$installed_addons)){
                ?>
                    <div class="fs_header"><?php echo $mgrlang['webset_h_comment_system']; ?></div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_comment_system']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_comment_system_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="comment_system" name="comment_system" onclick="cb_bool('comment_system','comment_system_ro','comment_system_ops');" <?php if($config['settings']['comment_system']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['comment_system']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="comment_system_ro"><a href="javascript:show_div('comment_system_ops');hide_div('comment_system_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="comment_system_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('comment_system_ops');show_div('comment_system_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_comment_system_lr']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_comment_system_lr_d']; ?></span>
                            </p>
                                                    
                            <input type="checkbox" name="comment_system_lr" id="comment_system_lr" value="1" <?php if($config['settings']['comment_system_lr']){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('comment_system_lr','comment_system_aa');" />
                            <select style="margin-left: 20px; display: <?php if($config['settings']['comment_system_lr'] == 1){ echo "inline"; } else { echo "none"; } ?>" name="comment_system_aa" id="comment_system_aa">
                                <option value="0" <?php if($config['settings']['comment_system_aa'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['manually_approve']; ?></option>
                                <option value="1" <?php if($config['settings']['comment_system_aa'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['auto_approve']; ?></option>
                            </select>
                            </div>                            
                        </div>
                    </div>
               	<?php
                    }
					if(in_array("tagging",$installed_addons)){
               	?>
                    <div class="fs_header"><?php echo $mgrlang['webset_h_tagging_system']; ?></div>                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_tagging_system']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_tagging_system_d']; ?></span>
                        </p>                        
                        <div style="float: left;"><input type="checkbox" value="1" id="tagging_system" name="tagging_system" onclick="cb_bool('tagging_system','tagging_system_ro','tagging_system_ops');" <?php if($config['settings']['tagging_system']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['tagging_system']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="tagging_system_ro"><a href="javascript:show_div('tagging_system_ops');hide_div('tagging_system_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="tagging_system_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('tagging_system_ops');show_div('tagging_system_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_tagging_system_lr']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_tagging_system_lr_d']; ?></span>
                            </p>
                                                    
                            <input type="checkbox" name="tagging_system_lr" id="tagging_system_lr" value="1" <?php if($config['settings']['tagging_system_lr']){ echo "checked='checked'"; } ?> onclick="cb_bool_inline('tagging_system_lr','tagging_system_aa');" />
                            <select style="margin-left: 20px; display: <?php if($config['settings']['tagging_system_lr'] == 1){ echo "inline"; } else { echo "none"; } ?>" name="tagging_system_aa" id="tagging_system_aa">
                                <option value="0" <?php if($config['settings']['tagging_system_aa'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['manually_approve']; ?></option>
                                <option value="1" <?php if($config['settings']['tagging_system_aa'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['auto_approve']; ?></option>
                            </select>
                            </div>                            
                        </div>
                    </div>
                <?php
                    }
                ?>
                
                <?php
                    if(in_array("lightbox",$installed_addons)){
                ?>	
                    <div class="fs_header"><?php echo $mgrlang['webset_h_lightbox']; ?></div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_lightbox']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_lightbox_d']; ?></span>
                        </p>                        
                        <div style="float: left;"><input type="checkbox" value="1" id="lightbox" name="lightbox" onclick="cb_bool('lightbox','lightbox_ro','lightbox_ops');" <?php if($config['settings']['lightbox']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['lightbox']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="lightbox_ro"><a href="javascript:show_div('lightbox_ops');hide_div('lightbox_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="lightbox_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('lightbox_ops');show_div('lightbox_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_glightbox']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_glightbox_d']; ?></span>
                            </p>
                            <input type="checkbox" name="glightbox" id="glightbox" value="1" <?php if($config['settings']['glightbox']){ echo "checked='checked'"; } ?> />
                            </div>                            
                        </div>
                    </div>
                <?php
                    }
                    if(in_array("contr",$installed_addons)){
                ?>
                    <div class="fs_header"><?php echo $mgrlang['webset_h_contr']; ?></div>
                    <?php /*
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_portfolios']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_portfolios_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contr_portfolios" <?php if($config['settings']['contr_portfolios']){ echo "checked"; } ?> />
                    </div>
					*/ ?>
                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_showcase']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_showcase_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="contr_showcase" name="contr_showcase" onclick="cb_bool('contr_showcase','contr_showcase_ro','contr_showcase_ops');" <?php if($config['settings']['contr_showcase']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['contr_showcase']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="contr_showcase_ro"><a href="javascript:show_div('contr_showcase_ops');hide_div('contr_showcase_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="contr_showcase_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('contr_showcase_ops');show_div('contr_showcase_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            	<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                	<?php echo $mgrlang['webset_f_contr_showcase1']; ?>:<br />
                            		<span><?php echo $mgrlang['webset_f_contr_showcase1_d']; ?></span>
                                </p>                            
                            	<input type="text" name="contr_num" value="<?php echo $config['settings']['contr_num']; ?>" style="width: 50px;">
                            </div>
							<?php
							/*
                            <div class="fs_row_off">
                            	<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                	<?php echo $mgrlang['webset_f_contr_showcase3']; ?>:<br />
                            		<span><?php echo $mgrlang['webset_f_contr_showcase3_d']; ?></span>
                                </p>                            
                            	<select name="contr_fm" style="float: left;">
                                	<option value="1" <?php if($config['settings']['contr_fm'] == "1"){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_contr_showcase3op1']; ?></option>
                                    <option value="2" <?php if($config['settings']['contr_fm'] == "2"){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_contr_showcase3op2']; ?></option>
                                    <option value="3" <?php if($config['settings']['contr_fm'] == "3"){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_contr_showcase3op3']; ?></option>
                                    <option value="4" <?php if($config['settings']['contr_fm'] == "4"){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_contr_showcase3op4']; ?></option>
                                    <option value="5" <?php if($config['settings']['contr_fm'] == "5"){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_contr_showcase3op5']; ?></option>                                
                                </select>
                            </div>
							*/
							?>
                        </div>
                  	</div>                   
                    <?php
						/*
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_metatags']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_metatags_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contr_metatags" <?php if($config['settings']['contr_metatags']){ echo "checked"; } ?> />
                    </div>                    
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_samples']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_samples_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contr_samples" <?php if($config['settings']['contr_samples']){ echo "checked"; } ?> />
                    </div>
                    
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_cd']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_cd_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contr_cd" <?php if($config['settings']['contr_cd']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_com_calc']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_com_calc_d']; ?></span>
                        </p>
                        <input type="radio" value="1" name="com_calc" id="com_calc_1" <?php if($config['settings']['com_calc'] == 1){ echo "checked"; } ?> /> <label for="com_calc_1"><?php echo $mgrlang['webset_com_calc_op1']; ?></label><br />
                        <input type="radio" value="2" name="com_calc" id="com_calc_2" <?php if($config['settings']['com_calc'] == 2){ echo "checked"; } ?> /> <label for="com_calc_2"><?php echo $mgrlang['webset_com_calc_op2']; ?></label>
                    </div>
						*/
					?>
					<?php
						/*
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_dvp']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_dvp_d']; ?></span>
                        </p>
                        <select name="contr_dvp" id="contr_dvp">
                            <option value="0" <?php if($config['settings']['contr_dvp'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_contrap']; ?></option>
                            <option value="1" <?php if($config['settings']['contr_dvp'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['gen_adminap2']; ?></option>
                        </select>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_col']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_col_d']; ?></span>
                        </p>
                        <select name="contr_col" id="contr_col">
                            <option value="0" <?php if($config['settings']['contr_col'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_contrap']; ?></option>
                            <option value="1" <?php if($config['settings']['contr_col'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['gen_adminap2']; ?></option>
                        </select>
                    </div>  
						*/
						if(in_array("creditsys",$installed_addons))
						{
					?>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_coc']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_coc_d']; ?></span>
                        </p>
                        <?php echo $mgrlang['one_credit']; ?> = <input type="text" value="<?php echo $cleanvalues->currency_display($config['settings']['credit_com']); ?>" name="credit_com" id="credit_com" style="width: 60px;" onblur="update_input_cur('credit_com');" /> <span class='mtag_dgrey' style="color: #FFF; padding: 3px;"><?php echo $config['settings']['cur_code']; ?></span>
                    </div>
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_cos']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_cos_d']; ?></span>
                        </p>
                        <?php echo $mgrlang['one_download']; ?> = <input type="text" value="<?php echo $cleanvalues->currency_display($config['settings']['sub_com']); ?>" name="sub_com" id="sub_com" style="width: 60px;" onblur="update_input_cur('sub_com');" /> <span class='mtag_dgrey' style="color: #FFF; padding: 3px;"><?php echo $config['settings']['cur_code']; ?></span>
                    </div>
                   	<?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contr_cd2']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contr_cd2_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="contr_cd2" name="contr_cd2" <?php if($config['settings']['contr_cd2']){ echo "checked"; } ?> onclick="cb_bool('contr_cd2','contr_cd2_ro','contr_cd2_ops');" /></div>  <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['contr_cd2']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="contr_cd2_ro"><a href="javascript:show_div('contr_cd2_ops');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>                        
                    	<div id="contr_cd2_ops" class="related_options" style="margin-left: -114px;">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('contr_cd2_ops');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['webset_f_contr_cd2_mes']; ?>:<br />
                                    <span><?php echo $mgrlang['webset_f_contr_cd2_mes_d']; ?></span>
                                </p>
                                <div style="float: left;">
                                    <textarea name="contr_cd2_mes" id="contr_cd2_mes" style="width: 300px; height: 75px; vertical-align: middle" /><?php echo $config['settings']['contr_cd2_mes']; ?></textarea>
                                    <?php
                                        if(in_array('multilang',$installed_addons)){
                                    ?>
                                        &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_cdmes','','','','plusminus-04');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
										
                                        <div id="lang_cdmes" style="display: none;">
                                        <?php
                                            foreach(array_unique($active_langs) as $value){
                                        ?>
                                            <textarea name="contr_cd2_mes_<?php echo $value; ?>" id="contr_cd2_mes_<?php echo $value; ?>" style="width: 300px; height: 75px; margin-top: 4px; vertical-align: middle" value="<?php echo ""; ?>" /></textarea> <span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span><br />
                                    <?php
                                            }
                                            echo "</div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>         
                <?php
                    }
					if(in_array("rss",$installed_addons)){
                ?>
                <div class="fs_header"><?php echo $mgrlang['webset_h_contr--']; ?>RSS Feeds</div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_news']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_news_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_news" <?php if($config['settings']['rss_news']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_galleries']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_galleries_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_galleries" <?php if($config['settings']['rss_galleries']){ echo "checked"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_fmedia']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_fmedia_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_featured_media" <?php if($config['settings']['rss_featured_media']){ echo "checked"; } ?> />
                    </div>
					<?php
						/*
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_search']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_search_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_search" <?php if($config['settings']['rss_search']){ echo "checked"; } ?> />
                    </div>
						*/
					?>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_newest']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_newest_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_newest" <?php if($config['settings']['rss_newest']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_popular']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_popular_d']; ?></span>
                        </p>
                      <input type="checkbox" value="1" name="rss_popular" <?php if($config['settings']['rss_popular']){ echo "checked"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_rss_records']; ?>:<br />
                            <span><?php echo $mgrlang['webset_rss_records_d']; ?></span>
                        </p>
                        <select name="rss_records" style="width: 90px;">
                        	<option <?php if($config['settings']['rss_records'] == 10){ echo "selected"; } ?>>10</option>
                            <option <?php if($config['settings']['rss_records'] == 20){ echo "selected"; } ?>>20</option>
                            <option <?php if($config['settings']['rss_records'] == 30){ echo "selected"; } ?>>30</option>
                            <option <?php if($config['settings']['rss_records'] == 40){ echo "selected"; } ?>>40</option>
                            <option <?php if($config['settings']['rss_records'] == 50){ echo "selected"; } ?>>50</option>
                            <option <?php if($config['settings']['rss_records'] == 75){ echo "selected"; } ?>>75</option>
                            <option <?php if($config['settings']['rss_records'] == 100){ echo "selected"; } ?>>100</option>
                        </select>
                    </div>
                <?php
					}
				?>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab9_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_member_avatars']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_member_avatars_d']; ?></span>
                    </p>
                    <div style="float: left; padding: 8px 0 0 0;" id="avatar_ro"><a href="javascript:show_div('avatar_ops');hide_div('avatar_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    <div id="avatar_ops" class="related_options" style="width: 490px; margin: 0;">
                        <div style="position: absolute; right: 4px;"><a href="javascript:hide_div('avatar_ops');show_div('avatar_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                        <?php /*							
                        <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_avatar_feature1']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_avatar_feature1_d']; ?></span>
                            </p>
                            <input type="checkbox" value="1" name="avatar_approval" <?php if($config['settings']['avatar_approval']){ echo "checked"; } ?> />
                        </div>
						
                        <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_avatar_feature2']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_avatar_feature2_d']; ?></span>
                            </p>
							<?php
                                # SLIDER POSITION
                                //$config['settings']['avatar_size']
                                $sb_multiplier = (145/2000);
                                $sb_position = round($config['settings']['avatar_size']*$sb_multiplier);
                            ?>
                            <div class="carpe_horizontal_slider_track">
                                <div class="carpe_slider_slit">&nbsp;</div>
                                <div class="carpe_slider"
                                    id="thumbslider"
                                    orientation="horizontal"
                                    distance="145"
                                    display="disthumbslider"
                                    style="left: <?php echo $sb_position; ?>px;" >&nbsp;</div><!-- HERE IS WHERE YOU CAN DEFINE THE STARTING POINT -->
                            </div>
                            <div class="carpe_slider_display_holder" style="display: inline; white-space: nowrap">
                                <input class="carpe_slider_display"
                                    id="disthumbslider"
                                    type="text" 
                                    from="25" 
                                    to="2000" 
                                    valuecount="40"
                                    value="<?php echo $config['settings']['avatar_size']; ?>"
                                    name="avatar_size" 
                                    typelock="off"
                                    slide_action="preview"
                                    drop_action="render_preview" />&nbsp;px
                            </div>
                        </div>
						*/ ?>
                        <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_avatar_feature4']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_avatar_feature4_d']; ?></span>
                            </p>
                            <select name="avatar_filesize">
                                <option value="10" <?php if($config['settings']['avatar_filesize'] == 10){ echo "selected"; } ?>>10kb</option>
                                <option value="20" <?php if($config['settings']['avatar_filesize'] == 20){ echo "selected"; } ?>>20kb</option>
                                <option value="30" <?php if($config['settings']['avatar_filesize'] == 30){ echo "selected"; } ?>>30kb</option>
                                <option value="40" <?php if($config['settings']['avatar_filesize'] == 40){ echo "selected"; } ?>>40kb</option>
                                <option value="50" <?php if($config['settings']['avatar_filesize'] == 50){ echo "selected"; } ?>>50kb</option>
                                <option value="60" <?php if($config['settings']['avatar_filesize'] == 60){ echo "selected"; } ?>>60kb</option>
                                <option value="70" <?php if($config['settings']['avatar_filesize'] == 70){ echo "selected"; } ?>>70kb</option>
                                <option value="80" <?php if($config['settings']['avatar_filesize'] == 80){ echo "selected"; } ?>>80kb</option>
                                <option value="90" <?php if($config['settings']['avatar_filesize'] == 90){ echo "selected"; } ?>>90kb</option>
                                <option value="100" <?php if($config['settings']['avatar_filesize'] == 100){ echo "selected"; } ?>>100kb</option>
                                <option value="200" <?php if($config['settings']['avatar_filesize'] == 200){ echo "selected"; } ?>>200kb</option>
                                <option value="300" <?php if($config['settings']['avatar_filesize'] == 300){ echo "selected"; } ?>>300kb</option>
                                <option value="400" <?php if($config['settings']['avatar_filesize'] == 400){ echo "selected"; } ?>>400kb</option>
                                <option value="500" <?php if($config['settings']['avatar_filesize'] == 500){ echo "selected"; } ?>>500kb</option>
                                <option value="600" <?php if($config['settings']['avatar_filesize'] == 600){ echo "selected"; } ?>>600kb</option>
                                <option value="700" <?php if($config['settings']['avatar_filesize'] == 700){ echo "selected"; } ?>>700kb</option>
                                <option value="800" <?php if($config['settings']['avatar_filesize'] == 800){ echo "selected"; } ?>>800kb</option>
                                <option value="900" <?php if($config['settings']['avatar_filesize'] == 900){ echo "selected"; } ?>>900kb</option>
                                <option value="10000" <?php if($config['settings']['avatar_filesize'] == 10000){ echo "selected"; } ?>>1mb+</option>
                            </select>
                        </div>
                        <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_avatar_feature3']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_avatar_feature3_d']; ?></span>
                            </p>
							<?php
                                $avatar_filetypes_array = explode(",",$config['settings']['avatar_filetypes']);
                            ?>
                            <div style="float: left; font-size: 11px;">
                                <input type="checkbox" name="avatar_filetypes[]" value="jpg" id="jpg" <?php if(in_array("jpg",$avatar_filetypes_array)){ echo "checked"; } ?> /> <label for="jpg">JPG</label> &nbsp;&nbsp;
                                <input type="checkbox" name="avatar_filetypes[]" value="gif" id="gif" <?php if(in_array("gif",$avatar_filetypes_array)){ echo "checked"; } ?> /> <label for="gif">GIF</label> &nbsp;&nbsp;
                                <input type="checkbox" name="avatar_filetypes[]" value="png" id="png" <?php if(in_array("png",$avatar_filetypes_array)){ echo "checked"; } ?> /> <label for="png">PNG</label> &nbsp;&nbsp;
                                <!--<input type="checkbox" name="avatar_filetypes[]" value="swf" <?php if(in_array("swf",$avatar_filetypes_array)){ echo "checked"; } ?> /> SWF &nbsp;&nbsp;-->
                            </div>
                        </div>
                    </div>
              </div>
			  <?php
			  	if(in_array("pro",$installed_addons))
				{
			?>
              <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_grp_login']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_grp_login_d']; ?></span>
                    </p>
                    <?php
						$login_groups = explode(",",$config['settings']['login_groups']);
						$mem_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'members' ORDER BY name");
						$mem_group_rows = mysqli_num_rows($mem_group_result);
						echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
						while($mem_group = mysqli_fetch_object($mem_group_result)){
							echo "<li><input type='checkbox' id='lg_$mem_group->gr_id' class='permcheckbox' name='login_groups[]' value='$mem_group->gr_id' "; if(in_array($mem_group->gr_id,$login_groups)){ echo "checked "; } echo "/> "; if($mem_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' /> "; } echo "<label for='lg_$mem_group->gr_id'>" . substr($mem_group->name,0,30)."</label></li>";
						}
						echo "</ul>";
					?>
                </div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_grp_signup']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_grp_signup_d']; ?></span>
                    </p>
                    <?php
						$signup_groups = explode(",",$config['settings']['signup_groups']);
						$mem_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'members' ORDER BY name");
						$mem_group_rows = mysqli_num_rows($mem_group_result);
						echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
						while($mem_group = mysqli_fetch_object($mem_group_result)){
							echo "<li><input type='checkbox' id='sg_$mem_group->gr_id' class='permcheckbox' name='signup_groups[]' value='$mem_group->gr_id' "; if(in_array($mem_group->gr_id,$signup_groups)){ echo "checked "; } echo "/> "; if($mem_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' /> "; } echo "<label for='sg_$mem_group->gr_id'>" . substr($mem_group->name,0,30)."</label></li>";
						}
						echo "</ul>";
					?>
                </div>
                <?php
					}
					if(in_array("contr",$installed_addons) or in_array("affiliate",$installed_addons)){
				?>
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['webset_f_compay']; ?>:<br />
							<span><?php echo $mgrlang['webset_f_compay_d']; ?></span>
						</p>
						<div style="float: left;">
                        	<?php
								$compays = explode(",",$config['settings']['compay']);
							?>
                            <input type="checkbox" name="compay[]" value="1" id="compay_op1" <?php if(in_array("1",$compays)){ echo "checked"; } ?> /> <label for="compay_op1"><?php echo $mgrlang['webset_f_compay_op1']; ?></label><br />
							<input type="checkbox" name="compay[]" value="2" id="compay_op2" <?php if(in_array("2",$compays)){ echo "checked"; } ?> /> <label for="compay_op2"><?php echo $mgrlang['webset_f_compay_op2']; ?></label><br />
							<input type="checkbox" name="compay[]" value="3" onclick="cb_bool('compay_op3','compay_div','');" id="compay_op3" <?php if(in_array("3",$compays)){ echo "checked"; } ?> /> <label for="compay_op3"><?php echo $mgrlang['webset_f_compay_op3']; ?></label>
                            <div style="padding: 6px; margin: 6px 6px 6px 0px; border: 1px solid #c7d3de; background-color: #dfe6ec; <?php if(!in_array("3",$compays)){ echo "display: none;"; } ?>" id="compay_div">
                            	<?php echo $mgrlang['webset_f_compay_op3b']; ?>:<br />
                                <input type="text" name="compay_other" style="width: 300px;" value="<?php echo $config['settings']['compay_other']; ?>">
                                <?php
									if(in_array('multilang',$installed_addons)){
								?>
									<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span><br />
									<a href="javascript:displaybool('lang_compay','','','','plusminus-05');"><img src="images/mgr.plusminus.0.gif" id="plusminus05" align="absmiddle" style="margin-right: 4px;" border="0" /></a><a href="javascript:displaybool('lang_compay','','','','plusminus-05');"><?php echo $mgrlang['gen_add_lang']; ?></a><br />
									<div id="lang_compay" style="display: none;">
									<?php
										foreach(array_unique($active_langs) as $value){
									?>
										<input type="text" name="compay_other_<?php echo $value; ?>" id="compay_other_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($config['settings']['compay_other' . "_" . $value]); ?>" /> <span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span><br />
								<?php
										}
										echo "</div>";
									}
								?>
                            </div>
                       	</div>
					</div>
				<?php
					}
				?>
            </div>
            
			
			
			<?php $row_color = 0; ?>
            <div id="tab2_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" fsrow='1' id="cart_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_cart']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_cart_d']; ?></span>
                    </p>
                    <div style="float: left;">
                        <select name="cart" id="cart" style="width: 150px;" onchange="check_cart_dd()">
                            <option value="0"><?php echo $mgrlang['gen_off']; ?></option>
                        <?php
                            if(in_array("creditsys",$installed_addons))
                            {
                                echo "<option value='1'"; if($config['settings']['cart'] == 1){ echo "selected='selected'"; } echo ">{$mgrlang[webset_cur_based]}</option>";
                                echo "<option value='2'"; if($config['settings']['cart'] == 2){ echo "selected='selected'"; } echo ">{$mgrlang[webset_cred_based]}</option>";
                                echo "<option value='3'"; if($config['settings']['cart'] == 3){ echo "selected='selected'"; } echo ">{$mgrlang[webset_both]}</option>";
                            }
                            else
                            {
                                echo "<option value='1'"; if($config['settings']['cart'] == 1){ echo "selected='selected'"; } echo ">{$mgrlang[webset_enabled]}</option>";
                            }
                        ?>
                        </select>
                    </div>
                    <div id="credit_selections" style="float: left; margin-left: 10px; border: 1px dotted #CCC; padding: 10px; background-color: #FFF; <?php if($config['settings']['cart'] == 0 or $config['settings']['cart'] == 1){ echo "display: none;"; } ?>">
                        <strong><?php echo $mgrlang['webset_pur_wcred']; ?>:</strong>
                        <br /><input type="checkbox" value="1" name="credits_digital" id="credits_digital" <?php if($config['settings']['credits_digital']){ echo "checked"; } ?> /> <label for="credits_digital"><?php echo $mgrlang['gen_dig_ver']; ?></label>
                        <!--<br /><input type="checkbox" value="1" name="credits_prof" id="credits_prof" <?php if($config['settings']['credits_prof']){ echo "checked"; } ?> /> Digital Profiles-->
                        <br /><input type="checkbox" value="1" name="credits_print" id="credits_print" <?php if($config['settings']['credits_print']){ echo "checked"; } ?> /> <label for="credits_print"><?php echo $mgrlang['gen_prints']; ?></label>
                        <br /><input type="checkbox" value="1" name="credits_prod" id="credits_prod" <?php if($config['settings']['credits_prod']){ echo "checked"; } ?> /> <label for="credits_prod"><?php echo $mgrlang['gen_prods']; ?></label>
                        <br /><input type="checkbox" value="1" name="credits_pack" id="credits_pack" <?php if($config['settings']['credits_pack']){ echo "checked"; } ?> /> <label for="credits_pack"><?php echo $mgrlang['gen_packs']; ?></label>
                        <br /><input type="checkbox" value="1" name="credits_coll" id="credits_coll" <?php if($config['settings']['credits_coll']){ echo "checked"; } ?> /> <label for="credits_coll"><?php echo $mgrlang['digital_collections']; ?></label>
                        <br /><input type="checkbox" value="1" name="credits_sub" id="credits_sub" <?php if($config['settings']['credits_sub']){ echo "checked"; } ?> /> <label for="credits_sub"><?php echo $mgrlang['gen_subs']; ?></label>
                    </div>
                </div>
				<?php
                    /*
                <div class="<?php fs_row_color(); ?>" fsrow='1' id="cart_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_currency']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_currency_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="enable_cbp" id="enable_cbp" <?php if($config['settings']['enable_cbp']){ echo "checked"; } ?> />
                    
                    <div style="float: left;"><input type="checkbox" value="1" onclick="cb_bool('cart','cart_ro','cart_ops');check_cur_cart();" name="cart" id="cart" <?php if($config['settings']['cart']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['cart']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="cart_ro"><a href="javascript:show_div('cart_ops');hide_div('cart_ro');" class="actionlink"><?php echo $mgrlang['webset_f_cart_ops']; ?></a></div>
                    <div id="cart_ops" class="related_options" style="width: 470px;">
                        <div style="position: absolute; right: 4px;"><a href="javascript:hide_div('cart_ops');show_div('cart_ro');check_cur_cart();"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                        <div class="fs_row_off" <?php if(!in_array("creditsys",$installed_addons)){ echo "style='display: none;'"; } ?>>
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_credits']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_credits_d']; ?></span>
                            </p>
                            <div style="float: left">
                            	<input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> />
                                <div style="margin-top: 10px; border: 1px dotted #CCC; padding: 10px; background-color: #FFF">
                                    <strong>Allow purchase with credits on:</strong>
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Digital Originals
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Digital Profiles
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Prints
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Products
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Packages
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Collections
                                    <br /><input type="checkbox" value="1" name="enable_credits" id="enable_credits" onclick="enable_cart();" <?php if($config['settings']['enable_credits']){ echo "checked"; } ?> /> Subscriptions
								</div>
                            </div>
                        </div>
                        <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['webset_f_currency']; ?>:<br />
                                <span><?php echo $mgrlang['webset_f_currency_d']; ?></span>
                            </p>
                            <input type="checkbox" value="1" name="enable_cbp" id="enable_cbp" onclick="enable_cart();" <?php if($config['settings']['enable_cbp']){ echo "checked"; } ?> />
                        </div>
                    </div>
					
                </div>*/
					?>
                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_ont']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_ont_d']; ?></span>
                    </p>
                    <div style="float: left;">
                    	<select name="order_num_type" id="order_num_type" style="width: 250px; float: left;" onchange="order_num_update();">
                            <option value="1" <?php if($config['settings']['order_num_type'] == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['webset_use_seq']; ?></option>
                            <option value="2" <?php if($config['settings']['order_num_type'] == 2){ echo "selected='selected'"; } ?>><?php echo $mgrlang['webset_use_rand']; ?></option>
                        </select><div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['order_num_type'] == 1){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="order_num_ro"><a href="javascript:show_div('order_num_options');hide_div('order_num_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    </div> 
                    <div id="order_num_options" class="related_options" style="margin-left: -250px;">
                        <div style="position: absolute; right: 4px;"><a href="javascript:hide_div('order_num_options');show_div('order_num_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                        <div class="fs_row_off">
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_non']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_non_d']; ?></span>
                        </p>
                        <input type="text" name="order_num_next"  value="<?php echo $config['settings']['order_num_next']; ?>" style="width: 100px;" />
                        </div>                            
                    </div>
                </div>
                
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_minicart']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_minicart_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="minicart" <?php if($config['settings']['minicart']){ echo "checked"; } ?> />
                </div>
                
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_signup_req']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_signup_req_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="accounts_required" <?php if($config['settings']['accounts_required']){ echo "checked"; } ?> />
                </div>
				
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_puragree']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_puragree_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="purchase_agreement" <?php if($config['settings']['purchase_agreement']){ echo "checked"; } ?> />
                </div>
				
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_taxid']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_taxid_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="customer_taxid" <?php if($config['settings']['customer_taxid']){ echo "checked"; } ?> />
                </div>
				
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_onotes']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_onotes_d']; ?>.</span>
                    </p>
                    <input type="checkbox" value="1" name="cart_notes" <?php if($config['settings']['cart_notes']){ echo "checked"; } ?> />
                </div>
                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_delete_carts']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_delete_carts_d']; ?></span>
                    </p>
                    <select name="delete_carts" style="width: 150px;">
                    	<option value="0" <?php if($config['settings']['delete_carts'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_never']; ?></option>
                        <option value="1" <?php if($config['settings']['delete_carts'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1d']; ?></option>
                        <option value="3" <?php if($config['settings']['delete_carts'] == 3){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_3d']; ?></option>
                        <option value="5" <?php if($config['settings']['delete_carts'] == 5){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_5d']; ?></option>
                        <option value="7" <?php if($config['settings']['delete_carts'] == 7){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1w']; ?></option>
                        <option value="14" <?php if($config['settings']['delete_carts'] == 14){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_2w']; ?></option>
                        <option value="30" <?php if($config['settings']['delete_carts'] == 30){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1m']; ?></option>
                        <option value="60" <?php if($config['settings']['delete_carts'] == 60){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_2m']; ?></option>
                        <option value="90" <?php if($config['settings']['delete_carts'] == 90){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_3m']; ?></option>
                        <option value="180" <?php if($config['settings']['delete_carts'] == 180){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_6m']; ?></option>
                        <option value="365" <?php if($config['settings']['delete_carts'] == 365){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1y']; ?></option>
                    </select>
                        
                    <!--<input type="text" name="delete_carts" id="delete_carts" style="width: 43px;" maxlength="3" value="<?php echo $config['settings']['delete_carts']; ?>" /> <?php echo $mgrlang['webset_f_days']; ?>-->
                </div>
                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_download_expire']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_download_expire_d']; ?></span>
                    </p>
                    <select name="expire_download" style="width: 150px;">
                    	<option value="0" <?php if($config['settings']['expire_download'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_never']; ?></option>
                        <option value="1" <?php if($config['settings']['expire_download'] == 1){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1d']; ?></option>
                        <option value="3" <?php if($config['settings']['expire_download'] == 3){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_3d']; ?></option>
                        <option value="5" <?php if($config['settings']['expire_download'] == 5){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_5d']; ?></option>
                        <option value="7" <?php if($config['settings']['expire_download'] == 7){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1w']; ?></option>
                        <option value="14" <?php if($config['settings']['expire_download'] == 14){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_2w']; ?></option>
                        <option value="30" <?php if($config['settings']['expire_download'] == 30){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1m']; ?></option>
                        <option value="60" <?php if($config['settings']['expire_download'] == 60){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_2m']; ?></option>
                        <option value="90" <?php if($config['settings']['expire_download'] == 90){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_3m']; ?></option>
                        <option value="180" <?php if($config['settings']['expire_download'] == 180){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_6m']; ?></option>
                        <option value="365" <?php if($config['settings']['expire_download'] == 365){ echo "selected"; } ?>><?php echo $mgrlang['webset_time_1y']; ?></option>
                    </select>
                </div>
                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_dl_attempts']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_dl_attempts_d']; ?></span>
                    </p>
                    <select name="dl_attempts" style="width: 100px;">
                    	<option value="0" <?php if($config['settings']['dl_attempts'] == 0){ echo "selected"; } ?>><?php echo $mgrlang['webset_f_dl_unlimited']; ?></option>
                        <option value="1" <?php if($config['settings']['dl_attempts'] == 1){ echo "selected"; } ?>>1</option>
                        <option value="2" <?php if($config['settings']['dl_attempts'] == 2){ echo "selected"; } ?>>2</option>
                        <option value="3" <?php if($config['settings']['dl_attempts'] == 3){ echo "selected"; } ?>>3</option>
                        <option value="4" <?php if($config['settings']['dl_attempts'] == 4){ echo "selected"; } ?>>4</option>
                        <option value="5" <?php if($config['settings']['dl_attempts'] == 5){ echo "selected"; } ?>>5</option>
                        <option value="6" <?php if($config['settings']['dl_attempts'] == 6){ echo "selected"; } ?>>6</option>
                        <option value="7" <?php if($config['settings']['dl_attempts'] == 7){ echo "selected"; } ?>>7</option>
                        <option value="8" <?php if($config['settings']['dl_attempts'] == 8){ echo "selected"; } ?>>8</option>
                        <option value="9" <?php if($config['settings']['dl_attempts'] == 9){ echo "selected"; } ?>>9</option>
                        <option value="10" <?php if($config['settings']['dl_attempts'] == 10){ echo "selected"; } ?>>10</option>
                        <option value="20" <?php if($config['settings']['dl_attempts'] == 20){ echo "selected"; } ?>>20</option>
                    </select>
                        
                    <!--<input type="text" name="delete_carts" id="delete_carts" style="width: 43px;" maxlength="3" value="<?php echo $config['settings']['delete_carts']; ?>" /> <?php echo $mgrlang['webset_f_days']; ?>-->
                </div>
                <?php
					/*
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_dle']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_dle_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="download_extensions" <?php if($config['settings']['download_extensions']){ echo "checked"; } ?> />
                </div>
                	*/
				?>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_auto_orders']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_auto_orders_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="auto_orders" <?php if($config['settings']['auto_orders']){ echo "checked"; } ?> />
                </div>
                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_default_price']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_default_price_d']; ?></span>
                    </p>
                    <input type="text" name="default_price" id="default_price" style="width: 43px;" maxlength="7" value="<?php echo $cleanvalues->currency_display($config['settings']['default_price']); ?>" />
                </div>                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_min_total']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_min_total_d']; ?></span>
                    </p>
                    <input type="text" name="min_total" id="min_total" style="width: 43px;" maxlength="7" value="<?php echo $cleanvalues->currency_display($config['settings']['min_total']); ?>" />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_fotomoto']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_fotomoto_d']; ?></span>
                    </p>
                    <input type="text" name="fotomoto" id="fotomoto" style="width: 250px;" value="<?php echo $config['settings']['fotomoto']; ?>" />
                </div>
                <?php
                    if(in_array("creditsys",$installed_addons)){
                ?>
                <div class="fs_header"><?php echo $mgrlang['webset_f_credsys']; ?></div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_default_credits']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_default_credits_d']; ?></span>
                    </p>
                    <input type="text" name="default_credits" id="default_credits" style="width: 43px;" maxlength="7" value="<?php echo $config['settings']['default_credits']; ?>" />
                </div>                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_skipship']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_skipship_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="skip_shipping" <?php if($config['settings']['skip_shipping']){ echo "checked"; } ?> />
                </div>
                <?php
					}
				?>
            </div>
            
            <?php
            	$row_color = 0;				
			?>
            <div id="tab10_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7;">
                    
                        <img src="images/mgr.icon.invoice.png" align="left" style="margin-right: 10px; margin-left: 10px;" />
                        <div style="padding-top: 10px;"><?php echo $mgrlang['webset_f_invoice_preview']; ?>:</div>
                        <div id="invoice_preview" style="font-weight: bold;">
							<?php
								echo $config['settings']['invoice_prefix'];
								echo $config['settings']['invoice_next'];
								echo $config['settings']['invoice_suffix'];
							?>
                        </div>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_invoice_prefix']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_invoice_prefix_d']; ?></span>
                    </p>
                    <input type="text" name="invoice_prefix" id="invoice_prefix" maxlength="20" onkeyup="invoice_preview();" style="width: 120px;" maxlength="250" value="<?php echo $config['settings']['invoice_prefix']; ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" style="overflow: auto;">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_invoice_next']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_invoice_next_d']; ?></span>
                    </p>
                    <div style="float: left;"><input type="text" name="invoice_next" id="invoice_next" maxlength="20" onkeyup="invoice_preview();" style="width: 120px;" maxlength="250" value="<?php echo $config['settings']['invoice_next']; ?>" /></div>
                    <div style="float: left; display: none; background-image:url(images/mgr.invoicenum.warn.png); background-repeat: no-repeat; width: 157px; height: 34px; padding: 12px 10px 20px 30px; margin: -4px 0 0 0; font-size: 11px" id="invoice_warn"><?php echo $mgrlang['webset_mes_invoice_warn']; ?></div>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_invoice_suffix']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_invoice_suffix_d']; ?></span>
                    </p>
                    <input type="text" name="invoice_suffix" id="invoice_suffix" maxlength="20" onkeyup="invoice_preview();" style="width: 120px;" maxlength="250" value="<?php echo $config['settings']['invoice_suffix']; ?>" />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab3_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" id="support_email_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_support_email']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_support_email_d']; ?></span>
                    </p>
                    <input type="text" name="support_email" id="support_email" style="width: 300px;" maxlength="250" value="<?php echo $config['settings']['support_email']; ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" id="sales_email_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_sales_email']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_sales_email_d']; ?></span>
                    </p>
                    <input type="text" name="sales_email" id="sales_email" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['sales_email']; ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" id="business_name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_business_name']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_business_name_d']; ?></span>
                    </p>
                    <input type="text" name="business_name" id="business_name" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['business_name']; ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" id="business_address_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_address']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_address_d']; ?></span>
                    </p>
                    <input type="text" name="business_address" id="business_address" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['business_address']; ?>" /><br />
                    <input type="text" name="business_address2" id="business_address2" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['business_address2']; ?>" />
                </div>                
                <div class="<?php fs_row_color(); ?>" id="business_city_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_business_city']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_business_city_d']; ?></span>
                    </p>
                    <input type="text" name="business_city" id="business_city" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['business_city']; ?>" />
                </div>
                <div class="<?php fs_row_color(); ?>" id="business_country_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_business_country']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_business_country_d']; ?></span>
                    </p>
                    <select style="width: 311px;" align="absmiddle" id="business_country" name="business_country" onchange="get_regions();" >
                        <option value="#"></option>
                        <?php
							$country_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}countries WHERE deleted='0' ORDER BY name");
							while($country = mysqli_fetch_object($country_result)){
								echo "<option value='$country->country_id'";
									if($config['settings']['business_country'] == $country->country_id){ echo "selected"; }
								echo ">$country->name</option>";
							}
						?>
                    </select>
                </div>                
                <div class="<?php fs_row_color(); ?>" id="business_state_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_business_state']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_business_state_d']; ?></span>
                    </p>
                    <!--<input type="text" name="business_state" id="business_state" style="width: 298px;" maxlength="250" value="<?php echo $config['settings']['business_state']; ?>" />-->
					<div id="region_div"></div>
                </div>                
                <div class="<?php fs_row_color(); ?>" id="business_zip_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_business_zip']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_business_zip_d']; ?></span>
                    </p>
                    <input type="text" name="business_zip" id="business_zip" style="width: 110px;" maxlength="250" value="<?php echo $config['settings']['business_zip']; ?>" />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab4_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_notify_sale']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_notify_sale_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="notify_sale" <?php if($config['settings']['notify_sale']){ echo "checked"; } ?> />
                </div>                
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_notify_account']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_notify_account_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="notify_account" <?php if($config['settings']['notify_account']){ echo "checked"; } ?> />
                </div>
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_notify_profile']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_notify_profile_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="notify_profile" <?php if($config['settings']['notify_profile']){ echo "checked"; } ?> />
                </div>				
				<?php
                    if(in_array("rating",$installed_addons)){
                ?>	
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_notify_rating']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_notify_rating_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="notify_rating" <?php if($config['settings']['notify_rating']){ echo "checked"; } ?> />
                    </div>
                <?php
                    }
                    if(in_array("tagging",$installed_addons)){
                ?>	
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_new_tags']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_new_tags_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="notify_tags" <?php if($config['settings']['notify_tags']){ echo "checked"; } ?> />
                    </div>
                <?php
                    }
					if(in_array("commenting",$installed_addons)){
                ?>	
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_notify_comment']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_notify_comment_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="notify_comment" <?php if($config['settings']['notify_comment']){ echo "checked"; } ?> />
                    </div>
                <?php
                    }
					if(in_array("lightbox",$installed_addons)){
                ?>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_notify_lightbox']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_notify_lightbox_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="notify_lightbox" <?php if($config['settings']['notify_lightbox']){ echo "checked"; } ?> />
                    </div>
                <?php
					}				
					if(in_array("contr",$installed_addons)){
				?>
                	<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_notify_contrup']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_notify_contrup_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="notify_contrup" <?php if($config['settings']['notify_contrup']){ echo "checked"; } ?> />
                    </div>
                <?php
					}
				?>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab5_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_search']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_search_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search" <?php if($config['settings']['search']){ echo "checked"; } ?> />
                </div>
				<?php
				/*
                <div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_esearch']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_esearch_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="esearch" <?php if($config['settings']['esearch']){ echo "checked"; } ?> />
                </div>
				*/
				?>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_fileds']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_fileds_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_fields" <?php if($config['settings']['search_fields']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_types']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_types_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_media_types" <?php if($config['settings']['search_media_types']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_orien']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_orien_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_orientation" <?php if($config['settings']['search_orientation']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_color']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_color_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_color" <?php if($config['settings']['search_color']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_dates']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_dates_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_dates" <?php if($config['settings']['search_dates']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_lictype']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_lictype_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_license_type" <?php if($config['settings']['search_license_type']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_galleries']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_galleries_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="search_galleries" <?php if($config['settings']['search_galleries']){ echo "checked"; } ?> />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab6_group" class="group" style="display: none;">
            	No longer needed                
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab7_group" class="group" style="display: none;">
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_forum_link']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_forum_link_d']; ?></span>
                    </p>
                    <input type="text" name="forum_link" id="forum_link" style="width: 300px;" maxlength="250" value="<?php echo $config['settings']['forum_link']; ?>" />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_facebook_link']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_facebook_link_d']; ?></span>
                    </p>
                    <input type="text" name="facebook_link" id="facebook_link" style="width: 300px;" maxlength="250" value="<?php echo $config['settings']['facebook_link']; ?>" />
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_twitter_link']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_twitter_link_d']; ?></span>
                    </p>
                    <input type="text" name="twitter_link" id="twitter_link" style="width: 300px;" maxlength="250" value="<?php echo $config['settings']['twitter_link']; ?>" />
                </div>
				<!--
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_poe']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_poe_d']; ?></span>
                    </p>
                    <div style="float: left; overflow: auto" id="lab_contacts">                    	
						<?php
							# SELECT LABS
                        	$lab_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}lab_contacts ORDER BY lab_id");
							$lab_rows = mysqli_num_rows($lab_result);
							
								echo "<div id='lab_title_row' style='clear: both;";
								if(!$lab_rows){ 
									echo " display: none;";
								}
								echo "'><p style='width: 115px; float: left;'>$mgrlang[webset_lab_name]</p><p style='width: 119px; float: left;'>$mgrlang[webset_lab_email]</p></div>";
							echo "<div style='clear: both;' id='lab_row0' class='lab_rows'></div>"; // FAKE ROW
                        	while($lab = mysqli_fetch_object($lab_result)){
						?>
                            <div style="clear: both; padding-top: 4px;" id="lab_row<?php echo $lab->lab_id; ?>" class="lab_rows">
                                <input type="hidden" name="lab_id[]" value="<?php echo $lab->lab_id; ?>" />
                                <input type="text" name="lab_name[]" value="<?php echo $lab->name; ?>" maxlength="250" style="width: 114px; float: left; margin-right: 5px;" />
                                <input type="text" name="lab_email[]" value="<?php echo $lab->email; ?>" maxlength="250" style="width: 114px; float: left; margin-right: 5px;" />
                                <a href="javascript:remove_lab_row('lab_row<?php echo $lab->lab_id; ?>','<?php echo $lab->lab_id; ?>');" class='actionlink' style='float: left; font-weight: normal'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                            </div>
                        <?php
							}
						?>
                        <p style="clear: both; margin: 5px 0 0 0; padding: 0; width: 308px;" align="right"><a href="javascript:add_lab_row();" class='actionlink' style='float: left; font-weight: normal'><img src="images/mgr.icon.greenplus.gif" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" />&nbsp;<?php echo $mgrlang['webset_b_lab']; ?>&nbsp;</a></p>
                    </div>
                </div>
				-->
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab8_group" class="group" style="display: none;">
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
                        <?php echo $mgrlang['setup_f_email_conf']; ?>:<br />
                        <span><?php echo $mgrlang['setup_f_email_conf_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="email_conf" <?php if($config['settings']['email_conf']){ echo "checked"; } ?> />
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_regfield']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_regfield_d']; ?></span>
                    </p>
                    <div style="float: left;" class="fs_row_part2">
						<?php
							$regForm = '';
							$regFormResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}registration_form");
							while($regFormDB = mysqli_fetch_array($regFormResult))
							{
								$regForm[$regFormDB['field_id']] = $regFormDB;
							}
							$checked = "checked='checked'";
						?>
						<table>
							<tr>
								<th><?php echo $mgrlang['webset_h_info']; ?></th>
								<th><?php echo $mgrlang['webset_h_disabled']; ?></th>
								<th><?php echo $mgrlang['webset_h_request']; ?></th>
								<th><?php echo $mgrlang['webset_h_require']; ?></th>
							</tr>
							<tr>
								<td><?php echo $mgrlang['mem_f_fname']; ?></td>
								<td align="center"><input type="radio" name="formFirstName" value="0" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formFirstName" value="1" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formFirstName" value="2" disabled="disabled" checked="checked" /></td>
							</tr>
							<tr style="background-color: #EEE">
								<td><?php echo $mgrlang['mem_f_lname']; ?></td>
								<td align="center"><input type="radio" name="formLastName" value="0" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formLastName" value="1" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formLastName" value="2" disabled="disabled" checked="checked" /></td>
							</tr>
							<tr>
								<td><?php echo $mgrlang['mem_f_email']; ?></td>
								<td align="center"><input type="radio" name="formEmail" value="0" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formEmail" value="1" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formEmail" value="2" disabled="disabled" checked="checked" /></td>
							</tr>
							<tr style="background-color: #EEE">
								<td><?php echo $mgrlang['mem_f_password']; ?></td>
								<td align="center"><input type="radio" name="formPassword" value="0" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formPassword" value="1" disabled="disabled" /></td>
								<td align="center"><input type="radio" name="formPassword" value="2" disabled="disabled" checked="checked" /></td>
							</tr>
							<tr>
								<td><?php echo $mgrlang['mem_f_address']; ?></td>
								<td align="center"><input type="radio" name="formAddress" value="0" <?php if($regForm['formAddress']['status'] == 0){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formAddress" value="1" <?php if($regForm['formAddress']['status'] == 1){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formAddress" value="2" <?php if($regForm['formAddress']['status'] == 2){ echo $checked; } ?> /></td>
							</tr>
							<tr style="background-color: #EEE">
								<td><?php echo $mgrlang['mem_f_phone']; ?></td>
								<td align="center"><input type="radio" name="formPhone" value="0" <?php if($regForm['formPhone']['status'] == 0){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formPhone" value="1" <?php if($regForm['formPhone']['status'] == 1){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formPhone" value="2" <?php if($regForm['formPhone']['status'] == 2){ echo $checked; } ?> /></td>
							</tr>
							<tr>
								<td><?php echo $mgrlang['mem_f_company_name']; ?></td>
								<td align="center"><input type="radio" name="formCompanyName" value="0" <?php if($regForm['formCompanyName']['status'] == 0){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formCompanyName" value="1" <?php if($regForm['formCompanyName']['status'] == 1){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formCompanyName" value="2" <?php if($regForm['formCompanyName']['status'] == 2){ echo $checked; } ?> /></td>
							</tr>
							<tr style="background-color: #EEE">
								<td><?php echo $mgrlang['mem_f_website']; ?></td>
								<td align="center"><input type="radio" name="formWebsite" value="0" <?php if($regForm['formWebsite']['status'] == 0){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formWebsite" value="1" <?php if($regForm['formWebsite']['status'] == 1){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formWebsite" value="2" <?php if($regForm['formWebsite']['status'] == 2){ echo $checked; } ?> /></td>
							</tr>
							<tr>
								<td><?php echo $mgrlang['webset_signup_argee']; ?></td>
								<td align="center"><input type="radio" name="formSignupAgreement" value="0" <?php if($regForm['formSignupAgreement']['status'] == 0){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formSignupAgreement" value="1" <?php if($regForm['formSignupAgreement']['status'] == 1){ echo $checked; } ?> /></td>
								<td align="center"><input type="radio" name="formSignupAgreement" value="2" <?php if($regForm['formSignupAgreement']['status'] == 2){ echo $checked; } ?> /></td>
							</tr>
						</table>
					</div>
                </div>
				<div class="<?php fs_row_color(); ?>" fsrow='1'>
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['webset_f_mships']; ?>:<br />
                        <span><?php echo $mgrlang['webset_f_mships_d']; ?></span>
                    </p>
                    <input type="checkbox" value="1" name="reg_memberships" <?php if($config['settings']['reg_memberships']){ echo "checked"; } ?> />
                </div>
            </div>
            
            <!-- ACTIONS BAR AREA -->
            <div id="save_bar">							
                <div style="float: right;"><input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.settings.php?ep=1');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" /></div>
            </div>                  
            </div>
            <div class="footer_spacer"></div>
            <!-- END CONTENT -->
            </form>
        </div>
        
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>
