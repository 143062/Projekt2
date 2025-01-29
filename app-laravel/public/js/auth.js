const Auth = {
    isAuthenticated: function () {
        return localStorage.getItem('auth_token') !== null;
    },

    getToken: function () {
        return localStorage.getItem('auth_token');
    },

    setToken: function (token) {
        localStorage.setItem('auth_token', token);
    },

    clearToken: function () {
        localStorage.removeItem('auth_token');
    },

    attachAuthHeaders: function (headers = {}) {
        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        return headers;
    },

    logout: function () {
        fetch('/api/auth/logout', {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${this.getToken()}`,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Wylogowano:', data.message);
            this.clearToken();
            window.location.href = '/login';
        })
        .catch(error => {
            console.error('Błąd podczas wylogowywania:', error);
            this.clearToken();
            window.location.href = '/login';
        });
    },

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
