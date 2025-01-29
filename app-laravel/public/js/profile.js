document.addEventListener('DOMContentLoaded', function () {
    loadUserProfile();

    // Obsługa wylogowania
    document.getElementById('logout-button').addEventListener('click', function (event) {
        event.preventDefault();
        Auth.logout();
    });
});

function loadUserProfile() {
    fetch('/api/users/me', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${Auth.getToken()}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('profile-picture').src = data.data.profile_picture ? '/' + data.data.profile_picture : '/img/default_profile_picture.png';
            document.getElementById('profile-name').textContent = data.data.login;
        } else {
            console.error('Błąd pobierania profilu:', data.message);
        }
    })
    .catch(error => {
        console.error('Błąd podczas ładowania profilu:', error);
    });
}

document.getElementById('profile-button').addEventListener('click', function () {
    document.getElementById('profile-form-modal').style.display = 'flex';
});

document.getElementById('file-input').addEventListener('change', function () {
    const fileInput = document.getElementById('file-input');
    const fileName = document.getElementById('file-name');
    const file = fileInput.files[0];

    if (file) {
        fileName.textContent = `Wybrano plik: ${file.name}`;
        fileName.style.color = 'white';
    } else {
        fileName.textContent = 'Nie wybrano pliku';
        fileName.style.color = 'red';
    }
});






////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////// LEGACY ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////






document.addEventListener('DOMContentLoaded', function () {
    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMetaTag ? csrfMetaTag.getAttribute('content') : null;

    const friendsModal = document.getElementById('manage-friends-modal');
    const profileModal = document.getElementById('profile-form-modal');
    const profileForm = document.getElementById('profile-form');
    const profilePicture = document.querySelector('.profile-picture');
    const fileInput = document.getElementById('file-input'); 
    const friendsBtn = document.getElementById('friends-button');
    const closeFriendsModal = document.querySelector('.manage-friends-modal .back-button');
    const closeProfileFormModal = document.getElementById('close-profile-form-modal');
    const fileName = document.getElementById('file-name');
    const friendLoginInput = document.getElementById('friend-login');

    function resetFileInput() {
        fileInput.value = ''; 
        fileName.textContent = ''; 
    }

    function resetModalMessage() {
        fileName.textContent = '';
    }

    friendsBtn.onclick = function () {
        friendsModal.style.display = 'flex';
        loadFriends();
    };

    closeFriendsModal.onclick = function () {
        friendsModal.style.display = 'none';
        friendLoginInput.value = ''; 
    };

    closeProfileFormModal.onclick = function () {
        profileModal.style.display = 'none';
        resetFileInput();
        resetModalMessage(); 
    };

    window.onclick = function (event) {
        if (event.target === friendsModal) {
            friendsModal.style.display = 'none';
            friendLoginInput.value = ''; 
        }
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
            resetFileInput(); 
            resetModalMessage(); 
        }
    };

    // Poprawiona funkcja zmiany zdjęcia profilowego
    profileForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!fileInput.files.length) {
            fileName.textContent = 'Nie wybrano pliku'; 
            fileName.style.color = 'red';
            return;
        }

        const formData = new FormData(profileForm);
        formData.append('_token', csrfToken); 

        fetch('/api/users/me/profile-picture', { // Poprawiona ścieżka API
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                profilePicture.src = '/' + data.path + '?' + new Date().getTime();
                profileModal.style.display = 'none';
                resetFileInput();
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

    function loadFriends() {
        fetch('/api/friends', { // Poprawiona ścieżka API
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const friendsList = document.getElementById('friends-list');
            friendsList.innerHTML = '';
            data.forEach(friend => {
                const listItem = document.createElement('li');
                const friendItem = document.createElement('div');
                friendItem.classList.add('friend-item');

                const friendProfilePic = document.createElement('img');
                friendProfilePic.src = friend.profile_picture ? '/' + friend.profile_picture : '/img/default_profile_picture.png';
                friendProfilePic.alt = 'Profilowe';
                friendProfilePic.classList.add('friend-profile-picture');

                const friendLogin = document.createElement('span');
                friendLogin.textContent = friend.login;

                friendItem.appendChild(friendProfilePic);
                friendItem.appendChild(friendLogin);

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Usuń';
                deleteButton.classList.add('delete-friend-button');
                deleteButton.onclick = function () {
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

    function removeFriend(friendLogin) {
        fetch('/api/friends/' + friendLogin, { // Poprawiona ścieżka API
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFriends();
            } else {
                console.error('Błąd podczas usuwania znajomego:', data.message);
            }
        })
        .catch(error => {
            console.error('Błąd podczas usuwania znajomego:', error);
        });
    }
});
