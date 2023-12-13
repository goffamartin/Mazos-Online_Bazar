<?php
$usernameError = "";
$passwordError = "";
$confirmPasswordError = "";
$genericError = "";
// Include the database connection file
global $conn;
include_once 'db.php';
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm-password"] ?? "";

    // Perform basic validation
    if (empty($username) || empty($password) || empty($confirmPassword) ||  isset($_POST["agreed"])) {
        $genericError = "Prosím vyplňte všechna pole";
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordError = "Hesla se neshodují";
    } else {
        // Check if the username is already taken
        $checkUsernameQuery = "SELECT * FROM User WHERE username = ?";
        $stmt = $conn->prepare($checkUsernameQuery);
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $usernameError = "Toto jméno už je zabrané";
        } else {
            // Insert the new user into the database
            $insertUserQuery = "INSERT INTO `User` (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($insertUserQuery);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt->execute([$username, $hashedPassword]);

            // Redirect to a success page
            header("Location: registration-success.html");
            exit(); // Ensure that no other code is executed after the redirect
        }
    }
}
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Mazoš.cz - Registrace</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../scripts/registration.js"></script>
</head>
<body>
<header>
    <nav>
        <a id="logo" href="../index.php">Mazoš.cz</a>
    </nav>
</header>
<main>
    <div class="container">
        <h2>Registrace</h2>
        <form id="registration-form" class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <span id="genericError" class="error-message"><?php echo $genericError?></span>
            <label for="username"></label>
            <input type="text"
                   id="username"
                   class="<?php echo (($usernameError != "") ?  "error" : "")?>"
                   name="username"
                   placeholder="Uživatelské jméno"
                   value="<?php echo $username ?? "" ?>">
            <span id="usernameError" class="error-message"><?php echo $usernameError?></span>
            <label for="password"></label>
            <input type="password"
                   name="password"
                   id="password"
                   class="<?php echo (($passwordError != "") ?  "error" : "")?>"
                   placeholder="Heslo"
                   value="<?php echo $password ?? "" ?>">
            <span id="passwordError" class="error-message"><?php echo $passwordError?></span>
            <label for="confirm-password"></label>
            <input type="password"
                   id="confirm-password"
                   class="<?php echo (($confirmPasswordError != "") ?  "error" : "")?>"
                   name="confirm-password"
                   placeholder="Heslo znovu"
                   value="<?php echo $confirmPassword ?? "" ?>">
            <span id="confirm-passwordError" class="error-message"><?php echo $confirmPasswordError?></span>
            <label for="agreement-checkbox">
                <input type="checkbox"
                       id="agreement-checkbox"
                       name="agreement"
                       required>
                Prečetl jsem a souhlasím s
                <a href="/index.php">podmínkami komunity Mazoš.cz</a>
            </label>

            <button id="submit-registration-button" name="submit-registration" type="submit">Registrovat se
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