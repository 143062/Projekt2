<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Bazy Danych</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <style>
        body {
            margin: 10px !important;
            padding: 10px !important;
        }
    </style>
</head>
<body>
    <h1>Importuj najnowszą bazę danych</h1>
    <form action="import_database.php" method="post">
        <button type="submit">Importuj najnowszy dump</button>
    </form>
</body>
</html>

<?php
$dump_dir = __DIR__ . '/dumps/';

if (!is_dir($dump_dir)) {
    echo "Katalog z dumpami nie istnieje: $dump_dir";
    exit;
}

$files = glob($dump_dir . '*.sql');

if (empty($files)) {
    echo "Brak plików .sql w katalogu $dump_dir";
    exit;
}

usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latest_file = $files[0];

$db_host = 'db';
$db_name = 'notatki_db';
$db_user = 'user';
$db_password = 'password';

$command = "PGPASSWORD='$db_password' pg_restore --clean -h $db_host -U $db_user -d $db_name -v $latest_file";

$output = [];
$return_var = null;
exec($command . ' 2>&1', $output, $return_var);

echo "<h2>Logi:</h2>";
echo "<pre>";
echo "Wykonana komenda: $command\n";
echo "Kod zwrócony przez pg_restore: $return_var\n";
echo "Wyjście:\n";
print_r($output);
echo "</pre>";

if ($return_var === 0) {
    echo "Baza danych została pomyślnie przywrócona z pliku: $latest_file";
} else {
    echo "Wystąpił błąd podczas przywracania bazy danych.";
}
?>
