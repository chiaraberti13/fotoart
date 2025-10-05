<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				{include file='subnav.tpl'}		
				<div class="col-md-9">
					<h1>{$content.name}</h1>
						{$content.body}
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>