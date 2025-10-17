<?php
session_start();

// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signIn.php");
    exit();
}

// Fetch user details
$userName = $_SESSION['user_name'];
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
            text-align: center;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .dashboard {
            margin-top: 50px;
        }
        .dashboard h1 {
            color: #333;
        }
        .button-container {
            margin-top: 30px;
        }
        .button-container a {
            display: block;
            margin: 10px auto;
            width: 200px;
            padding: 15px;
            background-color: #0078d7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
        .button-container a:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?= htmlspecialchars($userName) ?>!</h1>
        <p>Select an option below to proceed:</p>
        <div class="button-container">
            <a href="view_courses.php">Offered Courses</a>
            <a href="register_course.php">Register Courses</a> <!-- This takes the student to register_courses.php -->
            <a href="payment.php">Bill Payment</a>
            <a href="view_registered_courses.php">Show Registered Courses</a> <!-- Updated link -->
            <a href="sections.php">Sections</a>
        </div>
        <br>
        <a href="logout.php" style="color: red; text-decoration: none;">Log Out</a>
    </div>
</body>
</html>
