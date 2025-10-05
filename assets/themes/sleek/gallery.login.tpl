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
			<div class="content">
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