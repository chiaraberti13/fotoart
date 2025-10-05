<?php
# ORDERS NAV SETUP
$comp[] = array(
	"nav_order"		=> "3",
	"nav_name"		=> $mgrlang['nav_sales'],
	"nav_id"		=> "sales",
	"link"			=> "mgr.sales.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_orders'],
				"nav_id"		=> "orders",
				"new_win"		=> 0,
				"link"			=> "mgr.orders.php?ep=1",
				"badge"			=> "mgr.badge.orders.png",
				"desc"			=> $mgrlang['subnav_orders_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_billings'],
				"nav_id"		=> "billings",
				"new_win"		=> 0,
				"link"			=> "mgr.billings.php?ep=1",
				"badge"			=> "mgr.badge.invoice.png",
				"desc"			=> $mgrlang['subnav_billings_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_contr_sales'],
				"nav_id"		=> "contrsales",
				"new_win"		=> 0,
				"link"			=> "mgr.commissions.php?ep=1",
				"badge"			=> "mgr.badge.quote.png",
				"desc"			=> $mgrlang['subnav_contr_sales_d']
				)
			)
	);	
?>
