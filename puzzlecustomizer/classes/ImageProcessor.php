<?php
/**
 * Gestione elaborazione immagini caricati dagli utenti.
 */

class PuzzleImageProcessorException extends Exception
{
}

class ImageProcessor
{
    /**
     * Valida il file caricato.
     *
     * @throws PuzzleImageProcessorException
     */
    public function validateUpload(array $file, array $allowedFormats)
    {
        if (!isset($file['tmp_name']) || !is_file($file['tmp_name'])) {
            throw new PuzzleImageProcessorException('File non valido.');
        }

        $mime = mime_content_type($file['tmp_name']);
        $size = (int) $file['size'];

        foreach ($allowedFormats as $format) {
            $mimes = array_map('trim', explode(',', $format['mime_types']));
            $extensions = array_map('trim', explode(',', $format['extensions']));

            if ($format['active'] && in_array($mime, $mimes, true)) {
                if ($format['max_size'] > 0 && $size > ((int) $format['max_size'] * 1024 * 1024)) {
                    throw new PuzzleImageProcessorException('Il file supera la dimensione massima consentita.');
                }

                $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, $extensions, true)) {
                    throw new PuzzleImageProcessorException('Estensione non supportata.');
                }

                return true;
            }
        }

        throw new PuzzleImageProcessorException('Formato non supportato.');
    }

    /**
     * Copia il file nella cartella finale.
     */
    public function moveToCustomizationDirectory($tmpPath, $destination)
    {
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!@rename($tmpPath, $destination)) {
            throw new PuzzleImageProcessorException('Impossibile salvare il file.');
        }
    }
}
