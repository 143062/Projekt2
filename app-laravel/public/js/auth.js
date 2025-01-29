// auth.js - Obsługa autoryzacji użytkownika

const Auth = {
    // Sprawdzenie, czy użytkownik jest zalogowany
    isAuthenticated: function () {
        return localStorage.getItem('auth_token') !== null;
    },

    // Pobranie tokena użytkownika
    getToken: function () {
        return localStorage.getItem('auth_token');
    },

    // Zapisanie tokena do localStorage
    setToken: function (token) {
        localStorage.setItem('auth_token', token);
    },

    // Usunięcie tokena (wylogowanie)
    clearToken: function () {
        localStorage.removeItem('auth_token');
    },

    // Dołączanie tokena do nagłówków żądań API
    attachAuthHeaders: function (headers = {}) {
        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        return headers;
    },

    // Wylogowanie użytkownika
    logout: function () {
        const token = this.getToken();

        if (!token) {
            window.location.href = '/login';
            return;
        }

        fetch('/api/auth/logout', {
            method: 'POST',
            headers: this.attachAuthHeaders({ 'Content-Type': 'application/json' })
        })
        .then(() => {
            this.clearToken();
            window.location.href = '/login';
        })
        .catch(error => {
            console.error('Błąd podczas wylogowywania:', error);
            this.clearToken();
            window.location.href = '/login';
        });
    },

    // Sprawdzanie dostępu do stron
    checkAccess: function () {
        const isLoginPage = window.location.pathname === '/login';
        const isRegisterPage = window.location.pathname === '/register';

        if ((isLoginPage || isRegisterPage) && this.isAuthenticated()) {
            window.location.href = '/dashboard';
        }
    }
};

// Automatyczne sprawdzenie dostępu po załadowaniu strony
document.addEventListener('DOMContentLoaded', function () {
    Auth.checkAccess();
});
