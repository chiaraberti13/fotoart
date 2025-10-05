<?php /* Smarty version Smarty-3.1.8, created on 2025-08-30 22:04:40
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.review.tpl" */ ?>
<?php /*%%SmartyHeaderCode:166553518168b37578c9e2e6-01218904%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bc2441221cb731418aa6da4ccb68e807caa1f57a' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.review.tpl',
      1 => 1755093992,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '166553518168b37578c9e2e6-01218904',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'config' => 0,
    'stepNumber' => 0,
    'lang' => 0,
    'cartTotals' => 0,
    'shippingAddress' => 0,
    'billingAddress' => 0,
    'digitalInvoiceItems' => 0,
    'invoiceItem' => 0,
    'imgPath' => 0,
    'cartItem' => 0,
    'debugMode' => 0,
    'physicalInvoiceItems' => 0,
    'bPP' => 0,
    'gruppoOpzione' => 0,
    'k' => 0,
    'priCurrency' => 0,
    'selectedCurrency' => 0,
    'tax' => 0,
    'freeCart' => 0,
    'gateways' => 0,
    'gateway' => 0,
    'cartInfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b3757918f028_51919921',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b3757918f028_51919921')) {function content_68b3757918f028_51919921($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.review.js"></script>
	<script type="text/javascript">
		var settingsPurchaseAgreement = '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['purchase_agreement'];?>
';
	</script>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			
			<div class="row">
				<div class="col-md-12">
					<ul class="cartStepsBar <?php if ($_smarty_tpl->tpl_vars['stepNumber']->value['b']){?>cartStepsBar25<?php }else{ ?>cartStepsBar33<?php }?>">
						<li class="off cart"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['a'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
</div></li>
						<?php if ($_smarty_tpl->tpl_vars['stepNumber']->value['b']){?><li class="off shipping"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['b'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipping'];?>
</div></li><?php }?>
						<li class="on"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['c'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['reviewOrder'];?>
</div></li>
						<li class="off"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['d'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['payment'];?>
</div></li>
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8">		
						<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['shippingRequired']){?>
							<div style="clear: both; overflow: auto;">
								<div class="cartReviewAddresses">
									<div>
										<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipTo'];?>
:</h2>
										<p>
											<strong><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['name'];?>
</strong><br>
											<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['address'];?>
<br>
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['address2']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['address2'];?>
<br><?php }?>
											<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['city'];?>
, <?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['state'];?>
 <?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['postalCode'];?>
<br>
											<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['country'];?>
<br>
											<!--
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['phone'];?>
<br><?php }?>
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['email']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
<br><?php }?>
											-->
										</p>
									</div>
								</div>
								
								<div class="cartReviewAddresses">
									<div style="margin-left: 10px;">
										<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['billTo'];?>
:</h2>
										<p>
											<strong><?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['name'];?>
</strong><br>
											<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['address'];?>
<br>
											<?php if ($_smarty_tpl->tpl_vars['billingAddress']->value['address2']){?><?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['address2'];?>
<br><?php }?>
											<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['city'];?>
, <?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['state'];?>
 <?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['postalCode'];?>
<br>
											<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['country'];?>
<br>
											<!--
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['phone'];?>
<br><?php }?>
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['email']){?><?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
<br><?php }?>
											-->
										</p>
									</div>
								</div>
							</div>
						<?php }?>
						
						<?php if ($_smarty_tpl->tpl_vars['digitalInvoiceItems']->value){?>
							<h2 style="margin-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloads'];?>
</h2>
							<div class="container cartContainer">
								<?php  $_smarty_tpl->tpl_vars['invoiceItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoiceItem']->_loop = false;
 $_smarty_tpl->tpl_vars['invoiceItemKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['digitalInvoiceItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoiceItem']->key => $_smarty_tpl->tpl_vars['invoiceItem']->value){
$_smarty_tpl->tpl_vars['invoiceItem']->_loop = true;
 $_smarty_tpl->tpl_vars['invoiceItemKey']->value = $_smarty_tpl->tpl_vars['invoiceItem']->key;
?>
								<div class="row">
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
									<div class="col-md-5">
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
											</p>
										<?php }else{ ?>
											<p class="cartItemDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['description'],200);?>
</p>
										<?php }?>
										
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['has_options']){?>
											<div class="cartItemDetailsContainer">
												<a href="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['oi_id'];?>
" itemType="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['item_type'];?>
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
										<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?><?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['invoiceItem']->value,'title'=>'Cart Item'),$_smarty_tpl);?>
<?php }?>
										
									</div>
									<div class="col-md-2">
										<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['quantity'];?>

									</div>
									<div class="col-md-3">
										<span class="price" style="font-size: 13px; cursor: auto">
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['paytype']=='cur'){?>
											<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemPriceTotalLocal']['display'];?>
<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['taxInc']){?>*<?php }?>
										<?php }else{ ?>
											<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemCreditsTotal'];?>
 <sup><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</sup>
										<?php }?>
										</span><br>
									</div>
								</div>
								<?php } ?>
							</div>
							<input type="button" class="btn btn-xs btn-success editButton" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
" style="float: right; margin-top: 6px;"><br><br>
						<?php }?>
						
						<?php if ($_smarty_tpl->tpl_vars['physicalInvoiceItems']->value){?>
							<h2 style="margin-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['items'];?>
</h2>
							<div class="container cartContainer">
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
								<div class="row">
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
" class="thumb" style="width: 100px;">
										<?php }else{ ?>
											<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/blank.cart.item.png">
										<?php }?>
									</div>
									<div class="col-md-5">
										<h2><?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['name'];?>
</h2>
										<p class="cartItemDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['invoiceItem']->value['itemDetails']['description'],200);?>
</p>	
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['has_options']&&!$_smarty_tpl->tpl_vars['bPP']->value){?>
											<div class="cartItemDetailsContainer">
												<a href="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['oi_id'];?>
" itemType="<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['item_type'];?>
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
											<div>
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
									</div>
									<div class="col-md-2">
										<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['quantity'];?>

									</div>
									<div class="col-md-3">
										<span class="price" style="font-size: 13px; cursor: auto">
										<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['paytype']=='cur'){?>
											<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemPriceTotalLocal']['display'];?>
<?php if ($_smarty_tpl->tpl_vars['invoiceItem']->value['taxInc']){?>*<?php }?>
										<?php }else{ ?>
											<?php echo $_smarty_tpl->tpl_vars['invoiceItem']->value['lineItemCreditsTotal'];?>
 <sup><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</sup>
										<?php }?>
										</span>
									</div>
								</div>
								<?php } ?>
							</div>
							<input type="button" class="btn btn-xs btn-success editButton" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
" style="float: right; margin-top: 6px;">
						<?php }?>
					</div>
						
					<div class="col-md-4">
					
						<div class="cartTotalList">
							<?php if ($_smarty_tpl->tpl_vars['priCurrency']->value['currency_id']!=$_smarty_tpl->tpl_vars['selectedCurrency']->value){?><div class="cartTotalListWarning"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png"/><?php echo $_smarty_tpl->tpl_vars['lang']->value['cartTotalListWarning'];?>
</div><?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['priceSubTotal']){?>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subtotal'];?>
:</div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['subTotalLocal']['display'];?>
</span></div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['shippingRequired']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipping'];?>
:</div>
											<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearShipping']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['shippingTotalLocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxA']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxAName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_a_default'];?>
%)-->:</div>
											<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxALocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxB']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxBName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_b_default'];?>
%)-->:</div>
											<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxBLocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxC']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxCName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_c_default'];?>
%)-->:</div>
											<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxCLocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['totalDiscounts']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['discounts'];?>
:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalDiscountsLocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<div class="divTableRow">
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['total'];?>
:</span></div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['cartGrandTotalLocal']['display'];?>
</span></div>
									</div>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotal']){?>	
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsSubtotal'];?>
:</div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotal'];?>
</span></div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['totalCreditsDiscounts']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsDiscounts'];?>
:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalCreditsDiscounts'];?>
</span></div>
										</div>
									<?php }?>
									<div class="divTableRow">
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
:</span></div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsTotal'];?>
</span></div>
									</div>
								</div>
							<?php }?>
						</div>
						
						<?php if ($_smarty_tpl->tpl_vars['lang']->value['taxMessage']){?>
							<div class="cartTotalList">
								<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxMessage'];?>

							</div>
						<?php }?>
						
						<form id="cartPaymentForm" action="cart.payment.php" method="post">							
						<div class="cartTotalList paymentGatewaysBox">
							<?php if ($_smarty_tpl->tpl_vars['freeCart']->value){?>
								<input type="hidden" name="paymentType" value="freeCart">
							<?php }else{ ?>
								<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['paymentOptions'];?>
:</h2>
								<ul>
									<?php  $_smarty_tpl->tpl_vars['gateway'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gateway']->_loop = false;
 $_smarty_tpl->tpl_vars['gatewayKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['gateways']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['gateway']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['gateway']->key => $_smarty_tpl->tpl_vars['gateway']->value){
$_smarty_tpl->tpl_vars['gateway']->_loop = true;
 $_smarty_tpl->tpl_vars['gatewayKey']->value = $_smarty_tpl->tpl_vars['gateway']->key;
 $_smarty_tpl->tpl_vars['gateway']->index++;
 $_smarty_tpl->tpl_vars['gateway']->first = $_smarty_tpl->tpl_vars['gateway']->index === 0;
?>
										<li>
											<input type="radio" name="paymentType" value="<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
" id="paymentGateway<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['gateway']->first){?>checked="checked"<?php }?>>
											<?php if ($_smarty_tpl->tpl_vars['gateway']->value['logo']){?><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/logos/<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
.png"><?php }?>
											<label for="paymentGateway<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['gateway']->value['displayName'];?>
</label>
											<p><?php echo $_smarty_tpl->tpl_vars['gateway']->value['publicDescription'];?>
</p>
										</li>
									<?php } ?>
								</ul>
							<?php }?>
						</div>
						
						<div class="cartTotalList" <?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['email']){?>style="display: none;"<?php }?>>
							<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourEmail'];?>
:</h2>
							<div><input type="text" name="email" id="email" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
" class="form-control"></div>
						</div>
						
						<div class="cartTotalList">
							<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['purchase_agreement']){?><p style="float: left;"><input type="checkbox" name="purchaseAgreement" id="purchaseAgreement" value="1" style="vertical-align:middle; margin-top: -3px"> <label for="purchaseAgreement"><?php echo $_smarty_tpl->tpl_vars['lang']->value['iAgree'];?>
 <strong><a href="<?php echo linkto(array('page'=>'purchase.agreement.php'),$_smarty_tpl);?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseAgreement'];?>
</a></strong></label></p><?php }?>
							<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['checkout'];?>
" style="float: right" id="cartReviewButton" class="btn btn-xs btn-success">
						</div>
						</form>
					</div>						
				</div>
			</div>
				
			</div>
			
			
			<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['shippingAddress']->value,'title'=>'Shipping Address'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['billingAddress']->value,'title'=>'Billing Address'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartInfo']->value,'title'=>'Cart Info'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartTotals']->value,'title'=>'Cart Totals'),$_smarty_tpl);?>

			<?php }?>
			
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>