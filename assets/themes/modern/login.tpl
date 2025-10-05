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
		{include file='header3.tpl'}		
		
		<div class="container">
			<div class="row">
				{include file='subnav.tpl'}		
				<div class="col-md-4">
					
					<div class="divTableCell contentRightColumn">
						<div class="content">
							<h1>{$lang.login}</h1>
							<hr>
							{if $logNotice}<p class="notice">{$lang.{$logNotice}}</p><br>{/if}
							{$lang.loginMessage}
							<form id="loginForm" class="cleanForm form-group" action="login.php" method="post">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{$lang.email}:</div>
									<div class="divTableCell"><input type="text" id="memberEmail" name="memberEmail" style="min-width: 220px" class="form-control"></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{$lang.password}:</div>
									<div class="divTableCell"><input type="password" id="memberPassword" name="memberPassword" style="min-width: 220px" class="form-control"></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell"></div>
									<div class="divTableCell" style="text-align: right;"><a href="workbox.php?mode=forgotPassword" id="forgotPassword">{$lang.forgotPassword}</a> &nbsp; <input type="submit" value="{$lang.loginCaps}" class="btn btn-xs btn-primary"></div>
								</div>
							</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-1 hidden-xs" style="text-align: center; font-size: 9px; color: #EEE">
					|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br><span style="font-weight: bold; font-size: 16px; color: #999">OR</span><br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>
				</div>
				<div class="col-md-4">
					<h1>{$lang.createAccount}</h1>
					<hr>					
					<a href="create.account.php?jumpTo=members" class="btn btn-xs btn-primary" style="font-size: 16px;">&nbsp;{$lang.createAccount}&nbsp;</a>
				</div>				
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>