$(function()
{	
	/*
	* On load do these function
	*/
	updateOptionsPrice(); // Update prices
	updateOptionsCredits(); // Update credits	
	correctRadios(); // Correct radio buttons
	
	/*
	* packagesInCartDD
	*/
	$('#packagesInCartDD li').click(function(event)
	{
		workbox({ 'page' : $(this).attr('href'), 'skipOverlay' : true });
	});
	
	/*
	* Additional product shots
	*/
	$('#additionalShots img').click(function(event)
	{
		event.preventDefault();
		var myImage = new Image();
		myImage.src = $(this).parent('a').attr('href');
		$('#mainShotContainer').html('');
		
		$(myImage).load(function()
		{
			if(fadeInThumbnails.status){ $('#mainShotContainer').css('opacity',0); }
			$('#mainShotContainer').html(myImage).stop().animate({ opacity: 1.0 },fadeInThumbnails.speed);
		});
	})
	.hover(
		function()
		{
			$(this).stop().animate({ opacity: 1.0 },dimThumbsOnHover.speed);
		},
		function()
		{
			$(this).stop().animate({ opacity: 0.3 },dimThumbsOnHover.speed);
		}
	);
	
	if(fadeInThumbnails.status){ $('#mainShotContainer img, #additionalShots img').css('opacity',0); }		
	$('#mainShotContainer img').load(function()
	{
		if(fadeInThumbnails.status){ $(this).stop().animate({ opacity: 1.0 },fadeInThumbnails.speed); } else { $(this).show(); } // Fade in
	});
	
	$('#additionalShots img').load(function()
	{
		if(fadeInThumbnails.status){ $(this).stop().animate({ opacity: 0.3 },fadeInThumbnails.speed); } else { $(this).show(); } // Fade in
	});
	
	/*
	* Update the price and credits when the form is changed
	*/
	$('#workboxItemForm').change(function()
	{
		$('.formErrorMessage').remove(); // Remove the error messages from previous submits
		$('*').removeClass('formError'); // Remove the formError class from any errors previously
		
		updateOptionsPrice();
		updateOptionsCredits();
	});
	
	/*
	* Toggle the options div show/hide
	*/
	$('.optionsToggle').clicktoggle(
		function(event)
		{
			event.preventDefault();
			var control = $(this).attr('optionsGroup');
			$(this).html('&ndash;'); 
			$('#'+control).show();
		},
		function(event)
		{
			event.preventDefault();
			var control = $(this).attr('optionsGroup');
			$(this).html('+');
			$('#'+control).hide();	
		}
	);
});