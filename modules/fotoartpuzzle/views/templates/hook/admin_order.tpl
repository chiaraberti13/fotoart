{if isset($customizations) && $customizations}
<div class="card">
    <h3 class="card-header">{l s='FotoArt Puzzle customization' mod='fotoartpuzzle'}</h3>
    <div class="card-body">
        {foreach from=$customizations item=custom}
            <div class="fap-cart-summary">
                <p><strong>{l s='Customization ID:' mod='fotoartpuzzle'}</strong> {$custom.id_customization|escape:'html':'UTF-8'}</p>
                {if $custom.file}
                    <p>
                        <a class="btn btn-default" href="{$module->getDownloadLink($custom.file)|escape:'html':'UTF-8'}">{l s='Download asset' mod='fotoartpuzzle'}</a>
                    </p>
                {/if}
                {if $custom.text}
                    <p><strong>{l s='Box text:' mod='fotoartpuzzle'}</strong> {$custom.text|escape:'html':'UTF-8'}</p>
                {/if}
                {if $custom.metadata}
                    <ul>
                        {foreach from=$custom.metadata key=metaKey item=metaValue}
                            <li><strong>{$metaKey|escape:'html':'UTF-8'}:</strong> {$metaValue|escape:'html':'UTF-8'}</li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        {/foreach}
    </div>
</div>
{/if}
