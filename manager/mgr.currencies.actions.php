<?php
	###################################################################
	####	MANAGER CURRENCIES ACTIONS                             ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-4-2010                                      ####
	####	Modified: 1-4-2010                                     #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		//sleep(1);
		$page = "currencies";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
		
		error_reporting(0);
		
		# INCLUDE SECURITY CHECK FILE
		require_once('mgr.security.php'); // LEFT THIS IN SO THAT THE PAGE COULDNT BE CALLED DIRECTLY
		
		# INCLUDE MANAGER CONFIG FILE
		require_once('mgr.config.php');
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE SHARED FUNCTIONS FILE
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		include_lang();
		
		# ACTIONS
		switch($_REQUEST['action'])
		{
			# SET ACTIVE STATUS
			case "ac":
				$currency_result = mysqli_query($db,"SELECT active,defaultcur,code FROM {$dbinfo[pre]}currencies WHERE currency_id = '$_REQUEST[id]' AND deleted = '0'");
				$currency = mysqli_fetch_object($currency_result);
				
				# FLIP THE VALUE
				$new_value = (empty($currency->active) ? 1 : 0);
				
				if($currency->defaultcur == '1')
				{
					if($new_value == 0)
					{
						echo "<script language='javascript'>simple_message_box('$mgrlang[currencies_error3]','');$('acimg" . $_REQUEST['id'] . "').src = './images/mgr.small.check.1.png';</script>";
					}
					else
					{
						# ADDED THIS JUST IN CASE SOMEHOW THE PRIMARY CURRENCY GETS TURNED OFF
						$sql = "UPDATE {$dbinfo[pre]}currencies SET active='1' where currency_id = '$_REQUEST[id]'";
						$result = mysqli_query($db,$sql);
						echo "<script language='javascript'>";
						echo "\$('acimg" . $_REQUEST['id'] . "').src = './images/mgr.small.check.1.png';";
						// ADD IF NEEDED
						echo "update_ur_array('".$_REQUEST['id']."','$currency->code','$new_value');";
                        echo "</script>";
					}
				}
				else
				{			
					$sql = "UPDATE {$dbinfo[pre]}currencies SET active='$new_value' where currency_id = '$_REQUEST[id]'";
					$result = mysqli_query($db,$sql);
					
					echo "<script language='javascript'>";
					echo "\$('acimg" . $_REQUEST['id'] . "').src = './images/mgr.small.check." . $new_value . ".png';";
					// ADD IF NEEDED
					echo "update_ur_array('".$_REQUEST['id']."','$currency->code','$new_value');";
                    echo "</script>";
				}
			break;
			# SET ACTIVE STATUS
			case "df":
				$sql = "UPDATE {$dbinfo[pre]}currencies SET defaultcur='0',exchange_date='0000-00-00 00:00:00'";
				$result = mysqli_query($db,$sql);
				
				$updatedate = gmt_date();
				
				$sql = "UPDATE {$dbinfo[pre]}currencies SET defaultcur='1',active='1',exchange_rate='1',exchange_date='$updatedate' WHERE currency_id = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				$sql = "UPDATE {$dbinfo[pre]}settings SET defaultcur='{$_REQUEST[id]}' WHERE settings_id = 1";
				$result = mysqli_query($db,$sql);
?>
            	<script language='javascript'>update_default_checks(<?php echo $_REQUEST['id']; ?>);</script>
<?php
            break;
			# GRAB EXCHANGE RATE
			case "grabcur":
				require_once('../assets/classes/exchange.rates.php');
				$quote = new CurrencyConvert($_REQUEST['source']);
				$currency_list = $quote->currencies();
				
				foreach($currency_list as $code => $name)
				{	
					$code_array[] = $code;
				}
				
				# CHECK TO MAKE SURE THE RATE CAN BE GRABBED
				if($quote->convert(strtoupper($_REQUEST['from']),strtoupper($_REQUEST['to'])))
				{
					$quote_price = $quote->price();
					$updatedate = gmt_date();
					# IF AN ID IS PASSED THEN UPDATE THE DATABASE
					if($_REQUEST['id'])
					{
						$sql = "UPDATE {$dbinfo[pre]}currencies SET exchange_rate='$quote_price',exchange_date='$updatedate' WHERE currency_id = '$_REQUEST[id]'";
						$result = mysqli_query($db,$sql);
						
						$currency_result = mysqli_query($db,"SELECT code FROM {$dbinfo[pre]}currencies where currency_id = '$_REQUEST[id]'");
						$currency = mysqli_fetch_object($currency_result);
					}					
					echo "1"; // STATUS
					echo "|";
					echo $quote_price; // VALUE
					echo "|";
					echo $_REQUEST['id']; // ID IF PASSED
					echo "|";
					echo $currency->code; // RETURN CODE
				}
				else
				{
					echo "0"; // STATUS
					echo "|";
					echo "1"; // VALUE
					echo "|";
					echo $currency->code; // RETURN CODE
				}				
            break;
			# GRAB ALL EXCHANGE RATES
			case "grabcur_batch":
				$currency_result = mysqli_query($db,"SELECT code,name,exchange_updater,currency_id,defaultcur,active FROM {$dbinfo[pre]}currencies WHERE currency_id = '$_REQUEST[id]' AND deleted = '0'");
				$currency = mysqli_fetch_object($currency_result);
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				//$cleanvalues->decimal_separator = "....";
				
				
				# SEE WHERE IT SHOULD BE UPDATING THROUGH
				if($currency->exchange_updater == '2')
				{
					$exchange_updater = 'Yahoo';
				} 
				else
				{
					$exchange_updater = 'Google';
				}
				
				if($currency->defaultcur == 0 and $currency->active)
				{
					require_once('../assets/classes/exchange.rates.php');
					$quote = new CurrencyConvert($exchange_updater);
					$currency_list = $quote->currencies();
					
					##### ADD CHECK FOR CURRENCY NOT SUPPORTED
					if(in_array($currency->code,array_keys($currency_list)))
					{
						# CHECK TO MAKE SURE THE RATE CAN BE GRABBED
						if($quote->convert(strtoupper($currency->code),strtoupper($_REQUEST['to'])))
						{
							$quote_price = $quote->price();
							$updatedate = gmt_date();
							# UPDATE THE DATABASE
							$sql = "UPDATE {$dbinfo[pre]}currencies SET exchange_rate='$quote_price',exchange_date='$updatedate' WHERE currency_id = '$_REQUEST[id]'";
							$result = mysqli_query($db,$sql);
							# NO ERRORS
							$error = 0;
						}
						else
						{
							# RETURN ERROR
							$error = 1;
						}					
					}
					else
					{
						$error = 2;
					}
				}
				else
				{
					$error = 3;
				}
?>
				<script language='javascript'>
					$('dobutton').disable();
					var current_content = $('wbox_updates').innerHTML;
					<?php
						switch($error)
						{
							default:
							case "0":
					?>
							if($('er' + <?php echo $currency->currency_id; ?>) != null)
							{
								$('er' + <?php echo $currency->currency_id; ?>).update(number_display('<?php echo $quote_price; ?>',4,0));
								$('erimg' + <?php echo $currency->currency_id; ?>).src = './images/mgr.tiny.star.1.png';
							}
							var updated_content = current_content + '<?php echo "<strong>$mgrlang[currencies_mes1] $exchange_updater:</strong> $currency->name (" . $currency->code . ") = <strong>" . $cleanvalues->number_display($quote_price,4,0) . "</strong>"; ?><br />';
					<?php
							break;
							case "1":
					?>
							var updated_content = current_content + '<?php echo "<span style=\'color:#ff0000\'><strong>$mgrlang[currencies_error2]</strong> $currency->name (" . $currency->code . ") = <strong>" . $cleanvalues->number_display($quote_price,4,0) . "</strong>"; ?></span><br />';
					<?php
							break;
							case "2":
					?>
							var updated_content = current_content + '<?php echo "<span style=\'color:#ff0000\'><strong>$mgrlang[currencies_error1]</strong>: $currency->name (" . $currency->code . ")"; ?></span><br />';
					<?php
							break;
							case "3":
								echo "var updated_content = current_content;";
							break;
						}
					?>
					$('wbox_updates').update(updated_content);
					update_batch_er(<?php echo ($_REQUEST['arraynum'] + 1); ?>);
               	</script>
<?php	
			break;
			case "curwin":
				$currency_result = mysqli_query($db,"SELECT code,name,exchange_updater,currency_id,defaultcur,active,exchange_rate FROM {$dbinfo[pre]}currencies where currency_id = '$_REQUEST[id]'");
				$currency = mysqli_fetch_object($currency_result);
				
				$pricur_result = mysqli_query($db,"SELECT code,name,exchange_updater,currency_id,defaultcur,active,decimal_places,decimal_separator FROM {$dbinfo[pre]}currencies where defaultcur = '1'");
				$pricur = mysqli_fetch_object($pricur_result);
				
				$cleanvalues = new number_formatting;
				$cleanvalues->cur_decimal_places = $pricur->decimal_places;
				$cleanvalues->cur_decimal_separator = $pricur->decimal_separator;
				//$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				
				$calculated = $currency->exchange_rate*100;
				
				echo $cleanvalues->currency_display('100') . " <strong>(" . $currency->code . ")</strong> = " . $cleanvalues->currency_display($calculated,'','') . ' <strong>(' . $pricur->code . ")</strong>";
			break;
		}
?>
