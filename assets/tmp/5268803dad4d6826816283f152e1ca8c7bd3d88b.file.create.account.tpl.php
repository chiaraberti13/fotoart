<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 07:20:23
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/create.account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:148926191568b36e5c6b06c6-52480268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5268803dad4d6826816283f152e1ca8c7bd3d88b' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/create.account.tpl',
      1 => 1757834419,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '148926191568b36e5c6b06c6-52480268',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e5c902e25_88297656',
  'variables' => 
  array (
    'baseURL' => 0,
    'formNotice' => 0,
    'lang' => 0,
    'showMemberships' => 0,
    'msID' => 0,
    'form' => 0,
    'regForm' => 0,
    'countries' => 0,
    'config' => 0,
    'memberships' => 0,
    'membership' => 0,
    'selectedMembership' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e5c902e25_88297656')) {function content_68b36e5c902e25_88297656($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/function.html_options.php';
if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><!DOCTYPE HTML>
<html>
<head>
    <?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    
    <!-- jQuery deve essere il PRIMO JS caricato! -->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>

    <!-- Se esiste, carica qui public.min.js che contiene clicktoggle -->
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/public.min.js"></script>
    
    <!-- Ora puoi caricare i tuoi JS custom -->
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/create.account.js"></script>

    <!-- CSS per validazione form -->
    <style>
        /* Stili per validazione form di registrazione */
        .formError {
            border-color: #d32f2f !important;
            box-shadow: 0 0 5px rgba(211, 47, 47, 0.3) !important;
            background-color: rgba(255, 235, 235, 0.5) !important;
        }
        
        .formErrorMessage {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 3px;
            margin-bottom: 5px;
            display: block;
            font-weight: normal;
            line-height: 1.3;
        }
        
        input[type="checkbox"].formError {
            outline: 2px solid #d32f2f;
            outline-offset: 2px;
            background-color: transparent !important;
        }
        
        select.formError {
            border-color: #d32f2f !important;
            box-shadow: 0 0 5px rgba(211, 47, 47, 0.3) !important;
        }
        
        .email-check-loading {
            color: #666;
            font-size: 12px;
            margin-left: 5px;
            font-style: italic;
        }
        
        .requiredMark {
            color: #d32f2f;
            font-weight: bold;
        }
        
        .field-help {
            color: #666;
            font-size: 11px;
            display: block;
            margin-top: 2px;
        }
    </style>
    
    <script>
        $(function()
        {       
            /*
            * Display errors on fields with notices - MIGLIORATO
            */
            <?php if (in_array('emailBlocked',$_smarty_tpl->tpl_vars['formNotice']->value)){?>     displayFormError('#email','Email bloccata dal sistema');                <?php }?>
            <?php if (in_array('emailExists',$_smarty_tpl->tpl_vars['formNotice']->value)){?>       displayFormError('#email','Questa email è già registrata');             <?php }?>               
            <?php if (in_array('noFirstName',$_smarty_tpl->tpl_vars['formNotice']->value)){?>       displayFormError('#f_name','Il nome è obbligatorio');                  <?php }?>               
            <?php if (in_array('noLastName',$_smarty_tpl->tpl_vars['formNotice']->value)){?>        displayFormError('#l_name','Il cognome è obbligatorio');               <?php }?>               
            <?php if (in_array('noEmail',$_smarty_tpl->tpl_vars['formNotice']->value)){?>           displayFormError('#email','L\'email è obbligatoria');                  <?php }?>
            <?php if (in_array('noCompName',$_smarty_tpl->tpl_vars['formNotice']->value)){?>        displayFormError('#comp_name','Il nome azienda è obbligatorio');        <?php }?>
            <?php if (in_array('noPhone',$_smarty_tpl->tpl_vars['formNotice']->value)){?>           displayFormError('#phone','Il telefono è obbligatorio');               <?php }?>
            <?php if (in_array('noWebsite',$_smarty_tpl->tpl_vars['formNotice']->value)){?>         displayFormError('#website','Il sito web è obbligatorio');             <?php }?>
            <?php if (in_array('noCountry',$_smarty_tpl->tpl_vars['formNotice']->value)){?>         displayFormError('#country','Seleziona un paese');                     <?php }?>               
            <?php if (in_array('noAddress',$_smarty_tpl->tpl_vars['formNotice']->value)){?>         displayFormError('#address','L\'indirizzo è obbligatorio');            <?php }?>
            <?php if (in_array('noCity',$_smarty_tpl->tpl_vars['formNotice']->value)){?>            displayFormError('#city','La città è obbligatoria');                   <?php }?>
            <?php if (in_array('noState',$_smarty_tpl->tpl_vars['formNotice']->value)){?>           displayFormError('#state','La provincia/stato è obbligatoria');        <?php }?>
            <?php if (in_array('noPostalCode',$_smarty_tpl->tpl_vars['formNotice']->value)){?>      displayFormError('#postal_code','Il CAP è obbligatorio');              <?php }?>
            <?php if (in_array('noPassword',$_smarty_tpl->tpl_vars['formNotice']->value)){?>        displayFormError('#password','La password è obbligatoria');            <?php }?>
            <?php if (in_array('shortPassword',$_smarty_tpl->tpl_vars['formNotice']->value)){?>     displayFormError('#password','La password deve essere di almeno 6 caratteri');  <?php }?>
            <?php if (in_array('noSignupAgreement',$_smarty_tpl->tpl_vars['formNotice']->value)){?> displayFormError('#signupAgreement','Devi accettare i termini e condizioni');   <?php }?>
            <?php if (in_array('captchaError',$_smarty_tpl->tpl_vars['formNotice']->value)){?>      displayFormError('.g-recaptcha','Captcha non valido');    <?php }?>
        });
    </script>
</head>
<body>
    <?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <div id="container">
        <?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

        <?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
        
        
        <div class="container">
            <div class="row">
                <?php echo $_smarty_tpl->getSubTemplate ('subnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
     
                <div class="col-md-9">
                
                    <div class="content">
                        <h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccount'];?>
</h1>
                        <hr>
                        <?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccountMessage'];?>

                        
                        <form id="createAccountForm" class="cleanForm form-group" action="create.account.php" method="post"> 
                            <input type="hidden" name="showMemberships" value="<?php echo $_smarty_tpl->tpl_vars['showMemberships']->value;?>
">
                            <input type="hidden" name="msID" value="<?php echo $_smarty_tpl->tpl_vars['msID']->value;?>
">                    
                            
                            <h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['generalInfo'];?>
</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['firstName'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="text" id="f_name" name="f_name" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['f_name'];?>
" 
                                               require="require" errorMessage="Il nome è obbligatorio" 
                                               style="width: 306px;" class="form-control" maxlength="50">
                                    </div>
                                </div>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['lastName'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="text" id="l_name" name="l_name" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['l_name'];?>
" 
                                               require="require" errorMessage="Il cognome è obbligatorio" class="form-control" maxlength="50">
                                    </div>
                                </div>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="email" id="email" name="email" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['email'];?>
" 
                                               require="require" errorMessage="L'email è obbligatoria" 
                                               errorMessage2="Questa email è già registrata" 
                                               errorMessage3="Email bloccata dal sistema" 
                                               class="form-control" maxlength="100">
                                    </div>
                                </div>
                                <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formPhone']['status']){?>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formPhone']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['phone'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="tel" id="phone" name="phone" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['phone'];?>
" 
                                               <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formPhone']['status']==2){?>require="require"<?php }?> 
                                               errorMessage="Il telefono è obbligatorio" 
                                               class="form-control" maxlength="20">
                                    </div>
                                </div>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formCompanyName']['status']){?>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formCompanyName']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['companyName'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="comp_name" name="comp_name" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['comp_name'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formCompanyName']['status']==2){?>require="require"<?php }?> 
                                                   errorMessage="Il nome azienda è obbligatorio" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formWebsite']['status']){?>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formWebsite']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['website'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="url" id="website" name="website" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['website'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formWebsite']['status']==2){?>require="require"<?php }?> 
                                                   errorMessage="Il sito web è obbligatorio" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            
                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']){?>
                                <br><h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
</h2>
                                <div class="divTable">
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['country'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <select id="country" name="country" style="width: 306px;" class="form-control" 
                                                    <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> 
                                                    errorMessage="Seleziona un paese">
                                                <option value="">Seleziona paese</option>
                                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['countries']->value,'selected'=>$_smarty_tpl->tpl_vars['form']->value['country']),$_smarty_tpl);?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="address" name="address" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> 
                                                   value="<?php echo $_smarty_tpl->tpl_vars['form']->value['address'];?>
" 
                                                   errorMessage="L'indirizzo è obbligatorio" 
                                                   class="form-control" maxlength="200">
                                            <input type="text" name="address_2" id="address_2" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['address_2'];?>
" 
                                                   placeholder="Seconda riga indirizzo (opzionale)"
                                                   style="margin-top: 6px;" class="form-control" maxlength="200">
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['city'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="city" name="city" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['city'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> 
                                                   errorMessage="La città è obbligatoria" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['state'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="state" name="state" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['state'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> 
                                                   errorMessage="Inserisci provincia/stato" 
                                                   class="form-control" maxlength="100"
                                                   placeholder="es. Milano, Roma, California, Bayern...">
                                            <small class="field-help">
                                                Inserisci provincia, stato o regione
                                            </small>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['zip'];?>
:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="postal_code" name="postal_code" value="<?php echo $_smarty_tpl->tpl_vars['form']->value['postal_code'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> 
                                                   errorMessage="Il CAP è obbligatorio" 
                                                   class="form-control" maxlength="10">
                                        </div>
                                    </div>                              
                                </div>
                            <?php }?>
                            
                            <br><h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="password" id="password" name="password" 
                                               require="require" 
                                               errorMessage="La password è obbligatoria" 
                                               errorMessage2="Le password non corrispondono" 
                                               errorMessage3="La password deve essere di almeno 6 caratteri" 
                                               style="width: 306px;" class="form-control" 
                                               minlength="6" maxlength="50">
                                        <small style="color: #666; display: block; margin-top: 3px;">
                                            Minimo 6 caratteri
                                        </small>
                                    </div>
                                </div>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['verifyPass'];?>
:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="password" id="vpassword" name="vpassword" 
                                               require="require" errorMessage="Conferma la password" 
                                               class="form-control" minlength="6" maxlength="50">
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['captcha']){?>
                            <br><h2 class="infoHeader">Verifica Sicurezza</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;">
                                        <span class="requiredMark">*</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value['captcha'];?>
:
                                    </div>
                                    <div class="divTableCell captcha"><?php echo $_smarty_tpl->getSubTemplate ('captcha.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div>
                                </div>
                            </div>
                            <?php }?>
                            
                            <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formSignupAgreement']['status']){?>
                            <br><h2 class="infoHeader">Termini e Condizioni</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;">
                                        <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formSignupAgreement']['status']==2){?><span class="requiredMark">*</span> <?php }?><?php echo $_smarty_tpl->tpl_vars['lang']->value['agreements'];?>
:
                                    </div>
                                    <div class="divTableCell" style="padding-top: 14px;">
                                        <input type="checkbox" name="signupAgreement" id="signupAgreement" value="1" 
                                               <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formSignupAgreement']['status']==2){?>require="require"<?php }?> 
                                               errorMessage="Devi accettare i termini e condizioni"> 
                                        <label for="signupAgreement">
                                            <?php echo $_smarty_tpl->tpl_vars['lang']->value['readAgree'];?>
 
                                            <a href="<?php echo linkto(array('page'=>"content.php?id=11"),$_smarty_tpl);?>
" class="colorLink" target="_blank">
                                                <?php echo content(array('id'=>'signupAgreement','titleOnly'=>1),$_smarty_tpl);?>

                                            </a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            
                            <?php if ($_smarty_tpl->tpl_vars['showMemberships']->value){?>
                                <br><h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['membership'];?>
</h2>
                                <ul class="membershipList" style="list-style: none; padding: 0;">
                                    <?php  $_smarty_tpl->tpl_vars['membership'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['membership']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['memberships']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['membership']->key => $_smarty_tpl->tpl_vars['membership']->value){
$_smarty_tpl->tpl_vars['membership']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['membership']->key;
?>
                                        <li style="border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 10px; background-color: #fafafa;">
                                            <input type="radio" name="membership" id="membership_<?php echo $_smarty_tpl->tpl_vars['membership']->value['ms_id'];?>
" 
                                                   value="<?php echo $_smarty_tpl->tpl_vars['membership']->value['ums_id'];?>
" 
                                                   <?php if ($_smarty_tpl->tpl_vars['membership']->value['ums_id']==$_smarty_tpl->tpl_vars['selectedMembership']->value){?>checked="checked"<?php }?>>
                                            <label for="membership_<?php echo $_smarty_tpl->tpl_vars['membership']->value['ms_id'];?>
" style="font-weight: bold; cursor: pointer;">
                                                <?php echo $_smarty_tpl->tpl_vars['membership']->value['name'];?>

                                            </label> 
                                            <a href="membership.php?id=<?php echo $_smarty_tpl->tpl_vars['membership']->value['ums_id'];?>
" class="colorLink membershipWorkbox" 
                                               style="float: right;" target="_blank">[<?php echo mb_strtoupper($_smarty_tpl->tpl_vars['lang']->value['details'], 'UTF-8');?>
]</a>
                                            
                                            <?php if ($_smarty_tpl->tpl_vars['membership']->value['description']){?>
                                            <p class="membershipDetails" style="margin: 8px 0; color: #666; font-size: 14px;">
                                                <?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['membership']->value['description'],300);?>

                                            </p>
                                            <?php }?>
                                            
                                            <p class="membershipPriceDetails" style="margin: 8px 0 0 0; font-size: 14px;">
                                                <?php if ($_smarty_tpl->tpl_vars['membership']->value['mstype']=='free'){?>
                                                    <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPrice'];?>
: <span class="price" style="color: #2e7d32; font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['free'];?>
</span>
                                                <?php }?>           
                                                <?php if ($_smarty_tpl->tpl_vars['membership']->value['trail_status']){?>
                                                    <?php echo $_smarty_tpl->tpl_vars['lang']->value['freeTrial'];?>
: <span class="price" style="color: #2e7d32; font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['membership']->value['trial_length_num'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['membership']->value['trial_length_period']];?>
</span><br>
                                                <?php }?>                                               
                                                <?php if ($_smarty_tpl->tpl_vars['membership']->value['setupfee']){?>
                                                    <?php echo $_smarty_tpl->tpl_vars['lang']->value['setupFee'];?>
: <span class="price" style="color: #2e7d32; font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['membership']->value['setupfee']['display'];?>
</span><br>
                                                <?php }?>                                               
                                                <?php if ($_smarty_tpl->tpl_vars['membership']->value['mstype']=='recurring'){?>
                                                    <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPrice'];?>
: <span class="price" style="color: #2e7d32; font-weight: bold;"><?php echo $_smarty_tpl->tpl_vars['membership']->value['price']['display'];?>
</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['membership']->value['period']];?>

                                                <?php }?>
                                            </p>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php }?>
                            
                            <p><span class="requiredMark">* <?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
</span></p>  
                            
                            <div style="text-align: right; margin-top: 20px;">
                                <input type="submit" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" class="btn btn-primary" 
                                       style="padding: 10px 30px; font-size: 16px;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>