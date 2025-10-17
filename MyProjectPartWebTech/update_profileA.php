<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Admin') {
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['fullname']) || empty($data['email']) || empty($data['mobile'])) {
    echo json_encode(["error" => "All fields are required."]);
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $conn->real_escape_string($data['fullname']);
$email = $conn->real_escape_string($data['email']);
$mobile = $conn->real_escape_string($data['mobile']);

$sql = "UPDATE users SET fullname = ?, email = ?, mobile = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $fullname, $email, $mobile, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to update profile."]);
}

$stmt->close();
$conn->close();
?>
