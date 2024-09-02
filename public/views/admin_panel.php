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
            <div class="table-container">
                <h2>Aktualni użytkownicy</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nazwa Użytkownika</th>
                            <th>Email</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['login']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <form method="post" action="/admin/delete_user">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                            <button type="submit" class="delete-button">Usuń</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Brak użytkowników do wyświetlenia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-container">
                <h2>Dodaj Nowego Użytkownika</h2>
                <form method="post" action="/admin/add_user">
                    <div class="form-group">
                        <label for="username">Nazwa Użytkownika:</label>
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
                    <button type="submit" class="add-button">Dodaj Użytkownika</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
