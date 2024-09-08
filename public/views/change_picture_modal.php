<div id="profile-form-modal" class="profile-form-modal">
    <form id="profile-form" class="profile-form" method="post" action="/update_profile_picture" enctype="multipart/form-data">
        <span class="back-button"><img src="/public/img/arrow_back.svg" alt="Back" class="back-icon" id="close-profile-form-modal"></span>
        <h2>Zmień zdjęcie</h2> <!-- Dodany nagłówek -->
        <label for="file-input" class="file-input-label">Wybierz plik</label>
        <input type="file" id="file-input" class="file-input" name="profile_picture" accept="image/*">
        <p id="file-name" class="file-name"></p>
        <div class="form-buttons">
            <button type="submit" id="upload-button">Zmień</button>
        </div>
    </form>
</div>
