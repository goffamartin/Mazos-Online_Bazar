<?php
include 'DB_Connector.php';
$conn = DB_Connector::Connect();


// Check if username is set in the POST request
if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $getUserQuery = "SELECT * FROM `User` WHERE username = ?";
    $getUserStmt = $conn->prepare($getUserQuery);
    $getUserStmt->execute([$username]);

    echo $getUserStmt->rowCount() > 0 ? 'taken' : 'available';
} else {
    echo 'Username not provided';
}
