<?php
// Database connection
$host = 'localhost';
$db = 'user_db'; // Replace with your database name
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize error message 
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email'] ?? '');

    if (!empty($email)) {
        // Check if email exists in the database
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Email exists, redirect to sendCode.php
            header("Location: sendCode.php?email=" . urlencode($email));
            exit();
        } else {
            // Email does not exist
            $error = "Invalid email. Please try again.";
        }
    } else {
        $error = "Email field cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
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
        .form-container {
            width: 400px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            color: #555;
            font-size: 14px;
        }
        input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            color: white;
            background-color: #6a0dad;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
        }
        button:hover {
            background-color: #530b9e;
        }
        a {
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            color: #0078d7;
            font-size: 14px;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forget Password</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="email">Enter your Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <button type="submit">Verify Email</button>
        </form>
        <a href="signin.php">Back to Sign In</a>
    </div>
</body>
</html>
