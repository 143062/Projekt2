window.AdminAPI = {
    // 📌 Pobiera listę użytkowników
    // 📌 Pobiera listę użytkowników
    getUsers: function () {
        return fetch('/api/admin/users', {
            headers: Auth.attachAuthHeaders()
        })
        .then(response => {
            const contentType = response.headers.get("content-type");

            // 📌 Jeśli serwer zwraca HTML zamiast JSON (np. 403 lub 401)
            if (!contentType || !contentType.includes("application/json")) {
                console.error("[admin_panel-api.js] Serwer zwrócił niepoprawną odpowiedź (prawdopodobnie 403/401).");
                Auth.logout(); // 🔹 Automatyczne wylogowanie
                return Promise.reject({ message: "Niepoprawna odpowiedź serwera." });
            }

            return response.json().then(jsonData => {
                if (!response.ok) {
                    console.error(`[admin_panel-api.js] Błąd API: ${response.status}`, jsonData);

                    // 📌 Obsługa braku tokena (`401 Unauthorized`) lub braku uprawnień (`403 Forbidden`)
                    if (response.status === 401 || response.status === 403) {
                        console.warn("[admin_panel-api.js] Brak uprawnień lub brak tokena – użytkownik zostanie wylogowany.");
                        Auth.logout(); // 🔹 Automatyczne przekierowanie do /login
                    }

                    return Promise.reject(jsonData);
                }
                return jsonData;
            });
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd pobierania użytkowników:", error);
            return { status: "error", message: error.message };
        });
    },

    // Usuwa użytkownika
    deleteUser: function (userId) {
        return fetch(`/api/admin/users/${userId}`, {
            method: "DELETE",
            headers: Auth.attachAuthHeaders()
        })
        .then(response => {
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd usuwania użytkownika:", error);
            return { status: "error", message: error.message };
        });
    },

    // Dodaje nowego użytkownika
    addUser: function (login, email, password, role) {
        return fetch('/api/admin/users', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                ...Auth.attachAuthHeaders()
            },
            body: JSON.stringify({ login, email, password, role })
        })
        .then(response => {
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd dodawania użytkownika:", error);
            return { status: "error", message: error.message };
        });
    },



    // Importuje bazę danych
    importDatabase: function (formData) {
        return fetch('/api/admin/sql-import', {
            method: "POST",
            headers: Auth.attachAuthHeaders(), // `fetch()` automatycznie ustawia `multipart/form-data`
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");

            if (!response.ok) {
                const errorText = await response.text();
                console.error("[admin_panel-api.js] Błąd serwera:", response.status, errorText);
                throw new Error(`Błąd serwera (${response.status}): ${errorText}`);
            }

            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                throw new Error("Serwer zwrócił niepoprawny format odpowiedzi (nie JSON).");
            }
        })
        .then(data => {
            if (data.status === "success") {
                console.log("[admin_panel-api.js] Import bazy danych zakończony sukcesem:", data);
            } else {
                throw new Error(data.message || "Nieznany błąd podczas importu.");
            }
            return data;
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd importu bazy danych:", error);
            return { status: "error", message: error.message };
        });
    },



    // Resetuje hasło użytkownika
    changeUserPassword: function (userId, newPassword) {
        const apiUrl = `/api/admin/users/${userId}/password`; // 🔹 Upewniamy się, że URL jest poprawny
        console.log("[admin_panel-api.js] Wysyłanie żądania PUT na:", apiUrl);
        console.log("[admin_panel-api.js] Dane wysyłane do API:", { password: newPassword });
    
        return fetch(apiUrl, { 
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                ...Auth.attachAuthHeaders(),
            },
            body: JSON.stringify({ password: newPassword }),
        })
        .then(response => {
            console.log("[admin_panel-api.js] Otrzymano odpowiedź z serwera:", response);
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd resetowania hasła:", error);
            return { status: "error", message: error.message };
        });
    }
    ,


    
    // Uruchamia testy jednostkowe (na przyszłość)
    runTests: function () {
        return fetch('/api/admin/run-tests', {
            method: "POST",
            headers: Auth.attachAuthHeaders()
        })
        .then(response => {
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd uruchamiania testów:", error);
            return { status: "error", message: error.message };
        });
    }
};