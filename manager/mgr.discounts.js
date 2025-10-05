// LOAD DISCOUNTBOX
var discountBoxLoaded = 0;
function loadDiscountsBox()
{
	if(discountBoxLoaded == 0)
	{
		show_loader('discounts_div');
		var itemID = $('discounts_div').readAttribute('itemID');
		var itemType = $('discounts_div').readAttribute('itemType');		
		var pars = 'pmode=firstload&itemType='+itemType+'&itemID='+itemID;
		var myAjax = new Ajax.Updater('discounts_div', 'mgr.discountsbox.php', {method: 'get', parameters: pars, evalScripts: true, onComplete: function(){ updaterowcolors('.discountsbox_row','#fff','#f8f8f8'); } });
		discountBoxLoaded = 1;
	}
}

// CHECK TO SEE IF ANY OPTION GROUPS EXIST
function countDiscounts(adj)
{
	var numrows = $$('div.discountsbox_row').length;
	//alert(numrows);
	if((numrows-adj) > 0)
	{
		$('discounts_div').show();	
	}
	else
	{
		$('discounts_div').hide();
	}
}

// ADD DISCOUNT RANGE
function addDiscountRange()
{			
	if(demoMode)
		demo_message();
	else
	{				
		countDiscounts(0);
		
		// CREATE NEW
		var numrows = $$('div.discountsbox_row').length;
		var rowname = $$('div.discountsbox_row')[numrows-1].id;	
		
		var itemID = $('discounts_div').readAttribute('itemID');
		var itemType = $('discounts_div').readAttribute('itemType');
		
		var url = 'mgr.discountsbox.php';
		var pars = 'pmode=addnew&itemID='+itemID+'&itemType='+itemType;
		var myAjax = new Ajax.Request( 
			url, 
			{
				method: 'get', 
				parameters: pars,
				evalScripts: true,
				onComplete: function(transport){					
					//alert(transport.responseText);

					transport.responseText.evalScripts();					
					//alert(transport.responseText);
					//eval(transport.responseText);
					//var rowTemplate = new Template(templatedata);	
					//alert(rowname);	
					$(rowname).insert({after: transport.responseText});
					
					// FADE IN ROW THAT WAS JUST ADDED
					rowname = $$('div.discountsbox_row')[numrows].id;							
					Effect.Appear(rowname,{ duration: 0.5, from: 0.0, to: 1.0 });
					setTimeout(function(){ updaterowcolors('.discountsbox_row','#fff','#f8f8f8'); },200);
				}
			});
	}
}

// DELETE DISCOUNT
function deleteDiscount(id)
{
	if(demoMode)
		demo_message();
	else
	{
		if(verify_before_delete)
				message_box(gen_suredelete,"<input type='button' value='"+gen_b_cancel2+"' id='closebutton' class='button' onclick='close_message();' /><input type='button' value='"+gen_b_del+"' id='closebutton' class='button' onclick='doDeleteDiscount(\""+id+"\");close_message();' />",'');
		else
			doDeleteDiscount(id);
	}
}

// DO DELETE
function doDeleteDiscount(id)
{
	var rowname = 'discountRow_'+id;
	//show_loader(rowname);
	
	$('discountbox_delete_'+id).src='images/mgr.loader.gif';

	var pars = 'pmode=delete&deleteID='+id;
	new Ajax.Request('mgr.discountsbox.php', {method: 'get', parameters: pars, onComplete: function() {
		Effect.Fade('discountRow_'+id,{ duration: 0.5 });
		setTimeout(function(){ $(rowname).remove(); updaterowcolors('.discountsbox_row','#fff','#f8f8f8'); countDiscounts(1); },500);					
	} });
}