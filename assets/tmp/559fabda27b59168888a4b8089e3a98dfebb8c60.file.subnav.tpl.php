<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 20:55:28
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/subnav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:52225282368b36e57aab039-76550520%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '559fabda27b59168888a4b8089e3a98dfebb8c60' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/subnav.tpl',
      1 => 1757796004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '52225282368b36e57aab039-76550520',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e57d606e0_67390663',
  'variables' => 
  array (
    'featuredNewsRows' => 0,
    'lang' => 0,
    'config' => 0,
    'featuredNews' => 0,
    'news' => 0,
    'galleriesData' => 0,
    'mainLevelGalleries' => 0,
    'galID' => 0,
    'contentPages' => 0,
    'content' => 0,
    'contentBlocks' => 0,
    'featuredPrintsRows' => 0,
    'pageID' => 0,
    'featuredPrints' => 0,
    'print' => 0,
    'featuredProductsRows' => 0,
    'featuredProducts' => 0,
    'product' => 0,
    'featuredPackagesRows' => 0,
    'featuredPackages' => 0,
    'package' => 0,
    'featuredCollectionsRows' => 0,
    'featuredCollections' => 0,
    'collection' => 0,
    'featuredPromotionsRows' => 0,
    'featuredPromotions' => 0,
    'promotion' => 0,
    'featuredSubscriptionsRows' => 0,
    'featuredSubscriptions' => 0,
    'subscription' => 0,
    'featuredCreditsRows' => 0,
    'featuredCredits' => 0,
    'credits' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e57d606e0_67390663')) {function content_68b36e57d606e0_67390663($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><div class="col-md-3 hidden-xs">
	<!--<a href="#"><img class="img-responsive img-circle" src="images/panda.png"></a>-->
	
	<?php if ($_smarty_tpl->tpl_vars['featuredNewsRows']->value){?>
		<div class="subNavFeaturedBox" id="featuredNews">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['news'];?>
 <?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_news']){?><a href="<?php echo linkto(array('page'=>'rss.php?mode=news'),$_smarty_tpl);?>
" class="btn btn-xxs btn-warning"><?php echo $_smarty_tpl->tpl_vars['lang']->value['rss'];?>
</a><?php }?></h2>
			<ul>
			<?php  $_smarty_tpl->tpl_vars['news'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['news']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredNews']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['news']->key => $_smarty_tpl->tpl_vars['news']->value){
$_smarty_tpl->tpl_vars['news']->_loop = true;
?>
				<li><span class="newsDate"><?php echo $_smarty_tpl->tpl_vars['news']->value['display_date'];?>
</span><br><a href="<?php echo $_smarty_tpl->tpl_vars['news']->value['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['news']->value['title'];?>
</a></li>
			<?php } ?>
			</ul>
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['news']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'news.php'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['galleriesData']->value){?>
		<nav style="margin-bottom: 20px;">		
			<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['galleries'];?>
</h3>
			<ul class="">
				<?php  $_smarty_tpl->tpl_vars['gallery'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gallery']->_loop = false;
 $_smarty_tpl->tpl_vars['galID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['mainLevelGalleries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gallery']->key => $_smarty_tpl->tpl_vars['gallery']->value){
$_smarty_tpl->tpl_vars['gallery']->_loop = true;
 $_smarty_tpl->tpl_vars['galID']->value = $_smarty_tpl->tpl_vars['gallery']->key;
?>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['galID']->value]['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['galID']->value]['name'];?>
 <?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['gallery_count']&&$_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['galID']->value]['gallery_count']){?>(<?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['galID']->value]['gallery_count'];?>
)<?php }?></a></li>
				<?php } ?>
			</ul>		
		</nav>
	<?php }?>
	
	<?php if (count($_smarty_tpl->tpl_vars['contentPages']->value)>0){?>
		<hr>
		<div class="">
			<ul>
			<?php  $_smarty_tpl->tpl_vars['content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['content']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contentPages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['content']->key => $_smarty_tpl->tpl_vars['content']->value){
$_smarty_tpl->tpl_vars['content']->_loop = true;
?>
				<li>
				<?php if ($_smarty_tpl->tpl_vars['content']->value['linked']){?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['content']->value['linked'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['content']->value['name'];?>
</a>
				<?php }else{ ?>
				<a href="<?php echo linkto(array('page'=>"content.php?id=".($_smarty_tpl->tpl_vars['content']->value['content_id'])),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['content']->value['name'];?>
</a>
				<?php }?>
				</li>
			<?php } ?>
			</ul>
		</div>
	<?php }?>
	
	<?php  $_smarty_tpl->tpl_vars['content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['content']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contentBlocks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['content']->key => $_smarty_tpl->tpl_vars['content']->value){
$_smarty_tpl->tpl_vars['content']->_loop = true;
?>
		<?php if ($_smarty_tpl->tpl_vars['content']->value['specType']=='sncb'){?>		
			<div class="subNavCustomContent">
				<h3><?php echo $_smarty_tpl->tpl_vars['content']->value['name'];?>
</h3>
				<div><?php echo $_smarty_tpl->tpl_vars['content']->value['content'];?>
</div>
			</div>
		<?php }?>
	<?php } ?>
	
													
	<?php if ($_smarty_tpl->tpl_vars['featuredPrintsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredPrints" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredPrints'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['print'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['print']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredPrints']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['print']->key => $_smarty_tpl->tpl_vars['print']->value){
$_smarty_tpl->tpl_vars['print']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">
					<?php if ($_smarty_tpl->tpl_vars['print']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['print']->value['print_id'],'itemType'=>'print','photoID'=>$_smarty_tpl->tpl_vars['print']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['print']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['print']->value['name'];?>
</a></h3>
					<p class="featuredDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['print']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['print']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['print']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['print']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart']==3&&$_smarty_tpl->tpl_vars['config']->value['settings']['credits_print']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['print']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['print']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
				</div>
			<?php } ?>					
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['printpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=prints'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
			
							
	<?php if ($_smarty_tpl->tpl_vars['featuredProductsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredProducts" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredProducts'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredProducts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">
					<?php if ($_smarty_tpl->tpl_vars['product']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['product']->value['prod_id'],'itemType'=>'prod','photoID'=>$_smarty_tpl->tpl_vars['product']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['product']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['product']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['product']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart']==3&&$_smarty_tpl->tpl_vars['config']->value['settings']['credits_prod']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['product']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
				</div>
			<?php } ?>						
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['prodpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=products'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
														
	<?php if ($_smarty_tpl->tpl_vars['featuredPackagesRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredPackages" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredPackages'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['package'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['package']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredPackages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['package']->key => $_smarty_tpl->tpl_vars['package']->value){
$_smarty_tpl->tpl_vars['package']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">
					<?php if ($_smarty_tpl->tpl_vars['package']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['package']->value['pack_id'],'itemType'=>'pack','photoID'=>$_smarty_tpl->tpl_vars['package']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['package']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['package']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['package']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['package']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['package']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart']==3&&$_smarty_tpl->tpl_vars['config']->value['settings']['credits_pack']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['package']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
				</div>
			<?php } ?>					
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['packpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=packages'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
														
	<?php if ($_smarty_tpl->tpl_vars['featuredCollectionsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredCollections" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredCollections'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['collection'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['collection']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredCollections']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['collection']->key => $_smarty_tpl->tpl_vars['collection']->value){
$_smarty_tpl->tpl_vars['collection']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">					
					<?php if ($_smarty_tpl->tpl_vars['collection']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['collection']->value['coll_id'],'itemType'=>'coll','photoID'=>$_smarty_tpl->tpl_vars['collection']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['collection']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['collection']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['collection']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['collection']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['collection']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['collection']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart']==3&&$_smarty_tpl->tpl_vars['config']->value['settings']['credits_coll']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['collection']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['collection']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
				</div>
			<?php } ?>
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['printpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=prints'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
									
	<?php if ($_smarty_tpl->tpl_vars['featuredPromotionsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredPromos" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['promotions'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['promotion'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['promotion']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredPromotions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['promotion']->key => $_smarty_tpl->tpl_vars['promotion']->value){
$_smarty_tpl->tpl_vars['promotion']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">
					<?php if ($_smarty_tpl->tpl_vars['promotion']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['promotion']->value['promo_id'],'itemType'=>'promo','photoID'=>$_smarty_tpl->tpl_vars['promotion']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['promotion']->value['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['promotion']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['promotion']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['promotion']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['promotion']->value['price']['display'];?>
</span><?php }?></p>
				</div>
			<?php } ?>				
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['promopage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'promotions.php'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
									
	<?php if ($_smarty_tpl->tpl_vars['featuredSubscriptionsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredSubs" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredSubscriptions'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['subscription'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subscription']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredSubscriptions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subscription']->key => $_smarty_tpl->tpl_vars['subscription']->value){
$_smarty_tpl->tpl_vars['subscription']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">				
					<?php if ($_smarty_tpl->tpl_vars['subscription']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['subscription']->value['sub_id'],'itemType'=>'sub','photoID'=>$_smarty_tpl->tpl_vars['subscription']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['subscription']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['subscription']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['subscription']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['subscription']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['subscription']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['subscription']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart']==3&&$_smarty_tpl->tpl_vars['config']->value['settings']['credits_sub']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['subscription']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['subscription']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
				</div>
			<?php } ?>			
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['subpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=subscriptions'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
									
	<?php if ($_smarty_tpl->tpl_vars['featuredCreditsRows']->value&&$_smarty_tpl->tpl_vars['pageID']->value!='featured'){?>
		<div id="featuredCredits" class="subNavFeaturedBox">
			<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredCredits'];?>
</h2>
			<?php  $_smarty_tpl->tpl_vars['credits'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['credits']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredCredits']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['credits']->key => $_smarty_tpl->tpl_vars['credits']->value){
$_smarty_tpl->tpl_vars['credits']->_loop = true;
?>
				<div class="workboxLinkAttach subNavFeaturedItem">
					<?php if ($_smarty_tpl->tpl_vars['credits']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['credits']->value['credit_id'],'itemType'=>'credit','photoID'=>$_smarty_tpl->tpl_vars['credits']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
" class="lnFeaturedPS"><?php }?>
					<h3><a href="<?php echo $_smarty_tpl->tpl_vars['credits']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['credits']->value['name'];?>
</a></h3>
					<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['credits']->value['description'],60);?>
</p>
					<p class="featuredPrice"><?php if ($_smarty_tpl->tpl_vars['credits']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['credits']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['credits']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?></p>
				</div>
			<?php } ?>				
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['creditpage']){?><p class="text-right"><a href="<?php echo linkto(array('page'=>'featured.php?mode=credits'),$_smarty_tpl);?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['more'];?>
 &raquo;</a></p><?php }?>
		</div>
	<?php }?>
	
	
						
</div><?php }} ?>