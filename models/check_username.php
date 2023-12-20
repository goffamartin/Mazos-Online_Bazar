<?php
include 'db_helper.php';
$db = new db_helper();
$db->Connect();


// Check if username is set in the POST request
if (isset($_POST['username'])) {

    $result = $db->GetUserByUsername($_POST['username']);
    echo isset($result) ? 'taken' : 'available';
} else {
    echo 'Username not provided';
}
