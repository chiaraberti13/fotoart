<?php
/******************************************************************
* Test Sistema Gmail Centralizzato
* Testa il funzionamento del sistema email
******************************************************************/

require_once "gmail_mailer_central.php";

echo "<html><head><title>Test Gmail System</title></head><body>";
echo "<h1>Test Sistema Gmail</h1>";

if (isset($_POST['test'])) {
    $result = kmail_gmail_test($_POST['email']);
    echo "<div style='padding:20px; margin:20px; border:2px solid " . ($result['success'] ? 'green' : 'red') . "';'>";
    echo "<h2>" . ($result['success'] ? '✅ Successo!' : '❌ Errore!') . "</h2>";
    echo "<p>" . $result['message'] . "</p>";
    echo "<p><strong>Email inviata a:</strong> " . $result['email'] . "</p>";
    echo "</div>";
}

echo "<form method='post'>";
echo "<h3>Testa invio email:</h3>";
echo "<input type='email' name='email' placeholder='Email di test' value='mondopuzzle@gmail.com' required style='padding:10px; width:300px;'><br><br>";
echo "<button type='submit' name='test' value='1' style='padding:10px 20px; background:blue; color:white; border:none;'>Invia Email di Test</button>";
echo "</form>";
echo "</body></html>";
?>