<?php
session_start();

// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signIn.php");
    exit();
}

$student_id = $_SESSION['user_id']; // Student's ID from the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered courses
$sql = "SELECT course_name, course_section, registration_date FROM registered_courses WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Registered Courses</h1>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>Course Name</th><th>Course Section</th><th>Registration Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row['course_name']) . "</td><td>" . htmlspecialchars($row['course_section']) . "</td><td>" . $row['registration_date'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No courses registered yet.";
}

$stmt->close();
$conn->close();
?>
