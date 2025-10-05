<script type="text/javascript">
	// CHANGE OPTIONS BASED ON THE ORIGINAL DROPDOWN
	function original_dd()
	{
		var selected_original = $('original_copy').options[$('original_copy').selectedIndex].value;
		var licParts = selected_original.split('-');
		//$('rmspan').hide();
		
		//alert(licParts[0]);
		
		switch(licParts[0])
		{
			case "nfs":
				$('quantity_div').hide();
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('assign_price_div').hide();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('assign_credit_div').hide();<?php } ?>
				update_fsrow('tab3_group');
			break;
			case "fr":
				$('quantity_div').show();
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('assign_price_div').hide();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('assign_credit_div').hide();<?php } ?>
				update_fsrow('tab3_group');
			break;
			case "rf":
				$('quantity_div').show();
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('assign_price_div').show();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('assign_credit_div').show();<?php } ?>
				update_fsrow('tab3_group');
			break;
			case "rm":
				//$('rmspan').show();
				$('quantity_div').show();
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('assign_price_div').show();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('assign_credit_div').show();<?php } ?>
				update_fsrow('tab3_group');
			break;
			case "cu":
				$('quantity_div').show();
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('assign_price_div').hide();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('assign_credit_div').hide();<?php } ?>
				update_fsrow('tab3_group');
			break;
		}
	}
	
	// LOAD DSP DETAILS WINDOW
	function load_dsp_details(dsp_id)
	{
		//alert('test');
		$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
		$('digitalsp_'+dsp_id).checked = true;
		displayDSPOptions(dsp_id);
		$('dsp_customize_button_'+dsp_id).hide();
		
		// PROFILE HAS BEEN CUSTOMIZED - LOAD CUSTOMIZED SETTINGS
		if($F('dsp_customized_'+dsp_id) == '1')
		{
			//alert('test');
			var dsp_license = $F('dsp_license_'+dsp_id);
			//var dsp_rm_license = $F('dsp_rm_license_'+dsp_id);
			
			var dsp_format = $F('dsp_format_'+dsp_id);
			var dsp_hd = $F('dsp_hd_'+dsp_id);
			var dsp_running_time = $F('dsp_running_time_'+dsp_id);
			var dsp_width = $F('dsp_width_'+dsp_id);
			var dsp_height = $F('dsp_height_'+dsp_id);
			var dsp_fps = $F('dsp_fps_'+dsp_id);
			
			var dsp_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('dsp_price_'+dsp_id)<?php } else { echo "''"; } ?>;	
			var dsp_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('dsp_price_calc_'+dsp_id)<?php } else { echo "'norm'"; } ?>;
			var dsp_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('dsp_credits_'+dsp_id)<?php } else { echo "''"; } ?>;
			var dsp_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('dsp_credits_calc_'+dsp_id)<?php } else { echo "'norm'"; } ?>;

			
			var dsp_quantity = $F('dsp_quantity_'+dsp_id);
			show_div_fade_load('dsp_popup_'+dsp_id,'mgr.add.media.actions.php?mode=dps_details&customized=1&dsp_id='+dsp_id+'&license='+dsp_license+'&price='+dsp_price+'&price_calc='+dsp_price_calc+'&credits='+dsp_credits+'&credits_calc='+dsp_credits_calc+'&quantity='+dsp_quantity+'&width='+dsp_width+'&height='+dsp_height+'&running_time='+dsp_running_time+'&fps='+dsp_fps+'&hd='+dsp_hd+'&format='+dsp_format,'_content');
			//show_div_fade_load('dsp_popup_'+dsp_id,'mgr.add.media.actions.php?mode=dps_details&dsp_id='+dsp_id,'_content');
		}
		else
		{
			// LOAD DATABASE STORED VERSION
			show_div_fade_load('dsp_popup_'+dsp_id,'mgr.add.media.actions.php?mode=dps_details&dsp_id='+dsp_id,'_content');
		}
	}

	// SAVE ANY CUSTOMIZATIONS THAT YOU DO TO THE DSP AREA
	function save_dsp_customization(dsp_id)
	{
		hide_div('dsp_popup_'+dsp_id);
		
		var dsp_license_full = $F('dsp_license');
		var licParts = dsp_license_full.split('-');		
		var dsp_license = licParts[1];
		//var dsp_rm_license = $F('dsp_rm_license');
		var dsp_format = $F('dsp_format');
		var dsp_width = $F('dsp_width');
		var dsp_height = $F('dsp_height');
		
		if($F('dsp_custom_type') == 'video')
		{
			var dsp_fps = $F('dsp_fps');
			var dsp_hd = $F('dsp_hd');
			var dsp_running_time = $F('dsp_running_time');
		}

		var dsp_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('dsp_price')<?php } else { echo "''"; } ?>;
		var dsp_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('dsp_price_calc')<?php } else { echo "'norm'"; } ?>;
		var dsp_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('dsp_credits')<?php } else { echo "''"; } ?>;
		var dsp_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('dsp_credits_calc')<?php } else { echo "'norm'"; } ?>;
		//alert(dsp_price);
		
		var dsp_quantity = $F('dsp_quantity');
		
		$('dsp_customized_'+dsp_id).setValue('1');
		$('dsp_license_'+dsp_id).setValue(dsp_license);
		
		$('dsp_format_'+dsp_id).setValue(dsp_format);
		$('dsp_width_'+dsp_id).setValue(dsp_width);
		$('dsp_height_'+dsp_id).setValue(dsp_height);
		
		if($F('dsp_custom_type') == 'video')
		{
			$('dsp_fps_'+dsp_id).setValue(dsp_fps);
			$('dsp_hd_'+dsp_id).setValue(dsp_hd);
			$('dsp_running_time_'+dsp_id).setValue(dsp_running_time);
		}
		
		//$('dsp_rm_license_'+dsp_id).setValue(dsp_rm_license);
		<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>
			$('dsp_price_'+dsp_id).setValue(dsp_price);
			$('dsp_price_calc_'+dsp_id).setValue(dsp_price_calc);
		<?php } ?>
		<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>
			$('dsp_credits_'+dsp_id).setValue(dsp_credits);
			$('dsp_credits_calc_'+dsp_id).setValue(dsp_credits_calc);
		<?php } ?>
		
		$('dsp_quantity_'+dsp_id).setValue(dsp_quantity);
		
		$('dsp_clabel_'+dsp_id).show();
		$('dsp_customize_button_'+dsp_id).show();
		$('dsp_popup_'+dsp_id+'_content').update('');
		//show_div('dsp_customizations_'+dsp_id);
	}
	
	// SAVE ANY CUSTOMIZATIONS THAT YOU DO TO THE PROD AREA
	function save_prod_customization(prod_id)
	{
		hide_div('prod_popup_'+prod_id);

		var prod_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('prod_price')<?php } else { echo "''"; } ?>;
		var prod_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('prod_price_calc')<?php } else { echo "'norm'"; } ?>;
		var prod_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('prod_credits')<?php } else { echo "''"; } ?>;
		var prod_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('prod_credits_calc')<?php } else { echo "'norm'"; } ?>;
		
		var prod_quantity = $F('prod_quantity');
		
		$('prod_customized_'+prod_id).setValue('1');
		<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>
			$('prod_price_'+prod_id).setValue(prod_price);
			$('prod_price_calc_'+prod_id).setValue(prod_price_calc);
		<?php } ?>
		<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>
			$('prod_credits_'+prod_id).setValue(prod_credits);
			$('prod_credits_calc_'+prod_id).setValue(prod_credits_calc);
		<?php } ?>
		
		$('prod_quantity_'+prod_id).setValue(prod_quantity);
		
		$('prod_clabel_'+prod_id).show();
		$('prod_customize_button_'+prod_id).show();
		$('prod_popup_'+prod_id+'_content').update('');
		//show_div('dsp_customizations_'+dsp_id);
	}
	
	// SAVE ANY CUSTOMIZATIONS THAT YOU DO TO THE PRINT AREA
	function save_print_customization(print_id)
	{
		hide_div('print_popup_'+print_id);

		var print_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('print_price')<?php } else { echo "''"; } ?>;
		var print_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('print_price_calc')<?php } else { echo "'norm'"; } ?>;
		var print_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('print_credits')<?php } else { echo "''"; } ?>;
		var print_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('print_credits_calc')<?php } else { echo "'norm'"; } ?>;
		
		var print_quantity = $F('print_quantity');
		
		$('print_customized_'+print_id).setValue('1');
		<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>
			$('print_price_'+print_id).setValue(print_price);
			$('print_price_calc_'+print_id).setValue(print_price_calc);
		<?php } ?>
		<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>
			$('print_credits_'+print_id).setValue(print_credits);
			$('print_credits_calc_'+print_id).setValue(print_credits_calc);
		<?php } ?>
		
		$('print_quantity_'+print_id).setValue(print_quantity);
		
		$('print_clabel_'+print_id).show();
		$('print_customize_button_'+print_id).show();
		$('print_popup_'+print_id+'_content').update('');
		//show_div('dsp_customizations_'+dsp_id);
	}
	
	// LOAD PROD DETAILS WINDOW
	function load_prod_details(prod_id)
	{
		$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
		$('prod_'+prod_id).checked = true;
		$('prod_customize_button_'+prod_id).hide();

		// PROFILE HAS BEEN CUSTOMIZED - LOAD CUSTOMIZED SETTINGS
		if($F('prod_customized_'+prod_id) == '1')
		{
			var prod_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('prod_price_'+prod_id)<?php } else { echo "''"; } ?>;	
			var prod_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('prod_price_calc_'+prod_id)<?php } else { echo "'norm'"; } ?>;
			var prod_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('prod_credits_'+prod_id)<?php } else { echo "''"; } ?>;
			var prod_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('prod_credits_calc_'+prod_id)<?php } else { echo "'norm'"; } ?>;
			var prod_quantity = $F('prod_quantity_'+prod_id);
			show_div_fade_load('prod_popup_'+prod_id,'mgr.add.media.actions.php?mode=prod_details&customized=1&prod_id='+prod_id+'&price='+prod_price+'&price_calc='+prod_price_calc+'&credits='+prod_credits+'&credits_calc='+prod_credits_calc+'&quantity='+prod_quantity,'_content');
		}
		else
		{
			// LOAD DATABASE STORED VERSION
			show_div_fade_load('prod_popup_'+prod_id,'mgr.add.media.actions.php?mode=prod_details&prod_id='+prod_id,'_content');
		}
	}
	
	// LOAD print DETAILS WINDOW
	function load_print_details(print_id)
	{
		$$('.details_win').each(function(s) { s.setStyle({display: "none"}) });
		$('print_'+print_id).checked = true;
		$('print_customize_button_'+print_id).hide();

		// PROFILE HAS BEEN CUSTOMIZED - LOAD CUSTOMIZED SETTINGS
		if($F('print_customized_'+print_id) == '1')
		{
			var print_price = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('print_price_'+print_id)<?php } else { echo "''"; } ?>;	
			var print_price_calc = <?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$F('print_price_calc_'+print_id)<?php } else { echo "'norm'"; } ?>;
			var print_credits = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('print_credits_'+print_id)<?php } else { echo "''"; } ?>;
			var print_credits_calc = <?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$F('print_credits_calc_'+print_id)<?php } else { echo "'norm'"; } ?>;
			var print_quantity = $F('print_quantity_'+print_id);
			show_div_fade_load('print_popup_'+print_id,'mgr.add.media.actions.php?mode=print_details&customized=1&print_id='+print_id+'&price='+print_price+'&price_calc='+print_price_calc+'&credits='+print_credits+'&credits_calc='+print_credits_calc+'&quantity='+print_quantity,'_content');
		}
		else
		{
			// LOAD DATABASE STORED VERSION
			show_div_fade_load('print_popup_'+print_id,'mgr.add.media.actions.php?mode=print_details&print_id='+print_id,'_content');
		}
	}
	
	// CLOSE THE DSP CUSTOMIZE WINDOW AND CLEAR IT
	function close_dsp_window(dsp_id)
	{
		hide_div('dsp_popup_'+dsp_id);
		$('dsp_customize_button_'+dsp_id).show();
		$('dsp_popup_'+dsp_id+'_content').update('');
	}
	
	// CLOSE THE PROD CUSTOMIZE WINDOW AND CLEAR IT
	function close_prod_window(prod_id)
	{
		hide_div('prod_popup_'+prod_id);
		$('prod_customize_button_'+prod_id).show();
		$('prod_popup_'+prod_id+'_content').update('');
	}
	
	// CLOSE THE PRINT CUSTOMIZE WINDOW AND CLEAR IT
	function close_print_window(print_id)
	{
		hide_div('print_popup_'+print_id);
		$('print_customize_button_'+print_id).show();
		$('print_popup_'+print_id+'_content').update('');
	}
	
	// REMOVE ANY CUSTOMIZATIONS THAT YOU DO TO THE DSP AREA
	function remove_dsp_customization(dsp_id)
	{
		hide_div('dsp_popup_'+dsp_id);
		$('dsp_popup_'+dsp_id+'_content').update('');
		$('dsp_customized_'+dsp_id).setValue('0');
		$('dsp_clabel_'+dsp_id).hide();
		$('dsp_customize_button_'+dsp_id).show();
	}
	
	// REMOVE ANY CUSTOMIZATIONS THAT YOU DO TO THE PROD AREA
	function remove_prod_customization(prod_id)
	{
		hide_div('prod_popup_'+prod_id);
		$('prod_popup_'+prod_id+'_content').update('');
		$('prod_customized_'+prod_id).setValue('0');
		$('prod_clabel_'+prod_id).hide();
		$('prod_customize_button_'+prod_id).show();
	}
	
	// REMOVE ANY CUSTOMIZATIONS THAT YOU DO TO THE PRINT AREA
	function remove_print_customization(print_id)
	{
		hide_div('print_popup_'+print_id);
		$('print_popup_'+print_id+'_content').update('');
		$('print_customized_'+print_id).setValue('0');
		$('print_clabel_'+print_id).hide();
		$('print_customize_button_'+print_id).show();
	}
	
	// CHANGE THE DETAILS BASED ON THE DROPDOWN
	function update_dsp_license()
	{
		//show_div('dsp_rm_license_div');
		<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('dsp_price_div').show();<?php } ?>
		<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('dsp_credits_div').show();<?php } ?>

		show_div('dsp_quantity_div');
		var dsp_license = $('dsp_license').options[$('dsp_license').selectedIndex].value;
		var licParts = dsp_license.split('-');
		switch(licParts[0])
		{
			case "cu":
				//hide_div('dsp_rm_license_div');
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('dsp_price_div').hide();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('dsp_credits_div').hide();<?php } ?>
			break;
			case "fr":
				//hide_div('dsp_rm_license_div');
				<?php if($config['settings']['cart'] == 1 or $config['settings']['cart'] == 3){ ?>$('dsp_price_div').hide();<?php } ?>
				<?php if(($config['settings']['cart'] == 2 or $config['settings']['cart'] == 3) and $config['settings']['credits_digital']){ ?>$('dsp_credits_div').hide();<?php } ?>
			break;
			case "rm":
				
			break;
			case "ex":
			case "eu":
			case "rf":
				//hide_div('dsp_rm_license_div');
			break;
		}
		
	}
	
	// PREVIEW PRICE
	function price_preview(itemtype)
	{
		if($(itemtype+'_price_calc') != null)
		{
			//dsp_price_calc
			var selected_price_calc = $F(itemtype+'_price_calc');
			var price = currency_clean($F(itemtype+'_price'));
			var default_price = '<?php echo $config['settings']['default_price']; ?>';
			var original_copy_price;
			var original_copy_license = $F('original_copy');
			var total_price = '0';
			
			var licParts = original_copy_license.split('-');
			
			// CHECK WHAT THE LICENSE IS ON THE ORIGINAL
			switch(licParts[0])
			{
				case "rm":
				case "ex":
				case "eu":
				case "rf":
					original_copy_price = currency_clean($F('price'));
				break;
				case "fr":
				case "cu":
				case "nfs":
					original_copy_price = '0';
				break;
			}
			
			// CHECK TO SEE IF THE PRICE IS EMPTY
			if(original_copy_price == "")
			{
				original_copy_price = default_price;
			}
			
			// CHECK IF THE DSP PRICE = 0
			if(price == "")
			{
				price = default_price;
			}
			
			$(itemtype+'_price_preview').show();
			
			// SELECT WHICH CALC METHOD IS USED
			switch(selected_price_calc)
			{
				case "norm":
					$(itemtype+'_price_preview').hide();
				break;
				case "add":
					var calc_symbol = "+";
					total_price = (price*1) + (original_copy_price*1);
				break;
				case "sub":
					var calc_symbol = "-";
					total_price = original_copy_price - price;
					if(total_price < 0)
					{
						total_price = '0';
					}
				break;
				case "mult":
					var calc_symbol = "x";
					total_price = price * original_copy_price;
				break;
			}
			
			set_cur_hide_denotation(0);
			//alert(numset.cur_hide_denotation);
			original_copy_price = currency_display(original_copy_price,1);
			price = currency_display(price,1);
			total_price = currency_display(total_price,1);
			
			$(itemtype+'_price_preview').update("<strong><?php echo $mgrlang['gen_preview']; ?>:</strong>  <strong>"+original_copy_price+"</strong> (Original) <strong>"+calc_symbol+"</strong> <strong>"+price+"</strong> (Price) = <strong>"+total_price+"</strong>");
			set_cur_hide_denotation(1);
		}
	}
	
	// PREVIEW DSP CREDITS
	function credits_preview(itemtype)
	{
		if($(itemtype+'_credits_calc') != null)
		{
			//dsp_price_calc
			var selected_credits_calc = $F(itemtype+'_credits_calc');
			var credits = $F(itemtype+'_credits');
			var default_credits = '<?php echo $config['settings']['default_credits']; ?>';
			var original_copy_credits;
			var original_copy_license = $F('original_copy');
			var total_credits = '0';
			
			var licParts = original_copy_license.split('-');
			
			// CHECK WHAT THE LICENSE IS ON THE ORIGINAL
			switch(licParts[0])
			{
				case "rm":
				case "ex":
				case "eu":
				case "rf":
					original_copy_credits = $F('credits');
				break;
				case "fr":
				case "cu":
				case "nfs":
					original_copy_credits = '0';
				break;
			}
			
			// CHECK TO SEE IF THE PRICE IS EMPTY
			if(original_copy_credits == "")
			{
				original_copy_credits = default_credits;
			}
			
			// CHECK IF THE DSP PRICE = 0
			if(credits == "")
			{
				credits = default_credits;
			}
			
			$(itemtype+'_credits_preview').show();
			
			// SELECT WHICH CALC METHOD IS USED
			switch(selected_credits_calc)
			{
				case "norm":
					$(itemtype+'_credits_preview').hide();
				break;
				case "add":
					var calc_symbol = "+";
					total_credits = Number(credits) + Number(original_copy_credits);
				break;
				case "sub":
					var calc_symbol = "-";
					total_credits = original_copy_credits - credits;
					if(total_credits < 0)
					{
						total_credits = '0';
					}
				break;
				case "mult":
					var calc_symbol = "x";
					total_credits = credits * original_copy_credits;
				break;
			}
			
			total_credits = parseInt(total_credits);
			
			$(itemtype+'_credits_preview').update("<strong><?php echo $mgrlang['gen_preview']; ?>:</strong>  <strong>"+original_copy_credits+"</strong> (Original) <strong>"+calc_symbol+"</strong> <strong>"+credits+"</strong> (Credits) = <strong>"+total_credits+"</strong>");
		}
	}
</script>