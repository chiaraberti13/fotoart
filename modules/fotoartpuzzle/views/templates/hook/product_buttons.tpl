{assign var=formats value=$front_config.formats}
{assign var=colors value=$front_config.box.colors}
{assign var=fonts value=$front_config.box.fonts}
<div id="fap-wizard" class="fap-root"
    data-config="{$config|escape:'htmlall':'UTF-8'}"
    data-upload-url="{$upload_url|escape:'htmlall':'UTF-8'}"
    data-preview-url="{$preview_url|escape:'htmlall':'UTF-8'}"
    data-summary-url="{$summary_url|escape:'htmlall':'UTF-8'}"
    data-token-upload="{$module->getFrontToken('upload')|escape:'htmlall':'UTF-8'}"
    data-token-preview="{$module->getFrontToken('preview')|escape:'htmlall':'UTF-8'}"
    data-token-summary="{$module->getFrontToken('summary')|escape:'htmlall':'UTF-8'}"
    data-id-product="{$id_product|intval}"
    data-msg-upload-success="{l s='Image uploaded successfully.' mod='fotoartpuzzle'}"
    data-msg-preview-missing="{l s='Upload an image before requesting a preview.' mod='fotoartpuzzle'}"
    data-msg-generic-error="{l s='An unexpected error occurred. Please try again.' mod='fotoartpuzzle'}"
    data-msg-summary-success="{l s='Puzzle customization added to cart.' mod='fotoartpuzzle'}">
    <button type="button" class="btn btn-primary fap-launch">
        {l s='Create your custom puzzle' mod='fotoartpuzzle'}
    </button>
    <div class="fap-modal" aria-hidden="true">
        <div class="fap-modal__overlay" role="presentation"></div>
        <div class="fap-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="fap-modal-title">
            <button type="button" class="fap-modal__close" aria-label="{l s='Close wizard' mod='fotoartpuzzle'}">&times;</button>
            <h3 id="fap-modal-title" class="fap-modal__title">{l s='Personalize your puzzle' mod='fotoartpuzzle'}</h3>
            <div class="fap-feedback" role="alert" hidden></div>
            <form class="fap-form">
                <fieldset class="fap-fieldset">
                    <legend>{l s='Upload your picture' mod='fotoartpuzzle'}</legend>
                    <input type="file" name="fap_file" accept="{$front_config.extensions|@implode:','|escape:'htmlall':'UTF-8'}" required class="fap-input">
                    <p class="fap-help">
                        {l s='Maximum size: %s MB. Minimum dimensions: %s x %s px.' sprintf=[$front_config.maxUploadMb, $front_config.minWidth, $front_config.minHeight] mod='fotoartpuzzle'}
                    </p>
                </fieldset>
                <fieldset class="fap-fieldset">
                    <legend>{l s='Choose format and box style' mod='fotoartpuzzle'}</legend>
                    {if $formats}
                        <label class="fap-label" for="fap-format">{l s='Format' mod='fotoartpuzzle'}</label>
                        <select id="fap-format" name="format" class="fap-input">
                            {foreach from=$formats item=format}
                                <option value="{$format.name|escape:'htmlall':'UTF-8'}">
                                    {$format.name|escape:'htmlall':'UTF-8'}
                                    {if isset($format.pieces)} - {$format.pieces|intval} {l s='pieces' mod='fotoartpuzzle'}{/if}
                                </option>
                            {/foreach}
                        </select>
                    {/if}
                    <label class="fap-label" for="fap-text">{l s='Text on the box' mod='fotoartpuzzle'}</label>
                    <textarea id="fap-text" name="box_text" maxlength="{$front_config.box.maxChars|intval}" class="fap-input fap-textarea"></textarea>
                    {if $colors}
                        <label class="fap-label" for="fap-color">{l s='Color' mod='fotoartpuzzle'}</label>
                        <select id="fap-color" name="box_color" class="fap-input">
                            {foreach from=$colors item=color}
                                <option value="{$color|escape:'htmlall':'UTF-8'}">{$color|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/if}
                    {if $fonts}
                        <label class="fap-label" for="fap-font">{l s='Font' mod='fotoartpuzzle'}</label>
                        <select id="fap-font" name="box_font" class="fap-input">
                            {foreach from=$fonts item=font}
                                <option value="{$font|escape:'htmlall':'UTF-8'}">{$font|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    {/if}
                    <button type="button" class="btn btn-secondary fap-preview-btn">{l s='Update preview' mod='fotoartpuzzle'}</button>
                </fieldset>
                <section class="fap-preview" aria-live="polite">
                    <figure class="fap-preview__figure" hidden>
                        <img src="" alt="{l s='Puzzle preview' mod='fotoartpuzzle'}" class="fap-preview__image" />
                        <figcaption class="fap-preview__caption">{l s='Preview updated after upload or text change.' mod='fotoartpuzzle'}</figcaption>
                    </figure>
                </section>
                <div class="fap-actions">
                    <button type="submit" class="btn btn-primary fap-submit">{l s='Add to cart' mod='fotoartpuzzle'}</button>
                </div>
            </form>
        </div>
    </div>
</div>
