<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repositories\FriendRepository;
use App\Tests\TestHelper;
use PDO;
use PDOException;

require_once __DIR__ . '/../src/repositories/FriendRepository.php';
require_once __DIR__ . '/TestHelper.php';

class FriendRepositoryTest extends TestCase
{
    private static $pdo;
    private static $friendRepository;
    private static $userId;
    private static $friendId;

    public static function setUpBeforeClass(): void
    {
        try {
            // Użycie metody TestHelper::getPdo()
            self::$pdo = TestHelper::getPdo();
            self::$friendRepository = new FriendRepository(self::$pdo);

            // Generowanie UUID
            self::$userId = TestHelper::generateUuid();
            self::$friendId = TestHelper::generateUuid();

            // Dodanie testowych użytkowników
            TestHelper::addTestUserIfNotExists(self::$pdo, self::$userId, 'test_user', 'test_user@example.com');
            TestHelper::addTestUserIfNotExists(self::$pdo, self::$friendId, 'friend_user', 'friend_user@example.com');
        } catch (PDOException $e) {
            self::fail("Nie udało się połączyć z bazą danych: " . $e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Usuwanie testowych użytkowników
        TestHelper::deleteTestUser(self::$pdo, self::$userId);
        TestHelper::deleteTestUser(self::$pdo, self::$friendId);
    }

    public function testAddFriend(): void
    {
        echo "Dodawanie znajomego o ID: " . self::$friendId . " do użytkownika o ID: " . self::$userId . "\n";

        $result = self::$friendRepository->addFriend(self::$userId, self::$friendId);
        $this->assertTrue($result, 'Znajomy powinien zostać dodany poprawnie.');
    }

    public function testAddFriendThatAlreadyExists(): void
    {
        echo "Ponowne dodawanie znajomego o ID: " . self::$friendId . " do użytkownika o ID: " . self::$userId . "\n";

        $result = self::$friendRepository->addFriend(self::$userId, self::$friendId);
        $this->assertFalse($result, 'Znajomy nie powinien zostać dodany drugi raz.');
    }

    public function testIsFriend(): void
    {
        echo "Sprawdzanie czy użytkownik o ID: " . self::$userId . " jest znajomym użytkownika o ID: " . self::$friendId . "\n";

        $isFriend = self::$friendRepository->isFriend(self::$userId, self::$friendId);
        $this->assertTrue($isFriend, 'Użytkownicy powinni być znajomymi.');
    }

    public function testGetFriendsByUserId(): void
    {
        echo "Pobieranie listy znajomych dla użytkownika o ID: " . self::$userId . "\n";

        $friends = self::$friendRepository->getFriendsByUserId(self::$userId);
        $this->assertNotEmpty($friends, 'Użytkownik powinien mieć co najmniej jednego znajomego.');
        echo "Znajomi użytkownika: " . print_r($friends, true) . "\n";
    }

    public function testDeleteFriend(): void
    {
        echo "Usuwanie znajomego o ID: " . self::$friendId . " dla użytkownika o ID: " . self::$userId . "\n";

        $result = self::$friendRepository->deleteFriend(self::$userId, self::$friendId);
        $this->assertTrue($result['rowCount'] > 0, 'Znajomy powinien zostać poprawnie usunięty.');
        echo $result['log'] . "\n";
    }

    public function testDeleteNonExistingFriend(): void
    {
        echo "Próba usunięcia nieistniejącego znajomego o ID: " . self::$friendId . " dla użytkownika o ID: " . self::$userId . "\n";

        $result = self::$friendRepository->deleteFriend(self::$userId, 'non-existent-id');
        $this->assertEquals(0, $result['rowCount'], 'Nie powinno być możliwości usunięcia nieistniejącego znajomego.');
    }

    public function testAddFriendWithInvalidData(): void
    {
        echo "Dodawanie znajomego z nieprawidłowymi danymi.\n";
    
        // Sprawdzenie, czy dodanie znajomego z pustymi danymi zwróci false
        $result = self::$friendRepository->addFriend(null, null);
        $this->assertFalse($result, 'Operacja z nieprawidłowymi danymi powinna zwrócić false.');
    }
    
}
