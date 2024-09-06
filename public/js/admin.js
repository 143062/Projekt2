document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const passwordButtons = document.querySelectorAll('.reset-password-button');
    const modal = document.getElementById('adminPasswordResetModal');
    const closeModal = document.querySelector('.close');
    const modalUsername = document.getElementById('modal-username');
    const userIdInput = document.getElementById('user-id');

    // Obsługa przycisków usuwania użytkownika
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            console.log(`Kliknięto przycisk usuwania użytkownika o ID: ${userId}`);
            
            if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                fetch(`/delete_user.php?id=${userId}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    console.log(`Odpowiedź z serwera: ${data}`);
                    if (data === 'success') {
                        this.parentElement.parentElement.remove();
                        console.log('Użytkownik został usunięty.');
                    } else {
                        console.error('Wystąpił błąd podczas usuwania użytkownika.');
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
            console.log(`Kliknięto przycisk resetowania hasła dla użytkownika o ID: ${userId} (login: ${login})`);
            
            modalUsername.textContent = login;
            userIdInput.value = userId;
            modal.style.display = 'flex';
            console.log('Wyświetlono modal do resetowania hasła.');
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
            console.log('Kliknięto poza modalem, zamknięto modal resetowania hasła.');
        }
    };
});
