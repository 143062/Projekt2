window.Auth = {
    isAuthenticated: function () {
        return localStorage.getItem('auth_token') !== null;
    },

    getToken: function () {
        return localStorage.getItem('auth_token');
    },

    setToken: function (token, role) {
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user_role', role); // Przechowywanie roli użytkownika
    },

    clearToken: function () {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_role'); // Usuwanie roli użytkownika przy wylogowaniu
    },

    attachAuthHeaders: function (headers = {}) {
        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        } else {
            console.warn("[auth.js] Brak tokena autoryzacji.");
        }
        return headers;
    },

    login: function (loginOrEmail, password, uiCallbacks) {
        fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                login_or_email: loginOrEmail,
                password: password,
            }),
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok) {
                if (uiCallbacks.error) uiCallbacks.error(data.message || "Błąd logowania.");
                return;
            }

            // Ustawiamy token i rolę użytkownika
            this.setToken(data.token, data.user.role);

            console.log("[auth.js] Zalogowano jako:", data.user.role);
            if (uiCallbacks.success) uiCallbacks.success();
            
            // Przekierowanie do odpowiedniego panelu
            window.location.href = data.user.role === "admin" ? '/admin_panel' : '/dashboard';
        })
        .catch((error) => {
            console.error("[auth.js] Błąd logowania:", error.message);
            if (uiCallbacks.error) uiCallbacks.error("Błąd połączenia z serwerem.");
        });
    },

    register: function (email, login, password, passwordConfirmation, callback) {
        fetch('/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                login: login,
                password: password,
                password_confirmation: passwordConfirmation,
            }),
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || "Błąd rejestracji.");
            }
            callback(true, data);
        })
        .catch((error) => {
            console.error("[auth.js] Błąd rejestracji:", error.message);
            callback(false, error.message);
        });
    },

    logout: function () {
        fetch('/api/auth/logout', {
            method: 'DELETE',
            headers: this.attachAuthHeaders(),
        })
        .then(response => response.json())
        .then(data => {
            console.log('[auth.js] Wylogowano:', data.message);
            this.clearToken();
            window.location.href = '/login';
        })
        .catch(error => {
            console.error('[auth.js] Błąd podczas wylogowywania:', error);
            this.clearToken();
            window.location.href = '/login';
        });
    }
};

// Automatyczne sprawdzenie dostępu po załadowaniu strony
document.addEventListener('DOMContentLoaded', function () {
    console.log("[auth.js] Inicjalizacja Auth.");
});
