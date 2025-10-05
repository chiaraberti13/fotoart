<?php
	###################################################################
	####	MANAGER ADMIN CTIVITY LOG                              ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 10-15-2007                                    ####
	####	Modified: 9-30-2008                                    #### 
	###################################################################
		//sleep(3);
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "administrators";
		
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
		
		# INCLUDE THE LANGUAGE FILE	
		include_lang();
		
		$manager_sql = ($_GET['manager']) ? '1' : '0';
		
		# PURGE ACTIVITY LOGS
		if($_GET['purge'] == "1"){
			$mid=trim($_GET['mid']);			
			# GET THE CORRECT PURGE DATE
			$purge_date = convert_date_to_local($_GET['pyear'] . "-" . $_GET['pmonth'] . "-" . $_GET['pday'] . " 23:59:59");
			//echo $purge_date; exit;
			@mysqli_query($db,"DELETE FROM {$dbinfo[pre]}activity_log WHERE log_time < '$purge_date' AND member_id = '$mid' AND manager = '$manager_sql'");
			$vmessage = $mgrlang['util_mes_01'];
			
			# FIND USERNAME OF ADMIN GETTING PURGED
			$prg_result = mysqli_query($db,"SELECT username FROM {$dbinfo[pre]}admins WHERE admin_id = '$mid'");
			$prg = mysqli_fetch_object($prg_result);
			
			$acdate = new kdate;
            $acdate->distime = 0;
			$acdate->adjust_date = 0;
						
			# UPDATE ACTIVITY LOG
			save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['admin_tab3'],1,$mgrlang['setup_f_purge_ac'] . " ($prg->username) > <strong>$mgrlang[gen_b_purge_al] ".$acdate->showdate($_GET['pyear'] . "-" . convert_to_2digit($_GET['pmonth']) . "-" . convert_to_2digit($_GET['pday']))."</strong>");
		}
		
		$records = ($_GET['records']) ? $_GET['records'] : 100;
		$start = ($_GET['start']) ? $_GET['start'] : 0;
		
		/*old
		if(!$_GET['start']){
			$start=0;
		} else {
			$start=$_GET['start'];
		}
		*/
		$mid=trim($_GET['mid']);
		
		
		if($_GET['print_all'] == 1){			
			$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(log_id) FROM {$dbinfo[pre]}activity_log WHERE manager = '$manager_sql'"));
		} else {
			$r_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(log_id) FROM {$dbinfo[pre]}activity_log WHERE manager = '$manager_sql' AND member_id = '$mid'"));
		}
		
		# FIGURE TO DATE
		$to_date = convert_date_to_local($_GET['tyear'] . "-" . $_GET['tmonth'] . "-" . $_GET['tday'] . " 23:59:59");
		# FIGURE FROM DATE
		$from_date = convert_date_to_local($_GET['fyear'] . "-" . $_GET['fmonth'] . "-" . $_GET['fday'] . " 23:59:59");
		
		# FIND THE ADMIN NAME IF IT IS A PRINT OR DOWNLOAD
		if(($_GET['displaymode'] == "print" or $_GET['displaymode'] == "download") and $manager_sql == '1'){
			# SELECT ADMIN USERNAMES
			$adminarray[0] = $mgrlang['gen_sys_name'];
			$admin_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins ORDER BY 'admin_id'");
			$admin_rows = mysqli_num_rows($admin_result);
			while($admin = mysqli_fetch_object($admin_result)){
				if($admin->admin_id == '0'){
					$adminarray[$admin->admin_id] = 'blahblah';
				} else {
					$adminarray[$admin->admin_id] = $admin->username;
				}
			}
			$records = '100000000';
			$start = '0';
		}
		
		if($_GET['print_all'] != 1){	
			$show_members = " AND member_id = '$mid'";
		}
		
		# QUERY ACTIVITY LOG
		$alog_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}activity_log WHERE manager = '$manager_sql' $show_members AND log_time >= '$from_date' AND log_time <= '$to_date' ORDER BY log_time DESC LIMIT $start,$records");
       	$alog_rows = mysqli_num_rows($alog_result);	
		
		//echo $from_date."<br>".$to_date;
		
		# UTILITIES - CHECK FOR RECORDS
		if($_GET['displaymode'] == "checkrecords"){
			if($alog_rows){
				echo "yes";
			} else {
				echo "no";
			}
			exit;
		}
		# PRINTING
		if($_GET['displaymode'] == "print"){
			$row_color = 0;
			$ndate = new kdate;
			$ndate->distime = 0;
			$ndate->diswords = 1;
			$ndate->adjust_date = 0;
			echo "<span style='font-size: 16px; font-weight: bold;'>$mgrlang[admin_tab3] (" . $ndate->showdate($_GET['fyear'] . "-" . convert_to_2digit($_GET['fmonth']) . "-" . convert_to_2digit($_GET['fday'])) . " - " . $ndate->showdate($_GET['tyear'] . "-" . convert_to_2digit($_GET['tmonth']) . "-" . convert_to_2digit($_GET['tday'])) . ")</span>";
			echo "<table border='0' style='border: 0px; margin-top: 4px;' cellspacing='0' cellpadding='4' width='100%'>";			
			$ndate->distime = 1;
			$ndate->adjust_date = 1;
			while($alog = mysqli_fetch_object($alog_result)){
				@$row_color++;
				if ($row_color%2 == 0) {
					$color = "EEEEEE";
				} else {
					$color = "FFFFFF";
				}
				echo "<tr style='background-color: #$color;'>";					
					echo "<td style='font-weight: bold; color: #6e6e6e;'>".$adminarray[$alog->member_id]."</td><td style='width: 10px' align='center'> : </td>";
				echo "<td style='font-weight: bold; color: #6e6e6e;' nowrap>".$ndate->showdate($alog->log_time)."</td> <td style='width: 10px' align='center'> : </td><td width='100%'>" . $alog->area . " > " . $alog->details . "</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<script language='javascript'>window.print();</script>";
			exit;
		}
		# DOWNLOADING
		if($_GET['displaymode'] == "download"){
			$ctype="application/txt";
			$filename = "activity_log.csv";
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: $ctype, charset=UTF-8; encoding=UTF-8");
			header("Content-Disposition: attachment; filename=\"".$filename."\";");
			header("Content-Transfer-Encoding: binary");
			//header("Content-Length: ".@filesize($file));
			if(function_exists('set_time_limit')) set_time_limit(0);
			//@readfile("$file") or die("File not found.");
			echo '"USER ID","NAME","ADMINISTRATOR","DATE/TIME","AREA","ACTIVITY"' . PHP_EOL;
			# CHECK IF ADMIN OR NOT
			if($_GET['manager'] == 1){
				$admin = "yes";
			} else {
				$admin = "no";				
			}
			while($alog = mysqli_fetch_object($alog_result)){
				if($_GET['manager'] == 1){
					$name = $adminarray[$alog->member_id];
				} else {
					$member_result = mysqli_query($db,"SELECT f_name,l_name FROM {$dbinfo[pre]}members WHERE mem_id = '$alog->member_id'");
					$mgrMemberInfo = mysqli_fetch_object($member_result);
					$name = $mgrMemberInfo->f_name . " " . $mgrMemberInfo->l_name;
				}
				echo '"' . $alog->member_id . '","' . $name . '","' . $admin . '","' . $alog->log_time . '","' . $alog->area . '","' . strip_tags($alog->details) . '"' . PHP_EOL;
			}
			exit;
			
		}
		
		/*
		switch($_GET['displaymode']){
			case "print":
				//echo $_GET['tyear']; exit;
				echo "<table border='0' style='border: 0px;' cellspacing='0' cellpadding='3'>";
				# PRINT ALL ACTIVITY
				if($_GET['print_all']){
					# SELECT ADMIN USERNAMES
                    $admin_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins ORDER BY 'admin_id'");
                    $admin_rows = mysqli_num_rows($admin_result);
                    while($admin = mysqli_fetch_object($admin_result)){
						$adminarray[$admin->admin_id] = $admin->username;
					}
					$alog_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}activity_log WHERE manager = '1' ORDER BY log_time DESC");
				# PRINT JUST SELECTED MEMBER ACTIVITY
				} else {
					$alog_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}activity_log WHERE manager = '1' AND member_id = $mid AND log_time > '$from_date' AND log_time < '$to_date' ORDER BY log_time DESC LIMIT $start,$records");
				}
        		$alog_rows = mysqli_num_rows($alog_result);
				
				
			break;
			default:
				$alog_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}activity_log WHERE manager = '1' AND member_id = '$mid' AND log_time >= '$from_date' AND log_time <= '$to_date' ORDER BY log_time DESC LIMIT $start,$records");
        		$alog_rows = mysqli_num_rows($alog_result);
			break;
		}
		*/

?>
        <!--<div style="background-color: #efefef; padding: 10px; border-bottom: 1px solid #d6d3d6; border-top: 1px solid #ffffff;">
            <input type="text" name="ac_search" style="width: 100px; font-size: 11px;" /> <input type="submit" value="Search" />
        </div>-->
       	<?php /* <script language="javascript" type="text/javascript">startat=<?php echo $start; ?></script> */ ?>
        <?php
			if($r_rows){
		?>
        <div style="text-align: right; background-color: #fff; padding: 16px; border-bottom: 2px solid #d7d7d7; border-top: 1px solid #ffffff;">
            <p align='left' style='margin: 0; padding: 0; float: left; font-weight:bold; color: #999999'>
                &nbsp;From
                <select style="width: 60px;" name="from_month" id="from_month">
                    <?php
                        for($i=1; $i<13; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['fmonth']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 60px;" name="from_day" id="from_day">
                    <?php
                        for($i=1; $i<=31; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['fday']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 70px;" name="from_year" id="from_year">
                    <?php
                        for($i=2008; $i<=(date("Y")); $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['fyear']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select> To 
                <select style="width: 60px;" name="to_month" id="to_month">
                    <?php
                        for($i=1; $i<13; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['tmonth']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 60px;" name="to_day" id="to_day">
                    <?php
                        for($i=1; $i<=31; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['tday']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 70px;" name="to_year" id="to_year">
                    <?php
                        for($i=2008; $i<=(date("Y")); $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == $_GET['tyear']){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
            </p>
            <p align='left' style='margin: 2px 0 0 5px; padding: 0; float: left;'><input type='button' value='<?php echo $mgrlang['gen_b_display']; ?>' class='small_button' onclick="load_al(0);" /></p>
            <p align='left' style='margin: 2px 0 0 3px; padding: 0; float: left;'><input type='button' value='<?php echo $mgrlang['gen_b_print']; ?>' id='print_button' class='small_button' onclick="prep_printing();" <?php if(!$alog_rows){ echo "disabled"; } ?> /></p>
            <p align='left' style='margin: 2px 0 0 3px; padding: 0; float: left;'><input type='button' value='<?php echo $mgrlang['gen_b_download']; ?>' id='download_button' class='small_button' onclick="download_csv();" <?php if(!$alog_rows){ echo "disabled"; } ?> /></p>
            <img src="images/mgr.actions.bar.div.png" style='float: left; margin: 4px 10px 0 10px; width: 2px; height: 18px;' />
			<?php
           		if(!empty($_SESSION['admin_user']['superadmin']) or $_SESSION['admin_user']['admin_id'] == "DEMO"){
			?>
            <p align='left' style='margin: 0; padding: 0; float: left;'>
                <input type='button' value='<?php echo $mgrlang['gen_b_purge_al']; ?>...' class='small_button' style="margin-top: -5px;" onclick="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message();"; } else { echo "purge_activity_log();"; } ?>" />
                <select style="width: 60px;" name="purge_month" id="purge_month">
                    <?php
                        for($i=1; $i<13; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == date("m")){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 60px;" name="purge_day" id="purge_day">
                    <?php
                        for($i=1; $i<=31; $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == date("d")){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
                <select style="width: 70px;" name="purge_year" id="purge_year">
                    <?php
                        for($i=2008; $i<(date("Y")+6); $i++){
                            if(strlen($i) < 2){
                                $dis_i_as = "0$i";
                            } else {
                                $dis_i_as = $i;
                            }
                            echo "<option value='$i' ";
                            if($i == date("Y")){
                                echo "selected";
                            }
                            echo ">$dis_i_as</option>";
                        }
                    ?>
                </select>
            </p>
            <img src="images/mgr.actions.bar.div.png" style='float: left; margin: 4px 10px 0 10px; width: 2px; height: 18px;' />
            <?php
				}
			?>
            <input type="button" value="<?php echo $mgrlang['gen_b_previous']; ?>" <?php if($start > 0){ echo "onclick='load_al(" . ($start - $records) . ")'"; } else { echo "disabled"; } ?> /><input type="button" value="<?php echo $mgrlang['gen_b_next']; ?>" <?php if(($start+$records) < $r_rows){ echo "onclick='load_al(" . ($start + $records) . ")'"; } else { echo "disabled"; } ?> />
        </div>
        <?php
			# OUTPUT MESSAGE IF ONE EXISTS
			verify_message($vmessage);
			if($alog_rows){
		?>
        <div style="max-height: 400px; overflow: auto;" id="activity_window">
            <?php
                
				//echo "from: " . $from_date;
				//echo "to: " . $to_date;
				
				$row_color = 0;
                $ndate = new kdate;
                $ndate->distime = 1;
                $ndate->diswords = 1;
                while($alog = mysqli_fetch_object($alog_result)){
				//echo $end;
					@$row_color++;
					if ($row_color%2 == 0) {
						$color = "EEEEEE";
					} else {
						$color = "FFFFFF";
					}
            ?>
                <div style="padding: 6px 6px 6px 15px; background-color: #<?php echo $color; ?>; overflow: auto"><p style="font-weight: bold; color: #6e6e6e; width: 155px; float: left; margin: 0;  padding: 0;"><?php echo $ndate->showdate($alog->log_time); ?></p> <p style='float: left; padding: 0 10px 0 10px; margin: 0;'> : </p> <?php echo $alog->area . " > " . $alog->details; ?></div>
		<?php
            	}
        	} else {
				echo "<div style='padding: 15px; border-top: 1px solid #999999'><img src='images/mgr.notice.icon.white.gif' align='absmiddle' /><strong> &nbsp; $mgrlang[admin_al_none]</strong></div>";
			}
        ?>
        </div>
<?php
	} else {
		echo "<div style='padding: 15px;'><img src='images/mgr.notice.icon.white.gif' align='absmiddle' /><strong> &nbsp; $mgrlang[admin_al_none]</strong></div>";
	}
?>