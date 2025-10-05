$(function()
{
	$('.payButton').click(function(event)
	{
		event.preventDefault();
		var billID = $(this).attr('billID');
		workbox({ page : 'workbox.php?mode=billPayment&billID='+billID, mini: false });
	});
});