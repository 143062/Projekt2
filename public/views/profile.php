<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/profile.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <a href="/dashboard" class="back-button">
                <img src="/public/img/arrow_back.svg" alt="Back" class="back-icon">
            </a>
            <img src="/public/img/logo.svg" alt="Logo" class="logo">
        </div>
        <div class="profile-container">
            <div class="menu">
                <a href="#" class="menu-item" id="friends-button">
                    <img src="/public/img/friends_dark.svg" alt="Znajomi" class="menu-icon"> Znajomi
                </a>
                <div class="menu-divider"></div>
                <a href="#" class="menu-item" id="profile-button">
                    <img src="/public/img/adjust_dark.svg" alt="Zdjęcie" class="menu-icon"> Zdjęcie
                </a>
                <div class="menu-divider"></div>
                <a href="/logout" class="menu-item">
                    <img src="/public/img/logout.svg" alt="Wyloguj" class="menu-icon"> Wyloguj
                </a>
            </div>
            <div class="profile">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
                    <p class="success-message">Zdjęcie profilowe zostało zaktualizowane.</p>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    <p class="error-message">Wystąpił problem podczas aktualizacji zdjęcia profilowego.</p>
                <?php endif; ?>
                
                <!-- Wyświetlanie zdjęcia profilowego -->
                <img src="<?php echo htmlspecialchars($user['profile_picture'] ? $user['profile_picture'] : 'public/img/profile/default/default_profile_picture.jpg'); ?>" alt="Profile Picture" class="profile-picture" id="profile-picture">
                <p class="profile-name"><?php echo htmlspecialchars($user['login']); ?></p>
            </div>
        </div>
        <?php include 'change_picture_modal.php'; ?>
        <div id="manage-friends-modal" class="manage-friends-modal">
            <div class="manage-friends-modal-content">
                <span class="back-button"><img src="/public/img/arrow_back.svg" alt="Back" class="back-icon"></span>
                <h2>Zarządzaj znajomymi</h2>
                <form id="add-friend-form">
                    <label for="friend-login">Login znajomego:</label>
                    <input type="text" id="friend-login" name="friend-login" required>
                    <button type="submit">Dodaj znajomego</button>
                </form>
                <div id="current-friends">
                    <h3>Aktualni znajomi</h3>
                    <ul id="friends-list"></ul>
                </div>
            </div>
        </div>
    </div>
    <script src="/public/js/profile.js"></script>
</body>
</html>
