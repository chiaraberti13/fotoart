<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div id="contentContainer" class="center">
			{include file='search.row.tpl'}
			<div id="contentLeftContainer">
				<div>
					{include file='subnav.tpl'}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					<h1>{$content.name}</h1>
					{$content.body}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>