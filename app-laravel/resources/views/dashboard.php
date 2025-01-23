<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400">

    <!-- Meta-tag CSRF -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
        <img 
        src="<?php echo htmlspecialchars($user['profile_picture'] ?? '/img/profile/default/default_profile_picture.jpg'); ?>" 
        alt="Profile" 
        class="profile-icon" 
        onclick="location.href='/profile'">


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
                <?php if (!empty($notes)): ?>
                    <?php foreach ($notes as $index => $note): ?>
                        <div class="note-card" data-id="<?php echo htmlspecialchars($note->id); ?>" data-index="<?php echo $index; ?>">
                            <h3><?php echo htmlspecialchars($note->title); ?></h3>
                            <p><?php echo htmlspecialchars($note->content); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Brak notatek do wyświetlenia.</p>
                <?php endif; ?>
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
                    <?php if (!empty($sharedNotes)): ?>
                        <?php foreach ($sharedNotes as $index => $sharedNote): ?>
                            <div class="note-card shared" data-id="<?php echo htmlspecialchars($sharedNote->id); ?>" data-index="<?php echo $index; ?>">
                                <h3><?php echo htmlspecialchars($sharedNote->title); ?></h3>
                                <p><?php echo htmlspecialchars($sharedNote->content); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Brak współdzielonych notatek do wyświetlenia.</p>
                    <?php endif; ?>
                </div>
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
    <script src="/js/dashboard.js"></script>
</body>
</html>
