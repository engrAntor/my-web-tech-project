<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['account_type'])) {
    echo json_encode(["error" => "User not authenticated. Please log in."]);
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'library');
if (!$conn) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$account_type = mysqli_real_escape_string($conn, $_SESSION['account_type']);

$sql = "SELECT fullname, id, account_balance FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $balance = max(0, $user['account_balance']);

    echo json_encode([
        "fullname" => $user['fullname'],
        "id" => $user['id'],
        "balance" => $balance,
        "account_type" => $account_type
    ]);
} else {
    echo json_encode(["error" => "Error fetching user data!"]);
}

mysqli_close($conn);
?>
