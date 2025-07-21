<?php
require_once 'functions.php';

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match('/^\d{6}$/', $code)) {
        $errors[] = "Invalid code format. Must be 6 digits.";
    }

    if (empty($errors)) {
        if (verifySubscription($email, $code)) {
            $message = "✅ Email verification successful! You're now subscribed to task reminders.";
        } else {
            $errors[] = "Verification failed. Please check your email and code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 500px;
            background-color: #f5f5f5;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h1 {
            text-align: center;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-top: 20px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        form {
            margin-top: 20px;
        }
        input[type="email"], input[type="text"] {
            padding: 10px;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #0088cc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-link,
        .cron-link {
            margin-top: 20px;
            display: block;
            text-align: center;
            text-decoration: none;
            color: #0088cc;
        }
        .cron-link button {
            background-color: #0066cc;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>🔐 Verify Your Email</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="text" name="code" placeholder="Enter 6-digit code" required>
        <button type="submit">Verify</button>
    </form>

    <a href="index.php" class="back-link">← Back to Task Scheduler</a>

    <div class="cron-link">
        <form action="cron.php" method="get">
            <button type="submit">📧 Email Cron Log Viewer</button>
        </form>
    </div>

</body>
</html>
