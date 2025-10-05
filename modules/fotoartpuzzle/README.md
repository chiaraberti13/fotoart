# FotoArt Puzzle Module

Questo modulo per PrestaShop 1.7.6.9 consente la personalizzazione di puzzle tramite caricamento immagine.

## Funzioni principali
- Configurazione BO per upload, formati puzzle, personalizzazione scatola e notifiche email.
- Flusso front-end con upload protetto, anteprima scatola e riepilogo personalizzazione.
- Integrazione con il sistema di `customization` di PrestaShop per associare l’immagine all’ordine.
- Servizio di pulizia file temporanei e salvataggio asset in percorsi protetti.

## Struttura
- `classes/` servizi applicativi.
- `controllers/front/` controller per upload, anteprima, riepilogo, download sicuro e API.
- `views/` asset e template Smarty.

## Note
Il wizard front-end è da completare con l’UI definitiva. Le classi forniscono i punti d’estensione necessari per integrare le logiche di produzione e invio email.
