<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/auth_common.css">
    <link rel="stylesheet" href="/css/login.css">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="logo">
                <img src="/img/logo.svg" alt="Logo" class="logo__image">
            </div>

            <div class="login-form">
                <div class="error-container" style="display: none;">
                    <p class="error-message"></p>
                </div>
                <!-- Formularz -->
                <form id="login-form" class="form-column" novalidate>
                    <input type="text" id="login_or_email" name="login_or_email" placeholder="Login lub Email" required>
                    <input type="password" id="password" name="password" placeholder="Hasło" required>
                    <div class="buttons-container">
                        <button type="button" id="login-submit" class="form__submit">Zaloguj</button>
                        <a href="/register" class="form_redirect">Rejestracja</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/js/login.js"></script>
</body>
</html>
