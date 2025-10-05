$(function()
{
	assignDownloadButtonActions();
});

function assignDownloadButtonActions()
{				
	$('.orderDownloadButton').unbind('click');
	
	$('.orderDownloadButton').click(function()
	{
		var downloadableStatus = $(this).attr('downloadableStatus');
		
		//alert(downloadableStatus);
		
		switch(downloadableStatus)
		{
			case '1':
				//var gotoLink = baseURL+"/download.php" + "?mediaID=" + $(this).attr('mediaID') + "&profileID=" + $(this).attr('profileID') + "&downloadType=order&downloadTypeID=" + $('#orderID').val() + "&invoiceItemID=" + $(this).attr('invoiceItemID');
				var gotoLink = baseURL+"/download.php" + "?dlKey=" + $(this).attr('key');
				//alert(gotoLink);
				goto(gotoLink); // Initiate the download
			break;
			case '2':
				//alert('Expired');
				workbox({ 'page':'workbox.php?mode=downloadExpired', 'mini':true });
			break;
			case '3':
				//alert('Downloads Exceeded');
				workbox({ 'page':'workbox.php?mode=downloadsExceeded', 'mini':true });
			break;
			case '4':
				//alert('Not available for download');
				var key = $(this).attr('key');
				workbox({ 'page':'workbox.php?mode=downloadNotAvailable&dlKey=' + key, 'mini':true });
			break;
			case '5':
				var invoiceItemID = $(this).attr('invoiceItemID');
				
				$('#collectionList'+invoiceItemID).show();
				$(this).attr('disabled','disabled');
				$('#collectionList'+invoiceItemID).addClass('optionsBoxLoader');
				$.ajax({
					type: 'POST',
					url: baseURL+'/collection.download.list.php',
					data: "dlKey=" + $(this).attr('key'),
					success: function(data)
					{	
						$('#collectionList'+invoiceItemID).html(data);
					}
				});
			break;
			case '0':
			default:
				//alert('Not Approved');
				workbox({ 'page':'workbox.php?mode=downloadNotApproved', 'mini':true });
			break;
		}
		
	});
}