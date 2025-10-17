<?php
session_start();

if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

switch ($action) {
    case 'add_course':
        $course_name = $conn->real_escape_string($data['course_name']);
        $time_slot = $conn->real_escape_string($data['time_slot']);
        $available_seats = intval($data['available_seats']);

        $sql = "INSERT INTO courses (course_name, time_slot, available_seats) VALUES ('$course_name', '$time_slot', '$available_seats')";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Course added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding course.']);
        }
        break;

    case 'remove_course':
        $course_id = intval($data['course_id']);
        $sql = "DELETE FROM courses WHERE id = $course_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Course removed successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error removing course.']);
        }
        break;

    case 'update_time_slot':
        $course_id = intval($data['course_id']);
        $new_time_slot = $conn->real_escape_string($data['new_time_slot']);
        $sql = "UPDATE courses SET time_slot = '$new_time_slot' WHERE id = $course_id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Time slot updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating time slot.']);
        }
        break;

    case 'fetch_courses':
        $result = $conn->query("SELECT id, course_name, time_slot FROM courses");
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode(['success' => true, 'courses' => $courses]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

$conn->close();
?>
