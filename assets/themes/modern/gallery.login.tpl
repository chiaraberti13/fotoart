<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		<div class="container" style="min-height: 500px;">
			<h1>{foreach $crumbs as $key => $crumb}<a href="{$galleriesData.$key.linkto}">{$galleriesData.$key.name}</a> {if !$crumb@last} &raquo; {/if}{/foreach}</h1>
			<hr>
			{if $logNotice}<p class="notice">{$lang.{$logNotice}}</p><br>{/if}			
			<form id="galleryLoginForm" class="cleanForm form-group" action="gallery.login.php" method="post">
			<div class="controls form-inline">
				<h2>{$lang.galleryLogin}</h2><br>
				<input type="hidden" value="{$useID}" name="id">
				<label>{$lang.password}:</label> <input type="password" id="galleryPassword" name="galleryPassword" style="width: 260px" class="form-control"> <input type="submit" value="{$lang.loginCaps}" class="btn btn-xs btn-primary">
			</div>
			</form>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>