{* Percorso: art_puzzle/views/templates/front/landing.tpl *}

<div class="art-puzzle-landing container">
  <div class="text-center my-5">
    <h1>{l s='Crea il tuo puzzle personalizzato' mod='art_puzzle'}</h1>
    <p class="lead">{l s='Carica una tua foto, scegli il formato e personalizza la scatola. Inizia ora!' mod='art_puzzle'}</p>
    <a href="{$link->getModuleLink('art_puzzle', 'customizer')}" class="btn btn-primary btn-lg mt-4">
      {l s='Carica immagine' mod='art_puzzle'}
    </a>
  </div>
</div>

<style>
.art-puzzle-landing {
  max-width: 720px;
}
</style>
