document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const passwordButtons = document.querySelectorAll('.reset-password-button');
    const modal = document.getElementById('adminPasswordResetModal');
    const closeModal = document.querySelector('.close');
    const modalUsername = document.getElementById('modal-username');
    const userIdInput = document.getElementById('user-id');
    const searchInput = document.getElementById('user-search');
    const userTable = document.getElementById('user-list');
    const importStatus = document.getElementById('import-status');
    const sqlImportForm = document.getElementById('sql-import-form');
    const sqlFileInput = document.getElementById('sql-file');

    // Funkcja logująca dane do konsoli z oznaczeniem pliku
    function logToConsole(label, data) {
        console.log(`[admin_panel.js] ${label}:`, data);
    }

    // Funkcja do wyświetlania statusu importu
    function showImportStatus(message, isSuccess) {
        console.log("Wywołano showImportStatus"); // Log do weryfikacji wywołania funkcji
        importStatus.style.display = 'block'; // Ustawienie widoczności elementu
        importStatus.textContent = message;
        importStatus.style.backgroundColor = isSuccess ? '#4CAF50' : '#ff4d4d'; // Zmiana koloru
    }

    // Funkcja do ukrywania statusu
    function hideImportStatus() {
        importStatus.style.display = 'none'; // Ukrywanie powiadomienia
    }

    // Logowanie wyszukiwania użytkowników
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase();
        logToConsole('Wprowadzono wyszukiwanie', query);
        const rows = userTable.querySelectorAll('tr');
        rows.forEach(row => {
            const userId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const username = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            if (userId.includes(query) || username.includes(query) || email.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Logowanie przycisków usuwania użytkownika
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            logToConsole('Kliknięto przycisk usuwania dla użytkownika', userId);
            if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                fetch(`/delete_user.php?id=${userId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    logToConsole('Otrzymano odpowiedź serwera dla usuwania', data);
                    if (data === 'success') {
                        this.parentElement.parentElement.remove();
                    } else {
                        console.error('Błąd podczas usuwania użytkownika.');
                    }
                });
            }
        });
    });

    // Logowanie otwierania modala do resetowania hasła
    passwordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const login = this.getAttribute('data-login');
            modalUsername.textContent = login;
            userIdInput.value = userId;
            modal.style.display = 'flex';
            logToConsole(`Kliknięto przycisk resetowania hasła dla użytkownika o ID: ${userId}`, `Login: ${login}`);
        });
    });

    // Logowanie zamykania modala
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        logToConsole('Zamknięto modal resetowania hasła');
    });

    // Logowanie kliknięcia w tło modala
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            logToConsole('Kliknięto tło modala, modal został zamknięty');
        }
    };

    // Obsługa importu SQL - sprawdzenie, czy formularz istnieje
    if (sqlImportForm) {
        // Aktualizacja wyświetlania nazwy pliku po jego wyborze
        sqlFileInput.addEventListener('change', function() {
            const fileName = sqlFileInput.files[0]?.name || 'Nie wybrano pliku';
            document.querySelector('.file-name').textContent = fileName;
            logToConsole('Wybrano plik do importu', fileName);

            // Jeśli wybrano plik, ukryj status (powiadomienie)
            if (sqlFileInput.files.length > 0) {
                hideImportStatus();
            }
        });

        // Logowanie przesyłania pliku i odpowiedzi serwera
        sqlImportForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Sprawdzenie, czy wybrano plik
            if (!sqlFileInput.files.length) {
                logToConsole('Nie wybrano pliku SQL', null);
                showImportStatus('Proszę wybrać plik przed kliknięciem "Importuj SQL".', false);
                return;
            }

            const formData = new FormData(sqlImportForm);
            logToConsole('Wysłano plik SQL do importu', formData);

            fetch('/admin/sql_import', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    logToConsole('Import SQL zakończony sukcesem', data.logs);
                    showImportStatus('Baza danych została pomyślnie przywrócona!', true);
                } else {
                    logToConsole('Błąd podczas importu SQL', data.details);
                    showImportStatus('Wystąpił błąd podczas importu bazy danych.', false);
                }
            })
            .catch(error => {
                logToConsole('Wystąpił błąd podczas importu SQL', error);
                showImportStatus('Wystąpił błąd podczas importu bazy danych.', false);
            });
        });
    } else {
        logToConsole('Brak formularza SQL Import na stronie', null);
    }
});
