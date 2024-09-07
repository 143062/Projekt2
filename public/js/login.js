document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');

    // Zablokowanie domyślnej walidacji przeglądarki
    loginForm.setAttribute('novalidate', true);

    // Funkcja walidująca formularz
    function validateForm() {
        const login = loginInput.value.trim();
        const password = passwordInput.value.trim();

        // Sprawdzenie czy login został podany
        if (!login) {
            showError('Brak loginu');
            return false;
        }

        // Sprawdzenie czy hasło zostało podane
        if (!password) {
            showError('Brak hasła');
            return false;
        }

        return true; // Formularz prawidłowo wypełniony
    }

    // Funkcja do wyświetlania błędów
    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    loginForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Blokujemy domyślne działanie formularza

        // Walidacja formularza przed wysłaniem
        if (validateForm()) {
            const formData = new FormData(loginForm);

            // Wysłanie formularza, jeśli wszystko jest poprawne
            fetch('/login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Oczekujemy odpowiedzi w formacie JSON
            .then(data => {
                if (data.status === 'error') {
                    // Wyświetlenie błędu z serwera
                    errorContainer.style.display = 'block';
                    errorMessage.textContent = data.message;
                } else if (data.status === 'success') {
                    // Przekierowanie na odpowiednią stronę
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error('Wystąpił błąd:', error);
            });
        }
    });
});
