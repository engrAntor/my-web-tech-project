<?php
session_start();

// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signIn.php");
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
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $selected_courses = $data['courses'] ?? [];

    $user_id = $_SESSION['user_id'];

    // Remove unchecked courses
    $placeholders = implode(",", array_fill(0, count($selected_courses), '?'));
    $stmt = $conn->prepare(
        "DELETE FROM registered_courses WHERE user_id = ? AND CONCAT(course_name, ' ', course_section) NOT IN (" . ($placeholders ?: "''") . ")"
    );
    $types = "i" . str_repeat("s", count($selected_courses));
    $params = array_merge([$user_id], $selected_courses);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Add newly selected courses
    foreach ($selected_courses as $course) {
        $split_course = preg_split('/\s+/', $course);
        $course_section = array_pop($split_course); // Last element is the section
        $course_name = implode(" ", $split_course); // Join the remaining part as the course name

        $stmt = $conn->prepare("SELECT * FROM registered_courses WHERE user_id = ? AND course_name = ? AND course_section = ?");
        $stmt->bind_param("iss", $user_id, $course_name, $course_section);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt_insert = $conn->prepare("INSERT INTO registered_courses (user_id, course_name, course_section) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("iss", $user_id, $course_name, $course_section);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
        $stmt->close();
    }

    echo json_encode(['status' => 'success', 'message' => 'Course registration updated successfully!']);
    exit;
}

// Fetch courses and registered courses
$sql = "SELECT course_name, course_section FROM courses";
$result = $conn->query($sql);

$courses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[$row['course_name']][] = $row['course_section'];
    }
}

$sql_registered = "SELECT course_name, course_section FROM registered_courses WHERE user_id = ?";
$stmt = $conn->prepare($sql_registered);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result_registered = $stmt->get_result();
$registered_courses = [];
if ($result_registered->num_rows > 0) {
    while ($row = $result_registered->fetch_assoc()) {
        $registered_courses[$row['course_name']][] = $row['course_section'];
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration</title>
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
        .section {
            margin: 20px 0;
            text-align: left;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .section h3 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .btn {
            margin: 10px 5px;
            padding: 10px 20px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-confirm {
            background-color: #4CAF50;
        }
        .btn-show {
            background-color: #6a5acd;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            color: green;
            font-weight: bold;
            background-color: #e0f9e0;
            border: 1px solid #d0f0d0;
            border-radius: 5px;
            display: none;
        }
        .message.error {
            color: red;
            background-color: #f9e0e0;
            border-color: #f0d0d0;
        }
    </style>





    
    
    
    <script>
        async function handleFormSubmit(event) {
            event.preventDefault();

            const checkboxes = document.querySelectorAll('input[name="courses[]"]:checked');
            const selectedCourses = Array.from(checkboxes).map(checkbox => checkbox.value);

            try {
                const response = await fetch('register_course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ courses: selectedCourses }),
                });

                const result = await response.json();
                const messageDiv = document.querySelector('.message');

                messageDiv.textContent = result.message;
                messageDiv.style.display = 'block';

                if (result.status === 'success') {
                    messageDiv.classList.remove('error');
                } else {
                    messageDiv.classList.add('error');
                }
            } catch (error) {
                const messageDiv = document.querySelector('.message');
                messageDiv.textContent = 'An error occurred while processing your request.';
                messageDiv.style.display = 'block';
                messageDiv.classList.add('error');
            }
        }
    </script>








</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Register for Courses</h1>
        </div>

        <div class="message"></div>

        <form id="courseForm" onsubmit="handleFormSubmit(event)">
            <div class="content">
                <?php foreach ($courses as $course_name => $sections): ?>
                    <div class='section'>
                        <h3><?= htmlspecialchars($course_name) ?></h3>
                        <?php foreach ($sections as $section): ?>
                            <?php 
                                $course_key = "$course_name $section";
                                $checked = isset($registered_courses[$course_name]) && in_array($section, $registered_courses[$course_name]) ? "checked" : "";
                            ?>
                            <label>
                                <input type="checkbox" name="courses[]" value="<?= htmlspecialchars($course_key) ?>" <?= $checked ?>> 
                                <?= htmlspecialchars($course_name) ?> [<?= htmlspecialchars($section) ?>]
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div>
                <button type="submit" class="btn btn-confirm">Confirm</button>
                <button type="button" class="btn btn-show" onclick="location.href='view_registered_courses.php'">Show Registered Sections</button>
            </div>
        </form>
    </div>
</body>
</html>