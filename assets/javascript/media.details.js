$(function()
{
	$('#mediaPurchaseTabsContainer li:first').addClass('mediaPurchaseTabsSelected'); // Set the first tab as selected
	$('.mediaPurchaseContainers:first').show(); // Set the first purchase container visible
	
	$('#mediaPurchaseTabsContainer li').click(function(event)
	{	
		$('#mediaPurchaseTabsContainer li').removeClass('mediaPurchaseTabsSelected');
		$(this).addClass('mediaPurchaseTabsSelected');				
		$('.mediaPurchaseContainers').hide();
		$('#'+$(this).attr('container')).show();
	});
	
	/*
	* Load media comments
	*/
	if($('#mediaComments'))
	{				
		loadComments();
	}
	
	/*
	* Load media comments
	*/
	if($('#mediaTags'))
	{				
		loadTags();
	}
	
	/*
	* Load similar media
	*/
	if($('#mediaSimilarPhotos'))
	{				
		$('#mediaSimilarPhotos').addClass('mediaDetailsBoxesLoader');
		
		$.get(baseURL+'/similar.media.php',{ mediaID: $('#mediaSimilarPhotos').attr('mediaID'), galleryID: $('#mediaSimilarPhotos').attr('galleryID'), galleryMode: $('#mediaSimilarPhotos').attr('galleryMode') },function(data)
		{
			$('#mediaSimilarPhotos').removeClass('mediaDetailsBoxesLoader');
			$('#mediaSimilarPhotos').html(data);
		});
	}
	
	/*
	* Make color swatches clickable
	*/
	$('.colorSwatch').click(function()
	{
		var hexColor = $(this).attr('title').replace('#','');
		goto(baseURL+'/search.php?clearSearch=true&hex='+hexColor);
	});
	
	/*
	* Back/next/prev buttons
	*/
	$('#backButton, #nextButton, #prevButton').click(function()
	{
		var url = $(this).attr('href');
		goto(url);
	});
	
	
	registerDG({ id : 'mediaExtraInfoDG' }); // Register the data group mediaExtraInfoDG
});

/*
* Load media comments function
*/
function loadComments()
{
	$('#mediaComments').addClass('mediaDetailsBoxesLoader');
		
	$.get(baseURL+'/comments.php',{ mediaID: $('#mediaComments').attr('mediaID') },function(data)
	{
		$('#mediaComments').removeClass('mediaDetailsBoxesLoader');
		$('#mediaComments').html(data);
		
		var mediaID = $('#mediaComments').attr('mediaID');
		
		$('#showMoreComments').unbind('click');
		$('#newCommentForm').unbind('submit');
		
		$('#showMoreComments').click(function(event)
		{
			event.preventDefault();
			
			$('#mediaComments').html('');
			$('#mediaComments').addClass('mediaDetailsBoxesLoader');
			
			$.get('comments.php',{ mediaID: mediaID, limit : '10000' },function(data)
			{
				$('#mediaComments').removeClass('mediaDetailsBoxesLoader');
				$('#mediaComments').html(data);
			});	
		});
		
		/*
		* Submit comment
		*/
		$('#newCommentForm').submit(function(event)
		{
			event.preventDefault();
			var formData = $('#newCommentForm').serialize();
			formData = formData+'&mediaID='+mediaID; // Add media id
			
			$('#mediaComments').html('');
			
			$.ajax({
				type: 'POST',
				url: baseURL+'/actions.php',
				data: formData,
				dataType: 'json',
				success: function(data)
				{	
					$('#newCommentMessage').html(data.errorMessage);							
					$('#newComment').val('');
					
					loadComments();
												
					switch(data.errorCode)
					{
						default:									
						break;
						case "newCommentFailed":									
						break;
					}
				}
			});
		});
	});	
}

/*
* Load media comments function
*/
function loadTags()
{
	$('#mediaTags').addClass('mediaDetailsBoxesLoader');
		
	$.get(baseURL+'/tags.php',{ mediaID: $('#mediaTags').attr('mediaID') },function(data)
	{
		$('#mediaTags').removeClass('mediaDetailsBoxesLoader');
		$('#mediaTags').html(data);
		
		var mediaID = $('#mediaTags').attr('mediaID');

		$('#newTagForm').unbind('submit');
		
		/*
		* Submit tag
		*/
		$('#newTagForm').submit(function(event)
		{
			event.preventDefault();
			var formData = $('#newTagForm').serialize();
			formData = formData+'&mediaID='+mediaID; // Add media id
			
			$('#mediaTags').html('');
			
			$.ajax({
				type: 'POST',
				url: baseURL+'/actions.php',
				data: formData,
				dataType: 'json',
				success: function(data)
				{	
					$('#newTagMessage').html(data.errorMessage);							
					$('#newTag').val('');
					
					loadTags();
												
					switch(data.errorCode)
					{
						default:									
						break;
						case "newTagFailed":									
						break;
					}
				}
			});
		});
	});	
}