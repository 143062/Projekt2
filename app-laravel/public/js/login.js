document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const loginInput = document.getElementById('login_or_email');
    const passwordInput = document.getElementById('password');
    const loginButton = document.getElementById('login-submit');

    function validateForm() {
        if (!loginInput.value.trim() || !passwordInput.value.trim()) {
            showError('Wprowadź login/email i hasło.');
            return false;
        }
        return true;
    }

    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    loginButton.addEventListener('click', function () {
        if (validateForm()) {
            fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    login_or_email: loginInput.value.trim(),
                    password: passwordInput.value.trim(),
                }),
            })
            .then(async (response) => {
                const responseData = await response.json();
                if (!response.ok) throw new Error(responseData.message || 'Błąd logowania.');

                Auth.setToken(responseData.token);
                alert('Logowanie zakończone sukcesem!');
                window.location.href = '/dashboard';
            })
            .catch((error) => {
                showError(error.message);
            });
        }
    });
});
