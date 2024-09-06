document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const passwordButtons = document.querySelectorAll('.reset-password-button');
    const modal = document.getElementById('adminPasswordResetModal');
    const closeModal = document.querySelector('.close');
    const modalUsername = document.getElementById('modal-username');
    const userIdInput = document.getElementById('user-id');
    const searchInput = document.getElementById('user-search');
    const userTable = document.getElementById('user-list');

    // Obsługa wyszukiwania użytkowników
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase();
        const rows = userTable.querySelectorAll('tr');
        rows.forEach(row => {
            const userId = row.querySelector('td:nth-child(1)').textContent.toLowerCase(); // Wyszukiwanie po ID
            const username = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Wyszukiwanie po loginie
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase(); // Wyszukiwanie po emailu
            if (userId.includes(query) || username.includes(query) || email.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Obsługa przycisków usuwania użytkownika
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                fetch(`/delete_user.php?id=${userId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        this.parentElement.parentElement.remove();
                    } else {
                        alert('Wystąpił błąd podczas usuwania użytkownika.');
                    }
                });
            }
        });
    });

    // Obsługa otwierania modala do resetowania hasła
    passwordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const login = this.getAttribute('data-login');
            modalUsername.textContent = login;
            userIdInput.value = userId;
            modal.style.display = 'flex';
            console.log(`Kliknięto przycisk resetowania hasła dla użytkownika o ID: ${userId} (login: ${login})`);
        });
    });

    // Obsługa zamykania modala
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        console.log('Zamknięto modal resetowania hasła.');
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});
