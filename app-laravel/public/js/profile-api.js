window.loadUserProfile = function () {
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
            const profilePicture = data.data.profile_picture || '/img/profile/default/default_profile_picture.jpg';
            document.getElementById('profile-picture').src = profilePicture;
            document.getElementById('profile-name').textContent = data.data.login;
        } else {
            console.error('Błąd pobierania profilu:', data.message);
            document.getElementById('profile-picture').src = '/img/profile/default/default_profile_picture.jpg';
        }
    })
    .catch(error => {
        console.error('Błąd podczas ładowania profilu:', error);
        document.getElementById('profile-picture').src = '/img/profile/default/default_profile_picture.jpg';
    });
};



window.updateProfilePicture = function (formData) {
    fetch('/api/users/me/profile-picture', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${Auth.getToken()}`
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('profile-picture').src = data.path + '?' + new Date().getTime();
            document.getElementById('profile-form-modal').style.display = 'none';
            document.getElementById('file-input').value = ''; 
        } else {
            console.error('Błąd zmiany zdjęcia:', data.message);
        }
    })
    .catch(error => console.error('Błąd podczas zmiany zdjęcia:', error));
};


window.loadFriendsFromAPI = function () {
    fetch('/api/friends', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${Auth.getToken()}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayFriends(data);
    })
    .catch(error => console.error('Błąd podczas ładowania znajomych:', error));
};

window.addFriendToAPI = function (friendLogin) {
    fetch('/api/friends', { 
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${Auth.getToken()}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ friend_login: friendLogin })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            loadFriendsFromAPI();
        } else {
            console.error('Błąd podczas dodawania znajomego:', data.message);
        }
    })
    .catch(error => console.error('Błąd podczas dodawania znajomego:', error));
};

window.removeFriendFromAPI = function (friendLogin) {
    fetch('/api/friends', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${Auth.getToken()}`,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(friends => {
        const friend = friends.find(f => f.login === friendLogin);
        if (!friend) {
            console.error('Błąd: Nie znaleziono znajomego o podanym loginie.');
            return;
        }

        fetch('/api/friends/' + friend.id, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadFriendsFromAPI();
            } else {
                console.error('Błąd podczas usuwania znajomego:', data.message);
            }
        })
        .catch(error => console.error('Błąd podczas usuwania znajomego:', error));
    })
    .catch(error => console.error('Błąd podczas pobierania znajomych:', error));
};
