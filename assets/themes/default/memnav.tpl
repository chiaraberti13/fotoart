<div id="memberNav">
	<div class="subNavFeaturedBox" style="padding: 20px;">
		<p style="line-height: 1.5;">
			{*if $member.avatar}{/if*}
			{if $member.membershipDetails.avatar}<img src="{memberAvatar memID=$member.mem_id size=100}" class="memberAvatar" id="memNavAvatar">{/if}
			{$lang.loggedInAs}:<br>
			<strong><a href="{linkto page="members.php"}">{$member.f_name} {$member.l_name}</a></strong>
			<br><br>
			<p style="float: right"><a href="{linkto page="account.php"}" class="buttonLink">{$lang.editProfile}</a></p>
		</p>
	</div>
	
	{if $creditSystem}
		<div class="subNavFeaturedBox yourCredits" style="padding: 20px;">
			<div>{$lang.yourCredits}</div>
			<div class="myCredits">{if $member.credits}{$member.credits}{else}0{/if}</div><div style="float: right"><a href="{linkto page="featured.php?mode=credits"}" class="buttonLink">{$lang.purchaseCredits}</a></div>
		</div>
	{/if}
	
	{if $member.membershipDetails.allow_selling or $member.membershipDetails.allow_uploads}
	<div class="subNavFeaturedBox">
		<!--<h1>{$lang.contributors}</h1>-->
		<ul id="contrSubNav">
			<li>
				<a href="{linkto page="contributor.my.media.php?mode=all"}">{$lang.contMedia}</a>
				{if $pageID == 'contributorMyMedia'}
					<ul>
						{if $member.membershipDetails.allow_selling}<li><a href="{linkto page="contributor.my.media.php?mode=pending"}" class="lighterLink">{$lang.approvalStatus0}</a></li>{/if}
						{if $member.membershipDetails.allow_selling}<li><a href="{linkto page="contributor.my.media.php?mode=failed"}" class="lighterLink">{$lang.approvalStatus2}</a></li>{/if}
						{*if $member.membershipDetails.collections}<li><a href="{linkto page="contributor.my.media.php"}" class="lighterLink">{$lang.collections}</a></li>{/if*}
						{if $member.membershipDetails.personal_galleries or $member.membershipDetails.admin_galleries}<li><a href="{linkto page="contributor.my.media.php?mode=orphaned"}" class="lighterLink">{$lang.orphanedMedia}</a></li>{/if}
						<li><a href="{linkto page="contributor.my.media.php?mode=last"}" class="lighterLink">{$lang.lastBatch}</a></li>
						{if $member.membershipDetails.personal_galleries}
							{foreach $contrAlbums as $album}
								<li class="contrGalleries"><a href="{linkto page="contributor.my.media.php?mode=album&albumID={$album.ugallery_id}"}">{$album.name}</a></li>{*$album.linkto*}
							{/foreach}
							<li><a href="workbox.php?mode=contrNewAlbum" class="contrNewAlbum lighterLink">{$lang.newAlbum}...</a></li>
						{/if}
					</ul>
				{/if}
			</li>
			<li><a href="{linkto page="contributor.add.media.php"}">{$lang.contUploadNewMedia}</a></li>
			{if $member.membershipDetails.allow_selling}<li><a href="{linkto page="contributor.sales.php"}">{$lang.contViewSales}</a></li>{/if}
		</ul>
	</div>
	{/if}
	
	<div class="subNavFeaturedBox">
		<ul>
			<li><a href="{linkto page="members.php"}">{$lang.myAccount}</a></li>
			{if $cartStatus}<li><a href="{linkto page="orders.php"}">{$lang.orders}</a></li>{/if}
			<li><a href="{linkto page="download.history.php"}">{$lang.downloadHistory}</a></li>
			<li><a href="{linkto page="bills.php"}">{$lang.bills}</a></li>
			<li><a href="{linkto page="account.php"}">{$lang.accountInfo}</a></li>
			{if $lightboxSystem}<li><a href="{linkto page="lightboxes.php"}">{$lang.lightboxes}</a></li>{/if}
			{if $ticketSystem}<li><a href="{linkto page="tickets.php"}">{$lang.supportTickets}</a></li>{/if}
			{if $config.settings.subscriptions}<li><a href="{linkto page="member.subs.php"}">{$lang.subscriptions}</a></li>{/if}
			<li><a href="{linkto page="login.php?cmd=logout"}">{$lang.logout}</a></li>
		</ul>
	</div>
</div>