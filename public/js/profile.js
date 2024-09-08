document.getElementById('profile-button').addEventListener('click', function () {
    document.getElementById('profile-form-modal').style.display = 'flex';
});

document.getElementById('file-input').addEventListener('change', function () {
    const fileInput = document.getElementById('file-input');
    const fileName = document.getElementById('file-name');
    const file = fileInput.files[0];

    if (file) {
        fileName.textContent = `Wybrany plik: ${file.name}`;
    } else {
        fileName.textContent = '';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const friendsModal = document.getElementById('manage-friends-modal');
    const friendsBtn = document.getElementById('friends-button');
    const closeFriendsModal = document.querySelector('.manage-friends-modal .back-button');
    const closeProfileFormModal = document.getElementById('close-profile-form-modal');
    const message = document.createElement('p');
    message.style.display = 'none';
    document.getElementById('add-friend-form').appendChild(message);

    const errorMessage = document.getElementById('friend-error-message'); // Referencja do miejsca na błąd

    // Funkcja do walidacji pustego pola znajomego
    function validateFriendLogin() {
        const friendLogin = document.getElementById('friend-login').value.trim();
        
        if (!friendLogin) {
            errorMessage.textContent = 'Proszę wpisać login znajomego';
            errorMessage.style.display = 'block'; // Pokaż wiadomość o błędzie
            return false; // Zatrzymaj wysyłanie formularza, jeśli pole jest puste
        }
        errorMessage.style.display = 'none'; // Ukryj wiadomość, jeśli pole nie jest puste
        return true;
    }

    friendsBtn.onclick = function() {
        friendsModal.style.display = 'flex';
        console.log('Otwieranie modala znajomych');
        loadFriends(); // Ładuj znajomych przy otwieraniu modal
    };

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
        console.log('Zamykanie modala znajomych');
        resetMessage(); // Resetowanie wiadomości przy zamknięciu modala
        errorMessage.style.display = 'none'; // Dodane: Resetowanie wiadomości o błędzie przy zamknięciu modala
    };

    closeProfileFormModal.onclick = function() {
        document.getElementById('profile-form-modal').style.display = 'none';
    };

    document.getElementById('add-friend-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Zatrzymaj domyślną akcję wysyłania formularza
        if (!validateFriendLogin()) {
            return;
        }

        const friendLogin = document.getElementById('friend-login').value.trim();
        console.log(`Próba dodania znajomego: ${friendLogin}`);

        fetch('/add-friend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ friend_login: friendLogin })
        })
        .then(response => {
            console.log('Odpowiedź serwera (tekst):');
            return response.text(); // Zwróć odpowiedź jako tekst
        })
        .then(text => {
            console.log('Odpowiedź serwera (pełna treść):', text);
            try {
                const data = JSON.parse(text); // Spróbuj sparsować odpowiedź do JSON
                console.log('Dane z serwera (JSON):', data);
                
                if (data.success) {
                    const listItem = document.createElement('li');
                    listItem.textContent = friendLogin;
                    document.getElementById('friends-list').appendChild(listItem);
                    document.getElementById('friend-login').value = '';
                    message.textContent = 'Znajomy został dodany pomyślnie';
                    message.style.color = 'green';
                } else {
                    message.textContent = data.message;
                    message.style.color = 'red';
                }
                message.style.display = 'block';
            } catch (error) {
                console.error('Błąd podczas parsowania JSON:', error);
                message.textContent = 'Wystąpił problem z dodaniem znajomego. Spróbuj ponownie';
                message.style.color = 'red';
                message.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania znajomego:', error);
            message.textContent = 'Wystąpił problem z dodaniem znajomego. Spróbuj ponownie';
            message.style.color = 'red';
            message.style.display = 'block';
        });
    });

    // Funkcja, która zamyka okna, gdy klikniesz poza nimi
    window.onclick = function(event) {
        if (event.target == friendsModal) {
            friendsModal.style.display = 'none';
            resetMessage();
            errorMessage.style.display = 'none'; // Dodane: Resetowanie wiadomości o błędzie przy zamknięciu modala
        }
        if (event.target == document.getElementById('profile-form-modal')) {
            document.getElementById('profile-form-modal').style.display = 'none';
        }
    };

    function loadFriends() {
        console.log('Ładowanie znajomych');
        fetch('/friends')
            .then(response => response.json())
            .then(data => {
                console.log('Dane znajomych z serwera:', data);
                const friendsList = document.getElementById('friends-list');
                friendsList.innerHTML = ''; // Czyść listę przed załadowaniem
                data.forEach(friend => {
                    const listItem = document.createElement('li');
                    listItem.textContent = friend.login;
                    friendsList.appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('Błąd podczas ładowania znajomych:', error);
            });
    }

    function resetMessage() {
        message.style.display = 'none';
        message.textContent = '';
    }
});
