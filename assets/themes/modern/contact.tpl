<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
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
					
					<h1>{$lang.contactUs}</h1>
					<hr>
					
					<div class="container" style="padding: 0;">
						<div class="row">							
							<div class="col-md-9">								
								{if $contactNotice == "contactMessage"}
									<p class="notice">{$lang.{$contactNotice}}</p>
								{else}
									{if $contactNotice != "contactMessage"}
										<p class="notice">{$lang.{$contactNotice}}</p>
									{/if}
									{$lang.contactIntro}
									<form id="contactForm" class="cleanForm form-group" action="contact.php" method="post">
									<div class="divTable" style="width: 70%;">
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">{$lang.name}:</div>
											<div class="divTableCell"><input type="text" id="name" name="form[name]" value="{$form.name}" class="form-control"></div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">{$lang.email}:</div>
											<div class="divTableCell"><input type="text" id="email" name="form[email]" value="{$form.email}" class="form-control"></div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.question}:</div>
											<div class="divTableCell"><textarea id="question" name="form[question]" style="height: 160px;" class="form-control">{$form.question}</textarea></div>
										</div>
										
										{if $config.settings.contactCaptcha}
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="vertical-align: top;">{$lang.captcha}:</div>
											<div class="divTableCell captcha">{include file='captcha.tpl'}</div>
										</div>
										{/if}
					
										<div class="divTableRow">
											<div class="divTableCell"></div>
											<div class="divTableCell"><input type="submit" value="{$lang.submit}" class="btn btn-xs btn-primary" style="float: right;"></div>
										</div>
									</div>
									</form>
								{/if}								
							</div>
							<div class="col-md-3">
								<p>
									<strong>{$config.settings.business_name}</strong><br>
									{$config.settings.business_address}<br>
									{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
									{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
									{$config.settings.business_country}
								</p>
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