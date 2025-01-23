<div id="note-form-container" class="note-form-container">
    <div class="note-form">
        <!-- Token CSRF -->
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

        <input type="text" id="note-title" class="note-title" name="title" placeholder="Tytuł">
        <div class="share-section">
            <div id="shared-with" class="shared-with"></div>
            <button id="add-friend" class="add-friend">
                <img src="/img/plus.svg" alt="Dodaj znajomego">
            </button>
        </div>
        <textarea id="note-content" class="note-content" name="content" placeholder="Treść notatki"></textarea>
        <button id="save-note" class="save-note">
            <img src="/img/check.svg" alt="Zapisz">
        </button>
    </div>
</div>
