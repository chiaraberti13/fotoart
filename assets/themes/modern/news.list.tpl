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
				{include file='subnav.tpl'}		
				<div class="col-md-9">
					
					<h1>{$lang.news}{if $config.settings.rss_news}&nbsp;<a href="{linkto page='rss.php?mode=news'}" class="btn btn-xxs btn-warning">{$lang.rss}</a>{/if}</h1>
					<hr>
					{if $news}
						{foreach $news as $newsArticle}
							<h2 class="newsDate">{$newsArticle.display_date}</h2>
							<div class="newsArticle">
								<p class="newsTitle"><a href="{$newsArticle.linkto}">{$newsArticle.title}</a></p>
								<p class="newsShort">{$newsArticle.short}</p>
								{if $newsArticle.article != ''}<p class="text-right"><a href="{$newsArticle.linkto}" class="btn btn-xs btn-primary">{$lang.more}</a></p>{/if}
							</div>
						{/foreach}
					{else}
						<p class="notice">{$lang.noNews}</p>
					{/if}
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>