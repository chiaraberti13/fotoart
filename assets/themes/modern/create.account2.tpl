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
				
					<div class="content">
						<h1>{$lang.createAccount}</h1>
						<hr>
						{if $notice}<p class="notice">{$lang.{$notice}}</p><br>{/if}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>