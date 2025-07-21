<?php
require_once 'functions.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (verifySubscription($email, $code)) {
        // Remove from subscribers.txt
        $file = __DIR__ . '/subscribers.txt';
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $newLines = [];

        foreach ($lines as $line) {
            [$e, $c, $status] = explode('|', $line);
            if (trim($e) !== $email) {
                $newLines[] = $line;
            }
        }

        file_put_contents($file, implode("\n", $newLines));

        // Log in unsubscribed_mail.txt
        $unsubFile = __DIR__ . '/unsubscribed_mail.txt';
        file_put_contents($unsubFile, $email . "\n", FILE_APPEND | LOCK_EX);

        $success = "✅ $email has been unsubscribed successfully.";
    } else {
        $errors[] = "❌ Verification failed. Incorrect email or code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 600px;
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h1 {
            text-align: center;
        }

        input[type="email"],
        input[type="text"] {
            padding: 10px;
            width: 70%;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 5px 0;
        }

        button {
            padding: 10px 20px;
            background-color: #cc0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>🚫 Unsubscribe from Emails</h1>

    <?php if (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e ?? '') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Your registered email" required><br>
        <input type="text" name="code" placeholder="Verification code" required><br>
        <button type="submit">Unsubscribe</button>
    </form>

</body>
</html>
