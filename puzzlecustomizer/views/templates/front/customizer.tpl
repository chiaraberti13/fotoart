{extends file='page.tpl'}

{block name='page_content'}
<div class="puzzle-customizer" id="puzzle-customizer">
  <h1 class="puzzle-customizer__title">{l s='Personalizza il tuo puzzle' mod='puzzlecustomizer'}</h1>
  <div class="puzzle-customizer__steps">
    {include file='module:puzzlecustomizer/views/templates/front/upload.tpl'}
    {include file='module:puzzlecustomizer/views/templates/front/editor.tpl'}
    {include file='module:puzzlecustomizer/views/templates/front/options.tpl'}
  </div>
  <button class="btn btn-primary" id="puzzle-save">{l s='Salva configurazione' mod='puzzlecustomizer'}</button>
</div>
<script>
  window.puzzleCustomizer = {
    uploadUrl: '{$customizer_config.upload_url|escape:'javascript'}',
    saveUrl: '{$customizer_config.save_url|escape:'javascript'}',
    previewUrl: '{$customizer_config.preview_url|escape:'javascript'}'
  };
</script>
{/block}
