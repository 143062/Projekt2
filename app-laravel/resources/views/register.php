<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/auth_common.css">
    <link rel="stylesheet" href="/css/register.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="logo">
                <img src="/img/logo.svg" alt="Logo" class="logo__image">
            </div>

            <!-- Miejsce na dynamiczne komunikaty o błędach -->
            <div class="register-form">
                <div class="error-container" style="display: none;">
                    <p class="error-message"></p>
                </div>
                <form id="register-form" class="form-column" novalidate>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <input type="text" id="login" name="login" placeholder="Login" required>
                    <input type="password" id="password" name="password" placeholder="Hasło" required>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Potwierdź Hasło" required>
                    <div class="buttons-container">
                        <button type="button" id="register-submit" class="form__submit">Rejestruj</button>
                        <a href="/login" class="form_redirect">Logowanie</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Podpięcie globalnych skryptów -->
    <script src="/js/auth.js"></script>
    <script src="/js/init.js"></script>
    
    <script src="/js/register.js"></script>
</body>
</html>
