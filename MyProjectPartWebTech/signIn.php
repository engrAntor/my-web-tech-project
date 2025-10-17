

<?php

$host = 'localhost';
$db = 'user_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $role = htmlspecialchars($_POST['role'] ?? '');

    if (!empty($email) && !empty($password) && !empty($role)) {
        $sql = "SELECT * FROM users WHERE email = :email AND role = :role";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role === 'student') 
            {
                header("Location: studash.php");
            }
            exit();
        } else {
            $error = "Invalid email, password, or role.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signin-container {
            width: 400px;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        .signin-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .signin-container form {
            display: flex;
            flex-direction: column;
        }
        .signin-container input, .signin-container select {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .signin-container button {
            background-color: #0078d7;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .signin-container button:hover {
            background-color: #005bb5;
        }
        .signin-container a {
            margin-top: 10px;
            text-decoration: none;
            color: #0078d7;
        }
        .signin-container a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .link-container {
            margin-top: 10px;
        }
    </style>



  


    <script>
        function validateForm() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const role = document.getElementById('role').value;

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !emailPattern.test(email)) {
                alert('Please enter a valid email.');
                return false;
            }

            if (!password) {
                alert('Please enter your password.');
                return false;
            }

            if (!role) {
                alert('Please select your role.');
                return false;
            }

            return true;
        }
    </script>





</head>
<body>
    <div class="signin-container">
        <h2>Sign In</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="signin.php" onsubmit="return validateForm()">
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <select name="role" id="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Sign In</button>
        </form>

        <div class="link-container">
            <a href="forgetPass.php">Forgot Password?</a><br>
            <a href="signUp.php">Don't have an account? Sign up here</a>
        </div>
    </div>
</body>
</html>
