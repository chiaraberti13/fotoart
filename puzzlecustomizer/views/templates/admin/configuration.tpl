<div class="panel">
  <h3><i class="icon icon-cogs"></i> {l s='Configurazione generale' mod='puzzlecustomizer'}</h3>
  <form method="post" action="{$form_action}">
    <div class="form-group">
      <label class="control-label col-lg-3" for="puzzle-max-filesize">{l s='Dimensione massima file (MB)' mod='puzzlecustomizer'}</label>
      <div class="col-lg-9">
        <input type="number" class="form-control" name="PUZZLE_MAX_FILESIZE" id="puzzle-max-filesize" value="{$config.PUZZLE_MAX_FILESIZE|escape:'htmlall':'UTF-8'}" />
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3" for="puzzle-default-dpi">{l s='DPI di default' mod='puzzlecustomizer'}</label>
      <div class="col-lg-9">
        <input type="number" class="form-control" name="PUZZLE_DEFAULT_DPI" id="puzzle-default-dpi" value="{$config.PUZZLE_DEFAULT_DPI|escape:'htmlall':'UTF-8'}" />
      </div>
    </div>
    <div class="panel-footer">
      <button type="submit" name="submitPuzzleConfiguration" class="btn btn-primary">
        <i class="icon icon-save"></i> {l s='Salva' mod='puzzlecustomizer'}
      </button>
    </div>
  </form>
</div>
