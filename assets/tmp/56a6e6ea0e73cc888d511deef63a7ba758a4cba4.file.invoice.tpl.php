<?php /* Smarty version Smarty-3.1.8, created on 2025-09-02 13:47:40
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/invoice.tpl" */ ?>
<?php /*%%SmartyHeaderCode:133869995168b6f57c884233-95092702%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '56a6e6ea0e73cc888d511deef63a7ba758a4cba4' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/invoice.tpl',
      1 => 1755094002,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '133869995168b6f57c884233-95092702',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'theme' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b6f57c896eb0_09064243',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b6f57c896eb0_09064243')) {function content_68b6f57c896eb0_09064243($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/invoice.css">
</head>
<body>
	<div id="invoiceTemplate">
		<?php echo $_smarty_tpl->tpl_vars['content']->value['body'];?>

    </div>
</body>
</html><?php }} ?>