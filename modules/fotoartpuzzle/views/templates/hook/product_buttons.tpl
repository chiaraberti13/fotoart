<div id="fap-wizard" data-config="{$config|escape:'html':'UTF-8'}" data-upload-url="{$upload_url|escape:'html':'UTF-8'}"
    data-preview-url="{$preview_url|escape:'html':'UTF-8'}" data-summary-url="{$summary_url|escape:'html':'UTF-8'}"
    data-token-upload="{$module->getFrontToken('upload')|escape:'html':'UTF-8'}"
    data-token-preview="{$module->getFrontToken('preview')|escape:'html':'UTF-8'}"
    data-token-summary="{$module->getFrontToken('summary')|escape:'html':'UTF-8'}">
    <button type="button" class="btn btn-primary fap-launch">{l s='Create your custom puzzle' mod='fotoartpuzzle'}</button>
</div>
