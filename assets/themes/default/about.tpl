<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function(){ $('#navAboutUs').addClass('selectedNav'); });
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
					<div class="content" id="aboutUs">
						<h1>{$lang.aboutUs}</h1>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell">{$content.body}</div>
								<div class="divTableCell businessAddress">
									<p>
										<strong>{$config.settings.business_name}</strong><br>
										{$config.settings.business_address}<br>
										{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
										{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
										{$config.settings.business_country}
										{if $config.settings.contact}
											<br><br><a href="{linkto page="contact.php"}" class="colorLink">{$lang.contactUs}</a>
										{/if}
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>