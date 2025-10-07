# Piano migrazione filesystem FotoArt Puzzle

Per allineare il nuovo modulo ai percorsi condivisi è necessario spostare gradualmente gli asset generati nelle cartelle `modules/fotoartpuzzle/var/tmp` (bozze) e `modules/fotoartpuzzle/var/orders` (ordini consolidati).

## Step consigliati
1. **Freeze caricamenti**: schedulare un breve periodo di sola lettura in cui i nuovi upload vengono messi in coda.
2. **Backup completo**: archiviare le cartelle legacy `modules/fotoartpuzzle/uploads`, `boxes/` e `puzzle/` su storage esterno.
3. **Copia incrementale**: utilizzare `rsync` o `robocopy` per trasferire i file nelle nuove destinazioni mantenendo permessi e timestamp.
4. **Aggiornamento configurazione**: impostare i nuovi path in `FAPPathBuilder` e verificare che il cron di pulizia (`FAPCleanupService`) punti a `var/tmp`.
5. **Verifica ordini in corso**: assicurarsi che gli ordini aperti referenzino i file migrati aggiornando le entry nella tabella personalizzazioni.
6. **Pulizia residui**: conservare le cartelle legacy in sola lettura per 30 giorni; trascorso il periodo, eliminare o archiviare definitivamente.

## Scheduler
Configurare un job cron che esegua `php modules/fotoartpuzzle/cron/cleanup.php` almeno ogni 6 ore per spostare gli asset orfani e ripulire i temporanei.

Documentare ogni migrazione nel registro operativo per tracciare eventuali rollback.
