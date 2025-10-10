<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'art_puzzle/autoload.php';

function upgrade_module_1_0_1($module)
{
    \ArtPuzzle\ArtPuzzleLogger::log('Inizio aggiornamento alla versione 1.0.1', 'INFO');

    if (!method_exists($module, 'getConfigurationDefaultValues') || !method_exists($module, 'validateConfigurationValue')) {
        \ArtPuzzle\ArtPuzzleLogger::log('Metodi di gestione configurazioni non disponibili durante l\'aggiornamento 1.0.1.', 'ERROR');

        return false;
    }

    $defaults = $module->getConfigurationDefaultValues();

    if (!$module->validateConfigurationValues($defaults)) {
        \ArtPuzzle\ArtPuzzleLogger::log('Valori di default non validi rilevati durante l\'aggiornamento 1.0.1.', 'ERROR');

        return false;
    }

    $success = true;

    foreach ($defaults as $key => $defaultValue) {
        $currentValue = Configuration::get($key);

        if ($currentValue === false || !$module->validateConfigurationValue($key, $currentValue)) {
            $valueToPersist = $defaultValue;
        } else {
            $valueToPersist = $currentValue;
        }

        if (!$module->validateConfigurationValue($key, $valueToPersist)) {
            \ArtPuzzle\ArtPuzzleLogger::log(sprintf('Valore non valido per la chiave %s durante la migrazione 1.0.1.', $key), 'ERROR');
            $success = false;
            continue;
        }

        if ($currentValue === false || $valueToPersist !== $currentValue) {
            $success &= Configuration::updateValue($key, $valueToPersist);
        }
    }

    if ($success) {
        \ArtPuzzle\ArtPuzzleLogger::log('Aggiornamento alla versione 1.0.1 completato con successo.', 'INFO');
    } else {
        \ArtPuzzle\ArtPuzzleLogger::log('Aggiornamento alla versione 1.0.1 completato con errori.', 'ERROR');
    }

    return (bool) $success;
}
