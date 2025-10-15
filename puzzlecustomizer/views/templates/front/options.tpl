<div class="puzzle-customizer__section" id="puzzle-options">
  <h2>{l s='Opzioni puzzle' mod='puzzlecustomizer'}</h2>

  <div class="form-group">
    <label for="puzzle-dimension">{l s='Dimensione e numero pezzi' mod='puzzlecustomizer'}</label>
    <select id="puzzle-dimension" class="form-control">
      <option value="">{l s='Seleziona dimensione' mod='puzzlecustomizer'}</option>
      {foreach from=$puzzle_options item=option}
        <option value="{$option.id|intval}"
                data-width="{$option.width_mm|floatval}"
                data-height="{$option.height_mm|floatval}"
                data-pieces="{$option.pieces|intval}"
                data-price="{$option.price_impact|floatval}">
          {$option.name|escape:'html':'UTF-8'}
          {if $option.price_impact > 0}
            (+{$option.price_impact|string_format:"%.2f"}€)
          {/if}
        </option>
      {/foreach}
    </select>
  </div>

  <div class="form-group">
    <label for="puzzle-box-color">{l s='Colore scatola' mod='puzzlecustomizer'}</label>
    <select id="puzzle-box-color" class="form-control">
      <option value="">{l s='Seleziona colore' mod='puzzlecustomizer'}</option>
      {foreach from=$box_colors item=color}
        <option value="{$color.id|intval}" data-hex="{$color.hex|escape:'html':'UTF-8'}">
          {$color.name|escape:'html':'UTF-8'}
        </option>
      {/foreach}
    </select>
  </div>

  <div class="form-group">
    <label for="puzzle-text-input">{l s='Testo personalizzato (opzionale)' mod='puzzlecustomizer'}</label>
    <input type="text" id="puzzle-text-input" class="form-control" maxlength="500"
           placeholder="{l s='Inserisci testo' mod='puzzlecustomizer'}">
  </div>

  <div class="form-group">
    <label for="puzzle-text-color">{l s='Colore testo' mod='puzzlecustomizer'}</label>
    <select id="puzzle-text-color" class="form-control">
      <option value="">{l s='Seleziona colore' mod='puzzlecustomizer'}</option>
      {foreach from=$text_colors item=color}
        <option value="{$color.id|intval}" data-hex="{$color.hex|escape:'html':'UTF-8'}">
          {$color.name|escape:'html':'UTF-8'}
        </option>
      {/foreach}
    </select>
  </div>

  <div class="form-group">
    <label for="puzzle-font">{l s='Font testo' mod='puzzlecustomizer'}</label>
    <select id="puzzle-font" class="form-control">
      <option value="">{l s='Seleziona font' mod='puzzlecustomizer'}</option>
      {foreach from=$fonts item=font}
        <option value="{$font.id|intval}">
          {$font.name|escape:'html':'UTF-8'}
        </option>
      {/foreach}
    </select>
  </div>
</div>
