{if $tagRows}
	<ul class="tagList">
		{foreach $tagsArray as $tag}
			<li><a href="{linkto page="search.php?clearSearch=true&searchPhrase={$tag.keyword}"}">{$tag.keyword}</a></li>
		{/foreach}
	</ul>
{else}
	<p class="notice">{$lang.noTags}</p>
{/if}
