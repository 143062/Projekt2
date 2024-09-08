<div id="manage-friends-modal" class="manage-friends-modal">
    <div class="manage-friends-modal-content">
        <span class="back-button"><img src="/public/img/arrow_back.svg" alt="Back" class="back-icon"></span>
        <h2>Zarządzaj znajomymi</h2>
        <form id="add-friend-form" novalidate>
            <label for="friend-login">Login znajomego:</label>
            <input type="text" id="friend-login" name="friend-login" class="friend-username-input">
            <button type="submit">Dodaj znajomego</button>
        </form>
        <p id="friend-error-message" style="color: red; display: none;"></p>
        <div id="current-friends">
            <h3>Aktualni znajomi</h3>
            <ul id="friends-list"></ul>
        </div>
    </div>
</div>
