document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('register-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const emailInput = document.getElementById('email');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const registerButton = document.getElementById('register-submit');

    function validateForm() {
        if (!emailInput.value.trim() || !loginInput.value.trim() || !passwordInput.value || !confirmPasswordInput.value) {
            showError('Wypełnij wszystkie pola.');
            return false;
        }
        if (passwordInput.value !== confirmPasswordInput.value) {
            showError('Hasła nie są zgodne.');
            return false;
        }
        return true;
    }

    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    registerButton.addEventListener('click', function () {
        if (validateForm()) {
            fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: emailInput.value.trim(),
                    login: loginInput.value.trim(),
                    password: passwordInput.value,
                    password_confirmation: confirmPasswordInput.value,
                }),
            })
            .then(async (response) => {
                const responseData = await response.json();
                if (!response.ok) throw new Error(responseData.message || 'Błąd rejestracji.');


                window.location.href = '/login';
            })
            .catch((error) => {
                showError(error.message);
            });
        }
    });
});
