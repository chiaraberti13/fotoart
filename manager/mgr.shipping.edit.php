<?php
	###################################################################
	####	SHIPPING EDIT AREA                                 	   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-7-2006                                      ####
	####	Modified: 1-21-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
		$page = "shipping";
		$lnav = "settings";
		
		$supportPageID = '377';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php"))
		{			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		}
		else
		{ 											
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
		require_once('mgr.defaultcur.php');								# INCLUDE DEFAULT CURRENCY SETTINGS	
		
		
		# CREATE NEW NUMBER FORMATING OBJECT
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults();
		$cleanvalues->set_cur_defaults();
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$shipping_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping WHERE ship_id = '$_GET[edit]'");
			$shipping_rows = mysqli_num_rows($shipping_result);
			$shipping = mysqli_fetch_object($shipping_result);
		}

		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SAVE EDIT				
			case "save_edit":				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# FIX NUMBER VALUES
				//$longitude = $cleanvalues->number_clean($longitude,'','');
				//$latitude = $cleanvalues->number_clean($latitude,'','');
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$title_val = ${"title_" . $value};
					$description_val = ${"description_" . $value};
					$addsql.= "title_$value='$title_val',";
					$addsql.= "description_$value='$description_val',";
				}
				
				# DELETE ALL CURRENT RANGES
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}shipping_ranges WHERE ship_id = '$saveid'");
				
				# INSERT RANGES
				if($calc_type != '3')
				{
					# INSERT NEW RANGES
					foreach($rangeid as $key => $value){
						
						# BY FIXED AMOUNT
						if($cost_type == 1)
						{
							$price_clean = $cleanvalues->currency_clean($price[$key]);
						# BY PERCENTAGE
						} else {
							$price_clean = $cleanvalues->number_clean($price[$key]);
						}
						switch($calc_type)
						{
							# BY WEIGHT / BY QUANTITY
							case "1":
							case "4":
								$torange_clean = $cleanvalues->number_clean($torange[$key]);
								$fromrange_clean = $cleanvalues->number_clean($fromrange[$key]);								
							break;
							# BY SUBTOTAL
							case "2":
								$torange_clean = $cleanvalues->currency_clean($torange[$key]);
								$fromrange_clean = $cleanvalues->currency_clean($fromrange[$key]);
							break;
						}
						
						$sql = "INSERT INTO {$dbinfo[pre]}shipping_ranges (
							ship_id,
							fromrange,
							torange,
							price
							) VALUES (
							'$saveid',
							'$fromrange_clean',
							'$torange_clean',
							'$price_clean'
							)";
						$result = mysqli_query($db,$sql);
					}
				}
				else
				{
					$flat_rate = $cleanvalues->currency_clean($flat_rate); // clean the currency entered
				}
				
				# DELETE ALL CURRENT REGIONS
				@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}regionids WHERE mgrarea='$page' AND item_id = '$saveid'");
				# MAKE SURE IF NO REGIONS ARE SELECTED THAT IT IS SET TO EVERYWHERE
				if($region == 2 and $region_list)
				{
					# ADD NEW REGIONS IF NEEDED
					foreach($region_list as $value)
					{
						$reg = explode("-",$value);
						//echo "$value<br />";					
						$sql = "INSERT INTO {$dbinfo[pre]}regionids (
								reg_type,
								reg_id,
								item_id,
								mgrarea
								) VALUES (
								'$reg[0]',
								'$reg[1]',
								'$saveid',
								'$page'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				else
				{
					$region = 1;
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}shipping SET 
							title='$title',
							description='$description',";
				$sql.= $addsql;				
				$sql.= "	active='$active',
							calc_module='$calc_module',
							calc_type='$calc_type',
							flat_rate='$flat_rate',
							cost_type='$cost_type',
							day1='$day1',
							day2='$day2',
							ship_notes='$ship_notes',
							taxable='$taxable',
							region='$region'
							where ship_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				//echo $sql; exit;
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_shipping'],1,$mgrlang['gen_b_ed'] . " > <strong>$title</strong>");
				
				header("location: mgr.shipping.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":			

				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
								
				# CREATE COUNTRY ID
				$uship_id = create_unique2();
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value)
				{ 
					$title_val = ${"title_" . $value};
					$description_val = ${"description_" . $value};
					$addsqla.= ",title_$value";
					$addsqlb.= ",'$title_val'";
					$addsqla.= ",description_$value";
					$addsqlb.= ",'$description_val'";
				}
				
				# MAKE SURE IF NO REGIONS ARE SELECTED THAT IT IS SET TO EVERYWHERE
				if($region == 1 or !$region_list)
				{
					$region = 1;
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}shipping (
						title,
						uship_id,
						description,
						active,
						calc_type,
						flat_rate,
						cost_type,
						day1,
						day2,
						ship_notes,
						taxable,
						region";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$title',
						'$uship_id',
						'$description',
						'$active',
						'$calc_type',
						'$flat_rate',
						'$cost_type',
						'$day1',
						'$day2',
						'$ship_notes',
						'$taxable',
						'$region'
						";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);	
				
				# INSERT RANGES
				if($calc_type != '3')
				{	
					# INSERT NEW RANGES
					foreach($rangeid as $key => $value)
					{
						# BY FIXED AMOUNT
						if($cost_type == 1)
						{
							$price_clean = $cleanvalues->currency_clean($price[$key]);
						# BY PERCENTAGE
						} else {
							$price_clean = $cleanvalues->number_clean($price[$key]);
						}
						switch($calc_type)
						{
							# BY WEIGHT / BY QUANTITY
							case "1":
							case "4":
								$torange_clean = $cleanvalues->number_clean($torange[$key]);
								$fromrange_clean = $cleanvalues->number_clean($fromrange[$key]);								
							break;
							# BY SUBTOTAL
							case "2":
								$torange_clean = $cleanvalues->currency_clean($torange[$key]);
								$fromrange_clean = $cleanvalues->currency_clean($fromrange[$key]);
							break;
						}						
						//echo "from: $fromrange[$key] - to: $torange[$key]<br />";
						//echo print_r($rangeid);
						
						$sql = "INSERT INTO {$dbinfo[pre]}shipping_ranges (
							ship_id,
							fromrange,
							torange,
							price
							) VALUES (
							'$saveid',
							'$fromrange_clean',
							'$torange_clean',
							'$price_clean'
							)";
						$result = mysqli_query($db,$sql);
					}
				}	
				
				# ADD NEW REGIONS IF NEEDED
				if($region == 2)
				{
					foreach($region_list as $value)
					{
						$reg = explode("-",$value);
						//echo "$value<br />";					
						$sql = "INSERT INTO {$dbinfo[pre]}regionids (
								reg_type,
								reg_id,
								item_id,
								mgrarea
								) VALUES (
								'$reg[0]',
								'$reg[1]',
								'$saveid',
								'$page'
								)";
						$result = mysqli_query($db,$sql);
					}
				}
				
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_shipping'],1,$mgrlang['gen_b_new'] . " > <strong>$title</strong>");
				
				header("location: mgr.shipping.php?mes=new"); exit;
			break;		
		}
		
		# WEIGHT LABEL
		$sel_lang = $config['settings']['lang_file_mgr'];
		if($config['settings']['weight_tag_' . $sel_lang])
		{
			$weight_lang = $config['settings']['weight_tag_' . $sel_lang];
		}
		else
		{
			$weight_lang =  $config['settings']['weight_tag'];
		}
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_shipping']; ?></title>
	<!-- LOAD THE STYLE SHEETS -->
	<link rel="stylesheet" href="mgr.style.css" />
    <link rel="stylesheet" href="mgr.style.shipping.css" />
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
			$('ranges_div').className='fs_row_off';
			$('title_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					echo "return false;";
				}
				else
				{
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.shipping.edit.php?action=save_new" : "mgr.shipping.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("title","shipping_f_title",1);
			?>
					var calc_type = $('calc_type').options[$('calc_type').selectedIndex].value;
					switch(calc_type)
					{
						case "1":
						case "2":
							// CHECK TO MAKE SURE FILEDS ARE FILLED
							if($F('fromrange' + (cur - 1)) == "")
							{
								$('ranges_div').className='fs_row_error';
								simple_message_box("<?php echo $mgrlang['shipping_mes_01']; ?>",'fromrange' + (cur - 1));
								bringtofront('2');
								return false;
							}
							if($F('torange' + (cur - 1)) == "")
							{
								$('ranges_div').className='fs_row_error';
								simple_message_box("<?php echo $mgrlang['shipping_mes_02']; ?>",'torange' + (cur - 1));
								bringtofront('2');
								return false;
							}
							if($F('price' + (cur - 1)) == "")
							{
								$('ranges_div').className='fs_row_error';
								simple_message_box("<?php echo $mgrlang['shipping_mes_03']; ?>",'price' + (cur - 1));
								bringtofront('2');
								return false;
							}					
							// MAKE SURE FROM IS LESS THAN TO
							if(Number($F('fromrange' + (cur - 1))) >= Number($F('torange' + (cur - 1))))
							{
								simple_message_box("<?php echo $mgrlang['shipping_mes_04']; ?>",'torange' + (cur - 1));
								bringtofront('2');
								return false;
							}							
							if($F('price_end') == '' || $F('price_end') == null)
							{								
								$('ranges_div').className='fs_row_error';
								$('price_end').className='rangebox_error';
								simple_message_box("<?php echo $mgrlang['shipping_mes_06']; ?>",'price_end');
								bringtofront('2');
								return false;
							}
						break
						case "3":
							if($F('flat_rate') == '' || $F('flat_rate') == null)
							{
								simple_message_box("<?php echo $mgrlang['shipping_mes_05']; ?>",'flat_rate');
								$('ranges_div').className='fs_row_error';
								bringtofront('2');
								return false;	
							}
						break;
					}
			<?php
				}
			?>
		}
		
		// SWITCH STATUS FOR ACTIVE
		function switch_status(item_type,item_id)
		{
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = item_type + item_id;
				var loadpage = "mgr.shipping.actions.php?action=" + item_type + "&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
		}
		
		<?php
			$sr_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(sr_id) FROM {$dbinfo[pre]}shipping_ranges WHERE ship_id = '$_GET[edit]'"));
			
			if($_GET['edit'] == 'new' or !$sr_rows)
			{
				echo "\n var cur = 1; \n";
			}
			else
			{
				echo "var cur = " . ($sr_rows-1) . ";";
			}
		?>
		
		// ADD A NEW RANGE ROW
		function add_range_row()
		{
			$('ranges_div').className='fs_row_off';
			$('price_end').className='rangebox';
			
			var calc_type = $('calc_type').options[$('calc_type').selectedIndex].value;		
			var cost_type = $('cost_type').options[$('cost_type').selectedIndex].value;
			var numrows = ($$("div.range_row").length) - 1;
						
			// QUANTITY
			if(calc_type == '4')
			{
				// CHECK TO MAKE SURE FILEDS ARE FILLED
				var fromrange_clean = number_clean($F('fromrange' + (cur - 1)));
				var torange_clean = number_clean($F('torange' + (cur - 1)));
				var price_clean = number_clean($F('price' + (cur - 1)));
			}
			// WEIGHT
			else if(calc_type == '1')
			{
				// CHECK TO MAKE SURE FILEDS ARE FILLED
				var fromrange_clean = number_clean($F('fromrange' + (cur - 1)));
				var torange_clean = number_clean($F('torange' + (cur - 1)));
				var price_clean = number_clean($F('price' + (cur - 1)));
			}
			// EVERYTHING ELSE
			else
			{
				// CHECK TO MAKE SURE FILEDS ARE FILLED
				var fromrange_clean = currency_clean($F('fromrange' + (cur - 1)));
				var torange_clean = currency_clean($F('torange' + (cur - 1)));
				var price_clean = currency_clean($F('price' + (cur - 1)));
			}			
			
			if($F('fromrange' + (cur - 1)) == "" || !IsNumeric(fromrange_clean))
			{
				simple_message_box("<?php echo $mgrlang['shipping_mes_01']; ?>",'fromrange' + (cur - 1));
				return;
			}
			if($F('torange' + (cur - 1)) == "" || !IsNumeric(torange_clean))
			{
				simple_message_box("<?php echo $mgrlang['shipping_mes_02']; ?>",'torange' + (cur - 1));
				return;

			}
			if($F('price' + (cur - 1)) == "" || !IsNumeric(price_clean))
			{
				simple_message_box("<?php echo $mgrlang['shipping_mes_03']; ?>",'price' + (cur - 1));
				return;
			}
			
			// MAKE SURE FROM IS LESS THAN TO
			if(Number(fromrange_clean) >= Number(torange_clean))
			{
				simple_message_box("<?php echo $mgrlang['shipping_mes_04']; ?>",'torange' + (cur - 1));
				return;
			}
			
			// QUANTITY
			if(calc_type == '4')
			{
				//numset.decimal_places = 0;
				var set_from_range = Number($F('torange' + (cur - 1))) + 1;
				set_from_range = Math.round(set_from_range);
			}
			// WEIGHT
			else if(calc_type == '1')
			{
				var addnum = make_add_number();
				var set_from_range = Number(torange_clean) + addnum;
				set_from_range = number_display(set_from_range,'','');
			}
			// EVERYTHING ELSE
			else
			{
				var addnum = make_add_number();
				var set_from_range = Number(torange_clean) + addnum;
				set_from_range = currency_display(set_from_range,'','');
			}
			
			/*
			if(numset.decimal_places < '1' || $('calc_type').options[$('calc_type').selectedIndex].value == '4')
			{
				// NEW FROM VALUE
				var set_from_range = Number($F('torange' + (cur - 1))) + 1;
				set_from_range = Math.round(set_from_range);
			}
			else
			{
				// NEW FROM VALUE
				var addnum = make_add_number();
				var set_from_range = Number(torange_clean) + addnum;
				//alert(set_from_range);
				//set_from_range = Math.round(set_from_range*100)/100;
				set_from_range = number_display(set_from_range,'','');
			}
			*/
			
			// DISABLE LAST 
			$('fromrange' + (cur - 1)).setAttribute('readOnly','readonly');
			$('torange' + (cur - 1)).setAttribute('readOnly','readonly');
			$('price' + (cur - 1)).setAttribute('readOnly','readonly');
			// TO REMOVE ATTRIBUTE document.forms['myFormId'].myTextArea.removeAttribute('readOnly');			
			
			$('fromrange' + (cur - 1)).className = 'rangebox_disabled';
			$('torange' + (cur - 1)).className = 'rangebox_disabled';
			$('price' + (cur - 1)).className = 'rangebox_disabled';
			
			// SHOW THE END DIV
			if(cur > 0)
			{
				//show_div('range_end');
			}
			else
			{
				//hide_div('range_end');
			}
			
			// TURN OFF ALL DELETES
			$$('.rangedelete').each(function(s)
										{
										  	//s.className = 'alphabet_off';
											s.setStyle({ display: 'none' });
										});

			var last_row_name = $$("div.range_row")[numrows].id;			
			var templatedata = "<div style='clear: both; overflow: auto' id='range" + cur + "' class='range_row'>";
            templatedata += "<input type='hidden' name='rangeid[]' id='rangeid" + cur + "' value='" + cur + "' /><input type='text' name='fromrange[]' id='fromrange" + cur + "' value='" + set_from_range + "' maxlength='250' class='rangebox' readonly /> <input type='text' name='torange[]' id='torange" + cur + "' value='' maxlength='250' class='rangebox' onkeyup='update_end_range("+cur+");' onblur='update_end_range("+cur+");update_input_torange(" + cur + ");' /> <input type='text' name='price[]' pricebox='1' id='price" + cur + "' onblur='update_input_price(" + cur + ");' class='rangebox' />";
			templatedata += "<div id='rangedel" + cur + "' class='rangedelete'><a href=\"javascript:remove_row(" + cur + ");\" class='actionlink' style='float: left; margin: 0px 6px 0px 0px; font-weight: normal'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a></div>";
            templatedata += "</div>";
			var rowTemplate = new Template(templatedata);	
			$(last_row_name).insert({after: 
				rowTemplate.evaluate({
					id: '1'
				})});
			cur++;			
			//alert(last_row_name);
		}
		
		// UPDATE END RANGE
		function update_end_range(to)
		{
			var calc_type = $('calc_type').options[$('calc_type').selectedIndex].value;			
			// QUANTITY
			if(calc_type == '4')
			{
				var end_range = Math.round($F('torange' + to)) + Number(1);
			 	$('fromrange_end').value = end_range;
			}
			// WEIGHT
			else if(calc_type == '1')
			{
				var torange_clean = number_clean($F('torange' + to));
				var addnum = make_add_number();
				var end_range = Number(torange_clean) + addnum;
			 	$('fromrange_end').value = number_display(end_range,2,0);
			}
			// EVERYTHING ELSE
			else
			{
				var torange_clean = currency_clean($F('torange' + to));
				var addnum = make_add_number();
				var end_range = Number(torange_clean) + addnum;
			 	$('fromrange_end').value = currency_display(end_range);
			}			 
		}
		
		// MAKE MY ADD NUMBER
		function make_add_number()
		{
			var add_decimal = 1;
			for(var z=0; z<numset.decimal_places; z++)
			{
				add_decimal = add_decimal/10;
			}
			return add_decimal;
			//alert(add_decimal);
		}
		
		// FILL A FIELD WITH THE FORMATTED NUMBER ON BLUR
		function update_input_price(id)
		{				
			var cost_type = $('cost_type').options[$('cost_type').selectedIndex].value;			
			// FIXED AMOUNT
			if(cost_type == '2')
			{
				update_input_num('price' + id,1,0);
			}
			// PERCENTAGE
			else
			{
				update_input_cur('price' + id);
			}
		}
		
		function update_input_torange(id)
		{
			var calc_type = $('calc_type').options[$('calc_type').selectedIndex].value;	
			// QUANTITY
			if(calc_type == '4')
			{
				update_input_num('torange' + id,0,1);
			}
			// WEIGHT
			else if(calc_type == '1')
			{
				update_input_num('torange' + id,2,0);
			}
			// EVERYTHING ELSE
			else
			{
				update_input_cur('torange' + id);
			}
		}
		
		function update_input_price_end()
		{				
			var cost_type = $('cost_type').options[$('cost_type').selectedIndex].value;			
			// FIXED
			if(cost_type == '2')
			{
				update_input_num('price_end',1,0);
			}
			// PERCENTAGE
			else
			{
				update_input_cur('price_end');
			}
		}
		
		//make_add_number();
		
		/*
		function round_field(to)
		{	
			if(non_decimal_currency == '1' || $('calc_type').options[$('calc_type').selectedIndex].value == '4')
			{
				$('torange' + to).value = $F('torange' + to);
			}
			else
			{
				//$('torange' + to).value = Math.round(currency_fix($F('torange' + to))*100)/100;
				$('torange' + to).value = number_clean($F('torange' + to));
			}
		}
		
		
		function check_cost_field(num)
		{	
				if(non_decimal_currency == '1' || $('cost_type').options[$('cost_type').selectedIndex].value == '2')
				{
					return num;
				}
				else
				{
					$('price' + num).value = number_clean($F('price' + num));
				}
			
		}
		
		
		function check_price_end()
		{	
				if(non_decimal_currency == '1' || $('cost_type').options[$('cost_type').selectedIndex].value == '2')
				{
					//return num;
				}
				else
				{
					$('price_end').value = number_clean($F('price_end'));
				}
		}
		*/
		// REMOVE A RANGE ROW
		function remove_row(rowid)
		{
			$('range' + rowid).remove();			
			// ENABLE LAST
			$('torange' + (rowid - 1)).removeAttribute('readOnly');
			$('price' + (rowid - 1)).removeAttribute('readOnly');			
			
			if(rowid > 1)
			{
				$('fromrange' + (rowid - 1)).className = 'rangebox';
			}
			
			$('torange' + (rowid - 1)).className = 'rangebox';
			$('price' + (rowid - 1)).className = 'rangebox';

			if(rowid > 1)
			{
				$('rangedel' + (rowid - 1)).setStyle({ display: 'block' });
			}
			
			update_end_range(rowid-1);			
			cur--;
		}
		
		// UPDATE LABELS ON RANGE FORM
		function update_labels2()
		{
			var cost_type = $('cost_type').options[$('cost_type').selectedIndex].value;
			switch(cost_type)
			{
				case "1":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					$('pricelabel').update('<?php echo $mgrlang['shipping_f_calc_cost']; ?> ($)');
					
					$('cost_type_percentage').hide();
					
					//numset.decimal_places = '';
					
					/*
					// TURN COST TO DECIMAL NUMBERS
					$$('.range_row').each(function(s)
					{
						//s.className = 'alphabet_off';
						//s.setStyle({ display: 'none' });
						//s.getElementsBySelector('[pricebox="1"]');
						//alert(prices);
						
						// Source: http://prototypejs.org/api/element/select
						s.select('[pricebox="1"]').each(function(p)
						{	
							p.value = $F(p);	
						});
					});
					*/
					
					// TURN PERCENTAGES TO CURRENCY
					$$('.range_row').each(function(s)
					{
						s.select('[pricebox="1"]').each(function(p)
						{													
							if($F(p))
							{
								var p_value = number_clean($F(p));
								$(p).setValue(currency_display(p_value));
							}
							
						});	
					});
					
					if($F('price_end'))
					{
						var end_value = number_clean($F('price_end'));
						$('price_end').setValue(currency_display(end_value));
					}
					
				break;
				case "2":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					$('pricelabel').update('<?php echo $mgrlang['shipping_f_calc_cost']; ?> (%)');
					
					$('cost_type_percentage').show();
					
					// TURN PERCENTAGES TO WHOLE NUMBERS
					$$('.range_row').each(function(s)
					{
						s.select('[pricebox="1"]').each(function(p)
						{													
							if($F(p))
							{
								var p_value = Math.round($F(p));
								$(p).setValue(number_display(p_value,1,0));
							}
							
						});	
					});
					
					if($F('price_end'))
					{
						var end_value = Math.round($F('price_end'));
						$('price_end').setValue(number_display(end_value,1,0));
					}
				break;
			}
		}
		
		// UPDATE LABELS ON RANGE FORM
		function update_labels(){
			var calc_type = $('calc_type').options[$('calc_type').selectedIndex].value;
			switch(calc_type){
				case "1":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					$('fromlabel').update('<?php echo $mgrlang['shipping_f_calc_from']; ?> (<?php echo $weight_lang; ?>)');
					$('tolabel').update('<?php echo $mgrlang['shipping_f_calc_to']; ?> (<?php echo $weight_lang; ?>)');
					show_div('ranges');
					hide_div('flat_rate');
					$('ranges_title').update('<?php echo $mgrlang['shipping_f_ranges']; ?>: <br /><span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ranges_d']; ?></span>');
					
					// REMOVE ALL RANGED EXCEPT FOR THE FIRST ONE
					//$$('.range_row').each(function(s){
					//							  s.remove(); 
					//							});
					//add_range_row();
					
					// CLEAR THE PREVIOUS DATA
					var numtodel = ($$('.range_row').length)-2;
					for(var y=numtodel;y>0;y--)
					{
						remove_row(y);
					}
					$('torange0').clear();
					$('price0').clear();
					$('price_end').clear();
					$('fromrange_end').value = '?';
					
				break;
				case "2":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					$('fromlabel').update('<?php echo $mgrlang['shipping_f_calc_from']; ?> (<?php echo $config['settings']['cur_denotation']; ?>)');
					$('tolabel').update('<?php echo $mgrlang['shipping_f_calc_to']; ?> (<?php echo $config['settings']['cur_denotation']; ?>)');
					show_div('ranges');
					hide_div('flat_rate');
					$('ranges_title').update('<?php echo $mgrlang['shipping_f_ranges']; ?>: <br /><span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ranges_d']; ?></span>');
					
					// CLEAR THE PREVIOUS DATA
					var numtodel = ($$('.range_row').length)-2;
					for(var y=numtodel;y>0;y--)
					{
						remove_row(y);
					}
					$('torange0').clear();
					$('price0').clear();
					$('price_end').clear();
					$('fromrange_end').value = '?';
					
				break;
				case "3":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					hide_div('ranges');
					show_div('flat_rate');
					$('ranges_title').update('<?php echo $mgrlang['shipping_f_flatrate']; ?>: <br /><span class="input_label_subtext"><?php echo $mgrlang['shipping_f_flatrate_d']; ?></span>');
				break;
				case "4":
					$('ranges_div').className='fs_row_off';
					$('price_end').className='rangebox';
					$('fromlabel').update('<?php echo $mgrlang['shipping_f_calc_from']; ?> (<?php echo $mgrlang['gen_qty']; ?>)');
					$('tolabel').update('<?php echo $mgrlang['shipping_f_calc_to']; ?> (<?php echo $mgrlang['gen_qty']; ?>)');
					show_div('ranges');
					hide_div('flat_rate');
					$('ranges_title').update('<?php echo $mgrlang['shipping_f_ranges']; ?>: <br /><span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ranges_d']; ?></span>');
				
					// CLEAR THE PREVIOUS DATA
					var numtodel = ($$('.range_row').length)-2;
					for(var y=numtodel;y>0;y--)
					{
						remove_row(y);
					}
					$('torange0').clear();
					$('price0').clear();
					$('price_end').clear();
					$('fromrange_end').value = '?';
				
				break;
			}
		}
		
		// DO ON PAGE LOAD
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
		
		
		function shiptype_boxes(shiptype)
		{
			if(shiptype == 'global')
			{
				show_div('ship_global');
				hide_div('ship_regional');
				$('ship_global_b').className = 'subsubon';
				$('ship_regional_b').className = 'subsuboff';
			}
			else
			{
				hide_div('ship_global');
				show_div('ship_regional');
				$('ship_global_b').className = 'subsuboff';
				$('ship_regional_b').className = 'subsubon';
			}
		}
		
		// LOAD SHIPPING REGIONS
		var regions_loaded = 0;
		function load_shipping_regions(item_id)
		{		
			if(regions_loaded == 0)
			{
				$('shipping_regions').innerHTML = "<img src=\"images/mgr.loader2.gif\">";
				regions_loaded = 1;
				var updatecontent = 'shipping_regions';
				var loadpage = "mgr.shipping.actions.php?action=load_shipping_regions&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
		}
		
		// SELECT SUBS OF COUNTRIES OR STATES
		function subselect(item_type,item_id)
		{		
			switch(item_type)
			{
				// COUNTRIES
				case "c":
					if($('c-' + item_id).checked == true)
					{
						$$('[country="'+item_id+'"]').each(function(cb)
						{													
							$(cb).disable();
							$(cb).checked = true;
						});
					}
					else
					{
						$$('[country="'+item_id+'"]').each(function(cb)
						{													
							$(cb).enable();
							$(cb).checked = false;
						});
					}
				break;
				// STATES
				case "s":
					if($('s-' + item_id).checked == true)
					{
						$$('[state="'+item_id+'"]').each(function(cb)
						{													
							$(cb).disable();
							$(cb).checked = true;
						});
					}
					else
					{
						$$('[state="'+item_id+'"]').each(function(cb)
						{													
							$(cb).enable();
							$(cb).checked = false;
						});
					}
				break;
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" class="niceform" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.shipping.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['shipping_new_header'] : $mgrlang['shipping_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['shipping_new_message'] : $mgrlang['shipping_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div id="spacer_bar"></div>    
            <?php
				# PULL GROUPS
				$shipping_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$shipping_group_rows = mysqli_num_rows($shipping_group_result);
			?>            
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['shipping_tab1']; ?></div>
                <div class="subsuboff" onclick="bringtofront('4');load_shipping_regions('<?php echo $_GET['edit']; ?>');" id="tab4"><?php echo $mgrlang['shipping_tab4']; ?></div>
                <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['shipping_tab2']; ?></div>
                <?php if($shipping_group_rows){ ?><div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['shipping_tab3']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('5');" id="tab5" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['shipping_tab5']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">                
                <div class="<?php fs_row_color(); ?>" id="title_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['shipping_f_title']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_f_title_d']; ?></span>
                    </p>
                    <div class="additional_langs">
                        <input type="text" name="title" id="title" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($shipping->title); ?>" />
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_title" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="title_<?php echo $value; ?>" id="title_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($shipping->{"title" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
						<?php echo $mgrlang['shipping_f_ship_days']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ship_days_d']; ?></span>
                    </p>
                    <div style="font-size: 11px; padding-top: 10px;"><input type="text" style="width: 100px;" name="day1" value="<?php echo @stripslashes($shipping->day1); ?>" /> - <input type="text" style="width: 100px;" name="day2" value="<?php echo @stripslashes($shipping->day2); ?>" /> <?php echo $mgrlang['shipping_f_ship_days1']; ?></div>
                </div>
                
                <div class="<?php fs_row_color(); ?>" style="clear: both;">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="short" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['shipping_f_desc']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['shipping_f_desc_d']; ?></span>
                    </p>
                    <div class="additional_langs">
                        <textarea name="description" id="description" style="width: 300px; height: 50px; vertical-align: middle"><?php echo @stripslashes($shipping->description); ?></textarea>
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_description" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><textarea name="description_<?php echo $value; ?>" style="width: 300px; height: 50px; vertical-align: middle"><?php echo @stripslashes($shipping->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
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
						<?php echo $mgrlang['shipping_f_active']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['shipping_f_active_d']; ?></span>
                    </p>
                    <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($shipping->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>
            </div>
            
            
            <?php $row_color = 0; ?>
            <div id="tab2_group" class="group">
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_f_calc']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_f_calc_d']; ?></span>
                    </p>
                    <select id="calc_type" name="calc_type" style="width: 250px;" onchange="update_labels();">
                    	<option value="1" <?php if($shipping->calc_type == "1"){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_calc_op1']; ?></option>
                        <option value="2" <?php if($shipping->calc_type == "2" or $_GET['edit'] == 'new'){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_calc_op2']; ?></option>
                        <option value="4" <?php if($shipping->calc_type == "4"){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_calc_op4']; ?></option>
                        <option value="3" <?php if($shipping->calc_type == "3"){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_calc_op3']; ?></option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_f_ccalc']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ccalc_d']; ?></span>
                    </p>
                    <select id="cost_type" name="cost_type" style="width: 250px;" onchange="update_labels2();">
                    	<option value="1" <?php if($shipping->cost_type == "1" or $_GET['edit'] == 'new'){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_ccalc_op1']; ?></option>
                        <option value="2" <?php if($shipping->cost_type == "2"){ echo "selected"; } ?>><?php echo $mgrlang['shipping_f_ccalc_op2']; ?></option>
                    </select>
                </div>
                <div class="<?php fs_row_color(); ?>" id="ranges_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');" id="ranges_title">
                        <?php if($shipping->calc_type == "3"){ echo $mgrlang['shipping_f_flatrate']; } else { echo $mgrlang['shipping_f_ranges']; } ?>: <br />
                        <span class="input_label_subtext"><?php if($shipping->calc_type == "3"){ echo $mgrlang['shipping_f_flatrate_d']; } else { echo $mgrlang['shipping_f_ranges_d']; } ?></span>
                    </p>
                    <div id="flat_rate" style="float: left; <?php if($shipping->calc_type != "3"){ echo "display: none;"; } ?> margin-top: 10px;"><input type="text" name="flat_rate" value="<?php echo $cleanvalues->currency_display($shipping->flat_rate); ?>" style="width: 50px;" /> <span style="font-size: 18px; <?php if($shipping->cost_type != "2"){ echo "display: none"; } ?>" id="cost_type_percentage">%</span></div>
                    
                    <div style="float: left; overflow: auto; width: 350px; padding-bottom: 7px; <?php if($shipping->calc_type == "3"){ echo "display: none;"; } ?>" id="ranges">
                    	<div id="lab_title_row" style="clear: both; font-weight: bold; overflow: auto">
                        	<div id="fromlabel"><?php echo $mgrlang['shipping_f_calc_from']; ?> (<?php if($shipping->calc_type == "1"){ echo $weight_lang; } elseif($shipping->calc_type == "4"){ echo $mgrlang['gen_qty']; } else { echo $config['settings']['cur_denotation']; } ?>)</div><div id="tolabel" style="padding-left: 5px;"><?php echo $mgrlang['shipping_f_calc_to']; ?> (<?php if($shipping->calc_type == "1"){ echo $weight_lang; } elseif($shipping->calc_type == "4"){ echo $mgrlang['gen_qty']; } else { echo $config['settings']['cur_denotation']; } ?>)</div><div id="pricelabel" style="padding-left: 8px;"><?php echo $mgrlang['shipping_f_calc_cost']; ?> (<?php if($shipping->cost_type == "1" or $_GET['edit'] == 'new'){ echo $config['settings']['cur_denotation']; } else { echo "%"; } ?>)</div>
                        </div>
                        <div style='clear: both; height: 0px;' id='range' class='range_row'></div>
                        <?php
							if($_GET['edit'] == 'new' or !$sr_rows)
							{
						?>
							<div id="range0" class="range_row" style="overflow: auto; clear: both;">
                                <input type="hidden" name="rangeid[]" id="rangeid0" value="000000000000" />
                                <input type="text" name="fromrange[]" id="fromrange0" class="rangebox_disabled" value="0" readonly="readonly" />
                                <input type="text" name="torange[]" id="torange0" class="rangebox" value="" onkeyup="update_end_range(0);" onblur="update_end_range(0);update_input_torange(0);" />
                                <input type="text" name="price[]" pricebox="1" id="price0" class="rangebox" value="" onblur="update_input_price(0);" />
                            </div>
                            <div style="clear: both;" id="range_end" class="range_row_end">
                                <input type="hidden" name="rangeid[]" id="rangeid_end" value="000000000000" />
                                <input type="text" name="fromrange[]" id="fromrange_end" class="rangebox_disabled" value="?" readonly="readonly" />
                                <input type="hidden" name="torange[]" id="torange_end" value="99999999" />
                                <input type="text" name="torange_end2" id="torange_end2" class="rangebox_disabled" value="<?php echo $mgrlang['shipping_f_calc_inf']; ?>" readonly="readonly" />
                                <input type="text" name="price[]" pricebox="1" id="price_end" class="rangebox" value="" onblur="update_input_price_end();" />
                            </div>
                        <?php
							}
							else
							{
								# PULL SHIPPING RANGES
								$rowx = 0;
								$shipping_range_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}shipping_ranges WHERE ship_id = '$_GET[edit]' ORDER BY sr_id");
								while($shipping_range = mysqli_fetch_object($shipping_range_result))
								{
									
									switch($shipping->calc_type)
									{
										// QUANTITY
										case "4":
											$torange = floor($shipping_range->torange);
											$fromrange = floor($shipping_range->fromrange);
										break;
										// WEIGHT
										case "1":
											$torange = $cleanvalues->number_display($shipping_range->torange,2,0);
											$fromrange = $cleanvalues->number_display($shipping_range->fromrange,2,0);
										break;
										// EVERYTHING ELSE
										default:
											$torange = $cleanvalues->currency_display($shipping_range->torange);
											$fromrange = $cleanvalues->currency_display($shipping_range->fromrange);
										break;								
									}
									switch($shipping->cost_type)
									{
										// QUANTITY
										case "2":
											$price = $cleanvalues->number_display($shipping_range->price,1,0);
										break;
										// EVERYTHING ELSE
										default:
											$price = $cleanvalues->currency_display($shipping_range->price);
										break;								
									}
									
									if($rowx+1 < $sr_rows)
									{
						?>
                                    <div id="range<?php echo $rowx; ?>" class="range_row" style="overflow: auto; clear: both;">
                                        <input type="hidden" name="rangeid[]" id="rangeid<?php echo $rowx; ?>" value="<?php echo $rowx; ?>" />
                                        <input type="text" name="fromrange[]" id="fromrange<?php echo $rowx; ?>" class="rangebox_disabled" value="<?php if($rowx == 0){ echo "0"; } else { echo $fromrange; } ?>" readonly="readonly" />
                                        <input type="text" name="torange[]" id="torange<?php echo $rowx; ?>" class="rangebox" value="<?php echo $torange; ?>" onkeyup="update_end_range(<?php echo $rowx; ?>);" onblur="update_end_range(<?php echo $rowx; ?>);update_input_torange(<?php echo $rowx; ?>);" />
                                        <input type="text" name="price[]" pricebox="1" id="price<?php echo $rowx; ?>" class="rangebox" value="<?php echo $price; ?>" onblur="update_input_price(<?php echo $rowx; ?>);" />
                                        <?php
											//if($rowx != 0 and $rowx == ($sr_rows-2)){
											if($rowx != 0)
											{
										?>
                                        	<div id="rangedel<?php echo $rowx; ?>" class="rangedelete" style="<?php if($rowx != ($sr_rows-2)){ echo "display: none;"; } ?>"><a href="javascript:remove_row(<?php echo $rowx; ?>);" class='actionlink' style='float: left; margin: 0px 6px 0px 0px; font-weight: normal'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='<?php echo $mgrlang['gen_delete']; ?>' border='0' /><?php echo $mgrlang['gen_short_delete']; ?></a></div><br />
										<?php
											}
										?>
                                    </div>
                        <?php
									}
									else
									{
						?>
                                    <div style="clear: both;" id="range_end" class="range_row_end">
                                        <input type="hidden" name="rangeid[]" id="rangeid_end" value="99999" />
                                        <input type="text" name="fromrange[]" id="fromrange_end" class="rangebox_disabled" value="<?php if($rowx == 0){ echo "0"; } else { echo $fromrange; } ?>" readonly="readonly" />
                                        <input type="hidden" name="torange[]" id="torange_end" class="rangebox" value="<?php echo $torange; ?>" onkeyup="update_end_range(<?php echo $rowx; ?>);" onblur="update_end_range(<?php echo $rowx; ?>);update_input_torange(<?php echo $rowx; ?>);" />
                                        <input type="text" name="torange_end2" id="torange_end2" class="rangebox_disabled" value="<?php echo $mgrlang['shipping_f_calc_inf']; ?>" readonly="readonly" />
                                        <input type="text" name="price[]" pricebox="1" id="price_end" class="rangebox" value="<?php echo $price; ?>" onblur="update_input_price_end(<?php echo $rowx; ?>);" />
                                    </div>
                        <?php
									}
									$rowx++;
								}
							}
						?>
                        <p style="clear: both; margin: 5px 0 0 0; padding: 0; width: 308px;" align="right"><a href="javascript:add_range_row();" class='actionlink' style='float: left; font-weight: normal'><img src="images/mgr.icon.greenplus.gif" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" />&nbsp;<?php echo $mgrlang['webset_b_lab-']; ?><?php echo $mgrlang['add_range']; ?>&nbsp;</a></p>
                   	</div>
                </div>
				<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_f_taxable']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_f_taxable_d']; ?></span>
                    </p>
                    <input type="checkbox" name="taxable" value="1" style="padding: 0; margin: 6px 0 0 0;" <?php if($shipping->taxable or $_GET['edit'] == 'new'){ echo "checked"; } ?> />
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab4_group" class="group"> 
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['shipping_f_regions']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['shipping_f_regions_d']; ?></span>
                    </p>
                    
                    <div style="overflow: auto; position: relative">
                        <div class="<?php if($shipping->region == 1 or $_GET['edit'] == 'new'){ echo "subsubon"; } else { echo "subsuboff"; } ?>" id="ship_global_b" style="border-left: 1px solid #d8d7d7"><input type="radio" name="region" value="1" id="ship_global_option" onclick="shiptype_boxes('global')" <?php if($shipping->region == 1 or $_GET['edit'] == 'new'){ echo "checked"; } ?> /> <label for="ship_global_option"><?php echo $mgrlang['shipping_f_regions_op1']; ?></label></div>
                        <div class="<?php if($shipping->region == 2){ echo "subsubon"; } else { echo "subsuboff"; } ?>" id="ship_regional_b" style="border-right: 1px solid #d8d7d7;"><input type="radio" name="region" value="2" id="ship_regional_option" onclick="shiptype_boxes('regional')" <?php if($shipping->region == 2){ echo "checked"; } ?> /> <label for="ship_regional_option"><?php echo $mgrlang['shipping_f_regions_op2']; ?></label></div>
                    </div>
                    <div class="more_options" style="background-position:top;padding: 20px 5px 5px 15px; <?php if($shipping->region == 2){ echo "display: none"; } ?>" id="ship_global">
                    	<?php echo $mgrlang['shipping_f_regions_op1_d']; ?><br /><br /><br />
                    </div>
                    
                    <div class="more_options" style="padding-top: 20px;<?php if($shipping->region == 1 or $_GET['edit'] == 'new'){ echo "display: none"; } ?>" id="ship_regional">
                    	<?php echo $mgrlang['shipping_f_regions_op2_d']; ?>:<br /><br />
                        <div style="height: 400px; overflow: auto;" id="shipping_regions"></div>
                    </div>
                </div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab5_group" class="group"> 
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['shipping_f_ship_notes']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['shipping_f_ship_notes_d']; ?></span>
                    </p>
                    <textarea name="ship_notes" id="ship_notes" style="width: 300px; height: 50px;"><?php echo @stripslashes($shipping->ship_notes); ?></textarea>
                </div>
            </div>           
            
            <?php
            	if($shipping_group_rows)
				{
					$row_color = 0;
			?>
                <div id="tab3_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['shipping_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['shipping_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$shipping_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$shipping->ship_id' AND item_id != 0");
							while($shipping_groupids = mysqli_fetch_object($shipping_groupids_result))
							{
								$plangroups[] = $shipping_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($shipping_group = mysqli_fetch_object($shipping_group_result))
							{
								echo "<li><input type='checkbox' id='grp_$shipping_group->gr_id' class='permcheckbox' name='setgroups[]' value='$shipping_group->gr_id' "; if(in_array($shipping_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($shipping_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$shipping_group->flagtype' align='absmiddle' /> "; } echo "<label for='grp_$shipping_group->gr_id'>" . substr($shipping_group->name,0,30)."</label></li>";
							}
							echo "</ul>";
                        ?>
                    </div>
            	</div>
			<?php
                }
            ?>	
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.shipping.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>