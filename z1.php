<?php
$db_name = 'wordpress';
$backup_dir = "backup/$db_name";

if (!is_dir($backup_dir)) {
    if (!@mkdir($backup_dir)) {
        die ("<p>Ne možemo stvoriti direktorij$backup_dir.</p></body></html>");
    }
}

$time = time();

$dbc = @mysqli_connect('localhost', 'root', '', $db_name) or
    die ("<p>Ne možemo se spojiti na bazu $db_name.</p></body></html>");

$r = mysqli_query($dbc, 'SHOW TABLES');
if (mysqli_num_rows($r) > 0) {
    echo "<p>Backup za bazu podataka '$db_name'.</p>";
    while (
            list($table) = mysqli_fetch_array(
            $r,
            MYSQLI_NUM
        )
    ) {
        //dohvaćanje naziva stupaca tablice
        $column_names_query = "SELECT COLUMN_NAME 
          FROM INFORMATION_SCHEMA.COLUMNS 
          WHERE TABLE_NAME = '$table'";

        $column_names = mysqli_query($dbc, $column_names_query);

        //dohvaćanje podataka iz tablice
        $values_query = "SELECT * FROM $table";
        $values = mysqli_query($dbc, $values_query);

        //stvaranje "INSERT INTO naziv_tablice (" naredbe
        $insert_statement = buildInsertStatement($table, $column_names);

        //stvaranje backupa za pojedinu tablicu
        writeTableBackup($table, $values, $insert_statement, $backup_dir, $time);
    }
} else {
    echo "<p>Baza $db_name ne sadrži tablice.</p>";
}

function buildInsertStatement($table, $column_names)
{
    $insert_statement = "INSERT INTO $table (";

    if (mysqli_num_rows($column_names) > 0) {
        while ($row = mysqli_fetch_array($column_names, MYSQLI_NUM)) {
            $insert_statement .= $row[0] . ", ";
        }
        $insert_statement = rtrim($insert_statement, ', ') . ')';
        $insert_statement .= "\nVALUES (";
    }

    return $insert_statement;
}

function writeTableBackup($table, $values, $insert_statement, $backup_dir, $time)
{
    if (mysqli_num_rows($values) > 0) {
        if ($fp = gzopen("$backup_dir/{$table}_{$time}.txt.gz", 'w9')) {
            while ($row = mysqli_fetch_array($values, MYSQLI_NUM)) {
                $count = count($row);
                $current_index = 0;
                gzwrite($fp, $insert_statement);
                foreach ($row as $value) {
                    $value = addslashes($value);
                    gzwrite($fp, "'$value'");

                    if (++$current_index < $count) {
                        gzwrite($fp, ", ");
                    }
                }
                gzwrite($fp, ");\n");
            }
            gzclose($fp);
            echo "<p>Tablica '$table' je pohranjena.</p>";
        } else {
            echo "<p>Datoteka $backup_dir/{$table}_{$time}.txt.gz se ne može otvoriti.</p>";
            return false;
        }
    }
    return true;
}
?>