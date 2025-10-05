<?php
	require_once '../assets/includes/session.php';
	
	$page = "contrsales";
	
	// Keep the page from caching
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	error_reporting(0);

	require_once 'mgr.security.php'; // Left this in so that the page couldn't be called directly
	require_once 'mgr.config.php';
	if(file_exists("../assets/includes/db.config.php")){ require_once '../assets/includes/db.config.php'; } else { @$script_error[] = "The db.config.php file is missing."; }
	require_once '../assets/includes/shared.functions.php';
	require_once '../assets/includes/db.conn.php';
	require_once 'mgr.functions.php';
	require_once 'mgr.select.settings.php';
	require_once 'mgr.defaultcur.php';

	include_lang();
	
	require_once '../assets/includes/clean.data.php';
	require_once '../assets/classes/mediatools.php';
	
	if($compayAction)
	{
		//echo $compayAction;	
		$compayStatus = ($compayAction == 'markAsPaid') ? 1 : 0;
		$comIDs = implode(',',$comSales);
		$sql = "UPDATE {$dbinfo[pre]}commission SET compay_status='{$compayStatus}' WHERE com_id IN ({$comIDs})";
		$result = mysqli_query($db,$sql);
	}
	
	if(true)
	{
?>
	<?php
		verify_message($vmessage); // Output message if needed
		
		$runningPaidTotal = 0;
		$runningUnpaidTotal = 0;
		
		$orderDate = new kdate;
        $orderDate->distime = 0;
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		
		$statuses = implode(",",$_GET['contrSalesStatus']);
					
		//echo $statuses;
		
		$zindex = 1000;
		$contrSalesResult = mysqli_query($db,
		"
			SELECT * FROM {$dbinfo[pre]}commission 
			LEFT JOIN {$dbinfo[pre]}invoice_items 
			ON {$dbinfo[pre]}commission.oitem_id = {$dbinfo[pre]}invoice_items.oi_id  
			WHERE {$dbinfo[pre]}commission.contr_id = '{$_GET[mid]}' 
			AND {$dbinfo[pre]}commission.order_status = '1' 
			AND {$dbinfo[pre]}commission.compay_status IN ({$statuses})  
			ORDER BY {$dbinfo[pre]}commission.com_id DESC
		");
		$rows = mysqli_num_rows($contrSalesResult);
		if($rows)
		{
	?>
		<div id="contrSales">
			<!--Test: <?php echo $_GET['mid']; ?>-->
			<table style="width: 100%;">
				<tr>
					<th style="width: 100px;">
						<select name="compayAction" onchange="loadContrSales()">
							<option value=""><?php echo $mgrlang['gen_actions']; ?></option>
							<option value="markAsPaid"><?php echo $mgrlang['mark_as_paid']; ?></option>
							<option value="markAsUnpaid"><?php echo $mgrlang['mark_as_unpaid']; ?></option>
						</select>					
					</th>
					<th><?php echo $mgrlang['gen_t_id']; ?></th>
					<th><?php echo $mgrlang['gen_order_num_caps']; ?></th>
					<th><?php echo $mgrlang['gen_t_item']; ?></th>
					<th><?php echo $mgrlang['gen_t_commission']; ?></th>
					<th><?php echo $mgrlang['gen_t_date']; ?></th>
					<th><?php echo $mgrlang['gen_t_status']; ?></th>
					<th></th>
				</tr>
				<?php
					while($contrSales = mysqli_fetch_assoc($contrSalesResult))
					{
						$zindex--;
						
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
						else
							$mediaStatus = 0;
						
						
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
								
								//echo $contrSales['item_percent']; exit;
								
								//$itemCommission = round(($total*$contrSales['item_percent']*($contrSales['mem_percent']/100)),2);
								$itemCommission = round(($total*($contrSales['item_percent']/100)*($contrSales['mem_percent']/100)),2);
								
																
							break;
							case "cred": // Credit based commission
								$itemCommission = round(($contrSales['com_credits']*$contrSales['item_qty'])*$contrSales['per_credit_value'],2);
							break;
							case "sub": // Subscription download commission
								$itemCommission = $contrSales['com_total'];
							break;	
						}
						
						$commission = $itemCommission;
						
						if($contrSales['compay_status'] == 1)
							$runningPaidTotal+= $itemCommission;
						else
							$runningUnpaidTotal+= $itemCommission;			
						
						@$rowColor++;
						$bgColor = ($rowColor%2 == 0) ? "EEEEEE" : "FFFFFF";
					?>
					<tr style="background-color: #<?php echo $bgColor; ?>">
						<td style="text-align: center;"><input type="checkbox" name="comSales[]" value="<?php echo $contrSales['com_id']; ?>" /></td>
						<td style="text-align: center;"><?php echo $contrSales['com_id']; ?></td>
						<td style="text-align: center;"><?php if($orderNumber){ echo "<a href='mgr.orders.edit.php?edit={$orderID}'>{$orderNumber}</a>"; } else { echo "--"; } ?></td>
						<td>
						<?php
							echo "<a href='mgr.media.php?dtype=search&ep=1&search={$mediaInfo[media_id]}'>";
							if($mediaStatus == 1)
								echo "<img src='mgr.media.preview.php?src={$thumbInfo[thumb_filename]}&folder_id={$mediaInfo[folder_id]}&width=30' class='mediaFrame' style='vertical-align: middle; margin-right: 10px;' />";
							else
								echo "<img src='images/mgr.theme.blank.gif' class='mediaFrame' style='width: 30px; vertical-align: middle; margin-right: 10px;' />";
							echo "</a>";
							
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
								
								if($contrSales['item_type'] == 'digital')
								{
									if($contrSales['item_id'])
									{
										$dspResult = mysqli_query($db,"SELECT name,ds_id FROM {$dbinfo[pre]}digital_sizes WHERE ds_id = '{$contrSales[item_id]}'");
										$dsp = mysqli_fetch_assoc($dspResult);
										echo " : <a href='mgr.products.edit.php?edit={$dsp[ds_id]}'>{$dsp[name]}</a>";
									}
									else
										echo " : {$mgrlang[gen_base_price]}";
								}
							}
							
							//({$mgrlang[gen_t_id]}: {$mediaInfo[media_id]}) // ID For the media
						?>
						</td>
						<td style="text-align: center;"><?php echo $cleanvalues->currency_display($commission,1); ?></td>
						<td style="text-align: center;"><?php echo $orderDate->showdate($contrSales['order_date']); ?></td>
						<td style="text-align: center; width: 110px; position: relative;">
							<div style='width: 110px;'><!-- FORCE THE COLUMN WIDTH --></div>
							<div class='status_popup' id='compayment_sp_<?php echo $contrSales['com_id']; ?>' style="z-index: <?php echo $zindex; ?>; display: none; padding-left: 6px; width: 86px;" onmouseout="hide_sp();" onmouseover="clear_sp_timeout();"></div>
							<div id="compaycheck<?php echo $contrSales['com_id']; ?>" style="position: relative; z-index: <?php echo $zindex; ?>; margin-left: 17px; margin-top: -10px;">
								<?php
									switch($contrSales['compay_status'])
									{
										case 0: // UNPAID
											$tag_label = $mgrlang['gen_unpaid'];
											$mtag = 'mtag_processing';
										break;
										case 1: // APPROVED
											$tag_label = $mgrlang['gen_paid'];
											$mtag = 'mtag_paid';
										break;
									}
								?>
							  <div class='<?php echo $mtag; ?> mtag' style='cursor: pointer' onmouseover="show_sp('compayment_sp_<?php echo $contrSales['com_id']; ?>');write_status('payment','<?php echo $contrSales['com_id']; ?>',<?php echo $contrSales['compay_status']; ?>)"><?php echo $tag_label; ?></div>
							</div>
						</td>
						<td style="text-align: center;"><a href="#" class='actionlink' onclick="delete_commission(<?php echo $contrSales['com_id']; ?>);"><?php echo $mgrlang['gen_short_delete']; ?></a> <a href="#" class='actionlink' onclick="openCompayWorkbox('<?php echo $itemCommission; ?>','<?php echo $contrSales['com_id']; ?>');"><?php echo $mgrlang['gen_pay']; ?></a></td>
					</tr>
				<?php
					}
				?>
			</table>
		</div>
		<div class="contrSalesFooter">Paid: <span class="paid"><?php echo $cleanvalues->currency_display($runningPaidTotal,1); ?></span> Unpaid: <span class="unpaid"><?php echo $cleanvalues->currency_display($runningUnpaidTotal,1); ?></span> &nbsp;&nbsp;&nbsp; <a href="#" class='actionlink' onclick="openCompayWorkbox('<?php echo $runningUnpaidTotal; ?>',0);"><?php echo $mgrlang['gen_pay']; ?></a></div>
	<?php
		}
		else
			echo "<div style='padding: 15px;'><img src='images/mgr.notice.icon.white.gif' align='absmiddle' /><strong> &nbsp; $mgrlang[no_sales]</strong></div>";
	}
	else
		echo "<div style='padding: 15px;'><img src='images/mgr.notice.icon.white.gif' align='absmiddle' /><strong> &nbsp; $mgrlang[no_sales]</strong></div>";
?>