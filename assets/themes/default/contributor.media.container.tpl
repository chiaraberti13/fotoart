<div class="mediaContainer" style="width: {$config.settings.thumb_size}px" id="mediaContainer{$media.media_id}">
	{if $media.dsp_type == 'video'}<img src="{$imgPath}/dtype.video.png" class="dtypeIcon">{/if}
	<p class="mediaThumbContainer loader1Center" id="thumb{$media.media_id}" style="min-height: {$config.settings.thumbcrop_height}px;"><a href="{$media.linkto}"><img src="{if $media.thumbCachedLink}{$media.thumbCachedLink}{else}{mediaImage mediaID=$media.encryptedID type=thumb folderID=$media.encryptedFID seo=$media.seoName}{/if}" class="mediaThumb {if $config.settings.rollover_status}showHoverWindow{/if}" mediaID="{$media.encryptedID}" alt="{$media.title}"></a></p>							
	{if $media.approval_status != 1}
		<div class="approvalStatusBar approvalStatus{$media.approval_status}">{if $media.approval_status == 2}<a href="workbox.php?mode=contrFailedApproval&id={$media.media_id}" class="approvalMessage">{$lang.{$media.approvalStatusLang}}</a>{else}{$lang.{$media.approvalStatusLang}}{/if}</div>
	{/if}
	<ul class="mediaContent">
		{foreach $thumbMediaDetails.{$media.media_id} as $detail}
			<li>
				{if $detail.value != '' and $detail@key != 'owner'}
					<span class="mediaDetailLabel mediaDetailLabel{$detail@key}">{$detail.lang}</span>: <span class="mediaDetailValue mediaDetailValue{$detail@key}">{$detail.value|truncate:40}</span>
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
		<li style="width: {$config.settings.thumb_size-10}px;">
			{if $member.membershipDetails.editing}<img src="{$imgPath}/contr.edit.png" class="mediaContainerIcon contrMediaEdit" mediaID="{$media.encryptedID}" title="{$lang.editDetails}" style="float: left; height: 14px;">{/if}
			{if $member.membershipDetails.deleting}<img src="{$imgPath}/contr.delete.png" class="mediaContainerIcon contrMediaDelete" mediaID="{$media.encryptedID}" title="{$lang.deleteMedia}" style="float: left; height: 14px;">{/if}
			{if $media.showLightbox}<img src="{$imgPath}/lightbox.icon.{$media.inLightbox}.png" inLightbox="{$media.inLightbox}" lightboxItemID="{$media.lightboxItemID}" mediaID="{$media.media_id}" id="addToLightboxButton{$media.media_id}" class="mediaContainerIcon addToLightboxButton" title="{$lang.lightbox}">{/if}
			{if $config.settings.email_friend and $config.settings.thumbDetailsEmail}<img src="{$imgPath}/email.icon.0.png" class="mediaContainerIcon emailToFriend" mediaID="{$media.useMediaID}" title="{$lang.email}">{/if}
			{if $config.settings.thumbDetailsPackage}<img src="{$imgPath}/package.icon.0.png" class="mediaContainerIcon assignToPackageButton" mediaID="{$media.media_id}" title="{$lang.assignToPackage}">{/if}
		</li>
	</ul>
</div>