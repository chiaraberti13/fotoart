<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 11:38:57
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:163838516968b36e079b9d29-70638164%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ddf2ef1202b5ef88d5a1ede391e53cc74eb1a348' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/head.tpl',
      1 => 1757849928,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '163838516968b36e079b9d29-70638164',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e07a98062_72827431',
  'variables' => 
  array (
    'pageEncoding' => 0,
    'metaDescription' => 0,
    'metaTitle' => 0,
    'metaKeywords' => 0,
    'metaRobots' => 0,
    'pageID' => 0,
    'baseURL' => 0,
    'theme' => 0,
    'access' => 0,
    'loggedIn' => 0,
    'loginTimeout' => 0,
    'colorScheme' => 0,
    'imgPath' => 0,
    'pageMode' => 0,
    'config' => 0,
    'browser' => 0,
    'key' => 0,
    'value' => 0,
    'exchangeRate' => 0,
    'tax' => 0,
    'selectedLanguage' => 0,
    'activeLanguages' => 0,
    'faviconRef' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e07a98062_72827431')) {function content_68b36e07a98062_72827431($_smarty_tpl) {?><meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['pageEncoding']->value;?>
">
<title><?php echo $_smarty_tpl->tpl_vars['metaDescription']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['metaTitle']->value;?>
</title>
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['metaDescription']->value;?>
">
<meta name="keywords" content="<?php echo $_smarty_tpl->tpl_vars['metaKeywords']->value;?>
">
<meta name="robots" content="<?php echo $_smarty_tpl->tpl_vars['metaRobots']->value;?>
">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if ($_smarty_tpl->tpl_vars['pageID']->value!='photoPuzzle'){?>
	<link href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/css/bootstrap.min.css" rel="stylesheet" media="screen">
<?php }else{ ?>
	<link href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/css/photo.puzzle.css" rel="stylesheet" media="screen">
<?php }?>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
	<script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/js/html5shiv.js"></script>
	<script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/js/respond.min.js"></script>
<![endif]-->

<?php if (($_smarty_tpl->tpl_vars['access']->value=='private'||$_smarty_tpl->tpl_vars['loggedIn']->value==1)&&$_smarty_tpl->tpl_vars['pageID']->value!='contributorAddMedia'){?><meta http-equiv=refresh content="<?php echo $_smarty_tpl->tpl_vars['loginTimeout']->value;?>
; url=<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/login.php?cmd=logout"><?php }?>


<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/jquery/jquery.min.js"></script>

<?php if ($_smarty_tpl->tpl_vars['pageID']->value!='photoPuzzle'){?>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/shared.min.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/js/theme.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/public.min.js"></script>  
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/jwplayer/jwplayer.min.js"></script>
	

	<?php if ($_smarty_tpl->tpl_vars['access']->value=='private'||$_smarty_tpl->tpl_vars['loggedIn']->value==1){?>	
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/swfobject.js"></script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/private.js"></script>	
		
		<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/uploadify.css">
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/uploadify/jquery.uploadify.min.js"></script>
	<?php }?>
<?php }?>
<script type="text/javascript" language="javascript">
<!--
	var baseURL 	= '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
';
	var theme		= '<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
';
	var colorScheme	= '<?php echo $_smarty_tpl->tpl_vars['colorScheme']->value;?>
';
	var imgPath		= '<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
';
	var pageID		= '<?php echo $_smarty_tpl->tpl_vars['pageID']->value;?>
';
	var pageMode	= '<?php echo $_smarty_tpl->tpl_vars['pageMode']->value;?>
';
	var miniCart	= '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['minicart'];?>
';	
	var browser 	= { <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['browser']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['value']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['value']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['value']->key;
 $_smarty_tpl->tpl_vars['value']->iteration++;
 $_smarty_tpl->tpl_vars['value']->last = $_smarty_tpl->tpl_vars['value']->iteration === $_smarty_tpl->tpl_vars['value']->total;
?> '<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
':'<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
'<?php if (!$_smarty_tpl->tpl_vars['value']->last){?>,<?php }?> <?php } ?> }
	
	
	<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['disable_right_click']){?>
	/*
	* Disable right clicking
	*/
	$(function()
	{
		$(document).bind("contextmenu",function(e)
		{
       		return false;
    	});
	});
	<?php }?>

	/*
	* Currency Variables
	*/
	var numset = new Object();
	numset.cur_hide_denotation = 1;
	numset.cur_currency_id = '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_currency_id'];?>
';
	numset.cur_name = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_name'];?>
";
	numset.cur_code = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_code'];?>
";
	numset.cur_denotation = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_denotation'];?>
";
	numset.cur_denotation_reset = '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_denotation'];?>
';
	numset.cur_decimal_separator = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_decimal_separator'];?>
";
	numset.cur_decimal_places = <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_decimal_places'];?>
;
	numset.cur_thousands_separator = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_thousands_separator'];?>
";		
	numset.cur_pos_num_format = <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_pos_num_format'];?>
;
	numset.cur_neg_num_format = <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['cur_neg_num_format'];?>
;
	numset.exchange_rate = <?php echo $_smarty_tpl->tpl_vars['exchangeRate']->value;?>
;
	/*
	* Number Variables
	*/	
	numset.decimal_separator = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['decimal_separator'];?>
";
	numset.decimal_places = <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['decimal_places'];?>
;
	numset.thousands_separator = "<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['thousands_separator'];?>
";		
	numset.neg_num_format = <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['neg_num_format'];?>
;
	numset.strip_ezeros = 0;
	/*
	* Tax values
	*/
	numset.tax_a = <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_a_default'];?>
;
	numset.tax_b = <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_b_default'];?>
;
	numset.tax_c = <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_c_default'];?>
;
-->
</script>

<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['disable_printing']){?><link rel="stylesheet" type="text/css" media="print" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/css/noprint.css"><?php }?>

<?php if ($_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['rtl']){?>
	<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/style.rtl.css">
<?php }else{ ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['colorScheme']->value;?>
.css">
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['faviconRef']->value==1){?><link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/favicon.ico"><?php }?><?php }} ?>