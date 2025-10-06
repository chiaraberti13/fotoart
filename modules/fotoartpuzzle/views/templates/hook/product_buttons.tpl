{*
* FotoArt Puzzle - Product Customization Button
* Displays the wizard launch button on product pages
*}

<div id="fap-wizard" 
     class="fap-wizard-container"
     data-config="{$config|escape:'html':'UTF-8'}" 
     data-upload-url="{$upload_url|escape:'html':'UTF-8'}"
     data-preview-url="{$preview_url|escape:'html':'UTF-8'}" 
     data-summary-url="{$summary_url|escape:'html':'UTF-8'}"
     data-ajax-url="{$ajax_url|escape:'html':'UTF-8'}"
     data-id-product="{$id_product|intval}"
     data-token-upload="{$token_upload|escape:'html':'UTF-8'}"
     data-token-preview="{$token_preview|escape:'html':'UTF-8'}"
     data-token-summary="{$token_summary|escape:'html':'UTF-8'}">
    
    <button type="button" class="btn btn-primary fap-launch" aria-haspopup="dialog">
        <i class="material-icons">&#xE3F4;</i>
        <span>{l s='Crea il tuo puzzle personalizzato' mod='fotoartpuzzle'}</span>
    </button>
    
    <p class="fap-wizard-description" style="margin-top: 0.5rem; font-size: 0.9em; color: #666;">
        {l s='Carica la tua immagine e personalizza la scatola del puzzle' mod='fotoartpuzzle'}
    </p>
</div>

{* Script inline per debug (rimuovere in produzione) *}
{if isset($smarty.get.debug) && $smarty.get.debug == 1}
<script type="text/javascript">
console.log('FotoArt Puzzle Debug Info:', {
    config: {$config|@json_encode nofilter},
    uploadUrl: '{$upload_url|escape:'javascript':'UTF-8'}',
    previewUrl: '{$preview_url|escape:'javascript':'UTF-8'}',
    summaryUrl: '{$summary_url|escape:'javascript':'UTF-8'}',
    ajaxUrl: '{$ajax_url|escape:'javascript':'UTF-8'}',
    idProduct: {$id_product|intval},
    tokensPresent: {
        upload: '{$token_upload|escape:'javascript':'UTF-8'}' !== '',
        preview: '{$token_preview|escape:'javascript':'UTF-8'}' !== '',
        summary: '{$token_summary|escape:'javascript':'UTF-8'}' !== ''
    }
});
</script>
{/if}