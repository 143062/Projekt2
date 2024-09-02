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

    friendsBtn.onclick = function() {
        friendsModal.style.display = 'flex';
    }

    closeFriendsModal.onclick = function() {
        friendsModal.style.display = 'none';
    }

    closeProfileFormModal.onclick = function() {
        document.getElementById('profile-form-modal').style.display = 'none';
    }

    document.getElementById('add-friend-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const friendLogin = document.getElementById('friend-login').value;
        if (friendLogin) {
            const listItem = document.createElement('li');
            listItem.textContent = friendLogin;
            document.getElementById('friends-list').appendChild(listItem);
            document.getElementById('friend-login').value = '';
        }
    });

    const profileFormModal = document.getElementById('profile-form-modal');

    window.onclick = function(event) {
        if (event.target == profileFormModal) {
            profileFormModal.style.display = 'none';
        }
        if (event.target == friendsModal) {
            friendsModal.style.display = 'none';
        }
    }
});
