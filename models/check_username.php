<?php
include 'db_helper.php';
$db = new db_helper();
$db->Connect();


// Check if username is set in the POST request
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    /**
     * Check if the user exists in the database.
     *
     * This function is defined in 'db_helper.php'.
     * It checks the existence of a user by their username
     *
     * @param string $username The username to check for in the database.
     * @return user|null Returns user if the user exists, null otherwise.
     */
    $result = $db->GetUserByUsername($username);
    echo isset($result) ? 'taken' : 'available';
} else {
    echo 'Username not provided';
}
