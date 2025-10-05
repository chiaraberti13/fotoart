$(function()
{	
	// Prevent someone from hitting enter and submitting the for to the cart
	$('#workboxItemForm').submit(function(event)
	{
		//event.preventDefault();
	});
	
	$('#downloadFromSub').change(function()
	{
		if($('#downloadFromSub').val() != '0')
			$('#workboxDownload').removeAttr("disabled");
		else
			$('#workboxDownload').attr("disabled", "disabled"); // Disable button to prevent duplicate submissions
	});
	
	$('.subOptions').click(function()
	{
		//$('.subOptions input[type="radio"]').removeAttr("checked");
		
		var msub_id = $(this).attr("msub_id");
		
		//alert(msub_id);
		
		$('#sub'+msub_id).attr("checked", "checked");
		$('#workboxDownload').removeAttr("disabled");			   
	});
	
	$('#workboxDownload').click(function()
	{
		// Check to see if this can be instantly downloaded or not - if so then go to the download.php file
		if($('#instantDownloadAvailable').val() == 1)
		{
			var downloadType = $('#downloadType').val();
			
			if(downloadType == 'sub')
				var key = $('input[name="subscription"]:checked').attr('key');
			else
				var key = $(this).attr('key');
			
			$(this).attr("disabled", "disabled"); // Disable download button
			
			var gotoLink = baseURL+"/download.php" + "?dlKey=" + key;
			
			/*
			
			var gotoLink = baseURL+"/download.php" + "?mediaID=" + $('#mediaID').val() + "&profileID=" + $('#profileID').val() + "&downloadType=" + downloadType;
			
			if(downloadType == 'sub')
				gotoLink+= "&downloadTypeID=" + $('input[name="subscription"]:checked').val(); // If this is a sub then add the subID to the link
			
			$(this).fadeOut(); // Hide button
			$('#prevDownloadedMessage').fadeOut(); // Hide Message
			*/
			//alert(gotoLink);
			goto(gotoLink); // Initiate the download
			
			//closeWorkbox(); // Not sure if I want to close the workbox after download
		}
		// If not then put output notice
		else
		{
			$(this).attr('disabled','disabled'); // Disable button to prevent duplicate submissions 				
			submitRequestDownloadForm();
		}
	});
	
	// Request a download button
	$('#workboxRequestDownload').click(function()
	{
		if($('#loggedIn').val() == 1)
		{
			$(this).attr('disabled','disabled');  // Disable button to prevent duplicate submissions
			submitRequestDownloadForm();
		}
		else
		{
			$('#requestDownloadEmail').slideDown(100); // Slide open email field			
			$(this).unbind('click'); // Unbind previous click			
			$(this).click(function()
			{
				$(this).attr('disabled','disabled');  // Disable button to prevent duplicate submissions
				submitRequestDownloadForm();
			}); // Add new click			
			$(this).val($(this).attr('altText')); // Change button text
		}
	});
	
	// Clear request download email form
	$('#requestDownloadEmail').click(function()
	{
		$(this).val('');
	});
	
	// Submit form
	$('#workboxSubmit').click(function()
	{	
		$(this).attr('disabled','disabled');  // Disable button to prevent duplicate submissions
		
		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php',
			data: $("#workboxItemForm").serialize(),
			dataType: 'json',
			error: function(jqXHR,errorType,errorThrown)
			{
				alert(errorType+' - '+jqXHR.responseText);
			},
			success: function(data)
			{	
				$('#workboxSubmit').hide();
				$('#contactForPricing').hide();
				$('#contectForPricingSuccess').show();
			}
		});
	});
	
	// Submit the request for a download form
	function submitRequestDownloadForm()
	{
		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php',
			data: $("#workboxItemForm").serialize(),
			dataType: 'json',
			success: function(data)
			{	
				$('#workboxDownload').hide();
				$('#requestDownloadContainer').hide();
				$('#requestDownloadSuccess').show();
			}
		});
	}
			
});