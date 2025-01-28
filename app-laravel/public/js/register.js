document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('register-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const emailInput = document.getElementById('email');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const registerButton = document.getElementById('register-submit'); // Obsługa kliknięcia przycisku

    // Zablokowanie domyślnej walidacji przeglądarki
    registerForm.setAttribute('novalidate', true);

    // Ustawienie odpowiednich komunikatów dla każdego pola
    function validateForm() {
        const email = emailInput.value.trim();
        const login = loginInput.value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            showError('Brak adresu email');
            return false;
        } else if (!emailRegex.test(email)) {
            showError('Nieprawidłowy format email');
            return false;
        }

        if (!login) {
            showError('Brak loginu');
            return false;
        }

        if (!password) {
            showError('Brak hasła');
            return false;
        } else if (password.length < 6) {
            showError('Utwórz dłuższe hasło');
            return false;
        }

        if (!confirmPassword) {
            showError('Brak potwierdzenia hasła');
            return false;
        }

        if (password !== confirmPassword) {
            showError('Hasła nie są zgodne');
            return false;
        }

        return true;
    }

    // Funkcja do wyświetlania błędów z serwera
    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    // Obsługa kliknięcia przycisku "Rejestruj"
    registerButton.addEventListener('click', function () {
        if (validateForm()) {
            // Przygotowanie danych w formacie JSON
            const data = {
                email: emailInput.value.trim(),
                login: loginInput.value.trim(),
                password: passwordInput.value,
                password_confirmation: confirmPasswordInput.value,
            };

            // Wysłanie danych do API
            fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            })
                .then(async (response) => {
                    const responseData = await response.json();

                    console.error('Odpowiedź API:', responseData); // Debugowanie odpowiedzi API

                    if (!response.ok) {
                        // Szczegółowe wyświetlenie błędów zwróconych przez serwer
                        if (responseData.login || responseData.email) {
                            const loginErrors = responseData.login?.join(' ') || '';
                            const emailErrors = responseData.email?.join(' ') || '';
                            throw new Error(`${loginErrors} ${emailErrors}`.trim());
                        }

                        throw new Error(responseData.message || 'Błąd rejestracji.');
                    }

                    // Sukces: przekierowanie na stronę logowania
                    alert('Rejestracja zakończona sukcesem! Możesz się teraz zalogować.');
                    window.location.href = '/login';
                })
                .catch((error) => {
                    console.error('Wystąpił błąd:', error);
                    showError(error.message || 'Wystąpił problem z rejestracją.');
                });
        }
    });
});
