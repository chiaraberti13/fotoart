$(function()
{
	$('#memberEmail').focus();
	
	$('#forgotPassword').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
});