<!-- Sekcje notatek udostępnionych przez innych użytkowników -->
<?php if (!empty($sharedNotes)): 
    $groupedSharedNotes = [];
    foreach ($sharedNotes as $sharedNote) {
        $groupedSharedNotes[$sharedNote['owner_login']][] = $sharedNote;
    }
    ?>

    <?php foreach ($groupedSharedNotes as $ownerLogin => $ownerNotes): ?>
        <h2>Notatki <?= htmlspecialchars($ownerLogin); ?> 
            <span class="toggle-button" data-section="shared-notes-<?= htmlspecialchars($ownerLogin); ?>"> 
                <span class="material-symbols-outlined">hide</span> 
            </span>
        </h2>
        <div class="notes-container" id="shared-notes-<?= htmlspecialchars($ownerLogin); ?>">
            <?php foreach ($ownerNotes as $sharedNote): ?>
                <div class="note-card" data-id="<?= htmlspecialchars($sharedNote['id']); ?>" data-shared="true">
                    <h3><?= htmlspecialchars($sharedNote['title']); ?></h3>
                    <p><?= htmlspecialchars($sharedNote['content']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Brak udostępnionych notatek.</p>
<?php endif; ?>
