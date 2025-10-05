<script type="text/javascript" type="text/javascript">
	// PASSVARS
	var admin_id = "<?php echo $_SESSION['admin_user']['admin_id']; ?>";
	var verify_before_delete = "<?php echo $config['settings']['verify_before_delete']; ?>";
	var gen_b_del = "<?php echo $mgrlang['gen_b_del']; ?>";
	var gen_b_cancel2 = "<?php echo $mgrlang['gen_b_cancel2']; ?>";
	var gen_suredelete = "<?php echo $mgrlang['gen_suredelete']; ?>";
	var enable_credits = "<?php echo $config['settings']['enable_credits']; ?>";
	var enable_cbp = "<?php echo $config['settings']['enable_cbp']; ?>";
	var enable_cart = "<?php echo $config['settings']['enable_cart']; ?>";
	var gen_delete = "<?php echo $mgrlang['gen_delete']; ?>";
	var mulilang = "<?php if(in_array('multilang',$installed_addons)){ echo 1; } else { echo 0; } ?>";
	var gen_b_assign = "<?php echo $mgrlang['gen_b_assign']; ?>";
	var gen_to = "<?php echo $mgrlang['gen_to']; ?>";
	var gen_b_unassign = "<?php echo $mgrlang['gen_b_unassign']; ?>";
	var gen_from = "<?php echo $mgrlang['gen_from']; ?>";
	var gen_wb_noneavail = "<?php echo $mgrlang['gen_wb_noneavail']; ?>";
	var gen_b_close = "<?php echo $mgrlang['gen_b_close']; ?>";
	var gen_new_group = "<?php echo $mgrlang['gen_new_group']; ?>";
	var gen_edit_group = "<?php echo $mgrlang['gen_edit_group']; ?>";
	var demoMode = <?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "true"; } else { echo "false"; } ?>;
	
	// SET NUMBER VARIABLES
	var numset = new Object();
	<?php
		# CHECK IF CURRECNCY IS BEING PASSED
		if($config['settings']['cur_currency_id'])
		{
	?>
	// FOR CURRENCY
	numset.cur_hide_denotation = 1;
	numset.cur_currency_id = '<?php echo $config['settings']['cur_currency_id']; ?>';
	numset.cur_name = '<?php echo $config['settings']['cur_name']; ?>';
	numset.cur_code = '<?php echo $config['settings']['cur_code']; ?>';
	numset.cur_denotation = "<?php echo $config['settings']['cur_denotation']; ?>";
	numset.cur_denotation_reset = "<?php echo $config['settings']['cur_denotation']; ?>";
	numset.cur_decimal_separator = "<?php echo $config['settings']['cur_decimal_separator']; ?>";
	numset.cur_decimal_places = <?php echo $config['settings']['cur_decimal_places']; ?>;
	numset.cur_thousands_separator = "<?php echo $config['settings']['cur_thousands_separator']; ?>";		
	numset.cur_pos_num_format = <?php echo $config['settings']['cur_pos_num_format']; ?>;
	numset.cur_neg_num_format = <?php echo $config['settings']['cur_neg_num_format']; ?>;
	<?php
		}
	?>
	// FOR NUMBERS
	numset.decimal_separator = "<?php echo $config['settings']['decimal_separator']; ?>";
	numset.decimal_places = <?php echo $config['settings']['decimal_places']; ?>;
	numset.thousands_separator = "<?php echo $config['settings']['thousands_separator']; ?>";		
	numset.neg_num_format = <?php echo $config['settings']['neg_num_format']; ?>;
	numset.strip_ezeros = 0;
	
<?php
	/*
	if($profile_vars){
		# ADD LANGUAGES
		if(in_array('multilang',$installed_addons)){
			# GET THE ACTIVE LANGUAGES
			echo "var active_langs = new Array();\n";
			$active_lang_count=0;
			foreach($active_langs as $value){
				echo "active_langs[$active_lang_count]='".($value)."'\n";
				$active_lang_count++;
			}
		}
?>
		// OTHER
		var print_option_message = "<?php echo $mgrlang['prints_mes_02']; ?>";
		var leave_row = "<?php echo $mgrlang['gen_mes_leaverow']; ?>";		
		var prints_gh_order = "<?php echo $mgrlang['prints_gh_order']; ?>";
		var prints_gh_name = "<?php echo $mgrlang['prints_gh_name']; ?>";
		var prints_gh_price = "<?php echo $mgrlang['prints_gh_price']; ?>";
		var prints_gh_my_cost = "<?php echo $mgrlang['prints_gh_my_cost']; ?>";
		var prints_gh_weight = "<?php echo $mgrlang['prints_gh_weight']; ?>";
		var prints_op_dropdown = "<?php echo $mgrlang['prints_op_dropdown']; ?>";
		var prints_op_radio = "<?php echo $mgrlang['prints_op_radio']; ?>";
		var prints_op_checkbox = "<?php echo $mgrlang['prints_op_checkbox']; ?>";
		var prints_b_aog = "<?php echo $mgrlang['prints_b_aog']; ?>";
		var prints_b_aop = "<?php echo $mgrlang['prints_b_aop']; ?>";
		var prints_opgrp_type = "<?php echo $mgrlang['prints_opgrp_type']; ?>";
		var prints_opgrp_name = "<?php echo $mgrlang['prints_opgrp_name']; ?>";
		var prints_h_price = "<?php echo $mgrlang['prints_h_price']; ?>";
		var prints_base_price = "<?php echo $mgrlang['prints_base_price']; ?>";
<?php
	}
	*/
?>
</script>
<?php echo "\n"; ?>