

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mazoš - Tvůj online bazar</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>

    <header>
        <nav>
            <a id="logo" href="index.php">Mazoš.cz</a>
            <div class="navButtons">
                <?php
                session_start();
                // Check if the user is logged in
                if (!isset($_COOKIE['UserId'])) {
                    ?>
                    <a href="index.php/?me=true">Moje nabídky</a>
                    <a href="pages/product_detail.php">Nová nabídka</a>
                    <?php
                } else {
                    // User is not logged in
                    ?>
                    <!-- Display "Login" and "Register" buttons -->
                    <a href="pages/login.php">Přihlásit se</a>
                    <a href="pages/registration.php">Registrovat se</a>
                    <?php
                }
                ?>
            </div>
        </nav>
    </header>
    <main>

    </main>
    <footer>
        <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
    </footer>
</body>
</html>
