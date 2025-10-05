<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/login.js"></script>
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
					<div class="content">
						<h1>{$lang.login}</h1>
						{if $logNotice}<p class="notice">{$lang.{$logNotice}}</p><br>{/if}
						{$lang.loginMessage}
						<form id="loginForm" class="cleanForm" action="login.php" method="post">
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.email}:</div>
								<div class="divTableCell"><input type="text" id="memberEmail" name="memberEmail" style="width: 220px"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.password}:</div>
								<div class="divTableCell"><input type="password" id="memberPassword" name="memberPassword" style="width: 220px"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell"></div>
								<div class="divTableCell"><a href="workbox.php?mode=forgotPassword" id="forgotPassword">{$lang.forgotPassword}</a><input type="submit" value="{$lang.loginCaps}"></div>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>