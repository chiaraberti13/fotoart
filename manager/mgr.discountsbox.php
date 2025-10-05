<?php
	###################################################################
	####	MANAGER DISCOUNTS BOX		                           ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 1-4-2010                                      ####
	####	Modified: 1-4-2010                                     #### 
	###################################################################

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE

		$page = $_GET['page'];
		
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
		
		# INCLUDE MANAGER ADDONS FILE	
		require_once('../assets/includes/addons.php');
		
		$pmode = $_GET['pmode'];
		
		# DETERMINE IF IT IS A NEW ITEM
		if($_GET['item_id'] == "new" or !$_GET['item_id'])
		{
			$item_id = 0;
		}
		else
		{
			$item_id = $_GET['item_id'];
		}
		
		function discountbox($discount)
		{
			global $config, $db, $dbinfo, $active_langs, $mgrlang;			
?>	
			<div class="discountsbox_row divTableRow" id="discountRow_<?php echo $discount['dr_id']; ?>">
				<div class="divTableCell">
					<input type="hidden" name="discount[]" value="<?php echo $discount['dr_id']; ?>" /><?php echo $mgrlang['discounts_buy']; ?> <input type="text" name="discountNumber[]" value="<?php echo $discount['start_discount_number']; ?>" style="width: 50px" /> <?php echo $mgrlang['discounts_save']; ?>
				</div>
				<div class="divTableCell">
					<input type="text" name="discountPercentage[]" value="<?php echo $discount['discount_percent']; ?>" style="width: 50px" /> <span style="font-size: 16px; font-weight: bold;">%</span>
				</div>
				<div class="divTableCell" style="text-align: right"><a href="javascript:deleteDiscount(<?php echo $discount['dr_id']; ?>)" class="actionlink"><img src='images/mgr.icon.delete.png' border='0' align='absmiddle' id="discountbox_delete_<?php echo $discount['dr_id']; ?>" width="14" /> <?php echo $mgrlang['gen_short_delete']; ?></a></div>
			</div>
<?php
    	}
		
		switch($pmode)
		{
			case "firstload":
				
				include_lang();
				
				echo "<div class='divTable' style='width: 100%'>";
					echo "<div class='divTableRow tableHeaderRow'>";
						echo "<div class='divTableCell'>Quantity</div>";
						echo "<div class='divTableCell'>Percentage</div>";
						echo "<div class='divTableCell'></div>";
					echo "</div>";
				
				echo "<div class='discountsbox_row divTableRow' id='discountRow_0' style='display: none;'><div class='divTableCell'></div></div>";
				
				$discountResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}discount_ranges WHERE item_id = '{$_GET[itemID]}' AND item_type = '{$_GET[itemType]}' ORDER BY start_discount_number");
				$discountRows = mysqli_num_rows($discountResult);
				
				//echo 'test'.$itemID; exit;
				
				if($discountRows)
				{
					while($discount = mysqli_fetch_array($discountResult))
					{
						discountbox($discount);
					}
					
					echo "</div>";
					echo "<script>\$('discounts_div').show();</script>";
				}
				else
				{
					echo "<script>\$('discounts_div').hide();</script>";
				}
			break;
			case "delete":
				include_lang();
				
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}discount_ranges WHERE dr_id = '{$_GET[deleteID]}'");
				
				# UPDATE ACTIVITY LOG
				//save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_opgroup'],1,$mgrlang['gen_b_del'] . " > <strong>$opgroup->name ($_GET[delete])</strong>");
			break;
			case "addnew":
				include_lang();
				
				$itemID = ($_GET['itemID']) ? $_GET['itemID'] : 0;
				
				mysqli_query($db,"INSERT INTO {$dbinfo[pre]}discount_ranges (item_type,item_id) VALUES ('{$_GET[itemType]}','{$itemID}')");
				$saveid = mysqli_insert_id($db);
				
				$discountResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}discount_ranges WHERE dr_id = '{$saveid}'");
				$discountRows = mysqli_num_rows($discountResult);
				$discount = mysqli_fetch_array($discountResult);
				
				# UPDATE ACTIVITY LOG
				//save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_opgroup'],1,$mgrlang['gen_b_new'] . " > <strong>($saveid)</strong>");
				
				discountbox($discount);
			break;
		}
?>