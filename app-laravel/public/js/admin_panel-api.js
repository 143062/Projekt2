window.AdminAPI = {
    // Pobiera listę użytkowników
    getUsers: function () {
        return fetch('/api/admin/users', {
            headers: Auth.attachAuthHeaders()
        })
        .then(response => response.json())
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd pobierania użytkowników:", error);
        });
    },

    // Usuwa użytkownika
    deleteUser: function (userId) {
        return fetch(`/api/admin/users/${userId}`, {
            method: "DELETE",
            headers: Auth.attachAuthHeaders()
        })
        .then(response => response.json())
        .catch(error => console.error("[admin_panel-api.js] Błąd usuwania użytkownika:", error));
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
        .then(response => response.json())
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd dodawania użytkownika:", error);
        });
    },

    // Importuje bazę danych
    importDatabase: function (file) {
        const formData = new FormData();
        formData.append("sql_file", file);

        return fetch('/api/admin/sql-import', {
            method: "POST",
            headers: Auth.attachAuthHeaders(), // Upewniamy się, że jest nagłówek autoryzacji
            body: formData
        })
        .then(response => response.json())
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd importu bazy danych:", error);
        });
    },

    // Uruchamia testy jednostkowe (na przyszłość)
    runTests: function () {
        return fetch('/api/admin/run-tests', {
            method: "POST",
            headers: Auth.attachAuthHeaders()
        })
        .then(response => response.json())
        .catch(error => {
            console.error("[admin_panel-api.js] Błąd uruchamiania testów:", error);
        });
    }
};
