<div id="manage-friends-modal" class="manage-friends-modal">
    <div class="manage-friends-form">
        <img src="/public/img/arrow_back.svg" alt="Back" class="back-icon" id="close-manage-friends-modal">
        <form id="add-friend-form" novalidate>
            <input type="text" id="friend-login" class="friend-username-input">
            <button type="submit" class="add-friend-button">Dodaj znajomego</button>
        </form>
        <p id="friend-error-message" style="color: red; display: none;"></p>
        <div class="friends-list">
            <h3>Aktualni znajomi</h3>
            <ul id="friends-list" class="friends-list-ul">
                <!-- Lista znajomych -->
            </ul>
        </div>
    </div>
</div>
