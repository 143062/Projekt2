// Dodaje lub edytuje notatkę w API
window.saveNoteToAPI = function (noteData, noteId = null) { 
    const method = noteId ? 'PUT' : 'POST';
    const endpoint = noteId ? `/api/notes/${noteId}` : `/api/notes`; 

    return fetch(endpoint, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify(noteData)
    }).then(response => response.json().then(data => ({ status: response.status, body: data })));
};

// Pobiera znajomych użytkownika
window.fetchFriendsFromAPI = function () {
    const authToken = localStorage.getItem('auth_token');

    return fetch('/api/friends', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Błąd HTTP! Status: ${response.status}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas ładowania znajomych:', error);
        return Promise.reject(error); 
    });
};

// Pobiera szczegóły jednej notatki
window.fetchNoteFromAPI = function (noteId) {
    const authToken = localStorage.getItem('auth_token');

    return fetch(`/api/notes/${noteId}`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Błąd pobierania notatki: ${response.status}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas ładowania notatki:', error);
        alert('Nie udało się pobrać notatki.');
    });
};

// Usuwa notatkę
window.deleteNoteFromAPI = function (noteId) {
    const authToken = localStorage.getItem('auth_token');
    if (!authToken) {
        console.error('[dashboard-api.js] Brak tokena uwierzytelniającego');
        alert('Nie można wykonać żądania: brak tokena autoryzacji.');
        return Promise.reject('Brak tokena');
    }

    return fetch(`/api/notes/${noteId}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${authToken}` }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[dashboard-api.js] Notatka usunięta:', data);
        return Promise.resolve(data); 
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas usuwania notatki:', error.message);
        alert('Nie udało się usunąć notatki.');
        return Promise.reject(error); 
    });
};

// Pobiera wszystkie notatki zalogowanego użytkownika
window.fetchUserNotesFromAPI = function () {
    const authToken = localStorage.getItem('auth_token');
    if (!authToken) {
        console.error('[dashboard-api.js] Brak tokena autoryzacyjnego.');
        alert('Nie można pobrać notatek: brak autoryzacji.');
        return Promise.reject('Brak tokena');
    }

    return fetch('/api/notes', { // API do pobierania notatek
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Błąd HTTP! Status: ${response.status}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error('[dashboard-api.js] Błąd podczas pobierania notatek użytkownika:', error);
        return Promise.reject(error);
    });
};
