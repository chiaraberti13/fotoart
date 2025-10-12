# Analisi modulo Art Puzzle: prezzo personalizzazioni non applicato

## Contesto
Il modulo `art_puzzle` aggiunge un flusso di personalizzazione per alcuni prodotti di PrestaShop 1.7.6.9. Il cliente può caricare un'immagine, scegliere un formato (con prezzo dedicato), selezionare testo, colori e font della scatola. Al termine l'ordine deve riflettere il prezzo derivante dal formato scelto. Attualmente, però, il riepilogo finale del carrello e dell'ordine mostra soltanto il prezzo base del prodotto, ignorando il sovrapprezzo del formato personalizzato.

## Flusso implementato lato frontend
1. Il template `views/templates/hook/displayProductExtraContent.tpl` contiene il configuratore con i quattro step mostrati negli screenshot.
2. I formati disponibili e i relativi prezzi sono **hard-coded** nel template (`data-format="small" data-price="19.99"`, etc.). Le opzioni configurabili nel back office non vengono lette qui.
3. Il JavaScript del template salva la scelta dell'utente in `customizationData.price` e la inoltra via AJAX sia all'azione `saveCustomization` sia all'azione `addToCart` (`controllers/front/ajax.php`).

## Persistenza della personalizzazione
Nel metodo `handleSaveCustomization()` (`controllers/front/ajax.php`):
- I dati ricevuti (incluso `price`) vengono memorizzati nella tabella personalizzata `ps_art_puzzle_customization`.
- La tabella non ha alcun legame nativo con le tabelle PrestaShop dedicate alle personalizzazioni (`customized_data`, `customization`, ecc.).

Nel metodo `handleAddToCart()`:
- Il prodotto viene aggiunto al carrello tramite `Cart::updateQty()` senza alcuna informazione sulla personalizzazione.
- L'unico passo successivo è aggiornare il record su `ps_art_puzzle_customization` con l'`id_cart` corrente.

> **Conseguenza**: il carrello continua ad utilizzare il prezzo base del prodotto perché PrestaShop non riceve alcun `customization` o `specific price` da applicare.

## Mancato adeguamento del prezzo
PrestaShop applica il prezzo finale in base a combinazioni, specific prices, car rules o campi di personalizzazione a pagamento. Nel modulo attuale manca completamente:
- l'inserimento di un `Customization` standard (`Customization::add()` + `CustomizationField` per `Product::CUSTOMIZE_TEXTFIELD`),
- la definizione di un `SpecificPrice` legato alla riga del carrello (`id_cart`, `id_product`, `id_customer`),
- oppure la trasformazione dei formati in vere `ProductAttribute`/`Combination` con prezzi dedicati.

Senza uno di questi meccanismi, `Cart::getProducts()` restituisce sempre il prezzo catalogo (vedi `handleAddToCart()` in `controllers/front/ajax.php` dove non viene mai impostato `custom_price`). Da qui il riepilogo ordine errato.

## Incongruenze back office / front office
- Nel back office, tramite `hookDisplayAdminProductsExtra` (`art_puzzle.php` + `views/templates/admin/product_tab.tpl`), l'utente può definire formati, prezzi, font, colori. Tuttavia tali valori non sono utilizzati sul front end: `displayProductExtraContent.tpl` continua a mostrare l'elenco statico "Small/Medium/Large/XLarge".
- Il gestore `PuzzleFormatManager` prevede la lettura di `Configuration::get('ART_PUZZLE_FORMATS')`, ma nessun punto del front end lo richiama. Il mismatch spiega perché l'utente vede solo i formati di default anche se nel back office ne ha configurati altri.
- Analogamente, i font caricati (campo `ART_PUZZLE_FONTS`) e i colori scatola memorizzati in configurazione non vengono forniti al template: la UI mostra opzioni fisse.

## Requisiti per allinearsi a fotoartpuzzle.it
Per ottenere lo stesso comportamento del sito di riferimento è necessario:
1. **Allineare le opzioni front/back**: generare dinamicamente formati, prezzi, font e colori a partire dai dati salvati in configurazione (es. tramite Smarty assign in `hookDisplayProductExtraContent`).
2. **Integrare la personalizzazione con il carrello PrestaShop** in una delle seguenti modalità:
   - Creare un record di `Customization` associato al prodotto e collegare la personalizzazione salvata (ad esempio salvando l'ID della tabella custom nel campo testo). Successivamente usare l'hook `actionCartSave`/`actionObjectCustomizationAddAfter` per applicare un `SpecificPrice` per quel carrello.
   - Oppure creare combinazioni prodotto per ogni formato con relativo impatto prezzo, e selezionare l'`id_product_attribute` corretto in `updateQty()`.
   - In alternativa, utilizzare `SpecificPrice` al momento dell'aggiunta al carrello (impostando `price` o `reduction`) così che il riepilogo usi il prezzo fornito.
3. **Aggiornare il flusso ordine**: una volta legato l'ID personalizzazione al carrello, occorre gestire l'aggiornamento su conferma ordine (es. popolando `id_order` nella tabella custom e inviando gli allegati corretti).

## Punti di intervento suggeriti
- `Art_Puzzle::hookDisplayProductExtraContent()` deve assegnare a Smarty l'elenco formati/font/colori partendo dalle configurazioni salvate e non usare template statici.
- `Art_Puzzle::hookActionCartSave()` è attualmente vuoto: può essere sfruttato per recuperare la personalizzazione salvata, creare/aggiornare `SpecificPrice` e popolare i dati nella tabella standard delle personalizzazioni.
- `controllers/front/ajax.php::handleAddToCart()` dovrebbe:
  1. Recuperare il formato scelto (magari dalla configurazione back office per ottenere il prezzo reale),
  2. Creare una personalizzazione (`Customization`) e salvare l'ID ritornato,
  3. Applicare un `SpecificPrice` con `price` uguale al prezzo finale desiderato **oppure** selezionare una combinazione con impatto prezzo coerente.
- Aggiornare `views/templates/hook/displayProductExtraContent.tpl` per leggere i dati dinamicamente e per passare l'`id_product_attribute` (se si opta per le combinazioni) o l'ID personalizzazione standard.

## Conclusione
Il motivo per cui il checkout ignora i prezzi dei formati personalizzati è che il modulo non comunica a PrestaShop alcuna variazione di prezzo né usa le API di personalizzazione native. Le opzioni configurate nel back office non vengono esposte al front end, per cui anche l'esperienza utente risulta incoerente. Per replicare il comportamento di fotoartpuzzle.it è indispensabile integrare il flusso con i meccanismi standard di PrestaShop (customization fields, specific prices o combinazioni) e rendere dinamiche le opzioni mostrate al cliente.

## Aggiornamento implementazione
- Le dimensioni, i font e i colori configurati nel tab prodotto del back office vengono ora salvati tramite l'azione AJAX `savePuzzleConfig` e resi disponibili al front end in modo dinamico.
- Il salvataggio della personalizzazione calcola il prezzo del formato lato server (tax incl./excl) e lo memorizza nella tabella `art_puzzle_customization` insieme al font scelto.
- L'aggiunta al carrello genera una personalizzazione PrestaShop, collega l'ID alla riga del carrello e associa i prezzi calcolati.
- Gli hook `actionCartGetProductsAfter` e `actionValidateOrder` allineano rispettivamente il riepilogo carrello e i dettagli d'ordine ai prezzi delle personalizzazioni.
