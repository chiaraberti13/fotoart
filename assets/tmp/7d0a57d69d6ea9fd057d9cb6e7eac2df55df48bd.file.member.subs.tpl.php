<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 11:39:43
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/member.subs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22955909268c6a97f218cc9-55369854%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d0a57d69d6ea9fd057d9cb6e7eac2df55df48bd' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/member.subs.tpl',
      1 => 1757796003,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22955909268c6a97f218cc9-55369854',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
    'config' => 0,
    'memsubRows' => 0,
    'memsubArray' => 0,
    'memsub' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68c6a97f2db422_55573348',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68c6a97f2db422_55573348')) {function content_68c6a97f2db422_55573348($_smarty_tpl) {?><!DOCTYPE HTML>
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
				<?php echo $_smarty_tpl->getSubTemplate ('memnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
				<div class="col-md-9">
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['subscriptions'];?>
</h1>
					<hr>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['subpage']&&$_smarty_tpl->tpl_vars['config']->value['settings']['subscriptions']){?><a href="<?php echo linkto(array('page'=>"featured.php?mode=subscriptions"),$_smarty_tpl);?>
" class="btn btn-xs btn-success" style="float: right; margin-bottom: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseSub'];?>
</a><?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['memsubRows']->value){?>
						<table class="dataTable">
							<tr>
								<th style="text-align: center" class="hidden-xs"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subHeaderID'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['subHeaderSubscript'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['subHeaderExpires'];?>
</th>
								<th style="text-align: center">DOWNLOADS REMAINING</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subHeaderStatus'];?>
</th>
								<th style="text-align: center"></th>
							</tr>
							<?php  $_smarty_tpl->tpl_vars['memsub'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['memsub']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['memsubArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['memsub']->key => $_smarty_tpl->tpl_vars['memsub']->value){
$_smarty_tpl->tpl_vars['memsub']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['memsub']->key;
?>
								<tr>
									<td style="text-align: center" class="hidden-xs"><?php echo $_smarty_tpl->tpl_vars['memsub']->value['msub_id'];?>
</td>
									<td class="workboxLinkAttach"><?php if ($_smarty_tpl->tpl_vars['memsub']->value['active']){?><a href="<?php echo $_smarty_tpl->tpl_vars['memsub']->value['linkto'];?>
" class="workboxLink colorLink"><?php echo $_smarty_tpl->tpl_vars['memsub']->value['name'];?>
</a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['memsub']->value['name'];?>
<?php }?></td>
									<td><?php if ($_smarty_tpl->tpl_vars['memsub']->value['expired']){?><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['memsub']->value['expire_date_display'];?>
</span><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['memsub']->value['expire_date_display'];?>
<?php }?></td>
									<td style="text-align: center">
										<?php if ($_smarty_tpl->tpl_vars['memsub']->value['total_downloads']){?>
											<?php echo $_smarty_tpl->tpl_vars['memsub']->value['totalRemaining'];?>

										<?php }else{ ?>
											<?php echo $_smarty_tpl->tpl_vars['lang']->value['unlimited'];?>

										<?php }?>
										<span><?php if ($_smarty_tpl->tpl_vars['memsub']->value['perday']){?> (<?php echo $_smarty_tpl->tpl_vars['memsub']->value['todayRemaining'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['today'];?>
)</span><?php }?>
									</td>
									<td style="text-align: center"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['memsub']->value['status_lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['memsub']->value['status_lang']];?>
</span></td>
									<td style="text-align: center" class="workboxLinkAttach"><?php if ($_smarty_tpl->tpl_vars['memsub']->value['active']){?><a href="<?php echo $_smarty_tpl->tpl_vars['memsub']->value['linkto'];?>
" class="workboxLink btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['renew'];?>
</a><?php }?></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noSubs'];?>
</p>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>