<?php /* Smarty version Smarty-3.1.8, created on 2025-08-31 09:41:06
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/order.details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5649014368b418b25ddab8-01184704%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85f4e1e53b693e424bf2a95e1109f99010685f9b' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/order.details.tpl',
      1 => 1755093990,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5649014368b418b25ddab8-01184704',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'loggedIn' => 0,
    'lang' => 0,
    'orderInfo' => 0,
    'invoiceInfo' => 0,
    'tax' => 0,
    'adminCurrency' => 0,
    'shippingAddress' => 0,
    'imgPath' => 0,
    'digitalInvoiceItems' => 0,
    'invoiceItem' => 0,
    'config' => 0,
    'cartItem' => 0,
    'debugMode' => 0,
    'invoiceItemKey' => 0,
    'physicalInvoiceItems' => 0,
    'bPP' => 0,
    'gruppoOpzione' => 0,
    'k' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b418b28ceb47_19039560',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b418b28ceb47_19039560')) {function content_68b418b28ceb47_19039560($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/order.details.js"></script>
	<script type="text/javascript">
		$(function()
		{ 
			// Hide top nav so we can replace it with steps bar
			$('#searchBar').hide();
			$('#topNav').hide();
		});
	</script>
</head>
<body>
	<input type="hidden" id="loggedIn" name="loggedIn" value="<?php echo $_smarty_tpl->tpl_vars['loggedIn']->value;?>
">
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
		<div class="container">	
		
			<div class="orderDetailsHeader"><h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourOrder'];?>
<a href="invoice.php?orderID=<?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['uorder_id'];?>
" class="btn btn-xs btn-primary" target="_blank" style="float: right"><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewInvoice'];?>
</a></h1></div>
			
			<div class="orderDetailsBoxesContainer">
				<div class="orderDetailsBoxes" style="width: 33%">
					<div class="cartTotalList">					
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderNumber'];?>
:</div>
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['order_number'];?>
</div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderPlaced'];?>
:</div>
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['orderPlacedDate'];?>
</div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderStatus'];?>
:</div>
								<div class="divTableCell"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['orderStatusLang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['orderInfo']->value['orderStatusLang']];?>
</span></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['paymentStatus'];?>
:</div>
								<div class="divTableCell"><span class="highlightValue_<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['paymentStatusLang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['invoiceInfo']->value['paymentStatusLang']];?>
</span></div>
							</div>
						</div><!--	payment info here -->
					</div>
					
				</div>
							
				<div class="orderDetailsBoxes" style="width: 34%;">
					<div class="cartTotalList">
						<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['subtotal']){?>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subtotal'];?>
:</div>
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['priceSubTotal'];?>
</span></div>
							</div>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['shippable']){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipping'];?>
:</div>
									<div class="divTableCell"><span><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['shippingTotal'];?>
</span></div>
								</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['taxa_cost']>0){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxAName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_a_default'];?>
%)-->:</div>
									<div class="divTableCell"><span><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['taxA'];?>
</span></div>
								</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['taxb_cost']>0){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxBName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_b_default'];?>
%)-->:</div>
									<div class="divTableCell"><span><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['taxB'];?>
</span></div>
								</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['taxc_cost']>0){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxCName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_c_default'];?>
%)-->:</div>
									<div class="divTableCell"><span><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['taxC'];?>
</span></div>
								</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['discounts_total']>0){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['discounts'];?>
:</div>
									<div class="divTableCell"><span class="cartTotalDiscounts"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['discountsTotal'];?>
</span></div>
								</div>
							<?php }?>
							<div class="divTableRow">
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['total'];?>
:</span></div>
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['total'];?>
</span></div>
							</div>
						</div>
						<?php }?>	
						<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['creditsSubTotal']){?>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsSubtotal'];?>
:</div>
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['creditsSubTotal'];?>
</span></div>
							</div>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['discounts_credits_total']){?>
								<div class="divTableRow">
									<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsDiscounts'];?>
:</div>
									<div class="divTableCell"><span class="cartTotalDiscounts"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['totalCreditsDiscounts'];?>
</span></div>
								</div>
							<?php }?>
							<div class="divTableRow">
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
:</span></div>
								<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['creditsTotal'];?>
</span></div>
							</div>
						</div>
						<?php }?>
						
						*<?php echo $_smarty_tpl->tpl_vars['lang']->value['totalsShown'];?>
 <strong><?php echo $_smarty_tpl->tpl_vars['adminCurrency']->value['code'];?>
</strong>
					</div>
					
				</div>
				
				<div class="orderDetailsBoxes" style="width: 33%">
					<div style="padding-top: 5px;">
						<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['billTo'];?>
:</h2>
						<p>
							<strong><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_name'];?>
</strong><br>
							<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_address'];?>
<br>
							<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_address2']){?><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_address2'];?>
<br><?php }?>
							<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_city'];?>
, <?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_state'];?>
 <?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_zip'];?>
<br>
							<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['bill_country'];?>
<br>
							<!--
							<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['phone'];?>
<br><?php }?>
							<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['email']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
<br><?php }?>
							-->
						</p>
						<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['shippable']){?>
							<h2 style="margin-top: 20px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipTo'];?>
:</h2>
							<p>
								<strong><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_name'];?>
</strong><br>
								<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_address'];?>
<br>
								<?php if ($_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_address2']){?><?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_address2'];?>
<br><?php }?>
								<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_city'];?>
, <?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_state'];?>
 <?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_zip'];?>
<br>
								<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['ship_country'];?>
<br>
								<!--
								<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['phone'];?>
<br><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['email']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
<br><?php }?>
								-->
							</p>
						<?php }?>
					</div>
				</div>
			</div>
			
			<div class="messageBox"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orderInfoshipnotes'];?>
<?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['mem_notes'];?>
</div>
			
			
			<?php if ($_smarty_tpl->tpl_vars['digitalInvoiceItems']->value){?>
				<div class="container cartContainer">				
					<input type="hidden" name="orderID" id="orderID" value="<?php echo $_smarty_tpl->tpl_vars['invoiceInfo']->value['order_id'];?>
">
					<h2 style="margin-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloads'];?>
</h2>
					
					<?php  $_smarty_tpl->tpl_vars['invoiceItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoiceItem']->_loop = false;
 $_smarty_tpl->tpl_vars['invoiceItemKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['digitalInvoiceItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoiceItem']->key => $_smarty_tpl->tpl_vars['invoiceItem']->value){
$_smarty_tpl->tpl_vars['invoiceItem']->_loop = true;
 $_smarty_tpl->tpl_vars['invoiceItemKey']->value = $_smarty_tpl->tpl_vars['invoiceItem']->key;
?>
					<div class="row" style="background-color: #efefef; margin-bottom: 3px;">
						<div class="col-md-2 cartThumbColumn">
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']){?>
								<a href="media.details.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['useMediaID'];?>
"><img src="image.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['encryptedID'];?>
=&type=icon&folderID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['encryptedFID'];?>
==&size=60" class="thumb"></a>
							<?php }elseif($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['photo']){?>
								<img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['item_id'],'itemType'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['itemTypeShort'],'photoID'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['photo']['id'],'size'=>60),$_smarty_tpl);?>
" class="thumb">
							<?php }else{ ?>
								<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/blank.cart.item.png">
							<?php }?>
						</div>
						<div class="col-md-6">
							<h2><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['name'];?>
</h2>
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['item_type']=='digital'){?>
								<p class="cartItemDescription">
									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['display_license']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['license'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['licenseLang'];?>
</strong><br><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['width']||$_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['height']){?><strong><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['width'];?>
 x <?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['height'];?>
 px</strong> <!--<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['widthIC']||$_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['heightIC']){?><em>( <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['widthIC'];?>
 x <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['heightIC'];?>
 @ <?php echo $_smarty_tpl->tpl_vars['config']->value['dpiCalc'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['dpi'];?>
 )</em><?php }?>--><br><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['format']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['format'];?>
</strong><br><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['dsp_type']=='video'){?>
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['fps']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFPS'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['fps'];?>
</strong><br><?php }?>
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['running_time']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRunningTime'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['running_time'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['seconds'];?>
<br><?php }?>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['model_release_form']){?><br><a href="../assets/files/releases/<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['model_release_form'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRelease'];?>
</a><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['prop_release_form']){?><br><a href="../assets/files/releases/<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['prop_release_form'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPropRelease'];?>
</a><?php }?>
								</p>
							<?php }else{ ?>
								<p class="cartItemDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['description'],200);?>
</p>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?><?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['invoiceItem']->value,'title'=>'Item Details'),$_smarty_tpl);?>
<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['item_type']=='collection'){?><div id="collectionList<?php echo $_smarty_tpl->tpl_vars['invoiceItemKey']->value;?>
" style="display: none; padding: 0; max-height: 350px; min-height: 40px; overflow: auto;" class="cartItemDetailsContainer"></div><?php }?>
						</div>
						<div class="col-md-2">
							<span class="price" style="font-size: 13px; cursor: auto">
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['paytype']=='cur'){?>
								<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemPriceTotal'];?>
<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['taxInc']){?>*<?php }?>
							<?php }else{ ?>
								<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemCreditsTotal'];?>
 <sup><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</sup>
							<?php }?>
							</span>
						</div>
						<div class="col-md-2 orderDownloadColumn">
							<input 
								type="button" 
								value="<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['downloadableStatus']=='4'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['request'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadUpper'];?>
<?php }?>" 
								class="orderDownloadButton btn btn-xs btn-primary" 
								downloadableStatus="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['downloadableStatus'];?>
" 
								key="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['downloadKey'];?>
" 
								invoiceItemID="<?php echo $_smarty_tpl->tpl_vars['invoiceItemKey']->value;?>
" 
							/>
						</div>						
					</div>
				<?php } ?>
				<!--<input type="button" class="colorButton editButton" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
" style="float: right; margin-top: 6px;">-->
				</div>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['physicalInvoiceItems']->value){?>			
				<div class="container cartContainer">
					<h2 style="margin-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['items'];?>
</h2>					
					<?php  $_smarty_tpl->tpl_vars['invoiceItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoiceItem']->_loop = false;
 $_smarty_tpl->tpl_vars['invoiceItemKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['physicalInvoiceItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoiceItem']->key => $_smarty_tpl->tpl_vars['invoiceItem']->value){
$_smarty_tpl->tpl_vars['invoiceItem']->_loop = true;
 $_smarty_tpl->tpl_vars['invoiceItemKey']->value = $_smarty_tpl->tpl_vars['invoiceItem']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['photoPuzzleImg']){?>
							<?php $_smarty_tpl->tpl_vars['bPP'] = new Smarty_variable(1, null, 0);?>
						<?php }?>
				
					<div class="row" style="background-color: #efefef; margin-bottom: 3px;">
						<div class="col-md-2 cartThumbColumn">
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']&&!$_smarty_tpl->tpl_vars['bPP']->value){?>
								<a href="media.details.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['useMediaID'];?>
"><img src="image.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['encryptedID'];?>
=&type=icon&folderID=<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['media']['encryptedFID'];?>
==&size=60" class="thumb"></a>
							<?php }elseif($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['photo']){?>
								<img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['item_id'],'itemType'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['itemTypeShort'],'photoID'=>$_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['photo']['id'],'size'=>60),$_smarty_tpl);?>
" class="thumb">
							<?php }elseif($_smarty_tpl->tpl_vars['bPP']->value){?>
								<img src="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['photoPuzzleImg'];?>
" class="thumb" style="width: 150px;">
							<?php }else{ ?>
								<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/blank.cart.item.png"><!-- Spacer -->
							<?php }?>
						</div>
						<div class="col-md-6">
							<h2><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['name'];?>
</h2>
							<p class="cartItemDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['description'],200);?>
</p>
							
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['has_options']&&!$_smarty_tpl->tpl_vars['bPP']->value){?>
							<div class="cartItemDetailsContainerInline" style="margin-top: 6px; padding-left: 0; border-top: none;">
								<a href="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['oi_id'];?>
" itemType="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['item_type'];?>
" downloadOrderID="<?php echo $_smarty_tpl->tpl_vars['orderInfo']->value['uorder_id'];?>
" class="buttonLink cartItemDetailsButton">+</a> <?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['item_type']=='package'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewPackOptions'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewOptions'];?>
<?php }?>
								<!--<a href="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['cartEditLink'];?>
" class="colorLink cartItemEditLink" style="float: right;">[Edit]</a>-->
								<div style="display: none" id="optionsBox<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['oi_id'];?>
" class="optionsBox"></div>
								<!--ID: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
 - Type: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['item_type'];?>
-->
							</div>
							<?php }elseif($_smarty_tpl->tpl_vars['invoiceItem']->value['has_options']&&$_smarty_tpl->tpl_vars['bPP']->value){?>
								<div class="cartItemDetailsContainerInline">
								<?php  $_smarty_tpl->tpl_vars['gruppoOpzione'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gruppoOpzione']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['invoiceItem']->value['photoPuzzleGruppoOpzione']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gruppoOpzione']->key => $_smarty_tpl->tpl_vars['gruppoOpzione']->value){
$_smarty_tpl->tpl_vars['gruppoOpzione']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gruppoOpzione']->key;
?>
									<span><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['gruppoOpzione']->value];?>
: <?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['photoPuzzleOpzione'][$_smarty_tpl->tpl_vars['k']->value];?>
</span>
								<?php } ?>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?><?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['invoiceItem']->value,'title'=>'Item Details'),$_smarty_tpl);?>
<?php }?>
						</div>
						<div class="col-md-1">
							<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['quantity'];?>

						</div>
						<div class="col-md-1">
							<span class="price" style="font-size: 13px; cursor: auto">
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['paytype']=='cur'){?>
								<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemPriceTotal'];?>
<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['taxInc']){?>*<?php }?>
							<?php }else{ ?>
								<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemCreditsTotal'];?>
 <sup><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</sup>
							<?php }?>
							</span>
						</div>
						<div class="col-md-2 orderDownloadColumn">
							<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['physical_item']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['invoiceItem']->value['shippingStatusLang']];?>
<?php }?>
						</div>
					</div>
				<?php } ?>				
				<!--<input type="button" class="colorButton editButton" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
" style="float: right; margin-top: 6px;">-->
			<?php }?>
			<p class="totalsShownFooter">*<?php echo $_smarty_tpl->tpl_vars['lang']->value['totalsShown'];?>
 <strong><?php echo $_smarty_tpl->tpl_vars['adminCurrency']->value['code'];?>
</strong></p>
			</div>
		</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>