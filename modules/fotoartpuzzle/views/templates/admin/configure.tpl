{extends file="helpers/form/form.tpl"}
{block name="form"}
<form id="fap-config-form" class="defaultForm form-horizontal" method="post" action="{$form_action|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
    <input type="hidden" name="configure" value="{$module_name|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="tab_module" value="{$tab_module|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="module_name" value="{$module_name|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="submit_fap_config" value="1">
    <input type="hidden" name="{$config_keys.box_color_combinations|escape:'htmlall':'UTF-8'}" id="fap-box-color-combinations" value="{$config.color_combinations_json|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="{$config_keys.custom_fonts|escape:'htmlall':'UTF-8'}" id="fap-custom-fonts" value="{$config.custom_fonts_json|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="{$config_keys.puzzle_products|escape:'htmlall':'UTF-8'}" id="fap-puzzle-products" value="{$config.puzzle_products_raw|escape:'htmlall':'UTF-8'}">

    <div class="fap-admin-config">
        <div class="panel">
            <div class="panel-heading">{$translations.products_heading}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3">{$translations.product_id_label}</label>
                    <div class="col-lg-9 fap-products-add">
                        <input type="text" class="form-control" id="fap-product-id" placeholder="123">
                        <button type="button" class="btn btn-default" id="fap-add-product">{$translations.add_product}</button>
                    </div>
                </div>
                <div class="fap-product-list" id="fap-product-list">
                    {foreach from=$puzzle_products item=product}
                        <div class="fap-product-item" data-product-id="{$product.id_product}">
                            <span class="fap-product-id">#{$product.id_product}</span>
                            <span class="fap-product-name">{$product.name|escape:'htmlall':'UTF-8'}</span>
                            <button type="button" class="btn btn-link btn-sm fap-remove-product">{$translations.remove}</button>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">{$translations.configuration_upload}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-max-upload">{$translations.max_upload}</label>
                    <div class="col-lg-9">
                        <input type="number" min="1" name="{$config_keys.max_upload_size|escape:'htmlall':'UTF-8'}" id="fap-max-upload" class="form-control" value="{$config.max_upload_size}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-min-width">{$translations.min_width}</label>
                    <div class="col-lg-9">
                        <input type="number" min="1" name="{$config_keys.min_width|escape:'htmlall':'UTF-8'}" id="fap-min-width" class="form-control" value="{$config.min_width}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-min-height">{$translations.min_height}</label>
                    <div class="col-lg-9">
                        <input type="number" min="1" name="{$config_keys.min_height|escape:'htmlall':'UTF-8'}" id="fap-min-height" class="form-control" value="{$config.min_height}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-allowed-extensions">{$translations.allowed_extensions}</label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.allowed_extensions|escape:'htmlall':'UTF-8'}" id="fap-allowed-extensions" class="form-control" value="{$config.allowed_extensions|escape:'htmlall':'UTF-8'}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-upload-folder">{$translations.upload_folder}</label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.upload_folder|escape:'htmlall':'UTF-8'}" id="fap-upload-folder" class="form-control" value="{$config.upload_folder|escape:'htmlall':'UTF-8'}" readonly />
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">{$translations.fonts_heading}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-font-upload">{$translations.upload_font}</label>
                    <div class="col-lg-9 fap-font-upload">
                        <input type="file" id="fap-font-upload" accept=".ttf" />
                        <button type="button" class="btn btn-default" id="fap-add-font">{$translations.add_font}</button>
                    </div>
                </div>
                <div class="fap-font-list" id="fap-font-list">
                    {foreach from=$fonts item=font}
                        <div class="fap-font-item" data-font-name="{$font|escape:'htmlall':'UTF-8'}">
                            <span class="fap-font-name">{$font|escape:'htmlall':'UTF-8'}</span>
                            <button type="button" class="btn btn-link btn-sm fap-remove-font">{$translations.remove}</button>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">{$translations.functionality_heading}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-enable-orientation">{$translations.enable_orientation}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.enable_orientation|escape:'htmlall':'UTF-8'}" id="fap-enable-orientation-on" value="1" {if $config.enable_orientation}checked="checked"{/if}>
                            <label for="fap-enable-orientation-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.enable_orientation|escape:'htmlall':'UTF-8'}" id="fap-enable-orientation-off" value="0" {if !$config.enable_orientation}checked="checked"{/if}>
                            <label for="fap-enable-orientation-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-enable-crop">{$translations.enable_crop}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.enable_interactive_crop|escape:'htmlall':'UTF-8'}" id="fap-enable-crop-on" value="1" {if $config.enable_interactive_crop}checked="checked"{/if}>
                            <label for="fap-enable-crop-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.enable_interactive_crop|escape:'htmlall':'UTF-8'}" id="fap-enable-crop-off" value="0" {if !$config.enable_interactive_crop}checked="checked"{/if}>
                            <label for="fap-enable-crop-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">{$translations.box_heading}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-box-default-text">{$translations.box_default_text}</label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.box_default_text|escape:'htmlall':'UTF-8'}" id="fap-box-default-text" class="form-control" value="{$config.box_default_text|escape:'htmlall':'UTF-8'}" placeholder="Il mio puzzle">
                        <p class="help-block">{$translations.box_default_text_desc}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-box-max-chars">{$translations.box_max_chars}</label>
                    <div class="col-lg-9">
                        <input type="number" min="1" name="{$config_keys.box_max_chars|escape:'htmlall':'UTF-8'}" id="fap-box-max-chars" class="form-control" value="{$config.box_max_chars}" />
                        <p class="help-block">{$translations.box_max_chars_desc}</p>
                    </div>
                </div>
                <div class="row fap-color-pickers">
                    <div class="col-lg-6">
                        <label for="fap-box-color">{$translations.box_color}</label>
                        <div class="fap-color-picker">
                            <input type="color" id="fap-box-color" value="{$config.box_color|escape:'htmlall':'UTF-8'}">
                            <input type="text" name="{$config_keys.box_color|escape:'htmlall':'UTF-8'}" class="form-control" id="fap-box-color-hex" value="{$config.box_color|escape:'htmlall':'UTF-8'}">
                            <span class="fap-color-preview" id="fap-box-color-preview" style="background-color: {$config.box_color|escape:'htmlall':'UTF-8'}"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="fap-box-text-color">{$translations.box_text_color}</label>
                        <div class="fap-color-picker">
                            <input type="color" id="fap-box-text-color" value="{$config.box_text_color|escape:'htmlall':'UTF-8'}">
                            <input type="text" name="{$config_keys.box_text_color|escape:'htmlall':'UTF-8'}" class="form-control" id="fap-box-text-color-hex" value="{$config.box_text_color|escape:'htmlall':'UTF-8'}">
                            <span class="fap-color-preview" id="fap-box-text-color-preview" style="background-color: {$config.box_text_color|escape:'htmlall':'UTF-8'}"></span>
                        </div>
                    </div>
                </div>
                <div class="fap-color-actions">
                    <button type="button" class="btn btn-info" id="fap-add-color-combination">{$translations.add_combination}</button>
                </div>
                <div class="fap-color-combinations" id="fap-color-combinations">
                    {foreach from=$color_combinations item=combo name=colorLoop}
                        <div class="fap-color-combination" data-index="{$smarty.foreach.colorLoop.index}">
                            <div class="fap-color-chip" style="background-color: {$combo.box|escape:'htmlall':'UTF-8'}"></div>
                            <span class="fap-color-label">Scatola: {$combo.box|escape:'htmlall':'UTF-8'}</span>
                            <div class="fap-color-chip" style="background-color: {$combo.text|escape:'htmlall':'UTF-8'}"></div>
                            <span class="fap-color-label">Testo: {$combo.text|escape:'htmlall':'UTF-8'}</span>
                            <button type="button" class="btn btn-link btn-sm fap-remove-combination">{$translations.remove}</button>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">{$translations.email_heading}</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-email-user">{$translations.email_user}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.email_preview_user|escape:'htmlall':'UTF-8'}" id="fap-email-user-on" value="1" {if $config.email_preview_user}checked="checked"{/if}>
                            <label for="fap-email-user-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.email_preview_user|escape:'htmlall':'UTF-8'}" id="fap-email-user-off" value="0" {if !$config.email_preview_user}checked="checked"{/if}>
                            <label for="fap-email-user-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-email-admin">{$translations.email_admin}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.email_preview_admin|escape:'htmlall':'UTF-8'}" id="fap-email-admin-on" value="1" {if $config.email_preview_admin}checked="checked"{/if}>
                            <label for="fap-email-admin-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.email_preview_admin|escape:'htmlall':'UTF-8'}" id="fap-email-admin-off" value="0" {if !$config.email_preview_admin}checked="checked"{/if}>
                            <label for="fap-email-admin-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-email-admin-recipients">{$translations.email_admin_recipients}</label>
                    <div class="col-lg-9">
                        <textarea name="{$config_keys.email_admin_recipients|escape:'htmlall':'UTF-8'}" id="fap-email-admin-recipients" class="form-control" rows="2">{$config.email_admin_recipients|escape:'htmlall':'UTF-8'}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-enable-pdf-user">{$translations.enable_pdf_user}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.enable_pdf_user|escape:'htmlall':'UTF-8'}" id="fap-enable-pdf-user-on" value="1" {if $config.enable_pdf_user}checked="checked"{/if}>
                            <label for="fap-enable-pdf-user-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.enable_pdf_user|escape:'htmlall':'UTF-8'}" id="fap-enable-pdf-user-off" value="0" {if !$config.enable_pdf_user}checked="checked"{/if}>
                            <label for="fap-enable-pdf-user-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-enable-pdf-admin">{$translations.enable_pdf_admin}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="{$config_keys.enable_pdf_admin|escape:'htmlall':'UTF-8'}" id="fap-enable-pdf-admin-on" value="1" {if $config.enable_pdf_admin}checked="checked"{/if}>
                            <label for="fap-enable-pdf-admin-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                            <input type="radio" name="{$config_keys.enable_pdf_admin|escape:'htmlall':'UTF-8'}" id="fap-enable-pdf-admin-off" value="0" {if !$config.enable_pdf_admin}checked="checked"{/if}>
                            <label for="fap-enable-pdf-admin-off">{l s='No' mod='fotoartpuzzle'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-primary pull-right">{$translations.save}</button>
            </div>
        </div>
    </div>
</form>

{literal}
<script>
    window.fapAdminConfig = {
        ajaxUrl: '{/literal}{$ajax_url|escape:'htmlall':'UTF-8'}{literal}',
        products: {/literal}{$puzzle_products|json_encode}{literal},
        combinations: {/literal}{$color_combinations|json_encode}{literal},
        fonts: {/literal}{$fonts|json_encode}{literal},
        translations: {
            remove: '{/literal}{$translations.remove|escape:'javascript'}{literal}',
            error: '{/literal}{l s='Si è verificato un errore.' mod='fotoartpuzzle'}{literal}',
            success: '{/literal}{l s='Operazione completata.' mod='fotoartpuzzle'}{literal}'
        }
    };
</script>
{/literal}
{/block}
