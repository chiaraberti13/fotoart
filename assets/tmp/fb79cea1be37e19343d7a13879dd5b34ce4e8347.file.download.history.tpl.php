<?php /* Smarty version Smarty-3.1.8, created on 2025-08-30 22:05:55
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/download.history.tpl" */ ?>
<?php /*%%SmartyHeaderCode:45552826968b375c3095a65-50200116%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb79cea1be37e19343d7a13879dd5b34ce4e8347' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/download.history.tpl',
      1 => 1755093993,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '45552826968b375c3095a65-50200116',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang' => 0,
    'downloadsRows' => 0,
    'downloadsArray' => 0,
    'download' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b375c30e0b45_03327290',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b375c30e0b45_03327290')) {function content_68b375c30e0b45_03327290($_smarty_tpl) {?><!DOCTYPE HTML>
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
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadHistory'];?>
</h1>
					<hr>
					<?php if ($_smarty_tpl->tpl_vars['downloadsRows']->value){?>
						<table class="dataTable">
							<tr>
								<th style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['media'];?>
</th>
								<th style="width: 100%"><?php echo $_smarty_tpl->tpl_vars['lang']->value['dateDownloadUpper'];?>
</th>
								<!--<th style="text-align: center">VERSION</th>-->
								<th style="white-space: nowrap; text-align: center"><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadTypeUpper'];?>
</th>
							</tr>
							<?php  $_smarty_tpl->tpl_vars['download'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['download']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['downloadsArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['download']->key => $_smarty_tpl->tpl_vars['download']->value){
$_smarty_tpl->tpl_vars['download']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['download']->key;
?>
								<tr>
									<td style="text-align: center"><img src="<?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['download']->value['media']['encryptedID'],'type'=>'thumb','folderID'=>$_smarty_tpl->tpl_vars['download']->value['media']['encryptedFID'],'size'=>35),$_smarty_tpl);?>
" class="genericImgBorder"><!--<?php echo $_smarty_tpl->tpl_vars['download']->value['asset_id'];?>
--></td>
									<td><?php echo $_smarty_tpl->tpl_vars['download']->value['download_date_display'];?>
</td>
									<!--<td style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['download']->value['dsp_id'];?>
</td>-->
									<td style="text-align: center"><?php echo $_smarty_tpl->tpl_vars['download']->value['download_type_display'];?>
<?php if ($_smarty_tpl->tpl_vars['download']->value['dl_type_id']){?> <?php echo $_smarty_tpl->tpl_vars['download']->value['dl_type_id'];?>
<?php }?></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noDownloads'];?>
</p>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>