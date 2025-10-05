$(function()
{
	$('#country').change(function()
	{
		var countryID = $(this).val();
		getStateList(countryID);
	});
	
	
	/*
	* Include flash uploader for avatar uploading
	*/
	if($('#avatarUploader'))
	{
		//alert(baseURL);
		/*
		$('#avatarUploader').uploadify(
		{
			'uploader'  : baseURL+'/assets/uploadify/uploadify.swf',
			'script'    : baseURL+'/actions.php?action=updateAccountInfo%26mode=avatarUpload%26umem_id='+$('#umem_id').val()+'%26ms_id='+$('#membership_id').val(),
			'cancelImg' : baseURL+'/assets/uploadify/cancel.png',
			'folder'    : './assets/avatars',
			'auto'      : true,
			'wmode'		: 'transparent',
			'hideButton': true,
			'multi'		: false,
			'fileDesc'	: 'Images',
			'fileExt'	: $('#fileExt').val(),
			'sizeLimit'	: $('#maxAvatarFileSize').val(),
			'onAllComplete' : function(event,data)
			{
				//alert(data.filesUploaded + ' files uploaded successfully!');
				$('#editorAvatar').attr('src',baseURL+'/avatar.php?size=150&memID='+$('#mem_id').val());
				$('#memNavAvatar').attr('src',baseURL+'/avatar.php?size=100&memID='+$('#mem_id').val());
				$('#avatarDeleteDiv').show();
			}
		});
		*/
		var uploaderPath = baseURL+'/actions.php?action=updateAccountInfo&mode=avatarUpload&umem_id='+$('#umem_id').val()+'&ms_id='+$('#membership_id').val();
		var buttonText = $('#avatarUploader').attr('buttonText');
		
		$("#avatarUploader").uploadify({
			'formData'     : {
				'securityTimestamp' : $('#securityTimestamp').val(),
				'securityToken'     : $('#securityToken').val()
			},	
			'height'    	: 14,
			'swf'       	: baseURL+'/assets/uploadify/uploadify.swf',
			'uploader'  	: uploaderPath,
			'multi'			: false,
			'buttonText'	: buttonText,
			'fileTypeDesc'	: 'Images',			
			'fileTypeExts'	: $('#fileExt').val(),
			'fileSizeLimit'	: $('#maxAvatarFileSize').val(),			
			'width'     	: 142,
			'preventCaching': true,
			'onQueueComplete' : function(queueData)
			{
				//alert('test');
				//alert(data.filesUploaded + ' files uploaded successfully!');
				$('#editorAvatar').attr('src',baseURL+'/avatar.php?size=150&memID='+$('#mem_id').val());
				$('#memNavAvatar').attr('src',baseURL+'/avatar.php?size=100&memID='+$('#mem_id').val());
				$('#avatarDeleteDiv').show();
			}
		});

	}
});