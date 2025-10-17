<?php
// Assuming the database connection is set
// Replace this with your actual database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch courses and subjects from database
$sql = "SELECT course_name, subject FROM offered_courses";  // Assuming the courses table has course_name and subject columns
$result = $conn->query($sql);

$courses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
} else {
    $courses = []; // No courses found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offered Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .courses-container {
            width: 80%;
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-box {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-box input {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-courses {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <div class="courses-container">
        <h2>Offered Courses</h2>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search for a course..." onkeyup="filterCourses()">
        </div>

        <?php if (count($courses) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody id="coursesList">
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= htmlspecialchars($course['course_name']) ?></td>
                            <td><?= htmlspecialchars($course['subject']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-courses">No courses found!</p>
        <?php endif; ?>
    </div>

    <script>
        function filterCourses() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#coursesList tr");

            rows.forEach(row => {
                const courseName = row.cells[0].textContent.toLowerCase();
                const subject = row.cells[1].textContent.toLowerCase();
                if (courseName.includes(input) || subject.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
