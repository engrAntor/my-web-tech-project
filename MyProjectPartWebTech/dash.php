<?php
// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

// You can fetch user information from the database using the session `user_id` if needed
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }
        .header {
            width: 100%;
            background-color: #0078d7;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .welcome {
            margin: 20px;
            font-size: 18px;
        }
        .button-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        .button {
            width: 200px;
            padding: 15px;
            text-align: center;
            color: white;
            background-color: #0078d7;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #005bb5;
        }
        .logout {
            margin-top: 20px;
            padding: 10px;
            color: white;
            background-color: red;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="header">Student Dashboard</div>
    <div class="container">
        <div class="welcome">Welcome, <?= $user_name ?>!</div>

        <!-- Student Functionalities -->
        <div class="button-group">
            <a href="search_course.php" class="button">Search Course</a>
            <a href="register_course.php" class="button">Register Courses</a>
            <a href="bill_payment.php" class="button">Bill Payment</a>
            <a href="show_registered_course.php" class="button">Show Registered Course</a>
        </div>

        <!-- Admin Functionalities -->
        <div class="button-group" style="margin-top: 30px;">
            <a href="dashboard.php" class="button">Dashboard</a>
            <a href="update_student_info.php" class="button">Update Student Info</a>
            <a href="assign_course.php" class="button">Assign Course to Student</a>
            <a href="remove_course.php" class="button">Remove a Course</a>
            <a href="verify_transaction.php" class="button">Verify Transaction</a>
            <a href="view_account.php" class="button">View Account</a>
            <a href="edit_account.php" class="button">Edit Account</a>
            <a href="change_password.php" class="button">Change Password</a>
        </div>

        <!-- Logout -->
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
