<?php
	###################################################################
	####	WP: STATS : VERSION 1.0                                ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-21-2008                                     ####
	####	Modified: 1-26-2010                                    #### 
	###################################################################

	error_reporting(E_ALL ^ E_NOTICE); // All but notices

	define('BASE_PATH',dirname(dirname(dirname(dirname(__FILE__))))); // Define the base path
	
	# GRAB THE PANEL MODE
	$panel_mode = ($_GET['panel_mode']) ? $_GET['panel_mode']: $panel_mode;

	# INCLUDE THE SESSION FILE IF THE MODE IS OTHER THAN PRELOAD
	if($panel_mode != "preload"){
		# INCLUDE SESSION FILE
		require_once('../../../assets/includes/session.php');
		$panel_language = ($_SESSION['sess_mgr_lang']) ? $_SESSION['sess_mgr_lang']: 'english';
		# GRAB WIDGET LANGUAGE FILE
		if(file_exists('../../../assets/languages/'.$panel_language.'/lang.widgets.php'))
		{
			require_once('../../../assets/languages/'.$panel_language.'/lang.widgets.php');
		}
		else
		{
			require_once('../../../assets/languages/english/lang.widgets.php');
		}
		# GRAB THE CALENDAR LANGUAGE FILE
		if(file_exists('../../../assets/languages/'.$panel_language.'/lang.calendar.php'))
		{
			require_once('../../../assets/languages/'.$panel_language.'/lang.calendar.php');
		}
		else
		{
			require_once('../../../assets/languages/english/lang.calendar.php');
		}
	}
	
	# INCLUDE THE LANGUAGE INFO	IN THE PANEL INSTEAD OF READING FROM THE LANGUAGE FILE
	/*
	switch($panel_language){
		default:
		case "english": // ALL YOUR LANGUAGE FOR THIS PANEL SHOULD GO BELOW
			# structure should be $wplang[{panel_id}_{description}] = "YOUR LANGUAGE";
			$wplang['notes_title'] 	= "Notes";
		break;
		case "spanish":
			$wplang['notes_title'] 	= "Panel En blanco";
		break;
		case "german":
			$wplang['notes_title'] 	= "Leerplatte";
		break;
	}
	*/
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['stats_title'];
	$panel_id = basename(dirname(__FILE__)); // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
	$panel_id = preg_replace("[^A-Za-z0-9]", "", $panel_id); // CLEAN THE PANEL ID JUST IN CASE
	$panel_enable = 1; // ENABLE PANEL
	$panel_version = 1; // THIS SHOULD ALWAYS BE 1 UNLESS OTHERWISE NOTED BY KTOOLS
	$panel_filename = basename(dirname(__FILE__));
	$panel_template = 1; // USE THE PANEL TEMPLATE - CURRENTLY NOT USED BUT MAY BE IN THE FUTURE
	
	switch($panel_mode){
		case "preload";
			# PRELOAD SOME JAVASCRIPT BELOW
			# THIS IS THE OLD WAY OF DOING IT. PREFERABLY THE JAVASCRIPT SHOULD BE IN THE LOAD CASE TO INCREASE INITIAL LOAD TIMES ON THE WELCOME PAGE	
		?>
        	<script language="javascript">
            	function wp_stats_loadchartwin()
				{
					show_loader('wp_stats_chartwin');
					var loadpage = "widgets/sitestats/panel.php?panel_mode=charts";
					var updatecontent = 'wp_stats_chartwin';
					var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
				}
				function wp_stats_reload()
				{
					var statmode = $('statmode').options[$('statmode').selectedIndex].value;
					var statlength = $('statlength').options[$('statlength').selectedIndex].value;
					show_loader('wp_stats_chartwin');
					var loadpage = "widgets/sitestats/panel.php?panel_mode=charts&statmode="+statmode+"&statlength="+statlength;
					var updatecontent = 'wp_stats_chartwin';
					var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
				}
            </script>
            <style>
				.wp_stats_rstat{
					float: left;
					width: 35%;
					background-color: #75b1db;
					text-align: center;
					color: #FFF;
					font-weight: bold;
					height: 25px;
					/*-moz-border-radius-bottomright: 8px;
					border-bottom-right-radius: 8px;
					-moz-border-radius-topright: 8px;
					border-top-right-radius: 8px;*/
				}
				.wp_stats_rstat p{
					padding-top: 5px;
					vertical-align: middle;
					font-size: 12px;
				}
				.wp_stats_lstat{
					float: left;
					width: 65%;
					background-color: #FFF;
					height: 25px;
				}
				.wp_stats_lstat p{
					padding: 5px 0 0 6px;
					vertical-align: middle;
					text-align: left;
				}
				.wp_stats_lstat p span{
					padding: 10px 6px 6px 6px;
					font-size: 12px;
					color: #666;
				}
				.wp_stats_charttop{
					margin: 0 0 0 0;
					padding: 0;
					list-style: none;
					clear: both;
					border-left: 1px solid #b3d0e5;
					border-bottom: 1px solid #b3d0e5;
					overflow: visible;
					height: 121px;
					background-color: #e5f2fb
				}
				.wp_stats_charttop li{
					float: left;
					height: 120px;
					text-align: center;
					position: relative;
				}
				.wp_stats_chartbottom{
					margin: 0 0 0 0;
					padding: 4px 0 0 0;
					list-style: none;
					clear: both;
				}
				.wp_stats_chartbottom li{
					float: left;
					text-align: center;
					line-height: 1;
					color: #898989;
					font-weight: bold;
					/*
					-webkit-transform: rotate(-90deg); 
					-moz-transform: rotate(-90deg);	
					padding-top: 6px;
					*/
				}
				.wp_stats_bar_div{
					position: absolute;
					width: 16px;
					height: 100%;
					left: 50%;
					text-align: center;
				}
				.wp_stats_bar{
					background-color: #629dc6;
					position: absolute;
					width: 16px;
					bottom: -1px;
					background-image: url(images/mgr.char.barshade.png);
					background-repeat: repeat-y;
					-moz-border-radius-topleft: 4px;
					border-top-left-radius: 4px;
					-moz-border-radius-topright: 4px;
					border-top-right-radius: 4px;
				}
				.wp_stats_bar_tag{
					position: absolute;
					text-align: center;
					color: #666;
					z-index: 999;
					background-color: #FFF;
					border: 1px solid #b3d0e5;
					font-size: 11px;
					padding: 1px 3px 1px 3px;
					-moz-border-radius: 4px;
					border-radius: 4px;
					left: -50%;
					margin-left: -4px;
				}
				.wp_stats_bar_div2{
					position: absolute;
					width: 16px;
					height: 100%;
					left: 50%;
					text-align: center;
				}
				.wp_stats_bar2{
					background-color: #10a876;
					position: absolute;
					width: 16px;
					bottom: -1px;
					background-image: url(images/mgr.char.barshade.png);
					background-repeat: repeat-y;
					-moz-border-radius-topleft: 4px;
					border-top-left-radius: 4px;
					-moz-border-radius-topright: 4px;
					border-top-right-radius: 4px;
				}
				.wp_stats_bar_tag2{
					position: absolute;
					text-align: center;
					color: #666;
					z-index: 999;
					background-color: #FFF;
					border: 1px solid #b3d0e5;
					font-size: 11px;
					padding: 1px;
					-moz-border-radius: 4px;
					border-radius: 4px;
					width: 30px;
					left: -50%;
				}
				
				
			</style>
        <?php			
		break;
		case "install":
			# INSTALL THE ADD-ON IF NEEDED
		break;
		case "charts":
				
				# INCLUDE DATABASE CONFIG FILE
				require_once('../../../assets/includes/db.config.php');
				# INCLUDE DATABASE CONNECTION FILE
				require_once('../../../assets/includes/db.conn.php');
				
				# INCLUDE SHARED FUNCTIONS FILE
				require_once('../../../assets/includes/shared.functions.php');
				
				# INCLUDE TWEAKS FILE
				require_once('../../../assets/includes/tweak.php');
				
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('../../mgr.defaultcur.php');
				
				# SEE IF BILL ME LATERS SHOULD BE INCLUDED
				if($config['BillMeLaterStats'])
				{
					$addsql = "OR {$dbinfo[pre]}invoices.payment_status = '3'";
				}
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
				
				$stat_result = mysqli_query($db,"SELECT statmode,statlength FROM {$dbinfo[pre]}wp_stats WHERE stat_id = '1'");
				$stat = mysqli_fetch_object($stat_result);
				
				//echo $stat->statmode; exit;
				
				if($_GET['statmode'])
				{
					$statmode = $_GET['statmode'];
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}wp_stats SET statmode='$statmode' WHERE stat_id  = '1'";
					$result = mysqli_query($db,$sql);
				}
				else
				{
					$statmode = $stat->statmode;
				}
				
				if($_GET['statlength'])
				{
					$statlength = $_GET['statlength'];
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}wp_stats SET statlength='$statlength' WHERE stat_id  = '1'";
					$result = mysqli_query($db,$sql);
				}
				else
				{
					$statlength = $stat->statlength;
				}
				
				# SET THE MODE
				switch($statmode)
				{
					default:
					case "sales":
						$sales_sel = "selected='selected'";
					break;
					case "orders":
						$orders_sel = "selected='selected'";
					break;
					case "members":
						$members_sel = "selected='selected'";
					break;
				}
				
				# SET THE LENGTH
				switch($statlength)
				{
					default:
					case "days":
						$days_sel = "selected='selected'";
					break;
					case "months":
						$months_sel = "selected='selected'";
					break;
					case "years":
						$years_sel = "selected='selected'";
					break;
				}
				
				if($statmode == 'sales')
				{
					echo "<p style='float: left; padding: 10px 0 10px 20px; color: #666;'>";
						echo "<span style='background-color: #5a90b6; font-size: 9px;'>&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$wplang[stats_op1]}<br /><span style='background-color: #00895c; font-size: 9px;'>&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;{$wplang[stats_orders]}";
					echo "</p>";
				}
				echo "<p style='float: right; padding: 10px 18px 10px 10px'>";
					echo "<select id='statmode' onchange='wp_stats_reload();'>";
							echo "<option value='sales' $sales_sel>".$wplang['stats_op1']."</option>";
							echo "<option value='members' $members_sel>".$wplang['stats_op2']."</option>";
						echo "</select>";
						echo "<select id='statlength' onchange='wp_stats_reload();'>";
							echo "<option value='days' $days_sel>".$wplang['stats_op_7days']."</option>";
							echo "<option value='months' $months_sel>".$wplang['stats_op_6mon']."</option>";
							echo "<option value='years' $years_sel>".$wplang['stats_op_5year']."</option>";
						echo "</select>";
				echo "</p>";
				
				switch($statmode)
				{
					default:
					case "sales":
						switch($statlength)
						{
							default:
							case "days":
								# [todo] ADJUST FOR TIMEZONE
								$today = date("Y-m-d H:i:s");	
								for($i=0; $i<7; $i++){
									$stat_day[] = date("Y-m-d",strtotime("$today -$i days"));
								}
								$stat_day = array_reverse($stat_day);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										//$salesmax_result = mysqli_query($db,"SELECT MAX(total) AS max_total FROM {$dbinfo[pre]}orders");
										//$salesmax = mysqli_fetch_object($salesmax_result);
										
										$max_total = 0;
										$max_order = 0;
										foreach($stat_day as $value)
										{
											//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$value%' AND (order_status = '1' OR order_status = '0' OR order_status = '4')");
											$sales_result = mysqli_query($db,
											"
												SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
												FROM {$dbinfo[pre]}orders 
												LEFT JOIN {$dbinfo[pre]}invoices 
												ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
												WHERE {$dbinfo[pre]}orders.order_date LIKE '$value%' 
												AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
												AND {$dbinfo[pre]}orders.deleted = 0
											");
											//$sales_rows = mysqli_num_rows($sales_result);
											$sales = mysqli_fetch_object($sales_result);
											if($sales->sales_total > $max_total)
											{
												$max_total = $sales->sales_total;
											}
											if($sales->order_count > $max_order)
											{
												$max_order = $sales->order_count;
											}
											$day_totals[] = $sales->sales_total;
											$day_orders[] = $sales->order_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										$sizecalc = ($max_total) ? round(100/$max_total,2) : 0;
										$size2calc = ($max_order) ? round(75/$max_order,2) : 0;
										
										//echo $sizecalc; exit;
										
										foreach($day_totals as $key => $value)
										{
											//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$value%'");
											//$sales_rows = mysqli_num_rows($sales_result);
											//$sales = mysqli_fetch_object($sales_result);
											
											if($value <= 0)
											{
												$bar_height = 0;
												$bar2_height = 0;
												$sales_total = '';
												$order_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$bar2_height = round(round($day_orders[$key])*$size2calc);
												$sales_total = $cleanvalues->currency_display($value,1);
												$order_total = $day_orders[$key];
											}
									?>
                                        <li style="width: 14%">
                                            <div class="wp_stats_bar_div" style="margin-left: 0;">
                                            	<span class="wp_stats_bar_tag" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $sales_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                            <div class="wp_stats_bar_div2" style="margin-left: -10px;">
                                            	<span class="wp_stats_bar_tag2" id="barB_<?php echo $key; ?>" style="bottom: <?php echo $bar2_height; ?>px; display: none;"><?php echo $order_total; ?></span>
                                           		<p style="height: <?php echo $bar2_height; ?>px;" class="wp_stats_bar2" onmouseover="show_div('barB_<?php echo $key; ?>')" onmouseout="hide_div('barB_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_day as $value)
										{
											$date = explode("-",$value);
											$lang_month = $calendar['short_month'][round($date[1])];
											echo "<li style='width: 14%'>$lang_month<br />".round($date[2])."</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
							case "months":
								# [todo] ADJUST FOR TIMEZONE
								$today = date("Y-m-d H:i:s");	
								
								for($i=0; $i<6; $i++){
									$stat_month[] = date('Y-m', mktime(0,0,0,date('n')-$i,1,date('Y')));
									//$stat_month[] = date("Y-m",strtotime("first day of this month -{$i} months")); //$today
								}
								
								//echo date("Y-m",strtotime("first day of this month"));
								
								$stat_month = array_reverse($stat_month);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										$max_total = 0;
										$max_order = 0;
										foreach($stat_month as $value)
										{
											//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$value%' AND (order_status = '1' OR order_status = '0' OR order_status = '4')");
											$sales_result = mysqli_query($db,
											"
												SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
												FROM {$dbinfo[pre]}orders 
												LEFT JOIN {$dbinfo[pre]}invoices 
												ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
												WHERE {$dbinfo[pre]}orders.order_date LIKE '$value%' 
												AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
												AND {$dbinfo[pre]}orders.deleted = 0
											");
											$sales_rows = mysqli_num_rows($sales_result);
											$sales = mysqli_fetch_object($sales_result);
											if($sales->sales_total > $max_total)
											{
												$max_total = $sales->sales_total;
											}
											if($sales->order_count > $max_order)
											{
												$max_order = $sales->order_count;
											}
											$month_totals[] = $sales->sales_total; 
											$month_orders[] = $sales->order_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										@$sizecalc = round(100/$max_total,2);
										@$size2calc = round(75/$max_order,2);
										
										foreach($month_totals as $key => $value)
										{											
											if($value <= 0)
											{
												$bar_height = 0;
												$bar2_height = 0;
												$sales_total = '';
												$order_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$bar2_height = round(round($month_orders[$key])*$size2calc);
												$sales_total = $cleanvalues->currency_display($value,1);
												$order_total = $month_orders[$key];
											}
									?>
                                        <li style="width: 16%">
                                            <div class="wp_stats_bar_div" style="margin-left: -2px;">
                                            	<span class="wp_stats_bar_tag" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $sales_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                            <div class="wp_stats_bar_div2" style="margin-left: -12px;">
                                            	<span class="wp_stats_bar_tag2" id="barB_<?php echo $key; ?>" style="bottom: <?php echo $bar2_height; ?>px; display: none;"><?php echo $order_total; ?></span>
                                           		<p style="height: <?php echo $bar2_height; ?>px;" class="wp_stats_bar2" onmouseover="show_div('barB_<?php echo $key; ?>')" onmouseout="hide_div('barB_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_month as $value)
										{
											$date = explode("-",$value);
											$lang_month = $calendar['short_month'][round($date[1])];
											echo "<li style='width: 16%'>$lang_month</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
							case "years":
								# [todo] ADJUST FOR TIMEZONE
								for($i=0; $i<5; $i++){
									$stat_year[] = date("Y")-$i;
								}
								$stat_year = array_reverse($stat_year);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										
										$max_total = 0;
										$max_order = 0;
										foreach($stat_year as $value)
										{
											//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$value%' AND (order_status = '1' OR order_status = '0' OR order_status = '4')");
											$sales_result = mysqli_query($db,
											"
												SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
												FROM {$dbinfo[pre]}orders 
												LEFT JOIN {$dbinfo[pre]}invoices 
												ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
												WHERE {$dbinfo[pre]}orders.order_date LIKE '$value%' 
												AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
												AND {$dbinfo[pre]}orders.deleted = 0
											");
											$sales_rows = mysqli_num_rows($sales_result);
											$sales = mysqli_fetch_object($sales_result);
											if($sales->sales_total > $max_total)
											{
												$max_total = $sales->sales_total;
											}
											if($sales->order_count > $max_order)
											{
												$max_order = $sales->order_count;
											}
											$year_totals[] = $sales->sales_total;
											$year_orders[] = $sales->order_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										@$sizecalc = round(100/$max_total,2);
										@$size2calc = round(75/$max_order,2);
										
										foreach($year_totals as $key => $value)
										{											
											if($value <= 0)
											{
												$bar_height = 0;
												$bar2_height = 0;
												$sales_total = '';
												$order_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$bar2_height = round(round($year_orders[$key])*$size2calc);
												$sales_total = $cleanvalues->currency_display($value,1);
												$order_total = $year_orders[$key];
											}
									?>
                                        <li style="width: 20%">
                                            <div class="wp_stats_bar_div" style="margin-left: -2px;">
                                            	<span class="wp_stats_bar_tag" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $sales_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                            <div class="wp_stats_bar_div2" style="margin-left: -12px;">
                                            	<span class="wp_stats_bar_tag2" id="barB_<?php echo $key; ?>" style="bottom: <?php echo $bar2_height; ?>px; display: none;"><?php echo $order_total; ?></span>
                                           		<p style="height: <?php echo $bar2_height; ?>px;" class="wp_stats_bar2" onmouseover="show_div('barB_<?php echo $key; ?>')" onmouseout="hide_div('barB_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_year as $value)
										{
											echo "<li style='width: 20%'>$value</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
						}
					break;
					case "members":
						switch($statlength)
						{
							default:
							case "days":
								# [todo] ADJUST FOR TIMEZONE
								$today = date("Y-m-d H:i:s");	
								for($i=0; $i<7; $i++){
									$stat_day[] = date("Y-m-d",strtotime("$today -$i days"));
								}
								$stat_day = array_reverse($stat_day);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										//$salesmax_result = mysqli_query($db,"SELECT MAX(total) AS max_total FROM {$dbinfo[pre]}orders");
										//$salesmax = mysqli_fetch_object($salesmax_result);
										
										$max_total = 0;
										foreach($stat_day as $value)
										{
											$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$value%'");
											//$sales_rows = mysqli_num_rows($sales_result);
											$members = mysqli_fetch_object($members_result);
											if($members->member_count > $max_total)
											{
												$max_total = $members->member_count;
											}
											$day_totals[] = $members->member_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										if($max_total)
											$sizecalc = round(100/$max_total,2);
										else
											$sizecalc = 0;
										
										
										//echo $sizecalc; exit;
										
										foreach($day_totals as $key => $value)
										{
											//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$value%'");
											//$sales_rows = mysqli_num_rows($sales_result);
											//$sales = mysqli_fetch_object($sales_result);
											
											if($value <= 0)
											{
												$bar_height = 0;
												$members_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$members_total = $day_totals[$key];
											}
									?>
                                        <li style="width: 14%">
                                            <div class="wp_stats_bar_div" style="margin-left: -8px;">
                                            	<span class="wp_stats_bar_tag2" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $members_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_day as $value)
										{
											$date = explode("-",$value);
											$lang_month = $calendar['short_month'][round($date[1])];
											echo "<li style='width: 14%'>$lang_month<br />".round($date[2])."</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
							case "months":
								# [todo] ADJUST FOR TIMEZONE
								/*
								$today = date("Y-m-d H:i:s");	
								for($i=0; $i<6; $i++){
									$stat_month[] = date("Y-m",strtotime("first day of this month -{$i} months"));
								}
								$stat_month = array_reverse($stat_month);
								*/
								
								$today = date("Y-m-d H:i:s");	
								
								for($i=0; $i<6; $i++){
									$stat_month[] = date('Y-m', mktime(0,0,0,date('n')-$i,1,date('Y')));
									//$stat_month[] = date("Y-m",strtotime("first day of this month -{$i} months")); //$today
								}
								
								$stat_month = array_reverse($stat_month);
								
								//print_r($stat_month);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										$max_total = 0;
										foreach($stat_month as $value)
										{
											$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count  FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$value%'");
											$members_rows = mysqli_num_rows($members_result);
											$members = mysqli_fetch_object($members_result);
											if($members->member_count > $max_total)
											{
												$max_total = $members->member_count;
											}
											$month_totals[] = $members->member_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										@$sizecalc = round(100/$max_total,2);
										@$size2calc = round(75/$max_order,2);
										
										foreach($month_totals as $key => $value)
										{											
											if($value <= 0)
											{
												$bar_height = 0;
												$member_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$member_total = $month_totals[$key];
											}
									?>
                                        <li style="width: 16%">
                                            <div class="wp_stats_bar_div" style="margin-left: -9px;">
                                            	<span class="wp_stats_bar_tag2" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $member_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_month as $value)
										{
											$date = explode("-",$value);
											$lang_month = $calendar['short_month'][round($date[1])];
											echo "<li style='width: 16%'>$lang_month</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
							case "years":
								# [todo] ADJUST FOR TIMEZONE
								for($i=0; $i<5; $i++){
									$stat_year[] = date("Y")-$i;
								}
								$stat_year = array_reverse($stat_year);
						?>
                            <div style="padding: 0 20px 0 20px;">
                                <ul class="wp_stats_charttop">
                                    <?php
										
										$max_total = 0;
										foreach($stat_year as $value)
										{
											$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$value%'");
											$members = mysqli_fetch_object($members_result);
											if($members->member_count > $max_total)
											{
												$max_total = $members->member_count;
											}
											$year_totals[] = $members->member_count;
										}
										# FIND THE CALCULATION TO DETERMINE BAR HEIGHT
										@$sizecalc = round(100/$max_total,2);
										
										foreach($year_totals as $key => $value)
										{											
											if($value <= 0)
											{
												$bar_height = 0;
												$member_total = '';
											}
											else
											{
												$bar_height = round(round($value)*$sizecalc);
												$member_total = $year_totals[$key];
											}
									?>
                                        <li style="width: 20%">
                                            <div class="wp_stats_bar_div" style="margin-left: -9px;">
                                            	<span class="wp_stats_bar_tag2" id="barA_<?php echo $key; ?>" style="bottom: <?php echo $bar_height; ?>px; display: none;"><?php echo $member_total; ?></span>
                                            	<p style="height: <?php echo $bar_height; ?>px;" class="wp_stats_bar" onmouseover="show_div('barA_<?php echo $key; ?>')" onmouseout="hide_div('barA_<?php echo $key; ?>')"></p>
                                            </div>
                                        </li>
                                    <?php
										}
									?>
                                </ul>
                                <ul class="wp_stats_chartbottom">
                                    <?php
										foreach($stat_year as $value)
										{
											echo "<li style='width: 20%'>$value</li>";
										}
									?>
                                </ul>
                            </div>
						<?php
							break;
						}
					break;
				}
		break;
		case "load":		
			# KEEPS THE PAGE FROM CACHING
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			# INCLUDE DATABASE CONFIG FILE
			require_once('../../../assets/includes/db.config.php');
			# INCLUDE DATABASE CONNECTION FILE
			require_once('../../../assets/includes/db.conn.php');
			# INCLUDE SHARED FUNCTIONS FILE
			require_once('../../../assets/includes/shared.functions.php');
			# SELECT THE SETTINGS DATABASE
			require_once('../../mgr.select.settings.php');
			# INCLUDE TWEAKS FILE
			require_once('../../../assets/includes/tweak.php');
			
			# INCLUDE DEFAULT CURRENCY SETTINGS
			require_once('../../mgr.defaultcur.php');
			
			$cleanvalues = new number_formatting;
			$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
			$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>$wplang[load_failed]</div>"; exit;
			}
			
			# HERE YOU CAN ALSO CHECK TO SEE IF THE ADD-ON IS INSTALLED			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $wplang['stats_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
				wp_stats_loadchartwin();
			</script>            
            <div style="margin: 0; overflow: auto; height: 210px;">
                <div style="width: 45%; float: left; overflow: auto; height: 210px">
                    <?php
						# SEE IF BILL ME LATERS SHOULD BE INCLUDED
						if($config['BillMeLaterStats'])
						{
							$addsql = "OR {$dbinfo[pre]}invoices.payment_status = '3'";
						}
					
						$date = date("Y-m-d");
						//echo $date;
						//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$date%' AND (order_status = '1' OR order_status = '0' $addsql)");
						$sales_result = mysqli_query($db,
						"
							SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
							FROM {$dbinfo[pre]}orders 
							LEFT JOIN {$dbinfo[pre]}invoices 
							ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
							WHERE {$dbinfo[pre]}orders.order_date LIKE '$date%' 
							AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
							AND {$dbinfo[pre]}orders.deleted = 0
						");
						$sales = mysqli_fetch_object($sales_result);
					?>                    
                    <div class='wp_stats_lstat'><p><span><?php echo $wplang['stats_tdsales']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php if($sales->sales_total > 0){ echo $cleanvalues->currency_display($sales->sales_total,1); } else { echo "&mdash;"; } ?></p></div>
                    
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $wplang['stats_tdorders']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $sales->order_count; ?></p></div>
					
                    
                    <?php
						$date = date("Y-m");
						//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_date LIKE '$date%' AND (order_status = '1' OR order_status = '0' OR order_status = '4')");
						$sales_result = mysqli_query($db,
						"
							SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
							FROM {$dbinfo[pre]}orders 
							LEFT JOIN {$dbinfo[pre]}invoices 
							ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
							WHERE {$dbinfo[pre]}orders.order_date LIKE '$date%' 
							AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
							AND {$dbinfo[pre]}orders.deleted = 0
						");
						$sales = mysqli_fetch_object($sales_result);
					?>    
                    <div class='wp_stats_lstat'><p><span><?php echo $calendar['long_month'][round(date("m"))]; ?> <?php echo $wplang['stats_sales']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php if($sales->sales_total > 0){ echo $cleanvalues->currency_display($sales->sales_total,1); } else { echo "&mdash;"; } ?></p></div>
                    
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $calendar['long_month'][round(date("m"))]; ?> <?php echo $wplang['stats_orders']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $sales->order_count; ?></p></div>
                    
                    <?php
						$date = date("Y");
						$sales_result = mysqli_query($db,
						"
							SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
							FROM {$dbinfo[pre]}orders 
							LEFT JOIN {$dbinfo[pre]}invoices 
							ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
							WHERE {$dbinfo[pre]}orders.order_date LIKE '$date%' 
							AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
							AND {$dbinfo[pre]}orders.deleted = 0
						");
						$sales = mysqli_fetch_object($sales_result);
					?>
                    <div class='wp_stats_lstat'><p><span><?php echo date("Y"); ?> <?php echo $wplang['stats_sales']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php if($sales->sales_total > 0){ echo $cleanvalues->currency_display($sales->sales_total,1); } else { echo "&mdash;"; } ?></p></div>
                    
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo date("Y"); ?> <?php echo $wplang['stats_orders']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $sales->order_count; ?></p></div>
                    
                    <?php
						//$sales_result = mysqli_query($db,"SELECT SUM(total) AS sales_total,COUNT(order_id) AS order_count FROM {$dbinfo[pre]}orders WHERE order_status = '1' OR order_status = '0' OR order_status = '4'");
						$sales_result = mysqli_query($db,
						"
							SELECT SUM({$dbinfo[pre]}invoices.total) AS sales_total,COUNT({$dbinfo[pre]}orders.order_id) AS order_count 
							FROM {$dbinfo[pre]}orders 
							LEFT JOIN {$dbinfo[pre]}invoices 
							ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id 
							WHERE {$dbinfo[pre]}orders.deleted = 0 
							AND ({$dbinfo[pre]}orders.order_status = '1' AND ({$dbinfo[pre]}invoices.payment_status = '1' $addsql)) 
							AND {$dbinfo[pre]}orders.deleted = 0
						");
						$sales = mysqli_fetch_object($sales_result);
					?>
                    <div class='wp_stats_lstat'><p><span><?php echo $wplang['stats_atsales']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php if($sales->sales_total > 0){ echo $cleanvalues->currency_display($sales->sales_total,1); } else { echo "&mdash;"; } ?></p></div>
                    
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $wplang['stats_atorders']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $sales->order_count; ?></p></div>
                    
                    <?php
						$date = date("Y-m-d");
						$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$date%'");
						$members = mysqli_fetch_object($members_result);
					?>
                    <div class='wp_stats_lstat'><p><span><?php echo $wplang['stats_tdmems']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php echo $members->member_count; ?></p></div>
                    
                    <?php
						$date = date("Y-m");
						$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$date%'");
						$members = mysqli_fetch_object($members_result);
					?>
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $calendar['long_month'][round(date("m"))]; ?> <?php echo $wplang['stats_mems']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $members->member_count; ?></p></div>
                    
                    <?php
						$date = date("Y");
						$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE signup_date LIKE '$date%'");
						$members = mysqli_fetch_object($members_result);
					?>
                    <div class='wp_stats_lstat'><p><span><?php echo date("Y"); ?> <?php echo $wplang['stats_mems']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php echo $members->member_count; ?></p></div>
                    
                    <?php						
						$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE status = '1'");
						$members = mysqli_fetch_object($members_result);
					?>
                    <div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $wplang['stats_tamems']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $members->member_count; ?></p></div>
                    
                    <?php						
						$members_result = mysqli_query($db,"SELECT COUNT(mem_id) AS member_count FROM {$dbinfo[pre]}members WHERE status = '0' OR status = '2' ");
						$members = mysqli_fetch_object($members_result);
					?>
                    <div class='wp_stats_lstat'><p><span><?php echo $wplang['stats_timems']; ?></span></p></div>
                    <div class='wp_stats_rstat'><p><?php echo $members->member_count; ?></p></div>
					
					<div class='wp_stats_lstat' style='background-color: #EEE'><p><span><?php echo $wplang['stats_visitors']; ?></span></p></div>
                    <div class='wp_stats_rstat' style='background-color: #629dc6'><p><?php echo $config['settings']['site_visits']; ?></p></div>
                </div>
                <div style="float: left; width: 55%;" id="wp_stats_chartwin">
                    
                </div>                
            </div>
        <?php
		break;
		default:
			# DO NOTHING - NOT LOADED
		break;
		case "test":
			echo "Works!!";
		break;
	}	
?>