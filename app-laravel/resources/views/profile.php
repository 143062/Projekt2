<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/profile.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <a href="/dashboard" class="back-button">
                <img src="/img/arrow_back.svg" alt="Back" class="back-icon">
            </a>
            <img src="/img/logo.svg" alt="Logo" class="logo">
        </div>
        <div class="profile-container">
            <div class="menu">
                <a href="#" class="menu-item" id="friends-button">
                    <img src="/img/friends_dark.svg" alt="Znajomi" class="menu-icon"> Znajomi
                </a>
                <div class="menu-divider"></div>
                <a href="#" class="menu-item" id="profile-button">
                    <img src="/img/adjust_dark.svg" alt="Zdjęcie" class="menu-icon"> Zdjęcie
                </a>
                <div class="menu-divider"></div>
                <a href="#" class="menu-item" id="logout-button">
                    <img src="/img/logout.svg" alt="Wyloguj" class="menu-icon"> Wyloguj
                </a>
            </div>
            <div class="profile">
                <img src="" alt="Profile Picture" class="profile-picture" id="profile-picture">
                <p class="profile-name" id="profile-name"></p>
            </div>
        </div>

        <!-- Include modala do zmiany zdjęcia -->
        <?php include resource_path('views/change_picture_modal.php'); ?>

        <!-- Include modala do zarządzania znajomymi -->
        <?php include resource_path('views/manage_friends_modal.php'); ?>

    </div>

    <!-- Dodane skrypty do autoryzacji i inicjalizacji -->
    <script src="/js/auth.js"></script>
    <script src="/js/init.js"></script>

    <!-- Skrypt obsługujący profil użytkownika -->
    <script src="/js/profile.js"></script>
</body>
</html>
