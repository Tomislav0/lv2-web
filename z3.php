<?php
// Load XML from a file
$xml = simplexml_load_file('lv2.xml');

// Check if XML is loaded successfully
if ($xml === false) {
    echo "Failed to load XML file.";
    exit;
}

foreach ($xml->record as $record) {
    $id = (string) $record->id;
    $ime = (string) $record->ime;
    $prezime = (string) $record->prezime;
    $email = (string) $record->email;
    $spol = (string) $record->spol;
    $slika = (string) $record->slika;
    $zivotopis = (string) $record->zivotopis;

    echo "<img src=\"$slika\"><br>";
    echo "ID: $id<br>";
    echo "Ime: $ime<br>";
    echo "Prezime: $prezime<br>";
    echo "Email: $email<br>";
    echo "Spol: $spol<br>";
    echo "Životopis: $zivotopis<br><br>";
}
?>

<img>