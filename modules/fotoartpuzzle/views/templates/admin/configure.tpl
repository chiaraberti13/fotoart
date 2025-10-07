<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Configurazione FotoArt Puzzle' mod='fotoartpuzzle'}
    </div>
</div>

<form id="fap-config-form" class="defaultForm form-horizontal" method="post" action="{$form_action|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
    <input type="hidden" name="configure" value="{$module_name|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="tab_module" value="{$tab_module|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="module_name" value="{$module_name|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="submit_fap_config" value="1">
    <input type="hidden" name="{$config_keys.box_color_combinations|escape:'htmlall':'UTF-8'}" id="fap-box-color-combinations" value="{$config.color_combinations_json|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="{$config_keys.custom_fonts|escape:'htmlall':'UTF-8'}" id="fap-custom-fonts" value="{$config.custom_fonts_json|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="{$config_keys.puzzle_products|escape:'htmlall':'UTF-8'}" id="fap-puzzle-products" value="{$config.puzzle_products_raw|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="{$config_keys.legacy_map|escape:'htmlall':'UTF-8'}" id="fap-legacy-map" value="{$config.legacy_map_json|escape:'htmlall':'UTF-8'}">

    <div class="fap-admin-config">
        <!-- SEZIONE PRODOTTI PUZZLE -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-puzzle-piece"></i> {$translations.products_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3">{$translations.product_id_label|escape:'htmlall':'UTF-8'}</label>
                    <div class="col-lg-9 fap-products-add">
                        <input type="text" class="form-control" id="fap-product-id" placeholder="123" style="max-width: 150px;">
                        <button type="button" class="btn btn-default" id="fap-add-product">
                            <i class="icon-plus"></i> {$translations.add_product|escape:'htmlall':'UTF-8'}
                        </button>
                    </div>
                </div>
                <div class="fap-product-list" id="fap-product-list">
                    {if isset($puzzle_products) && $puzzle_products}
                        {foreach from=$puzzle_products item=product}
                            <div class="fap-product-item" data-product-id="{$product.id_product|intval}">
                                <span class="fap-product-id">#{$product.id_product|intval}</span>
                                <span class="fap-product-name">{$product.name|escape:'htmlall':'UTF-8'}</span>
                                <button type="button" class="btn btn-link btn-sm fap-remove-product">
                                    <i class="icon-trash"></i> {$translations.remove|escape:'htmlall':'UTF-8'}
                                </button>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>

        <!-- SEZIONE CONFIGURAZIONE UPLOAD -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-upload"></i> {$translations.configuration_upload|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-max-upload">
                        {$translations.max_upload|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input type="number" min="1" max="100" name="{$config_keys.max_upload_size|escape:'htmlall':'UTF-8'}" id="fap-max-upload" class="form-control" value="{$config.max_upload_size|intval}" style="max-width: 150px;">
                            <span class="input-group-addon">MB</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-min-width">
                        {$translations.min_width|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input type="number" min="100" name="{$config_keys.min_width|escape:'htmlall':'UTF-8'}" id="fap-min-width" class="form-control" value="{$config.min_width|intval}" style="max-width: 150px;">
                            <span class="input-group-addon">px</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-min-height">
                        {$translations.min_height|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input type="number" min="100" name="{$config_keys.min_height|escape:'htmlall':'UTF-8'}" id="fap-min-height" class="form-control" value="{$config.min_height|intval}" style="max-width: 150px;">
                            <span class="input-group-addon">px</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-allowed-extensions">
                        {$translations.allowed_extensions|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.allowed_extensions|escape:'htmlall':'UTF-8'}" id="fap-allowed-extensions" class="form-control" value="{$config.allowed_extensions|escape:'htmlall':'UTF-8'}" placeholder="jpg,jpeg,png">
                        <p class="help-block">{l s='Formati separati da virgola (es: jpg,jpeg,png)' mod='fotoartpuzzle'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-upload-folder">
                        {$translations.upload_folder|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.upload_folder|escape:'htmlall':'UTF-8'}" id="fap-upload-folder" class="form-control" value="{$config.upload_folder|escape:'htmlall':'UTF-8'}" readonly>
                        <p class="help-block">{l s='Percorso automatico gestito dal modulo' mod='fotoartpuzzle'}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEZIONE FONT PERSONALIZZATI -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-font"></i> {$translations.fonts_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-font-upload">
                        {$translations.upload_font|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9 fap-font-upload">
                        <input type="file" id="fap-font-upload" accept=".ttf,.otf" class="form-control" style="max-width: 300px; display: inline-block;">
                        <button type="button" class="btn btn-default" id="fap-add-font">
                            <i class="icon-plus"></i> {$translations.add_font|escape:'htmlall':'UTF-8'}
                        </button>
                        <p class="help-block">{l s='Sono supportati file TTF e OTF. I file vengono copiati automaticamente nella cartella del modulo.' mod='fotoartpuzzle'}</p>
                    </div>
                </div>
                <div class="fap-font-list" id="fap-font-list">
                    {if isset($fonts) && $fonts}
                        {foreach from=$fonts item=font}
                            <div class="fap-font-item" data-font-name="{$font.filename|escape:'htmlall':'UTF-8'}">
                                <span class="fap-font-name">{$font.name|escape:'htmlall':'UTF-8'}</span>
                                {if isset($font.filename)}<span class="fap-font-file"> ({$font.filename|escape:'htmlall':'UTF-8'})</span>{/if}
                                <button type="button" class="btn btn-link btn-sm fap-remove-font">
                                    <i class="icon-trash"></i> {$translations.remove|escape:'htmlall':'UTF-8'}
                                </button>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>

        <!-- SEZIONE PERSONALIZZAZIONE SCATOLA -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-gift"></i> {$translations.box_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-box-default-text">
                        {$translations.box_default_text|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <input type="text" name="{$config_keys.box_default_text|escape:'htmlall':'UTF-8'}" id="fap-box-default-text" class="form-control" value="{$config.box_default_text|escape:'htmlall':'UTF-8'}" placeholder="Il mio puzzle">
                        <p class="help-block">{$translations.box_default_text_desc|escape:'htmlall':'UTF-8'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="fap-box-max-chars">
                        {$translations.box_max_chars|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <input type="number" min="1" max="100" name="{$config_keys.box_max_chars|escape:'htmlall':'UTF-8'}" id="fap-box-max-chars" class="form-control" value="{$config.box_max_chars|intval}" style="max-width: 150px;">
                        <p class="help-block">{$translations.box_max_chars_desc|escape:'htmlall':'UTF-8'}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Colori scatola e testo' mod='fotoartpuzzle'}
                    </label>
                    <div class="col-lg-9">
                        <div class="row fap-color-pickers">
                            <div class="col-lg-6">
                                <label for="fap-box-color">{$translations.box_color|escape:'htmlall':'UTF-8'}</label>
                                <div class="fap-color-picker">
                                    <input type="color" id="fap-box-color" value="{$config.box_color|escape:'htmlall':'UTF-8'}">
                                    <input type="text" name="{$config_keys.box_color|escape:'htmlall':'UTF-8'}" class="form-control" id="fap-box-color-hex" value="{$config.box_color|escape:'htmlall':'UTF-8'}" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#FFFFFF">
                                    <span class="fap-color-preview" id="fap-box-color-preview" style="background-color: {$config.box_color|escape:'htmlall':'UTF-8'}"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="fap-box-text-color">{$translations.box_text_color|escape:'htmlall':'UTF-8'}</label>
                                <div class="fap-color-picker">
                                    <input type="color" id="fap-box-text-color" value="{$config.box_text_color|escape:'htmlall':'UTF-8'}">
                                    <input type="text" name="{$config_keys.box_text_color|escape:'htmlall':'UTF-8'}" class="form-control" id="fap-box-text-color-hex" value="{$config.box_text_color|escape:'htmlall':'UTF-8'}" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#000000">
                                    <span class="fap-color-preview" id="fap-box-text-color-preview" style="background-color: {$config.box_text_color|escape:'htmlall':'UTF-8'}"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Combinazioni predefinite' mod='fotoartpuzzle'}
                    </label>
                    <div class="col-lg-9">
                        <button type="button" class="btn btn-info" id="fap-add-color-combination">
                            <i class="icon-plus"></i> {$translations.add_combination|escape:'htmlall':'UTF-8'}
                        </button>
                        <p class="help-block">{l s='Aggiungi combinazioni di colori che gli utenti potranno scegliere' mod='fotoartpuzzle'}</p>
                    </div>
                </div>

                <div class="fap-color-combinations" id="fap-color-combinations">
                    {if isset($color_combinations) && $color_combinations}
                        {foreach from=$color_combinations item=combo name=colorLoop}
                            <div class="fap-color-combination" data-index="{$smarty.foreach.colorLoop.index}">
                                <div class="fap-color-chip" style="background-color: {$combo.box|escape:'htmlall':'UTF-8'}"></div>
                                <span class="fap-color-label">{l s='Scatola:' mod='fotoartpuzzle'} {$combo.box|escape:'htmlall':'UTF-8'}</span>
                                <div class="fap-color-chip" style="background-color: {$combo.text|escape:'htmlall':'UTF-8'}"></div>
                                <span class="fap-color-label">{l s='Testo:' mod='fotoartpuzzle'} {$combo.text|escape:'htmlall':'UTF-8'}</span>
                                <button type="button" class="btn btn-link btn-sm fap-remove-combination">
                                    <i class="icon-trash"></i> {$translations.remove|escape:'htmlall':'UTF-8'}
                                </button>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>

        <!-- SEZIONE MAPPATURA LEGACY -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-sitemap"></i> {$translations.legacy_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="fap-legacy-form">
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="control-label" for="fap-legacy-product">{$translations.legacy_product_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="number" id="fap-legacy-product" class="form-control" min="1" placeholder="101">
                        </div>
                        <div class="col-lg-2">
                            <label class="control-label" for="fap-legacy-attribute">{$translations.legacy_attribute_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="number" id="fap-legacy-attribute" class="form-control" min="0" placeholder="0">
                        </div>
                        <div class="col-lg-2">
                            <label class="control-label" for="fap-legacy-code">{$translations.legacy_code_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="text" id="fap-legacy-code" class="form-control" maxlength="128" placeholder="BOX-500">
                        </div>
                        <div class="col-lg-1">
                            <label class="control-label" for="fap-legacy-pieces">{$translations.legacy_pieces_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="number" id="fap-legacy-pieces" class="form-control" min="0" step="1" placeholder="500">
                        </div>
                        <div class="col-lg-1">
                            <label class="control-label" for="fap-legacy-width">{$translations.legacy_width_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="number" id="fap-legacy-width" class="form-control" min="0" step="1" placeholder="680">
                        </div>
                        <div class="col-lg-1">
                            <label class="control-label" for="fap-legacy-height">{$translations.legacy_height_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="number" id="fap-legacy-height" class="form-control" min="0" step="1" placeholder="480">
                        </div>
                        <div class="col-lg-1">
                            <label class="control-label" for="fap-legacy-price">{$translations.legacy_price_label|escape:'htmlall':'UTF-8'}</label>
                            <input type="text" id="fap-legacy-price" class="form-control" placeholder="49.90">
                        </div>
                        <div class="col-lg-1">
                            <label class="control-label" for="fap-legacy-available">{$translations.legacy_available_label|escape:'htmlall':'UTF-8'}</label>
                            <div>
                                <span class="switch prestashop-switch fixed-width-lg">
                                    <input type="radio" name="fap-legacy-available" id="fap-legacy-available-on" value="1" checked>
                                    <label for="fap-legacy-available-on">{l s='Sì' mod='fotoartpuzzle'}</label>
                                    <input type="radio" name="fap-legacy-available" id="fap-legacy-available-off" value="0">
                                    <label for="fap-legacy-available-off">{l s='No' mod='fotoartpuzzle'}</label>
                                    <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label class="control-label" for="fap-legacy-add">&nbsp;</label>
                            <button type="button" class="btn btn-default btn-block" id="fap-legacy-add">
                                <i class="icon-plus"></i> {$translations.legacy_add|escape:'htmlall':'UTF-8'}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="fap-legacy-table">
                        <thead>
                            <tr>
                                <th>{$translations.legacy_product_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_attribute_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_code_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_pieces_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_width_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_height_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_price_label|escape:'htmlall':'UTF-8'}</th>
                                <th>{$translations.legacy_available_label|escape:'htmlall':'UTF-8'}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {if isset($legacy_mappings) && $legacy_mappings}
                                {foreach from=$legacy_mappings item=mapping}
                                    <tr data-index="{$mapping@iteration-1}">
                                        <td>#{if isset($mapping.id_product)}{$mapping.id_product|intval}{/if}</td>
                                        <td>{if isset($mapping.id_product_attribute) && $mapping.id_product_attribute}{$mapping.id_product_attribute|intval}{else}-{/if}</td>
                                        <td>{$mapping.legacy_code|escape:'htmlall':'UTF-8'}</td>
                                        <td>{if isset($mapping.pieces) && $mapping.pieces}{$mapping.pieces|intval}{else}-{/if}</td>
                                        <td>{if isset($mapping.width_mm) && $mapping.width_mm}{$mapping.width_mm|intval}{else}-{/if}</td>
                                        <td>{if isset($mapping.height_mm) && $mapping.height_mm}{$mapping.height_mm|intval}{else}-{/if}</td>
                                        <td>{if isset($mapping.price) && $mapping.price !== null}{$mapping.price|escape:'htmlall':'UTF-8'}{else}-{/if}</td>
                                        <td>
                                            {if !empty($mapping.available)}
                                                <span class="label label-success">{l s='Sì' mod='fotoartpuzzle'}</span>
                                            {else}
                                                <span class="label label-danger">{l s='No' mod='fotoartpuzzle'}</span>
                                            {/if}
                                        </td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-link btn-sm fap-legacy-remove">
                                                <i class="icon-trash"></i> {$translations.remove|escape:'htmlall':'UTF-8'}
                                            </button>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr class="fap-legacy-empty">
                                    <td colspan="9" class="text-center text-muted">{$translations.legacy_empty|escape:'htmlall':'UTF-8'}</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEZIONE FUNZIONALITÀ -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-cog"></i> {$translations.functionality_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {$translations.enable_orientation|escape:'htmlall':'UTF-8'}
                    </label>
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
                    <label class="control-label col-lg-3">
                        {$translations.enable_crop|escape:'htmlall':'UTF-8'}
                    </label>
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

        <!-- SEZIONE EMAIL E PDF -->
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-envelope"></i> {$translations.email_heading|escape:'htmlall':'UTF-8'}
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {$translations.email_user|escape:'htmlall':'UTF-8'}
                    </label>
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
                    <label class="control-label col-lg-3">
                        {$translations.email_admin|escape:'htmlall':'UTF-8'}
                    </label>
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
                    <label class="control-label col-lg-3" for="fap-email-admin-recipients">
                        {$translations.email_admin_recipients|escape:'htmlall':'UTF-8'}
                    </label>
                    <div class="col-lg-9">
                        <textarea name="{$config_keys.email_admin_recipients|escape:'htmlall':'UTF-8'}" id="fap-email-admin-recipients" class="form-control" rows="2" placeholder="email@example.com">{$config.email_admin_recipients|escape:'htmlall':'UTF-8'}</textarea>
                        <p class="help-block">{l s='Separare più email con virgola o spazio' mod='fotoartpuzzle'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {$translations.enable_pdf_user|escape:'htmlall':'UTF-8'}
                    </label>
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
                    <label class="control-label col-lg-3">
                        {$translations.enable_pdf_admin|escape:'htmlall':'UTF-8'}
                    </label>
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
        </div>

        <!-- BOTTONE SALVA -->
        <div class="panel">
            <div class="panel-footer">
                <button type="submit" value="1" id="fap-config-submit" name="submit_fap_config" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {$translations.save|escape:'htmlall':'UTF-8'}
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    if (typeof window.fapAdminConfig === 'undefined') {
        window.fapAdminConfig = {
            ajaxUrl: '{$ajax_url|escape:'javascript':'UTF-8'}',
            products: {if isset($puzzle_products)}{$puzzle_products|@json_encode nofilter}{else}[]{/if},
            combinations: {if isset($color_combinations)}{$color_combinations|@json_encode nofilter}{else}[]{/if},
            fonts: {if isset($fonts)}{$fonts|@json_encode nofilter}{else}[]{/if},
            legacyMappings: {if isset($legacy_mappings)}{$legacy_mappings|@json_encode nofilter}{else}[]{/if},
            token: '{$token|escape:'javascript':'UTF-8'}',
            translations: {
                remove: '{$translations.remove|escape:'javascript':'UTF-8'}',
                error: '{l s='Si è verificato un errore.' mod='fotoartpuzzle' js=1}',
                success: '{l s='Operazione completata.' mod='fotoartpuzzle' js=1}',
                legacyValidation: '{$translations.legacy_validation_error|escape:'javascript':'UTF-8'}',
                legacyEmpty: '{$translations.legacy_empty|escape:'javascript':'UTF-8'}',
                yes: '{l s='Sì' mod='fotoartpuzzle' js=1}',
                no: '{l s='No' mod='fotoartpuzzle' js=1}'
            }
        };
    }
</script>