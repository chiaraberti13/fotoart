{*
* Art Puzzle Module - Product Buttons Template
* FIXED VERSION - Corretto problema $link nullo
*}

{if isset($puzzleAjaxUrl) && $puzzleAjaxUrl}
<div class="art-puzzle-customize-button my-3">
    <button type="button" class="btn btn-primary btn-block" id="art-puzzle-customize-btn-{$id_product|intval}">
        <i class="material-icons">brush</i> {l s='Personalizza il tuo puzzle' mod='art_puzzle'}
    </button>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var customizeBtn = document.getElementById('art-puzzle-customize-btn-{$id_product|intval}');
    var puzzleAjaxUrl = "{$puzzleAjaxUrl|escape:'javascript'}";
    
    if (customizeBtn) {
        customizeBtn.addEventListener('click', function() {
            console.log('Art Puzzle: Pulsante personalizzazione cliccato');
            
            // Cerca il tab del puzzle
            var tabs = document.querySelectorAll('.nav-tabs .nav-link');
            var tabFound = false;
            
            if (tabs && tabs.length > 0) {
                for (var i = 0; i < tabs.length; i++) {
                    var tabText = tabs[i].textContent.toLowerCase().trim();
                    if (tabText.indexOf('personalizza') !== -1 && tabText.indexOf('puzzle') !== -1) {
                        console.log('Art Puzzle: Tab trovato e attivato');
                        tabs[i].click();
                        tabFound = true;
                        
                        // Scroll al contenuto
                        setTimeout(function() {
                            var tabContent = document.getElementById('art-puzzle-tab-content') || 
                                            document.querySelector('.art-puzzle-container');
                            if (tabContent) {
                                tabContent.scrollIntoView({ldelim}behavior: "smooth"{rdelim});
                            }
                        }, 300);
                        break;
                    }
                }
            }
            
            if (!tabFound) {
                console.log('Art Puzzle: Tab non trovato, provo apertura diretta');
                if (puzzleAjaxUrl) {
                    // Fallback: apri il customizer direttamente
                    window.location.href = puzzleAjaxUrl;
                }
            }
        });
    }
});
</script>
{else}
<!-- Art Puzzle: URL Ajax non disponibile -->
{/if}