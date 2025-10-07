# Checklist modulo FotoArt Puzzle

## Backend
- [x] Implementare entry point pubblico nel controller front (`controllers/front/index.php`) per gestire il wizard del prodotto puzzle.
- [x] Ampliare l'endpoint di upload per calcolare orientamento, qualità, coordinate di crop e generare preview/thumb delle immagini caricate.
- [x] Introdurre un servizio di qualità (`FAPQualityService`) che replichi gli algoritmi `getQuality`, `setCoordinates`, `getLandscapeSelection`, ecc.
- [x] Gestire elenchi puzzle e box tramite nuove strutture dati/tabelle, esponendo endpoint dedicati (`getPuzzles`, `getBoxes`, ecc.).

- [ ] Estendere `FAPCustomizationService` per persistere metadati completi: ID puzzle, coordinate crop, qualità, ID box, note PDF, ecc.
- [ ] Implementare la generazione PDF (utente/admin) sfruttando librerie come TCPDF/FPDI e allegare gli output a email/ordini.
- [ ] Migliorare la sicurezza dei token front-end legandoli alla sessione carrello/cliente invece della data.
- [ ] Allineare i font utilizzati dal front-end con quelli disponibili lato server e aggiornare `FAPBoxRenderer` per i template grafici.
- [ ] Ampliare gli endpoint AJAX per coprire tutte le azioni legacy (sessione, asset manageriali, recupero configurazioni, ecc.).
- [ ] Aggiornare i template admin per mostrare preview hi-res, crop, qualità e consentire download asset.

## Frontend
- [ ] Estendere il wizard JavaScript (`puzzle.js`) con step di crop interattivo, rotazione, scelta box avanzata e valutazione qualità in tempo reale.
- [ ] Gestire uno stato completo che includa ID puzzle, crop, qualità, box, preview hi-res e inviarlo al controller `summary`.
- [ ] Sostituire l'invio automatico al carrello con una gestione che attenda il salvataggio server-side e sincronizzi `id_customization`.
- [ ] Mostrare al cliente anteprime realistiche della scatola, caricando le immagini generate dal server in base al template scelto.

## Integrazione e configurazione
- [ ] Adeguare `configure.tpl` per mappare prodotti/varianti PrestaShop ai puzzle storici e validare prezzi/dimensioni/disponibilità.
- [ ] Pianificare migrazioni del filesystem (immagini caricate, ordini in lavorazione) nelle nuove cartelle `var/tmp` e `var/orders`.
- [ ] Documentare le dipendenze (es. estensioni PHP, librerie PDF, Cropper.js) necessarie per lo sviluppo e la produzione.

## Test e qualità
- [ ] Definire una suite di test manuali/automatici che copra upload con varie risoluzioni, scelta formati/box, invio ordini e verifica asset admin.
- [ ] Verificare compatibilità multilingua e multi-shop, inclusi token e gestione sessioni.
