<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/media.details.js"></script>
	{if $config.settings.zoomonoff == 1}
		<script type="text/javascript" src="{$baseURL}/assets/javascript/imagelens/jquery.imageLens.js"></script>
		<script type="text/javascript" language="javascript">
			$(function()
			{
				$("#imagetozoom").imageLens({
					lensSize: {$config.settings.zoomlenssize},
					borderSize: {$config.settings.zoombordersize},
					imageSrc: "{if $media.zoomCachedLink}{$media.zoomCachedLink}{else}{mediaImage mediaID=$media.encryptedID type=sample folderID=$media.encryptedFID seo=$media.seoName size=1024}{/if}",
					borderColor: "#{$config.settings.zoombordercolor}"
				});
			});
		</script>
	{/if}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
			<div class="content" style="padding: 20px 0 0 0;">
				{if $noAccess}
					<p class="notice">{$lang.noAccess}</p>
				{else}
					{if $crumbs}<h1>{foreach $crumbs as $key => $crumb}<a href="{$crumb.linkto}">{$crumb.name}</a> {if !$crumb@last} &raquo; {/if}{/foreach}{if $media.details.title.value} &raquo; {$media.details.title.value}{/if}</h1>{/if}
					<input type="hidden" value="{$media.media_id}" id="mediaID">
					<div id="mediaPreviewContainer">
						{if $galleryMode}
							<div class="prevNext">
								{if $backButton}<input type="button" value="< {$lang.backUpper}" id="backButton" href="{$backButton.linkto}">{/if}
								{if $nextButtonID}<input type="button" value="{$lang.nextUpper} >" id="nextButton" href="{linkto page="media.details.php?mediaID={$nextButtonID}"}" style="float: right; margin-left: 2px;"><!--&raquo;-->{/if}
								{if $prevButtonID}<input type="button" value="< {$lang.prevUpper}" id="prevButton" href="{linkto page="media.details.php?mediaID={$prevButtonID}"}" style="float: right;">{/if}
							</div>
						{/if}
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell" id="mediaPreviewImage">
									{if $media.videoStatus}									
										<div id="hoverMediaContainer" class="hoverMediaContainer" style="width: {$config.settings.video_sample_width}px; height: {$config.settings.video_sample_height}px; background-image: none;"><p id="vidContainer"></p></div>
										<script type="text/javascript">
											jwplayer("vidContainer").setup(
											{
												'flashplayer': "{$baseURL}/assets/jwplayer/player.swf",
												'file': "{$media.videoInfo.url}",
												{if $config.settings.video_autoplay}'autostart': true,{/if}
												'type': 'video',
												'repeat': '{$config.settings.video_autorepeat}',
												'controlbar.position': '{$config.settings.video_controls}',
												'logo.file': '{$baseURL}/assets/watermarks/{$config.settings.vidpreview_wm}',
												'logo.hide': false,
												'logo.position': '{$config.settings.video_wmpos}',
												'stretching': '{$config.featuredVideoStretch}',
												'width': '100%',
												'height': '100%',
												'skin': '{$baseURL}/assets/jwplayer/skins/{$config.settings.video_skin}/{$config.settings.video_skin}.zip',
												'screencolor': '{$config.settings.video_bg_color}',
												'volume': 100,
												'modes': [
													{ 'type': {if $media.videoInfo.vidsample_extension == 'flv' or $config.forceFlashVideoPlayer}'flash', src: '{$baseURL}/assets/jwplayer/player.swf'{else}'html5'{/if} },
													{ 'type': 'download' }
												],
												events: {
													onComplete: function() { }
												}
											});
											
											{if $config.settings.video_autoresize}
												jwplayer("vidContainer").onMeta(function()
												{
													vidWindowResize("vidContainer");
												});
											{/if}
										</script>
									{else}
										<p class="previewContainer" style="min-width: {$media.previewWidth}px; width: {$media.previewWidth}px; height: {$media.previewHeight}px;"><img {if $config.settings.zoomonoff == 1}id="imagetozoom"{/if} src="{if $media.sampleCachedLink}{$media.sampleCachedLink}{else}{mediaImage mediaID=$media.encryptedID type=sample folderID=$media.encryptedFID seo=$media.seoName}{/if}" title="{$media.details.title.value}" alt="{$media.details.title.value}" style="width: {$media.previewWidth}px; height: {$media.previewHeight}px;"></p>
									{/if}
									<div style="margin-top: 4px;">
										<p style="float: left;">{$config.settings.sn_code}</p>
										{if $media.showLightbox}<p class="mediaPreviewContainerIcon addToLightboxButton" lightboxItemID="{$media.lightboxItemID}" mediaID="{$media.media_id}"><img src="{$imgPath}/lightbox.icon.{$media.inLightbox}.png" id="addToLightboxButton{$media.media_id}" inLightbox="{$media.inLightbox}"> {$lang.lightbox}</p>{/if}
										{if $config.settings.email_friend}<p class="mediaPreviewContainerIcon emailToFriend" mediaID="{$useMediaID}"><img src="{$imgPath}/email.icon.0.png"> {$lang.email}</p>{/if}										
										{if $packageRows}<p class="mediaPreviewContainerIcon assignToPackageButton" mediaID="{$media.media_id}"><img src="{$imgPath}/package.icon.0.png"> {$lang.assignToPackage}</p>{/if}
										<!--<p class="mediaPreviewContainerIcon clickToEnlarge" mediaID="{$useMediaID}"><img src="{$imgPath}/enlarge.icon.0.png"> Enlarge</p>-->
									</div>
								</div>
								<div class="divTableCell" style="width: 100%">
									<ul class="mediaDetailsList">
										{foreach $media.details as $detail}
											{if $detail.value != ''}
												<li>
													<span class="mediaDetailLabel mediaDetailLabel{$detail@key}" style="float: left;">{$detail.lang}:&nbsp;</span> 
													{if $detail@key == 'keywords'}
														{foreach $detail.value as $keyword}
															<a href="{linkto page="search.php?clearSearch=true&searchPhrase={$keyword}"}" class="colorLink">{$keyword}</a>{if !$keyword@last},{/if} 
														{/foreach}
													{elseif $detail@key == 'colorPalette'}
														{foreach $detail.value as $colorKey => $color}
															<p class="colorSwatch" style="background-color: #{$color.hex}" title='#{$color.hex}'></p>
														{/foreach}
													{elseif $detail@key == 'owner'}
														<span class="mediaDetailValue mediaDetailValue{$detail@key}">
															{if $detail.value.useID}
																<a href="{linkto page="contributors.php?id={$detail.value.useID}&seoName={$detail.value.seoName}"}" class="colorLink">
																{*if $detail.value.avatar}<img src="{memberAvatar memID={$detail.value.mem_id} size=20 crop=20}">{/if*}
																{$detail.value.displayName}
																</a>
															{else}
																{$detail.value.displayName}
															{/if}
														</span>
													{else}
														<span class="mediaDetailValue mediaDetailValue{$detail@key}">{$detail.value}</span>
													{/if}
												</li>
											{/if}									
										{/foreach}
										{if $media.showRating}
											<li style="padding-top: 15px; clear: both;">
												<p class="{if $media.allowRating}starRating{/if}" mediaID="{$media.media_id}">
													{foreach $media.rating.stars as $stars}<img src="{$imgPath}/star.{$stars}.png" class="ratingStar" originalStatus="{$stars}">{/foreach}
													&nbsp;<span class="mediaDetailValue"><strong>{$media.rating.average}</strong>/{$config.RatingStars} ({$media.rating.votes} {$lang.votes})</span><br>
												</p>
											</li>
										{/if}
										{if $media.latitude and $media.longitude}
											<li style="padding-top: 15px; clear: both;">
												<span class="mediaDetailLabel mediaDetailLabelLocation" style="float: left;">{$lang.location}:&nbsp;</span><br>
												<p>
													<a href='http://maps.google.com/maps?q={$media.latitude},{$media.longitude}' target='_blank' border='0'>
													<img src='http://maps.googleapis.com/maps/api/staticmap?center={$media.latitude},{$media.longitude}&zoom={$config.settings.gpszoom}&size={$config.settings.gpswidth}x{$config.settings.gpsheight}&maptype={$config.settings.gpsmaptype}&sensor=false&markers=color:{$config.settings.gpscolor}%7C{$media.latitude},{$media.longitude}'>
													</a>
												</p>
											</li>
										{/if}
									</ul>
								</div>
							</div>	
						</div>
					</div>
					
					<ul id="mediaPurchaseTabsContainer">
						{if $digitalRows}<li container="purchaseDigitalDownloads">{$lang.digitalDownloads}</li>{/if}
						{if $printRows}<li container="purchasePrints">{$lang.prints}</li>{/if}
						{if $productRows}<li container="purchaseProducts">{$lang.products}</li>{/if}
						{if $collectionRows}<li container="purchaseCollections">{$lang.collections}</li>{/if}
						{if $packageRows}<li container="purchasePackages">{$lang.packages}</li>{/if}
					</ul>
					
					{if $digitalRows}
						<div class="mediaPurchaseContainers" id="purchaseDigitalDownloads" style="display: block">
							<!--{$digitalRows} purchaseDigitalDownloads <input type="button" value="Download" onclick="workbox({ 'page' : 'digital.php' });">-->
							<div class="divTable" style="width: 100%">
								{foreach $digitalsArray as $digital}
									<div class="divTableRow">
										<div class="divTableCell" style="width: 100%;">
											<h2><a href="{$digital.linkto}" class="workboxLink workboxLinkAttach">{$digital.name}</a>{if $digital.dsp_type == 'video' and $digital.hd}<img src="{$imgPath}/hd.png" style="vertical-align:top;">{/if}</h2>
											<!--{$digital.customizeID} - {$digital.customized}<br>-->
											{if $config.settings.display_license}<p class="purchaseListDescription">{$lang.license}: <strong>{$digital.licenseLang}</strong> {if $digital.attachlicense > 0}<a href="{$baseURL}/content.php?id={$digital.attachlicense}">{$lang.moreInfoPlus}</a>{/if}</p>{/if}
											{if $digital.width or $digital.height}<p class="purchaseListDescription">Resolution: <strong>{$digital.width} x {$digital.height} {$lang.px}</strong> {if $digital.widthIC or $digital.heightIC}<em>( {$digital.widthIC} x {$digital.heightIC} @ {$config.dpiCalc} {$lang.dpi} )</em>{/if}</p>{/if}
											<!--{$digital.quantity}<br>-->
											{if $digital.format}<p class="purchaseListDescription">{$lang.mediaLabelFormat}: <strong>{$digital.format}</strong></p>{/if}
											{if $digital.dsp_type == 'video'}
												<p class="purchaseListDescription">
													{if $digital.fps}{$lang.mediaLabelFPS}: <strong>{$digital.fps}</strong><br>{/if}
													{if $digital.running_time}{$lang.mediaLabelRunningTime}: <strong>{$digital.running_time}</strong> {$lang.seconds}<br>{/if}
												</p>
											{/if}
											<p class="purchaseListDescription">{$digital.description|truncate:200}</p>
											{if $digital.license == 'rf' or $digital.license == 'ex' or $digital.license == 'eu'}<p class="purchaseListPrice">{if $digital.price}<span class="price">{$digital.price.display}</span>{if $digital.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $digital.credits and $digital.price} {$lang.priceCreditSep} {/if}{if $digital.credits}<span class="price">{$digital.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>{/if}
										</div>
										<div class="divTableCell" style="text-align: right; vertical-align: top; white-space: nowrap;"><a href="{$digital.linkto}" class="colorButtonLink workboxLink workboxLinkAttach">{$lang.downloadUpper}</a></div>
									</div>
								{/foreach}
							</div>	
						</div>
					{/if}
					
					{* Prints Area *}
					{if $printRows}
						<div class="mediaPurchaseContainers" id="purchasePrints">
							<div class="divTable" style="width: 100%">
								{foreach $prints as $print}
									<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top">{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=75 crop=50}">{/if}</div>
										<div class="divTableCell" style="width: 100%;">
											<h2><a href="{$print.linkto}" class="workboxLink workboxLinkAttach">{$print.name}</a></h2>
											<p class="purchaseListDescription">{$print.description|truncate:200}</p>
											<p class="purchaseListPrice">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $print.credits and $print.price} {$lang.priceCreditSep} {/if}{if $print.credits}<span class="price">{$print.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
										{if $cartStatus}<div class="divTableCell" style="text-align: right; vertical-align: top; white-space: nowrap;"><a href="{$print.addToCartLink}" class="colorButtonLink{if !$print.directToCart} workboxLink workboxLinkAttach{elseif $config.settings.minicart} miniCartLink{/if}" rel="nofollow">{$lang.addToCart}</a></div>{/if}
									</div>
								{/foreach}
							</div>
						</div>
					{/if}
					
					{* Products Area *}
					{if $productRows}
						<div class="mediaPurchaseContainers" id="purchaseProducts">						
							<div class="divTable" style="width: 100%">
								{foreach $products as $product}
									<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top">{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=75 crop=50}">{/if}</div>
										<div class="divTableCell" style="width: 100%;">
											<h2><a href="{$product.linkto}" class="workboxLink workboxLinkAttach">{$product.name}</a></h2>
											<p class="purchaseListDescription">{$product.description|truncate:200}</p>
											<p class="purchaseListPrice">{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $product.credits and $product.price} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
										{if $cartStatus}<div class="divTableCell" style="text-align: right; vertical-align: top; white-space: nowrap;"><a href="{$product.addToCartLink}" class="colorButtonLink{if !$product.directToCart} workboxLink workboxLinkAttach{elseif $config.settings.minicart} miniCartLink{/if}" rel="nofollow">{$lang.addToCart}</a></div>{/if}
									</div>
								{/foreach}
							</div>						
						</div>
					{/if}
					
					{* Collections Area *}
					{if $collectionRows}
						<div class="mediaPurchaseContainers" id="purchaseCollections">						
							<p class="mpcDescription">{$lang.mediaIncludedInColl}</p>
							<div class="divTable" style="width: 100%">
								{foreach $collections as $collection}
									<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top">{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=75 crop=50}">{/if}</div>
										<div class="divTableCell" style="width: 100%;">
											<h2><a href="{$collection.linkto}" class="workboxLink workboxLinkAttach">{$collection.name}</a></h2>
											<p class="purchaseListDescription">{$collection.description|truncate:200}</p>
											<p class="purchaseListPrice">{if $collection.price}<span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $collection.credits and $collection.price} {$lang.priceCreditSep} {/if}{if $collection.credits}<span class="price">{$collection.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
										<div class="divTableCell" style="text-align: right; vertical-align: top; white-space: nowrap;"><a href="{$collection.viewCollectionLink}" class="buttonLink">{$lang.viewCollection}</a>{if $cartStatus} <a href="{$collection.addToCartLink}" class="colorButtonLink {if $config.settings.minicart}miniCartLink{/if}" rel="nofollow">{$lang.addToCart}</a>{/if}</div>
									</div>
								{/foreach}
							</div>						
						</div>
					{/if}
					
					{* Packages Area *}
					{if $packageRows}
						<div class="mediaPurchaseContainers" id="purchasePackages">
							<div class="divTable" style="width: 100%">
								{foreach $packages as $package}
									<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=75 crop=50}">{/if}</div>
										<div class="divTableCell" style="width: 100%;">
											<h2><a href="{$package.linkto}" class="workboxLink workboxLinkAttach">{$package.name}</a></h2>
											<p class="purchaseListDescription">{$package.description|truncate:200}</p>
											<p class="purchaseListPrice">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $package.credits and $package.price} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
										{if $cartStatus}<div class="divTableCell" style="text-align: right; vertical-align: top; white-space: nowrap;"><a href="{$package.linkto}" class="colorButtonLink workboxLink workboxLinkAttach" rel="nofollow">{$lang.addToCart}</a></div>{/if}
									</div>
								{/foreach}
							</div>
						</div>
					{/if}
					
					<div id="mediaExtraInfoDG" style="margin-bottom: 30px;">
						<ul class="tabs">
							{if $config.settings.related_media}<li container="similarMediaDGC">{$lang.similarMedia}</li>{/if}
							{if $commentSystem}<li container="commentsDGC">{$lang.comments}</li>{/if}
							{if $taggingSystem}<li container="taggingDGC">{$lang.memberTags}</li>{/if}
							{if $iptcRows}<li container="iptcDGC">{$lang.iptc}</li>{/if}
							{if $exifRows}<li container="exifDGC">{$lang.exif}</li>{/if}
							{if $config.settings.share}<li container="shareMedia">{$lang.share}</li>{/if}
						</ul>
						{if $config.settings.related_media}
							<div class="dataGroupContainer" id="similarMediaDGC">
								<div id="mediaSimilarPhotos" mediaID="{$useMediaID}" galleryID="{$useGalleryID}" galleryMode="{$galleryMode}" style="overflow: auto; min-height: 30px;"></div>
							</div>
						{/if}
						{if $commentSystem}
							<div class="dataGroupContainer" id="commentsDGC">
								<p class="notice" id="newCommentMessage"></p>
								{if $member.allowCommenting}
									<form action="" id="newCommentForm" method="post">
										<input type="hidden" name="action" value="newComment">
										<input type="hidden" name="formKey" value="{$formKey}">						
										<div id="newCommentContainer">
											{$lang.addNewComment} <img src="{$imgPath}/down.arrow.png"><br>								
											<textarea class="commentTextarea" name="newComment" id="newComment"></textarea><br>
											<input type="submit" value="{$lang.submit}" style="float: right;">
										</div>
									</form>
								{/if}
								<div id="mediaComments" mediaID="{$useMediaID}" style="min-height: 30px;"></div>
							</div>
						{/if}
						{if $taggingSystem}
							<div class="dataGroupContainer" id="taggingDGC" style="overflow: auto;">
								{if $member.allowTagging}
									<div style="float: right; color: #666; font-weight: bold; font-size: 11px; float: right; margin-top: 10px;">
										{$lang.addTag}: <br>
										<form action="" id="newTagForm" method="post">
											<input type="hidden" name="action" value="newTag">
											<input type="hidden" name="formKey" value="{$formKey}">
											<input type="text" style="width: 150px;" name="newTag" id="newTag"> 
											<input type="submit" value="{$lang.submit}" style="height: 24px; margin-top: -4px; vertical-align: middle">
										</form>
									</div>
								{/if}
								<p class="notice" id="newTagMessage"></p>
								<div id="mediaTags" mediaID="{$useMediaID}" style="min-height: 30px;"></div>
							</div>
						{/if}
						
						{if $iptcRows}
							<div class="dataGroupContainer" id="iptcDGC">
								<ul style="color: #999">
									{foreach $media.iptc as $iptc}
										{if $iptc}
											<li>
												<span class="mediaDetailLabel mediaDetailLabel{$iptc@key}">{$lang.{$iptc@key}}</span>: 
												<span class="mediaDetailValue mediaDetailValue{$iptc@key}">{$iptc}</span>
											</li>
										{/if}
									{/foreach}
								</ul>
							</div>
						{/if}
						
						{if $exifRows}
							<div class="dataGroupContainer" id="exifDGC">
								<ul style="color: #999">
									{foreach $media.exif as $exif}
										{if $exif}
											<li>
												<span class="mediaDetailLabel mediaDetailLabel{$exif@key}">{$lang.{$exif@key}}</span>: 
												<span class="mediaDetailValue mediaDetailValue{$exif@key}">{$exif}</span>
											</li>
										{/if}
									{/foreach}
								</ul>
							</div>
						{/if}
						
						{* Share Area *}
						{if $config.settings.share}
						<div class="dataGroupContainer" id="shareMedia">
							<div class="divTable" style="width: 100%">
								<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top"></div>
										<div class="divTableCell" style="width: 100%;">
											<h2>{$lang.bbcode}</h2>
											<p class="purchaseListDescription"><textarea name="bbcode" rows="4" cols="100">[url={$fullURL}][img]{mediaImage mediaID=$media.encryptedID type=sample folderID=$media.encryptedFID seo=$media.seoName}[/img][/url]</textarea></p>
										</div>
								</div>
								<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top"></div>
										<div class="divTableCell" style="width: 100%;padding-top:10px">
											<h2>{$lang.html}</h2>
											<p class="purchaseListDescription"><textarea name="html" rows="4" cols="100"><a href="{$fullURL}" title="{$media.title}" alt="{$media.title}"><img src="{mediaImage mediaID=$media.encryptedID type=sample folderID=$media.encryptedFID seo=$media.seoName}" title="{$media.title}" alt="{$media.title}" border="0"></a></textarea></p>
										</div>
								</div>
								<div class="divTableRow">
										<div class="divTableCell" style="vertical-align: top"></div>
										<div class="divTableCell" style="width: 100%;padding-top:10px">
											<h2>{$lang.link}</h2>
											<p class="purchaseListDescription"><input type="textbox" name="linkto" value="{$fullURL}" style="width:609px"></p>
										</div>
								</div>
							</div>
						</div>
						{/if}
					</div>
				{/if}				
			</div>
		</div>
		
		{* Debug Area *}
		{if $debugMode}
			{debugOutput value=$media title='media'}
		{/if}
		
		{include file='footer.tpl'}
    </div>
</body>
</html>