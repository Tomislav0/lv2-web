<?php
$encryptedDir = "uploads/";

$encryptedFiles = glob($encryptedDir . "encrypted_*");

foreach ($encryptedFiles as $encryptedFilePath) {

    $fileContent = file_get_contents($encryptedFilePath);

    $encryptionKey = md5('encriptionKey');

    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($fileContent, 0, $ivLength);
    $encripted_content = substr($fileContent, $ivLength);

    $decryptedContent = openssl_decrypt($encripted_content, 'aes-256-cbc', $encryptionKey, 0, $iv);

    $decryptedFilePath = "uploads/decrypted_" . substr(basename($encryptedFilePath), 10); // Remove "encrypted_" prefix and adjust the directory

    file_put_contents($decryptedFilePath, $decryptedContent);

    echo "File decrypted successfully: <a href='$decryptedFilePath'>$decryptedFilePath</a><br>";
}
?>