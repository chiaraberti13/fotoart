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
					<h1>{$lang.supportTickets} <span><a href="{linkto page="tickets.php"}" class="colorLink">{$lang.back}</a></span></h1>
						
					{if $notice}
						<p class="notice">{$lang.{$notice}}</p><br>
					{/if}
					
					<form method="post" action="ticket.details.php">
					<input type="hidden" name="id" value="{$ticket.ticket_id}">
					<table class="dataTable vTable">
						<tr>
							<th>{$lang.id|upper}</th>
							<td>{$ticket.ticket_id}</td>
						</tr>
						<tr>
							<th>{$lang.summary|upper}</th>
							<td>{$ticket.summary}</td>
						</tr>
						<tr>
							<th>{$lang.opened|upper}</th>
							<td>{$ticket.opened}</td>
						</tr>
						<tr>
							<th>{$lang.lastUpdated|upper}</th>
							<td>{$ticket.lastupdated}</td>
						</tr>
						<tr>
							<th>{$lang.status|upper}</th>
							<td style="">{if $ticket.status == 1 or $ticket.status == 2}<strong>{$lang.open}</strong> &nbsp; <a href="{linkto page="ticket.details.php?closeTicket={$ticket.ticket_id}"}" class="colorLink" style="font-size: 11px;">[{$lang.closeTicket}]</a>{else}<span class="highlightValue">{$lang.closed}</span>{/if}</td>
						</tr>
						{if $ticketFileRows}
							<tr>
								<th>{$lang.files|upper}</th>
								<td>
									<table style="width: 100%">
										{foreach $ticketFilesArray as $key => $file}
										<tr>
											<td><a href="">{$file.original_name}</a></td>
											<td>{$file.uploaddate}</td>
											<td>{$file.filesize}{$lang.KB}</td>
											<td><a href="" class="colorLink">{$lang.downloadUpper}</a></td>
										</tr>
										{/foreach}
									</table>
								</td>
							</tr>
						{/if}
						<tr>
							<th>{$lang.reply|upper}</th>
							<td>
								{if $ticket.status == 1 or $ticket.status == 2}
									<textarea style="width: 524px; height: 130px;" name="ticketReply"></textarea>
									<br>
									<input type="submit" value="{$lang.reply}" style="float: right; margin-top: 4px;">
								{else}
									<p class="notice">{$lang.ticketNoReplies}</p>
								{/if}
							</td>
						</tr>
					</table>
					</form>
					
					{if $messageRows}
						{foreach $messagesArray as $key => $message}
							<h2 class="upArrow{$message.admin_response}">&nbsp;</h2>
							<div class="ticketMessage{$message.admin_response}">
								<p class="messageDetails">
									{$lang.messageID}: {$message.message_id}<br>
									{$lang.by} <strong>{if $message.admin_response}{$config.settings.business_name}{else}{$member.f_name} {$member.l_name}{/if}</strong><br>
									@ {$message.submit_date}
								</p>{$message.message}
							</div>
						{/foreach}
					{else}
						<br><p class="notice">{$lang.emptyTicket}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>