<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<style type="text/css">
	.processOrderNotice p { background-image: none; float: none; }
	</style>
</head>
<body>
	<div style="width: 100%;">
		<div class="processOrderNotice">
			<p>Kies hieronder uw bank:</p><br />
			<form method="post">
				<select name="bank_id">
					{foreach $banks as $bank_id => $bank}
						<option value="{$bank_id}">{$bank}</option>
					{/foreach}
				</select><br />
				<input type="hidden" name="email" value="{$email|escape}" />
				<input type="hidden" name="paymentType" value="mollieideal" />
				<input type="submit" name="submit" value="Verzenden" />
			</form>
		</div>
	</div>
	{$gatewayForm}
</body>
</html>