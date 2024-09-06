<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" />
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ? $user['profile_picture'] : 'public/img/profile/default/default_profile_picture.jpg'); ?>" alt="Profile" class="profile-icon" onclick="location.href='/profile'">
            <div class="search-bar">
                <img src="/public/img/search_dark.svg" alt="Szukaj">
                <input type="text" placeholder="Szukaj">
            </div>
            <img src="/public/img/logo.svg" alt="Logo" class="logo">
        </div>
        <div class="notes-section">
            <h2>Moje notatki <span class="toggle-button" data-section="my-notes"> 
                <span class="material-symbols-outlined">hide</span> 
            </span></h2>
            <div class="notes-container" id="my-notes">
                <?php if (!empty($notes)): ?>
                    <?php foreach ($notes as $index => $note): ?>
                        <div class="note-card" data-id="<?php echo htmlspecialchars($note['id']); ?>" data-index="<?php echo $index; ?>">
                            <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                            <p><?php echo htmlspecialchars($note['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Brak notatek do wyświetlenia.</p>
                <?php endif; ?>
            </div>

            <!-- Include shared notes -->
            <?php include 'shared_notes.php'; ?>
        </div>

        <button id="add-note-button" class="add-note-button">
            <img src="/public/img/plus.svg" alt="Dodaj">
        </button>

        <!-- Include add note modal -->
        <?php include 'add_note_modal.php'; ?>

        <!-- Include edit note modal -->
        <?php include 'edit_note_modal.php'; ?>
        
        <!-- Include share note modal -->
        <?php include 'share_note_modal.php'; ?>
    </div>
    <script src="/public/js/dashboard.js"></script>
</body>
</html>
