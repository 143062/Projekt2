<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/admin_panel.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <a href="/logout" class="profile-button">
                <img src="/public/img/logout.svg" alt="Logout" class="profile-icon">
            </a>
            <h1 class="admin-title">Panel Administratora</h1>
            <img src="/public/img/logo.svg" alt="Logo" class="logo">
        </div>

        <div class="content">
            <!-- Sekcja użytkowników -->
            <div class="table-container">
                <h2>Użytkownicy</h2>
                <div class="search-bar">
                    <img src="/public/img/search_dark.svg" alt="Szukaj">
                    <input type="text" id="user-search" placeholder="Szukaj...">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rola</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Data utworzenia</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody id="user-list">
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo htmlspecialchars($user['login']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($user['created_at'], 0, 19)); ?></td>
                                    <td>
                                        <button type="button" class="reset-password-button button" data-user-id="<?php echo htmlspecialchars($user['id']); ?>" data-login="<?php echo htmlspecialchars($user['login']); ?>">Hasło</button>
                                        <form method="post" action="/admin/delete_user" style="display:inline-block;">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                            <button type="submit" class="delete-button button">Usuń</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Brak użytkowników do wyświetlenia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sekcja dodawania użytkownika -->
            <div class="form-container">
                <h2>Dodaj Nowego Użytkownika</h2>
                <form method="post" action="/admin/add_user">
                    <div class="form-group">
                        <label for="username">Login:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Hasło:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rola:</label>
                        <select id="role" name="role" required>
                            <option value="user">Użytkownik</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="add-button">Dodaj Użytkownika</button>
                </form>
            </div>

            <!-- Sekcja SQL Dump i Import -->
            <div class="sql-dump-container">
                <h2>Zarządzanie Bazą Danych</h2>
                <div id="import-status" class="alert" style="display: none;"></div> <!-- Miejsce na komunikat o sukcesie/błędzie -->
                <form method="post" action="/admin/sql_dump">
                    <button type="submit" class="sql-dump-button">Pobierz SQL Dump</button>
                </form>
                <form method="post" action="/admin/sql_import" enctype="multipart/form-data" id="sql-import-form">
                    <div class="file-input-container">
                        <label for="sql-file" class="file-input-label">Wybierz plik SQL</label>
                        <input type="file" id="sql-file" name="sql_file" class="file-input">
                        <span class="file-name">Nie wybrano pliku</span>
                    </div>
                    <button type="submit" class="sql-import-button">Importuj SQL</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal do resetu hasła -->
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

    <script src="/public/js/admin_panel.js"></script>
</body>
</html>
