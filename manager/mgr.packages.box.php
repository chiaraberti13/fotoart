<?php
	function print_row($id=0,$isnew="new"){
		global $config, $mgrlang, $print, $dbinfo, $active_langs;
		
		echo "\n<div style='clear: both; background-color: #FFFFFF; width: 100%;' id='print_row".$id."' class='print_row'>";
			echo "\n<div style='clear: both; width: 100%;'>";
				echo "\n<div class='ps_datarow' style='width: 7%'><img src='images/mgr.updown.arrow.png' class='handle' onmouseover=\"close_options('print_options');\" /><input type='hidden' name='print_id[]' id='print_id".$id."' value='".$id."' /><input type='hidden' name='print_refid[]' id='print_ref".$id."' value='".$id."' /><input type='hidden' name='ps_perm[]' id='ps_perm".$id."' value='"; if(@empty($print->perm)){ echo 'everyone'; } else { echo $print->perm; } echo "' /></div>";
				echo "\n<div class='ps_datarow' style='width: 20%'><input type='text' name='print_item_code[]' id='print_item_code".$id."' value='".@$print->item_code."' style='width: 180px' /><input type='hidden' name='print_isnew[]' id='print_isnew".$id."' value='".$isnew."' /></div>";
				echo "\n<div class='ps_datarow' style='width: 25%; height: auto;'><input type='text' class='print_item_name' name='print_item_name[]' id='ps_item_name".$id."' value='".@$print->item_name."' style='width: 90%' />";
					if(in_array('multilang',$installed_addons)){
						echo " <a href=\"javascript:displaybool('inlang_".$id."','','','','plusminus-in".$id."');\"><img src='images/mgr.plusminus.0.gif' id='plusminusin".$id."' border='0' class='plusminus' align='absmiddle' /></a>";
						echo "\n<div id='inlang_".$id."' style='display: none; font-size: 9px; text-align: left; color: #919191; margin-top: 3px;' class='lang_boxes'>";
							foreach($active_langs as $value){
								echo "\n" . ucfirst($value) . "<br /><input type='text' name='print_item_name_".$value."[]' id='print_item_name".$value.$id."' value='".@$print->{'item_name_' . $value}."' style='width: 100%;margin-bottom: 3px;' /><br />\n";
							}
						echo "\n</div>";
					}
				echo "\n</div>";
				//echo "\n<div class='ps_datarow' style='width: 10%'><input type='text' name='print_quantity[]' id='print_quantity".$id."' style='width: 50px' value='".@$print->quantity."' /></div>";
				echo "\n<div class='ps_datarow' style='width: 10%;'><a href=\"javascript:displaybool('print_options_pr".$id."','','','','plusminus-".$id."');\"><img src='images/mgr.plusminus.0.gif' id='plusminus".$id."' border='0' class='plusminus' style='margin-top: 5px;' /></a></div>";
				echo "\n<div class='ps_datarow' style='width: 10%; float: right;'><a href=\"javascript:remove_row('print_row".$id."','".$id."','print_row','1','print_row');\" class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='". $mgrlang['gen_delete']."' border='0' />".$mgrlang['gen_short_delete']."</a>&nbsp;</div>";
			echo "\n</div>";
			echo "\n<div id='print_options_pr".$id."' class='print_options'>";
				echo "\n<div class='options_box'>";
					echo "\n<p style='margin-bottom: 4px;'><label>".$mgrlang['packages_f_labcode']."<br /><span>".$mgrlang['packages_f_labcode_d']."</span></label><input type='text' name='print_lab_code[]' id='print_lab_code".$id."' value='".@$print->lab_code."' style='width: 100px' /></p>";
					//echo "\n<p style='margin-bottom: 4px;'><label>".$mgrlang['packages_f_discount']."<br /><span>".$mgrlang['packages_f_discount_d']."</span></label><input type='text' name='print_discount[]' class='textbox' value='".@$print->discount."' style='width: 100px;' /></p>";
					echo "\n<a href=\"javascript:print_add_group('".$id."');\" class='actionlink' style='clear: both; float: right;'><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> $mgrlang[printps_b_aog] >></a>";
				echo "\n</div>";
				echo "\n<div style='float: right; margin: 10px 20px 0 0; width: 59%' id='print_group_pr".$id."'>";
					echo "\n<!--PLACE HOLDER --><div id='print_group_item_pr".$id."_g0' class='print_group_pr".$id."'></div>";
					
					if($isnew != "new"){
						$grp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}print_grp WHERE parent_id = '$id' AND active = '1' ORDER BY sortorder");
						$grp_rows = mysqli_num_rows($grp_result);
						while($grp = mysqli_fetch_object($grp_result)){
							echo "\n<div style='width: 100%; background-color: #f5f7f9; border: 1px solid #cccccc; margin-bottom: 6px;' id='print_group_item_pr".$id."_g".$grp->prg_id."' class='print_group_pr".$id."'>";
								echo "\n<div class='print_group_header'><a href=\"javascript:remove_row('print_group_item_pr".$id."_g".$grp->prg_id."','".$grp->prg_id."','print_group_pr".$id."','','print_group');\" class='actionlink' style='float: right; margin: 5px 6px 0px 0px; font-weight: normal'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='".$mgrlang['gen_delete']."' border='0' />".$mgrlang['gen_short_delete']."</a><img src='images/mgr.updown.arrow.png' class='handle' align='absmiddle' style='margin: 8px 20px 8px 20px;' />$mgrlang[packages_opgrp_name] <input type='text' name='option_group_name[]' value='".$grp->name."' style='width: 150px;' align='absmiddle' />";
								if(in_array('multilang',$installed_addons)){
									echo "\n<a href=\"javascript:displaybool('lang_".$id."_g".$grp->prg_id."','','','','plusminus-".$id."_g".$grp->prg_id."');\"><img src='images/mgr.plusminus.0.gif' id='plusminus".$id."_g".$grp->prg_id."' border='0' class='plusminus' align='texttop' /></a>";
								}
								echo " $mgrlang[packages_opgrp_type] ";
								echo "\n<select class='select' name='option_group_type[]'>";
									echo "\n<option value='dropdown'"; if($grp->ltype == 'dropdown'){ echo "selected"; }; echo ">".$mgrlang['packages_op_dropdown']."</option>";
									echo "\n<option value='radio'"; if($grp->ltype == 'radio'){ echo "selected"; }; echo ">".$mgrlang['packages_op_radio']."</option>";
									echo "\n<option value='checkbox'"; if($grp->ltype == 'checkbox'){ echo "selected"; }; echo ">".$mgrlang['packages_op_checkbox']."</option>";
									echo "\n</select>";
									//echo "\n<div>test</div>";
									if(in_array('multilang',$installed_addons)){
										//echo "";
										echo "\n<div id='lang_".$id."_g".$grp->prg_id."' style='display: none; font-size: 9px; text-align: left; color: #ffffff; padding: 0 0 5px 162px;' class='lang_boxes'>";
											foreach($active_langs as $value){
												echo "\n" . ucfirst($value) . "<br /><input type='text' name='option_group_name_".$value."[]' value='".@$grp->{'name_' . $value}."' style='width: 150px;margin-bottom: 3px;' /><br />\n";
											}
										echo "\n</div>";
									}
									echo "\n</div>";
								echo "\n<div style='clear: both; float: left; width: 5%; padding: 4px; text-align: center; margin-left: 10px;'><input type='hidden' name='group_id[]' id='group_id".$grp->prg_id."' value='".$grp->prg_id."' /><input type='hidden' name='group_isnew[]' id='group_isnew".$grp->prg_id."' value='".$grp->prg_id."' /><input type='hidden' name='group_parent[]' id='group_parent".$grp->prg_id."' value='".$id."' />".$mgrlang['packages_gh_order']."</div>";
								echo "\n<div style='float: left; width: 25%; padding: 4px; text-align: center'>".$mgrlang['packages_gh_name']."</div>";
								echo "\n<div style='float: left; width: 25%; padding: 4px; text-align: center'>".$mgrlang['packages_gh_price']."</div>";
								echo "\n<div style='float: left; width: 10%; padding: 4px; text-align: center'>".$mgrlang['packages_gh_my_cost']."</div>";
								echo "\n<div style='float: left; width: 13%; padding: 4px; text-align: center'>".$mgrlang['packages_gh_weight']."</div>";
								echo "\n<div style='float: left; width: 8%; padding: 4px; text-align: center'></div>";
								echo "\n<div style='clear: both; width: 100%;' id='print_option_pr".$id."_g".$grp->prg_id."'>";
									echo "\n<!-- PLACE HOLDER --><div id='print_option_item_pr".$id."_g".$grp->prg_id."_i0' class='print_option_pr".$id."_g".$grp->prg_id."'></div>";
									
									$option_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}print_option WHERE parent_id = '$grp->prg_id' AND active = '1' ORDER BY sortorder");
									$option_rows = mysqli_num_rows($option_result);
									while($option = mysqli_fetch_object($option_result)){
										echo "\n<div style='width: 100%; clear: both; background-color: #f5f7f9' id='print_option_item_pr".$id."_g".$grp->prg_id."_i".$option->op_id."' class='print_option_pr".$id."_g".$grp->prg_id."'>";
											echo "\n<div style='clear: both; float: left; width: 5%; padding: 4px; text-align: center; margin-left: 10px;'><img src='images/mgr.updown.arrow.png' class='handle' /><input type='hidden' name='option_id[]' id='id".$grp->prg_id."' value='".$option->op_id."' /><input type='hidden' name='option_parent[]' id='parent".$option->op_id."' value='".$grp->prg_id."' /></div>";
											echo "\n<div style='float: left; width: 25%; padding: 4px; text-align: center'><input type='text' name='option_name[]' value='".$option->name."' class='print_option_name' id='option_name".$option->op_id."' style='width: 80%' />";
											if(in_array('multilang',$installed_addons)){
												echo " <a href=\"javascript:displaybool('lang_".$id."_g".$grp->prg_id."_i".$option->op_id."','','','','plusminus-".$id."_g".$grp->prg_id."_i".$option->op_id."');\"><img src='images/mgr.plusminus.0.gif' id='plusminus".$id."_g".$grp->prg_id."_i".$option->op_id."' border='0' class='plusminus' align='absmiddle' /></a>";
												echo "\n<div id='lang_".$id."_g".$grp->prg_id."_i".$option->op_id."' style='display: none; font-size: 9px; text-align: left; color: #919191; margin-top: 3px; padding-left: 7px;' class='lang_boxes'>";
													foreach($active_langs as $value){
														echo "\n" . ucfirst($value) . "<br /><input type='text' name='option_name_".$value."[]' value='".@$option->{'name_' . $value}."' style='width: 90%;margin-bottom: 3px;' /><br />\n";
													}
												echo "\n</div>";
											}											
											echo "\n</div>";
											echo "\n<div style='float: left; width: 25%; padding: 1px 4px 4px 4px; text-align: center'>";
												echo "<select name='option_pricea[]' class='select'>";
													echo "<option value='+' "; if(@$option->price_mod == '+'){ echo "selected"; }; echo ">".$mgrlang['packages_h_price']." +</option>";
													echo "<option value='-' "; if(@$option->price_mod == '-'){ echo "selected"; }; echo ">".$mgrlang['packages_h_price']." &ndash;</option>";
													echo "<option value='x' "; if(@$option->price_mod == 'x'){ echo "selected"; }; echo ">".$mgrlang['packages_h_price']." &times;</option>";
													echo "<option value='$' "; if(@$option->price_mod == '$'){ echo "selected"; }; echo ">".$config['settings']['cur_denotation']."</option>";
												echo "</select><input type='text' name='option_priceb[]' value='".$option->price."' id='option_priceb".$grp->prg_id."' style='width: 50px' /></div>";
											echo "\n<div style='float: left; width: 10%; padding: 4px; text-align: center'>$&nbsp;<input type='text' name='option_cost[]' value='".$option->my_cost."' id='option_cost".$option->op_id."' style='width: 50px' /></div>";
											echo "\n<div style='float: left; width: 13%; padding: 4px; text-align: center'><input type='text' name='option_weight[]' value='".$option->add_weight."' id='option_weight0' style='width: 50px' /></div>";
											echo "\n<div style='float: left; width: 10%; padding: 4px; text-align: right'><a href=\"javascript:remove_row('print_option_item_pr".$id."_g".$grp->prg_id."_i".$option->op_id."','".$option->op_id."','print_option_pr".$id."_g".$grp->prg_id."','','print_option');\" class='actionlink'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='".$mgrlang['gen_delete']."' border='0' />".$mgrlang['gen_short_delete']."</a>&nbsp;</div>";
										echo "\n</div>";
										echo "\n<script language='javascript' type='text/javascript'>create_sortlist('print_option_pr".$id."_g".$grp->prg_id."',0);</script>";
									}
								echo "\n</div>";								
								echo "\n<div class='add_option_row' align='right'>";
									echo "\n<a href=\"javascript:print_add_option('".$id."','".$grp->prg_id."','1')\" class='actionlink'><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> $mgrlang[packages_b_aop]</a>";
									//echo "\n<input type='button' value='Add Option' class='small_button' onclick=\"print_add_option('".$id."','".$grp->prg_id."','1')\" style='margin: 7px 8px 5px 0px;' />";
								echo "\n</div>";
							echo "\n</div>";
						}
						echo "\n<script language='javascript' type='text/javascript'>create_sortlist('print_group_pr".$id."',0);</script>";
					}
				echo "\n</div>";
				echo "\n<div style='width: 100%; height: 14px;' align='right'></div>";
			echo "\n</div>";
		echo "\n</div>";
	}
?>
<div id="ps_box" style="overflow: auto;">
    <div style="clear: both; width: 100%; background-image: url(./images/mgr.table.bar.bg.gif); background-repeat: repeat-x;" onclick="support_popup(0);">
        <div class="ps_header" style="width: 7%"><div onclick="create_unique_id();"><?php echo $mgrlang['packages_h_order']; ?></div></div>
        <div class="ps_header" style="width: 20%"><div><?php echo $mgrlang['packages_h_item_code']; ?></div></div>
        <div class="ps_header" style="width: 25%"><div><?php echo $mgrlang['packages_h_item_name']; ?></div></div>
        <div class="ps_header" style="width: 10%"><div><?php echo $mgrlang['packages_h_options']; ?></div></div>
        <div class="ps_header" style="width: 10%"><div>&nbsp;</div></div>
    </div>
    <div style="clear: both; width: 100%;" id="print_row">
    <?php
		$print_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}prints WHERE parent_id = '$_GET[edit]' AND active = '1' AND ptype = 'pack' ORDER BY sortorder");
		$print_rows = mysqli_num_rows($print_result);
		# ADDED THE CHECK FOR ROWS JUST IN CASE SOMETHING GOES WRONG AND WE NEED THE NEW RECORD BACK IN
		if($_GET['edit'] == "new" or empty($print_rows)){
			print_row();
		} else {
			while($print = mysqli_fetch_object($print_result)){					
				print_row($print->print_id,$print->print_id);
			}
		}
	?>
    </div>
    <div style="height: 25px; clear: both; float: none; padding: 8px 8px 0px 0px;; border-top: 1px solid #d7d7d7; background-color: #eeeeee; background-image: url(images/mgr.tabarea.fade.gif); background-repeat:repeat-x; color: #777777;" align="right" id="new_path_div">
        <!--<p style="float: left; margin: 5px 5px 5px 15px;"><input type="button" value="Preview" onclick="print_add_row('');" /></p>-->
        <p style="float: left; margin: 0px 5px 5px 5px;"><input type="text" style="width: 30px; font-size: 9px; background-color: #eeeeee; border-top: 1px solid #c9c8c8; border-left: 1px solid #c9c8c8; border-bottom: 1px solid #fff; border-right: 1px solid #fff; padding: 1px 2px 1px 3px; color: #777777;" value="0" id="ounces" onkeyup="convert_o2g();" /> Ounces = <span id="grams" style="font-weight: bold;">0</span> Grams</p>
        <a href="javascript:print_add_row('');" class="actionlink" style="float: right"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['packages_b_add']; ?></a>
    </div>
</div>