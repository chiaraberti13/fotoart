<?php /* Smarty version Smarty-3.1.8, created on 2025-08-31 11:08:33
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/about.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12546932768b42d31970d94-92344265%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0bc4824c63f91c5705abba1b40cb00e675925b84' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/about.tpl',
      1 => 1755094005,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12546932768b42d31970d94-92344265',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
    'content' => 0,
    'config' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b42d319bc151_57224862',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b42d319bc151_57224862')) {function content_68b42d319bc151_57224862($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			<div class="row">
				<?php echo $_smarty_tpl->getSubTemplate ('subnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
				<div class="col-md-9">
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['aboutUs'];?>
</h1>
					<div class="container" style="padding: 0;">
						<div class="row">
							
							<div class="col-md-9">
								<?php echo $_smarty_tpl->tpl_vars['content']->value['body'];?>

							</div>
							
							<div class="col-md-3">
								<p>
									<strong><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_name'];?>
</strong><br>
									<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_address'];?>
<br>
									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['business_address2']){?><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_address2'];?>
<br><?php }?>
									<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_city'];?>
, <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_state'];?>
 <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_zip'];?>
<br>
									<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_country'];?>

									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['contact']){?>
										<br><br><a href="<?php echo linkto(array('page'=>"contact.php"),$_smarty_tpl);?>
" class="colorLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contactUs'];?>
</a>
									<?php }?>
								</p>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>