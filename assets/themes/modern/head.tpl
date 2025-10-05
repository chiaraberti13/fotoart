<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset={$pageEncoding}">
<title>{$metaDescription} {$metaTitle}</title>
<meta name="description" content="{$metaDescription}">
<meta name="keywords" content="{$metaKeywords}">
<meta name="robots" content="{$metaRobots}">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
{if $pageID != 'photoPuzzle'}
	<link href="{$baseURL}/assets/themes/{$theme}/css/bootstrap.min.css" rel="stylesheet" media="screen">
{else}
	<link href="{$baseURL}/assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="{$baseURL}/assets/css/photo.puzzle.css" rel="stylesheet" media="screen">
{/if}
{*<link href="{$baseURL}/assets/themes/{$theme}/css/bootstrap-responsive.css" rel="stylesheet" media="screen">*}
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
	<script src="{$baseURL}/assets/themes/{$theme}/js/html5shiv.js"></script>
	<script src="{$baseURL}/assets/themes/{$theme}/js/respond.min.js"></script>
<![endif]-->

{if ($access == 'private' or $loggedIn == 1) and $pageID != 'contributorAddMedia'}<meta http-equiv=refresh content="{$loginTimeout}; url={$baseURL}/login.php?cmd=logout">{/if}


<script type="text/javascript" src="{$baseURL}/assets/javascript/jquery/jquery.min.js"></script>
{*<script type="text/javascript" src="{$baseURL}/assets/javascript/jqueryUI/jqueryUI.js"></script>*}
{if $pageID != 'photoPuzzle'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/shared.min.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/themes/{$theme}/js/theme.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/public.min.js"></script>  
	<script type="text/javascript" src="{$baseURL}/assets/jwplayer/jwplayer.min.js"></script>
	{*<script type="text/javascript" src="{$baseURL}/assets/javascript/jstree/jquery.jstree.min.js"></script>*}

	{if $access == 'private' or $loggedIn == 1}	
		<script type="text/javascript" src="{$baseURL}/assets/javascript/swfobject.js"></script>
		<script type="text/javascript" src="{$baseURL}/assets/javascript/private.js"></script>	
		{* Uploadify *}
		<link rel="stylesheet" type="text/css" href="{$baseURL}/assets/themes/{$theme}/uploadify.css">
		<script type="text/javascript" src="{$baseURL}/assets/uploadify/jquery.uploadify.min.js"></script>
	{/if}
{/if}
<script type="text/javascript" language="javascript">
<!--
	var baseURL 	= '{$baseURL}';
	var theme		= '{$theme}';
	var colorScheme	= '{$colorScheme}';
	var imgPath		= '{$imgPath}';
	var pageID		= '{$pageID}';
	var pageMode	= '{$pageMode}';
	var miniCart	= '{$config.settings.minicart}';	
	var browser 	= { {foreach $browser as $key => $value} '{$key}':'{$value}'{if !$value@last},{/if} {/foreach} }
	
	{* Disable right clicking *}
	{if $config.settings.disable_right_click}
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
	{/if}

	/*
	* Currency Variables
	*/
	var numset = new Object();
	numset.cur_hide_denotation = 1;
	numset.cur_currency_id = '{$config.settings.cur_currency_id}';
	numset.cur_name = "{$config.settings.cur_name}";
	numset.cur_code = "{$config.settings.cur_code}";
	numset.cur_denotation = "{$config.settings.cur_denotation}";
	numset.cur_denotation_reset = '{$config.settings.cur_denotation}';
	numset.cur_decimal_separator = "{$config.settings.cur_decimal_separator}";
	numset.cur_decimal_places = {$config.settings.cur_decimal_places};
	numset.cur_thousands_separator = "{$config.settings.cur_thousands_separator}";		
	numset.cur_pos_num_format = {$config.settings.cur_pos_num_format};
	numset.cur_neg_num_format = {$config.settings.cur_neg_num_format};
	numset.exchange_rate = {$exchangeRate};
	/*
	* Number Variables
	*/	
	numset.decimal_separator = "{$config.settings.decimal_separator}";
	numset.decimal_places = {$config.settings.decimal_places};
	numset.thousands_separator = "{$config.settings.thousands_separator}";		
	numset.neg_num_format = {$config.settings.neg_num_format};
	numset.strip_ezeros = 0;
	/*
	* Tax values
	*/
	numset.tax_a = {$tax.tax_a_default};
	numset.tax_b = {$tax.tax_b_default};
	numset.tax_c = {$tax.tax_c_default};
-->
</script>

{if $config.settings.disable_printing}<link rel="stylesheet" type="text/css" media="print" href="{$baseURL}/assets/css/noprint.css">{/if}
{*<link rel="stylesheet" type="text/css" href="{$baseURL}/assets/css/public.css">*}
{if $activeLanguages.$selectedLanguage.rtl}
	<link rel="stylesheet" type="text/css" href="{$baseURL}/assets/themes/{$theme}/style.rtl.css">
{else}
	<link rel="stylesheet" type="text/css" href="{$baseURL}/assets/themes/{$theme}/{$colorScheme}.css">
{/if}
{*<link rel="stylesheet" type="text/css" href="{$baseURL}/assets/css/jqueryUI/smoothness/jquery-ui-1.8.12.custom.css">*}
{if $faviconRef == 1}<link rel="shortcut icon" href="{$baseURL}/favicon.ico">{/if}