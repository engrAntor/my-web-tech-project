<?php

session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if ($input['action'] === 'accept') {
    $_SESSION['terms_accepted'] = true;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>