<div class="mediaContainer" style="width: {$config.settings.thumb_size}px" id="mediaContainer{$media.media_id}">
	{if $media.dsp_type == 'video'}<img src="{$imgPath}/dtype.video.png" class="dtypeIcon">{/if}
	<p class="mediaThumbContainer loader1Center" id="thumb{$media.media_id}" style="min-height: {$config.settings.thumbcrop_height}px;"><a href="{$media.linkto}"><img src="{if $media.thumbCachedLink}{$media.thumbCachedLink}{else}{mediaImage mediaID=$media.encryptedID type=thumb folderID=$media.encryptedFID seo=$media.seoName}{/if}" class="mediaThumb {if $config.settings.rollover_status}showHoverWindow{/if}" mediaID="{$media.encryptedID}" alt="{$media.title}"></a></p>							
	{*if $thumbMediaDetails or $media.showRating or $media.showLightbox*}
		{if $media.percentage}<div class="colorSwatchMC"><p class="colorSwatch" style="background-color: #{$media.hex}; cursor: auto" title='#{$media.hex}'></p><p style="margin: 9px 0 0 2px; float: left;">{$media.percentage}%</p></div>{/if}
		<!--width: {$media.thumb.originalWidth}-->
		<ul class="mediaContent">
			{foreach $thumbMediaDetails.{$media.media_id} as $detail}
				<li>
					{if $detail.value != ''}
						<span class="mediaDetailLabel mediaDetailLabel{$detail@key}">{$detail.lang}</span>: <span class="mediaDetailValue mediaDetailValue{$detail@key}">
						{if $detail@key == 'owner'}
							{if $detail.value.useID}
								<a href="{linkto page="contributors.php?id={$detail.value.useID}&seoName={$detail.value.seoName}"}" class="colorLink">{$detail.value.displayName}</a>
							{else}
								{$detail.value.displayName}
							{/if}
						{else}						
							{$detail.value|truncate:40}
						{/if}
						</span>
					{/if}
				</li>
			{/foreach}
			{if $media.showRating}
				<li>
					<p class="ratingStarsContainer {if $media.allowRating}starRating{/if}" mediaID="{$media@key}">
						{foreach $media.rating.stars as $stars}<img src="{$imgPath}/star.{$stars}.png" class="ratingStar" originalStatus="{$stars}">{/foreach}
						&nbsp;<span class="mediaDetailValue"><strong>{$media.rating.average}</strong>/{$config.RatingStars} ({$media.rating.votes} {$lang.votes})</span><br>
					</p>
				</li>
			{/if}
			<li>
				{if $media.showLightbox}<div><img src="{$imgPath}/lightbox.icon.{$media.inLightbox}.png" inLightbox="{$media.inLightbox}" lightboxItemID="{$media.lightboxItemID}" mediaID="{$media.media_id}" id="addToLightboxButton{$media.media_id}" class="mediaContainerIcon addToLightboxButton" title="{$lang.lightbox}"></div>{/if}
				{if $config.settings.email_friend and $config.settings.thumbDetailsEmail}<div><img src="{$imgPath}/email.icon.0.png" class="mediaContainerIcon emailToFriend" mediaID="{$media.useMediaID}" title="{$lang.email}"></div>{/if}
				{if $config.settings.thumbDetailsPackage}<div><img src="{$imgPath}/package.icon.0.png" class="mediaContainerIcon assignToPackageButton" mediaID="{$media.media_id}" title="{$lang.assignToPackage}"></div>{/if}
				{if $config.settings.thumbDetailsDownloads}
				<div>
					<div class="thumbDownloadContainer" id="thumbDownloadContainer{$media.media_id}"></div>
					<img src="{$imgPath}/download.icon.0.png" mediaID="{$media.media_id}" id="downloadMediaButton{$media.media_id}" class="mediaContainerIcon downloadMediaButton" title="{$lang.download}">
				</div>
				{/if}
			</li>
		</ul>
	{*/if*}
</div>