<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

	$page = "";

	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/db.config.php');						# INCLUDE DATABASE CONFIG FILE
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/tweak.php');							# INCLUDE THE TWEAK FILE
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE	
		
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	switch($_GET['box']){
		default:
		break;
		case "rmWorkbox":
			//echo "workbox"; // mgr.rights.managed.php
			echo "<form id='rmConfigForm' name='rmConfigForm' action='mgr.rights.managed.actions.php' method='post'>";
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[rm_pricing]}:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick=\"close_workbox();\"></p></div>";
			echo "<input type='hidden' name='licID' id='licID' value='{$_GET[licID]}' />"; // License ID
			echo "<input type='hidden' name='amode' id='rmamode' value='saveRMList' />";
			echo "<div id='wbbody' style='min-height: 60px;'>";
				echo "<div style='overflow: auto; position: relative' id='options_button_row'>";
					echo "<div class='subsubon' id='rmListButton' onclick=\"showRMCategoryContainer();\" style='border-left: 1px solid #d8d7d7'>$mgrlang[gen_pricing_scheme]</div>";
					echo "<div class='subsuboff' id='rmOptionGroupButton' onclick=\"rmEditOptionGroup('new');\" style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_new_op_grp]</div>";
					echo "<div class='subsuboff' id='rmOptionButton' onclick=\"rmEditOption('new');\" style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_new_option]</div>";
				echo "</div>";
				echo "<div class='more_options' style='width: 808px; background-image:none;' id='rmListContainer'></div>";
				echo "<div class='more_options' style='width: 808px; background-image:none; display: none; padding: 10px 0 0 0;' id='rmOptionGroupContainer'></div>";
				echo "<div class='more_options' style='width: 808px; background-image:none; display: none; padding: 10px 0 0 0;' id='rmOptionContainer'></div>";
			echo "</div>";
			
    		echo "<div id='wbfooter'><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick='close_workbox();' />";
			
			if($_SESSION['admin_user']['admin_id'] == "DEMO")
			{
				echo "<input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='demo_message2();' class='small_button' />";
			}
			else
			{
				echo "<input type='button' value='$mgrlang[gen_b_save]' class='small_button' onclick='saveRMOptions();' />";
			}
			
			echo "</div>";
			
			echo "
				<script>
					showRMCategoryContainer();
					$('rmFauxBasePrice').setValue($('rmBasePrice').value);
				</script>";
			echo "</form>";
		break;
		case "compay":
			
			require_once 'mgr.defaultcur.php';
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			// Select member details
			$memberResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '{$_GET[memID]}'");
            $memberRows = mysqli_num_rows($memberResult);
            $member = mysqli_fetch_assoc($memberResult);
			
			# GET THE MEMBERS ADDRESS
			$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '{$member[mem_id]}'");
			$address_rows = mysqli_num_rows($address_result);
			$address = mysqli_fetch_object($address_result);
			
			$country_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}countries WHERE country_id = '$address->country'");
			$country_rows = mysqli_num_rows($country_result);
			$country = mysqli_fetch_object($country_result);
			
			$state_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}states WHERE state_id = '$address->state'");
			$state_rows = mysqli_num_rows($state_result);
			$state = mysqli_fetch_object($state_result);
			
			echo "<form id='group_assign' action='' method='post' onsubmit='assign_groups();' ><input type='hidden' name='mes' value='edit' /><input type='hidden' name='action' value='save_groups' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[pay_commission]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   				echo "<div id='wbbody'>";			
					
					if($memberRows)
					{
						
						$compayOptions = explode(",",$config['settings']['compay']);
						
						$paypalEmail = ($member['paypal_email']) ? $member['paypal_email'] : $member['email'];
						
						echo "Amount: <strong>" . $cleanvalues->currency_display($_GET['amount'],1) . "</strong><br />";
						echo "Member: <strong>{$member[f_name]} {$member[l_name]}</strong><br /><br /><br />";
						
						$itemName = "{$mgrlang[com_pay_from]} - {$config[settings][site_title]}";
						
						echo "<ul class='compayOptions'>";					
							$paypalLink = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business={$paypalEmail}&currency_code={$config[settings][cur_code]}&item_name={$itemName}&item_number=commission-{$_GET[comID]}&amount=" . round($_GET['amount'],2) . "&return={$config[settings][site_url]}";
							
							echo "<li";
								if($member['compay'] == 1) echo " class='coSelected'";
							echo "><strong>{$mgrlang[pay_paypal]}</strong> &nbsp; <a href='{$paypalLink}' target='_blank' class='actionlink'>&nbsp;{$mgrlang[gen_pay]}&nbsp;</a><br /><br />({$mgrlang[mem_f_email]}: {$paypalEmail})</li>";					
							
							echo "<li";
								if($member['compay'] == 2) echo " class='coSelected'";
							echo "><strong>{$mgrlang[send_cmo]}</strong><br /><br />";
								echo "{$member[f_name]} {$member[l_name]}<br />";
								echo $address->address . "<br />";
								if($address->address_2){ echo $address->address_2 . "<br />"; }
								echo $address->city;											
								if($state_rows){ echo ", " . $state->name; }
								echo " " . $address->postal_code . "<br />";
								if($country_rows){ echo $country->name; }
								if($mgrMemberInfo->phone){ echo "<br /><br />".$mgrMemberInfo->phone; }
							echo "</li>";					
							
							echo "<li";
								if($member['compay'] == 3) echo " class='coSelected'";
							echo "><strong>";
								if($config['settings']['compay_other']) echo "{$config[settings][compay_other]}"; else echo $mgrlang['gen_other'];
								echo "</strong> <a href='mgr.website.settings.php?ep=1&jump=9'>[edit label]</a>";
							echo "</li>";					
						echo "</ul>";
					}
					else
						echo $mgrlang['no_member'];
					
				echo "</div>";
				echo "<div id='wbfooter'>";
				if($_GET['comID']) echo "<input type='button' value='$mgrlang[mark_as_paid]' class='small_button' onclick='switch_status_compay({$_GET[comID]},1);close_workbox();' />";
				echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></div>";				
			echo "</div>";			
			echo "</form>";
		break;
		
		case "test_email":
			echo "<form id='group_assign' action='' method='post' onsubmit='assign_groups();' ><input type='hidden' name='mes' value='edit' /><input type='hidden' name='action' value='save_groups' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[setup_f_test_email]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   				echo "<div id='wbbody'>";			
					
					try
					{
						kmail($config['settings']['support_email'],$config['settings']['support_email'],$config['settings']['support_email'],'Email Test','Testing Email','Your email settings seem to be working.',$options); // Send email
						echo $mgrlang['setup_te_sent'];
					}
					catch(Exception $e)
					{
						echo $e->getMessage();
					}
					
					
				echo "</div>";
				echo "<div id='wbfooter'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></div>";				
			echo "</div>";			
			echo "</form>";
		break;
		case "menuBuilder":
			$galleryResult = mysqli_query($db,"SELECT gallery_id,parent_gal FROM {$dbinfo[pre]}galleries");
			while($gallery = mysqli_fetch_array($galleryResult))
			{
				# UPDATE WITH THE ACTUAL NUMBER OF MEDIA FILES IN EACH GALLERY
				$mediaCount = mysqli_num_rows(mysqli_query($db,
				"
					SELECT {$dbinfo[pre]}media.umedia_id
					FROM {$dbinfo[pre]}media
					LEFT JOIN {$dbinfo[pre]}media_galleries 
					ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}media_galleries.gmedia_id
					WHERE {$dbinfo[pre]}media_galleries.gallery_id = {$gallery[gallery_id]}
					AND {$dbinfo[pre]}media.active = 1
					GROUP BY {$dbinfo[pre]}media.media_id
				")); // Get the total number of items			
				
				mysqli_query($db,"UPDATE {$dbinfo[pre]}galleries SET gallery_count='{$mediaCount}' WHERE gallery_id = '{$gallery[gallery_id]}'");
			}
		
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[util_f_ugmc]}:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody' style='min-height: 60px;'>";
			
				echo "<div style='margin: 10px 22px 0px 22px;'>{$mgrlang[gen_error_29c]}</div>";
			
			echo "</div>";
    		echo "<div id='wbfooter'><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick='close_workbox();' /></div>";
		break;
		case "memselect":
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_item_for]:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";
			
			echo "<div style='margin: 0px 22px 0px 22px;'>";
				echo "<input type='radio' name='uperm' id='uperm_everyone' onclick=\"unload_wbselector();$('perm'+wboxid).value = 'everyone';\" checked /> <label for='uperm_everyone'>$mgrlang[gen_wb_everyone]</label><br />";
				echo "<input type='radio' name='uperm' id='uperm_specific' onclick=\"load_wbselector('$_GET[inputbox]');\" /> <label for='uperm_specific'>$mgrlang[gen_wb_specific]</label><br />";		
			
				echo "<div id='wbmembers' class='wbselector' style='width: 300px;'><p align='center'>$mgrlang[gen_wb_members]</p>";
					echo "<div id='wbmembers_inner' class='wbselector_inner' style='height: 171px'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div><p style='text-align: center; height: 18px; font-size: 10px; font-weight: normal; border-top: 1px solid #ffffff; border-bottom: none; background-image: url(images/mgr.tabarea.fade2.gif); background-repeat: repeat-x;'>";
						$x=0;
						//$alphabet = explode(",",$mgrlang['alphabet']);
						
						$alphabet = array();
							
						$memlet_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members");
						while($memlet = mysqli_fetch_object($memlet_result)){
							$trimmed_lname = strtoupper(substr($memlet->l_name,0,1));
							if(!in_array($trimmed_lname,$alphabet)) $alphabet[] = $trimmed_lname;
						}
						
						sort($alphabet);
						
						foreach($alphabet as $value){
							//if($x==0){
							//	echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value'  class='alphabet_on'>$value</a>";
							//} else {
								echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value' class='alphabet_off'>$value</a>";
							//}
							$x++;
						}
				echo "</p></div>";				
				echo "<div id='wbgroups' class='wbselector'><p align='center'>$mgrlang[gen_wb_groups]</p>";
				
					echo "<div id='wbgroups_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";				
				echo "</div>";
				
				echo "<div id='wbmemberships' class='wbselector'><p align='center'>$mgrlang[gen_wb_memberships]</p>";
				
					echo "<div id='wbmemberships_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";
				
				echo "</div>";
			
			echo "</div>";
			
			echo "</div>";
    		echo "<div id='wbfooter'><p><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick='close_workbox(); check_perm_box();' /></p></div>";
			echo "<script language='javascript' type='text/javascript'>wbselect();</script>";
		break;
		
		case "permissions_selector":
			$passobj = "{inputbox: '$_GET[inputbox]', multiple: '$_GET[multiple]', updatenamearea: '$_GET[updatenamearea]'}";
			
			echo "<div id='wbheader'><p style='float: left'>$mgrlang[gen_item_for]:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
			
   			echo "<div id='wbbody'>";
			
			echo "<div style='margin: 0px 22px 0px 22px;'>";
				switch($_GET['style'])
				{
					case "everyone": // MEMBERS MODE WITH EVERYONE OPTION
						echo "<input type='radio' name='uperm' id='uperm_everyone' onclick=\"unload_wbselector();\$('$_GET[inputbox]').setValue('everyone');\" checked /> <label for='uperm_everyone'>$mgrlang[gen_wb_everyone]</label><br />";
						echo "<input type='radio' name='uperm' id='uperm_specific' onclick=\"load_wbselector($passobj);\" /> <label for='uperm_specific'>$mgrlang[gen_wb_specific]</label><br />";
						
						echo "<script language='javascript' type='text/javascript'>wbselect($passobj);</script>";
					break;
				}
				
				echo "<div id='wbmembers' class='wbselector' style='width: 300px;'><p align='center'>$mgrlang[gen_wb_members]</p>";
					echo "<div id='wbmembers_inner' class='wbselector_inner' style='height: 171px'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div><p style='text-align: center; height: 18px; font-size: 10px; font-weight: normal; border-top: 1px solid #ffffff; border-bottom: none; background-image: url(images/mgr.tabarea.fade2.gif); background-repeat: repeat-x;'>";
						$x=0;
						//$alphabet = explode(",",$mgrlang['alphabet']);
						
						$alphabet = array();
							
						$memlet_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members");
						while($memlet = mysqli_fetch_object($memlet_result)){
							$trimmed_lname = strtoupper(substr($memlet->l_name,0,1));
							if(!in_array($trimmed_lname,$alphabet)) $alphabet[] = $trimmed_lname;
						}
						
						sort($alphabet);
						
						foreach($alphabet as $value){
							//if($x==0){
							//	echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value'  class='alphabet_on'>$value</a>";
							//} else {
								echo "<a href=\"javascript:load_wbmembers_inner('$value',$passobj);\" id='sl_$value' class='alphabet_off'>$value</a>";
							//}
							$x++;
						}
				echo "</p></div>";				
				echo "<div id='wbgroups' class='wbselector'><p align='center'>$mgrlang[gen_wb_groups]</p>";
				
					echo "<div id='wbgroups_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";				
				echo "</div>";
				
				echo "<div id='wbmemberships' class='wbselector'><p align='center'>$mgrlang[gen_wb_memberships]</p>";
				
					echo "<div id='wbmemberships_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
			
			echo "</div>";
    		echo "<div id='wbfooter'><p><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick=\"close_workbox(); check_perm_box('$_GET[inputbox]');\" /></p></div>";
		break;
		
		case "member_selector":
			$passobj = "{inputbox: '$_GET[inputbox]', multiple: '$_GET[multiple]', updatenamearea: '$_GET[updatenamearea]'}";
			
			echo "<div id='wbheader'><p style='float: left;'>";			
				
				echo $mgrlang['mem_selector'];
				
			echo ":</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
			
   			echo "<div id='wbbody'>";
			
			echo "<div style='margin: 0px 22px 0px 22px;'>";
				switch($_GET['style'])
				{
					case "gal_owner":
						echo "<input type='radio' name='uperm' id='uperm_everyone' onclick=\"unload_wbselector_owner('".$config['settings']['business_name']."','$_GET[inputbox]');\$('$_GET[inputbox]').setValue('0');\" checked /> <label for='uperm_everyone'>".$config['settings']['business_name']."</label><br />";
						echo "<input type='radio' name='uperm' id='uperm_specific' onclick=\"load_wbselector($passobj);\" /> <label for='uperm_specific'>{$mgrlang[gen_member]}</label><br />";
						echo "<script language='javascript' type='text/javascript'>wbselect_owner($passobj);</script>";	
					break;
					case "guest": // MEMBERS MODE WITH GUEST OPTION
						echo "<input type='radio' name='uperm' id='uperm_everyone' onclick=\"unload_wbselector_owner('<strong>".$mgrlang['gen_visitor']."</strong>','$_GET[inputbox]');\$('$_GET[inputbox]').setValue('0');\" checked /> <label for='uperm_everyone'>".$mgrlang['gen_visitor']."</label><br />";	
						echo "<input type='radio' name='uperm' id='uperm_specific' onclick=\"load_wbselector($passobj);\" /> <label for='uperm_specific'>{$mgrlang[gen_member]}</label><br />";
						echo "<script language='javascript' type='text/javascript'>wbselect_owner($passobj);</script>";	
					break;
					case "membersonly": // MEMBERS MODE WITH NO OTHER OPTIONS
						echo "<script>load_wbselector($passobj);</script>";	
					break;
					//case "everyone": // MEMBERS MODE WITH EVERYONE OPTION
					//	echo "<input type='radio' name='uperm' id='uperm_everyone' onclick=\"unload_wbselector_owner('".$mgrlang['gen_visitor']."Everyone');$('perm'+wboxid).value = '0';\" checked /> <label for='uperm_everyone'>".$mgrlang['gen_visitor']."</label><br />";	
					//	echo "<input type='radio' name='uperm' id='uperm_specific' onclick=\"load_wbselector($passobj);\" /> <label for='uperm_specific'>Member</label><br />";
					//break;
				}
				
				echo "<div id='wbmembers' class='wbselector' style='width: 690px'><p align='center'>$mgrlang[gen_wb_members]</p>";
					echo "<div id='wbmembers_inner' class='wbselector_inner' style='height: 171px'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div><p style='text-align: center; height: 18px; font-size: 10px; font-weight: normal; border-top: 1px solid #ffffff; border-bottom: none; background-image: url(images/mgr.tabarea.fade2.gif); background-repeat: repeat-x;'><strong>{$mgrlang[mem_f_lname]}:</strong>";
						$x=0;
						//$alphabet = explode(",",$mgrlang['alphabet']);
						
						$alphabet = array();
							
						$memlet_result = mysqli_query($db,"SELECT l_name FROM {$dbinfo[pre]}members");
						while($memlet = mysqli_fetch_object($memlet_result)){
							$trimmed_lname = strtoupper(substr($memlet->l_name,0,1));
							if(!in_array($trimmed_lname,$alphabet)) $alphabet[] = $trimmed_lname;
						}
						
						sort($alphabet);
						
						foreach($alphabet as $value){
							//if($x==0){
							//	echo "<a href=\"javascript:load_wbmembers_inner('$value');\" id='sl_$value'  class='alphabet_on'>$value</a>";
							//} else {
								echo "<a href=\"javascript:load_wbmembers_inner('$value',$passobj);\" id='sl_$value' class='alphabet_off'>$value</a>";
							//}
							$x++;
						}
				echo "</p></div>";
				
				/*
				echo "<div id='wbgroups' class='wbselector'><p align='center'>$mgrlang[gen_wb_groups]</p>";
				
					echo "<div id='wbgroups_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";				
				echo "</div>";
				
				echo "<div id='wbmemberships' class='wbselector'><p align='center'>$mgrlang[gen_wb_memberships]</p>";
				
					echo "<div id='wbmemberships_inner' class='wbselector_inner'>";
						echo "<img src='./images/mgr.loader.gif' align='absmiddle' style='margin: 10px;' />";
					echo "</div>";
				
				echo "</div>";
				*/
			echo "</div>";
			
			echo "</div>";
    		echo "<div id='wbfooter'><p><input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick=\"close_workbox();";
			# RESET THE PARENT GALLERY IF THE OWNER CHANGES
			if($_GET['style'] == 'gal_owner'){ echo "$('parentgal_override').setValue('0');"; }
			echo "\" /></p></div>";
			/*echo "<script language='javascript' type='text/javascript'>wbselect_owner();</script>";*/
		break;
		case "assign_groups":
			/*echo "<script language='javascript'>$('group_assign').action=workboxobj.filename;</script>";*/
			echo "<form id='group_assign' action='' method='post' onsubmit='assign_groups();' ><input type='hidden' name='mes' value='edit' /><input type='hidden' name='action' value='save_groups' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_togroups]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   				echo "<div id='wbbody'>";			
				echo "<div style='overflow: auto; position: relative'>";
                    echo "<div class='subsubon' id='assign_b' style='border-left: 1px solid #d8d7d7;'><input type='radio' name='amode' value='assign' id='assign' onclick=\"assignbutton();\" checked />&nbsp;<label for='assign'>$mgrlang[gen_b_assign]</label></div>";
                    echo "<div class='subsuboff' id='unassign_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='amode' value='unassign' id='unassign' onclick=\"unassignbutton();\" />&nbsp;<label for='unassign'>$mgrlang[gen_b_unassign]</label></div>";
                echo "</div>";
                echo "<div class='more_options' style='width: 808px' id='ship_regional'>";
					//echo "<div style='margin: 0px 22px 0px 20px;'>";
						//echo "<span class='boldtext'>&nbsp;&nbsp;&nbsp;</span>";
						switch($_REQUEST['page']){
							case "digital_sp":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'digital_sp' ORDER BY name"); 
							break;
							case "news":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'news' ORDER BY name");
							break;
							case "memberships":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'memberships' ORDER BY name");
							break;
							case "prints":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'prints' ORDER BY name");
							break;
							case "products":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'products' ORDER BY name");
							break;
							case "packages":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'packages' ORDER BY name");
							break;
							case "shipping":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'shipping' ORDER BY name");
							break;
							case "storage":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'storage' ORDER BY name");
							break;
							case "folders":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'folders' ORDER BY name");
							break;
							case "media_types":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'media_types' ORDER BY name");
							break;
							case "collections":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'collections' ORDER BY name");
							break;
							case "countries":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'countries' ORDER BY name");
							break;
							case "subscriptions":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'subscriptions' ORDER BY name");
							break;
							case "promotions":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'promotions' ORDER BY name");
							break;
							case "credits":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'credits' ORDER BY name");
							break;
							case "media":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'media' ORDER BY name");
							break;
							case "lightboxes":
								$group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'lightboxes' ORDER BY name");
							break;
						}
						echo "<input style='margin-left: 10px' type='radio' name='assign_to' value='selitems' id='selitems' checked /> <label for='selitems'>$mgrlang[gen_selitems]</label><br /><input type='radio' style='margin-left: 10px' name='assign_to' id='all_items' value='all' /> <label for='all_items'>$mgrlang[gen_allitems]</label><br /><br />";
						echo "<span class='boldtext'><span id='tofrom'>$mgrlang[gen_to]</span> $mgrlang[gen_tfg]:</span><br /><br />";				
						$group_rows = mysqli_num_rows($group_result);
						while($group = mysqli_fetch_object($group_result)){
							echo "<p style='margin-left: 10px; '><input type='checkbox' name='assigngroups[]' id='at_$group->gr_id' value='$group->gr_id' class='atgroups' /><label for='at_$group->gr_id'> "; if($group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$group->flagtype' align='absmiddle' /> "; } echo substr($group->name,0,30)."</label></p>";
						}
						echo "</br /><div id='clear_prev_div'><input type='checkbox' name='clear_prev' id='clear_prev' /> <label for='clear_prev'>$mgrlang[gen_clear_groups]</label></div>";
						//echo "<p style='float: right'><input type='submit' value='$mgrlang[gen_b_assign]' id='dobutton' class='small_button' /><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></p>";
				echo "</div>";
				echo "</div>";
				echo "<div id='wbfooter'>";
				echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' />";
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "<input type='button' value='$mgrlang[gen_b_assign]' id='dobutton' onclick='demo_message2();' class='small_button' />";
				}
				else
				{
					echo "<input type='submit' value='$mgrlang[gen_b_assign]' id='dobutton' class='small_button' />";
				}
				echo "</div>";
			echo "</div>";			
			echo "</form>";
		break;
		case "sort_items":
			/*echo "<script language='javascript'>$('sort_assign').action=workboxobj.filename;</script>";*/
			echo "<form id='sort_assign' action='' method='post' onsubmit='make_sortlist();'><input type='hidden' name='action' value='save_sort' /><input type='hidden' id='sort_list_vals' name='sort_list_vals' value='' /><input type='hidden' name='listby' value='sortorder' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[currencies_wb_uptxx]Sorting</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
			//echo "<div id='wbheader'><p style='float: right'><img src='images/mgr.button.info.gif' style='cursor: pointer' border='0' alt='$mgrlang[gen_info]' onclick='support_popup(workboxobj.supportid);' /></p><p style='float: left'>";
			# SELECT THE CORRECT DB AND INFO
			switch($_REQUEST['page']){
				case "digital_sp":
					$items_result = mysqli_query($db,"SELECT name,ds_id FROM {$dbinfo[pre]}digital_sizes WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'name';
					$item_id = 'ds_id'; 
				break;
				case "news":
					$items_result = mysqli_query($db,"SELECT title,news_id FROM {$dbinfo[pre]}news ORDER BY sortorder");
					$item_name = 'title';
					$item_id = 'news_id';
				break;
				case "memberships":
					$items_result = mysqli_query($db,"SELECT name,ms_id FROM {$dbinfo[pre]}memberships WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'name';
					$item_id = 'ms_id'; 
				break;
				case "prints":
					$items_result = mysqli_query($db,"SELECT item_name,print_id FROM {$dbinfo[pre]}prints WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'item_name';
					$item_id = 'print_id';
				break;
				case "products":
					$items_result = mysqli_query($db,"SELECT item_name,prod_id FROM {$dbinfo[pre]}products WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'item_name';
					$item_id = 'prod_id';
				break;
				case "packages":
					$items_result = mysqli_query($db,"SELECT item_name,pack_id FROM {$dbinfo[pre]}packages WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'item_name';
					$item_id = 'pack_id';
				break;
				case "shipping":
					$items_result = mysqli_query($db,"SELECT title,ship_id FROM {$dbinfo[pre]}shipping WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'title';
					$item_id = 'ship_id';
				break;
				case "collections":
					$items_result = mysqli_query($db,"SELECT item_name,coll_id FROM {$dbinfo[pre]}collections WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'item_name';
					$item_id = 'coll_id';
				break;
				case "subscriptions":
					$items_result = mysqli_query($db,"SELECT sub_id,item_name FROM {$dbinfo[pre]}subscriptions WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'item_name';
					$item_id = 'sub_id';
				break;
				case "promotions":
					$items_result = mysqli_query($db,"SELECT promo_id,name FROM {$dbinfo[pre]}promotions WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'name';
					$item_id = 'promo_id';
				break;
				case "credits":
					$items_result = mysqli_query($db,"SELECT credit_id,name FROM {$dbinfo[pre]}credits WHERE deleted = '0' ORDER BY sortorder");
					$item_name = 'name';
					$item_id = 'credit_id';
				break;
			}
			$items_rows = mysqli_num_rows($items_result);
			
			//echo "</p></div>";
   			echo "<div id='wbbody'>";
				//if($items_rows > 1){ echo "<div style='margin: 0px 10px 0px 10px;'><span class='boldtext'>$mgrlang[gen_h_dd]</span><br /><br /></div>"; }
				echo "<div style='margin: 0px 22px 0px 20px;'><span class='boldtext'>$mgrlang[gen_h_dd]</span><br /><br />";
				echo "<div id='sortlist' style='margin: 0px 22px 0px 26px;'>";
				if($items_rows > 1){
					while($items = mysqli_fetch_object($items_result)){
						# CHECK FOR OTHER LANGUAGES
						if($items->{$item_name . "_" . $config['settings']['lang_file_mgr']}){
							$name = $items->{$item_name . "_" . $config['settings']['lang_file_mgr']};
						} else {
							$name = $items->{$item_name};
						}
						
						echo "<div style='margin-left: 10px; cursor: pointer;' class='sortlist_items' id='sortlist_".$items->{$item_id}."'><img src='images/mgr.updown.arrow.png' align='absmiddle' style='margin: 1px;' /> &nbsp;".substr($name,0,30)."</div>";
					}
				} else {
					echo "<div style='font-weight: bold; color: #ff0000; text-align: center; padding: 40px 0px 50px 0px;'><img src='images/mgr.notice.icon.small.png' align='absmiddle' /> $mgrlang[gen_gmes_3]</div>";
				}
				echo "</div>";
				echo "<p style='float: right'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /><input type='button' value='$mgrlang[gen_b_clear_sort]' onclick='clear_sorts();' class='small_button' /><input type='submit' value='$mgrlang[gen_b_save]' class='small_button' /></p>";
			echo "</div>";
			echo "</div>";
			echo "</form>";
	?>
    		<script language="javascript">
				Sortable.create('sortlist',
				{tag:'div',only: 'sortlist_items', overlap:'vertical',constraint:'vertical',dropOnEmpty:true, 
					onUpdate:function()
						{
							//update_panels();
							//alert('test');
						}
				});
			</script>
    <?php
		break;
		case "print_box":
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_item_fordss]Print...</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
			echo "<div style='margin: 0px 22px 0px 30px;'>";
			switch($_REQUEST['page']){
				default:
				case "administrators":
					echo "<input type='radio' style='margin-left: 10px' name='pages' id='allpages' checked /> <label for='allpages'>$mgrlang[gen_print_all]</label><br /><input type='radio' style='margin-left: 10px' name='pages' id='thispage' /> <label for='thispage'>$mgrlang[gen_print_this]</label>";
				break;
			}
			echo "</div>";
			echo "</div>";
			echo "<div id='wbfooter'><p><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /><input type='button' value='$mgrlang[gen_b_print]' class='small_button' onclick='do_printing();' /></p></div>";
		break;		
		case "install_shipping_mod":
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[add_ship_mod]}</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   				echo "<div id='wbbody'>";			
					echo "<div style='margin: 0px 22px 0px 20px;'><span class='boldtext'>$mgrlang[gen_setyy]DHL/FedEX/UPS/USPS</span><br /><br />";
					echo "<p style='float: right'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></p>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		break;		
		case "set_status":
			echo "<form id='status_assign' action='' method='post' onsubmit='assign_status();' ><input type='hidden' name='action' value='save_status' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_tostatus]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
				echo "<div style='overflow: auto; position: relative'>";
                    echo "<div class='subsubon' id='active_b' style='border-left: 1px solid #d8d7d7;'><input type='radio' name='status' value='1' id='active' onclick=\"activebutton();\" checked /> <label for='active'>$mgrlang[gen_active]</label></div>";
                    echo "<div class='subsuboff' id='inactive_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='status' id='inactive' onclick=\"inactivebutton();\" value='0' /> <label for='inactive'>$mgrlang[gen_inactive]</label></div>";
                echo "</div>";
                echo "<div class='more_options' style='background-position:top; width: 808px' id='ship_regional'>";
					echo "<input style='margin-left: 10px' type='radio' name='set_to' value='selitems' id='selitems' checked /> <label for='selitems'>$mgrlang[gen_selitems]</label><br /><input type='radio' style='margin-left: 10px' name='set_to' id='all_items' value='all' /> <label for='all_items'>$mgrlang[gen_allitems]</label><br /><br />";
				echo "</div>";				
			echo "</div>";
			echo "<div id='wbfooter'>";
				echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' />";	
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "<input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='demo_message2();' class='small_button' />";
				}
				else
				{
					echo "<input type='submit' value='$mgrlang[gen_b_save]' id='dobutton' class='small_button' />";
				}			
			echo "</div>";
			echo "</form>";
		break;
		case "approve_media":
			echo "<form id='contr_status_assign' action='' method='post' onsubmit='assign_cmedia_status();' ><input type='hidden' name='action' value='contr_save_status' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[approve_media]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
				echo "<div style='overflow: auto; position: relative'>";
                    echo "<div class='subsubon' id='active_b' style='border-left: 1px solid #d8d7d7;'><input type='radio' name='status' value='1' id='active' onclick=\"activebutton();\" checked /> <label for='active'>$mgrlang[gen_approved]</label></div>";
                    echo "<div class='subsuboff' id='inactive_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='status' id='inactive' onclick=\"inactivebutton();\" value='2' /> <label for='inactive'>$mgrlang[gen_failed]</label></div>";
                echo "</div>";
                echo "<div class='more_options' style='background-position:top; width: 808px' id='ship_regional'>";
					echo "<input style='margin-left: 10px' type='radio' name='set_to' value='selitems' id='selitems' checked /> <label for='selitems'>$mgrlang[gen_selitems]</label><br /><input type='radio' style='margin-left: 10px' name='set_to' id='all_items' value='all' /> <label for='all_items'>$mgrlang[gen_allitems]</label><br /><br />";
				echo "</div>";				
			echo "</div>";
			echo "<div id='wbfooter'>";
				echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' />";	
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "<input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='demo_message2();' class='small_button' />";
				}
				else
				{
					echo "<input type='submit' value='$mgrlang[gen_b_save]' id='dobutton' class='small_button' />";
				}			
			echo "</div>";
			echo "</form>";
		break;
		case "set_approved":
			echo "<form id='status_assign' action='' method='post' onsubmit='assign_approved();' ><input type='hidden' name='action' value='save_approved_status' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_toapproved]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
				echo "<div style='overflow: auto; position: relative'>";
                    echo "<div class='subsubon' id='active_b' style='border-left: 1px solid #d8d7d7'><input type='radio' name='status' value='1' id='active' onclick=\"activebutton();\" checked /> <label for='active'>$mgrlang[gen_approved]</label></div>";
                    echo "<div class='subsuboff' id='inactive_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='status' id='inactive' onclick=\"inactivebutton();\" value='0' /> <label for='inactive'>$mgrlang[gen_unapproved]</label></div>";
                echo "</div>";
                echo "<div class='more_options' style='background-position:top; width: 808px' id='ship_regional'>";
					echo "<input style='margin-left: 10px' type='radio' name='set_to' value='selitems' id='selitems' checked /> <label for='selitems'>$mgrlang[gen_selitems]</label><br /><input type='radio' style='margin-left: 10px' name='set_to' id='all_items' value='all' /> <label for='all_items'>$mgrlang[gen_allitems]</label><br /><br />";
				echo "</div>";				
			echo "</div>";
			echo "<div id='wbfooter'>";
				echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' />";		
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "<input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='demo_message2();' class='small_button' />";
				}
				else
				{
					echo "<input type='submit' value='$mgrlang[gen_b_save]' id='dobutton' class='small_button' />";
				}		
			echo "</div>";
			echo "</form>";
		break;
		case "update_exchange_rates":
			//echo "<form id='status_assign' action='' method='post' onsubmit='assign_status();' ><input type='hidden' name='action' value='save_status' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[currencies_wb_upt]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
			echo "<div style='margin: 0px 10px 0px 10px;'><span class='boldtext'>$mgrlang[currencies_wb_upd]</span><br /><br /></div>";
			echo "<div id='wbox_updates' style='margin: 0px 10px 0px 10px;'></div>";
			echo "<p style='float: right'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='stopbatch=1;close_workbox();' /><input type='submit' value='$mgrlang[gen_b_update]' id='dobutton' onclick='";
			if($_SESSION['admin_user']['admin_id'] == "DEMO")
			{
				echo "demo_message2();";
			}
			else
			{
				echo "prefill_wbox_updates();update_batch_er(0);";
			}
			echo "' class='small_button' /></p></div>";
			echo "</div>";
		break;
		case "addMemSub":
			echo "<form id='subSelection' method='POST' action='mgr.member.actions.php'>";
			echo "<input type='hidden' name='pmode' value='addMemSub' />";
			echo "<input type='hidden' name='memID' value='{$_GET[memID]}' />";
			echo "<input type='hidden' name='pass' value='".md5($config['settings']['serial_number'])."' />";
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[add_sub]}</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";			
			echo "<div style='margin: 0px 10px 0px 10px;'>{$mgrlang[select_sub_mes]}";
				echo "<ul style='margin-top: 10px;'>";
				$subResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}subscriptions WHERE deleted = 0");
				while($sub = mysqli_fetch_assoc($subResult))
				{
					echo "<li><input name='newMemSub' type='radio' value='$sub[sub_id]}' id='newMemSub{$sub[sub_id]}' /> <label for='newMemSub{$sub[sub_id]}'>{$sub[item_name]}</label></li>";	
				}
				echo "</ul>";
			echo "</div>";
			echo "<p style='float: right'><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='stopbatch=1;close_workbox();' /><input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='";
			if($_SESSION['admin_user']['admin_id'] == "DEMO")
			{
				echo "demo_message2();";
			}
			else
			{
				echo "addMemSub();";
			}
			echo "' class='small_button' /></p></div>";
			echo "</div>";
			echo "</form>";
		break;
		case "encfiles":
			# COUNT TOTAL MEDIA FILES
			
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_item_fordss]Encrypt/Decrypt...</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
				echo "<div id='wbbody'>";			
					echo "<div style='margin: 0px 22px 0px 30px; height: 200px;'>";
						
						echo "<div id='enclist'></div>";
					echo "</div>";
				echo "</div>";
			echo "<div id='wbfooter'><p><input type='button' value='{$mgrlang[start]}' class='small_button' onclick='close_workbox();' /><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></p></div>";
		break;
		case "encrypt":
			
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[enc_decry]}...</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
				echo "<div id='wbbody'>";			
					echo "<div style='margin: 0px 22px 0px 30px; min-height: 100px; font-size: 12px;'>";
						if($_REQUEST['page'] == 'encfolders'){
							# COUNT TOTAL GALLERIES
							echo "<img src='images/mgr.lock.icon.png' align='absmiddle' border='0' /> Folder name encryption is currenty <strong>OFF</strong>. Click the 'Start' button to turn it <strong>ON</strong> and start the encryption of all gallery folder names.<br />";
						} else {
							# COUNT TOTAL FILES
							echo "<img src='images/mgr.lock.icon.png' align='absmiddle' border='0' /> File name encryption is currenty <strong>OFF</strong>. Click the 'Start' button to turn it <strong>ON</strong> and start the encryption of all media file names.<br />";
						}
		?>
        				<script type="text/javascript" language="javascript">
							current_file_number=1;
							replacement_files = Array();
							replacement_files[0] = "";
							<?php
								/*
								if($modfiles){
									$x=1;
									foreach($modfiles as $value){
										echo "replacement_files[$x] = '$value';";
										$x++;
									}
								}
								*/
								for($x=1;$x<1000;$x++){
									echo "replacement_files[$x] = '$x';";
								}
							?>
						</script>
                                                
                        <div id='enclist' style="border: 1px solid #8b8b8b; overflow: auto; margin: 10px 0 10px 0; display: none;">
                        	<div id="current_file_window" style="border-bottom: 1px solid #CCCCCC; padding: 3px 5px 3px 5px; display: none; background-color: #e2eaf2; font-weight: 11px;">&nbsp;</div>
                            <div style="height: 100px; overflow: auto; padding: 5px; background-color: #ffffff;" id="file_window">
                            	
                            </div>
                            <div id="status_bar" style="display: none; padding: 4px 4px 2px 4px; background-color: #868686; border-bottom: 1px solid #666666; border-top: 1px solid #666666; height: 16px; color: #ffffff;">
                                <div id="files_processed" style="float: left;"></div>
                                <div style="float: left; width: 20px;" align="center"><img src="images/mgr.isb.div.gif" /></div>
                                <div id="time_calc" style="float: left;"></div>
                                <div style="float: left; width: 20px;" align="center"><img src="images/mgr.isb.div.gif" /></div>
                                <div style="width: 150px; border: 1px solid #5a5a5a; background-color: #5a5a5a; height: 10px; float: left;"><div id="progress_bar" style="width: 0%; height: 10px; background-image: url(images/mgr.loader3.gif); background-repeat: repeat-x"></div></div>
                                <div id="show_perc" style="float: left; padding: 0px 4px 0px 4px;"></div>
                            </div>
                        </div>
        <?php
					echo "</div>";
				echo "</div>";
			echo "<div id='wbfooter'><p><input type='button' value='$mgrlang[gen_b_cancel2s]Start' class='small_button' id='start_button' onclick='start_encryption();encrypt_files();' /><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' id='cancel_button' onclick='close_workbox();' /></p></div>";
		break;
		case "set_regions":
			echo "<form id='status_assign' action='' method='post' onsubmit='assign_regions();' ><input type='hidden' name='action' value='save_regions' /><input type='hidden' id='selected_items' name='selected_items' value='' />";
			echo "<div id='wbheader'><p style='float: left;'>$mgrlang[gen_toregions]</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
   			echo "<div id='wbbody'>";
				echo "<div style='overflow: auto; position: relative'>";
                    echo "<div class='subsubon' id='assign_b'><input type='radio' name='amode' value='assign' id='assign' onclick=\"rassignbutton();\" checked />&nbsp;<label for='assign'>$mgrlang[gen_b_assign]</label></div>";
                    echo "<div class='subsuboff' id='unassign_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='amode' value='unassign' onclick=\"runassignbutton();\" id='unassign' />&nbsp;<label for='unassign'>$mgrlang[gen_b_unassign]</label></div>";
                echo "</div>";
				echo "<div class='more_options' style='width: 808px' id='global'>";
					//echo "Select the regions to assign/unassign:<br /><br />";
					echo "<input style='margin-left: 10px' type='radio' name='assign_to' value='selitems' id='selitems' checked /> <label for='selitems'>$mgrlang[gen_selitems]</label><br /><input type='radio' style='margin-left: 10px' name='assign_to' id='all_items' value='all' /> <label for='all_items'>$mgrlang[gen_allitems]</label><br /><br />";
					echo "<span class='boldtext'><span id='tofrom'>$mgrlang[gen_to]</span> $mgrlang[gen_tfr]:</span><br /><br />";
		?>
        		<span id="global_option_span"><input type="radio" name="region" value="1" id="global_option" style='margin-left: 10px' onclick="regiontype_boxes('global')" checked="checked" /> <label for="global_option"><?php echo $mgrlang['shipping_f_regions_op1']; ?></label><br /></span>
                <span><input type="radio" name="region" value="2" id="regional_option" style='margin-left: 10px' onclick="regiontype_boxes('regional')" /> <label for="regional_option"><?php echo $mgrlang['shipping_f_regions_op2']; ?></label></span>
                
                <div style="border-left: 1px solid #eee; border-top: 1px solid #eee; border-right: 1px solid #d2d2d2; border-bottom: 1px solid #d2d2d2; margin-top: 10px; display: none; height: 200px; overflow: auto; padding-left: 25px; background-image: none; background-color: #FFF" id="regions" class='fs_row_off'></div>
                <div style='padding: 10px 0 0 10px;' id='regions_clear'><input type='checkbox' name='clear_prev' id='clear_prev' value='1' /> <label for='clear_prev'><?php echo $mgrlang['clear_regions']; ?></label></div>
            </div>
		<?php
			//echo "<input style='margin-left: 10px' type='radio' name='region' value='1' id='ship_global_option' onclick='shiptype_boxes(\"global\")' checked /> <label for='ship_global_option'>$mgrlang[shipping_f_regions_op1]</label><br />";
			//echo "<input type='radio' style='margin-left: 10px' name='region' value='2' id='ship_regional_option' onclick='shiptype_boxes(\"regional\")' /> <label for='ship_regional_option'>$mgrlang[shipping_f_regions_op2]</label>";
			//echo "<div style='backgroud-color: none; margin: 0; padding-left: 24px; height: 400px; overflow: auto; display: none;' id='shipping_regions' class='fs_row_off'>test</div>";
			//echo "<div style='text-align: right; margin: 0 0 0 11px; padding-top: 10px; display: none;' id='shipping_regions_clear'><input type='checkbox' /> Clear All Previously Assigned Regions</div>";
			//echo "<p style='clear: both; float: right; margin-top: 20px;'>";
			
			echo "</div>";
			echo "<div id='wbfooter'>";
			echo "<input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' />";			
			if($_SESSION['admin_user']['admin_id'] == "DEMO")
			{
				echo "<input type='button' value='$mgrlang[gen_b_save]' id='dobutton' onclick='demo_message2();' class='small_button' />";
			}
			else
			{
				echo "<input type='submit' value='$mgrlang[gen_b_save]' id='dobutton' class='small_button' />";
			}
			echo "</div>";
			echo "</form>";
		break;
		
		case "plupload":
			
			# SET THE DEFAULT WIDTH AND HEIGHT
			$uploader_width = "100%";
			$uploader_height = "430";
			
			# FIND THE MAXIMUM FILESIZE PHP WILL ALLOW
			$upload_max_filesize = (str_replace('M','',ini_get('upload_max_filesize'))*1024)*1024;
			
			# SELECT WHICH DETAILS TO USE FOR THE UPLOAD
			switch($_GET['page'])
			{
				default:
				case "add_media":
					$workbox_title = "{$mgrlang[batch_upload]}:";
					
					foreach(getAlldTypeExtensions() as $value)
					{
						$allowedFiletypes .= "{$value},";
					}
					
					$uploadURL = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=add_media_plupload";
					
				break;
				case "item_photos":
					$workbox_title = "{$mgrlang[upload_photos]}:";
					
					$uploadURL = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=item_photos_plupload&item_id={$_GET[id]}&mgrarea={$_GET[mgrarea]}";
					$allowedFiletypes = "jpg,jpeg,jpe";
				
				break;
			}
		
			$uploadMaxFilesize = str_replace('M','',ini_get('upload_max_filesize'));
			
			echo "<div id='wbheader'><p style='float: left'>{$workbox_title}</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='";
			
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";

			echo "'></p></div>";
   				echo "<div id='wbbody' style='margin: 0; padding: 0; overflow: visible;'>";			
					echo "<div id='pluploadcontent'>";
		?>        
                <script>
					$j(function()
					{
						plupload.addI18n({
								'Select files' : '<?php echo $mgrlang['plupAddFilesToQueue']; ?>',
								'Add files to the upload queue and click the start button.' : '<?php echo $mgrlang['plupAddFilesToQueue']; ?>',
								'Filename' : '<?php echo $mgrlang['plupFilename']; ?>',
								'Status' : '<?php echo $mgrlang['plupStatus']; ?>',
								'Size' : '<?php echo $mgrlang['plupSize']; ?>',
								'Add files' : '<?php echo $mgrlang['plupAddFiles']; ?>',
								'Start upload':'<?php echo $mgrlang['plupStartUplaod']; ?>',
								'Stop current upload' : '<?php echo $mgrlang['plupStopUpload']; ?>',
								'Start uploading queue' : '<?php echo $mgrlang['plupStartQueue']; ?>',
								'Drag files here.' : '<?php echo $mgrlang['plupDragFilesHere']; ?>'
						});
						
						$j('#cancelUpload').click(function(event)
						{					
							//window.close();
							// Reload parent
						});
											
						$j("#uploadContainer").pluploadQueue({
							// General settings
							runtimes : 'gears,html5,flash,silverlight,browserplus',
							url : '<?php echo $uploadURL; ?>',
							max_file_size : '<?php echo $uploadMaxFilesize; ?>mb',
							chunk_size : '1mb',
							unique_names : false,				
							// Specify what files to browse for
							filters : [
								{ title : "Files", extensions : "<?php echo $allowedFiletypes; ?>" }
							],
							// Flash settings
							flash_swf_url : '../assets/plupload/plupload.flash.swf',				
							// Silverlight settings
							silverlight_xap_url : '../assets/plupload/plupload.silverlight.xap'
						});	
						
						var uploader = $j("#uploadContainer").pluploadQueue();
						
						// preinit: attachCallbacks,
						
						$j('#startContrUpload').click(function(event)
						{					
							uploader.start();
						});
						
						uploader.bind('UploadComplete', function(Up, File, Response)
						{
							//$('#uploadMediaStep1').hide();
							//var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=newUpload';
							//workbox({ page : workboxPage, skipOverlay : true });						
							//window.close();
							
							<?php
								if($_GET['page'] == 'add_media')
									echo "close_uploadbox(); load_import_win();";
								else
									echo "close_workbox(); load_ip();";
							?>
							
						});
						
						uploader.bind('FilesAdded', function(Up, File, Response)
						{
							if(uploader.files.length > 0)
							{
								$j('#startContrUpload').removeAttr('disabled');
							}
							else
							{
								$j('#startContrUpload').attr('disabled','disabled');
							}
						});
						
						uploader.bind('FilesRemoved', function(Up, File, Response)
						{
							if(uploader.files.length > 0)
							{
								$j('#startContrUpload').removeAttr('disabled');
							}
							else
							{
								$j('#startContrUpload').attr('disabled','disabled');
							}
						});
					});
				</script> 
				
				<div id="uploadContainer">
					Uploader
				</div>   
        <?php
					echo "</div>";
				echo "</div>";
			echo "<div id='wbfooter' style='padding: 15px 0 20px 20px; margin: 0;'><img src='images/mgr.notice.icon.small2.png' style='float: left; margin: 5px 5px 0 0;' /><p style='float: left; width: 300px; text-align: left; font-size: 10px; color: #6c6c6c'><strong>{$mgrlang[setup_f_uploader]}:</strong> {$mgrlang[change_batch]} $mgrlang[nav_settings] > ";
			# SEE IF ACCESS TO SOFTWARE SETUP IS ALLOWED
			if(in_array('software_setup',$_SESSION['admin_user']['permissions']))
			{
				echo "<a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a>";
			}
			else
			{
				echo "<strong>$mgrlang[subnav_software_setup]</strong>";
			}
			echo "</p><p style='float: right;'>";
			echo "<input type='button' value='$mgrlang[gen_b_close]' class='small_button' id='cancelUpload' onclick='";
				
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";
			
			echo "' /><input type='button' value='{$mgrlang[start_upload]}' class='small_button' id='startContrUpload' disabled='disabled' /></p></div>";
			
		break;
		
		case "java_upload":
			
			# SET THE DEFAULT WIDTH AND HEIGHT
			$uploader_width = "100%";
			$uploader_height = "430";
			
			# FIND THE MAXIMUM FILESIZE PHP WILL ALLOW
			$upload_max_filesize = (str_replace('M','',ini_get('upload_max_filesize'))*1024)*1024;
			
			# SELECT WHICH DETAILS TO USE FOR THE UPLOAD
			switch($_GET['page'])
			{
				default:
				case "add_media":
					# FIND THE ALLOWED FILE TYPES
					foreach(getAlldTypeExtensions() as $value)
					{
						$allowed_filetypes .= "($value)";
						$allowed_filetypes .= "|";
					}
					$allowed_filetypes = substr($allowed_filetypes,0,strlen($allowed_filetypes)-1);
					
					$workbox_title = "{$mgrlang[batch_upload]}:";
					$max_files = ($_GET['maxfiles']) ? $_GET['maxfiles'] : 1000;
					$upload_handler = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=add_media";
					
					$instances = 1;					
					$uc_scaledInstanceNames = "icon,thumb,sample";
					$uc_scaledInstanceDimensions = "$config[IconDefaultSize]x$config[IconDefaultSize],$config[ThumbDefaultSize]x$config[ThumbDefaultSize],$config[SampleDefaultSize]x$config[SampleDefaultSize]";
					$uc_scaledInstanceQualityFactors = "1000,1000,1000";
					
					//echo $upload_handler;
					
				break;
				case "item_photos":
					$allowed_filetypes = "(jpg)|(jpeg)|(jpe)"; // Only JPG at this time
					$workbox_title = "{$mgrlang[upload_photos]}:";
					$max_files = ($_GET['maxfiles']) ? $_GET['maxfiles'] : 5;
					$upload_handler = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."&handler_type=item_photos";
					
					$instances = 1;					
					$uc_scaledInstanceNames = "small,medium";
					$uc_scaledInstanceDimensions = "200x200,500x500";
					$uc_scaledInstanceQualityFactors = "1000,1000";
				break;
			}
			
			echo "<div id='wbheader'><p style='float: left'>{$workbox_title}</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='";
			
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";

			echo "'></p></div>";
   				echo "<div id='wbbody' style='margin: 0; padding: 0; overflow: visible;'>";			
					echo "<div id='javacontent'>";
		?>        
                       <!--<div style="padding: 4px;"><input type='button' value='Add Files' class='small_button' onclick='stopJavaUpload();' /></div>-->
                       <?php
					   /*
					   <applet name="jumpLoaderApplet"
                            code="jmaster.jumploader.app.JumpLoaderApplet.class"
                            archive="../assets/jumploader/jumploader_z.jar"
                            width="<?php echo $uploader_width; ?>"
                            height="<?php echo $uploader_height; ?>" 
							id="jloader" 
                        mayscript>
                            <param name="ac_fireAppletInitialized" value="true"/>
                            <param name="vc_lookAndFeel" value="system"/>
                            <param name="uc_uploadUrl" value="<?php echo $upload_handler; ?>"/>                            
                            <param name="uc_maxFileLength" value="<?php echo $upload_max_filesize; ?>"/>
                            <param name="uc_maxFiles" value="<?php echo $max_files; ?>"/>
                            <param name="uc_uploadScaledImagesNoZip" value="true"/>
                            <param name="uc_uploadOriginalImage" value="true"/> 
                         	
                            <!-- ALLOWED FILE TYPES http://jumploader.com/forum/viewtopic.php?t=56 -->
							<param name="uc_fileNamePattern" value="^.+\.(?i)(<?php echo $allowed_filetypes; ?>)$"/>
                            
                            <!-- SHOW MENU BAR AND START BAR -->
							<param name="vc_uploadViewMenuBarVisible" value="true"/>
                            <param name="vc_uploadViewStartActionVisible" value="false"/>
                            
                            <!-- SHOW FILE TREE LIST / NOT WORKING -->
                            <param name="vc_mainViewFileListViewVisible" value="false"/>
                            <param name="vc_mainViewFileListViewHeightPercent" value="20"/>
                            
                            <!-- SET EVENT HANDLERS -->
                            <param name="ac_fireUploaderFileStatusChanged" value="true"/> 
                            <param name="ac_fireUploaderFileAdded" value="true"/>                             
                            <param name="ac_fireUploaderFileRemoved" value="true"/> 
                            
                            <?php if($instances){ ?>
								<param name="uc_uploadScaledImages" value="true"/>
                            	<param name="uc_scaledInstanceNames" value="<?php echo $uc_scaledInstanceNames; ?>"/>
                            	<param name="uc_scaledInstanceDimensions" value="<?php echo $uc_scaledInstanceDimensions; ?>"/>
                            	<param name="uc_scaledInstanceQualityFactors" value="<?php echo $uc_scaledInstanceQualityFactors; ?>"/>
                            <?php } ?>
                            
                            <param name="uc_imageSubsamplingFactor" value="20"/>  
                        </applet>
						*/
					?>
					<object name="jumpLoaderApplet" type="application/x-java-applet" height="<?php echo $uploader_height; ?>" width="<?php echo $uploader_width; ?>" mayscript> 
					<param name="code" value="jmaster.jumploader.app.JumpLoaderApplet" /> 
					<param name="archive" value="../assets/jumploader/jumploader_z.jar" /> 
					<param name="mayscript" value="true" />
					
					<param name="ac_fireAppletInitialized" value="true" />
					<param name="vc_lookAndFeel" value="system"/>
					<param name="uc_uploadUrl" value="<?php echo $upload_handler; ?>"/>                            
					<param name="uc_maxFileLength" value="<?php echo $upload_max_filesize; ?>"/>
					<param name="uc_maxFiles" value="<?php echo $max_files; ?>"/>
					<param name="uc_uploadScaledImagesNoZip" value="true"/>
					<param name="uc_uploadOriginalImage" value="true"/> 
					<param name="uc_fileNamePattern" value="^.+\.(?i)(<?php echo $allowed_filetypes; ?>)$"/>
					<param name="vc_uploadViewMenuBarVisible" value="true"/>
					<param name="vc_uploadViewStartActionVisible" value="false"/>
					<param name="vc_mainViewFileListViewVisible" value="false"/>
					<param name="vc_mainViewFileListViewHeightPercent" value="20"/>
					<param name="ac_fireUploaderFileStatusChanged" value="true"/> 
					<param name="ac_fireUploaderFileAdded" value="true"/>                             
					<param name="ac_fireUploaderFileRemoved" value="true"/>
					<?php if($instances){ ?>
					<param name="uc_uploadScaledImages" value="true"/>
					<param name="uc_scaledInstanceNames" value="<?php echo $uc_scaledInstanceNames; ?>"/>
					<param name="uc_scaledInstanceDimensions" value="<?php echo $uc_scaledInstanceDimensions; ?>"/>
					<param name="uc_scaledInstanceQualityFactors" value="<?php echo $uc_scaledInstanceQualityFactors; ?>"/>
					<?php } ?>
					<param name="uc_imageSubsamplingFactor" value="20"/>
					</object>
        <?php
					echo "</div>";
				echo "</div>";
			echo "<div id='wbfooter' style='padding: 15px 0 20px 20px; margin: 0;'><img src='images/mgr.notice.icon.small2.png' style='float: left; margin: 5px 5px 0 0;' /><p style='float: left; width: 300px; text-align: left; font-size: 10px; color: #6c6c6c'><strong>{$mgrlang[setup_f_uploader]}:</strong> {$mgrlang[change_batch]} $mgrlang[nav_settings] > ";
			# SEE IF ACCESS TO SOFTWARE SETUP IS ALLOWED
			if(in_array('software_setup',$_SESSION['admin_user']['permissions']))
			{
				echo "<a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a>";
			}
			else
			{
				echo "<strong>$mgrlang[subnav_software_setup]</strong>";
			}
			echo "</p><p style='float: right;'>";
			echo "<input type='button' value='$mgrlang[gen_b_close]' class='small_button' onclick='stopJavaUpload();";
			
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";
			
			echo "' /><input type='button' value='{$mgrlang[start_upload]}' class='small_button' id='start_button' onclick='startJavaUpload();' disabled='disabled' /></p></div>";
			/*
			//echo "<script>";
				//echo "getViewConfig().setUploadViewStartActionVisible(0);"; // Hide Start Button
				//echo "getViewConfig().setUploadViewMenuBarVisible(1);"; // Hide Menu Bar
				//echo "getViewConfig().setMainViewFileListViewVisible(1);"; // File Tree Visible
				//echo "getUploadView().updateView();"; // Not Working
            //echo "</script>";
			*/
		break;
		
		case "flash_upload":
			
			//echo $_GET['page']; exit; // Testing
			
			# FIND THE MAXIMUM FILESIZE PHP WILL ALLOW
			$upload_max_filesize = (str_replace('M','',ini_get('upload_max_filesize'))*1024)*1024;
			
			# SELECT WHICH DETAILS TO USE FOR THE UPLOAD
			switch($_GET['page'])
			{
				default:
				case "add_media":
					# FIND THE ALLOWED FILE TYPES
					foreach(getAlldTypeExtensions() as $value)
					{
						$allowed_filetypes .= "*.{$value};";
						//$allowed_filetypes .= "|";
					}
					//$allowed_filetypes = substr($allowed_filetypes,0,strlen($allowed_filetypes)-1).';';
					
					//echo $allowed_filetypes; exit; // Testing
					
					$workbox_title = "{$mgrlang[batch_upload]}:";
					$max_files = ($_GET['maxfiles']) ? $_GET['maxfiles'] : 1000;
					$upload_handler = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."%26handler_type=add_media_flash";
					
				break;
				case "item_photos":
					$allowed_filetypes = "*.jpg;*.jpeg;*.jpe)"; // Only JPG at this time
					$workbox_title = "{$mgrlang[upload_photos]}:";
					$max_files = ($_GET['maxfiles']) ? $_GET['maxfiles'] : 5;
					$upload_handler = "mgr.upload.handler.php?pass=".md5($config['settings']['access_code'])."%26handler_type=item_photos_flash%26item_id={$_GET[id]}%26mgrarea={$_GET[mgrarea]}";
				break;
			}
			
			$allowed_filetypes = substr($allowed_filetypes,0,strlen($allowed_filetypes)-1);
		
			echo "<div id='wbheader'><p style='float: left'>{$workbox_title}</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='";
			
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";
			
			echo "'></p></div>";
   				echo "<div id='wbbody' style='margin: 0; padding: 0; overflow: visible;'>";			
					//echo $upload_handler;
					echo "<div id='flashcontent' style='margin-left: 70px;'>";
					//echo $upload_max_filesize;
		?>        
                      <!--Flash Uploader Here <?php echo $upload_max_filesize; ?>-->Flash Uploader - If you are seeing this you may not have flash installed. Flash must be installed to use this uploader.
					  <script>
							var flashObj = new FlashObject ("uploader.swf", "uploader", "650", "300", 8, "#FFFFFF", true);			
                            flashObj.addVariable ("myextensions", "<?php echo $allowed_filetypes; ?>");
							//flashObj.addVariable ("myextensions", "*.jpg;*.gif;*.png;*.php");
                            flashObj.addVariable ("uploadUrl", "<?php echo $upload_handler; ?>");
							//flashObj.addVariable ("uploadUrl", "mgr.flashupload.php");
                            //flashObj.addVariable ("downloadListUrl", "mgr.flashviewFiles.php");
                            flashObj.addVariable ("maxFileSize", "<?php echo $upload_max_filesize; ?>"); // in kb
                            flashObj.write ("flashcontent");
                    </script>
        <?php
					echo "</div>";
				echo "</div>";
			echo "<div id='wbfooter' style='padding: 15px 0 20px 20px; margin: 0;'><img src='images/mgr.notice.icon.small2.png' style='float: left; margin: 5px 5px 0 0;' /><p style='float: left; width: 300px; text-align: left; font-size: 10px; color: #6c6c6c'><strong>{$mgrlang[setup_f_uploader]}:</strong> {$mgrlang[change_batch]} $mgrlang[nav_settings] > ";
			# SEE IF ACCESS TO SOFTWARE SETUP IS ALLOWED
			if(in_array('software_setup',$_SESSION['admin_user']['permissions']))
			{
				echo "<a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a>";
			}
			else
			{
				echo "<strong>$mgrlang[subnav_software_setup]</strong>";
			}
			echo "</p><p style='float: right;'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='";
			
			if($_GET['page'] == 'add_media')
				echo "close_uploadbox();";
			else
				echo "close_workbox();";
			
			echo "' /></p></div>";
		break;
		/*
		case "assign_details":
			
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('mgr.defaultcur.php');
			
			$supportPageID = '';
			
			# GET THE ACTIVE LANGUAGES
			//$active_langs = explode(",",$config['settings']['lang_file_pub']);
			//$active_langs[] = $config['settings']['lang_file_mgr'];
			//$active_langs = array_unique($active_langs);
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			$cleanvalues->cur_hide_denotation = 0;
			//$cleanvalues->decimal_places = 4;
			//$cleanvalues->strip_ezeros = 1;
			
			echo "<div id='wbheader'><p>Details For Batch: ".date('U')."</p></div>";
			echo "<div id='vmessage2' style='display: none; border-bottom: 1px solid #6a0a09; background-color: #a91513; color: #FFFFFF; padding: 10px; font-weight: bold; background-image: url(images/mgr.warning.bg.gif); background-repeat: repeat-x;'><img src='images/mgr.notice.icon.png' style='float: left; width: 30px;' />Some of the files you are about to import are over the file size limit and cannot be copied to an external storage location. These files will be skipped during the import process. The file size limit is: $config[OffsiteStogageLimit]MB</div>";
			echo "<div id='wbbody' style='overflow: auto; padding: 8px; margin: 0;'>";
				echo "<p style='padding: 10px;'>Before you import your files you can add some details and pricing to them if you would like. When you are ready to start the import click the Start Importing button below.</p>";
				
				echo "<div id='button_bar'>";
					echo "<div class='subsubon' onclick=\"bringtofront('1');\" id='tab1'>Details</div>";
					echo "<div class='subsuboff' onclick=\"bringtofront('2');\" id='tab2'>Galleries</div>";
					echo "<div class='subsuboff' onclick=\"bringtofront('3');\" id='tab3'>Digital Versions</div>";
					if($config['settings']['cart'])
					{
						echo "<div class='subsuboff' onclick=\"bringtofront('4');\" id='tab4'>Products</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('5');\" id='tab5'>Prints</div>";
						echo "<div class='subsuboff' onclick=\"bringtofront('9');\" id='tab9'>Packages</div>";
					}
					echo "<div class='subsuboff' onclick=\"bringtofront('6');\" id='tab6'>Collections</div>";
					echo "<div class='subsuboff' onclick=\"bringtofront('7');\" id='tab7'>Media Types</div>";
					echo "<div class='subsuboff' onclick=\"bringtofront('8');\" id='tab8' style='border-right: 1px solid #d8d7d7;'>Advanced</div>";
				echo "</div>";
				echo "<form name='batch_details_form' id='batch_details_form'>";
					echo "<input type='hidden' name='batch_id' value='".date('U')."' />";
		?>
				<div id="tab1_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            Title: <br />
                            <span class="input_label_subtext">Assign a title that you would like to add to all media you are importing in this batch.</span>
                        </p>
                        <div class="additional_langs">
                            <input type="text" name="title" id="title" style="width: 330px;" maxlength="100" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo strtoupper($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_title" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="title_<?php echo $value; ?>" id="title_<?php echo $value; ?>" style="width: 330px;" maxlength="100" value="<?php echo @stripslashes($shipping->{"title" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo strtoupper($value); ?></span></li>
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
                            Description: <br />
                            <span class="input_label_subtext">Assign a description that you would like to add to all media you are importing in this batch.</span>
                        </p>
                        <div class="additional_langs">
                            <textarea name="description" id="description" style="width: 330px; height: 50px; vertical-align: middle"></textarea>
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_description','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo strtoupper($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_description" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><textarea name="description_<?php echo $value; ?>" style="width: 330px; height: 50px; vertical-align: middle"><?php echo @stripslashes($shipping->{"description" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo strtoupper($value); ?></span></li>
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
                            Keywords: <br />
                            <span class="input_label_subtext">Assign keywords that you would like to add to all media you are importing in this batch.</span>
                        </p>
                        <div class="additional_langs">
                            <div style="width: 415px;">
                                <div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_keywords','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo strtoupper($config['settings']['lang_file_mgr']); ?></span>&nbsp;<input type="text" id="new_keyword_DEFAULT" /> <input type="button" value="Add" onclick="add_keyword('DEFAULT');" style="margin-top: -4px;" /></div>
                                <div class="keyword_list" id="keywords_DEFAULT">
                                	<div style="display: none;" kwlanguage="DEFAULT" id="placeholder_DEFAULT"></div>
                                </div>
                            </div>
                            
							<?php
                                if(in_array('multilang',$installed_addons))
                                {
                            ?>
                                <div id="lang_keywords" style="display: none;">
                                <?php
                                    foreach($active_langs as $value)
                                    {
										$value = strtoupper($value);
                                ?>
                                    <!--<li><textarea name="keywords_<?php echo $value; ?>" style="width: 200px; height: 50px;"><?php echo @stripslashes($shipping->{"description" . "_" . $value}); ?></textarea> (<?php echo strtoupper($value); ?>)</li>-->
                            		<div style="width: 415px; margin-top: 5px">
                                        <div class="keyword_list_header"><span class="mtag_dblue" style="color: #FFF;"><?php echo strtoupper($value); ?></span>&nbsp;<input type="text" id="new_keyword_<?php echo $value; ?>" /> <input type="button" value="Add" onclick="add_keyword('<?php echo $value; ?>');" style="margin-top: -4px;" /></div>
                                        <div class="keyword_list" id="keywords_<?php echo $value; ?>">
                                            <div style="display: none;" kwlanguage="<?php echo $value; ?>" id="placeholder_<?php echo $value; ?>"></div>
                                        </div>
                                    </div>
							<?php
                                    }
                                    echo "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab2_group" class="group">
                	<div class="<?php fs_row_color(); ?>" style="float: left; margin-bottom: 20px;">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Galleries/Events: <br />
                            <span>Assign galleries or events that media in this batch will be displayed in.</span>
                       	</p>
                        <div style="float: left; width: 415px;">
                        	<div style="background-color: #eee; padding: 5px; border: 1px solid #d9d9d9; font-size: 11px; font-weight: bold; text-align: right"><input type="text" style="height: 14px; width: 150px" id="new_gallery_name" /> <input type="button" value="Create" style="margin-top: -4px;" onclick="create_gallery();" /></div>
                            <div name="gals" id="gals" style="border: 1px solid #d9d9d9; font-size: 11px; padding: 5px;"></div>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab3_group" class="group">                	
                    <div class="<?php fs_row_color(); ?>" fsrow="1">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Original Copy:<br />
                            <span>Select how the original version of these files should be listed.</span>
                       	</p>
                        <select id="original_copy" name="original_copy" onchange="original_dd();" style="width: 298px;">
                        	<option value="nfs" <?php if(!$config['settings']['cart']){ echo "selected"; } ?>>Hidden (not available for download)</option>                            
                            <?php
								if($config['settings']['cart'])
								{
							?>
                            <option value="rf" selected><?php echo $mgrlang['dsp_op_rf']; ?></option>
                            <option value="rm"><?php echo $mgrlang['dsp_op_rm']; ?></option>
                            <?php
								}
							?>
                            <option value="fr"><?php echo $mgrlang['dsp_op_fr']; ?></option>
                            <option value="cu"><?php echo $mgrlang['dsp_op_cu']; ?></option>                            
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="rmspan" fsrow='1' style="display: none;">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['dsp_f_license-']; ?>Rights Managed Pricing Scheme:<br />
                        	<span><?php echo $mgrlang['dsp_f_license_d-']; ?></span>
                        </p>
                        <select name="rm_license" id="rm_license" style="width: 298px;">
                            <?php
								$rm_result = mysqli_query($db,"SELECT name,rm_id FROM {$dbinfo[pre]}rm_schemes WHERE active = '1' ORDER BY name");
								$rm_rows = mysqli_num_rows($rm_result);
								if($rm_rows and $config['settings']['enable_cbp']){
									while($rm_scheme = mysqli_fetch_object($rm_result)){
										echo "\n<option value='$rm_scheme->rm_id'>".$rm_scheme->name."</option>";
									}
								}
							?>
                   		</select>
                   	</div>
                    <div class="<?php fs_row_color(); ?>" id="quantity_div" fsrow="1" style="<?php if(!$config['settings']['cart']){ echo "display: none;"; } ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Quantity: <br />
                            <span>Leave blank for unlimited quantity.</span>
                       	</p>
                        <input type="text" name="quantity" id="quantity" style="width: 90px;" maxlength="50" value="" />
                    </div>
                    <?php
						if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3)
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="assign_price_div" fsrow="1">
                            <img src="images/mgr.ast.off.gif" class="ast" /></td>
                            <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                Price: <br />
                                <span>Price of the original version of this item.</span>
                            </p>
                            <div style="float: left;">
                            	<input type="text" name="price" id="price" style="width: 90px;" maxlength="50" onkeyup="dsp_price_preview();" onblur="update_input_cur('price');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?><br />
                            	<span style="font-size: 10px; color: #999;">Leave blank to use default of: <strong><?php echo $cleanvalues->currency_display($config['settings']['default_price'],1); ?></strong></span>
                            </div>
                        </div>
                    <?php
                        }
                        if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_print'])
						{
                    ?>
                        <div class="<?php fs_row_color(); ?>" id="assign_credit_div" fsrow="1">
                            <img src="images/mgr.ast.off.gif" class="ast" /></td>
                            <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                Credits: <br />
                                <span>Amount of credits for the original version of this item.</span>
                            </p>
                            <input type="text" name="credits" id="credits" style="width: 90px;" maxlength="50" onkeyup="dsp_credits_preview();" /><br /><span style="font-size: 10px; color: #999;">Leave blank to use default of: <strong><?php echo $config['settings']['default_credits']; ?></strong></span>
                        </div>                    
                	<?php
                        }
                    ?>
                    
                    <?php
						$digital_sp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE active = '1' ORDER BY sortorder");
						$digital_sp_rows = mysqli_num_rows($digital_sp_result);
						if($digital_sp_rows)
						{
					?>
                        <div class="<?php fs_row_color(); ?>" fsrow="1">
                            <img src="images/mgr.ast.off.gif" class="ast" /></td>
                            <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                                Digital Profiles: <br />
                                <span>Additional variations that you would like to sell with this item. You can customize these profiles to add specific licensing and pricing for this batch.</span>
                            </p>
                            <div style="width: 430px; float: left;">
                                <ul style="padding: 10px; margin: 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
                                	<li style='list-style:none; font-weight: bold;'>Groups</li>
								<?php
									
									$dsp_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'digital_sp' ORDER BY name");
									$dsp_group_rows = mysqli_num_rows($dsp_group_result);
									while($dsp_group = mysqli_fetch_object($dsp_group_result))
									{
										//echo "$prod_group->name<br />";
										echo "<li style='list-style:none'><input type='checkbox' name='dspgroup[]' id='dspgroupcb_$dsp_group->gr_id' value='$dsp_group->gr_id' /> &nbsp; "; if($dsp_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$dsp_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$dsp_group->flagtype' align='absmiddle' /> "; } echo "<label for='dspgroupcb_$dsp_group->gr_id'><strong>" . $dsp_group->name . "</strong></label>";
										
										$dsp_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}digital_sizes.name FROM {$dbinfo[pre]}digital_sizes JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}digital_sizes.ds_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$dsp_group->gr_id'");
										$dsp_groupids_rows = mysqli_num_rows($dsp_groupids_result);
										
										if($dsp_groupids_rows)
										{
											echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
                                			echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
										
											while($dsp_groupids = mysqli_fetch_object($dsp_groupids_result)){
												echo $dsp_groupids->name . "<br />";
											}										
											echo "</div>";
										}
										
										echo "</li>";
									}
									
									
									echo "</ul>";
									if($dsp_group_rows)
									{
										//echo "<hr style='margin-top: 15px; border: 0;  width: 98%; color: #f00;background-color: #e3e3e3; height: 1px;' />";
									}
									echo "<ul style='padding: 10px; margin: 0; border-top: 1px solid #fff'> ";
								
									while($digital_sp = mysqli_fetch_object($digital_sp_result)){
										echo "<li style='list-style:none; clear: both; overflow: visible;'>";
										
										echo "<div style='float: left;'><input type='checkbox' value='$digital_sp->ds_id' name='digitalsp[]' id='digitalsp_$digital_sp->ds_id' /> &nbsp; <label for='digitalsp_$digital_sp->ds_id'><strong>$digital_sp->name</strong> <span style='display: none;' id='dsp_clabel_$digital_sp->ds_id' style='color: #949494'><em>(customized)</em></span></label>";
										echo "<input type='button' value='Customize' style='height: 20px; margin-left: 4px;' onclick='load_dsp_details($digital_sp->ds_id);' id='dsp_customize_button_$digital_sp->ds_id' /></div>";
										
										echo "<div style='float: left;'><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->";
											echo "<div id='dsp_popup_$digital_sp->ds_id' style='display: none;' class='details_win'>";
                                                echo "<div class='details_win_inner'>";
                                                    echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
                                                    echo "<div id='dsp_popup_".$digital_sp->ds_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
                                                echo "</div>";
                                            echo "</div>";
										echo "</div>";
										
										echo "<div id='dsp_customizations_$digital_sp->ds_id' style='display: none; clear: both;'>";
											echo "Customized:<input type='text' name='dsp_customized_$digital_sp->ds_id' id='dsp_customized_$digital_sp->ds_id' value='0' /><br />";
											echo "License:<input type='text' name='dsp_license_$digital_sp->ds_id' id='dsp_license_$digital_sp->ds_id' value='' /><br />";
											echo "RM License:<input type='text' name='dsp_rm_license_$digital_sp->ds_id' id='dsp_rm_license_$digital_sp->ds_id' value='' /><br />";
											echo "Price:<input type='text' name='dsp_price_$digital_sp->ds_id' id='dsp_price_$digital_sp->ds_id' value='' /><br />";
											echo "Credits:<input type='text' name='dsp_credits_$digital_sp->ds_id' id='dsp_credits_$digital_sp->ds_id' value='' /><br />";
											echo "Quantity:<input type='text' name='dsp_quantity_$digital_sp->ds_id' id='dsp_quantity_$digital_sp->ds_id' value='' /><br />";
											echo "Credits Calc:<input type='text' name='dsp_credits_calc_$digital_sp->ds_id' id='dsp_credits_calc_$digital_sp->ds_id' value='' /><br />";
											echo "Price Calc:<input type='text' name='dsp_price_calc_$digital_sp->ds_id' id='dsp_price_calc_$digital_sp->ds_id' value='' /><br />";
										echo "</div>";
										
										echo "</li>";
									}
                                ?>
                                </ul>
                            </div>                        
                        </div>
                    <?php
						}
					?>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Products: <br />
                            <span>List these products for sale with the media in this batch.</span>
                       	</p>
                        <div style="width: 430px; float: left;">
                            <ul style="padding: 10px; margin: 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
                                <li style='list-style:none; font-weight: bold;'>Groups</li>                  
                                <?php
                                    $prod_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'products' ORDER BY name");
                                    $prod_group_rows = mysqli_num_rows($prod_group_result);
                                    while($prod_group = mysqli_fetch_object($prod_group_result))
                                    {
                                        //echo "$prod_group->name<br />";
                                        echo "<li style='list-style:none'><input type='checkbox' value='$prod_group->gr_id' id='prodgroupcb_$prod_group->gr_id' name='prodgroup[]' /> &nbsp; "; if($prod_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$prod_group->flagtype' align='absmiddle' /> "; } echo "<label for='prodgroupcb_$prod_group->gr_id'><strong>" . $prod_group->name . "</strong></label>";	
										
										$prod_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}products.item_name FROM {$dbinfo[pre]}products JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}products.prod_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$prod_group->gr_id' AND {$dbinfo[pre]}products.deleted='0'");
										$prod_groupids_rows = mysqli_num_rows($prod_groupids_result);
										
										if($prod_groupids_rows)
										{
											echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprodgp".$prod_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('prodgroup_$prod_group->gr_id','','','','plusminus-prodgp$prod_group->gr_id');\" />";
                                			echo "<div id=\"prodgroup_$prod_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
											
											//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
                                			//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
										
											while($prod_groupids = mysqli_fetch_object($prod_groupids_result)){
												echo $prod_groupids->item_name . "$prod_groupids_rows<br />";
											}										
											echo "</div>";
										}
										echo "</li>";
                                    }
									
									echo "</ul>";
									echo "<ul style='padding: 10px; margin: 0; border-top: 1px solid #fff'> ";
									
                                    $prod_result = mysqli_query($db,"SELECT prod_id,item_name,price,credits,product_type FROM {$dbinfo[pre]}products WHERE deleted='0'");
                                    $prod_rows = mysqli_num_rows($prod_result);
                                    while($prod = mysqli_fetch_object($prod_result))
                                    {
                                        echo "<li style='list-style:none; font-weight: bold; clear: both; overflow: visible;'>";
										echo "<div style='float: left;'><input type='checkbox' value='$prod->prod_id' id='prod_$prod->prod_id' name='proditem[]' /> <label for='prod_$prod->prod_id'>{$prod->item_name} <span style='display: none;' id='prod_clabel_$prod->prod_id' style='color: #949494'><em>(customized)</em></span></label>";
										
										if($prod->product_type == '1')
										{
											echo "<input type='button' value='Customize' style='height: 20px; margin-left: 4px;' onclick='load_prod_details($prod->prod_id);' id='prod_customize_button_$prod->prod_id' /></div>";
											
											echo "<div style='float: left;'><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->";
												echo "<div id='prod_popup_$prod->prod_id' style='display: none;' class='details_win'>";
													echo "<div class='details_win_inner'>";
														echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
														echo "<div id='prod_popup_".$prod->prod_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										}
										
										echo "<div id='prod_customizations_$prod->prod_id' style='display: none; clear: both;'>";
											echo "Customized:<input type='text' name='prod_customized_$prod->prod_id' id='prod_customized_$prod->prod_id' value='0' /><br />";
											echo "Price:<input type='text' name='prod_price_$prod->prod_id' id='prod_price_$prod->prod_id' value='' /><br />";
											echo "Credits:<input type='text' name='prod_credits_$prod->prod_id' id='prod_credits_$prod->prod_id' value='' /><br />";
											echo "Quantity:<input type='text' name='prod_quantity_$prod->prod_id' id='prod_quantity_$prod->prod_id' value='' /><br />";
											echo "Credits Calc:<input type='text' name='prod_credits_calc_$prod->prod_id' id='prod_credits_calc_$prod->prod_id' value='' /><br />";
											echo "Price Calc:<input type='text' name='prod_price_calc_$prod->prod_id' id='prod_price_calc_$prod->prod_id' value='' /><br />";
										echo "</div>";
										
                                        echo "</li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Prints: <br />
                            <span>List these prints for sale with the media in this batch. Any groups selected will always add any prints in that group.</span>
                       	</p>
                        <div style="width: 430px; float: left;">
                            <ul style="padding: 10px; margin: 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
                                <li style='list-style:none; font-weight: bold;'>Groups</li>                  
                                <?php
                                    $print_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'prints' ORDER BY name");
                                    $print_group_rows = mysqli_num_rows($print_group_result);
                                    while($print_group = mysqli_fetch_object($print_group_result))
                                    {
                                        //echo "$prod_group->name<br />";
                                        echo "<li style='list-style:none'><input type='checkbox' value='$print_group->gr_id' id='printgroupcb_$print_group->gr_id' name='printgroup[]' /> &nbsp; "; if($print_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$print_group->flagtype' align='absmiddle' /> "; } echo "<label for='printgroupcb_$print_group->gr_id'><strong>" . $print_group->name . "</strong></label>";	
										
										$print_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}prints.item_name FROM {$dbinfo[pre]}prints JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}prints.print_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$print_group->gr_id' AND {$dbinfo[pre]}prints.deleted='0'");
										$print_groupids_rows = mysqli_num_rows($print_groupids_result);
										
										if($print_groupids_rows)
										{
											echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusprintgp".$print_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('printgroup_$print_group->gr_id','','','','plusminus-printgp$print_group->gr_id');\" />";
                                			echo "<div id=\"printgroup_$print_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
											
											//echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
                                			//echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
										
											while($print_groupids = mysqli_fetch_object($print_groupids_result)){
												echo $print_groupids->item_name . "<br />";
											}										
											echo "</div>";
										}
										echo "</li>";
                                    }
									
									echo "</ul>";
									echo "<ul style='padding: 10px; margin: 0; border-top: 1px solid #fff'> ";
									
                                    $print_result = mysqli_query($db,"SELECT print_id,item_name FROM {$dbinfo[pre]}prints WHERE deleted='0'");
                                    $print_rows = mysqli_num_rows($print_result);
                                    while($print = mysqli_fetch_object($print_result))
                                    {
                                        echo "<li style='list-style:none; font-weight: bold; clear: both; overflow: visible;'>";
                                        
										echo "<div style='float: left;'><input type='checkbox' value='$print->print_id' id='print_$print->print_id' name='printitem[]' /> <label for='print_$print->print_id'>{$print->item_name} <span style='display: none;' id='print_clabel_$print->print_id' style='color: #949494'><em>(customized)</em></span></label>";

										echo "<input type='button' value='Customize' style='height: 20px; margin-left: 4px;' onclick='load_print_details($print->print_id);' id='print_customize_button_$print->print_id' /></div>";
										
										echo "<div style='float: left;'><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->";
											echo "<div id='print_popup_$print->print_id' style='display: none;' class='details_win'>";
												echo "<div class='details_win_inner'>";
													echo "<img src='images/mgr.detailswin.arrow.gif' style='position: absolute; margin: 16px 0 0 -9px;' />";
													echo "<div id='print_popup_".$print->print_id."_content' style='overflow: auto; border: 1px solid #fff'><img src='images/mgr.loader.gif' style='margin: 40px;' /></div>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
										
										echo "<div id='print_customizations_$print->print_id' style='display: none; clear: both;'>";
											echo "Customized:<input type='text' name='print_customized_$print->print_id' id='print_customized_$print->print_id' value='0' /><br />";
											echo "Price:<input type='text' name='print_price_$print->print_id' id='print_price_$print->print_id' value='' /><br />";
											echo "Credits:<input type='text' name='print_credits_$print->print_id' id='print_credits_$print->print_id' value='' /><br />";
											echo "Quantity:<input type='text' name='print_quantity_$print->print_id' id='print_quantity_$print->print_id' value='' /><br />";
											echo "Credits Calc:<input type='text' name='print_credits_calc_$print->print_id' id='print_credits_calc_$print->print_id' value='' /><br />";
											echo "Price Calc:<input type='text' name='print_price_calc_$print->print_id' id='print_price_calc_$print->print_id' value='' /><br />";
										echo "</div>";
										
                                        echo "</li>";
										
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group">
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Collections: <br />
                            <span>Add this media to the following collections. Only collections with the type 'Create Collection From Individual Media' will be listed here.</span>
                       	</p>
                        <ul style="float: left; padding: 0; margin: 0;">                   
						<?php
                            $coll_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}collections WHERE colltype = '2'");
                            $coll_rows = mysqli_num_rows($coll_result);
                            while($coll = mysqli_fetch_object($coll_result))
                            {
                                echo "<li style='list-style:none'><input type='checkbox' value='$coll->coll_id' id='coll_$coll->coll_id' name='collection[]' /> <label for='coll_$coll->coll_id'>" . $coll->item_name . "</label></li>";	
                            }
                        ?>
                        </ul>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Media Types: <br />
                            <span>Select media types that fit these media files.</span>
                       	</p>
                        <ul style="float: left; padding: 0; margin: 0;">                   
						<?php
                            $mt_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_types");
                            $mt_rows = mysqli_num_rows($mt_result);
                            while($mt = mysqli_fetch_object($mt_result))
                            {
                                echo "<li style='list-style:none'><input type='checkbox' value='$mt->mt_id' id='mt_$mt->mt_id' name='media_types[]' /> <label for='mt_$mt->mt_id'>" . $mt->name . "</label></li>";	
                            }
                        ?>
                        </ul>
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">
                	
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Keep Originals: <br />
                            <span>Choose to keep or discard originals. If you do not keep originals you will have to upload them when purchased.</span>
                       	</p>
                        <input type="checkbox" name="keep_originals" value="1" checked="checked" />
                    </div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab9_group" class="group">
                	<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" /></td>
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
							Packages: <br />
                            <span>Select the packages that will display with the photos in this batch.</span>
                       	</p>
                        <div style="width: 430px; float: left;">
                            <ul style="padding: 10px; margin: 0; background-color: #eee; border-bottom: 1px solid #CCC"> 
                                <li style='list-style:none; font-weight: bold;'>Groups</li>
								<?php
                                    $pack_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'packages' ORDER BY name");
                                    $pack_group_rows = mysqli_num_rows($pack_group_result);
                                    while($pack_group = mysqli_fetch_object($pack_group_result))
                                    {
                                        //echo "$prod_group->name<br />";
                                        echo "<li style='list-style:none'><input type='checkbox' value='$pack_group->gr_id' id='packgroupcb_$pack_group->gr_id' name='packgroup[]' /> &nbsp; "; if($pack_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$pack_group->flagtype' align='absmiddle' /> "; } echo "<label for='packgroupcb_$pack_group->gr_id'><strong>" . $pack_group->name . "</strong></label>";	
                                        
                                        $pack_groupids_result = mysqli_query($db,"SELECT {$dbinfo[pre]}packages.item_name FROM {$dbinfo[pre]}packages JOIN {$dbinfo[pre]}groupids ON {$dbinfo[pre]}packages.pack_id = {$dbinfo[pre]}groupids.item_id WHERE {$dbinfo[pre]}groupids.group_id='$pack_group->gr_id' AND {$dbinfo[pre]}packages.deleted='0'");
                                        $pack_groupids_rows = mysqli_num_rows($pack_groupids_result);
                                        
                                        if($pack_groupids_rows)
                                        {
                                            echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminuspackgp".$pack_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('packgroup_$pack_group->gr_id','','','','plusminus-packgp$pack_group->gr_id');\" />";
                                            echo "<div id=\"packgroup_$pack_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
                                            
                                            //echo "<img src=\"images/mgr.plusminus.0.gif\" id=\"plusminusdpg".$dsp_group->gr_id."\" align=\"absmiddle\" style=\"margin: 0 0 0 4px; cursor: pointer\" border=\"0\" onclick=\"javascript:displaybool('dpgroup_$dsp_group->gr_id','','','','plusminus-dpg$dsp_group->gr_id');\" />";
                                            //echo "<div id=\"dpgroup_$dsp_group->gr_id\" style=\"display: none; padding: 6px 0 0 42px; line-height: 1.5;\">";
                                        
                                            while($pack_groupids = mysqli_fetch_object($pack_groupids_result)){
                                                echo $pack_groupids->item_name . "<br />";
                                            }										
                                            echo "</div>";
                                        }
                                        echo "</li>";
                                    }
                                    
                                    echo "</ul>";
                                ?>
                            <ul style="padding: 10px; margin: 0; border-top: 1px solid #fff">                   
                            <?php
                                $pack_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}packages");
                                $pack_rows = mysqli_num_rows($pack_result);
                                while($pack = mysqli_fetch_object($pack_result))
                                {
                                    echo "<li style='list-style:none'><input type='checkbox' value='$pack->pack_id' id='pack_$pack->pack_id' name='packages[]' /> <label for='pack_$pack->pack_id'>" . $pack->item_name . "</label></li>";	
                                }
                            ?>
                            </ul>
                        </div>
                    </div>
                </div>
		<?php		
				//echo "<div class='more_options' style='width: 708px' id='global'><br /><br /><br /><br /><br /><br /><br /><br />";
				//echo "</div>";
			
			echo "</form>";
			echo "</div>";
			echo "<div id='wbfooter' style='padding-left: 30px;'>";
			
			echo "<p style='float: right; margin: 0; padding: 0;'><input type='button' value='Start Importing' class='small_button' onclick='start_importing();' /><input type='button' value='$mgrlang[gen_b_cancel2]' class='small_button' onclick='close_workbox();' /></p></div>";
			echo "<script>";
				echo "update_fsrow('tab3_group');\n";
				echo "load_gals();\n";
				echo "checkFileSizeRestrictions();\n";
				echo "Event.observe('new_keyword_DEFAULT', 'keypress', function(){ checkkey('DEFAULT'); });\n";
				echo "Event.observe('new_gallery_name', 'keypress', checkkeygallery);\n";
				
				if(in_array('multilang',$installed_addons))
                {
					foreach($active_langs as $value)
                    {
						$value = strtoupper($value);
						echo "Event.observe('new_keyword_".$value."', 'keypress', function(){ checkkey('".$value."'); });\n";	
					}
				}
			echo "</script>";
		break;
		*/
		case "newticket":
			$mem_id = $_GET['id'];
			
			$member_result = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '$mem_id'");
			$member_rows = mysqli_num_rows($member_result);
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			$member_name = "<strong>$mgrMemberInfo->f_name $mgrMemberInfo->l_name</strong>";
			if($mgrMemberInfo->email) $member_name.= " ($mgrMemberInfo->email)";
			
			echo "<form id='support_ticket_form' action='mgr.member.actions.php' method='post'>";
			echo "<input type='hidden' name='pmode' value='submit_message' />";
			echo "<input type='hidden' name='mem_id' value='$mem_id' />";
			echo "<input type='hidden' name='pass' id='SNpass' value='".md5($config['settings']['serial_number'])."' />";
			echo "<div id='wbheader'><p style='float: left;'>{$mgrlang[mes_to_mem]}:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
				echo "<div id='wbbody'>";			
					if(in_array("ticketsystem",$installed_addons))
					{
						echo "<div style='overflow: auto; position: relative'>";
					}
					else
					{
						echo "<div style='overflow: auto; position: relative; display: none;'>";
					}
							echo "<div class='subsubon' id='email_b' style='border-left: 1px solid #d8d7d7;'><input type='radio' name='message_type' value='email' id='message_type_email' onclick=\"emailbutton();\" checked />&nbsp;<label for='message_type_email'>{$mgrlang[email]}</label></div>";
							echo "<div class='subsuboff' id='ticket_b' style='border-right: 1px solid #d8d7d7;'><input type='radio' name='message_type' value='ticket' id='message_type_ticket' onclick=\"ticketbutton();\" />&nbsp;<label for='message_type_ticket'>{$mgrlang[support_ticket]}</label></div>";
						echo "</div>";
					echo "<div class='more_options' id='message_email_div' style='width: 738px; padding: 0;'>";
						echo "<div style='white-space: nowrap; margin: 0; background-color: #fff; border-bottom: 1px dotted #d7d7d7; overflow: auto; padding: 20px 20px 20px 25px'>";
						if(file_exists("../assets/avatars/" . $mem_id . "_small.png"))
						{
							echo "<img src='../assets/avatars/" . $mem_id . "_small.png?rmd=" . create_unique() . "' width='19' style='border: 2px solid #$border_color; vertical-align: middle; margin-right: 5px;' />";
						}
						else
						{
							echo "<img src='images/mgr.no.avatar.gif' width='19' style='border: 2px solid #$border_color; vertical-align: middle; margin-right: 5px;' />";
						}
						echo $member_name;
						echo "</div>";
						
						echo "<div style='padding: 25px 25px 0 25px;'>
							<strong>{$mgrlang[gen_email_template]}</strong>:<br>
							<select style='width: 350px;' id='emailTemplateDD' onchange='chooseEmailTemplate()'>
								<option value='0'>{$mgrlang[gen_t_none]}</option>
						";
						$contentResult = mysqli_query($db,"SELECT name,content_id FROM {$dbinfo[pre]}content WHERE ca_id = 5 ORDER BY name");
						$contentRows = mysqli_num_rows($contentResult);
						while($content = mysqli_fetch_assoc($contentResult))
						{	
							echo "<option value='{$content[content_id]}'>{$content[name]}</option>";
						}
						echo "</select>
						</div>";
						echo "<div style='padding: 25px;' id='emailContentContainer'>";						
							echo "<p style='float: left;'><strong>$mgrlang[tickets_f_summary]:</strong><br /><input type='text' name='email_summary' style='width: 338px;'></p>";
							echo "<br style='clear: both;' /><br /><strong>{$mgrlang[gen_message]}:</strong><br /><textarea style='width: 678px; height: 100px' id='email_body' name='email_body'></textarea><br />";
							//echo "<br /><br /><input type='checkbox' name='notify' id='notify' checked='checked' value='1' /> <label for='notify'><strong>Send Copy To Myself</strong></label>";
						echo "</div>";
					echo "</div>";
					
					echo "<div class='more_options' id='message_ticket_div' style='width: 738px; padding: 0; display: none;'>";
						echo "<div style='white-space: nowrap; margin: 0; background-color: #fff; border-bottom: 1px dotted #d7d7d7; overflow: auto; padding: 20px 20px 20px 25px'>";
						if(file_exists("../assets/avatars/" . $mem_id . "_small.png"))
						{
							echo "<img src='../assets/avatars/" . $mem_id . "_small.png?rmd=" . create_unique() . "' width='19' style='border: 2px solid #$border_color; vertical-align: middle; margin-right: 5px;' />";
						}
						else
						{
							echo "<img src='images/mgr.no.avatar.gif' width='19' style='border: 2px solid #$border_color; vertical-align: middle; margin-right: 5px;' />";
						}
						echo $member_name;
						echo "</div>";
						echo "<div style='padding: 25px;'>";						
							echo "<p style='clear: both; float: left; margin-right: 8px;'><strong>{$mgrlang[ticket]}:</strong><br />";
								echo "<select name='ticket_id' id='ticket_id' style='width: 330px;' onchange='ticket_summary();'>";
									echo "<option value='0'>{$mgrlang[create_new_ticket]}</option>";
									$ticket_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}tickets WHERE member_id = '$_GET[id]' ORDER BY lastupdated DESC");
									while($ticket = mysqli_fetch_object($ticket_result))
									{
										$ticket_summary = (strlen($ticket->summary) < 100) ? $ticket->summary : substr($ticket->summary,0,100) . "...";
										echo "<option value='$ticket->ticket_id'>$ticket_summary ($ticket->ticket_id)</option>";
									}
								echo "</select>";
							echo "</p>";
							echo "<p style='float: left;' id='summary_p'><strong>$mgrlang[tickets_f_summary]:</strong><br /><input type='text' name='summary' style='width: 338px;'></p>";
							echo "<br style='clear: both;' /><br /><strong>{$mgrlang[gen_message]}:</strong><br /><textarea style='width: 678px; height: 100px' id='reply' name='reply'></textarea><br />";
							echo "<br /><br /><input type='checkbox' name='notify' id='notify' checked='checked' value='1' /> <label for='notify'><strong>$mgrlang[tickets_f_notify]</strong></label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='close' id='close' value='1' /> <label for='close'><strong>$mgrlang[tickets_f_close]</strong></label> ";
						echo "</div>";
				echo "</div>";
			echo "</div>";
			echo "<div id='wbfooter' style='padding: 5px 8px 20px 20px; margin: 0;'><p style='float: right;'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='close_workbox();' /><input type='button' value='{$mgrlang[gen_b_submit]}' class='small_button' id='submit_button' onclick='submit_new_message();' /></p></div>";
			echo "</form>";
			if($_GET['tickettab'] == 1)
			{
				echo "<script>ticketbutton();</script>";
			}
		break;
		/*
		case "groups":
			echo "<form>";
			echo "<div id='wbheader'><p>Groups:</p></div>";
   				echo "<div id='wbbody'>";	
					
					echo "<div style='overflow: auto; position: relative'>";
						echo "<div class='subsubon' id='group_list'>Group List</div>";
						echo "<div class='subsuboff' id='edit_group' style='border-right: 1px solid #d8d7d7;'>Add New Group</div>";
					echo "</div>";
					echo "<div class='more_options' style='background-position:top; width: 708px' id='ship_regional'>";
						echo "blah";
					echo "</div>";
					
				echo "</div>";
			echo "</div>";
			echo "<div id='wbfooter' style='padding: 15px 0 20px 20px; margin: 0;'>";
				echo "<p style='float: right;'><input type='button' value='Close' class='small_button' onclick='close_workbox();' /></p>";
			echo "</div>";
			echo "</form>";
		break;
		*/
	}	
?>
