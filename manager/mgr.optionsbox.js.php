<script language="javascript" type="text/javascript">	
	var handlesel = 0;
	
	// SET LAST MOVED DIV
	function set_moved_div(id)
	{
		handlesel = id;
		//alert(handlesel);
	}
	
	// CREATE SORTABLE LIST
	function create_sortlist(){
		Sortable.create('optionsbox',
			{tag:'div',handle: 'handle',only:'optionsbox_row',overlap:'vertical',constraint:'vertical',dropOnEmpty:true,onUpdate: update_optiongrp });
	}
	
	// SAVE SORTING ON THE LIST
	function update_optiongrp()
	{
		var opboxlist = Sortable.serialize('optionsbox');
		//alert(opboxlist);
		
		$('optionsbox_handle_'+handlesel).src='images/mgr.loader.gif';
		
		var url = 'mgr.optionsbox.php';
		var pars = 'pmode=update&item_id=<?php echo $_GET['edit']; ?>&page=<?php echo $page; ?>&'+opboxlist;
		var myAjax = new Ajax.Request( 
			url, 
			{
				method: 'get', 
				parameters: pars,
				evalScripts: true,
				onSuccess: function(transport){					
					transport.responseText.evalScripts();
					setTimeout(function(){ $('optionsbox_handle_'+handlesel).src='images/mgr.updown.arrow.png'; },500);
					updaterowcolors('.optionsbox_row','#fff','#f8f8f8');
					//alert(transport.responseText);
					//eval(transport.responseText);
					//var rowTemplate = new Template(templatedata);	
					//alert(rowname);	
					//$(rowname).insert({after: transport.responseText});
				}
			});
	}
	
	// LOAD OPTIONBOX
	var opboxloaded = 0;
	function load_optionsbox()
	{
		if(opboxloaded == 0)
		{
			show_loader('optionsbox');
			var pars = 'pmode=firstload&page=<?php echo $page; ?>&item_id=<?php echo $_GET['edit']; ?>';
			var myAjax = new Ajax.Updater('optionsbox', 'mgr.optionsbox.php', {method: 'get', parameters: pars, evalScripts: true, onComplete: function(){ updaterowcolors('.optionsbox_row','#fff','#f8f8f8'); } });
			opboxloaded = 1;
		}
	}
	
	// HIDE OPTIONS DROPDOWN IF NEEDED
	function optionGroupTypeChange(id)
	{
		if($F('optionsGroupTypeDD_'+id) == '2')
		{
			$('optiongrprequired_'+id).hide();
			$('optiongrprequired_'+id).checked=false;
		}
		else
		{
			$('optiongrprequired_'+id).show();
		}
	}
	
	// CHECK TO SEE IF ANY OPTION GROUPS EXIST
	function count_optiongrp(adj)
	{
		var numrows = $$('div.optionsbox_row').length;
		//alert(numrows);
		if((numrows-adj) > 0)
		{
			$('optionsbox').show();	
		}
		else
		{
			$('optionsbox').hide();
		}
	}
	
	// ADD OPTIONGRP
	function add_optiongrp()
	{			
		if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
		{
			demo_message();
		}
		else
		{				
			count_optiongrp(0);
			
			// CREATE NEW
			var numrows = $$('div.optionsbox_row').length;
			var rowname = $$('div.optionsbox_row')[numrows-1].id;	
			
			var url = 'mgr.optionsbox.php';
			var pars = 'pmode=addnew&item_id=<?php echo $_GET['edit']; ?>&page=<?php echo $page; ?>';
			var myAjax = new Ajax.Request( 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true,
					onComplete: function(transport){					
						transport.responseText.evalScripts();					
						//alert(transport.responseText);
						//eval(transport.responseText);
						//var rowTemplate = new Template(templatedata);	
						//alert(rowname);	
						$(rowname).insert({after: transport.responseText});
						
						// FADE IN ROW THAT WAS JUST ADDED
						rowname = $$('div.optionsbox_row')[numrows].id;							
						Effect.Appear(rowname,{ duration: 0.5, from: 0.0, to: 1.0 });
						setTimeout(function(){ updaterowcolors('.optionsbox_row','#fff','#f8f8f8'); },200);
					}
				});
		}
	}
	
	// DELETE OPTIONGRP
	function delete_optiongrp(id)
	{
		if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
		{
			demo_message();
		}
		else
		{
			<?php
				// IF VERIFT BEFORE DELETE IS ON
				if($config['settings']['verify_before_delete'])
				{
			?>
					message_box("<?php echo $mgrlang['gen_suredelete']; ?>","<input type='button' value='<?php echo $mgrlang['gen_b_cancel2']; ?>' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='<?php echo $mgrlang['gen_short_delete']; ?>' id='closebutton' class='button' onclick='do_delete_optiongrp(\""+id+"\");close_message();' />",'');
			<?php
				}
				else
				{
					echo "do_delete_optiongrp(id);";
				}
			?>
		}
	}
	
	// SET INITIAL OPTIONLOADED TO NOTHING
	var handlesel_op = 0;
	
	// DO DELETE
	function do_delete_optiongrp(id)
	{
		var rowname = 'optiongroup_'+id;
		//show_loader(rowname);
		
		$('optionsbox_delete_'+id).src='images/mgr.loader.gif';
		
		//setTimeout(function(){
				var pars = 'pmode=delete&page=<?php echo $page; ?>&item_id=<?php echo $_GET['edit']; ?>&delete='+id;
				new Ajax.Request('mgr.optionsbox.php', {method: 'get', parameters: pars, onComplete: function() {
					Effect.Fade('optiongroup_'+id,{ duration: 0.5 });
					setTimeout(function(){ $(rowname).remove(); updaterowcolors('.optionsbox_row','#fff','#f8f8f8'); count_optiongrp(1); },500);					
				} });
			//},100);
	}
	
	// SET THE LAST MOVED OPTION
	function set_moved_option(id)
	{
		handlesel_op = id;
	}
		
	function edit_option(op_id,parentType)
	{
		//alert(parentType);
		
		show_options_edit();
		if(op_id == 'new')
		{
			$('option_edit').update('<?php echo $mgrlang['gen_newoption']; ?>');
		}
		else
		{
			$('option_edit').update('<?php echo $mgrlang['gen_editoption']; ?>');
		}
		show_loader('options_edit_win');
		var updatecontent = 'options_edit_win';
		var loadpage = "mgr.optionsbox.actions.php?opboxmode=edit_option&op_id=" + op_id + "&parentType=" + parentType;
		var pars = "";
		var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars});
	}
	function delete_option(item_id)
	{
		var updatecontent = 'hidden_box';
		var loadpage = "mgr.optionsbox.actions.php?opboxmode=delete_option&id=" + item_id;
		var pars = "";
		Effect.Fade('option_row_'+item_id,{ duration: 0.5 });
		setTimeout(function(){ var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars, onComplete: create_options_sortlist}); },500);
	}
	
	function fix_options_button_row()
	{
		$('options_button_row').setStyle({
			zIndex: "999"
		});
	}
	
	// UPDATE THE OPTIONS LIST
	function update_optionlist()
	{
		var oplist = Sortable.serialize('options_list_win');
		//alert(oplist);
		
		$('option_handle_'+handlesel_op).src='images/mgr.loader.gif';
		
		var url = 'mgr.optionsbox.actions.php';
		var pars = 'opboxmode=update_oplist&'+oplist;
		var myAjax = new Ajax.Request( 
			url, 
			{
				method: 'get', 
				parameters: pars,
				evalScripts: true,
				onSuccess: function(transport){					
					transport.responseText.evalScripts();
					setTimeout(function(){ $('option_handle_'+handlesel_op).src='images/mgr.updown.arrow.png'; },500);
					updaterowcolors('.prodoptionrow','#FFF','#EEE');
					//alert(transport.responseText);
					//eval(transport.responseText);
					//var rowTemplate = new Template(templatedata);	
					//alert(rowname);	
					//$(rowname).insert({after: transport.responseText});
				}
			});
	}
	
	// CREATE SORTABLE LIST
	function create_options_sortlist(){
		Sortable.create('options_list_win',
			{tag:'div',handle: 'handle',only:'prodoptionrow',overlap:'vertical',constraint:'vertical',dropOnEmpty:true,onUpdate: update_optionlist });
	}
	
	function show_options_list(){
		show_div('options_list_win');
		hide_div('options_edit_win');		
		show_div('options_list_win_buttons');
		hide_div('options_edit_win_buttons');
		$('option_edit').update('<?php echo $mgrlang['gen_newoption']; ?>');
		//$('dobutton').value = gen_b_assign;
		//$('tofrom').update(gen_to);		
		$('option_list').className = 'subsubon';
		$('option_edit').className = 'subsuboff';
	}
	function show_options_edit(){
		hide_div('options_list_win');
		show_div('options_edit_win');
		hide_div('options_list_win_buttons');
		show_div('options_edit_win_buttons');
		//$('dobutton').value = gen_b_assign;
		//$('tofrom').update(gen_to);		
		$('option_list').className = 'subsuboff';
		$('option_edit').className = 'subsubon';
	}
	function load_options_list_win(og_id)
	{
		show_loader('options_list_win');
		var updatecontent = 'options_list_win';
		var loadpage = "mgr.optionsbox.actions.php?page=test&opboxmode=options_list&og_id=" + og_id;
		var pars = "";
		var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: pars, onComplete: function(){ create_options_sortlist(); fix_options_button_row(); }});
	}
	function submit_option_form(og_id)
	{
		if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
		{
			demo_message2();
		}
		else
		{
			if($F('option_name') == '' || $F('option_name') == null){
				$('option_name_div').className='fs_row_error';
				return false;
			}
			
			//alert($('option_edit_form').serialize());
	
			$('option_edit_form').request({
				onFailure: function() { alert('failed'); }, 
				onSuccess: function(transport) {
					load_options_list_win(og_id);
					show_options_list();
				}
			});
		}
	}
</script>