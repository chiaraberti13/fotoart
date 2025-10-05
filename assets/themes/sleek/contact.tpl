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
					<h1>{$lang.contactUs}</h1>
					{if $contactNotice == "contactMessage"}
						<p class="notice">{$lang.{$contactNotice}}</p>
					{else}
						{if $contactNotice != "contactMessage"}
							<p class="notice">{$lang.{$contactNotice}}</p>
						{/if}
						<p class="cuAddress">
							<strong>{$config.settings.business_name}</strong><br>
							{$config.settings.business_address}<br>
							{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
							{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
							{$config.settings.business_country}
						</p>
						{$lang.contactIntro}
						<form id="contactForm" class="cleanForm" action="contact.php" method="post">
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.name}:</div>
								<div class="divTableCell"><input type="text" id="name" name="form[name]" value="{$form.name}" style="width: 300px"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.email}:</div>
								<div class="divTableCell"><input type="text" id="email" name="form[email]" value="{$form.email}" style="width: 300px"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.question}:</div>
								<div class="divTableCell"><textarea id="question" name="form[question]" style="width: 302px; height: 160px;">{$form.question}</textarea></div>
							</div>
							
							{if $config.settings.contactCaptcha}
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel" style="vertical-align: top;">{$lang.captcha}:</div>
								<div class="divTableCell captcha">{include file='captcha.tpl'}</div>
							</div>
							{/if}
							<div class="divTableRow">
								<div class="divTableCell"></div>
								<div class="divTableCell"><input type="submit" value="{$lang.submit}"></div>
							</div>
						</div>
						</form>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>