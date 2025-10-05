<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
			<div class="content" style="padding-left: 0; padding-right: 0;">
				<h1>{foreach $crumbs as $key => $crumb}<a href="{$galleriesData.$key.linkto}">{$galleriesData.$key.name}</a> {if !$crumb@last} &raquo; {/if}{/foreach}</h1>
				
				{if $logNotice}<p class="notice">{$lang.{$logNotice}}</p><br>{/if}
				{$lang.galleryLogin}
				<form id="galleryLoginForm" class="cleanForm" action="gallery.login.php" method="post">
				<input type="hidden" value="{$useID}" name="id">
					<span class="formFieldLabel">{$lang.password}:</span> <input type="password" id="galleryPassword" name="galleryPassword" style="width: 220px"> <input type="submit" value="{$lang.loginCaps}">
				</form>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>