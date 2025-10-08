# FAQ

## Il modulo `fotoartpuzzle` è stato sviluppato per WordPress?

No. Il file principale [`fotoartpuzzle.php`](../fotoartpuzzle.php) definisce la classe `FotoArtPuzzle` che estende la classe `Module` di PrestaShop, registra gli hook core (`displayHeader`, `displayProductButtons`, `actionObjectOrderAddAfter`, ecc.) e invoca servizi del namespace del modulo (`FAPConfiguration`, `FAPSessionService`, `FAPSecurityTokenService`...). Inoltre protegge l’esecuzione verificando la costante `_PS_VERSION_`, come previsto dai moduli PrestaShop, e carica le dipendenze dalla cartella `classes/` del modulo. Non sono presenti chiamate a funzioni tipiche di WordPress come `add_action()` o `wp_enqueue_script()`.

## Perché compare il controllo `if (!defined('_PS_VERSION_')) { exit; }`?

È il guard standard raccomandato da PrestaShop per impedire che il file venga eseguito direttamente fuori dal contesto del CMS. Quando il modulo è caricato da PrestaShop, la costante `_PS_VERSION_` è definita, quindi il codice prosegue normalmente senza terminare.

## Come viene inizializzata la classe del modulo?

L’istanza viene creata dal kernel di PrestaShop quando il modulo viene installato o caricato. Nel costruttore `__construct()` vengono impostati `name`, `tab`, `version`, `author` e `ps_versions_compliancy`, prima di richiamare `parent::__construct()`. Durante `install()` il modulo registra gli hook necessari, crea le tabelle e inizializza la configurazione mediante i servizi dedicati.

