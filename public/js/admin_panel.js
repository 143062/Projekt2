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
    const addUserForm = document.querySelector('form[action="/admin/add_user"]');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const roleSelect = document.getElementById('role');
    const runTestsButton = document.getElementById('run-tests-button');
    const testResults = document.getElementById('test-results');

    // Dodaj animację ładowania
    const loader = document.createElement('div');
    loader.classList.add('loader');
    testResults.before(loader); // Dodaj loader przed wynikami testów

    // Funkcja logująca dane do konsoli z oznaczeniem pliku
    function logToConsole(label, data) {
        console.log(`[admin_panel.js] ${label}:`, data);
    }

    // Funkcja do przewijania strony do określonego elementu
    function scrollToElement(element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }

    // Funkcja do wyświetlania statusu importu
    function showImportStatus(message, isSuccess) {
        logToConsole('Wywołano showImportStatus', message);
        importStatus.style.display = 'block';
        importStatus.textContent = message;
        importStatus.style.backgroundColor = isSuccess ? '#4CAF50' : '#ff4d4d';
    }

    // Funkcja do ukrywania statusu
    function hideImportStatus() {
        logToConsole('Ukrywanie statusu importu', null);
        importStatus.style.display = 'none';
    }

    // Funkcja do pobierania aktualnej listy użytkowników i odświeżenia tabeli
    function fetchAndUpdateUserList() {
        fetch('/admin/get_users')
            .then(response => response.json())
            .then(data => {
                logToConsole('Pobrano listę użytkowników', data);
                const userTableBody = document.getElementById('user-list');
                userTableBody.innerHTML = ''; // Wyczyść obecną tabelę

                data.forEach(user => {
                    // Przytnij datę utworzenia, aby usunąć część po kropce
                    const createdAt = user.created_at.split('.')[0]; // Usuwa wszystko po kropce

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.role}</td>
                        <td>${user.login}</td>
                        <td>${user.email}</td>
                        <td>${createdAt}</td>
                        <td>
                            <button type="button" class="reset-password-button button" data-user-id="${user.id}" data-login="${user.login}">Hasło</button>
                            <form method="post" action="/admin/delete_user" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="${user.id}">
                                <button type="submit" class="delete-button button">Usuń</button>
                            </form>
                        </td>
                    `;
                    userTableBody.appendChild(row);
                });

                // Ponownie dodaj event listenery po dynamicznej aktualizacji
                attachDeleteButtonEvents();
                attachPasswordButtonEvents();
            })
            .catch(error => {
                logToConsole('Błąd podczas pobierania listy użytkowników', error);
            });
    }

    // Funkcja do dynamicznego usuwania użytkownika bez potwierdzenia
    function attachDeleteButtonEvents() {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Zablokuj domyślne przesłanie formularza
                const userId = this.parentElement.querySelector('input[name="user_id"]').value;
                logToConsole('Usuwanie użytkownika o ID', userId);

                fetch(`/admin/delete_user`, {
                    method: 'POST',
                    body: new URLSearchParams({
                        user_id: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = this.closest('tr');
                        row.remove();
                        logToConsole(`Użytkownik o ID ${userId} został usunięty.`, data);
                    } else {
                        logToConsole(`Błąd podczas usuwania użytkownika o ID ${userId}`, data.message);
                    }
                })
                .catch(error => {
                    logToConsole('Błąd podczas usuwania użytkownika', error);
                });
            });
        });
    }

    // Obsługa formularza dodawania użytkownika
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(addUserForm);
            logToConsole('Wysłano dane do dodania użytkownika', formData);

            fetch('/admin/add_user', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    logToConsole('Użytkownik został dodany', data);
                    fetchAndUpdateUserList(); // Odśwież listę użytkowników po dodaniu nowego użytkownika
                    usernameInput.value = '';
                    emailInput.value = '';
                    passwordInput.value = '';
                    roleSelect.value = 'user';
                } else {
                    logToConsole('Błąd podczas dodawania użytkownika', data.message);
                }
            })
            .catch(error => {
                logToConsole('Błąd podczas dodawania użytkownika', error);
            });
        });
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

    // Funkcja do obsługi przycisków resetowania hasła
    function attachPasswordButtonEvents() {
        const passwordButtons = document.querySelectorAll('.reset-password-button');
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
    }

    // Logowanie zamykania modala
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        logToConsole('Zamknięto modal resetowania hasła', null);
    });

    // Logowanie kliknięcia w tło modala
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            logToConsole('Kliknięto tło modala, modal został zamknięty', null);
        }
    };

    // Obsługa importu SQL
    if (sqlImportForm) {
        sqlFileInput.addEventListener('change', function() {
            const fileName = sqlFileInput.files[0]?.name || 'Nie wybrano pliku';
            document.querySelector('.file-name').textContent = fileName;
            logToConsole('Wybrano plik do importu', fileName);

            if (sqlFileInput.files.length > 0) {
                hideImportStatus();
            }
        });

        sqlImportForm.addEventListener('submit', function(event) {
            event.preventDefault();

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
                    fetchAndUpdateUserList(); // Odśwież listę użytkowników po imporcie
                } else {
                    logToConsole('Błąd podczas importu SQL', data.details);
                    showImportStatus('Wystąpił błąd podczas importu bazy danych.', false);
                }
            })
            .catch(error => {
                logToConsole('Błąd podczas importu SQL', error);
                showImportStatus('Wystąpił błąd podczas importu bazy danych.', false);
            });
        });
    }

    // Wywołanie funkcji do dynamicznego przypisywania zdarzeń dla przycisków usuwania
    attachDeleteButtonEvents();
    attachPasswordButtonEvents();

    // Uruchomienie testów jednostkowych
    if (runTestsButton) {
        runTestsButton.addEventListener('click', function () {
            logToConsole('Uruchamianie testów jednostkowych', null);
    
            // Ukryj pole wyników i pokaż loader
            testResults.style.display = 'none';
            loader.style.display = 'block';
    
            // Przewiń do loadera
            scrollToElement(loader);
    
            fetch('/admin/run_tests', {
                method: 'POST'
            })
            .then(response => {
                logToConsole('Odpowiedź z serwera na uruchomienie testów', response);
                return response.json(); // Spróbuj sparsować odpowiedź jako JSON
            })
            .then(data => {
                logToConsole('Otrzymane dane JSON', data);
    
                // Tworzenie bardziej czytelnego podsumowania
                const totalTests = data.totalTests || 0;
                const totalAssertions = data.totalAssertions || 0;
                const totalFailures = data.totalFailures || 0;
                const passedTests = totalTests - totalFailures;
    
                let resultSummary = `
                    <h3>Podsumowanie testów:</h3>
                    <p><strong>Łączna liczba testów:</strong> ${totalTests}</p>
                    <p><strong>Łączna liczba asercji:</strong> ${totalAssertions}</p>
                    <p class="text-success"><strong>Testy zakończone sukcesem:</strong> ${passedTests}</p>
                    <p class="text-failure"><strong>Testy zakończone niepowodzeniem:</strong> ${totalFailures}</p>
                `;
    
                // Opcjonalnie, wyświetlenie szczegółów każdego repozytorium
                if (data.results && data.results.length > 0) {
                    let details = '<h4>Szczegóły testów dla poszczególnych repozytoriów:</h4><ul>';
                    data.results.forEach(result => {
                        details += `<li>${result.repository}: ${result.tests} testy, ${result.assertions} asercje, ${result.failures} błędy</li>`;
                    });
                    details += '</ul>';
                    resultSummary += details;
                }
    
                resultSummary += `<h3><a class="raw-link" href="http://localhost:8080/run_tests_raw" target="_blank">RAW</a></h3>`;
    
                // Wyświetlenie podsumowania i szczegółów
                testResults.innerHTML = resultSummary;
    
                // Ukryj loader i pokaż wyniki
                loader.style.display = 'none';
                testResults.style.display = 'block';
                scrollToElement(testResults); // Przewiń do wyników
            })
            .catch(error => {
                logToConsole('Błąd podczas uruchamiania testów', error);
                testResults.textContent = 'Wystąpił błąd podczas uruchamiania testów.';
                loader.style.display = 'none';
                testResults.style.display = 'block';
                scrollToElement(testResults);
            });
        });
    }
});
