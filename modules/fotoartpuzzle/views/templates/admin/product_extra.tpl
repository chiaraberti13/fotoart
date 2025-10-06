{*
* FotoArt Puzzle - Admin Product Extra Section
* Displays module information in the admin product page
*}

<div class="panel fap-admin-product-panel">
    <div class="panel-heading">
        <i class="icon-puzzle-piece"></i> {$module_display_name|escape:'htmlall':'UTF-8'}
    </div>
    
    <div class="panel-body">
        <div class="alert alert-info">
            <p><strong>{l s='Custom Puzzle Enabled' mod='fotoartpuzzle'}</strong></p>
            <p>{l s='This product is configured to allow customers to create custom puzzles with their own images.' mod='fotoartpuzzle'}</p>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <h4>{l s='Available Formats' mod='fotoartpuzzle'}</h4>
                {if isset($formats) && $formats|@count > 0}
                    <ul class="list-unstyled fap-formats-list">
                        {foreach from=$formats item=format}
                            <li>
                                <i class="icon-puzzle-piece text-muted"></i>
                                <strong>{$format.name|escape:'htmlall':'UTF-8'}</strong>
                                <span class="text-muted">
                                    - {$format.pieces|intval} {l s='pieces' mod='fotoartpuzzle'}
                                    ({$format.width|intval}x{$format.height|intval} cm)
                                </span>
                            </li>
                        {/foreach}
                    </ul>
                {else}
                    <p class="text-muted">{l s='No formats configured' mod='fotoartpuzzle'}</p>
                {/if}
            </div>

            <div class="col-lg-6">
                <h4>{l s='Configuration' mod='fotoartpuzzle'}</h4>
                <dl>
                    <dt>{l s='Maximum upload:' mod='fotoartpuzzle'}</dt>
                    <dd>{$config.upload.maxSize|intval} MB</dd>

                    <dt>{l s='Minimum dimensions:' mod='fotoartpuzzle'}</dt>
                    <dd>{$config.upload.minWidth|intval}x{$config.upload.minHeight|intval} px</dd>

                    <dt>{l s='Accepted formats:' mod='fotoartpuzzle'}</dt>
                    <dd>{$config.upload.allowedExtensions|escape:'htmlall':'UTF-8'}</dd>
                </dl>
            </div>
        </div>

        <hr>

        <div class="fap-actions">
            <a href="{$configure_url|escape:'htmlall':'UTF-8'}" class="btn btn-default" target="_blank">
                <i class="icon-cogs"></i>
                {l s='Configure Module' mod='fotoartpuzzle'}
            </a>
            
            <a href="javascript:void(0)" class="btn btn-default" onclick="alert('Feature available in a future version')">
                <i class="icon-eye"></i>
                {l s='View Customizations' mod='fotoartpuzzle'}
            </a>
        </div>
    </div>
</div>

<style>
.fap-admin-product-panel .fap-formats-list {
    margin-top: 10px;
}

.fap-admin-product-panel .fap-formats-list li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.fap-admin-product-panel .fap-formats-list li:last-child {
    border-bottom: none;
}

.fap-admin-product-panel dl {
    margin-bottom: 0;
}

.fap-admin-product-panel dt {
    font-weight: 600;
    margin-top: 8px;
}

.fap-admin-product-panel dd {
    margin-left: 0;
    color: #666;
}

.fap-admin-product-panel .fap-actions {
    margin-top: 15px;
}

.fap-admin-product-panel .fap-actions .btn {
    margin-right: 10px;
}
</style>
