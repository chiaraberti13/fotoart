<div class="panel">
    <div class="panel-heading">
        {l s='FotoArt Puzzle Production Dashboard' mod='fotoartpuzzle'}
    </div>
    <div class="panel-body">
        {if isset($confirmations) && $confirmations}
            {foreach from=$confirmations item=message}
                <div class="alert alert-success" role="alert">{$message|escape:'html':'UTF-8'}</div>
            {/foreach}
        {/if}
        {if isset($errors) && $errors}
            {foreach from=$errors item=message}
                <div class="alert alert-danger" role="alert">{$message|escape:'html':'UTF-8'}</div>
            {/foreach}
        {/if}

        <form method="get" action="{$controller_link|escape:'html':'UTF-8'}" class="form-inline fap-filters mb-3">
            <input type="hidden" name="controller" value="AdminFotoArtPuzzle" />
            <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
            <div class="form-group mr-2">
                <label class="mr-2" for="fap_date_from">{l s='From' mod='fotoartpuzzle'}</label>
                <input type="date" name="fap_date_from" id="fap_date_from" class="form-control" value="{$filters.date_from|escape:'html':'UTF-8'}" />
            </div>
            <div class="form-group mr-2">
                <label class="mr-2" for="fap_date_to">{l s='To' mod='fotoartpuzzle'}</label>
                <input type="date" name="fap_date_to" id="fap_date_to" class="form-control" value="{$filters.date_to|escape:'html':'UTF-8'}" />
            </div>
            <div class="form-group mr-2">
                <label class="mr-2" for="fap_status">{l s='Status' mod='fotoartpuzzle'}</label>
                <select name="fap_status" id="fap_status" class="form-control">
                    <option value="all" {if $filters.status == 'all'}selected="selected"{/if}>{l s='All statuses' mod='fotoartpuzzle'}</option>
                    {foreach from=$production_statuses key=code item=label}
                        <option value="{$code|escape:'html':'UTF-8'}" {if $filters.status == $code}selected="selected"{/if}>{$label|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <button type="submit" class="btn btn-primary mr-2">{l s='Apply filters' mod='fotoartpuzzle'}</button>
            <a class="btn btn-default" href="{$controller_link|escape:'html':'UTF-8'}">{l s='Reset' mod='fotoartpuzzle'}</a>
        </form>

        <form id="fap-download-form" method="post" action="{$controller_link|escape:'html':'UTF-8'}" class="d-inline">
            <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
        </form>

        {if $orders}
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <span class="sr-only">{l s='Select order' mod='fotoartpuzzle'}</span>
                            </th>
                            <th>{l s='Order' mod='fotoartpuzzle'}</th>
                            <th>{l s='Customer' mod='fotoartpuzzle'}</th>
                            <th>{l s='Order status' mod='fotoartpuzzle'}</th>
                            <th>{l s='Production status' mod='fotoartpuzzle'}</th>
                            <th>{l s='Customizations' mod='fotoartpuzzle'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$orders item=order}
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="fap_orders[]" value="{$order.id_order|intval}" form="fap-download-form" />
                                </td>
                                <td>
                                    <a href="{$order.order_link|escape:'html':'UTF-8'}" class="font-weight-bold">{$order.reference|escape:'html':'UTF-8'}</a><br />
                                    <small class="text-muted">{$order.date_add|escape:'html':'UTF-8'}</small>
                                </td>
                                <td>{$order.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$order.order_state|escape:'html':'UTF-8'}</td>
                                <td>
                                    <form method="post" action="{$controller_link|escape:'html':'UTF-8'}" class="form-inline">
                                        <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
                                        <input type="hidden" name="id_order" value="{$order.id_order|intval}" />
                                        <select name="production_status" class="form-control input-sm mr-2">
                                            {foreach from=$production_statuses key=code item=label}
                                                <option value="{$code|escape:'html':'UTF-8'}" {if $code == $order.production_status}selected="selected"{/if}>{$label|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                        <button type="submit" name="fapUpdateStatus" class="btn btn-primary btn-sm">{l s='Update' mod='fotoartpuzzle'}</button>
                                    </form>
                                    {if $order.status_updated}
                                        <small class="text-muted d-block mt-1">{l s='Updated on %s' sprintf=[$order.status_updated] mod='fotoartpuzzle'}</small>
                                    {/if}
                                </td>
                                <td>
                                    {if $order.customizations}
                                        <ul class="list-unstyled fap-customization-list">
                                            {foreach from=$order.customizations item=custom}
                                                <li class="mb-3">
                                                    <strong>{l s='Customization #%d' sprintf=[$custom.id_customization] mod='fotoartpuzzle'}</strong>
                                                    {if $custom.text}
                                                        <div>{l s='Text:' mod='fotoartpuzzle'} {$custom.text|escape:'html':'UTF-8'}</div>
                                                    {/if}
                                                    {if $custom.metadata}
                                                        <ul class="list-inline">
                                                            {foreach from=$custom.metadata key=metaKey item=metaValue}
                                                                <li class="list-inline-item"><small><strong>{$metaKey|escape:'html':'UTF-8'}:</strong> {$metaValue|escape:'html':'UTF-8'}</small></li>
                                                            {/foreach}
                                                        </ul>
                                                    {/if}
                                                    <div class="btn-group btn-group-sm mt-1" role="group">
                                                        {if $custom.image_link}
                                                            <a class="btn btn-default" href="{$custom.image_link|escape:'html':'UTF-8'}" target="_blank">{l s='Image' mod='fotoartpuzzle'}</a>
                                                        {/if}
                                                        {if $custom.preview_link}
                                                            <a class="btn btn-default" href="{$custom.preview_link|escape:'html':'UTF-8'}" target="_blank">{l s='Preview' mod='fotoartpuzzle'}</a>
                                                        {/if}
                                                        {if isset($custom.asset_downloads) && $custom.asset_downloads}
                                                            {foreach from=$custom.asset_downloads key=assetKey item=assetLink}
                                                                <a class="btn btn-default" href="{$assetLink|escape:'html':'UTF-8'}" target="_blank">{l s='%s asset' sprintf=[{$assetKey|replace:'_':' '|capitalize}] mod='fotoartpuzzle'}</a>
                                                            {/foreach}
                                                        {/if}
                                                    </div>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    {else}
                                        <em class="text-muted">{l s='No customization details found.' mod='fotoartpuzzle'}</em>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                <button type="submit" name="fapDownloadAssets" value="1" class="btn btn-primary" form="fap-download-form">
                    {l s='Download selected assets' mod='fotoartpuzzle'}
                </button>
            </div>
        {else}
            <p class="text-muted mb-0">{l s='No custom puzzle orders found for the selected filters.' mod='fotoartpuzzle'}</p>
        {/if}
    </div>
</div>
