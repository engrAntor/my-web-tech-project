<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'Admin') {
    header("Location: signIn.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'library');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; 
$sql = "SELECT fullname, email, mobile FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo json_encode(["error" => "User data not found."]);
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Profile</h1>
        <form id="updateProfileForm">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="mobile">Mobile Number:</label>
            <input type="tel" id="mobile" name="mobile" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>

            <button type="submit">Update Profile</button>
        </form>
        <p id="updateStatus"></p>

        <hr>
        <h2>Change Password</h2>
        <form id="changePasswordForm">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Change Password</button>
        </form>
        <p id="passwordStatus"></p>
    </div>

    <script>
        
        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fullname = document.getElementById('fullname').value.trim();
            const email = document.getElementById('email').value.trim();
            const mobile = document.getElementById('mobile').value.trim();
            const status = document.getElementById('updateStatus');

            fetch('update_profileA.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fullname, email, mobile })
            })
            .then(response => response.json())
            .then(data => {
                status.textContent = data.success ? 'Profile updated successfully!' : data.error;
                status.style.color = data.success ? 'green' : 'red';
            });
        });

    
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const current_password = document.getElementById('current_password').value.trim();
            const new_password = document.getElementById('new_password').value.trim();
            const confirm_password = document.getElementById('confirm_password').value.trim();
            const status = document.getElementById('passwordStatus');

            if (new_password !== confirm_password) {
                status.textContent = 'New passwords do not match.';
                status.style.color = 'red';
                return;
            }

            fetch('change_passwordA.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ current_password, new_password })
            })
            .then(response => response.json())
            .then(data => {
                status.textContent = data.success ? 'Password changed successfully!' : data.error;
                status.style.color = data.success ? 'green' : 'red';
            });
        });
    </script>
</body>
</html>
