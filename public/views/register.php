<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/auth_common.css">
    <link rel="stylesheet" href="/public/css/register.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="logo">
                <img src="/public/img/logo.svg" alt="Logo" class="logo__image">
            </div>

            <!-- Miejsce na dynamiczne komunikaty o błędach -->
            <div class="register-form">
                <div class="error-container" style="display: none;">
                    <p class="error-message"></p>
                </div>
                <form id="register-form" class="form-column">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <input type="text" id="login" name="login" placeholder="Login" required>
                    <input type="password" id="password" name="password" placeholder="Hasło" required>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Potwierdź Hasło" required>
                    <div class="buttons-container">
                        <button type="submit" class="form__submit">Rejestruj</button>
                        <a href="/login" class="form_redirect">Logowanie</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dodajemy skrypt do obsługi AJAX -->
    <script src="/public/js/register.js"></script>
</body>
</html>
