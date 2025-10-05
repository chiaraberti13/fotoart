<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function(){ $('#navNews').addClass('selectedNav'); });
		-->
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					{include file='subnav.tpl'}
				</div>
				<div class="divTableCell contentRightColumn">
					<div class="content">
						<h1>{$lang.news}{if $config.settings.rss_news}<a href="{linkto page='rss.php?mode=news'}"><img src="{$imgPath}/rss.icon.small.png" class="rssH1Icon rssPageH1Icon"></a>{/if}</h1>
						{if $news}
							{foreach $news as $newsArticle}
								<h2 class="newsDate">{$newsArticle.display_date}</h2>
								<div class="newsArticle">
									<p class="newsTitle"><a href="{$newsArticle.linkto}">{$newsArticle.title}</a></p>
									<p class="newsShort">{$newsArticle.short}</p>
									{if $newsArticle.article != ''}<p class="newsMore"><a href="{$newsArticle.linkto}" class="colorLink">{$lang.more}</a></p>{/if}
								</div>
							{/foreach}
						{else}
							<p class="notice">{$lang.noNews}</p>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>