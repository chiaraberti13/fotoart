# FotoArt Puzzle Module

Questo modulo per PrestaShop 1.7.6.9 consente la personalizzazione di puzzle tramite caricamento immagine.

## Funzioni principali
- Configurazione BO per upload, formati puzzle, personalizzazione scatola e notifiche email.
- Flusso front-end con upload protetto, anteprima scatola e riepilogo personalizzazione.
- Integrazione con il sistema di `customization` di PrestaShop per associare l’immagine all’ordine.
- Servizio di pulizia file temporanei e salvataggio asset in percorsi protetti.
- Token HMAC con scadenza per upload, AJAX e download (parità con i flussi `fotoart/`).
- Validazione percorsi e asset per impedire traversal e accessi fuori root.

## Mappatura funzionale rispetto allo script legacy

| Funzionalità legacy (`fotoart/`) | Implementazione modulo |
| -------------------------------- | ----------------------- |
| Upload immagine con analisi qualità, orientamento e anteprime | `controllers/front/upload.php`, `FAPImageProcessor`, `FAPImageAnalysis`, preview generata con `FAPBoxRenderer` |
| Wizard multi-step (upload → formato → box → riepilogo) | `views/js/puzzle.js` con token CSRF per ogni endpoint; `controllers/front/index.php` |
| Gestione sessione personalizzazione lato server | `FAPSessionService` + endpoint `controllers/front/ajax.php` |
| Persistenza metadati e aggancio alle customizzazioni ordine | `FAPCustomizationService` e hook ordine |
| Download asset sicuro per admin e cliente | `FotoArtPuzzle::getDownloadLink()` + controller `download.php` con token HMAC e autorizzazioni |
| Generazione asset derivati (crop, preview, mockup scatola) | `FAPAssetGenerationService`, `FAPBoxRenderer`, `FAPPdfGenerator` |

## Test automatici

Dal root del repository:

```bash
composer install
composer test
```

La suite include:
- `TokenServiceTest`: validazione firma, scadenza e autorizzazioni dei token.
- `PathValidatorTest`: assicurazione contro traversal fuori dalle directory consentite.
- `SessionServiceTest`: ciclo di vita dei salvataggi temporanei del wizard.

Per eseguire il linting e l’analisi statica:

```bash
composer lint
composer phpstan
```

## Checklist manuale consigliata

1. Installare il modulo su PrestaShop 1.7.6 (PHP 7.3) ed eseguire `FAPConfiguration::installDefaults()`.
2. Associare un prodotto al puzzle dal pannello modulo e verificare il pulsante "Crea il tuo puzzle" sul front-end.
3. Eseguire il wizard caricando un file ad alta risoluzione, confermare anteprime e completare l’inserimento nel carrello.
4. Confermare un ordine e verificare da BO la scheda extra con asset scaricabili e PDF generati.
5. Testare i link di download da email/BO e lato cliente verificando la scadenza dei token.
6. Verificare multistore cambiando shop: i parametri specifici (prodotti abilitati, colori scatola) restano isolati.

## CI

La pipeline GitHub Actions (`.github/workflows/ci.yml`) esegue automaticamente:
- `composer lint` (PHP-CS-Fixer in modalità dry-run + `php -l`).
- `composer phpstan` a livello 5 con bootstrap dei mock PrestaShop.
- `composer test` per la suite unit test.

## Ambiente di sviluppo

- PHP 7.3 con estensioni GD, JSON, cURL, mbstring.
- PrestaShop 1.7.6.9.
- Composer per installare le dipendenze di sviluppo.

## Struttura
- `classes/` servizi applicativi.
- `controllers/front/` controller per upload, anteprima, riepilogo, download sicuro e API.
- `views/` asset e template Smarty.

## Note
Il wizard front-end è da completare con l’UI definitiva. Le classi forniscono i punti d’estensione necessari per integrare le logiche di produzione e invio email.
