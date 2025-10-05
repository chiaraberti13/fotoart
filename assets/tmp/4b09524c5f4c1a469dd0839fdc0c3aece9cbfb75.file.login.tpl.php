<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 21:15:20
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:58937662968b36e57a64e89-33608378%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b09524c5f4c1a469dd0839fdc0c3aece9cbfb75' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/login.tpl',
      1 => 1757796000,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '58937662968b36e57a64e89-33608378',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e57a9bcf9_83412340',
  'variables' => 
  array (
    'baseURL' => 0,
    'lang' => 0,
    'logNotice' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e57a9bcf9_83412340')) {function content_68b36e57a9bcf9_83412340($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/login.js"></script>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			<div class="row">
				<?php echo $_smarty_tpl->getSubTemplate ('subnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
				<div class="col-md-4">
					
					<div class="divTableCell contentRightColumn">
						<div class="content">
							<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['login'];?>
</h1>
							<hr>
							<?php if ($_smarty_tpl->tpl_vars['logNotice']->value){?><p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['logNotice']->value];?>
</p><br><?php }?>
							<?php echo $_smarty_tpl->tpl_vars['lang']->value['loginMessage'];?>

							<form id="loginForm" class="cleanForm form-group" action="login.php" method="post">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
:</div>
									<div class="divTableCell"><input type="text" id="memberEmail" name="memberEmail" style="min-width: 220px" class="form-control"></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
:</div>
									<div class="divTableCell"><input type="password" id="memberPassword" name="memberPassword" style="min-width: 220px" class="form-control"></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell"></div>
									<div class="divTableCell" style="text-align: right;"><a href="workbox.php?mode=forgotPassword" id="forgotPassword"><?php echo $_smarty_tpl->tpl_vars['lang']->value['forgotPassword'];?>
</a> &nbsp; <input type="submit" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['loginCaps'];?>
" class="btn btn-xs btn-primary"></div>
								</div>
							</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-1 hidden-xs" style="text-align: center; font-size: 9px; color: #EEE">
					|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br><span style="font-weight: bold; font-size: 16px; color: #999">OR</span><br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>|<br>
				</div>
				<div class="col-md-4">
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccount'];?>
</h1>
					<hr>					
					<a href="create.account.php?jumpTo=members" class="btn btn-xs btn-primary" style="font-size: 16px;">&nbsp;<?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccount'];?>
&nbsp;</a>
				</div>				
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>