<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz - Registrace</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" type="text/css" href="./css/registration-login.css">
    <script src="../scripts/registration.js"></script>
</head>
<body>
<header>
    <nav>
        <a id="logo" href="./index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container registration-login-container">
        <h2>Registrace</h2>
        <form id="registration-form" class="form" action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <span id="genericError" class="error-message"><?= getFormData('generic', $errors) ?></span>
            <label for="username"></label>
            <input type="text"
                   id="username"
                   class="<?= ((getFormData('username', $errors) != "") ? "error" : "") ?>"
                   name="username"
                   placeholder="Uživatelské jméno"
                   value="<?= getFormData('username', $data) ?>">
            <span id="usernameError" class="error-message"><?= getFormData('username', $errors) ?></span>
            <label for="password"></label>
            <input type="password"
                   name="password"
                   id="password"
                   class="<?= ((getFormData('password', $errors) != "") ? "error" : "") ?>"
                   placeholder="Heslo"
                   value="<?= getFormData('password', $data) ?>">
            <span id="passwordError" class="error-message"><?= getFormData('password', $errors) ?></span>
            <label for="confirm-password"></label>
            <input type="password"
                   id="confirm-password"
                   class="<?= (getFormData('confirmPassword', $errors)) ? "error" : "" ?>"
                   name="confirm-password"
                   placeholder="Heslo znovu">
            <span id="confirm-passwordError" class="error-message"><?= getFormData('confirmPassword', $errors) ?></span>
            <label for="agreement-checkbox">
                <input type="checkbox"
                       id="agreement-checkbox"
                       name="agreement"
                       required>
                <span>Prečetl jsem a souhlasím s <a href="index.php"><!--placeholder-->podmínkami komunity Mazoš.cz</a></span>
            </label>

            <button class="primary-button" id="submit-registration-button" name="submit-registration" type="submit">
                Registrovat se
            </button>
        </form>
        <p class="small-text-link">Už máte účet? <a href="login.php">Přihlaste se</a></p>
    </div>
</main>
<footer>
    <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>