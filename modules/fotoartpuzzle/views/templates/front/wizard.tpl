{extends file='page.tpl'}

{block name='page_content'}
<div class="container fap-wizard-page">
  <div class="row">
    <div class="col-12 col-lg-4 mb-4">
      <div class="card fap-wizard-summary">
        <div class="card-body">
          <h1 class="h4 card-title">{l s='Create your custom puzzle' mod='fotoartpuzzle'}</h1>
          <p class="card-text">{l s='Upload your image, choose the puzzle format, and personalize the box before adding the product to your cart.' mod='fotoartpuzzle'}</p>
          <p class="card-text">
            <a class="btn btn-link p-0" href="{$product_link|escape:'html':'UTF-8'}">
              &laquo; {l s='Back to product page' mod='fotoartpuzzle'}
            </a>
          </p>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-8">
      <div id="fap-wizard"
           class="fap-wizard-container"
           data-config="{$wizard.config_json|escape:'html':'UTF-8'}"
           data-upload-url="{$wizard.upload_url|escape:'html':'UTF-8'}"
           data-preview-url="{$wizard.preview_url|escape:'html':'UTF-8'}"
           data-summary-url="{$wizard.summary_url|escape:'html':'UTF-8'}"
           data-ajax-url="{$wizard.ajax_url|escape:'html':'UTF-8'}"
           data-id-product="{$product->id|intval}"
           data-token-upload="{$wizard.token_upload|escape:'html':'UTF-8'}"
           data-token-preview="{$wizard.token_preview|escape:'html':'UTF-8'}"
           data-token-summary="{$wizard.token_summary|escape:'html':'UTF-8'}"
           data-token-ajax="{$wizard.token_ajax|escape:'html':'UTF-8'}">
        <button type="button" class="btn btn-primary fap-launch" aria-haspopup="dialog">
          <i class="material-icons">&#xE3F4;</i>
          <span>{l s='Launch customization wizard' mod='fotoartpuzzle'}</span>
        </button>
        <p class="fap-wizard-description mt-3">
          {l s='The wizard will open in a modal window where you can complete your customization.' mod='fotoartpuzzle'}
        </p>
      </div>
    </div>
  </div>
</div>
{/block}
