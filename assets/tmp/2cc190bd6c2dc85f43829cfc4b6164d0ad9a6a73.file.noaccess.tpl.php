<?php /* Smarty version Smarty-3.1.8, created on 2025-09-03 09:17:03
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/noaccess.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7199825868b8078fb2c2b2-01377041%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2cc190bd6c2dc85f43829cfc4b6164d0ad9a6a73' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/noaccess.tpl',
      1 => 1755093993,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7199825868b8078fb2c2b2-01377041',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b8078fb3a772_84694182',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b8078fb3a772_84694182')) {function content_68b8078fb3a772_84694182($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head>
<body>
	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<div class="contentContainer">
			<div class="content">
				<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noAccess'];?>
</p>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>