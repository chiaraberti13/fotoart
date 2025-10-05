{if isset($customizations) && $customizations}
<div class="fap-cart-summary">
    <h4>{l s='Puzzle customization' mod='fotoartpuzzle'}</h4>
    <ul>
        {foreach from=$customizations item=custom}
            {assign var=formatName value=''}
            {if isset($custom.metadata.format_data.name) && $custom.metadata.format_data.name}
                {assign var=formatName value=$custom.metadata.format_data.name}
            {elseif isset($custom.metadata.format) && $custom.metadata.format}
                {assign var=formatName value=$custom.metadata.format}
            {/if}

            {if isset($custom.metadata.filename) && $custom.metadata.filename}
                <li><span>{l s='Image file:' mod='fotoartpuzzle'}</span> {$custom.metadata.filename|escape:'html':'UTF-8'}</li>
            {elseif $custom.file}
                <li><span>{l s='Uploaded image stored securely.' mod='fotoartpuzzle'}</span></li>
            {/if}

            {if $custom.text}
                <li><span>{l s='Box text:' mod='fotoartpuzzle'}</span> {$custom.text|escape:'html':'UTF-8'}</li>
            {/if}

            {if $formatName}
                <li><span>{l s='Format:' mod='fotoartpuzzle'}</span> {$formatName|escape:'html':'UTF-8'}</li>
            {/if}

            {if isset($custom.metadata.format_data.pieces) && $custom.metadata.format_data.pieces}
                <li><span>{l s='Pieces:' mod='fotoartpuzzle'}</span> {$custom.metadata.format_data.pieces|intval}</li>
            {/if}

            {if isset($custom.metadata.format_data.width) && isset($custom.metadata.format_data.height)}
                <li><span>{l s='Dimensions:' mod='fotoartpuzzle'}</span> {$custom.metadata.format_data.width|intval} x {$custom.metadata.format_data.height|intval} px</li>
            {/if}

            {if isset($custom.metadata.color) && $custom.metadata.color}
                <li><span>{l s='Text color:' mod='fotoartpuzzle'}</span> {$custom.metadata.color|escape:'html':'UTF-8'}</li>
            {/if}

            {if isset($custom.metadata.font) && $custom.metadata.font}
                <li><span>{l s='Font:' mod='fotoartpuzzle'}</span> {$custom.metadata.font|escape:'html':'UTF-8'}</li>
            {/if}
        {/foreach}
    </ul>
</div>
{/if}
