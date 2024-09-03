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

    friendsBtn.onclick = function() {
        friendsModal.style.display = 'flex';
        loadFriends(); // Ładuj znajomych przy otwieraniu modal
    }

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
        resetMessage(); // Resetowanie wiadomości przy zamknięciu modala
    }

    closeProfileFormModal.onclick = function() {
        document.getElementById('profile-form-modal').style.display = 'none';
    }

    document.getElementById('add-friend-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const friendLogin = document.getElementById('friend-login').value;

        if (friendLogin) {
            fetch('/add-friend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ friend_login: friendLogin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const listItem = document.createElement('li');
                    listItem.textContent = friendLogin;
                    document.getElementById('friends-list').appendChild(listItem);
                    document.getElementById('friend-login').value = '';
                    message.textContent = 'Znajomy został dodany pomyślnie.';
                    message.style.color = 'green';
                    message.style.display = 'block';
                } else {
                    message.textContent = data.message;
                    message.style.color = 'red';
                    message.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Błąd podczas dodawania znajomego:', error);
            });
        }
    });

    const profileFormModal = document.getElementById('profile-form-modal');

    window.onclick = function(event) {
        if (event.target == profileFormModal) {
            profileFormModal.style.display = 'none';
        }
        if (event.target == friendsModal) {
            friendsModal.style.display = 'none';
            resetMessage(); // Resetowanie wiadomości przy zamknięciu modala
        }
    }

    function loadFriends() {
        fetch('/friends')
            .then(response => response.json())
            .then(data => {
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
