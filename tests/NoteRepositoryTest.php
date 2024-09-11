<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repositories\NoteRepository;
use App\Tests\TestHelper;
use PDO;
use PDOException;

require_once __DIR__ . '/../src/repositories/NoteRepository.php';
require_once __DIR__ . '/TestHelper.php';

class NoteRepositoryTest extends TestCase
{
    private static $pdo;
    private static $noteRepository;
    private static $userId;
    private static $friendId; // Do testowania udostępniania notatek
    private static $noteId;   // Przechowywanie ID testowej notatki

    public static function setUpBeforeClass(): void
    {
        try {
            // Pobieranie połączenia z bazą danych
            self::$pdo = TestHelper::getPdo();
            self::$noteRepository = new NoteRepository(self::$pdo);

            // Generowanie UUID dla testowego użytkownika i znajomego
            self::$userId = TestHelper::generateUuid();
            self::$friendId = TestHelper::generateUuid();

            // Dodanie testowego użytkownika i znajomego, jeśli nie istnieją
            TestHelper::addTestUserIfNotExists(self::$pdo, self::$userId, 'test_user', 'test_user@example.com');
            TestHelper::addTestUserIfNotExists(self::$pdo, self::$friendId, 'friend_user', 'friend_user@example.com');
        } catch (PDOException $e) {
            self::fail("Nie udało się połączyć z bazą danych: " . $e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Usuwanie testowego użytkownika
        TestHelper::deleteTestUser(self::$pdo, self::$userId);
        TestHelper::deleteTestUser(self::$pdo, self::$friendId);
        echo "Użytkownicy zostali usunięci z bazy danych.\n";
    }

    public function testAddNote(): void
    {
        $title = 'Test Title';
        $content = 'Test Content';

        // Dodawanie notatki dla użytkownika
        $result = self::$noteRepository->saveNote(self::$userId, null, $title, $content);
        self::$noteId = $result;

        $this->assertNotFalse($result, 'Notatka powinna zostać poprawnie dodana.');
    }

    public function testGetNotesByUserId(): void
    {
        // Pobieranie notatek użytkownika
        $notes = self::$noteRepository->getNotesByUserId(self::$userId);
        $this->assertIsArray($notes, 'Notatki powinny być zwrócone w formie tablicy.');
        $this->assertNotEmpty($notes, 'Powinna być co najmniej jedna notatka.');
    }

    public function testShareNoteWithUser(): void
    {
        // Udostępnienie notatki znajomemu
        self::$noteRepository->shareNoteWithUser(self::$noteId, self::$friendId);

        // Sprawdzenie, czy notatka została udostępniona
        $sharedUsers = self::$noteRepository->getSharedUsersByNoteId(self::$noteId);
        $this->assertNotEmpty($sharedUsers, 'Notatka powinna być udostępniona przynajmniej jednemu użytkownikowi.');
    }

    public function testGetSharedNotesWithUser(): void
    {
        // Pobieranie udostępnionych notatek dla znajomego
        $sharedNotes = self::$noteRepository->getSharedNotesWithUser(self::$friendId);
        $this->assertNotEmpty($sharedNotes, 'Znajomy powinien mieć co najmniej jedną udostępnioną notatkę.');
    }

    public function testGetNoteById(): void
    {
        // Pobranie notatki po ID
        $note = self::$noteRepository->getNoteById(self::$noteId, self::$userId);
        $this->assertNotNull($note, 'Notatka powinna zostać zwrócona.');
    }

    public function testDeleteNoteById(): void
    {
        // Usuwanie notatki
        $result = self::$noteRepository->deleteNoteById(self::$noteId, self::$userId);
        $this->assertTrue($result, 'Notatka powinna zostać usunięta.');
    }

    public function testClearSharedNotes(): void
    {
        // Dodanie nowej notatki, aby przetestować usuwanie powiązań
        $noteId = self::$noteRepository->saveNote(self::$userId, null, 'Shared Title', 'Shared Content');
        self::$noteRepository->shareNoteWithUser($noteId, self::$friendId);

        // Usuwanie powiązań z tabeli shared_notes
        self::$noteRepository->clearSharedNotes($noteId);

        // Sprawdzenie, czy nie ma więcej powiązań
        $sharedUsers = self::$noteRepository->getSharedUsersByNoteId($noteId);
        $this->assertEmpty($sharedUsers, 'Powiązania udostępnienia notatki powinny zostać usunięte.');
    }
}
