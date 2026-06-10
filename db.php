<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_event";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    exit("Database connection failed.");
}
?>