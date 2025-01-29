document.addEventListener('DOMContentLoaded', function () {
    loadUserProfile();

    // Obsługa wylogowania
    document.getElementById('logout-button').addEventListener('click', function (event) {
        event.preventDefault();
        Auth.logout();
    });
});

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

document.addEventListener('DOMContentLoaded', function () {
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
    const addFriendForm = document.getElementById('add-friend-form');

    function resetFileInput() {
        fileInput.value = ''; 
        fileName.textContent = ''; 
    }

    function resetModalMessage() {
        fileName.textContent = '';
    }

    friendsBtn.onclick = function () {
        friendsModal.style.display = 'flex';
        loadFriendsFromAPI(); // Wywołujemy funkcję z profile-api.js
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

    // Obsługa zmiany zdjęcia profilowego
    profileForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!fileInput.files.length) {
            fileName.textContent = 'Nie wybrano pliku'; 
            fileName.style.color = 'red';
            return;
        }

        const formData = new FormData(profileForm);
        updateProfilePicture(formData); // Wywołujemy funkcję z profile-api.js
    });

    // Obsługa dodawania znajomego
    addFriendForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const friendLogin = friendLoginInput.value.trim();

        if (friendLogin) {
            addFriendToAPI(friendLogin); // Przeniesione do profile-api.js
        } else {
            console.error("Login znajomego jest pusty.");
        }
    });
});

function displayFriends(friends) {
    const friendsList = document.getElementById('friends-list');
    friendsList.innerHTML = '';

    friends.forEach(friend => {
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
            removeFriendFromAPI(friend.login); // Przeniesione do profile-api.js
        };

        listItem.appendChild(friendItem);
        listItem.appendChild(deleteButton);
        friendsList.appendChild(listItem);
    });
}

function handleFriendRemoval(friendLogin, success) {
    if (success) {
        loadFriendsFromAPI(); // Wywołujemy ponownie API
    } else {
        console.error('Błąd podczas usuwania znajomego:', friendLogin);
    }
}
