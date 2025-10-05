<?php
	###################################################################
	####	COMMISSIONS EDIT AREA                                  ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 10-2-2012                                     ####
	####	Modified: 10-2-2012                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "contrsales";
		$lnav = "sales";
		
		$supportPageID = '357';
	
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
		require_once('../assets/includes/photo.puzzle.inc.php');							# FP
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		require_once('../assets/classes/mediatools.php');				# INCLUDE MEDIA TOOLS CLASS
		
		require_once '../assets/includes/clean.data.php';
		require_once 'mgr.defaultcur.php';
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			
			$orderDate = new kdate;
			$orderDate->distime = 0;
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS

			$contrSalesResult = mysqli_query($db,
			"
				SELECT * FROM {$dbinfo[pre]}commission 
				LEFT JOIN {$dbinfo[pre]}invoice_items 
				ON {$dbinfo[pre]}commission.oitem_id = {$dbinfo[pre]}invoice_items.oi_id  
				WHERE {$dbinfo[pre]}commission.com_id = '{$_GET[edit]}'
			");
			$rows = mysqli_num_rows($contrSalesResult);
			$contrSales = mysqli_fetch_assoc($contrSalesResult);
			
			$memberResult = mysqli_query($db,"SELECT f_name,l_name,email,mem_id FROM {$dbinfo[pre]}members WHERE mem_id = '{$contrSales[contr_id]}'");
           	$memberRows = mysqli_num_rows($memberResult);
			$member = mysqli_fetch_assoc($memberResult);
			
			if($contrSales['invoice_id']) // Part of an order - Get the order number and ID
			{						
				$orderResult =  mysqli_query($db,
				"
					SELECT {$dbinfo[pre]}orders.order_number,{$dbinfo[pre]}orders.order_id FROM {$dbinfo[pre]}orders 
					LEFT JOIN {$dbinfo[pre]}invoices 
					ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id								
					WHERE {$dbinfo[pre]}invoices.invoice_id = '{$contrSales[invoice_id]}'
				");
				$order = mysqli_fetch_assoc($orderResult);
				
				$orderNumber = $order['order_number'];
				$orderID = $order['order_id'];
			}
			else
				$orderNumber = false;
			
			switch($contrSales['comtype']) // Type of purchase or download
			{
				default:
				case "cur": // Currency based payment
					$total = ($contrSales['com_total']*$contrSales['item_qty']);
					
					if($contrSales['item_percent'] == 0) // Change a 0 to a 100%
						$contrSales['item_percent'] = 100;
					
					//FP
					$queryProduct = tep_db_query("SELECT b.item_id as product_id
													FROM {$dbinfo['pre']}commission as a
													LEFT JOIN
													{$dbinfo['pre']}invoice_items as b ON a.oitem_id = b.oi_id
													where a.com_id = ".$_GET['edit']);
					$aQueryProduct = tep_db_fetch_array($queryProduct);
					$bPP = is_photo_puzzle($aQueryProduct['product_id']);
					
					if(!$bPP) $itemCommission = round(($total*($contrSales['item_percent']/100)*($contrSales['mem_percent']/100)),2);
					else $itemCommission = round(($total*($contrSales['mem_percent']/100)),2);													
				break;
				case "cred": // Credit based commission
					$itemCommission = round(($contrSales['com_credits']*$contrSales['item_qty'])*$contrSales['per_credit_value'],2);
				break;
				case "sub": // Subscription download commission
					$itemCommission = $contrSales['com_total'];
				break;	
			}
			
			$commission = $itemCommission;
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":							
				//$save_date = $_POST['posted_year']."-".$_POST['posted_month']."-".$_POST['posted_day']." " .$_POST['posted_hour']. ":" .$_POST['posted_minute']. ":00";	
				//$save_date = $ndate->formdate_to_gmt($save_date);
				//echo $save_date; exit;
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# STIP ANY HTML THAT IS INSERTED
				$comment = trim(strip_tags($comment));
				
				if($originalCompayStatus != $compay_status and $compay_status == 1)
					$addSQL = ',pay_date=now()';
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}commission SET 
							compay_status='{$compay_status}'{$addSQL}
							WHERE com_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_contr_sales'],1,$mgrlang['gen_b_ed'] . " > <strong>($saveid)</strong>");
				
				# FIND OUT HOW MANY MORE ARE PENDING
				//$_SESSION['pending_media_comments'] = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mc_id) FROM {$dbinfo[pre]}media_comments WHERE status = '0'"));
				
				header("location: mgr.commissions.php?mes=edit"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_comments']; ?></title>
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
    <!-- MESSAGE WINDOW JS -->
	<script type="text/javascript" src="mgr.js.messagewin.php"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INNOVA EDITOR -->
	<script type="text/javascript" src="../assets/javascript/innovaeditor/scripts/innovaeditor.js"></script>
	<script language="javascript">	
		function fixspaces(){
			$('url').value = removeSpaces($('url').value);
		}
		function form_sumbit(){
			// REVERT BACK
			$('commnent_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.commissions.edit.php?action=save_new" : "mgr.commissions.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					//js_validate_field("comment","media_comments_f_com",1);			
			?>
				// FIX SPACES
				//fixspaces();

				//$('data_form').action = "<?php echo $action_link; ?>";
				//$('data_form').submit();
			<?php
				}
			?>
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
		});	
		
		// START MEMBER DETAILS PANEL
		var start_panel;
		function start_mem_panel(id)
		{
			var mem_panel = 'more_info_' + id;
			$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
			start_panel = setTimeout("show_div_fade_load('" + mem_panel + "','mgr.members.dwin.php?id="+id+"','_content')",'550');
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
		function cancel_mem_panel(id)
		{
			clearTimeout(start_panel);
			$$('.mem_details_win').each(function(s) { s.setStyle({display: "none"}) });
			$("more_info_" + id + "_content").update('<img src="images/mgr.loader.gif" style="margin: 40px;" />');
		}
		
		// Open commission window
		function openCompayWorkbox(amount,comID,memID)
		{
			workbox2({'page' : 'mgr.workbox.php?box=compay&memID='+memID+'&comID='+comID+'&amount='+amount});
		}
		
		function updateStatusDropdown()
		{
			var options = $$('select#compay_status option')
			options[0].selected = true;
		}
		
		// SWITCH STATUS ON COMMISSIONS
		function switch_status_compay(item_id,newstatus){
			//alert(newstatus);
			if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
			{
				demo_message();
			}
			else
			{	
				//$('compayment_sp_'+item_id).hide();
				//hide_sp();				
				var updatecontent = 'paidDate';				
				var loadpage = "mgr.commissions.actions.php?mode=updateCompayStatus&id=" + item_id + "&newstatus=" + newstatus + "&pass=<?php echo md5($config['settings']['serial_number']); ?>&func=updateStatusDropdown&returnMode=paidDate";
				var pars = "";
				var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});				
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
		<?php include('mgr.shortcuts.cont.php'); ?>
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # CHECK FOR DEMO MODE
            //demo_message($_SESSION['admin_user']['admin_id']);					
        ?>
            <form name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.quote.png" class="badge" />
                <p><strong><?php echo $mgrlang['contributor_sale']; ?></strong><br /><span><?php echo $mgrlang['contributor_sale_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <?php
						if($contrSales['omedia_id'])
						{
							try
							{
								$media = new mediaTools($contrSales['omedia_id']);
								$mediaInfo = $media->getMediaInfoFromDB();
								$thumbInfo = $media->getIconInfoFromDB();										
								$verify = $media->verifyMediaSubFileExists('icons');										
								$mediaStatus = $verify['status'];
							}
							catch(Exception $e)
							{
								$mediaStatus = 0;
							}
						}
					?>
					<div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_comments_f_med']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['media_comments_f_med_dXXXX']; ?></span></p>
                        <div style="width: 300px; float: left;">
							<?php
								if($mediaStatus == 1)
								{
							?>
								<img src="mgr.media.preview.php?src=<?php echo $thumbInfo['thumb_filename']; ?>&folder_id=<?php echo $mediaInfo['folder_id']; ?>&width=150" class="mediaFrame" style="float: left; margin-right: 10px;" />
							<?php
								}
								else
								{
									echo "<img src='images/mgr.theme.blank.gif' style='width: 150px;' class='mediaFrame' style='float: left; margin-right: 10px;' />";
								}
							?>
						</div>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['contrsales_f_io']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_io_d']; ?></span></p>
                        <?php
							if($contrSales['dl_sub_id'])
								echo "<strong>{$mgrlang[gen_dig_sub_dl]}</strong> : <a href='mgr.media.php?dtype=search&ep=1&search={$contrSales[omedia_id]}'>{$mgrlang[gen_medianame_media]} {$contrSales[omedia_id]}</a>";
							else
							{
								echo "<strong>{$contrSales[item_type]}</strong>";
									
								if($contrSales['item_type'] != 'digital')
								{
									if($contrSales['item_type'] == 'print')
									{								
										$printResult = mysqli_query($db,"SELECT item_name,print_id FROM {$dbinfo[pre]}prints WHERE print_id = '{$contrSales[item_id]}'");
										$print = mysqli_fetch_assoc($printResult);
										echo " : <a href='mgr.prints.edit.php?edit={$print[print_id]}'>{$print[item_name]}</a>";
									}
									if($contrSales['item_type'] == 'product')
									{								
										$prodResult = mysqli_query($db,"SELECT item_name,prod_id FROM {$dbinfo[pre]}products WHERE prod_id = '{$contrSales[item_id]}'");
										$prod = mysqli_fetch_assoc($prodResult);
										echo " : <a href='mgr.products.edit.php?edit={$prod[prod_id]}'>{$prod[item_name]}</a>";
									}
								}
							}
						?>
                    </div>
                    <div class="<?php fs_row_color(); ?>">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['media_comments_f_mem']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_mem_d']; ?></span></p>
                        <div style="white-space: nowrap; margin-top: 0; float: left;">
                        <?php
                            if(file_exists("../assets/avatars/" . $contrSales['contr_id'] . "_small.png"))
                            {
                                echo "<img src='../assets/avatars/" . $contrSales['contr_id'] . "_small.png?rmd=" . create_unique() . "' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                            }
                            else
                            {
                                echo "<img src='images/mgr.no.avatar.gif' width='19' style='vertical-align: middle; margin-right: 5px;' class='mediaFrame' />";
                            }
                        ?>
                        <strong><a href="mgr.members.edit.php?edit=<?php echo $member['mem_id']; ?>" class="editlink" onmouseover="start_mem_panel(<?php echo $member['mem_id']; ?>);" onmouseout="cancel_mem_panel(<?php echo $member['mem_id']; ?>);"><?php echo $member['f_name']." ".$member['l_name']; ?></a></strong> <?php echo "(<a href='mailto:{$member[email]}'>{$member[email]}</a>) <img src='images/mgr.icon.email.gif' align='absmiddle' style='cursor: pointer; margin-left: 6px;' onclick='message_window({$member[mem_id]});' />"; ?>
                        </div>
                        
                        <div style="float: left;"><!-- USED TO GET CORRECT ALIGNMENT - WINDOW AFTER NAME -->
                            <div id="more_info_<?php echo $contrSales['contr_id']; ?>" style="display: none; margin-left: -14px" class="mem_details_win">
                                <div class="mem_details_win_inner">
                                    <img src="images/mgr.detailswin.arrow.gif" style="position: absolute; margin: 16px 0 0 -9px;" />
                                    <div id="more_info_<?php echo $contrSales['contr_id']; ?>_content" style="overflow: auto; border: 1px solid #fff"><img src="images/mgr.loader.gif" style="margin: 40px;" /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['gen_payment_status']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_ps_d']; ?></span></p>
                        <div style="float: left;">
							<input type="hidden" name="originalCompayStatus" value="<?php echo $contrSales['compay_status']; ?>" />
							<select name="compay_status" id="compay_status">
								<option value="1" <?php if($contrSales['compay_status'] == 1) echo "selected='selected'"; ?> ><?php echo $mgrlang['gen_paid']; ?></option>
								<option value="0" <?php if($contrSales['compay_status'] == 0) echo "selected='selected'"; ?>><?php echo $mgrlang['gen_unpaid']; ?></option>
							</select>
						</div>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['order_f_ordernum']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_on_d']; ?></span></p>
                        <?php if($orderNumber){ echo "<a href='mgr.orders.edit.php?edit={$orderID}'>{$orderNumber}</a>"; } else { echo "--"; } ?>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['contrsales_f_com']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_com_d']; ?></span></p>
                        <div style="float: left;">
							<span class="paid" style="color: #060; font-size: 14px; font-weight: bold;"><?php echo $cleanvalues->currency_display($commission,1); ?></span> &nbsp;&nbsp; <a href="#" class='actionlink' onclick="openCompayWorkbox('<?php echo $itemCommission; ?>','<?php echo $contrSales['com_id']; ?>','<?php echo $contrSales['contr_id']; ?>');"><?php echo $mgrlang['gen_pay']; ?></a>
							<!--<br /><br />Commission Calculated Example-->
						</div>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['contrsales_f_sd']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_sd_d']; ?></span></p>
                        <?php echo $orderDate->showdate($contrSales['order_date']); ?>
                    </div>
					<div class="<?php fs_row_color(); ?>" id="comment_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['contrsales_f_dp']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['contrsales_f_dp_d']; ?></span></p>
                        <div style="float: left;" id="paidDate"><?php if($contrSales['pay_date'] != '0000-00-00 00:00:00') echo $orderDate->showdate($contrSales['pay_date']); else echo "--"; ?></div>
                    </div>
                </div>
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.commissions.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
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