// 📌 Pobieranie nagłówków autoryzacji (wspiera Sanctum)
function getAuthHeaders() {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    const authToken = Auth.getToken();
    if (authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
    } else {
        console.error("[dashboard-api.js] Brak tokena autoryzacji.");
    }

    return headers;
}

// 📌 Dodawanie lub edycja notatki w API
window.saveNoteToAPI = function (noteData, noteId = null) { 
    const method = noteId ? 'PUT' : 'POST';
    const endpoint = noteId ? `/api/notes/${noteId}` : `/api/notes`;

    return fetch(endpoint, {
        method: method,
        headers: getAuthHeaders(),
        body: JSON.stringify(noteData),
        credentials: 'include' // Wymagane dla Sanctum
    })
    .then(async response => {
        let jsonData;
        try {
            jsonData = await response.json(); // Parsowanie JSON
        } catch (error) {
            console.error("[dashboard-api.js] Niepoprawny format odpowiedzi z API:", error);
            return Promise.reject({ message: "Niepoprawny format odpowiedzi z API." });
        }

        if (!response.ok) {
            console.error(`[dashboard-api.js] Błąd API: ${response.status}`, jsonData);
            return Promise.reject(jsonData);
        }

        return jsonData;
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas zapisywania notatki:', error);
        return Promise.reject(error);
    });
};


// 📌 Pobiera znajomych użytkownika
window.fetchFriendsFromAPI = function () {
    return fetch('/api/friends', { 
        method: 'GET', 
        headers: getAuthHeaders(),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error(`Błąd HTTP! Status: ${response.status}`);
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas ładowania znajomych:', error);
        return Promise.reject(error);
    });
};

// 📌 Pobiera szczegóły jednej notatki
window.fetchNoteFromAPI = function (noteId) {
    console.log(`[dashboard-api.js] Pobieranie notatki o ID: ${noteId}`);

    return fetch(`/api/notes/${noteId}`, { 
        method: 'GET', 
        headers: getAuthHeaders(),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error(`Błąd pobierania notatki: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log(`[dashboard-api.js] Odpowiedź API dla notatki ID ${noteId}:`, data);
        return data;
    })
    .catch(error => {
        console.error(`[dashboard-api.js] Błąd podczas ładowania notatki ${noteId}:`, error);
        return Promise.reject(error);
    });
};

// 📌 Usuwanie notatki
window.deleteNoteFromAPI = function (noteId) {
    return fetch(`/api/notes/${noteId}`, { 
        method: 'DELETE', 
        headers: getAuthHeaders(),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas usuwania notatki:', error);
        return Promise.reject(error);
    });
};

// 📌 Pobiera wszystkie notatki zalogowanego użytkownika
window.fetchUserNotesFromAPI = function () {
    return fetch('/api/notes', { 
        method: 'GET', 
        headers: getAuthHeaders(),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error(`Błąd HTTP! Status: ${response.status}`);
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas pobierania notatek użytkownika:', error);
        return Promise.reject(error);
    });
};
