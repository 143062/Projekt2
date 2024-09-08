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
    const profileModal = document.getElementById('profile-form-modal');
    const friendsBtn = document.getElementById('friends-button');
    const closeFriendsModal = document.querySelector('.manage-friends-modal .back-button');
    const closeProfileFormModal = document.getElementById('close-profile-form-modal');
    const message = document.createElement('p');
    message.style.display = 'none';
    document.getElementById('add-friend-form').appendChild(message);

    // Funkcja wyświetlająca komunikaty
    function showMessage(messageText, color) {
        message.textContent = messageText;
        message.style.color = color;
        message.style.display = 'block';
    }

    // Funkcja resetująca komunikaty
    function resetMessage() {
        message.style.display = 'none';
        message.textContent = '';
    }

    const errorMessage = document.getElementById('friend-error-message'); 

    function validateFriendLogin() {
        const friendLogin = document.getElementById('friend-login').value.trim();
        
        if (!friendLogin) {
            showMessage('Proszę wpisać login znajomego', 'red');
            return false;
        }
        resetMessage(); 
        return true;
    }

    friendsBtn.onclick = function() {
        console.log('Otwieranie modala znajomych');
        friendsModal.style.display = 'flex';
        loadFriends();
    };

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
        console.log('Zamykanie modala znajomych');
        resetMessage();
    };

    closeProfileFormModal.onclick = function() {
        document.getElementById('profile-form-modal').style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target === friendsModal) {
            friendsModal.style.display = 'none';
            console.log('Kliknięcie poza modal - zamykanie modala znajomych');
            resetMessage();
        }
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
            console.log('Kliknięcie poza modal - zamykanie modala profilowego');
            resetMessage();
        }
    };

    document.getElementById('add-friend-form').addEventListener('submit', function(event) {
        event.preventDefault(); 
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
            return response.text(); 
        })
        .then(text => {
            console.log('Odpowiedź serwera (pełna treść):', text);
            try {
                const data = JSON.parse(text);
                console.log('Dane z serwera (JSON):', data);
                
                if (data.success) {
                    loadFriends();
                    document.getElementById('friend-login').value = '';
                    showMessage('Znajomy został dodany pomyślnie', 'green');
                } else {
                    showMessage(data.message, 'red');
                }
            } catch (error) {
                console.error('Błąd podczas parsowania JSON:', error);
                showMessage('Wystąpił problem z dodaniem znajomego. Spróbuj ponownie', 'red');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania znajomego:', error);
            showMessage('Wystąpił problem z dodaniem znajomego. Spróbuj ponownie', 'red');
        });
    });

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
            console.log('Pełna odpowiedź z serwera:', response);
            return response.text(); 
        })
        .then(text => {
            console.log('Odpowiedź serwera (pełna treść):', text);
            try {
                const data = JSON.parse(text);
                console.log('Dane z serwera (JSON):', data);
                if (data.success) {
                    console.log(`Znajomy ${friendLogin} został usunięty.`);
                    loadFriends();
                    showMessage('Znajomy został pomyślnie usunięty', 'green'); // Dodanie powiadomienia o usunięciu
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
                friendsList.innerHTML = '';
                data.forEach(friend => {
                    const listItem = document.createElement('li');
                    const friendItem = document.createElement('div');
                    friendItem.classList.add('friend-item');

                    const friendProfilePic = document.createElement('img');
                    friendProfilePic.src = friend.profile_picture ? friend.profile_picture : '/public/img/default_profile_picture.png';
                    friendProfilePic.alt = 'Profilowe';
                    friendProfilePic.classList.add('friend-profile-picture');

                    const friendLogin = document.createElement('span');
                    friendLogin.textContent = friend.login;

                    friendItem.appendChild(friendProfilePic);
                    friendItem.appendChild(friendLogin);

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Usuń';
                    deleteButton.classList.add('delete-friend-button');
                    deleteButton.onclick = function() {
                        console.log(`Kliknięto przycisk "Usuń" dla znajomego: ${friend.login}`);
                        removeFriend(friend.login);
                    };

                    listItem.appendChild(friendItem);
                    listItem.appendChild(deleteButton);
                    friendsList.appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('Błąd podczas ładowania znajomych:', error);
            });
    }
});
