$(function()
{	
	$('#workboxAddToCart').attr('disabled','disabled');
	//$('#workboxAddToCart').hide();
	
	// Check for new options when an option group is selected
	function initRMDropdowns()
	{
		$('.rmContainer select').unbind(); // Unbind any existing change events
		
		$('.rmContainer select').change(function()
		{
			var optionID = $(this).val();
			var licID = $(this).attr('licID');
			var parentRow = $(this).closest('tr');
			var parentTable = $(this).closest('table');
			var newRow;
			
			if(optionID)
			{
				$(parentRow).nextAll('tr').remove(); // Remove any rows after the current one

				$.ajax({
					type: 'GET',
					url: baseURL+'/rights.managed.data.php',
					data: {"pmode" : "getRMGroups", "optionID" : optionID, "licID" : licID},
					dataType: 'json',
					error: function(jqXHR,errorType,errorThrown)
					{
						alert(errorType+' - '+jqXHR.responseText+' - '+errorThrown);
					},
					success: function(data)
					{
						if(data && data.rmGroup != 0) // Make sure one is found before loading a new row
						{
							//alert(data.rmGroup);
							
							newRow = "<tr><td>"+data.rmGroup.name+"</td><td>";
							newRow += "<select name='rmGroup["+data.rmGroup.id+"]' licID='"+licID+"'>";
							newRow += "<option value='0'></option>";
							
							if(data.rmOptions) // Check for options
							{
								$.each(data.rmOptions,function(key,rmOption)
								{
									//stateOptions += '<option value='+key+'>'+state+'</option>';	
									newRow += "<option value='"+rmOption.id+"' price='"+rmOption.price+"' credits='"+rmOption.credits+"' priceMod='"+rmOption.priceMod+"'  displayPrice=''>"+rmOption.name+"</option>";			 
								});	
							}					
							
							newRow += "</select>";								
							newRow += "</td></tr>";
							
							$(newRow).insertAfter(parentRow);
							
						}
						
						initRMDropdowns(); // Register new dropdowns							
						getRMTotals(); // Get totals
						checkRMForm(); // Check form												
					}
				});
			}
		 
		});
	}
	
	initRMDropdowns();	
	getRMTotals();	
});


function getRMTotals()
{
	var runningTotal = parseFloat($('#rmBasePrice').val());
	var runningCreditsTotal = parseFloat($('#rmBaseCredits').val());

	$('#rmPricingCalculator select').each(function(selectIndex,selectElem)
	{
		var selectedOption = $(selectElem).find('option:selected');
		var priceMod = $(selectedOption).attr('priceMod');
		var price = $(selectedOption).attr('price');
		var credits = $(selectedOption).attr('credits');
		
		if(priceMod)
		{	
			if(price) price = parseFloat(price);
			if(credits) credits = parseFloat(credits);
			
			switch(priceMod)
			{
				case '+':
					runningTotal = runningTotal + price;
					runningCreditsTotal = runningCreditsTotal + credits;
				break;
				case '-':
					runningTotal = runningTotal - price;
					runningCreditsTotal = runningCreditsTotal - credits;
				break;
				case 'x':
					runningTotal = runningTotal * price;
					runningCreditsTotal = runningCreditsTotal * credits;
				break;
			}
		}		
	});
	
	
	
	submitRMTotals(runningTotal,runningCreditsTotal); // Submit totals for conversion or updates	
	$('#workboxItemCredits').html(runningCreditsTotal);
}

function getRMTotalsOLD()
{
	var runningTotal = 0;
	var runningCreditsTotal = 0;
	var rmGroupTotal;
	var rmGroupCreditsTotal;
	
	$('.rmContainer').each(function(index,elem)
	{
		rmGroupTotal = parseFloat($('#rmBasePrice').val());
		rmGroupCreditsTotal = parseFloat($('#rmBaseCredits').val());
		
		$(elem).find('select').each(function(selectIndex,selectElem)
		{
			var selectedOption = $(selectElem).find('option:selected');
			var priceMod = $(selectedOption).attr('priceMod');
			var price = $(selectedOption).attr('price');
			var credits = $(selectedOption).attr('credits');
			
			if(priceMod)
			{	
				if(price) price = parseFloat(price);
				if(credits) credits = parseFloat(credits);
				
				switch(priceMod)
				{
					case '+':
						rmGroupTotal = rmGroupTotal + price;
						rmGroupCreditsTotal = rmGroupCreditsTotal + credits;
					break;
					case '-':
						rmGroupTotal = rmGroupTotal - price;
						rmGroupCreditsTotal = rmGroupCreditsTotal - credits;
					break;
					case 'x':
						rmGroupTotal = rmGroupTotal * price;
						rmGroupCreditsTotal = rmGroupCreditsTotal * credits;
					break;
				}
			}
			
		});
		
		//$('#rmTotal').html(rmGroupTotal);
		
		runningTotal = runningTotal + rmGroupTotal;
		runningCreditsTotal = runningCreditsTotal + rmGroupCreditsTotal;
		
	});
	
	//$('#rmTotal').html(runningTotal);
	
	submitRMTotals(runningTotal,runningCreditsTotal); // Submit totals for conversion or updates	
	$('#workboxItemCredits').html(runningCreditsTotal);
}

function submitRMTotals(price,credits)
{
	//alert(credits);	
	$.ajax({
		type: 'GET',
		url: baseURL+'/rights.managed.data.php',
		data: {"pmode" : "getRMTotals", "price" : price, "credits" : credits},
		dataType: 'json',
		success: function(data)
		{					
			//alert('worked');
			if(data) // Make sure one is found before loading a new row
			{
				$('#rmPriceEnc').val(data.price.priceEnc);
				$('#rmCreditsEnc').val(data.creditsEnc);
				$('#workboxItemPrice').html(data.price.display);
				//alert(data.creditsEnc);				
			}												
		}
	});	
}

function checkRMForm()
{
	$('#workboxAddToCart').removeAttr('disabled');
	
	if($('#rmPriceEnc').val() != '' && $('#rmCreditsEnc').val() != '')
	{
		$('#rmPricingCalculator select').each(function(index,elem)
		{
			if($(elem).val() == 0)
				$('#workboxAddToCart').attr('disabled','disabled');
		});
	}
}