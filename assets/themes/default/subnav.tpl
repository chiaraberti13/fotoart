<div id="galleryList">
	{*foreach $galleriesData as $gallery}
		{$gallery.name}{if $gallery.password} (locked){/if}--<br>
	{/foreach*}	
	<div id="treeMenu"></div>
</div>
<!--
{
    "data" : "node_title", // omit `attr` if not needed; the `attr` object gets passed to the jQuery `attr` function
    "attr" : { "id" : "node_identificator", "some-other-attribute" : "attribute_value" },
    "state" : "closed", // or "open", defaults to "closed"
    "children" : [ /* an array of child nodes objects */ ]
}
-->


<div class="subNavFeaturedBox">
	
	{if $galleriesData}
		<div id="galleryPicker">
			<a href="{linkto page="gallery.php?mode=gallery&id=0"}">{$lang.galleries}</a> <img src="{$imgPath}/gallery.arrow.png" id="galleryListToggle">
		</div>
	{/if}
	
	{if $contentPages|@count > 0}
		<ul id="customPages">
			{foreach $contentPages as $content}
				<li>
				{if $content.linked}
				<a href="{$content.linked}" target="_blank">{$content.name}</a>
				{else}
				<a href="{linkto page="content.php?id={$content.content_id}"}">{$content.name}</a>
				{/if}
				</li>
			{/foreach}
		</ul>
	{/if}
	
</div>

{foreach $contentBlocks as $content}
	{if $content.specType == 'sncb'}
		<div class="subNavFeaturedBox cbSubnav">
			<h1>{$content.name}</h1>
			<div>{$content.content}</div>
		</div>
	{/if}
{/foreach}

{if $pageID != 'homepage'}
	{* Featured Contributors Area *}
	{if $featuredContributors}
		<div class="subNavFeaturedBox" id="subNavContributors">
			<img src="{$imgPath}/feature.box.c.icon.png" class="fbHero opac20">
			<h1>{$lang.showcasedContributors}</h1>
			<div class="divTable" style="margin-bottom: 20px;">
			{foreach $featuredContributors as $contributor}
				<div class="divTableRow">
					<div class="divTableCell"><img src="{memberAvatar memID=$contributor.mem_id size=30 crop=30}"></div>
					<div class="divTableCell"><a href="{linkto page="contributors.php?id={$contributor.useID}&seoName={$contributor.seoName}"}">{$contributor.display_name}</a></div>
				</div>
			{/foreach}
			</div>
		</div>						
	{/if}
	
	{* Members Online Area *}
	{if $config.settings.members_online}
		<div class="subNavFeaturedBox" id="subNavOnlineMembers">
			<img src="{$imgPath}/feature.box.b.icon.png" class="fbHero opac20">
			<h1>{$lang.membersOnline}</h1>
			<ul>
				{if $membersOnline}
					{foreach $membersOnline as $member}
						<li>{$member.display_name} <span class="time">({$member.lastSeen} {$lang.minutesAgo})</span></li>
					{/foreach}
				{else}
					<li>{$lang.none}</li>
				{/if}
			</ul>
		</div>
	{/if}
	
	{* Site Stats Area *}
	{if $siteStats}
		<div class="subNavStatsBox" id="subNavStats">
			<img src="{$imgPath}/feature.box.a.icon.png" class="fbHero opac20">
			<h1>{$lang.siteStats}</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell">{$lang.members}:</div>
					<div class="divTableCell"><strong>{$siteStats.members}</strong></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell">{$lang.media}:</div>
					<div class="divTableCell"><strong>{$siteStats.media}</strong></div>
				</div>
				{*if $siteStats.contributors}
					<div class="divTableRow">
						<div class="divTableCell">{$lang.contributors}:</div>
						<div class="divTableCell"><strong>200</strong></div>
					</div>
				{/if*}
				<div class="divTableRow">
					<div class="divTableCell">{$lang.visits}:</div>
					<div class="divTableCell"><strong>{$siteStats.visits}</strong></div>
				</div>
			</div>
		</div>
	{/if}
	
{/if}