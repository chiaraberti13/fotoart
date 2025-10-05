<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/colorpicker/js/colorpicker.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/search.js"></script>
	<script>
		var enterKeywords = '{$lang.enterKeywords}';
		var requireSearchKeyword = '{$config.requireSearchKeyword}';
		var searchFormHex = '{$searchForm.hex}';
		
		$(function()
		{
			//$('.headerSearchBox').hide();
		});
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<form action="search.php" method="get" id="searchForm">
		<input type="hidden" name="postSearchForm" id="postSearchForm" value="1">
		<input type="hidden" name="keywordsExist" id="keywordsExist" value="{if $searchForm.keywords}1{else}0{/if}">
		
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					
					<div class="searchOptionsColumn">
						<h1>{$lang.currentSearch}</h1>
						
						<div style="padding: 0 14px 0 14px; margin-bottom: 20px;">
							<div class="currentSearchBox">
							{if $searchForm.inSearch}
								<ul>
									<li>
										<strong>{$lang.searchKeywords}:</strong>
										{if $searchForm.keywords}
											{foreach $searchForm.keywords as $key => $value}
												<ul class="keywordQueries">
													<li>
														{if !$value@first}<a href="{linkto page="search.php?clear=searchPhrase&batch=$key"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{/if}
														{if $value.connector}<span class="searchConnector">{$lang.{$value.connector}} ( </span>{/if}
														{foreach $value.displayWords as $key => $keyword}
															<span>{$keyword}{if !$keyword@last} + {/if}</span>
														{/foreach}
														{if $value.connector}<span class="searchConnector">)</span>{/if}
													</li>
												</ul>
											{/foreach}
										{else}
											<span>{$lang.none}</span>
										{/if}
									</li>
									{if $mediaTypesRows and $config.settings.search_media_types}<li><strong>{$lang.searchHeaderType}:</strong> <span>{if $searchForm.mediaTypes}<a href="{linkto page="search.php?clear=mediaTypes"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{foreach $searchForm.mediaTypes as $mediaTypeID}{$mediaTypes.$mediaTypeID.name}{if !$mediaTypeID@last} + {/if}{/foreach}{else}{$lang.searchAll}{/if}</span></li>{/if}
									{if $config.settings.search_fields}<li><strong>{$lang.searchHeaderFields}:</strong> <span>{if $searchForm.fields}<a href="{linkto page="search.php?clear=fields"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{foreach $searchForm.fields as $key => $name}{$name}{if !$name@last} + {/if}{/foreach}{else}{$lang.searchAll}{/if}</span></li>{/if}
									{if $config.settings.search_orientation}<li><strong>{$lang.searchHeaderOrientation}:</strong> <span>{if $searchForm.orientations}<a href="{linkto page="search.php?clear=orientations"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{foreach $searchForm.orientations as $key => $name}{$name}{if !$name@last} + {/if}{/foreach}{else}{$lang.searchAll}{/if}</span></li>{/if}
									{if $config.settings.search_dates}<li><strong>{$lang.dateRange}:</strong> <span>{if $searchForm.searchDate.dateRangeSearch}<a href="{linkto page="search.php?clear=dates"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a><br>{$fromDateText} - {$toDateText}{else}{$lang.searchAll}{/if}</span></li>{/if}
									{if $config.settings.search_license_type}<li><strong>{$lang.license}:</strong> <span>{if $searchForm.licenses}<a href="{linkto page="search.php?clear=licenses"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{foreach $searchForm.licenses as $key => $licenseID}{$licensesList.{$licenseID}.licenseLang}{if !$licenseID@last} + {/if}{/foreach}{else}{$lang.searchAll}{/if}</span></li>{/if}
									{if $config.settings.search_color}<li><strong>{$lang.searchHeaderColor}:</strong> {if $searchForm.hex}<a href="{linkto page="search.php?clear=colors"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a><span class="colorSearchSpan" style="background-color: #{$searchForm.hex}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>{else}<span>{$lang.searchAll}</span>{/if}</li>{/if}
									{if $config.settings.search_galleries}<li><strong>{$lang.searchHeaderGalleries}:</strong> <span>{if $searchForm.galleries}<a href="{linkto page="search.php?clear=galleries"}"><img src="{$imgPath}/close.button.png" class="searchCloseButton"></a>{foreach $searchForm.galleries as $key => $galleryID}{$galleriesData.$galleryID.name}{if !$galleryID@last} + {/if}{/foreach}{else}{$lang.searchAll}{/if}</span></li>{/if}
									<li><a href="{linkto page="search.php?clearSearch=true"}">{$lang.searchClear}</a></li>
								</ul>
							{else}
								{$lang.searchStart}
							{/if}
							</div>
						</div>
						
						{if !$searchForm.inSearch or $mediaRows}
						
							{if $searchForm.inSearch}<h1>{$lang.searchNarrow}</h1>{/if}
							
							<div style="padding: 0 14px 14px 14px;">
								<h2>{$lang.searchHeaderKeywords}</h2>
								<div style="position: relative">
									<input type="text" name="searchPhrase" id="searchPhrase2" value="{*$lang.enterKeywords*}" class="searchInputBox" style="width: 95%;">									
									<p class="searchMatchTerms"><label for="exactMatch">{$lang.exactMatch}</label> <input type="checkbox" name="exactMatch" value="1" id="exactMatch"></p>
								</div>
							
								{if $config.settings.search_fields}
									<h2>{$lang.searchHeaderFields}</h2>								
									<ul>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="fields[mediaID]" value="{$lang.id}" id="fieldMediaID" {if $searchForm.fields.mediaID}checked="checked"{/if}> <label for="fieldMediaID">{$lang.id}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="fields[keywords]" value="{$lang.searchKeywords}" id="fieldKeywords" {if $searchForm.fields.keywords}checked="checked"{/if}> <label for="fieldKeywords">{$lang.searchKeywords}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="fields[title]" value="{$lang.searchTitle}" id="fieldTitle" {if $searchForm.fields.title}checked="checked"{/if}> <label for="fieldTitle">{$lang.searchTitle}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="fields[description]" value="{$lang.searchDescription}" id="fieldDescription" {if $searchForm.fields.description}checked="checked"{/if}> <label for="fieldDescription">{$lang.searchDescription}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="fields[filename]" value="{$lang.searchFilename}" id="fieldFilename" {if $searchForm.fields.filename}checked="checked"{/if}> <label for="fieldFilename">{$lang.searchFilename}</label></li>
									</ul>
								{/if}
								
								{if $mediaTypesRows and $config.settings.search_media_types}
									<h2>{$lang.searchHeaderType}</h2>								
									<ul>
										{foreach $mediaTypes as $mediaType}
											<li style="margin-bottom: 6px;"><input type="checkbox" name="mediaTypes[{$mediaType.mt_id}]" id="mediaType{$mediaType.mt_id}" value="{$mediaType.mt_id}" {if $mediaType.selected}checked="checked"{/if}> <label for="mediaType{$mediaType.mt_id}">{$mediaType.name}</label></li>
										{/foreach}
									</ul>
								{/if}
								
								<div style="overflow: auto; clear: both;">
									{if $config.settings.search_orientation}									
										<div style="float: left; width: 50%">
											<h2>{$lang.searchHeaderOrientation}</h2>
											<ul>
												<li style="margin-bottom: 6px;"><input type="checkbox" name="orientations[portrait]" value="{$lang.searchPortrait}" id="orientationPortrait" {if $searchForm.orientations.portrait}checked="checked"{/if}> <label for="orientationPortrait">{$lang.searchPortrait}</label></li>
												<li style="margin-bottom: 6px;"><input type="checkbox" name="orientations[landscape]" value="{$lang.searchLandscape}" id="orientationLandscape" {if $searchForm.orientations.landscape}checked="checked"{/if} > <label for="orientationLandscape">{$lang.searchLandscape}</label></li>
												<li style="margin-bottom: 6px;"><input type="checkbox" name="orientations[square]" value="{$lang.searchSquare}" id="orientationSquare" {if $searchForm.orientations.square}checked="checked"{/if}> <label for="orientationSquare">{$lang.searchSquare}</label></li>
											</ul>									
										</div>
									{/if}
									{if $config.settings.search_color}
										<div style="float: left; width: 50%">
											<h2>{$lang.searchHeaderColor}</h2>	
											<input type="hidden" name="red" id="red" value="{$searchForm.red}">
											<input type="hidden" name="green" id="green" value="{$searchForm.green}">
											<input type="hidden" name="blue" id="blue" value="{$searchForm.blue}">	
											<input type="hidden" name="hex" id="hex" value="{$searchForm.hex}">								
											<div id="colorpickerHolder">
												<div></div>
											</div>
										</div>
									{/if}
								</div>
						
								{if $config.settings.search_dates}
									<div>
										<h2>{$lang.dates}</h2>								
										<input type="hidden" name="searchDate[dateRangeSearch]" id="dateRangeSearch" value="{if $searchForm.searchDate.dateRangeSearch}on{else}off{/if}">
										<p style="padding-bottom: 4px;"><input type="checkbox" name="searchDate[dateRangeSearchCB]" id="dateRangeSearchCB" value="1" {if $searchForm.searchDate.dateRangeSearch}checked="checked"{/if}> <label for="dateRangeSearchCB">{$lang.searchDateRange}</label></p>
										<div class="searchDate" {if !$searchForm.searchDate.dateRangeSearch}style="display: none;"{/if}>
											<p>{$lang.from}</p>
											<select name="searchDate[fromYear]">
												{html_options options=$fromYear selected=$searchForm.searchDate.fromYear}
											</select>
											<select name="searchDate[fromMonth]">
												{html_options options=$fromMonth selected=$searchForm.searchDate.fromMonth}
											</select>
											<select name="searchDate[fromDay]">
												{html_options options=$fromDay selected=$searchForm.searchDate.fromDay}
											</select>
											<p>{$lang.to}</p>
											<select name="searchDate[toYear]">
												{html_options options=$toYear selected=$searchForm.searchDate.toYear}
											</select>
											<select name="searchDate[toMonth]">
												{html_options options=$toMonth selected=$searchForm.searchDate.toMonth}
											</select>
											<select name="searchDate[toDay]">
												{html_options options=$toDay selected=$searchForm.searchDate.toDay}
											</select>
										</div>
									</div>
								{/if}
								
								{if $config.settings.search_license_type}
									<h2>{$lang.licenseType}</h2>							
									<!--
									<ul>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[royaltyFree]" value="{$lang.searchRoyaltyFree}" id="licenseRoyaltyFree" {if $searchForm.licenses.royaltyFree}checked="checked"{/if}> <label for="licenseRoyaltyFree">{$lang.searchRoyaltyFree}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[free]" value="{$lang.searchFree}" id="licenseFree" {if $searchForm.licenses.free}checked="checked"{/if}> <label for="licenseFree">{$lang.searchFree}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[contactUs]" value="{$lang.searchContactUs}" id="licenseContactUs" {if $searchForm.licenses.contactUs}checked="checked"{/if}> <label for="licenseContactUs">{$lang.searchContactUs}</label></li>										
										<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[extended]" value="{$lang.mediaLicenseEX}" id="licenseExtended" {if $searchForm.licenses.extended}checked="checked"{/if}> <label for="licenseExtended">{$lang.mediaLicenseEX}</label></li>
										<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[editorial]" value="{$lang.mediaLicenseEU}" id="licenseEditorial" {if $searchForm.licenses.editorial}checked="checked"{/if}> <label for="licenseEditorial">{$lang.mediaLicenseEU}</label></li>
									</ul>
									-->
									{*print_r($searchForm.licenses)*}
									<ul>
										{foreach $licensesList as $license}
											<li style="margin-bottom: 6px;"><input type="checkbox" name="licenses[{$license.license_id}]" value="{$license.license_id}" id="lic{$license.license_id}" {if $license.selected}checked="checked"{/if}> <label for="lic{$license.license_id}">{$license.licenseLang}</label></li>	
										{/foreach}
									</ul>
									
								{/if}
								
								{if $config.settings.search_galleries}
									<h2>{$lang.searchHeaderGalleries}</h2>
									<ul>
										{foreach $galleries as $key => $gallery}
											<li style="margin-bottom: 6px;"><input type="checkbox" name="galleries[]" id="gallery{$key}" value="{$key}" {if $gallery.selected}checked="checked"{/if}> <label for="gallery{$key}">{$gallery.name}</label></li>
										{/foreach}
									</ul>
								{/if}
								
								<br><br>						
								<input type="button" value="{$lang.searchUpper}" style="width: 100%;" class="colorButton" id="searchButton">							
								<br><br>
							
							</div>
						{/if}
					</div>
					
				</div>
				<div class="divTableCell contentRightColumn">
					<div class="content">
						<h1>{$lang.search}</h1>
						
						{if $searchForm.inSearch}
							{if $mediaRows}
								<p class="sortByContainer">
									{$lang.sortBy}  
									<select name="searchSortBy" id="searchSortBy">
										{html_options options=$searchSortByOptions selected=$searchForm.searchSortBy}
									</select>
									<!--
									<select name="searchSortType" id="searchSortType">
										{html_options options=$searchSortByTypeOptions selected=$searchForm.searchSortType}
									</select>
									-->
								</p>
								
								{include file="paging.tpl" paging=$mediaPaging}
								<div id="mediaListContainer">
									{foreach $mediaArray as $media}
										{include file='media.container.tpl'}
									{/foreach}
								</div>
								{include file="paging.tpl" paging=$mediaPaging}
							{else}
								<p class="notice">{$lang.searchNoResults}</p><br>
							{/if}
						{else}
							{$lang.searchEnterKeyMessage}
						{/if}
						
						{if $tags}
							<ul class="searchTagList">
							{foreach $tags as $tag}
								<li><a href="search.php?clearSearch=true&searchPhrase={$tag.keyword}">{$tag.keyword}</a><!-- <span class="searchTagCount">({$tag.count})</span>--></li>
							{/foreach}
							</ul>
							<!-- Tag cloud
							<div style="clear: both; margin-top: 20px;">
							{foreach $tags as $tag}
								<a href="search.php?clearSearch=true&searchPhrase={$tag.keyword}" style="font-size: {$tag.fontSize}px">{$tag.keyword}</a> {if !$tag@last},{/if}
							{/foreach}
							</div							-->
						{/if}
					</div>
				</div>
			</div>
		</div>
		</form>
		
		{* Debug Area *}
		{if $debugMode}
			{debugOutput value=$searchForm title='searchForm'}
			{debugOutput value=$sql title='sql'}
		{/if}
		
		{include file='footer.tpl'}
    </div>
</body>
</html>