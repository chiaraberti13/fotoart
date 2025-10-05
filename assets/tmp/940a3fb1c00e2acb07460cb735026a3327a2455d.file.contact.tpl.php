<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 08:04:45
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/contact.tpl" */ ?>
<?php /*%%SmartyHeaderCode:66700429968b3774396ca10-62756730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '940a3fb1c00e2acb07460cb735026a3327a2455d' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/contact.tpl',
      1 => 1757796002,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '66700429968b3774396ca10-62756730',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b37743a14a81_66274178',
  'variables' => 
  array (
    'baseURL' => 0,
    'lang' => 0,
    'contactNotice' => 0,
    'form' => 0,
    'config' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b37743a14a81_66274178')) {function content_68b37743a14a81_66274178($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/featured.page.js"></script>
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
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['contactUs'];?>
</h1>
					<hr>
					
					<div class="container" style="padding: 0;">
						<div class="row">							
							<div class="col-md-9">								
								<?php if ($_smarty_tpl->tpl_vars['contactNotice']->value=="contactMessage"){?>
									<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['contactNotice']->value];?>
</p>
								<?php }else{ ?>
									<?php if ($_smarty_tpl->tpl_vars['contactNotice']->value!="contactMessage"){?>
										<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['contactNotice']->value];?>
</p>
									<?php }?>
									<?php echo $_smarty_tpl->tpl_vars['lang']->value['contactIntro'];?>

									<form id="contactForm" class="cleanForm form-group" action="contact.php" method="post">
									<div class="divTable" style="width: 70%;">
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
:</div>
											<div class="divTableCell"><input type="text" id="name" name="form[name]" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['name'];?>
" class="form-control"></div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
:</div>
											<div class="divTableCell"><input type="text" id="email" name="form[email]" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['email'];?>
" class="form-control"></div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['question'];?>
:</div>
											<div class="divTableCell"><textarea id="question" name="form[question]" style="height: 160px;" class="form-control"><?php echo $_smarty_tpl->tpl_vars['form']->value['question'];?>
</textarea></div>
										</div>
										
										<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['contactCaptcha']){?>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="vertical-align: top;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['captcha'];?>
:</div>
											<div class="divTableCell captcha"><?php echo $_smarty_tpl->getSubTemplate ('captcha.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div>
										</div>
										<?php }?>
					
										<div class="divTableRow">
											<div class="divTableCell"></div>
											<div class="divTableCell"><input type="submit" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" class="btn btn-xs btn-primary" style="float: right;"></div>
										</div>
									</div>
									</form>
								<?php }?>								
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