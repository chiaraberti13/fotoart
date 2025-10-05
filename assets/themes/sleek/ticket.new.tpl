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
					{include file='memnav.tpl'}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					<h1>{$lang.newTicket} <span><a href="{linkto page="tickets.php"}" class="colorLink">{$lang.back}</a></span></h1>
						
					{if $notice}
						<p class="notice">{$lang.{$notice}}</p>
					{else}
						<form method="post" action="ticket.new.php" id="ticketForm" class="cleanForm">
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{$lang.summary}:</div>
								<div class="divTableCell"><input type="text" name="summary" id="summary" value="" require="require" errorMessage="{$lang.required}" style="width: 580px;"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.message}:</div>
								<div class="divTableCell"><textarea style="width: 580px; height: 200px;" id="message" name="message" require="require" errorMessage="{$lang.required}"></textarea></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell"></div>
								<div class="divTableCell" style="text-align: right"><input type="submit" value="{$lang.submit}"></div>
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