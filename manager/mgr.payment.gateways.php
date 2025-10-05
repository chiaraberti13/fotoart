<?php
	###################################################################
	####	MANAGER PAYMENT GATEWAYS PAGE                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 8-21-2008                                    #### 
	###################################################################
		
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
		
		$page = "payment_options";
		$lnav = "settings";
	
		$supportPageID = '381';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
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
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title; ?></title>
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
		
		function deactivate_gateway(gateway_id)
		{
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
						message_box("<?php echo $mgrlang['gen_suredeact']; ?>","<input type='button' value='<?php echo $mgrlang['gen_deactivate']; ?>' id='closebutton' class='button' onclick='do_deactivate_gateway(\""+gateway_id+"\");close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_deactivate_gateway(gateway_id);";
					}
				?>
			}
		}
		
		function do_deactivate_gateway(gateway_id)
		{
			var url = 'mgr.payment.gateways.actions.php';
			var pars = 'mode=deactivate&gatewayid='+gateway_id;
			var myAjax = new Ajax.Request(url, {method: 'get', parameters: pars, onSuccess: function(transport){ transport.responseText.evalScripts(); Effect.Fade(gateway_id+'div',{ duration: 0.5 }); }});
		}
		
		function activate_gateway()
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
				var selected_value = $('gateways').options[$('gateways').selectedIndex].value;
				//alert(selected_value);
				
				$('gateways').setValue('0');
				
				//alert(selected_value);
				
				if(selected_value != '0')
				{
					var url = 'mgr.payment.gateways.actions.php';
					var pars = 'mode=activate&gatewayid='+selected_value;
					var myAjax = new Ajax.Request(url, {method: 'get', parameters: pars, onSuccess: function(transport){ transport.responseText.evalScripts(); Effect.Appear(selected_value+'div',{ duration: 0.5, from: 0.0, to: 1.0 }); }});
				}
			<?php
				}
			?>
		}
		
		function save_payment_form(gateway_id)
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
				//alert($(gateway_id+'form').serialize());
				$(gateway_id+'form').request({
					onFailure: function() { alert('failed'); }, 
					onSuccess: function(transport) {
						//alert(transport.responseText);
						transport.responseText.evalScripts();
					}
				});
			<?php
				}
			?>
		}
		
		<?php
			/*
			$gateway_mode = 'mgr_form_js';
			
			$dh = opendir ('../assets/gateways/');
			while (false !== $file = readdir ($dh))
			{
				if (is_file ('../assets/gateways/' . $file))
				{
					if (((($file != '.' and $file != '..') and $file != 'index.php') and $file != ''))
					{
						require('../assets/gateways/' . $file);
					}
				}
			}
			closedir ($dh);
			*/
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
        <div id="content_container">
			<?php
                					
            ?>            
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.payment.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_payment_options']; ?></strong><br /><span><?php echo $mgrlang['pmntgate_edit_header']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
             <!-- START CONTENT -->
            <div id="content">							
                <!--<div id="spacer_bar"></div>       		
                
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['webset_tab1-']; ?>General Settings</div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['webset_tab2-']; ?>Gateways</div>
                    <div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['webset_tab3-']; ?>Offline Payments</div>
                    <div class="subsuboff" onclick="bringtofront('4');" id="tab4" style="border-right: 1px solid #6b6b6b;"><?php echo $mgrlang['webset_tab7-']; ?>Advanced Settings</div>
                </div>
                -->
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding: 20px;">
					<?php
						$gatewayMode = 'initialize';
						
						$dh = opendir ('../assets/gateways/');
						while(false !== $file = readdir($dh))
						{
							if(is_dir('../assets/gateways/'.$file))
							{
								if(((($file != '.' and $file != '..') and $file != 'index.php') and $file != ''))
								{
									if(file_exists("../assets/gateways/{$file}/config.php") and file_exists("../assets/gateways/{$file}/functions.php"))
									{
										require_once("../assets/gateways/{$file}/config.php");
										require_once("../assets/gateways/{$file}/functions.php");
									
										if($gatewaymodule['active']) // Make sure gateway is active
										{
											if(!$gatewaymodule['proOnly'] or in_array("pro",$installed_addons))
											{
												$gatewayname[] = $gatewaymodule['displayName'];
												$gatewayid[] = $file;
											}
										}
										unset($gatewaymodule); // Clear the gatewaymodule var
									}
								}
							}
						}
						closedir ($dh);
					?>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <?php
							if(count($gatewayname) > 0)
							{
								echo "<strong>$mgrlang[pmntgate_actgate]:</strong> <select name='gateways' id='gateways' style='width: 200px;'>";
								echo "<option value='0'></option>";
								foreach ($gatewayname as $key => $gname)
								{
									if ($gname != '')
									{
										echo "<option value='$gatewayid[$key]'>$gname</option>";
										continue;
									}
								}
								
								echo "</select> <input type='button' value='$mgrlang[gen_activate]' onclick='activate_gateway();' style='margin-top: -4px;'>";
							}
							else
							{
								//
							}
						?>
                    </div>
                    
                    <?php
                    	
						# SELECT THE GATEWAYS FROM THE DB						
						$gateway_result = mysqli_query($db,"SELECT DISTINCT gateway FROM {$dbinfo[pre]}paymentgateways");
						$gateway_rows = mysqli_num_rows($gateway_result);
						while($gateway = mysqli_fetch_object($gateway_result))
						{
							$active_gateways[] = $gateway->gateway;
						}
						
						//print_r($active_gateways);
						/*echo "<script language=\"javascript\">setTimeout(\"hide_timer('vmessage')\",'5500');</script>";*/
						
						$gatewayMode = 'mgrForm';
						
						$dh = opendir ('../assets/gateways/');
						while(false !== $file = readdir ($dh))
						{
							if(is_dir('../assets/gateways/'.$file))
							{
								if(((($file != '.' and $file != '..') and $file != 'index.php') and $file != ''))
								{	
									# GRAB THE DATA FROM THE DATABASE
									$pgsetting_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}paymentgateways WHERE gateway = '$file'");
									$pgsetting_rows = mysqli_num_rows($pgsetting_result);
									while($pgsetting = mysqli_fetch_object($pgsetting_result))
										$data[$pgsetting->setting] = $pgsetting->value;
									
									$gatewaymodule['id'] = $file;
									
									if(file_exists("../assets/gateways/{$file}/config.php"))
										require("../assets/gateways/{$file}/config.php");
									
									if(file_exists("../assets/gateways/{$file}/functions.php"))
										require("../assets/gateways/{$file}/functions.php");
									
									//$display = (in_array($gatewaymodule['id'],$active_gateways)) ? 'block' : 'none';
									
									if(@in_array($gatewaymodule['id'],$active_gateways))
									{
										$display = 'block';
									}
									else
									{
										$display = 'none'; // none
									}
									
									//echo "$gatewaymodule[id]";
									
									echo "
										<form id='".$gatewaymodule['id']."form' action='mgr.payment.gateways.actions.php' method='post'>
										<input type='hidden' name='mode' value='save_form' />
										<input type='hidden' name='active' value='1' />
										<input type='hidden' name='gateway_page' value='".$gatewaymodule['filename']."' />
										<input type='hidden' name='gateway' value='".$gatewaymodule['id']."' />
										<div id='".$gatewaymodule['id']."div' style='margin-bottom: 10px; border: 1px dotted #CCC; padding: 3px; display: $display'>
											<div class='vmessage' id='vmessage_".$gatewaymodule['id']."' style='display: none;'><img src='images/mgr.notice.icon.small.gif' align='absmiddle' /> &nbsp; $mgrlang[gen_mes_changesave]</div>
											<div style='background-color: #9a9a9a; padding: 4px 10px 4px 14px; overflow: auto; border-bottom: 1px solid #FFF'><p style='font-weight: bold; text-align: center; font-size: 14px; color: #FFF;'>".$gatewaymodule['displayName']."</p></div>
											<div style='background-color: #d8d8d8; padding: 6px;  overflow: auto; border-bottom: 1px solid #FFF'>";
											if(file_exists("../assets/gateways/{$gatewaymodule[id]}/logo.png"))
											{
												echo "<p style='float: left; padding-left: 4px;'>";
												if($gatewaymodule['affLink']) echo "<a href='{$gatewaymodule[affLink]}'>";
												echo "<img src='../assets/gateways/{$gatewaymodule[id]}/logo.png' style='border: 1px solid #9a9a9a;' />";
												if($gatewaymodule['affLink']) echo "</a>";
												echo "</p>";
											}
											echo"
												<p style='float: right; margin-top: 2px;'><input type='button' value='$mgrlang[gen_deactivate]' onclick=\"deactivate_gateway('".$gatewaymodule['id']."');\" /><input type='button' style='float: right;' value='$mgrlang[gen_b_save]' onclick=\"save_payment_form('".$gatewaymodule['id']."');\" /></p>
											</div>
											";											
											if($gatewaymodule['instructions'])
											{
												echo "<div style='background-color: #EEE; padding: 4px 10px 4px 14px; overflow: auto; color: #666; font-style: italic'><img src='images/mgr.notice.icon.small2.png' style='vertical-align:middle; margin-right: 4px;' />$gatewaymodule[instructions]</div>";	
											}
											if($input)
											{
												foreach($input as $value)
												{
													echo $value;
												}
											}
									echo "
										</div>
										</form>";
									unset($data);	
									unset($input);
								}
							}
						}
						closedir ($dh);
					?>
                    
                </div>               
                                        
            </div>
            <!-- END CONTENT -->
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
		
</body>
</html>
<?php mysqli_close($db); ?>
