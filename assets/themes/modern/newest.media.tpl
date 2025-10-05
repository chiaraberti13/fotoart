<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		<div class="container">

			<div class="row">				
<center>			<h1>{$lang.newestMedia}{if $config.settings.rss_newest} <a href="{linkto page='rss.php?mode=newestMedia'}" class="btn btn-xxs btn-warning">{$lang.rss}</a>{/if}</h1></center>
			<hr>
			{if $mediaRows}
				{include file="paging.tpl" paging=$mediaPaging}
				</div>
				<div id="mediaListContainer">
				<div class="row">
					{foreach $mediaArray as $media}
						{include file='media.container.tpl'}
					{/foreach}
				</div>
				<div class="container">
				{include file="paging.tpl" paging=$mediaPaging}
			{else}
				<p class="notice">{$lang.noMedia}</p>
			{/if}
		</div>
		
		{include file='footer.tpl'}
    </div>
</body>
</html>
