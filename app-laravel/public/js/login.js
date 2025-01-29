document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const loginInput = document.getElementById('login_or_email'); // Zmieniono id na login_or_email
    const passwordInput = document.getElementById('password');
    const loginButton = document.getElementById('login-submit'); // Obsługa kliknięcia przycisku

    // Walidacja formularza
    function validateForm() {
        const loginOrEmail = loginInput.value.trim();
        const password = passwordInput.value.trim();

        if (!loginOrEmail) {
            showError('Brak loginu lub emaila');
            return false;
        }

        if (!password) {
            showError('Brak hasła');
            return false;
        }

        return true;
    }

    // Funkcja do wyświetlania błędów
    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    // Obsługa kliknięcia przycisku "Zaloguj"
    loginButton.addEventListener('click', function () {
        if (validateForm()) {
            const data = {
                login_or_email: loginInput.value.trim(),
                password: passwordInput.value.trim(),
            };

            // Wysłanie danych do API
            fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            })
                .then(async (response) => {
                    const responseData = await response.json();

                    if (!response.ok) {
                        // Wyświetlenie błędów zwróconych przez serwer
                        throw new Error(responseData.message || 'Błąd logowania.');
                    }

                    // Zapisz token w localStorage
                    localStorage.setItem('auth_token', responseData.token);

                    // Przekierowanie na stronę główną
                    alert('Logowanie zakończone sukcesem!');
                    window.location.href = '/dashboard';
                })
                .catch((error) => {
                    console.error('Wystąpił błąd:', error);
                    showError(error.message || 'Wystąpił problem z logowaniem.');
                });
        }
    });
});
