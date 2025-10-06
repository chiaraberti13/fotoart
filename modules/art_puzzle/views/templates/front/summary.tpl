{extends file='page.tpl'}

{block name='page_title'}
  {l s='Conferma Personalizzazione' mod='art_puzzle'}
{/block}

{block name='page_content'}
<div class="art-puzzle-summary container my-5">
  <h2 class="mb-4 text-center">{l s='Riepilogo della tua personalizzazione' mod='art_puzzle'}</h2>

  <div class="row mb-4">
    <div class="col-md-6">
      <h4>{l s='Anteprima Puzzle' mod='art_puzzle'}</h4>
      {if isset($summary_image)}
        <img src="{$module_dir|escape:'htmlall':'UTF-8'}upload/{$summary_image}" class="img-fluid border" />
      {else}
        <p class="text-danger">{l s='Nessuna immagine disponibile.' mod='art_puzzle'}</p>
      {/if}
    </div>
    <div class="col-md-6">
      <h4>{l s='Dati personalizzazione' mod='art_puzzle'}</h4>
      <p><strong>{l s='Formato:' mod='art_puzzle'}</strong> {$summary_format}</p>
      <p><strong>{l s='Testo scatola:' mod='art_puzzle'}</strong> {$summary_box_text}</p>
    </div>
  </div>

  <form method="post" action="{$confirm_url}">
    <div class="form-group form-check">
      <input type="checkbox" class="form-check-input" id="confirm_summary" name="confirm_summary" required>
      <label class="form-check-label" for="confirm_summary">
        {l s='Confermo che i dati inseriti, il layout e i testi sono corretti.' mod='art_puzzle'}
      </label>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="material-icons">check_circle</i> {l s='Procedi al carrello' mod='art_puzzle'}
      </button>
    </div>
  </form>
</div>
{/block}
