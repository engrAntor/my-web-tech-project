<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost'; // Change if using a remote server
$db = 'user_db'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $confirmPassword = htmlspecialchars($_POST['confirm_password'] ?? '');
    $role = htmlspecialchars($_POST['role'] ?? '');

    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match!']);
        exit;
    } elseif (empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select a role.']);
        exit;
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Sign-up successful!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .form-container {
            width: 400px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 60px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            margin-bottom: 10px;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 106.5%;
            padding: 12px;
            color: white;
            background-color: #0078d7;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #005bb5;
        }
        .link {
            margin-top: 10px;
            display: block;
            color: #0078d7;
            text-decoration: none;
        }
        .message {
            margin: 10px 0;
            font-size: 14px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        async function handleFormSubmit(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            try {
                const response = await fetch('signup.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                const messageDiv = document.querySelector('.message');
                if (result.status === 'success') {
                    messageDiv.textContent = result.message;
                    messageDiv.className = 'message success';
                    event.target.reset();
                } else {
                    messageDiv.textContent = result.message;
                    messageDiv.className = 'message error';
                }
            } catch (error) {
                const messageDiv = document.querySelector('.message');
                messageDiv.textContent = 'An error occurred. Please try again later.';
                messageDiv.className = 'message error';
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <div class="message"></div>
        <form id="signupForm" onsubmit="handleFormSubmit(event)">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your password" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Sign Up</button>
        </form>
        <a href="signin.php" class="link">Sign in</a>
    </div>
</body>
</html>