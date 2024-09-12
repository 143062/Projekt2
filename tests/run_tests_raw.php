<?php
// Rozpoczęcie sesji
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany oraz czy ma uprawnienia administratora
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header('Location: /login'); // Przekierowanie na stronę logowania, jeśli użytkownik nie jest zalogowany
    exit();
}

// Sprawdzenie, czy użytkownik ma rolę administratora
require_once __DIR__ . '/../src/repositories/UserRepository.php';

$userRepository = new \App\Repositories\UserRepository();
$stmt = $userRepository->getPdo()->prepare('SELECT id FROM Roles WHERE name = :role_name');
$stmt->execute(['role_name' => 'admin']);
$adminRoleId = $stmt->fetchColumn();

if ($_SESSION['role_id'] !== $adminRoleId) {
    echo "Brak uprawnień do uruchamiania testów.";
    exit();
}

// Ścieżka do PHPUnit
$phpunit = '/usr/local/bin/phpunit';

// Ścieżka do folderu z testami
$testDirectory = __DIR__ . ''; // Zmieniona ścieżka do folderu tests

// Przełącznik dla bardziej szczegółowych logów PHPUnit
$verbose = '-v'; // To ustawi PHPUnit na bardziej szczegółowe logowanie

// Tablica z nazwami repozytoriów i kontrolerów oraz odpowiadającymi im plikami testów
$testFiles = [
    'UserRepository' => '/UserRepositoryTest.php',
    'NoteRepository' => '/NoteRepositoryTest.php',
    'FriendRepository' => '/FriendRepositoryTest.php'
];

// Inicjalizacja zmiennych do przechowywania sumarycznych wyników testów
$totalTests = 0;
$totalAssertions = 0;
$totalFailures = 0;
$totalErrors = 0;

echo <<<HTML
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podsumowanie Testów</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <style>
        body {
            margin: 10px !important;
            padding: 10px !important;
        }
    </style>
</head>
<body>
<h1>Podsumowanie testów</h1>
HTML;

// Zbieranie wyników
$results = [];

foreach ($testFiles as $testName => $testFile) {
    // Sekcja informująca, jakie testy są aktualnie uruchamiane
    echo "<h2>Testowanie: $testName</h2>";

    // Ścieżka do pliku testów
    $testFilePath = $testDirectory . $testFile; // Zmiana ścieżki do folderu tests

    // Uruchom PHPUnit dla każdego pliku testów z wyłączeniem cache wyników
    $output = [];
    $return_var = 0;
    exec("$phpunit --no-configuration --testdox --do-not-cache-result $verbose $testFilePath 2>&1", $output, $return_var);

    // Zbieranie statystyk z wyniku testu
    $outputString = implode("\n", $output);
    
    // Wyciąganie podsumowania z logów (liczba testów, asercji, błędów)
    preg_match('/OK \((\d+) tests?, (\d+) assertions?\)/', $outputString, $matches);
    if ($matches) {
        $tests = (int) $matches[1];
        $assertions = (int) $matches[2];
        $failures = 0;
    } else {
        preg_match('/FAILURES!\nTests: (\d+), Assertions: (\d+), Failures: (\d+), Errors: (\d+)/', $outputString, $failMatch);
        if ($failMatch) {
            $tests = (int) $failMatch[1];
            $assertions = (int) $failMatch[2];
            $failures = (int) $failMatch[3] + (int) $failMatch[4];
        } else {
            $tests = 0;
            $assertions = 0;
            $failures = 0;
        }
    }

    // Aktualizacja sumarycznych wyników testów
    $totalTests += $tests;
    $totalAssertions += $assertions;
    $totalFailures += $failures;

    // Wyświetl wyniki dla danego repozytorium
    echo "<h3>Wyniki testów jednostkowych dla $testName:</h3>";
    echo "<p>Testy: $tests, Asercje: $assertions, Błędy: $failures</p>";

    if ($failures > 0) {
        echo "<h4 style='color: red;'>Testy dla $testName zakończone błędami</h4>";
    } else {
        echo "<h4 style='color: green;'>Testy dla $testName przeszły pomyślnie!</h4>";
    }

    // Wyświetlenie bardziej szczegółowego logu, jeśli potrzeba
    echo "<details><summary>Pokaż szczegółowe logi</summary><pre>";
    echo htmlspecialchars($outputString);
    echo "</pre></details>";

    echo "<hr style='width:100%; border:1px solid #000;'>";

    // Usunięcie użytkowników testowych
    try {
        $pdo = new PDO('pgsql:host=db;port=5432;dbname=notatki_db', 'user', 'password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE login LIKE :login");
        $stmt->execute(['login' => 'test%']);

        echo "<p>Wszyscy testowi użytkownicy zostali poprawnie usunięci po teście: $testName.</p>";

    } catch (PDOException $e) {
        echo "<p>Błąd podczas połączenia z bazą danych: " . $e->getMessage() . "</p>";
    }
}

// Wyświetlenie podsumowania wszystkich testów
echo "<h2>Podsumowanie wszystkich testów</h2>";
echo "<p>Liczba testów: $totalTests</p>";
echo "<p>Liczba asercji: $totalAssertions</p>";
echo "<p>Liczba błędów: $totalFailures</p>";

// Wyświetlanie dodatkowych informacji o środowisku
echo "<h2>Informacje o środowisku</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

// Zakończenie dokumentu HTML
echo <<<HTML
</body>
</html>
HTML;
