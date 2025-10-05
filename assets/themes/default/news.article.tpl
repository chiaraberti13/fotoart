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
						<h1>{$newsArticle.title} <span><a href="{linkto page="news.php"}" class="colorLink">{$lang.back}</a></span></h1>
						<p class="newsDate2">{$newsArticle.display_date}</p>
						{$newsArticle.article}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>