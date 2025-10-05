<?php
	###################################################################
	####	MANAGER TAXES PAGE                                     ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 12-15-2009                                   #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE		
			
		$page = "taxes";
		$lnav = "settings";
		
		$supportPageID = '378';
	
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
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		
		# GET THE ACTIVE LANGUAGES
		//$active_langs = explode(",",$config['settings']['lang_file_pub']);
		//$active_langs[] = $config['settings']['lang_file_mgr'];
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		# ACTIONS HERE		
		if($_POST){			
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			/*
			# MAKE SURE THE VALUE IS NUMBERIC AND IS BETWEEN 1 AND 99
			if(empty($_POST['tax_default']) or $_POST['tax_default'] < 1 or $_POST['tax_default'] > 99){
				$tax_default = 0;
			} else {
				$tax_default = $_POST['tax_default'];
			}
			*/
			
			//echo $tax_stm_dutch; exit;
			
			//$tax_a_default = $_POST['tax_a_default']*1;
			//$tax_b_default = $_POST['tax_b_default']*1; 
			//$tax_c_default = $_POST['tax_c_default']*1; 
			
			$tax_a_default = $cleanvalues->number_clean($tax_a_default);
			$tax_b_default = $cleanvalues->number_clean($tax_b_default);
			$tax_c_default = $cleanvalues->number_clean($tax_c_default);
			
			$tax_a_digital = $cleanvalues->number_clean($tax_a_digital);
			$tax_b_digital = $cleanvalues->number_clean($tax_b_digital);
			$tax_c_digital = $cleanvalues->number_clean($tax_c_digital);
			
			
			# ADD SUPPORT FOR ADDITIONAL LANGUAGES
			foreach($active_langs as $value){ 
				$taxa_name_val 	= ${"taxa_name_" . $value};
				$taxb_name_val 	= ${"taxb_name_" . $value};
				$taxc_name_val 	= ${"taxc_name_" . $value};
				$tax_stm_val 	= ${"tax_stm_" . $value};
				$addsql.= "taxa_name_$value='$taxa_name_val',";
				$addsql.= "taxb_name_$value='$taxb_name_val',";
				$addsql.= "taxc_name_$value='$taxc_name_val',";
				$addsql.= "tax_stm_$value='$tax_stm_val',";
			}
			
			//var_dump($tax_a_default); exit; // Testing
			
			# UPDATE THE SETTINGS DATABASE
			$sql = "UPDATE {$dbinfo[pre]}settings SET
						taxa_name='$taxa_name',
						taxb_name='$taxb_name',
						taxc_name='$taxc_name',
						tax_a_default='{$tax_a_default}',
						tax_b_default='{$tax_b_default}',
						tax_c_default='{$tax_c_default}',
						tax_a_digital='{$tax_a_digital}',
						tax_b_digital='{$tax_b_digital}',
						tax_c_digital='{$tax_c_digital}',";
			$sql.= $addsql;			
			$sql.= "tax_inc='$_POST[tax_inc]',
					tax_optout='$_POST[tax_optout]',
					tax_stm='$tax_stm',
					tax_ms='$tax_ms',
					tax_prints='$tax_prints',
					tax_digital='$tax_digital',
					tax_subs='$tax_subs',
					tax_shipping='$tax_shipping',
					tax_credits='$tax_credits',
					tax_type='$_POST[tax_type]'
					where settings_id  = '1'";
			
			//echo $sql; exit;
			
			$result = mysqli_query($db,$sql);			
			
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_taxes'],1,"<strong>".$mgrlang['gen_b_sav']."</strong>");
			
			header("location: mgr.taxes.php?ep=1&mes=saved");
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_taxes']; ?></title>
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
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<script language="javascript">
		function form_submitter(){
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = "mgr.taxes.php";
			?>
				// CHECK TO MAKE SURE ONLY NUMBERS ARE ENTERED
				//if(!IsNumeric($('tax_default').value)){
				//	simple_message_box('<?php echo $mgrlang['tax_mes_05']; ?>');
				//	bringtofront('1');
				//	return false;
				//}
				// CHECK TO MAKE SURE THE VALUE IS LESS THAN 100
				//if($('tax_default').value >= 100){
				//	simple_message_box('<?php echo $mgrlang['tax_mes_06']; ?>');
				//	bringtofront('1');
				//	return false;
				//}
				// CHECK TO MAKE SURE THE VALUE IS GREATER THAN 1
				//if($('tax_default').value < 1 && $('tax_default').value != 0){
				//	simple_message_box('<?php echo $mgrlang['tax_mes_07']; ?>');
				//	bringtofront('1');
				//	return false;
				//}
				// SELECT THE CHANGED PROFILES BEFORE SUBMITTING
				//var listlen = $('regions').length;
				//for(var x=0; x < listlen; x++){	
				//	$('regions').options[x].value = $('regions').options[x].value + "," + $('regions').options[x].tax;
				//	$('regions').options[x].selected = true;
					
					//if($('regions').options[x].tax != ""){
						//$('regions').options[x].selected = true;
					//} else {
					//	$('regions').options[x].selected = false;
					//}
				//}		
				// SUBMIT THE FORM
				$('data_form').action = "<?php echo $action_link; ?>";
				$('data_form').submit();
			<?php
				}
			?>
		}
		
		function taxtype_boxes(taxtype)
		{
			if(taxtype == 'global')
			{
				show_div('tax_global');
				hide_div('tax_regional');
				$('tax_global_b').className = 'subsubon';
				$('tax_regional_b').className = 'subsuboff';
			}
			else
			{
				hide_div('tax_global');
				show_div('tax_regional');
				$('tax_global_b').className = 'subsuboff';
				$('tax_regional_b').className = 'subsubon';
			}
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
				
				$('taxa_name').observe('change', function()
						{
							$('taxa_text').update('&nbsp;( ' + $F('taxa_name') + ' )');
						});
				$('taxb_name').observe('change', function()
						{
							$('taxb_text').update('&nbsp;( ' + $F('taxb_name') + ' )');
						});
				$('taxc_name').observe('change', function()
						{
							$('taxc_text').update('&nbsp;( ' + $F('taxc_name') + ' )');
						});
			});
		
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
                # OUTPUT MESSAGE IF ONE EXISTS
                verify_message($vmessage);
            ?>
            <form name="data_form" id="data_form" method="post" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.taxes.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_taxes']; ?></strong><br /><span><?php echo $mgrlang['subnav_taxes_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
    
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['tax_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['tax_tab2']; ?></div>
                   <!-- <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['tax_tab3']; ?></div>-->
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tax_f_taxa']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxa_d']; ?></span></p>                        
                        <div class="additional_langs">
                            <input type="text" name="taxa_name" id="taxa_name" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxa_name']; ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_taxa_name','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_taxa_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="taxa_name_<?php echo $value; ?>" id="taxa_name_<?php echo $value; ?>" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxa_name_' . $value]; ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tax_f_taxb']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxb_d']; ?></span></p>
                        <div class="additional_langs">
                            <input type="text" name="taxb_name" id="taxb_name" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxb_name']; ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_taxb_name','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_taxb_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="taxb_name_<?php echo $value; ?>" id="taxb_name_<?php echo $value; ?>" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxb_name_' . $value]; ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tax_f_taxc']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxc_d']; ?></span></p>
                        <div class="additional_langs">
                            <input type="text" name="taxc_name" id="taxc_name" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxc_name']; ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_taxc_name','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_taxc_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="taxc_name_<?php echo $value; ?>" id="taxc_name_<?php echo $value; ?>" style="width: 200px;" maxlength="100" value="<?php echo @$config['settings']['taxc_name_' . $value]; ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tax_f_stm']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_stm_d']; ?></span></p>
                        
                        <div class="additional_langs">
                            <textarea name="tax_stm" style="width: 300px; height: 75px; vertical-align:middle"><?php echo @$config['settings']['tax_stm']; ?></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('tax_stm_name','','','','plusminus-04');"><img src="images/mgr.plusminus.0.png" id="plusminus04" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="tax_stm_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="tax_stm_<?php echo $value; ?>" id="tax_stm_<?php echo $value; ?>" style="width: 300px; height: 75px; vertical-align:middle"><?php echo @$config['settings']['tax_stm_' . $value]; ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>" style="padding-bottom: 20px;">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');" style="height: 90px;"><?php echo $mgrlang['tax_f_taxtype']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxtype_d']; ?></span></p>
                        
                        <div style="overflow: auto; position: relative">
                        	<div class="<?php if($config['settings']['tax_type'] == 1){ echo "subsubon"; } else { echo "subsuboff"; } ?>" id="tax_global_b" style="border-left: 1px solid #d8d7d7"><input type="radio" name="tax_type" value="1" id="tax_global_option" onclick="taxtype_boxes('global')" <?php if($config['settings']['tax_type'] == 1){ echo "checked"; } ?> /> <label for="tax_global_option"><?php echo $mgrlang['tax_globally']; ?></label></div>
                            <div class="<?php if($config['settings']['tax_type'] == 0){ echo "subsubon"; } else { echo "subsuboff"; } ?>" id="tax_regional_b" style="border-right: 1px solid #d8d7d7;"><input type="radio" name="tax_type" value="0" id="tax_regional_option" onclick="taxtype_boxes('regional')" <?php if($config['settings']['tax_type'] == 0){ echo "checked"; } ?> /> <label for="tax_regional_option"><?php echo $mgrlang['tax_by_region']; ?></label></div>
                        </div>
                        
                        <div class="more_options" style="padding: 5px 5px 5px 15px; <?php if($config['settings']['tax_type'] == 0){ echo "display: none"; } ?>" id="tax_global">
                            <div class="fs_row_off">
                                <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                    <?php echo $mgrlang['tax_f_inc']; ?>:<br />
                                    <span><?php echo $mgrlang['tax_f_inc_d']; ?></span>
                                </p>
                                <input type="checkbox" value="1" name="tax_inc" <?php if($config['settings']['tax_inc']){ echo "checked"; } ?> />
                            </div>
							<hr />
                            <div style="padding-top: 10px;">
								<strong><?php echo $mgrlang['tax_pit']; ?></strong> <span style="font-style:italic; color: #999"><?php echo $mgrlang['tax_pit_d']; ?></span>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxa_default']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxa_default_d']; ?></span>
									</p>
									<input type="text" name="tax_a_default" id="tax_a_default" style="width: 50px;" onblur="update_input_num('tax_a_default',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_a_default'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxb_default']; ?>: <span id="taxb_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxb_default_d']; ?></span>
									</p>
									<input type="text" name="tax_b_default" id="tax_b_default" style="width: 50px;" onblur="update_input_num('tax_b_default',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_b_default'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxc_default']; ?>: <span id="taxc_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxc_default_d']; ?></span>
									</p>
									<input type="text" name="tax_c_default" id="tax_c_default" style="width: 50px;" onblur="update_input_num('tax_c_default',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_c_default'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['zipcodes_f_tax_prints']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxprints_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_prints" id="tax_prints" value="1" <?php if(@!empty($config['settings']['tax_prints'])){ echo "checked"; } ?> />
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['zipcodes_f_tax_shipping']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxshipping_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_shipping" id="tax_shipping" value="1" <?php if(@!empty($config['settings']['tax_shipping'])){ echo "checked"; } ?> />
								</div>
							</div>
							<hr />
							<div style="padding-top: 10px;">
								<strong><?php echo $mgrlang['tax_dit']; ?></strong> <span style="font-style:italic; color: #999"><?php echo $mgrlang['tax_dit_d']; ?></span>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxa_default']; ?>: <span id="taxa_text">&nbsp;<?php if($config['settings']['taxa_name']){ echo "( " . @$config['settings']['taxa_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxa_default_d']; ?></span>
									</p>
									<input type="text" name="tax_a_digital" id="tax_a_digital" style="width: 50px;" onblur="update_input_num('tax_a_digital',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_a_digital'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxb_default']; ?>: <span id="taxb_text">&nbsp;<?php if($config['settings']['taxb_name']){ echo "( " . @$config['settings']['taxb_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxb_default_d']; ?></span>
									</p>
									<input type="text" name="tax_b_digital" id="tax_b_digital" style="width: 50px;" onblur="update_input_num('tax_b_digital',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_b_digital'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxc_default']; ?>: <span id="taxc_text">&nbsp;<?php if($config['settings']['taxc_name']){ echo "( " . @$config['settings']['taxc_name'] . " )"; } ?></span><br />
										<span><?php echo $mgrlang['tax_f_taxc_default_d']; ?></span>
									</p>
									<input type="text" name="tax_c_digital" id="tax_c_digital" style="width: 50px;" onblur="update_input_num('tax_c_digital',3,1);" value="<?php echo @$cleanvalues->number_display($config['settings']['tax_c_digital'],3); ?>" /> %
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['tax_f_taxmem']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxms_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_ms" id="tax_ms" value="1" <?php if(@!empty($config['settings']['tax_ms'])){ echo "checked"; } ?> />
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['zipcodes_f_tax_digital']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxdigital_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_digital" id="tax_digital" value="1" <?php if(@!empty($config['settings']['tax_digital'])){ echo "checked"; } ?> />
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['zipcodes_f_tax_subs']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxsubs_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_subs" id="tax_subs" value="1" <?php if(@!empty($config['settings']['tax_subs'])){ echo "checked"; } ?> />
								</div>
								<div class="fs_row_off">
									<p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
										<?php echo $mgrlang['zipcodes_f_tax_credits']; ?>: <br />
										<span class="input_label_subtext"><?php echo $mgrlang['tax_f_taxcredits_d']; ?></span>
									</p>
									<input type="checkbox" name="tax_credits" id="tax_credits" value="1" <?php if(@!empty($config['settings']['tax_credits'])){ echo "checked"; } ?> />
								</div>
							</div>
							
                        </div>
                        
                        <div class="more_options" style="background-position:top;<?php if($config['settings']['tax_type'] == 1){ echo "display: none"; } ?>" id="tax_regional">
                        	<?php
								echo $mgrlang['tax_f_tbr_d'];
                            	if($config['settings']['tax_type'] == 0)
								{
							?>
                            	<br />
                                <br />
                                <?php if(in_array('countries',$_SESSION['admin_user']['permissions'])){ ?><a href="mgr.countries.php?ep=1"><?php echo $mgrlang['subnav_countries']; ?></a><br /><?php } ?>
                                <?php if(in_array('states',$_SESSION['admin_user']['permissions'])){ ?><a href="mgr.states.php?ep=1"><?php echo $mgrlang['subnav_states']; ?></a><br /><?php } ?>
                                <?php if(in_array('zipcodes',$_SESSION['admin_user']['permissions'])){ ?><a href="mgr.zipcodes.php?ep=1"><?php echo $mgrlang['subnav_zipcodes']; ?></a><?php } ?>
                            <?php
								}
							?>
						</div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['tax_f_optout']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['tax_f_optout_d']; ?></span></p>
                        <input type="checkbox" name="tax_optout" value="1" <?php if($config['settings']['tax_optout']){ echo "checked"; } ?> />
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
		<?php include("mgr.footer.php"); ?>		
	</div>		
</body>
</html>
<?php mysqli_close($db); ?>
