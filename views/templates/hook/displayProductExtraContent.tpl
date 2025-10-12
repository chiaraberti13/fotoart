{*
* Art Puzzle Module - VERSIONE FINALE
* Apertura diretta customizer senza doppio pulsante
*}

<div id="art-puzzle-tab-content" class="art-puzzle-container tab-pane">
    {* Area del customizer SEMPRE VISIBILE *}
    <div class="art-puzzle-customizer-area" id="art-puzzle-customizer-area">
        <div class="row">
            <div class="col-md-12">
                <h3>{l s='Personalizza il tuo Puzzle' mod='art_puzzle'}</h3>
                
                {* Step 1: Upload immagine *}
                <div class="step-container" id="step-upload">
                    <h4><span class="badge badge-primary">1</span> {l s='Carica la tua immagine' mod='art_puzzle'}</h4>
                    <div class="upload-area text-center p-4 border rounded" id="upload-area" style="border: 2px dashed #2196F3; cursor: pointer; background: #f8f9fa;">
                        <i class="material-icons" style="font-size: 48px; color: #2196F3;">cloud_upload</i>
                        <h5>{l s='Clicca o trascina qui la tua immagine' mod='art_puzzle'}</h5>
                        <p class="text-muted">{l s='Formati supportati: JPG, PNG, GIF' mod='art_puzzle'}</p>
                        <p class="text-muted small">{l s='Dimensione massima: 20MB' mod='art_puzzle'}</p>
                        <input type="file" id="puzzle-image-input" accept="image/*" style="display: none;">
                    </div>
                    <div id="image-preview" class="mt-3 text-center" style="display: none;">
                        <img id="preview-img" src="" alt="Preview" class="img-fluid" style="max-height: 400px; border-radius: 8px;">
                        <div class="mt-3">
                            <button class="btn btn-secondary btn-sm" id="change-image">
                                <i class="material-icons">refresh</i> {l s='Cambia immagine' mod='art_puzzle'}
                            </button>
                        </div>
                    </div>
                </div>
                
                {* Step 2: Seleziona formato *}
                <div class="step-container mt-4" id="step-format" style="display: none;">
                    <h4><span class="badge badge-primary">2</span> {l s='Scegli il formato del puzzle' mod='art_puzzle'}</h4>
                    <div class="format-options row">
                        {if isset($puzzle_formats) && $puzzle_formats|@count > 0}
                            {foreach from=$puzzle_formats item=format}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="format-option card h-100" style="cursor: pointer;"
                                         data-format-id="{$format.id|escape:'html':'UTF-8'}"
                                         data-format-name="{$format.name|escape:'html':'UTF-8'}"
                                         data-price="{$format.price|floatval}"
                                         data-price-display="{$format.price_display|escape:'html':'UTF-8'}">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{$format.name|escape:'html':'UTF-8'}</h5>
                                            <p class="card-text">{$format.dimensions|escape:'html':'UTF-8'}</p>
                                            <p class="price font-weight-bold text-primary">{$format.price_display|escape:'html':'UTF-8'}</p>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        {else}
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    {l s='Non sono stati configurati formati disponibili per questo prodotto.' mod='art_puzzle'}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
                
                {* Step 3: Personalizza scatola *}
                <div class="step-container mt-4" id="step-box" style="display: none;">
                    <h4><span class="badge badge-primary">3</span> {l s='Personalizza la scatola (opzionale)' mod='art_puzzle'}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{l s='Testo sulla scatola' mod='art_puzzle'}</label>
                                <input type="text" class="form-control" id="box-text"
                                       value="{if isset($default_box_text)}{$default_box_text|escape:'html':'UTF-8'}{/if}"
                                       placeholder="{l s='Es: Il nostro puzzle speciale' mod='art_puzzle'}"
                                       {if isset($max_box_text_length)}maxlength="{$max_box_text_length|intval}"{/if}>
                                <small class="form-text text-muted">
                                    {if isset($max_box_text_length)}
                                        {l s='Max %d caratteri' sprintf=[$max_box_text_length|intval] mod='art_puzzle'}
                                    {else}
                                        {l s='Max 30 caratteri' mod='art_puzzle'}
                                    {/if}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{l s='Colore scatola' mod='art_puzzle'}</label>
                                <div class="color-options d-flex flex-wrap">
                                    {if isset($puzzle_box_colors) && $puzzle_box_colors|@count > 0}
                                        {foreach from=$puzzle_box_colors item=color name=colorLoop}
                                            <div class="color-option mr-2 mb-2{if $smarty.foreach.colorLoop.iteration == 1} selected{/if}"
                                                 data-color="{$color.value|escape:'html':'UTF-8'}"
                                                 data-text-color="{$color.text_color|escape:'html':'UTF-8'}"
                                                 data-color-label="{$color.label|escape:'html':'UTF-8'}"
                                                 style="width: 40px; height: 40px; background: {$color.value|escape:'html':'UTF-8'}; border: 2px solid #ccc; cursor: pointer; border-radius: 4px;">
                                                <span class="sr-only">{$color.label|escape:'html':'UTF-8'}</span>
                                            </div>
                                        {/foreach}
                                    {else}
                                        <div class="text-muted">{l s='Nessun colore disponibile' mod='art_puzzle'}</div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{l s='Font del testo' mod='art_puzzle'}</label>
                                <div class="font-options d-flex flex-wrap">
                                    {if isset($puzzle_fonts) && $puzzle_fonts|@count > 0}
                                        {foreach from=$puzzle_fonts item=font name=fontLoop}
                                            <button type="button"
                                                    class="btn btn-outline-secondary font-option mr-2 mb-2{if $smarty.foreach.fontLoop.iteration == 1} active{/if}"
                                                    data-font="{$font.id|escape:'html':'UTF-8'}"
                                                    data-font-label="{$font.label|escape:'html':'UTF-8'}"
                                                    style="font-family: {$font.font_family|escape:'html':'UTF-8'};">
                                                {$font.label|escape:'html':'UTF-8'}
                                            </button>
                                        {/foreach}
                                    {else}
                                        <div class="text-muted">{l s='Utilizzeremo il font predefinito' mod='art_puzzle'}</div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {* Riepilogo finale *}
                <div class="step-container mt-4" id="step-summary" style="display: none;">
                    <h4><span class="badge badge-success">✓</span> {l s='Riepilogo personalizzazione' mod='art_puzzle'}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img id="summary-image" src="" alt="" class="img-fluid rounded">
                                </div>
                                <div class="col-md-8">
                                    <h5>{l s='Il tuo puzzle personalizzato' mod='art_puzzle'}</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>{l s='Formato:' mod='art_puzzle'}</strong> <span id="summary-format"></span></li>
                                        <li><strong>{l s='Prezzo:' mod='art_puzzle'}</strong> <span id="summary-price"></span></li>
                                        <li><strong>{l s='Testo scatola:' mod='art_puzzle'}</strong> <span id="summary-text"></span></li>
                                        <li><strong>{l s='Colore scatola:' mod='art_puzzle'}</strong> <span id="summary-color"></span></li>
                                        <li><strong>{l s='Font:' mod='art_puzzle'}</strong> <span id="summary-font"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {* Pulsanti navigazione *}
                <div class="action-buttons mt-4 d-flex justify-content-between" id="action-buttons">
                    <button class="btn btn-outline-secondary" id="btn-back" style="display: none;">
                        <i class="material-icons">arrow_back</i> {l s='Indietro' mod='art_puzzle'}
                    </button>
                    <button class="btn btn-primary" id="btn-next">
                        {l s='Continua' mod='art_puzzle'} <i class="material-icons">arrow_forward</i>
                    </button>
                    <button class="btn btn-success btn-lg" id="btn-add-to-cart" style="display: none;">
                        <i class="material-icons">shopping_cart</i> {l s='Aggiungi al carrello' mod='art_puzzle'}
                    </button>
                </div>
                
                {* Progress bar *}
                <div class="progress mt-4" style="height: 5px;">
                    <div class="progress-bar bg-primary" id="progress-bar" role="progressbar" style="width: 25%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{* Hidden inputs *}
<input type="hidden" id="art-puzzle-ajax-url" value="{if isset($puzzleAjaxUrl)}{$puzzleAjaxUrl|escape:'html'}{/if}" />
<input type="hidden" id="art-puzzle-token" value="{if isset($securityToken)}{$securityToken|escape:'html'}{/if}" />
<input type="hidden" id="art-puzzle-product-id" value="{if isset($id_product)}{$id_product|intval}{/if}" />

{* CSS inline migliorato *}
<style>
.format-option.selected {
    border: 2px solid #2196F3 !important;
    box-shadow: 0 0 10px rgba(33,150,243,0.3);
}
.color-option.selected {
    border: 3px solid #2196F3 !important;
    transform: scale(1.1);
}
.font-option.active {
    border-color: #2196F3;
    color: #2196F3;
}
.upload-area.dragover {
    background-color: #e3f2fd !important;
    border-color: #1976D2 !important;
}
.step-container h4 .badge {
    font-size: 0.9em;
    vertical-align: middle;
    margin-right: 10px;
}
</style>

{* JavaScript senza jQuery - vanilla JavaScript *}
<script type="text/javascript">
(function() {
    'use strict';
    
    // Aspetta che il DOM sia pronto
    function domReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }
    
    domReady(function() {
        console.log('Art Puzzle Customizer: Inizializzazione (No jQuery)');
        
        // Variabili
        var currentStep = 1;
        var totalSteps = 4;
        var customizationData = {
            image: null,
            imagePreview: null,
            formatId: null,
            formatName: '',
            price: 0,
            priceFormatted: '',
            boxText: document.getElementById('box-text') ? document.getElementById('box-text').value : '',
            boxColor: '',
            boxColorLabel: '',
            boxFont: document.querySelector('.font-option.active') ? document.querySelector('.font-option.active').getAttribute('data-font') : '',
            boxFontLabel: document.querySelector('.font-option.active') ? document.querySelector('.font-option.active').getAttribute('data-font-label') : '',
            productId: document.getElementById('art-puzzle-product-id')?.value || ''
        };
        
        // Elementi DOM
        var elements = {
            uploadArea: document.getElementById('upload-area'),
            fileInput: document.getElementById('puzzle-image-input'),
            imagePreview: document.getElementById('image-preview'),
            previewImg: document.getElementById('preview-img'),
            changeImageBtn: document.getElementById('change-image'),
            formatOptions: document.querySelectorAll('.format-option'),
            colorOptions: document.querySelectorAll('.color-option'),
            fontOptions: document.querySelectorAll('.font-option'),
            boxText: document.getElementById('box-text'),
            btnNext: document.getElementById('btn-next'),
            btnBack: document.getElementById('btn-back'),
            btnAddToCart: document.getElementById('btn-add-to-cart'),
            progressBar: document.getElementById('progress-bar'),
            steps: {
                upload: document.getElementById('step-upload'),
                format: document.getElementById('step-format'),
                box: document.getElementById('step-box'),
                summary: document.getElementById('step-summary')
            }
        };
        
        // Event: Click su area upload
        if (elements.uploadArea) {
            elements.uploadArea.addEventListener('click', function() {
                elements.fileInput.click();
            });
            
            // Drag and drop
            elements.uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            elements.uploadArea.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });
            
            elements.uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });
        }
        
        // Event: File input change
        if (elements.fileInput) {
            elements.fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    handleFileSelect(this.files[0]);
                }
            });
        }
        
        // Event: Cambio immagine
        if (elements.changeImageBtn) {
            elements.changeImageBtn.addEventListener('click', function() {
                elements.fileInput.click();
            });
        }
        
        // Event: Selezione formato
        elements.formatOptions.forEach(function(option, index) {
            option.addEventListener('click', function() {
                elements.formatOptions.forEach(function(opt) {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                customizationData.formatId = this.getAttribute('data-format-id');
                customizationData.formatName = this.getAttribute('data-format-name') || '';
                customizationData.price = parseFloat(this.getAttribute('data-price') || '0');
                customizationData.priceFormatted = this.getAttribute('data-price-display') || '';
            });

            if (index === 0) {
                option.click();
            }
        });

        // Event: Selezione colore
        elements.colorOptions.forEach(function(option, index) {
            option.addEventListener('click', function() {
                elements.colorOptions.forEach(function(opt) {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                customizationData.boxColor = this.getAttribute('data-color') || '';
                customizationData.boxColorLabel = this.getAttribute('data-color-label') || customizationData.boxColor;
            });

            if (index === 0) {
                option.click();
            }
        });

        // Event: Selezione font
        elements.fontOptions.forEach(function(option, index) {
            option.addEventListener('click', function() {
                elements.fontOptions.forEach(function(opt) {
                    opt.classList.remove('active');
                });
                this.classList.add('active');
                customizationData.boxFont = this.getAttribute('data-font') || '';
                customizationData.boxFontLabel = this.getAttribute('data-font-label') || '';
            });

            if (index === 0) {
                option.click();
            }
        });
        
        // Event: Testo scatola
        if (elements.boxText) {
            elements.boxText.addEventListener('input', function() {
                customizationData.boxText = this.value;
            });
        }
        
        // Event: Navigazione
        if (elements.btnNext) {
            elements.btnNext.addEventListener('click', function() {
                if (validateCurrentStep()) {
                    nextStep();
                }
            });
        }
        
        if (elements.btnBack) {
            elements.btnBack.addEventListener('click', function() {
                previousStep();
            });
        }
        
        if (elements.btnAddToCart) {
            elements.btnAddToCart.addEventListener('click', function() {
                addToCart();
            });
        }
        
        // Funzioni
        function handleFileSelect(file) {
            if (!file.type.match('image.*')) {
                alert('{l s="Seleziona un file immagine valido" mod="art_puzzle" js=1}');
                return;
            }
            
            if (file.size > 20 * 1024 * 1024) {
                alert('{l s="Il file è troppo grande. Max 20MB" mod="art_puzzle" js=1}');
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
                elements.previewImg.src = e.target.result;
                elements.uploadArea.style.display = 'none';
                elements.imagePreview.style.display = 'block';
                customizationData.image = file;
                customizationData.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
        
        function validateCurrentStep() {
            switch(currentStep) {
                case 1:
                    if (!customizationData.image) {
                        alert('{l s="Carica un'immagine per continuare" mod="art_puzzle" js=1}');
                        return false;
                    }
                    break;
                case 2:
                    if (!customizationData.formatId) {
                        alert('{l s="Seleziona un formato per continuare" mod="art_puzzle" js=1}');
                        return false;
                    }
                    break;
            }
            return true;
        }
        
        function showStep(step) {
            // Nascondi tutti gli step
            Object.values(elements.steps).forEach(function(el) {
                if (el) el.style.display = 'none';
            });
            
            // Mostra lo step corrente
            switch(step) {
                case 1:
                    elements.steps.upload.style.display = 'block';
                    break;
                case 2:
                    elements.steps.format.style.display = 'block';
                    break;
                case 3:
                    elements.steps.box.style.display = 'block';
                    break;
                case 4:
                    updateSummary();
                    elements.steps.summary.style.display = 'block';
                    break;
            }
            
            updateProgressBar();
            updateButtons();
        }
        
        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
        
        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }
        
        function updateButtons() {
            elements.btnBack.style.display = currentStep > 1 ? 'block' : 'none';
            elements.btnNext.style.display = currentStep < totalSteps ? 'block' : 'none';
            elements.btnAddToCart.style.display = currentStep === totalSteps ? 'block' : 'none';
            
            // Centra il pulsante se è solo
            if (currentStep === 1) {
                elements.btnNext.classList.add('mx-auto');
            } else {
                elements.btnNext.classList.remove('mx-auto');
            }
        }
        
        function updateProgressBar() {
            var progress = (currentStep / totalSteps) * 100;
            elements.progressBar.style.width = progress + '%';
        }
        
        function updateSummary() {
            var summaryImage = document.getElementById('summary-image');
            var summaryFormat = document.getElementById('summary-format');
            var summaryPrice = document.getElementById('summary-price');
            var summaryText = document.getElementById('summary-text');
            var summaryColor = document.getElementById('summary-color');
            var summaryFont = document.getElementById('summary-font');

            if (summaryImage) summaryImage.src = customizationData.imagePreview || '';
            if (summaryFormat) summaryFormat.textContent = customizationData.formatName || '-';
            if (summaryPrice) summaryPrice.textContent = customizationData.priceFormatted || '0';
            if (summaryText) summaryText.textContent = customizationData.boxText || '{l s="Nessun testo" mod="art_puzzle" js=1}';
            if (summaryColor) summaryColor.textContent = customizationData.boxColorLabel || customizationData.boxColor || '-';
            if (summaryFont) summaryFont.textContent = customizationData.boxFontLabel || '{l s="Font predefinito" mod="art_puzzle" js=1}';
        }

        function addToCart() {
            console.log('Aggiunta al carrello:', customizationData);
            
            // Mostra loading
            var btnCart = document.getElementById('btn-add-to-cart');
            var originalText = btnCart.innerHTML;
            btnCart.disabled = true;
            btnCart.innerHTML = '<i class="material-icons rotating">refresh</i> ' + '{l s="Elaborazione..." mod="art_puzzle" js=1}';
            
            // Step 1: Upload immagine
            if (customizationData.image) {
                var formData = new FormData();
                formData.append('action', 'uploadImage');
                formData.append('image', customizationData.image);
                formData.append('ajax', '1');
                formData.append('token', document.getElementById('art-puzzle-token')?.value || '');
                
                fetch(document.getElementById('art-puzzle-ajax-url').value || '/module/art_puzzle/ajax', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(uploadResult => {
                    if (uploadResult.success) {
                        console.log('Immagine caricata:', uploadResult.data);
                        
                        // Step 2: Salva personalizzazione
                        var saveData = new FormData();
                        saveData.append('action', 'saveCustomization');
                        saveData.append('product_id', customizationData.productId);
                        saveData.append('format', customizationData.formatId);
                        saveData.append('box_text', customizationData.boxText);
                        saveData.append('box_color', customizationData.boxColor);
                        saveData.append('box_font', customizationData.boxFont);
                        saveData.append('image_filename', uploadResult.data.filename);
                        saveData.append('ajax', '1');
                        saveData.append('token', document.getElementById('art-puzzle-token')?.value || '');
                        
                        return fetch(document.getElementById('art-puzzle-ajax-url').value || '/module/art_puzzle/ajax', {
                            method: 'POST',
                            body: saveData
                        });
                    } else {
                        throw new Error(uploadResult.message || 'Errore upload immagine');
                    }
                })
                .then(response => response.json())
                .then(saveResult => {
                    if (saveResult.success) {
                        console.log('Personalizzazione salvata:', saveResult.data);
                        
                        // Step 3: Aggiungi al carrello
                        var cartData = new FormData();
                        cartData.append('action', 'addToCart');
                        cartData.append('product_id', customizationData.productId);
                        cartData.append('customization_id', saveResult.data.customization_id);
                        cartData.append('format', customizationData.formatId);
                        cartData.append('ajax', '1');
                        cartData.append('token', document.getElementById('art-puzzle-token')?.value || '');
                        
                        return fetch(document.getElementById('art-puzzle-ajax-url').value || '/module/art_puzzle/ajax', {
                            method: 'POST',
                            body: cartData
                        });
                    } else {
                        throw new Error(saveResult.message || 'Errore salvataggio personalizzazione');
                    }
                })
                .then(response => response.json())
                .then(cartResult => {
                    if (cartResult.success) {
                        console.log('Aggiunto al carrello:', cartResult.data);
                        
                        // Aggiorna counter carrello se esiste
                        var cartCounter = document.querySelector('.cart-products-count');
                        if (cartCounter) {
                            cartCounter.textContent = '(' + cartResult.data.cart_count + ')';
                        }
                        
                        // Mostra messaggio di successo
                        alert('{l s="Puzzle personalizzato aggiunto al carrello!" mod="art_puzzle" js=1}');
                        
                        // Opzionale: redirect al carrello
                        if (confirm('{l s="Vuoi andare al carrello?" mod="art_puzzle" js=1}')) {
                            window.location.href = cartResult.data.cart_url;
                        } else {
                            // Reset del form
                            window.location.reload();
                        }
                    } else {
                        throw new Error(cartResult.message || 'Errore aggiunta al carrello');
                    }
                })
                .catch(error => {
                    console.error('Errore:', error);
                    alert('{l s="Errore durante l\'elaborazione. Riprova." mod="art_puzzle" js=1}' + '\n\n' + error.message);
                    btnCart.disabled = false;
                    btnCart.innerHTML = originalText;
                });
            }
        }
        
        // Inizializza
        showStep(1);
        console.log('Art Puzzle Customizer: Pronto!');
    });
})();
</script>