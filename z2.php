<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset ($_FILES["file"])) {
    $uploadDir = "uploads/";
    $fileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $allowedExtensions = array("pdf", "jpeg", "jpg", "png");

    if (in_array($fileExtension, $allowedExtensions)) {
        $fileName = uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            $encryptionKey = md5('encriptionKey');
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encryptedFilePath = $uploadDir . 'encrypted_' . $fileName;
            if ($data = openssl_encrypt(file_get_contents($filePath), 'aes-256-cbc', $encryptionKey, 0, $iv)) {
                file_put_contents($encryptedFilePath, $iv . $data);
                unlink($filePath);

                echo "File uploaded and encrypted successfully.";
            } else {
                echo "Encryption failed.";
            }
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "Invalid file format. Allowed formats: PDF, JPEG, PNG.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Encryption</title>
</head>

<body>
    <h2>Upload File</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".pdf, .jpeg, .jpg, .png" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>