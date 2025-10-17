<?php
session_start();

// Ensure user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signIn.php");
    exit();
}

// Check if courses are selected
if (isset($_POST['courses']) && !empty($_POST['courses'])) {
    $selected_courses = $_POST['courses']; // Get selected courses
    
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert selected courses into the database
    foreach ($selected_courses as $course) {
        // Split the course and section
        list($course_name, $course_section) = explode(" ", $course);

        // Prepare the SQL query to insert the registration
        $stmt = $conn->prepare("INSERT INTO registered_courses (user_id, course_name, course_section) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $course_name, $course_section);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Course registered successfully!";
        } else {
            echo "Error registering course: " . $conn->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No courses selected!";
}
?>
