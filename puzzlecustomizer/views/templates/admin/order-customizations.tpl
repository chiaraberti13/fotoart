<div class="card">
  <h3 class="card-header">
    {l s='Puzzle Customizations' mod='puzzlecustomizer'}
  </h3>
  <div class="card-body">
    <table class="table">
      <thead>
        <tr>
          <th>{l s='ID' mod='puzzlecustomizer'}</th>
          <th>{l s='Cart' mod='puzzlecustomizer'}</th>
          <th>{l s='Status' mod='puzzlecustomizer'}</th>
          <th>{l s='Image' mod='puzzlecustomizer'}</th>
          <th>{l s='Created At' mod='puzzlecustomizer'}</th>
          <th>{l s='Updated At' mod='puzzlecustomizer'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$customizations item=customization}
          <tr>
            <td>{$customization.id_puzzle_customization|intval}</td>
            <td>{$customization.id_cart|intval}</td>
            <td>{$customization.status|escape:'htmlall':'UTF-8'}</td>
            <td>
              {if $customization.image_path}
                <a href="{$module_dir}uploads/customizations/{$customization.image_path|escape:'htmlall':'UTF-8'}" target="_blank">
                  {l s='Download' mod='puzzlecustomizer'}
                </a>
              {else}
                {l s='N/A' mod='puzzlecustomizer'}
              {/if}
            </td>
            <td>{$customization.created_at|escape:'htmlall':'UTF-8'}</td>
            <td>{$customization.updated_at|escape:'htmlall':'UTF-8'}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  </div>
</div>
