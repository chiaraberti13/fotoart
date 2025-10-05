<?php
	###################################################################
	####	MANAGER SOFTWARE SETTUP PAGE                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 1-19-2008                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "look_feel";
		$lnav = "settings";
	
		$supportPageID = '367';
	
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
		
		if($_POST){
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');

			# MAKE RESTORE POINT
			if($config['settings']['auto_rp']){
				DuplicateMySQLRecord($dbinfo[pre].'settings', 'settings_id', '1');
			}
			
			# SAVE ANY FILES BEING UPLOADED AND RECORD INTO DATABASE
			if($_FILES['uploadWatermark']['name'] != ""){
				upload_file($_FILES['uploadWatermark'],$_FILES['uploadWatermark']['name'],"../assets/watermarks/");
			}
			# DELETE ANY FILES THAT WERE UPLOADED
			if($_POST['deleteWatermark']){
				foreach($_POST['deleteWatermark'] as $value){
					if(file_exists("../assets/watermarks/$value")){
						unlink("../assets/watermarks/$value");
					}
				}
			}
				
			// If checkbox is unchecked make sure you default these to 0
			if(!$thumb_sharpen) $thumb_sharpen = 0;
			if(!$rollover_sharpen) $rollover_sharpen = 0;
			if(!$preview_sharpen) $preview_sharpen = 0;
			if(!$thumbcrop) $thumbcrop = 0;
			if(!$rollovercrop) $rollovercrop = 0;
			if(!$gallerythumbcrop) $gallerythumbcrop = 0;
			
			//CHECK TO MAKE SURE SETTINGS ARE NOT LARGER THAN LIMITS
			if($gpswidth > 640){
				$gpswidth = 640;
			}
			if($gpsheight > 640){
				$gpsheight = 640;
			}
			if($gpszoom > 20){
				$gpszoom = 20;
			}
			if($zoomlenssize > 600){
				$zoomlenssize = 600;
			}
			
			// Thumbnail clear cache
			if
				(
					$config['settings']['thumb_size'] != $thumb_size or 
					$config['settings']['thumb_quality'] != $thumb_quality or 
					$config['settings']['thumb_wm'] != $thumb_watermark or 
					$config['settings']['thumbcrop'] != $thumbcrop or 
					$config['settings']['thumbcrop_height'] != $thumbcrop_height or 
					$config['settings']['rollover_size'] != $rollover_size or 
					$config['settings']['rollovercrop'] != $rollovercrop or 
					$config['settings']['rollovercrop_height'] != $rollovercrop_height or 
					$config['settings']['rollover_quality'] != $rollover_quality or 
					$config['settings']['rollover_wm'] != $rollover_watermark or 
					$config['settings']['preview_size'] != $preview_size or 
					$config['settings']['preview_quality'] != $preview_quality or 
					$config['settings']['preview_wm'] != $preview_watermark or 
					$config['settings']['thumb_sharpen'] != $thumb_sharpen or 
					$config['settings']['rollover_sharpen'] != $rollover_sharpen or 
					$config['settings']['preview_sharpen'] != $preview_sharpen or 
					$config['settings']['featured_wm'] != $featured_wm
				)
			{
				clearCache();
				$clearCache = true; // Testing
			}
			
			// Gallery image clear cache
			if
				(
					$config['settings']['gallery_thumb_size'] != $gallery_thumb_size or 
					$config['settings']['gallerythumbcrop'] != $gallerythumbcrop or 
					$config['settings']['gallerythumbcrop_height'] != $gallerythumbcrop_height				
				)
			{
				clearCache('../assets/cache/ps*');
				$clearCache = true; // Testing
			}
			
			# SEE IF MENU NEEDS TO BE REBUILT
			if($gallery_count_orig == 0 and $gallery_count == 1)
			{
				$sql = "UPDATE {$dbinfo[pre]}settings SET menubuild='$status' WHERE settings_id  = '1'"; // DIDN'T USE FUNCTION BECAUSE IT NEEDS TO BE FORCED SINCE $config['settings']['gallery_count'] SETTINGS WEREN'T UPDATED
				$_SESSION['menubuildalert'] = $status;
			}
			
			//if($thumb_watermark_resize == true){}
			
			//echo $thumb_watermark_resize; exit;
			
			if($thumb_details) $thumb_details = implode(",",$thumb_details);
			if($rollover_details) $rollover_details = implode(",",$rollover_details);
			if($preview_details) $preview_details = implode(",",$preview_details);
			
			$colorScheme = $_POST['colorScheme-'.$theme]; // Get the color scheme that was set
			
			# UPDATE THE SETTINGS DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings SET 
						style='$theme',
						thumb_size='$thumb_size',
						thumb_quality='$thumb_quality',
						thumb_wm='$thumb_watermark',
						thumb_wmr='$thumb_watermark_resize',
						thumbcrop_height='$thumbcrop_height',
						rollover_status='$rollover_status',
						rollover_size='$rollover_size',
						rollovercrop_height='$rollovercrop_height',
						rollover_quality='$rollover_quality',
						rollover_wm='$rollover_watermark',
						rollover_wmr='$rollover_watermark_resize',
						rolloverDetailsRating='$rolloverDetailsRating',
						previewDetailsRating='$previewDetailsRating',
						display_license='$display_license',
						preview_size='$preview_size',
						preview_quality='$preview_quality',
						preview_wm='$preview_watermark',
						preview_wmr='$preview_watermark_resize',
						preview_image='$preview_image',
						gpsonoff='$gpsonoff',
						gpswidth='$gpswidth',
						gpsheight='$gpsheight',
						gpszoom='$gpszoom',
						gpscolor='$gpscolor',
						gpsmaptype='$gpsmaptype',
						zoomonoff='$zoomonoff',
						zoombordercolor='$zoombordercolor',
						zoombordersize='$zoombordersize',
						zoomlenssize='$zoomlenssize',
						gallery_thumb_size='$gallery_thumb_size',
						gallerythumbcrop='$gallerythumbcrop',
						gallery_perpage='$gallery_perpage',
						gallerythumbcrop_height='$gallerythumbcrop_height',
						thumb_details='$thumb_details',
						thumbDetailsRating='$thumbDetailsRating',
						thumbDetailsCart='$thumbDetailsCart',
						thumbDetailsLightbox='$thumbDetailsLightbox',
						thumbDetailsPackage='$thumbDetailsPackage',
						thumbDetailsEmail='$thumbDetailsEmail',
						rollover_details='$rollover_details',
						preview_details='$preview_details',
						thumb_sharpen='$thumb_sharpen',
						rollover_sharpen='$rollover_sharpen',
						preview_sharpen='$preview_sharpen',						
						media_perpage='$media_perpage',
						site_stats='$site_stats',
						dsorting='$dsorting',
						dsorting2='$dsorting2',
						color_scheme='{$colorScheme}',
						gallery_count='$gallery_count',
						members_online='$members_online',
						new_media_count='$new_media_count',
						popular_media_count='$popular_media_count',
						random_media_count='$random_media_count',
						related_media='$related_media',						
						news='$news',
						display_login='$display_login',
						contact='$contact',
						aboutpage='$aboutpage',						
						pppage='$pppage',
						papage='$papage',
						tospage='$tospage',
						promopage='$promopage',
						featuredpage='$featuredpage',
						creditpage='$creditpage',
						newestpage='$newestpage',
						popularpage='$popularpage',
						printpage='$printpage',
						prodpage='$prodpage',
						packpage='$packpage',
						collpage='$collpage',
						thumbcrop='$thumbcrop',
						rollovercrop='$rollovercrop',
						subpage='$subpage',
						hpnews='$hpnews',
						hppromos='$hppromos',
						hprandmedia='$hprandmedia',
						hpnewestmedia='$hpnewestmedia',
						hpfeaturedmedia='$hpfeaturedmedia',
						hppopularmedia='$hppopularmedia',
						hpprints='$hpprints',
						hpprods='$hpprods',
						hppacks='$hppacks',
						hpcolls='$hpcolls',
						hpsubs='$hpsubs',
						hpcredits='$hpcredits',
						sn_code='{$sn_code}',
						display_iptc='$display_iptc',
						display_exif='$display_exif',
						video_controls='$video_controls',
						video_autoplay='$video_autoplay',
						video_rollover_width='$video_rollover_width',
						video_rollover_height='$video_rollover_height',
						video_sample_width='$video_sample_width',
						video_sample_height='$video_sample_height',
						video_autorepeat='$video_autorepeat',
						video_bg_color='$video_bg_color',
						video_skin='$video_skin',
						vidrollover_wm='$vidrollover_wm',
						vidpreview_wm='$vidpreview_wm',
						video_autoresize='$video_autoresize',
						video_wmpos='$video_wmpos',
						hpf_width='$hpf_width',
						hpf_crop_to='$hpf_crop_to',
						hpf_fade_speed='$hpf_fade_speed',
						hpf_inverval='$hpf_inverval',
						hpf_details_delay='$hpf_details_delay',
						hpf_details_distime='$hpf_details_distime',
						featured_wm='$featured_wm'
						WHERE settings_id  = '1'";
			$result = mysqli_query($db,$sql);
			# UPDATE THE SETTINGS2 DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings2 SET ";
						# CONTRIBUTORS ADD-ON
						if(in_array("contr",$installed_addons)){
							$sql.= "contrib_link='$contrib_link',";
						}
			$sql.= "
				share='{$share}',
				thumbDetailsDownloads='{$thumbDetailsDownloads}',
				tagCloudSort='{$tagCloudSort}',
				tagCloudOn='{$tagCloudOn}',
				gallerySortBy='{$gallerySortBy}',
				gallerySortOrder='{$gallerySortOrder}' 
				WHERE settings_id  = '1'";
			$result = mysqli_query($db,$sql);
			
			//echo $sql; exit;
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_look'],1,"<strong>".$mgrlang['gen_b_sav']."</strong>");
			
			header("location: mgr.look.feel.php?ep=1&mes=saved");
			exit;
		}
		
		# OUTPUT MESSAGES
		if($_GET['mes'] == "saved"){
			$vmessage = $mgrlang['changes_saved'];
		}
		
		# READ ALL WATERMARK FILES
		$real_dir = realpath("../assets/watermarks/");
		$dir = opendir($real_dir);
		# LOOP THROUGH THE WATERMARK DIRECTORY
		$watermarks = array();
		while($file = readdir($dir)){
			// MAKE SURE IT IS A VALID FILE
			$ispng = explode(".", $file);
			if($file != ".." && $file != "." && is_file("../assets/watermarks/" . $file) && @$ispng[count($ispng) - 1] == "png"){
				$watermark[] = "$file";
			}
		}
		
		// CALCULATE MAX UPLOAD SIZE FOR LOGO
		if(ini_get("upload_max_filesize"))
		{
			$upload_limit = ini_get("upload_max_filesize") * 1024;
			$upload_limit-= 50;
		}
		else
		{
			$upload_limit = 1950;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_look']; ?></title>
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
    <!-- FLASH OBJECT -->
	<script type="text/javascript" src="../assets/javascript/swfobject.js"></script>  
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
    
	<script>
		function form_sumbitter(){
			$('media_perpage_div').className='fs_row_on';
			//$('support_email_div').className='fs_row_off';
			//$('sales_email_div').className='fs_row_on';
			//$('business_address_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = "mgr.look.feel.php";
					js_validate_field("media_perpage","lookfeel_f_mpp",4);
					//js_validate_field("support_email","webset_f_support_email",3);
					//js_validate_field("sales_email","webset_f_sales_email",3);
					//js_validate_field("business_address","webset_f_address",3);
				}
			?>
		}
		
		// LOGO FLASH UPLOADER
		/*
		var flashObj = new SWFObject("./mgr.single.uploader.swf", "uploadDownload", "300", "40", 8, "#FFFFFF", true);
		flashObj.addVariable ("myextensions", "*.jpg;*.gif;*.png;*.jpeg");
		flashObj.addVariable ("uploadUrl", "mgr.look.feel.actions.php");
		flashObj.addVariable ("uploadParms","?mode=upload_logo%26pass=<?php echo md5($config['settings']['serial_number']); ?>"); 
		flashObj.addVariable ("maxFileSize", "<?php echo maxUploadSize('kb'); ?>");
		flashObj.addVariable ("maxFileSizeError", "<?php echo $mgrlang['gen_error_25']; ?>");
		flashObj.addVariable ("uploadButtonLabel", "<?php echo $mgrlang['gen_b_upload']; ?>");
		flashObj.addVariable ("allowScriptAccess","always");
		//flashObj.addParam ("wmode","opaque");
		*/
		var flashvars = {
			myextensions: "*.jpg;*.gif;*.png;*.jpeg",
			uploadUrl: "mgr.look.feel.actions.php",
			uploadParms: "?mode=upload_logo%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26adminID=<?php echo $_SESSION['admin_user']['admin_id']; ?>",
			maxFileSize: "<?php echo maxUploadSize('kb'); ?>",
			maxFileSizeError: "<?php echo $mgrlang['gen_error_25']; ?>",
			uploadButtonLabel: "<?php echo $mgrlang['gen_b_upload']; ?>"
		};
		var params = {
			bgcolor: "#FFFFFF",
			allowScriptAccess: "always",
			wmode: "opaque"
		};
		var attributes = {
			id: "uploader",
			tester: "1234"
		}; 
		
		var thumb_size_slider;
		var thumb_quality_slider;
		var rollover_size_slider;
		var rollover_quality_slider;
		var preview_size_slider;
		var preview_quality_slider;
		var gpswidth_size_slider;
		var zoom_size_slider;
		var zoomborder_size_slider;
		
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
			
			//bringtofront('5');
			// MAKE THE THUMB DETAILS SORTABLE
			Sortable.create('thumb_details_sort',{tag:'li',overlap:'verticle',constraint:false,dropOnEmpty:false,handle:'handle'});
			// MAKE THE ROLLOVER DETAILS SORTABLE
			Sortable.create('rollover_details_sort',{tag:'li',overlap:'verticle',constraint:false,dropOnEmpty:false,handle:'handle'});
			// MAKE THE PREVIEW DETAILS SORTABLE
			Sortable.create('preview_details_sort',{tag:'li',overlap:'verticle',constraint:false,dropOnEmpty:false,handle:'handle'});
			
			thumb_size_slider = new Control.Slider('thumb_size_ball','thumb_size_track', {
							   	range: $R(100, 500),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['thumb_size']; ?>,
							   	onSlide: function(value){
											preview_resizer(value,'thumb');
										},
								onChange: function(value){
											//show_loader('thumb_quality_details');
											preview_resizer(value,'thumb');
											render_image('thumb');
											//get_image_filesize('thumb');
										}
							});
			thumb_quality_slider = new Control.Slider('thumb_quality_ball','thumb_quality_track', {
							   	range: $R(5, 100),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['thumb_quality']; ?>,
							   	onSlide: function(value){
											preview_qualiziter(value,'thumb');
										},
								onChange: function(value){
											//show_loader('thumb_quality_details');
											preview_qualiziter(value,'thumb');
											render_image('thumb');
										}
							});
			rollover_size_slider = new Control.Slider('rollover_size_ball','rollover_size_track', {
							   	range: $R(100, 500),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['rollover_size']; ?>,
							   	onSlide: function(value){
											preview_resizer(value,'rollover');
										},
								onChange: function(value){
											//show_loader('rollover_quality_details');
											preview_resizer(value,'rollover');
											render_image('rollover');
										}
							});
			rollover_quality_slider = new Control.Slider('rollover_quality_ball','rollover_quality_track', {
							   	range: $R(5, 100),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['rollover_quality']; ?>,
							   	onSlide: function(value){
											preview_qualiziter(value,'rollover');
										},
								onChange: function(value){
											//show_loader('rollover_quality_details');
											preview_qualiziter(value,'rollover');
											render_image('rollover');
										}
							});
			preview_size_slider = new Control.Slider('preview_size_ball','preview_size_track', {
							   	range: $R(100, 1024),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['preview_size']; ?>,
							   	onSlide: function(value){
											preview_resizer(value,'preview');
										},
								onChange: function(value){
											//show_loader('preview_quality_details');
											preview_resizer(value,'preview');
											render_image('preview');
										}
							});
			preview_quality_slider = new Control.Slider('preview_quality_ball','preview_quality_track', {
							   	range: $R(5, 100),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['preview_quality']; ?>,
							   	onSlide: function(value){
											preview_qualiziter(value,'preview');
										},
								onChange: function(value){
											//show_loader('preview_quality_details');
											preview_qualiziter(value,'preview');
											render_image('preview');
										}
							});
			gpswidth_size_slider = new Control.Slider('gpswidth_size_ball','gpswidth_size_track', {
							   	range: $R(50, 640),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['gpswidth']; ?>,
							   	onSlide: function(value){
											ap_sizer(value,'gpswidth');
										},
								onChange: function(value){
											ap_sizer(value,'gpswidth');
										}
							});
			gpsheight_size_slider = new Control.Slider('gpsheight_size_ball','gpsheight_size_track', {
							   	range: $R(50, 640),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['gpsheight']; ?>,
							   	onSlide: function(value){
											ap_sizer(value,'gpsheight');
										},
								onChange: function(value){
											ap_sizer(value,'gpsheight');
										}
							});
			gpszoom_size_slider = new Control.Slider('gpszoom_size_ball','gpszoom_size_track', {
							   	range: $R(1, 20),
								increment: 1,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['gpszoom']; ?>,
							   	onSlide: function(value){
											ap_sizer(value,'gpszoom');
										},
								onChange: function(value){
											ap_sizer(value,'gpszoom');
										}
							});
			zoom_size_slider = new Control.Slider('zoom_size_ball','zoom_size_track', {
							   	range: $R(50, 600),
								increment: 10,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['zoomlenssize']; ?>,
							   	onSlide: function(value){
											ap_sizer(value,'zoomlenssize');
										},
								onChange: function(value){
											ap_sizer(value,'zoomlenssize');
										}
							});
			zoomborder_size_slider = new Control.Slider('zoomborder_size_ball','zoomborder_size_track', {
							   	range: $R(0, 20),
								increment: 1,
								alignX: 4,
      							sliderValue: <?php echo $config['settings']['zoombordersize']; ?>,
							   	onSlide: function(value){
											ap_sizer(value,'zoombordersize');
										},
								onChange: function(value){
											ap_sizer(value,'zoombordersize');
										}
							});
			//$('thumb_quality_preview').observe('load', function(){ Effect.Fade('thumb_loader',{ duration: 0.3 }); });
			//$('rollover_quality_preview').observe('load', function(){ $('rollover_quality_details').show(); });
			//$('preview_quality_preview').observe('load', function(){ $('preview_quality_details').show(); });
			
			var pretypes = ['thumb','rollover','preview'];
			
			pretypes.each(function(id){
									$(id+'_size').observe('blur', function(){ set_slider_value(eval(id+'_size_slider'),$F(id+'_size')); });
									$(id+'_quality').observe('blur', function(){ set_slider_value(eval(id+'_quality_slider'),$F(id+'_quality')); });
									$(id+'_quality_preview').observe('load', function(){ Effect.Fade(id+'_loader',{ duration: 0.3 }); });
								   });
			
			//$('rollover_size').observe('blur', function(){ manual_size_change($F('thumb_size'),'thumb'); });
			//$('thumb_quality').observe('blur', function(){ manual_size_change($F('thumb_quality'),'thumb'); });
			
			//$('thumb_size').observe('blur', function(){ manual_size_change($F('thumb_size'),'thumb'); });
			//$('thumb_quality').observe('blur', function(){ manual_size_change($F('thumb_quality'),'thumb'); });
			
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('uploader').update('$mgrlang[gen_disabled]');";
				}
				else
				{
					if(file_exists('../assets/logos/'.$config['settings']['mainlogo']))
					{
						echo "update_image_win();";
					}
					else
					{
						//echo "flashObj.write('logo_div');";
						echo "create_swfobj();";
					}
				}
			?>
			
		});
		
		function create_swfobj()
		{
			swfobject.embedSWF("mgr.single.uploader.swf", "uploader", "300", "40", "6.0.0", "expressInstall.swf", flashvars, params, attributes);	
		}
		
		function update_image_win()
		{
			//alert('test');
			$('uploadwrapper').hide();
			show_loader_mt('logo_div');
			var myAjax = new Ajax.Updater(
			'logo_div', 
			'mgr.look.feel.actions.php', 
			{
				method: 'get', 
				parameters: 'mode=logo_window&pass=<?php echo md5($config['settings']['serial_number']); ?>',
				evalScripts: true
			});			
		}
		
		function do_delete_logo()
		{
			show_loader_mt('logo_div');
			var myAjax = new Ajax.Updater(
			'logo_div', 
			'mgr.look.feel.actions.php', 
			{
				method: 'get', 
				parameters: 'mode=delete_logo&pass=<?php echo md5($config['settings']['serial_number']); ?>',
				evalScripts: true
			});	
		}
		
		// DELETE THE LOGO
		function delete_logo()
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					//echo "return false;";
				}
				else
				{
					// IF VERIFT BEFORE DELETE IS ON
					if($config['settings']['verify_before_delete'])
					{
			?>
						message_box("<?php echo $mgrlang['gen_del_logo']; ?>","<input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_logo();close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
			<?php
					}
					else
					{
						echo "do_delete_logo();";
					}
				}
			?>
		}
		
		function check_keypress(event)
		{
			if (Event.KEY_TAB == event.keyCode)
			{
				alert('test');
				manual_size_change($F('thumb_size'),'thumb');
			}
			return false;
			$('data_form').submit.stop();
		}
		
		function set_slider_value(slider,value)
		{
			// due to onChange code above we need this or 
			// a 0 will be put in the text box when you delete the value
			if (value == '') return;
			//alert(value);
			
			if (isNaN(value))
			{
				slider.setValue(0);
			}
			else
			{
				slider.setValue(value);
			}
		}
		
		function manual_quality_change(value,id)
		{
			preview_qualiziter(value,id);
			render_image(id);
		}
		
		function preview_resizer(value,id)
		{
			var rounded_value = Math.floor(value);
			//reset_quality_details(id);
			//$(id+'_quality_details').hide();
			
			clearTimeout(details_slide_down);
			$(id+'_quality_details').hide();
			
			//var marginleft = Math.floor(rounded_value/2);
			//var height = Math.floor(rounded_value*.75);
			//var margintop = Math.floor(height/2);
			$(id+'_size').setValue(rounded_value);
			$(id+'_quality_preview').setStyle({
										width: rounded_value+'px'
									});
			$(id+'_quality_details').setStyle({
										width: rounded_value+'px'
									});
		}
		
		function reset_quality_details(id)
		{
			//$(id+'_quality_details').hide();
			clearTimeout(gifstimeout);
			$(id+'_quality_details').slideUp({duration:0.3});
		}
		
		function preview_qualiziter(value,id)
		{
			$(id+'_quality_details').hide();
			var rounded_value = Math.floor(value);
			$(id+'_quality').setValue(rounded_value);
		}
		
		function ap_sizer(value,id)
		{
			var rounded_value = Math.floor(value);
			$(id).setValue(rounded_value);
		}
		
		var tab_mode = 'thumb';
		var gifstimeout;
		var details_slide_down;
		var myslider;
		var datetime = new Date();
		
		function render_image(id)
		{
			clearTimeout(gifstimeout);
			clearTimeout(details_slide_down);
			$(id+'_quality_details').hide();
			if(myslider != null){ myslider.cancel(); }
			
			var nocache = datetime.getTime();
			
			//reset_quality_details(id);
			$(id+'_loader').show();
			var src = $F('preview_image');
			var size = $F(id+'_size');
			var quality = $F(id+'_quality');
			var watermark = $F(id+'_watermark');
			var watermark_resize = $F(id+'_watermark_resize');
			var sharpen = $F(id+'_sharpen');
			$(id+'_quality_preview').setAttribute('src','mgr.image.preview.php?width='+size+'&quality='+quality+'&src='+src+'&watermark='+watermark+'&rescale='+watermark_resize+'&sharpen='+sharpen+'&processor=<?php if(class_exists('Imagick') and $config['settings']['imageproc'] == 2){ echo "im"; } else { echo "gd"; } ?>&dt='+nocache);
			gifstimeout = setTimeout(function(){ get_image_filesize(id); },500);
			
			switch(id)
			{
				default:
				case "thumb":
				break;
				case "rollover":
				break;
				case "preview":
				break;
			}
		}
		
		function set_tab_mode(id)
		{
			tab_mode = id;
		}
		
		function get_image_filesize(id)
		{
			//clearTimeout(details_slide_down);
			//$(id+'_quality_details').hide();
			
			var quality = $F(id+'_quality');
			var size = $F(id+'_size');
			var src = $F('preview_image');
			var url = 'mgr.image.preview.php';
			var pars = 'return_size=1&width='+size+'&quality='+quality+'&src='+src+'&save=../assets/tmp/'+id+'_test.jpg';
			var myAjax = new Ajax.Request(url, {method: 'get', parameters: pars, onSuccess: function(transport){
																										 var details = transport.responseText.split("|");
																										 $(id+'_quality_details_inner').update('Size: <strong>'+details[0]+'kb</strong> | Process: <strong>'+details[1]+'</strong>sec');
																										 //myslider.stop();
																										 $(id+'_quality_details').slideDown({duration:0.4});
																										 details_slide_down = setTimeout(function(){ myslider = Effect.SlideUp(id+'_quality_details',{ duration: 0.3 }); },3000);
																										 //$(id+'_loader').hide();
																									 }});
		}
		
		function show_previews_details(id)
		{
			$(id+'_quality_details').toggle();
		}
		
		function change_tcrop(){
			if($('thumbcrop').checked){
				$('thumbcropimg').src = "images/mgr.sample.crop.gif";
				show_div('thumbcropheight');
			} else {
				$('thumbcropimg').src = "images/mgr.sample.nocrop.gif";
				hide_div('thumbcropheight');
			}
		}
		
		function change_gtcrop(){
			if($('gallerythumbcrop').checked){
				$('gallerythumbcropimg').src = "images/mgr.sample.crop.gif";
				show_div('gallerythumbcropheight');
			} else {
				$('gallerythumbcropimg').src = "images/mgr.sample.nocrop.gif";
				hide_div('gallerythumbcropheight');
			}
		}
		
		function change_rcrop(){
			if($('rollovercrop').checked){
				$('rollovercropimg').src = "images/mgr.sample.crop2.gif";
				show_div('rollovercropheight');
			} else {
				$('rollovercropimg').src = "images/mgr.sample.nocrop2.gif";
				hide_div('rollovercropheight');
			}
		}
	</script>
	
    <style>
		.photo_border
		{
			position: relative;
			margin-right: auto;
			margin-left: auto;
			margin-top: auto;
			margin-bottom: 15px;
			overflow: visible;
			text-align:center;
			border: 1px solid #CCC;
			padding: 5px;
			background-color: #fff;
			-moz-box-shadow: 1px 1px 4px #d9d9d9; 
			-webkit-box-shadow: 1px 1px 4px #d9d9d9;     
			box-shadow: 1px 1px 4px #d9d9d9;
			/* For IE 8 */ 
			-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#d9d9d9')";   
			/* For IE 5.5 - 7 */     
			filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#d9d9d9'); 
			float: left;
			min-width: 100px;
			min-height: 75px;
			/*
			background-image: url(images/mgr.loader.gif);
			background-repeat: no-repeat;
			background-position: 10px 10px;
			*/
		}
		.photo_details{
			opacity:0.6;
			filter:alpha(opacity=60);
			position: absolute;
			background-color: #333;
			color: #FFF; bottom: 5px;
			border-top: 1px solid #777;
			overflow: hidden; 
		}
		.photo_loader{
			opacity:0.8;
			filter:alpha(opacity=80);
			position: absolute;
			width: 100px;
			overflow: hidden;
			height: 10px;
			left: 50%;
			top: 50%;
			margin-top: -25px;
			margin-left: -50px;
			border-top: 1px solid #8c8c8c;
			border-left: 1px solid #8c8c8c;
			border-bottom: 1px solid #EEE;
			border-right: 1px solid #EEE;
		}
		.photo_loader img{
			margin-top: -1px;
			margin-left: -1px;
		}
		.watermark_indent{
			margin-left: 270px;
		}
	</style>
</head>
<body>
	<div style="width:100%; height: 100%; background-color:#333; z-index: 1; position: absolute; display: none;" id="overlay2"></div>
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
            <form enctype="multipart/form-data" id="data_form" name="data_form" method="post" action="<?php echo $action_link; ?>" onsubmit="return form_sumbitter();">
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.look.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_look']; ?></strong><br /><span><?php echo $mgrlang['subnav_look_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
                
            </div>

            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div>
        		<img src="images/samples/mgr.sample.photo1.1024px.jpg" id="size_preview" style="z-index: 2; width: 100px; height: 75px; border: 2px solid #FFF; position: absolute; left: 50%; top: 50%; margin-top: -37px; margin-left: -50px; display: none; background-color: #cbf4f0;">
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');$('preview_image_selector').hide();" id="tab1"><?php echo $mgrlang['landf_theme']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');set_tab_mode('thumb');render_image('thumb');$('preview_image_selector').show();" id="tab2"><?php echo $mgrlang['landf_thumbnails']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('10');set_tab_mode('rollover');render_image('rollover');$('preview_image_selector').show();" id="tab10"><?php echo $mgrlang['landf_rollovers']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('3');set_tab_mode('preview');render_image('preview');$('preview_image_selector').show();" id="tab3"><?php echo $mgrlang['landf_previews']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('13');$('preview_image_selector').hide();" id="tab13"><?php echo $mgrlang['landf_galleries']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('5');$('preview_image_selector').hide();" id="tab5"><?php echo $mgrlang['landf_logo']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('11');$('preview_image_selector').hide();" id="tab11"><?php echo $mgrlang['landf_pages']; ?></div>
					<div class="subsuboff" onclick="bringtofront('12');$('preview_image_selector').hide();" id="tab12"><?php echo $mgrlang['landf_homepage']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('4');$('preview_image_selector').hide();" id="tab4"><?php echo $mgrlang['landf_other']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('15');$('preview_image_selector').hide();" id="tab15"><?php echo $mgrlang['landf_watermark']; ?></div>
                    <!--<div class="subsuboff" onclick="bringtofront('7');$('preview_image_selector').hide();" id="tab7">Slideshow</div>-->
                    <!--<div class="subsuboff" onclick="bringtofront('8');$('preview_image_selector').hide();" id="tab8">Flash Elements</div>-->
                    <?php if(in_array("mediaextender",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('14');" id="tab14" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['landf_videos']; ?></div><?php } ?>
					<!--<div class="subsuboff" onclick="bringtofront('6');$('preview_image_selector').hide();" id="tab6" style="border-right: 1px solid #d8d7d7;">Advanced</div>--> 
                    
                    <div style="position: absolute; font-size: 12px; margin-right: 20px; right: 18px; margin-top: -8px; border-right: 1px solid #d8d7d7; display: none;" class="subsuboff" id="preview_image_selector">                        
                        <strong><?php echo $mgrlang['landf_preimg']; ?>:</strong>&nbsp;
                        <select onchange="render_image(tab_mode);" id="preview_image" name="preview_image">
                            <?php $preview_image = 'images/samples/mgr.sample.photo1.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_01']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo2.350px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_02']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo3.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_03']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo4.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_04']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo5.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_05']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo6.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_06']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo7.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_04']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo8.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_01']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo9.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_07']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo10.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_08']; ?></option>
                            <?php $preview_image = 'images/samples/mgr.sample.photo11.1024px.jpg'; ?><option value="<?php echo $preview_image; ?>" <?php if($config['settings']['preview_image'] == $preview_image){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_preimg_09']; ?></option>
                        </select>                   
                    </div>                   
                </div>
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding: 30px;">
					<?php
                        $real_dir = realpath("../assets/themes/");
                        $dir = opendir($real_dir);
                        $i = 0;
                        # LOOP THROUGH THE NAV DIRECTORY
                        while($file = readdir($dir))
						{
                             if($file != ".." && $file != "." && is_dir("../assets/themes/" . $file) && file_exists("../assets/themes/" . $file . "/config.php") && !strpos($file," ") && $file != 'debug')
							 {
                                include("../assets/themes/" . $file . "/config.php");
                                $ss = (file_exists("../assets/themes/" . $file . "/sample.jpg")) ? "../assets/themes/$file/sample.jpg" : "images/mgr.theme.blank.gif";
                                echo "
									<div class='colorSchemeContainer'>
										<div class='ip_div_inner' style='width: 308px;'>";
											
											if($config['productVersion'] > $template_config['prod_version']){ $oldTemplate=true; echo "<img src='images/mgr.notice.icon.png' style='vertical-align:middle; margin-left: -20px; margin-top: -20px; position: absolute;'>"; }
								echo "
											<label for='$file'><img src='$ss' style='width: 300px; border: 4px solid #FFF; cursor: pointer' /></label>
										</div>
										<input type='radio' name='theme' value='$file' id='$file' class='radio' ";
										if($config['settings']['style'] == $file){ echo "checked"; }
										echo " /><label for='$file' style='font-weight: bold; color: #333333'>$template_config[name]";										
										echo "</label>";
										
										if($template_config['details'])
											echo "<br><span style='color: #999;'>{$template_config[details]}</span>";
										
										if($template_config['color_schemes'])
										{
											echo "<select name='colorScheme-{$file}' class='colorScheme'>";
												foreach($template_config['color_schemes'] as $colorSchemeKey => $colorSchemeName)
												{
													echo "<option value='{$colorSchemeKey}'";
													if($config['settings']['color_scheme'] == $colorSchemeKey) echo " selected='selected'";
													echo ">{$colorSchemeName}</option>";
												}
											echo "</select>";
										}
										
										
										
                                echo "
									</div>";
                            }                                  
                        }
                        closedir($dir);
                        //
                    ?>
                    <?php if($oldTemplate){ ?><div style="clear: both; color: #666; padding-top: 20px;"><img src="images/mgr.notice.icon.small2.png" style="vertical-align:middle; margin-right: 4px;"><?php echo $mgrlang['landf_olderver']; ?></div><?php } ?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group">
                    <div class="<?php fs_row_color(); ?>" style="padding-left: 35px;">                        
                        <div style="float: left; width: 210px; margin-right: 30px;">
                            <div style="border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_size']; ?><br />
                                <div style="width: 150px;" id="thumb_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="thumb_size_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['thumb_size']; ?>" class="slide_input" id="thumb_size" name="thumb_size" />px
                            </div>
                            <div style="margin-top: 15px; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_quality']; ?><br />
                                <div style="width: 150px;" id="thumb_quality_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="thumb_quality_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['thumb_quality']; ?>" class="slide_input" id="thumb_quality" name="thumb_quality" />%
                            </div>
                            <div style="margin-top: 10px; line-height: 2; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_watermark']; ?><br />
                                <select style="width: 130px;" name="thumb_watermark" id="thumb_watermark" onchange="render_image('thumb');">                                    
                                    <option value=""><?php echo $mgrlang['gen_none']; ?></option>
                                    <?php
                                        foreach($watermark as $value){
                                            echo "<option value=\"$value\" ";
											if($value == $config['settings']['thumb_wm']){ echo "selected='selected'"; }
											echo ">$value</option>";
                                        }
                                    ?>
                                </select> <input type="hidden" value="1" name="thumb_watermark_resize" id="thumb_watermark_resize" onclick="render_image('thumb');" style="margin: 0 0 0 8px; padding: 0; vertical-align:text-top" <?php if($config['settings']['thumb_wmr']){ echo "checked='checked'"; } ?> /> <label for="thumb_watermark_resize"><!--Resize--></label>
                            </div>
                            <?php
								if(function_exists("imageconvolution"))
								{
							?>
                                <div style="margin-top: 15px;">
                                    <input type="checkbox" value="1" name="thumb_sharpen" id="thumb_sharpen" onclick="render_image('thumb');" style="margin: 0 3px 0 0; padding: 0; vertical-align:text-top" <?php if($config['settings']['thumb_sharpen']){ echo "checked='checked'"; } ?> /> <label for="thumb_sharpen"><?php echo $mgrlang['gen_sharpen']; ?></label><br />
                                </div>
                            <?php
								}
							?>
                        </div>
                        <div class="photo_border"><div class="photo_details" id="thumb_quality_details" style="display: none; width: <?php echo $config['settings']['thumb_size']; ?>px"><p id="thumb_quality_details_inner" style="text-align: center; margin: 4px; width: 100%; color: #FFF; font-weight: normal"></p></div><div class="photo_loader" id="thumb_loader"><img src="images/mgr.loader2.gif" /></div><img src="images/samples/mgr.sample.photo2.350px.jpg" onclick="show_previews_details('thumb');" style="width: <?php echo $config['settings']['thumb_size']; ?>px" id="thumb_quality_preview" /></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_details']; ?>: <br />
                        	<span><?php echo $mgrlang['landf_f_details_d']; ?></span>
                        </p>
                        <?php
							$active_thumb_details = explode(",",$config['settings']['thumb_details']);
							
							$thumb_details_list[] = array('id' => 'id','name' => $mgrlang['mediadet_id'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'title','name' => $mgrlang['mediadet_title'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'filename','name' => $mgrlang['mediadet_filename'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'owner','name' => $mgrlang['mediadet_owner'],'sorder' => 99); // [todo] only show this option if the contributors add-on is installed
							$thumb_details_list[] = array('id' => 'price','name' => $mgrlang['mediadet_priceorg'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'credits','name' => $mgrlang['mediadet_creditsorg'],'sorder' => 99); // [todo] only show this option if credits add-on is installed and credits cart is on
							$thumb_details_list[] = array('id' => 'date','name' => $mgrlang['mediadet_dateadded'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'created','name' => $mgrlang['mediadet_datecreated'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'description','name' => $mgrlang['mediadet_description'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'views','name' => $mgrlang['mediadet_views'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'resolution','name' => $mgrlang['mediadet_res'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'filesize','name' => $mgrlang['mediadet_filesize'],'sorder' => 99);
							$thumb_details_list[] = array('id' => 'mediatypes','name' => $mgrlang['gen_media_types'],'sorder' => 99);
							
							//$thumb_details_list[] = array('id' => 'purchases','name' => 'Purchases','sorder' => 99);
							//$thumb_details_list[] = array('id' => 'downloads','name' => 'Downloads','sorder' => 99);
							
							foreach ($thumb_details_list as $key => $row) {
								$tdl_id[$key]  = $row['id'];
								$tdl_name[$key] = $row['name'];
								$tdl_sorder[$key] = $row['sorder'];
							}
								
							foreach($active_thumb_details as $key => $value)
							{
								foreach($tdl_id as $key2 => $value2)
								{
									if($value == $value2){ $tdl_sorder[$key2] = $key; }
								}
							}
							
							asort($tdl_sorder);
							
						?>
                        <div style="float: left;">
                        	<ul id="thumb_details_sort" class="clean_ul_list" style="color: #666">
                            	<?php
									foreach($tdl_sorder as $key => $value)
									{
										echo "<li><input type='checkbox' value='".$tdl_id[$key]."' name='thumb_details[]'";
										if(in_array($tdl_id[$key],$active_thumb_details)){ echo "checked='checked'"; }
										echo "/> <span class='handle'>".$tdl_name[$key]."</span></li>";
									}
								?>
                            </ul>
                            <ul class="clean_ul_list" style="border-top: 1px dotted #CCC; margin-top: 10px;">
                                 <li><input type="checkbox" name="thumbDetailsDownloads" id="tdl_downloads" value="1" <?php if($config['settings']['thumbDetailsDownloads']){ echo "checked='checked'"; } ?> /> <label for="tdl_downloads"><?php echo $mgrlang['gen_downloads']; ?></label></li>
								
								<?php if($config['settings']['rating_system']){ ?><li><input type="checkbox" name="thumbDetailsRating" id="tdl_rating" value="1" <?php if($config['settings']['thumbDetailsRating']){ echo "checked='checked'"; } ?> /> <label for="tdl_rating"><?php echo $mgrlang['mediadet_rating']; ?></label></li><?php } ?>
                                <!--<li><input type="checkbox" name="thumbDetailsCart" id="tdl_cart" value="1" <?php if($config['settings']['thumbDetailsCart']){ echo "checked='checked'"; } ?> /> <label for="tdl_cart">Add To Cart</label></li>-->
                                <?php if($config['settings']['lightbox']){ ?><li><input type="checkbox" name="thumbDetailsLightbox" id="tdl_lightbox" value="1" <?php if($config['settings']['thumbDetailsLightbox']){ echo "checked='checked'"; } ?> /> <label for="tdl_lightbox"><?php echo $mgrlang['mediadet_add_lb']; ?></label></li><?php } ?>
								
								<li><input type="checkbox" name="thumbDetailsPackage" id="tdl_package" value="1" <?php if($config['settings']['thumbDetailsPackage']){ echo "checked='checked'"; } ?> /> <label for="tdl_package"><?php echo $mgrlang['mediadet_add_pack']; ?></label></li>
								<?php if($config['settings']['email_friend']){ ?><li><input type="checkbox" name="thumbDetailsEmail" id="tdl_email" value="1" <?php if($config['settings']['thumbDetailsEmail']){ echo "checked='checked'"; } ?> /> <label for="tdl_email"><?php echo $mgrlang['mediadet_email']; ?></label></li><?php } ?>
                            </ul>
                        </div>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_crop_thumb']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_crop_thumb_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" name="thumbcrop" id="thumbcrop" value="1" onclick="change_tcrop();" <?php if($config['settings']['thumbcrop']){ echo "checked='checked'"; } ?> /> <img src="images/mgr.sample.<?php if($config['settings']['thumbcrop']){ echo "crop"; } else { echo "nocrop"; } ?>.gif" id="thumbcropimg" align="absmiddle" /></div>
						<div id="thumbcropheight" style="float: left; <?php if(!$config['settings']['thumbcrop']){ echo "display: none;"; } ?> padding-left: 10px; padding-top: 8px;">
							<?php echo $mgrlang['landf_crop_height']; ?>: <input type="text" name="thumbcrop_height" value="<?php echo $config['settings']['thumbcrop_height']; ?>" style="width: 26px" /> px
						</div>
                    </div>
              	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab10_group" class="group">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_actrollover']; ?>: <br />
                        	<span><?php echo $mgrlang['landf_f_actrollover_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="rollover_status" id="rollover_status" <?php if($config['settings']['rollover_status']){ echo "checked='checked'"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>" style="padding-left: 35px;">
                        <div style="float: left; width: 210px; margin-right: 30px;"">
                            <div style="border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_size']; ?><br />
                                <div style="width: 150px;" id="rollover_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="rollover_size_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['rollover_size']; ?>" class="slide_input" id="rollover_size" name="rollover_size" />px
                            </div>
                            <div style="margin-top: 15px; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_quality']; ?><br />
                                <div style="width: 150px;" id="rollover_quality_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="rollover_quality_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['rollover_quality']; ?>" class="slide_input" id="rollover_quality" name="rollover_quality" />%
                            </div>
                            <div style="margin-top: 10px; line-height: 2; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_watermark']; ?><br />
                                <select style="width: 130px;" name="rollover_watermark" id="rollover_watermark" onchange="render_image('rollover');">
                                    <option value=""><?php echo $mgrlang['gen_none']; ?></option>
                                    <?php
                                        foreach($watermark as $value){
                                            echo "<option value=\"$value\" ";
											if($value == $config['settings']['rollover_wm']){ echo "selected='selected'"; }
											echo ">$value</option>";
                                        }
                                    ?>
                                </select> <input type="hidden" value="1" name="rollover_watermark_resize" id="rollover_watermark_resize" onclick="render_image('rollover');" style="margin: 0 0 0 8px; padding: 0; vertical-align:text-top" <?php if($config['settings']['rollover_wmr']){ echo "checked='checked'"; } ?> /> <label for="rollover_watermark_resize"><!--Resize--></label>
                            </div>
                            <?php
								if(function_exists("imageconvolution"))
								{
							?>
                                <div style="margin-top: 15px;">
                                    <input type="checkbox" value="1" name="rollover_sharpen" id="rollover_sharpen" onclick="render_image('rollover');" style="margin: 0 3px 0 0; padding: 0; vertical-align:text-top" <?php if($config['settings']['rollover_sharpen']){ echo "checked='checked'"; } ?> /> <label for="rollover_sharpen"><?php echo $mgrlang['gen_sharpen']; ?></label><br />
                                </div>
                            <?php
								}
							?>
                        </div>
                    	<div class="photo_border"><div class="photo_details" id="rollover_quality_details" style="display: none; width: <?php echo $config['settings']['rollover_size']; ?>px"><p id="rollover_quality_details_inner" style="text-align: center; margin: 4px; width: 100%; color: #FFF; font-weight: normal"></p></div><div class="photo_loader" id="rollover_loader"><img src="images/mgr.loader2.gif" /></div><img src="images/samples/mgr.sample.photo2.350px.jpg" onclick="show_previews_details('rollover');" style="width: <?php echo $config['settings']['rollover_size']; ?>px" id="rollover_quality_preview" /></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_details']; ?>: <br />
                        	<span><?php echo $mgrlang['landf_f_detro_d']; ?></span>
                        </p>
                        <?php
							$active_rollover_details = explode(",",$config['settings']['rollover_details']);
							
							$rollover_details_list[] = array('id' => 'id','name' => $mgrlang['mediadet_id'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'title','name' => $mgrlang['mediadet_title'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'filename','name' => $mgrlang['mediadet_filename'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'owner','name' => $mgrlang['mediadet_owner'],'sorder' => 99); // [todo] only show this option if the contributors add-on is installed
							$rollover_details_list[] = array('id' => 'price','name' => $mgrlang['mediadet_priceorg'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'credits','name' => $mgrlang['mediadet_creditsorg'],'sorder' => 99); // [todo] only show this option if credits add-on is installed and credits cart is on
							$rollover_details_list[] = array('id' => 'date','name' => $mgrlang['mediadet_dateadded'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'created','name' => $mgrlang['mediadet_datecreated'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'views','name' => $mgrlang['mediadet_views'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'description','name' => $mgrlang['mediadet_description'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'resolution','name' => $mgrlang['mediadet_res'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'filesize','name' => $mgrlang['mediadet_filesize'],'sorder' => 99);
							$rollover_details_list[] = array('id' => 'mediatypes','name' => $mgrlang['gen_media_types'],'sorder' => 99);
							
							foreach ($rollover_details_list as $key => $row) {
								$rdl_id[$key]  = $row['id'];
								$rdl_name[$key] = $row['name'];
								$rdl_sorder[$key] = $row['sorder'];
							}
								
							foreach($active_rollover_details as $key => $value)
							{
								foreach($rdl_id as $key2 => $value2)
								{
									if($value == $value2){ $rdl_sorder[$key2] = $key; }
								}
							}
							
							asort($rdl_sorder);
						?>
                        <div style="float: left;">
                        	<ul id="rollover_details_sort" class="clean_ul_list" style="color: #666">
                            	<?php
									foreach($rdl_sorder as $key => $value)
									{
										echo "<li><input type='checkbox' value='".$rdl_id[$key]."' name='rollover_details[]'";
										if(in_array($rdl_id[$key],$active_rollover_details)){ echo "checked='checked'"; }
										echo "/> <span class='handle'>".$rdl_name[$key]."</span></li>";
									}
								?>
                            </ul>
							<ul class="clean_ul_list" style="border-top: 1px dotted #CCC; margin-top: 10px;">
                                <?php if($config['settings']['rating_system']){ ?><li><input type="checkbox" name="rolloverDetailsRating" id="rdl_rating" value="1" <?php if($config['settings']['rolloverDetailsRating']){ echo "checked='checked'"; } ?> /> <label for="rdl_rating"><?php echo $mgrlang['mediadet_rating']; ?></label></li><?php } ?>
                            </ul>
                        </div>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_crop_ro']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_crop_ro_d']; ?></span>
                        </p>
						<div style="float: left;"><input type="checkbox" name="rollovercrop" id="rollovercrop" value="1" onclick="change_rcrop();" <?php if($config['settings']['rollovercrop']){ echo "checked='checked'"; } ?>  /> <img src="images/mgr.sample.<?php if($config['settings']['rollovercrop']){ echo "crop"; } else { echo "nocrop"; } ?>2.gif" id="rollovercropimg" align="absmiddle" /></div>
						<div id="rollovercropheight" style="float: left; <?php if(!$config['settings']['rollovercrop']){ echo "display: none;"; } ?> padding-left: 10px; padding-top: 8px;">
							<?php echo $mgrlang['landf_crop_height']; ?>: <input type="text" name="rollovercrop_height" value="<?php echo $config['settings']['rollovercrop_height']; ?>" style="width: 26px" /> px
						</div>
                    </div> 						                    			
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">
                	<div class="<?php fs_row_color(); ?>" style="padding-left: 35px;">
                        <div style="float: left; width: 210px; margin-right: 30px;">
                            <div style="border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_size']; ?><br />
                                <div style="width: 150px;" id="preview_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="preview_size_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['preview_size']; ?>" class="slide_input" id="preview_size" name="preview_size" />px
                            </div>
                            <div style="margin-top: 15px; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_quality']; ?><br />
                                <div style="width: 150px;" id="preview_quality_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="preview_quality_ball" /></div>
                                <input type="text" value="<?php echo $config['settings']['preview_quality']; ?>" class="slide_input" id="preview_quality" name="preview_quality" />%
                            </div>
                            <div style="margin-top: 10px; line-height: 2; border-bottom: 1px dotted #CCC; padding-bottom: 15px;">
                                <?php echo $mgrlang['gen_watermark']; ?><br />
                                <select style="width: 130px;" name="preview_watermark" id="preview_watermark" onchange="render_image('preview');">
                                    <option value=""><?php echo $mgrlang['gen_none']; ?></option>
                                    <?php
                                        foreach($watermark as $value){
                                            echo "<option value=\"$value\" ";
											if($value == $config['settings']['preview_wm']){ echo "selected='selected'"; }
											echo ">$value</option>";
                                        }
                                    ?>
                                </select> <input type="hidden" value="1" name="preview_watermark_resize" id="preview_watermark_resize" onclick="render_image('preview');" style="margin: 0 0 0 8px; padding: 0; vertical-align:text-top" <?php if($config['settings']['preview_wmr']){ echo "checked='checked'"; } ?> /> <label for="preview_watermark_resize"><!--Resize--></label>
                            </div>
                            <?php
								if(function_exists("imageconvolution"))
								{
							?>
                                <div style="margin-top: 15px;">
                                    <input type="checkbox" value="1" name="preview_sharpen" id="preview_sharpen" onclick="render_image('preview');" style="margin: 0 3px 0 0; padding: 0; vertical-align:text-top" <?php if($config['settings']['preview_sharpen']){ echo "checked='checked'"; } ?> /> <label for="preview_sharpen"><?php echo $mgrlang['gen_sharpen']; ?></label><br />
                                </div>
                            <?php
								}
							?>
                        </div>
                        <div class="photo_border"><div class="photo_details" id="preview_quality_details" style="display: none; width: <?php echo $config['settings']['preview_size']; ?>px"><p id="preview_quality_details_inner" style="text-align: center; margin: 4px; width: 100%; color: #FFF; font-weight: normal"></p></div><div class="photo_loader" id="preview_loader"><img src="images/mgr.loader2.gif" /></div><img src="images/samples/mgr.sample.photo2.350px.jpg" onclick="show_previews_details('preview');" style="width: <?php echo $config['settings']['preview_size']; ?>px" id="preview_quality_preview" /></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_detpage']; ?>: <br />
                        	<span><?php echo $mgrlang['landf_f_detpage_d']; ?></span>
                        </p>
                        <?php
							$active_preview_details = explode(",",$config['settings']['preview_details']);
							
							$preview_details_list[] = array('id' => 'id','name' => $mgrlang['mediadet_id'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'title','name' => $mgrlang['mediadet_title'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'filename','name' => $mgrlang['mediadet_filename'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'owner','name' => $mgrlang['mediadet_owner'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'date','name' => $mgrlang['mediadet_dateadded'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'created','name' => $mgrlang['mediadet_datecreated'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'description','name' => $mgrlang['mediadet_description'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'views','name' => $mgrlang['mediadet_views'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'keywords','name' => $mgrlang['mediadet_keywords'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'colorPalette','name' => $mgrlang['mediadet_colors'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'copyright','name' => $mgrlang['mediadet_copyright'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'usage_restrictions','name' => $mgrlang['mediadet_usage_rest'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'model_release','name' => $mgrlang['mediadet_mod_release'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'prop_release','name' => $mgrlang['media_f_pr'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'filesize','name' => $mgrlang['mediadet_filesize'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'resolution','name' => $mgrlang['mediadet_res'],'sorder' => 99);
							$preview_details_list[] = array('id' => 'mediatypes','name' => $mgrlang['gen_media_types'],'sorder' => 99);
							//$preview_details_list[] = array('id' => 'purchases','name' => 'Purchases','sorder' => 99);
							//$preview_details_list[] = array('id' => 'downloads','name' => 'Downloads','sorder' => 99);
							
							foreach ($preview_details_list as $key => $row) {
								$pdl_id[$key]  = $row['id'];
								$pdl_name[$key] = $row['name'];
								$pdl_sorder[$key] = $row['sorder'];
							}
								
							foreach($active_preview_details as $key => $value)
							{
								foreach($pdl_id as $key2 => $value2)
								{
									if($value == $value2){ $pdl_sorder[$key2] = $key; }
								}
							}
							
							asort($pdl_sorder);
						?>
                        <div style="float: left;">
                        	<ul id="preview_details_sort" class="clean_ul_list" style="color: #666">
                            	<?php
									foreach($pdl_sorder as $key => $value)
									{
										echo "<li><input type='checkbox' value='".$pdl_id[$key]."' name='preview_details[]'";
										if(in_array($pdl_id[$key],$active_preview_details)){ echo "checked='checked'"; }
										echo "/> <span class='handle'>".$pdl_name[$key]."</span></li>";
									}
								?>
                            </ul>
							<ul class="clean_ul_list" style="border-top: 1px dotted #CCC; margin-top: 10px;">
								<?php if($config['settings']['rating_system']){ ?><li><input type="checkbox" name="previewDetailsRating" id="pdl_rating" value="1" <?php if($config['settings']['previewDetailsRating']){ echo "checked='checked'"; } ?> /> <label for="pdl_rating"><?php echo $mgrlang['mediadet_rating']; ?></label></li><?php } ?>
								<?php /*
							    <?php if($config['settings']['lightbox']){ ?><li><input type="checkbox" name="previewDetailsLightbox" id="pdl_lightbox" value="1" <?php if($config['settings']['previewDetailsLightbox']){ echo "checked='checked'"; } ?> /> <label for="pdl_lightbox"><?php echo $mgrlang['mediadet_add_lb']; ?></label></li><?php } ?>
								<li><input type="checkbox" name="previewDetailsPackage" id="pdl_package" value="1" <?php if($config['settings']['previewDetailsPackage']){ echo "checked='checked'"; } ?> /> <label for="pdl_package"><?php echo $mgrlang['mediadet_add_pack']; ?></label></li>
								<?php if($config['settings']['email_friend']){ ?><li><input type="checkbox" name="previewDetailsEmail" id="pdl_email" value="1" <?php if($config['settings']['previewDetailsEmail']){ echo "checked='checked'"; } ?> /> <label for="pdl_email"><?php echo $mgrlang['mediadet_email']; ?></label></li><?php } ?>
								*/ ?>
                            </ul>
                        </div>
                    </div>
                    <div class="fs_header"><?php echo $mgrlang['geo_location_area']; ?></div>
                    <div class="<?php fs_row_color(); ?>">
                      <img src="images/mgr.ast.off.gif" class="ast" />
                      <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                      	<?php echo $mgrlang['geo_on_off']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['geo_on_off_d']; ?></span>
                      </p>
                      <input type="checkbox" value="1" name="gpsonoff" <?php if($config['settings']['gpsonoff']){ echo "checked='checked'"; } ?> />
                    </div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['geo_width']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['geo_width_d']; ?></span>
											</p>
											<div style="padding-bottom: 10px;">
                        <div style="width: 150px;" id="gpswidth_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="gpswidth_size_ball" /></div>
                        <input type="text" value="<?php echo $config['settings']['gpswidth']; ?>" class="slide_input" id="gpswidth" name="gpswidth" />px
                     	</div>
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['geo_height']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['geo_height_d']; ?></span>
											</p>
											<div style="padding-bottom: 10px;">
                        <div style="width: 150px;" id="gpsheight_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="gpsheight_size_ball" /></div>
                        <input type="text" value="<?php echo $config['settings']['gpsheight']; ?>" class="slide_input" id="gpsheight" name="gpsheight" />px
                     	</div>
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['geo_zoom']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['geo_zoom_d']; ?></span>
											</p>
											<div style="padding-bottom: 10px;">
                        <div style="width: 150px;" id="gpszoom_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="gpszoom_size_ball" /></div>
                        <input type="text" value="<?php echo $config['settings']['gpszoom']; ?>" class="slide_input" id="gpszoom" name="gpszoom" />px
                     	</div>
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['geo_pin_color']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['geo_pin_color_d']; ?></span>
											</p>
											<input type="text" name="gpscolor" value="<?php echo $config['settings']['gpscolor']; ?>" />
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['geo_map_type']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['geo_map_type_d']; ?></span>
											</p>
											<select name="gpsmaptype">
                        <option value="roadmap" <?php if($config['settings']['gpsmaptype'] == 'roadmap'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['geo_map_type_roadmap']; ?></option>
                        <option value="satellite" <?php if($config['settings']['gpsmaptype'] == 'satellite'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['geo_map_type_satellite']; ?></option>
                        <option value="terrain" <?php if($config['settings']['gpsmaptype'] == 'terrain'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['geo_map_type_terrain']; ?></option>
                        <option value="hybrid" <?php if($config['settings']['gpsmaptype'] == 'hybrid'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['geo_map_type_hybrid']; ?></option>
                      </select>
										</div>
										
										
										<div class="fs_header"><?php echo $mgrlang['zoom_location_area']; ?></div>
										<div style="background-color: #900; color: #FFF; padding: 5px; text-align: center"><?php echo $mgrlang['image_zoom_warning']; ?></div>
                    <div class="<?php fs_row_color(); ?>">
                      <img src="images/mgr.ast.off.gif" class="ast" />
                      <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                      	<?php echo $mgrlang['zoom_on_off']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['zoom_on_off_d']; ?></span>
                      </p>
                      <input type="checkbox" value="1" name="zoomonoff" <?php if($config['settings']['zoomonoff']){ echo "checked='checked'"; } ?> />
                    </div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['zoom_lens_size']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['zoom_lens_size_d']; ?></span>
											</p>
											<div style="padding-bottom: 10px;">
                        <div style="width: 150px;" id="zoom_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="zoom_size_ball" /></div>
                        <input type="text" value="<?php echo $config['settings']['zoomlenssize']; ?>" class="slide_input" id="zoomlenssize" name="zoomlenssize" />
                     	</div>
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['zoom_border_size']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['zoom_border_size_d']; ?></span>
											</p>
											<div style="padding-bottom: 10px;">
                        <div style="width: 150px;" id="zoomborder_size_track" class="slide_track"><img src="images/mgr.slider.ball2.png" class="slide_ball" id="zoomborder_size_ball" /></div>
                        <input type="text" value="<?php echo $config['settings']['zoombordersize']; ?>" class="slide_input" id="zoombordersize" name="zoombordersize" />
                     	</div>
										</div>
										<div class="<?php fs_row_color(); ?>">
											<img src="images/mgr.ast.off.gif" class="ast" />
											<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
												<?php echo $mgrlang['zoom_border_color']; ?>: <br />
												<span class="input_label_subtext"><?php echo $mgrlang['zoom_border_color_d']; ?></span>
											</p>
											<input type="text" name="zoombordercolor" value="<?php echo $config['settings']['zoombordercolor']; ?>" />
										</div>
										
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab11_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_news']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_news_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="news" <?php if($config['settings']['news']){ echo "checked"; } ?> />
                    </div>                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['webset_f_contact']; ?>:<br />
                            <span><?php echo $mgrlang['webset_f_contact_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contact" <?php if($config['settings']['contact']){ echo "checked"; } ?> />
                    </div>  
					
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_aupage']; ?>:<br />
                            <span><?php echo $mgrlang['landf_f_aupage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="aboutpage" <?php if($config['settings']['aboutpage']){ echo "checked"; } ?> />
                    </div>
					
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_tospage']; ?>:<br />
                            <span><?php echo $mgrlang['landf_f_tospage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="tospage" <?php if($config['settings']['tospage']){ echo "checked"; } ?> />
                    </div> 
					
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_pppage']; ?>:<br />
                            <span><?php echo $mgrlang['landf_f_pppage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="pppage" <?php if($config['settings']['pppage']){ echo "checked"; } ?> />
                    </div> 
					
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_papage']; ?>:<br />
                            <span><?php echo $mgrlang['landf_f_papage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="papage" <?php if($config['settings']['papage']){ echo "checked"; } ?> />
                    </div>           
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fppage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fppage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="promopage" <?php if($config['settings']['promopage']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_nmpage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_nmpage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="newestpage" <?php if($config['settings']['newestpage']){ echo "checked"; } ?> />
                    </div>
                    
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fmpage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fmpage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="featuredpage" <?php if($config['settings']['featuredpage']){ echo "checked"; } ?> />
                    </div>
					
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_pmpage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_pmpage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="popularpage" <?php if($config['settings']['popularpage']){ echo "checked"; } ?> /> <!--by purchases, by downloads, etc-->
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fprintspage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fprintspage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="printpage" <?php if($config['settings']['printpage']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fprodspage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fprodspage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="prodpage" <?php if($config['settings']['prodpage']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fpackspage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fpackspage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="packpage" <?php if($config['settings']['packpage']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_fcollspage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_fcollspage_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="collpage" <?php if($config['settings']['collpage']){ echo "checked"; } ?> />
                    </div>
                    <?php
						if($config['settings']['subscriptions'])
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['landf_f_fsubspage']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['landf_f_fsubspage_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="subpage" <?php if($config['settings']['subpage']){ echo "checked"; } ?> />
						</div>
					<?php
						}
						if(in_array("creditsys",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['landf_f_fcredpage']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['landf_f_fcredpage_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="creditpage" <?php if($config['settings']['creditpage']){ echo "checked"; } ?> />
						</div>
					<?php
						}
					?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_license']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_license_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="display_license" <?php if($config['settings']['display_license']){ echo "checked='checked'"; } ?> />
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_login']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_login_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="display_login" <?php if($config['settings']['display_login']){ echo "checked='checked'"; } ?> />
                    </div>
          <?php
            if(in_array('contr',$installed_addons)){
          ?>
          <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_contri_link']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_contri_link_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="contrib_link" <?php if($config['settings']['contrib_link']){ echo "checked='checked'"; } ?> />
                    </div>
          <?php
        		}
        	?>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_medcount']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_medcount_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="gallery_count" <?php if($config['settings']['gallery_count']){ echo "checked='checked'"; } ?> />
                        <input type="hidden" value="<?php echo $config['settings']['gallery_count']; ?>" name="gallery_count_orig" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_stats']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_stats_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="site_stats" <?php if($config['settings']['site_stats']){ echo "checked='checked'"; } ?> />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_memonline']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_memonline_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="members_online" <?php if($config['settings']['members_online']){ echo "checked='checked'"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_relatedmed']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_relatedmed_d']; ?></span>
                        </p>
                        <input type="checkbox" name="related_media" value="1" <?php if($config['settings']['related_media'] or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_iptc']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_iptc_d']; ?></span>
                        </p>
                        <input type="checkbox" name="display_iptc" value="1" <?php if($config['settings']['display_iptc'] or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_exif']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_exif_d']; ?></span>
                        </p>
                        <input type="checkbox" name="display_exif" value="1" <?php if($config['settings']['display_exif'] or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
           <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_share']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_share_d']; ?></span>
                        </p>
                        <input type="checkbox" name="share" value="1" <?php if($config['settings']['share'] or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
           
           <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_tagCloudOn']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_tagCloudOn_d']; ?></span>
                        </p>
                        <input type="checkbox" name="tagCloudOn" value="1" <?php if($config['settings']['tagCloudOn'] or $_GET['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
           <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_tagCloudSort']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_tagCloudSort_d']; ?></span>
                        </p>
                        <select name="tagCloudSort" style="width: 150px;">
                           <option value="default" <?php if($config['settings']['tagCloudSort'] == 'default'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_tagCloudSort_default']; ?></option>
                           <option value="keyword" <?php if($config['settings']['tagCloudSort'] == 'keyword'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_tagCloudSort_keyword']; ?></option>
                        </select>
                    </div>
                    
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        	<?php echo $mgrlang['landf_f_socnet']; ?>: <br />
                            <span><?php echo $mgrlang['landf_f_socnet_d']; ?></span>
                        </p>
                        <textarea name="sn_code" style="width: 300px; height: 100px;"><?php echo $config['settings']['sn_code']; ?></textarea>
                    </div>
					
					
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab15_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                  	<img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                    	<?php echo $mgrlang['landf_f_uploadwatermark']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['landf_f_uploadwatermark_d']; ?></span>
                    </p>
										<input name="uploadWatermark" type="file" style="font-size: 11; width: 500">
									</div>
									<div class="<?php fs_row_color(); ?>">
                  	<img src="images/mgr.ast.off.gif" class="ast" />
                		<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                    	<?php echo $mgrlang['landf_f_deletewatermark']; ?>: <br />
										</p>
										<br>
                			<?PHP
                			foreach($watermark as $value){
                				if($config['settings']['thumb_wm'] == $value or $config['settings']['rollover_wm'] == $value or $config['settings']['preview_wm'] == $value or $config['settings']['vidrollover_wm'] == $value or $config['settings']['vidpreview_wm'] == $value){
                					//match so skip showing it
                				} else {
                    			echo "<span class='watermark_indent'><input type='checkbox' name='deleteWatermark[]' value='$value'>&nbsp;&nbsp;&nbsp;".$value."</span><br>";
												}
                				
											}
											?>
                	</div>
                </div>
                	
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_logo']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_logo_d']; ?></span>
                        </p>
                        <div id="uploadwrapper"><div style="color: #990000; padding-top: 6px;" id="uploader"></div></div>
                        <div id="logo_div" style='float: left;'><!--<?php echo $config['settings']['mainlogo']; ?>--></div>
                    </div>              			
                </div>
				
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group">
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group">
					slideshow active (not per gallery) / slideshow newest, popular, searches, galleries / transition time / size & watermark (same as preview)?  <br />             			
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">
                      photo transition: fade/etc<br />            			
                </div>
				
				<?php $row_color = 0; ?>
                <div id="tab12_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_newsarea']; ?>:<br />
                            <span><?php echo $mgrlang['landf_f_newsarea_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hpnews" <?php if($config['settings']['hpnews']){ echo "checked"; } ?> />
                    </div>
                    
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_newmed']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_newmed_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="hpnewestmedia" name="hpnewestmedia" onclick="cb_bool('hpnewestmedia','new_media_ro','new_media_ops');" <?php if($config['settings']['hpnewestmedia']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['hpnewestmedia']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="new_media_ro"><a href="javascript:show_div('new_media_ops');hide_div('new_media_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="new_media_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('new_media_ops');show_div('new_media_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_how_many']; ?>:<br />
                                <span><?php echo $mgrlang['landf_f_newmed2']; ?></span>
                            </p>
                            <input type="text" value="<?php echo $config['settings']['new_media_count']; ?>" name="new_media_count" style="width: 50px" />
                            </div>                            
                        </div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_popmed']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_popmed_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="hppopularmedia" name="hppopularmedia" onclick="cb_bool('popular_media','popular_media_ro','popular_media_ops');" <?php if($config['settings']['hppopularmedia']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['hppopularmedia']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="popular_media_ro"><a href="javascript:show_div('popular_media_ops');hide_div('popular_media_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="popular_media_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('popular_media_ops');show_div('popular_media_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_how_many']; ?>:<br />
                                <span><?php echo $mgrlang['landf_f_popmed2']; ?></span>
                            </p>
                            <input type="text" value="<?php echo $config['settings']['popular_media_count']; ?>" name="popular_media_count" style="width: 50px" />
                            </div>                            
                        </div>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_randmed']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_randmed_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" value="1" id="hprandmedia" name="hprandmedia" onclick="cb_bool('hprandmedia','random_media_ro','random_media_ops');" <?php if($config['settings']['hprandmedia']){ echo "checked"; } ?> /></div> <div style="float: left; padding: 8px 0 0 14px; <?php if($config['settings']['hprandmedia']){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="random_media_ro"><a href="javascript:show_div('random_media_ops');hide_div('random_media_ro');" class="actionlink"><?php echo $mgrlang['gen_related_actions']; ?></a></div>
                    	<div id="random_media_ops" class="related_options">
                        	<div style="position: absolute; right: 4px;"><a href="javascript:hide_div('random_media_ops');show_div('random_media_ro');"><img src="images/mgr.button.close2.png" border="0" style="margin: 10px 10px 10px 10px;" /></a></div>
                            <div class="fs_row_off">
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_how_many']; ?>:<br />
                                <span><?php echo $mgrlang['landf_f_randmed2']; ?></span>
                            </p>
                            <input type="text" value="<?php echo $config['settings']['random_media_count']; ?>" name="random_media_count" style="width: 50px" />
                            </div>                            
                        </div>
                    </div>
					
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featpromo']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featpromo_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hppromos" <?php if($config['settings']['hppromos']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featprint']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featprint_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hpprints" <?php if($config['settings']['hpprints']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featprod']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featprod_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hpprods" <?php if($config['settings']['hpprods']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featpacks']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featpacks_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hppacks" <?php if($config['settings']['hppacks']){ echo "checked"; } ?> />
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featcolls']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featcolls_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hpcolls" <?php if($config['settings']['hpcolls']){ echo "checked"; } ?> />
                    </div>
                    <?php
						if($config['settings']['subscriptions'])
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['landf_f_featsubs']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['landf_f_featsubs_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="hpsubs" <?php if($config['settings']['hpsubs']){ echo "checked"; } ?> />
						</div>
					<?php
						}
						if(in_array("creditsys",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['landf_f_featcred']; ?>: <br />
								<span class="input_label_subtext"><?php echo $mgrlang['landf_f_featcred_d']; ?></span>
							</p>
							<input type="checkbox" value="1" name="hpcredits" <?php if($config['settings']['hpcredits']){ echo "checked"; } ?> />
						</div>
					<?php
						}
					?>
					<div class="fs_header"><?php echo $mgrlang['webset_featured_media']; ?></div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_featuremed']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_featuremed_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="hpfeaturedmedia" <?php if($config['settings']['hpfeaturedmedia']){ echo "checked"; } ?> />
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dp_watermark']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_watermark_d']; ?></span>
						</p>
						<input type="checkbox" value="1" name="featured_wm" <?php if($config['settings']['featured_wm']){ echo "checked"; } ?> />
					</div>
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_width']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_width_d']; ?></span>
						</p>
						<input type="text" name="hpf_width" value="<?php echo $config['settings']['hpf_width']; ?>" />
					</div>
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_crop']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_crop_d']; ?></span>
						</p>
						<input type="text" name="hpf_crop_to" value="<?php echo $config['settings']['hpf_crop_to']; ?>" />
					</div>
					<?php if($config['settings']['style'] == 'modern'){ ?><div style="display: none;"><?php } else { ?><div class="<?php fs_row_color(); ?>"><?php } ?>
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_fadespeed']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_fadespeed_d']; ?></span>
						</p>
						<input type="text" name="hpf_fade_speed" value="<?php echo $config['settings']['hpf_fade_speed']; ?>" /> <?php echo $mgrlang['gen_milliseconds']; ?>
					</div>
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_inverval']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_inverval_d']; ?></span>
						</p>
						<input type="text" name="hpf_inverval" value="<?php echo $config['settings']['hpf_inverval']; ?>" /> <?php echo $mgrlang['gen_milliseconds']; ?>
					</div>
					<?php if($config['settings']['style'] == 'modern'){ ?><div style="display: none;"><?php } else { ?><div class="<?php fs_row_color(); ?>"><?php } ?>
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_ddelay']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_ddelay_d']; ?></span>
						</p>
						<input type="text" name="hpf_details_delay" value="<?php echo $config['settings']['hpf_details_delay']; ?>" /> <?php echo $mgrlang['gen_milliseconds']; ?>
					</div>
					<?php if($config['settings']['style'] == 'modern'){ ?><div style="display: none;"><?php } else { ?><div class="<?php fs_row_color(); ?>"><?php } ?>
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_hpf_ddis']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_hpf_ddis_d']; ?></span>
						</p>
						<input type="text" name="hpf_details_distime" value="<?php echo $config['settings']['hpf_details_distime']; ?>" /> <?php echo $mgrlang['gen_milliseconds']; ?>
					</div>
				</div>
				
				<?php $row_color = 0; ?>
                <div id="tab13_group" class="group">
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['landf_f_galthumbsize']; ?>: <br />
							<span class="input_label_subtext"><?php echo $mgrlang['landf_f_galthumbsize_d']; ?></span>
						</p>
						<input type="text" id="gallery_thumb_size" name="gallery_thumb_size" value="<?php echo $config['settings']['gallery_thumb_size']; ?>" />
					</div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_cropgalthumb']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_cropgalthumb_d']; ?></span>
                        </p>
                        <div style="float: left;"><input type="checkbox" name="gallerythumbcrop" id="gallerythumbcrop" value="1" onclick="change_gtcrop();" <?php if($config['settings']['gallerythumbcrop']){ echo "checked='checked'"; } ?> /> <img src="images/mgr.sample.<?php if($config['settings']['gallerythumbcrop']){ echo "crop"; } else { echo "nocrop"; } ?>.gif" id="gallerythumbcropimg" align="absmiddle" /></div>
						<div id="gallerythumbcropheight" style="float: left; <?php if(!$config['settings']['gallerythumbcrop']){ echo "display: none;"; } ?> padding-left: 10px; padding-top: 8px;">
							<?php echo $mgrlang['landf_crop_height']; ?>: <input type="text" name="gallerythumbcrop_height" value="<?php echo $config['settings']['gallerythumbcrop_height']; ?>" style="width: 26px" /> px
						</div>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_galperpage']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_galperpage_d']; ?></span>
                        </p>
                        <input type="text" name="gallery_perpage" id="gallery_perpage" value="<?php echo $config['settings']['gallery_perpage']; ?>" style="width: 50px;" />
                    </div>
					<div class="<?php fs_row_color(); ?>" id="media_perpage_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['lookfeel_f_mpp']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['lookfeel_f_mpp_d']; ?></span>
                        </p>
                        <input type="text" name="media_perpage" id="media_perpage" value="<?php echo $config['settings']['media_perpage']; ?>" style="width: 50px;" />
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_galdsort']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_galdsort_d']; ?></span>
                        </p>
						<select name="dsorting">
                            <option value="date_added" <?php if($config['settings']['dsorting'] == 'date_added'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_added']; ?></option>
							<option value="date_created" <?php if($config['settings']['dsorting'] == 'date_created'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['mediadet_datecreated']; ?></option>
                            <option value="media_id" <?php if($config['settings']['dsorting'] == 'media_id'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_id']; ?></option>
                            <option value="title" <?php if($config['settings']['dsorting'] == 'title'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_title']; ?></option>
                            <option value="filename" <?php if($config['settings']['dsorting'] == 'filename'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_filename']; ?></option>
							<option value="filesize" <?php if($config['settings']['dsorting'] == 'filesize'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_filesize']; ?></option>
							<!--<option value="ofilename" <?php if($config['settings']['dsorting'] == 'ofilename'){ echo "selected='selected'"; } ?>>Original Filename</option>-->
							<option value="width" <?php if($config['settings']['dsorting'] == 'width'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_width']; ?></option>
							<option value="height" <?php if($config['settings']['dsorting'] == 'height'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_height']; ?></option>
							<option value="sortorder" <?php if($config['settings']['dsorting'] == 'sortorder'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_snumber']; ?></option>							
							<option value="batch_id" <?php if($config['settings']['dsorting'] == 'batch_id'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_bid']; ?></option>
							<option value="featured" <?php if($config['settings']['dsorting'] == 'featured'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_featured']; ?></option>
							<option value="views" <?php if($config['settings']['dsorting'] == 'views'){ echo "selected='views'"; } ?>><?php echo $mgrlang['sort_views']; ?></option>
                            <!--
							<option value="rating" <?php if($config['settings']['dsorting'] == 'rating'){ echo "selected='selected'"; } ?>>Rating</option>
                            <option value="comments" <?php if($config['settings']['dsorting'] == 'comments'){ echo "selected='selected'"; } ?>>Comments</option>
							-->
                        </select>
                        <select name="dsorting2">
                            <option value="ASC" <?php if($config['settings']['dsorting2'] == 'ASC'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_asce']; ?></option>
                            <option value="DESC" <?php if($config['settings']['dsorting2'] == 'DESC'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['sort_desc']; ?></option>
                        </select>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_gallerySorting']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_gallerySorting_d']; ?></span>
                        </p>
                        <select name="gallerySortBy" style="width: 150px;">
                           <option value="sort_number" <?php if($config['settings']['gallerySortBy'] == 'sort_number'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_sortnumber']; ?></option>
                           <option value="name" <?php if($config['settings']['gallerySortBy'] == 'name'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_name']; ?></option>
                           <option value="created" <?php if($config['settings']['gallerySortBy'] == 'created'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_created']; ?></option>
                           <option value="edited" <?php if($config['settings']['gallerySortBy'] == 'edited'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_edited']; ?></option>
                           <option value="active_date" <?php if($config['settings']['gallerySortBy'] == 'active_date'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_activedate']; ?></option>
                           <option value="expire_date" <?php if($config['settings']['gallerySortBy'] == 'expire_date'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_expiredate']; ?></option>
                           <option value="event_date" <?php if($config['settings']['gallerySortBy'] == 'event_date'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_eventdate']; ?></option>
                           <option value="event_location" <?php if($config['settings']['gallerySortBy'] == 'event_location'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_eventlocation']; ?></option>
                           <option value="event_code" <?php if($config['settings']['gallerySortBy'] == 'event_code'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySortBy_eventcode']; ?></option>
                        </select>
                        <select name="gallerySortOrder" style="width: 150px;">
                           <option value="" <?php if($config['settings']['gallerySortOrder'] == ''){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySorting_ascending']; ?></option>
                           <option value="DESC" <?php if($config['settings']['gallerySortOrder'] == 'DESC'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_gallerySorting_descending']; ?></option>
                        </select>
                    </div>
				</div>
                
				<?php $row_color = 0; ?>
				<!-- Logo/watermark options: http://www.longtailvideo.com/support/jw-player/jw-player-for-flash-v5/12536/configuration-options -->
                <div id="tab14_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_cont']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_cont_d']; ?></span>
                        </p>
                        <select name="video_controls" style="width: 150px;">
                            <option value="top" <?php if($config['settings']['video_controls'] == 'top'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_top']; ?></option>
                            <option value="bottom" <?php if($config['settings']['video_controls'] == 'bottom'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_bottom']; ?></option>
                            <option value="over" <?php if($config['settings']['video_controls'] == 'over'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_over']; ?></option>
							<option value="none" <?php if($config['settings']['video_controls'] == 'none'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_hidden']; ?></option>
                        </select>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_skin']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_skin_d']; ?></span>
                        </p>
                        <select name="video_skin" style="width: 150px;">
                            <?php
								$realSkinsDir = realpath("../assets/jwplayer/skins");
								$skinsDir = opendir($realSkinsDir);
								while($file = readdir($skinsDir))
								{
									if($file != ".." && $file != "." && is_dir("../assets/jwplayer/skins/" . $file))
									{
										echo "<option value='{$file}'";
										if($config['settings']['video_skin'] == $file){ echo "selected='selected'"; }
										echo ">{$file}</option>";
										
									}
								}
							?>
                        </select>
                    </div>					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_ap']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_ap']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="video_autoplay" id="video_autoplay" <?php if($config['settings']['video_autoplay']){ echo "checked='checked'"; } ?> />
                    </div> 
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_loop']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_loop_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="video_autorepeat" id="video_autorepeat" <?php if($config['settings']['video_autorepeat']){ echo "checked='checked'"; } ?> />
                    </div>  
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_bgcolor']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_bgcolor']; ?></span>
                        </p>
                        <input type="text" name="video_bg_color" value="<?php echo $config['settings']['video_bg_color']; ?>" style="width: 50px;" />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_rosize']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_rosize_d']; ?></span>
                        </p>
                        <?php echo $mgrlang['gen_width']; ?>: <input type="text" name="video_rollover_width" value="<?php echo $config['settings']['video_rollover_width']; ?>" style="width: 50px;" /> <?php echo $mgrlang['gen_height']; ?>: <input type="text" name="video_rollover_height" value="<?php echo $config['settings']['video_rollover_height']; ?>" style="width: 50px;" />
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_prsize']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_prsize_d']; ?></span>
                        </p>
                        <?php echo $mgrlang['gen_width']; ?>: <input type="text" name="video_sample_width" value="<?php echo $config['settings']['video_sample_width']; ?>" style="width: 50px;" /> <?php echo $mgrlang['gen_height']; ?>: <input type="text" name="video_sample_height" value="<?php echo $config['settings']['video_sample_height']; ?>" style="width: 50px;" />
                    </div> 
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_autors']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_autors_d']; ?></span>
                        </p>
                        <input type="checkbox" value="1" name="video_autoresize" id="video_autoresize" <?php if($config['settings']['video_autoresize']){ echo "checked='checked'"; } ?> />
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_rowm']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_rowm_d']; ?></span>
                        </p>
                        <select style="width: 150px;" name="vidrollover_wm" id="vidrollover_wm">                                    
							<option value=""><?php echo $mgrlang['gen_none']; ?></option>
							<?php
								foreach($watermark as $value){
									echo "<option value=\"$value\" ";
									if($value == $config['settings']['vidrollover_wm']){ echo "selected='selected'"; }
									echo ">$value</option>";
								}
							?>
						</select>
                    </div> 
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_prwm']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_prwm_d']; ?></span>
                        </p>
                        <select style="width: 150px;" name="vidpreview_wm" id="vidpreview_wm">                                    
							<option value=""><?php echo $mgrlang['gen_none']; ?></option>
							<?php
								foreach($watermark as $value){
									echo "<option value=\"$value\" ";
									if($value == $config['settings']['vidpreview_wm']){ echo "selected='selected'"; }
									echo ">$value</option>";
								}
							?>
						</select>
                    </div>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['landf_f_vid_wmpos']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['landf_f_vid_wmpos_d']; ?></span>
                        </p>
                        <select name="video_wmpos" style="width: 150px;">
                            <option value="bottom-left" <?php if($config['settings']['video_wmpos'] == 'bottom-left'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_vid_wmpos1']; ?></option>
							<option value="bottom-right" <?php if($config['settings']['video_wmpos'] == 'bottom-right'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_vid_wmpos2']; ?></option>
							<option value="top-left" <?php if($config['settings']['video_wmpos'] == 'top-left'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_vid_wmpos3']; ?></option>
							<option value="top-right" <?php if($config['settings']['video_wmpos'] == 'top-right'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['landf_f_vid_wmpos4']; ?></option>
                        </select>
                    </div>	           			
                </div>
				
                <div id="save_bar">							
                    <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.settings.php?ep=1');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
                </div>
            </div>
            <!-- END CONTENT -->
            <div class="footer_spacer"></div>
        </div>
        
        <!-- END CONTENT CONTAINER -->
        </form>
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>
