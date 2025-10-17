<?php
session_start();
require_once 'config.php';

// Include Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configure error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sending the code
    if (isset($_POST['send_code'])) {
        $email = htmlspecialchars($_POST['email'] ?? '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Generate a random verification code
            $code = rand(100000, 999999);
            $_SESSION['verification_code'] = $code;
            $_SESSION['email'] = $email;

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = '---------'; // Replace with your email
                $mail->Password = '---------'; // Replace with your email password or app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email settings
                $mail->setFrom('your_email@gmail.com', 'Your Application Name');
                $mail->addAddress($email);
                $mail->Subject = 'Your Verification Code';
                $mail->Body = "Your verification code is: $code";

                // Send email
                $mail->send();
                $message = "A verification code has been sent to $email.";
            } catch (Exception $e) {
                $error = "Error sending email: " . $mail->ErrorInfo;
            }
        } else {
            $error = "Invalid email address.";
        }
    }

    // Handle verification of the code
    if (isset($_POST['verify_code'])) {
        $entered_code = htmlspecialchars($_POST['code'] ?? '');

        if ($entered_code == ($_SESSION['verification_code'] ?? '')) {
            // Redirect to change_password.php on successful verification
            header("Location: change_password.php");
            exit();
        } else {
            $error = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .box {
            width: 400px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        input, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            border: none;
            cursor: pointer;
            color: white;
        }
        .btn-send {
            background-color: #6a5acd;
        }
        .btn-verify {
            background-color: #4caf50;
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
</head>
<body>
    <div class="box">
        <h2>Send Code Via Email</h2>
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php elseif (isset($message)): ?>
            <div class="message success"><?= $message ?></div>
        <?php elseif (isset($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST" action="sendCode.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button class="btn-send" type="submit" name="send_code">Send Code</button>
        </form>
        <form method="POST" action="sendCode.php">
            <input type="text" name="code" placeholder="Enter verification code" required>
            <button class="btn-verify" type="submit" name="verify_code">Verify</button>
        </form>
        <a href="signin.php" class="link">Back</a>
    </div>
</body>
</html>
