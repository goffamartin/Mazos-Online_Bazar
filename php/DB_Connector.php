<?php
class DB_Connector
{
    private static string $DbHost = "localhost";
    private static string $DbName = "MazosDB";
    private static string $DbUser = "goffamar";
    private static string $DbPass = "webove aplikace";

    public static function Connect(): ?PDO
    {
        try {
            // Create a PDO connection
            $conn = new PDO("mysql:host=".DB_Connector::$DbHost.";dbname=".DB_Connector::$DbName, DB_Connector::$DbUser, DB_Connector::$DbPass);

            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}