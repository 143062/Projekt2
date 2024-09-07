document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('register-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const emailInput = document.getElementById('email');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    // Zablokowanie domyślnej walidacji przeglądarki
    registerForm.setAttribute('novalidate', true);

    // Ustawienie odpowiednich komunikatów dla każdego pola
    function validateForm() {
        // Pobranie wartości z pól
        const email = emailInput.value;
        const login = loginInput.value;
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Walidacja formatu e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            showError('Brak adresu email');
            return false;
        } else if (!emailRegex.test(email)) {
            showError('Nieprawidłowy format email');
            return false;
        }

        // Walidacja loginu
        if (!login) {
            showError('Brak loginu');
            return false;
        }

        // Walidacja hasła
        if (!password) {
            showError('Brak hasła');
            return false;
        }

        // Walidacja potwierdzenia hasła
        if (!confirmPassword) {
            showError('Brak potwierdzenia hasła');
            return false;
        }

        // Sprawdzenie zgodności haseł
        if (password !== confirmPassword) {
            showError('Hasła nie są zgodne');
            return false;
        }

        return true; // Jeśli wszystko jest poprawne, zwracamy true
    }

    // Funkcja do wyświetlania błędów
    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    registerForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Blokujemy domyślne działanie formularza

        // Walidacja krok po kroku
        if (validateForm()) {
            const formData = new FormData(registerForm);

            // Wysłanie formularza, jeśli wszystko jest poprawne
            fetch('/register', {
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
                    // Przekierowanie na stronę logowania
                    window.location.href = '/login';
                }
            })
            .catch(error => {
                console.error('Wystąpił błąd:', error);
            });
        }
    });
});
