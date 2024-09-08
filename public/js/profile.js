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
        console.log('Otwieranie modala znajomych');
        friendsModal.style.display = 'flex';
        loadFriends(); // Ładuj znajomych przy otwieraniu modal
    };

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
        console.log('Zamykanie modala znajomych');
        resetMessage(); // Resetowanie wiadomości przy zamknięciu modala
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

                    // Dodaj przycisk "Usuń"
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Usuń';
                    deleteButton.classList.add('delete-friend-button');
                    deleteButton.onclick = function() {
                        removeFriend(friendLogin);
                    };

                    listItem.appendChild(deleteButton);
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

    // Funkcja usuwania znajomego
    function removeFriend(friendLogin) {
        console.log(`Próba usunięcia znajomego: ${friendLogin}`);
        fetch('/remove-friend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ friend_login: friendLogin })
        })
        .then(response => {
            // Wyświetl pełną odpowiedź, zanim spróbujesz ją sparsować jako JSON
            console.log('Pełna odpowiedź z serwera:', response);
            return response.text(); // Pobierz odpowiedź jako tekst
        })
        .then(text => {
            console.log('Odpowiedź serwera (pełna treść):', text);
            try {
                const data = JSON.parse(text); // Spróbuj sparsować odpowiedź do JSON
                console.log('Dane z serwera (JSON):', data);
                if (data.success) {
                    console.log(`Znajomy ${friendLogin} został usunięty.`);
                    loadFriends(); // Odśwież listę znajomych
                } else {
                    console.error('Błąd podczas usuwania znajomego:', data.message);
                    console.error(`Log serwera: ${data.log}`);
                }
            } catch (error) {
                console.error('Błąd podczas parsowania JSON:', error);
            }
        })
        .catch(error => {
            console.error('Błąd podczas usuwania znajomego:', error);
        });
    }
    
    
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
    
                    // Dodaj przycisk "Usuń" do każdego znajomego
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Usuń';
                    deleteButton.classList.add('delete-friend-button');
                    deleteButton.onclick = function() {
                        console.log(`Kliknięto przycisk "Usuń" dla znajomego: ${friend.login}`);
                        removeFriend(friend.login);
                    };
    
                    listItem.appendChild(deleteButton);
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
