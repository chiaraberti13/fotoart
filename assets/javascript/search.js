$(function()
{
	$('#colorpickerHolder').ColorPicker({
		color: 'FFFFFF',
		onShow: function (colpkr)
		{
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr)
		{
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb)
		{
			$('#red').val(rgb.r);
			$('#green').val(rgb.g);
			$('#blue').val(rgb.b);
			$('#hex').val(hex);
			$('#colorpickerHolder div').css('backgroundColor', '#' + hex);
		}
	});
	
	$('#searchButton').click(function()
	{		
		if($('#searchPhrase2').val() == enterKeywords)
			$('#searchPhrase2').val(''); // Clear searchPhrase box if it is the default enter keywords
		
		//alert(requireSearchKeyword);
		
		if(requireSearchKeyword)
		{
			if($('#keywordsExist').val() == 1 || $('#searchPhrase2').val() != '')
				$('#searchForm').submit();
			else
				workbox({ page: "workbox.php?mode=missingSearchTerms", mini: true });
		}
		else
		{
			$('#searchForm').submit();
		}
		
	});
	
	$('#searchSortBy, #searchSortType').change(function()
	{
		//alert('test');
		$('#searchForm').submit();
	});
		
	$('#dateRangeSearchCB').click(function()
	{
		if($(this).is(":checked"))
		{
			$('#dateRangeSearch').val('on');
			$('.searchDate').show();
		}
		else
		{
			$('#dateRangeSearch').val('off');
			$('.searchDate').hide();
		}
	});
	
	if(searchFormHex)
	{			
		$('#colorpickerHolder').ColorPickerSetColor(searchFormHex);
		$('#colorpickerHolder div').css('backgroundColor', '#'+searchFormHex);
	}
});