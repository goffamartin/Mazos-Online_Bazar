<?php
// Database connection parameters
$dbhost = "localhost";
$dbname = "MazosDB";
$dbuser = "goffamar";
$dbpass = "webove aplikace";

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
