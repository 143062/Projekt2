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
    const sqlImportForm = document.getElementById("sql-import-form");
    const sqlFileInput = document.getElementById('sql-file');
    const addUserForm = document.getElementById("add-user-form");
    const usernameInput = document.getElementById("username");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const roleSelect = document.getElementById("role");
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



/////////////////////////// ^^^^ TO ZOSTALO STOCKOWE ^^^^ ///////////////////







    // Funkcja do pobierania aktualnej listy użytkowników i odświeżenia tabeli
    document.addEventListener("DOMContentLoaded", function () {
        fetchAndUpdateUserList();
    });
    
    // Funkcja do pobierania listy użytkowników i odświeżania tabeli
    window.fetchAndUpdateUserList = function () {
        AdminAPI.getUsers().then(data => {
            if (!data || !data.data) {
                console.error('[admin_panel.js] Błąd: Brak danych użytkowników');
                return;
            }
    
            console.log('[admin_panel.js] Pobrano listę użytkowników:', data);
            const userTableBody = document.getElementById('user-list');
            userTableBody.innerHTML = ''; // Wyczyść obecną tabelę
    
            data.data.forEach(user => {
                const createdAt = user.created_at ? user.created_at.split('.')[0] : 'Brak danych';
    
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.role}</td>
                    <td>${user.login}</td>
                    <td>${user.email}</td>
                    <td>${createdAt}</td>
                    <td>
                        <button type="button" class="reset-password-button button" data-user-id="${user.id}" data-login="${user.login}">Hasło</button>
                        <button type="button" class="delete-button button" data-user-id="${user.id}">Usuń</button>
                    </td>
                `;
                userTableBody.appendChild(row);
            });
    
            // Ponownie dodaj event listenery po dynamicznej aktualizacji
            attachDeleteButtonEvents();
            attachPasswordButtonEvents();
        }).catch(error => console.error('[admin_panel.js] Błąd pobierania użytkowników:', error));
    };
    
    
    // Usuwa użytkownika
    window.deleteUser = function (userId) {
        AdminAPI.deleteUser(userId).then(() => {
            alert("Użytkownik usunięty!");
            fetchAndUpdateUserList(); // Odśwież tabelę użytkowników
        });
    }
    







// Funkcja do dynamicznego usuwania użytkownika bez potwierdzenia
window.attachDeleteButtonEvents = function () {
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const userId = this.dataset.userId; // Pobranie userId z `data-user-id`

            if (!userId) {
                console.error('[admin_panel.js] Błąd: Nie znaleziono `data-user-id` w przycisku usuwania.');
                return;
            }

            logToConsole('Usuwanie użytkownika o ID', userId);

            AdminAPI.deleteUser(userId).then(data => {
                if (data && data.status === 'success') {
                    const row = this.closest('tr');
                    if (row) row.remove();
                    logToConsole(`Użytkownik o ID ${userId} został usunięty.`, data);
                } else {
                    logToConsole(`Błąd podczas usuwania użytkownika o ID ${userId}`, data ? data.message : "Nieznany błąd");
                }
            }).catch(error => {
                logToConsole('Błąd podczas usuwania użytkownika', error.message);
                alert(`Nie udało się usunąć użytkownika: ${error.message}`);
            });
        });
    });
};






    

// Obsługa formularza dodawania użytkownika

if (addUserForm) {
    addUserForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const login = usernameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const role = roleSelect.value;
        const submitButton = addUserForm.querySelector("button[type='submit']");

        if (!login || !email || !password) {
            alert("Proszę wypełnić wszystkie pola przed dodaniem użytkownika.");
            return;
        }

        logToConsole("Wysłano dane do dodania użytkownika", { login, email, role });

        // Zablokowanie przycisku, aby użytkownik nie mógł kliknąć wielokrotnie
        submitButton.disabled = true;

        AdminAPI.addUser(login, email, password, role)
            .then(data => {
                if (data && data.status === "success") {
                    logToConsole("Użytkownik został dodany", data);
                    fetchAndUpdateUserList(); // Odśwież listę użytkowników po dodaniu nowego użytkownika
                    usernameInput.value = "";
                    emailInput.value = "";
                    passwordInput.value = "";
                    roleSelect.value = "user";
                    alert("Użytkownik został pomyślnie dodany!");
                } else {
                    logToConsole("Błąd podczas dodawania użytkownika", data ? data.message : "Nieznany błąd");
                    alert("Błąd podczas dodawania użytkownika: " + (data ? data.message : "Nieznany błąd"));
                }
            })
            .catch(error => {
                logToConsole("Błąd podczas dodawania użytkownika", error);
                alert("Wystąpił błąd podczas dodawania użytkownika. Spróbuj ponownie.");
            })
            .finally(() => {
                // Odblokowanie przycisku po zakończeniu operacji
                submitButton.disabled = false;
            });
    });
}



///////////////////////////////////////////////////////////////////

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

/////////////////////////// TO ZOSTALO STOCKOWE ///////////////////


///////////////////////////////////////////////////////////////////

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


///////// TO niby tez stockowe ale cos mi tu nie pasuje, bo skad ten data-user-id i data-login? chyba, ze to jest z tabelki, to wtedy git ///////////////////



///////////////////////////////////////////////////////////////////

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


/////////////////////////// TO ZOSTALO STOCKOWE ///////////////////











// Obsługa pobierania SQL Dump
const sqlDumpButton = document.getElementById("sql-dump-button");
if (sqlDumpButton) {
    sqlDumpButton.addEventListener("click", function () {
        fetch('/api/admin/sql-dump', {
            method: "GET",
            headers: Auth.attachAuthHeaders(),
        })
        .then(response => {
            const filename = response.headers.get('Content-Disposition')
                ?.split('filename=')[1]
                ?.replace(/["']/g, '') || "backup.sql"; // Pobiera nazwę pliku z API
        
            return response.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = filename; 
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => console.error("[admin_panel.js] Błąd pobierania SQL Dump:", error));
        
    });
}

// Obsługa importu SQL

if (sqlImportForm) {
    sqlImportForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const sqlFileInput = document.getElementById("sql-file");
        if (!sqlFileInput.files.length) {
            alert("Proszę wybrać plik przed importem.");
            return;
        }

        const formData = new FormData();
        formData.append("sql_file", sqlFileInput.files[0]);

        const importButton = document.getElementById("sql-import-button");
        importButton.disabled = true;

        AdminAPI.importDatabase(sqlFileInput.files[0]).then(data => {
            if (data && data.status === "success") {
                showImportStatus("Baza danych została pomyślnie przywrócona!", true);
            } else {
                showImportStatus("Błąd podczas importu bazy danych.", false);
            }
        }).catch(error => {
            console.error("[admin_panel.js] Błąd importu bazy danych:", error);
            showImportStatus("Błąd podczas importu bazy danych.", false);
        }).finally(() => {
            importButton.disabled = false;
        });
    });
}



    // Wywołanie funkcji do dynamicznego przypisywania zdarzeń dla przycisków usuwania
    attachDeleteButtonEvents();
    attachPasswordButtonEvents();










//////////////////////////////////// LEGACY

////////////////////////////////////// TEGO I TAK RACZEJ NIE BEDE IMPLEMENTOWAL ALE SIE FAJNIE KRECI PRZYNAJMNIEJ, WIEC MA ZOSTAC



    // Uruchomienie testów jednostkowych
    if (runTestsButton) {
        runTestsButton.addEventListener('click', function () {
            logToConsole('Uruchamianie testów jednostkowych', null);
    
            // Ukryj pole wyników i pokaż loader
            testResults.style.display = 'none';
            loader.style.display = 'block';
    
            // Przewiń do loadera
            scrollToElement(loader);
    
            AdminAPI.runTests().then(data => {
                if (!data) {
                    testResults.textContent = 'Błąd: brak odpowiedzi z serwera.';
                    return;
                }
    
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
    
                // Opcjonalnie, wyświetlenie szczegółów testów
                if (data.results && data.results.length > 0) {
                    let details = '<h4>Szczegóły testów:</h4><ul>';
                    data.results.forEach(result => {
                        details += `<li>${result.repository}: ${result.tests} testy, ${result.assertions} asercje, ${result.failures} błędy</li>`;
                    });
                    details += '</ul>';
                    resultSummary += details;
                }
    
                resultSummary += `<h3><a class="raw-link" href="http://localhost:8080/run_tests_raw" target="_blank">RAW</a></h3>`;
    
                // Wyświetlenie wyników
                testResults.innerHTML = resultSummary;
                loader.style.display = 'none';
                testResults.style.display = 'block';
                scrollToElement(testResults); // Przewiń do wyników
            }).catch(error => {
                logToConsole('Błąd podczas uruchamiania testów', error);
                testResults.textContent = 'Wystąpił błąd podczas uruchamiania testów.';
                loader.style.display = 'none';
                testResults.style.display = 'block';
                scrollToElement(testResults);
            });
        });
    }
    


// Obsługa uruchamiania testów jednostkowych


if (runTestsButton) {
    runTestsButton.addEventListener("click", function () {
        logToConsole("Uruchamianie testów jednostkowych", null);

        testResults.textContent = "Trwa uruchamianie testów...";

        AdminAPI.runTests().then(data => {
            if (!data) {
                testResults.textContent = "Błąd: brak odpowiedzi z serwera.";
                return;
            }

            logToConsole("Otrzymane dane testów", data);

            // Tworzenie bardziej czytelnego podsumowania
            const totalTests = data.totalTests || 0;
            const totalAssertions = data.totalAssertions || 0;
            const totalFailures = data.totalFailures || 0;
            const passedTests = totalTests - totalFailures;

            let resultSummary = `
                ✅ Łączna liczba testów: ${totalTests}
                ✅ Łączna liczba asercji: ${totalAssertions}
                ✅ Testy zakończone sukcesem: ${passedTests}
                ❌ Testy zakończone niepowodzeniem: ${totalFailures}
            `;

            // Opcjonalnie, wyświetlenie szczegółów testów
            if (data.results && data.results.length > 0) {
                resultSummary += `\n🔍 Szczegóły testów:\n`;
                data.results.forEach(result => {
                    resultSummary += `📌 ${result.repository}: ${result.tests} testy, ${result.assertions} asercje, ${result.failures} błędy\n`;
                });
            }

            testResults.textContent = resultSummary;
        }).catch(error => {
            logToConsole("Błąd podczas uruchamiania testów", error);
            testResults.textContent = "Wystąpił błąd podczas uruchamiania testów.";
        });
    });
}







});
