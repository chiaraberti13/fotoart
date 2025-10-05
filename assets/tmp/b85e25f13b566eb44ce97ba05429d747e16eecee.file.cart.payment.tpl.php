<?php /* Smarty version Smarty-3.1.8, created on 2025-08-31 09:19:57
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:86901542668b413bd47e139-28767269%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b85e25f13b566eb44ce97ba05429d747e16eecee' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.payment.tpl',
      1 => 1755093999,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '86901542668b413bd47e139-28767269',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'submitSleep' => 0,
    'baseURL' => 0,
    'lang' => 0,
    'gatewayForm' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b413bd497364_50896948',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b413bd497364_50896948')) {function content_68b413bd497364_50896948($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript">
		var submitSleep = <?php echo $_smarty_tpl->tpl_vars['submitSleep']->value;?>
; // Amount of time to wait before submitting the form
	</script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.payment.js"></script>
</head>
<body>
	<div style="width: 100%;">
		<div class="processOrderNotice">
			<p></p> <?php echo $_smarty_tpl->tpl_vars['lang']->value['pleaseWait'];?>

		</div>
	</div>
	<?php echo $_smarty_tpl->tpl_vars['gatewayForm']->value;?>

</body>
</html><?php }} ?>