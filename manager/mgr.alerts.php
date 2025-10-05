<?php
	###################################################################
	####	MANAGER ALERTS                                         ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 3-18-2008                                     ####
	####	Modified: 3-18-2008                                    #### 
	###################################################################
	
	require_once('../assets/includes/session.php'); # INCLUDE THE SESSION START FILE
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	error_reporting(0);
	
	# INCLUDE SECURITY CHECK FILE
	require_once('mgr.security.php');
	
	# INCLUDE MANAGER CONFIG FILE
	require_once('mgr.config.php');
	
	# INCLUDE DATABASE CONFIG FILE
	if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
	
	# INCLUDE DATABASE CONNECTION FILE
	require_once('../assets/includes/db.conn.php');
	
	# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
	require_once('../assets/includes/shared.functions.php');
	
	# INCLUDE TWEAK FILE
	require_once('../assets/includes/tweak.php');
	
	# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.functions.php');
	
	# SELECT THE SETTINGS DATABASE
	require_once('mgr.select.settings.php');
	
	# INCLUDE THE LANGUAGE FILE	
	include_lang();

	//echo "my test";
	
	$_SESSION['testing']['alertLoadTimes']++;
	
	if($_GET['recent']){
?>
	<img src="images/mgr.alert.point.png" style="position: absolute; clear: both; margin: -18px 0 0 167px;" />
    <div id="alertsinner">
        <div style='float: right;'><a href='javascript:close_alerts2()'><img src='images/mgr.button.close2.png' border='0' /></a></div>
        <?php
            if(count($_SESSION['sesalerts']) > 0){
                echo "<script language='javascript' type='text/javascript'>hl_alert_icon();</script>\n";
                $rev = array_reverse($_SESSION['sesalerts']);
                $slice = array_splice($rev,0,8);
                $x=0;
                foreach($slice as $value){
                    echo "$value";
                    if($x < count($slice)-1){ echo "<hr />"; }
                    $x++;
                }
                unset($_SESSION['sesalerts']); // = $slice;
            } else {
                echo $mgrlang['gen_noalerts'];
            }
        ?>
    </div>
<?php
		exit;
	}	
	
	$alert=array();
	
	# CHECK IF THE DATABASE NEEDS TO BE BACKED UP
	$next_backup = date("Y-m-d H:i:00",strtotime($config['settings']['last_backup']." +".$config['settings']['backup_days']." day"));
	if($next_backup < gmdate("Y-m-d H:i:00") and $config['settings']['backup_days'] > 0){
		
		//$_SESSION['testing']['doBU'] = 'yes';
		
		$sql = "UPDATE {$dbinfo[pre]}settings SET last_backup='" . gmt_date() . "' where settings_id  = '1' LIMIT 1";
		$result = mysqli_query($db,$sql);
		
		$backup_inc = 1;
		$backupmode = "backup";
		//$perm = md5($config['settings']['serial_number']);
		include('mgr.sql.backup.php');		
		# UPDATE THE DATABASE
		$ndate = new kdate;
		$alert[] = $mgrlang['gen_dbbackup'] . " " . $ndate->showdate(gmt_date());
		
		save_activity("0",$mgrlang['subnav_software_setup'],1,"<strong>".$mgrlang['gen_dbbackup2']."</strong>");				
	}
	
	# CLEAN OUT OLD CACHE FILES
	if($config['cleanupCacheImages'] and !$_SESSION['oneTimeCacheCleanup'])  
	{
		$cacheTime = gmdate("U")-$config['cacheImagesTime'];
		
		$cachePath = '../assets/cache/*.jpg';		
		$cacheFiles = glob($cachePath);
		
		if($cacheFiles)
		{			
			foreach($cacheFiles as $cFile)
			{	
				$fileTime = filemtime($cFile);	
				if($cacheTime > $fileTime)
				{
					//$alert[] = 'yes';
					@unlink($cFile);
				}
			}
		}
		
		$_SESSION['oneTimeCacheCleanup'] = true;
	}
	
	
	# CLEAN UP OLD CARTS
	//!$_SESSION['oneTimeCartCleanup'] and 	
	if(!$_SESSION['oneTimeCartCleanup'] and $config['settings']['delete_carts'] > 0)
	{		
		// Delete Before Date
		$adandonedCartDate = date("Y-m-d H:i:00",strtotime("-{$config[settings][delete_carts]} days"));

		// Find orders		
		$orderResult = mysqli_query($db,"SELECT order_number,order_id FROM {$dbinfo[pre]}orders WHERE order_date < '{$adandonedCartDate}' AND order_status = 2");	
		if($numOfOrders = mysqli_num_rows($orderResult))
		{
			//$alert[] = 'numorders: '.$numOfOrders; // Testing
			while($order = mysqli_fetch_array($orderResult))
			{	
				$invoiceResult = mysqli_query($db,"SELECT invoice_id,payment_status FROM {$dbinfo[pre]}invoices WHERE order_id = '{$order[order_id]}'");
				if($numOfInvoices = mysqli_num_rows($invoiceResult))
				{
					while($invoice = mysqli_fetch_array($invoiceResult))
					{					
						if($invoice['payment_status'] == 2)
						{
							$deletedCarts++;
							
							@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}invoices WHERE order_id = '{$order[order_id]}'"); // Delete Orders						
							
							$invoiceItemResult = mysqli_query($db,"SELECT oi_id,invoice_id FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '{$invoice[invoice_id]}'");
							if($numOfInvoiceItems = mysqli_num_rows($invoiceItemResult))
							{
								while($invoiceItem = mysqli_fetch_array($invoiceItemResult))
								{
									@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}invoice_options WHERE invoice_item_id = '{$invoiceItem[oi_id]}'"); // Delete invoice item options
								}
							}							
							
							@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}invoice_items WHERE invoice_id = '{$invoice[invoice_id]}'"); // Delete invoicce items
							
							@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}orders WHERE order_id = '{$order[order_id]}'"); // Delete invoicce items
							
							//$alert[] = $order['order_id'];
							
						}
					}
				}
			}
		}
		
		if($deletedCarts > 0)
		{
			$cartCleanupMessage = "Database cleanup. {$deletedCarts} abandoned carts deleted!";
			$alert[] = $cartCleanupMessage;
		
			$ndate = new kdate;
			//$alert[] = $mgrlang['gen_dbbackup'] . " " . $ndate->showdate(gmt_date());		
			save_activity("0",$mgrlang['subnav_software_setup'],1,"<strong>{$cartCleanupMessage}</strong>");
		}
		
		$_SESSION['oneTimeCartCleanup'] = true;			
	}
	
	
	
	
	
	# CHECK IF GALLERY NAV NEEDS TO BE REBUILT
	if($config['settings']['menubuild'] == 0 and $_SESSION['menubuildalert'] == 0)
	{
		$alert[] = "{$mgrlang[gen_error_29]} <a href=\"javascript:workbox2({page: 'mgr.workbox.php?box=menuBuilder'});close_alerts2()\">{$mgrlang[gen_error_29b]}</a>";
		$_SESSION['menubuildalert'] = 1;
	}
	
	//$alert[] = "Just testing!!!";
	
	# DO CLEANUPS HERE
		// CHECK LAST CLEANUP TIME
		// DO CLEANUPS EVERY MONTH
		
		// CHECK DIGITAL SIZES, IF THEY ARE INACTIVE AND NONE HAVE BEEN PURCHASED THEN DELETE
	
	
	# CLEAN ACTIVITY LOG
		//$config['ActivityLogDays'];
		//$alert[] = "Your activity log has been cleaned up";
	
	
	# DELETE ABANDONDED CARTS
	
	# NOTIFY IF PENDING AVATAR APPROVALS
	
	# CHECK KTOOLS.NET FOR NEW MESSAGES
	
	# BUILD THE MENU - ADD FIELD TO DO DB CALLED build_menu. IT IS 0 FOR NEED A NEW BUILD AND 1 FOR IT IS OK. CHECK FOR THAT HERE.
	
	# USE SESSIONS TO MAKE SURE SOME THINGS ONLY RUN ONCE WHEN LOGGED IN INSTEAD OF OVER AND OVER
	
	//$alert[] = "testing123";
	//$alert[] = $next_backup;
	//$alert[] = "You have a new order for <strong>$20</strong> from <strong>Jon Doe</strong>" . count($_SESSION['sesalerts']);
	//$alert[] = "<strong>Bill Doe</strong> just signed up as a <strong>Photographer</strong>";
	//$alert[] = "Your database has been backed up.";
	//$alert[] = "This is just a fake alert to test how well this works.";
	//$alert[] = "This is a really cool alert system fsdfds.";
	
	if(!isset($_SESSION['sesalerts'])){
		$_SESSION['sesalerts'] = $alert;
	} else {
		foreach($alert as $value){
			$_SESSION['sesalerts'][] = $value;
		}
	}
	
	
	if(!empty($alert))
	{
?>
<script language="javascript" type="text/javascript">
	<?php
		if(!empty($config['settings']['display_alerts'])){
			echo "myalerts = new Array;";
			$x=0;
			foreach($alert as $value){
				echo "myalerts[$x]= '$value';";
				$x++;
			}			
			$totaltime = 9000*count($alert);
			echo "show_alerts($totaltime);";
		} else{
			echo "\$('alerticon').src='images/mgr.alert.icon.png';";
		}
	?>
</script>
<img src="images/mgr.alert.point.png" style="position: absolute; clear: both; margin: -18px 0 0 167px;" />
<div id="alertsinner"></div>
<?php
	} else {
?>
<script language="javascript" type="text/javascript">
	<?php
		if(count($_SESSION['sesalerts']) < 1)
		{
	?>
		$('alerticon').src="images/mgr.alert.icon.gray.png";
	<?php
		}
	?>
</script>
<?php
	}
?>