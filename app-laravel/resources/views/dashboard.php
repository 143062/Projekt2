<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img id="profile-picture" src="/img/profile/default/default_profile_picture.jpg" 
                 alt="Profile" class="profile-icon" onclick="location.href='/profile'">

            <div class="search-bar">
                <img src="/img/search_dark.svg" alt="Szukaj">
                <input type="text" placeholder="Szukaj">
            </div>
            <img src="/img/logo.svg" alt="Logo" class="logo">
        </div>


        <!-- Notes Section -->
        <div class="notes-section">
            <h2>
                Moje notatki 
                <span class="toggle-button" data-section="my-notes"> 
                    <span class="material-symbols-outlined">hide</span>
                </span>
            </h2>
            <div class="notes-container" id="my-notes">
                <p>Ładowanie notatek...</p>
            </div>



            <!-- Shared Notes Section -->
            <div class="shared-notes-section">
    <h2>
        Współdzielone notatki 
        <span class="toggle-button" data-section="shared-notes"> 
            <span class="material-symbols-outlined">hide</span>
        </span>
    </h2>
    <div class="notes-container" id="shared-notes">
        <p>Ładowanie współdzielonych notatek...</p>
    </div>
</div>



                            



        <!-- Add Note Button -->
        <button id="add-note-button" class="add-note-button">
            <img src="/img/plus.svg" alt="Dodaj">
        </button>

        <!-- Modals -->
        <?php include resource_path('views/add_note_modal.php'); ?>
        <?php include resource_path('views/edit_note_modal.php'); ?>
        <?php include resource_path('views/share_note_modal.php'); ?>
    </div>

    <!-- Scripts -->
    <script src="/js/auth.js"></script>
    <script src="/js/init.js"></script>

    <script src="/js/dashboard-api.js"></script>
    <script src="/js/dashboard.js"></script>
    

</body>
</html>
