// init.js - Automatyczne zarządzanie sesją użytkownika
document.addEventListener('DOMContentLoaded', function () {
    // Sprawdza, czy użytkownik ma dostęp do aktualnej strony
    Auth.checkAccess();

    // Automatyczna obsługa przycisku "Wyloguj"
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function () {
            Auth.logout();
        });
    }
});
