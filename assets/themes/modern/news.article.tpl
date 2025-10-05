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
					
					<h1>{$newsArticle.title}</h1>					
					<p class="newsDate">{$newsArticle.display_date}</p>
					<div>{$newsArticle.article}</div>
					<p class="text-right"><br><br><br><a href="{linkto page="news.php"}" class="btn btn-xs btn-primary">{$lang.back} &raquo;</a></p>
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>