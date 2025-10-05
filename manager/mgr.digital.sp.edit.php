<?php
	###################################################################
	####	DIGITAL PRICING SCHEMES EDITOR                         ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-31-2008                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "digital_sp";
		$lnav = "library";
		
		$supportPageID = '321';
		
		$profile_vars = 1;
	
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
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '$_GET[edit]'");
			$digital_sp_rows = mysqli_num_rows($digital_sp_result);
			$digital_sp = mysqli_fetch_object($digital_sp_result);
			
			$dpLicenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses WHERE license_id = '{$digital_sp->license}'");
			$dpLicenseRows = mysqli_num_rows($dpLicenseResult);
			$dpLicense = mysqli_fetch_assoc($dpLicenseResult);
			
		}
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		# READ ALL WATERMARK FILES
		$real_dir = realpath("../assets/watermarks/");
		$dir = opendir($real_dir);
		# LOOP THROUGH THE WATERMARK DIRECTORY
		$watermark = array();
		while($file = readdir($dir)){
			// MAKE SURE IT IS A VALID FILE
			$ispng = explode(".", $file);
			if($file != ".." && $file != "." && is_file("../assets/watermarks/" . $file) && @$ispng[count($ispng) - 1] == "png"){
				$watermark[] = "$file";
			}
		}
		
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" ){
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			$licParts = explode("-",$license);
						
			# FIX PRICE ISSUE
			if($licParts[0] == "fr"){
				$price = "0";
			}
			# FIX SIZE
			if(empty($size) or !is_numeric($size) or $size == '0'){
				$size = "500";
			}
			# SET CREDITS
			if($license == "fr"){
				$credits = "0";
			}
			# SET PERM
			if(empty($perm) or $perm == 'everyone'){
				$everyone = "1";
			} else {
				$everyone = '0';
			}
			
			# CLEAN THE DOLLAR VALUES
			$price_clean = $cleanvalues->currency_clean($price);
			$min_contr_price_clean = $cleanvalues->currency_clean($min_contr_price);
			$max_contr_price_clean = $cleanvalues->currency_clean($max_contr_price);
			$commission_dollar_clean = $cleanvalues->currency_clean($commission_dollar);
			
			if($_REQUEST['action'] == "save_edit"){
				# UPDATE THE DATABASE
				//echo "---" . $saveid; exit;
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$name_val = ${"name_" . $value};
					$addsql.= "name_$value='$name_val',";
				}	
				
				if($attachment == 'none') $all_galleries = 0;		
				
				$sql = "UPDATE {$dbinfo[pre]}digital_sizes SET 
							name='$name',";
				$sql.= $addsql;
				$sql.= "	active='1',
							dspgroups='$plangroups',
							item_code='$item_code',
							size='$size',
							dsp_type='{$dsp_type}',
							format='{$format}',
							fps='{$fps}',
							hd='{$hd}',
							running_time='{$running_time}',
							width='{$width}',
							height='{$height}',
							force_list='{$force_list}',
							license='{$licParts[1]}',
							real_sizes='{$real_sizes}',
							rm_license='$rm_license',
							price='$price_clean',
							price_calc='$price_calc',
							credits='$credits',
							credits_calc='$credits_calc',
							quantity='$quantity',
							taxable='$taxable',
							everyone='$everyone',
							commission='$commission',
							min_contr_price='$min_contr_price_clean',
							max_contr_price='$max_contr_price_clean',
							min_contr_credits='$min_contr_credits',
							max_contr_credits='$max_contr_credits',
							commission_type='$commission_type',
							commission_dollar='$commission_dollar_clean',
							attachment='$attachment',
							all_galleries='$all_galleries',
							delivery_method='$delivery_method',
							contr_sell='{$contr_sell}',
							watermark='$watermark'
							where ds_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
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
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_digital_sp'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				$headerloc = "mgr.digital.sp.php?mes=edit";
			}
			
			if($_REQUEST['action'] == "save_new"){
			
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$name_val = ${"name_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}digital_sizes (
						name,
						active,
						dspgroups,
						item_code,
						size,
						dsp_type,
						fps,
						hd,
						format,
						running_time,
						width,
						height,
						force_list,
						license,
						real_sizes,
						rm_license,
						price,
						price_calc,
						credits,
						credits_calc,
						quantity,
						taxable,
						everyone,
						perm,
						commission,
						min_contr_price,
						max_contr_price,
						min_contr_credits,
						max_contr_credits,
						commission_type,
						commission_dollar,
						attachment,
						all_galleries,
						delivery_method,
						contr_sell,
						watermark";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'1',
						'$plangroups',
						'$item_code',
						'$size',
						'{$dsp_type}',
						'{$fps}',
						'{$hd}',
						'{$format}',
						'{$running_time}',
						'{$width}',
						'{$height}',
						'{$force_list}',
						'{$licParts[1]}',
						'{$real_sizes}',
						'$rm_license',
						'$price_clean',
						'$price_calc',
						'$credits',
						'$credits_calc',
						'$quantity',
						'$taxable',
						'$everyone',
						'$perm',
						'$commission',
						'$min_contr_price_clean',
						'$max_contr_price_clean',
						'$min_contr_credits',
						'$max_contr_credits',
						'$commission_type',
						'$commission_dollar_clean',
						'$attachment',
						'$all_galleries',
						'$delivery_method',
						'{$contr_sell}',
						'$watermark'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);
				//$last_id = mysqli_insert_id($db);
				$saveid = mysqli_insert_id($db);
				
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
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
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_digital_sp'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
			
				$headerloc = "mgr.digital.sp.php?mes=new";					
			}
			
			//echo $saveid; exit;
			/*
			$order=1;				
			foreach($ps_isnew as $key => $value){
				# SET THE QUANTITY TO 9999 IF IT IS LEFT BLANK
				
				//echo "test". $ps_credit_calc[$key]; exit;
				
				if(empty($ps_quantity[$key]) and $ps_quantity[$key] != '0'){
					$quan = '9999';
				} else {
					$quan = $ps_quantity[$key];
				}
				# FIX PRICE ISSUE
				if(empty($ps_price[$key]) or $ps_license[$key] == "fr"){
					$price = "0";
					//echo $price; exit;
				} else {
					$price = $ps_price[$key];
				}
				# SET CREDITS
				if($ps_license[$key] == "fr"){
					$credits = "0";
					//echo $price; exit;
				} else {
					$credits = $ps_credits[$key];
				}
				# SET PERM
				if(empty($ps_perm[$key])){
					$perm = "everyone";
					//echo $price; exit;
				} else {
					$perm = $ps_perm[$key];
				}
				
				//echo $value; exit;

				# ENTER NEW ITEMS INTO THE DATABASE
				if($value == "new"){
					//echo "-$value : " . $_POST['ps_item_name'][$key] . " : " . $_POST['ps_id'][$key] . "<br />";						
					//echo $ps_quantity[$key]; exit;						
					$uds_id = create_unique2();						
					
					$addsqla="";
					$addsqlb="";
					# ADD SUPPORT FOR ADDITIONAL LANGUAGES
					foreach($active_langs as $value2){ 
						$lname = ${"ps_item_name_" . $value2}[$key];
						$addsqla.= ",item_name_$value2";
						$addsqlb.= ",'$lname'"; 
					}
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}digital_sizes (
							uds_id,
							parent_id,
							item_code,
							item_name,
							perm,
							size,
							price,
							price_calc,
							credits,
							credits_calc,
							quantity,
							taxable,
							license,
							active,
							sortorder";
					$sql.=$addsqla;
					$sql.=	") VALUES (
							'$uds_id',
							'$saveid',
							'" . $ps_item_code[$key] . "',
							'" . $ps_item_name[$key] . "',
							'$perm',
							'" . $ps_size[$key] . "',
							'$price',
							'" . $ps_price_calc[$key] . "',
							'$credits',
							'" . $ps_credit_calc[$key] . "',
							'" . $quan . "',
							'" . $ps_taxable[$key] . "',
							'" . $ps_license[$key] . "',
							'1',
							'$order'";
					$sql.=$addsqlb;
					$sql.=	")";
					//echo "$sql"; exit;
					$result = mysqli_query($db,$sql);						
				# UPDATE ITEMS ALREADY IN THE DATABASE
				} else {						
					//echo $ps_taxable[$key]; exit;
					$addsql="";
					# ADD SUPPORT FOR ADDITIONAL LANGUAGES
					foreach($active_langs as $value2){ 
						$lname = ${"ps_item_name_" . $value2}[$key];
						$addsql.= "item_name_$value2='$lname',";
					}
					
					//echo $addsql; exit;
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}digital_sizes SET 
								item_code='" . $ps_item_code[$key] . "',
								item_name='" . $ps_item_name[$key] . "',
								perm='$perm',";
					$sql.=$addsql;			
					$sql.=		"size='" . $ps_size[$key] . "',
								price='$price',
								price_calc='" . $ps_price_calc[$key] . "',
								credits='$credits',
								credits_calc='" . $ps_credit_calc[$key] . "',
								quantity='" . $ps_quantity[$key] . "',
								taxable='" . $ps_taxable[$key] . "',
								license='" . $ps_license[$key] . "',
								active='1',
								sortorder='$order'
								where ds_id  = '$value'";
					//echo $sql; exit;
					$result = mysqli_query($db,$sql);						
				}
				//echo "-$value : " . $_POST['ps_item_name'][$key] . " : " . $_POST['ps_id'][$key] . "<br />";
				$order++;
			}
			*/
			header("location: $headerloc"); exit;
		}
					
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_digital_sp']; ?></title>
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
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
    <!-- LOAD SLIDER CODE -->
    <script type="text/javascript" src="../assets/javascript/slider.js"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
	<script language="javascript" type="text/javascript">	
		function form_submitter(){
			// REVERT BACK
			$('name_div').className='fs_row_off';
			
			// CHECK FOR ITEM NAME
			//var curinput = 0;
			//var inputerror = 0;
			//$$('input.input_itemname').each(
			//	function (){
			//		if($F($$('input.input_itemname')[curinput]) == "" || $F($$('input.input_itemname')[curinput]) ==  null){
			//			inputerror = 1;
			//		}
			//		curinput++;
			//	}				
			//);
			//if(inputerror == 1){
			//	simple_message_box('<?php echo $mgrlang['dsp_mes_01']; ?>','');
			//	return false;
			//}
			// CHECK FOR SIZES
			//var curinput = 0;
			//$$('input.input_size').each(
			//	function (){
			//		if($F($$('input.input_size')[curinput]) == "" || $F($$('input.input_size')[curinput]) ==  null){
			//			inputerror = 1;
			//		}
			//		curinput++;
			//	}				
			//);
			//if(inputerror == 1){
			//	simple_message_box('<?php echo $mgrlang['dsp_mes_03']; ?>','');
			//	return false;
			//}
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.digital.sp.edit.php?action=save_new" : "mgr.digital.sp.edit.php?action=save_edit";
					
			?>
					var selecteditem = $('license').options[$('license').selectedIndex].value;					
					var licParts = selecteditem.split('-');
					
					if(licParts[0] == "rf" || licParts[0] == "rm")
					{
						// CHECK FOR PRICE OF 0 - LEAVE THIS OUT FOR NOW
						if($F('price') <= 0 && $F('price') != "")
						{
							//alert('test');
							return false;
						}
						
					}
			<?php	
					
					//min_contr_credits
					
					js_validate_field("name","dsp_f_name",1);
					//js_validate_field("size","dsp_f_size",1);
					//echo "\n return false;";
				}
			?>
			//return false;
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
		
		function dspTypeSelect()
		{	
			var selecteditem = $F('dsp_type');
			$('dmAttachFile').setAttribute('selected','selected');
			switch(selecteditem)
			{			
				default:
					$('video_div').hide();
					$('dmCreateAuto').show();
					
				break;
				case "video":
					$('video_div').show();
					$('dmCreateAuto').hide();
					$('createAutoMessage').hide();
				break;
			}
		}
		
		function dspDeliveryMethod()
		{
			if($F('delivery_method') == '1')
				$('createAutoMessage').show();	
			else
				$('createAutoMessage').hide();
		}
		
		var permissions = new Array();
		
		function test_perm_array(){
			alert(permissions.length);
			//permissions.each(function(value){ alert(value); });
		}
		
		function update_license(){
			var selecteditem = $F('license');	
			var licParts = selecteditem.split('-');
			
			$('pricespan').show();
			$('creditsspan').show();
			//$('rmspan').hide();
			
			switch(licParts[0]){
				case "cu":
					$('pricespan').hide();
					$('creditsspan').hide();
				break;
				case "fr":
					$('pricespan').hide();
					$('creditsspan').hide();
				break;
				case "rm":
					//$('rmspan').show();
				break;
				case "ex":
				case "eu":
				case "rf":
				default:					
				break;
			}
			update_fsrow('tab5_group');
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
			
			update_fsrow('tab1_group');
			update_fsrow('tab4_group');
			// LOAD THE GALLERIES
			load_gals();
		});
		
		// LOAD PARENT GALLERIES BASED OFF OF PERMISSIONS
		function load_gals()
		{
			show_loader('gals');
			//alert($F('permowner'));
			var pars = 'pmode=galleries&id=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('gals', 'mgr.digital.sp.actions.php', {method: 'get', parameters: pars, onComplete: display_in_all_check});
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
		
		// THE COMMISSION TYPE BOX HAS BEEN CHANGED
		function commission_type_change()
		{
			var selecteditem = $('commission_type').options[$('commission_type').selectedIndex].value;
			
			if(selecteditem == '1')
			{
				show_div('com_percentage');
				hide_div('com_dollar');
				update_fsrow('tab4_group');
			}
			else
			{
				hide_div('com_percentage');
				show_div('com_dollar');
				update_fsrow('tab4_group');
			}
		}
	</script>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php');?>
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
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['dsp_new_header'] : $mgrlang['dsp_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['dsp_new_message'] : $mgrlang['dsp_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
                <div id="spacer_bar"></div>        
                <?php
					# PULL GROUPS
					$dsp_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$dsp_group_rows = mysqli_num_rows($dsp_group_result);
				?>
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['gen_tab_details']; ?></div> 
                    <div class="subsuboff" onclick="bringtofront('5');" id="tab5"><?php echo $mgrlang['gen_tab_pricing']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('6');" id="tab6"><?php echo $mgrlang['gen_tab_attach']; ?></div>                     
                    <?php if($dsp_group_rows and in_array("pro",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['gen_tab_groups']; ?></div><?php } ?>
                    <?php if($config['settings']['cart'] and in_array("contr",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['gen_tab_contributors']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding-bottom: 5px;">                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_profiletype']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_profiletype_d']; ?></span>
                        </p>
                        <select style="width: 300px;" name="dsp_type" id="dsp_type" onchange="dspTypeSelect();">
                        	<option value="photo" <?php if($digital_sp->dsp_type == 'photo'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_photo']; ?></option>
                            <?php if(in_array('mediaextender',$installed_addons)) { ?><option value="video" <?php if($digital_sp->dsp_type == 'video'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_video']; ?></option><?php } ?>
                            <option value="other" <?php if($digital_sp->dsp_type == 'other'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_other']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="name_div" fsrow='1'>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_name']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_name_d']; ?></span>
                        </p>
                        
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 290px;" maxlength="100" value="<?php echo @stripslashes($digital_sp->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>                    
                    <div id="video_div" style="display: <?php if($digital_sp->dsp_type == 'video'){ echo "block"; } else { echo "none"; } ?>">
                        <div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['media_f_hd']; ?>:<br />
                                <span><?php echo $mgrlang['media_f_hd_d']; ?></span>
                            </p>
							<input type="checkbox" name="hd" id="hd" value="1" <?php if($digital_sp->hd){ echo "checked='checked'"; } ?> />
                        </div>
						<div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['media_f_fps']; ?>:<br />
                                <span><?php echo $mgrlang['media_f_fps_d']; ?></span>
                            </p>
							<input type="text" name="fps" id="fps" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->fps); ?>" />
                        </div>
                        <div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['media_f_runtime']; ?>:<br />
                                <span><?php echo $mgrlang['media_f_runtime_d']; ?></span>
                            </p>
							<input type="text" name="running_time" id="running_time" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->running_time); ?>" /> <?php echo $mgrlang['gen_seconds']; ?>
                        </div>
                    </div>
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['media_f_format']; ?>:<br />
							<span><?php echo $mgrlang['media_f_format_d']; ?></span>
						</p>
						<input type="text" name="format" id="format" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($digital_sp->format); ?>" />
					</div>                    
                    <div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['media_f_width']; ?>:<br />
                        	<span><?php echo $mgrlang['media_f_width_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="width" id="width" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->width); ?>" />
                    </div> 
                    <div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['media_f_height']; ?>:<br />
                        	<span><?php echo $mgrlang['media_f_height_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="height" id="height" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->height); ?>" />
                    </div>
					<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_delivery']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_delivery_d']; ?></span>
                        </p>						
						<select name="delivery_method" id="delivery_method" style="width: 300px; float: left;" onChange="dspDeliveryMethod();">
							<option id="dmAttachFile" value="0" <?php if($digital_sp->delivery_method == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['del_linked_file']; ?></option>
							<option id="dmCreateAuto" value="1" <?php if($digital_sp->delivery_method == 1){ echo "selected='selected'"; } ?> <?php if($digital_sp->dsp_type == 'video'){ echo "style='display: none;'"; } ?>><?php echo $mgrlang['create_automat']; ?></option>
							<option id="dmDelManually" value="2" <?php if($digital_sp->delivery_method == 2){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_del_manually']; ?></option>
							<option id="dmDelOriginal" value="3" <?php if($digital_sp->delivery_method == 3){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_del_orig']; ?></option>
						</select>
						<div style="float: left; margin-left: 10px; vertical-align: middle; <?php if($digital_sp->delivery_method != 1){ echo "display: none"; } ?>" id="createAutoMessage">
							<img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 7px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['gen_create_auto_mes']; ?></div>
						</div>
                    </div>
                </div>  
                    
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_code']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_code_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="item_code" id="item_code" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->item_code); ?>" />
                    </div> 
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['dsp_f_force_dis']; ?>:<br />
                            <span><?php echo $mgrlang['dsp_f_force_dis_d']; ?></span>
                        </p>
                        <input type="checkbox" name="force_list" value="1" <?php if($digital_sp->force_list or $_REQUEST['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div>
					<div class="<?php fs_row_color(); ?>" id="size_div" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_cal_rs']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_cal_rs_d']; ?></span>
                        </p>
                        <input type="checkbox" name="real_sizes" value="1" <?php if($digital_sp->real_sizes or $_REQUEST['edit'] == 'new'){ echo "checked='checked'"; } ?> />
                    </div> 
                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                      <img src="images/mgr.ast.off.gif" class="ast" />
                      <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['dp_watermark']; ?>:<br />
                        <span><?php echo $mgrlang['dp_watermark_d']; ?></span>
                      </p>
                      <select style="width: 250px;" name="watermark" id="watermark">                                    
                        <option value=""><?php echo $mgrlang['gen_none']; ?></option>
                        <?php
                        foreach($watermark as $value){
                        	list($wm_width, $wm_height) = getimagesize("../assets/watermarks/$value");
                          echo "<option value=\"$value\" ";
												if($value == $digital_sp->watermark){ echo "selected='selected'"; }
													echo ">$value - ".$wm_width."x".$wm_height."px</option>";
                        	unset($wm_width);
                        	unset($wm_height);
                        }
                        ?>
                      </select>
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
							if($_GET['edit'] != 'new' and $digital_sp->everyone == '0'){
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
                        <div style="float: left; padding-right: 10px; font-weight: bold; <?php if($digital_sp->everyone == 1 or $_GET['edit'] == 'new'){ echo "display: block;"; } else { echo "display: none;"; }  ?>" id="perm_div_a"><?php echo $mgrlang['gen_wb_everyone']; ?></div>
						<div style="float: left; padding-right: 10px; font-weight: bold; <?php if($digital_sp->everyone == 0 and $_GET['edit'] != 'new'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="perm_div_b"><?php echo $mgrlang['gen_wb_limited']; ?></div>	
                        <input type="hidden" name="perm" id="perm<?php echo $digital_sp->ds_id; ?>" value="<?php echo $perm_value; ?>" />
                        <div style="float: left;"><a href="javascript:workbox2({page: 'mgr.workbox.php',pars: 'box=permissions_selector&style=everyone&inputbox=perm<?php echo $digital_sp->ds_id; ?>&multiple=1'});" class="actionlink"><img src="images/mgr.people.icon.png" border="0" id="perm_icon" align="middle" /></a></div>
                    </div>                   
            	</div>
                
                <?php
                	if($dsp_group_rows){
						$row_color = 0;
				?>
                    <div id="tab2_group" class="group"> 
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['dsp_f_groups']; ?>:<br />
                                <span><?php echo $mgrlang['dsp_f_groups_d']; ?></span>
                            </p>
                            <?php
                                $plangroups = array();
                                # FIND THE GROUPS THAT THIS ITEM IS IN
                                $dsp_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$digital_sp->ds_id' AND item_id != 0");
                                while($dsp_groupids = mysqli_fetch_object($dsp_groupids_result)){
                                    $plangroups[] = $dsp_groupids->group_id;
                                }
                                
                                echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
                                while($dsp_group = mysqli_fetch_object($dsp_group_result)){
                                    echo "<li><input type='checkbox' id='$dsp_group->gr_id' class='permcheckbox' name='setgroups[]' value='$dsp_group->gr_id' "; if(in_array($dsp_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($dsp_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$dsp_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$dsp_group->flagtype' align='absmiddle' /> "; } echo "<label for='$dsp_group->gr_id'>".substr($dsp_group->name,0,30)."</label></li>";
                                }
                                echo "</ul>";
                            ?>
                        </div>
                    </div>
                <?php
					}
				?>
                
                <?php
                	$row_color = 0;
                	if($config['settings']['cart'] and in_array("contr",$installed_addons))
                    {
                ?>
                	<div id="tab4_group" class="group">                	
						<div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['gen_contr_sell']; ?>:<br />
                                <span><?php echo $mgrlang['gen_contr_sell_d']; ?></span>
                            </p>
                            <input type="checkbox" name="contr_sell" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($digital_sp->contr_sell){ echo "checked"; } ?> onclick="$('contr_settings').toggle()" />
                        </div>
						<div id="contr_settings" style="display: <?php if($digital_sp->contr_sell){ echo "blcok"; } else { echo "none"; } ?>;">
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['dsp_f_comtype']; ?>:<br />
									<span><?php echo $mgrlang['dsp_f_comtype_d']; ?></span>
								</p>
								<select style="width: 200px;" id="commission_type" name="commission_type" onchange="commission_type_change();">
									<option value="1" <?php if($digital_sp->commission_type == '1' or $_GET['edit'] == "new"){ echo "selected"; } ?>><?php echo $mgrlang['percentage'] ?></option>
									<option value="2" <?php if($digital_sp->commission_type == '2'){ echo "selected"; } ?>><?php echo $mgrlang['dollar_value']; ?></option>
								</select>
							</div>
							<div class="<?php fs_row_color(); ?>" id="com_dollar" fsrow='1' style="<?php if($digital_sp->commission_type == '1' or $_GET['edit'] == "new"){ echo "display: none;"; } ?>">
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['dsp_f_comval']; ?>:<br />
									<span><?php echo $mgrlang['dsp_f_comval_d']; ?></span>
								</p>
								<input type="text" name="commission_dollar" id="commission_dollar" onblur="update_input_cur('commission_dollar');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($digital_sp->commission_dollar); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
							</div>                      
							<div class="<?php fs_row_color(); ?>" id="com_percentage" fsrow='1' style="<?php if($digital_sp->commission_type == '2'){ echo "display: none;"; } ?>">
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['dsp_f_comlevel']; ?>:<br />
									<span><?php echo $mgrlang['dsp_f_comlevel_d']; ?></span>
								</p>
								<?php
									# SLIDER POSITION
									//$config['settings']['avatar_size']
									$sb_multiplier = (135/100);
									
									$commission = ($_GET['edit'] == 'new') ? 0 : $digital_sp->commission;
									
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
							<?php /*
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['dsp_f_mmprice']; ?>:<br />
									<span><?php echo $mgrlang['dsp_f_mmprice_d']; ?></span>
								</p>
								<div style="float: left;  font-size: 11px;"><strong><?php echo $mgrlang['min']; ?></strong><br /><input type="text" name="min_contr_price" id="min_contr_price" onblur="update_input_cur('min_contr_price');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($digital_sp->min_contr_price); ?>" /></div>
								<div style="float: left;  font-size: 11px; margin-left: 5px;"><strong><?php echo $mgrlang['max']; ?></strong><br /><input type="text" name="max_contr_price" id="max_contr_price" onblur="update_input_cur('max_contr_price');" style="width: 70px;" maxlength="50" value="<?php echo @$cleanvalues->currency_display($digital_sp->max_contr_price); ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?></div>
							</div>
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['dsp_f_mmcredits']; ?>:<br />
									<span><?php echo $mgrlang['dsp_f_mmcredits_d']; ?></span>
								</p>
								<div style="float: left;  font-size: 11px;"><strong><?php echo $mgrlang['min']; ?></strong><br /><input type="text" name="min_contr_credits" id="min_contr_credits" style="width: 70px;" maxlength="50" value="<?php echo $cleanvalues->number_display($digital_sp->min_contr_credits); ?>" /></div>
								<div style="float: left;  font-size: 11px; margin-left: 5px;"><strong><?php echo $mgrlang['max']; ?></strong><br /><input type="text" name="max_contr_credits" id="max_contr_credits" style="width: 70px;" maxlength="50" value="<?php echo $cleanvalues->number_display($digital_sp->max_contr_credits); ?>" /></div>
							</div>
							*/ ?>
						</div>
                	</div>
                <?php
					}						
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
				    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_license']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_license_d']; ?></span>
                        </p>
                        <select name="license" id="license" onchange="update_license();" style="width: 298px;">
                            <?php
								$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses");
								while($license = mysqli_fetch_assoc($licenseResult))
								{
									echo "<option ' value='{$license['lic_purchase_type']}-{$license['license_id']}' ";
									if($digital_sp->license == $license['license_id']) echo "selected='selected'";
									echo ">{$license[lic_name]}</option>";
								}
							?>
                   		</select>
                   	</div>
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                    <div class="<?php fs_row_color(); ?>" id="pricespan" fsrow='1' style="<?php if($dpLicense['lic_purchase_type'] == 'cu' or $dpLicense['lic_purchase_type'] == 'fr'){ echo "display: none;"; } ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_price']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_price_d']; ?></span>
                        </p>
                        <?php
                        	if($config['settings']['flexpricing'] == 1){
						?>
                        <select id="price_calc" name="price_calc" style="width: 150px;">
							<option value="norm" <?php if(@$digital_sp->price_calc == 'norm'){ echo "selected"; } ?>><?php echo $config['settings']['cur_denotation']; ?></option>
							<option value="add" <?php if(@$digital_sp->price_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> (+)</option>
							<option value="sub" <?php if(@$digital_sp->price_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> (-)</option>
							<option value="mult" <?php if(@$digital_sp->price_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_price']; ?> (x)</option>
						</select>
                        <?php
							} else {
								echo "<input type='hidden' name='price_calc' value='norm' />";
							}
						?>
                        <input type="text" name="price" id="price" style="width: 90px;" maxlength="50" onblur="update_input_cur('price');" value="<?php if($digital_sp->price > 0){ echo @$cleanvalues->currency_display($digital_sp->price); } ?>" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                        <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
                    </div>
					<?php
						/*
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="tax_div" style="<?php if(@$digital_sp->license == 'cu' or $digital_sp->license == 'fr'){ echo "display: none;"; } ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_taxable']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_taxable_d']; ?></span>
                        </p>
                        <input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($digital_sp->taxable){ echo "checked"; } ?> />
                   	</div> 
						*/
					?>
                    <?php
						}
						
						//echo $config['settings']['cart']."<br>";
						//echo $config['settings']['credits_digital']."<br>";
						
						if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital'])
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="creditsspan" fsrow='1' style="<?php if($dpLicense['lic_purchase_type'] == 'cu' or $dpLicense['lic_purchase_type'] == 'fr'){ echo "display: none;"; } ?>">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['dsp_f_credits']; ?>:<br />
                                <span><?php echo $mgrlang['dsp_f_credits_d']; ?></span>
                            </p>
                            <?php
								if($config['settings']['flexpricing'] == 1){
							?>
                            <select id="credits_calc" name="credits_calc" style="width: 150px;">
                                <option value="norm" <?php if(@$digital_sp->credits_calc == 'norm'){ echo "selected"; } ?>></option>
                                <option value="add" <?php if(@$digital_sp->credits_calc == 'add'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> (+)</option>
                                <option value="sub" <?php if(@$digital_sp->credits_calc == 'sub'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> (-)</option>
                                <option value="mult" <?php if(@$digital_sp->credits_calc == 'mult'){ echo "selected"; } ?>><?php echo $mgrlang['gen_base_credits']; ?> (x)</option>
                            </select>
                            <?php
								} else {
									echo "<input type='hidden' name='credits_calc' value='norm' />";
								}
							?>
                            <input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($digital_sp->credits); ?>" />
                            <br /><span style="font-size: 10px; color: #999;"><?php echo $mgrlang['gen_leave_blank_mes']; ?>: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
                        </div> 
                    <?php
						}
					?>                 
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['attachment']; ?>:<br />
                        	<span><?php echo $mgrlang['dsp_f_attach_d']; ?></span>
                        </p>
                        <select name="attachment" id="attachment" onchange="attachment_update();">
                        	<option value="none" <?php if($digital_sp->attachment == 'none'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['gen_none']; ?></option>
                            <option value="media" <?php if($digital_sp->attachment == 'media'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['attachment_mwsg']; ?></option>
                        </select>
                    </div> 
                    <div class="<?php fs_row_color(); ?>" id="gals_div" style="display: <?php if($digital_sp->attachment == 'none' or $_GET['edit'] == 'new'){ echo "none"; } else { echo "block"; } ?>">
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
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.digital.sp.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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