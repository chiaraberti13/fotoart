<?php
/**
 * smtp_test.php — Test invio email con SwiftMailer (PHP 5.3)
 * Percorso libreria: assets/classes/swiftmailer/swift_required.php
 * Prova 465/SSL; stampa log dettagliato.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// === SwiftMailer (percorso ESATTO indicato) ===
require_once dirname(__FILE__) . '/assets/classes/swiftmailer/swift_required.php';

// === CREDENZIALI TEST (Gmail App Password SENZA spazi) ===
$SMTP_HOST = 'smtp.gmail.com';
$SMTP_PORT = 465;          // 465 = SSL
$SMTP_SCHEME = 'ssl';
$SMTP_USER = 'mondopuzzle@gmail.com';
$SMTP_PASS = 'lmolrtywguujmgju'; // <- senza spazi!

$FROM_EMAIL = 'mondopuzzle@gmail.com';
$FROM_NAME  = 'MondoPuzzle (Test)';
$TO_EMAIL   = 'mondopuzzle@gmail.com';

// --- funzione invio con log ---
function send_test($host, $port, $scheme, $user, $pass, $fromEmail, $fromName, $toEmail, &$outLog, &$err) {
    $outLog = '';
    $err = '';
    try {
        $transport = Swift_SmtpTransport::newInstance($host, (int)$port, $scheme)
            ->setUsername($user)
            ->setPassword($pass);

        // Se in ambiente legacy vedi "certificate verify failed", sblocca SOLO per test:
        /*
        if (method_exists($transport, 'setStreamOptions')) {
            $transport->setStreamOptions(array(
                'ssl' => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                )
            ));
        }
        */

        $mailer = Swift_Mailer::newInstance($transport);

        // Logger per vedere dialogo SMTP
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

        $message = Swift_Message::newInstance('Test SMTP SwiftMailer ('.$port.'/'.$scheme.')')
            ->setFrom(array($fromEmail => $fromName))
            ->setTo(array($toEmail))
            ->setBody("Ciao! Test SMTP via SwiftMailer su porta ".$port." (".$scheme.").");

        $sent = $mailer->send($message);
        $outLog = $logger->dump();

        if ($sent) return true;
        $err = 'send() ha restituito 0 (nessun destinatario accettato).';
        return false;
    } catch (Exception $e) {
        $err = $e->getMessage();
        return false;
    }
}

// --- Esecuzione test 465/SSL ---
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "Host: $SMTP_HOST\nUser: $SMTP_USER\n\n";

$log = '';
$err = '';
$ok  = send_test($SMTP_HOST, $SMTP_PORT, $SMTP_SCHEME, $SMTP_USER, $SMTP_PASS, $FROM_EMAIL, $FROM_NAME, $TO_EMAIL, $log, $err);

if ($ok) {
    echo "OK: email inviata\n\n";
} else {
    echo "ERRORE: $err\n\n";
}

echo "--- LOG SMTP ---\n";
echo nl2br(htmlspecialchars($log));
echo "\n";
