<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 11:39:47
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/bills.tpl" */ ?>
<?php /*%%SmartyHeaderCode:162778852568b375c6d6af11-86918288%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '25622207bd9e919b607989a904f161f6fac69f20' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/bills.tpl',
      1 => 1757795998,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '162778852568b375c6d6af11-86918288',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b375c6ddfe10_12037397',
  'variables' => 
  array (
    'baseURL' => 0,
    'lang' => 0,
    'notice' => 0,
    'billRows' => 0,
    'billsArray' => 0,
    'bill' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b375c6ddfe10_12037397')) {function content_68b375c6ddfe10_12037397($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/bills.js"></script>	
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
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['bills'];?>
</h1>
					<hr>
					<?php if ($_smarty_tpl->tpl_vars['notice']->value){?>
						<p class="notice" style="margin-bottom: 14px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['notice']->value];?>
</p>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['billRows']->value){?>
						<table class="dataTable">
							<tr>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderInvoice'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderDate'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderDueDate'];?>
</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderTotal'];?>
</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderStatus'];?>
</th>
								<th></th>
							</tr>
							<?php  $_smarty_tpl->tpl_vars['bill'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['bill']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['billsArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['bill']->key => $_smarty_tpl->tpl_vars['bill']->value){
$_smarty_tpl->tpl_vars['bill']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['bill']->key;
?>
								<tr>
									<td><a href="<?php echo linkto(array('page'=>"invoice.php?billID=".($_smarty_tpl->tpl_vars['bill']->value['ubill_id'])),$_smarty_tpl);?>
" target="_blank" class="colorLink"><?php echo $_smarty_tpl->tpl_vars['bill']->value['invoice_number'];?>
</a></td>
									<td><?php echo $_smarty_tpl->tpl_vars['bill']->value['invoice_date_display'];?>
</td>
									<td><?php if ($_smarty_tpl->tpl_vars['bill']->value['past_due']){?><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['bill']->value['due_date_display'];?>
</span><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['bill']->value['due_date_display'];?>
<?php }?></td>
									<td style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['bill']->value['total']['display'];?>
</td>
									<td style="text-align: center"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['bill']->value['payment_status_lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['bill']->value['payment_status_lang']];?>
</span></td>
									<td><?php if ($_smarty_tpl->tpl_vars['bill']->value['payment_status']==2){?><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['pay'];?>
" billID="<?php echo $_smarty_tpl->tpl_vars['bill']->value['ubill_id'];?>
" class="payButton btn btn-xs btn-success"><?php }?></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noBills'];?>
</p>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>