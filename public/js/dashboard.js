document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-button');
    const noteFormContainer = document.getElementById('note-form-container');
    const addNoteButton = document.getElementById('add-note-button');
    const saveNoteButton = document.getElementById('save-note');
    const addFriendButton = document.getElementById('add-friend');
    const sharedWithContainer = document.getElementById('shared-with');
    const noteTitle = document.getElementById('note-title');
    const noteContent = document.getElementById('note-content');
    const noteModalContainer = document.getElementById('note-modal-container');
    const modalNoteTitle = document.getElementById('modal-note-title');
    const modalNoteContent = document.getElementById('modal-note-content');
    const editNoteButton = document.getElementById('edit-note');
    const modalSharedWithContainer = document.getElementById('modal-shared-with');
    const shareNoteModalContainer = document.getElementById('share-note-modal-container');
    const shareNoteSaveButton = document.getElementById('share-note-save');
    const friendsListContainer = document.getElementById('friends-list');
    const searchInput = document.querySelector('.search-bar input');
    const myNotesContainer = document.getElementById('my-notes');
    let friends = [];
    let editingNoteIndex = -1;

    // Eventy przełączania widoczności notatek
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const icon = this.querySelector('.material-symbols-outlined');
            const sectionId = this.getAttribute('data-section');
            const section = document.getElementById(sectionId);

            if (section.style.display === 'none') {
                section.style.display = 'flex';
                icon.textContent = 'hide';
            } else {
                section.style.display = 'none';
                icon.textContent = 'unfold_more';
            }
        });
    });

    // Event dodawania nowej notatki
    addNoteButton.addEventListener('click', function () {
        editingNoteIndex = -1;
        friends = [];
        updateSharedWith();
        noteTitle.value = '';  // Reset tytułu
        noteContent.value = '';  // Reset treści
        noteFormContainer.style.display = 'flex';
        modalNoteTitle.dataset.id = '';  // Resetowanie ID w przypadku nowej notatki
    });

    // Zapisanie notatki (nowej lub edytowanej)
    saveNoteButton.addEventListener('click', function () {
        const title = noteTitle.value.trim();
        const content = noteContent.value.trim();

        if (title === '' && content === '') {
            console.error('[dashboard.js] Nie można dodać pustej notatki');
            return;
        }

        const noteData = {
            title: title,
            content: content,
            shared_with: friends.map(friend => friend.id)
        };

        const noteId = modalNoteTitle.dataset.id;  // Pobieramy ID notatki
        if (noteId) {
            noteData.id = noteId;  // Jeśli ID istnieje, przypisujemy je
        }

        console.log("[dashboard.js] Wysyłanie danych notatki:", noteData);

        fetch('/add_note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(noteData)
        })
        .then(response => response.text())  // Pobieramy odpowiedź jako tekst, aby zobaczyć surową odpowiedź
        .then(text => {
            console.log("[dashboard.js] Surowa odpowiedź z serwera:", text);

            try {
                const data = JSON.parse(text);
                console.log("[dashboard.js] Otrzymano odpowiedź z serwera:", data);

                if (data.success) {
                    noteFormContainer.style.display = 'none';

                    if (!noteData.id) {  // Nowa notatka
                        const noteIndex = document.querySelectorAll('.note-card').length;
                        const noteCard = document.createElement('div');
                        noteCard.className = 'note-card';
                        noteCard.setAttribute('data-index', noteIndex);
                        noteCard.setAttribute('data-id', data.id);  // Nowo wygenerowane ID

                        noteCard.innerHTML = `
                            <h3>${title}</h3>
                            <p>${content}</p>
                        `;
                        noteCard.addEventListener('click', function () {
                            showNoteModal(noteCard, noteIndex);
                        });

                        myNotesContainer.appendChild(noteCard);

                        const noNotesMessage = document.querySelector('#my-notes p');
                        if (noNotesMessage) {
                            noNotesMessage.style.display = 'none';
                        }
                    } else {  // Edytowana notatka
                        const noteCard = document.querySelector(`.note-card[data-index="${editingNoteIndex}"]`);
                        if (noteCard) {
                            noteCard.querySelector('h3').textContent = title;
                            noteCard.querySelector('p').textContent = content;
                        } else {
                            console.error(`[dashboard.js] Nie znaleziono notatki o indexie: ${editingNoteIndex}`);
                        }
                    }

                    noteTitle.value = '';
                    noteContent.value = '';
                    sharedWithContainer.innerHTML = '';
                    friends = [];
                    editingNoteIndex = -1;
                    modalNoteTitle.dataset.id = '';
                } else {
                    console.error("[dashboard.js] Błąd podczas zapisywania notatki. Szczegóły:", data.message);
                }
            } catch (error) {
                console.error("[dashboard.js] Błąd parsowania JSON:", error, "Surowa odpowiedź:", text);
            }
        })
        .catch(error => {
            console.error('[dashboard.js] Błąd podczas dodawania notatki:', error);
        });
    });

    // Ukrywanie formularza po kliknięciu poza nim
    noteFormContainer.addEventListener('click', function (event) {
        if (event.target === noteFormContainer) {
            noteFormContainer.style.display = 'none';
        }
    });

    // Dodawanie znajomych do udostępniania notatki
    addFriendButton.addEventListener('click', function () {
        fetch('/friends')
            .then(response => response.json())
            .then(data => {
                friendsListContainer.innerHTML = '';

                data.forEach(friend => {
                    const isChecked = friends.some(f => f.id === friend.id) ? 'checked' : '';
                    const friendItem = document.createElement('div');
                    friendItem.className = 'friend-item';
                    friendItem.innerHTML = `
                        <img src="${friend.profile_picture}" alt="${friend.login}">
                        <span>${friend.login}</span>
                        <input type="checkbox" class="friend-checkbox" data-id="${friend.id}" ${isChecked}>
                    `;
                    friendsListContainer.appendChild(friendItem);
                });

                shareNoteModalContainer.style.display = 'flex';
            })
            .catch(error => {
                console.error('Błąd podczas ładowania znajomych:', error);
            });
    });

    // Zapisanie udostępnienia notatki
    shareNoteSaveButton.addEventListener('click', function () {
        const selectedFriends = document.querySelectorAll('.friend-checkbox:checked');

        friends = Array.from(selectedFriends).map(checkbox => {
            const id = checkbox.getAttribute('data-id');
            const friendItem = checkbox.closest('.friend-item');
            return {
                id: id,
                login: friendItem.querySelector('span').textContent,
                profile_picture: friendItem.querySelector('img').src
            };
        });

        updateSharedWith();
        shareNoteModalContainer.style.display = 'none';
    });

    // Ukrywanie modalnego okna udostępniania notatki
    shareNoteModalContainer.addEventListener('click', function (event) {
        if (event.target === shareNoteModalContainer) {
            shareNoteModalContainer.style.display = 'none';
        }
    });

    // Aktualizacja sekcji "Udostępniono dla"
    function updateSharedWith() {
        sharedWithContainer.innerHTML = '';
        friends.forEach(friend => {
            const friendDiv = document.createElement('div');
            friendDiv.className = 'friend';
            friendDiv.innerHTML = `
                <img src="${friend.profile_picture}" alt="${friend.login}" class="friend-icon">
                <span>${friend.login}</span>
                <img src="/public/img/minus.svg" alt="Usuń" class="remove-icon" data-id="${friend.id}">
            `;
            sharedWithContainer.appendChild(friendDiv);
        });

        // Usuwanie znajomych z listy udostępnionych
        document.querySelectorAll('.remove-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                friends = friends.filter(friend => friend.id !== id);
                updateSharedWith();
            });
        });
    }

    // Wyświetlenie modala notatki
    function showNoteModal(noteCard, index) {
        const noteId = noteCard.getAttribute('data-id');
        const isShared = noteCard.getAttribute('data-shared') === 'true';  // Sprawdzenie, czy notatka jest udostępniona

        console.log(`[dashboard.js] Ustawiam editingNoteIndex na: ${index}`);
        editingNoteIndex = index;

        fetch(`/get_note?id=${noteId}`)
            .then(response => {
                return response.text().then(text => {
                    console.log("[dashboard.js] Otrzymano odpowiedź tekstową podczas ładowania notatki:", text);
                    try {
                        return JSON.parse(text);
                    } catch (error) {
                        console.error("[dashboard.js] Błąd parsowania odpowiedzi JSON", error);
                        throw error;
                    }
                });
            })
            .then(data => {
                console.log("[dashboard.js] Ładowanie notatki do edycji:", data);
                modalNoteTitle.textContent = data.note.title;
                modalNoteTitle.dataset.id = noteId;  // Ustawienie ID notatki
                modalNoteContent.textContent = data.note.content;

                // Resetowanie listy znajomych
                friends = data.sharedUsers || [];
                updateSharedWith();

                if (isShared) {
                    editNoteButton.style.display = 'none';  // Ukrycie przycisku edycji dla udostępnionych notatek
                } else {
                    editNoteButton.style.display = 'inline-block';  // Pokaż przycisk edycji dla własnych notatek
                }

                noteModalContainer.style.display = 'flex';
            })
            .catch(error => {
                console.error('[dashboard.js] Błąd podczas ładowania notatki:', error);
            });
    }

    // Ukrywanie modala notatki
    noteModalContainer.addEventListener('click', function (event) {
        if (event.target === noteModalContainer) {
            noteModalContainer.style.display = 'none';
        }
    });

    // Edytowanie notatki
    editNoteButton.addEventListener('click', function () {
        noteTitle.value = modalNoteTitle.textContent;
        noteContent.value = modalNoteContent.textContent;
        updateSharedWith();
        noteModalContainer.style.display = 'none';
        noteFormContainer.style.display = 'flex';
    });

    // Funkcja wyszukiwania notatek
    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.toLowerCase();
        document.querySelectorAll('.note-card').forEach(noteCard => {
            const title = noteCard.querySelector('h3').textContent.toLowerCase();
            const content = noteCard.querySelector('p').textContent.toLowerCase();
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                noteCard.style.display = 'block';
            } else {
                noteCard.style.display = 'none';
            }
        });
    });

    // Obsługa kliknięcia w notatki
    document.querySelectorAll('.note-card').forEach((noteCard, index) => {
        noteCard.addEventListener('click', function () {
            showNoteModal(noteCard, index);
        });
    });
});
