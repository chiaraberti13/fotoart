<?php
	###################################################################
	####	CURRENCIES EDIT AREA                                   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-4-2010                                      ####
	####	Modified: 1-4-2010                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "currencies";
		$lnav = "settings";
		
		//header('Content-Type: text/html; charset=iso-8895-1');
		
		$supportPageID = '383';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php"))
		{			
			require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
		}
		else
		{ 											
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
		
		require_once('../assets/classes/exchange.rates.php');			# INCLUDE CURRENCY EXCHANGE RATE CLASS
		require_once('mgr.defaultcur.php');								# INCLUDE DEFAULT CURRENCY SETTINGS	
		
		$quote = new CurrencyConvert('Google');
		$currency_list = $quote->currencies();
		
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$currency_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}currencies WHERE currency_id = '$_GET[edit]'");
			$currency_rows = mysqli_num_rows($currency_result);
			$currency = mysqli_fetch_object($currency_result);
		}
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		//$cleanvalues->decimal_places = 9;
		//$cleanvalues->strip_ezeros = 1;
		
		if($_REQUEST['action'])
		{
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			/*
			if($thousands_separator == 'none')
				$thousands_separator = '';
				
			if($thousands_separator == 'space')
				$thousands_separator = ' ';
			*/
		}
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SAVE EDIT				
			case "save_edit":				
				
				$fixed_symbol = currency_symbol_to_html($denotation);
				//echo $fixed_symbol; exit;
				
				$exchange_rate = ($exchange_rate) ? $cleanvalues->number_clean($exchange_rate) : 1;
				
				$code = strtoupper($code);
				
				//gmt_date()
				
				# IF THIS IS SET TO DEFAULT THEN CLEAR PREVIOUS DEFAULTS AND SET NEW
				if($defaultcur == '1')
				{
					$active = 1;
					$exchange_rate = 1;
					$sql = "UPDATE {$dbinfo[pre]}currencies SET defaultcur='0'";
					$result = mysqli_query($db,$sql);
				
					$sql = "UPDATE {$dbinfo[pre]}currencies SET defaultcur='1',active='1' WHERE currency_id = '$saveid'";
					$result = mysqli_query($db,$sql);
				}
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$name_val = ${"name_" . $value};
					$addsql.= "name_$value='$name_val',";
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}currencies SET 
							name='$name',
							code='$code',
							denotation='$fixed_symbol',";
				$sql.= $addsql;				
				$sql.= "	active='$active',
							defaultcur='$defaultcur',
							decimal_separator='$decimal_separator',
							thousands_separator='$thousands_separator',
							decimal_places='$decimal_places',
							neg_num_format='$neg_num_format',
							pos_num_format='$pos_num_format',
							exchange_rate='$exchange_rate',
							exchange_date='".gmt_date()."',
							exchange_updater='$exchange_updater',
							exchange_autoupdate='$exchange_autoupdate'
							WHERE currency_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_currencies'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.currencies.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":			
				
				$fixed_symbol = currency_symbol_to_html($denotation);
				
				$exchange_rate = ($exchange_rate) ? $cleanvalues->number_clean($exchange_rate) : 1;
				
				$code = strtoupper($code);
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$name_val = ${"name_" . $value};
					$addsqla.= ",name_$value";
					$addsqlb.= ",'$name_val'";
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}currencies (
						name,
						code,
						active,
						defaultcur,
						denotation,
						decimal_separator,
						thousands_separator,
						decimal_places,
						neg_num_format,
						pos_num_format,
						exchange_rate,
						exchange_date,
						exchange_updater,
						exchange_autoupdate";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$code',
						'$active',
						'$defaultcur',
						'$fixed_symbol',
						'$decimal_separator',
						'$thousands_separator',
						'$decimal_places',
						'$neg_num_format',
						'$pos_num_format',
						'$exchange_rate',
						'".gmt_date()."',
						'$exchange_updater',
						'$exchange_autoupdate'
						";
				$sql.= $addsqlb;
				$sql.= ")";
				
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);

				# IF THIS IS SET TO DEFAULT THEN CLEAR PREVIOUS DEFAULTS
				if($defaultcur == '1')
				{
					$active = 1;
					$exchange_rate = 1;
					$sql = "UPDATE {$dbinfo[pre]}currencies SET defaultcur='0' WHERE currency_id != '$saveid'";
					$result = mysqli_query($db,$sql);
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_currencies'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.currencies.php?mes=new"); exit;
			break;		
		}
		
		# FIND PRIMARY CURRENCY INFO
		$pricur_result = mysqli_query($db,"SELECT code,decimal_places FROM {$dbinfo[pre]}currencies WHERE defaultcur = '1'");
		$pricur_rows = mysqli_num_rows($pricur_result);
		$pricur = mysqli_fetch_object($pricur_result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_countries_currencies']; ?></title>
	<!-- LOAD THE STYLE SHEETS -->
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
	<script language="javascript">
		function form_submitter(){
			$('name_div').className='fs_row_on';
			$('code_div').className='fs_row_off';
			$('denotation_div').className='fs_row_on';	
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.currencies.edit.php?action=save_new" : "mgr.currencies.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","currencies_f_name",1);
					js_validate_field("code","currencies_f_code",1);
					js_validate_field("denotation","currencies_f_den",1);
				}
			?>
		}
		Event.observe(window, 'load', function()
			{
				// UPDATE THE NUMBER PREVIEW WINDOW
				update_number_preview();
				
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
		
		// UPDATE NUMBER PREVIEW
		function update_number_preview()
		{
			// ALSO UPDATE SELECTS
			update_selects();
			update_exchange_preview();
			
			var denotation = $F('denotation');
			
			// 1234568.00
			var my_number = '12';			
			
			var thousands_separator = $('thousands_separator').options[$('thousands_separator').selectedIndex].value;
			if(thousands_separator == 'none')
			{
				thousands_separator = "";
			}
			else if(thousands_separator == "space")
			{
				thousands_separator = " ";
			}
			my_number = my_number + thousands_separator + '345';
			my_number = my_number + thousands_separator + '678';
			
			var decimal_separator = $('decimal_separator').options[$('decimal_separator').selectedIndex].value;
			
			var decimal_places = $('decimal_places').options[$('decimal_places').selectedIndex].value;
			var decimal = "";
			for(var y=0;y<decimal_places;y++)
			{
				decimal = decimal + "0";	
			}
			
			if(decimal_places != "0")
			{
				my_number = my_number + decimal_separator + decimal;
			}			
			
			// NEGATIVE NUMBER
			var neg_num_format = $('neg_num_format').options[$('neg_num_format').selectedIndex].value;
			var neg_number;
			switch(neg_num_format)
			{
				case "1":
					neg_number = "(" + denotation + my_number + ")";
				break;
				case "2":
					neg_number = "(" + denotation + ' ' + my_number + ")";
				break
				case "3":
					neg_number = "(" + my_number + denotation + ")";
				break;
				case "4":
					neg_number = "(" + my_number + " " + denotation + ")";
				break;
				case "5":
					neg_number = denotation + '-' + my_number;
				break;
				case "6":
					neg_number = denotation + ' -' + my_number;
				break;
				case "7":
					neg_number = "-" + denotation + my_number;
				break;
				case "8":
					neg_number = "- " + denotation + my_number;
				break;
				case "9":
					neg_number = "-" + denotation + ' ' + my_number;
				break;
				case "10":
					neg_number = "- " + denotation + ' ' + my_number;
				break;
				case "11":
					neg_number = "-" + my_number + denotation;
				break;
				case "12":
					neg_number = "-" + ' ' + my_number + denotation;
				break;
				case "13":
					neg_number = "-" + my_number + " " + denotation;
				break;
				case "14":
					neg_number = "- " + my_number + " " + denotation;
				break;
			}
			
			// NEGATIVE NUMBER
			var pos_num_format = $('pos_num_format').options[$('pos_num_format').selectedIndex].value;
			var pos_number;
			switch(pos_num_format)
			{
				case "1":
					pos_number = denotation + my_number;
				break;
				case "2":
					pos_number = denotation + " " + my_number;
				break
				case "3":
					pos_number = my_number +  denotation;
				break;
				case "4":
					pos_number = my_number +  " " + denotation;
				break;
			}
			
			$('number_preview').update("<?php echo $mgrlang['currencies_f_num_pos']; ?>: " + pos_number + "<br /><?php echo $mgrlang['currencies_f_num_neg']; ?>: " + neg_number);
		}
		
		// UPDATE SELECT DROPDOWNS
		function update_selects()
		{
			//var pos_num_format = $('pos_num_format').options[$('pos_num_format').selectedIndex].original;
			
			var denotation = $F('denotation');
			var decimal_places = $('decimal_places').options[$('decimal_places').selectedIndex].value;
			var decimal = "";
			for(var y=0;y<decimal_places;y++)
			{
				decimal = decimal + "0";	
			}
			if(decimal_places != "0")
			{
				var decimal_separator = $('decimal_separator').options[$('decimal_separator').selectedIndex].value;
			}
			else
			{
				var decimal_separator = '';
			}
			
			$('pos_num_format').options[0].update(denotation + '25' + decimal_separator + decimal);
			$('pos_num_format').options[1].update(denotation + ' 25' + decimal_separator + decimal);
			$('pos_num_format').options[2].update('25' + decimal_separator + decimal + denotation);
			$('pos_num_format').options[3].update('25' + decimal_separator + decimal + " " + denotation);
			
			$('neg_num_format').options[0].update("(" + denotation + '25' + decimal_separator + decimal + ")");
			$('neg_num_format').options[1].update("(" + denotation + ' 25' + decimal_separator + decimal + ")");
			$('neg_num_format').options[2].update("(" + '25' + decimal_separator + decimal + denotation + ")");
			$('neg_num_format').options[3].update("(" + '25' + decimal_separator + decimal + " " + denotation + ")");
			$('neg_num_format').options[4].update(denotation + '-25' + decimal_separator + decimal);
			$('neg_num_format').options[5].update(denotation + ' -25' + decimal_separator + decimal);
			$('neg_num_format').options[6].update("-" + denotation + '25' + decimal_separator + decimal);
			$('neg_num_format').options[7].update("- " + denotation + '25' + decimal_separator + decimal);
			$('neg_num_format').options[8].update("-" + denotation + ' 25' + decimal_separator + decimal);
			$('neg_num_format').options[9].update("- " + denotation + ' 25' + decimal_separator + decimal);
			$('neg_num_format').options[10].update("-" + '25' + decimal_separator + decimal + denotation);
			$('neg_num_format').options[11].update("-" + ' 25' + decimal_separator + decimal + denotation);
			$('neg_num_format').options[12].update("-" + '25' + decimal_separator + decimal + " " + denotation);
			$('neg_num_format').options[13].update("- " + '25' + decimal_separator + decimal + " " + denotation);
		}
		
		// UPDATE EXCHANGE PREVIEW
		function update_exchange_preview(){
			if($F('code') == "")
			{
				var current_code = '?';
			}
			else
			{
				var current_code = $F('code');
			}
			
			current_code = current_code.toUpperCase();
		
			// MAKE SURE THE CURRENCIES ARE SUPPORTED FOR EXCHANGE RATE FIRST
			if(!exchange_rate_support.inArray('<?php echo $pricur->code; ?>') || !exchange_rate_support.inArray(current_code))
			{
				$('exchange_buttons').hide();
				$('exchange_updater_div').hide();
				$('automatic_updater_div').hide();
			}
			else
			{
				$('exchange_buttons').show();
				$('exchange_updater_div').show();
				$('automatic_updater_div').show();
			}
			
			if($('exchange_rate') != null)
			{
				if($F('exchange_rate') != '')
				{
					var primary_code = '<?php echo $pricur->code; ?>';
					//var decimal_separator = $('decimal_separator').options[$('decimal_separator').selectedIndex].value;
					
					$('code_preview').update(current_code);
					
					numset.decimal_places = <?php echo $config['settings']['decimal_places']; ?>;
					
					var exchange_rate_fixed = number_clean($F('exchange_rate'));
					//alert(exchange_rate_fixed);
					
					var val1 = (100 * exchange_rate_fixed);
					var val2 = (100/exchange_rate_fixed);
					
					var new_text = '100 ' + primary_code + " = " + currency_display(val2) + " " + current_code;
					new_text += '<br />';
					new_text += '100 ' + current_code + " = " + currency_display(val1) + " " + primary_code;
					
					$('exchange_preview').update(new_text);
				}
				else
				{
					// DO NOTHING
				}
			}
		}
		
		// CLEAN THE EXCHANGE BOX ON BLUR
		function cleanexchangebox()
		{
			if(!$F('exchange_rate'))
			{
				$('exchange_rate').setValue('1');
			}
			// FIX FIELD FIRST
			update_input_num('exchange_rate',4,0);
			update_exchange_preview();
		}
		
		// IF DEFAULT GETS CHECKED
		function defaultcur_check()
		{
			if($('defaultcur').checked)
			{
				$('active').checked = 1;
				if($('exchange_rate') != null)
				{
					$('tab2').hide();
					$('exchange_rate').disable();
					$('exchange_rate').setValue('1');
				}
			}
			else
			{
				if($('exchange_rate') != null)
				{
					$('tab2').show();
					$('exchange_rate').enable();
				}
			}
		}
		
		// GRAB SUPPORTED CURRENCY CODES FOR EXCHANGE RATES
		var exchange_rate_support = new Array();
		<?php
			$x=0;
			foreach($currency_list as $code => $name)
			{	
				echo "exchange_rate_support[$x] = '" . $code . "';\n";
				$x++;
			}
		?>
		
		// GRAB THE CURRENT EXCHANGE RATE
		function grab_cur_rate(source)
		{
			var from = $F('code');
			var to = '<?php echo $pricur->code; ?>';
			
			//$('exchange_rate').setValue('Fetching...');
			$(source + '_button').setValue('<?php echo $mgrlang['currencies_f_er_fetching']; ?>...');
			$(source + '_button').disable();
			
			// MAKE SURE THE CURRENCIES ARE SUPPORTED FOR EXCHANGE RATE FIRST
			if(exchange_rate_support.inArray(from) && exchange_rate_support.inArray(to))
			{
				var loadpage = "mgr.currencies.actions.php";
				var myAjax = new Ajax.Request(
											  loadpage,
												{
												  method: 'get',
												  parameters: {action: 'grabcur', from: from, to: to, source: source},
												  evalScripts: true,
												  onSuccess: update_rate_box 
												});
			}
			else
			{
				rate_failure('2');	
			}
		}
		
		// UPDATE THE RATE BOX OR PROVIDE ERROR
		function update_rate_box(returnedvalue)
		{
			var values = returnedvalue.responseText.split("|");
		  	if(values[0] == '1' && values[1] != '')
			{
				$('exchange_rate').setValue(number_display(values[1],4,1));
			}
			else
			{
				rate_failure('1');
			}
			update_exchange_preview();
			$('Google_button').enable();
			$('Yahoo_button').enable();
			$('Google_button').setValue('<?php echo $mgrlang['currencies_f_er_ffg']; ?>');
			$('Yahoo_button').setValue('<?php echo $mgrlang['currencies_f_er_ffy']; ?>');
		}
		
		// FAILURE ERRORS
		function rate_failure(error)
		{
			if(error == '1')
			{
				simple_message_box('<?php echo $mgrlang['currencies_error2']; ?>','');
			}
			else
			{
				simple_message_box('<?php echo $mgrlang['currencies_error1']; ?>','');
			}
		}
	</script>
</head>
<body>
	<?php echo $browser; ?>
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
                <img src="./images/mgr.badge.currencies.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['currencies_new_header'] : $mgrlang['currencies_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['currencies_new_message'] : $mgrlang['currencies_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div style="display: none;" id="hidden_box"></div>
            <?php if(in_array('multicur',$installed_addons) and $currency->defaultcur != '1'){ ?>
            <div id="spacer_bar"></div>    
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['currencies_tab1']; ?></div>
                <div class="subsuboff" onclick="bringtofront('2');update_number_preview();" id="tab2" style="border-right: 1px solid #d8d7d7;<?php if($currency->defaultcur == '1'){ echo "display: none"; } ?>"><?php echo $mgrlang['currencies_tab2']; ?></div>
            </div>
            <?php } ?>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">
                <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7;">                        
                    <img src="images/mgr.icon.numbers.png" align="left" style="margin-right: 10px; margin-left: 10px;" />
                    <div style="padding-top: 1px;"><?php echo $mgrlang['currencies_f_num_preview']; ?>:</div>
                    <div id="number_preview" style="font-weight: bold;">
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" id="name_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['currencies_f_name']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_name_d']; ?></span>
                    </p>
                    <div class="additional_langs">
                        <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($currency->name); ?>" />
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_title" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($currency->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                
                <div class="<?php fs_row_color(); ?>" id="code_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_code']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_code_d']; ?></span>
                    </p>
                    <input type="text" name="code" id="code" style="width: 50px;" maxlength="100" value="<?php echo @stripslashes($currency->code); ?>" /> (<a href="http://en.wikipedia.org/wiki/Currency_code" target="_blank">Wikipedia</a>)                   
                </div>
                <div class="<?php fs_row_color(); ?>" id="denotation_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_den']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_den_d']; ?></span>
                    </p>
                    <input type="text" name="denotation" id="denotation" style="width: 50px;" maxlength="100" value="<?php echo @currency_html_to_symbol(stripslashes($currency->denotation)); ?>" onblur="update_number_preview();" />                   
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_dec_sep']; ?>: <br />
                        <span><?php echo $mgrlang['currencies_f_dec_sep_d']; ?></span>
                    </p>
                    <select name="decimal_separator" id="decimal_separator" style="width: 100px;" onchange="update_number_preview();">
                        <option <?php if($currency->decimal_separator == "." or $_GET['edit'] == 'new'){ echo "selected"; } ?> value="."><?php echo $mgrlang['gen_chr_period']; ?> ( . )</option>
                        <option <?php if($currency->decimal_separator == ","){ echo "selected"; } ?> value=","><?php echo $mgrlang['gen_chr_comma']; ?> ( , )</option>
                        <option <?php if($currency->decimal_separator == "-"){ echo "selected"; } ?> value="-"><?php echo $mgrlang['gen_chr_dash']; ?> ( - )</option>
                        <option <?php if($currency->decimal_separator == "="){ echo "selected"; } ?> value="="><?php echo $mgrlang['gen_chr_equals']; ?> ( = )</option>
                        <option <?php if($currency->decimal_separator == "/"){ echo "selected"; } ?> value="/"><?php echo $mgrlang['gen_chr_slash']; ?> ( / )</option>
                        <option <?php if($currency->decimal_separator == ";"){ echo "selected"; } ?> value=";"><?php echo $mgrlang['gen_chr_semicolon']; ?> ( ; )</option>
                        <option <?php if($currency->decimal_separator == ":"){ echo "selected"; } ?> value=":"><?php echo $mgrlang['gen_chr_colon']; ?> ( : )</option>
                        <option <?php if($currency->decimal_separator == "'"){ echo "selected"; } ?> value="'"><?php echo $mgrlang['gen_chr_apostrophe']; ?> ( ' )</option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_thou_sep']; ?>: <br />
                        <span><?php echo $mgrlang['currencies_f_thou_sep_d']; ?></span>
                    </p>
                    <select name="thousands_separator" id="thousands_separator" style="width: 100px;" onchange="update_number_preview();">
                        <option <?php if($currency->thousands_separator == "no"){ echo "selected"; } ?> value="none"><?php echo $mgrlang['setup_f_thou_sep_none']; ?></option>
                        <option <?php if($currency->thousands_separator == "," or $_GET['edit'] == 'new'){ echo "selected"; } ?> value=","><?php echo $mgrlang['gen_chr_comma']; ?> ( , )</option>
                        <option <?php if($currency->thousands_separator == "sp"){ echo "selected"; } ?> value="space"><?php echo $mgrlang['setup_f_thou_sep_space']; ?></option>
                        <option <?php if($currency->thousands_separator == "."){ echo "selected"; } ?> value="."><?php echo $mgrlang['gen_chr_period']; ?> ( . )</option>
                        <option <?php if($currency->thousands_separator == "'"){ echo "selected"; } ?> value="'"><?php echo $mgrlang['gen_chr_apostrophe']; ?> ( , )</option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_num_after']; ?>: <br />
                        <span><?php echo $mgrlang['currencies_f_num_after_d']; ?></span>
                    </p>
                    <select name="decimal_places" id="decimal_places" style="width: 100px;" onchange="update_number_preview();">
                        <option <?php if($currency->decimal_places == "0"){ echo "selected"; } ?> value="0">0</option>
                        <option <?php if($currency->decimal_places == "1"){ echo "selected"; } ?> value="1">1</option>
                        <option <?php if($currency->decimal_places == "2" or $_GET['edit'] == 'new'){ echo "selected"; } ?> value="2">2</option>
                        <option <?php if($currency->decimal_places == "3"){ echo "selected"; } ?> value="3">3</option>
                        <option <?php if($currency->decimal_places == "4"){ echo "selected"; } ?> value="4">4</option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_pos_format']; ?>: <br />
                        <span><?php echo $mgrlang['currencies_f_pos_format_d']; ?></span>
                    </p>
                    <select name="pos_num_format" id="pos_num_format" style="width: 100px;" onchange="update_number_preview();">
                        <option <?php if($currency->pos_num_format == "1"){ echo "selected"; } ?> value="1" dd="1" original="$25.00">$25.00</option>
                        <option <?php if($currency->pos_num_format == "2"){ echo "selected"; } ?> value="2" dd="1" original="$ 25.00">$ 25.00</option>
                        <option <?php if($currency->pos_num_format == "3"){ echo "selected"; } ?> value="3" dd="1" original="25.00$">25.00$</option>
                        <option <?php if($currency->pos_num_format == "4"){ echo "selected"; } ?> value="4" dd="1" original="25.00 $">25.00 $</option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_neg_format']; ?>: <br />
                        <span><?php echo $mgrlang['currencies_f_neg_format_d']; ?></span>
                    </p>
                    <select name="neg_num_format" id="neg_num_format" style="width: 100px;" onchange="update_number_preview();">
                        <option <?php if($currency->neg_num_format == "1"){ echo "selected"; } ?> value="1">($25.00)</option>
                        <option <?php if($currency->neg_num_format == "2"){ echo "selected"; } ?> value="2">($ 25.00)</option>
                        <option <?php if($currency->neg_num_format == "3"){ echo "selected"; } ?> value="3">(25.00$)</option>
                        <option <?php if($currency->neg_num_format == "4"){ echo "selected"; } ?> value="4">(25.00 $)</option>
                        <option <?php if($currency->neg_num_format == "5"){ echo "selected"; } ?> value="5">$-25.00</option>
                        <option <?php if($currency->neg_num_format == "6"){ echo "selected"; } ?> value="6">$ -25.00</option>
                        <option <?php if($currency->neg_num_format == "7" or $_GET['edit'] == 'new'){ echo "selected"; } ?> value="7">-$25.00</option>
                        <option <?php if($currency->neg_num_format == "8"){ echo "selected"; } ?> value="8">- $25.00</option>
                        <option <?php if($currency->neg_num_format == "9"){ echo "selected"; } ?> value="9">-$ 25.00</option>
                        <option <?php if($currency->neg_num_format == "10"){ echo "selected"; } ?> value="10">- $ 25.00</option>
                        <option <?php if($currency->neg_num_format == "11"){ echo "selected"; } ?> value="11">-25.00$</option>
                        <option <?php if($currency->neg_num_format == "12"){ echo "selected"; } ?> value="12">- 25.00$</option>
                        <option <?php if($currency->neg_num_format == "13"){ echo "selected"; } ?> value="13">-25.00 $</option>
                        <option <?php if($currency->neg_num_format == "14"){ echo "selected"; } ?> value="14">- 25.00 $</option>
                    </select>
                </div>
                <?php
					if(in_array('multicur',$installed_addons))
					{
				?>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['currencies_f_active']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_active_d']; ?></span>
                        </p>
                        <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($currency->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                    </div>
                <?php
					}
				?>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['currencies_f_defaultcur']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['currencies_f_defaultcur_d']; ?></span>
                    </p>
                    <?php
                    	if(@!empty($currency->defaultcur))
						{
					?>
                    	<input type="checkbox" name="defaultcur_fake" id="defaultcur_fake" value="1" checked='checked' disabled='disabled' />
                        <input type="hidden" name="defaultcur" value="1" />
                    <?php
						}
						else
						{
					?>
                    	<input type="checkbox" name="defaultcur" id="defaultcur" value="1" onclick="defaultcur_check();" />
                    <?php	
						}
					?>
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab2_group" class="group"> 
                <div class="<?php fs_row_color(); ?>" style="border-bottom: 2px solid #d7d7d7;">                        
                    <img src="images/mgr.icon.numbers.png" align="left" style="margin-right: 10px; margin-left: 10px;" />
                    <div style="padding-top: 1px;"><?php echo $mgrlang['currencies_f_er_preview']; ?>:</div>
                    <div id="exchange_preview" style="font-weight: bold;">
                    </div>
                </div>
                <div class="<?php fs_row_color(); ?>" id="code_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_er']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_er_d']; ?></span><br /><br />
                    </p>
                    1 <span id="code_preview" style="color: #000;">?</span> = <input type="text" name="exchange_rate" id="exchange_rate" style="width: 50px;" maxlength="100" value="<?php if($_GET['edit'] == 'new'){ echo "1"; } else { if($currency->exchange_rate){ echo @$cleanvalues->number_display($currency->exchange_rate,4,1); }} ?>" onkeyup="update_exchange_preview();" onblur="cleanexchangebox('exchange_rate');" /> <?php echo $pricur->code; ?>  <?php $cleanvalues->example_number_text(0.67,1.342); ?>              
                    <div style="padding-top: 12px;" id="exchange_buttons"><input type="button" value="<?php echo $mgrlang['currencies_f_er_ffg']; ?>" id="Google_button" onclick="grab_cur_rate('Google');" /> <input type="button" value="<?php echo $mgrlang['currencies_f_er_ffy']; ?>" id="Yahoo_button" onclick="grab_cur_rate('Yahoo');" /></div>
                </div>
                <div class="<?php fs_row_color(); ?>" id="code_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_last_update']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_last_update_d']; ?></span>
                    </p>
                    <br /><?php
                    	if($currency->exchange_date == '0000-00-00 00:00:00' or $_GET['edit'] == 'new')
						{
							echo "$mgrlang[currencies_f_never_update]";
						}
						else
						{
							$ndate = new kdate;
							$ndate->distime = 1;
							echo $ndate->showdate($currency->exchange_date);
						}
					?>
                </div>
                <div class="<?php fs_row_color(); ?>" id="exchange_updater_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_er_updater']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_er_updater_d']; ?></span>
                    </p>
                    <select name="exchange_updater" id="exchange_updater" style="margin-top: 10px;">
                        <!--<option <?php if($currency->exchange_updater == "0" or $_GET['edit'] == 'new'){ echo "selected"; } ?> value="0">Do Not Automatically Update</option>-->
                        <option <?php if($currency->exchange_updater == "1" or $_GET['edit'] == 'new'){ echo "selected"; } ?> value="1"><?php echo $mgrlang['currencies_f_er_ffg']; ?></option>
                        <option <?php if($currency->exchange_updater == "2"){ echo "selected"; } ?> value="2"><?php echo $mgrlang['currencies_f_er_ffy']; ?></option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>" id="automatic_updater_div">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['currencies_f_auto_update']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['currencies_f_auto_update_d']; ?></span>
                    </p>
                    <input type="checkbox" name="exchange_autoupdate" id="exchange_autoupdate" value="1" style="margin-top: 20px;" <?php if(@!empty($currency->exchange_autoupdate) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
            </div>
                       
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.currencies.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>