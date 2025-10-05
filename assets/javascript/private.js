$(function()
{
	/*
	* Account info workboxes
	*/
	$('.accountInfoWorkbox').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage });
		scroll(0,0);
	});
	
	/*
	* Contributors
	*/
	$('.approvalMessage').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrNewAlbum').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrEditAlbum').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrDeleteAlbum').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrDeleteImportMedia').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrUploadMedia').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage });
		scroll(0,0);
	});
	
	$('.contrMailinMedia').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage, mini: true });
		scroll(0,0);
	});
	
	$('.contrImportSelectAll').click(function(event)
	{
		event.preventDefault();
		selectAllCheckboxes('.importFiles');
		checkImportCheckboxes();
	});
	
	$('.contrImportSelectNone').click(function(event)
	{
		event.preventDefault();
		deselectAllCheckboxes('.importFiles');
		checkImportCheckboxes();
	});	
	
	$('#importSelectedButton').click(function(event)
	{
		event.preventDefault();
		//var formData = $('#importFilesForm').serialize();
		var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=import';
		workbox({ page : workboxPage });
		scroll(0,0);
	});
	
	$('.contrMediaDelete').click(function(event)
	{
		event.preventDefault();
		var mediaID = $(this).attr('mediaID');
		// MODIFICA: Usa URL relativo invece di baseURL
		workbox({ page: '/workbox.php?mode=deleteContrMedia&mediaID='+mediaID, mini: true });
	});
	
	$('.contrMediaEdit').click(function(event)
	{
		event.preventDefault();
		var mediaID = $(this).attr('mediaID');
		// MODIFICA: Usa URL relativo invece di baseURL
		workbox({ page: '/workbox.php?mode=editContrMedia&mediaID='+mediaID, mini: false });
	});
	
});


function registerAssignDetailsButtons()
{
	//alert('testing1234');
	$('.closeImportWorkbox').click(function(event)
	{
		if($('#saveMode').val() == 'newUpload')
		{
			$('#contrImportContainer').show();
			loadContrImportWindow();	
		}
		closeWorkbox();
	});
				
	$('.saveContrAssignMediaDetails').click(function(event)
	{	
		//alert($('#gallerySelector').val());
		
		$(this).attr('disabled','disabled');
		
		$('#contrMediaDetailsContainer').hide(); // Hide the details container
		$('#contrImportContainerWB').show(); // Show the import progress container
		
		var importFiles = []; // Create import files array
		
		//alert('test'+$('#gallerySelector').val());
		
		switch($('#saveMode').val())
		{
			case 'newUpload':
				//alert('newUpload');
				
				$('.uploadFiles').each(function(num,elem)
				{	
					importFiles[num] = $(elem).val(); // Get a list of all the file hashes
				});
				
			break;
			case 'import':
				//alert($('.importFiles:checked').serialize()); // Testing
				
				$('.importFiles:checked').each(function(num,elem)
				{	
					importFiles[num] = $(elem).val(); // Get a list of all the file hashes
				});
				
			break;	
		}
		
		//alert($(importFiles).length); // Testing
		
		if($(importFiles).length > 0)
		{
			contrMediaImporter(0,importFiles)
		}
	});
	
	$('#contrMediaImportDetails').click(function(event)
	{
		event.preventDefault();
		$('#contrImportLog').clicktoggle(); //toggle
	});	
}

function contrMediaImporter(arrayNum,importFiles)
{
	//alert(arrayNum); // Testing
	var formData = $('#workboxForm').serialize();
	formData = formData+'&file='+importFiles[arrayNum];
	
	//alert(formData);
	
	if(arrayNum == 0)
	{
		//$('#workbox').addClass('miniWorkbox');
		
		$('#workbox').animate({
			'width': '500px',
			'min-height': '150px',
			'left': '50%',
			'top': '25%',
			'margin-left': '-250px'
		  }, 200,'swing', function() {
			// Animation complete.
		});
	}
	
	if(importFiles.length == arrayNum)
	{
		$('#importSavingMes').html('Complete');
		//$('#contrImportStatusRow p:first').hide();
		$('#loaderContainer p').css('width','144px').html('100%');
		$('.closeWorkbox').removeAttr('disabled');
	}
	else
	{
		//$('#workbox').addClass('miniWorkbox');
		var progress = 144*(arrayNum/importFiles.length);
		var progressPX = Math.round(progress);
		var progressPercentage = Math.round((arrayNum/importFiles.length)*100);
		$('#loaderContainer p').css('width',progressPX+'px').html(progressPercentage+'%');
	}
	
	if(importFiles.length > arrayNum)
	{
		var importFilePath = Base64.decode(importFiles[arrayNum]);
		var importFileName = importFilePath.replace(/^.*[\\\/]/, '');
		
		//dataType: 'json',
		
		$.ajax({
			type: 'POST',
			url: '/actions.php', // MODIFICA: URL relativo invece di baseURL+'/actions.php'
			data: formData,
			dataType: 'json',
			error: function(info,textStatus,errorThrown)
			{
				$('#contrImportStatusRow p').show();				
				$('#contrImportLogList li:first').before('<li><span class="fileName">'+importFileName+'</span> - <span class="error">Error Adding File ('+textStatus+' - '+errorThrown+')</span></li>');							
				arrayNum++;
				contrMediaImporter(arrayNum,importFiles);
				//alert(info);
			},
			success: function(data)
			{	
				//alert(data);
				$('#contrImportStatusRow p').show();
				
				//alert(data);
				
				if(data.errorCode == 0)
				{
					var messageClass = '';
					$('#contrImportLogList li:first').before('<li><span class="fileName">'+data.fileName+'</span> - <span class="'+messageClass+'">'+data.message+'</span></li>');
				}
				else
				{
					$('#contrImportLog').show();					
					var messageClass = 'error';
					$('#contrImportLogList li:first').before('<li><span class="fileName">'+importFileName+'</span> - <span class="'+messageClass+'">'+data.message+'</span></li>');
				}				
				//$('#currentImportFile').html(data.fileName);				
											
				arrayNum++;
				contrMediaImporter(arrayNum,importFiles);
			}
		});
	}
}


/*
* Java File Uploader
*/ 
function getUploader()
{
	return document.jumpLoaderApplet.getUploader();
}

function getUploaderConfig()
{
	return document.jumpLoaderApplet.getUploaderConfig();
}

function getUploadView()
{
	return getMainView().getUploadView();
}

function getMainView()
{
	return getApplet().getMainView();
}

function getApplet()
{
	return document.jumpLoaderApplet;
}

function getViewConfig()
{
	return getApplet().getViewConfig();
}

function finishJavaUpload()
{
	$('#uploadMediaStep1').hide();
	var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=newUpload';
	workbox({ page : workboxPage, skipOverlay : true });	
}

function stopJavaUpload( index )
{
	var error = getUploader().stopUpload();
	//if(error != null) alert(error);
}

function uploaderFileAdded(uploader,file)
{
	$('#startContrUpload').removeAttr('disabled'); // Enable start button	
}

function uploaderFileStatusChanged(uploader,file)
{ 
	var status = file.getStatus(); 
	if(status == 2)
	{ 
		if((file.getIndex()+1) == uploader.getFileCount())
		{
			finishJavaUpload();
		}
	} 
}

function uploaderFileRemoved(uploader,file)
{
	if(uploader.getFileCount() == 0)
		$('#startContrUpload').attr('disabled','disabled'); // Disable start button
}

function loadContrImportWindow()
{	
	// Load contributor import list				
	$.ajax({
		type: 'GET',
		url: 'contributor.import.list.php',
		data: '',
		success: function(data)
		{					
			$('#contrImportListContainer').removeClass('importWindowLoader');
			$('#contrImportListContainer').html(data);
			activateImportCheckboxes();
			activateImportThumbs();
			checkImportCheckboxes();				
		}
	});	
}

function activateImportThumbs()
{
	$('.importListImgContainer img').click(function(event)
	{
		$(this).closest('.contrImportListItems').children('input[type="checkbox"]').attr('checked','checked');
		checkImportCheckboxes();
	});	
}

function activateImportCheckboxes()
{
	$('.importFiles').click(function(event)
	{
		checkImportCheckboxes();
	});	
}

function checkImportCheckboxes()
{
	// alert($('.importFiles:checked').length); // Testing	
	if($('.importFiles:checked').length > 0)
		$('#importSelectedButton').show();
	else
		$('#importSelectedButton').hide();	
}

function doDeleteImportMedia()
{
	//alert($('#importFilesForm').serialize()); // Testing
	var formData = $('#importFilesForm').serialize();
	$('#contrImportListContainer').addClass('importWindowLoader');
	$('#contrImportListContainer').html('');
	
	$.ajax({
		type: 'POST',
		url: '/actions.php', // MODIFICA: URL relativo invece di baseURL+'/actions.php'
		data: formData,
		dataType: 'json',
		success: function(data)
		{	
			loadContrImportWindow();
			//alert(data.filesPassed+'-test');
		}
	});
}

/*
* Include flash uploader
*/
function sampleUploader(fileInputID,secID)
{
	if($(fileInputID))
	{	
		var mediaID = $('#mediaID').val();
		
		switch(fileInputID)
		{
			default:
			case "#thumbUploader":
				var uploadPage = 'actions.php?action=uploadThumb&mediaID='+mediaID;
				var fileExt = '*.jpg;';
			break;
			case "#videoUploader":
				var uploadPage = 'actions.php?action=uploadVideoPreview&mediaID='+mediaID;
				var fileExt = '*.flv;*.mp4;';
			break;
			case "#dspUploader":
				var uploadPage = 'actions.php?action=uploadDSP&mediaID='+mediaID+'&dspID='+secID;
				var fileExt = '*.*';
			break;
			case "#propReleaseUploader":
				var uploadPage = 'actions.php?action=uploadPropRelease&mediaID='+mediaID;
				var fileExt = '*.*';
			break;
			case "#modelReleaseUploader":
				var uploadPage = 'actions.php?action=uploadModelRelease&mediaID='+mediaID;
				var fileExt = '*.*';
			break;					
		}
		
		// MODIFICA: Usa URL relativo per uploaderPath
		var uploaderPath = '/'+uploadPage;
		var buttonText = $(fileInputID).attr('buttonText');
		
		$(fileInputID).uploadify({
			'formData'     : {
				'securityTimestamp' : $('#securityTimestamp').val(),
				'securityToken'     : $('#securityToken').val()
			},			
			'height'    	: 14,
			'swf'       	: '/assets/uploadify/uploadify.swf', // MODIFICA: URL relativo
			'uploader'  	: uploaderPath,
			'multi'			: false,
			'buttonText'	: buttonText,
			'fileTypeDesc'	: 'Images',			
			'fileTypeExts'	: fileExt,
			'fileSizeLimit'	: $('#maxUploadSize').val(),
			'width'     	: 146,
			'preventCaching': true,
			'onQueueComplete' : function(queueData)
			{
				switch(fileInputID)
				{
					case "#thumbUploader":
						var newSRC_a = $('#contrMediaThumbnail').attr('src'); // Replace thumbs
						$('#contrMediaThumbnail').attr('src',newSRC_a);
						var newSRC_b = $('#thumb'+mediaID).find('img').attr('src');
						$('#thumb'+mediaID).find('img').attr('src',newSRC_b);
						$('#thumbUploaderContainer').hide();
					break;
					case "#videoUploader":					
						$('#videoUploaderContainer').hide();
					break;
					case "#dspUploader":					
						$('#detachButton-'+secID).show();					
						$('#attachButton-'+secID).hide();						
						$('#dspUploader').uploadify('destroy');
						$('#attachFileUploaderContainer').hide();
					break;
					case "#propReleaseUploader":
						$('#propRelease').attr('checked','checked');
					break;
					case "#modelReleaseUploader":
						$('#modelRelease').attr('checked','checked');
					break;	
				}
			}
		});
	}
}