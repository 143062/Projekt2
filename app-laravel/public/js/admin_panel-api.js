window.AdminAPI = {
    // Pobiera listę użytkowników
    getUsers: function () {
        return fetch('/api/admin/users', {
            headers: Auth.attachAuthHeaders()
        })
        .then(response => {
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
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
        return fetch(`/api/admin/users/${userId}/password`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                ...Auth.attachAuthHeaders(),
            },
            body: JSON.stringify({ password: newPassword }),
        })
        .then(response => {
            if (!response.ok) throw new Error(`Błąd serwera: ${response.status}`);
            return response.json();
        })
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd resetowania hasła:", error);
            return { status: "error", message: error.message };
        });
    },


    
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
