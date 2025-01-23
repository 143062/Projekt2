document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const errorContainer = document.querySelector('.error-container');
    const errorMessage = document.querySelector('.error-message');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');

    // Zablokowanie domyślnej walidacji przeglądarki
    loginForm.setAttribute('novalidate', true);

    function validateForm() {
        const login = loginInput.value.trim();
        const password = passwordInput.value.trim();

        if (!login) {
            showError('Brak loginu');
            return false;
        }

        if (!password) {
            showError('Brak hasła');
            return false;
        }

        return true;
    }

    function showError(message) {
        errorContainer.style.display = 'block';
        errorMessage.textContent = message;
    }

    loginForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (validateForm()) {
            const formData = new FormData(loginForm);

            fetch('/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        showError(data.message);
                    } else if (data.status === 'success') {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Wystąpił błąd:', error);
                });
        }
    });
});
