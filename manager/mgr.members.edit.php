<?php
	###################################################################
	####	MEMBER EDITOR                                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-22-2008                                     ####
	####	Modified: 8-29-2009                                    #### 
	###################################################################
	
		$page = "members";
		$lnav = "users";
		$supportPageID = '345';
		
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE		
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
		//require_once('../assets/classes/encryption.php');				# INCLUDE ENCRYPTION CLASS		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		require_once('../assets/classes/browser.detect.php');			# INCLUDE BROWSER DETECTION CLASS
		
		# GET BROWSER VERSION
		$browser = new Browser();
		$browser_name = $browser->getBrowser();
		$browser_version = $browser->getVersion();
		
		# INCLUDE DEFAULT CURRENCY SETTINGS
		require_once('mgr.defaultcur.php');
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		$ndate = new kdate;
		$ndate->distime = 1;
	
		# CHECK TO MAKE SURE THE TWEAK IS SET RIGHT
		if($config['RatingStars'] != 5 and $config['RatingStars'] != 10)
		{
			$config['RatingStars']  = 5;
		}
	
		# GET THE ACTIVE LANGUAGES
		//$active_langs = explode(",",$config['settings']['lang_file_pub']);
		//$active_langs[] = $config['settings']['lang_file_mgr'];
		//$active_langs = array_unique($active_langs);
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action'])
		{
			$member_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members WHERE mem_id = '$_GET[edit]'");
			$member_rows = mysqli_num_rows($member_result);
			$mgrMemberInfo = mysqli_fetch_object($member_result);
			
			# GET THE MEMBERS ADDRESS
			$address_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}members_address WHERE member_id = '$mgrMemberInfo->mem_id'");
			$address_rows = mysqli_num_rows($address_result);
			$address = mysqli_fetch_object($address_result);
			
		}
		
		// CALCULATE MAX UPLOAD SIZE FOR AVATAR
		if(ini_get("upload_max_filesize"))
		{
			$upload_limit = ini_get("upload_max_filesize") * 1024;
			$upload_limit-= 50;
		}
		else
		{
			$upload_limit = 1950;
		}
		
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" )
		{
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../assets/includes/clean.data.php');
			
			$signup_date = $_POST['signup_year']."-".$_POST['signup_month']."-".$_POST['signup_day']." ".$_POST['signup_hour'].":".$_POST['signup_minute'].":00";
			
			$signup_date = $ndate->formdate_to_gmt($signup_date);
			
			if($_POST['msexpire_type'] == 1)
			{
				$msexpire_date = $_POST['msexpire_year']."-".$_POST['msexpire_month']."-".$_POST['msexpire_day']." 00:00:00";
				$msexpire_date = $ndate->formdate_to_gmt($msexpire_date);
			}
			else
			{
				$msexpire_date = '0000-00-00 00:00:00';
			}
			
			# ENCRYPT THE PASSWORD BEFORE SAVING TO THE DB
			/*
			$crypt = new encryption_class;
			$encrypt_result = $crypt->encrypt($config['settings']['serial_number'], $password, 20);
			$errors = $crypt->errors;
			$password = $encrypt_result;
			*/
			$password = k_encrypt($password);
			
			# STIP ANY HTML THAT IS INSERTED
			$bio_content = trim(strip_tags($bio_content));
			
			if(!$bio_content)
			{
				$bio_status = 0;
			}
			
			if(!$avatar_status)
			{
				$avatar_status = 0;	
			}
			
			# UPDATE AN EXISTING MEMBER
			if($_REQUEST['action'] == "save_edit")
			{
				# UPDATE THE MEMBERS DATABASE
				$sql = "UPDATE {$dbinfo[pre]}members SET 
							signup_date='$signup_date',
							f_name='$f_name',
							l_name='$l_name',
							email='$email',
							status='$status',
							password='$password',
							comp_name='$comp_name',
							website='$website',
							phone='$phone',
							notes='$notes',
							membership='$membership',
							compay='$compay',
							paypal_email='$paypal_email',
							bill_me_later='$bill_me_later',
							credits='$credits',
							com_source='$com_source',
							com_level='$com_level',
							avatar_status='$avatar_status',
							bio_status='$bio_status',
							bio_content='$bio_content',
							featured='$featured',
							ms_end_date='{$msexpire_date}',
							display_name='{$display_name}',
							showcase='{$showcase}'
							where mem_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE THE ADDRESS DATABASE
				$sql2 = "UPDATE {$dbinfo[pre]}members_address SET 
							address='$address',
							address_2='$address_2',
							city='$city',
							state='$state',
							postal_code='$postal_code',
							country='$country'
							where member_id  = '$saveid'";
				$result2 = mysqli_query($db,$sql2);
				
				//echo $sql2; exit;
				
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
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_members'],1,$mgrlang['gen_b_ed'] . " > <strong>$f_name $l_name</strong>");	
			}			
			
			# SAVE A NEW MEMBER
			if($_REQUEST['action'] == "save_new")
			{			
				# CREATE USER ID
				$umem_id = create_unique2();
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}members (
						signup_date,
						umem_id,
						f_name,
						l_name,
						email,
						status,
						password,
						comp_name,
						website,
						phone,
						notes,
						membership,
						compay,
						paypal_email,
						bill_me_later,
						credits,
						com_source,
						com_level,
						avatar_status,
						bio_status,
						bio_content,
						featured,
						display_name,
						showcase,
						ms_end_date
						) VALUES (
						'$signup_date',
						'$umem_id',
						'$f_name',
						'$l_name',
						'$email',
						'1',
						'$password',
						'$comp_name',
						'$website',
						'$phone',
						'$notes',
						'$membership',
						'$compay',
						'$paypal_email',
						'$bill_me_later',
						'$credits',
						'$com_source',
						'$com_level',
						'$avatar_status',
						'$bio_status',
						'$bio_content',
						'$featured',
						'{$display_name}',
						'{$showcase}',
						'{$msexpire_date}'
						)";
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
				
				//echo $address; exit;
				
				# INSERT ADDRESS INFO INTO THE DATABASE
				$sql2 = "INSERT INTO {$dbinfo[pre]}members_address (
						member_id,
						address,
						address_2,
						city,
						state,
						postal_code,
						country
						) VALUES (
						'$saveid',
						'$address',
						'$address_2',
						'$city',
						'$state',
						'$postal_code',
						'$country'
						)";
				$result2 = mysqli_query($db,$sql2);
				
				//echo $saveid; exit;
				
				# ADD GROUPS
				if($setgroups)
				{
					foreach($setgroups as $value)
					{
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_members'],1,$mgrlang['gen_b_new'] . " > <strong>$f_name $l_name</strong>");
				
			}
			
			# FIND OUT HOW MANY MORE ARE PENDING
			$_SESSION['pending_member_bios'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE bio_status = '2'"));			
			$_SESSION['pending_member_avatars'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE avatar_status = '2'"));
			$_SESSION['pending_members_inactive'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE status = '2'"));
			$_SESSION['pending_members'] = $_SESSION['pending_member_bios'] + $_SESSION['pending_member_avatars'] + $_SESSION['pending_members_inactive'];
			
			header("location: mgr.members.php?mes=new"); exit;
		}
					
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_members']; ?></title>
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
        
    <style>
		.rating_stars_div{
			
		}
		.rating_stars_div img{
			margin-right: 4px;
			cursor: pointer;
			<?php if($config['RatingStars'] == 10){ echo "width: 10px;"; } else { echo "width: 14px;"; } ?>
		}
		
		.rating_details{
			position: absolute;
			bottom: 5px;
			height: 15px;
			background-color: #333;
			font-size: 11px;
			border-top: 1px solid #000;
			color: #fff;
			opacity:0.5;
			filter:alpha(opacity=50);
			padding: 4px; 	
		}
	</style>
    
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<!-- FLASH OBJECT -->
	<script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>    
    <!-- MESSAGE WINDOW JS -->
	<script type="text/javascript" src="mgr.js.messagewin.php"></script>
    
	
	<script language="javascript" type="text/javascript">	
		function form_submitter(){
			// REVERT BACK
			$('f_name_div').className='fs_row_on';
			$('l_name_div').className='fs_row_off';
			$('email_div').className='fs_row_on';
			$('password_div').className='fs_row_off';
			
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message();";
					echo "return false;";
				}
				else
				{
					$action_link = ($_GET['edit'] == "new") ? "mgr.members.edit.php?action=save_new" : "mgr.members.edit.php?action=save_edit";
					js_validate_field("f_name","mem_f_fname",1);
					js_validate_field("l_name","mem_f_lname",1);
					js_validate_field("email","mem_f_email",1);
					js_validate_field("password","mem_f_password",1);
			?>
				//return false;
				var pars = 'pmode=checkEmail&pass=<?php echo md5($config['settings']['serial_number']); ?>&email='+$F('email')+'&edit=<?php echo $_GET['edit']; ?>';
				var myAjax = new Ajax.Updater(
					'emailWarning', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true,
						onSuccess: function(transport){							
							var json = transport.responseText.evalJSON(true);
							
							//alert(json.errorCode);
							//return false;
							
							if(json.errorCode == 'good')
							{
								$('data_form').submit();
							}
							if(json.errorCode == 'emailInUse')
							{
								simple_message_box("<?php echo $mgrlang['mem_f_email_error']; ?>","email");
								$('email_div').className='fs_row_error';
								bringtofront('1');
								return false;
							}
						}
					});
			<?php
					

					//echo "\n return false;";
				}
			?>
			
		}
		
		// Add subscription window
		function openAddSubWorkbox()
		{
			workbox2({'page' : 'mgr.workbox.php?box=addMemSub&memID=<?php echo $_GET['edit']; ?>'});
		}
		
		// Add subscription to database
		function addMemSub()
		{
			$('subSelection').request({
				onComplete: function(response)
				{
					//alert(response.responseText); // Testing
					load_subs();
					close_workbox();
				}
			});	
		}
		
		// TICKET WORKBOX
		//workboxobj = {};
		//workboxobj.mode = 'newticket';
		//workboxobj.id = '<?php echo $mgrMemberInfo->mem_id; ?>';
		
		// LOAD THE STATE/REGIONS DROPDOWN
		function get_regions()
		{
			$('region_div').update('<img src="./images/mgr.loader2.gif" align="absmiddle" style="margin-top: 8px;" />');						
			var selecteditem = $('country').options[$('country').selectedIndex].value;
			//alert(selecteditem);
			var updatecontent = "region_div";
			var loadpage = "mgr.get.regions.php";
			var pars = "cid=" + selecteditem;
			<?php
				if($_GET['edit'] != "new")
				{
					echo "\n" . "pars = pars + \"&sid=$address->state\";";
				}
				else
				{
					echo "\n" . "pars = pars + \"&sid=new\";";
				}
			?>
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});		
		}
		
		<?php
			if($_GET['edit'] != "new")
			{
		?>
			
			// LOAD ACTIVITY LOG
			function load_al(startat)
			{			
				if($('activity_window') != null)
				{
					show_loader('activity_window');
				}
				else
				{
					show_loader('activity_log');
				}
				var pars = 'mid=<?php echo $mgrMemberInfo->mem_id; ?>&manager=0&start=' + startat + get_to_date() + get_from_date();
				var myAjax = new Ajax.Updater(
					'activity_log', 
					'mgr.activity.log.php', 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true
					});
			}
			
			// DOWNLOAD CSV
			function download_csv()
			{
				location.href='mgr.activity.log.php'+'?displaymode=download&manager=0&mid=<?php echo $mgrMemberInfo->mem_id; ?>' + get_to_date() + get_from_date();
			}
			
			// PURGE THE ACTIVITY LOG BEFORE A CERTAIN DATE
			function purge_activity_log()
			{
				var url = "mgr.activity.log.php";
				var updatebox = "activity_log";
				//var pars;
				var pday = $('purge_day').options[$('purge_day').selectedIndex].value;
				var pmonth = $('purge_month').options[$('purge_month').selectedIndex].value;
				var pyear = $('purge_year').options[$('purge_year').selectedIndex].value;
				var pars = "purge=1&manager=0&mid=<?php echo $mgrMemberInfo->mem_id; ?>&pday="+pday+"&pmonth="+pmonth+"&pyear="+pyear;			
				pars = pars + get_to_date() + get_from_date();			
				var myAjax = new Ajax.Updater(
					updatebox, 
					url, 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true
					});
			}

			// CREATE THE PRINT WINDOW AND INVOKE PRINTING
			function prep_printing()
			{
				var print_details = new Object();
				print_details.updatecontent = 'print_window_inner';
				print_details.loadpath = 'mgr.activity.log.php';
				print_details.pars = 'displaymode=print&manager=0&mid=<?php echo $mgrMemberInfo->mem_id; ?>';
				print_details.pars = print_details.pars + get_to_date() + get_from_date();
				do_printing(print_details);
			}
			
			// BLOCK SIGNUP IP
			function block_ip_signup(startat)
			{
				<?php
					if($_SESSION['admin_user']['admin_id'] == "DEMO")
					{
						echo "demo_message();";
					}
					else
					{
				?>
					show_loader('div_ip_signup');
					var pars = 'pmode=block_ip&ip=<?php echo $mgrMemberInfo->ip_signup; ?>';
					var myAjax = new Ajax.Updater(
						'div_ip_signup', 
						'mgr.shared.actions.php', 
						{
							method: 'get', 
							parameters: pars,
							evalScripts: true
						});
				<?php
					}
				?>
			}
			
			// BLOCK LOGIN IP
			function block_ip_login(startat)
			{
				<?php
					if($_SESSION['admin_user']['admin_id'] == "DEMO"){
						echo "demo_message();";
					} else {
				?>
					show_loader('div_ip_login');
					var pars = 'pmode=block_ip&ip=<?php echo $mgrMemberInfo->ip_login; ?>';
					var myAjax = new Ajax.Updater(
						'div_ip_login', 
						'mgr.shared.actions.php', 
						{
							method: 'get', 
							parameters: pars,
							evalScripts: true
						});
				<?php
					}
				?>
			}
			
			// BLOCK REFERRER
			function block_referrer(startat)
			{
				<?php
					if($_SESSION['admin_user']['admin_id'] == "DEMO")
					{
						echo "demo_message();";
					}
					else
					{
				?>
					show_loader('div_referrer');
					var pars = 'pmode=block_domain&domain=<?php echo $mgrMemberInfo->referrer; ?>';
					var myAjax = new Ajax.Updater(
						'div_referrer', 
						'mgr.shared.actions.php', 
						{
							method: 'get', 
							parameters: pars,
							evalScripts: true
						});
				<?php
					}
				?>
			}
			
			// BLOCK REFERRER
			function block_email(startat)
			{
				<?php
					if($_SESSION['admin_user']['admin_id'] == "DEMO")
					{
						echo "demo_message();";
					}
					else
					{
				?>
					show_loader('div_block_email');
					var pars = 'pmode=block_email&email=<?php echo $mgrMemberInfo->email; ?>';
					var myAjax = new Ajax.Updater(
						'div_block_email', 
						'mgr.shared.actions.php', 
						{
							method: 'get', 
							parameters: pars,
							evalScripts: true
						});
				<?php
					}
				?>
			}
			
			<?php
				# GET AVATAR FILE TYPES
				$avatar_ft = explode(",",$config['settings']['avatar_filetypes']);
				foreach($avatar_ft as $key => $value)
				{
					$avatar_ft[$key] = "*.$value";
					if($value == "jpg")
					{
						$avatar_ft[] = "*.jpeg";
					}
				}
				$avatar_ft = implode(";",$avatar_ft);
				//echo $avatar_ft; exit;
			?>
			
			// AVATAR FLASH UPLOADER
			var flashObj = new SWFObject ("./mgr.single.uploader.swf", "uploadDownload", "300", "40", 8, "#FFFFFF", true);
			flashObj.addVariable ("myextensions", "<?php echo $avatar_ft; ?>");
            flashObj.addVariable ("uploadUrl", "mgr.member.actions.php");
			flashObj.addVariable ("uploadParms","?pmode=upload_avatar%26mid=<?php echo $_GET['edit']; ?>%26pass=<?php echo md5($config['settings']['serial_number']); ?>%26aid=<?php echo $_SESSION['admin_user']['admin_id']; ?>"); 
            flashObj.addVariable ("maxFileSize", "<?php echo maxUploadSize('kb'); ?>");
			flashObj.addVariable ("maxFileSizeError", "<?php echo $mgrlang['gen_error_25']; ?>");
			flashObj.addVariable ("uploadButtonLabel", "<?php echo $mgrlang['gen_b_upload']; ?>");			
			
			// UPDATE AVATAR WINDOW
			function update_avatar_win()
			{
				show_loader_mt('avatar_box');
				var myAjax = new Ajax.Updater(
					'avatar_box', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: 'pmode=show_avatar_win&mid=<?php echo $_GET['edit']; ?>&upd=1&pass=<?php echo md5($config['settings']['serial_number']); ?>',
						evalScripts: true
					});
			}
			
			function update_image_win()
			{
				update_avatar_win();
			}
			
			// LOAD AVATAR WINDOW
			function show_avatar_win()
			{
				show_loader_mt('avatar_box');
				var myAjax = new Ajax.Updater(
					'avatar_box', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: 'pmode=show_avatar_win&mid=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>'
					});
			}
			
			
			
			// DELETE BILL
			function deleteBill(billID)
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
							message_box("<?php echo $mgrlang['mem_del_bill']; ?>","<input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='doDeleteBill("+billID+");close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
				<?php
						}
						else
						{
							echo "doDeleteBill(billID);";
						}
					}
				?>
			}
			
			// DELETE THE BILL
			function doDeleteBill(billID)
			{
				
				show_loader('bills_div');
				
				var myAjax = new Ajax.Updater(
					'subs', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: 'pmode=deleteBill&mid=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>&billID='+billID,
						evalScripts: true,
						onSuccess: function()
						{
							//alert(subID);
							load_bills();
						}
					});
				
			}
			
			
			// DELETE SUBSCRIPTION
			function delete_sub(subID)
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
							message_box("<?php echo $mgrlang['mem_del_sub']; ?>","<input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_sub("+subID+");close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
				<?php
						}
						else
						{
							echo "do_delete_sub(subID);";
						}
					}
				?>
			}
			
			// DELETE THE SUB
			function do_delete_sub(subID)
			{
				show_loader('subs');
				
				var myAjax = new Ajax.Updater(
					'subs', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: 'pmode=deleteMemSub&mid=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>&subID='+subID,
						evalScripts: true,
						onSuccess: function()
						{
							//alert(subID);
							load_subs();
						}
					});
			}
			
			// DELETE THE AVATAR
			function delete_avatar()
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
							message_box("<?php echo $mgrlang['mem_del_avatar']; ?>","<input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_avatar();close_message();' /><input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' />",'');
				<?php
						}
						else
						{
							echo "do_delete_avatar();";
						}
					}
				?>
			}
			
			// DELETE THE AVATAR
			function do_delete_avatar()
			{
				show_loader_mt('avatar_box');
				var myAjax = new Ajax.Updater(
					'avatar_box', 
					'mgr.member.actions.php', 
					{
						method: 'get', 
						parameters: 'pmode=delete_avatar&mid=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>&aid=<?php echo $_SESSION['admin_user']['admin_id']; ?>',
						evalScripts: true,
						onSuccess: reset_avatar_summary
					});
			}
			
			// RESET THE AVATAR SUMMARY AREA PHOTO
			function reset_avatar_summary()
			{
				$('avatar_summary').update("<img src='images/mgr.icon.mem.summary.gif' style='border: 2px solid #ffffff; margin-right: 1px;' width='70' />");
			}
			// UPDATE THE AVATAR SUMMARY AREA PHOTO
			function update_avatar_summary()
			{
				$('avatar_summary').update("<img src='mgr.display.avatar.php?mem_id=<?php echo $mgrMemberInfo->mem_id; ?>&size=70&ext=<?php echo $mgrMemberInfo->avatar; ?>' style='border: 2px solid #ffffff; margin-right: 1px;' />");
			}
			
			// UPLOAD FAILED TRY AGAIN
			function try_again()
			{
				flashObj.write("avatar_box");
			}
			
			// LOAD REGIONS ON PAGE LOAD
			Event.observe(window, 'load', function()
			{
				get_regions();
				<?php
					# CHECK TO MAKE SURE THE AVATARS DIRECTORY EXISTS AND IS WRITABLE
					if(!file_exists("../assets/avatars") or !is_writable("../assets/avatars"))
					{
						echo "$('avatar_box').update(\"<div style='float: left; padding: 10px; background-color: #fae8e8; border: 1px solid #ba0202; width: 380px'><strong>$mgrlang[gen_error_24]</strong><br />$mgrlang[gen_error_27]</div>\")";
					}
					else
					{
						# SHOW AVATAR WINDOW OR UPLOAD WINDOW
						if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png"))
						{
							echo "show_avatar_win();";
						}
						else
						{
							if($_SESSION['admin_user']['admin_id'] == "DEMO")
							{
								echo "\$('avatar_box_inner').update('$mgrlang[gen_disabled]');";
							}
							else
							{
								echo "flashObj.write(\"avatar_box\");";
							}
						}
					}
				?>
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
				
				resizefsdiv('support_tickets',360);
				//resizefsdiv('orders_div',360);
				//resizefsdiv('comments',360);
				//resizefsdiv('tickets_div',360);
				
			});
			
			Event.observe(window, 'resize', function()
			{
				resizefsdiv('support_tickets',360);
				//resizefsdiv('orders_div',360);
				//resizefsdiv('comments',360);
				//resizefsdiv('tickets_div',360);
			});
			
			var load_once = false;
			function load_avatar_once()
			{
				 if(load_once == false){
				 	load_once = true;
					show_avatar_win();
				}
			}
		<?php
			}
		?>
		
		// LOAD TICKETS
		function load_tickets()
		{
			show_loader('tickets_div');
			var pars = 'pmode=tickets&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('tickets_div', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// LOAD BILLS
		function load_bills()
		{
			show_loader('bills_div');
			var pars = 'pmode=bills&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('bills_div', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// LOAD COMMENTS
		function load_comments()
		{
			show_loader('comments');
			var pars = 'pmode=comments&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('comments', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// SWITCH STATUS ON COMMENTS
		function switch_status_comment(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('comment_sp_'+item_id).hide();
				hide_sp();
				var updatecontent = 'commentcheck' + item_id;
				var loadpage = "mgr.media.comments.actions.php?mode=ap&mempage=1&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// SWITCH STATUS ON COMMISSIONS
		function switch_status_compay(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				$('compayment_sp_'+item_id).hide();
				hide_sp();				
				var updatecontent = 'compaycheck' + item_id;				
				var loadpage = "mgr.member.actions.php?pmode=updateCompayStatus&id=" + item_id + "&newstatus=" + newstatus + "&pass=<?php echo md5($config['settings']['serial_number']); ?>";
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});				
			}
		}
		
		// DELETE COMMISSION
		function delete_commission(com_id)
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
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_commission(\""+com_id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_commission(com_id);";
					}
				?>
			}
		}
		
		// DO THE DELETE OF THE COMMISSION
		function do_delete_commission(com_id)
		{
			show_loader('contrSalesContainer');
			//alert(com_id);
			var pars = 'pmode=deleteCommission&com_id='+com_id+'&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('contrSalesContainer', 'mgr.member.actions.php', {method: 'get', parameters: pars, evalScripts: true});
		}
		
		
		
		// DELETE COMMENT
		function delete_comment(mc_id)
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
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_comment(\""+mc_id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_comment(mc_id);";
					}
				?>
			}
		}
		
		// DO THE DELETE OF THE COMMENT
		function do_delete_comment(mc_id)
		{
			show_loader('comments');
			//alert($F('permowner'));
			var pars = 'pmode=delete_comment&mc_id='+mc_id+'&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('comments', 'mgr.member.actions.php', {method: 'get', parameters: pars, evalScripts: true});
		}
		
		// LOAD TAGS
		function load_tags()
		{
			show_loader('tags');
			var pars = 'pmode=tags&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('tags', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// LOAD DOWNLOADS
		function load_downloads()
		{
			show_loader('downloads');
			var pars = 'pmode=memberDownloads&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('downloads', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// LOAD SUBS
		function load_subs()
		{
			show_loader('subs');
			var pars = 'pmode=subscriptions&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('subs', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// SWITCH STATUS ON COMMENTS
		function switch_status_tag(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				
				$('tag_sp_'+item_id).hide();
				hide_sp();
				//$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'tagcheck' + item_id;
				var loadpage = "mgr.media.tags.actions.php?mode=ap&mempage=1&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// DELETE COMMENT
		function delete_tag(key_id)
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
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_tag(\""+key_id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_tag(key_id);";
					}
				?>
			}
		}
		// DO DELETE TAG
		function do_delete_tag(key_id)
		{
			show_loader('tags');
			//alert($F('permowner'));
			var pars = 'pmode=delete_tag&key_id='+key_id+'&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('tags', 'mgr.member.actions.php', {method: 'get', parameters: pars, evalScripts: true});	
		}
		
		// LOAD RATINGS
		function load_ratings()
		{
			show_loader('ratings');
			var pars = 'pmode=ratings&mem_id=<?php echo $_GET['edit']; ?>&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('ratings', 'mgr.member.actions.php', {method: 'get', parameters: pars});
		}
		
		// DELETE COMMENT
		function delete_rating(mr_id)
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
						message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_rating(\""+mr_id+"\");close_message();' />",'');
				<?php
					}
					else
					{
						echo "do_delete_rating(mr_id);";
					}
				?>
			}
		}
		
		// DO DELETE RATING
		function do_delete_rating(mr_id)
		{
			show_loader('ratings');
			//alert($F('permowner'));
			var pars = 'pmode=delete_rating&mr_id='+mr_id+'&pass=<?php echo md5($config['settings']['serial_number']); ?>';
			var myAjax = new Ajax.Updater('ratings', 'mgr.member.actions.php', {method: 'get', parameters: pars, evalScripts: true});	
		}
		
		// UPDATE STARS
		function update_rating_stars(rating,id)
		{
			// CHECK FOR IE 8 OR LOWER
			<?php if($browser_name != 'Internet Explorer' or ($browser_name == 'Internet Explorer' and $browser_version >= 9)){ ?>
				Effect.Appear('star_div_'+id,{ duration: 1.0, from: 0.4, to: 1.0 });
			<?php } ?>
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				var updatecontent = "star_div_" + id;
				var loadpage = "mgr.media.ratings.actions.php?mode=stars&rating=" + rating + "&id=" + id;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars});
			}
		}
		
		
		var rollto = [];
		// ROLLOVER STARS
		function rollover_stars(current_star,id)
		{
			clearTimeout(rollto[id]);
			$$('#star_div_'+id+' img').each(function(s)
			{
				if(s.getAttribute('starnumber') <= current_star)
				{
					s.src = 'images/mgr.icon.star.2.png';
				}
				else
				{
					s.src = 'images/mgr.icon.star.0.png';
				}
			});
		}
		
		function rollout_stars_delay(id)
		{
			//alert();
			rollto[id] = setTimeout("rollout_stars("+id+")",200);
		}
		
		// ROLLOUT STARS
		function rollout_stars(id)
		{
			$$('#star_div_'+id+' img').each(function(s)
			{
				s.src = 'images/mgr.icon.star.'+s.getAttribute('initialvalue')+'.png';	
			});
		}
		
		// SWITCH STATUS ON RATING
		function switch_status_rating(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{
				$('rating_sp_'+item_id).hide();
				hide_sp();
				//$(item_type + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'ratingcheck' + item_id;
				var loadpage = "mgr.media.ratings.actions.php?mode=ap&mempage=1&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		// COM SOURCE DROP DOWN UPDATED
		function update_com_source()
		{
			var com_source_value = $('com_source').options[$('com_source').selectedIndex].value;
			if(com_source_value == 2)
			{
				$('commission_slider_div').show();
			}
			else
			{
				$('commission_slider_div').hide();	
			}
		}
		
		// ONCHANGE FOR TICKET SUMMARY IN WORKBOX
		function ticket_summary()
		{
			var ticket_id_value = $('ticket_id').options[$('ticket_id').selectedIndex].value;
			if(ticket_id_value == 0)
			{
				$('summary_p').show();
			}
			else
			{
				$('summary_p').hide();	
			}
		}
		
		// SUBMIT NEW SUPPORT TICKET MESSAGE
		function submit_new_message()
		{
			$('submit_button').setAttribute('disabled','disabled');
			
			$('support_ticket_form').request({
				evalScripts: true,
				onFailure: function() { alert('error'); }, 
				onSuccess: function(transport) {
					//eval(transport.responseText);
					//alert('testing');
					//alert(transport.responseText);
					//$('testresult').update(t.responseText);
					close_workbox();
					load_tickets();
				}
			});
		}
		
		function show_sp(id)
		{
			clearTimeout(status_popup_timeout);
			$(id).show();
			$$('.status_popup').each(function(e){ if(id != e.id){ e.hide(); } });
		}
		
		var status_popup_timeout;
		
		function hide_sp()
		{
			clearTimeout(status_popup_timeout);
			status_popup_timeout = setTimeout(function(){$$('.status_popup').each(function(e){ e.hide(); });},200); // e.fade({ duration: 0.3 });
		}
		
		function clear_sp_timeout()
		{
			clearTimeout(status_popup_timeout);
		}
		
		function write_status(mode,id,curstatus)
		{
			var content = ''
			var div_id = ''
			//alert(curstatus);
			switch(mode)
			{
				case "rating":
					div_id = "rating_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' style='cursor: pointer' onclick=\"switch_status_rating('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' style='cursor: pointer' onclick=\"switch_status_rating('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_b_approved']; ?></div>"; }
				break;
				case "tag":
					div_id = "tag_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' style='cursor: pointer' onclick=\"switch_status_tag('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' style='cursor: pointer' onclick=\"switch_status_tag('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_b_approved']; ?></div>"; }
				break;
				case "comment":
					div_id = "comment_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' style='cursor: pointer' onclick=\"switch_status_comment('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_pending']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' style='cursor: pointer' onclick=\"switch_status_comment('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_b_approved']; ?></div>"; }
				break;
				case "payment":
					//alert(curstatus);
					div_id = "compayment_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_pending mtag' style='cursor: pointer' onclick=\"switch_status_compay('"+id+"',0);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_unpaid']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_approved mtag' style='cursor: pointer' onclick=\"switch_status_compay('"+id+"',1);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_paid']; ?></div>"; }
				break;
			}			
			$(div_id).update(content);
		}
		
		// Load Contributor Sales
		function loadContrSales()
		{			
			var passtophp = $('data_form').serialize();			
			show_loader('contrSalesContainer');

			var pars = 'mid=<?php echo $mgrMemberInfo->mem_id; ?>&'+passtophp;
			var myAjax = new Ajax.Updater(
				'contrSalesContainer', 
				'mgr.contr.sales.php', 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		
		// Open commission window
		function openCompayWorkbox(amount,comID)
		{
			//alert(comID);
			workbox2({'page' : 'mgr.workbox.php?box=compay&memID=<?php echo $_GET['edit']; ?>&comID='+comID+'&amount='+amount});
		}
		
		function msexpire_type_status()
		{
			var msexpire_selected = $('msexpire_type').options[$('msexpire_type').selectedIndex].value;
			if(msexpire_selected == 1)
			{
				show_div('msexpire_date_div');
			}
			else
			{
				hide_div('msexpire_date_div');
			}
		}
		
		// SWITCH PAYMENT STATUS
		function switch_bill_payment_status(item_id,newstatus){
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				$('billstatus_sp_'+item_id).hide();
				hide_bill_sp();
				$('billpaymentstatuscheck' + item_id).innerHTML = "<img src=\"images/mgr.loader.gif\">";
				var updatecontent = 'billpaymentstatuscheck' + item_id;
				var loadpage = "mgr.billings.actions.php?mode=payment_status&id=" + item_id + "&newstatus=" + newstatus;
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
			}
		}
		
		function show_bill_sp(id)
		{
			//alert(id);
			clearTimeout(status_bill_popup_timeout);
			$(id).show();
			$$('.status_popup').each(function(e){ if(id != e.id){ e.hide(); } });
		}
		
		var status_bill_popup_timeout;
		
		function hide_bill_sp()
		{
			clearTimeout(status_bill_popup_timeout);
			status_bill_popup_timeout = setTimeout(function(){$$('.status_popup').each(function(e){ e.hide(); });},200); // e.fade({ duration: 0.3 });
		}
		
		function clear_bill_sp_timeout()
		{
			clearTimeout(status_bill_popup_timeout);
		}
		
		function write_bill_status(mode,id,curstatus)
		{
			var content = ''
			var div_id = ''
			//alert(curstatus);
			switch(mode)
			{
				case "billstatus":
					div_id = "billstatus_sp_"+id;
					if(curstatus != 0){ content+= "<div class='mtag_processing mtag' onclick=\"switch_bill_payment_status('"+id+"',0);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_processing']; ?></div>"; }
					if(curstatus != 1){ content+= "<div class='mtag_paid mtag' onclick=\"switch_bill_payment_status('"+id+"',1);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_paid']; ?></div>"; }
					if(curstatus != 2){ content+= "<div class='mtag_unpaid mtag' onclick=\"switch_bill_payment_status('"+id+"',2);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_unpaid']; ?></div>"; }
					//if(curstatus != 3){ content+= "<div class='mtag_purple' onclick=\"switch_payment_status('"+id+"',3);\" onmouseover='clear_sp_timeout();' onmouseout='hide_sp();'><?php echo $mgrlang['gen_bill']; ?></div>"; }
					if(curstatus != 4){ content+= "<div class='mtag_failed mtag' onclick=\"switch_bill_payment_status('"+id+"',4);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_failed']; ?></div>"; }
					if(curstatus != 5){ content+= "<div class='mtag_refunded mtag' onclick=\"switch_bill_payment_status('"+id+"',5);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_refunded']; ?></div>"; }
					if(curstatus != 6){ content+= "<div class='mtag_cancelled mtag' onclick=\"switch_bill_payment_status('"+id+"',6);\" onmouseover='clear_bill_sp_timeout();' onmouseout='hide_bill_sp();'><?php echo $mgrlang['gen_cancelled']; ?></div>"; }
				break;
			}
			$(div_id).update(content);
		}
	</script>

</head>
<body>
	<?php
		if($_GET['edit'] != "new")
		{
			include("mgr.print.window.php");
		}
	?>
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
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.members.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['members_new_header'] : $mgrlang['members_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['members_new_message'] : $mgrlang['members_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">       
                <?php
					# PULL GROUPS
					$mem_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
					$mem_group_rows = mysqli_num_rows($mem_group_result);
					
					$mem_ms_result = mysqli_query($db,"SELECT ms_id,flagtype,name FROM {$dbinfo[pre]}memberships ORDER BY name");
					$mem_ms_rows = mysqli_num_rows($mem_ms_result);
				?>
                <?php if($_GET['edit'] != "new"){ ?>
                    <div style="margin-bottom: 1px; padding: 10px 20px 20px 20px;">                        
						<div class="tg_header_info" style="float: none; margin: 0; position: relative">
							<?php if(in_array("contr",$installed_addons)){ ?><div style="position: absolute; right: 20px; top: 20px;"><a href="mgr.media.php?owner=<?php echo $mgrMemberInfo->mem_id; ?>" class="actionlink"><?php echo $mgrlang['media_mem_media']; ?></a></div><?php } ?>
							<?php
                                $avatar_width2 = 100;
                            ?>
                            <div id="avatar_summary" style="float: left; background-image: url(images/mgr.loader.gif); background-repeat: no-repeat; background-position: center; min-height: 50px;">
                                <?php
                                    if(file_exists("../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png"))
                                    {
                                        //echo "<img src='../assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png" . "' style='border: 2px solid #ffffff; margin-right: 1px;' width='$avatar_width2' />";
                                        $mem_needed = figure_memory_needed($config['base_path']."/assets/avatars/" . $mgrMemberInfo->mem_id . "_large.png");
                                        if(ini_get("memory_limit"))
                                        {
                                            $memory_limit = ini_get("memory_limit");
                                        }
                                        else
                                        {
                                            $memory_limit = $config['DefaultMemory'];
                                        }
                                        if($memory_limit > $mem_needed)
                                        {
                                            // GO FOR IT
                                            echo "<img src='mgr.display.avatar.php?mem_id=$mgrMemberInfo->mem_id&size=$avatar_width2&ext=$mgrMemberInfo->avatar' style='border: 4px solid #FFF; margin-right: 1px;' class='mediaFrame' />";
                                        }
                                        else
                                        {
                                            echo "<div style='margin: 4px 0 0 10px; padding: 10px; background-color: #fae8e8; width: 200px; border: 1px solid #ba0202;'><img src='images/mgr.icon.mem.summary2.gif' style='border: 2px solid #eeeeee; margin-left: 10px; margin-right: 10px;' width='40' align='left' />$mgrlang[gen_error_20] : <strong>" . $mem_needed . "mb</strong></div>";
                                        }
                                    }
                                    else
                                    { 
                                        echo "<img src='images/mgr.icon.mem.summary.gif' style='border: 4px solid #FFF; margin-right: 1px;' width='$avatar_width2' class='mediaFrame' />";
                                    }
                                ?>
                            </div>
                            <div style="float: left; margin-left: 10px;">
                                <?php
                                    $country_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}countries WHERE country_id = '$address->country'");
                                    $country_rows = mysqli_num_rows($country_result);
                                    $country = mysqli_fetch_object($country_result);
                                    
                                    $state_result = mysqli_query($db,"SELECT name FROM {$dbinfo[pre]}states WHERE state_id = '$address->state'");
                                    $state_rows = mysqli_num_rows($state_result);
                                    $state = mysqli_fetch_object($state_result);
                                ?>
                                
                                <table cellpadding="0" cellspacing="4">
                                    <tr>
                                        <td rowspan="<?php if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language){ echo 5; } else { echo 4; } ?>" nowrap valign="top">
                                            <strong><?php echo @stripslashes($mgrMemberInfo->f_name . " " . $mgrMemberInfo->l_name); ?></strong><br /><a href="mailto:<?php echo @stripslashes($mgrMemberInfo->email); ?>"><?php echo @stripslashes($mgrMemberInfo->email); ?></a> <img src="images/mgr.icon.email.gif" align="absmiddle" style="cursor: pointer; margin-left: 3px;" onclick="message_window('<?php echo $mgrMemberInfo->mem_id; ?>');" />
                                            <br />
                                            <?php
                                                echo $address->address . "<br />";
                                                if($address->address_2){ echo $address->address_2 . "<br />"; }
                                                echo $address->city;											
                                                if($state_rows){ echo ", " . $state->name; }
                                                echo " " . $address->postal_code . "<br />";
                                                if($country_rows){ echo $country->name; }
                                                if($mgrMemberInfo->phone){ echo "<br /><br />".$mgrMemberInfo->phone; }
                                            ?>
                                        </td>
                                        <td rowspan="<?php if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language){ echo 5; } else { echo 4; } ?>" width="10">&nbsp;</td> 
                                        <td nowrap><strong><?php echo $mgrlang['mem_unique_id']; ?>:</strong></td>
                                        <td nowrap><?php echo $mgrMemberInfo->umem_id; ?></td>
                                    </tr>
                                    <tr>
                                        <td nowrap width="80"><strong><?php echo $mgrlang['mem_member_num']; ?>:</strong></td>
                                        <td nowrap><?php echo $mgrMemberInfo->mem_id; ?></td>
                                    </tr>
                                    <tr>
                                        <td nowrap><strong><?php echo $mgrlang['mem_last_login']; ?>:</strong></td>
                                        <td nowrap><?php if($mgrMemberInfo->last_login == "0000-00-00 00:00:00"){ echo $mgrlang['mem_never']; } else { echo $ndate->showdate($mgrMemberInfo->last_login); } ?></td>
                                    </tr>
                                    <tr>
                                        <td nowrap valign="top"><strong><?php echo $mgrlang['mem_signup_date']; ?>:</strong></td>
                                        <td nowrap valign="top"><?php echo $ndate->showdate($mgrMemberInfo->signup_date); ?></td>
                                    </tr>
                                    <?php
										if(in_array('multilang',$installed_addons) and $mgrMemberInfo->language)
										{
									?>
									<tr>
										<td nowrap valign="top"><strong><?php echo $mgrlang['gen_language']; ?>:</strong></td>
										<td nowrap valign="top"><span class="mtag_dblue" style="color: #FFF;"><?php echo ucfirst($mgrMemberInfo->language); ?></span></td>
									</tr>
									<?php
										}
									?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php } ?>  
                <div id="button_bar">
                    <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['mem_tab1']; ?></div>
                    <div class="subsuboff" onclick="bringtofront('14');" id="tab14"><?php echo $mgrlang['mem_tab14']; ?></div>
                    <?php if($mem_ms_rows){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['mem_tab2']; ?></div><?php } ?>
                    <?php
                    	if($_GET['edit'] != "new")
						{
					?>
                    	<div class="subsuboff" onclick="bringtofront('3');" id="tab3"><?php echo $mgrlang['mem_tab3']; ?></div>
                        <div class="subsuboff" onclick="bringtofront('17');load_bills();" id="tab17"><?php echo $mgrlang['gen_bills']; ?></div><?php if(in_array("pro",$installed_addons)){ ?><?php } ?>
                        <div class="subsuboff" onclick="bringtofront('4');" id="tab4"><?php echo $mgrlang['mem_tab4']; ?></div>
                    	<?php if(in_array("rating",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('5');load_ratings();" id="tab5"><?php echo $mgrlang['mem_tab5']; ?></div><?php } ?>
                        <?php if(in_array("commenting",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('13');load_comments();" id="tab13"><?php echo $mgrlang['mem_tab13']; ?></div><?php } ?>
                        <?php if(in_array("tagging",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('15');load_tags();" id="tab15"><?php echo $mgrlang['mem_tab15']; ?></div><?php } ?>
	                    <?php if(in_array("ticketsystem",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('6');load_tickets();" id="tab6"><?php echo $mgrlang['mem_tab6']; ?></div><?php } ?>
						<?php if(in_array("contr",$installed_addons)){ ?><div class="subsuboff" onclick="bringtofront('7'); loadContrSales();" id="tab7"><?php echo $mgrlang['mem_tab17']; ?></div><?php } ?>
						<div class="subsuboff" onclick="bringtofront('18');load_downloads();" id="tab18"><?php echo $mgrlang['gen_downloads']; ?></div>
						<div class="subsuboff" onclick="bringtofront('19');load_subs();" id="tab19"><?php echo $mgrlang['gen_subs']; ?></div>
                    	<div class="subsuboff" onclick="bringtofront('9');load_al(0);" id="tab9"><?php echo $mgrlang['mem_tab9']; ?></div>
                    <?php
                    	}
					?>
                    <div class="subsuboff" onclick="bringtofront('16');" id="tab16"><?php echo $mgrlang['mem_tab16']; ?></div>
                    <?php if($mem_group_rows){ ?><div class="subsuboff" onclick="bringtofront('10');" id="tab10"><?php echo $mgrlang['mem_tab10']; ?></div><?php } ?>
                    <div class="subsuboff" onclick="bringtofront('11');" id="tab11" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['mem_tab11']; ?></div>
                </div>
                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block; padding-bottom: 5px;">                    
                	                 
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_signupdate']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_signupdate_d']; ?></span>
                        </p>
                        <?php 
							$form_signup_date = $ndate->date_to_form($mgrMemberInfo->signup_date);
						?>
                        <select style="width: 50px;" name="signup_month">
							<?php
                                for($i=1; $i<13; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_signup_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 50px;" name="signup_day">
                            <?php
                                for($i=1; $i<=31; $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_signup_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        /
                        <select style="width: 65px;" name="signup_year">
                            <?php
                                for($i=2005; $i<(date("Y")+6); $i++)
								{
                                    if(strlen($i) < 2)
									{
                                        $dis_i_as = "0$i";
                                    }
									else
									{
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_signup_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as))
									{
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        &nbsp;
                        <select style="width: 50px;" name="signup_hour">
                            <?php
                                for($i=0; $i<24; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_signup_date['hour'] == $dis_i_as or ($_GET['edit'] == "new" and date("H") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        :
                        <select style="width: 50px;" name="signup_minute">
                            <?php
                                for($i=1; $i<60; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_signup_date['minute'] == $dis_i_as or ($_GET['edit'] == "new" and date("i") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="f_name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_fname']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_fname_d']; ?>.</span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="f_name" id="f_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->f_name); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="l_name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_lname']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_lname_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="l_name" id="l_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->l_name); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_disname']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_disname_d']; ?>.</span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="display_name" id="display_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->display_name); ?>" />
                    </div>
					<div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_email']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_email_d']; ?></span><br />&nbsp;
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="email" id="email" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->email); ?>" /> <div id="emailWarning" style="display: none;"></div>
                        <?php
                        	if(in_array("pro",$installed_addons))
							{
								if($mgrMemberInfo->email)
								{
									$blockemails = explode("\n",$config['settings']['blockemails']);
									if(in_array($mgrMemberInfo->email,$blockemails))
									{
										echo "<div style='margin-left: 252px;'><span style='color: #bb0000;'>$mgrlang[gen_block_email]: <strong>$mgrMemberInfo->email</strong></span><br />$mgrlang[gen_block_email2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
									}
									else
									{
						?>
                                    <div style="padding-top: 8px;" id="div_block_email">
                                        <a href="javascript:block_email();" class='actionlink'><img src="images/mgr.icon.block.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_block_list']; ?></a>
                                    </div>
                    	<?php
									}
                        		}
							}
						?>
                    </div>
                    
					<?php 
						if(!$config['showMemPasswords'] and $_REQUEST['edit'] != 'new')
						{
					?>
						<div fsrow='1' id="password_div" style="display: none;">
					<?php
						}
						else
						{
					?>
						<div class="<?php fs_row_color(); ?>" fsrow='1' id="password_div">
					<?php
						}
					?>
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_password']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_password_d']; ?></span><br /><br />
                        </p>
                        <?php
							if($_GET['edit'] != "new")
							{
								/*
								$crypt = new encryption_class;
								$decrypt_result = $crypt->decrypt($config['settings']['serial_number'],$mgrMemberInfo->password);
								$errors = $crypt->errors;
								*/
								$decrypt_result = k_decrypt($mgrMemberInfo->password);
							}
						?>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="password" id="password" style="width: 290px; margin-top: 0;" maxlength="50" value="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "<hidden>"; } else { echo $decrypt_result; } ?>" />
                        	<div style="padding-top: 8px;">
                            	<?php
                                	if($mgrMemberInfo->password)
									{
								?>
                                	<!--<a href="#" target="_blank" class='actionlink'><img src="images/mgr.icon.email.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_f_email_pass']; ?></a>-->
                            	<?php
                                	}
								?>
                                <a href="#" class='actionlink' onClick="window.open('mgr.password.form.php', 'PassGen', 'toolbar=no, directories=no, location=no, status=yes, menubar=no, resizable=yes, scrollbars=no, width=270, height=350'); return false;" class="toolslinks" target="_blank"><img src="images/mgr.icon.passgen.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['gen_passgen']; ?></a>
                            </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_company_name']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_company_name_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="comp_name" id="comp_name" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->comp_name); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_website']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_website_d']; ?></span><br /><br />
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="website" id="website" style="width: 290px; margin-top: 0" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->website); ?>" />
						<?php if($mgrMemberInfo->website){ ?>
                        	<div style="padding-top: 8px;">
                            	<a href="<?php echo @stripslashes($mgrMemberInfo->website); ?>" target="_blank" class='actionlink'><img src="images/mgr.icon.visit.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_f_visit']; ?></a>
							</div>		
						<?php } ?>
                    </div>                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_phone']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_phone_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="phone" id="phone" style="width: 290px;" maxlength="50" value="<?php echo @stripslashes($mgrMemberInfo->phone); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_status']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_status_d']; ?></span>
                        </p>
                        <select name="status">
                        	<option value="0" <?php if($mgrMemberInfo->status == 0){ echo "selected"; } ?>><?php echo $mgrlang['gen_closed']; ?></option>
                            <option value="1" <?php if($mgrMemberInfo->status == 1 or $_GET['edit'] == 'new'){ echo "selected"; } ?> ><?php echo $mgrlang['gen_active']; ?></option>
                            <option value="2" <?php if($mgrMemberInfo->status == 2){ echo "selected"; } ?>><?php echo $mgrlang['gen_pending']; ?></option>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1' style="margin-bottom: -5px;">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_notes']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_notes_d']; ?></span>
                        </p>
                        <textarea name="notes" style="width: 450px; height: 100px;"><?php echo @stripslashes($mgrMemberInfo->notes); ?></textarea>
                    </div>
            	</div>
                
                <?php
					if($mem_ms_rows){
						$row_color = 0;
				?>
                <div id="tab2_group" class="group">
                	<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_membership']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_membership_d']; ?></span>
                        </p>
                        <?php
							if($_GET['edit'] != "new")
							{
								$selected_membership = $mgrMemberInfo->membership;
							}
							else
							{
								$selected_membership = 1;
							}							
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($mem_ms = mysqli_fetch_object($mem_ms_result))
							{
								echo "<li><input type='radio' id='$mem_ms->ms_id' class='permcheckbox' name='membership' value='$mem_ms->ms_id' "; if($mem_ms->ms_id == $selected_membership){ echo "checked "; } echo "/> "; if($mem_ms->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_ms->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_ms->flagtype' align='absmiddle' /> "; } echo "<label for='$mem_ms->ms_id'>" . substr($mem_ms->name,0,100) . "</label>";
								if($mem_ms->ms_id == '1')
								{ 
									echo " <span style='color: #888888; font-style:italic'>($mgrlang[membership_default])</span>";
								}
								echo "</li>";
							}
							echo "</ul>";
						?>
                	</div>
					<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_msexpires']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_msexpires_d']; ?></span>
                        </p>
						<select style="float: left;" name="msexpire_type" id="msexpire_type" onchange="msexpire_type_status()">
                        	<option value="0" <?php if($mgrMemberInfo->ms_end_date == '0000-00-00 00:00:00'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_never']; ?></option>
                            <option value="1" <?php if($mgrMemberInfo->ms_end_date != '0000-00-00 00:00:00'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_set_date']; ?>...</option>
                        </select>
                        <div style="float: left; padding-left: 15px; <?php if($mgrMemberInfo->ms_end_date != '0000-00-00 00:00:00'){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="msexpire_date_div">
                            <?php 
								$form_expire_date = $ndate->date_to_form($mgrMemberInfo->ms_end_date);
							?>
                            <select style="width: 132px;" name="msexpire_year">
                                <?php
                                    for($i=2005; $i<(date("Y")+10); $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="msexpire_month">
                                <?php
                                    for($i=1; $i<13; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>
                            <select style="width: 75px;" name="msexpire_day">
                                <?php
                                    for($i=1; $i<=31; $i++){
                                        if(strlen($i) < 2){
                                            $dis_i_as = "0$i";
                                        } else {
                                            $dis_i_as = $i;
                                        }
                                        echo "<option ";
                                        if($form_expire_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                            echo "selected";
                                        }
                                        echo ">$dis_i_as</option>";
                                    }
                                ?>
                            </select>                        
                        </div>
                	</div>
            	</div>
                <?php
					}
				?>
                
                <?php
                	if($_GET['edit'] != 'new')
					{
					$row_color = 0;
				?>
                <div id="tab3_group" class="group" style="padding: 20px;">
                    <div class="fs_row_part2" id="orders_div" style="width: 100%;">
                    <?php
                        # CREATE A DATE OBJECT
                        $orderdate = new kdate;
                        $orderdate->distime = 1;
                        
                        # ORDERS
                        $order_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE {$dbinfo[pre]}orders.member_id = '$mgrMemberInfo->mem_id' AND {$dbinfo[pre]}orders.order_status != '2' AND {$dbinfo[pre]}orders.deleted = 0 ORDER BY {$dbinfo[pre]}orders.order_date DESC");
                        $order_rows = mysqli_num_rows($order_result);
                        if($order_rows)
                        {
                    ?>
                        <table width="100%">
                            <tr>
                                <th><?php echo $mgrlang['gen_t_id']; ?></th>
                                <th align="left"><?php echo $mgrlang['gen_order_num_caps']; ?></th>
                                <th align="left"><?php echo $mgrlang['order_t_invoicenum']; ?></th>
                                <th><?php echo $mgrlang['gen_t_date']; ?></th>
                                <th><?php echo $mgrlang['gen_total_caps'] ?></th>
                                <th><?php echo $mgrlang['order_t_payment']; ?></th>
                                <th><?php echo $mgrlang['gen_t_status']; ?></th>
                                <th></th>
                            </tr>
                            <?php
                                while($order = mysqli_fetch_object($order_result))
                                {
                                    //$order_total = $order->subtotal + $order->tax_cost + $order->shipping_cost;
                                    
                                    # SET THE ROW COLOR
                                    @$row_color++;
                                    if ($row_color%2 == 0)
                                    {
                                        $backcolor = "FFF";
                                    }
                                    else
                                    {
                                        $backcolor = "EEE";
                                    }
                            ?>
                                <tr style="background-color: #<?php echo $backcolor; ?>">
                                    <td align="center"><a href="mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>"><?php echo $order->order_id; ?></a></td>
                                    <td align="left"><a href="mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>"><?php echo $order->order_number; ?></a></td>
                                    <td align="left"><?php echo $order->invoice_number; ?></td>
                                    <td><?php echo $orderdate->showdate($order->order_date); ?></td>
                                    <td align="center"><?php echo $cleanvalues->currency_display($order->total,1); ?></td>
                                    <td align="center">
                                        <div id="paymentstatuscheck<?php echo $order->order_id; ?>">
                                        <?php
                                            switch($order->payment_status)
                                            {
                                                case 0: // PENDING                                                
                                                    echo "<div class='mtag_pending mtag'>$mgrlang[gen_processing]</div>";
                                                break;
                                                case 1: // APPROVED/PAID
                                                    echo "<div class='mtag_approved mtag'>$mgrlang[gen_paid]</div>";
                                                break;
                                                case 2: // INCOMPLETE/NONE/UNPAID
                                                    echo "<div class='mtag_incomplete mtag'>$mgrlang[gen_unpaid]</div>";
                                                break;
												case 3: // BILL LATER
                                                    echo "<div class='mtag_bill mtag'>$mgrlang[gen_bill]</div>";
                                                break;
                                                case 4: // FAILED/CANCELLED
                                                    echo "<div class='mtag_failed mtag'>$mgrlang[gen_failed]</div>";
                                                break;
												case 5: // REFUNDED
                                                    echo "<div class='mtag_refunded mtag'>$mgrlang[gen_refunded]</div>";
                                                break;
                                            }
                                        ?>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div id="orderstatuscheck<?php echo $order->order_id; ?>">
                                        <?php
                                            switch($order->order_status)
                                            {
                                                case 0: // PENDING
                                                    echo "<div class='mtag_pending mtag'>$mgrlang[gen_pending]</div>";
                                                break;
                                                case 1: // APPROVED
                                                    echo "<div class='mtag_approved mtag'>$mgrlang[gen_approved]</div>";
                                                break;
                                                case 2: // INCOMPLETE                                                
                                                    echo "<div class='mtag_incomplete mtag'>$mgrlang[gen_incomplete]</div>";
                                                break;
												case 3: // CANCELLED
                                                    echo "<div class='mtag_cancelled mtag'>$mgrlang[gen_cancelled]</div>";
                                                break;
                                                case 4: // FAILED
                                                    echo "<div class='mtag_failed mtag'>$mgrlang[gen_failed]</div>";
                                                break;
                                            }							
                                        ?>
                                        </div>
                                    </td>
                                    <td align="center"><a href="mgr.orders.edit.php?edit=<?php echo $order->order_id; ?>" class="actionlink">View</a></td>
                                </tr>
                        <?php
                                }
                                echo "</table>";
                            }
                            else
                            {
                                echo "<div style='padding: 10px; font-weight: bold;'>{$mgrlang[mem_mes_08]}</div>";	
                            }
                        ?>
                    </div>                    
            	</div>
                
                <?php $row_color = 0; ?>
                <div id="tab4_group" class="group" style="padding: 20px 28px 20px 20px;">
                	
                    <?php
                        # CREATE A DATE OBJECT
                        $lbdate = new kdate;
                        $lbdate->distime = 1;
                        
                        # ORDERS
                        $lb_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}lightboxes WHERE member_id = '$mgrMemberInfo->mem_id' AND deleted = 0 ORDER BY created DESC");
                        $lb_rows = mysqli_num_rows($lb_result);
                        if($lb_rows)
                        {
                    ?>
                        <div class="fs_row_part2" id="orders_div" style="width: 100%;">
						<table width="100%">
                            <tr>
                                <th><?php echo $mgrlang['gen_t_id']; ?></th>
                                <th align="left"><?php echo $mgrlang['gen_t_lb_name']; ?></th>
                                <th><?php echo $mgrlang['gen_t_date']; ?></th>
                                <th><?php echo $mgrlang['gen_media_caps']; ?></th>
                                <th></th>
                            </tr>
                            <?php
                                while($lb = mysqli_fetch_object($lb_result))
                                {
                                    //$order_total = $order->subtotal + $order->tax_cost + $order->shipping_cost;
                                    
                                    # SET THE ROW COLOR
                                    @$row_color++;
                                    if ($row_color%2 == 0)
                                    {
                                        $backcolor = "FFF";
                                    }
                                    else
                                    {
                                        $backcolor = "EEE";
                                    }
									
									$lbi_result = mysqli_query($db,"SELECT item_id FROM {$dbinfo[pre]}lightbox_items WHERE lb_id = '$lb->lightbox_id'");
                       				$lbi_rows = mysqli_num_rows($lbi_result);
                            ?>
                                <tr style="background-color: #<?php echo $backcolor; ?>">
                                    <td align="center"><a href="mgr.media.php?dtype=lightbox&lbid=<?php echo $lb->lightbox_id; ?>"><?php echo $lb->lightbox_id; ?></a></td>
                                    <td align="left"><a href="mgr.media.php?dtype=lightbox&lbid=<?php echo $lb->lightbox_id; ?>"><?php echo $lb->name; ?></a></td>
                                    <td align="center"><?php echo $lbdate->showdate($lb->created); ?></td>
                                    <td align="center"><?php echo $lbi_rows; ?></td>
                                    <td align="center"><a href="mgr.media.php?dtype=lightbox&lbid=<?php echo $lb->lightbox_id; ?>" class="actionlink">View</a></td>
                                </tr>
                        <?php
                                }
                                echo "</table></div>";
                            }
                            else
                            {
                                echo "<div style='padding: 20px; font-weight: bold;'><img src='images/mgr.notice.icon.white.gif' align='absmiddle'>{$mgrlang[mem_mes_09]}</div>";	
                            }
                        ?>
            	</div>
                <?php				
					}
				?>
                
                <?php $row_color = 0; ?>
                <div id="tab5_group" class="group" style="padding: 20px">
                	<div id="ratings"></div>                	
            	</div>
                <?php $row_color = 0; ?>
                <div id="tab6_group" class="group" style="padding: 10px 20px 20px 20px;">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <div style="padding: 0 0 10px 4px"><a href="javascript:support_ticket_window(<?php echo $mgrMemberInfo->mem_id; ?>);" class="actionlink"><?php echo $mgrlang['mem_new_ticket']; ?></a></div>
                        <div id="tickets_div"></div>
                    </div>
            	</div>
                <?php $row_color = 0; ?>
                <div id="tab7_group" class="group">
                	<!--<div style="padding: 15px 20px 20px 25px;"><a href="mgr.media.php?owner=<?php echo $mgrMemberInfo->mem_id; ?>" class="actionlink"><?php echo $mgrlang['media_mem_media']; ?></a></div>-->
					<div class="contrSalesHeader">
						<p>
							<strong><?php echo $mgrlang['gen_b_display']; ?>:</strong> &nbsp; <input type="checkbox" id="contrSalesPaid" checked="checked" name="contrSalesStatus[]" value="1" onclick="loadContrSales()" class="contrSalesForm" /> <label for="contrSalesPaid"><?php echo $mgrlang['gen_paid']; ?></label> &nbsp;&nbsp; <input type="checkbox" id="contrSalesUnpaid" name="contrSalesStatus[]" checked="checked" value="0" onclick="loadContrSales()" class="contrSalesForm" /> <label for="contrSalesUnpaid"><?php echo $mgrlang['gen_unpaid']; ?></label> &nbsp;&nbsp;
						</p>
					</div>
					<div id="contrSalesContainer"></div>
            	</div>
                <?php $row_color = 0; ?>
                <div id="tab8_group" class="group">
                	this will show all uploads that a member has (photographers add-on).<br />
                    need area to show sales and commissions
            	</div>
                <?php $row_color = 0; ?>
                <div id="tab9_group" class="group">
                	<div id="activity_log"></div>
            	</div>
                <?php
                	$blockips = explode("\n",$config['settings']['blockips']);
					$blockdomains = explode("\n",$config['settings']['blockreferrer']);
					$row_color = 0;
				?>
                <div id="tab11_group" class="group">
                	<?php
						if(in_array("contr",$installed_addons))
						{
					?>
                        <div class="<?php fs_row_color(); ?>" fsrow='1'>
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['mem_f_profviews']; ?>:<br />
								<span><?php echo $mgrlang['mem_f_profviews_d']; ?></span>
							</p>
							<div style="float: left; font-size: 24px; font-weight: bold; padding-top: 6px; color: #999;"><?php echo $mgrMemberInfo->profile_views; ?></div>   
						</div>
						
						<div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['mem_comlevel']; ?>:<br />
                                <span><?php echo $mgrlang['mem_comlevel_d']; ?></span>
                            </p> 
                            <select name="com_source" id="com_source" style="float: left;" onchange="update_com_source();">
                                <option value="1" <?php if($mgrMemberInfo->com_source == 1 or $_GET['edit'] == 'new'){ echo "selected"; } ?>><?php echo $mgrlang['mem_comlevelms']; ?></option>
                                <option value="2" <?php if($mgrMemberInfo->com_source == 2){ echo "selected"; } ?>><?php echo $mgrlang['mem_comlevelcus']; ?></option>
                            </select>
                            <div style="padding: 4px 0 0 10px; float: left; <?php if($mgrMemberInfo->com_source != 2){ echo "display: none"; } ?>" id="commission_slider_div">
                            <?php
								# SLIDER POSITION
								//$config['settings']['avatar_size']
								$sb_multiplier = (135/100);
								
								$commission = ($_GET['edit'] == 'new') ? 0 : $mgrMemberInfo->com_level;
								
								$sb_position = round($commission*$sb_multiplier);
							?>
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
										name="com_level"
										type="text" 
										from="0" 
										to="100" 
										valuecount="60"
										value="<?php echo $commission; ?>"
										typelock="off"
										slide_action="preview"
										drop_action="render_preview" />&nbsp;%
								</div>                            
                            </div>
                        </div>
                        <div class="<?php fs_row_color(); ?>" fsrow='1'>
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['mem_f_compref']; ?>:<br />
                                <span><?php echo $mgrlang['mem_f_compref_d']; ?></span>
                            </p>
                            <?php
								if($config['settings']['compay'])
								{
									$compays = explode(",",$config['settings']['compay']);
							?>
                                <div style="float: left;">
                                    <?php if(in_array("1",$compays)){ ?><input type="radio" name="compay" value="1" id='compay1' onclick="show_div('compay_div');" <?php if($mgrMemberInfo->compay == 1){ echo "checked"; } ?> /> <label for='compay1'><?php echo $mgrlang['webset_f_compay_op1']; ?><label><br />
                                        <div style="padding: 6px; margin: 6px 6px 6px 0px; border: 1px solid #c7d3de; background-color: #dfe6ec; <?php if($mgrMemberInfo->compay != 1){ echo "display: none;"; } ?>" id="compay_div"><?php echo $mgrlang['mem_f_paypal_email']; ?>:<br /><input type="text" name="paypal_email" style="width: 200px;" value="<?php echo $mgrMemberInfo->paypal_email; ?>" /></div>
                                    <?php } ?>
                                    <?php if(in_array("2",$compays)){ ?><input type="radio" name="compay" value="2" id='compay2' <?php if(in_array("1",$compays)){ ?>onclick="hide_div('compay_div');"<?php } ?> <?php if($mgrMemberInfo->compay == 2){ echo "checked"; } ?> /> <label for='compay2'><?php echo $mgrlang['webset_f_compay_op2']; ?></label><br /><?php } ?>
                                    <?php if(in_array("3",$compays)){ ?><input type="radio" name="compay" value="3" id='compay3' <?php if(in_array("1",$compays)){ ?>onclick="hide_div('compay_div');"<?php } ?> <?php if($mgrMemberInfo->compay == 3){ echo "checked"; } ?> /> <label for='compay3'><?php $sel_lang = $config['settings']['lang_file_mgr']; if($config['settings']['compay_other_' . $sel_lang]){ echo $config['settings']['compay_other_' . $sel_lang]; } else { if($config['settings']['compay_other']){ echo $config['settings']['compay_other']; } else { echo $mgrlang['webset_f_compay_op3']; } echo "</label> <a href='mgr.website.settings.php?ep=1&jump=9'>[edit label]</a>"; } } ?>
                                 </div>
                            <?php
								}
								else
								{
									echo "<div style='float: left; padding-top: 12px; color: #bb0000;'>$mgrlang[mem_mes_01] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.website.settings.php?ep=1'>$mgrlang[subnav_website_settings]</a> > <a href='mgr.website.settings.php?ep=1&jump=9'>$mgrlang[webset_tab9]</a></div>";
								}
							?>      
                        </div>
						<?php
							if($config['settings']['contr_fm'] == 5)
							{
						?>
							<div class="<?php fs_row_color(); ?>" fsrow='1'>
								<img src="images/mgr.ast.off.gif" class="ast" />
								<p onclick="support_popup('<?php echo $supportPageID; ?>');">
									<?php echo $mgrlang['mem_f_ftm']; ?>:<br />
									<span><?php echo $mgrlang['mem_f_ftm_d']; ?></span>
								</p>
								<input type="checkbox" name="featured" id="featured" value="1" <?php if($mgrMemberInfo->featured){ echo "checked"; } ?> />     
							</div>
                    <?php
							}
						}
					
						if(in_array("contr",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['mem_f_showcase']; ?>:<br />
								<span><?php echo $mgrlang['mem_f_showcase_d']; ?></span>
							</p> 
							<input type="checkbox" name="showcase" id="showcase" value="1" <?php if(@!empty($mgrMemberInfo->showcase)){ echo "checked"; } ?> />
						</div>
					<?php
						}
					
						if(in_array("pro",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['mem_f_billlater']; ?>:<br />
								<span><?php echo $mgrlang['mem_f_billlater_d']; ?></span>
							</p> 
							<input type="checkbox" name="bill_me_later" id="bill_me_later" value="1" <?php if(@!empty($mgrMemberInfo->bill_me_later)){ echo "checked"; } ?> />
						</div>
					<?php
						}
						if(in_array("creditsys",$installed_addons))
						{
					?>
						<div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
							<img src="images/mgr.ast.off.gif" class="ast" />
							<p onclick="support_popup('<?php echo $supportPageID; ?>');">
								<?php echo $mgrlang['gen_credits']; ?>:<br />
								<span><?php echo $mgrlang['mem_f_crdits_d']; ?></span>
							</p> 
							<input type="text" name="credits" id="credits" value="<?php echo $mgrMemberInfo->credits; ?>" />
						</div>
					<?php
						}
						if($_GET['edit'] != "new")
						{
					?>
                        <div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['mem_f_avatar']; ?>:<br />
                                <span><?php echo $mgrlang['mem_f_avatar_d']; ?></span>
                            </p> 
                            <div id="avatar_box"><div style="color: #990000; padding-top: 6px;" id="avatar_box_inner"><?php echo $mgrlang['gen_error_26']; ?></div></div>
                        </div>
                    <?php
						}
					?>
                    
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_signup_ip']; ?><br />
                        	<span><?php echo $mgrlang['mem_f_signup_ip_d']; ?></span>
                        </p>
                        <div id="div_ip_signup">
                        <?php
							if(in_array($mgrMemberInfo->ip_signup,$blockips) and $mgrMemberInfo->ip_signup)
							{
								echo "<div style='padding-top: 10px;'><span style='color: #bb0000;'>$mgrlang[gen_block_ip]: <strong>$mgrMemberInfo->ip_signup</strong></span><br />$mgrlang[gen_block_ip2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
							}
							else
							{
						?>
                            <span class="groupinfo"><em><?php if($mgrMemberInfo->ip_signup){ echo $mgrMemberInfo->ip_signup; } else { echo $mgrlang['mem_unknown']; } ?></em></span>
                        	<?php if($mgrMemberInfo->ip_signup and in_array("pro",$installed_addons)){ ?><div style="margin-bottom: 10px;"><a href="javascript:block_ip_signup();" class='actionlink'><img src="images/mgr.icon.block.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_block_list']; ?></a></div><?php } ?>
                        <?php
							}
						?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_login_ip']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_login_ip_d']; ?></span>
                        </p>
                        <div id="div_ip_login">
                       	<?php
							if(in_array($mgrMemberInfo->ip_login,$blockips) and $mgrMemberInfo->ip_login)
							{
								echo "<div style='padding-top: 10px;'><span style='color: #bb0000;'>$mgrlang[gen_block_ip]: <strong>$mgrMemberInfo->ip_login</strong></span><br />$mgrlang[gen_block_ip2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
							}
							else
							{
						?>
                        	<span class="groupinfo"><em><?php if($mgrMemberInfo->ip_login){ echo $mgrMemberInfo->ip_login; } else { echo $mgrlang['mem_unknown']; } ?></em></span>
                        	<?php if($mgrMemberInfo->ip_login and in_array("pro",$installed_addons)){ ?><div style="margin-bottom: 10px;"><a href="javascript:block_ip_login();" class='actionlink'><img src="images/mgr.icon.block.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_block_list']; ?></a></div><?php } ?>
                    	<?php
							}
						?>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1' id="email_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_referrer']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_referrer_d']; ?></span>
                        </p>
                        <div id="div_referrer">
                        <?php
							if(in_array($mgrMemberInfo->referrer,$blockdomains) and $mgrMemberInfo->referrer)
							{
								echo "<div style='padding-top: 10px;'><span style='color: #bb0000;'>$mgrlang[gen_block_domain]: <strong>$mgrMemberInfo->referrer</strong></span><br />$mgrlang[gen_block_domain2] <a href='mgr.settings.php?ep=1'>$mgrlang[nav_settings]</a> > <a href='mgr.software.setup.php?ep=1'>$mgrlang[subnav_software_setup]</a> > <a href='mgr.software.setup.php?ep=1&jump=9'>$mgrlang[setup_tab9]</a></div>";
							}
							else
							{
						?>
                        	<span class="groupinfo"><em><?php if($mgrMemberInfo->referrer){ echo "<a href='$mgrMemberInfo->referrer' target='_blank'>$mgrMemberInfo->referrer</a>"; } else { echo $mgrlang['mem_unknown']; } ?></em></span>
                        	<?php if($mgrMemberInfo->referrer and in_array("pro",$installed_addons)){ ?><div style="margin-bottom: 10px;"><a href="javascript:block_referrer();" class='actionlink'><img src="images/mgr.icon.block.gif" border="0" align="absmiddle" /> <?php echo $mgrlang['mem_block_list']; ?></a></div><?php } ?>                       
                        <?php
							}
						?>
                        </div>
                    </div>
            	</div>
                
                <?php
                	if($mem_group_rows)
					{
						$row_color = 0;
				?>
                    <div id="tab10_group" class="group"> 
                        <div class="<?php fs_row_color(); ?>" id="name_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p onclick="support_popup('<?php echo $supportPageID; ?>');">
                                <?php echo $mgrlang['mem_f_groups']; ?>:<br />
                                <span><?php echo $mgrlang['mem_f_groups_d']; ?></span>
                            </p>
                            <?php
                                $plangroups = array();
                                # FIND THE GROUPS THAT THIS ITEM IS IN
                                $mem_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$mgrMemberInfo->mem_id' AND item_id != 0");
                                while($mem_groupids = mysqli_fetch_object($mem_groupids_result))
								{
                                    $plangroups[] = $mem_groupids->group_id;
                                }
                                //echo $mgrMemberInfo->mem_id;
                                echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
                                while($mem_group = mysqli_fetch_object($mem_group_result))
								{
                                    echo "<li><input type='checkbox' id='$mem_group->gr_id' class='permcheckbox' name='setgroups[]' value='$mem_group->gr_id' "; if(in_array($mem_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($mem_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$mem_group->flagtype' align='absmiddle' /> "; } echo "<label for='$mem_group->gr_id'>" . substr($mem_group->name,0,30)."</label></li>";
                                }
                                echo "</ul>";
                            ?>
                        </div>
                    </div>
                <?php
					}
				?> 
                <?php $row_color = 0; ?>
                <div id="tab12_group" class="group">
                	<div style="padding: 20px; font-weight: bold; font-size: 16px; color: #999">Area Coming Soon!</div>
            	</div>
				
				<?php $row_color = 0; ?>
                <div id="tab18_group" class="group" style="padding: 20px">
                	<div id="downloads">Downloads</div>
            	</div> 
				
				<?php $row_color = 0; ?>
                <div id="tab19_group" class="group" style="padding: 20px">
                	<div style="padding: 0 0 10px 4px"><a href="javascript:openAddSubWorkbox();" class="actionlink"><?php echo $mgrlang['mem_add_sub']; ?></a></div>
					<div id="subs">Subscriptions</div>
            	</div> 
                
                <?php $row_color = 0; ?>
                <div id="tab13_group" class="group" style="padding: 20px">
                	<div id="comments"></div>
            	</div> 
                
                <?php $row_color = 0; ?>
                <div id="tab15_group" class="group" style="padding: 20px">
                	<div id="tags"></div>
            	</div>  
                
                <?php
                	$row_color = 0;
				?>
                <div id="tab16_group" class="group">
                	<?php
						if($_GET['edit'] != 'new')
						{
					?>
                        <div class="<?php fs_row_color(); ?>" id="comment_div">
                            <img src="images/mgr.ast.off.gif" class="ast" />
                            <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['mem_f_bio_updated']; ?>:<br />
                            <span class="input_label_subtext"><?php echo $mgrlang['mem_f_bio_updated_d']; ?></span></p>
                            <div style="float: left; padding-top: 4px;"><?php if($mgrMemberInfo->bio_updated != '0000-00-00 00:00:00'){ echo $ndate->showdate($mgrMemberInfo->bio_updated); } else { echo $mgrlang['get_never']; } ?></div>
                        </div>
                    <?php
						}
					?>
                    <div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['mem_f_bio']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['mem_f_bio_d']; ?></span></p>
                        <textarea id="bio_content" name="bio_content" style="width: 600px; height: 300px;"><?php echo $mgrMemberInfo->bio_content; ?></textarea>
                    </div>
                    
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['mem_f_status']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['mem_f_status_d']; ?></span></p>
                        <select name="bio_status">
                        	<option value="0" <?php if($mgrMemberInfo->bio_status == 0 or $_GET['edit'] == 'new'){ echo "selected"; } ?>><?php echo $mgrlang['gen_b_napproved']; ?></option>
                            <option value="1" <?php if($mgrMemberInfo->bio_status == 1){ echo "selected"; } ?> ><?php echo $mgrlang['gen_b_approved']; ?></option>
                            <option value="2" <?php if($mgrMemberInfo->bio_status == 2){ echo "selected"; } ?>><?php echo $mgrlang['gen_pending']; ?></option>
                        </select>
                    </div>
            	</div> 
                
                <?php $row_color = 0; ?>
                <div id="tab17_group" class="group" style="padding: 10px 20px 20px 20px;">
                	<div class="<?php fs_row_color(); ?>" id="name_div">
                        <?php if(in_array("pro",$installed_addons)){ ?><div style="padding: 0 0 10px 4px"><a href="javascript:location.href='mgr.billings.edit.php?edit=new'" class="actionlink"><?php echo $mgrlang['mem_new_bill']; ?></a></div><?php } ?>
                        <div id="bills_div"></div>
                    </div>
            	</div>  
                
                <?php $row_color = 0; ?>
                <div id="tab14_group" class="group">
                	<div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');" style="height: 50px;">
							<?php echo $mgrlang['mem_f_address']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_address_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="address" id="address" style="width: 298px;" maxlength="50" value="<?php echo @stripslashes($address->address); ?>" /><br />
                        <input type="text" name="address_2" id="address_2" style="width: 298px;" maxlength="50" value="<?php echo @stripslashes($address->address_2); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_city']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_city_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <input type="text" name="city" id="city" style="width: 298px;" maxlength="50" value="<?php echo @stripslashes($address->city); ?>" />
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_country']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_country_d']; ?></span>
                        </p>
                        <!--<input type="button" value="sort" onclick="createsort();" /><input type="button" value="colortest" onclick="row_colors();" />-->
                        <select name="country" id="country" style="width: 311px;" onchange="get_regions();">
                        	<option value="0"></option>
							<?php
								# CHECK FOR OTHER LANGUAGES
                                $country_result = mysqli_query($db,"SELECT name,country_id FROM {$dbinfo[pre]}countries WHERE deleted = '0'");
								while($country = mysqli_fetch_object($country_result))
								{
                            		echo "<option value='$country->country_id'";
									if($country->country_id == $address->country)
									{
										echo " selected";
									}
									echo ">";
									
									# PULL CORRECT LANGUAGE
									if($country->{"name_" . $config['settings']['lang_file_mgr']})
									{
										echo $country->{"name_" . $config['settings']['lang_file_mgr']};
									}
									else
									{
										echo $country->name;
									}
									
									echo "</option>";
								}
							?>
                        </select>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_state']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_state_d']; ?></span>
                        </p>
                        <div id="region_div"></div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" fsrow='1'>
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['mem_f_zip']; ?>:<br />
                        	<span><?php echo $mgrlang['mem_f_zip_d']; ?></span>
                        </p>
                        <input type="text" name="postal_code" id="postal_code" style="width: 90px;" maxlength="50" value="<?php echo @stripslashes($address->postal_code); ?>" />
                    </div>
            	</div>
                          
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.members.php');" /><input type="button" onclick="form_submitter();" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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