<div class="puzzle-customizer__section" id="puzzle-editor">
  <h2>{l s='Editor Immagine' mod='puzzlecustomizer'}</h2>

  <div class="row">
    <div class="col-md-9">
      <canvas id="puzzle-canvas" width="800" height="600"></canvas>
    </div>

    <div class="col-md-3">
      <div class="editor-controls">

        <h4>{l s='Zoom' mod='puzzlecustomizer'}</h4>
        <div class="form-group">
          <input type="range" id="zoom-slider" class="form-control"
                 min="0.5" max="3" step="0.1" value="1" disabled>
          <span id="zoom-value">100%</span>
        </div>

        <h4>{l s='Rotazione' mod='puzzlecustomizer'}</h4>
        <div class="btn-group" role="group">
          <button type="button" id="rotate-left" class="btn btn-secondary" disabled>
            <i class="material-icons">rotate_left</i> {l s='90° Sinistra' mod='puzzlecustomizer'}
          </button>
          <button type="button" id="rotate-right" class="btn btn-secondary" disabled>
            <i class="material-icons">rotate_right</i> {l s='90° Destra' mod='puzzlecustomizer'}
          </button>
        </div>

        <h4>{l s='Specchia' mod='puzzlecustomizer'}</h4>
        <div class="btn-group" role="group">
          <button type="button" id="flip-horizontal" class="btn btn-secondary">
            <i class="material-icons">flip</i> {l s='Orizzontale' mod='puzzlecustomizer'}
          </button>
          <button type="button" id="flip-vertical" class="btn btn-secondary">
            <i class="material-icons">flip</i> {l s='Verticale' mod='puzzlecustomizer'}
          </button>
        </div>

        <h4>{l s='Ritaglia' mod='puzzlecustomizer'}</h4>
        <div class="btn-group-vertical" role="group">
          <button type="button" id="crop-button" class="btn btn-info">
            <i class="material-icons">crop</i> {l s='Abilita ritaglio' mod='puzzlecustomizer'}
          </button>
          <button type="button" id="apply-crop" class="btn btn-success" style="display:none;">
            <i class="material-icons">check</i> {l s='Applica' mod='puzzlecustomizer'}
          </button>
          <button type="button" id="cancel-crop" class="btn btn-danger" style="display:none;">
            <i class="material-icons">close</i> {l s='Annulla' mod='puzzlecustomizer'}
          </button>
        </div>

        <h4>{l s='Filtri' mod='puzzlecustomizer'}</h4>
        <div class="btn-group-vertical" role="group">
          <button type="button" class="btn btn-secondary" data-filter="none">
            {l s='Nessuno' mod='puzzlecustomizer'}
          </button>
          <button type="button" class="btn btn-secondary" data-filter="grayscale">
            {l s='Bianco e Nero' mod='puzzlecustomizer'}
          </button>
          <button type="button" class="btn btn-secondary" data-filter="sepia">
            {l s='Seppia' mod='puzzlecustomizer'}
          </button>
          <button type="button" class="btn btn-secondary" data-filter="invert">
            {l s='Inverti' mod='puzzlecustomizer'}
          </button>
          <button type="button" class="btn btn-secondary" data-filter="brightness">
            {l s='Luminosità+' mod='puzzlecustomizer'}
          </button>
          <button type="button" class="btn btn-secondary" data-filter="contrast">
            {l s='Contrasto+' mod='puzzlecustomizer'}
          </button>
        </div>

        <h4>{l s='Testo' mod='puzzlecustomizer'}</h4>
        <button type="button" id="add-text" class="btn btn-primary btn-block">
          <i class="material-icons">text_fields</i> {l s='Aggiungi Testo' mod='puzzlecustomizer'}
        </button>

        <button type="button" id="reset-editor" class="btn btn-warning btn-block mt-3">
          <i class="material-icons">refresh</i> {l s='Reset' mod='puzzlecustomizer'}
        </button>

      </div>
    </div>
  </div>
</div>
