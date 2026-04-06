<?php
$servername = "localhost";
$username   = "ssbgovlk_admin";
$password   = "ssb@gov.lk";
$dbname     = "ssbgovlk_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
