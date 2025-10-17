<?php
// 1. Include the secret configuration file
require_once 'config.php';

// 2. Use the defined constants to connect
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>