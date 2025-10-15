# art-puzzle
PrestaShop Module for Custom Puzzle Creation This module allows users to upload their own image, choose a puzzle format, customize the box with text and colors, preview the result, and complete the order. It includes personalized PDF generation for both customers and administrators, session handling, and full compatibility with PrestaShop 1.7.6.9


ART PUZZLE MODULE – CUSTOM PUZZLE PERSONALIZATION FOR PRESTASHOP
-----------------------------------------------------------

Version: 1.0.0
Author: Chiara Berti 13
Compatibility: PrestaShop 1.7.6.9 | PHP 7.3.33

DESCRIPTION (ENG)
-----------------
The Art Puzzle module allows users to create personalized puzzles directly from your PrestaShop shop.

Main features:
• Image upload by the user
• Puzzle format selection (pieces, size, orientation)
• Image quality evaluation
• Box customization with colors and text
• Realistic preview generation
• Integration with cart and checkout
• Automatic PDF summary sent to both user and administrator
• Full session-based customization flow

Installation:
1. Compress the "art_puzzle" folder into a .zip file.
2. Install the module via the PrestaShop back-office.
3. Configure the module from the “Modules” section in the admin panel.

ℹ️ Make sure the "upload/" and "logs/" folders have write permissions.



# art-puzzle  
Modulo PrestaShop per la creazione di puzzle personalizzati  
Questo modulo consente agli utenti di caricare una propria immagine, scegliere il formato del puzzle, personalizzare la scatola con testo e colori, visualizzare un’anteprima e completare l’ordine.  
Include la generazione di PDF personalizzati per cliente e amministratore, gestione della sessione e piena compatibilità con PrestaShop 1.7.6.9.


Modulo Art Puzzle – Personalizzazione puzzle per PrestaShop
-----------------------------------------------------------

Versione: 1.0.0
Autore: Chiara Berti
Compatibilità: PrestaShop 1.7.6.9 | PHP 7.3.33

Descrizione (ITA)
-----------------
Il modulo Art Puzzle consente agli utenti di creare puzzle personalizzati direttamente dal tuo shop PrestaShop.

Funzionalità principali:
- Caricamento immagine da parte dell’utente
- Selezione formato puzzle (pezzi, dimensioni, orientamento)
- Valutazione qualità immagine
- Personalizzazione della scatola con colori e testo
- Generazione di anteprime realistiche
- Integrazione con carrello e checkout
- Invio automatico di PDF riepilogativi all’utente e all’amministratore
- Gestione completa tramite sessione

Installazione:
1. Comprimi la cartella "art_puzzle" in un file .zip.
2. Installa il modulo tramite il back-office di PrestaShop.
3. Configura il modulo dalla voce "Moduli" nel pannello amministrativo.

ℹ️ Assicurati che le cartelle "upload/" e "logs/" abbiano permessi di scrittura.

📌 **Nota sul logo del modulo**

Per rispettare il vincolo del repository che non consente il versionamento di file binari, l'icona del modulo (`logo.png`) non è
inclusa in questa sorgente. Prima di impacchettare il modulo per l'installazione in PrestaShop, aggiungi manualmente un file PNG
quadrato da 200×200 px nella cartella principale del modulo (`puzzlecustomizer/logo.png`).


Compatibilità PHP 7.3
---------------------
Per garantire che il modulo resti compatibile con PHP 7.3, è disponibile uno script di verifica automatica:

```
composer run check-php73
```

Il comando segnala l'eventuale presenza di `create_function()`, `each()` o di costanti definite come case-insensitive.
