<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 05:36:08
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:49860907668b3941e527876-43964759%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3aedf2c681aeeee9c69f64ae8f9781236ab144c2' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/content.tpl',
      1 => 1757796001,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49860907668b3941e527876-43964759',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b3941e53d6b7_95056402',
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b3941e53d6b7_95056402')) {function content_68b3941e53d6b7_95056402($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head>
<body>
	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			<div class="row">
				<?php echo $_smarty_tpl->getSubTemplate ('subnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
				<div class="col-md-9">
					<h1><?php echo $_smarty_tpl->tpl_vars['content']->value['name'];?>
</h1>
						<?php echo $_smarty_tpl->tpl_vars['content']->value['body'];?>

				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>