<div id="headerSearchBox">
	{* Header Search Box Area *}
	{if $config.settings.search}
		<form action="{linkto page="search.php"}" method="get" id="searchFormTest">
		<input type="hidden" name="clearSearch" value="true">
		<strong>Search:</strong> &nbsp; <input type="text" id="searchPhrase" name="searchPhrase" class="searchInputBox" value="{$lang.enterKeywords}">{if $currentGallery.gallery_id}<p class="currentGalleryOnly"><input type="checkbox" name="galleries" id="searchCurrentGallery" value="{$currentGallery.gallery_id}" checked="checked"> <label for="searchCurrentGallery">{$lang.curGalleryOnly}</label></p>{/if}<input type="button" value="{$lang.go}" class="eyeGlass">  <span><a href="{linkto page='search.php'}">{$lang.advancedSearch}</a></span>
		</form>
	{/if}
</div>