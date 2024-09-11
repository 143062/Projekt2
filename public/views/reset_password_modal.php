<div id="adminPasswordResetModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Resetowanie hasła dla użytkownika: <span id="modal-username"></span></h2>
        <form id="reset-password-form" method="post" action="/admin/reset_password">
            <input type="hidden" id="user-id" name="user_id">
            <div class="form-group">
                <label for="new-password">Nowe hasło:</label>
                <input type="password" id="new-password" name="new_password" required>
            </div>
            <button type="submit" class="reset-button">Zresetuj hasło</button>
        </form>
    </div>
</div>
