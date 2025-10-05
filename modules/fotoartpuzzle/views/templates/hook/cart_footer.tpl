{if isset($customizations) && $customizations}
<div class="fap-cart-summary">
    <h4>{l s='Puzzle customization' mod='fotoartpuzzle'}</h4>
    <ul>
        {foreach from=$customizations item=custom}
            {if $custom.file}
            <li><span>{l s='Uploaded image stored securely.' mod='fotoartpuzzle'}</span></li>
            {/if}
            {if $custom.text}
            <li><span>{$custom.text|escape:'html':'UTF-8'}</span></li>
            {/if}
            {if isset($custom.metadata.format)}
            <li><span>{l s='Format:' mod='fotoartpuzzle'} {$custom.metadata.format|escape:'html':'UTF-8'}</span></li>
            {/if}
        {/foreach}
    </ul>
</div>
{/if}
