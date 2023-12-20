<?php
session_start();
// Include the database connection file
include './models/db_helper.php';
include './models/form_helper.php';
$db = new db_helper();
$db->Connect();

$errors = array();
$data = array();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $data['username'] = $_POST["username"] ?? "";
    $data['password'] = $_POST["password"] ?? "";
    $data['confirmPassword'] = $_POST["confirm-password"] ?? "";

    // Perform basic validation
    if (empty($data['username']) || empty($data['password']) || empty($data['confirmPassword']) || isset($_POST["agreed"])) {
        $errors['generic'] = "Prosím vyplňte všechna pole";
    } elseif ($data['password'] !== $data['confirmPassword']) {
        $errors['confirmPassword'] = "Hesla se neshodují";
    } else {
        $result = $db->InsertUser($_POST["username"], $_POST["password"]);
        if ($result === true) {
            // Redirect to a success page
            header("Location: ./views/registration_success.html");
            exit(); // Ensure that no other code is executed after the redirect
        }
        $errors['username'] = 'Toto jméno už je zabrané';
    }
}
include './views/registration.php';