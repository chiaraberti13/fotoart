<?php
	###################################################################
	####	MANAGER CURRENCIES PAGE                                ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-4-2010                                      ####
	####	Modified: 1-4-2010                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "currencies";
		$lnav = "settings";
		
		$supportPageID = '382';
	
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
		
		# GET SUPPORTED EXCHANGE CURRENCIES
		$quote = new CurrencyConvert('Google');
		$currency_list = $quote->currencies();
		/*
		foreach(array_keys($currency_list) as $value)
		{
			echo $value . "<br />";
		}
		exit;
		*/		
		# IF AN ENTRY PAGE CLEAR CURRENTPAGE SESSION
		if(!empty($_REQUEST['ep']) && isset($_SESSION['currentpage'])){ $_SESSION['currentpage'] = 1; }
		
		# FIND PRIMARY CURRENCY INFO
		$pricur_result = mysqli_query($db,"SELECT code FROM {$dbinfo[pre]}currencies WHERE defaultcur = '1'");
		$pricur_rows = mysqli_num_rows($pricur_result);
		$pricur = mysqli_fetch_object($pricur_result);
		
		# ACTIONS
		switch($_REQUEST['action'])
		{		
			case "del":
				if(!empty($_REQUEST['items']))
				{
					$items = $_REQUEST['items'];
										
					if(!is_array($items))
					{
						$items = explode(",",$items);
					}				
					$delete_array = implode(",",$items);
					
					# GET TITLES FOR LOG
					$log_result = mysqli_query($db,"SELECT name,currency_id FROM {$dbinfo[pre]}currencies WHERE currency_id IN ($delete_array)");
					while($log = mysqli_fetch_object($log_result))
					{
						$log_titles.= "$log->name ($log->currency_id), ";
					}
					if(substr($log_titles,strlen($log_titles)-2,2) == ", ")
					{
						$log_titles = substr($log_titles,0,strlen($log_titles)-2);
					}
					
					# DELETE
					//@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}currencies WHERE currency_id IN ($delete_array)");
					
					# MARK AS DELETED
					$sql = "UPDATE {$dbinfo[pre]}currencies SET deleted = '1' WHERE currency_id IN ($delete_array)";
					$result = mysqli_query($db,$sql);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_currencies'],1,$mgrlang['gen_b_del'] . " > <strong>$log_titles</strong>");
				}
				else
				{
					# OUTPUT A VERIFICATION MESSAGE
					$vmessage=$mgrlang['gen_mes_noitem'];
				}			
			break;
			
			case "save_status":
				//$defaultcur_result = mysqli_query($db,"SELECT currency_id FROM {$dbinfo[pre]}currencies WHERE defaultcur ");
				//$defaultcur = mysqli_fetch_object($defaultcur_result))
				
				# SAVE STATUS
				if($_POST['set_to'] == 'selitems')
				{				
					# TURN ITEMS INTO ARRAY
					//$items = explode(",",$_POST['selected_items']);
					//$update_array = implode(",",$items);
					//echo $update_array; exit;
					
					$trimto = strlen($_POST['selected_items']) - 1;
					$update_string = substr($_POST['selected_items'],0,$trimto);
					//echo $update_string; exit;
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}currencies SET active='$_POST[status]' WHERE currency_id IN ($update_string) AND defaultcur != '1'";
					$result = mysqli_query($db,$sql);				
				}
				else
				{
					$sql = "UPDATE {$dbinfo[pre]}currencies SET active='$_POST[status]' WHERE defaultcur != '1'";
					$result = mysqli_query($db,$sql);	
				}
				$vmessage = $mgrlang['gen_mes_changesave'];				
			break;
		}
		
		# HIDE DELETE LINK FOR DEMO MODE
		if($_SESSION['admin_user']['admin_id'] == "DEMO")
		{
			$delete_link = "DEMO_";
		}
		else
		{
			$delete_link = $_SERVER['PHP_SELF'] . "?action=ds&id=";
		}
		
		if($_GET['mes'] == "new")
		{
			$vmessage = $mgrlang['gen_mes_save'];
		}
		if($_GET['mes'] == "edit")
		{
			$vmessage = $mgrlang['gen_mes_changesave'];
		}
				
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_countries_currencies']; ?></title>
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
		<script language="javascript" type="text/javascript">
		// DELETE RECORD FUNCION
		function deleterec(idnum)
		{
			if(idnum){ var gotopage = '&items=' + idnum; var dtype = 'link'; } else { var gotopage = ''; var dtype = 'form'; }			
			delete_link('<?php echo $_SESSION['admin_user']['admin_id']; ?>','<?php echo $config['settings']['verify_before_delete']; ?>',dtype,'<?php echo $_SERVER[PHP_SELF] . "?action=del" ; ?>' + gotopage);
		}
		
		// DEFINE PRIMARY CURRENCY CODE
		var primarycur = '<?php echo $pricur->code; ?>';
		
		// SWITCH STATUS ON HOMEPAGE OR ACTIVE
		function switch_status(item_type,item_id,pricode)
		{
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$(item_type + 'img' + item_id).src = "./images/mgr.loader.gif";
				var updatecontent = 'hidden_box';
				//var updatecontent = item_type + item_id;
				var loadpage = "mgr.currencies.actions.php?action=" + item_type + "&id=" + item_id;
				var pars = "";
				var myAjax = new Ajax.Updater(
											  updatecontent,
											  loadpage,
											  	{
													method: 'get',
													parameters: pars,
													evalScripts: true
												});
	
				// UPDATE PRIMARY IF NEEDED
				if(item_type == 'df')
				{
					primarycur = pricode;
				}
			}
		}
		
		function update_default_checks(seton){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				// DEFAULT ICONS
				$$('[dfimg="1"]').each(function(s)
				{
					s.src = './images/mgr.small.check.0.png';
				});
				// SET CORRECT ONE ON
				$('dfimg' + seton).src = './images/mgr.small.check.1.png';
				
				<?php
					if(in_array('multicur',$installed_addons))
					{
				?>			
					// EXCHANGE RATE ICONS
					$$('[erimg="1"]').each(function(s)
					{
						s.src = './images/mgr.tiny.star.0.png';
					});
					// SET CORRECT ONE ON
					$('erimg' + seton).src = './images/mgr.tiny.star.1.png';
					$('er' + seton).update(number_display('1',4,0));
					
					// SET ACTIVE TO ON
					if($('acimg' + seton) != null)
					{
						$('acimg' + seton).src = './images/mgr.small.check.1.png';
					}
				<?php
					}
				?>
				// SET ALL CHECKBOXES ENABLED
				$$('.atitems').each(function(s)
				{
					s.enable();
				});
				// REENABLE CHECKBOX
				$('chk' + seton).disable();
				
				// SET ALL DELETES VISIBLE
				$$('[dellink="1"]').each(function(s)
				{
					s.setStyle({display: ''});
				});
				// HIDE DELETE
				$('del' + seton).setStyle({display: 'none'});
			}
		}
		
		// DO WORKBOX ACTIONS
		function do_actions()
		{
			var selecteditem = $('actionsdd').options[$('actionsdd').selectedIndex].value;
			// REVERT BACK TO ACTIONS TITLE
			$('actionsdd').options[0].selected = 1;
			
			// CREATE THE WORKBOX OBJECT
			workboxobj = new Object();
			
			switch(selecteditem)
			{
				case "set_status":					
					workboxobj.mode = 'set_status';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
				break;
				case "update_exchange_rates":					
					workboxobj.mode = 'update_exchange_rates';
					workboxobj.page = '<?php echo $page; ?>';
					workboxobj.filename = '<?php echo $_SERVER['SCRIPT_NAME']; ?>';
					workboxobj.supportid = '<?php echo $supportPageID; ?>';
					workbox(workboxobj);
					stopbatch=0;
				break;
			}
		}
			
		// SET NUMBER VARIABLES
		var numset = new Object();
		numset.format_as_currency = 0;
		numset.code = "USD";
		numset.denotation = "$";
		numset.decimal_separator = "<?php echo $config['settings']['decimal_separator']; ?>";
		numset.decimal_places = 4;
		numset.thousands_separator = "<?php echo $config['settings']['thousands_separator']; ?>";		
		numset.neg_num_format = "<?php echo $config['settings']['neg_num_format']; ?>";
		numset.pos_cur_format = 1;
		numset.neg_cur_format = 1;
		numset.exchange_rate = 1;
		numset.strip_ezeros = 1;	
		
		// DO ON PAGE LOAD
		Event.observe(window, 'load', function()
			{			
			// ADD NEW BUTTON
			if($('abutton_add_new')!=null)
			{
				$('abutton_add_new').observe('click', function()
					{
						window.location.href='mgr.currencies.edit.php?edit=new';
					});
				$('abutton_add_new').observe('mouseover', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.png';
					});
				$('abutton_add_new').observe('mouseout', function()
					{
						$('img_add_new').src='./images/mgr.button.add.new.off.png';
					});
			}
			
			// SELECT ALL BUTTON
			if($('abutton_select_all')!=null)
			{
				$('abutton_select_all').observe('click', function()
					{
						select_all_cb('datalist');
					});
				$('abutton_select_all').observe('mouseover', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.png';
					});
				$('abutton_select_all').observe('mouseout', function()
					{
						$('img_select_all').src='./images/mgr.button.select.all.off.png';
					});
			}
			
			// SELECT NONE BUTTON
			if($('abutton_select_none')!=null)
			{
				$('abutton_select_none').observe('click', function()
					{
						deselect_all_cb('datalist');
					});
				$('abutton_select_none').observe('mouseover', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.png';
					});
				$('abutton_select_none').observe('mouseout', function()
					{
						$('img_select_none').src='./images/mgr.button.select.none.off.png';
					});
			}
			
			// DELETE BUTTON
			if($('abutton_delete')!=null)
			{
				$('abutton_delete').observe('click', function()
					{
						deleterec();
					});
				$('abutton_delete').observe('mouseover', function()
					{
						$('img_delete').src='./images/mgr.button.delete.png';
					});
				$('abutton_delete').observe('mouseout', function()
					{
						$('img_delete').src='./images/mgr.button.delete.off.png';
					});
			}
						
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
		<?php
			if(in_array('multicur',$installed_addons))
			{
		?>
			// GRAB SUPPORTED CURRENCY CODES FOR EXCHANGE RATES
			var exchange_rate_support = new Array();
			<?php
				$x=0;
				foreach($currency_list as $code => $name)
				{	
					$exchange_rate_support[] = $code; // FOR PHP
					echo "exchange_rate_support[$x] = '" . $code . "';\n";
					$x++;
				}
			?>
			
			// GRAB THE CURRENT EXCHANGE RATE
			function grab_cur_rate(id,source,from)
			{
				if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
				{
					demo_message();
				}
				else
				{				
					switch(source)
					{
						case "0":
						default:
						case "1":
							source = 'Google';
						break;
						case "2":
							source = 'Yahoo';
						break;
					}
					
					var to = primarycur;
					$('erimg' + id).src = './images/mgr.loader.gif';			
					
					// MAKE SURE THE CURRENCIES ARE SUPPORTED FOR EXCHANGE RATE FIRST
					if(exchange_rate_support.inArray(from) && exchange_rate_support.inArray(to))
					{
						var loadpage = "mgr.currencies.actions.php";
						var myAjax = new Ajax.Request(
													  loadpage,
														{
														  method: 'get',
														  parameters: {action: 'grabcur', from: from, to: to, source: source, id: id},
														  evalScripts: true,
														  onSuccess: update_rate_box 
														});
					}
					else
					{
						if(!exchange_rate_support.inArray(from))
						{
							var currency = from;
						}
						if(!exchange_rate_support.inArray(to))
						{
							var currency = to;
						}				
						simple_message_box('<?php echo $mgrlang['currencies_error1']; ?> ('+currency+')','');				
						$('erimg' + id).src = './images/mgr.tiny.star.0.png';
					}
				}
			}
			
			<?php
				# GRAB ALL ACTIVE CURRENCY IDS
				//echo "var update_records = new Array();";
		
				# FIND THE ACTIVE SUPPORTED CURRENCY INFO
				$x=0;
				$activecur_result = mysqli_query($db,"SELECT currency_id FROM {$dbinfo[pre]}currencies WHERE active = '1'");
				//$activecur_rows = mysqli_num_rows($activecur_result);
				while($activecur = mysqli_fetch_object($activecur_result))
				{
					//$exchange_rate_support[] = $code; // FOR PHP
					//echo "update_records[$x] = '" . $activecur->currency_id . "';\n";
					$myarrayvals.="\"$activecur->currency_id\",";
					$x++;
				}
				$myarrayvals = substr($myarrayvals,0,(strlen($myarrayvals)-1));
				echo "var update_records = [$myarrayvals];";
			?>
			
			// UPDATE THE RATE BOX OR PROVIDE ERROR
			function update_rate_box(returnedvalue)
			{
				var values = returnedvalue.responseText.split("|");
				if(values[0] == '1' && values[1] != '')
				{
					//$('er' + values[2]).update('1 ' + primarycur + ' = ' + values[1] + ' ' + values[3]);
					$('er' + values[2]).update(number_display(values[1],4,0));
					$('erimg' + values[2]).src = './images/mgr.tiny.star.1.png';
					//$('exchange_rate').setValue(values[2]);
					//alert(values[2]);
				}
				else
				{
					simple_message_box('<?php echo $mgrlang['currencies_error2']; ?>','');
					$('erimg' + values[2]).src = './images/mgr.tiny.star.0.png';
				}
				//update_exchange_preview();
			}
			
			// UPDATE THE ENTIRE BATCH OF EXCHANGE RATES
			//var current_array_num = 0;
			var update_records_length = update_records.length-1;
			var stopbatch = 0;
			function update_batch_er(arraynum)
			{
				update_records_length = update_records.length-1; // UPDATE EVERY TIME
				//alert(update_records);
				if(arraynum <= update_records_length && stopbatch==0)
				{
					id = update_records[arraynum];
					var to = primarycur;
					var loadpage = "mgr.currencies.actions.php";
					var myAjax = new Ajax.Updater(
												  'hidden_box',
												  loadpage,
													{
													  method: 'get',
													  parameters: {action: 'grabcur_batch', id: id, to: to, arraynum: arraynum},
													  evalScripts: true
													});
				}
				else
				{
					var current_content = $('wbox_updates').innerHTML;
					var updated_content = current_content + "<span style='font-weight: bold; color: #56ab09'><?php echo $mgrlang['currencies_mes2']; ?></span>";
					$('wbox_updates').update(updated_content);
				}
			}
			
			// PREFILL THE WORKBOX UPDATED DIV WITH AN UPDATING MESSAGE
			function prefill_wbox_updates()
			{
				$('wbox_updates').update('<strong><?php echo $mgrlang['currencies_mes3']; ?></strong><br />');	
			}
			
			// ADD NEWLY SET ACTIVE TO THE ARRAY
			function update_ur_array(id,code,status)
			{				
				if(status == 1)
				{
					// MAKE SURE IT IS SUPPORTED AND 'NOT ALREADY' IN THE ARRAY
					//if(exchange_rate_support.inArray(code) && !update_records.inArray(id))
					if(!update_records.inArray(id))
					{
						update_records.push(id);
					}
				}
				else
				{
					// MAKE SURE IT IS SUPPORTED AND 'ALREADY' IN THE ARRAY
					//if(exchange_rate_support.inArray(code) && update_records.inArray(id))
					if(update_records.inArray(id))
					{
						//alert('can_remove');
						// FIND THE POSITION IN THE ARRAY	
						var xy=0;
						for(xy=1;xy < update_records.length; xy++)
						{
							if(update_records[xy] == id)
							{
								//alert('can_remove');
								update_records.splice(xy,1);
								return;
							}
							//update_records.splice(indexnumber,1);
						}
					}
				}
			}
			
			// START CURRENCY DETAILS PANEL
			var start_panel;
			function start_cur_panel(id,code)
			{
				var cur_panel = 'more_info_' + id;
				$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
				//start_panel = setTimeout("show_div_fade('" + cur_panel + "')",'350');
				start_panel = setTimeout("show_div_fade_load('" + cur_panel + "','mgr.currencies.actions.php?action=curwin&id="+id+"','_content')",'550');
				//fill_cur_panel(id,code);
			}
			
			// FILL THE PANEL WITH INFO
			function fill_cur_panel(id,code)
			{
				/*
				var cur_panel_content = 'more_info_' + id + '_content';
				var exchange_rate = $('er' + id).innerHTML;
				var start_rate = number_display('1','','');
				
				var content = start_rate + ' <strong>' + primarycur + '</strong> = ' + exchange_rate + '<strong> ' + code + '</strong>';
				
				$(cur_panel_content).update(content);
				*/
			}
			
			// BRING THE PANEL TO THE FRONT
			function mem_details_tofront(id)
			{
				var mem_panel = 'more_info_' + id;
				z_index++;
				$(mem_panel).setStyle({
					zIndex: z_index
				});
			}
			
			// CANCEL LOAD AND CLOSE ALL PANELS
			function cancel_cur_panel(id)
			{
				clearTimeout(start_panel);
				$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
				$("more_info_" + id + "_content").update('<img src="images/mgr.loader.gif" style="margin: 40px;" />');
			}
		<?php
			}
		?>
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
                                
            # INCLUDE DATASORTS CLASS
			require_once('mgr.class.datasort.php');			
			$sortprefix="currencies";
			$datasorts = new data_sorting;
			$datasorts->prefix = $sortprefix;
            $datasorts->clear_sorts($_GET['ep']);
			$id_field_name = "currency_id";		
			require_once('mgr.datasort.logic.php');	

			$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(currency_id) FROM {$dbinfo[pre]}currencies WHERE deleted = '0'"));
			
            $pages = ceil($r_rows/$perpage);
           
            # CHECK TO SEE IF THE CURRENT PAGE IS SET
            if(isset($_SESSION['currentpage']))
			{
                if(!empty($_REQUEST['updatepage'])) $_SESSION['currentpage'] = $_REQUEST['updatepage'];
            }
			else
			{
               // session_register();
                $_SESSION['currentpage'] = 1;
            }
            
            # CALCULATE THE STARTING RECORD						
            $startrecord = ($_SESSION['currentpage'] == 1) ? 0 : (($_SESSION['currentpage'] - 1) * $perpage);
            
            # FIX FOR RECORDS GETTING DELETED
            if($startrecord > ($r_rows - 1))
			{
                $startrecord-=$perpage;
            }
            
            # SELECT ITEMS
			$currency_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}currencies WHERE deleted = '0' ORDER BY $listby $listtype LIMIT $startrecord,$perpage");
	
            # CREATE DELETE LINKS - TO AVOID IF STATEMENTS LATER
            if($_SESSION['admin_user']['admin_id'] == "DEMO")
			{
                $dmode = "demo";
            }
			else
			{
                if($config['settings']['verify_before_delete'])
				{
                    $dmode = "verify";
                }
				else
				{
                    $dmode = "direct";
                }
            }
            
        ?>
            <!-- ACTIONS BAR AREA -->
            <div id="actions_bar">							
                <div class="sec_bar">
                    <img src="./images/mgr.badge.currencies.png" align="absmiddle" /><span><?php echo $mgrlang['subnav_currencies']; ?></span> &nbsp; 
                </div>							
                <div style="float: left;"><img src="./images/mgr.actions.bar.div.png" class="action_bar_divider" /></div>
                
                <div style="float: left; padding-left: 3px;">
                    <div style="float: left;" class="abuttons" id="abutton_add_new"><img src="./images/mgr.button.add.new.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_new']; ?>" id="img_add_new" /><br /><?php echo $mgrlang['gen_b_new']; ?></div>
                   	<?php if(!empty($r_rows)){ ?>
                    	<div style="float: left;" class="abuttons" id="abutton_select_all"><img src="./images/mgr.button.select.all.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sa']; ?>" id="img_select_all" /><br /><?php echo $mgrlang['gen_b_sa']; ?></div>
                        <div style="float: left;" class="abuttons" id="abutton_select_none"><img src="./images/mgr.button.select.none.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_sn']; ?>" id="img_select_none" /><br /><?php echo $mgrlang['gen_b_sn']; ?></div>	
                        <div style="float: left;" class="abuttons" id="abutton_delete"><img src="./images/mgr.button.delete.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_del']; ?>" id="img_delete" /><br /><?php echo $mgrlang['gen_b_del']; ?></div>					
                    <?php } ?>                    
                	<div style="float: left;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
				</div>
                <?php
					if($r_rows and in_array('multicur',$installed_addons))
					{
				?>                
                               
                <form name="actions">
                <div style="padding-top: 9px; margin-right: 20px;">
                    
                    <select align="absmiddle" id="actionsdd" onchange="do_actions();">
                       	<option value="#"><?php echo $mgrlang['gen_actions']; ?>:</option>
                        <option value="set_status">&nbsp; <?php echo $mgrlang['gen_tostatus']; ?></option>
                        <option value="update_exchange_rates">&nbsp; <?php echo $mgrlang['currencies_dd_ur']; ?></option>
                    </select>
                </div>	
                </form>
                             
                <?php
					}
				?>
            </div>
            <?php
				if(in_array('multicur',$installed_addons))
				{
			?>
                <div class="perpage_bar" style="text-align: right">
                    <img src="images/mgr.tiny.star.1.png" style="vertical-align: middle" />&nbsp;<span style="font-weight: bold;"> = <?php echo $mgrlang['currencies_updated_24hours']; ?></span>
                </div>
            <?php
				}
			?>
            <!-- START CONTENT -->
            <?php
                # CHECK TO MAKE SURE THERE ARE RECORDS
                if(!empty($r_rows))
				{
					if($r_rows > 10 and $perpage > 10)
					{
						include('mgr.perpage.php');	
					}
            ?>
                <div id="content">						
                    <form name="datalist" id="datalist" action="#" method="post">
                    <div style="display: none;" id="hidden_box"></div>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- DATA TABLE HEADER -->
                        <tr>
							<?php $header_name = "currency_id";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_id']; ?></a></div></div></td>
                            <?php $header_name = "code";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" width="100">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_code']; ?></a></div></div></td>
                            <?php $header_name = "name";		if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" width="100%">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_name']; ?></a></div></div></td>
                            <?php if(in_array('multicur',$installed_addons)){ $header_name = "exchange_rate"; if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center" nowrap="nowrap">	<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['currencies_t_er']; ?></a></div></div></td><?php } ?>
							<?php if(in_array('multicur',$installed_addons)){ $header_name = "active"; if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['gen_t_active']; ?></a></div></div></td><?php } ?>
                            <?php $header_name = "defaultcur";	if($listby == $header_name){ $sel = "_sel"; } else { $sel = ""; }	$hlink = ""; $hlink = $_SERVER['PHP_SELF'] . "?listby=" . $header_name . "&listtype="; if($listtype == "asc" && $listby == $header_name){ $hlink .= "desc"; } else { $hlink .= "asc"; } ?><td class="cth<?php echo $sel; ?>" onclick="window.location.href='<?php echo $hlink; ?>'" align="center">				<div class="content_table_header<?php echo $sel; ?>"><div><a href="<?php echo $hlink ?>"><?php echo $mgrlang['currencies_t_defaultcur']; ?></a></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center"><div class="content_table_header"><div><?php echo $mgrlang['gen_t_actions']; ?></div></div></td>
                            <td class="cth" style="cursor: auto;" align="center" nowrap><div class="content_table_header"><div><a href="#" onclick="select_all_cb('datalist');"><?php echo $mgrlang['gen_t_all']; ?></a> | <a href="#" onclick="deselect_all_cb('datalist');"><?php echo $mgrlang['gen_t_none']; ?></a></div></div></td>
                        </tr>
                        
                        <!-- DATA TABLE CONTENT -->
                        <?php								
                            $current_date = gmt_date();							
							//date("Y-m-d H:i:s",strtotime("$this->pdate +$adj_hours hours $adj_minutes minutes"));							
							$past_date = gmdate("Y-m-d H:i:s",strtotime("$current_date -$config[DisplayRatesAsNew] hours"));							
							//echo $current_date; exit;
							
							$cleanvalues = new number_formatting;
							$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
							$cleanvalues->decimal_places = 4;
							$cleanvalues->strip_ezeros = 1;
							
							# SELECT LOOP THRU ITEMS									
                            while($currency = mysqli_fetch_object($currency_result))
							{
								//echo "Tester"; exit;
                            
                                # SET THE ROW COLOR
                                @$row_color++;
                                if ($row_color%2 == 0)
								{
                                    $row_class = "list_row_on";
                                    $color_fade = "EEEEEE";
                                }
								else
								{
                                    $row_class = "list_row_off";
                                    $color_fade = "FFFFFF";
                                }
                                
								$title = $currency->name;
								
                                # IF THE shipping TITLE IS TOO LONG CROP IT / ALSO TAKE OUT ALL HTML IF THERE IS ANY
                                if(strlen(strip_tags($title)) > 70)
								{
                                    $title = substr(strip_tags($title),0,67) . "..."; 
                                }
								else
								{
                                    $title = strip_tags($title);
                                }						
                        ?>
                            <tr><td height="1" colspan="8" bgcolor="ffffff" style="background-color: #FFFFFF;"></td></tr>
                            <tr class="<?php echo $row_class; ?>" onmouseover="cellover(this,'#<?php echo $color_fade; ?>',32);" onmouseout="cellout(this,'#<?php echo $color_fade; ?>');">
                                <td align="center"><a name="row_<?php echo $currency->currency_id; ?>"></a><?php echo $currency->currency_id; ?></td>
                                <td align="center"><a name="row_<?php echo $currency->currency_id; ?>"></a><?php echo $currency->code; ?></td>
                                <td onclick="window.location.href='mgr.currencies.edit.php?edit=<?php echo $currency->currency_id; ?>'"><a href="mgr.currencies.edit.php?edit=<?php echo $currency->currency_id; ?>" class="editlink"><?php echo $title; ?></a>&nbsp;</td>
                                <?php if(in_array('multicur',$installed_addons)){ ?>
                                	<td align="center" nowrap="nowrap">
                                    	<a href="javascript:<?php if(in_array($currency->code,$exchange_rate_support)){ ?>grab_cur_rate('<?php echo $currency->currency_id; ?>','<?php echo $currency->exchange_updater; ?>','<?php echo $currency->code; ?>');<?php } else { echo "simple_message_box('$mgrlang[currencies_error1] ($currency->code)','');"; } ?>"><img src="images/mgr.tiny.star.<?php if($currency->exchange_date > $past_date){ echo "1"; } else { echo "0"; } ?>.png" id="erimg<?php echo $currency->currency_id; ?>" erimg='1' style="float: left" border="0" /></a>
                                        <div id="er<?php echo $currency->currency_id; ?>" style="font-weight: bold;" onmouseover="start_cur_panel(<?php echo $currency->currency_id; ?>,'<?php echo $currency->code; ?>')" onmouseout="cancel_cur_panel(<?php echo $currency->currency_id; ?>);"><?php if($currency->defaultcur){ echo $cleanvalues->number_display('1',4,0);; } else { echo $cleanvalues->number_display($currency->exchange_rate,4,0); } ?></div>
                                        
                                        <div id="more_info_<?php echo $currency->currency_id; ?>" style="display: none; margin: -30px -10px -10px 80px;" class="details_win">
                                            <div class="details_win_inner">
                                                <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                                <div id="more_info_<?php echo $currency->currency_id; ?>_content" style="overflow: auto; padding: 20px; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                            </div>
                                        </div>
                                        
                                   	</td>
								<?php } ?>
								<?php if(in_array('multicur',$installed_addons)){ ?><td align="center"><div id="ac<?php echo $currency->currency_id; ?>"><a href="javascript:switch_status('ac','<?php echo $currency->currency_id; ?>','<?php echo $currency->code; ?>');"><img src="images/mgr.small.check.<?php echo $currency->active; ?>.png" id="acimg<?php echo $currency->currency_id; ?>" acimg='1' border="0" /></a></div></td><?php } ?>
                                <td align="center"><div id="df<?php echo $currency->currency_id; ?>"><a href="javascript:switch_status('df','<?php echo $currency->currency_id; ?>','<?php echo $currency->code; ?>');"><img src="images/mgr.small.check.<?php echo $currency->defaultcur; ?>.png" id="dfimg<?php echo $currency->currency_id; ?>" dfimg='1' border="0" /></a></div></td>
                                <td align="center" valign="middle" nowrap>
                                    <a href="mgr.currencies.edit.php?edit=<?php echo $currency->currency_id; ?>" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /><?php echo $mgrlang['gen_short_edit']; ?></a> 
                                    <a href="javascript:deleterec(<?php echo $currency->currency_id; ?>);" class='actionlink' dellink="1" id="del<?php echo $currency->currency_id; ?>" <?php if($currency->defaultcur){ echo "style='display:none'"; } ?>><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /><?php echo $mgrlang['gen_short_delete']; ?></a>
                                </td>
                                <td align="center" valign="middle" nowrap>                                                
                                    <input type="checkbox" name="items[]" id="chk<?php echo $currency->currency_id; ?>" value="<?php echo $currency->currency_id; ?>" class="atitems" style="padding: 0px; margin: 0px 0px 0px 2px;" <?php if($currency->defaultcur){ echo "disabled='disabled'"; } ?> />
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </table>
                    </form>					
                </div>
                <?php include('mgr.perpage.php'); ?>
                <div class="footer_spacer"></div>
            <?php
                }
				else
				{
                    notice($mgrlang['gen_empty']);
                }
            ?>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>