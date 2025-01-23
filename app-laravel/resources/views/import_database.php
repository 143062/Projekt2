<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Bazy Danych</title>
    <link rel="stylesheet" href="/css/common.css">
    <style>
        body {
            margin: 10px !important;
            padding: 10px !important;
        }
    </style>
</head>
<body>
    <h1>Importuj najnowszą bazę danych</h1>
    <form action="/import_database" method="post">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <button type="submit">Importuj najnowszy dump</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ścieżka do katalogu z dumpami
        $dumpDir = base_path('database/dumps');

        if (!is_dir($dumpDir)) {
            echo "Katalog z dumpami nie istnieje: $dumpDir";
            exit;
        }

        $files = glob($dumpDir . '/*.sql');

        if (empty($files)) {
            echo "Brak plików .sql w katalogu $dumpDir";
            exit;
        }

        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];

        $dbHost = env('DB_HOST', 'localhost');
        $dbName = env('DB_DATABASE', 'notatki_db');
        $dbUser = env('DB_USERNAME', 'user');
        $dbPassword = env('DB_PASSWORD', 'password');

        $command = "PGPASSWORD='$dbPassword' pg_restore --clean -h $dbHost -U $dbUser -d $dbName -v $latestFile";

        exec($command . ' 2>&1', $output, $returnVar);

        echo "<h2>Logi:</h2>";
        echo "<pre>";
        echo "Wykonana komenda: $command\n";
        echo "Kod zwrócony przez pg_restore: $returnVar\n";
        echo "Wyjście:\n";
        print_r($output);
        echo "</pre>";

        if ($returnVar === 0) {
            echo "Baza danych została pomyślnie przywrócona z pliku: $latestFile";
        } else {
            echo "Wystąpił błąd podczas przywracania bazy danych.";
        }
    }
    ?>
</body>
</html>
