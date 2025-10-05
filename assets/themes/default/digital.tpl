<script type="text/javascript" src="{$baseURL}/assets/javascript/digital.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
{if $digital.lic_purchase_type == 'rm'}<script type="text/javascript" src="{$baseURL}/assets/javascript/rights.managed.js"></script>{/if}

<form class="cleanForm" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="digital" id="type">
<input type="hidden" name="profileID" id="profileID" value="{$useID}">
<input type="hidden" name="id" id="id" value="{$useID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}
<!--<input type="hidden" name="customizeID" id="customizeID" value="{$digital.useCustomizeID}">-->
<input type="hidden" name="mediaID" id="mediaID" value="{$useMediaID}">
<input type="hidden" id="instantDownloadAvailable" name="instantDownloadAvailable" value="{if $digital.fileCheck.status or $digital.autoCreate}1{/if}">
<input type="hidden" id="loggedIn" name="loggedIn" value="{$loggedIn}">
{if !$digital.fileCheck.status and !$digital.autoCreate}<input type="hidden" name="action" value="emailForFile">{/if}
<input type="hidden" name="licenseType" id="licenseType" value="{$digital.lic_purchase_type}">

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<!--{$digital.downloadKey}-->
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			{if $media}
				<div class="divTableCell workboxLeftColumn">					
					<p id="mainShotContainer"><img src="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID size=300}"></p>
					<ul class="purchaseListDescription">
						<!--
						<li>Profile: {$id}</li>
						<li>Media: {$mediaID}</li>
						<li>Customize: {$customizeID}</li>
						-->
						{if $media.copyright}<li><strong>{$lang.mediaLabelCopyright}:</strong> {$media.copyright}</li>{/if}
						{if $media.usage_restrictions}<li><strong>{$lang.mediaLabelRestrictions}:</strong> {$media.usage_restrictions}</li>{/if}
						{if $media.model_release_status}<li><strong>{$lang.mediaLabelRelease}:</strong> {$lang.yes}</li>{/if}
						<li><strong>{$lang.instantDownload}:</strong> 
						{if $digital.fileCheck.status or $digital.autoCreate}
							<img src="{$imgPath}/small.check.1.png" class="fileCheckIcon">
						{else}
							<span class="notice" style="margin: 0; font-weight: normal; font-style: italic">{$lang.noInstantDownload}</span>
						{/if}
						</li>
					</ul>
				</div>
			{/if}
			<div class="divTableCell workboxRightColumn">
				<!--AC: {$digital.autoCreate}<br><br>-->
			
				{if $media.title}<h1>{$media.title}</h1>{/if}
				
				<p class="purchaseListDescription" style="margin-top: 16px; font-weight: bold;">{$digital.name}{if $digital.dsp_type == 'video' and $digital.hd}<img src="{$imgPath}/hd.png" style="vertical-align:top;">{/if}</p>
				
				{if $config.settings.display_license}<p class="purchaseListDescription">{$lang.license}: <strong>{$digital.licenseLang}</strong><br><span class="licenseDescriptionWB">{$digital.licenseDescLang}</span></p>{/if}
				{if $digital.width or $digital.height}<p class="purchaseListDescription">{$lang.resolution}: <strong>{$digital.width} x {$digital.height} px</strong> {if $digital.widthIC or $digital.heightIC}<em>( {$digital.widthIC} x {$digital.heightIC} @ {$config.dpiCalc} {$lang.dpi} )</em>{/if}</p>{/if}
				{if $digital.format}<p class="purchaseListDescription">{$lang.mediaLabelFormat}: <strong>{$digital.format}</strong></p>{/if}
				{if $digital.dsp_type == 'video'}
					<p class="purchaseListDescription">
						{if $digital.fps}{$lang.mediaLabelFPS}: <strong>{$digital.fps}</strong><br>{/if}
						{if $digital.running_time}{$lang.mediaLabelRunningTime}: <strong>{$digital.running_time}</strong> {$lang.seconds}<br>{/if}
					</p>
				{/if}
				<p class="purchaseListDescription">{$digital.description|truncate:200}</p>
				<p class="purchaseListDescription">{$lang.quantityAvailable}: <strong>{$digital.quantityText}</strong></p>
				
				{if $digital.lic_purchase_type == 'rm'}
				<input type="hidden" name="rmBasePrice" id="rmBasePrice" value="{$rmBasePrice}">
				<input type="hidden" name="rmBaseCredits" id="rmBaseCredits" value="{$rmBaseCredits}">				
				<input type="hidden" name="rmCreditsEnc" id="rmCreditsEnc" value="">
				<input type="hidden" name="rmPriceEnc" id="rmPriceEnc" value="">
				
				<div id="rmPricingCalculator">
					<h1>{$lang.pricingCalculator}</h1><br>
					{foreach $topLevelRM as $key => $rmGroup}
						<div id="rmContainer{$rmGroup.og_id}" class="rmContainer">
							<table>
								<tr>
									<td><strong>{$rmGroup.og_name}</strong></td>
									<td>
										<select name="rmGroup[{$rmGroup.og_id}]" licID="{$digital.license_id}">
											<option value="0"></option>
											{foreach $rmGroup.options as $optionKey => $option}
												<option value="{$option.op_id}" price="{$option.price}" credits="{$option.credits}" priceMod="{$option.price_mod}"  displayPrice="{$option.price}">{$option.op_name}</option>
											{/foreach}
										</select>
									</td>
								</tr>
							</table>
						</div>
					{/foreach}
				</div>
				{/if}
				{* Testing
				<div id="rmTotal" style="border: 1px solid #FF0000; padding: 10px;"></div>
				<div id="rmCredits" style="border: 1px solid #FF0000; padding: 10px;"></div>
				*}
				
				
				<!--
				SUB ROWS: {$subRows}<br>
				DOWNLOADS: {$downloadRows}<br>
				-->

				{if $digital.license == 'cu' and !$downloadRows and (!$subRows or !$subsAvailable)}
					<div><p class="notice" style="margin-left: 0; display: none;" id="contectForPricingSuccess">{$lang.thankRequest}</p></div>
					<div style="margin-top: 20px;" id="contactForPricing">
						{$lang.pleaseContactForm}:
						<input type="hidden" name="action" value="emailForQuote">
						<br><br>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.name}:</div>
								<div class="divTableCell"><input type="text" name="contactForm[name]" id="contactName" require="require" errorMessage="{$lang.required}" style="width: 250px;"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.email}:</div>
								<div class="divTableCell"><input type="text" name="contactForm[email]" id="contactEmail" require="require" errorMessage="{$lang.required}" style="width: 250px;"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.message}:</div>
								<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="contactForm[message]" id="contactMessage"></textarea></div>
							</div>
						</div>
					</div>
				{/if}
								
				{if !$downloadRows and $subRows and $subsAvailable and $digital.license != 'fr' and $digital.lic_purchase_type != 'rm'}
					<input type="hidden" name="downloadType" id="downloadType" value="sub">
					<div class="downloadSubs">
						<span style="">{$lang.downWithSub}:</span><br>
						<ul>
						{foreach $subsArray as $subscription}
							{if $subscription.available}
								<li class="subOptions" msub_id="{$subscription.msub_id}">
									<input type="radio" name="subscription" value="{$subscription.msub_id}" id="sub{$subscription.msub_id}" key="{$subscription.downloadKey}" {*if $subscription@first}checked="checked"{/if*}> 
									<p>
										<label for="sub{$subscription.msub_id}">{$subscription.item_name}</label><br>
										{$lang.expires}: <strong>{$subscription.expireDate}</strong><br>
										{$lang.downloadsRemainingB}: 
										<strong>
											{if $subscription.total_downloads}
												{$subscription.totalRemaining}
											{else}
												{$lang.unlimited}
											{/if}
											{if $subscription.perday} ({$subscription.todayRemaining} {$lang.today}){/if}
										</strong>
									</p>
								</li>
							{/if}
						{/foreach}
						</ul>
						<div id="requestDownloadSuccess" class="notice" style="display: none; margin: 0; float: left;">{$lang.requestDownloadSuccess}</div>
						<input type="button" value="{if $digital.fileCheck.status or $digital.autoCreate}{$lang.downloadUpper}{else}{$lang.requestDownload}{/if}" disabled="disabled" style="float: right; margin-top: 10px;" id="workboxDownload">
					</div>
				{/if}
				
				{if $digital.license == 'rf'}{/if}
			
			</div>
		</div>
	</div>
{/if}
<div class="workboxActionButtons">
	{if $downloadRows}
		<span class="notice" style="margin: 0" id="prevDownloadedMessage">{$lang.prevDownloaded}</span>
		<input type="hidden" name="downloadType" id="downloadType" value="prevDown">
		<input type="button" value="{$lang.downloadUpper}" id="workboxDownload" key="{$digital.downloadKey}">
	{else}
		{if !$downloadRows and $subRows and $subsAvailable and $digital.license != 'fr' and $digital.lic_purchase_type != 'rm'}
			{*
			<select style="vertical-align: middle;" id="downloadFromSub">
				<option value="0">{$lang.downWithSubscription}:</option>
				{foreach $subsArray as $subscription}
					{if $subscription.available}
						<option value="{$subscription.sub_id}">{$subscription.item_name} (Expires: {$subscription.expireDate} | Remaining Today: {$subscription.downloadsRemainingText})<!-- {$subscription.downloadsAvailable}/{$subscription.todayDownloads} --></option>
					{/if}
				{/foreach}
			</select>
			<input type="button" value="{$lang.downloadUpper}" disabled="disabled" style="height: 28px;" id="workboxAddToCart" class="colorButton">
			*}
		{else}
			{if ($digital.license == 'rf' or $digital.license == 'rm' or  $digital.license == 'ex' or  $digital.license == 'eu') and $cartStatus}
				{if $media and $digital.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$digital.price.display}</span>{if $digital.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
				{if $media and $digital.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$digital.credits}</span>{/if}
				{if !$noAccess and !$edit}<br><input type="button" value="{$lang.addToCart}" id="workboxAddToCart" class="colorButton">{/if}
			{/if}
			
			{if $digital.license == 'fr'}
				<input type="hidden" name="downloadType" id="downloadType" value="free">
				{if $digital.fileCheck.status or $digital.autoCreate}
					<input type="button" value="{$lang.downloadUpper}" id="workboxDownload" key="{$digital.downloadKey}">
				{else}
					<div id="requestDownloadSuccess" class="notice" style="display: none; margin: 0; float: left;">{$lang.requestDownloadSuccess}</div>
					<div style="float: left;" id="requestDownloadContainer"><input type="text" name="requestDownloadEmail" id="requestDownloadEmail" style="display: none;" value="{$lang.enterEmail}"> <input type="button" value="{$lang.requestDownload}" altText="{$lang.submit}" id="workboxRequestDownload"></div>
				{/if}
			{/if}
		
			{if $digital.license == 'cu'}{if !$noAccess}<br><input type="button" value="{$lang.submit}" id="workboxSubmit">{/if}{/if}			
		{/if}
	{/if}	
</div>
</form>