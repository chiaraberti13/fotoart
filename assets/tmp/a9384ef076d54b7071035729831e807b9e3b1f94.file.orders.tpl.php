<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 11:23:40
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/orders.tpl" */ ?>
<?php /*%%SmartyHeaderCode:165117931368b374c5d7d483-84705459%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9384ef076d54b7071035729831e807b9e3b1f94' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/orders.tpl',
      1 => 1757796008,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '165117931368b374c5d7d483-84705459',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b374c5e50da2_59432919',
  'variables' => 
  array (
    'lang' => 0,
    'orderRows' => 0,
    'ordersArray' => 0,
    'order' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b374c5e50da2_59432919')) {function content_68b374c5e50da2_59432919($_smarty_tpl) {?><!DOCTYPE HTML>
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
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['orders'];?>
</h1>
					<hr>
					<?php if ($_smarty_tpl->tpl_vars['orderRows']->value){?>
						<table class="dataTable">
							<tr>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderNumUpper'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderDateUpper'];?>
</th>
								<th><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderTotal'];?>
</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['paymentUpper'];?>
</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderStatus'];?>
</th>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['billHeaderInvoice'];?>
</th>
							</tr>
							<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ordersArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['order']->key;
?>
								<tr>
									<td style="text-align: center"><a href="<?php echo linkto(array('page'=>"order.details.php?orderID=".($_smarty_tpl->tpl_vars['order']->value['uorder_id'])),$_smarty_tpl);?>
" target="_blank" class="colorLink"><?php echo $_smarty_tpl->tpl_vars['order']->value['order_number'];?>
</a></td>
									<td><?php echo $_smarty_tpl->tpl_vars['order']->value['order_date_display'];?>
</td>
									<td><?php if ($_smarty_tpl->tpl_vars['order']->value['total']['raw']>0){?><?php echo $_smarty_tpl->tpl_vars['order']->value['total']['display'];?>
<br><?php }?><?php if ($_smarty_tpl->tpl_vars['order']->value['credits_total']>0){?><?php echo $_smarty_tpl->tpl_vars['order']->value['credits_total'];?>
 <span class="credits"><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</span><?php }?></td>
									<td style="text-align: center"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['order']->value['order_payment_lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['order']->value['order_payment_lang']];?>
</span></td>
									<td style="text-align: center"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['order']->value['order_status_lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['order']->value['order_status_lang']];?>
</span></td>
									<td style="text-align: center"><a href="<?php echo linkto(array('page'=>"invoice.php?orderID=".($_smarty_tpl->tpl_vars['order']->value['uorder_id'])),$_smarty_tpl);?>
" target="_blank" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['view'];?>
</a></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noOrders'];?>
</p>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>