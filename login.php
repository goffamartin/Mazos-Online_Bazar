<?php
session_start();
// Include the database connection file
include 'models/db_helper.php';
$db = new db_helper();
if (($majorError = $db->Connect()) !== null){
    include './views/error.php';
    die();
}


$errors = array();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for the user
    $user = $db->GetUserByUsername($username);

    if (isset($user)) {
        $storedPassword = $user['password'];

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $storedPassword)) {
            // Sets Cookie to remember user
            $_SESSION['UserId'] = $user['user_Id'];
            // Passwords match, login successful
            header("Location: index.php");
            exit;
        } else {
            // Invalid password
            $errors['password'] = "Nesprávné heslo";
        }
    } else {
        // User not found
        $errors['username'] = "Uživatelské jméno neexistuje";
    }
}
include './views/login.php';

