<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 09:46:51
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/members.tpl" */ ?>
<?php /*%%SmartyHeaderCode:26936684068b374bdf0c316-80264226%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '81ebc347f168d856cff05c1c44e662ee7fda9509' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/members.tpl',
      1 => 1757796004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26936684068b374bdf0c316-80264226',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b374be0b3300_73095199',
  'variables' => 
  array (
    'lang' => 0,
    'member' => 0,
    'lastLoginDisplay' => 0,
    'ticketSystem' => 0,
    'tickets' => 0,
    'bills' => 0,
    'membership' => 0,
    'memberSpecGallery' => 0,
    'gallery' => 0,
    'sales' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b374be0b3300_73095199')) {function content_68b374be0b3300_73095199($_smarty_tpl) {?><!DOCTYPE HTML>
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
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['welcome'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
</h1>
					<hr>
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['notices'];?>
</li>
						<li><?php echo $_smarty_tpl->tpl_vars['lang']->value['lastLogin'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['lastLoginDisplay']->value;?>
</strong></li>
						<?php if ($_smarty_tpl->tpl_vars['ticketSystem']->value&&$_smarty_tpl->tpl_vars['tickets']->value){?><li><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['tickets']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['newTicketsMessage'];?>
 <a href="<?php echo linkto(array('page'=>"tickets.php"),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['view'];?>
</a></li><?php }?>
						<!--<li><span class="highlightValue">0</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['newSales'];?>
 <a href="<?php echo linkto(array('page'=>"contr.sales.php"),$_smarty_tpl);?>
" class="colorLink">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['view'];?>
]</a></li>-->
						<?php if ($_smarty_tpl->tpl_vars['bills']->value){?><li><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['bills']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['unpaidBills'];?>
 <a href="<?php echo linkto(array('page'=>"bills.php"),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['view'];?>
</a></li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membership']!=1&&$_smarty_tpl->tpl_vars['membership']->value['msExpired']){?><li><span class="highlightValue">Expired</span> - <?php echo $_smarty_tpl->tpl_vars['lang']->value['msExpired'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['membership']->value['name'];?>
</strong> <a href="<?php echo linkto(array('page'=>"account.edit.php?mode=membership"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['renew'];?>
]</a></li><?php }?>
					</ul>
					
					<?php if ($_smarty_tpl->tpl_vars['memberSpecGallery']->value){?>
						<ul class="accountInfoList">
							<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['myGalleries'];?>
</li>
							<?php  $_smarty_tpl->tpl_vars['gallery'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gallery']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['memberSpecGallery']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gallery']->key => $_smarty_tpl->tpl_vars['gallery']->value){
$_smarty_tpl->tpl_vars['gallery']->_loop = true;
?>
								<li><a href="<?php echo $_smarty_tpl->tpl_vars['gallery']->value['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['gallery']->value['name'];?>
</a></li>
							<?php } ?>
							</li>
						</ul>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membership']!=1){?>
						<ul class="accountInfoList">
							<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['membership'];?>
</li>
							<li><a href="<?php echo linkto(array('page'=>"membership.php?id=".($_smarty_tpl->tpl_vars['membership']->value['ums_id'])),$_smarty_tpl);?>
" class="membershipWorkbox"><strong><?php echo $_smarty_tpl->tpl_vars['membership']->value['name'];?>
</strong></a> <?php if ($_smarty_tpl->tpl_vars['membership']->value['msExpired']){?><!--<span class="highlightValue">(expired)</span>--> <a href="<?php echo linkto(array('page'=>"account.edit.php?mode=membership"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['renew'];?>
]</a><?php }?></li>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['expires'];?>
:</strong> <?php if ($_smarty_tpl->tpl_vars['membership']->value['msExpired']){?><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['membership']->value['msExpireDate'];?>
</span><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['membership']->value['msExpireDate'];?>
<?php }?></li>
							<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=membership"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
						</ul>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
						<ul class="accountInfoList">
							<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contributors'];?>
</li>
							<li><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['sales']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['newSales'];?>
 <a href="<?php echo linkto(array('page'=>"contributor.sales.php"),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['view'];?>
</a></li>
						</ul>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>