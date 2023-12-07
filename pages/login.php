<?php
session_start();
// Include the database connection file
global $conn;

include_once 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for the user
    $getUserQuery = "SELECT * FROM `User` WHERE `username` = ?";
    $stmt = $conn->prepare($getUserQuery);
    $stmt->execute([$username]);

    // Check if the user exists
    if ($stmt->rowCount() > 0) {
        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedPassword = $user['password'];

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $storedPassword)) {
            // Sets Cookie to remember user
            setcookie("UserId", $user['user_Id'], time() + 86400);
            // Passwords match, login successful
            header("Location: ../index.php");
            exit;
        } else {
            // Invalid password
            $genericError = "Nesprávné jméno nebo heslo";
        }
    } else {
        // User not found
        $genericError = "Nesprávné jméno nebo heslo";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mazoš.cz - Registrace</title>
  <link rel="stylesheet" href="/css/style.css"/>
  <script src="../scripts/login.js"></script>
</head>
<body>
<header>
  <nav>
    <a id="logo" href="../index.php">Mazoš.cz</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>Přihlášení</h2>
    <form id="login_form" class="form" action="login.php" method="post">
        <span id="genericError" class="error-message"><?php echo $genericError ?? ""; ?></span>

        <label for="username"></label>
        <input type="text"
               id="username"
               name="username"
               placeholder="Uživatelské jméno" value="<?php echo $username ?? "" ?>">

        <label for="password"></label>
        <input type="password"
               name="password"
               id="password"
               placeholder="Heslo">

      <button id="submit_login_button" type="submit">Přihlásit se</button>
    </form>
  </div>
</main>
<footer>
  <p>© Copyright - Mazoš.cz | site by <b>GOFFA</b></p>
</footer>
</body>
</html>