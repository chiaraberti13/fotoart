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
					<h1>{$lang.aboutUs}</h1>
					<p class="cuAddress">
						<strong>{$config.settings.business_name}</strong><br>
						{$config.settings.business_address}<br>
						{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
						{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
						{$config.settings.business_country}
						{if $config.settings.contact}
							<br><br><a href="{linkto page="contact.php"}" class="colorLink">{$lang.contactUs}</a>
						{/if}
					</p>
					{$content.body}					
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>