<?php
session_start();

// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signin.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the registered courses for the logged-in student
$user_id = $_SESSION['user_id'];
$sql = "SELECT course_name, course_section FROM registered_courses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px auto;
            width: 80%;
            text-align: center;
        }
        .header {
            background-color: #0078d7;
            color: white;
            padding: 10px 0;
        }
        .course {
            text-align: left;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Registered Courses</h1>
        </div>
        <div>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='course'>" . htmlspecialchars($row['course_name']) . " [" . htmlspecialchars($row['course_section']) . "]</div>";
                }
            } else {
                echo "<div>No courses registered yet.</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
