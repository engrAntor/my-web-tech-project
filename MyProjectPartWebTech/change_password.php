<?php
session_start();
header('Content-Type: application/json');  

// Debugging session values
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode([
        "error" => "Session values not set.",
        "session" => $_SESSION // Include session data for debugging
    ]);
    exit();
}

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    echo json_encode([
        "error" => "Unauthorized access: role mismatch.",
        "session" => $_SESSION // Include session data for debugging
    ]);
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Decode the input JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$current_password = $data['current_password'] ?? '';
$new_password = $data['new_password'] ?? '';

// Validate input
if (empty($current_password) || empty($new_password)) {
    echo json_encode(["error" => "All fields are required."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current password from the database
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "User not found."]);
    exit();
}

$user = $result->fetch_assoc();
$current_hashed_password = $user['password'];

// Verify the current password
if (!password_verify($current_password, $current_hashed_password)) {
    echo json_encode(["error" => "Current password is incorrect."]);
    exit();
}

// Hash the new password and update it in the database
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$update_sql = "UPDATE users SET password = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $new_hashed_password, $user_id);

if ($update_stmt->execute()) {
    echo json_encode(["success" => "Password updated successfully."]);
} else {
    echo json_encode(["error" => "Failed to update password."]);
}

// Close resources
$stmt->close();
$update_stmt->close();
$conn->close();
?>
