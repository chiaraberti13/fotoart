<!DOCTYPE HTML>
<html>
<head>
    {include file='head.tpl'}
    
    <!-- jQuery deve essere il PRIMO JS caricato! -->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>

    <!-- Se esiste, carica qui public.min.js che contiene clicktoggle -->
    <script type="text/javascript" src="{$baseURL}/assets/javascript/public.min.js"></script>
    
    <!-- Ora puoi caricare i tuoi JS custom -->
    <script type="text/javascript" src="{$baseURL}/assets/javascript/create.account.js"></script>

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
            {if in_array('emailBlocked',$formNotice)}     displayFormError('#email','Email bloccata dal sistema');                {/if}
            {if in_array('emailExists',$formNotice)}       displayFormError('#email','Questa email è già registrata');             {/if}               
            {if in_array('noFirstName',$formNotice)}       displayFormError('#f_name','Il nome è obbligatorio');                  {/if}               
            {if in_array('noLastName',$formNotice)}        displayFormError('#l_name','Il cognome è obbligatorio');               {/if}               
            {if in_array('noEmail',$formNotice)}           displayFormError('#email','L\'email è obbligatoria');                  {/if}
            {if in_array('noCompName',$formNotice)}        displayFormError('#comp_name','Il nome azienda è obbligatorio');        {/if}
            {if in_array('noPhone',$formNotice)}           displayFormError('#phone','Il telefono è obbligatorio');               {/if}
            {if in_array('noWebsite',$formNotice)}         displayFormError('#website','Il sito web è obbligatorio');             {/if}
            {if in_array('noCountry',$formNotice)}         displayFormError('#country','Seleziona un paese');                     {/if}               
            {if in_array('noAddress',$formNotice)}         displayFormError('#address','L\'indirizzo è obbligatorio');            {/if}
            {if in_array('noCity',$formNotice)}            displayFormError('#city','La città è obbligatoria');                   {/if}
            {if in_array('noState',$formNotice)}           displayFormError('#state','La provincia/stato è obbligatoria');        {/if}
            {if in_array('noPostalCode',$formNotice)}      displayFormError('#postal_code','Il CAP è obbligatorio');              {/if}
            {if in_array('noPassword',$formNotice)}        displayFormError('#password','La password è obbligatoria');            {/if}
            {if in_array('shortPassword',$formNotice)}     displayFormError('#password','La password deve essere di almeno 6 caratteri');  {/if}
            {if in_array('noSignupAgreement',$formNotice)} displayFormError('#signupAgreement','Devi accettare i termini e condizioni');   {/if}
            {if in_array('captchaError',$formNotice)}      displayFormError('.g-recaptcha','Captcha non valido');    {/if}
        });
    </script>
</head>
<body>
    {include file='overlays.tpl'}
    <div id="container">
        {include file='header.tpl'}
        {include file='header2.tpl'}        
        
        <div class="container">
            <div class="row">
                {include file='subnav.tpl'}     
                <div class="col-md-9">
                
                    <div class="content">
                        <h1>{$lang.createAccount}</h1>
                        <hr>
                        {$lang.createAccountMessage}
                        
                        <form id="createAccountForm" class="cleanForm form-group" action="create.account.php" method="post"> 
                            <input type="hidden" name="showMemberships" value="{$showMemberships}">
                            <input type="hidden" name="msID" value="{$msID}">                    
                            
                            <h2 class="infoHeader">{$lang.generalInfo}</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> {$lang.firstName}:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="text" id="f_name" name="f_name" value="{$form.f_name}" 
                                               require="require" errorMessage="Il nome è obbligatorio" 
                                               style="width: 306px;" class="form-control" maxlength="50">
                                    </div>
                                </div>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> {$lang.lastName}:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="text" id="l_name" name="l_name" value="{$form.l_name}" 
                                               require="require" errorMessage="Il cognome è obbligatorio" class="form-control" maxlength="50">
                                    </div>
                                </div>
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> {$lang.email}:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="email" id="email" name="email" value="{$form.email}" 
                                               require="require" errorMessage="L'email è obbligatoria" 
                                               errorMessage2="Questa email è già registrata" 
                                               errorMessage3="Email bloccata dal sistema" 
                                               class="form-control" maxlength="100">
                                    </div>
                                </div>
                                {if $regForm.formPhone.status}
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        {if $regForm.formPhone.status == 2}<span class="requiredMark">*</span> {/if}{$lang.phone}:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="tel" id="phone" name="phone" value="{$form.phone}" 
                                               {if $regForm.formPhone.status == 2}require="require"{/if} 
                                               errorMessage="Il telefono è obbligatorio" 
                                               class="form-control" maxlength="20">
                                    </div>
                                </div>
                                {/if}
                                {if $regForm.formCompanyName.status}
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formCompanyName.status == 2}<span class="requiredMark">*</span> {/if}{$lang.companyName}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="comp_name" name="comp_name" value="{$form.comp_name}" 
                                                   {if $regForm.formCompanyName.status == 2}require="require"{/if} 
                                                   errorMessage="Il nome azienda è obbligatorio" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                {/if}
                                {if $regForm.formWebsite.status}
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formWebsite.status == 2}<span class="requiredMark">*</span> {/if}{$lang.website}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="url" id="website" name="website" value="{$form.website}" 
                                                   {if $regForm.formWebsite.status == 2}require="require"{/if} 
                                                   errorMessage="Il sito web è obbligatorio" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                {/if}
                            </div>
                            
                            {if $regForm.formAddress.status}
                                <br><h2 class="infoHeader">{$lang.address}</h2>
                                <div class="divTable">
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.country}:
                                        </div>
                                        <div class="divTableCell">
                                            <select id="country" name="country" style="width: 306px;" class="form-control" 
                                                    {if $regForm.formAddress.status == 2}require="require"{/if} 
                                                    errorMessage="Seleziona un paese">
                                                <option value="">Seleziona paese</option>
                                                {html_options options=$countries selected=$form.country}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.address}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="address" name="address" 
                                                   {if $regForm.formAddress.status == 2}require="require"{/if} 
                                                   value="{$form.address}" 
                                                   errorMessage="L'indirizzo è obbligatorio" 
                                                   class="form-control" maxlength="200">
                                            <input type="text" name="address_2" id="address_2" value="{$form.address_2}" 
                                                   placeholder="Seconda riga indirizzo (opzionale)"
                                                   style="margin-top: 6px;" class="form-control" maxlength="200">
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.city}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="city" name="city" value="{$form.city}" 
                                                   {if $regForm.formAddress.status == 2}require="require"{/if} 
                                                   errorMessage="La città è obbligatoria" 
                                                   class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell formFieldLabel">
                                            {if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.state}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="state" name="state" value="{$form.state}" 
                                                   {if $regForm.formAddress.status == 2}require="require"{/if} 
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
                                            {if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.zip}:
                                        </div>
                                        <div class="divTableCell">
                                            <input type="text" id="postal_code" name="postal_code" value="{$form.postal_code}" 
                                                   {if $regForm.formAddress.status == 2}require="require"{/if} 
                                                   errorMessage="Il CAP è obbligatorio" 
                                                   class="form-control" maxlength="10">
                                        </div>
                                    </div>                              
                                </div>
                            {/if}
                            
                            <br><h2 class="infoHeader">{$lang.password}</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel">
                                        <span class="requiredMark">*</span> {$lang.password}:
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
                                        <span class="requiredMark">*</span> {$lang.verifyPass}:
                                    </div>
                                    <div class="divTableCell">
                                        <input type="password" id="vpassword" name="vpassword" 
                                               require="require" errorMessage="Conferma la password" 
                                               class="form-control" minlength="6" maxlength="50">
                                    </div>
                                </div>
                            </div>
                            
                            {if $config.settings.captcha}
                            <br><h2 class="infoHeader">Verifica Sicurezza</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;">
                                        <span class="requiredMark">*</span> {$lang.captcha}:
                                    </div>
                                    <div class="divTableCell captcha">{include file='captcha.tpl'}</div>
                                </div>
                            </div>
                            {/if}
                            
                            {if $regForm.formSignupAgreement.status}
                            <br><h2 class="infoHeader">Termini e Condizioni</h2>
                            <div class="divTable">
                                <div class="divTableRow">
                                    <div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;">
                                        {if $regForm.formSignupAgreement.status == 2}<span class="requiredMark">*</span> {/if}{$lang.agreements}:
                                    </div>
                                    <div class="divTableCell" style="padding-top: 14px;">
                                        <input type="checkbox" name="signupAgreement" id="signupAgreement" value="1" 
                                               {if $regForm.formSignupAgreement.status == 2}require="require"{/if} 
                                               errorMessage="Devi accettare i termini e condizioni"> 
                                        <label for="signupAgreement">
                                            {$lang.readAgree} 
                                            <a href="{linkto page="content.php?id=11"}" class="colorLink" target="_blank">
                                                {content id='signupAgreement' titleOnly=1}
                                            </a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            {/if}
                            
                            {if $showMemberships}
                                <br><h2 class="infoHeader">{$lang.membership}</h2>
                                <ul class="membershipList" style="list-style: none; padding: 0;">
                                    {foreach $memberships as $key => $membership}
                                        <li style="border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 10px; background-color: #fafafa;">
                                            <input type="radio" name="membership" id="membership_{$membership.ms_id}" 
                                                   value="{$membership.ums_id}" 
                                                   {if $membership.ums_id == $selectedMembership}checked="checked"{/if}>
                                            <label for="membership_{$membership.ms_id}" style="font-weight: bold; cursor: pointer;">
                                                {$membership.name}
                                            </label> 
                                            <a href="membership.php?id={$membership.ums_id}" class="colorLink membershipWorkbox" 
                                               style="float: right;" target="_blank">[{$lang.details|upper}]</a>
                                            
                                            {if $membership.description}
                                            <p class="membershipDetails" style="margin: 8px 0; color: #666; font-size: 14px;">
                                                {$membership.description|truncate:300}
                                            </p>
                                            {/if}
                                            
                                            <p class="membershipPriceDetails" style="margin: 8px 0 0 0; font-size: 14px;">
                                                {if $membership.mstype == 'free'}
                                                    {$lang.mediaLabelPrice}: <span class="price" style="color: #2e7d32; font-weight: bold;">{$lang.free}</span>
                                                {/if}           
                                                {if $membership.trail_status}
                                                    {$lang.freeTrial}: <span class="price" style="color: #2e7d32; font-weight: bold;">{$membership.trial_length_num} {$lang.{$membership.trial_length_period}}</span><br>
                                                {/if}                                               
                                                {if $membership.setupfee}
                                                    {$lang.setupFee}: <span class="price" style="color: #2e7d32; font-weight: bold;">{$membership.setupfee.display}</span><br>
                                                {/if}                                               
                                                {if $membership.mstype == 'recurring'}
                                                    {$lang.mediaLabelPrice}: <span class="price" style="color: #2e7d32; font-weight: bold;">{$membership.price.display}</span> {$lang.{$membership.period}}
                                                {/if}
                                            </p>
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                            
                            <p><span class="requiredMark">* {$lang.required}</span></p>  
                            
                            <div style="text-align: right; margin-top: 20px;">
                                <input type="submit" value="{$lang.submit}" class="btn btn-primary" 
                                       style="padding: 10px 30px; font-size: 16px;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {include file='footer.tpl'}
    </div>
</body>
</html>