
<?php
	function option_row($id=0,$isnew="new"){
		global $config, $mgrlang, $print, $dbinfo, $active_langs;
		
		if($isnew != "new"){
			$grp_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}print_grp WHERE parent_id = '$id' AND active = '1' ORDER BY sortorder");
			$grp_rows = mysqli_num_rows($grp_result);
			while($grp = mysqli_fetch_object($grp_result)){
				echo "\n<div style='background-color: #f5f7f9; border: 1px solid #cccccc; margin-bottom: 6px;' id='print_group_item_pr".$id."_g".$grp->prg_id."' class='print_group_pr".$id."'>";
					echo "\n<div class='print_group_header'><a href=\"javascript:remove_row('print_group_item_pr".$id."_g".$grp->prg_id."','".$grp->prg_id."','print_group_pr".$id."','','print_group');\" class='actionlink' style='float: right; margin: 5px 6px 0px 0px; font-weight: normal'><img src='images/mgr.icon.delete.png' align='absmiddle' alt='".$mgrlang['gen_delete']."' border='0' />".$mgrlang['gen_short_delete']."</a><img src='images/mgr.updown.arrow.png' class='handle' align='absmiddle' style='margin: 8px 20px 8px 20px;' />$mgrlang[prints_opgrp_name] <input type='text' name='option_group_name[]' value='".$grp->name."' style='width: 150px;' align='absmiddle' />";
					if(in_array('multilang',$installed_addons)){
						echo "\n<a href=\"javascript:displaybool('lang_".$id."_g".$grp->prg_id."','','','','plusminus-".$id."_g".$grp->prg_id."');\"><img src='images/mgr.plusminus.0.gif' id='plusminus".$id."_g".$grp->prg_id."' border='0' class='plusminus' align='texttop' /></a>";
					}
					echo " $mgrlang[prints_opgrp_type] ";
					echo "\n<select class='select' name='option_group_type[]'>";
						echo "\n<option value='dropdown'"; if($grp->ltype == 'dropdown'){ echo "selected"; }; echo ">".$mgrlang['prints_op_dropdown']."</option>";
						echo "\n<option value='radio'"; if($grp->ltype == 'radio'){ echo "selected"; }; echo ">".$mgrlang['prints_op_radio']."</option>";
						echo "\n<option value='checkbox'"; if($grp->ltype == 'checkbox'){ echo "selected"; }; echo ">".$mgrlang['prints_op_checkbox']."</option>";
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
					echo "\n<div style='clear: both; float: left; width: 5%; padding: 4px; text-align: center; margin-left: 10px;'><input type='hidden' name='group_id[]' id='group_id".$grp->prg_id."' value='".$grp->prg_id."' /><input type='hidden' name='group_isnew[]' id='group_isnew".$grp->prg_id."' value='".$grp->prg_id."' /><input type='hidden' name='group_parent[]' id='group_parent".$grp->prg_id."' value='".$id."' />".$mgrlang['prints_gh_order']."</div>";
					echo "\n<div style='float: left; width: 25%; padding: 4px; text-align: center'>".$mgrlang['prints_gh_name']."</div>";
					echo "\n<div style='float: left; width: 25%; padding: 4px; text-align: center'>".$mgrlang['prints_gh_price']."</div>";
					echo "\n<div style='float: left; width: 10%; padding: 4px; text-align: center'>".$mgrlang['prints_gh_my_cost']."</div>";
					echo "\n<div style='float: left; width: 13%; padding: 4px; text-align: center'>".$mgrlang['prints_gh_weight']."</div>";
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
										echo "<option value='+' "; if(@$option->price_mod == '+'){ echo "selected"; }; echo ">".$mgrlang['prints_h_price']." +</option>";
										echo "<option value='-' "; if(@$option->price_mod == '-'){ echo "selected"; }; echo ">".$mgrlang['prints_h_price']." &ndash;</option>";
										echo "<option value='x' "; if(@$option->price_mod == 'x'){ echo "selected"; }; echo ">".$mgrlang['prints_h_price']." &times;</option>";
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
						echo "\n<a href=\"javascript:print_add_option('".$id."','".$grp->prg_id."','1')\" class='actionlink'><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> $mgrlang[prints_b_aop]</a>";
						//echo "\n<input type='button' value='Add Option' class='small_button' onclick=\"print_add_option('".$id."','".$grp->prg_id."','1')\" style='margin: 7px 8px 5px 0px;' />";
					echo "\n</div>";
				echo "\n</div>";
			}
			echo "\n<script language='javascript' type='text/javascript'>create_sortlist('print_group_pr".$id."',0);</script>";
		}			
	}
?>
<div id="options_box">
	<?php
		echo "\n<div id='print_group_pr".$id."'>";
			echo "\n<!--PLACE HOLDER --><div id='print_group_item_pr".$id."_g0' class='print_group_pr".$id."'></div>";		
				option_row($id,$id);			
		echo "\n</div>";

	?>
    <!--
    <div style="height: 30px; clear: both; padding: 8px 8px 0px 0px; border: 1px solid #d7d7d7; background-color: #eeeeee; background-image: url(images/mgr.tabarea.fade.gif); background-repeat:repeat-x; color: #777777;" align="right" id="new_path_div">
        <a href="javascript:print_add_group('<?php echo $id; ?>');" class="actionlink" style="float: right"><img src='images/mgr.icon.greenplus.gif' border='0' align='absmiddle' /> <?php echo $mgrlang['prints_b_aog']; ?></a>
    </div>
    -->
</div>