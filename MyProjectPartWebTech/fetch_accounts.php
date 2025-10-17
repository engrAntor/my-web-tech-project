<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Admin') {
    echo json_encode(["error" => "Unauthorized access!"]);
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$sql = "SELECT id, fullname, email, mobile, account_type, account_balance FROM users";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
} else {
    echo json_encode([]);
}

$conn->close();
?>
