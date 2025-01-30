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
        headers: Auth.attachAuthHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(noteData),
        credentials: 'include'
    })
    .then(async response => {
        let jsonData;
        try {
            jsonData = await response.json();
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


function fetchSharedNotesFromAPI() {
    fetch('/api/notes/shared', {
        method: 'GET',
        headers: Auth.attachAuthHeaders(),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(notes => {
        console.log("[dashboard.js] Otrzymano współdzielone notatki:", notes);

        const sharedNotesContainer = document.getElementById('shared-notes');
        sharedNotesContainer.innerHTML = ''; // Czyści kontener przed załadowaniem

        if (!Array.isArray(notes) || notes.length === 0) {
            sharedNotesContainer.innerHTML = '<p>Brak współdzielonych notatek do wyświetlenia.</p>';
            return;
        }

        notes.forEach((note, index) => {
            const noteCard = document.createElement('div');
            noteCard.className = 'note-card shared';
            noteCard.dataset.id = note.id;
            noteCard.dataset.index = index;
            noteCard.innerHTML = `
                <h3>${note.title}</h3>
                <p>${note.content}</p>
                <p class="shared-owner">Udostępnione przez: ${note.owner_login}</p>
            `;
            sharedNotesContainer.appendChild(noteCard);
        });
    })
    .catch(error => {
        console.error('[dashboard.js] Błąd podczas pobierania współdzielonych notatek:', error);
    });
}


window.shareNoteToAPI = function (noteId, selectedFriends) {
    console.log("[dashboard-api.js] Udostępnianie notatki:", { noteId, selectedFriends });

    return fetch(`/api/notes/${noteId}/share`, {
        method: 'POST',
        headers: Auth.attachAuthHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({ shared_with: selectedFriends }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log("[dashboard-api.js] Odpowiedź API dla udostępnienia:", data);
        return data;
    })
    .catch(error => {
        console.error("[dashboard-api.js] Błąd podczas udostępniania notatki:", error);
        return Promise.reject(error);
    });
};


//  Pobiera użytkowników, którym udostępniono notatkę
window.fetchSharedUsersForNote = function (noteId) {
    return fetch(`/api/notes/${noteId}/shared-users`, {
        method: 'GET',
        headers: getAuthHeaders(),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Błąd API: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (!Array.isArray(data)) {
            console.error(`[dashboard-api.js] Błąd: API nie zwróciło poprawnej tablicy użytkowników.`, data);
            return [];
        }
        console.log(`[dashboard-api.js] Użytkownicy z dostępem do notatki ${noteId}:`, data);
        return data;
    })
    .catch(error => {
        console.error(`[dashboard-api.js] Błąd podczas pobierania użytkowników z dostępem do notatki ${noteId}:`, error);
        return [];
    });
};
