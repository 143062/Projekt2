// init.js - Automatyczne zarządzanie sesją użytkownika
document.addEventListener('DOMContentLoaded', function () {
    console.log("[init.js] Inicjalizacja init.js");

    // Automatyczna obsługa przycisku "Wyloguj"
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function () {
            Auth.logout();
        });
    }
});
