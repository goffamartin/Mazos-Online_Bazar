<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz - Přihlášení</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" type="text/css" href="./css/registration-login.css">
    <script src="../scripts/login.js"></script>
</head>
<body>
<header>
    <nav>
        <a id="logo" href="./index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container registration-login-container">
        <h2>Přihlášení</h2>
        <form id="login-form" class="form" action="<?= $_SERVER["PHP_SELF"]; ?>" method="post">

            <label for="username"></label>
            <input type="text"
                   id="username"
                   class="<?= ((isset($errors['username'])) ? "error" : "") ?>"
                   name="username"
                   placeholder="Uživatelské jméno" value="<?= $username ?? "" ?>">
            <span id="usernameError" class="error-message"><?= $errors['username'] ?? "" ?></span>

            <label for="password"></label>
            <input type="password"
                   id="password"
                   class="<?= ((isset($errors['password'])) ? "error" : "") ?>"
                   name="password"
                   placeholder="Heslo">
            <span id="passwordError" class="error-message"><?= $errors['password'] ?? ""?></span>

            <button class="primary-button" id="submit-login-button" type="submit">Přihlásit se</button>
            <p class="small-text-link">Nemáte účet? <a href="registration.php">Registrujte se</a></p>
        </form>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>