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

    function hideError() {
        errorContainer.style.display = 'none';
        errorMessage.textContent = '';
    }

    loginButton.addEventListener('click', function (event) {
        event.preventDefault();

        if (validateForm()) {
            hideError();

            Auth.login(
                loginInput.value.trim(),
                passwordInput.value.trim(),
                {
                    success: function () {
                        console.log("[login.js] Logowanie zakończone sukcesem!");
                    },
                    error: function (message) {
                        showError(message);
                    }
                }
            );
        }
    });
});
