<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		var submitSleep = {$submitSleep}; // Amount of time to wait before submitting the form
	</script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.payment.js"></script>
</head>
<body>
	<div style="width: 100%;">
		<div class="processOrderNotice">
			<p></p> {$lang.pleaseWait}
		</div>
	</div>
	{$gatewayForm}
</body>
</html>