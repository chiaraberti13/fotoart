# FotoArt Puzzle Module

Questo modulo per PrestaShop 1.7.6.9 consente la personalizzazione di puzzle tramite caricamento immagine.

## Funzioni principali
- Configurazione BO per upload, formati puzzle, personalizzazione scatola e notifiche email.
- Flusso front-end con upload protetto, anteprima scatola e riepilogo personalizzazione.
- Wizard modale pronto all'uso che guida il cliente dal caricamento all'inserimento nel carrello.
- Integrazione con il sistema di `customization` di PrestaShop per associare l’immagine all’ordine.
- Servizio di pulizia file temporanei e salvataggio asset in percorsi protetti.

## Struttura
- `classes/` servizi applicativi.
- `controllers/front/` controller per upload, anteprima, riepilogo, download sicuro e API.
- `views/` asset e template Smarty.

## Note
Per abilitare il wizard occorre indicare gli ID prodotto nella configurazione del modulo (lista separata da virgole). Le classi forniscono i punti d’estensione necessari per integrare le logiche di produzione e invio email.
