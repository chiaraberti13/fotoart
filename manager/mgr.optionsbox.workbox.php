<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
	//sleep(1);
	
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	

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
	# FP
	require_once('../assets/includes/config.php');
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
	
	
	switch($_GET['box']){
		default:
		case "optionswb":
		
			$og_id = $_GET['og_id'];
		
			# SELECT ITEMS
			$options_result = mysqli_query($db,"SELECT op_id FROM {$dbinfo[pre]}options WHERE deleted = '0' AND parent_id = '$og_id' ORDER BY sortorder");
			$options_rows = mysqli_num_rows($options_result);
			//$options = mysqli_fetch_object($options_result);
			//var_dump($options->op_id);
			
			//$optiongrp_result = mysqli_query($db,"SELECT name,parent_type FROM {$dbinfo[pre]}option_grp WHERE og_id = '{$og_id}'");
			$optiongrp_result = mysqli_query($db,"SELECT og_id,name,parent_type FROM {$dbinfo[pre]}option_grp WHERE og_id = '{$og_id}'");
			$optiongrp = mysqli_fetch_object($optiongrp_result);
			
			# FP
			if($optiongrp->name == PUZZLES_BOX_OPTION_NAME) {
				
				//scatole disponibili
				$products_options_values = mysqli_query($db, "SELECT * FROM photo_puzzle_products_options_values where products_options_group_name = '".PUZZLES_BOX_OPTION_NAME."' AND deleted = 0");
				//scatole già assegnate
				$optioniAttribuite = mysqli_query($db, "SELECT a.id, a.op_id, a.products_options_values_id, b.deleted as opt_deleted, b.active as opt_active
														FROM photo_puzzle_ps4_options_to_products_options_values as a 
														LEFT JOIN ps4_options as b
														ON (a.op_id = b.op_id)
														WHERE b.parent_id = ".$optiongrp->og_id." 
														AND
														a.products_options_values_id 
														IN 
														(SELECT products_options_values_id FROM photo_puzzle_products_options_values where products_options_group_name = '".PUZZLES_BOX_OPTION_NAME."' AND deleted = 0)");				
				$aOpzioniAttribuite = array();
				$cnt = 0;
				while ($attribuzione = mysqli_fetch_array($optioniAttribuite, MYSQLI_ASSOC)) {
					foreach ($attribuzione as $key => $value) $aOpzioniAttribuite[$key][$cnt] = $value;
					$cnt++;
					//$aOpzioniAttribuite['products_options_values_id'][] = $attribuzione['products_options_values_id'];
					//$aOpzioniAttribuite['op_id'][] = $attribuzione['op_id'];
				}
				print "<PRE>";
				//print_r($aOpzioniAttribuite);
				print "</PRE>";
				
				if(mysqli_num_rows($products_options_values) >= 0) {
					
					//$options = mysqli_fetch_object($options_result); //OPZIONI PER QUESTO PRODOTTO
					echo "<form id='option_edit_form' name='option_edit_form' action='mgr.optionsbox.actions.php' method='post'>";
						echo "<input type='hidden' name='og_id' value='$og_id' />";
						echo "<div id='wbheader'><p>$optiongrp->name &nbsp;</p></div>";
							echo "<div id='wbbody'>";
								echo "<div style='overflow: auto; position: relative' id='options_button_row'>";
									echo "<div class='subsubon' id='option_list' onclick=\"show_options_list();load_options_list_win('$og_id');\" style='border-left: 1px solid #d8d7d7'>$mgrlang[gen_listoption]</div>";
								echo "</div>";
								echo "<div class='more_options' style='background-image:none; width: 735px; padding: 0' id='options_list_win'>";
					
								$cntRow = 0;
								$cntK = 0;
								while ($row = mysqli_fetch_array($products_options_values, MYSQLI_ASSOC)) {
									$aJson = json_decode($row['products_options_values_name'], TRUE); 
									$color = ($cntRow %2 == 0) ? "255, 255, 255" : "238, 238, 238";
									echo "<div options-values-id='".$row['products_options_values_id']."' option-group-id='".$optiongrp->og_id."' option-name='".$aJson['desc']."' class='prodoptionrow' style='overflow: auto; height: 44px; background-color: rgb(".$color."); position: relative;'>";
										echo "<p onclick='' style='float: left; margin-left: 48px; margin-top: 14px; cursor: pointer; width: 520px'>".$aJson['desc']."</p>";
										echo '<p align="right" nowrap="nowrap" style="float: right; margin-top: 14px; margin-right: 14px;"><span style="margin-right:15px;">abilita/disabilita</span>';
										/*
										if(in_array($row['products_options_values_id'], $aOpzioniAttribuite['products_options_values_id'])) {
											echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$aOpzioniAttribuite['op_id'][$cntK]."' checked>";
											$cntK++;
										 }
										else echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$row['products_options_values_id']."'>";
										*/
										//var_dump($row['products_options_values_id']. ' - ' .$aOpzioniAttribuite['products_options_values_id'][$cntK]);
										if(in_array($row['products_options_values_id'], $aOpzioniAttribuite['products_options_values_id'])) {
											
											if ($aOpzioniAttribuite['opt_active'][$cntK] == 1) echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$aOpzioniAttribuite['op_id'][$cntK]."' checked>";
											else echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$aOpzioniAttribuite['op_id'][$cntK]."'>";
											//else echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$row['products_options_values_id']."'>";
											$cntK++;
											
										} else { //aggiungo l'opzione
											
											//$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
											$mysqliLink = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
											$mysqliLink->query("INSERT INTO ps4_options (parent_id, uop_id, name, price, price_mod, credits, credits_mod, my_cost, add_weight, sortorder, active, deleted, name_english, name_russian, name_spanish, name_dutch, name_french, name_german, name_, name_polish, name_romanian, name_slovenian, name_italian) 
															VALUES (".$optiongrp->og_id.", '', '".$aJson['desc']."', 0.0000, 'add', 0, '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '')");
											$linkId = $mysqliLink->insert_id;
											$mysqliLink->query("INSERT INTO photo_puzzle_ps4_options_to_products_options_values (op_id, products_options_values_id) VALUES(".$linkId.",".$row['products_options_values_id'].")");
											echo "<input type='checkbox' onchange='gestisciOpzioniBox(this)' name='abilita-box' value='".$linkId."'>";
											$mysqliLink->close();
											
										}
										
										echo '</p>';
									echo "</div>";
									$cntRow++;
								}
								echo "</div>";
							echo "</div>";
							echo "<div id='wbfooter' style='padding: 0 13px 20px 20px; margin: 0;'>";
								echo "<p style='float: right;' id='options_list_win_buttons'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='close_workbox();' /></p>";
								echo "<p style='float: right; display: none;' id='options_edit_win_buttons'><input type='button' value='$mgrlang[gen_b_cancel]' onclick=\"show_options_list();load_options_list_win('$og_id');\" /><input type='button' value='$mgrlang[gen_b_save]' onclick=\"submit_option_form('$og_id');\" /></p>";
							echo "</div>";
					echo "</form>";
					
				} else echo "non ci sono scatole da assegnare";
				/*echo "<form id='option_edit_form' name='option_edit_form' action='mgr.optionsbox.actions.php' method='post'>";
					echo "<input type='hidden' name='og_id' value='$og_id' />";
					echo "<div id='wbheader'><p>$optiongrp->name &nbsp;</p></div>";
	   					echo "<div id='wbbody'>";
							echo "<div style='overflow: auto; position: relative' id='options_button_row'>";
								echo "<div class='subsubon' id='option_list' onclick=\"show_options_list();load_options_list_win('$og_id');\" style='border-left: 1px solid #d8d7d7'>$mgrlang[gen_listoption]</div>";
								//echo "<div class='subsuboff' id='option_edit' onclick=\"edit_option('new','{$optiongrp->parent_type}');\" style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_newoption]</div>";
							echo "</div>";
							echo "<div class='more_options' style='background-image:none; width: 735px; padding: 0' id='options_list_win'></div>";
							echo "<div class='more_options' style='background-position:top; width: 735px; padding: 0; display: none;' id='options_edit_win'></div>";
						echo "</div>";
					echo "</div>";
					echo "<div id='wbfooter' style='padding: 0 13px 20px 20px; margin: 0;'>";
						echo "<p style='float: right;' id='options_list_win_buttons'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='close_workbox();' /></p>";
						echo "<p style='float: right; display: none;' id='options_edit_win_buttons'><input type='button' value='$mgrlang[gen_b_cancel]' onclick=\"show_options_list();load_options_list_win('$og_id');\" /><input type='button' value='$mgrlang[gen_b_save]' onclick=\"submit_option_form('$og_id');\" /></p>";
					echo "</div>";
				echo "</form>";*/
				
				
			}
			else {
			
				echo "<form id='option_edit_form' name='option_edit_form' action='mgr.optionsbox.actions.php' method='post'>";
				echo "<input type='hidden' name='og_id' value='$og_id' />";
				echo "<div id='wbheader'><p>$optiongrp->name &nbsp;</p></div>";
	   				echo "<div id='wbbody'>";
						echo "<div style='overflow: auto; position: relative' id='options_button_row'>";
							echo "<div class='subsubon' id='option_list' onclick=\"show_options_list();load_options_list_win('$og_id');\" style='border-left: 1px solid #d8d7d7'>$mgrlang[gen_listoption]</div>";
							echo "<div class='subsuboff' id='option_edit' onclick=\"edit_option('new','{$optiongrp->parent_type}');\" style='border-right: 1px solid #d8d7d7;'>$mgrlang[gen_newoption]</div>";
						echo "</div>";
						echo "<div class='more_options' style='background-image:none; width: 735px; padding: 0' id='options_list_win'></div>";
						echo "<div class='more_options' style='background-position:top; width: 735px; padding: 0; display: none;' id='options_edit_win'></div>";
					echo "</div>";
				echo "</div>";
				echo "<div id='wbfooter' style='padding: 0 13px 20px 20px; margin: 0;'>";
					echo "<p style='float: right;' id='options_list_win_buttons'><input type='button' value='{$mgrlang[gen_b_close]}' class='small_button' onclick='close_workbox();' /></p>";
					echo "<p style='float: right; display: none;' id='options_edit_win_buttons'><input type='button' value='$mgrlang[gen_b_cancel]' onclick=\"show_options_list();load_options_list_win('$og_id');\" /><input type='button' value='$mgrlang[gen_b_save]' onclick=\"submit_option_form('$og_id');\" /></p>";
				echo "</div>";
				echo "</form>";
			
				/*echo "<script>alert($options_rows);</script>";*/
				
				if($options_rows)
				{
					echo "<script>load_options_list_win('".$_GET['og_id']."');</script>";
				}
				else
				{
					echo "<script>edit_option('new','{$optiongrp->parent_type}');</script>";
				}
			}
		break;		
	}
	 	
?>
