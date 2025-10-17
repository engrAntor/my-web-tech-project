<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Student') {
        echo json_encode(["error" => "You must be logged in as a student."]);
        exit();
    }

    
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($inputData['fullname']) || !isset($inputData['email']) || !isset($inputData['mobile'])) {
        echo json_encode(["error" => "All fields are required."]);
        exit();
    }

    $fullname = trim($inputData['fullname']);
    $email = trim($inputData['email']);
    $mobile = trim($inputData['mobile']);

    
    if (empty($fullname) || empty($email) || empty($mobile)) {
        echo json_encode(["error" => "All fields must be filled."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format."]);
        exit();
    }

    if (!preg_match('/^\d{10}$/', $mobile)) {
        echo json_encode(["error" => "Mobile number must be 10 digits."]);
        exit();
    }

    
    $conn = new mysqli('localhost', 'root', '', 'library');
    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    
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
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>


