<?php
	###################################################################
	####	MANAGER LANGUAGES PAGE                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-21-2006                                     ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "languages";
		$lnav = "settings";
		
		$supportPageID = '369';

		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		//error_reporting(0);											# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		//echo $mgrlang['gen_error_15']; exit;
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	

		//$config['mulilang']
		
		# SAVE INFORMATION
		if(!empty($_POST)){
			//echo "test"; exit;
			
			if(empty($_POST['selected_langs']) and empty($_POST['default_pub_lang'])){
				$languages = "english";
			} else {
				if(in_array("multilang",$installed_addons)){ $_POST['selected_langs'][] = $_POST['default_pub_lang']; }		
				$languages = implode(",",$_POST['selected_langs']);
				
				# GET LANGS READY TO PASS TO THE FUNCTION
				$langs = $_POST['selected_langs'];
				$langs[] = $_POST['selected_mgrlang'];
				if(in_array("multilang",$installed_addons)){ $langs[] = $_POST['default_pub_lang']; }
				$langs = array_unique($langs);
				
				//print_r($langs); exit;
				
				# CREATE THE ADDITIONAL FIELDS
				create_lang_fields($langs);		
			}
			
			if(in_array("multilang",$installed_addons) or in_array("pro",$installed_addons))
				$defaultPubLanguage = $_POST['default_pub_lang'];
			else
				$defaultPubLanguage = $_POST['selected_langs'][0];	
			
			/* // Testing
			echo $_POST['selected_mgrlang'];
			echo " - " . $defaultPubLanguage;
			exit;
			*/
			
			//echo $defaultPubLanguage; exit;
			
			$sql = "UPDATE {$dbinfo[pre]}settings SET lang_file_pub='$languages',lang_file_mgr='{$_POST[selected_mgrlang]}',default_lang='{$defaultPubLanguage}' WHERE settings_id  = '1'";
			if(!mysqli_query($db,$sql))
			{
				echo "Failed To Save";
				exit;
			}
									
			header("location: mgr.languages.php?ep=1&mes=saved");
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
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_languages']; ?></title>
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
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	
	<script language="javascript">
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
			
		});
		
		function form_sumbit(){
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = "mgr.languages.php";
			?>
				// SUBMIT THE FORM
				$('data_form').action = "<?php echo $action_link; ?>";
				$('data_form').submit();
			<?php
				}
			?>
		}
		
		// SWITCH STATUS ON HOMEPAGE OR ACTIVE
		function lang_preview(div,lang,arrow){		
			$(div).innerHTML = "<img src=\"images/mgr.loader.gif\">";
			var updatecontent = div;
			var loadpage = "mgr.lang.preview.php?lang=" + lang + "&ldir=" + div;
			var pars = "";
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			
			for(var x = 0; x<20; x++){
				if($(div + "_" + x) != null){
					$(div + "_" + x).style.display = 'none';
				}
			}
			//$(div + "_" + arrow).style.display = 'block';
		}
		
		function check_default_radio(lang_id){
			//if($(lang_id).checked == true){
				//alert(lang_id);
				$$('.checkbox').each(function(s){ s.enable(); });
				
				$(lang_id).checked=true;
				$(lang_id).disable();
				//selected_langs
			//}
		}
	</script>
	
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0" onload="shortcuts_height();" onresize="shortcuts_height();">
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
            <form name="data_form" id="data_form" method="post">
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.languages.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_languages']; ?></strong><br /><span><?php echo $mgrlang['subnav_languages_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <div id="spacer_bar"></div>
    
                <div id="button_bar">
                    <div class="subsubon" id="tab1" onclick="bringtofront('1');"><?php echo $mgrlang['languages_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('2');" id="tab2" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['languages_tab2']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['languages_f_uselang']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['languages_f_uselang_d']; ?></span></p>
                        <div style="margin: 0px 0px 0px 0px; float: left;">
						
						<table cellpadding="4" cellspacing="0" width="220" style="border: 1px solid #d8d7d7; background-color: #f7f7f7">
							<tr>
                                <?php if(in_array("multilang",$installed_addons)){ ?><td class="cth"><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['languages_default']; ?></div></div></td><?php } ?>
                                <td class="cth" align='center'><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['flag']; ?></div></div></td>
                                <td class="cth"><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['languages_lang']; ?></div></div></td>
                                <td class="cth" align='center'><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['gen_details']; ?></div></div></td>
                            </tr>
						<?php
                            
							# READ LANGUAGE FILES
                            $run_lang = explode(",",$config['settings']['lang_file_pub']);
                            $inputtype = (in_array("multilang",$installed_addons)) ? "checkbox" : "radio";
							
							//echo 'run lang: '.$config['settings']['lang_file_pub'];
                            
                            # READ LANGUAGE FILES													
                            $real_dir = realpath("../assets/languages/");
                            $dir = opendir($real_dir);													
                            $i = 0;													
                            # LOOP THROUGH THE DIRECTORY
                            while($file = readdir($dir)){
                                if($file != ".." && $file != "."){
                                    if(file_exists("../assets/languages/" . $file . "/lang.settings.php") and file_exists("../assets/languages/" . $file . "/lang.public.php")){															
                                        include("../assets/languages/" . $file . "/lang.settings.php");
                                        if($langset['active'])
										{
											echo "<tr>";
											if(in_array("multilang",$installed_addons)){
												echo "<td align='center'><input type='radio' name='default_pub_lang' id='d_".$langset['id']."' onclick=\"check_default_radio('lang_$i');\" value='".$langset['id']."' style='margin: 0; padding: 0;'";
												if($langset['id'] == $config['settings']['default_lang']){
													echo "checked";
												}
												if($config['settings']['default_lang'] == "" and $i == 0){
													echo "checked";
												}
												echo " /></td>";
											}
											echo "<td align='center'><img src='";
											if(file_exists("../assets/languages/" . $file . "/flag.png")){ echo "../assets/languages/" . $file . "/flag.png"; } else { echo "images/mgr.default.flag.png"; }
											echo "' style='width: 20px' /></td>";
											echo "<td><label for=\"lang_$i\" style='white-space: nowrap; width: auto; cursor: auto'><input type=\"$inputtype\" value=\"" . $langset['id'] . "\" name=\"selected_langs[]\" id=\"lang_$i\" class=\"$inputtype\" style='padding-left: 10px; margin: 0;' ";
											
											//echo "<div style='clear: left; float: left; padding: 4px 10px 4px 4px;'><label for=\"lang_$i\" style='margin: 0; padding: 0; float: left; white-space: nowrap; width: auto; cursor: auto'><input type=\"$inputtype\" value=\"" . $langset['id'] . "\" name=\"selected_langs[]\" id=\"lang_$i\" class=\"$inputtype\" style='padding: 0; margin: 0;' ";
											if(in_array($langset['id'],$run_lang)){ echo "checked"; }
											if($langset['id'] == $config['settings']['default_lang'] and in_array("multilang",$installed_addons)){ echo " disabled"; }
											echo " />&nbsp;&nbsp;" . $langset['name'] . "</label></td><td align='center'>(<span style='font-weight: normal;'><a href=\"javascript:lang_preview('lang_detailsp','$file','$i');\">$mgrlang[gen_details]</a></span>)\n";
											//echo "<div id='lang_detailsp_$i' style='float: left; display: none; padding-top: 2px;'><img src='images/mgr.lang.arrow.gif' /></div>";
											echo "</td></tr>";
											$i++;
										}
                                    }
                                }
                                //unset($isphp);
                            }
                            closedir($dir);
                        ?>
                        </table>
                        </div>
                        <div id="lang_detailsp" style="margin: 0px 0px 0px 4px; float: left;"></div>
                    </div>
                </div>
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group" style="display: none;">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['languages_f_uselang']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['languages_f_uselang_d2']; ?></span></p>
                        <div style="margin: 0px 0px 0px 0px; float: left;">      
						<table cellpadding="4" cellspacing="0" width="220" style="border: 1px solid #d8d7d7; background-color: #f7f7f7">
							<tr>
                                <td class="cth"><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['flag']; ?></div></div></td>
                                <td class="cth"><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['languages_lang']; ?></div></div></td>
                                <td class="cth" align='center'><div class="content_table_header" style="cursor: auto"><div><?php echo $mgrlang['gen_details']; ?></div></div></td>
                            </tr>						
						<?php
                            # READ LANGUAGE FILES													
                            $real_dir = realpath("../assets/languages/");
                            $dir = opendir($real_dir);
                            $i = 0;
                            # LOOP THROUGH THE DIRECTORY
                            while($file = readdir($dir)){
                                if($file != ".." && $file != "."){
                                    if(file_exists("../assets/languages/" . $file . "/lang.settings.php") and file_exists("../assets/languages/" . $file . "/lang.manager.php")){															
                                        include("../assets/languages/" . $file . "/lang.settings.php");
                                        //echo "<div id='lang_detailsm_$i' style='float: right; display: none; padding-top: 2px;'><img src='images/mgr.lang.arrow.gif' /></div>";
                                        
										if($langset['active'] and $langset['mgmtAreaTrans'])
										{
											echo "<tr>";
											echo "<td align='center'><img src='";
											if(file_exists("../assets/languages/" . $file . "/flag.png")){ echo "../assets/languages/" . $file . "/flag.png"; } else { echo "images/mgr.default.flag.png"; }
											echo "' style='width: 20px' /></td>";										
											echo "<td style='padding-left: 14px'><label for=\"langm_$i\" style='white-space: nowrap; width: auto; cursor: auto'><input type=\"radio\" value=\"" . $langset['id'] . "\" name=\"selected_mgrlang\" id=\"langm_$i\" class=\"radio\" ";
											if($config['settings']['lang_file_mgr'] == $langset['id']){ echo "checked"; }
												echo " />&nbsp;" . $langset['name'] . "</label></td><td align='center'>(<span style='font-weight: normal;'><a href=\"javascript:lang_preview('lang_detailsm','$file','$i');\">$mgrlang[gen_details]</a></span>)</div>\n";
												//echo "<br /><br />";
											}
											echo "</td></tr>";																
											$i++;
										}
                                    }
                                //unset($isphp);
                            }
							closedir($dir);
                        ?>
                        </table>
                        </div>
                        <div id="lang_detailsm" style="margin: 0px 0px 0px 4px; float: left;"></div>
                    </div>
                </div>
                <!-- ACTIONS BAR AREA -->
                <div id="save_bar">							
                    <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.settings.php?ep=1');" /><input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="form_sumbit();" />
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