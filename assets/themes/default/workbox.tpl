<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
{if $access == 'private'}<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.private.js"></script>{/if}
<form class="cleanForm" method="post" action="{$baseURL}/actions.php" id="workboxForm" enctype="multipart/form-data">
<input type="hidden" value="{$mode}" name="action" id="action">
<input type="hidden" value="{$mode}" name="mode" id="mode">
<input type="hidden" value="{$member.umem_id}" name="umem_id" id="umem_id">
<input type="hidden" value="{$member.mem_id}" name="mem_id" id="mem_id">
<input type="hidden" value="{$securityTimestamp}" name="securityTimestamp" id="securityTimestamp">
<input type="hidden" value="{$securityToken}" name="securityToken" id="securityToken">

<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<!--<img src="{$imgPath}/close.button.png" id="closeWorkbox">-->
{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	
	{if $mode == 'forgotPassword'}
		<div id="editWorkbox">
			<h1>{$lang.forgotPassword}</h1>
			<p class="notice" style="display: none;" id="emailPasswordSent">{$lang.passwordSent}</p>
			<p class="notice" style="display: none;" id="emailPasswordFailed">{$lang.passwordFailed}</p>
			
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.yourEmail}:</div>
					<div class="divTableCell"><input type="text" name="form[toEmail]" value="" id="toEmail" style="width: 250px;" require="require" errorMessage="{$lang.required}" /></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox" /> <input type="button" value="{$lang.submit}" id="saveWorkboxForm" /></div>
	{/if}
	
	{if $mode == 'deleteLightbox'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.lbDeleteVerify}</p>
			<div class="workboxActionButtons"><a href="{$lightboxID}" class="buttonLink" id="deleteLightboxYes">{$lang.yes}</a> <a href="#" class="buttonLink closeWorkbox">{$lang.no}</a></div>
		</div>
	{/if}
	
	{if $mode == 'deleteContrMedia'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.cmDeleteVerify}</p>
			<div class="workboxActionButtons"><a href="{$mediaID}" class="buttonLink" id="deleteContrMediaYes">{$lang.yes}</a> <a href="#" class="buttonLink closeWorkbox">{$lang.no}</a></div>
		</div>
	{/if}
	
	{if $mode == 'newLightbox' or $mode == 'editLightbox'}
		<div id="editWorkbox">
			<script>$('#lightboxName').focus();</script>
			<input type="hidden" name="ulightbox_id" value="{$lightbox.ulightbox_id}">
			<img src="{$imgPath}/close.button.png" id="closeWorkbox">
			<h1>{if $mode == 'editLightbox'}{$lang.editLightbox}{else}{$lang.createNewLightbox}{/if}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.name}:</div>
					<div class="divTableCell"><input type="text" name="lightboxName" value="{$lightbox.name}" id="lightboxName" require="require" errorMessage="{$lang.required}" style="width: 250px;"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.mediaLabelDesc}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="lightboxNotes" id="lightboxNotes">{$lightbox.notes}</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{if $mode == 'newLightbox'}{$lang.create}{else}{$lang.save}{/if}" id="saveWorkboxForm"></div>
	{/if}
	
	{if $mode == 'addToLightbox'}
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="{$mediaID}">
			<h1>{$lang.addToLightbox}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.lightboxes}:</div>
					<div class="divTableCell">
					<select style="width: 258px;" name="lightbox" id="lightboxDropdown">
						<option value="0">{$lang.newLightbox}...</option>
						{html_options options=$lightboxes selected=$selectedLightbox}
					</select>
					</div>
				</div>
				<div class="divTableRow newLightboxRow" {if $selectedLightbox}style="display: none;"{/if}>
					<div class="divTableCell formFieldLabel">{$lang.name}:</div>
					<div class="divTableCell"><input type="text" name="lightboxName" value="{$lightbox.name}" id="lightboxName" errorMessage1="{$lang.required}" style="width: 200px;"></div><!--require="require"-->
				</div>
				<div class="divTableRow newLightboxRow" {if $selectedLightbox}style="display: none;"{/if}>
					<div class="divTableCell formFieldLabel">{$lang.mediaLabelDesc}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="lightboxNotes" id="lightboxNotes">{$lightbox.notes}</textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"></div>
					<div class="divTableCell" style="text-align: right"><a href="#" class="colorLink" id="addNotesLink">{$lang.addNotes}</a></div>
				</div>
				<div class="divTableRow" style="display: none;" id="mediaNotesRow">
					<div class="divTableCell formFieldLabel">{$lang.notes}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="mediaNotes" id="mediaNotes">{$lightbox.notes}</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{$lang.add}" id="saveWorkboxForm"></div>
	{/if}
	
	{if $mode == 'editLightboxItem'}
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="{$mediaID}">
			<input type="hidden" name="lightboxItemID" id="lightboxItemID" value="{$lightboxItemID}">
			<h1>{$lang.editLightboxItem}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.notes}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="mediaNotes" id="mediaNotes">{$lightboxItem.notes}</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{$lang.removeFromLightbox}" mediaID="{$mediaID}" id="removeItemFromLightbox"> <input type="button" value="{$lang.save}" id="saveWorkboxForm"></div>
	{/if}
	
	{if $mode == 'assignPackage'}
		<div id="editWorkbox" style="padding-right: 10px; margin-bottom: 40px;">
			<input type="hidden" name="useMediaID" value="{$useMediaID}" id="useMediaID">
			<h1>{$lang.assignToPackage}</h1>
			{if $notice}
				<p style="margin-top: 15px;"><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.mediaNotElidgiblePack}</p>
			{/if}
			
			<div class="assignToPackageListContainer">
				{if $cartPackageRows}
					<h3>{$lang.packagesInCart}:</h3>
					<div class="divTable assignToPackageList">
						{foreach $cartPackages as $cartItemID => $package}
							<div class="divTableRow" usePackageID="{$package.usePackageID}" cartEditID="{$cartItemID}">
								<div class="divTableCell" style="vertical-align: top">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=75 crop=50}">{/if}</div>
								<div class="divTableCell" style="width: 100%;">
									<h2>{$package.name}</h2>
									<div class="packageFilledContainer">
										<div class="packageFilledBar"><p style="width: {$package.package_media_percentage}%"></p></div>
										<!--{$cartItem.package_media_filled}/{$cartItem.package_media_needed} = --><strong>{$package.package_media_remaining}</strong> {$lang.leftToFill}
									</div>
									<p class="purchaseListDescription">{$package.description|truncate:200}</p>
								</div>
							</div>
						{/foreach}
					</div>
				{/if}
				
				{if $newPackageRows}
					<h3>{$lang.startNewPackage}:</h3>				
					<div class="divTable assignToPackageList">
						{foreach $newPackages as $package}
							<div class="divTableRow" usePackageID="{$package.usePackageID}" cartEditID="0">
								<div class="divTableCell" style="vertical-align: top">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=75 crop=50}">{/if}</div>
								<div class="divTableCell" style="width: 100%;">
									<h2>{$package.name}</h2>
									<p class="purchaseListDescription">{$package.description|truncate:200}</p>
									<p class="purchaseListPrice">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $package.credits} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
								</div>
							</div>
						{/foreach}
					</div>
				{/if}
			</div>
				
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
	{/if}
	
	{if $mode == 'emailToFriend'}
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="{$mediaID}">
			<h1>{$lang.emailToFriend}</h1>
			<p class="notice" style="display: none;" id="emailSentNotice">{$lang.emailToFriendSent}</p>
			<div class="divTable">
				{if !$loggedIn}
					<div class="divTableRow">
						<div class="divTableCell formFieldLabel">{$lang.yourName}:</div>
						<div class="divTableCell"><input type="text" style="width: 250px;" name="form[fromName]" id="fromName" require="require" errorMessage="{$lang.required}"></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell formFieldLabel">{$lang.yourEmail}:</div>
						<div class="divTableCell"><input type="text" style="width: 250px;" name="form[fromEmail]" id="fromEmail" require="require" errorMessage="{$lang.required}"></div>
					</div>
				{/if}
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.emailTo}:</div>
					<div class="divTableCell"><input type="text" style="width: 250px;" name="form[toEmail]" id="toEmail" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.message}:</div>
					<div class="divTableCell"><textarea style="height: 100px; width: 250px;" name="form[message]" id="emailMessage"></textarea></div>
				</div>
			</div>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{$lang.send}" id="saveWorkboxForm"></div>
		</div>
	{/if}
	
	{if $mode == 'creditsWarning'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.creditsWarning}</p>
			<div class="workboxActionButtons"><!--<input type="button" value="{$lang.cancel}" class="closeWorkbox"> --><input type="button" value="{$lang.purchaseCredits}" href="featured.php?mode=credits" class="goToButton"> {if !$loggedIn}<input type="button" value="{$lang.loginCaps}" href="login.php" class="goToButton">{/if}</div>
		</div>
	{/if}
	
	{if $mode == 'subtotalWarning'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.subtotalWarning} {$subTotalMin.display}</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	
	{if $mode == 'createOrLogin'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.haveAccountQuestion}</h1>
			<p class="dimMessage" style="line-height: 2;">
				<a href="login.php?jumpTo=cart">{$lang.yesLogin}</a><br>
				<a href="create.account.php?jumpTo=cart">{$lang.noCreateAccount}</a>
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	
	{if $mode == 'billPayment'}
		<script>
			$(function()
			{
				$('#workboxForm li:first').addClass('paymentGatewaySelected'); // Select the first payment method
				
				$('#workboxForm input[type="radio"]').click(function()
				{
					$('#workboxForm li').removeClass('paymentGatewaySelected');
					$(this).closest('li').addClass('paymentGatewaySelected');
				});
				
				$('#workboxForm li').click(function()
				{
					$(this).children('input[type="radio"]').attr('checked','checked');
					$('#workboxForm li').removeClass('paymentGatewaySelected');
					$(this).addClass('paymentGatewaySelected');
				});
				
				$('#submitPaymentButton').click(function()
				{
					var paymentType = $('input[name="paymentType"]:checked').val();
					goto('bill.payment.php?billID='+$('#billID').val()+'&paymentType='+paymentType );
				});
			});
		</script>
		<input type="hidden" name="billID" id="billID" value="{$billID}">
		<div id="editWorkbox">
			<h1>{$lang.choosePaymentMethod}</h1>
			<div class="cartTotalList paymentGatewaysBox cartPaymentForm" style="border-top: 0; margin: 20px 0 20px 0;">
				<ul>
					{foreach $gateways as $gatewayKey => $gateway}
						<li>
							<input type="radio" name="paymentType" value="{$gateway.id}" id="paymentGateway{$gateway.id}" {if $gateway@first}checked="checked"{/if}>
							{if $gateway.logo}<img src="{$imgPath}/logos/{$gateway.id}.png">{/if}
							<label for="paymentGateway{$gateway.id}">{$gateway.displayName}</label>
							<p>{$gateway.publicDescription}</p>
						</li>
					{/foreach}
				</ul>
			</div>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{$lang.continue}" id="submitPaymentButton"></div>
		</div>
	{/if}
	{if $mode == 'unfinishedPackage'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> Incomplete Packages!</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				One or more of the packages in your cart have additional items left to fill before you can checkout.
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	{if $mode == 'missingSearchTerms'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> Keywords Required</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				You must enter keywords before you begin your search.
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	{if $mode == 'downloadExpired'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.downloadExpired}</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				{$lang.downloadExpiredMes}
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	{if $mode == 'downloadsExceeded'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.downloadMax}</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				{$lang.downloadMaxMes}
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	{if $mode == 'downloadNotApproved'}
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.downloadNotApproved}</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				{$lang.downloadNotApprovedMes}
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"></div>
		</div>
	{/if}
	{if $mode == 'downloadNotAvailable'}
		<script>
			$(function()
			{
				// Request a download button
				$('#workboxRequestDownload').click(function()
				{
					//alert('test');
					
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
				
				$('#requestDownloadEmail').click(function()
				{
					$(this).val('');
				});
				
				// Submit the request for a download form
				function submitRequestDownloadForm()
				{
					//alert($("#workboxForm").serialize());
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: $("#workboxForm").serialize(),
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
		</script>
		
		<div id="editWorkbox">
			<input type="hidden" name="action" value="emailForFile">
			<input type="hidden" name="mediaID" value="{$mediaID}">
			<input type="hidden" name="profileID" value="{$profileID}">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.downloadNotAvail}</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				{$lang.downloadNotAvailMes}
			</p>
			<div class="workboxActionButtons">
				<div id="requestDownloadSuccess" class="notice" style="display: none; margin: 0; float: left;">{$lang.requestDownloadSuccess}</div>
				<div style="float: left;" id="requestDownloadContainer"><input type="text" name="requestDownloadEmail" id="requestDownloadEmail" style="display: none;" value="{$lang.enterEmail}"> <input type="button" value="{$lang.requestDownload}" altText="{$lang.submit}" id="workboxRequestDownload"></div>
				<!--<input type="button" value="{$lang.cancel}" class="closeWorkbox">-->
			</div>
		</div>
	{/if}
	
	{if $mode == 'contrNewAlbum'}
		<script>
			$(function()
			{
				$('#newAlbumPublic').click(function()
				{					
					$('#albumPasswordRow').toggle();
				});
			});
		</script>		
		<div id="editWorkbox">
			<h1>{$lang.newAlbum}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.albumName}:</div>
					<div class="divTableCell"><input type="text" name="newAlbumName" id="newAlbumName" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.mediaLabelDesc}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="newAlbumDescription" id="newAlbumDescription"></textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.makePublic}:</div>
					<div class="divTableCell"><input type="checkbox" name="newAlbumPublic" id="newAlbumPublic" value="1"></div>
				</div>
				<div class="divTableRow" id="albumPasswordRow" style="display: none;">
					<div class="divTableCell formFieldLabel">{$lang.password}:</div>
					<div class="divTableCell"><input type="text" name="newAlbumPassword" id="newAlbumPassword" class="form-control" /><span style="font-size: 10px; color: #999">{$lang.passwordLeaveBlank}</span></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox"> <input type="button" value="{$lang.save}" id="saveWorkboxForm"></div>
	{/if}
	
	{if $mode == 'contrEditAlbum'}
		<script>
			$(function()
			{
				$('#albumPublic').click(function()
				{	
					if($('#albumPublic').is(':checked'))
					{
						$('.shareLinkDiv').show();
					}
					else
					{
						$('.shareLinkDiv').hide();
					}
				});
			});
		</script>
		<div id="editWorkbox">
			<input type="hidden" name="albumID" id="albumID" value="{$album.ugallery_id}">
			<h1>{$lang.editAlbum}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.albumName}:</div>
					<div class="divTableCell"><input type="text" name="albumName" id="albumName" value="{$album.name}" require="require" errorMessage="{$lang.required}" class="form-control"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.mediaLabelDesc}:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="albumDescription" id="albumDescription" class="form-control">{$album.description}</textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.makePublic}:</div>
					<div class="divTableCell" style="vertical-align: middle; padding-top: 18px;"><input type="checkbox" name="albumPublic" id="albumPublic" value="1" {if $album.publicgal}checked="checked"{/if}></div>
				</div>
				{if $config.contrPasswordAlbums}
				<div class="divTableRow shareLinkDiv" {if !$album.publicgal}style="display: none;"{/if}>
					<div class="divTableCell formFieldLabel">{$lang.password}:</div>
					<div class="divTableCell"><input type="text" name="albumPassword" value="{$album.password}" class="form-control"><span style="font-size: 10px; color: #999">{$lang.passwordLeaveBlank}</span></div>
				</div>
				{/if}
				<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
				<div class="divTableRow shareLinkDiv" {if !$album.publicgal}style="display: none;"{/if}>
					<div class="divTableCell formFieldLabel">{$lang.link}:</div>
					<div class="divTableCell"><input type="text" name="shareLink" value="{$shareLink}" class="form-control"></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.cancel}" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="{$lang.save}" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	{/if}
	
	{if $mode == 'contrDeleteImportMedia'}
		<script>
			$(function()
			{
				$('#deleteMediaButton').click(function(event)
				{
					doDeleteImportMedia();
					closeWorkbox();
				});
			});
		</script>
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.deleteMediaMes}</p>
		</div>
		<div class="workboxActionButtons"><input type="button" href="actions.php?action=contrDeleteAlbum&albumID={$contrAlbumID}" value="{$lang.yes}" id="deleteMediaButton"> <input type="button" value="{$lang.no}" class="closeWorkbox"></div>
	{/if}
	
	{if $mode == 'contrDeleteAlbum'}
		<script>
			$(function()
			{
				$('#deleteAlbumButton').click(function(event)
				{
					goto($(this).attr('href'));
				});
			});
		</script>
		
		<div id="editWorkbox">
			<h1><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.warning}</h1>
			<p class="dimMessage">{$lang.deleteAlbumMes}</p>
		</div>
		<div class="workboxActionButtons"><input type="button" href="actions.php?action=contrDeleteAlbum&albumID={$contrAlbumID}" value="{$lang.yes}" id="deleteAlbumButton"> <input type="button" value="{$lang.no}" class="closeWorkbox"></div>
	{/if}
	
	{if $mode == 'contrUploadMedia'}
		
		{if $member.uploader == 2}
			<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
			<script type="text/javascript" src="{$baseURL}/assets/plupload/plupload.full.js"></script>
			<script type="text/javascript" src="{$baseURL}/assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
			<style type="text/css">
				@import url({$baseURL}/assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);
				.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
			</style>
		{/if}
		
		<script>			
			$(function()
			{				
				{if $member.uploader == 1} // Java				
					$('#startContrUpload').click(function(event)
					{					
						$(this).attr('disabled','disabled'); // Disable start button					
						var error = getUploader().startUpload(); // Start java uploader
						if(error != null) alert(error); // Output error if something goes wrong
					});
					
					$('.cancelUpload').click(function(event)
					{					
						closeWorkbox();
						stopJavaUpload();
					});
				{/if}
				{if $member.uploader == 2} // Plupload
					plupload.addI18n({
							'Select files' : "{$lang.plupAddFilesToQueue}",
							'Add files to the upload queue and click the start button.' : "{$lang.plupAddFilesToQueue}",
							'Filename' : "{$lang.plupFilename}",
							'Status' : "{$lang.plupStatus}",
							'Size' : "{$lang.plupSize}",
							'Add files' : "{$lang.plupAddFiles}",
							'Start upload':"{$lang.plupStartUplaod}",
							'Stop current upload' : "{$lang.plupStopUpload}",
							'Start uploading queue' : "{$lang.plupStartQueue}",
							'Drag files here.' : "{$lang.plupDragFilesHere}"
					});		
					
					$('.cancelUpload').click(function(event)
					{					
						closeWorkbox();
					});
										
					$("#uploadContainer").pluploadQueue({
						// General settings
						runtimes : 'gears,html5,flash,silverlight,browserplus',
						url : '{$upSet.handler}',
						max_file_size : '{$upSet.maxFilesize}mb',
						chunk_size : '1mb',
						unique_names : false,				
						// Specify what files to browse for
						filters : [
							{ title : "Files", extensions : "{$upSet.allowedFileTypes}" }
						],
						// Flash settings
						flash_swf_url : '{$baseURL}/assets/plupload/plupload.flash.swf',				
						// Silverlight settings
						silverlight_xap_url : '{$baseURL}/assets/plupload/plupload.silverlight.xap'
					});	
					
					var uploader = $("#uploadContainer").pluploadQueue();
					
					// preinit: attachCallbacks,
					
					$('#startContrUpload').click(function(event)
					{					
						uploader.start();
					});
					
					uploader.bind('UploadComplete', function(Up, File, Response)
					{
						$('#uploadMediaStep1').hide();
						var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=newUpload';
						workbox({ page : workboxPage, skipOverlay : true });						
					});
					
					uploader.bind('FilesAdded', function(Up, File, Response)
					{
						if(uploader.files.length > 0)
						{
							$('#startContrUpload').removeAttr('disabled');
						}
						else
						{
							$('#startContrUpload').attr('disabled','disabled');
						}
					});
					
					uploader.bind('FilesRemoved', function(Up, File, Response)
					{
						if(uploader.files.length > 0)
						{
							$('#startContrUpload').removeAttr('disabled');
						}
						else
						{
							$('#startContrUpload').attr('disabled','disabled');
						}
					});
					
					/*
					uploader.bind('BeforeUpload', function(up, file)
					{
						if('thumb' in file)
						  up.settings.resize = { width : 150, height : 150, quality : 100 };
						else
						  up.settings.resize = { width : 1600, height : 1600, quality : 100 };
					});
					
					uploader.bind('FileUploaded', function(up, file) {
						if(!('thumb' in file))
						{
							file.thumb = true;
							file.loaded = 0;
							file.percent = 0;
							file.status = plupload.QUEUED;
							up.trigger("QueueChanged");
							up.refresh();
						}
					});
					*/
															
				{/if}
				
			});
			
			/*
			function attachCallbacks(uploader)
			{
				uploader.bind('UploadComplete', function(Up, File, Response) {
					alert('test');
				});
			}
			*/
		</script>
		
		<div id="editWorkbox">
			<div id="uploadMediaStep1">
				<h1>{$lang.uploadMediaUpper}</h1>
				<div style="margin-bottom: 30px;" id="uploadContainer">
				{if $member.uploader == 1}
					<object name="jumpLoaderApplet" type="application/x-java-applet" height="400" width="100%" mayscript> 
						<param name="code" value="jmaster.jumploader.app.JumpLoaderApplet" > 
						<param name="archive" value="{$baseURL}/assets/jumploader/jumploader_z.jar" > 
						<param name="mayscript" value="true" >
						<param name="ac_fireAppletInitialized" value="true" >
						<param name="vc_lookAndFeel" value="system">
						<param name="uc_uploadUrl" value="{$upSet.handler}">                            
						<param name="uc_maxFileLength" value="{$upSet.maxFilesize}">
						<param name="uc_maxFiles" value="{$upSet.maxFiles}">
						<param name="uc_uploadScaledImagesNoZip" value="true">
						<param name="uc_uploadOriginalImage" value="true"> 
						<param name="uc_fileNamePattern" value="^.+\.(?i)({$upSet.allowedFileTypes})$">
						<param name="vc_uploadViewMenuBarVisible" value="true">
						<param name="vc_uploadViewStartActionVisible" value="false">
						<param name="vc_mainViewFileListViewVisible" value="false">
						<param name="vc_mainViewFileListViewHeightPercent" value="20">
						<param name="ac_fireUploaderFileStatusChanged" value="true"> 
						<param name="ac_fireUploaderFileAdded" value="true">                             
						<param name="ac_fireUploaderFileRemoved" value="true">
						<param name="uc_uploadScaledImages" value="true">
						<param name="uc_scaledInstanceNames" value="{$upSet.uc_scaledInstanceNames}">
						<param name="uc_scaledInstanceDimensions" value="{$upSet.uc_scaledInstanceDimensions}">
						<param name="uc_scaledInstanceQualityFactors" value="{$upSet.uc_scaledInstanceQualityFactors}">
						<param name="uc_imageSubsamplingFactor" value="20">
					</object>
				{/if}
				</div>
				<div style="margin-bottom: 30px; padding: 5px 0 0 5px;"><strong>{$lang.batchUploader}:</strong> {$lang.uploader.{$member.uploader}} <a href="{linkto page="account.php"}" class="colorLink">[{$lang.change}]</a></div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="{$lang.cancel}" class="cancelUpload"> <input type="button" value="{$lang.startUpper}" id="startContrUpload" disabled="disabled"></div>
			</div>
		</div>
	{/if}
	
	{if $mode == 'contrAssignMediaDetails'}
		<script>
			$(function()
			{				
				registerDG({ id : 'contrUploadDetailsDG' }); // Register the data group contrUploadDetailsDG
				registerAssignDetailsButtons();
				
				var selectedGalleries = [];
				
				{if $member.membershipDetails.admin_galleries}
					loadContrGalleries(); // Load galleries if admin galleries is turned on
				{/if}
				
				$('#originalLicense').change(function()
				{
					var ltype = $('#originalLicense option:selected').attr('ltype');

					if(ltype == 'fr')
						$('#originalPriceContainer').hide();
					else
						$('#originalPriceContainer').show();
				});
				
				$('.moreLangs').clicktoggle(function()
					{
						var control = $(this).attr('control');
						$('#'+control).show();			
						$(this).attr('value','--');
					},
					function()
					{
						var control = $(this).attr('control');
						$('#'+control).hide();			
						$(this).attr('value','+');
					}
				);
				
				$('.keywordEntry').click(function()
				{
					var control = ($(this).attr('id').split('-'))[1]; // Find the language that we are controlling
					var keywords = ($('#keywordInput-'+control).val()).split(',');	// Split keywords by comma
					$('#keywordInput-'+control).val(''); // Clear the input
					
					if($(keywords).length > 0)
						$('#keywordsContainer-'+control).show(); // Show the container
					
					$(keywords).each(function(key,elem)
					{
						if(elem)
						{
							elem = $.trim(elem); // Trim whitespace
							$('#keywordsContainer-'+control).append('<p class="removeKeyword"><input type="hidden" name="keyword['+control+'][]" value="'+elem+'">'+elem+' <img src="{$imgPath}/close.button.png"></p>');
						}
					});
					
					registerKeywordRemove(); // Register the remove function
				});
				
				//registerKeywordRemove(); // Register this function when first loaded - Only needed on an edit
				
				$('#albumPublic').click(function()
				{	
					if($('#albumPublic').is(':checked'))
					{
						$('#albumPasswordRow').show();
					}
					else
					{
						$('#albumPasswordRow').hide();
					}
				});
			});
			
			function registerKeywordRemove()
			{
				$('.removeKeyword').unbind('click');				
				$('.removeKeyword').click(function()
				{
					$(this).remove();
				});	
			}
		</script>
		
		<div id="editWorkbox">
			<div id="contrImportContainerWB" style="display: none;">
				<p style="font-size: 24px; margin-bottom: 20px;" id="importSavingMes"><img src="{$imgPath}/loader1.gif"> {$lang.saving}...</p>
				<div>
					<div id="contrImportStatusRow">
						<p><a href="#" id="contrMediaImportDetails">{$lang.details}</a></p>
						<div id="loaderContainer"><p></p></div>
					</div>
					<div id="contrImportLog" style="display: none;">
						<ul id="contrImportLogList">
							<li style="display: none;"></li>		
						</ul>
					</div>
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="{$lang.close}" class="closeWorkbox" disabled="disabled"></div>
			</div>
			<div id="contrMediaDetailsContainer">
				<input type="hidden" name="saveMode" id="saveMode" value="{$saveMode}">
				<input type="hidden" name="contrSaveSessionForm" id="contrSaveSessionForm" value="{$contrSaveSessionForm}">
				{foreach $contrImportFiles as $filePath}
					<input type="hidden" name="files[]" value="{$filePath}" class="uploadFiles">
				{/foreach}
				<h1>{$lang.addMediaDetails}</h1><br> 
				<div id="contrUploadDetailsDG" style="margin-bottom: 30px;">
					<ul class="tabs">
						{if $member.membershipDetails.personal_galleries}<li container="albumsDGC">{$lang.ablum}</li>{/if}
						{if $member.membershipDetails.admin_galleries}<li container="galleriesDGC" id="galleriesTab">{$lang.galleries}</li>{/if}
						<li container="detailsDGC">{$lang.details}</li>
						{if $sellDigital}<li container="pricingDGC">{$lang.pricing}</li>{/if}
						{if $member.membershipDetails.admin_prints and $printRows}<li container="printsDGC">{$lang.prints}</li>{/if}
						{if $member.membershipDetails.admin_products and $productRows}<li container="productsDGC">{$lang.products}</li>{/if}
					</ul>
					{if $member.membershipDetails.personal_galleries}
						<div class="dataGroupContainer" id="albumsDGC">							
							<br><br>{$lang.selectAlbumMes}
							<div class="divTable ablumTypeTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="none" checked="checked" id="ablumTypeNone" class="albumType"></div>
									<div class="divTableCell"><label for="ablumTypeNone">{$lang.none}</label></div>
								</div>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="new" id="ablumTypeNew" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeNew">
											{$lang.newAlbum}<br>
											<input type="text" name="newAlbumName" style="margin-top: 6px;">
										</label>
										<div class="divTable" style="margin: 0; display: none;" id="newAlbumSettings">
											<div class="divTableRow">
												<div class="divTableCell formFieldLabel">{$lang.makePublic}:</div>
												<div class="divTableCell" style="vertical-align: middle; padding-top: 5px;"><input type="checkbox" name="albumPublic" id="albumPublic" value="1"></div>
											</div>
											<div class="divTableRow" id="albumPasswordRow" style="display: none;">
												<div class="divTableCell formFieldLabel">{$lang.password}:</div>
												<div class="divTableCell"><input type="text" name="newAlbumPassword" id="newAlbumPassword" class="form-control" /><br><span style="font-size: 10px; color: #999">{$lang.passwordLeaveBlank}</span></div>
											</div>
										</div>
									</div>
								</div>
								{if $contrAlbums}
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="existing" id="ablumTypeCurrent" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeCurrent">
											{$lang.myAlbums}<br>
											<select style="width: 258px; margin-top: 6px;" name="albumID">
												{foreach $contrAlbums as $album}
													<option value="{$album.gallery_id}">{$album.name}</option>
												{/foreach}
											</select>
										</label>										
									</div>
								</div>
								{/if}
							</div>
						</div>
					{/if}
					{if $member.membershipDetails.admin_galleries}
						<div class="dataGroupContainer" id="galleriesDGC">
							<br><br>{$lang.selectGalleriesMes}
							<select id="gallerySelector" multiple="multiple" name="contrGalleries[]" style="width: 100%; height: 200px; margin-top: 20px; margin-bottom: 20px;">
								{*foreach $selectableGalleries as $gallery}
									<option>{$gallery.name}</option>
								{/foreach*}
							</select>
						</div>
					{/if}
					<div class="dataGroupContainer" id="detailsDGC" style="overflow: auto;">
						<div style="float: left">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Title</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><input type="text" name="title[{$activeLanguages.{$selectedLanguage}.id}]"> {if $displayLanguages|@count > 1}&nbsp;{$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="titles" class="moreLangs">{/if}</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="titles">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}<li><input type="text" name="title[{$language@key}]"> &nbsp;{$language.name}</li>{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}									
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Description</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><textarea style="width: 250px; height: 80px; float: left;" name="description[{$activeLanguages.{$selectedLanguage}.id}]"></textarea> {if $displayLanguages|@count > 1} &nbsp; {$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="descriptions" class="moreLangs">{/if}</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="descriptions">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}<li><textarea style="width: 250px; height: 80px; float: left;" name="description[{$language@key}]"></textarea> &nbsp; {$language.name}</li>{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Keywords</div>
									<div class="divTableCell formFieldList">										
										<ul>
											<li>
												<div><input type="text" id="keywordInput-{$selectedLanguage}"> <input type="button" value="{$lang.addUpper}" id="keywordAdd-{$selectedLanguage}" class="keywordEntry" style="margin: -4px 0 0 -44px;"> {if $displayLanguages|@count > 1} &nbsp; {$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="keywords" class="moreLangs">{/if}</div>
												<div class="keywordsContainer" id="keywordsContainer-{$selectedLanguage}" style="display: none;"></div>
											</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="keywords">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}
														<li>
															<div><input type="text" id="keywordInput-{$language@key}"> <input type="button" value="{$lang.addUpper}" id="keywordAdd-{$language@key}" class="keywordEntry" style="margin: -4px 0 0 -44px;"> &nbsp; {$language.name}</div>
															<div class="keywordsContainer" id="keywordsContainer-{$language@key}" style="display: none;"></div>
														</li>
													{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}									
									</div>
								</div>
							</div>
						</div>
						<div style="float: left">
							<div class="divTable">
								{if $mediaTypesRows and $member.membershipDetails.allow_selling}
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.mediaLabelMediaTypes}</div>
									<div class="divTableCell">
										<ul>
											{foreach $mediaTypes as $mediaType}
												<li style="margin-bottom: 6px;"><input type="checkbox" name="mediaTypes[{$mediaType.mt_id}]" id="mediaType{$mediaType.mt_id}" value="{$mediaType.mt_id}" {if $mediaType.selected}checked="checked"{/if}> <label for="mediaType{$mediaType.mt_id}">{$mediaType.name}</label></li>
											{/foreach}
										</ul>
									</div>
								</div>
								{/if}
							</div>
						</div>
					</div>
					{if $member.membershipDetails.allow_selling}
						<div class="dataGroupContainer" id="pricingDGC">
							<br><br>{$lang.chooseItemsMes}<br><br><br>
							
							{if $member.membershipDetails.contr_digital}
							<h1>{$lang.original}</h1>
							<div class="divTable contrItemSelectTable">
								<div class="divTableRow">
									<div class="divTableCell"><input type="checkbox" name="original" value="1" class="contrItem" id="contrDigitalOriginal" checked="checked"></div>
									<div class="divTableCell">
										<label for="contrDigitalOriginal">{$lang.original}</label>
									</div>
									<div class="divTableCell">
										<div class="pricingInfo" style="display: block">
											<div id="originalPriceContainer">
												{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="originalCredits" style="width: 100px;"><p></p></div>{/if}
												{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="originalPrice" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
											</div>
											<div {if $member.membershipDetails.contr_col}style="display: none;"{/if}>
												<select id="originalLicense" name="originalLicense">
													{foreach $licenses as $key => $license}
														<option value="{$key}" ltype="{$license.lic_purchase_type}">{$license.name}</option>
													{/foreach}
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
							</div>
							{/if}
							
							{if $digitalRows.photo and $member.membershipDetails.additional_sizes}
								<h1>{$lang.photoProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $photoProfiles as $profile}
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}"></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
							{if $digitalRows.video and $member.membershipDetails.additional_sizes}
								<h1>{$lang.videoProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $videoProfiles as $profile}
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}"></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
														{if $profile.dsp_type == 'video'}
															{if $profile.fps}<li>{$lang.mediaLabelFPS}: <strong>{$digital.fps}</strong></li>{/if}
															{if $profile.running_time}<li>{$lang.mediaLabelRunningTime}: <strong>{$digital.running_time}</strong> {$lang.seconds}</li>{/if}
														{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
							{if $digitalRows.other and $member.membershipDetails.additional_sizes}
								<h1>{$lang.otherProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $otherProfiles as $profile}
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}"></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
						</div>
					{/if}
					{if $member.membershipDetails.admin_prints and $printRows}
						<div class="dataGroupContainer" id="printsDGC">
							<br><br>{$lang.chooseItemsMes}<br>
							<div class="divTable contrItemSelectTable">
								{foreach $prints as $print}
									<div class="divTableRow opac40">
										<div class="divTableCell"><input type="checkbox" name="print[]" value="{$print.print_id}" class="contrItem" id="contrPrint{$print.print_id}"></div>
										<div class="divTableCell">
											<label for="contrPrint{$print.print_id}">{$print.name}</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo">
												<ul>
													{if $print.description}<li>{$lang.mediaLabelDesc}: <strong>{$print.description}</strong></li>{/if}
												</ul>
												{if $printCreditsCartStatus}<div>{$lang.credits} <input type="text" name="printCredits[{$print.print_id}]" style="width: 100px;"><p></p></div>{/if}
												{if $printCurrencyCartStatus}<div>{$lang.price} <input type="text" name="printPrice[{$print.print_id}]" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								{/foreach}
							</div>
						</div>
					{/if}
					{if $member.membershipDetails.admin_products and $productRows}
						<div class="dataGroupContainer" id="productsDGC">
							<br><br>{$lang.chooseItemsMes}<br>
							<div class="divTable contrItemSelectTable">
								{foreach $products as $product}
									<div class="divTableRow opac40">
										<div class="divTableCell"><input type="checkbox" name="product[]" value="{$product.prod_id}" class="contrItem" id="contrProd{$product.prod_id}"></div>
										<div class="divTableCell">
											<label for="contrProd{$product.prod_id}">{$product.name}</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo">
												<ul>
													{if $product.description}<li>{$lang.mediaLabelDesc}: <strong>{$product.description}</strong></li>{/if}
												</ul>
												{if $prodCreditsCartStatus}<div>{$lang.credits} <input type="text" name="productCredits[{$product.prod_id}]" style="width: 100px;"><p></p></div>{/if}
												{if $prodCurrencyCartStatus}<div>{$lang.price} <input type="text" name="productPrice[{$product.prod_id}]" style="width: 100px;"> {$priCurrency.code}<p></p></div>{/if}
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								{/foreach}
							</div>
						</div>
					{/if}
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="{$lang.cancel}" class="closeImportWorkbox"> <input type="button" value="{$lang.save}" class="saveContrAssignMediaDetails"></div>
			</div>
		</div>
	{/if}
	
	{if $mode == 'contrFailedApproval'}
		<div id="editWorkbox">
			<h1>{$lang.{$media.approvalStatusLang}}</h1>
			<p class="notice">
				{if $media.approval_message}{$media.approval_message}{else}{$lang.noDetailsMes}{/if}
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.close}" class="closeWorkbox"></div>
		</div>
	{/if}
	
	{if $mode == 'contrMailinMedia'}
		<div id="editWorkbox">
			<h1>{$lang.mailInMedia}</h1>
			<p class="dimMessage">
				{$config.settings.contr_cd2_mes}
				<br><br>
				<strong>{$config.settings.business_name}</strong><br>
				{$config.settings.business_address}<br>
				{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
				{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
				{$config.settings.business_country}
				<br><br>
			</p>
			<div class="workboxActionButtons"><input type="button" value="{$lang.close}" class="closeWorkbox"></div>
		</div>
	{/if}
	
	{if $mode == 'editContrMedia'}
		<script>
			function setSelectedGalleries()
			{	
				{foreach $selectedGalleries as $galleryID}
					selectedGalleries[{$galleryID@key}] = 'galleryTree{$galleryID}';
				{/foreach}
			}
			
			$(function()
			{				
				registerDG({ id : 'contrUploadDetailsDG' }); // Register the data group contrUploadDetailsDG
				registerAssignDetailsButtons();
				registerKeywordRemove();
				setSelectedGalleries();
				
				sampleUploader('#thumbUploader');
				sampleUploader('#videoUploader'); 
				sampleUploader('#propReleaseUploader');
				sampleUploader('#modelReleaseUploader');
				
				{if $member.membershipDetails.admin_galleries}
					loadContrGalleries(); // Load galleries if admin galleries is turned on
				{/if}
				
				$('#contrMediaTypeSelection').change(function()
				{
					var profile = $(this).val();
					
					if(profile == 'video')
					{
						$('.typeVideo').show();
						$('#videoButton').show();
					}
					else
					{
						$('.typeVideo').hide();
						$('#videoButton').hide();
					}
				});
				
				$('#originalLicense').change(function()
				{
					var ltype = $('#originalLicense option:selected').attr('ltype');

					if(ltype == 'fr')
						$('#originalPriceContainer').hide();
					else
						$('#originalPriceContainer').show();
				});
				
				$('.moreLangs').clicktoggle(function()
					{
						var control = $(this).attr('control');
						$('#'+control).show();			
						$(this).attr('value','--');
					},
					function()
					{
						var control = $(this).attr('control');
						$('#'+control).hide();			
						$(this).attr('value','+');
					}
				);
				
				$('.keywordEntry').click(function()
				{
					var control = ($(this).attr('id').split('-'))[1]; // Find the language that we are controlling
					var keywords = ($('#keywordInput-'+control).val()).split(',');	// Split keywords by comma
					$('#keywordInput-'+control).val(''); // Clear the input
					
					if($(keywords).length > 0)
						$('#keywordsContainer-'+control).show(); // Show the container
					
					var mediaID = $('#mediaID').val();
					
					$(keywords).each(function(key,elem)
					{
						if(elem)
						{							
							elem = $.trim(elem); // Trim whitespace
							
							$.ajax({
								type: 'POST',
								url: baseURL+'/actions.php',
								data: 'action=addContrKeyword&mediaID='+mediaID+'&keyLang='+control+'&keyword='+elem,
								dataType: 'json',
								success: function(data)
								{
									$('#keywordsContainer-'+control).append('<p class="removeKeyword" keyID="'+data.keyID+'"><input type="hidden" name="keyword['+control+'][]" value="'+elem+'">'+elem+' <img src="{$imgPath}/close.button.png"></p>');
									registerKeywordRemove(); // Register the remove function
								}
							});
						}
					});
				});
				
				$('.attachFile').click(function(event)
				{
					$('#attachFileUploaderContainer').show();
					var dspID = $(this).attr('dspID');
					sampleUploader('#dspUploader',dspID);
					scroll(0,0);
				});
				
				$('.deleteAttachFile').click(function(event)
				{
					var dspID = $(this).attr('dspID');
					var mediaID = $('#mediaID').val();
					
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: 'action=deleteDSP&mediaID='+mediaID+'&dspID='+dspID,
						success: function(data)
						{	
							$('#detachButton-'+dspID).hide();					
							$('#attachButton-'+dspID).show();
						}
					});
					
				});
				
				$('.attachFileCloseButton').click(function(event)
				{					
					$('#dspUploader').uploadify('destroy');
				});
				
				//registerKeywordRemove(); // Register this function when first loaded - Only needed on an edit
			});
			
			function registerKeywordRemove()
			{
				$('.removeKeyword').unbind('click');				
				$('.removeKeyword').click(function()
				{
					var keyID = $(this).attr('keyID');
					
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: 'action=removeContrKeyword&keyID='+keyID,
						dataType: 'json',
						success: function(data)
						{
							
						}
					});
					
					$(this).remove();
				});	
			}
		</script>
		
		<div id="editWorkbox">
			<div id="attachFileUploaderContainer" class="sampleUploaderBox">
				<div class="attachFilePopup">
					{$lang.attachMessage}<br><br>
					<img src="{$imgPath}/close.button.png" class="closeButton closeSampleUploaderBox attachFileCloseButton">
					<input id="dspUploader" name="dspUploader" type="file" buttonText="{$lang.browse}...">
				</div>
			</div>
			<div id="contrMediaDetailsContainer">
				<input type="hidden" name="saveMode" id="saveMode" value="{$saveMode}">
				<input type="hidden" name="contrSaveSessionForm" id="contrSaveSessionForm" value="{$contrSaveSessionForm}">
				<input type="hidden" name="albumTypeOriginal" id="albumTypeOriginal" value="{$selectedAlbum}">
				<input type="hidden" name="mediaID" id="mediaID" value="{$media.media_id}">				
				<input type="hidden" name="maxUploadSize" id="maxUploadSize" value="{$maxUploadSize}">
				<!--<input type="text" name="dspID" id="dspID" value="">-->
				<input type="hidden" name="originalMediaDS" id="originalMediaDS" value="{$originalMediaDS}">
				
				
				{*foreach $contrImportFiles as $filePath}
					<input type="hidden" name="files[]" value="{$filePath}" class="uploadFiles">
				{/foreach*}
				
				<h1>{$lang.editMediaDetails}</h1><br> 
				<div id="contrUploadDetailsDG" style="margin-bottom: 30px;">
					<ul class="tabs">
						{if $member.membershipDetails.personal_galleries}<li container="albumsDGC">{$lang.ablum}</li>{/if}
						{if $member.membershipDetails.admin_galleries}<li container="galleriesDGC" id="galleriesTab">{$lang.galleries}</li>{/if}
						<li container="detailsDGC">{$lang.details}</li>
						{if $sellDigital}<li container="pricingDGC">{$lang.pricing}</li>{/if}
						{if $member.membershipDetails.admin_prints and $printRows}<li container="printsDGC">{$lang.prints}</li>{/if}
						{if $member.membershipDetails.admin_products and $productRows}<li container="productsDGC">{$lang.products}</li>{/if}
					</ul>
					{if $member.membershipDetails.personal_galleries}
						<div class="dataGroupContainer" id="albumsDGC">			
							<br><br>{$lang.selectAlbumMes}
							<div class="divTable ablumTypeTable">
								<div class="divTableRow {if $selectedAlbum}opac40{/if}">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="none" {if !$selectedAlbum}checked="checked"{/if} id="ablumTypeNone" class="albumType"></div>
									<div class="divTableCell"><label for="ablumTypeNone">{$lang.none}</label></div>
								</div>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="new" id="ablumTypeNew" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeNew">
											{$lang.newAlbum}<br>
											<input type="text" name="newAlbumName" style="margin-top: 6px;">
										</label>
									</div>
								</div>
								{if $contrAlbums}
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow {if !$selectedAlbum}opac40{/if}">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="existing" {if $selectedAlbum}checked="checked"{/if} id="ablumTypeCurrent" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeCurrent">
											My Albums<br>
											<select style="width: 258px; margin-top: 6px;" name="albumID">
												{foreach $contrAlbums as $album}
													<option value="{$album.gallery_id}" {if $album.gallery_id == $selectedAlbum}selected="selected"{/if}>{$album.name}</option>
												{/foreach}
											</select>
										</label>										
									</div>
								</div>
								{/if}
							</div>
						</div>
					{/if}
					{if $member.membershipDetails.admin_galleries}
						<div class="dataGroupContainer" id="galleriesDGC">
							<br><br>{$lang.selectGalleriesMes}
							<select id="gallerySelector" multiple="multiple" name="contrGalleries[]" style="width: 100%; height: 200px; margin-top: 20px; margin-bottom: 20px;">
								{*foreach $selectableGalleries as $gallery}
									<option>{$gallery.name}</option>
								{/foreach*}
							</select>
						</div>
					{/if}
					<div class="dataGroupContainer" id="detailsDGC" style="overflow: auto;">
						<div style="float: left">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{$lang.mediaLabelTitle}</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><input type="text" name="title[{$activeLanguages.{$selectedLanguage}.id}]" value="{$media.title}"> {if $displayLanguages|@count > 1}&nbsp;{$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="titles" class="moreLangs">{/if}</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="titles">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}<li><input type="text" name="title[{$language@key}]" value="{$media.titleLang.{$language@key}}"> &nbsp;{$language.name}</li>{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}									
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{$lang.mediaLabelDesc}</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><textarea style="width: 250px; height: 80px; float: left;" name="description[{$activeLanguages.{$selectedLanguage}.id}]">{$media.description}</textarea> {if $displayLanguages|@count > 1} &nbsp; {$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="descriptions" class="moreLangs">{/if}</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="descriptions">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}<li><textarea style="width: 250px; height: 80px; float: left;" name="description[{$language@key}]">{$media.descriptionLang.{$language@key}}</textarea> &nbsp; {$language.name}</li>{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{$lang.mediaLabelKeys}</div>
									<div class="divTableCell formFieldList">										
										<ul>
											<li>
												<div><input type="text" id="keywordInput-{$selectedLanguage}"> <input type="button" value="{$lang.addUpper}" id="keywordAdd-{$selectedLanguage}" class="keywordEntry" style="margin: -4px 0 0 -44px;"> {if $displayLanguages|@count > 1} &nbsp; {$activeLanguages.{$selectedLanguage}.name} <input type="button" value="+" style="margin-top: 3px;" control="keywords" class="moreLangs">{/if}</div>
												<div class="keywordsContainer" id="keywordsContainer-{$selectedLanguage}" {if !$keywords.{$selectedLanguage}}style="display: none;{/if}">
													{foreach $keywords.{$selectedLanguage} as $key => $keyword}
														<p class="removeKeyword" keyID="{$key}"><input type="hidden" name="keyword[{$selectedLanguage}][]" value="{$key}">{$keyword} <img src="{$imgPath}/close.button.png"></p>
													{/foreach}
												</div>
											</li>
										</ul>
										{if $displayLanguages|@count > 1}
										<ul style="display: none" id="keywords">
											{if $activeLanguages|@count > 1}
												{foreach $activeLanguages as $language}
													{if $language@key != $selectedLanguage}
														<li>
															<div><input type="text" id="keywordInput-{$language@key}"> <input type="button" value="{$lang.addUpper}" id="keywordAdd-{$language@key}" class="keywordEntry" style="margin: -4px 0 0 -44px;"> &nbsp; {$language.name}</div>
															<div class="keywordsContainer" id="keywordsContainer-{$language@key}" {if !$keywords.{$language@key}}style="display: none;{/if}">
																{foreach $keywords.{$language@key} as $key => $keyword}
																	<p class="removeKeyword" keyID="{$key}"><input type="hidden" name="keyword[{$language@key}][]" value="{$key}">{$keyword} <img src="{$imgPath}/close.button.png"></p>
																{/foreach}
															</div>
														</li>
													{/if}
												{/foreach}
											{/if}
										</ul>
										{/if}									
									</div>
								</div>
							</div>
						</div>
						<div class="detailsColumn2">
							<div id="thumbUploaderContainer" class="sampleUploaderBox">
								<div style="padding: 20px;">
									<img src="{$imgPath}/close.button.png" class="closeButton closeSampleUploaderBox">
									<img src="{mediaImage mediaID=$media.encryptedID type=thumb folderID=$media.encryptedFID size=160 seo=$media.seoName}" alt="{$media.title}" class="similarMedia" id="contrMediaThumbnail">
									<div id="sampleUploaderDiv" style="clear: both; position: relative;">
										<input id="thumbUploader" name="thumbUploader" type="file" buttonText="{$lang.uploadThumb}">
										<!--<a href="#" style="position: absolute; z-index: 1; margin-top: -30px;" class="buttonLink">{$lang.uploadAvatarXXX}Upload Thumbnail</a>-->
									</div>
								</div>
							</div>
							<div id="videoUploaderContainer" class="sampleUploaderBox">
								<div style="padding: 20px;">
									<img src="{$imgPath}/close.button.png" class="closeButton closeSampleUploaderBox">
									<input id="videoUploader" name="videoUploader" type="file" buttonText="{$lang.uploadVideo}">
								</div>
							</div>
							<div class="samplesButtons">
								<p id="thumbButton">{$lang.thumbnail}</p>
								<p id="videoButton" {if $media.dsp_type != 'video'}style="display: none;"{/if}>{$lang.videoSample}</p>
							</div>
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.mediaProfile}</div>
									<div class="divTableCell">
										<select style="width: 100%;" name="dsp_type" id="contrMediaTypeSelection">
											<option value="photo" {if $media.dsp_type == 'photo'}selected="selected"{/if}>{$lang.photo}</option>
											<option value="video" {if $media.dsp_type == 'video'}selected="selected"{/if}>{$lang.video}</option>
											<option value="other" {if $media.dsp_type == 'other'}selected="selected"{/if}>{$lang.other}</option>
										</select>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.width}</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="width" value="{$media.width}" style="min-width: 50px; width: 50px;"> {$lang.px}
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.height}</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="height" value="{$media.height}" style="min-width: 50px; width: 50px;"> {$lang.px}
									</div>
								</div>
								<div class="divTableRow typeVideo" {if $media.dsp_type != 'video'}style="display: none;"{/if}>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.mediaLabelFPS}</div>
									<div class="divTableCell">
										<input type="text" name="fps" value="{$media.fps}" style="min-width: 50px; width: 50px;">
									</div>
								</div>
								<div class="divTableRow typeVideo" {if $media.dsp_type != 'video'}style="display: none;"{/if}>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.mediaLabelFormat}</div>
									<div class="divTableCell">
										<input type="text" name="format" value="{$media.format}" style="min-width: 50px; width: 50px;">
									</div>
								</div>
								<div class="divTableRow typeVideo" {if $media.dsp_type != 'video'}style="display: none;"{/if}>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.mediaLabelRunningTime}</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="running_time" value="{$media.running_time}" style="min-width: 50px; width: 50px;"> {$lang.seconds}
									</div>
								</div>
								<div class="divTableRow typeVideo" {if $media.dsp_type != 'video'}style="display: none;"{/if}>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle">{$lang.hd}</div>
									<div class="divTableCell">
										<input type="checkbox" name="hd" value="1" {if $media.hd}checked="checked"{/if}>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								{if $mediaTypesRows and $member.membershipDetails.allow_selling}
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.mediaLabelMediaTypes}</div>
									<div class="divTableCell">
										<ul>
											{foreach $mediaTypes as $mediaType}
												<li style="margin-bottom: 6px;"><input type="checkbox" name="mediaTypes[{$mediaType.mt_id}]" id="mediaType{$mediaType.mt_id}" value="{$mediaType.mt_id}" {if $mediaType.selected}checked="checked"{/if}> <label for="mediaType{$mediaType.mt_id}">{$mediaType.name}</label></li>
											{/foreach}
										</ul>
									</div>
								</div>
								{/if}
								{if $member.membershipDetails.allow_selling}
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.mediaLabelCopyright}</div>
									<div class="divTableCell">
										<textarea name="copyright" style="width: 100%; height: 50px;">{$media.copyright}</textarea>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.mediaLabelRelease}</div>
									<div class="divTableCell" style="padding-top: 15px;">
										<input type="checkbox" name="modelRelease" id="modelRelease" value="1" {if $media.model_release_status}checked="checked"{/if} style="float: left;">
										<div id="modelReleaseUploaderDiv" style="float: left;">
											<input id="modelReleaseUploader" name="modelReleaseUploader" type="file" buttonText="{$lang.uploadFile}">
											{if $media.model_release_form}<div id="modelReleaseFileDiv"><a href="./assets/files/releases/{$media.model_release_form}" target="_blank">{$media.model_release_form}</a> <input type="button" value="{$lang.delete}" class="deleteRelease" rType="model"></div>{/if}
										</div>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.mediaLabelPropRelease}</div>
									<div class="divTableCell" style="padding-top: 18px;">
										<input type="checkbox" name="propRelease" id="propRelease" value="1" {if $media.prop_release_status}checked="checked"{/if} style="float: left;">
										<div id="propReleaseUploaderDiv" style="float: left;">
											<input id="propReleaseUploader" name="propReleaseUploader" type="file" buttonText="{$lang.uploadFile}">
											{if $media.prop_release_form}<div id="propReleaseFileDiv"><a href="./assets/files/releases/{$media.prop_release_form}" target="_blank">{$media.prop_release_form}</a> <input type="button" value="{$lang.delete}" class="deleteRelease" rType="prop"></div>{/if}
										</div>
									</div>
								</div>
								{/if}
							</div>
						</div>
					</div>
					{if $member.membershipDetails.allow_selling}
						<div class="dataGroupContainer" id="pricingDGC">
							<br><br>{$lang.chooseItemsMes}<br><br><br>
							
							{if $member.membershipDetails.contr_digital}
							<h1>{$lang.original}</h1>
							<div class="divTable contrItemSelectTable">
								<div class="divTableRow {if !$media.orgSelected}opac40{/if}">
									<div class="divTableCell"><input type="checkbox" name="original" value="1" class="contrItem" id="contrDigitalOriginal" {if $media.orgSelected}checked="checked"{/if}></div>
									<div class="divTableCell">
										<label for="contrDigitalOriginal">{$lang.original}</label>
									</div>
									<div class="divTableCell">
										<div class="pricingInfo" {if $media.orgSelected}style="display: block"{/if}>
											<div id="originalPriceContainer" {if $media.license == 'fr'}style="display: none"{/if}>
												{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="originalCredits" style="width: 100px;" value="{$media.setCredits}"><p></p></div>{/if}
												{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="originalPrice" style="width: 100px;" value="{$media.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
											</div>
											<div {if $member.membershipDetails.contr_col}style="display: none;"{/if}>
												<select id="originalLicense" name="originalLicense">
													{foreach $licenses as $key => $license}
														<option value="{$key}" ltype="{$license.lic_purchase_type}" {if $license.selected}selected="selected"{/if}>{$license.name}</option>
													{/foreach}
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
							</div>
							{/if}
							
							{if $digitalRows.photo and $member.membershipDetails.additional_sizes}
								<h1>{$lang.photoProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $photoProfiles as $profile}
										<input type="hidden" name="digitalFilename[{$profile.ds_id}]" value="{$profile.filename}">
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow {if !$profile.selected}opac40{/if}">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}" {if $profile.selected}checked="checked"{/if}></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" {if $profile.selected}style="display: block"{/if}>
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setCredits}"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
													<div class="attachFileContainer">
														<p id="detachButton-{$profile.ds_id}" style="{if $profile.fileExists}display: block;{/if}">{$lang.fileAttached} {if $profile.fileExists}({$profile.filename}){/if} <input type="button" value="X" dspID="{$profile.ds_id}" class="deleteAttachFile"></p>
														<p id="attachButton-{$profile.ds_id}" style="{if !$profile.fileExists}display: block;{/if}"><input type="button" value="{$lang.attachFile}" dspID="{$profile.ds_id}" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
							{if $digitalRows.video and $member.membershipDetails.additional_sizes}
								<h1>{$lang.videoProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $videoProfiles as $profile}
										<input type="hidden" name="digitalFilename[{$profile.ds_id}]" value="{$profile.filename}">
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow {if !$profile.selected}opac40{/if}">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}" {if $profile.selected}checked="checked"{/if}></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" {if $profile.selected}style="display: block"{/if}>
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
														{if $profile.dsp_type == 'video'}
															{if $profile.fps}<li>{$lang.mediaLabelFPS}: <strong>{$digital.fps}</strong></li>{/if}
															{if $profile.running_time}<li>{$lang.mediaLabelRunningTime}: <strong>{$digital.running_time}</strong> {$lang.seconds}</li>{/if}
														{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setCredits}"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
													<div class="attachFileContainer">
														<p id="detachButton-{$profile.ds_id}" style="{if $profile.fileExists}display: block;{/if}">{$lang.fileAttached} {if $profile.fileExists}({$profile.filename}){/if} <input type="button" value="X" dspID="{$profile.ds_id}" class="deleteAttachFile"></p>
														<p id="attachButton-{$profile.ds_id}" style="{if !$profile.fileExists}display: block;{/if}"><input type="button" value="{$lang.attachFile}" dspID="{$profile.ds_id}" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
							{if $digitalRows.other and $member.membershipDetails.additional_sizes}
								<h1>{$lang.otherProfiles}</h1>
								<div class="divTable contrItemSelectTable">
									{foreach $otherProfiles as $profile}
										<input type="hidden" name="digitalFilename[{$profile.ds_id}]" value="{$profile.filename}">
										<input type="hidden" name="digitalLicense[{$profile.ds_id}]" value="{$profile.license_id}">
										<div class="divTableRow {if !$profile.selected}opac40{/if}">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="{$profile.ds_id}" class="contrItem" id="contrDSP{$profile.ds_id}" {if $profile.selected}checked="checked"{/if}></div>
											<div class="divTableCell">
												<label for="contrDSP{$profile.ds_id}">{$profile.name}</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" {if $profile.selected}style="display: block"{/if}>
													<ul>
														{if $profile.license}<li>{$lang.licenseType}: <strong>{$profile.licenseLang}</strong></li>{/if}
														{if $profile.width or $profile.height}<li>{$lang.resolution}: <strong>{$profile.width}x{$profile.height}{$lang.px}</strong></li>{/if}
														{if $profile.format}<li>{$lang.mediaLabelFormat}: <strong>{$profile.format}</strong></li>{/if}
													</ul>
													{if $profile.license != 'fr' and $profile.license != 'cu'}
														{if $digitalCreditsCartStatus}<div>{$lang.credits} <input type="text" name="digitalCredits[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setCredits}"><p></p></div>{/if}
														{if $digitalCurrencyCartStatus}<div>{$lang.price} <input type="text" name="digitalPrice[{$profile.ds_id}]" style="width: 100px;" value="{$profile.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
													{/if}
													<div class="attachFileContainer">
														<p id="detachButton-{$profile.ds_id}" style="{if $profile.fileExists}display: block;{/if}">{$lang.fileAttached} {if $profile.fileExists}({$profile.filename}){/if} <input type="button" value="X" dspID="{$profile.ds_id}" class="deleteAttachFile"></p>
														<p id="attachButton-{$profile.ds_id}" style="{if !$profile.fileExists}display: block;{/if}"><input type="button" value="{$lang.attachFile}" dspID="{$profile.ds_id}" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									{/foreach}
								</div>
							{/if}
							
						</div>
					{/if}
					{if $member.membershipDetails.admin_prints and $printRows}
						<div class="dataGroupContainer" id="printsDGC">
							<br><br>{$lang.chooseItemsMes}<br>
							<div class="divTable contrItemSelectTable">
								{foreach $prints as $print}
									<div class="divTableRow {if !$print.selected}opac40{/if}">
										<div class="divTableCell"><input type="checkbox" name="print[]" value="{$print.print_id}" class="contrItem" id="contrPrint{$print.print_id}" {if $print.selected}checked="checked"{/if}></div>
										<div class="divTableCell">
											<label for="contrPrint{$print.print_id}">{$print.name}</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo" {if $print.selected}style="display: block"{/if}>
												<ul>
													{if $print.description}<li>{$lang.mediaLabelDesc}: <strong>{$print.description}</strong></li>{/if}
												</ul>
												{if $printCreditsCartStatus}<div>{$lang.credits} <input type="text" name="printCredits[{$print.print_id}]" style="width: 100px;" value="{$print.setCredits}"><p></p></div>{/if}
												{if $printCurrencyCartStatus}<div>{$lang.price} <input type="text" name="printPrice[{$print.print_id}]" style="width: 100px;" value="{$print.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								{/foreach}
							</div>
						</div>
					{/if}
					{if $member.membershipDetails.admin_products and $productRows}
						<div class="dataGroupContainer" id="productsDGC">
							<br><br>{$lang.chooseItemsMes}<br>
							<div class="divTable contrItemSelectTable">
								{foreach $products as $product}
									<div class="divTableRow {if !$product.selected}opac40{/if}">
										<div class="divTableCell"><input type="checkbox" name="product[]" value="{$product.prod_id}" class="contrItem" id="contrProd{$product.prod_id}" {if $product.selected}checked="checked"{/if}></div>
										<div class="divTableCell">
											<label for="contrProd{$product.prod_id}">{$product.name}</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo" {if $product.selected}style="display: block"{/if}>
												<ul>
													{if $product.description}<li>{$lang.mediaLabelDesc}: <strong>{$product.description}</strong></li>{/if}
												</ul>
												{if $prodCreditsCartStatus}<div>{$lang.credits} <input type="text" name="productCredits[{$product.prod_id}]" style="width: 100px;" value="{$product.setCredits}"><p></p></div>{/if}
												{if $prodCurrencyCartStatus}<div>{$lang.price} <input type="text" name="productPrice[{$product.prod_id}]" style="width: 100px;" value="{$product.setPrice}"> {$priCurrency.code}<p></p></div>{/if}
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								{/foreach}
							</div>
						</div>
					{/if}
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="{$lang.cancel}" class="closeImportWorkbox"> <input type="button" value="{$lang.save}" id="saveWorkboxForm"></div>
			</div>
		</div>
	{/if}
	
	{if $mode == 'cartAddNotes'}
		<div id="editWorkbox">
			<input type="hidden" name="cartItemID" value="{$cartItemID}">
			<h1>{$lang.notes}</h1>
			<p class="notice" style="display: none;" id="emailPasswordSent">{$lang.passwordSent}</p>
			<p class="notice" style="display: none;" id="emailPasswordFailed">{$lang.passwordFailed}</p>
			<div style="padding-bottom: 30px; padding-top: 6px;">
				<textarea style="width: 450px; height: 50px;" name="cartItemNotes">{$cartItemNotes}</textarea>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="{$lang.close}" class="closeWorkbox" /> <input type="button" value="{$lang.save}" id="saveWorkboxForm" /></div>
	{/if}

{/if}
</form>