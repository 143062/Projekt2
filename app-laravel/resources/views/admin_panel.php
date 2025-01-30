<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/admin_panel.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
        <button id="logout-button" class="profile-button">
            <img src="/img/logout.svg" alt="Logout" class="profile-icon">
        </button>

            <h1 class="admin-title">Panel Administratora</h1>
            <img src="/img/logo.svg" alt="Logo" class="logo">
        </div>



        <div class="content">






            <!-- Sekcja użytkowników -->
            <div class="table-container">
                <h2>Użytkownicy</h2>
                <div class="search-bar">
                    <img src="/img/search_dark.svg" alt="Szukaj">
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
                        <tr>
                            <td colspan="6">Ładowanie użytkowników...</td>
                        </tr>
                    </tbody>
                </table>
            </div>



            <!-- Sekcja dodawania użytkownika -->
            <div class="form-container">
                <h2>Dodaj Nowego Użytkownika</h2>
                <form id="add-user-form">
                    <div class="form-group">
                        <label for="username">Login:</label>
                        <input type="text" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Hasło:</label>
                        <input type="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rola:</label>
                        <select id="role" required>
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
                
                <button id="sql-dump-button" class="sql-dump-button">Pobierz SQL Dump</button>

                <form id="sql-import-form">
                    <div class="file-input-container">
                        <label for="sql-file" class="file-input-label">Wybierz plik SQL</label>
                        <input type="file" id="sql-file" class="file-input">
                        <span class="file-name">Nie wybrano pliku</span>
                    </div>
                    <button type="submit" id="sql-import-button" class="sql-import-button">Importuj SQL</button>
                </form>
            </div>



            

            <!-- Nowa sekcja testów jednostkowych -->
            <div class="test-unit-container">
                <h2>Testy Jednostkowe</h2>
                <button id="run-tests-button" class="run-tests-button">Uruchom</button>
                <pre id="test-results" class="test-results"></pre> <!-- Miejsce na wyniki testów -->
            </div>




        </div>



    </div>





    <!-- Modal do resetu hasła -->
    <?php include resource_path('views/reset_password_modal.php'); ?>


    <script src="/js/auth.js"></script>
    <script src="/js/init.js"></script>


    <script src="/js/admin_panel-api.js"></script>
    <script src="/js/admin_panel.js"></script>


</body>
</html>
