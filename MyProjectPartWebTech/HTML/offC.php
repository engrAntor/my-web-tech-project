<?php
// Array of offered courses
$courses = [
    "Object Oriented Programming 2",
    "Artificial Intelligence",
    "Computer Graphics",
    "Data Structures",
    "Database Management Systems",
    "Software Engineering",
    "Operating Systems",
    "Networking and Communications"
];

// Handle search query if submitted
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

$filteredCourses = $courses;

// Filter courses if search query is present
if ($searchQuery) {
    $filteredCourses = array_filter($courses, function ($course) use ($searchQuery) {
        return strpos(strtolower($course), $searchQuery) !== false;
    });
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
            width: 500px;
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
        .courses-list {
            list-style-type: none;
            padding: 0;
        }
        .courses-list li {
            margin: 5px 0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .courses-list li:last-child {
            border-bottom: none;
        }
        .no-results {
            text-align: center;
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="courses-container">
        <h2>Offered Courses</h2>
        <div class="search-box">
            <form method="GET" action="offered_courses.php">
                <input type="text" name="search" placeholder="Search for a course..." value="<?= htmlspecialchars($searchQuery) ?>">
            </form>
        </div>
        <ul class="courses-list">
            <?php if (!empty($filteredCourses)): ?>
                <?php foreach ($filteredCourses as $course): ?>
                    <li><?= htmlspecialchars($course) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="no-results">No courses found</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
