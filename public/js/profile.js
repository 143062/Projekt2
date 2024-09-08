document.getElementById('profile-button').addEventListener('click', function () {
    document.getElementById('profile-form-modal').style.display = 'flex';
});

document.getElementById('file-input').addEventListener('change', function () {
    const fileInput = document.getElementById('file-input');
    const fileName = document.getElementById('file-name');
    const file = fileInput.files[0];

    if (file) {
        fileName.textContent = `Wybrano plik: ${file.name}`;
        fileName.style.color = 'white'; // Zmiana koloru komunikatu na biały, gdy plik został wybrany
    } else {
        fileName.textContent = 'Nie wybrano pliku';
        fileName.style.color = 'red'; // Kolor czerwony, gdy nie wybrano pliku
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const friendsModal = document.getElementById('manage-friends-modal');
    const profileModal = document.getElementById('profile-form-modal');
    const profileForm = document.getElementById('profile-form');
    const profilePicture = document.querySelector('.profile-picture'); 
    const fileInput = document.getElementById('file-input'); // Referencja do inputu wyboru pliku
    const friendsBtn = document.getElementById('friends-button');
    const closeFriendsModal = document.querySelector('.manage-friends-modal .back-button');
    const closeProfileFormModal = document.getElementById('close-profile-form-modal');
    const fileName = document.getElementById('file-name');
    const friendLoginInput = document.getElementById('friend-login');
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

    // Resetowanie wartości inputu pliku
    function resetFileInput() {
        fileInput.value = ''; // Resetuje wartość inputu pliku
        document.getElementById('file-name').textContent = ''; // Resetuje tekst wybranego pliku
    }

    function resetModalMessage() {
        fileName.textContent = ''; // Resetuje komunikat o pliku przy zamknięciu modala
    }

    friendsBtn.onclick = function() {
        friendsModal.style.display = 'flex';
        loadFriends();
    };

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
        friendLoginInput.value = ''; // Resetowanie pola login znajomego
        resetMessage(); // Resetuj wiadomości po zamknięciu modala
    };

    closeProfileFormModal.onclick = function() {
        profileModal.style.display = 'none';
        resetFileInput(); // Resetuj pole pliku, gdy modal zostaje zamknięty
        resetModalMessage(); // Resetuj komunikat o pliku, gdy modal zostaje zamknięty
    };

    window.onclick = function(event) {
        if (event.target === friendsModal) {
            friendsModal.style.display = 'none';
            friendLoginInput.value = ''; // Resetowanie pola login znajomego
            resetMessage(); // Resetuj wiadomości po zamknięciu modala
        }
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
            resetFileInput(); // Resetuj pole pliku, gdy modal zostaje zamknięty
            resetModalMessage(); // Resetuj komunikat o pliku, gdy modal zostaje zamknięty
        }
    };

    // Funkcja zmieniająca zdjęcie profilowe
    profileForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Sprawdzenie, czy plik został wybrany
        if (!fileInput.files.length) {
            fileName.textContent = 'Nie wybrano pliku'; // Wyświetl informację o braku pliku
            fileName.style.color = 'red';
            return;
        }

        const formData = new FormData(profileForm);
        fetch('/update_profile_picture', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                profilePicture.src = data.newProfilePictureUrl + '?' + new Date().getTime(); // Załaduj nowe zdjęcie, aby ominąć cache
                profileModal.style.display = 'none'; // Zamknij modal
                resetFileInput(); // Resetuj pole pliku po udanej aktualizacji zdjęcia
            } else {
                fileName.textContent = 'Wystąpił problem z aktualizacją zdjęcia';
                fileName.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Błąd podczas zmiany zdjęcia profilowego:', error);
            fileName.textContent = 'Wystąpił problem z aktualizacją zdjęcia';
            fileName.style.color = 'red';
        });
    });

    document.getElementById('add-friend-form').addEventListener('submit', function(event) {
        event.preventDefault(); 
        if (!validateFriendLogin()) {
            return;
        }

        const friendLogin = friendLoginInput.value.trim();
        console.log(`Próba dodania znajomego: ${friendLogin}`);

        fetch('/add-friend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ friend_login: friendLogin })
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    loadFriends();
                    friendLoginInput.value = '';
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
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    loadFriends();
                    showMessage('Znajomy został pomyślnie usunięty', 'green');
                } else {
                    showMessage('Wystąpił problem z usunięciem znajomego', 'red');
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
        fetch('/friends')
            .then(response => response.json())
            .then(data => {
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

    function validateFriendLogin() {
        const friendLogin = friendLoginInput.value.trim();
        if (!friendLogin) {
            showMessage('Proszę wpisać login znajomego', 'red');
            return false;
        }
        return true;
    }
});
