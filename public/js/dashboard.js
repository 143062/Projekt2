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
    const searchInput = document.querySelector('.search-bar input');
    const myNotesContainer = document.getElementById('my-notes');
    const maxFriends = 3; // Maksymalna liczba wyświetlanych znajomych
    let friends = [];
    let friendCounter = 1; // Licznik znajomych
    let editingNoteIndex = -1; // Indeks edytowanej notatki

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

    addNoteButton.addEventListener('click', function () {
        editingNoteIndex = -1; // Resetowanie indeksu edytowanej notatki
        friends = [];
        updateSharedWith();
        noteFormContainer.style.display = 'flex';
    });

    saveNoteButton.addEventListener('click', function () {
        const title = noteTitle.value.trim();
        const content = noteContent.value.trim();

        if (title === '' && content === '') {
            alert('Nie można dodać pustej notatki');
            return;
        }

        const noteData = {
            title: title,
            content: content
        };

        if (editingNoteIndex !== -1) {
            noteData.id = modalNoteTitle.dataset.id; // Używamy dataset.id
        }

        console.log("Editing note index:", editingNoteIndex);
        console.log("Note ID:", noteData.id);
        console.log("Sending note data:", noteData);

        fetch('/add_note', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(noteData)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Received response:", data);

            // Po zapisaniu notatki zamykamy okno formularza
            noteFormContainer.style.display = 'none';

            if (editingNoteIndex === -1) {
                const noteIndex = document.querySelectorAll('.note-card').length;
                const noteCard = document.createElement('div');
                noteCard.className = 'note-card';
                noteCard.setAttribute('data-index', noteIndex);
                noteCard.setAttribute('data-id', data.id);
                noteCard.innerHTML = `
                    <h3>${title}</h3>
                    <p>${content}</p>
                `;
                noteCard.addEventListener('click', function () {
                    showNoteModal(noteCard, noteIndex);
                });

                myNotesContainer.appendChild(noteCard);
            } else {
                const noteCard = document.querySelector(`.note-card[data-index="${editingNoteIndex}"]`);
                noteCard.querySelector('h3').textContent = title;
                noteCard.querySelector('p').textContent = content;
            }

            // Czyścimy pola formularza
            noteTitle.value = '';
            noteContent.value = '';
            sharedWithContainer.innerHTML = '';
            friends = [];
        })
        .catch(error => {
            console.error('Błąd podczas dodawania notatki:', error);
        });
    });

    noteFormContainer.addEventListener('click', function (event) {
        if (event.target === noteFormContainer) {
            noteFormContainer.style.display = 'none';
        }
    });

    addFriendButton.addEventListener('click', function () {
        const newFriend = {
            id: friendCounter++,
            name: `Znajomy ${friendCounter - 1}`,
            img: '/public/img/profile.jpg'
        };

        if (friends.length >= maxFriends) {
            friends.shift();
        }
        friends.push(newFriend);

        updateSharedWith();
    });

    function updateSharedWith() {
        sharedWithContainer.innerHTML = '';
        friends.forEach(friend => {
            const friendDiv = document.createElement('div');
            friendDiv.className = 'friend';
            friendDiv.innerHTML = `
                <img src="${friend.img}" alt="${friend.name}" class="friend-icon">
                <span>${friend.name}</span>
                <img src="/public/img/minus.svg" alt="Usuń" class="remove-icon" data-id="${friend.id}">
            `;
            sharedWithContainer.appendChild(friendDiv);
        });

        document.querySelectorAll('.remove-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                const id = parseInt(this.getAttribute('data-id'));
                friends = friends.filter(friend => friend.id !== id);
                updateSharedWith();
            });
        });
    }

    function showNoteModal(noteCard, index) {
        const noteId = noteCard.getAttribute('data-id');
        const note = {
            title: noteCard.querySelector('h3').textContent,
            content: noteCard.querySelector('p').textContent,
            friends: []
        };

        console.log("Showing modal for note index:", index);
        console.log("Note ID in modal:", noteId);

        modalNoteTitle.textContent = note.title;
        modalNoteTitle.dataset.id = noteId;  // Dodanie przypisania ID do dataset
        modalNoteContent.textContent = note.content;
        friends = [...note.friends];
        updateSharedWithModal();
        editingNoteIndex = index;
        noteModalContainer.style.display = 'flex';
    }

    function updateSharedWithModal() {
        modalSharedWithContainer.innerHTML = '';
        friends.forEach(friend => {
            const friendDiv = document.createElement('div');
            friendDiv.className = 'friend';
            friendDiv.innerHTML = `
                <img src="${friend.img}" alt="${friend.name}" class="friend-icon">
                <span>${friend.name}</span>
            `;
            modalSharedWithContainer.appendChild(friendDiv);
        });
    }

    noteModalContainer.addEventListener('click', function (event) {
        if (event.target === noteModalContainer) {
            noteModalContainer.style.display = 'none';
        }
    });

    editNoteButton.addEventListener('click', function () {
        noteTitle.value = modalNoteTitle.textContent;
        noteContent.value = modalNoteContent.textContent;
        updateSharedWith();
        noteModalContainer.style.display = 'none';
        noteFormContainer.style.display = 'flex';
    });

    // Nasłuchiwanie na wpisywanie w pole wyszukiwania
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

    // Dodaj nasłuchiwanie na istniejące notatki
    document.querySelectorAll('.note-card').forEach((noteCard, index) => {
        noteCard.addEventListener('click', function () {
            showNoteModal(noteCard, index);
        });
    });
});
