# FotoArt Puzzle – dipendenze applicative

Questa sezione riepiloga le librerie e le estensioni richieste per sviluppare, testare e distribuire il configuratore puzzle.

## PHP
- **GD** o **Imagick**: generazione delle anteprime, creazione dei crop e rendering delle scatole.
- **mbstring**, **json**, **zip**: gestiscono rispettivamente caratteri multibyte, serializzazione dei payload AJAX e creazione pacchetti di produzione.
- **openssl**: firma dei token download e cifratura temporanei.

## Strumenti CLI
- `ghostscript` e `imagemagick`: necessari per comporre i PDF e generare gli overlay dei template legacy.
- `zip`/`unzip`: utilizzati dalla dashboard produzione per impacchettare asset multipli.

## Librerie JavaScript
- **Cropper.js** ≥ 1.5: controllo del ritaglio interattivo lato front-end.
- **GSAP** (facoltativa): animazioni del wizard.
- **jQuery** (incluso da PrestaShop) e **Growl**: gestione UI lato BO.

## Font e asset
- I font caricati vengono copiati in `modules/fotoartpuzzle/fonts/` e devono essere in formato `.ttf` o `.otf`.
- I template grafici delle scatole (PSD/PNG) risiedono in `modules/fotoartpuzzle/var/templates/boxes/`.

Verificare che tutti i binari siano disponibili sia in ambienti di sviluppo che in produzione e che il cron di sistema disponga dei permessi per accedere alle nuove cartelle `var/tmp` e `var/orders`.
