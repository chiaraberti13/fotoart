<?php
	###################################################################
	####	GROUPS ACTIONS		   	                     		   ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 7-27-2010                                     ####
	####	Modified: 7-27-2010                                    #### 
	###################################################################
		
		//sleep(3);

		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "options";
		
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
		
		# INCLUDE TWEAK FILE
		require_once('../assets/includes/tweak.php');
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE
		include_lang();	
		
		# INCLUDE MANAGER ADDONS FILE	
		require_once('../assets/includes/addons.php');
		
		# FP
		//require_once('../assets/includes/config.php');
		
		# ACTIONS
		switch($_REQUEST['opboxmode'])
		{
			default:
			break;
			# DELETE OPTION
			case "delete_option":
				# FIND DETAILS ABOUT THE SELECTED GROUP
				$option_result = mysqli_query($db,"SELECT name,op_id,parent_id FROM {$dbinfo[pre]}options WHERE op_id = '$_REQUEST[id]'");
				$option = mysqli_fetch_object($option_result);

				# SET TO DELETED
				$sql = "UPDATE {$dbinfo[pre]}options SET deleted='1' WHERE op_id  = '$_REQUEST[id]'";
				$result = mysqli_query($db,$sql);
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_option'],1,$mgrlang['gen_b_del'] . " > <strong>$option->name ($_REQUEST[id])</strong>");
				
				# FIND THE NUMBER OF REMAINING GROUPS
				$new_option_count = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(op_id) FROM {$dbinfo[pre]}options WHERE deleted != '1' AND parent_id = '$option->parent_id'"));
				
				echo "<script>$('option_row_$_REQUEST[id]').remove();updaterowcolors('.prodoptionrow','#FFF','#EEE');</script>";
				
				if($new_option_count < 1)
				{
					echo "<script>$('options_list_win').update('<div style=\"padding: 20px;\"><img src=\"images/mgr.notice.icon.png\" align=\"absmiddle\" />$mgrlang[gen_empty_options]</div>');</script>";
				}
			break;
			case "options_list":
				$og_id = $_GET['og_id'];
				//echo $og_id; exit;
				
				//echo print_r($_GET).'adsasdsad';
				
				//echo "<div style='overflow: auto;'><input type='button' value='test' onclick='tothefront();' /></div>";
				
				$optionGroupresult = mysqli_query($db,"SELECT og_id,parent_type FROM {$dbinfo[pre]}option_grp WHERE og_id = '{$og_id}'");
				$optionGroup = mysqli_fetch_assoc($optionGroupresult);
				
				# SELECT ITEMS
				$option_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}options WHERE deleted = '0' AND parent_id = '$og_id' ORDER BY sortorder");
				$option_rows = mysqli_num_rows($option_result);
				if($option_rows)
				{
					while($option = mysqli_fetch_object($option_result))
					{
						# SET THE ROW COLOR
						@$row_color++;
						if ($row_color%2 == 0) {
							$color = "EEE";
						} else {
							$color = "FFF";
						}
						
						echo "<div id='option_row_$option->op_id' class='prodoptionrow' style='overflow: auto; height: 44px; background-color: #$color'>";
							echo "<p style='float: left; width: 48px; text-align: center; margin-top: 12px;'><img src='images/mgr.updown.arrow.png' class='handle' onmousedown='set_moved_option($option->op_id);' id='option_handle_$option->op_id' align='absmiddle' /></p>";
							echo "<p style='float: left; margin-top: 14px; cursor: pointer; width: 520px'";
							if($_SESSION['admin_user']['admin_id'] != "DEMO"){ echo " onclick=\"edit_option($option->op_id,'{$optionGroup[parent_type]}');\""; } 
							echo ">";
								echo "$option->name";
							echo "</p>";
							echo "<p align='right' style='float: right; margin-top: 14px; margin-right: 14px;' nowrap='nowrap'>";
								echo "<a href=\"javascript:";
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message2();"; } else { echo "edit_option($option->op_id,'{$optionGroup[parent_type]}');"; } 
								echo "\" class='actionlink'><img src='images/mgr.icon.edit.png' align='absmiddle' alt='$mgrlang[gen_edit]' border='0' />$mgrlang[gen_short_edit]</a>&nbsp;";
								echo "<a href='javascript:";
								if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "demo_message2();"; } else { echo "delete_option($option->op_id);"; } 
								echo "' class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='$mgrlang[gen_delete]' border='0' />$mgrlang[gen_short_delete]</a>";
							echo "</p>";
						echo "</div>";
					}
				}
				else
				{
					echo "<div style='padding: 20px;'><img src='images/mgr.notice.icon.png' align='absmiddle' />$mgrlang[gen_empty_options]</div>";	
				}
			break;
			case "edit_option":
				
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
				
				if($_GET['op_id'] != "new"){
					$option_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}options WHERE op_id = '$_GET[op_id]'");
					// FP					
					/*$option_result = mysqli_query($db,"	SELECT
														a.op_id, a.uop_id, a.name, a.price, a.price_mod, a.credits, a.credits_mod, a.my_cost, a.add_weight, a.sortorder,
														a.active, a.deleted, a.name_english, a.name_russian, a.name_spanish, a.name_dutch, a.name_french, a.name_german,
														a.name_, a.name_polish, a.name_romanian, a.name_italian, b.name as nome_gruppo_opzione 
 														FROM {$dbinfo[pre]}options as a INNER JOIN {$dbinfo[pre]}option_grp as b ON(a.parent_id = b.og_id) WHERE op_id = '$_GET[op_id]'");*/
					$option_rows = mysqli_num_rows($option_result);
					$option = mysqli_fetch_object($option_result);
				}
?>
                <input type="hidden" name="op_id" id="op_id" value="<?php echo $_GET['op_id']; ?>" />
                <input type="hidden" name="opboxmode" id="opboxmode" value="save_option" />
                <?php $row_color = 0; ?>
                <div class="fs_row_off" id="option_name_div" style="background-color: #FFF;">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['gen_option_f_name']; ?>:<br />
                        <span><?php echo $mgrlang['gen_option_f_name']; ?></span>
                    </p>
                    
                    
                    <div style="float: left;" class="additional_langs">
                        <input type="text" name="option_name" id="option_name" style="width: 244px;" maxlength="50" value="<?php echo @stripslashes($option->name); ?>" />
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('option_names','','','','plusminus-01_opt');"><img src="images/mgr.plusminus.0.png" id="plusminus01_opt" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="option_names" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="optname_<?php echo $value; ?>" style="width: 244px;" maxlength="100" value="<?php echo @stripslashes($option->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                <div class="fs_row_on" style="background-color: #f7f7f7;">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['gen_price']; ?>:<br />
                        <span><?php echo $mgrlang['gen_option_f_price_d']; ?></span>
                    </p>
                    <select style="width: 110px;" name="option_price_mod">
                        <option value="add" <?php if($option->price_mod == 'add' or $_GET['op_id'] == 'new'){ echo " selected='selected'"; } ?>><?php echo $mgrlang['gen_price']; ?> ( + )</option>
                        <option value="sub" <?php if($option->price_mod == 'sub'){ echo " selected='selected'"; } ?>><?php echo $mgrlang['gen_price']; ?> ( - )</option>
                    </select> <input type="text" name="option_price" id="option_price" value="<?php echo @$cleanvalues->currency_display($option->price); ?>" onblur="update_input_cur('option_price');" /> <?php $cleanvalues->example_currency_text(5.00,6.50); ?>
                </div>
                <?php
					//echo $_GET['parentType'];
					
					switch($_GET['parentType'])
					{
						case "products":
							$creditsType = 'credits_prod';
						break;						
						case "prints":
							$creditsType = 'credits_print';
						break;						
					}
				
					if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings'][$creditsType])
					{
				?>
                    <div class="fs_row_off" style="background-color: #FFF;">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['gen_credits']; ?>:<br />
                            <span><?php echo $mgrlang['gen_option_f_credits_d']; ?></span>
                        </p>
                        <select style="width: 110px;" name="option_credits_mod">
                            <option <?php if($option->credits_mod == 'add' or $_GET['op_id'] == 'new'){ echo " selected='selected'"; } ?> value="add"><?php echo $mgrlang['gen_credits']; ?> ( + )</option>
                            <option <?php if($option->credits_mod == 'sub'){ echo " selected='selected'"; } ?> value="sub"><?php echo $mgrlang['gen_credits']; ?> ( - )</option>
                        </select> <input type="text" name="option_credits" value="<?php echo $option->credits; ?>" />
                    </div>
<?php
					}

			// FP
			//if ( $option->nome_gruppo_opzione == PUZZLES_BOX_OPTION_NAME ) echo "GESTISCO LE SCATOLE";
			//var_dump($option->nome_gruppo_opzione);
			break;
			case "save_option":
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				// fix price
				# INCLUDE DEFAULT CURRENCY SETTINGS
				require_once('mgr.defaultcur.php');
				
				$cleanvalues = new number_formatting;
				$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
				$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
				
				$price_clean = $cleanvalues->currency_clean($option_price);
				
							
				# SAVE NEW				
				if($op_id == 'new')
				{
					/*
					# INSERT INFO INTO THE DATABASE - OLD
					$sql = "INSERT INTO {$dbinfo[pre]}options (
							name,parent_id,price_mod,price,credits_mod,credits
							) VALUES (
							'$option_name','$og_id','$option_price_mod','$option_price','$option_credits_mod','$option_credits'
							)";
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					*/
					
					# ADD SUPPORT FOR ADDITIONAL LANGUAGES
					foreach($active_langs as $value){ 
						$name_val = ${"langname_" . $value};
						$addsqla.= ",name_$value";
						$addsqlb.= ",'$name_val'";
					}
					
					# INSERT INFO INTO THE DATABASE
					$sql = "INSERT INTO {$dbinfo[pre]}options (
							name,
							parent_id,
							price_mod,
							price,
							credits_mod,
							credits";
					$sql.= $addsqla;
					$sql.= ") VALUES (
							'$option_name',
							'$og_id',
							'$option_price_mod',
							'$price_clean',
							'$option_credits_mod',
							'$option_credits'";
					$sql.= $addsqlb;
					$sql.= ")";				
					$result = mysqli_query($db,$sql);
					$saveid = mysqli_insert_id($db);
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_option'],1,$mgrlang['gen_b_new'] . " > <strong>$option_name ($saveid)</strong>");
				}
				# SAVE EDIT
				else
				{
					# UPDATE THE DATABASE - OLD
					//$sql = "UPDATE {$dbinfo[pre]}options SET 
					//			name='$option_name',price_mod='$option_price_mod',price='$option_price',credits_mod='$option_credits_mod',credits='$option_credits'	where op_id  = '$op_id'";
					//$result = mysqli_query($db,$sql);
					
					# ADD SUPPORT FOR ADDITIONAL LANGUAGES
					foreach($active_langs as $value){ 
						$name_val = ${"optname_" . $value};
						$addsql.= "name_$value='$name_val',";
					}
					
					# UPDATE THE DATABASE
					$sql = "UPDATE {$dbinfo[pre]}options SET 
								name='{$option_name}',";
					$sql.= $addsql;
					$sql.= "	price_mod='$option_price_mod',
								price='$price_clean',
								credits_mod='$option_credits_mod',
								credits='$option_credits'
								where op_id  = '$op_id'";
					$result = mysqli_query($db,$sql);
										
					
					# UPDATE ACTIVITY LOG
					save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['gen_option'],1,$mgrlang['gen_b_ed'] . " > <strong>$option_name ($op_id)</strong>");
				}
				
				/*echo "<script>load_group_list_win('$mgrarea');</script>";*/
				
			break;
			case "update_oplist":
				$x = 0;
				foreach($_GET['options_list_win'] as $value)
				{
					if($value)
					{
						$sql = "UPDATE {$dbinfo[pre]}options SET sortorder='$x' WHERE op_id  = '$value'";
						$result = mysqli_query($db,$sql);
					}
					$x++;
				}
			break;
		}	
?>
