<?php


class ArtPuzzleAjaxErrorHandler
{
    /** @var callable|null */
    protected static $errorCallback;

    /**
     * Registra un gestore globale per errori PHP, eccezioni e fatal error.
     *
     * @param callable $onError Callback invocata con l'eccezione generata.
     */
    public static function register(callable $onError)
    {
        self::$errorCallback = $onError;

        set_error_handler([self::class, 'handlePhpError']);
        set_exception_handler([self::class, 'handleThrowable']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Esegue una callback assicurandosi che eventuali errori PHP o eccezioni
     * vengano convertiti in una risposta JSON tramite il callback $onError.
     *
     * @param callable $callback  Logica principale da eseguire.
     * @param callable $onError   Callback invocata in caso di errore con l'eccezione.
     *
     * @return mixed
     */
    public static function run(callable $callback, callable $onError)
    {
        set_error_handler([self::class, 'handlePhpError']);

        try {
            return $callback();
        } catch (\Throwable $throwable) {
            self::logThrowable($throwable);
            $onError($throwable);
        } finally {
            restore_error_handler();
        }

        return null;
    }

    /**
     * Converte gli errori PHP in eccezioni per permettere una gestione unificata.
     */
    public static function handlePhpError($severity, $message, $file = '', $line = 0)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Gestisce eccezioni non intercettate.
     */
    public static function handleThrowable(\Throwable $throwable)
    {
        self::logThrowable($throwable);

        if (self::$errorCallback) {
            call_user_func(self::$errorCallback, $throwable);
        }

        return true;
    }

    /**
     * Converte fatal error in eccezioni gestite.
     */
    public static function handleShutdown()
    {
        $error = error_get_last();

        if (!$error) {
            return;
        }

        $fatalErrors = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
        ];

        if (in_array($error['type'], $fatalErrors, true)) {
            $throwable = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            self::handleThrowable($throwable);
        }
    }

    /**
     * Registra l'errore sul logger del modulo se disponibile.
     */
    protected static function logThrowable(\Throwable $throwable)
    {
        $loggerPath = _PS_MODULE_DIR_ . 'art_puzzle/classes/ArtPuzzleLogger.php';

        if (!class_exists('ArtPuzzleLogger') && file_exists($loggerPath)) {
            require_once $loggerPath;
        }

        if (class_exists('ArtPuzzleLogger')) {
            $context = sprintf('%s:%d', $throwable->getFile(), $throwable->getLine());
            $message = sprintf('[AJAX ERROR] %s - %s', $throwable->getMessage(), $context);
            ArtPuzzleLogger::log($message, 'ERROR');
        }
    }
}

