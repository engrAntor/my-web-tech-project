<?php
// Start session to manage user login
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'library');

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $method = $_POST['method'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $student_id = intval($_POST['student_id']);
    $account = trim($_POST['account']);
    $amount = floatval($_POST['amount']);
    
    $errors = [];

    // Validate Student ID and fetch user balance
    $stmt = $conn->prepare("SELECT id, fullname, email, account_balance FROM users WHERE id = ? AND account_type = 'Student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $errors[] = "Invalid Student ID.";
    } else {
        $student = $result->fetch_assoc();

        // Validate email matches the Student ID
        if ($email !== $student['email']) {
            $errors[] = "Email does not match the Student ID.";
        }

        // Check for sufficient balance
        if ($student['account_balance'] < $amount) {
            $errors[] = "Insufficient balance. Available balance: $" . number_format($student['account_balance'], 2);
        }
    }

    // If no errors, process payment
    if (empty($errors)) {
        $new_balance = $student['account_balance'] - $amount;

        // Update user's account balance
        $stmt = $conn->prepare("UPDATE users SET account_balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_balance, $student_id);

        if ($stmt->execute()) {
            echo "<p class='success'>Payment of $" . number_format($amount, 2) . " successful. New balance: $" . number_format($new_balance, 2) . "</p>";

            // Log the transaction
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, method, amount, date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("isd", $student_id, $method, $amount);
            $stmt->execute();
        } else {
            echo "<p class='error'>Error processing payment. Please try again.</p>";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    }

    $stmt->close();
}

$conn->close();
?>
